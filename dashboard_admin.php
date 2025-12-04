<?php
// dashboard_admin.php
require 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

// ACTION : Activer/Désactiver Utilisateur
if (isset($_GET['action_user']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $new_status = ($_GET['action_user'] == 'activate') ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ? AND role != 'admin'");
    $stmt->execute([$new_status, $id]);
    header("Location: dashboard_admin.php"); exit();
}

// ACTION : Activer/Désactiver Quiz (NOUVEAU)
if (isset($_GET['action_quiz']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $new_status = ($_GET['action_quiz'] == 'activate') ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE quizzes SET is_active = ? WHERE id = ?");
    $stmt->execute([$new_status, $id]);
    header("Location: dashboard_admin.php"); exit();
}

// Récupération des données
$users = $pdo->query("SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC")->fetchAll();
$quizzes = $pdo->query("SELECT q.*, u.nom as auteur FROM quizzes q JOIN users u ON q.user_id = u.id ORDER BY q.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Quizzeo</title>
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
        <h2>Gestion des Utilisateurs</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nom']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= ucfirst($u['role']) ?></td>
                    <td>
                        <?php if($u['is_active']): ?>
                            <span class="text-success text-bold">Actif</span>
                        <?php else: ?>
                            <span class="text-danger text-bold">Bloqué</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($u['is_active']): ?>
                            <a href="?action_user=deactivate&id=<?= $u['id'] ?>" class="btn btn-small btn-danger">Bloquer</a>
                        <?php else: ?>
                            <a href="?action_user=activate&id=<?= $u['id'] ?>" class="btn btn-small btn-success">Activer</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr style="margin: 40px 0;">

        <h2>Gestion des Quiz</h2>
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>État</th>
                    <th>Visibilité</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($quizzes as $q): ?>
                <tr>
                    <td><?= htmlspecialchars($q['titre']) ?></td>
                    <td><?= htmlspecialchars($q['auteur']) ?></td>
                    <td>
                        <?php 
                            if($q['status'] == 'en_cours') echo '<span class="text-warning">Brouillon</span>';
                            if($q['status'] == 'lance') echo '<span class="text-success text-bold">Lancé</span>';
                            if($q['status'] == 'termine') echo '<span class="text-danger">Terminé</span>';
                        ?>
                    </td>
                    <td>
                        <?php if($q['is_active']): ?>
                            <span class="text-success">Visible</span>
                        <?php else: ?>
                            <span class="text-danger text-bold">Masqué (Admin)</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($q['is_active']): ?>
                            <a href="?action_quiz=deactivate&id=<?= $q['id'] ?>" class="btn btn-small btn-danger">Masquer</a>
                        <?php else: ?>
                            <a href="?action_quiz=activate&id=<?= $q['id'] ?>" class="btn btn-small btn-success">Rendre visible</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>