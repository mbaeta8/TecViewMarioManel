<?php
session_start();
require './lib/controlDB.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$conn = getDBConnection();
$user = $_SESSION['user'];

$foto_perfil = 'img/default_profile.jpg';

  
$query = "SELECT foto_perfil FROM users WHERE username = :user";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user', $user);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && !empty($row['foto_perfil'])) {
    $imagen = $row['foto_perfil'];
    
    if (strpos($imagen, 'data:image') === 0) {
        $foto_perfil = $imagen;
    } else {
        $tipo = detectarFormatoBase64($imagen);
        $foto_perfil = "data:$tipo;base64,$imagen";
    }
} else {
    echo "<pre>No se encontró imagen base64 en la base de datos</pre>";
}

function detectarFormatoBase64($base64) {
    $inicio = substr($base64, 0, 4); 
    
    if ($inicio === '/9j/') return 'image/jpeg'; 
    if ($inicio === 'iVBOR') return 'image/png'; 
    if ($inicio === 'R0lG') return 'image/gif'; 
    
    return 'image/jpeg'; 
}


if (isset($_POST['logout'])) 
{
    session_destroy();
    header('Location: ./index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="es" >
    <head>
    <meta charset="UTF-8">
        <title>TecView</title>
        <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans'>
        <link rel='stylesheet' href='css/home.css'>
        <link rel="icon" href="./img/logo.ico">
    </head>
    <body>
        <header>
            <div class="logo">
                <img src="img/logo.png" alt="Logo">
            </div>
            <h1>Inicio</h1>
            <div class="user-menu" id="userMenu">
                <span><?php echo htmlspecialchars($_SESSION['user']); ?></span>
                <div class="profile-icon">
                    <img id="profileImage" src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Perfil" width="45" height="45">
                </div>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="perfil.php">Perfil</a>
                    <form method="post" action="">
                        <button type="submit" name="logout" id="logout">Cerrar Sesión</button>
                    </form>
                </div>
            </div>
        </header>
        <main>
            <h1 class="welcome-message">BIENVENIDO, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
        </main>
        <footer>
            <p>© 2021 TecView. Todos los derechos reservados.</p>
        </footer>
        <script>
            function toggleMenu(event) {
                event.stopPropagation(); 
                document.getElementById("dropdownMenu").classList.toggle("show");
            }

            document.getElementById("userMenu").addEventListener("click", toggleMenu);

            window.addEventListener("click", function(event) {
                let dropdown = document.getElementById("dropdownMenu");
                let userMenu = document.getElementById("userMenu");

                if (!userMenu.contains(event.target) && dropdown.classList.contains("show")) {
                    dropdown.classList.remove("show");
                }
            });
        </script>
    </body>
</html>