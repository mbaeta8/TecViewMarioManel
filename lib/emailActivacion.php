<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    require '../vendor/autoload.php';     

    function enviarCorreoActivacion($email, $username, $activationLink)
    {
        $mail = new PHPMailer(true);

        try
        {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mario.baetam@educem.net';
            $mail->Password = 'vqdb nrrt vdrj cdqv';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('mario.baetam@educem.net', 'Mbaetam');
            $mail->addAddress($email, $username);
            
            $mail->isHTML(true);
            $mail->Subject = 'Activa tu cuenta en TecView!';
            $logoUrl = '../img/logo.png';

            $mail->Body = "<div style='text-align: center; font-family: Arial, sans-serif;'>
                <img src='$logoUrl' alt='TecView Logo' style='width: 150px; margin-bottom: 20px;'>
                <h1 style='color: #007bff;'>Bienvenido a TecView, $username</h1>
                <p style='font-size: 16px; color: #333;'> 
                    Hemos recibido una solicitud para activar tu contrase�a.  
                    Haz clic en el bot�n de abajo para continuar:
                </p>
                <a href='$activationLink' 
                   style='display: inline-block; padding: 10px 20px; margin: 20px 0;
                        background-color: #007bff; color: #fff; text-decoration: none;
                        font-size: 18px; border-radius: 5px;'>
                    Activa tu cuenta ahora! $username
                </a>
                <p style='font-size: 14px; color: #666;'>Si no solicitaste este cambio, ignora este correo.</p>
            </div>";

            $mail->send();
        } catch (Exception $e){
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    }