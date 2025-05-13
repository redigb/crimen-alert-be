<?php

namespace App\Http\Controllers;

use App\Models\ReportVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportVoteController extends Controller
{
    // Registrar un voto
    public function store(Request $request) {
        $request->validate([
            'report_id' => 'required|exists:users_reports,id',
            'vote' => 'required|in:conforme,no_conforme',
            'comment' => 'nullable|string|max:255'
        ]);
        $userId = Auth::id() ?? $request->user()->id;
        // Evitar votos duplicados
        $existingVote = ReportVote::where('user_id', $userId)
            ->where('report_id', $request->report_id)
            ->first();

        if ($existingVote) {
            return response()->json([
                'status' => false,
                'msg' => 'Ya has votado en este reporte.'
            ], 409);
        }

        $vote = ReportVote::create([
            'user_id' => $userId,
            'report_id' => $request->report_id,
            'vote' => $request->vote,
            'comment' => $request->comment
        ]);

        return response()->json([
            'status' => true,
            'msg' => 'Voto registrado correctamente.',
            'data' => $vote
        ], 201);
    }

    // Listar votos de un reporte
    public function listVotos($report_id) {
        $votes = ReportVote::where('report_id', $report_id)
            ->with('user:id,name,image_profile')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $votes
        ]);
    }
}
