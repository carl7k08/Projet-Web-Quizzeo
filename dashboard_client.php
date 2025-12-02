<?php
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'ecole' && $_SESSION['role'] != 'entreprise')) {
    header('Location: login.php');
    exit();
}

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
            <p>Vous n'avez pas encore crée de quiz.</p>
        <?php else: ?>
            <p>Liste des quiz...</p>
        <?php endif; ?>
    </div>
</body>
</html>