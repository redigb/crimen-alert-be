<?php

namespace App\Http\Controllers;

use App\Models\UserReports;
use App\Services\ImageService;
use Illuminate\Http\Request;

class UserReportsController extends Controller {

    protected $imageService;

    public function __construct(ImageService $imageService) {
        $this->imageService = $imageService;
    }

    public function reportsList() {
        $reports = UserReports::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 200,
            'message' => 'Lista de reportes',
            'data' => $reports->items(),
            'pagination' => [
                'current_page' => $reports->currentPage(),
                'next_page_url' => $reports->nextPageUrl(),
                'has_more' => $reports->hasMorePages(),
            ]
        ], 200);
    }


    public function userReportCreate(Request $request) {
        $validation = $this->imageService->validateFile($request, [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'user_id' => 'required|exists:users,id',

            'image' => 'mimes:jpeg,png,jpg,gif|max:5120',
            'video' => 'mimes:mp4,mov,avi,flv|max:20480',
        ]);

        if (!$validation['status']) {
            return response()->json([
                'status' => false,
                'errors' => $validation['errors'],
            ], 422);
        }

        $imagePath = null;
        $videoPath = null;

        // Subir imagen si existe
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->uploadFile($request->file('image'), 'images');
        }

        // Subir video si existe
        if ($request->hasFile('video')) {
            $videoPath = $this->imageService->uploadFile($request->file('video'), 'videos');
        }

        // Crear reporte
        $report = UserReports::create([
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'image' => $imagePath ? 'storage/' . $imagePath : null,
            'video' => $videoPath ? 'storage/' . $videoPath : null,
            'fecha_hora_report' => $request->input('fecha_hora_report') ?? now()->toDateTimeString(),
            'user_id' => $request->user()->id
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Reporte subido exitosamente',
            'report' => $report,
            'image_url' => $imagePath ? asset('storage/' . $imagePath) : null,
            'video_url' => $videoPath ? asset('storage/' . $videoPath) : null,
        ], 201);
    }

    public function userReportDelete(Request $request, $id) {
        $report = UserReports::find($id);

        if (!$report) {
            return response()->json([
                'status' => 404,
                'message' => 'Reporte no encontrado',
            ], 404);
        }

        // Eliminar imagen y video si existen
        if ($report->image) {
            $this->imageService->deleteFile($report->image);
        }
        if ($report->video) {
            $this->imageService->deleteFile($report->video);
        }

        // Eliminar reporte
        $report->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Reporte eliminado exitosamente',
        ], 200);
    }
  
    public function userReportUpdate(Request $request, $id) {
        $report = UserReports::find($id);

        if (!$report) {
            return response()->json([
                'status' => 404,
                'message' => 'Reporte no encontrado',
            ], 404);
        }

        // Validar campos
        $validation = $this->imageService->validateFile($request, [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'user_id' => 'required|exists:users,id',

            'image' => 'mimes:jpeg,png,jpg,gif|max:5120',
            'video' => 'mimes:mp4,mov,avi,flv|max:20480',
        ]);

        if (!$validation['status']) {
            return response()->json([
                'status' => false,
                'errors' => $validation['errors'],
            ], 422);
        }

        // Subir imagen si existe
        if ($request->hasFile('image')) {
            if ($report->image) {
                $this->imageService->deleteFile($report->image);
            }
            $report->image = $this->imageService->uploadFile($request->file('image'), 'images');
        }

        // Subir video si existe
        if ($request->hasFile('video')) {
            if ($report->video) {
                $this->imageService->deleteFile($report->video);
            }
            $report->video = $this->imageService->uploadFile($request->file('video'), 'videos');
        }

        // Actualizar reporte
        $report->update($request->all());

        return response()->json([
            'status' => 200,
            'message' => 'Reporte actualizado exitosamente',
            'report' => $report,
        ], 200);
    }
}
