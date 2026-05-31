<?php
include("../includes/auth.php");
include("../includes/db.php");

$id_utilisateur = $_SESSION["id_utilisateur"];

$sql = "
SELECT c.nom_cours, s.date_seance, s.heure_debut, s.heure_fin, p.statut
FROM presences p
JOIN seances s ON p.id_seance = s.id_seance
JOIN cours c ON s.id_cours = c.id_cours
JOIN etudiants e ON p.id_etudiant = e.id_etudiant
WHERE e.id_utilisateur = $id_utilisateur
ORDER BY s.date_seance DESC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Présences PDF</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main>
    <section class="card">
        <h1>Relevé de présences</h1>
        <p><strong>Étudiant :</strong> <?php echo $_SESSION["prenom"] . " " . $_SESSION["nom"]; ?></p>

        <table>
            <tr>
                <th>Cours</th>
                <th>Date</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Statut</th>
            </tr>

            <?php while ($presence = mysqli_fetch_assoc($result)) { ?>
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
        <button onclick="window.print()">Exporter en PDF</button>
    </section>
</main>

</body>
</html>