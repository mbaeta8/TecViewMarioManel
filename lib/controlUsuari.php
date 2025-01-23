<?php
    require_once './lib/controlDB.php';

    function verificarUsuari($credential, $pass) {
        return verificarUsuarioDB($credential, $pass);
    }