<?php

require 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    $new_status = ($action == 'activate') ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ? AND role != 'admin'");
    $stmt->execute([$new_status, $id]);
    header("Location: dashboard_admin.php"); exit();
}

$stmt = $pdo->query("SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC");
$users = $stmt->fetchAll();
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
                            <span class="text-danger text-bold">Désactivé</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($u['is_active']): ?>
                            <a href="?action=deactivate&id=<?= $u['id'] ?>" class="btn btn-small btn-danger">Désactiver</a>
                        <?php else: ?>
                            <a href="?action=activate&id=<?= $u['id'] ?>" class="btn btn-small btn-success">Activer</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>