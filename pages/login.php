<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - SmartCampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-body">

<div class="login-page">

    <div class="login-left">
        <h1>SmartCampus</h1>
        <p>Votre plateforme de gestion académique intelligente.</p>

        <ul>
            <li>Accès étudiant, enseignant et administrateur</li>
            <li>Notes, présences et emploi du temps</li>
            <li>Messagerie interne et suivi pédagogique</li>
        </ul>
    </div>

    <div class="login-card">

        <h2>Connexion</h2>
        <p class="login-subtitle">Connectez-vous à votre espace personnel</p>

        <?php if (isset($_GET["erreur"])) { ?>
            <p class="error-message">
                Il n'existe pas de compte avec ces identifiants.
            </p>
        <?php } ?>

        <form method="post" action="../actions/login_action.php">

            <label>Email</label>
            <input type="email" name="email" placeholder="exemple@smartcampus.fr" required>

            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" placeholder="Votre mot de passe" required>

            <button type="submit" name="login_btn">
                Se connecter
            </button>

        </form>

        <p class="login-link">
            Pas encore de compte ?
            <a href="inscription.php">Créer un compte</a>
        </p>

        <p class="login-back">
            <a href="../index.php">← Retour à l'accueil</a>
        </p>

    </div>

</div>

</body>
</html>