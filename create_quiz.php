<?php 
require 'config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'ecole' && $_SESSION['role'] != 'entreprise')) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = htmlspecialchars($_POST['titre']);
    $description = htmlspecialchars($_POST['description']);
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO quizzes (user_id, titre, description, status) VALUES (?, ?, ?, 'en_cours')");
    $stmt->execute([$user_id, $titre, $description]);
    $quiz_id = $pdo->lastInsertId();   

    header("Location: edit_quiz.php?id=" . $quiz_id);
    exit(); 

}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Quiz - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
     <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span> Création</div>
        <a href="dashboard_client.php" class="btn">Retour</a>
    </header>
    
    <div class="container">
        <h2>Commencer un Nouveau Quiz</h2>
        <form method="POST" class="quiz-form">
            <div class="form-group">
                <label for="titre">Titre du Quiz:</label>
                <input type="text" id="titre" name="titre" placeholder="Ex: Examen de Mathématiques" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" placeholder="Ex: Chapitre 1 à 3"></textarea>
            </div>

            <button type="submit" class="btn btn-submit">Étape suivante : Ajouter des questions</button>
        </form>
    </div>
</body>
</html>