<?php
session_start();
require("db.php");

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['id'])) {
    header("location: index.php");
    exit();
}

// Récupérer les notifications de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['id']]);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - LeBonCoin</title>
    <?php include("header.php"); ?>
</head>
<body>
    <?php include("navbar.php"); ?>
    
    <div class="container py-4">
        <h2>Notifications</h2>
        <div class="list-group">
            <?php if (count($notifications) > 0): ?>
                <?php foreach ($notifications as $notification): ?>
                    <a href="chat.php?id=<?= $notification['conversation_id'] ?>" class="list-group-item list-group-item-action">
                        <?= htmlspecialchars($notification['message']) ?> <small class="text-muted"><?= date('H:i', strtotime($notification['created_at'])) ?></small>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune notification.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include("footer.php"); ?>
</body>
</html>
