<?php

namespace App\controllers;
use App\Classes\Session;
use MVC\Router;

class LoginController{
    public static function login(Router $router){
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
        var_dump($_POST);
    }

    public static function logout(){
        echo "Cerrando Sesion";
    }
    public static function forgetPassword(Router $router){
        $router->render('auth/forget',[]);
    }

    public static function recoveryPassword(){
        echo "recuperando la contraseña";
    }
}