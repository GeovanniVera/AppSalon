<?php
use App\Classes\Session;

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

function verificarSesion() {
    Session::start();
    if (!Session::has('user')) {
        Session::set('errores', ["Debes iniciar sesión"]);
        header("Location: /");
        exit;
    }
}