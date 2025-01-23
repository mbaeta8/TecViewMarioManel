<?php

    function getDBConnection()
    {
        $connString = 'mysql:host=localhost;port=3335;dbname=tecview';
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

    function verificarUsuarioDB($credential, $pass) 
    {
        $result = false;
        $conn = getDBConnection();
        $sql = "SELECT `iduser`, `passHash`, `username` FROM `users` WHERE (`mail`=:credential OR `username`=:credential) AND `activat`= 1";
        try 
        {
            $usuaris = $conn->prepare($sql);
            $usuaris->execute([':credential' => $credential]);
            if ($usuaris->rowCount() == 1) {
                $dadesUsuari = $usuaris->fetch(PDO::FETCH_ASSOC);
            
                if (password_verify($pass, $dadesUsuari['passHash'])) {
                    $result = ['idUsuari' => $dadesUsuari['iduser'], 'name' => $dadesUsuari['username']];
                    return $result;
                }
                echo "<p style=\"color:red;\">Error " . "PSSWD and HASH do not match" . "</p>";
            }
        } catch (PDOException $e) {
            echo "<p style=\"color:red;\">Error " . $e->getMessage() . "</p>";
        } finally {
            return $result;
        }        
    }