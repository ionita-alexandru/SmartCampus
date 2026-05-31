<?php

include("../includes/auth.php");
include("../includes/db.php");

$id_utilisateur = $_SESSION["id_utilisateur"];
$message = "";

if (isset($_POST["envoyer_message"])) {

    $id_destinataire = $_POST["id_destinataire"];
    $sujet = $_POST["sujet"];
    $contenu = $_POST["contenu"];

    mysqli_query($conn, "
        INSERT INTO messages (id_expediteur, id_destinataire, sujet, contenu)
        VALUES ($id_utilisateur, $id_destinataire, '$sujet', '$contenu')
    ");

    $message = "Message envoyé avec succès.";
}

$utilisateurs = mysqli_query($conn, "
    SELECT id_utilisateur, nom, prenom, role
    FROM utilisateurs
    WHERE id_utilisateur != $id_utilisateur
    AND actif = 1
    ORDER BY role, nom
    LIMIT 100
");

$messagesRecus = mysqli_query($conn, "
    SELECT 
        m.sujet,
        m.contenu,
        m.date_envoi,
        u.nom,
        u.prenom,
        u.role
    FROM messages m
    JOIN utilisateurs u ON m.id_expediteur = u.id_utilisateur
    WHERE m.id_destinataire = $id_utilisateur
    ORDER BY m.date_envoi DESC
    LIMIT 20
");

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messagerie - SmartCampus</title>
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

<?php if ($message != "") { ?>
    <section class="card">
        <p><strong><?php echo $message; ?></strong></p>
    </section>
<?php } ?>

<section class="card">

    <h2>Nouveau message</h2>

    <form method="post">

        <label>Destinataire</label><br>
        <select name="id_destinataire" required>
            <?php while ($user = mysqli_fetch_assoc($utilisateurs)) { ?>
                <option value="<?php echo $user["id_utilisateur"]; ?>">
                    <?php echo $user["prenom"] . " " . $user["nom"] . " (" . $user["role"] . ")"; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label>Sujet</label><br>
        <input type="text" name="sujet" required><br><br>

        <label>Message</label><br>
        <textarea name="contenu" rows="6" required></textarea><br><br>

        <button type="submit" name="envoyer_message">
            Envoyer
        </button>

    </form>

</section>

<section class="card">

    <h2>Messages reçus</h2>

    <table>
        <tr>
            <th>Expéditeur</th>
            <th>Rôle</th>
            <th>Sujet</th>
            <th>Message</th>
            <th>Date</th>
        </tr>

        <?php while ($msg = mysqli_fetch_assoc($messagesRecus)) { ?>
            <tr>
                <td><?php echo $msg["prenom"] . " " . $msg["nom"]; ?></td>
                <td><?php echo $msg["role"]; ?></td>
                <td><?php echo $msg["sujet"]; ?></td>
                <td><?php echo $msg["contenu"]; ?></td>
                <td><?php echo $msg["date_envoi"]; ?></td>
            </tr>
        <?php } ?>
    </table>

</section>

</main>

<?php include("../includes/footer.php"); ?>

<script src="../assets/js/script.js"></script>

</body>
</html>