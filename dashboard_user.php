<?php

require 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'utilisateur') {
    header("Location: login.php"); exit();
}

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['game_pin'])) {
    $pin = htmlspecialchars($_POST['game_pin']);
    $stmt = $pdo->prepare("SELECT id FROM quizzes WHERE access_code = ? AND status = 'lance' AND is_active = 1");
    $stmt->execute([$pin]);
    $quiz = $stmt->fetch();

    if ($quiz) {
        header("Location: view_quiz.php?id=" . $quiz['id']);
        exit();
    } else {
        $error_msg = "Code introuvable ou session fermée.";
    }
}

$stmt = $pdo->prepare("
    SELECT a.*, q.titre, u.nom as auteur, u.role as auteur_role 
    FROM attempts a 
    JOIN quizzes q ON a.quiz_id = q.id 
    JOIN users u ON q.user_id = u.id
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
    <title>Espace Participant - Quizzeo</title>
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
        <div class="kahoot-box">
            <h1 style="margin-top:0;">REJOINDRE UNE SESSION</h1>
            <p>Entre le CODE PIN fourni par l'école ou l'entreprise</p>
            
            <?php if($error_msg): ?>
                <div style="background: white; color: var(--color-danger); padding: 10px; border-radius: 5px; display:inline-block; margin-bottom: 15px; font-weight:bold;">
                    ⚠️ <?= $error_msg ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="game_pin" class="pin-input" placeholder="000000" maxlength="6" autocomplete="off" required>
                <button type="submit" class="btn-play">GO !</button>
            </form>
        </div>

        <hr>

        <h3>Mon Historique</h3>
        <?php if(empty($history)): ?>
            <div class="box-gray">Aucune participation pour le moment.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Organisateur</th>
                        <th>Date</th>
                        <th>Résultat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($history as $h): ?>
                    <tr class="tr-hover">
                        <td><?= htmlspecialchars($h['titre']) ?></td>
                        <td>
                            <?= htmlspecialchars($h['auteur']) ?>
                            <span class="text-small">(<?= ucfirst($h['auteur_role']) ?>)</span>
                        </td>
                        <td><?= $h['finished_at'] ?></td>
                        <td>
                            <?php if($h['auteur_role'] == 'ecole'): ?>
                                <span class="score-display"><?= $h['score'] ?> / <?= $h['total_points'] ?></span>
                            <?php else: ?>
                                <span class="text-success text-bold">Participé</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <?php if(isset($_GET['success'])): ?>
            <p class="text-success text-bold mt-20 text-center">Réponses envoyées avec succès !</p>
        <?php endif; ?>
    </div>
</body>
</html>