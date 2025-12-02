<?php
require 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}


if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'activate') $new_status = 1;
    if ($action == 'deactivate') $new_status = 0;

    $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ? and role != 'admin'");
    $stmt->execute([$new_status, $id]);

    header("Location: dashboard_admin.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/style_dashboard_admin.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class='last'>O</span> Admin</div>
        <div>
            <span>Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?></span>
            <a href='logout.php' class='btn' style="background-color: #333; margin-left: 10px;">Se déconnecter</a>
        </div>
    </header>

    <div class="container" style='max-width: 1000px;'>
        <h2>Gestion des utilisateurs</h2>

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
                            <span class='status-on'>Actif</span>
                        <?php else: ?>
                            <span class='status-off'>Désactivé</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($u['is_active']): ?>
                            <a href='?action=deactivate&id=<?= $u['id'] ?>' class='btn btn-small btn-danger'>Désactiver</a>
                        <?php else: ?>
                            <a href='?action=activate&id=<?= $u['id'] ?>' class='btn btn-small' style='background-color:green;'>Activer</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>