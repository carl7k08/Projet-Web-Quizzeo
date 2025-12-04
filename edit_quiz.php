<?php
// edit_quiz.php
require 'config/database.php';
// ... (Toute la logique PHP reste identique, je ne la remets pas pour raccourcir, mais garde-la !)
if (!isset($_GET['id'])) { header("Location: dashboard_client.php"); exit(); }
$quiz_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND user_id = ?");
$stmt->execute([$quiz_id, $user_id]);
$quiz = $stmt->fetch();
if (!$quiz) die("Erreur.");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_question'])) {
    // ... (Ton code d'insertion ici) ...
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
    header("Location: edit_quiz.php?id=" . $quiz_id); exit();
}
if (isset($_GET['publish']) && $_GET['publish'] == 'true') {
    $stmt = $pdo->prepare("UPDATE quizzes SET status = 'lance' WHERE id = ?");
    $stmt->execute([$quiz_id]);
    header("Location: dashboard_client.php"); exit();
}
$stmt_q = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
$stmt_q->execute([$quiz_id]);
$questions = $stmt_q->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Éditer le Quiz</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span> Éditeur</div>
        <a href="dashboard_client.php" class="btn">Retour au Dashboard</a>
    </header>

    <div class="container">
        <h2><?= htmlspecialchars($quiz['titre']) ?> <span class="text-small">(<?= $quiz['status'] ?>)</span></h2>
        <p><?= htmlspecialchars($quiz['description']) ?></p>
        <hr>

        <h3>Questions du quiz (<?= count($questions) ?>)</h3>
        <?php foreach($questions as $q): ?>
            <div class="question-box">
                <strong><?= htmlspecialchars($q['question_text']) ?></strong><br>
                <span class="badge"><?= strtoupper($q['type']) ?></span>
                <span class="badge badge-points"><?= $q['points'] ?> pts</span>
                
                <?php if($q['type'] == 'qcm'): 
                    $stmt_a = $pdo->prepare("SELECT * FROM answers WHERE question_id = ?");
                    $stmt_a->execute([$q['id']]);
                    $answers = $stmt_a->fetchAll();
                ?>
                    <ul class="text-small">
                    <?php foreach($answers as $a): ?>
                        <li class="<?= $a['is_correct'] ? 'text-success text-bold' : '' ?>">
                            <?= htmlspecialchars($a['answer_text']) ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <hr>

        <h3 class="link-primary">Ajouter une question</h3>
        <form method="POST" class="form-box">
            <input type="hidden" name="add_question" value="1">
            <label>Intitulé :</label>
            <input type="text" name="question_text" required placeholder="Question...">

            <div class="flex-gap">
                <div class="flex-1">
                    <label>Type :</label>
                    <select name="type" id="typeSelect" onchange="toggleOptions()">
                        <option value="qcm">QCM</option>
                        <option value="libre">Libre</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label>Points :</label>
                    <input type="number" name="points" value="1" min="1">
                </div>
            </div>

            <div id="qcmOptions" class="qcm-options">
                <p><strong>Propositions :</strong> (Cochez la bonne réponse)</p>
                <?php for($i=1; $i<=4; $i++): ?>
                <div class="radio-group">
                    <input type="radio" name="correct_answer" value="<?= $i ?>" <?= $i==1 ? 'checked' : '' ?> class="radio-input">
                    <input type="text" name="answers[]" placeholder="Réponse <?= $i ?>" class="text-input-simple">
                </div>
                <?php endfor; ?>
            </div>

            <button type="submit" class="btn btn-full mt-20">Enregistrer la question</button>
        </form>

        <div class="text-center mt-20">
            <a href="edit_quiz.php?id=<?= $quiz_id ?>&publish=true" class="btn btn-success" onclick="return confirm('Confirmer ?')">
                🚀 LANCER LE QUIZ
            </a>
        </div>
    </div>
    <script>
        function toggleOptions() {
            var type = document.getElementById('typeSelect').value;
            document.getElementById('qcmOptions').style.display = (type === 'qcm') ? 'block' : 'none';
        }
        toggleOptions();
    </script>
</body>
</html>