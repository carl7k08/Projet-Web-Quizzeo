<?php
require 'config/database.php';

if (!isset($_GET['id'])) { header("Location: dashboard_client.php"); exit(); }
$quiz_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND user_id = ?");
$stmt->execute([$quiz_id, $user_id]);
$quiz = $stmt->fetch();
if (!$quiz) die("Erreur.");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_question'])) {
    $question_text = htmlspecialchars($_POST['question_text']);
    $type = $_POST['type'];
    
    $points = ($user_role == 'ecole') ? intval($_POST['points']) : 0;

    $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, type, points) VALUES (?, ?, ?, ?)");
    $stmt->execute([$quiz_id, $question_text, $type, $points]);
    $question_id = $pdo->lastInsertId();

    if ($type == 'qcm' && isset($_POST['answers'])) {
        $correct_index = ($user_role == 'ecole') ? intval($_POST['correct_answer']) : -1;
        
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

$questions = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
$questions->execute([$quiz_id]);
$questions = $questions->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Éditeur - <?= ucfirst($user_role) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span> Éditeur</div>
        <a href="dashboard_client.php" class="btn">Retour</a>
    </header>

    <div class="container">
        <h2><?= htmlspecialchars($quiz['titre']) ?> <span class="text-small">(<?= $quiz['status'] ?>)</span></h2>
        
        <h3>Questions (<?= count($questions) ?>)</h3>
        <?php foreach($questions as $q): ?>
            <div class="question-box">
                <strong><?= htmlspecialchars($q['question_text']) ?></strong><br>
                <span class="badge"><?= strtoupper($q['type']) ?></span>
                
                <?php if($user_role == 'ecole'): ?>
                    <span class="badge badge-points"><?= $q['points'] ?> pts</span>
                <?php endif; ?>
                
                <?php if($q['type'] == 'qcm'): 
                    $stmt_a = $pdo->prepare("SELECT * FROM answers WHERE question_id = ?");
                    $stmt_a->execute([$q['id']]);
                    foreach($stmt_a->fetchAll() as $a): 
                        $style = ($a['is_correct'] && $user_role == 'ecole') ? 'color:#388E3C; font-weight:bold;' : '';
                ?>
                        <div class="text-small" style="margin-left:10px; <?= $style ?>">
                            - <?= htmlspecialchars($a['answer_text']) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <hr>

        <h3 class="link-primary">Ajouter une question</h3>
        <form method="POST" class="form-box">
            <input type="hidden" name="add_question" value="1">
            <label>Question :</label>
            <input type="text" name="question_text" required placeholder="Intitulé de la question...">

            <div class="flex-gap">
                <div class="flex-1">
                    <label>Type :</label>
                    <select name="type" id="typeSelect" onchange="toggleOptions()">
                        <option value="qcm">QCM (Choix Multiples)</option>
                        <option value="libre">Réponse Libre (Texte)</option>
                    </select>
                </div>
                
                <?php if($user_role == 'ecole'): ?>
                <div class="flex-1">
                    <label>Points :</label>
                    <input type="number" name="points" value="1" min="1">
                </div>
                <?php endif; ?>
            </div>

            <div id="qcmOptions" class="qcm-options">
                <p><strong>Réponses possibles :</strong>
                <?php if($user_role == 'ecole'): ?>
                     (Cochez la bonne réponse)
                <?php else: ?>
                     (Sondage : pas de bonne réponse)
                <?php endif; ?>
                </p>
                
                <?php for($i=1; $i<=4; $i++): ?>
                <div class="radio-group">
                    <?php if($user_role == 'ecole'): ?>
                        <input type="radio" name="correct_answer" value="<?= $i ?>" <?= $i==1 ? 'checked' : '' ?> class="radio-input">
                    <?php else: ?>
                        <span style="margin-right:10px;">🔹</span>
                    <?php endif; ?>
                    <input type="text" name="answers[]" placeholder="Choix <?= $i ?>" class="text-input-simple">
                </div>
                <?php endfor; ?>
            </div>

            <button type="submit" class="btn btn-full mt-20">Ajouter</button>
        </form>

        <div class="text-center mt-20">
            <a href="edit_quiz.php?id=<?= $quiz_id ?>&publish=true" class="btn btn-success" onclick="return confirm('Publier ce <?= $user_role == 'ecole' ? 'examen' : 'sondage' ?> ?')">
                LANCER LE <?= strtoupper($user_role == 'ecole' ? 'QUIZ' : 'SONDAGE') ?>
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