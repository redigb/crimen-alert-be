<?php

namespace App\Http\Controllers;

use App\Models\ReportComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReportCommentController extends Controller
{
    // Registrar un comentario o respuesta
    public function store(Request $request) {
        $request->validate([
            'report_id' => 'required|exists:users_reports,id',
            'comment' => 'required|string',
            'parent_id' => 'nullable|exists:report_comments,id'
        ]);

        $userId = Auth::id() ?? $request->user()->id;

        $comment = ReportComment::create([
            'user_id' => $userId,
            'report_id' => $request->report_id,
            'parent_id' => $request->parent_id,
            'comment' => $request->comment
        ]);

        return response()->json([
            'status' => true,
            'msg' => 'Comentario registrado correctamente.',
            'data' => $comment
        ], 201);
    }

    // Listar comentarios de un reporte (anidados)
    public function index($report_id)
    {
        // Trae solo los comentarios principales y sus respuestas anidadas
        $comments = ReportComment::where('report_id', $report_id)
            ->whereNull('parent_id')
            ->with(['user:id,name,image_profile', 'replies.user:id,name,image_profile', 'replies.replies'])
            ->get();

        return response()->json([
            'status' => true,
            'data' => $comments
        ]);
    }
}
