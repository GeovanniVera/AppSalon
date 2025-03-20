<?php

namespace App\Models;

use App\Classes\Database;
use PDO;
use PDOException;
use InvalidArgumentException;

class ActiveRecord
{
    protected ?int $id = null;



    /**
     * Metodo para obtener el nombre de la tabla
     * funciona para los metodos estaticos que se conectan a 
     * la base de datos
     * */

    public static function getTable()
    {
        return null;
    }

    /**
     * Metodos estaticos para las solicitudes a la base de datos
     * -all -> recupera todos los registros de la tabla
     * -find -> recupera un regostro por id
     * -where -> recupera un registro usando un campo y valor ej id,1
     */

    /**
     * @return ?array retorna un arreglo de objetos 
     */
    public static function all(): ?array
    {

        try {
            $conn = Database::getInstance()->getConnection();
            $query = "SELECT * FROM " . static::getTable();
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$res) {
                return [];
            }

            $resultado = [];
            foreach ($res as $row) {
                $resultado[] = static::arrayToObject($row);
            }
            return $resultado;
        } catch (PDOException $e) {
            $message = "Error al recuper los registros de la base de datos:  {$e->getMessage()} en la linea {$e->getLine()}";
            error_log($message);
            return null;
        }
    }

    /**
     * Metodo find($id) -> este metodo recupera un registro de la base de datos por su id
     * @param int $id -> es el id por el que se recuperara el registro
     * @return ?object -> retorna un objeto si existe el registro, null si no existe el registro 
     */

    public static function find(int $id)
    {
        try {
            $query = "SELECT * FROM " . self::getTable() . " WHERE id = :id";
            $conn = Database::getInstance()->getConnection();
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($res) {
                return null;
            }
            $resultado = static::arrayToObject($res);
            return $resultado;
        } catch (PDOException $e) {
            $message = "Error al recuper el registro la base de datos:  {$e->getMessage()} en la linea {$e->getLine()}";
            error_log($message);
            return null;
        }
    }

    /**
     * Metodo where($field,$value) -> recupera un registro de la base de datos filtrando por campo y valor
     * @param mixed $value -> valor por el que se filtrara
     * @param string $field -> campo por el que se filtrara
     * @return object objeto recuperado de la base de datos, null si no existe el registro
     */
    public static function where($field, $value)
    {
        try {
            $query = "SELECT * FROM " . static::getTable() . " WHERE $field = :$field LIMIT 1";
            $conn = Database::getInstance()->getConnection();
            $stmt = $conn->prepare($query);
            $paramType = static::getValueParam($value);
            $stmt->bindValue(":$field", $value,$paramType);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$res){
                return null;
            }
            $obj = static::arrayToObject($res);
            
            return $obj;
        } catch (PDOException $e) {
            $message = "Error al recuper el registro la base de datos:  {$e->getMessage()} en la linea {$e->getLine()}";
            error_log($message);
            return null;
        }
    }

    /**
     * Metodos estaticos para solicitudes a la base de datos
     * -save()
     * -create()
     * -update()
     * -delete()
     * 
     */

     public static function save(object $model){
        if(!is_null($model->getId()))
        {
           //self::update($model); 
           return static::update($model);
        }
        else
        {
            //self::create($model);
            return static::create($model);
        }
    }

    /**
     * Metodo crear -> recibe un objeto modelo y crea un registro en la base de datos.
     * @param object $model
     * @return bool
     */
    public static function create(object $model): bool
    {
        try {
            $conn = Database::getInstance()->getConnection();
            $properties = get_object_vars($model); // Obtener las propiedades del objeto

            $keys = implode(', ', array_keys($properties));
            $placeholders = ':' . implode(', :', array_keys($properties));

            $query = "INSERT INTO " . static::getTable() . " (" . $keys . ") VALUES (" . $placeholders . ")";
            $stmt = $conn->prepare($query);

            foreach ($properties as $key => $value) {
                $paramType = static::getValueParam($value);
                $stmt->bindValue(':' . $key, $value, $paramType);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

   
    /**
     * Metodo update -> recibe un objeto modelo y actualiza un registro en la base de datos.
     * @param object $model
     * @return bool
     */
    public static function update(object $model): bool
    {
        try {
            $conn = Database::getInstance()->getConnection();
            $properties = get_object_vars($model);

            // Verificar si el modelo tiene un ID
            if (!isset($properties['id']) || empty($properties['id'])) {
                throw new InvalidArgumentException("El modelo debe tener un ID para actualizar.");
            }

            $id = $properties['id'];
            unset($properties['id']); // Eliminar el ID de las propiedades a actualizar

            $setClause = '';
            $params = [];
            foreach ($properties as $key => $value) {
                $setClause .= "$key = :$key, ";
                $params[":$key"] = $value;
            }
            $setClause = rtrim($setClause, ', '); // Eliminar la coma extra

            $query = "UPDATE " . static::getTable() . " SET " . $setClause . " WHERE id = :id";
            $stmt = $conn->prepare($query);

            // Vincular los parámetros
            foreach ($params as $key => $value) {
                $paramType = static::getValueParam($value);
                $stmt->bindValue($key, $value, $paramType);
            }
            $stmt->bindValue(':id', $id, PDO::PARAM_INT); // Vincular el ID

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    /**
     * Metodos estaticos auxiliares para el manejo de datos, mapeos o conversiones
     * -arrayToObject -> Metodo que mapea un arreglo a un objeto
     * -getValueParams -> metodo que evalua el tipo de parametro que se va a usar
     */


    /**
     * Convierte un array asociativo en un objeto de la clase actual,
     * utilizando setters para asignar los valores a las propiedades.
     *
     * Este método estático toma un array asociativo y crea una nueva instancia
     * de la clase actual. Luego, itera a través del array y, para cada par
     * clave-valor, intenta llamar al setter correspondiente para asignar el valor
     * a la propiedad del objeto.
     *
     * La clave del array se convierte de snake_case a camelCase para construir
     * el nombre del setter. Por ejemplo, la clave 'last_name' se convierte en
     * 'setLastName'.
     *
     * @param array $data El array asociativo que contiene los datos para el objeto.
     * @return object Una instancia de la clase actual con las propiedades asignadas.
     *
     * @example
     * // Ejemplo de uso:
     * class User extends ActiveRecord {
     * private $firstName;
     * private $lastName;
     *
     * public function setFirstName($firstName) {
     * $this->firstName = $firstName;
     * }
     *
     * public function setLastName($lastName) {
     * $this->lastName = $lastName;
     * }
     *
     * protected static function getTable() {
     * return 'users';
     * }
     * }
     *
     * $data = [
     * 'first_name' => 'John',
     * 'last_name' => 'Doe'
     * ];
     *
     * $user = User::arrayToObject($data);
     *
     * echo $user->getFirstName(); // John
     * echo $user->getLastName(); // Doe
     */
    public static function arrayToObject(array $data): object
    {
        $model = new static;
        foreach ($data as $key => $value) {
            $camelCaseKey = lcfirst(str_replace('_', '', ucwords($key, '_')));
            $setter = 'set' . ucfirst($camelCaseKey);
            if (method_exists($model, $setter)) {
                $model->$setter($value);
            }
        }
        return $model;
    }

    /**
     * Metodo getValueParams -> evalua el valor y recupera el tipo de parametro pdo
     * @param mixed $value
     * @return int
     */
    public static function getValueParam(mixed $value): int
    {
        if (is_int($value)) {
            return PDO::PARAM_INT;
        } elseif (is_bool($value)) {
            return PDO::PARAM_BOOL;
        } elseif (is_null($value)) {
            return PDO::PARAM_NULL;
        } else {
            return PDO::PARAM_STR;
        }
    }
}
