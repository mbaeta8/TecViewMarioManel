<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once './controlDB.php';
    require '../vendor/autoload.php';

    $conn = getDBConnection();

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $email = $_POST['email'];
        $resetPassCode = hash('sha256', random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $stmt = $conn->prepare("UPDATE users SET resetPassCode = :resetPassCode, resetPassExpiry = :expiry WHERE mail = :email");
        $stmt->bindValue(":resetPassCode", $resetPassCode, PDO::PARAM_STR);
        $stmt->bindValue(":expiry", $expiry, PDO::PARAM_STR);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            enviarCorreoReset($email, $resetPassCode);
            echo "Se ha enviado un correo con instrucciones para restablecer tu contraseña.";
        } else {
            echo "Error al generar el enlace de recuperación.";
        }
        $stmt->closeCursor();
    }

    function enviarCorreoReset($email, $resetPassCode) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mario.baetam@educem.net';
            $mail->Password = 'vqdb nrrt vdrj cdqv';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom('mario.baetam@educem.net', 'Mbaetam');
            $mail->addAddress($email);
    
            $mail->isHTML(true);
            $mail->Subject = 'Recupera tu contraseña - TecView';
            $logoUrl = '../img/logo.png';

            $resetUrl = "localhost/IsitecMarioManel/resetPassword.php?code=" . urlencode($resetPassCode) . "&mail=" . urlencode($email);
            $mail->Body = "<div style='text-align: center; font-family: Arial, sans-serif;'>
                <img src='$logoUrl' alt='TecView Logo' style='width: 150px; margin-bottom: 20px;'>
                <h1 style='color: #007bff;'>TecView</h1>
                <p style='font-size: 16px; color: #333;'> 
                    Hemos recibido una solicitud para restablecer tu contraseña.  
                    Haz clic en el botón de abajo para continuar:
                </p>
                <a href='$resetUrl' 
                   style='display: inline-block; padding: 10px 20px; margin: 20px 0;
                        background-color: #007bff; color: #fff; text-decoration: none;
                        font-size: 18px; border-radius: 5px;'>
                    Restablecer contraseña
                </a>
                <p style='font-size: 14px; color: #666;'>Si no solicitaste este cambio, ignora este correo.</p>
            </div>";

            $mail->send();
        } catch (Exception $e) {
            echo "? Error al enviar correo: {$mail->ErrorInfo}";
        }
    }