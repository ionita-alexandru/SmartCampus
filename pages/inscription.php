<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - SmartCampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="register-body">

<div class="register-page">

    <div class="register-left">
        <h1>SmartCampus</h1>
        <p>Créez votre compte pour accéder à votre espace académique personnalisé.</p>

        <ul>
            <li>Compte étudiant, enseignant ou administrateur</li>
            <li>Accès sécurisé selon votre rôle</li>
            <li>Données enregistrées dans la base de données</li>
        </ul>
    </div>

    <div class="register-card">

        <h2>Créer un compte</h2>
        <p class="register-subtitle">Remplissez les informations ci-dessous</p>

        <?php if (isset($_GET["erreur"])) { ?>
            <p class="error-message">Cet email est déjà utilisé.</p>
        <?php } ?>

        <?php if (isset($_GET["success"])) { ?>
            <p class="success-message">Compte créé avec succès. Vous pouvez vous connecter.</p>
        <?php } ?>

        <form method="post" action="../actions/register_action.php">

            <label>Nom</label>
            <input type="text" name="nom" placeholder="Votre nom" required>

            <label>Prénom</label>
            <input type="text" name="prenom" placeholder="Votre prénom" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="exemple@smartcampus.fr" required>

            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" placeholder="Votre mot de passe" required>

            <label>Rôle</label>
            <select name="role" required>
                <option value="etudiant">Étudiant</option>
                <option value="enseignant">Enseignant</option>
                <option value="admin">Administrateur</option>
            </select>

            <button type="submit" name="register_btn">Créer le compte</button>

        </form>

        <p class="register-link">
            Déjà un compte ?
            <a href="login.php">Se connecter</a>
        </p>

        <p class="register-back">
            <a href="../index.php">← Retour à l'accueil</a>
        </p>

    </div>

</div>

</body>
</html>