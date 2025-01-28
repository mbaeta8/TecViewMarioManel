<?php
session_start();
use PDO;
use PDOException;

if (isset($_POST['logout'])) {
    header('Location: index.php');
    exit();
}

    function getDBConnection()
    {
        $connString = 'mysql:host=localhost;port=3335;dbname=TecView';
        $user = 'root';
        $pass = '';
        $db = null;
        try {
            $db = new PDO($connString, $user, $pass, [PDO::ATTR_PERSISTENT => true]);
        } catch (PDOException $e) {
            echo "<p style=\"color:red;\">Error " . $e->getMessage() . "</p>";
        } finally {
            return $db;
        }


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

            <h1>BIENVENIDO</h1><h1 <?php echo $_SESSION['username']?>></h1>
        </main>
        <footer>

        </footer>
    </body>
</html>