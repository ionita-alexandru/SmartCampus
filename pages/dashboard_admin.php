<?php
include("../includes/auth.php");
verifierRole(["admin"]);
include("../includes/db.php");

$nbEtudiants = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM etudiants"))["total"];
$nbEnseignants = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM enseignants"))["total"];
$nbCours = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM cours"))["total"];
$nbGroupes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM groupes"))["total"];
$nbPromotions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM promotions"))["total"];
$nbSeances = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM seances"))["total"];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrateur - SmartCampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="connected-page">

<?php include("../includes/header.php"); ?>

<nav>
    <a href="../index.php">Accueil</a>
    <a href="dashboard_admin.php">Dashboard</a>
    <a href="utilisateurs.php">Utilisateurs</a>
    <a href="cours.php">Cours</a>
    <a href="inscriptions.php">Inscriptions</a>
    <a href="emploi_du_temps.php">Emploi du temps</a>
    <a href="messagerie.php">Messagerie</a>
    <a href="../actions/logout.php">Déconnexion</a>
</nav>

<main>

    <section class="card">
        <h2>Statistiques générales</h2>
        <p><strong>Étudiants :</strong> <?php echo $nbEtudiants; ?></p>
        <p><strong>Enseignants :</strong> <?php echo $nbEnseignants; ?></p>
        <p><strong>Cours :</strong> <?php echo $nbCours; ?></p>
        <p><strong>Groupes TD :</strong> <?php echo $nbGroupes; ?></p>
        <p><strong>Promotions :</strong> <?php echo $nbPromotions; ?></p>
        <p><strong>Séances planifiées :</strong> <?php echo $nbSeances; ?></p>
    </section>

    <section class="card">
        <h2>Actions rapides</h2>
        <p><a href="utilisateurs.php">Gérer les utilisateurs</a></p>
        <p><a href="cours.php">Consulter les cours</a></p>
        <p><a href="inscriptions.php">Gérer les inscriptions</a></p>
        <p><a href="emploi_du_temps.php">Voir l’emploi du temps</a></p>
    </section>

</main>

<?php include("../includes/footer.php"); ?>

<script src="../assets/js/script.js"></script>

</body>
</html>