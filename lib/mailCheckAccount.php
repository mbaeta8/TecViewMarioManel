<?php
    require './controlDB.php';

    $db = getDBConnection();

    if(isset($_GET['code']) && isset($_GET['mail']))
    {
        $code = $_GET['code'];
        $email = $_GET['mail'];

        $stmt = $db->prepare("SELECT activationCode FROM users WHERE mail = :email AND activationCode = :code");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':code', $code, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->fetch())
        {
            $stmt = $db->prepare("UPDATE users SET activat = 1, activationCode = NULL, activationDate = NOW() WHERE mail = :email");
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            echo "<script>alert('Cuenta activada correctamente.'); window.location.href = '../index.php';</script>";
        } else {
            echo "<script>alert('El codigo de activacion es invalido o ya esta usado.'); window.location.href = '../index.php';</script>";
        }
    }
