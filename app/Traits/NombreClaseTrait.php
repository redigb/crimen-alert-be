<?php
namespace App\Traits;
trait NombreClaseTrait
{
    public function getNombreClaseModificado()
    {
        $nombreClase = class_basename(get_class($this));
        $nombreClase = str_replace('Controller', '', $nombreClase);
        $nombreClase = strtolower($nombreClase); 
        $nombreClase .= 's'; 
        return $nombreClase;
    }
}
