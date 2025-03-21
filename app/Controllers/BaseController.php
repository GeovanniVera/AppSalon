<?php

namespace App\Controllers;
use App\Classes\Validators;

 class BaseController{
    
    /**
     * Sanitiza los datos del usuario.
     *
     * Este método utiliza htmlspecialchars() para escapar caracteres especiales y trim() para
     * eliminar espacios en blanco al principio y al final de cada valor.
     *
     * @param array $userData Los datos del usuario.
     * @return array Los datos del usuario sanitizados.
     */
    protected static function sanitizateData($Data)
    {
        foreach ($Data as $key => $value) {
            // Sanitización general
            $sanitizedValue = $value !== null
                ? htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8')
                : null;

            // Sanitización especial para 'id'
            if ($key === 'id') {
                $sanitizedValue = ($sanitizedValue !== null && $sanitizedValue !== '')
                    ? (int) $sanitizedValue  // Convertir a entero
                    : null;
            }

            $Data[$key] = $sanitizedValue;
        }
        return $Data;
    }

    public static function validarVacios($data){
        // Revisar Vacíos
        $errores = [];
        foreach($data as $key => $value){
            if($key == 'id') continue;
            $errores[] = Validators::required($value, $key);
        }
        return array_filter($errores);
    }

}