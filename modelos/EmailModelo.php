<?php

// Importar PHPMailer classes al "namespace global"
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

class Email {
  public static function enviarCorreo($correoUsuarioNotificaciones, $asunto, $cuerpo) {
    $mail = new PHPMailer(true);

    try {
      // Leer la configuración del servidor de correo del archivo .env
      $env = parse_ini_file('../.env');

      // Configuración del servidor de correo
      $mail->SMTPDebug = 0;                                 
      $mail->isSMTP();                                      
      $mail->Host = $env['SMTP_HOST'];  
      $mail->SMTPAuth = true;                               
      $mail->Username = $env['SMTP_USERNAME'];                 
      $mail->Password = $env['SMTP_PASSWORD'];                           
      $mail->SMTPSecure = $env['SMTP_SECURE'];                            
      $mail->Port = $env['SMTP_PORT']; 

      // Permitir caracteres UTF-8
      $mail->CharSet = 'UTF-8';

      // Recipientes
      $mail->setFrom($env['SMTP_USERNAME'], 'Calendario escolar');
      $mail->addAddress($correoUsuarioNotificaciones);     

      // Contenido
      $mail->isHTML(true);                                  
      $mail->Subject = $asunto;
      $mail->Body    = $cuerpo;
      $mail->AltBody = strip_tags($cuerpo);

      $mail->send();
    } catch (Exception $e) {
      echo "Hubo un problema en el envío del correo electrónico, pídele al administrador que revise la configuración del servidor de correo.";
    }
  }
}
?>