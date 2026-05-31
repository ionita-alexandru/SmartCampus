<?php

include("../includes/db.php");

if (isset($_POST["register_btn"])) {

    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $mot_de_passe = $_POST["mot_de_passe"];
    $role = $_POST["role"];
    $id_promotion = isset($_POST["id_promotion"]) ? $_POST["id_promotion"] : 2;

    $check = "SELECT * FROM utilisateurs WHERE email = '$email'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        header("Location: ../pages/inscription.php?erreur=1");
        exit();
    }

    mysqli_query($conn, "
        INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role)
        VALUES ('$nom', '$prenom', '$email', '$mot_de_passe', '$role')
    ");

    $id_utilisateur = mysqli_insert_id($conn);

    if ($role == "etudiant") {

        $resGroupe = mysqli_query($conn, "
            SELECT g.id_groupe, COUNT(e.id_etudiant) AS nb
            FROM groupes g
            LEFT JOIN etudiants e ON e.id_groupe = g.id_groupe
            WHERE g.id_promotion = $id_promotion
            GROUP BY g.id_groupe
            ORDER BY nb ASC
            LIMIT 1
        ");

        $groupe = mysqli_fetch_assoc($resGroupe);

        if (!$groupe) {
            header("Location: ../pages/inscription.php?erreur_groupe=1");
            exit();
        }

        $id_groupe = $groupe["id_groupe"];
        $numero = "NEW" . $id_utilisateur;

        mysqli_query($conn, "
            INSERT INTO etudiants (id_utilisateur, numero_etudiant, id_promotion, id_groupe)
            VALUES ($id_utilisateur, '$numero', $id_promotion, $id_groupe)
        ");

        $id_etudiant = mysqli_insert_id($conn);

        mysqli_query($conn, "
            INSERT INTO inscriptions (id_etudiant, id_cours)
            SELECT $id_etudiant, id_cours
            FROM cours
            WHERE id_promotion = $id_promotion
            AND type_cours IN ('CM', 'TD')
        ");

        mysqli_query($conn, "
            INSERT INTO presences (id_etudiant, id_seance, statut)
            SELECT $id_etudiant, s.id_seance, 'absent'
            FROM seances s
            JOIN groupes g ON g.id_groupe = $id_groupe
            WHERE s.id_groupe = $id_groupe
            OR s.id_amphi = g.id_amphi
        ");
    }

    if ($role == "enseignant") {
        mysqli_query($conn, "
            INSERT INTO enseignants (id_utilisateur, grade)
            VALUES ($id_utilisateur, 'Enseignant')
        ");
    }

    header("Location: ../pages/inscription.php?success=1");
    exit();
}

header("Location: ../pages/inscription.php");
exit();

?>