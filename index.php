<? 
  require_once './lib/controlUsuari.php';
  require_once './lib/nouUser.php';
  $error = '';

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $credential = isset($_POST['username']) ? filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) : '';
    $pass = isset($_POST['password']) ? filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING) : '';
    $pass2 = isset($_POST['password2']) ? filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_STRING) : '';
    $user = isset($_POST['username']) ? filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) : '';
    $email = isset($_POST['email']) ? filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) : '';
    $firstName = isset($_POST['nombre']) ? filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING) : '';
    $lastName = isset($_POST['apellido']) ? filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING) : '';
    $login = verificarUsuario($credential, $pass);

    if ($login !== false) {
      session_start();
      $_SESSION['id'] = $login['idUsuario'];
      $_SESSION['user'] = $login['user'];

      header('Location: ./home.php');
      exit;
    } else {
      $error = 'Usuario/Email i/o contraseña incorrectos';
    }

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
        require_once './lib/mail.php';
      } else {
        $error = "Error al insertar el usuario en la base de datos";
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="es" >
<head>
  <meta charset="UTF-8">
  <title>TecView</title>
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans'>
  <link rel="stylesheet" href="./css/main.css">
  <link rel="icon" href="./img/logo.ico">
</head>
<body>
<div class="cont">
  <div method="POST" class="form sign-in">
    <h2>Bienvenido a TecView  <img src="./img/logo.png" width="50"></h2>
    <label>
      <span>Email/Usuario</span>
      <input autocomplete="username" name="username" id="username" type="email" placeholder="Email/Usuario" required/>
    </label>
    <label>
      <span>Contraseña</span>
      <input autocomplete="username" name="password" id="password" type="password" placeholder="Contraseña" required/>
    </label>
    <p class="forgot-pass">Forgot password?</p>
    <button type="button" name="login" class="submit">Iniciar Sesion</button>
  </div>
  <div class="sub-cont">
    <div class="img">
      <div class="img__text m--up">
        <h2>Eres Nuevo?</h2>
        <p>Registrate y descubre la gran variedad de reviews de hardware de nuestra pagina!</p>
      </div>
      <div class="img__text m--in">
        <h2>Ya tienes una cuenta?</h2>
        <p>Si ya tienes una cuenta, simplemente inicia sesion. Te hechamos de menos!</p>
      </div>
      <div class="img__btn">
        <span class="m--up">Registrarse</span>
        <span class="m--in">Iniciar Sesion</span>
      </div>
    </div>
    <div method="POST" class="form sign-up">
      <h2>Registro  <img src="./img/logo.png" width="50"></h2>
      <label>
        <span>Usuario</span>
        <input type="text" name="username" id="username" placeholder="Username" required/>
      </label>
      <label>
        <span>Email</span>
        <input type="email" name="email" id="email" placeholder="Email" required/>
      </label>
      <label>
        <span>Nombre</span>
        <input type="text" name="nombre" id="nombre" placeholder="Nombre" required/>
      </label>
      <label>
        <span>Apellido</span>
        <input type="text" name="apellido" id="apellido" placeholder="Apellido" required/>
      </label>
      <label>
        <span>Contraseña</span>
        <input type="password" name="password" id="password" placeholder="Contraseña" required/>
      </label>
      <label>
        <span>Verificar Contraseña</span>
        <input type="password" name="password2" id="password2" placeholder="Verificar Contraseña" required/>
      </label>
      <button type="button" name="register" class="submit">Registrarse</button>
      <p class="error"><?=$error?></p>
    </div>
  </div>
</div>
  <script  src="./css/main.js"></script>
</body>
</html>
