<?php
include("../includes/auth.php");
include("../includes/db.php");

$sql = "
    SELECT 
        c.code_cours,
        c.nom_cours,
        c.type_cours,
        p.nom_promotion,
        c.ects,
        c.coefficient,
        c.capacite_max,
        u.nom,
        u.prenom
    FROM cours c
    JOIN promotions p ON c.id_promotion = p.id_promotion
    JOIN enseignants e ON c.id_enseignant = e.id_enseignant
    JOIN utilisateurs u ON e.id_utilisateur = u.id_utilisateur
    ORDER BY p.nom_promotion, c.type_cours, c.nom_cours
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Cours - SmartCampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="connected-page">

<?php include("../includes/header.php"); ?>

<nav>

    <a href="../index.php">Accueil</a>

    <?php if ($_SESSION["role"] == "admin") { ?>

        <a href="dashboard_admin.php">Dashboard</a>
        <a href="utilisateurs.php">Utilisateurs</a>
        <a href="cours.php">Cours</a>
        <a href="inscriptions.php">Inscriptions</a>
        <a href="emploi_du_temps.php">Emploi du temps</a>
        <a href="messagerie.php">Messagerie</a>

    <?php } ?>

    <?php if ($_SESSION["role"] == "enseignant") { ?>

        <a href="dashboard_enseignant.php">Dashboard</a>
        <a href="cours.php">Cours</a>
        <a href="emploi_du_temps.php">Emploi du temps</a>
        <a href="notes.php">Notes</a>
        <a href="presences.php">Présences</a>
        <a href="messagerie.php">Messagerie</a>

    <?php } ?>

    <?php if ($_SESSION["role"] == "etudiant") { ?>

        <a href="dashboard_etudiant.php">Dashboard</a>
        <a href="cours.php">Cours</a>
        <a href="inscriptions.php">Mes inscriptions</a>
        <a href="emploi_du_temps.php">Emploi du temps</a>
        <a href="notes.php">Notes</a>
        <a href="presences.php">Présences</a>
        <a href="messagerie.php">Messagerie</a>

    <?php } ?>

    <a href="../actions/logout.php">Déconnexion</a>

</nav>

<main>

    <section class="card">
        <h2>Catalogue des cours</h2>

        <table>
            <tr>
                <th>Code</th>
                <th>Cours</th>
                <th>Type</th>
                <th>Promotion</th>
                <th>Enseignant</th>
                <th>ECTS</th>
                <th>Coefficient</th>
                <th>Capacité</th>
            </tr>

            <?php while ($cours = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $cours["code_cours"]; ?></td>
                    <td><?php echo $cours["nom_cours"]; ?></td>
                    <td><?php echo $cours["type_cours"]; ?></td>
                    <td><?php echo $cours["nom_promotion"]; ?></td>
                    <td><?php echo $cours["prenom"] . " " . $cours["nom"]; ?></td>
                    <td><?php echo $cours["ects"]; ?></td>
                    <td><?php echo $cours["coefficient"]; ?></td>
                    <td><?php echo $cours["capacite_max"]; ?></td>
                </tr>
            <?php } ?>
        </table>
    </section>

</main>

<?php include("../includes/footer.php"); ?>

<script src="../assets/js/script.js"></script>

</body>
</html>