<?php

$host = "localhost";
$user = "root";
$password = "root";
$database = "smartcampusdb";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}

?>