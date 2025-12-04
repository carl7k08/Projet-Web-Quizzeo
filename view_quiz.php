<?php

require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$quiz_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();

if (!$quiz || $quiz['status'] != 'lance' || $quiz['is_active'] == 0) {
    die("Ce quiz n'est pas disponible (il a peut-être été désactivé par l'administrateur ou n'est pas encore lancé).");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $score = 0;
    $total_points = 0;
    
    $stmt = $pdo->prepare("INSERT INTO attempts (user_id, quiz_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $quiz_id]);
    $attempt_id = $pdo->lastInsertId();

    $stmt_q = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
    $stmt_q->execute([$quiz_id]);
    $questions_check = $stmt_q->fetchAll();

    foreach ($questions_check as $q) {
        $qid = $q['id'];
        $user_response = isset($_POST['q_' . $qid]) ? $_POST['q_' . $qid] : '';
        $save_text = '';

        if ($q['type'] == 'qcm') {
            $total_points += $q['points'];
            
            if ($user_response) {
                $stmt_check = $pdo->prepare("SELECT is_correct, answer_text FROM answers WHERE id = ?");
                $stmt_check->execute([$user_response]);
                $ans = $stmt_check->fetch();
                
                if ($ans) {
                    if ($ans['is_correct']) {
                        $score += $q['points'];
                    }
                    $save_text = $ans['answer_text'];
                }
            }
        } else {
            $save_text = $user_response;
        }

        $stmt_ins = $pdo->prepare("INSERT INTO user_answers (attempt_id, question_id, answer_text) VALUES (?, ?, ?)");
        $stmt_ins->execute([$attempt_id, $qid, $save_text]);
    }

    $stmt_up = $pdo->prepare("UPDATE attempts SET score = ?, total_points = ? WHERE id = ?");
    $stmt_up->execute([$score, $total_points, $attempt_id]);

    header("Location: dashboard_user.php?success=1");
    exit();
}

$stmt_q = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt_q->execute([$quiz_id]);
$questions = $stmt_q->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Répondre - <?= htmlspecialchars($quiz['titre']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span></div>
        <a href="dashboard_user.php" class="btn">Quitter</a>
    </header>

    <div class="container">
        <h2><?= htmlspecialchars($quiz['titre']) ?></h2>
        <p><?= htmlspecialchars($quiz['description']) ?></p>
        <hr>

        <form method="POST">
            <?php foreach($questions as $q): ?>
                <div class="quiz-question-block">
                    <p><strong><?= htmlspecialchars($q['question_text']) ?></strong></p>

                    <?php if($q['type'] == 'qcm'): 
                        $stmt_a = $pdo->prepare("SELECT * FROM answers WHERE question_id = ?");
                        $stmt_a->execute([$q['id']]);
                        $answers = $stmt_a->fetchAll();
                    ?>
                        <?php foreach($answers as $a): ?>
                            <div>
                                <label>
                                    <input type="radio" name="q_<?= $q['id'] ?>" value="<?= $a['id'] ?>" required>
                                    <?= htmlspecialchars($a['answer_text']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <textarea name="q_<?= $q['id'] ?>" rows="3" required placeholder="Votre réponse..."></textarea>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-full">Envoyer mes réponses</button>
        </form>
    </div>
</body>
</html>