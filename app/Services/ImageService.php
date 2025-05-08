<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageService {
    
    /**
     * Validar archivo subido.
     */

    public function validateFile(Request $request, array $rules){
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return [
                'status' => false,
                'errors' => $validator->errors()
            ];
        }
        return ['status' => true];
    }

    /**
     * Subir archivo (imagen o video) al disco configurado.
     */
    public function uploadFile($file, $folder = 'uploads'){
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, 'public'); // usando disco 'public'
        return $path;
    }

    /**
     * Eliminar archivo del disco configurado.
     */
    public function deleteFile($filepath){
        if (Storage::disk('public')->exists($filepath)) {
            Storage::disk('public')->delete($filepath);
            return true;
        }
        return false;
    }
}