<?php
    require_once './lib/controlDB.php';

    function verificarUsuario($credential, $pass) {
        return verificarUsuarioDB($credential, $pass);
    }