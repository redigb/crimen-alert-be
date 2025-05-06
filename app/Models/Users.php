<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Notifications\Notifiable;

class Users extends Model
{
    // Token
    use HasApiTokens, Notifiable, HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'is_premium',   
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*protected $casts = [
        'email_verified_at' => 'datetime',
    ];*/
}
