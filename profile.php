<?php

require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit();
}

$message = "";
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $password, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ? WHERE id = ?");
        $stmt->execute([$nom, $email, $user_id]);
    }
    
    $_SESSION['nom'] = $nom; 
    $message = "Informations mises à jour avec succès !";
}

$stmt = $pdo->prepare("SELECT nom, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span> Profil</div>
        <?php 
            $back_link = "dashboard_user.php";
            if($_SESSION['role'] == 'admin') $back_link = "dashboard_admin.php";
            if($_SESSION['role'] == 'ecole' || $_SESSION['role'] == 'entreprise') $back_link = "dashboard_client.php";
        ?>
        <a href="<?= $back_link ?>" class="btn">Retour au Dashboard</a>
    </header>

    <div class="container">
        <h2>Modifier mes informations</h2>
        <?php if($message): ?>
            <p class="text-success text-bold"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Nom complet :</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

            <label>Email :</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label>Nouveau mot de passe (laisser vide pour ne pas changer) :</label>
            <input type="password" name="password">

            <button type="submit" class="btn btn-danger mt-20">Enregistrer les modifications</button>
        </form>
    </div>
</body>
</html>