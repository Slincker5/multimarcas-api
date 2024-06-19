<?php

namespace App\Models;

use App\Models\Database;
use App\Models\Notification;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Mailgun\Mailgun;
use Symfony\Component\Dotenv\Dotenv;


class Email extends Database
{
  private $response;
  private $correoSistema = 'multimarcasapp@outlook.com';
  private $key = 'zzzktqwzclzmobqc';
  private $mail;
  private $apiKey;
  private $domain;
  private $apiKeyVerifyEmail;



  public function __construct()
  {
    // Cargar las variables de entorno desde el archivo .env
    $dotenv = new Dotenv();
    $dotenv->loadEnv(dirname(__DIR__, 2) . '/.env');

    // Inicializar las propiedades con las variables de entorno
    $this->apiKey = $_ENV['MAILGUN_API_KEY'];
    $this->domain = $_ENV['MAILGUN_DOMAIN'];
    $this->apiKeyVerifyEmail = $_ENV['EMAIL_VERIFY_API_KEY'];

    // Inicializar PHPMailer
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

  private function maskEmail($email)
  {
    // Dividir el correo en la parte local y el dominio
    list($local, $domain) = explode('@', $email);

    // Si la parte local tiene menos de 4 caracteres, no se puede enmascarar correctamente
    if (strlen($local) < 4) {
      return $email;
    }

    // Primer carácter y los dos últimos caracteres
    $firstChar = $local[0];
    $lastTwoChars = substr($local, -2);

    // Construir la parte enmascarada del correo
    $maskedLocal = $firstChar . '**' . $lastTwoChars;

    // Combinar la parte enmascarada con el dominio
    $maskedEmail = $maskedLocal . '@' . $domain;

    return $maskedEmail;
  }

  private function generateCodeVerification($email)
  {
    date_default_timezone_set('America/El_Salvador');
    $code = mt_rand(100000, 999999);
    $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    $sql = 'INSERT INTO email_verification_codes (email, code, expires_at) VALUES (?, ?, ?)';
    $this->ejecutarConsulta($sql, [$email, $code, $expires_at]);
    return $code;
  }

  private function validateEmailUser($email)
  {
    $sql = 'SELECT * FROM usuarios WHERE email = ? AND pass IS NOT NULL';
    $response = $this->ejecutarConsulta($sql, [$email]);
    $list = $response->fetchAll(\PDO::FETCH_ASSOC);
    return $list;
  }


  public function validateCodeEmail($code)
  {
    $fecha_actual = date("Y-m-d H:i:s");
    $sql = 'SELECT code, expires_at FROM email_verification_codes WHERE code = ?';
    $response = $this->ejecutarConsulta($sql, [$code]);
    $list = $response->fetchAll(\PDO::FETCH_ASSOC);
    if (count($list)) {
      if ($fecha_actual > $list[0]["expires_at"]) {
        $this->response['status'] = 'error';
        $this->response['message'] = 'El codigo de seguridad ya expiro.';
        return $this->response;
      } else {
        $this->response['status'] = 'ok';
        $this->response['message'] = 'Codigo de seguridad valido';
        return $this->response;
      }
    } else {
      $this->response['status'] = 'error';
      $this->response['message'] = 'El codigo de seguridad es incorrecto.';
      return $this->response;
    }
  }


  public function recoveryPassword($email)
  {
    try {
      if (count(self::validateEmailUser($email))) {
        $mgClient = Mailgun::create($this->apiKey);
        $domain = $this->domain;
        $perfil = self::validateEmailUser($email);
        $nombreCompleto = $perfil[0]["username"] === NULL ? $perfil[0]["nombre"] . ' ' . $perfil[0]["apellido"] : $perfil[0]["username"];
        $result = $mgClient->messages()->send($domain, [
          'from'    => 'Equipo de Multimarcas <no-reply@multimarcas.app>',
          'to'      => $nombreCompleto . ' <' . $email . '>',
          'subject' => 'Recuperación de contraseña',
          'html'    => '
          <h1 style="font-size:18px;color:#989898;display:block;font-weight:400;">Cuenta Multimarcas</h1>
          <h2 style="font-size:38px;color:#5592f6;display:block;font-weight:400">Codigo de seguridad</h2>
          <p style="font-size:15px;">Usa el siguiente codigo de seguridad para la cuenta de multimarcas <span style="color:blue;">' . self::maskEmail($email) . '</span>.
          <div style="font-size:15px;padding:2rem 0;">Codigo de seguridad: <b>' . self::generateCodeVerification($email) . '</b></div>
          <div style="font-size:15px;">Multimarcas App</div>
          '
        ]);

        $this->response['status'] = 'ok';
        $this->response['message'] = 'Correo enviado con éxito.';
        $this->response['id'] = $result->getId();
        
        $instanciaNotificacion = new Notification();
        $cuerpoNotificacion = "Email de recuperacion enviado";
        $instanciaNotificacion->createNotification("Olvidaron Contraeña", $cuerpoNotificacion);

        return $this->response;
      } else {
        $this->response['status'] = 'error';
        $this->response['message'] = 'El correo electronico proporcionado no se encuntra registrado o esta registrado con el servicio de google.';
        return $this->response;
      }
    } catch (\Exception $e) {
      return [
        "status" => "error",
        "msg" => "No se pudo enviar el correo.",
        "error" => $e->getMessage(),
      ];
    }
  }

  public function verifyEmailService($email)
  {
    $key = $this->apiKeyVerifyEmail;
    $url = "https://apps.emaillistverify.com/api/verifyEmail?secret=" . $key . "&email=" . $email;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    echo $response;
    curl_close($ch);
  }


  public function listEmails()
  {
    $sql = 'SELECT * FROM correos';
    $correos = $this->ejecutarConsulta($sql);
    $data = $correos->fetchAll(\PDO::FETCH_ASSOC);
    return $data;
  }
}
