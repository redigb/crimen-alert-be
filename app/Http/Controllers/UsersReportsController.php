<?php

namespace App\Http\Controllers;

use App\Models\UsersReports;
use App\Utils\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UsersReportsController extends Controller
{
    public function usersReport(Request $request)
    {
        $request->validate([
            'image' => 'mimes:jpeg,png,jpg,gif|max:5120',
            'video' => 'mimes:mp4,mov,avi,flv|max:20480',
        ]);

        $imagePath = null;
        $videoPath = null;

        // Procesar la imagen si existe
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = FileHelper::generateFilename($imageFile, 'IMG');
            $imageFile->storeAs('images', $imageName, 'public');
            $imagePath = 'storage/images/' . $imageName;
        }

        // Procesar el video si existe
        if ($request->hasFile('video')) {
            $videoFile = $request->file('video');
            $videoName = FileHelper::generateFilename($videoFile, 'VIDEO');
            $videoFile->storeAs('videos', $videoName, 'public');
            $videoPath = 'storage/videos/' . $videoName;
        }

        // Crear el reporte con los datos del request y añadir las rutas de los archivos
        $report = UsersReports::create(array_merge(
            $request->all(),
            [
                'image' => $imagePath,
                'video' => $videoPath,
                'fecha_hora_report' => $request->fecha_hora_report ?? now()->toDateTimeString()
            ]
        ));

        // Devolver una respuesta con las URLs de los archivos
        return response()->json([
            'message' => 'Reporte subido exitosamente',
            'report' => $report,
            'image_url' => $imagePath ? asset($imagePath) : null,
            'video_url' => $videoPath ? asset($videoPath) : null,
        ], 201);
    }
}