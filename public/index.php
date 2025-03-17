<?php 

require_once __DIR__ . '/../app/Includes/app.php';

use App\controllers\LoginController;
use App\controllers\RegisterController;
use MVC\Router;

$router = new Router();

//Login 
$router->get('/',[LoginController::class,'login']);
$router->post('/',[LoginController::class,'loginProcess']);
$router->get('/logout',[LoginController::class,'logout']);

//Recuperar password
$router->get('/forgetPassword',[LoginController::class,'forgetPassword']);
$router->post('/forgetPassword',[LoginController::class,'forgetPassword']);
$router->get('/recoveryPassword',[LoginController::class,'recoveryPassword']);
$router->post('/recoveryPassword',[LoginController::class,'recoveryPassword']);

//Crear Cuenta
$router->get('/register',[RegisterController::class,'register']);
$router->post('/register',[RegisterController::class,'saveUser']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();