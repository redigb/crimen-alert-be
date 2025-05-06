<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LogService;
use App\Models\Users;
use App\Traits\NombreClaseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UsersController extends Controller 
{
    use NombreClaseTrait;
    
    protected $logService;
    protected $nombreTable;

    
    public function __construct(LogService $logService) 
    {
        $this->logService = $logService;
        $this->nombreTable = $this->getNombreClaseModificado();
    }
    
    public function users() 
    {
        $response = ["status" => false, "msg" => "", "data" => []];

        $users = Users::all();

        if ($users->isNotEmpty()) {
            $response["status"] = true;
            $response["msg"] = "Usuarios encontrados correctamente.";
            $response["data"] = $users;
        } else {
            $response["msg"] = "No se encontraron usuarios.";
        }
        return response()->json($response);
    }

    public function login(Request $request)
    {
        $response = ["status" => false, "msg" => ""];
        
        // Validar los datos de entrada
        $validator = Validator::make(json_decode($request->getContent(), true) ?? $request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if ($validator->fails()) {
            $response["msg"] = "Datos de entrada inválidos.";
            return response()->json($response, 422);
        }
        
        $data = json_decode($request->getContent());
        if (!$data && $request->has('email')) {
            $data = (object) $request->all();
        }

        $user = Users::where('email', $data->email)->first();

        if ($user && Hash::check($data->password, $user->password)) {
            $token = $user->createToken("example");

            $response["status"] = true;
            $response["msg"] = "Usuario encontrado correctamente.";
            $response["name"] = $user->name;
            $response["email"] = $user->email;
            $response["token"] = $token->plainTextToken;
        } else {
            $response["msg"] = "Credenciales incorrectas.";
        }
        return response()->json($response);
    }
    
    public function store(Request $request)
    {
        $this->logService->registrarInfo('*** Init Registro de Usuario ***');
        
        try {
            // Validar los datos de entrada
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
            ]);
            
            if ($validator->fails()) {
                $this->logService->registrarInfo('Validación fallida: ' . json_encode($validator->errors()));
                return response()->json([
                    'status' => false,
                    'msg' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Procesar la contraseña
            $password = $request->input('password');
            $request->merge(['password' => Hash::make($password)]);

            // Crear el usuario
            $user = Users::create($request->all());

            $this->logService->registrarInfo('Usuario creado con ID: ' . $user->id);
            $this->logService->registrarInfo('*** End Registro de Usuario ***');

            return response()->json([
                'status' => true,
                'msg' => 'Usuario registrado correctamente',
                'data' => $user
            ], 201);
            
        } catch (\Exception $e) {
            $this->logService->registrarInfo('Error: ' . $e->getMessage());
            $this->logService->registrarInfo('*** End Registro de Usuario con Error ***');
            
            return response()->json([
                'status' => false,
                'msg' => 'Error al registrar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}