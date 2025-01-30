<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
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
            <form method="post" action="">
                <button type="submit" name="logout" id="logout">Log Out</button>
            </form>
        </header>
        <main>
            <h1>BIENVENIDO, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
        </main>
        <footer>

        </footer>
    </body>
</html>