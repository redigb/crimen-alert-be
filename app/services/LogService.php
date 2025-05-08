<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LogService
{
    public function registrarInfo($mensaje){
        Log::channel('daily_custom')->info(now()->format('[Y-m-d H:i:s]') . ' ' . $mensaje);
    }

    public function registrarError($mensaje){
        Log::channel('daily_custom')->error(now()->format('[Y-m-d H:i:s]') . ' ' . $mensaje);
    }

    public function registrarWarning($mensaje){
        Log::channel('daily_custom')->warning(now()->format('[Y-m-d H:i:s]') . ' ' . $mensaje);
    }
}
