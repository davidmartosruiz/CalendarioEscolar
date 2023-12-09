<?php
require_once "../database/Conexion.php";
require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class EmailModelo {

  public static function enviarEmail($destinatario, $asunto, $cuerpo): bool {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $mail = new PHPMailer(true);

    try {
      // Configuración del servidor
      $mail->SMTPDebug = 2;                                 
      $mail->isSMTP();                                      
      $mail->Host       = $_ENV['SMTP_HOST'];  
      $mail->SMTPAuth   = true;                             
      $mail->Username   = $_ENV['SMTP_USER'];  
      $mail->Password   = $_ENV['SMTP_PASS'];            
      $mail->SMTPSecure = 'tls';               
      $mail->Port       = $_ENV['SMTP_PORT'];                 

      // Destinatarios
      $mail->setFrom($_ENV['SMTP_USER'], 'Mailer');
      $mail->addAddress($destinatario);     

      // Contenido
      $mail->isHTML(true);                                  
      $mail->Subject = $asunto;
      $mail->Body    = $cuerpo;

      $mail->send();
      return true;
    } catch (Exception $e) {
      error_log("No se pudo enviar el correo. Error: {$mail->ErrorInfo}");
      return false;
    }
  }
}
?>