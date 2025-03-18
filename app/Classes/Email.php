<?php

namespace App\Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{
    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        try {
            $phpmailer = new PHPMailer();
            $phpmailer->isSMTP();
            $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
            $phpmailer->SMTPAuth = true;
            $phpmailer->Port = 2525; // Puerto para STARTTLS
            $phpmailer->Username = '4c285440b120b4'; // Usuario de Mailtrap
            $phpmailer->Password = '6c28c7b28b7b45'; // Contraseña de Mailtrap
            $phpmailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // ¡Clave para TLS!
            // $phpmailer->SMTPDebug = 3; // Descomenta para depuración
    
            $phpmailer->setFrom('cuentas@appsalon.com');
            $phpmailer->addAddress($this->email, $this->nombre);
            $phpmailer->Subject = "Confirma tu cuenta.";
            $phpmailer->isHTML(true);
            $phpmailer->CharSet = 'UTF-8';
            $phpmailer->Body = $this->generarContenido();
    
            if ($phpmailer->send()) {
                return true;
            } else {
                error_log("Error al enviar email: " . $phpmailer->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Error de PHPMailer: " . $e->getMessage());
            return false;
        }
    }


    public function generarContenido()
    {
        $url = "http://localhost:3000/confirmAccount?token=" . urlencode($this->token);

        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Confirmación de Cuenta</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    text-align: center;
                    padding: 20px;
                }
                .container {
                    background: white;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    max-width: 400px;
                    margin: auto;
                }
                h1 {
                    color: #333;
                }
                p {
                    color: #555;
                }
                .button {
                    display: inline-block;
                    background: #007BFF;
                    color: white;
                    padding: 10px 20px;
                    text-decoration: none;
                    border-radius: 5px;
                    margin-top: 20px;
                }
                .button:hover {
                    background: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Confirma tu Cuenta</h1>
                <p>Gracias por registrarte. Por favor, haz clic en el botón de abajo para confirmar tu cuenta.</p>
                <a href="$url" class="button">Confirmar Cuenta</a>
            </div>
        </body>
        </html>
        HTML;
    }
}
