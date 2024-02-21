<?php

namespace App\Http\Services;

use App\Exceptions\AppErrors;
use App\Exceptions\BussinessException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class EmailService
{

    public function sendEmail(string $host, string $sender, string $password, string $senderDomain, string $senderDomainName, string $to, string $subject, $view, $attachmentPath = null): bool
    {
        $sent = false;
        
        try {

            if (is_object($view) && method_exists($view, 'render')) {
                // El objeto $view tiene un método render()
                $view = $view->render();
            } 

            $mail = new PHPMailer(true);
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host          = $host;
            $mail->SMTPAuth      = true;
            $mail->Username      = $sender;
            $mail->Password      = $password;
            $mail->SMTPSecure    = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port          = 587;

            $mail->setFrom($senderDomain, $senderDomainName);
            $mail->addAddress(strtolower(trim($to)));
            $mail->isHTML(true);
            $mail->Subject       = $subject;
            $mail->Body          = $view;

            // Adjuntar el archivo si se proporciona la ruta del archivo
            if ($attachmentPath !== null) {
                $mail->addAttachment($attachmentPath);
            }

            if ($mail->send()) {
                $sent = true;
            }
        } catch (Exception $e) {
            $sent = false;
            Log::error('Error al enviar el correo: ' . $e->getMessage());
            throw new BussinessException(AppErrors::EMAIL_NO_ENVIADO_MESSAGE, AppErrors::EMAIL_NO_ENVIADO_CODE);
        }

        return $sent;
    }
}