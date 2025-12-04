<?php

require 'config/database.php';

if (!isset($_SESSION['captcha_result'])) {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['captcha_result'] = $num1 + $num2;
    $_SESSION['captcha_text'] = "$num1 + $num2";
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (intval($_POST['captcha']) !== $_SESSION['captcha_result']) {
        $message = "Mauvais résultat au calcul (Captcha) !";
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
                unset($_SESSION['captcha_result']);
                header("Location: login.php");
                exit();
            } else {
                $message = "Erreur lors de l'inscription.";
            }
        }
    }
    
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['captcha_result'] = $num1 + $num2;
    $_SESSION['captcha_text'] = "$num1 + $num2";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span></div>
        <a href="login.php" class="link-primary">Se connecter</a>
    </header>

    <div class="container">
        <h2>Créer un compte</h2>
        <?php if($message): ?>
            <p class="text-danger"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Nom complet :</label>
            <input type="text" name="nom" required>

            <label>Email :</label>
            <input type="email" name="email" required>

            <label>Mot de passe :</label>
            <input type="password" name="password" required>

            <label>Vous êtes :</label>
            <select name="role">
                <option value="utilisateur">Utilisateur (Je veux répondre à des quiz)</option>
                <option value="ecole">École (Je veux noter des étudiants)</option>
                <option value="entreprise">Entreprise (Je veux faire des sondages)</option>
            </select>

            <label>Vérification : Combien font <?= $_SESSION['captcha_text'] ?> ?</label>
            <input type="number" name="captcha" required>

            <button type="submit" class="btn">M'inscrire</button>
        </form>
    </div>
</body>
</html>