<?php

function getDBConnection(){
    $connString = 'mysql:host=localhost;port=3335;dbname=tecview';
    $user = 'root';
    $pass = '';
    $db = null;
    try{
        $db = new PDO($connString,$user,$pass,[PDO::ATTR_PERSISTENT => True]);
    }catch(PDOException $e){
        echo "<p style=\"color:red;\">Error " . $e->getMessage() . "</p>";
    }finally{
        return $db;
    }
}

function insertarNuevoUsuario($username, $email, $firstName, $lastName, $password, $active, $lastSignIn, $creationDate, $activationCodeValue, $activationCode) {
    $db = getDBConnection(); 

    if ($db) {
        $passHash = password_hash($password, PASSWORD_DEFAULT);       
        $mailHash = filter_var($activationCode, FILTER_SANITIZE_STRING);
        
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

        if ($stmt->execute()) {
            return true; 
        }
        return false;
    }
    return false;
}