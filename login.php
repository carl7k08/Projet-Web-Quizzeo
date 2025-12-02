<?php
require 'config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if (user['is_active'] == 0) {
            $message = 'Votre compte a été désactivé par un de nos administrateurs.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nom'] = $user['nom'];

            if ($user['role'] == 'admin') {
                header('Location: dashboard_admin.php');
            } elseif ($user['role'] == 'ecole' || $user['role'] == 'entreprise') {
                header('Location: dashboard_client.php');
            } else {
                header('Location: dashboard_user.php');
            }
            exit();
        }
    } else {
        $message = 'Email ou mot de passe incorrect.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class='last'>O</span></div>
        <a href="register.php" style="color:var(--color-primary)">Créer un compte</a>
    </header>

    <div class='container'>
        <h2>Connexion</h2>
        <?php if ($message): ?>
            <p style="color:red;"><?= $message; ?></p>
        <?php endif; ?>

        <form method='POST'>
            <label>Email:</label>
            <input type='email' name='email' required>

            <label>Mot de passe:</label>
            <input type='password' name='password' required>

            <button type='submit'>Se connecter</button>
        </form>

        <p style='margin-top:20px; font-size:0.9em;'>
            Compte admin par défaut : admin@quizzeo.com / admin123
        </p>
    </div>
</body>
</html>