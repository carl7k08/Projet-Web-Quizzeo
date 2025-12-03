<?php
require 'config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'ecole' && $_SESSION['role'] != 'entreprise')) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->prepare('SELECT * FROM quizzes WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$quizzes = $stmt->fetchAll();

$quizzes = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class='logo'>Q<span>UIZZE</span><span class='last'>O</span> <?= ucfirst($_SESSION['role']) ?></div>
        <div>
            <a href='logout.php' class='btn' style="background-color: #333;">Se déconnecter</a>
        </div>
    </header>

    <div class="container">
        <div style='display:flex; justify-content:space-between; align-items:center;'>
            <h2>Mes Quiz</h2>
            <a href='create_quiz.php' class='btn'> + Créer un nouveau quiz</a>
        </div>

        <hr>

        <?php if(empty($quizzes)): ?>
            <p>Vous n'avez pas encore créé de quiz.</p>
        <?php else: ?>
            <table style="width:100%; border-collapse:collapse; margin-top:20px;">
                <thead>
                    <tr style="background:#ddd; text-align:left;">
                        <th style="padding:10px;">Titre</th>
                        <th style="padding:10px;">Statut</th>
                        <th style="padding:10px;">Lien (à partager)</th>
                        <th style="padding:10px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($quizzes as $q): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:10px;">
                            <strong><?= htmlspecialchars($q['titre']) ?></strong><br>
                            <small><?= count($pdo->query("SELECT id FROM questions WHERE quiz_id=".$q['id'])->fetchAll()) ?> questions</small>
                        </td>
                        <td style="padding:10px;">
                            <?php 
                                if($q['status'] == 'en_cours') echo '<span style="color:orange;">Brouillon</span>';
                                if($q['status'] == 'lance') echo '<span style="color:green; font-weight:bold;">Lancé</span>';
                                if($q['status'] == 'termine') echo '<span style="color:red;">Terminé</span>';
                            ?>
                        </td>
                        <td style="padding:10px;">
                            <?php if($q['status'] == 'lance'): ?>
                                <a href="view_quiz.php?id=<?= $q['id'] ?>" target="_blank">Lien du quiz</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td style="padding:10px;">
                            <?php if($q['status'] == 'en_cours'): ?>
                                <a href="edit_quiz.php?id=<?= $q['id'] ?>" class="btn" style="padding:5px 10px; font-size:12px;">Modifier</a>
                            <?php else: ?>
                                <a href="quiz_results.php?id=<?= $q['id'] ?>" class="btn" style="background-color:var(--color-accent); color:#333; padding:5px 10px; font-size:12px;">Voir Résultats</a>
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