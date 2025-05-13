<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReports extends Model {
    protected $table = 'users_reports'; 

    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'direccion',
        'latitude',
        'longitude',
        'image',
        'video',
        'fecha_hora_report'
    ];

    // Relación con el modelo User
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

     // Relación con los votos del reporte
    public function votes() {
        return $this->hasMany(\App\Models\ReportVote::class, 'report_id');
    }

    // Relación con los comentarios del reporte (opcional, si usas withCount(['comments as comments_count']))
    public function comments() {
        return $this->hasMany(\App\Models\ReportComment::class, 'report_id');
    }
}