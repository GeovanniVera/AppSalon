<?php
namespace App\Classes;

use App\Classes\Session;

class Middlewares {
    
    public static function isAuth($requiredRole = null) {
        Session::start();
        if (Session::has('user')) {
            $user = Session::get('user');
            if (is_object($user)) {
                // Verificar si el usuario está autenticado y confirmado
                if (!method_exists($user, 'getConfirmed') || $user->getConfirmed() !== 1) {
                    return false;
                }
    
                // Verificar el rol si se proporciona
                if ($requiredRole !== null) {
                    switch ($requiredRole) {
                        case 'admin':
                            if (!method_exists($user, 'getAdmin') || $user->getAdmin() !== 1) {
                                return false;
                            }
                            break;
                    }
                }
    
                return true; // Usuario autenticado y con el rol requerido (si aplica)
            }
        }
        return false; // Usuario no autenticado
    }
}
?>