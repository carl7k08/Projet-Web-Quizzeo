<?php
require 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'utilisateur') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Espace - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class='last'>O</span> Utilisateur</div>
        <div>
            <a href="profile.php" class='btn' style='background-color: var(--color-primary);'>Mon Profil</a>
            <a href='logout.php' class='btn' style="background-color: #333;">Déconnexion</a>
        </div>
    </header>

    <div class="container">
        <h2>Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?> !</h2>
        <p>Voici les questionnaires auxquels vous avez déjà répondu :</p>

        <div style='background: #eee; padding: 20px; text-align: center; border-radius: 5px;'>
            <em>Aucun historique pour le moment.</em>
        </div>

        <br>
        <h3>Comment participer ?</h3>
        <p>Pour répondre à un quiz, demandez le lien unique à votre école ou votre entreprise.</p>
    </div>
</body>
</html>