<?php
namespace App\Models;

use App\Models\Database;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Email extends Database
{
    private $correoSistema = 'multimarcasapp@outlook.com';
    private $key = 'zzzktqwzclzmobqc';
    private $mail;
    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        try {
          $this->mail->isSMTP();
          $this->mail->Host = 'smtp.office365.com';
          $this->mail->SMTPAuth = true;
          $this->mail->Username = $this->correoSistema;
          $this->mail->Password = $this->key;
          $this->mail->SMTPSecure = 'tls';
          $this->mail->Port = 587;
          $this->mail->SMTPDebug = 0;
          $this->mail->setFrom($this->correoSistema, 'MULTIMARCAS APP');
        } catch (Exception $e) {
          return [
            "status" => "error",
            "message" => $e->getMessage()
          ];
        }
    }

    public function validarEmailExistencia($email)
    {
        $sql = 'SELECT COUNT(*) FROM correos WHERE correo = ?';
        $getData = $this->ejecutarConsulta($sql, [$email]);
        $total = $getData->fetchColumn();
        return $total;
    }


    public function sendMailLabel($receptor, $nombreReceptor, $documentoEmisor, $asunto, $comentarios, $cantidad, $username)
    {
        try {
            $this->mail->addAddress($receptor, $nombreReceptor);
            $this->mail->addAttachment($documentoEmisor);
            $this->mail->isHTML(true);
            $this->mail->Subject = $asunto;
            $this->mail->Body = '
      <div style="border: 1px solid #ddd; font-size: 1.2rem; font-family: Arial;">
      <div style="padding: 1rem;">
        <h1 style="font-weight: 600; font-size: 1.3rem; color: #252525; display: flex; align-items: center;">
         Estimado(a) operador o contralor de ' . $nombreReceptor . '
        </h1>
        Nos complace confirmarte que hemos recibido y procesado exitosamente los cintillos del usuario <b>' . $username . '</b> a través de nuestra aplicación <a href="https://cintillos-plazamundo.netlify.app" target="_blank" style="text-decoration: none; color: #6fa8dc;">multimarcas</a>. A continuacion le muestro mas detalles del documento compartido.
      </div>
      <div style="padding: 1rem; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
        <div style="font-size: 14px "><span style="font-weight: 600; ">CANTIDAD DE CINTILLOS:</span> ' . $cantidad . ' </div>
        <div style="font-size: 14px; padding: 1rem 0"><span style="font-weight: 600;  margin-right: .5rem">COMENTARIOS:</span><div style="font-size: 1.1rem;">' . $comentarios . '</div></div>
      </div>
      <div style="padding: 1rem;">
        Agradecemos la confianza que nos tienen por usar nuestros servicios, cualquier duda puede contactar con nosotros.
      </div>
    </div>';
            $this->mail->AltBody = '';
            $this->mail->CharSet = 'UTF-8';
            $this->mail->send();

            return [
                "status" => "OK",
                "message" => "Correo enviado con exito.",
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "msg" => "No se pudo enviar el correo.",
                "error" => $this->mail->ErrorInfo,
            ];
        }
    }

    public function sendMailPoster($receptor, $nombreReceptor, $documentoEmisor, $asunto, $comentarios, $cantidad, $username)
    {
        try {
            $this->mail->addAddress($receptor, $nombreReceptor);
            $this->mail->addAttachment($documentoEmisor);
            $this->mail->isHTML(true);
            $this->mail->Subject = $asunto;
            $this->mail->Body = '
      <div style="border: 1px solid #ddd; font-size: 1.2rem; font-family: Arial;">
      <div style="padding: 1rem;">
        <h1 style="font-weight: 600; font-size: 1.3rem; color: #252525; display: flex; align-items: center;">
         Estimado(a) operador o contralor de ' . $nombreReceptor . '
        </h1>
        Nos complace confirmarte que hemos recibido y procesado exitosamente los afiches del usuario <b>' . $username . '</b> a través de nuestra aplicación multimarcas. A continuacion le muestro mas detalles del documento compartido.
      </div>
      <div style="padding: 1rem; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
        <div style="font-size: 14px "><span style="font-weight: 600; ">CANTIDAD DE ROTULOS:</span> ' . $cantidad . ' </div>
        <div style="font-size: 14px; padding: 1rem 0"><span style="font-weight: 600;  margin-right: .5rem">COMENTARIOS:</span><div style="font-size: 1.1rem;">' . $comentarios . '</div></div>
        <div style="display: flex; align-items: start; font-size: 14px "><span style="font-weight: 600; margin-right: .5rem">TIPO DE ROTULO:</span> Super Oferta 4x4</div>
      </div>
      <div style="padding: 1rem;">
        Agradecemos la confianza que nos tienen por usar nuestros servicios, cualquier duda puede contactar con nosotros.
      </div>
    </div>';
            $this->mail->AltBody = '';
            $this->mail->CharSet = 'UTF-8';
            $this->mail->send();

            return [
                "status" => "OK",
                "message" => "Correo enviado con exito.",
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "msg" => "No se pudo enviar el correo.",
                "error" => $this->mail->ErrorInfo,
            ];
        }
    }

    public function sendMailPosterSmall($receptor, $nombreReceptor, $documentoEmisor, $asunto, $comentarios, $cantidad, $username)
    {
        try {
            $this->mail->addAddress($receptor, $nombreReceptor);
            $this->mail->addAttachment($documentoEmisor);
            $this->mail->isHTML(true);
            $this->mail->Subject = $asunto;
            $this->mail->Body = '
      <div style="border: 1px solid #ddd; font-size: 1.2rem; font-family: Arial;">
      <div style="padding: 1rem;">
        <h1 style="font-weight: 600; font-size: 1.3rem; color: #252525; display: flex; align-items: center;">
         Estimado(a) operador o contralor de ' . $nombreReceptor . '
        </h1>
        Nos complace confirmarte que hemos recibido y procesado exitosamente los afiches del usuario <b>' . $username . '</b> a través de nuestra aplicación multimarcas. A continuacion le muestro mas detalles del documento compartido.
      </div>
      <div style="padding: 1rem; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
        <div style="font-size: 14px "><span style="font-weight: 600; ">CANTIDAD DE ROTULOS:</span> ' . $cantidad . ' </div>
        <div style="font-size: 14px; padding: 1rem 0"><span style="font-weight: 600;  margin-right: .5rem">COMENTARIOS:</span><div style="font-size: 1.1rem;">' . $comentarios . '</div></div>
        <div style="display: flex; align-items: start; font-size: 14px "><span style="font-weight: 600; margin-right: .5rem">TIPO DE ROTULO:</span> Super Oferta 3x9</div>
      </div>
      <div style="padding: 1rem;">
        Agradecemos la confianza que nos tienen por usar nuestros servicios, cualquier duda puede contactar con nosotros.
      </div>
    </div>';
            $this->mail->AltBody = '';
            $this->mail->CharSet = 'UTF-8';
            $this->mail->send();

            return [
                "status" => "OK",
                "message" => "Correo enviado con exito.",
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "msg" => "No se pudo enviar el correo.",
                "error" => $this->mail->ErrorInfo,
            ];
        }
    }

    public function sendMailPosterSmallDesc($receptor, $nombreReceptor, $documentoEmisor, $asunto, $comentarios, $cantidad, $username)
    {
        try {
            $this->mail->addAddress($receptor, $nombreReceptor);
            $this->mail->addAttachment($documentoEmisor);
            $this->mail->isHTML(true);
            $this->mail->Subject = $asunto;
            $this->mail->Body = '
      <div style="border: 1px solid #ddd; font-size: 1.2rem; font-family: Arial;">
      <div style="padding: 1rem;">
        <h1 style="font-weight: 600; font-size: 1.3rem; color: #252525; display: flex; align-items: center;">
         Estimado(a) operador o contralor de ' . $nombreReceptor . '
        </h1>
        Nos complace confirmarte que hemos recibido y procesado exitosamente los afiches del usuario <b>' . $username . '</b> a través de nuestra aplicación multimarcas. A continuacion le muestro mas detalles del documento compartido.
      </div>
      <div style="padding: 1rem; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
        <div style="font-size: 14px "><span style="font-weight: 600; ">CANTIDAD DE ROTULOS:</span> ' . $cantidad . ' </div>
        <div style="font-size: 14px; padding: 1rem 0"><span style="font-weight: 600;  margin-right: .5rem">COMENTARIOS:</span><div style="font-size: 1.1rem;">' . $comentarios . '</div></div>
        <div style="display: flex; align-items: start; font-size: 14px "><span style="font-weight: 600; margin-right: .5rem">TIPO DE ROTULO:</span> Super Oferta Pequeños (Descuentos/2x1) 3x3</div>
      </div>
      <div style="padding: 1rem;">
        Agradecemos la confianza que nos tienen por usar nuestros servicios, cualquier duda puede contactar con nosotros.
      </div>
    </div>';
            $this->mail->AltBody = '';
            $this->mail->CharSet = 'UTF-8';
            $this->mail->send();

            return [
                "status" => "OK",
                "message" => "Correo enviado con exito.",
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "msg" => "No se pudo enviar el correo.",
                "error" => $this->mail->ErrorInfo,
            ];
        }
    }

    public function sendMailPosterLowPriceSmall($receptor, $nombreReceptor, $documentoEmisor, $asunto, $comentarios, $cantidad, $username)
    {
        try {
            $this->mail->addAddress($receptor, $nombreReceptor);
            $this->mail->addAttachment($documentoEmisor);
            $this->mail->isHTML(true);
            $this->mail->Subject = $asunto;
            $this->mail->Body = '
      <div style="border: 1px solid #ddd; font-size: 1.2rem; font-family: Arial;">
      <div style="padding: 1rem;">
        <h1 style="font-weight: 600; font-size: 1.3rem; color: #252525; display: flex; align-items: center;">
         Estimado(a) operador o contralor de ' . $nombreReceptor . '
        </h1>
        Nos complace confirmarte que hemos recibido y procesado exitosamente los afiches del usuario <b>' . $username . '</b> a través de nuestra aplicación multimarcas. A continuacion le muestro mas detalles del documento compartido.
      </div>
      <div style="padding: 1rem; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
        <div style="font-size: 14px "><span style="font-weight: 600; ">CANTIDAD DE ROTULOS:</span> ' . $cantidad . ' </div>
        <div style="font-size: 14px; padding: 1rem 0"><span style="font-weight: 600;  margin-right: .5rem">COMENTARIOS:</span><div style="font-size: 1.1rem;">' . $comentarios . '</div></div>
        <div style="display: flex; align-items: start; font-size: 14px "><span style="font-weight: 600; margin-right: .5rem">TIPO DE ROTULO:</span> Baja de Precios 3x9</div>
      </div>
      <div style="padding: 1rem;">
        Agradecemos la confianza que nos tienen por usar nuestros servicios, cualquier duda puede contactar con nosotros.
      </div>
    </div>';
            $this->mail->AltBody = '';
            $this->mail->CharSet = 'UTF-8';
            $this->mail->send();

            return [
                "status" => "OK",
                "message" => "Correo enviado con exito.",
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "msg" => "No se pudo enviar el correo.",
                "error" => $this->mail->ErrorInfo,
            ];
        }
    }

    public function listEmails()
    {
        $sql = 'SELECT * FROM correos';
        $correos = $this->ejecutarConsulta($sql);
        $data = $correos->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }
}
