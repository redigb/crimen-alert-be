<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportComment extends Model {

    protected $table = 'report_comments';

    protected $fillable = [
        'user_id',
        'report_id',
        'parent_id',
        'comment'
    ];

    // Relación con el usuario que hizo el comentario
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relación con el reporte
    public function report() {
        return $this->belongsTo(UserReports::class, 'report_id');
    }

    // Relación con el comentario padre (si es respuesta)
    public function parent() {
        return $this->belongsTo(ReportComment::class, 'parent_id');
    }

    // Relación con los comentarios hijos (respuestas)
    public function replies() {
        return $this->hasMany(ReportComment::class, 'parent_id');
    }
}
