<?php

namespace App\Controllers;

use App\Classes\Middlewares;
use App\Classes\Session;
use App\Models\User;
use MVC\Router;
use App\Classes\Validators;

class AuthController extends BaseController
{
    public static function login(Router $router)
    {
        if (Middlewares::isAuth()) {
            header("Location: /dashboard");
            exit;
        }
        $data = [];
        if (Session::has('errores')) {
            $data['errores'] = Session::get('errores');
            Session::delete('errores');
        }
        if (Session::has('exitos')) {
            $data['exitos'] = Session::get('exitos');
            Session::delete('exitos');
        }
        $router->render('auth/login', $data);
    }

    public static function loginProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'email' => $_POST['email'],
                'password' => $_POST['password']
            ];
            $errores = self::validarDatos($data);
            if (!empty($errores)) {
                Session::set('errores', $errores);
                header("Location: /");
                exit;
            }
            $data = self::sanitizateData($data);
            $user = User::where("email", $data['email']);
            if (!$user) {
                Session::set('errores', ["El correo no esta registrado"]);
                header("Location: /");
                exit;
            }
            $userVerify = $user->verifyPassword($data['password']);
            if (!$userVerify) {
                Session::set('errores', ["Contraseña incorrecta."]);
                header("Location: /");
                exit;
            }
            Session::set('user', $user);
            header("Location: /dashboard");
            exit;
        }
    }

    public static function dashboard(Router $router)
    {
        verificarSesion();
        if (!Middlewares::isAuth()) {
            Session::set('errores', ["Tu cuenta no ha sido confirmada"]);
            header("Location: /");
            exit;
        }
        $data = [];
        $data['user'] = Session::get('user');
        $router->render('auth/dashboard', $data);
    }

    public static function logout()
    {
        Session::destroy();
        header('Location: /');
    }
    public static function forgetPassword(Router $router)
    {
        $router->render('auth/forget', []);
    }

    public static function recoveryPassword()
    {
        echo "recuperando la contraseña";
    }

    private static function validarDatos($Data)
    {

        $errores = [];
        $errores[] = self::validarVacios($Data);
        // Revisar formatos
        $errores[] = Validators::email($Data['email'], 'Email');
        // Filtrar valores vacíos para evitar NULLs en el array
        return array_filter($errores);
    }
}
