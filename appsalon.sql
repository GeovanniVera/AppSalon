CREATE DATABASE IF NOT EXISTS APPSALON;

CREATE USER IF NOT EXISTS 'userappsalon'@'%' IDENTIFIED BY 'appsalonpassword12@';
GRANT ALL PRIVILEGES ON APPSALON.* TO 'userappsalon'@'%';
FLUSH PRIVILEGES;

USE APPSALON;

CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    lastName VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(13) NOT NULL,
    admin TINYINT(1) DEFAULT 0,
    confirmed TINYINT(1) DEFAULT 0,
    token VARCHAR(255),
    status TINYINT(1) DEFAULT 0,
    password CHAR(72) NOT NULL,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_users_email (email),
    INDEX idx_users_phone (phone),
    INDEX idx_users_status (status)
);

CREATE TABLE IF NOT EXISTS dates (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    dateTime TIME NOT NULL,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    idUser INT(11),
    CONSTRAINT fk_dates_user
        FOREIGN KEY (idUser)
        REFERENCES users (id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS services (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(5, 2)
);

CREATE TABLE IF NOT EXISTS dates_services (
    idDate INT(11),
    idService INT(11),
    PRIMARY KEY (idDate, idService),
    CONSTRAINT fk_dates_services_date
        FOREIGN KEY (idDate)
        REFERENCES dates (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_dates_services_service
        FOREIGN KEY (idService)
        REFERENCES services (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);