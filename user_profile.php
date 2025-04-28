<?php
session_start();
require("db.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("Utilisateur non trouvé.");
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($username) || empty($name) || empty($email)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } elseif ($password !== $password_confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Check if email or username already exists for other users
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (email = ? OR username = ?) AND id != ?");
        $stmt->execute([$email, $username, $user_id]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $error = "L'email ou le nom d'utilisateur est déjà utilisé.";
        } else {
            // Update user data
            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET username = ?, name = ?, email = ?, password = ? WHERE id = ?");
                $updated = $stmt->execute([$username, $name, $email, $password_hash, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = ?, name = ?, email = ? WHERE id = ?");
                $updated = $stmt->execute([$username, $name, $email, $user_id]);
            }
            if ($updated) {
                $success = "Profil mis à jour avec succès.";
                // Update session username
                $_SESSION['username'] = $username;
            } else {
                $error = "Erreur lors de la mise à jour du profil.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mon profil - LeBonCoin</title>
    <?php include 'header.php'; ?>
</head>
<body class="bg-lbc-light">
    <?php include 'navbar.php'; ?>

    <div class="container py-4">
        <h1 class="text-lbc-orange mb-4">Mon profil</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" class="lbc-form">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur *</label>
                <input type="text" class="form-control" id="username" name="username" required value="<?= htmlspecialchars($user['username']) ?>" />
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Nom complet *</label>
                <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($user['name']) ?>" />
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Adresse email *</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>" />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" class="form-control" id="password" name="password" />
            </div>
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Confirmer le nouveau mot de passe</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" />
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-orange">Mettre à jour</button>
            </div>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
