<?php

namespace App\Models;

use App\Classes\Database;
use PDO;
use PDOException;

class ActiveRecord
{
    protected ?int $id;

    protected  static $tabla = '';
    protected  array  $atributosExcluir = ['id'];

    public function __construct()
    {
        self::$tabla = '';
    }


    /**
     * Ejecuta una consulta SQL y retorna los resultados como un array de objetos o null.
     *
     * Esta función utiliza PDO para conectar a la base de datos, preparar y ejecutar consultas SQL.
     * Permite el uso de parámetros nombrados para prevenir inyecciones SQL.
     *
     * @param string $query La consulta SQL a ejecutar.
     * @param array $array Un array asociativo de parámetros para la consulta (opcional).
     * Los parámetros se vinculan a los marcadores de posición nombrados en la consulta.
     * Ejemplo: ['nombre' => 'Juan', 'email' => 'juan@example.com'].
     * @return ?array Un array de objetos creados a partir de los resultados de la consulta,
     * o null si no hay resultados, o un array vacío en caso de error.
     * @throws PDOException Si ocurre un error durante la ejecución de la consulta.
     */
    public static function consultarSQL($query, $array = []): ?array
    {
        try {
            $conn = Database::getInstance()->getConnection();
            $stmt = $conn->prepare($query);

            // Vincular los parámetros a la consulta preparada (si se proporcionan).
            if ($array) {
                foreach ($array as $key => $value) {
                    $stmt->bindValue(":$key", $value);
                }
            }
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$resultados) {
                return null;
            }
            // Crear un array de objetos a partir de los resultados de la consulta.
            $array = [];
            foreach ($resultados as $registro) {
                $array[] = static::crearObjeto($registro);
            }
            return $array;
        } catch (PDOException $e) {
            // 9. Manejar errores de PDO.
            error_log("Error en la consulta SQL: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crea un objeto en memoria a partir de un array asociativo que representa un registro de la base de datos.
     *
     * Esta función toma un array asociativo ($registro) y crea una instancia de la clase actual (static).
     * Luego, itera sobre los elementos del array y asigna los valores a las propiedades correspondientes del objeto,
     * siempre que la propiedad exista en el objeto.
     *
     * @param array $registro Un array asociativo que representa un registro de la base de datos.
     * Las claves del array deben coincidir con los nombres de las propiedades del objeto.
     * @return static Una instancia de la clase actual con las propiedades asignadas a partir del registro.
     */
    protected static function crearObjeto($registro)
    {
        // 1. Crear una nueva instancia de la clase actual (static) Esto crea un objeto vacío en memoria.
        $objeto = new static;
        foreach ($registro as $key => $value) {
            if (property_exists($objeto, $key)) {
                $objeto->$key = $value;
            }
        }
        return $objeto;
    }


    /**
     * Obtiene los atributos de un objeto y sus valores correspondientes, con opciones para incluir o excluir atributos.
     *
     * Esta función permite recuperar los atributos de un objeto y sus valores en un array asociativo.
     * Se pueden especificar opciones para incluir o excluir atributos específicos.
     *
     * @param bool $incluirExcluidos un boolean true ignora exclusiones, false filtra los valores excluidos
     * @param array $opciones Un array asociativo de opciones para controlar qué atributos incluir o excluir.
     * Opciones disponibles:
     * - 'incluir' => array de nombres de atributos a incluir.
     * - 'excluir' => array de nombres de atributos a excluir.
     * @return array Un array asociativo donde las claves son los nombres de los atributos y los valores son los valores correspondientes.
     */
    public function atributos($opciones = [], $incluirExcluidos = false)
    {
        $atributos = [];
        $propiedades = get_object_vars($this);


        // Internal properties that should NEVER be in the database
        $exclusionesInternas = ['atributosExcluir', 'tabla']; // Add others here if needed

        // Default exclusions (like 'id') unless $incluirExcluidos is true
        $exclusiones = $incluirExcluidos
            ? []
            : $this->atributosExcluir;

        // Merge all exclusions (internal + default + options)
        $exclusiones = array_merge($exclusiones, $exclusionesInternas);

        if (isset($opciones['excluir'])) {
            $exclusiones = array_merge($exclusiones, $opciones['excluir']);
        }

        foreach ($propiedades as $columna => $valor) {
            if (in_array($columna, $exclusiones)) {
                continue; // Skip excluded properties
            }

            if (isset($opciones['incluir']) && !in_array($columna, $opciones['incluir'])) {
                continue; // Skip if not in inclusion list
            }

            $atributos[$columna] = $valor;
        }



        return $atributos;
    }


    /**
     * Guarda o actualiza un registro en la base de datos según si el objeto tiene un ID asignado.
     *
     * Si el objeto tiene un ID asignado, se llama al método `actualizar()` para actualizar el
     * registro existente. Si el objeto no tiene un ID asignado, se llama al método `crear()`
     * para crear un nuevo registro.
     *
     * @return array Un array asociativo con el resultado de la operación. El formato del array
     * depende del método llamado (crear() o actualizar()).
     *
     * Para crear():
     * [
     * 'resultado' => bool, // true si la inserción fue exitosa, false si falló.
     * 'id' => int|null, // ID del registro insertado si la inserción fue exitosa, null si falló.
     * 'error' => string|null // Mensaje de error si la inserción falló, null si fue exitosa.
     * ]
     *
     * Para actualizar():
     * [
     * 'resultado' => bool, // true si la actualización fue exitosa, false si falló.
     * 'filas_afectadas' => int|null, // Número de filas afectadas si la actualización fue exitosa, null si falló.
     * 'error' => string|null // Mensaje de error si la actualización falló, null si fue exitosa.
     * ]
     */
    public function save()
    {

        $resultado = [];
        if (!is_null($this->id)) {
            // Actualizar registro existente.
            $resultado = $this->actualizar();
        } else {
            // Crear nuevo registro.
            $resultado = $this->crear();
        }
        return $resultado;
    }

    /**
     * Crea un nuevo registro en la base de datos utilizando los atributos del objeto actual.
     *
     * Esta función obtiene los atributos del objeto, excluyendo el 'id' y los atributos definidos
     * en la propiedad $atributosExcluir de la clase, y construye una consulta SQL INSERT
     * para insertar un nuevo registro en la tabla correspondiente. Utiliza PDO para preparar y
     * ejecutar la consulta de manera segura, vinculando los valores de los atributos a los
     * marcadores de posición nombrados.
     *
     * @return array Un array asociativo con el resultado de la operación y el ID del registro insertado,
     * o un array con el resultado y un mensaje de error si la inserción falla.
     * Formato del array de retorno:
     * [
     * 'resultado' => bool, // true si la inserción fue exitosa, false si falló.
     * 'id' => int|null, // ID del registro insertado si la inserción fue exitosa, null si falló.
     * 'error' => string|null // Mensaje de error si la inserción falló, null si fue exitosa.
     * ]
     */
    public function crear()
    {
        try {
            $conn = Database::getInstance()->getConnection();
            $atributos = $this->atributos([], false);

            // Validar atributos y tabla
            if (empty($atributos)) {
                throw new \RuntimeException("No hay atributos para insertar.");
            }
            if (empty(self::$tabla)) {
                throw new \RuntimeException("La tabla no está definida.");
            }

            // Construir consulta
            $columnas = array_keys($atributos);
            $marcadores = ':' . implode(', :', $columnas);
            $query = "INSERT INTO " . self::$tabla . " (" . implode(', ', $columnas) . ") VALUES ($marcadores)";

            // Preparar y vincular
            $stmt = $conn->prepare($query);
            foreach ($atributos as $columna => $valor) {
                $stmt->bindValue(":$columna", $valor, self::getTipoParametro($valor));
            }

            // Ejecutar y verificar resultado
            if ($stmt->execute()) {
                $this->id = $conn->lastInsertId();
                return ['resultado' => true, 'object' => self::find($this->id)];
            } else {
                error_log("Error en la consulta: " . print_r($stmt->errorInfo(), true));
                return ['resultado' => false, 'error' => $stmt->errorInfo()[2] ?? 'Error desconocido'];
            }
        } catch (PDOException $e) {
            error_log("Error PDO: " . $e->getMessage());
            return ['resultado' => false, 'error' => $e->getMessage()];
        } catch (\RuntimeException $e) {
            error_log("Error de lógica: " . $e->getMessage());
            return ['resultado' => false, 'error' => $e->getMessage()];
        }
    }


    // Método auxiliar para determinar el tipo de parámetro
    private static function getTipoParametro($valor)
    {
        switch (true) {
            case is_int($valor):
                return PDO::PARAM_INT;
            case is_bool($valor):
                return PDO::PARAM_BOOL;
            case is_null($valor):
                return PDO::PARAM_NULL;
            default:
                return PDO::PARAM_STR;
        }
    }

    /**
     * Actualiza un registro existente en la base de datos utilizando los atributos del objeto actual.
     *
     * Esta función obtiene todos los atributos del objeto, incluyendo los excluidos, y construye
     * una consulta SQL UPDATE para actualizar el registro correspondiente en la tabla. Utiliza
     * PDO para preparar y ejecutar la consulta de manera segura, vinculando los valores de los
     * atributos a los marcadores de posición nombrados.
     *
     * @return array Un array asociativo con el resultado de la operación y el número de filas
     * afectadas, o un array con el resultado y un mensaje de error si la actualización falla.
     * Formato del array de retorno:
     * [
     * 'resultado' => bool, // true si la actualización fue exitosa, false si falló.
     * 'filas_afectadas' => int|null, // Número de filas afectadas si la actualización fue exitosa, null si falló.
     * 'error' => string|null // Mensaje de error si la actualización falló, null si fue exitosa.
     * ]
     */
    public function actualizar()
    {
        // 1. Obtener la conexión a la base de datos utilizando el patrón Singleton.
        $conn = Database::getInstance()->getConnection();

        // 2. Obtener todos los atributos, incluyendo los excluidos, luego excluir 'id'.
        $atributos = $this->atributos([], true);
        unset($atributos['id']);

        // 3. Construir la consulta SQL UPDATE con marcadores de posición nombrados.
        $columnas = array_keys($atributos);
        $setClauses = [];
        foreach ($columnas as $columna) {
            $setClauses[] = $columna . " = :" . $columna;
        }

        // 4. Construir la consulta SQL UPDATE completa.
        $query = "UPDATE " . self::$tabla . " SET " . implode(', ', $setClauses) . " WHERE id = :id";

        $stmt = $conn->prepare($query);

        // 5. Vincular los valores de los atributos a los marcadores de posición nombrados.
        foreach ($atributos as $columna => $valor) {
            $stmt->bindValue(":" . $columna, $valor);
        }
        $stmt->bindValue(":id", $this->id); // Vincular el ID para la cláusula WHERE.

        // 6. Ejecutar la sentencia preparada.
        $resultado = $stmt->execute();

        // 7. Verificar el resultado de la ejecución y retornar el resultado correspondiente.
        if ($resultado) {
            return [
                'resultado' => true,
                'filas_afectadas' => $stmt->rowCount()
            ];
        } else {
            return [
                'resultado' => false,
                'error' => "Error al actualizar el registro."
            ];
        }
    }

    /**
     * Obtiene todos los registros de la tabla correspondiente.
     *
     * Esta función ejecuta una consulta SQL SELECT para obtener todos los registros de la tabla
     * definida en la propiedad estática `$this->tabla`. Utiliza el método `consultarSQL()`
     * para ejecutar la consulta y retornar los resultados como un array de objetos.
     *
     * @return array|null Un array de objetos que representan los registros de la tabla,
     * o null si la consulta falla o no hay resultados.
     */
    public static function all()
    {
        // 1. Construir la consulta SQL SELECT para obtener todos los registros.
        $query = "SELECT * FROM " . self::$tabla;

        // 2. Ejecutar la consulta utilizando el método consultarSQL().
        $resultado = self::consultarSQL($query);

        // 3. Retornar el resultado de la consulta.
        return $resultado;
    }

    /**
     * Busca un registro en la tabla por su ID.
     *
     * Esta función construye una consulta SQL SELECT para obtener un registro específico de la
     * tabla definida en la propiedad estática `$this->tabla`, utilizando el ID proporcionado.
     * Utiliza el método `consultarSQL()` para ejecutar la consulta con un parámetro nombrado
     * `:id` y retorna el primer registro encontrado (si existe) como un objeto.
     *
     * @param int $id El ID del registro a buscar.
     * @return object|null El objeto que representa el registro encontrado, o null si no se encuentra
     * ningún registro con el ID proporcionado o si la consulta falla.
     */
    public static function find($id)
    {
        // 1. Construir la consulta SQL SELECT para buscar un registro por su ID.
        $query = "SELECT * FROM " . self::$tabla . " WHERE id = :id";

        // 2. Ejecutar la consulta utilizando el método consultarSQL() con el ID como parámetro.
        $resultado = self::consultarSQL($query, ['id' => $id]);

        // 3. Retornar el primer registro encontrado (si existe).
        // array_shift() extrae el primer elemento del array y lo retorna.
        return array_shift($resultado);
    }

    /**
     * Busca un registro en la base de datos en función de un valor y un campo específico.
     *
     * Este método ejecuta una consulta SQL para encontrar un único registro en la tabla asociada a la clase
     * mediante la búsqueda de un valor específico en un campo determinado. Si se encuentra un registro, 
     * lo devuelve como un arreglo asociativo. Si no se encuentra ningún registro, devuelve null. En caso
     * de error durante la ejecución de la consulta, se captura la excepción y se retorna un arreglo con el
     * detalle del error.
     *
     * @param mixed $value El valor a buscar en la base de datos. Puede ser un string, número o cualquier otro tipo
     *                     de dato soportado por la base de datos.
     * @param string $field El nombre del campo en la base de datos en el que se realizará la búsqueda.
     * 
     * @return array|null Si se encuentra un registro, devuelve un arreglo asociativo con los datos del registro.
     *                    Si no se encuentra un registro, devuelve null.
     *                    Si ocurre un error, devuelve un arreglo con el estado `resultado` como `false` 
     *                    y el mensaje de error.
     *
     * @throws PDOException Si ocurre un error en la consulta SQL, el error será registrado y se devuelve un arreglo
     *                      con el detalle del error.
     */
    public static function findBy($value, $field)
    {
        try {
            $query = "SELECT * FROM " . self::$tabla . " WHERE $field = :$field LIMIT 1";
            $conn = Database::getInstance()->getConnection();
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":$field", $value, self::getTipoParametro($value));
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si se encuentra un resultado, lo devuelve
            if ($resultado) {
                return $resultado;
            }

            return null;
        } catch (PDOException $e) {
            // Si ocurre un error, se registra y se devuelve un arreglo con el error
            error_log("Error PDO: " . $e->getMessage());
            return ['resultado' => false, 'error' => $e->getMessage()];
        }
    }





    /**
     * Obtiene un número limitado de registros de la tabla, utilizando sentencias preparadas y manejo de excepciones.
     *
     * Esta función construye una consulta SQL SELECT para obtener un número específico de
     * registros de la tabla definida en la propiedad estática `$this->tabla`, utilizando
     * la cláusula `LIMIT` y un parámetro nombrado `:limite`. Utiliza el método `consultarSQL()`
     * para ejecutar la consulta de manera segura y retornar los resultados como un array de objetos.
     *
     * @param int $limite El número máximo de registros a obtener.
     * @return array Un array de objetos que representan los registros encontrados,
     * o null si no hay resultados, o un array vacío en caso de error.
     */
    public static function get($limite)
    {
        $query = "SELECT * FROM " . self::$tabla . " LIMIT :limite";
        return self::consultarSQL($query, ['limite' => $limite]);
    }


    /**
     * Elimina el registro actual de la base de datos.
     *
     * Esta función construye una consulta SQL DELETE para eliminar el registro correspondiente
     * al objeto actual de la tabla definida en la propiedad estática `$this->tabla`. Utiliza
     * el ID del objeto actual para identificar el registro a eliminar.
     *
     * @return bool true si la eliminación fue exitosa, false si falló.
     */
    public function eliminar()
    {
        if (is_null($this->id)) {
            return false;
        }
        $conn = Database::getInstance()->getConnection();
        $query = "DELETE FROM " . $this->tabla . " WHERE id = :id LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $resultado = $stmt->execute();
        return $resultado;
    }
}
