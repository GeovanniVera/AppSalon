<?php

namespace App\Controllers;

use App\Classes\Middlewares;
use App\Classes\Session;
use MVC\Router;

class LoginController{
    public static function login(Router $router){
        Middlewares::isAuth();
        
        $data = [];

        if (Session::has('errores')) {
            $data['errores'] = Session::get('errores');
            Session::delete('errores');
        }

        if (Session::has('exitos')) {
            $data['exitos'] = Session::get('exitos');
            Session::delete('exitos');
        }

        $router->render('auth/login',$data);
    }   

    public static function loginProcess(){
        ;
    }

    public static function logout(){
        Session::destroy();
        header('Location: /');
    }
    public static function forgetPassword(Router $router){
        $router->render('auth/forget',[]);
    }

    public static function recoveryPassword(){
        echo "recuperando la contraseña";
    }
}