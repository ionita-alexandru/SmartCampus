<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - SmartCampus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header>
    <h1>SmartCampus</h1>
</header>

<main>
    <h2>Connexion</h2>

    <form method="post" action="../actions/login_action.php">
        <label>Email :</label><br>
        <input type="email" name="email" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit" name="login_btn">Se connecter</button>
    </form>
</main>

<footer>
    <p>SmartCampus - Projet Web Dynamique 2026</p>
</footer>

</body>
</html>
