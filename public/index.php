<?php 

require_once __DIR__ . '/../app/Includes/app.php';

use App\Controllers\AuthController;
use App\Controllers\RegisterController;
use App\Controllers\UserController;
use MVC\Router;

$router = new Router();

// Login
$router->get('/', [AuthController::class, 'login']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'loginProcess']);
$router->get('/logout', [AuthController::class, 'logout']);

// Recuperar password
$router->get('/forgetPassword', [AuthController::class, 'forgetPassword']);
$router->post('/forgetPassword', [AuthController::class, 'forgetPassword']);
$router->get('/recoveryPassword', [AuthController::class, 'recoveryPassword']);
$router->post('/recoveryPassword', [AuthController::class, 'recoveryPassword']);

// Crear Cuenta
$router->get('/register', [RegisterController::class, 'register']);
$router->post('/register', [RegisterController::class, 'saveUser']);

// Confirmar Cuenta
$router->get('/confirmAccount', [RegisterController::class, 'confirmAccount']);

// Pagina principal
$router->get('/dashboard', [AuthController::class, 'dashboard']);

// Usuarios Api
$router->get('/api/users', [UserController::class, 'getAll']);
$router->get('/api/users/{id}', [UserController::class, 'getUserById']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();