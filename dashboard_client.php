<?php
require 'config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'ecole' && $_SESSION['role'] != 'entreprise')) {
    header("Location: login.php"); exit();
}

$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$quizzes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span> <?= ucfirst($_SESSION['role']) ?></div>
        <a href="logout.php" class="btn btn-dark">Déconnexion</a>
    </header>

    <div class="container">
        <div class="flex-header">
            <h2>Mes Quiz</h2>
            <a href="create_quiz.php" class="btn-align"> + Créer un nouveau Quiz</a>
        </div>
        <hr>

        <?php if(empty($quizzes)): ?>
            <p class="box-gray">Vous n'avez pas encore créé de quiz.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>CODE PIN (Kahoot)</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($quizzes as $q): ?>
                    <tr class="tr-hover">
                        <td>
                            <strong><?= htmlspecialchars($q['titre']) ?></strong><br>
                            <small class="text-small"><?= count($pdo->query("SELECT id FROM questions WHERE quiz_id=".$q['id'])->fetchAll()) ?> questions</small>
                        </td>
                        <td>
                            <?php if($q['status'] == 'lance'): ?>
                                <span style="font-size: 1.5em; font-weight: bold; color: var(--color-primary); letter-spacing: 2px;">
                                    <?= $q['access_code'] ?>
                                </span>
                             <?php else: ?>
                                <span class="text-small">-</span>
                             <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                                if($q['status'] == 'en_cours') echo '<span class="text-warning">Brouillon</span>';
                                if($q['status'] == 'lance') echo '<span class="text-success text-bold">LANCÉ</span>';
                                if($q['status'] == 'termine') echo '<span class="text-danger">Terminé</span>';
                            ?>
                        </td>
                        <td>
                            <?php if($q['status'] == 'en_cours'): ?>
                                <a href="edit_quiz.php?id=<?= $q['id'] ?>" class="btn btn-small">Modifier</a>
                            <?php else: ?>
                                <a href="quiz_results.php?id=<?= $q['id'] ?>" class="btn btn-small btn-accent">Voir Résultats</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>