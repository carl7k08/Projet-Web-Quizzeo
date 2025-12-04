<?php

require 'config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $remote_ip = $_SERVER['REMOTE_ADDR'];
    $api_url = "https://www.google.com/recaptcha/api/siteverify?secret=" . RECAPTCHA_SECRET_KEY . "&response=$recaptcha_response&remoteip=$remote_ip";
    $response = file_get_contents($api_url);
    $response_keys = json_decode($response, true);

    if(!$response_keys["success"]) {
        $message = "Veuillez cocher la case 'Je ne suis pas un robot'.";
    } else {
        $nom = htmlspecialchars($_POST['nom']);
        $email = htmlspecialchars($_POST['email']);
        $role = $_POST['role'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $message = "Cet email est déjà utilisé.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nom, $email, $password, $role])) {
                header("Location: login.php");
                exit();
            } else {
                $message = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span></div>
        <a href="login.php" class="btn">Se connecter</a>
    </header>

    <div class="container">
        <h2>Créer un compte</h2>
        <?php if($message): ?>
            <p class="text-danger text-bold"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Nom complet :</label>
            <input type="text" name="nom" required placeholder="Votre nom">

            <label>Email :</label>
            <input type="email" name="email" required placeholder="votre@email.com">

            <label>Mot de passe :</label>
            <input type="password" name="password" required>

            <label>Vous êtes :</label>
            <select name="role">
                <option value="utilisateur">Utilisateur (Je veux répondre à des quiz)</option>
                <option value="ecole">École (Je veux noter des étudiants)</option>
                <option value="entreprise">Entreprise (Je veux faire des sondages)</option>
            </select>

            <div style="margin: 20px 0;">
                <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
            </div>

            <button type="submit" class="btn btn-full">M'inscrire</button>
        </form>
    </div>
</body>
</html>