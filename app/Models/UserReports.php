<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersReports extends Model
{
    protected $table = 'users_reports'; 

    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'latitude',
        'longitude',
        'image',
        'video',
        'fecha_hora_report'
    ];

    // Relación con el modelo User (si existe)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}