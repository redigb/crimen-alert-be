<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LogService;
use App\Services\ImageService;
use App\Models\User;
use App\Traits\NombreClaseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller {

    use NombreClaseTrait;
    
    protected $logService;
    protected $nombreTable;
    protected $mediaService;

    public function __construct(LogService $logService, ImageService $mediaService) {
        $this->logService = $logService;
        $this->mediaService = $mediaService;
        $this->nombreTable = $this->getNombreClaseModificado();
    }
    
    public function users() {
        $response = ["status" => false, "msg" => "", "data" => []];

        $users = User::all();

        if ($users->isNotEmpty()) {
            $response["status"] = 200;
            $response["msg"] = "Usuarios encontrados correctamente.";
            $response["data"] = $users;
        } else {
            $response["status"] = 200;
            $response["msg"] = "No se encontraron usuarios.";
        }
        return response()->json($response);
    }

    public function userConsult(Request $name) {
        $response = ["status" => false, "msg" => "", "data" => []];

        $user = User::where('name', $name->name)->first();

        if ($user) {
            $response["status"] = 200;
            $response["msg"] = "Usuario encontrado!";
            $response["data"] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'telefono' => $user->telefono,
                'active' => $user->active,
                'image_profile' => $user->image_profile,
            ];
        } else {
            $response["status"] = 404;
            $response["msg"] = "No se encontró el usuario.";
        }
        return response()->json($response);
    }

    public function login(Request $request) {
        $response = ["status" => false, "msg" => ""];

        $validator = Validator::make(json_decode($request->getContent(), true) ?? $request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $response["status"] = 422;
            $response["msg"] = "Error de validación";
            return response()->json($response, 422);
        }

        $data = json_decode($request->getContent());
        if (!$data && $request->has('email')) {
            $data = (object) $request->all();
        }

        $user = User::where('email', $data->email)->first();

        if ($user && Hash::check($data->password, $user->password)) {
            $tokenCount = $user->tokens()->count();
            if ($tokenCount >= 2) {
                $oldestToken = $user->tokens()->oldest()->first();
                if ($oldestToken) {
                    $oldestToken->delete();
                }
            }

            $token = $user->createToken('auth_token', ['*'], now()->addHours(2));

            if (!$user->active) {
                $user->active = true;
                $user->save();
            }

            $response["status"] = 200;
            $response["msg"] = "Success.";
            $response["name"] = $user->name;
            $response["email"] = $user->email;
            $response["token"] = $token->plainTextToken;
        } else {
            $response["status"] = 401;
            $response["msg"] = "Usuario o contraseña incorrectos.";
        }

        return response()->json($response);
    }
    
    public function logout(Request $request){
        $user = $request->user();
        if ($user) {
            /**
             * * Desactivar el usuario 
             * * ERROR DE SECCION - en espera de coreecion en manejo de estado
             */
            $user->active = false;
            $user->save();
            // Eliminar solo token de uso
            $user->currentAccessToken()->delete();
            return response()->json([
                'status' => true,
                'message' => 'Cierre de sesión exitoso.',
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'No autorizado.'
        ], 401);
    }

    public function register(Request $request) {
        $this->logService->registrarInfo('*** Init Registro de Usuario ***');
        
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'telefono' => ['required', 'regex:/^\+51\d{9}$/'],
                'password' => 'required|string|min:6',
            ]);
            
            if ($validator->fails()) {
                $this->logService->registrarInfo('Validación fallida: ' . json_encode($validator->errors()));
                return response()->json([
                    'status' => 422,
                    'msg' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Procesar la contraseña
            $password = $request->input('password');
            $request->merge(['password' => Hash::make($password)]);

            // Crear el usuario
            $user = User::create($request->all());

            $this->logService->registrarInfo('Usuario creado con ID: ' . $user->id);
            $this->logService->registrarInfo('*** End Registro de Usuario ***');

            return response()->json([
                'status' => 201,
                'msg' => 'Usuario registrado correctamente',
                'data' => $user
            ], 201);
            
        } catch (\Exception $e) {
            $this->logService->registrarInfo('Error: ' . $e->getMessage());
            $this->logService->registrarInfo('*** End Registro de Usuario con Error ***');
            
            return response()->json([
                'status' => 400,
                'msg' => 'Error al registrar el usuario',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function uploadImageProfile(Request $request) {
        $user = $request->user();
        $response = ["status" => false, "msg" => "", "data" => []];
        //-Caso: Eliminar-imagen
        if ($request->has('delete') && $request->delete == true) {
            if ($user->image_profile) {
                $this->mediaService->deleteFile($user->image_profile);
                $user->image_profile = null;
                $user->save();
            }
            $response["status"] = 200;
            $response["msg"] = "Imagen de perfil eliminada correctamente.";
            return response()->json($response, 200);
        }
        //-validar-imagen-subida
        $validation = $this->mediaService->validateFile($request, [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        if (!$validation['status']) {
            return response()->json([
                'status' => 422,
                'errors' => $validation['errors']
            ], 422);
        }
        //-borrar-imagen-anterior
        if ($user->image_profile) {
            $this->mediaService->deleteFile($user->image_profile);
        }
        //-nueva-imagen
        $path = $this->mediaService->uploadFile($request->file('file'), 'images');
        $user->image_profile = $path;
        //-guardar-Imagen
        $user->save();
        $response["status"] = 200;
        $response["msg"] = "Imagen actualizada.";
        $response["data"] = ["path" => $path];
        return response()->json($response, 200);
    }
}