<?php

require 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'")->execute([$id]);
    header("Location: dashboard_admin.php?msg=deleted"); exit();
}

if (isset($_GET['delete_quiz'])) {
    $id = intval($_GET['delete_quiz']);
    $pdo->prepare("DELETE FROM quizzes WHERE id = ?")->execute([$id]);
    header("Location: dashboard_admin.php?msg=deleted"); exit();
}

if (isset($_GET['action_user']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $status = ($_GET['action_user'] == 'activate') ? 1 : 0;
    $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?")->execute([$status, $id]);
    header("Location: dashboard_admin.php"); exit();
}

if (isset($_GET['action_quiz']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $status = ($_GET['action_quiz'] == 'activate') ? 1 : 0;
    $pdo->prepare("UPDATE quizzes SET is_active = ? WHERE id = ?")->execute([$status, $id]);
    header("Location: dashboard_admin.php"); exit();
}

$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn(),
    'quizzes' => $pdo->query("SELECT COUNT(*) FROM quizzes")->fetchColumn(),
    'attempts' => $pdo->query("SELECT COUNT(*) FROM attempts")->fetchColumn()
];

$users = $pdo->query("SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC")->fetchAll();
$quizzes = $pdo->query("SELECT q.*, u.nom as auteur FROM quizzes q JOIN users u ON q.user_id = u.id ORDER BY q.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Super Admin - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span> Admin</div>
        <div>
            <span>Bonjour, <?= htmlspecialchars($_SESSION['nom']) ?></span>
            <a href="logout.php" class="btn btn-dark" style="margin-left: 10px;">Déconnexion</a>
        </div>
    </header>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['users'] ?></div>
                <div>Utilisateurs Inscrits</div>
            </div>
            <div class="stat-card" style="background: var(--color-secondary);">
                <div class="stat-number"><?= $stats['quizzes'] ?></div>
                <div>Quiz Créés</div>
            </div>
            <div class="stat-card" style="background: var(--color-accent); color: #333;">
                <div class="stat-number"><?= $stats['attempts'] ?></div>
                <div>Parties Jouées</div>
            </div>
        </div>

        <hr>

        <h2>Gestion des Utilisateurs</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>État</th>
                    <th>Actions (Super-Admin)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nom']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= ucfirst($u['role']) ?></td>
                    <td>
                        <?= $u['is_active'] ? '<span class="text-success text-bold">Actif</span>' : '<span class="text-danger">Bloqué</span>' ?>
                    </td>
                    <td>
                        <div class="action-group">
                            <?php if($u['is_active']): ?>
                                <a href="?action_user=deactivate&id=<?= $u['id'] ?>" class="btn btn-small btn-dark" title="Bloquer">🔒</a>
                            <?php else: ?>
                                <a href="?action_user=activate&id=<?= $u['id'] ?>" class="btn btn-small btn-success" title="Activer">🔓</a>
                            <?php endif; ?>

                            <a href="admin_edit_user.php?id=<?= $u['id'] ?>" class="btn btn-small" title="Modifier">✏️</a>

                            <a href="?delete_user=<?= $u['id'] ?>" class="btn btn-small btn-danger" onclick="return confirm('⚠️ SUPPRIMER DÉFINITIVEMENT cet utilisateur et TOUS ses quiz ?')" title="Supprimer">🗑️</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <br><br>

        <h2>Gestion des Quiz (Modération)</h2>
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Code PIN</th>
                    <th>État</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($quizzes as $q): ?>
                <tr>
                    <td><?= htmlspecialchars($q['titre']) ?></td>
                    <td><?= htmlspecialchars($q['auteur']) ?></td>
                    <td><strong><?= $q['access_code'] ?></strong></td>
                    <td>
                        <?= $q['is_active'] ? '<span class="text-success">Visible</span>' : '<span class="text-danger text-bold">Masqué</span>' ?>
                    </td>
                    <td>
                        <div class="action-group">
                            <?php if($q['is_active']): ?>
                                <a href="?action_quiz=deactivate&id=<?= $q['id'] ?>" class="btn btn-small btn-dark">Masquer</a>
                            <?php else: ?>
                                <a href="?action_quiz=activate&id=<?= $q['id'] ?>" class="btn btn-small btn-success">Afficher</a>
                            <?php endif; ?>
                            
                            <a href="?delete_quiz=<?= $q['id'] ?>" class="btn btn-small btn-danger" onclick="return confirm('Supprimer ce quiz définitivement ?')">🗑️</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>