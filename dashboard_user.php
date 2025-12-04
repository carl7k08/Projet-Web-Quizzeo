<?php

require 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'utilisateur') {
    header("Location: login.php");
    exit();
}

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
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Espace - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span></div>
        <div>
            <a href="profile.php" class="btn btn-accent">Mon Profil</a>
            <a href="logout.php" class="btn btn-dark">Déconnexion</a>
        </div>
    </header>

    <div class="container">
        <h2>Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?> !</h2>
        <p>Voici les questionnaires auxquels vous avez déjà répondu :</p>
        
        <?php if(empty($history)): ?>
            <div class="box-gray">
                <em>Vous n'avez répondu à aucun quiz pour le moment.</em>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Quiz</th>
                        <th>Date</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($history as $h): ?>
                    <tr class="tr-hover">
                        <td><?= htmlspecialchars($h['titre']) ?></td>
                        <td><?= $h['finished_at'] ?></td>
                        <td class="score-display">
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
        
        <?php if(isset($_GET['success'])): ?>
            <p class="text-success text-bold mt-20">✅ Réponses envoyées avec succès !</p>
        <?php endif; ?>
    </div>
</body>
</html>