<?php

include("../includes/auth.php");
include("../includes/db.php");

$role = $_SESSION["role"];
$id_utilisateur = $_SESSION["id_utilisateur"];

$message = "";

if ($role == "etudiant") {

    $sqlEtudiant = "
        SELECT id_etudiant, id_promotion
        FROM etudiants
        WHERE id_utilisateur = $id_utilisateur
    ";

    $resultEtudiant = mysqli_query($conn, $sqlEtudiant);
    $etudiant = mysqli_fetch_assoc($resultEtudiant);

    $id_etudiant = $etudiant["id_etudiant"];
    $id_promotion = $etudiant["id_promotion"];

    if (isset($_POST["inscrire_option"])) {

        $id_cours = $_POST["id_cours"];

        $check = "
            SELECT *
            FROM inscriptions
            WHERE id_etudiant = $id_etudiant
            AND id_cours = $id_cours
        ";

        $resultCheck = mysqli_query($conn, $check);

        if (mysqli_num_rows($resultCheck) > 0) {

            $message = "Vous êtes déjà inscrit à ce cours.";

        } else {

            $capacite = "
                SELECT 
                    c.capacite_max,
                    COUNT(i.id_inscription) AS inscrits
                FROM cours c
                LEFT JOIN inscriptions i ON c.id_cours = i.id_cours
                WHERE c.id_cours = $id_cours
                GROUP BY c.id_cours
            ";

            $resCapacite = mysqli_query($conn, $capacite);
            $dataCapacite = mysqli_fetch_assoc($resCapacite);

            if ($dataCapacite["inscrits"] >= $dataCapacite["capacite_max"]) {
                $message = "Inscription impossible : capacité maximale atteinte.";
            } else {
                mysqli_query($conn, "
                    INSERT INTO inscriptions (id_etudiant, id_cours)
                    VALUES ($id_etudiant, $id_cours)
                ");

                $message = "Inscription au cours optionnel validée.";
            }
        }
    }

    $sqlOptions = "
        SELECT 
            c.id_cours,
            c.nom_cours,
            c.ects,
            c.capacite_max,
            COUNT(i.id_inscription) AS inscrits
        FROM cours c
        LEFT JOIN inscriptions i ON c.id_cours = i.id_cours
        WHERE c.type_cours = 'OPTIONNEL'
        AND c.id_promotion = $id_promotion
        GROUP BY c.id_cours
        ORDER BY c.nom_cours
    ";

    $resultOptions = mysqli_query($conn, $sqlOptions);

    $sqlMesCours = "
        SELECT 
            c.nom_cours,
            c.type_cours,
            c.ects
        FROM inscriptions i
        JOIN cours c ON i.id_cours = c.id_cours
        WHERE i.id_etudiant = $id_etudiant
        ORDER BY c.type_cours, c.nom_cours
    ";

    $resultMesCours = mysqli_query($conn, $sqlMesCours);

} else {

    $sql = "
        SELECT 
            u.prenom,
            u.nom,
            p.nom_promotion,
            g.nom_groupe,
            c.nom_cours,
            c.type_cours,
            i.date_inscription
        FROM inscriptions i
        JOIN etudiants e ON i.id_etudiant = e.id_etudiant
        JOIN utilisateurs u ON e.id_utilisateur = u.id_utilisateur
        JOIN groupes g ON e.id_groupe = g.id_groupe
        JOIN promotions p ON e.id_promotion = p.id_promotion
        JOIN cours c ON i.id_cours = c.id_cours
        ORDER BY p.nom_promotion, g.nom_groupe, u.nom
        LIMIT 300
    ";

    $result = mysqli_query($conn, $sql);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscriptions - SmartCampus</title>
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

    <h2>Cours optionnels disponibles</h2>

    <table>
        <tr>
            <th>Cours</th>
            <th>ECTS</th>
            <th>Places</th>
            <th>Action</th>
        </tr>

        <?php while($cours = mysqli_fetch_assoc($resultOptions)) { ?>

        <tr>
            <td><?php echo $cours["nom_cours"]; ?></td>
            <td><?php echo $cours["ects"]; ?></td>
            <td><?php echo $cours["inscrits"] . " / " . $cours["capacite_max"]; ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="id_cours" value="<?php echo $cours["id_cours"]; ?>">
                    <button type="submit" name="inscrire_option">
                        S'inscrire
                    </button>
                </form>
            </td>
        </tr>

        <?php } ?>
    </table>

</section>

<section class="card">

    <h2>Mes inscriptions</h2>

    <table>
        <tr>
            <th>Cours</th>
            <th>Type</th>
            <th>ECTS</th>
        </tr>

        <?php while($cours = mysqli_fetch_assoc($resultMesCours)) { ?>

        <tr>
            <td><?php echo $cours["nom_cours"]; ?></td>
            <td><?php echo $cours["type_cours"]; ?></td>
            <td><?php echo $cours["ects"]; ?></td>
        </tr>

        <?php } ?>
    </table>

</section>

<?php } else { ?>

<section class="card">

    <h2>Liste des inscriptions</h2>

    <table>
        <tr>
            <th>Étudiant</th>
            <th>Promotion</th>
            <th>Groupe</th>
            <th>Cours</th>
            <th>Type</th>
            <th>Date</th>
        </tr>

        <?php while($inscription = mysqli_fetch_assoc($result)) { ?>

        <tr>
            <td><?php echo $inscription["prenom"] . " " . $inscription["nom"]; ?></td>
            <td><?php echo $inscription["nom_promotion"]; ?></td>
            <td><?php echo $inscription["nom_groupe"]; ?></td>
            <td><?php echo $inscription["nom_cours"]; ?></td>
            <td><?php echo $inscription["type_cours"]; ?></td>
            <td><?php echo $inscription["date_inscription"]; ?></td>
        </tr>

        <?php } ?>
    </table>

</section>

<?php } ?>

</main>

<?php include("../includes/footer.php"); ?>

<script src="../assets/js/script.js"></script>

</body>
</html>