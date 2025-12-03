<?php

require 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard_client.php");
    exit();
}
$quiz_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND user_id = ?");
$stmt->execute([$quiz_id, $user_id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    die("Ce quiz n'existe pas ou ne vous appartient pas.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_question'])) {
    $question_text = htmlspecialchars($_POST['question_text']);
    $type = $_POST['type'];
    $points = intval($_POST['points']);

    $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, type, points) VALUES (?, ?, ?, ?)");
    $stmt->execute([$quiz_id, $question_text, $type, $points]);
    $question_id = $pdo->lastInsertId();

    if ($type == 'qcm' && isset($_POST['answers'])) {
        $correct_index = intval($_POST['correct_answer']);
        
        foreach ($_POST['answers'] as $index => $ans_text) {
            if (trim($ans_text) != "") {
                $is_correct = ($index + 1 == $correct_index) ? 1 : 0;
                $stmt_ans = $pdo->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");
                $stmt_ans->execute([$question_id, htmlspecialchars($ans_text), $is_correct]);
            }
        }
    }
    header("Location: edit_quiz.php?id=" . $quiz_id);
    exit();
}

if (isset($_GET['publish']) && $_GET['publish'] == 'true') {
    $stmt = $pdo->prepare("UPDATE quizzes SET status = 'lance' WHERE id = ?");
    $stmt->execute([$quiz_id]);
    header("Location: dashboard_client.php");
    exit();
}

$stmt_q = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
$stmt_q->execute([$quiz_id]);
$questions = $stmt_q->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Éditer le Quiz - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .question-box { background: #fff; border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; border-left: 5px solid var(--color-primary); }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 12px; color: white; background: #666; }
        .qcm-options { display: none; background: #f9f9f9; padding: 15px; border: 1px dashed #ccc; margin-top: 10px; }
    </style>
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span> Éditeur</div>
        <a href="dashboard_client.php" class="btn">Retour au Dashboard</a>
    </header>

    <div class="container">
        <h2><?= htmlspecialchars($quiz['titre']) ?> <span style="font-size:0.5em; color:gray;">(<?= $quiz['status'] ?>)</span></h2>
        <p><?= htmlspecialchars($quiz['description']) ?></p>

        <hr>

        <h3>Questions du quiz (<?= count($questions) ?>)</h3>
        <?php foreach($questions as $q): ?>
            <div class="question-box">
                <strong><?= htmlspecialchars($q['question_text']) ?></strong>
                <br>
                <span class="badge"><?= strtoupper($q['type']) ?></span>
                <span class="badge" style="background:var(--color-accent); color:#333;"><?= $q['points'] ?> pts</span>
                
                <?php if($q['type'] == 'qcm'):
                    $stmt_a = $pdo->prepare("SELECT * FROM answers WHERE question_id = ?");
                    $stmt_a->execute([$q['id']]);
                    $answers = $stmt_a->fetchAll();
                ?>
                    <ul style="margin-top:5px; font-size:0.9em; color:#555;">
                    <?php foreach($answers as $a): ?>
                        <li <?= $a['is_correct'] ? 'style="color:green; font-weight:bold;"' : '' ?>>
                            <?= htmlspecialchars($a['answer_text']) ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <hr>

        <h3 style="color:var(--color-primary);">Ajouter une question</h3>
        <form method="POST" style="background:#f4f4f9; padding:20px; border-radius:8px;">
            <input type="hidden" name="add_question" value="1">
            
            <label>Intitulé de la question :</label>
            <input type="text" name="question_text" required placeholder="Ex: Quelle est la capitale de la France ?">

            <div style="display:flex; gap:20px;">
                <div style="flex:1;">
                    <label>Type de question :</label>
                    <select name="type" id="typeSelect" onchange="toggleOptions()">
                        <option value="qcm">QCM (Choix Multiples)</option>
                        <option value="libre">Réponse Libre (Texte)</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <label>Points :</label>
                    <input type="number" name="points" value="1" min="1">
                </div>
            </div>

            <div id="qcmOptions" class="qcm-options" style="display:block;">
                <p><strong>Propositions de réponses :</strong> (Cochez la bonne réponse)</p>
                
                <?php for($i=1; $i<=4; $i++): ?>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:5px;">
                    <input type="radio" name="correct_answer" value="<?= $i ?>" <?= $i==1 ? 'checked' : '' ?> style="width:auto; margin:0;">
                    <input type="text" name="answers[]" placeholder="Réponse <?= $i ?>" style="margin:0;">
                </div>
                <?php endfor; ?>
            </div>

            <button type="submit" class="btn" style="width:100%; margin-top:15px;">Enregistrer la question</button>
        </form>

        <br><br>
        <div style="text-align:center;">
            <a href="edit_quiz.php?id=<?= $quiz_id ?>&publish=true" class="btn" style="background-color:green; padding:15px 30px; font-size:1.2em;" onclick="return confirm('Attention, une fois lancé, vous ne pourrez plus modifier le quiz. Continuer ?')">
                🚀 LANCER LE QUIZ (PUBLIER)
            </a>
        </div>
    </div>

    <script>
        function toggleOptions() {
            var type = document.getElementById('typeSelect').value;
            var optionsDiv = document.getElementById('qcmOptions');
            if (type === 'qcm') {
                optionsDiv.style.display = 'block';
            } else {
                optionsDiv.style.display = 'none';
            }
        }
        toggleOptions();
    </script>
</body>
</html>