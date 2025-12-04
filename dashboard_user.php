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

        <?php
            // Récupérer l'historique
            $stmt = $pdo->prepare("
                SELECT a.*, q.titre 
                FROM attempts a 
                JOIN quizzes q ON a.quiz_id = q.id 
                WHERE a.user_id = ? 
                ORDER BY a.finished_at DESC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $history = $stmt->fetchAll();
        ?>

        <?php if(empty($history)): ?>
            <div style="background:#eee; padding:20px; text-align:center; border-radius:5px;">
                <em>Vous n'avez répondu à aucun quiz pour le moment.</em>
            </div>
        <?php else: ?>
            <table style="width:100%; margin-top:20px; border-collapse:collapse;">
                <thead>
                    <tr style="background:#ddd;">
                        <th style="padding:10px; text-align:left;">Quiz</th>
                        <th style="padding:10px; text-align:left;">Date</th>
                        <th style="padding:10px; text-align:left;">Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($history as $h): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:10px;"><?= htmlspecialchars($h['titre']) ?></td>
                        <td style="padding:10px;"><?= $h['finished_at'] ?></td>
                        <td style="padding:10px; font-weight:bold; color:var(--color-secondary);">
                            <?= $h['score'] ?> / <?= $h['total_points'] ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <br>
        <h3>Comment participer ?</h3>
        <p>Pour répondre à un quiz, demandez le lien unique à votre école ou votre entreprise.</p>
    </div>
</body>
</html>