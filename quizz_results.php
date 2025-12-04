<?php
require 'config/db.php';

if (!isset($_GET['id'])) header("Location: dashboard_client.php");

$quiz_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND user_id = ?");
$stmt->execute([$quiz_id, $user_id]);
$quiz = $stmt->fetch();

if (!$quiz) die("Accès interdit.");

$stmt_att = $pdo->prepare("
    SELECT a.*, u.nom, u.email 
    FROM attempts a 
    JOIN users u ON a.user_id = u.id 
    WHERE a.quiz_id = ? 
    ORDER BY a.finished_at DESC
");
$stmt_att->execute([$quiz_id]);
$attempts = $stmt_att->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span> Résultats</div>
        <a href="dashboard_client.php" class="btn">Retour</a>
    </header>

    <div class="container">
        <h2>Résultats : <?= htmlspecialchars($quiz['titre']) ?></h2>
        <p>Total des participations : <strong><?= count($attempts) ?></strong></p>
        <hr>

        <?php if($_SESSION['role'] == 'ecole'): ?>
            <h3>Notes des étudiants</h3>
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--color-primary); color:white;">
                        <th style="padding:10px;">Élève</th>
                        <th style="padding:10px;">Email</th>
                        <th style="padding:10px;">Note</th>
                        <th style="padding:10px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($attempts as $a): ?>
                        <tr style="border-bottom:1px solid #ddd;">
                            <td style="padding:10px;"><?= htmlspecialchars($a['nom']) ?></td>
                            <td style="padding:10px;"><?= htmlspecialchars($a['email']) ?></td>
                            <td style="padding:10px; font-weight:bold; color:var(--color-secondary);">
                                <?= $a['score'] ?> / <?= $a['total_points'] ?>
                            </td>
                            <td style="padding:10px;"><?= $a['finished_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif($_SESSION['role'] == 'entreprise'): ?>
            <h3>Statistiques des réponses</h3>
            <?php
            $stmt_q = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ?");
            $stmt_q->execute([$quiz_id]);
            $questions = $stmt_q->fetchAll();

            foreach($questions as $q):
                $stmt_stats = $pdo->prepare("
                    SELECT answer_text, COUNT(*) as count 
                    FROM user_answers 
                    WHERE question_id = ? 
                    GROUP BY answer_text
                ");
                $stmt_stats->execute([$q['id']]);
                $stats = $stmt_stats->fetchAll();
            ?>
                <div style="margin-bottom:20px; border:1px solid #eee; padding:15px; border-radius:5px;">
                    <h4><?= htmlspecialchars($q['question_text']) ?></h4>
                    <ul>
                        <?php foreach($stats as $s): 
                            $percent = count($attempts) > 0 ? round(($s['count'] / count($attempts)) * 100) : 0;
                        ?>
                            <li>
                                "<?= htmlspecialchars($s['answer_text']) ?>" : 
                                <strong><?= $percent ?>%</strong> (<?= $s['count'] ?> votes)
                                <div style="background:#eee; width:100%; height:10px; margin-top:5px;">
                                    <div style="background:var(--color-accent); height:100%; width:<?= $percent ?>%;"></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</body>
</html>