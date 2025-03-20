<?php

namespace App\Controllers;

use App\Classes\Email;
use App\Classes\Middlewares;
use App\Classes\Validators;
use App\Classes\Session;
use MVC\Router;
use App\Models\User;

class RegisterController
{
    /**
     * Renderiza la vista de registro de usuario.
     *
     * @param Router $router El enrutador MVC.
     */
    public static function register(Router $router)
    {
        $data = [];

        if (Session::has('errores')) {
            $data['errores'] = Session::get('errores');
            Session::delete('errores');
        }

        $router->render('auth/register', $data);
    }

    /**
     * Guarda un nuevo usuario o actualiza uno existente.
     *
     * Este método maneja la lógica para guardar o actualizar un usuario en la base de datos.
     * Primero, obtiene los datos del formulario, los valida y los sanitiza. Luego, crea una
     * instancia de la clase Usuario,revisa que no exista un usuario registrado llamando el metdodo findBy, 
     * asigna los valores y llama al método save() para guardar o actualizar el registro. 
     * Finalmente mmaneja el resultado instancia un objeto de la clase Email y ejecuta el metodo enviar confirmacion,
     * setea un mensaje apropiado y redirecciona a login.
     */
    public static function saveUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Obtener los datos del formulario.
            Middlewares::isAuth();
            Session::start();
            if (!isset($_POST['id']) || $_POST['id'] == '') {
                $id = null;
            }
            
            $userData = [
                'id' => $id, // Obtener el ID si existe (para actualizar).
                'name' => $_POST['name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'password' => $_POST['password']
            ];


            // 2. Validar datos.
            $errores = self::validarDatos($userData);

            if (!empty($errores)) {
                Session::set('errores', $errores);
                header('Location: /register');
                exit;
            }

            // 3. Sanitizar datos.
            $userData = self::sanitizateData($userData);

            // 4. Crear una instancia de Usuario.
            $user = User::arrayToObject($userData);
            $user->generateToken();
            var_dump($user->getEmail());
            $isValidEmail= User::where('email',$user->getEmail());
            //valida que no exista en la base de datos.
            if ($isValidEmail) {
                Session::set('errores', ["ⓘ El usuario {$isValidEmail->getEmail()} ya esta registrado"]);
                header('Location: /register');
                exit;
            }
            // 6. Llamar a la función save() para guardar o actualizar el registro.
            $resultado = User::save($user);

            // 7. Manejar el resultado.
            if ($resultado) {
                $email = new Email($user->getEmail(), $user->getName(), $user->getToken());
                $email->enviarconfirmacion();
                Session::set('exitos', ["Usuario {$user->getEmail()} creado correctamente verifica tu cuenta desde tu correo"]);
                header('Location: /');
                exit;
            }
        }
    }

    public static function confirmAccount() {
        $token = self::sanitizateData($_GET['token']);
        $user = User::where('token',$token);
        if(!$user){
            Session::set('errores', ["Token Invalido"]);
                header('Location: /');
                exit;
        }
        $user->setToken(null);
        $user->setConfirmed(1);
        $res = $user->save($user);
        if($res){
            Session::set('exitos', ["Tu cuenta ha sido confirmada inicia sesion"]);
                header('Location: /');
                exit;
        }
    }

    /**
     * Sanitiza los datos del usuario.
     *
     * Este método utiliza htmlspecialchars() para escapar caracteres especiales y trim() para
     * eliminar espacios en blanco al principio y al final de cada valor.
     *
     * @param array $userData Los datos del usuario.
     * @return array Los datos del usuario sanitizados.
     */
    private static function sanitizateData($userData)
    {
        foreach ($userData as $key => $value) {
            // Sanitización general
            $sanitizedValue = $value !== null
                ? htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8')
                : null;

            // Sanitización especial para 'id'
            if ($key === 'id') {
                $sanitizedValue = ($sanitizedValue !== null && $sanitizedValue !== '')
                    ? (int) $sanitizedValue  // Convertir a entero
                    : null;
            }

            $userData[$key] = $sanitizedValue;
        }
        return $userData;
    }


    /**
     * Valida los datos del usuario.
     *
     * @param array $userData Los datos del usuario.
     * @return array Un array de errores.
     */
    private static function validarDatos($Data)
    {
        $errores = [];

        // Revisar Vacíos
        foreach($Data as $key => $value){
            if($key == 'id') continue;
            $errores[] = Validators::required($value, $key);
        }

        // Revisar formatos
        $errores[] = Validators::alfa($Data['name'], 'Nombre');
        $errores[] = Validators::alfa($Data['last_name'], 'Apellido');
        $errores[] = Validators::email($Data['email'], 'Email');
        $errores[] = Validators::password($Data['password']);
        $errores[] = Validators::telefono($Data['phone']);

        // Filtrar valores vacíos para evitar NULLs en el array
        return array_filter($errores);
    }
}
