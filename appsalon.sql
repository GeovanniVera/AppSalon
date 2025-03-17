CREATE DATABASE IF NOT EXISTS APPSALON;

CREATE USER IF NOT EXISTS 'userappsalon'@'%' IDENTIFIED BY 'appsalonpassword12@';
GRANT ALL PRIVILEGES ON APPSALON.* TO 'userappsalon'@'%';
FLUSH PRIVILEGES;

use APPSALON;

CREATE TABLE IF NOT EXISTS usuarios(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(13) NOT NULL,
    admin tinyInt(1)  DEFAULT 0,
    confirmado tinyInt(1) DEFAULT 0,
    token VARCHAR(255),
    estado TINYINT(1)  DEFAULT 1,
    password CHAR(72) NOT NULL,
    fecha_de_alta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_telefono (telefono),
    INDEX idx_estado (estado)
);

CREATE TABLE IF NOT EXISTS citas (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    fecha_de_alta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT(11),
    CONSTRAINT fk_id_usuario_citas
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS servicios(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(5,2)
);

CREATE TABLE IF NOT EXISTS citas_servicios (
    id_cita INT(11),
    id_servicio INT(11),
    PRIMARY KEY (id_cita, id_servicio),
    CONSTRAINT fk_id_cita_citas_servicios
        FOREIGN KEY (id_cita)
        REFERENCES citas (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_id_servicio_citas_servicios
        FOREIGN KEY (id_servicio)
        REFERENCES servicios (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

