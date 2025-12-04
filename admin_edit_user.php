<?php

require 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

if (!isset($_GET['id'])) { header("Location: dashboard_admin.php"); exit(); }
$id = intval($_GET['id']);
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $role = $_POST['role'];
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET nom=?, email=?, role=?, password=? WHERE id=?");
        $stmt->execute([$nom, $email, $role, $password, $id]);
        $message = "Utilisateur mis à jour avec nouveau mot de passe !";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nom=?, email=?, role=? WHERE id=?");
        $stmt->execute([$nom, $email, $role, $id]);
        $message = "Utilisateur mis à jour !";
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) die("Utilisateur introuvable.");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Utilisateur - Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Q<span>UIZZE</span><span class="last">O</span> Admin</div>
        <a href="dashboard_admin.php" class="btn">Retour</a>
    </header>

    <div class="container">
        <h2>Modifier : <?= htmlspecialchars($user['nom']) ?></h2>
        
        <?php if($message): ?>
            <p class="text-success text-bold"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST" class="form-box">
            <label>Nom complet :</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

            <label>Email :</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label>Rôle :</label>
            <select name="role">
                <option value="utilisateur" <?= $user['role']=='utilisateur'?'selected':'' ?>>Utilisateur</option>
                <option value="ecole" <?= $user['role']=='ecole'?'selected':'' ?>>École</option>
                <option value="entreprise" <?= $user['role']=='entreprise'?'selected':'' ?>>Entreprise</option>
            </select>

            <hr style="margin: 20px 0;">
            
            <label style="color:var(--color-danger);">Réinitialiser le mot de passe (Laisser vide pour ne pas changer) :</label>
            <input type="text" name="password" placeholder="Nouveau mot de passe...">

            <button type="submit" class="btn btn-full mt-20">Enregistrer les modifications</button>
        </form>
    </div>
</body>
</html>