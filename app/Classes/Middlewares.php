<?php 
namespace App\Classes;

use App\Classes\Session;

class Middlewares{
    public static function isAuth(){
        Session::start();//inicia la sesion si no esta iniciada
        if(Session::has('user')){
            header('Location: /dashboard');
            exit;
        }
    }
}