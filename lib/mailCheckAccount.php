<?php
    require './controlDB.php';

    if (isset($_GET['code']) && isset($_GET['email']))
    {
        $code = $_GET['code'];
        $email = $_GET['email'];

        $stmt = $db->prepare("SELECT activationCode FROM users WHERE email = :email AND activationCode = :code");
        $stmt->bind_param("ss", $email, $code);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0)
        {
            $stmt = $conn->prepare("UPDATE users SET activat = 1, activationCode = NULL, activationDate = NOW() WHERE email = :email");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            echo "<script>alert('Cuenta activada correctamente.'); window.location.href = 'index.php';</script>";
        } else {
            echo "<script>alert('Codigo de activacion invalido o ya usado.'); window.location.href = 'index.php';</script>";
        }
        $stmt->close();
    }
