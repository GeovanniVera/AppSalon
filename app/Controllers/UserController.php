<?php

namespace App\Controllers;

use App\Models\User;
use MVC\Router;

class UserController extends BaseController
{
    /**
     * Obtiene todos los usuarios y los devuelve en formato JSON.
     *
     * Este método recupera todos los registros de usuarios de la base de datos
     * utilizando el modelo User y los serializa en formato JSON para ser enviados
     * como respuesta a una solicitud HTTP.
     *
     * @return void
     *
     * @throws \Exception Si ocurre un error durante la obtención o serialización de los usuarios.
     *
     * @api
     * @route GET /api/users
     * @access public (o admin, dependiendo de la lógica de permisos)
     */
    public static function getAll()
    {
        verificarSesion();
        try {
            // Obtener todos los usuarios utilizando el método estático all() del modelo User.
            $users = User::all();

            // Inicializar un array para almacenar los usuarios serializados.
            $usersArray = [];

            // Iterar sobre cada usuario obtenido.
            foreach ($users as $user) {
                // Serializar el objeto User a un array asociativo utilizando el método toJson().
                $usersArray[] = $user->toJson();
            }

            // Establecer el encabezado Content-Type para indicar que la respuesta es JSON y está codificada en UTF-8.
            header('Content-Type: application/json; charset=utf-8');

            // Enviar la respuesta JSON con los usuarios serializados.
            echo json_encode($usersArray, JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            // Capturar cualquier excepción que ocurra durante la ejecución del bloque try.

            // Establecer el código de estado HTTP 500 (Error interno del servidor).
            http_response_code(500);

            // Establecer el encabezado Content-Type para indicar que la respuesta es JSON y está codificada en UTF-8.
            header('Content-Type: application/json; charset=utf-8');

            // Enviar una respuesta JSON con un mensaje de error detallado.
            echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Obtiene un usuario por su ID y lo devuelve en formato JSON.
     *
     * Este método recupera un registro de usuario específico de la base de datos
     * utilizando su ID y lo serializa en formato JSON para ser enviado como
     * respuesta a una solicitud HTTP.
     *
     * @param int $id El ID del usuario a recuperar.
     *
     * @return void
     *
     * @throws \Exception Si ocurre un error durante la obtención o serialización del usuario.
     *
     * @api
     * @route GET /api/users/{id}
     * @access public (o admin, dependiendo de la lógica de permisos)
     */
    public static function getUserById(Router $router)
    {
        verificarSesion();
        try {
            // Buscar el usuario por su ID utilizando el método find() del modelo User.
            $id = $router->getParam('id');
            // Validación básica
            if (!ctype_digit($id)) { // Verifica si es numérico
                http_response_code(400);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'ID debe ser numérico'],JSON_UNESCAPED_UNICODE);
                return;
            }
            $user = User::find($id);

            // Verificar si el usuario fue encontrado.
            if (!$user) {
                // Si el usuario no fue encontrado, establecer el código de estado HTTP 404 (No encontrado).
                http_response_code(404);

                // Establecer el encabezado Content-Type para indicar que la respuesta es JSON y está codificada en UTF-8.
                header('Content-Type: application/json; charset=utf-8');

                // Enviar una respuesta JSON con un mensaje de error detallado.
                echo json_encode(['error' => "Usuario $id no encontrado"], JSON_UNESCAPED_UNICODE);

                // Detener la ejecución del método.
                return;
            }

            // Si el usuario fue encontrado, establecer el encabezado Content-Type para indicar que la respuesta es JSON y está codificada en UTF-8.
            header('Content-Type: application/json; charset=utf-8');

            // Serializar el objeto User a un array asociativo utilizando el método toJson() y enviarlo como respuesta JSON.
            echo json_encode($user->toJson(), JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            // Capturar cualquier excepción que ocurra durante la ejecución del bloque try.

            // Establecer el código de estado HTTP 500 (Error interno del servidor).
            http_response_code(500);

            // Establecer el encabezado Content-Type para indicar que la respuesta es JSON y está codificada en UTF-8.
            header('Content-Type: application/json; charset=utf-8');

            // Enviar una respuesta JSON con un mensaje de error detallado.
            echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}
