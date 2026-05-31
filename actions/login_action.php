<?php

session_start();

include("../includes/db.php");

if (!isset($_POST["login_btn"])) {
    header("Location: ../pages/login.php");
    exit();
}

$email = $_POST["email"];
$mot_de_passe = $_POST["mot_de_passe"];

$sql = "SELECT * FROM utilisateurs
        WHERE email = ?
        AND mot_de_passe = ?
        AND actif = 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $email, $mot_de_passe);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 1) {

    $user = mysqli_fetch_assoc($result);

    $_SESSION["id_utilisateur"] = $user["id_utilisateur"];
    $_SESSION["nom"] = $user["nom"];
    $_SESSION["prenom"] = $user["prenom"];
    $_SESSION["role"] = $user["role"];

    if ($user["role"] == "etudiant") {
        header("Location: ../pages/dashboard_etudiant.php");
        exit();
    }

    if ($user["role"] == "enseignant") {
        header("Location: ../pages/dashboard_enseignant.php");
        exit();
    }

    if ($user["role"] == "admin") {
        header("Location: ../pages/dashboard_admin.php");
        exit();
    }

    header("Location: ../pages/login.php?erreur=1");
    exit();

} else {
    header("Location: ../pages/login.php?erreur=1");
    exit();
}

?>