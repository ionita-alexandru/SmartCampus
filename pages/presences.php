<?php

include("../includes/auth.php");
include("../includes/db.php");

$role = $_SESSION["role"];
$id_utilisateur = $_SESSION["id_utilisateur"];

$message = "";

if ($role == "etudiant" && isset($_POST["valider_presence"])) {

    $code = $_POST["code_presence"];

    $sqlEtudiant = "
        SELECT id_etudiant
        FROM etudiants
        WHERE id_utilisateur = $id_utilisateur
    ";

    $resultEtudiant = mysqli_query($conn, $sqlEtudiant);
    $etudiant = mysqli_fetch_assoc($resultEtudiant);

    $id_etudiant = $etudiant["id_etudiant"];

    $sqlPresence = "
        SELECT p.id_presence
        FROM presences p
        JOIN seances s ON p.id_seance = s.id_seance
        WHERE p.id_etudiant = $id_etudiant
        AND s.code_presence = '$code'
    ";

    $resultPresence = mysqli_query($conn, $sqlPresence);

    if (mysqli_num_rows($resultPresence) == 1) {

        $presence = mysqli_fetch_assoc($resultPresence);

        mysqli_query($conn, "
            UPDATE presences
            SET statut = 'present',
                code_saisi = '$code',
                date_validation = NOW()
            WHERE id_presence = " . $presence["id_presence"]
        );

        $message = "Présence validée avec succès.";

    } else {

        $message = "Code de présence invalide.";

    }
}

if ($role == "etudiant") {

    $sql = "
        SELECT 
            c.nom_cours,
            s.date_seance,
            s.heure_debut,
            s.heure_fin,
            p.statut
        FROM presences p
        JOIN seances s ON p.id_seance = s.id_seance
        JOIN cours c ON s.id_cours = c.id_cours
        JOIN etudiants e ON p.id_etudiant = e.id_etudiant
        WHERE e.id_utilisateur = $id_utilisateur
        ORDER BY s.date_seance DESC
    ";

} elseif ($role == "enseignant") {

    $sql = "
        SELECT 
            c.nom_cours,
            s.date_seance,
            s.heure_debut,
            s.heure_fin,
            COUNT(CASE WHEN p.statut = 'present' THEN 1 END) AS presents,
            COUNT(*) AS total
        FROM presences p
        JOIN seances s ON p.id_seance = s.id_seance
        JOIN cours c ON s.id_cours = c.id_cours
        JOIN enseignants e ON c.id_enseignant = e.id_enseignant
        WHERE e.id_utilisateur = $id_utilisateur
        GROUP BY s.id_seance
        ORDER BY s.date_seance DESC
    ";

} else {

    $sql = "
        SELECT 
            COUNT(*) AS total_presences
        FROM presences
        WHERE statut = 'present'
    ";
}

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Présences - SmartCampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

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

<?php if ($role == "etudiant") { ?>

<section class="card">

    <h2>Valider une présence</h2>

    <form method="post">

        <label>Code donné par le professeur</label><br>
        <input type="text" name="code_presence" required><br><br>

        <button type="submit" name="valider_presence">
            Valider ma présence
        </button>

    </form>

</section>

<section class="card">

    <h2>Historique des présences</h2>

    <table>

        <tr>
            <th>Cours</th>
            <th>Date</th>
            <th>Début</th>
            <th>Fin</th>
            <th>Statut</th>
        </tr>

        <?php while($presence = mysqli_fetch_assoc($result)) { ?>

        <tr>

            <td><?php echo $presence["nom_cours"]; ?></td>

            <td><?php echo $presence["date_seance"]; ?></td>

            <td><?php echo $presence["heure_debut"]; ?></td>

            <td><?php echo $presence["heure_fin"]; ?></td>

            <td><?php echo $presence["statut"]; ?></td>

        </tr>

        <?php } ?>

    </table>

    <br>

<a href="../exports/export_presences.php" target="_blank">
    <button>Exporter mes présences en PDF</button>
</a>

</section>

<?php } ?>

<?php if ($role == "enseignant") { ?>

<section class="card">

    <h2>Suivi des présences</h2>

    <table>

        <tr>
            <th>Cours</th>
            <th>Date</th>
            <th>Début</th>
            <th>Fin</th>
            <th>Présents</th>
            <th>Total</th>
        </tr>

        <?php while($presence = mysqli_fetch_assoc($result)) { ?>

        <tr>

            <td><?php echo $presence["nom_cours"]; ?></td>

            <td><?php echo $presence["date_seance"]; ?></td>

            <td><?php echo $presence["heure_debut"]; ?></td>

            <td><?php echo $presence["heure_fin"]; ?></td>

            <td><?php echo $presence["presents"]; ?></td>

            <td><?php echo $presence["total"]; ?></td>

        </tr>

        <?php } ?>

    </table>

</section>

<?php } ?>

<?php if ($role == "admin") {

    $admin = mysqli_fetch_assoc($result);

?>

<section class="card">

    <h2>Statistiques de présence</h2>

    <p>
        <strong>Présences validées :</strong>
        <?php echo $admin["total_presences"]; ?>
    </p>

</section>

<?php } ?>

</main>

<?php include("../includes/footer.php"); ?>

<script src="../assets/js/script.js"></script>

</body>
</html>