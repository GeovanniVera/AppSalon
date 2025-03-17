<?php

namespace App\controllers;

use MVC\Router;

class LoginController{
    public static function login(Router $router){
        $router->render('auth/login',[]);
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