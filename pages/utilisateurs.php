<?php

include("../includes/auth.php");
verifierRole(["admin"]);

include("../includes/db.php");

$sql = "
    SELECT 
        u.id_utilisateur,
        u.nom,
        u.prenom,
        u.email,
        u.role,
        u.actif
    FROM utilisateurs u
    ORDER BY u.role, u.nom
";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Utilisateurs - SmartCampus</title>
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

    <h2>Liste des utilisateurs</h2>

    <table>

        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Statut</th>
        </tr>

        <?php while($user = mysqli_fetch_assoc($result)) { ?>

        <tr>

            <td><?php echo $user["id_utilisateur"]; ?></td>

            <td><?php echo $user["nom"]; ?></td>

            <td><?php echo $user["prenom"]; ?></td>

            <td><?php echo $user["email"]; ?></td>

            <td><?php echo $user["role"]; ?></td>

            <td>

                <?php
                    echo $user["actif"]
                    ? "Actif"
                    : "Inactif";
                ?>

            </td>

        </tr>

        <?php } ?>

    </table>

</section>

</main>

<?php include("../includes/footer.php"); ?>

<script src="../assets/js/script.js"></script>

</body>
</html>