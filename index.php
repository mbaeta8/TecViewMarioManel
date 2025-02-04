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
    <p class="message">You forget your Password? <a href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">Reset your Password</a></p>
    <button type="submit">Login</button>
    <p class="message">Not registered? <a href="./register.php">Create an account</a></p>
    <?php if ($error): ?>
                <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
  <form>
  </div>
</div>

<!-- Modal de recuperación de contraseña -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">Recuperar Contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="resetPasswordSend.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
