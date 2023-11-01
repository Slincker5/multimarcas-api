<?php
namespace App\Models;

use App\Models\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email extends Database {
    private $correoSistema = 'multimarcasapp@outlook.com';
    private $key = 'zzzktqwzclzmobqc';

    public function enviarCorreo($receptor, $nombreReceptor, $documentoEmisor, $asuntoEmisor) {
        $mail = new PHPMailer(true);
        try {
             // Configuraciones del servidor
             $mail->isSMTP();                                            
             $mail->SMTPDebug = 2;                                       
             $mail->Host       = 'smtp.office365.com';  
             $mail->SMTPAuth   = true;                                   
             $mail->Username   = $this->correoSistema;              
             $mail->Password   = $this->key;                        
             $mail->SMTPSecure = 'tls';                                  
             $mail->Port       = 587;
             $mail->SMTPDebug = 0;

    
             // Destinatarios
             $mail->setFrom($this->correoSistema, 'MULTIMARCAS APP');
             $mail->addAddress($receptor, $nombreReceptor);
    
             // Adjuntar un archivo
             $mail->addAttachment($documentoEmisor);
    
             // Contenido
             $mail->isHTML(true);                                  
             $mail->Subject = $asuntoEmisor;
             $mail->Body    = '
Estimado(a) ' . $nombreReceptor . ',<br><br>

Nos complace confirmarte que hemos recibido y procesado exitosamente los cintillos creados por nuestro usuario a través de nuestra aplicación. En este correo, te compartimos los cintillos en el formato de impresión especificado: 7 filas por 4 columnas.<br><br>

Por favor, revisa los archivos adjuntos y no dudes en contactarnos si tienes alguna pregunta o si necesitas alguna aclaración. Estamos aquí para ayudarte y agradecemos tu confianza en nuestros servicios.

<br><br>
Cordiales saludos
<br>
Gerson Borja<br>
Developer app';
             $mail->AltBody = '';
	     $mail->CharSet = 'UTF-8';
             $mail->send();
             
             $res = [
                "status" => "OK",
                "msg" => "Correo enviado con exito."
             ];
             return $res;
            } catch (Exception $e) {
                echo "El mensaje no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}";
            }
        }

        public function listEmails() {
            $sql = 'SELECT * FROM correos'; 
            $correos = $this->ejecutarConsulta($sql);
            $data = $correos->fetchAll(\PDO::FETCH_ASSOC);
            return $data;
        }
}
?>
