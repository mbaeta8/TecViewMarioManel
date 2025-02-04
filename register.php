<?php
    require_once './lib/nouUser.php';
    
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = isset($_POST['username']) ? filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) : '';
        $email = isset($_POST['email']) ? filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) : '';
        $firstName = isset($_POST['nombres']) ? filter_input(INPUT_POST, 'nombres', FILTER_SANITIZE_EMAIL) : '';
        $lastName = isset($_POST['apellidos']) ? filter_input(INPUT_POST, 'apellidos', FILTER_SANITIZE_EMAIL) : '';
        $pass = isset($_POST['password']) ? filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING) : '';
        $pass2 = isset($_POST['password2']) ? filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_STRING) : '';        
        


        if (empty($user) || empty($email) || empty($pass)) {
            $error = "El nombre de usuario, el correo electronico y la password son obligatorios";
        } elseif ($pass !== $pass2) {
            $error = "Las passwords no coinciden";
        } else {
            
            $active = 0;
            $lastSignIn = date('Y-m-d H:i:s');
            $creationDate = date('Y-m-d H:i:s');
            $idUser = 1;
            $activationCodeValue = random_int(100000, 999999);
             
            $validacioCorrecta = insertarNuevoUsuario($user, $email, $firstName, $lastName, $pass, $active, $lastSignIn, $creationDate, $activationCodeValue);
           
            if ($validacioCorrecta) {
                header('Location: ./index.php');
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