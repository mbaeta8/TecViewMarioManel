<?php
  require_once './lib/controlUsuari.php';
  $error = '';
  
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $credential = isset($_POST['username']) ? filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) : '';
      $pass = isset($_POST['password']) ? filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING) : '';
      $login = verificarUsuari($credential, $pass);
      if ($login) {
          session_start();
          $_SESSION['user'] = $credential;
          
          header('Location: home.php');
          exit();
      } else { 
        $error = "Revisa l'email/username i/o la contrasenya";
      }
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
    <input type="text" name="username" placeholder="username/email" required/>
    <input type="password" name="password" placeholder="password" required/>
    <button type="submit">Login</button>
    <p class="message">Not registered? <a href="./register.php">Create an account</a></p>
    <?php if ($error): ?>
                <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
  <form>
  </div>
</div>
</body>
</html>
