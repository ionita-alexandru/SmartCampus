<?php

session_start();

if (!isset($_SESSION["role"])) {

    header("Location: ../pages/login.php");
    exit();

}

function verifierRole($rolesAutorises) {

    if (!in_array($_SESSION["role"], $rolesAutorises)) {

        header("Location: ../index.php");
        exit();

    }

}

?>