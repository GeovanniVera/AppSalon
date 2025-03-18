<?php

namespace App\Classes;

class Validators
{
    public static function required($value, $field)
    {
        return empty($value) ? "$field obligatorio." : "";
    }

    public static function alfa($value, $field)
    {
        return !preg_match('/^[a-zA-ZáéíóúüñÁÉÍÓÚÜÑ\s]+$/', $value) 
            ? "El campo $field solo puede contener letras y espacios." 
            : "";
    }

    public static function email($value, $fieldName)
    {
        return !filter_var($value, FILTER_VALIDATE_EMAIL) 
            ? "El campo $fieldName debe ser un correo electrónico válido." 
            : "";
    }

    public static function isInt($value, $fieldName)
    {
        return !filter_var($value, FILTER_VALIDATE_INT) 
            ? "El $fieldName debe de ser un número entero" 
            : "";
    }

    public static function password($value)
    {
        return !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+.\-]).{8,}$/', $value) 
            ? "La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, un número y un símbolo especial." 
            : "";
    }

    public static function telefono($value)
    {
        return !preg_match('/^\+52\d{10}$/', $value) 
            ? "El formato del teléfono debe ser como el siguiente: +525611889450." 
            : "";
    }
}

