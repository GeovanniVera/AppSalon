<?php

namespace App\Models;

use InvalidArgumentException;


class User extends ActiveRecord
{

    protected ?int $id = null;
    protected int $admin = 0;
    protected int $confirmed = 0;
    protected int $status = 1;
    protected string $name; //obligatorio en la base de datos
    protected string $lastName; //obligatorio en la base de datos
    protected string $email; //obligatorio en la base de datos
    protected string $phone; //obligatorio en la base de datos
    protected string $password; //obligatorio en la base de datos
    protected ?string $token;
    protected string $dateCreation;

    public static function getTable()
    {
        return "users";
    }

    /**
     * Establece el id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * Recupera el ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * Establece Admin 1 si es Admin 2 si no 
     * 
     */
    public function setAdmin($admin): void
    {
        $this->validateValues($admin);
        $this->admin = $admin;
    }
    /**
     * Recupera el Admin
     */
    public function getAdmin(): int
    {
        return $this->admin;
    }
    /**
     * Establece el estado de la Confirmado 1 es confirmado 0 es no confirmado
     */
    public function setConfirmed($confirmed): void
    {
        $this->validateValues($confirmed);
        $this->confirmed = $confirmed;
    }
    /**
     * Recupera el estado del correo 
     */
    public function getConfirmed(): int
    {
        return $this->confirmed;
    }
    /** 
     * Establece el estado del usuario 1 es activo 0 inactivo
     * */
    public function setStatus($status): void
    {
        $this->validateValues($status);
        $this->status = $status;
    }
    /**
     * Recupera el estado del usuario
     */
    public function getStatus(): int
    {
        return $this->status;
    }
    /**
     * Establece el nombre del usuario
     */
    public function setName($name): void
    {
        if (strlen($name) > 60) {
            throw new InvalidArgumentException("El nombre no puede tener más de 60 caracteres");
        }
        $this->name = strtolower(trim($name));
    }
    /**
     * Recupera el nombre del usuario
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * Establece el Apellido del usuario
     */
    public function setLastName($lastName): void
    {
        if (strlen($lastName) > 255) {
            throw new InvalidArgumentException("El apellido no puede tener más de 255 caracteres");
        }
        $this->lastName = strtolower(trim($lastName));
    }
    /** 
     * Recupera el Apellido
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }
    /**
     * Establece el correo electronico
     */
    public function setEmail($email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("El correo electrónico no es válido");
        }
        $this->email = $email;
    }
    /**
     * Recupera el correo electronico
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    /**
     * Establece el telefono 
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }
    /**
     * Recupera el Telefono
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * Hashea la contraseña
     * @param string $password
     * @throws InvalidArgumentException
     */
    public function setPassword(string $password): void
    {
        if (empty($password)) {
            throw new InvalidArgumentException("La contraseña no puede estar vacía");
        }
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }
    /**
     * Verifica la contraseña
     * @param string $password
     * @return bool
     */

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * Establece el token único al momento de crear la cuenta
     */
    public function generateToken(): void
    {
        $this->token = bin2hex(random_bytes(32));
    }

    /**
     * Establece el token cuando el usuario confirme su cuenta
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }
    /**
     * Recupera el token para validaciones en el controlador
     */
    public function getToken(): string
    {
        return $this->token;
    }
    /**
     * Establece la fecha de creacion
     */
    public function setDateCreation($dateCreation){
        $this->dateCreation = $dateCreation;
    }
    /**
     * Recupera el token
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }


    public function __toString(): string
    {
        return "Usuario #{$this->id}: {$this->name} {$this->name}";
    }



    /**
     * Valores validos
     */
    private function validateValues($value): void
    {
        //valores validos
        $values = [0, 1];
        if (!in_array($value, $values)) {
            throw new InvalidArgumentException("Solo existen los valores 0 y 1");
        }
    }
}
