<?php

include("../includes/auth.php");
verifierRole(["enseignant"]);
include("../includes/db.php");

$id_utilisateur = $_SESSION["id_utilisateur"];

$sqlProf = "
    SELECT id_enseignant
    FROM enseignants
    WHERE id_utilisateur = $id_utilisateur
";

$resultProf = mysqli_query($conn, $sqlProf);
$prof = mysqli_fetch_assoc($resultProf);

$id_enseignant = $prof["id_enseignant"];

$sqlCours = "
    SELECT COUNT(*) AS total
    FROM cours
    WHERE id_enseignant = $id_enseignant
";

$nbCours = mysqli_fetch_assoc(mysqli_query($conn, $sqlCours))["total"];

$sqlSeances = "
    SELECT COUNT(*) AS total
    FROM seances s
    JOIN cours c ON s.id_cours = c.id_cours
    WHERE c.id_enseignant = $id_enseignant
";

$nbSeances = mysqli_fetch_assoc(mysqli_query($conn, $sqlSeances))["total"];

$sqlEtudiants = "
    SELECT COUNT(DISTINCT i.id_etudiant) AS total
    FROM inscriptions i
    JOIN cours c ON i.id_cours = c.id_cours
    WHERE c.id_enseignant = $id_enseignant
";

$nbEtudiants = mysqli_fetch_assoc(mysqli_query($conn, $sqlEtudiants))["total"];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Enseignant - SmartCampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="connected-page">

<?php include("../includes/header.php"); ?>

<nav>
    <a href="../index.php">Accueil</a>
    <a href="dashboard_enseignant.php">Dashboard</a>
    <a href="cours.php">Cours</a>
    <a href="emploi_du_temps.php">Emploi du temps</a>
    <a href="notes.php">Notes</a>
    <a href="presences.php">Présences</a>
    <a href="messagerie.php">Messagerie</a>
    <a href="../actions/logout.php">Déconnexion</a>
</nav>

<main>

    <section class="card">

        <h2>Informations enseignant</h2>

        <p>
            <strong>Nom :</strong>
            <?php echo $_SESSION["prenom"] . " " . $_SESSION["nom"]; ?>
        </p>

        <p>
            <strong>Cours enseignés :</strong>
            <?php echo $nbCours; ?>
        </p>

        <p>
            <strong>Séances planifiées :</strong>
            <?php echo $nbSeances; ?>
        </p>

        <p>
            <strong>Étudiants suivis :</strong>
            <?php echo $nbEtudiants; ?>
        </p>

    </section>

</main>

<?php include("../includes/footer.php"); ?>

<script src="../assets/js/script.js"></script>

</body>
</html>