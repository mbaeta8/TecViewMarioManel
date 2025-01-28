<?php
  require_once './lib/controlUsuari.php';
  $error = '';
  
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $credential = isset($_POST['username']) ? filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) : '';
      $pass = isset($_POST['password']) ? filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING) : '';
      $login = verificarUsuari($credential, $pass);
      if ($login !== false) {
          session_start();
          $_SESSION['id'] = $login['idUsuari'];
          $_SESSION['user'] = $login['name'];
          
          header('Location: ./home.php');
          exit();
      } else { $error = "Revisa l'email/username i/o la contrasenya";}
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
  <form method="POST">
    <h2>TecView  <img src="./img/logo.png" width="40"></h2>
    <form class="login-form">
      <input type="text" placeholder="username/email" required/>
      <input type="password" placeholder="password" required/>
      <button>login</button>
      <p class="message">Not registered? <a href="./register.php">Create an account</a></p>
    </form>
  <form>
  </div>
</div>
</body>
</html>
