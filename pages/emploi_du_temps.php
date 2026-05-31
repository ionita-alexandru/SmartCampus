<?php

include("../includes/auth.php");
include("../includes/db.php");

$id_utilisateur = $_SESSION["id_utilisateur"];
$role = $_SESSION["role"];

$jours = [
    "Monday" => "Lundi",
    "Tuesday" => "Mardi",
    "Wednesday" => "Mercredi",
    "Thursday" => "Jeudi",
    "Friday" => "Vendredi"
];

if (!isset($_GET["semaine"])) {
    $semaine = date("W");
} else {
    $semaine = intval($_GET["semaine"]);
}

if ($role == "etudiant") {

    $sql = "
    SELECT 
        s.date_seance,
        s.heure_debut,
        s.heure_fin,
        c.nom_cours,
        c.type_cours,
        sa.nom_salle,
        u.nom,
        u.prenom
    FROM seances s
    JOIN cours c ON s.id_cours = c.id_cours
    JOIN salles sa ON s.id_salle = sa.id_salle
    JOIN enseignants e ON c.id_enseignant = e.id_enseignant
    JOIN utilisateurs u ON e.id_utilisateur = u.id_utilisateur
    JOIN etudiants etu ON etu.id_utilisateur = $id_utilisateur
    WHERE WEEK(s.date_seance,1) = $semaine
    ORDER BY s.date_seance, s.heure_debut
    ";

} elseif ($role == "enseignant") {

    $sql = "
    SELECT 
        s.date_seance,
        s.heure_debut,
        s.heure_fin,
        c.nom_cours,
        c.type_cours,
        sa.nom_salle
    FROM seances s
    JOIN cours c ON s.id_cours = c.id_cours
    JOIN salles sa ON s.id_salle = sa.id_salle
    JOIN enseignants e ON c.id_enseignant = e.id_enseignant
    WHERE e.id_utilisateur = $id_utilisateur
    AND WEEK(s.date_seance,1) = $semaine
    ORDER BY s.date_seance, s.heure_debut
    ";

} else {

    $sql = "
    SELECT 
        s.date_seance,
        s.heure_debut,
        s.heure_fin,
        c.nom_cours,
        c.type_cours,
        sa.nom_salle
    FROM seances s
    JOIN cours c ON s.id_cours = c.id_cours
    JOIN salles sa ON s.id_salle = sa.id_salle
    WHERE WEEK(s.date_seance,1) = $semaine
    ORDER BY s.date_seance, s.heure_debut
    ";
}

$result = mysqli_query($conn, $sql);

$emploi = [];

while ($row = mysqli_fetch_assoc($result)) {

    $jour = date("l", strtotime($row["date_seance"]));

    $emploi[$jour][] = $row;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Emploi du temps - SmartCampus</title>
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

    <h2>Emploi du temps — Semaine <?php echo $semaine; ?></h2>

    <div class="week-navigation">

        <a href="?semaine=<?php echo $semaine - 1; ?>">
            <button>← Semaine précédente</button>
        </a>

        <a href="?semaine=<?php echo $semaine + 1; ?>">
            <button>Semaine suivante →</button>
        </a>

    </div>

</section>

<div class="edt-grid">

<?php foreach ($jours as $jourAnglais => $jourFrancais) { ?>

    <div class="jour-column">

        <h3><?php echo $jourFrancais; ?></h3>

        <?php if (isset($emploi[$jourAnglais])) { ?>

            <?php foreach ($emploi[$jourAnglais] as $cours) { ?>

                <div class="cours-card <?php echo strtolower($cours["type_cours"]); ?>">

                    <strong><?php echo $cours["nom_cours"]; ?></strong>

                    <p>
                        <?php echo $cours["heure_debut"]; ?>
                        →
                        <?php echo $cours["heure_fin"]; ?>
                    </p>

                    <p>
                        Salle :
                        <?php echo $cours["nom_salle"]; ?>
                    </p>

                    <p>
                        Type :
                        <?php echo $cours["type_cours"]; ?>
                    </p>

                    <?php if ($role == "etudiant") { ?>

                        <p>
                            Prof :
                            <?php echo $cours["prenom"] . " " . $cours["nom"]; ?>
                        </p>

                    <?php } ?>

                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="empty-day">
                Aucun cours
            </div>

        <?php } ?>

    </div>

<?php } ?>

</div>

<section class="card">

    <a href="../exports/export_edt.php" target="_blank">
        <button>Exporter l'emploi du temps en PDF</button>
    </a>

</section>

</main>

<?php include("../includes/footer.php"); ?>

</body>
</html>