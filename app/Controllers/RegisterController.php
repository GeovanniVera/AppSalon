<?php

namespace App\controllers;

use MVC\Router;
use App\Models\Usuario;
use App\Includes\Session;

class RegisterController
{
    /**
     * Renderiza la vista de registro de usuario.
     *
     * @param Router $router El enrutador MVC.
     */
    public static function register(Router $router)
    {
        $router->render('auth/register', []);
    }

    /**
     * Guarda un nuevo usuario o actualiza uno existente.
     *
     * Este método maneja la lógica para guardar o actualizar un usuario en la base de datos.
     * Primero, obtiene los datos del formulario, los valida y los sanitiza. Luego, crea una
     * instancia de la clase Usuario, asigna los valores y llama al método save() para
     * guardar o actualizar el registro. Finalmente, maneja el resultado y muestra un mensaje
     * apropiado.
     */
    public static function saveUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Obtener los datos del formulario.

            Session::start();

            if(!isset($_POST['id']) ||$_POST['id'] == '' ){
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
                echo "Error al guardar el usuario: " . implode(', ', $errores);
                header('Location: /');
                return;
            }

            // 3. Sanitizar datos.
            $userData = self::sanitizateData($userData);

            // 4. Crear una instancia de Usuario.
            $user = self::InstanceModel($userData);

            //valida que no exista en la base de datos.
            if(Usuario::findBy($user->getEmail(),'email')){
                Session::set('errores','El usuario ya esta registrado');
                header('Location: /');
                return;
            }
            // 5. Llamar a la función save() para guardar o actualizar el registro.
            $resultado = $user->save();

            // 6. Manejar el resultado.
            if ($resultado['resultado']) {
                if (isset($resultado['id'])) {
                    echo "Usuario creado con éxito. ID: " . $resultado['id'];
                } else {
                    echo "Usuario actualizado con éxito. Filas afectadas: " . $resultado['filas_afectadas'];
                }
            } else {
                echo "Error al guardar el usuario. Error: " . $resultado['error'];
            }
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
    private static function sanitizateData($userData) {
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
        if (empty($userData['nombre'])) {
            $errores[] = "El nombre es obligatorio.";
        }
        if (empty($userData['apellido'])) {
            $errores[] = "El apellido es obligatorio.";
        }
        if (empty($userData['email']) || !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El email no es válido.";
        }
        if (empty($userData['contraseña'])) {
            $errores[] = "La contraseña es obligatoria.";
        }
        return $errores;
    }
}