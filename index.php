<?php
    require_once './lib/controlUsuari.php';
    $error = '';
    $successMessage = '';
  
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        $successMessage = "Se ha enviado un correo de activación a tu correo. Por favor, revisa tu bandeja de entrada.";
    }

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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php if ($successMessage): ?>
    <div class="notification" id="notification"><?php echo $successMessage; ?></div>
<?php endif; ?> 
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
  <form method="POST" class="login-form">
    <h2>TecView  <img src="./img/logo.png" width="40"></h2>
    <input type="text" name="username" placeholder="username/email" required/>
    <input type="password" name="password" placeholder="password" required/>
    <p class="message">You forget your Password? <a href="#" id="openModal">Reset your Password</a></p>
    <button type="submit">Login</button>
    <p class="message">Not registered? <a href="./register.php">Create an account</a></p>
    <?php if ($error): ?>
                <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    </form>
</div>
    <script>
        document.getElementById('openModal').addEventListener('click', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Recuperar Contraseña',
                input: 'email',
                inputLabel: 'Correo Electrónico',
                inputPlaceholder: 'Ingrese su email',
                showCancelButton: true,
                confirmButtonText: 'Enviar',
                cancelButtonText: 'Cancelar',
                preConfirm: (email) => {
                    if (!email) {
                        Swal.showValidationMessage('Por favor, ingrese un correo válido');
                        return false;
                    }
                    return fetch('./lib/resetPasswordSend.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `email=${encodeURIComponent(email)}`
                    })
                    .then(response => response.text())
                    .then(result => {
                        if (result.trim() === "success") {
                            Swal.fire('Correo enviado', 'Revisa tu bandeja de entrada', 'success');
                        } else if (result.trim() === "email_not_found") {
                            Swal.fire('Error', 'El correo no se encuentra registrado', 'error');
                        } else if (result.trim() === "error_envio") {
                            Swal.fire('Error', 'Hubo un problema al enviar el correo', 'error');
                        } else {
                            Swal.fire('Error', 'Hubo un problema desconocido', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                    });
                }
            });
        });
    </script>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let notification = document.getElementById("notification");
        setTimeout(() => {
            notification.style.display = "none";
        }, 5000);
    });
</script>
</body>
</html>
