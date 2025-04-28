<?php
session_start();
require("db.php");

// Redirect if not logged in
if(!isset($_SESSION['id'])) {
    header("location: index.php");
    exit();
}

// Get conversation ID from URL
$conversation_id = $_GET['id'] ?? null;

// If no conversation ID, redirect to home
if(!$conversation_id) {
    header("location: home.php");
    exit();
}

// Check if user is part of this conversation
$stmt = $pdo->prepare("SELECT * FROM conversations WHERE id = ? AND (user1_id = ? OR user2_id = ?)");
$stmt->execute([$conversation_id, $_SESSION['id'], $_SESSION['id']]);
$conversation = $stmt->fetch();

if(!$conversation) {
    header("location: home.php");
    exit();
}

// Get other user's info
$other_user_id = ($conversation['user1_id'] == $_SESSION['id']) ? $conversation['user2_id'] : $conversation['user1_id'];
$other_user = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$other_user->execute([$other_user_id]);
$other_user = $other_user->fetch();

// Get ad info
$ad = $pdo->prepare("SELECT title FROM ads WHERE id = ?");
$ad->execute([$conversation['ad_id']]);
$ad = $ad->fetch();

// Handle new message submission
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["message"])) {
    $message = trim($_POST["message"]);
    
    if(!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$conversation_id, $_SESSION['id'], $message]);

        // Insert notification for the other user in the conversation
        $other_user_id = ($conversation['user1_id'] == $_SESSION['id']) ? $conversation['user2_id'] : $conversation['user1_id'];
        $notification_message = "Nouveau message de " . htmlspecialchars($_SESSION['username']);
        $notif_stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, conversation_id, message) 
            VALUES (?, ?, ?)
        ");
        $notif_stmt->execute([$other_user_id, $conversation_id, $notification_message]);
    }
}

// Get all messages for this conversation
$messages = $pdo->prepare("
    SELECT m.*, u.username 
    FROM messages m 
    JOIN users u ON m.sender_id = u.id 
    WHERE m.conversation_id = ? 
    ORDER BY m.created_at ASC
");
$messages->execute([$conversation_id]);
$messages = $messages->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - LeBonCoin</title>
    <?php include("link.php"); ?>
    <style>
        .message-container {
            height: 60vh;
            overflow-y: auto;
        }
        .message {
            max-width: 70%;
            margin-bottom: 10px;
        }
        .sent {
            margin-left: auto;
            background-color: #e3f2fd;
        }
        .received {
            margin-right: auto;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body class="bg-lbc-light">
    <?php include("navbar.php"); ?>
    
    <div class="container py-4">
        <div class="card">
            <div class="card-header bg-lbc-orange text-white">
                <h5 class="mb-0">Discussion à propos de: <?php echo htmlspecialchars($ad['title']); ?></h5>
                <small>Avec <?php echo htmlspecialchars($other_user['username']); ?></small>
            </div>
            
            <div class="card-body message-container">
                <?php foreach($messages as $message): ?>
                    <div class="message p-3 rounded <?php echo $message['sender_id'] == $_SESSION['id'] ? 'sent' : 'received'; ?>">
                        <div class="d-flex justify-content-between">
                            <strong><?php echo htmlspecialchars($message['username']); ?></strong>
                            <small class="text-muted"><?php echo date('H:i', strtotime($message['created_at'])); ?></small>
                        </div>
                        <p class="mb-0"><?php echo htmlspecialchars($message['message']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="card-footer">
                <form method="POST">
                    <div class="input-group">
                        <input type="text" class="form-control" name="message" placeholder="Écrivez votre message..." required>
                        <button class="btn btn-orange" type="submit">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include("footer.php"); ?>
    
    <script>
        // Auto-scroll to bottom of messages
        window.onload = function() {
            const container = document.querySelector('.message-container');
            container.scrollTop = container.scrollHeight;
        };
    </script>
</body>
</html>
