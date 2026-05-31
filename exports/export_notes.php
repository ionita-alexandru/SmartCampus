<?php
include("../includes/auth.php");
include("../includes/db.php");

$id_utilisateur = $_SESSION["id_utilisateur"];

$sql = "
SELECT c.nom_cours, n.note, n.verrouille
FROM notes n
JOIN cours c ON n.id_cours = c.id_cours
JOIN etudiants e ON n.id_etudiant = e.id_etudiant
WHERE e.id_utilisateur = $id_utilisateur
ORDER BY c.nom_cours
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Relevé de notes</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main>
    <section class="card">
        <h1>Relevé de notes</h1>
        <p><strong>Étudiant :</strong> <?php echo $_SESSION["prenom"] . " " . $_SESSION["nom"]; ?></p>

        <table>
            <tr>
                <th>Cours</th>
                <th>Note</th>
                <th>Statut</th>
            </tr>

            <?php while ($note = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $note["nom_cours"]; ?></td>
                    <td><?php echo $note["note"]; ?>/20</td>
                    <td><?php echo $note["verrouille"] ? "Verrouillée" : "Provisoire"; ?></td>
                </tr>
            <?php } ?>
        </table>

        <br>
        <button onclick="window.print()">Exporter en PDF</button>
    </section>
</main>

</body>
</html>