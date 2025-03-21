<?php
namespace MVC;

use App\Classes\Session;

/**
 * Clase Router - Maneja el enrutamiento de la aplicación y parámetros dinámicos
 * 
 * Proporciona sistema de rutas estáticas y dinámicas con parámetros nombrados
 * Ejemplo: /api/users/{id} captura el ID como parámetro
 */
class Router
{
    /**
     * @var array $getRoutes - Rutas registradas para método GET
     * @var array $postRoutes - Rutas registradas para método POST
     * @var array $params - Parámetros capturados de la URL
     */
    public array $getRoutes = [];
    public array $postRoutes = [];
    public array $params = [];

    /**
     * Registra una ruta GET
     * @param string $url - Patrón de la ruta (puede contener {parametros})
     * @param mixed $fn - Función anónima o array [Clase, método]
     */
    public function get($url, $fn)
    {
        $this->getRoutes[] = [
            'pattern' => $url,          // Patrón original (ej: /users/{id})
            'regex' => $this->convertPatternToRegex($url), // Regex generado
            'params' => $this->extractParamNames($url),    // Nombres de parámetros
            'fn' => $fn                  // Función asociada
        ];
    }

    /**
     * Registra una ruta POST
     * @param string $url - Patrón de la ruta (puede contener {parametros})
     * @param mixed $fn - Función anónima o array [Clase, método]
     */
    public function post($url, $fn)
    {
        $this->postRoutes[] = [
            'pattern' => $url,
            'regex' => $this->convertPatternToRegex($url),
            'params' => $this->extractParamNames($url),
            'fn' => $fn
        ];
    }

    /**
     * Convierte un patrón de ruta en una expresión regular
     * @param string $pattern - Patrón de ruta (ej: /users/{id})
     * @return string - Expresión regular compatible (ej: #^/users/(?<id>[^/]+)$#)
     */
    private function convertPatternToRegex($pattern)
    {
        // Escapar caracteres especiales del patrón
        $regex = preg_quote($pattern, '#');
        
        // Reemplazar {param} con grupo regex nombrado
        $regex = preg_replace(
            '/\\\{([a-zA-Z0-9_]+)\\\}/', // Buscar {param}
            '(?<$1>[^/]+)',                // Reemplazar con grupo nombrado
            $regex
        );
        
        return '#^' . $regex . '$#';
    }

    /**
     * Extrae nombres de parámetros de un patrón de ruta
     * @param string $pattern - Patrón de ruta (ej: /posts/{slug}/comments/{id})
     * @return array - Nombres de parámetros (ej: ['slug', 'id'])
     */
    private function extractParamNames($pattern)
    {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $pattern, $matches);
        return $matches[1]; // Retorna solo los nombres de los grupos
    }

    /**
     * Comprueba y ejecuta la ruta solicitada
     * 
     * 1. Inicia la sesión
     * 2. Determina URL y método actual
     * 3. Busca coincidencia en rutas registradas
     * 4. Captura parámetros dinámicos
     * 5. Ejecuta la función asociada
     * 6. Maneja errores y rutas no encontradas
     */
    public function comprobarRutas()
    {
        Session::start();

        // Obtener URL actual (PATH_INFO es más confiable que REQUEST_URI)
        $currentUrl = $_SERVER['PATH_INFO'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        // Seleccionar rutas según el método HTTP
        $routes = $method === 'GET' ? $this->getRoutes : $this->postRoutes;

        foreach ($routes as $route) {
            // Comparar URL con regex de la ruta
            if (preg_match($route['regex'], $currentUrl, $matches)) {
                
                // Construir array de parámetros con nombres
                $params = [];
                foreach ($route['params'] as $paramName) {
                    $params[$paramName] = $matches[$paramName];
                }
                $this->params = $params;

                // Obtener función/callback a ejecutar
                $fn = $route['fn'];

                try {
                    // Ejecutar función del controlador
                    if (is_array($fn)) {
                        // Si es array [Clase, método], crear instancia y llamar método
                        call_user_func([new $fn[0], $fn[1]], $this);
                    } else {
                        // Si es función anónima, llamar directamente
                        call_user_func($fn, $this);
                    }
                } catch (\Throwable $th) {
                    // Manejo de errores en ejecución
                    echo "Ocurrió un error interno" ." Error: " . "$th";
                    error_log("Error en la ruta: " . $currentUrl . ". Error: " . $th);
                }

                return; // Salir tras encontrar coincidencia
            }
        }

        // Ninguna ruta coincidió - Error 404
        http_response_code(404);
        echo "Página No Encontrada";
    }

    /**
     * Obtiene un parámetro de la URL por nombre
     * @param string $name - Nombre del parámetro
     * @return mixed|null - Valor del parámetro o null si no existe
     */
    public function getParam($name)
    {
        return $this->params[$name] ?? null;
    }

    /**
     * Renderiza una vista con layout
     * @param string $view - Nombre del archivo de vista (sin .php)
     * @param array $datos - Variables para inyectar en la vista
     * 
     * Uso en controladores:
     * $router->render('vista', ['titulo' => 'Inicio']);
     */
    public function render($view, $datos = [])
    {
        // Extraer variables del array $datos a variables individuales
        foreach ($datos as $key => $value) {
            $$key = $value; // Variable variable (ej: $titulo = 'Inicio')
        }

        // Almacenar vista en buffer
        ob_start();
        include_once __DIR__ . "/app/views/$view.php";
        $contenido = ob_get_clean(); // Obtener contenido del buffer
        
        // Incluir layout principal
        include_once __DIR__ . '/app/views/layout.php';
    }
}