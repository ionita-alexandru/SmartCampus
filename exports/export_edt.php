<?php
include("../includes/auth.php");
include("../includes/db.php");

$id_utilisateur = $_SESSION["id_utilisateur"];
$role = $_SESSION["role"];

if ($role == "etudiant") {
    $sql = "
    SELECT s.date_seance, s.heure_debut, s.heure_fin, c.nom_cours, sa.nom_salle, c.type_cours
    FROM seances s
    JOIN cours c ON s.id_cours = c.id_cours
    JOIN salles sa ON s.id_salle = sa.id_salle
    JOIN etudiants e ON e.id_utilisateur = $id_utilisateur
    JOIN groupes g ON e.id_groupe = g.id_groupe
    WHERE s.id_groupe = g.id_groupe OR s.id_amphi = g.id_amphi
    ORDER BY s.date_seance, s.heure_debut
    ";
} else {
    $sql = "
    SELECT s.date_seance, s.heure_debut, s.heure_fin, c.nom_cours, sa.nom_salle, c.type_cours
    FROM seances s
    JOIN cours c ON s.id_cours = c.id_cours
    JOIN salles sa ON s.id_salle = sa.id_salle
    ORDER BY s.date_seance, s.heure_debut
    ";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Emploi du temps PDF</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main>
    <section class="card">
        <h1>Emploi du temps</h1>
        <p><strong>Utilisateur :</strong> <?php echo $_SESSION["prenom"] . " " . $_SESSION["nom"]; ?></p>

        <table>
            <tr>
                <th>Date</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Cours</th>
                <th>Type</th>
                <th>Salle</th>
            </tr>

            <?php while ($seance = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $seance["date_seance"]; ?></td>
                    <td><?php echo $seance["heure_debut"]; ?></td>
                    <td><?php echo $seance["heure_fin"]; ?></td>
                    <td><?php echo $seance["nom_cours"]; ?></td>
                    <td><?php echo $seance["type_cours"]; ?></td>
                    <td><?php echo $seance["nom_salle"]; ?></td>
                </tr>
            <?php } ?>
        </table>

        <br>
        <button onclick="window.print()">Exporter en PDF</button>
    </section>
</main>

</body>
</html>