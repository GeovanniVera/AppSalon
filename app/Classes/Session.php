<?php
namespace App\Classes;
// app/core/Session.php
class Session {
    // Iniciar la sesión
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Establecer un valor en la sesión
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    // Obtener un valor de la sesión
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    // Verificar si una clave existe en la sesión
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }

    // Eliminar una clave de la sesión
    public static function delete($key) {
        self::start();
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }

    // Destruir la sesión
    public static function destroy() {
        self::start();
        session_unset();  // Elimina todas las variables de sesión
        session_destroy(); // Destruye la sesión
    }

    // Regenerar el ID de la sesión (para seguridad)
    public static function regenerate() {
        self::start();
        session_regenerate_id(true);
    }
}
?>