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
        <title>TecView</title>
        <link rel="stylesheet" href="./css/main.css">
        <link rel="icon" href="./img/logo.ico">
    </head>
    <body>
    <div class="login-page">
        <div class="form">
        <h2>TecView  <img src="./img/logo.png" width="40"></h2>
            <input type="text" placeholder="username" required/>
            <input type="password" placeholder="password" required/>
            <input type="text" placeholder="verify password" required/>
            <input type="text" placeholder="name" required/>
            <input type="text" placeholder="lastname" required>
            <input type="text" placeholder="email" required/>
            <button>create</button>
            <p class="message">Already registered? <a href="./index.php">Sign In</a></p>
        </div>
    </div>
    </body>
</html>