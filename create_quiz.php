<?php
require 'config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'ecole' && $_SESSION['role'] != 'entreprise')) {
    header("Location: login.php"); exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    $user_id = $_SESSION['user_id'];
    
    $access_code = rand(100000, 999999);

    $stmt = $pdo->prepare("INSERT INTO quizzes (user_id, titre, description, status, access_code) VALUES (?, ?, ?, 'en_cours', ?)");
    $stmt->execute([$user_id, $titre, $description, $access_code]);
    
    $quiz_id = $pdo->lastInsertId();
    header("Location: edit_quiz.php?id=" . $quiz_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau Quiz</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span> Création</div>
        <a href="dashboard_client.php" class="btn">Retour</a>
    </header>

    <div class="container">
        <h2>Commencer un nouveau Quiz</h2>
        <form method="POST">
            <label>Titre du Quiz :</label>
            <input type="text" name="titre" placeholder="Ex: Culture Générale" required>

            <label>Description :</label>
            <textarea name="description" rows="4" placeholder="Description courte..."></textarea>

            <button type="submit" class="btn btn-full mt-20">Étape suivante : Ajouter des questions</button>
        </form>
    </div>
</body>
</html>