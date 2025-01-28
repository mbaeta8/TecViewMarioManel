<?php

function getDBConnection(){
    $connString = 'mysql:host=localhost;port=3335;dbname=TecView';
    $user = 'root';
    $pass = '';
    $db = null;
    try{
        $db = new PDO($connString,$user,$pass,[
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }catch(PDOException $e){
        echo "<p style=\"color:red;\">Error " . $e->getMessage() . "</p>";
    }finally{
        return $db;
    }
}

function insertarNuevoUsuario($username, $email, $firstName, $lastName, $password, $active, $lastSignIn, $creationDate, $activationCodeValue){
    $db = getDBConnection(); 

    if ($db) {
        $passHash = password_hash($password, PASSWORD_DEFAULT);       
        
        $query = "INSERT INTO users (username, mail, userFirstName, userLastName, passHash, activat, lastSignIn, creationDate) 
            VALUES (:username, :email, :firstName, :lastName, :password, :active, :lastSignIn, :creationDate)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':password', $passHash);
        $stmt->bindParam(':active', $active);
        $stmt->bindParam(':lastSignIn', $lastSignIn);
        $stmt->bindParam(':creationDate', $creationDate);

        try {
            if($stmt->execute()){
                return true;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "Error: El correo o nombre de usuario ua existe.";
            } else {
                echo "Error al insertar el usuario: " . $e->getMessage();
            }
            return false;
        }
    }
    return false;
}