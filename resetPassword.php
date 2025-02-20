<?php
    require_once './lib/controlDB.php';

    $conn = getDBConnection();

    if (isset($_GET['code']) && isset($_GET['mail']))
    {
        $code = $_GET['code'];
        $email = $_GET['mail'];

        $stmt = $conn->prepare("SELECT passHash FROM users WHERE mail = :email AND resetPassCode = :code AND resetPassExpiry > NOW()");
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":code", $code, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if($user) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $newPassword = $_POST['password'];
                $confirmPassword = $_POST['confirm_password'];
                $currentPasswordHash = $user['passHash'];

                $regex = "/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";

                if (!preg_match($regex, $newPassword)) {
                    echo "<script>alert('La contraseña debe tener al menos 8 caracteres, una mayuscula, un numero y un caracter especial.');</script>";
                } elseif ($newPassword !== $confirmPassword) {
                    echo "<script>alert('Las contraseñas no coinciden.');</script>";
                } elseif (password_verify($newPassword, $currentPasswordHash)) {
                    echo "<script>alert('No puedes usar la misma contraseña anterior. Elige una diferente.');</script>";
                } else {

                    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                
                    $stmt = $conn->prepare("UPDATE users SET passHash = :newPassword, resetPassCode = NULL, resetPassExpiry = NULL WHERE mail = :email");
                    $stmt->bindValue(":newPassword", $newPasswordHash, PDO::PARAM_STR);
                    $stmt->bindValue(":email", $email, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        echo "<script>alert('Contraseña actualizada correctamente.'); window.location.href='index.php';</script>";
                    } else {
                        echo "<script>alert('Error al actualizar la contraseña.'); window.location.href='index.php';</script>";
                    }
                }
            }
        } else {
            echo "<script>alert('Codigo invalido o expirado.'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('Acceso no autorizado.'); window.location.href='index.php';</script>";
    }
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>TecView</title>
  <link rel="stylesheet" href="./css/main.css">
  <link rel="icon" href="./img/logo.ico">
</head>
<body>
<div class="login-page">
    <div class="form">
        <form method="POST" class="login-form">
            <h2>TecView  <img src="./img/logo.png" width="40"></h2>
            <input type="password" name="password" placeholder="New password" required/>
            <input type="password" name="confirm_password" placeholder="Repeat password" required/>
            <button type="submit">Actualizar Contraseña</button>
        </form>
    </div>
</div>
</body>
</html>
    