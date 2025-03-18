<?php

namespace App\controllers;

use App\Classes\Email;
use App\Classes\Validators;
use App\Classes\Session;
use MVC\Router;
use App\Models\Usuario;

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

            Session::start();

            if (!isset($_POST['id']) || $_POST['id'] == '') {
                $id = null;
            }
            
            $userData = [
                'id' => $id, // Obtener el ID si existe (para actualizar).
                'nombre' => $_POST['name'],
                'apellido' => $_POST['last_name'],
                'email' => $_POST['email'],
                'telefono' => $_POST['phone'],
                'contraseña' => $_POST['password']
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
            $user = self::InstanceModel($userData);

            //valida que no exista en la base de datos.
            if (Usuario::findBy($user->getEmail(), 'email')) {
                Session::set('errores', ['ⓘ El usuario ya esta registrado']);
                header('Location: /register');
                exit;
            }
            // 6. Llamar a la función save() para guardar o actualizar el registro.
            $resultado = $user->save();
            // 7. Manejar el resultado.
            if ($resultado['resultado']) {
                $email = new Email($user->getEmail(), $user->getNombre(), $user->getToken());
                $email->enviarconfirmacion();
                Session::set('exitos', ["Usuario {$user->getEmail()} creado correctamente"]);
                header('Location: /');
                exit;
            }
        }
    }

    public static function confirmAccount() {}

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
     * Crea una instancia de la clase Usuario y asigna los valores.
     *
     * @param array $userData Los datos del usuario.
     * @return Usuario La instancia de la clase Usuario.
     */
    private static function InstanceModel($userData)
    {
        $user = new Usuario();

        // Asignar los valores a las propiedades del objeto.
        $user->setId($userData['id'] ?? null); // Asignar el ID si existe.
        $user->setNombre($userData['nombre']);
        $user->setApellido($userData['apellido']);
        $user->setEmail($userData['email']);
        $user->setTelefono($userData['telefono']);
        $user->setContraseña($userData['contraseña']); // Hash de la contraseña.
        $user->setToken();
        return $user;
    }

    /**
     * Valida los datos del usuario.
     *
     * @param array $userData Los datos del usuario.
     * @return array Un array de errores.
     */
    private static function validarDatos($userData)
    {
        $errores = [];

        // Revisar Vacíos
        $errores[] = Validators::required($userData['nombre'], 'Nombre');
        $errores[] = Validators::required($userData['apellido'], 'Apellido');
        $errores[] = Validators::required($userData['email'], 'Email');
        $errores[] = Validators::required($userData['contraseña'], 'Contraseña');
        $errores[] = Validators::required($userData['telefono'], 'Teléfono');

        // Revisar formatos
        $errores[] = Validators::alfa($userData['nombre'], 'Nombre');
        $errores[] = Validators::alfa($userData['apellido'], 'Apellido');
        $errores[] = Validators::email($userData['email'], 'Email');
        $errores[] = Validators::password($userData['contraseña']);
        $errores[] = Validators::telefono($userData['telefono']);

        // Filtrar valores vacíos para evitar NULLs en el array
        return array_filter($errores);
    }
}
