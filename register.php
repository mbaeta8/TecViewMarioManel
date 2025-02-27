<?php
    require_once './lib/nouUser.php';
    require './lib/emailActivacion.php';

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = isset($_POST['username']) ? filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) : '';
        $email = isset($_POST['email']) ? filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) : '';
        $firstName = isset($_POST['nombres']) ? filter_input(INPUT_POST, 'nombres', FILTER_SANITIZE_STRING) : '';
        $lastName = isset($_POST['apellidos']) ? filter_input(INPUT_POST, 'apellidos', FILTER_SANITIZE_STRING) : '';
        $pass = isset($_POST['password']) ? filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING) : '';
        $pass2 = isset($_POST['password2']) ? filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_STRING) : '';        
        
        if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $pass)) {
            $error = "La contraseña debe tener al menos 8 caracteres, una mayuscula y un caracter especial.";
        } else if (empty($user) || empty($email) || empty($pass)) {
            $error = "El nombre de usuario, el correo electronico y la password son obligatorios";
        } elseif ($pass !== $pass2) {
            $error = "Las passwords no coinciden";
        } else {
            
            $active = 0;
            $lastSignIn = date('Y-m-d H:i:s');
            $creationDate = date('Y-m-d H:i:s');

            $randomValue = bin2hex(random_bytes(32));
            $activationCodeValue = hash('sha256', $randomValue);
             
            $validacioCorrecta = insertarNuevoUsuario($user, $email, $firstName, $lastName, $pass, $active, $lastSignIn, $creationDate, $activationCodeValue);
           
            if ($validacioCorrecta) {
                $activationLink = "localhost/IsitecMarioManel/lib/mailCheckAccount.php?code=" . urlencode($activationCodeValue) . "&mail=" . urlencode($email);
                enviarCorreoActivacion($email, $user, $activationLink);

                header('Location: ./index.php?success=1');
                exit();
            } else {
                $error = "Error al insertar el usuario en la base de datos";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" style="text/css" href="./css/main.css">
        <link rel="icon" href="./img/logo.ico">
        <title>TecView</title>
    </head>
    <body>
    <div class="ocean">
  <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
    viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
    <defs>
      <path id="wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>
    </defs>
    <g class="parallax">
      <use xlink:href="#wave" x="48" y="0" fill="rgba(255,255,255,0.7)"></use>
      <use xlink:href="#wave" x="48" y="3" fill="rgba(255,255,255,0.5)"></use>
      <use xlink:href="#wave" x="48" y="6" fill="rgba(255,255,255,0.3)"></use>
      <use xlink:href="#wave" x="48" y="9" fill="rgba(255,255,255,1)"></use>
    </g>
  </svg>
</div>
    <div class="login-page">
        <div class="form">
        <form method="POST">
        <h2>TecView  <img src="./img/logo.png" width="40"></h2>
            <input type="text" name="username" placeholder="username" required/>
            <input type="text" name="email" placeholder="email" required/>
            <input type="text" name="nombres" placeholder="name"/>
            <input type="text" name="apellidos" placeholder="lastname">
            <input type="password" name="password" placeholder="password" required/>
            <input type="password" name="password2" placeholder="verify password" required/>
            <button type="submit">create</button>
            <p class="message">Already registered? <a href="./index.php">Sign In</a></p>
            <?php if ($error): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
        </div>
    </div>
    </body>
</html>