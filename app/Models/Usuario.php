<?php

namespace App\Models;
use InvalidArgumentException;
class Usuario extends ActiveRecord
{
    // Base de datos

    protected array $atributosExcluir = ['id', 'admin', 'confirmado', 'estado', 'fecha_de_alta'];

    public function __construct()
    {   
        parent::__construct();
        self::$tabla = 'usuarios';
    }

    protected ?int $id;
    protected int $admin;
    protected int $confirmado;
    protected int $estado;
    protected string $nombre;
    protected string $apellido;
    protected string $email;
    protected string $telefono;
    protected string $password;
    protected string $token;
    protected string $fechaDeAlta;


    /**
     * Establece el ID del usuario.
     *
     * @param int $id El ID del usuario.
     * @throws \InvalidArgumentException Si el ID no es un entero positivo.
     */
    public function setId($id): void {
        if ($id !== null && !is_int($id)) {
            throw new InvalidArgumentException("El ID debe ser un entero o null");
        }
        $this->id = $id;
    }

    /**
     * Obtiene el ID del usuario.
     *
     * @return int El ID del usuario.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Establece el rol de administrador del usuario.
     *
     * @param int $admin El rol de administrador del usuario.
     * @throws \InvalidArgumentException Si el rol de administrador no es 0 o 1.
     */
    public function setAdmin($admin)
    {
        if (!in_array($admin, [0, 1])) {
            throw new \InvalidArgumentException("El rol de administrador debe ser 0 o 1.");
        }
        $this->admin = $admin;
    }

    /**
     * Obtiene el rol de administrador del usuario.
     *
     * @return int El rol de administrador del usuario.
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Establece el estado de confirmación del usuario.
     *
     * @param int $confirmado El estado de confirmación del usuario.
     * @throws \InvalidArgumentException Si el estado de confirmación no es 0 o 1.
     */
    public function setConfirmado($confirmado)
    {
        if (!in_array($confirmado, [0, 1])) {
            throw new \InvalidArgumentException("El estado de confirmación debe ser 0 o 1.");
        }
        $this->confirmado = $confirmado;
    }

    /**
     * Obtiene el estado de confirmación del usuario.
     *
     * @return int El estado de confirmación del usuario.
     */
    public function getConfirmado()
    {
        return $this->confirmado;
    }

    /**
     * Establece el estado del usuario.
     *
     * @param int $estado El estado del usuario.
     * @throws \InvalidArgumentException Si el estado del usuario no es 0 o 1.
     */
    public function setEstado($estado)
    {
        if (!in_array($estado, [0, 1])) {
            throw new \InvalidArgumentException("El estado del usuario debe ser 0 o 1.");
        }
        $this->estado = $estado;
    }

    /**
     * Obtiene el estado del usuario.
     *
     * @return int El estado del usuario.
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Establece el nombre del usuario.
     *
     * @param string $nombre El nombre del usuario.
     * @throws \InvalidArgumentException Si el nombre está vacío.
     */
    public function setNombre($nombre)
    {
        if (empty($nombre)) {
            throw new \InvalidArgumentException("El nombre no puede estar vacío.");
        }
        $this->nombre = strtolower($nombre);
    }

    /**
     * Obtiene el nombre del usuario.
     *
     * @return string El nombre del usuario.
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Establece el apellido del usuario.
     *
     * @param string $apellido El apellido del usuario.
     * @throws \InvalidArgumentException Si el apellido está vacío.
     */
    public function setApellido($apellido)
    {
        if (empty($apellido)) {
            throw new \InvalidArgumentException("El apellido no puede estar vacío.");
        }
        $this->apellido = strtolower($apellido);
    }

    /**
     * Obtiene el apellido del usuario.
     *
     * @return string El apellido del usuario.
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Establece el email del usuario.
     *
     * @param string $email El email del usuario.
     * @throws \InvalidArgumentException Si el email no es válido.
     */
    public function setEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("El email no es válido.");
        }
        $this->email = $email;
    }

    /**
     * Obtiene el email del usuario.
     *
     * @return string El email del usuario.
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Establece el teléfono del usuario.
     *
     * @param string $telefono El teléfono del usuario.
     * @throws \InvalidArgumentException Si el teléfono está vacío.
     */
    public function setTelefono($telefono)
    {
        if (empty($telefono)) {
            throw new \InvalidArgumentException("El teléfono no puede estar vacío.");
        }
        $this->telefono = $telefono;
    }

    /**
     * Obtiene el teléfono del usuario.
     *
     * @return string El teléfono del usuario.
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Establece la contraseña del usuario, hasheándola con PASSWORD_BCRYPT.
     *
     * @param string $contraseña La contraseña del usuario.
     * @throws \InvalidArgumentException Si la contraseña está vacía.
     */
    public function setContraseña($contraseña)
    {
        if (empty($contraseña)) {
            throw new \InvalidArgumentException("La contraseña no puede estar vacía.");
        }
        $this->password = password_hash($contraseña, PASSWORD_BCRYPT);
    }

    /**
     * Obtiene la contraseña del usuario (hasheada).
     *
     * @return string La contraseña del usuario (hasheada).
     */
    public function getContraseña()
    {
        return $this->password;
    }

    /**
     * Establece el token del usuario.
     *
     * @param string $token El token del usuario.
     * @throws \InvalidArgumentException Si el token está vacío.
     */
    public function setToken()
    {
        $this->token = uniqid();
    }

    /**
     * Obtiene el token del usuario.
     *
     * @return string El token del usuario.
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Establece la fecha de alta del usuario.
     *
     * @param string $fechaDeAlta La fecha de alta del usuario.
     * @throws \InvalidArgumentException Si la fecha de alta está vacía.
     */
    public function setFechaDeAlta($fechaDeAlta)
    {
        if (empty($fechaDeAlta)) {
            throw new \InvalidArgumentException("La fecha de alta no puede estar vacía.");
        }
        $this->fechaDeAlta = $fechaDeAlta;
    }

    /**
     * Obtiene la fecha de alta del usuario.
     *
     * @return string La fecha de alta del usuario.
     */
    public function getFechaDeAlta()
    {
        return $this->fechaDeAlta;
    }

    public function toString()
    {
        return `
            Usuario :  {$this->getNombre()}  
            ID: {$this->getID()}  
        `;
    }
}