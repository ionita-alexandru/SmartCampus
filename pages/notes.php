<?php

include("../includes/auth.php");
include("../includes/db.php");

$role = $_SESSION["role"];
$id_utilisateur = $_SESSION["id_utilisateur"];
$message = "";

if ($role == "etudiant") {

    $sqlEtudiant = "
        SELECT id_etudiant
        FROM etudiants
        WHERE id_utilisateur = $id_utilisateur
    ";

    $etudiant = mysqli_fetch_assoc(mysqli_query($conn, $sqlEtudiant));
    $id_etudiant = $etudiant["id_etudiant"];

    $resultNotes = mysqli_query($conn, "
        SELECT 
            c.nom_cours,
            c.ects,
            n.type_evaluation,
            n.note,
            n.coefficient_note,
            n.verrouille
        FROM notes n
        JOIN cours c ON n.id_cours = c.id_cours
        WHERE n.id_etudiant = $id_etudiant
        ORDER BY c.nom_cours, n.type_evaluation
    ");

    $notesParCours = [];

    while ($row = mysqli_fetch_assoc($resultNotes)) {
        $notesParCours[$row["nom_cours"]][] = $row;
    }
}

if ($role == "enseignant") {

    $sqlProf = "
        SELECT id_enseignant
        FROM enseignants
        WHERE id_utilisateur = $id_utilisateur
    ";

    $prof = mysqli_fetch_assoc(mysqli_query($conn, $sqlProf));
    $id_enseignant = $prof["id_enseignant"];

    if (isset($_POST["ajouter_note"])) {

        $id_etudiant = $_POST["id_etudiant"];
        $id_cours = $_POST["id_cours"];
        $type_evaluation = $_POST["type_evaluation"];
        $note = $_POST["note"];

        $coef = ($type_evaluation == "CC") ? 0.25 : 0.75;

        mysqli_query($conn, "
            INSERT INTO notes 
            (id_etudiant, id_cours, note, verrouille, type_evaluation, coefficient_note)
            VALUES 
            ($id_etudiant, $id_cours, $note, 0, '$type_evaluation', $coef)
        ");

        $message = "Note ajoutée avec succès.";
    }

    if (isset($_POST["verrouiller_note"])) {

        $id_note = $_POST["id_note"];

        mysqli_query($conn, "
            UPDATE notes
            SET verrouille = 1
            WHERE id_note = $id_note
        ");

        $message = "Note verrouillée.";
    }

    $coursProf = mysqli_query($conn, "
        SELECT id_cours, nom_cours
        FROM cours
        WHERE id_enseignant = $id_enseignant
        ORDER BY nom_cours
    ");

    $etudiants = mysqli_query($conn, "
        SELECT DISTINCT 
            e.id_etudiant,
            u.nom,
            u.prenom,
            p.nom_promotion,
            g.nom_groupe
        FROM etudiants e
        JOIN utilisateurs u ON e.id_utilisateur = u.id_utilisateur
        JOIN promotions p ON e.id_promotion = p.id_promotion
        JOIN groupes g ON e.id_groupe = g.id_groupe
        JOIN inscriptions i ON e.id_etudiant = i.id_etudiant
        JOIN cours c ON i.id_cours = c.id_cours
        WHERE c.id_enseignant = $id_enseignant
        ORDER BY p.nom_promotion, g.nom_groupe, u.nom
        LIMIT 300
    ");

    $notesRecentes = mysqli_query($conn, "
        SELECT 
            n.id_note,
            u.nom,
            u.prenom,
            c.nom_cours,
            n.type_evaluation,
            n.note,
            n.coefficient_note,
            n.verrouille
        FROM notes n
        JOIN etudiants e ON n.id_etudiant = e.id_etudiant
        JOIN utilisateurs u ON e.id_utilisateur = u.id_utilisateur
        JOIN cours c ON n.id_cours = c.id_cours
        WHERE c.id_enseignant = $id_enseignant
        ORDER BY n.id_note DESC
        LIMIT 50
    ");
}

if ($role == "admin") {

    $statsNotes = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT 
            COUNT(*) AS total_notes,
            ROUND(AVG(note), 2) AS moyenne_generale
        FROM notes
    "));
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notes - SmartCampus</title>
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

<?php if ($role == "etudiant") { ?>

<section class="card">
    <h2>Mes notes par matière</h2>

    <?php if (empty($notesParCours)) { ?>

        <p>Aucune note disponible pour le moment.</p>

    <?php } else { ?>

        <?php foreach ($notesParCours as $cours => $notes) { ?>

            <?php
            $total = 0;
            $ects = $notes[0]["ects"];

            foreach ($notes as $n) {
                $total += $n["note"] * $n["coefficient_note"];
            }
            ?>

            <div class="note-block">
                <h3>
                    <?php echo $cours; ?>
                    <span class="badge"><?php echo $ects; ?> ECTS</span>
                </h3>

                <table>
                    <tr>
                        <th>Évaluation</th>
                        <th>Coefficient</th>
                        <th>Note</th>
                        <th>Calcul</th>
                        <th>Statut</th>
                    </tr>

                    <?php foreach ($notes as $note) { ?>
                    <tr>
                        <td>
                            <?php echo $note["type_evaluation"] == "CC" ? "Contrôle continu" : "Examen"; ?>
                        </td>
                        <td><?php echo $note["coefficient_note"] * 100; ?>%</td>
                        <td><?php echo $note["note"]; ?>/20</td>
                        <td><?php echo round($note["note"] * $note["coefficient_note"], 2); ?></td>
                        <td><?php echo $note["verrouille"] ? "Verrouillée" : "Provisoire"; ?></td>
                    </tr>
                    <?php } ?>

                    <tr>
                        <th colspan="2">Moyenne pondérée</th>
                        <th colspan="3"><?php echo round($total, 2); ?>/20</th>
                    </tr>
                </table>
            </div>

        <?php } ?>

    <?php } ?>

    <br>
    <a href="../exports/export_notes.php" target="_blank">
        <button>Exporter mes notes en PDF</button>
    </a>

</section>

<?php } ?>

<?php if ($role == "enseignant") { ?>

<section class="card">
    <h2>Ajouter une note</h2>

    <form method="post">

        <label>Étudiant</label><br>
        <select name="id_etudiant" required>
            <?php while ($etu = mysqli_fetch_assoc($etudiants)) { ?>
                <option value="<?php echo $etu["id_etudiant"]; ?>">
                    <?php
                    echo $etu["prenom"] . " " . $etu["nom"] .
                    " - " . $etu["nom_promotion"] .
                    " - " . $etu["nom_groupe"];
                    ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label>Cours</label><br>
        <select name="id_cours" required>
            <?php while ($cours = mysqli_fetch_assoc($coursProf)) { ?>
                <option value="<?php echo $cours["id_cours"]; ?>">
                    <?php echo $cours["nom_cours"]; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label>Type d’évaluation</label><br>
        <select name="type_evaluation" required>
            <option value="CC">Contrôle continu - 25%</option>
            <option value="EXAMEN">Examen - 75%</option>
        </select><br><br>

        <label>Note /20</label><br>
        <input type="number" name="note" min="0" max="20" step="0.1" required><br><br>

        <button type="submit" name="ajouter_note">Ajouter la note</button>

    </form>
</section>

<section class="card">
    <h2>Dernières notes saisies</h2>

    <table>
        <tr>
            <th>Étudiant</th>
            <th>Cours</th>
            <th>Évaluation</th>
            <th>Note</th>
            <th>Coefficient</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>

        <?php while ($note = mysqli_fetch_assoc($notesRecentes)) { ?>
        <tr>
            <td><?php echo $note["prenom"] . " " . $note["nom"]; ?></td>
            <td><?php echo $note["nom_cours"]; ?></td>
            <td><?php echo $note["type_evaluation"]; ?></td>
            <td><?php echo $note["note"]; ?>/20</td>
            <td><?php echo $note["coefficient_note"] * 100; ?>%</td>
            <td><?php echo $note["verrouille"] ? "Verrouillée" : "Provisoire"; ?></td>
            <td>
                <?php if (!$note["verrouille"]) { ?>
                    <form method="post">
                        <input type="hidden" name="id_note" value="<?php echo $note["id_note"]; ?>">
                        <button type="submit" name="verrouiller_note">Verrouiller</button>
                    </form>
                <?php } else { ?>
                    Aucune action
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</section>

<?php } ?>

<?php if ($role == "admin") { ?>

<section class="card">
    <h2>Statistiques des notes</h2>

    <p>
        <strong>Nombre total de notes :</strong>
        <?php echo $statsNotes["total_notes"]; ?>
    </p>

    <p>
        <strong>Moyenne générale :</strong>
        <?php echo $statsNotes["moyenne_generale"]; ?>/20
    </p>
</section>

<?php } ?>

</main>

<?php include("../includes/footer.php"); ?>

<script src="../assets/js/script.js"></script>

</body>
</html>