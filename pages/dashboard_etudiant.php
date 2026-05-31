<?php

include("../includes/auth.php");
verifierRole(["etudiant"]);
include("../includes/db.php");

$id_utilisateur = $_SESSION["id_utilisateur"];

$sqlEtudiant = "
    SELECT 
        e.id_etudiant,
        g.nom_groupe,
        p.nom_promotion,
        a.nom_amphi
    FROM etudiants e
    JOIN groupes g ON e.id_groupe = g.id_groupe
    JOIN promotions p ON e.id_promotion = p.id_promotion
    JOIN amphis a ON g.id_amphi = a.id_amphi
    WHERE e.id_utilisateur = $id_utilisateur
";

$resultEtudiant = mysqli_query($conn, $sqlEtudiant);
$etudiant = mysqli_fetch_assoc($resultEtudiant);

$id_etudiant = $etudiant["id_etudiant"];

$sqlCours = "
    SELECT COUNT(*) AS total
    FROM inscriptions
    WHERE id_etudiant = $id_etudiant
";

$nbCours = mysqli_fetch_assoc(mysqli_query($conn, $sqlCours))["total"];

$sqlNotes = "
    SELECT ROUND(AVG(note),2) AS moyenne
    FROM notes
    WHERE id_etudiant = $id_etudiant
";

$moyenne = mysqli_fetch_assoc(mysqli_query($conn, $sqlNotes))["moyenne"];

$sqlPresence = "
    SELECT COUNT(*) AS total
    FROM presences
    WHERE id_etudiant = $id_etudiant
    AND statut = 'present'
";

$nbPresences = mysqli_fetch_assoc(mysqli_query($conn, $sqlPresence))["total"];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Étudiant - SmartCampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="connected-page">

<?php include("../includes/header.php"); ?>

<nav>
    <a href="../index.php">Accueil</a>
    <a href="dashboard_etudiant.php">Dashboard</a>
    <a href="cours.php">Cours</a>
    <a href="inscriptions.php">Mes inscriptions</a>
    <a href="emploi_du_temps.php">Emploi du temps</a>
    <a href="notes.php">Notes</a>
    <a href="presences.php">Présences</a>
    <a href="messagerie.php">Messagerie</a>
    <a href="../actions/logout.php">Déconnexion</a>
</nav>

<main>

    <section class="card">
        <h2>Informations étudiant</h2>

        <p><strong>Nom :</strong>
            <?php echo $_SESSION["prenom"] . " " . $_SESSION["nom"]; ?>
        </p>

        <p><strong>Promotion :</strong>
            <?php echo $etudiant["nom_promotion"]; ?>
        </p>

        <p><strong>Groupe TD :</strong>
            <?php echo $etudiant["nom_groupe"]; ?>
        </p>

        <p><strong>Amphi :</strong>
            <?php echo $etudiant["nom_amphi"]; ?>
        </p>
    </section>

    <section class="card">
        <h2>Statistiques académiques</h2>

        <p><strong>Cours inscrits :</strong> <?php echo $nbCours; ?></p>

        <p><strong>Moyenne générale :</strong>
            <?php echo $moyenne ? $moyenne . "/20" : "Aucune note"; ?>
        </p>

        <p><strong>Présences validées :</strong>
            <?php echo $nbPresences; ?>
        </p>
    </section>

</main>

<?php include("../includes/footer.php"); ?>

<script src="../assets/js/script.js"></script>

</body>
</html>