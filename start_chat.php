<?php
session_start();
require("db.php");

// Debug log function
function debug_log($message) {
    error_log("[start_chat.php] " . $message);
}

// Redirect if not logged in
if(!isset($_SESSION['id'])) {
    debug_log("User not logged in, redirecting to index.php");
    header("location: index.php");
    exit();
}

// Get ad ID from URL
$ad_id = $_GET['ad_id'] ?? null;
debug_log("Received ad_id: " . var_export($ad_id, true));

// If no ad ID, redirect to home
if(!$ad_id) {
    debug_log("No ad_id provided, redirecting to home.php");
    header("location: home.php");
    exit();
}

// Get ad info
$ad = $pdo->prepare("SELECT * FROM ads WHERE id = ?");
$ad->execute([$ad_id]);
$ad = $ad->fetch();
debug_log("Ad fetched: " . var_export($ad, true));

if(!$ad) {
    debug_log("Ad not found, redirecting to home.php");
    header("location: home.php");
    exit();
}

// Check if user is trying to contact themselves
if($ad['user_id'] == $_SESSION['id']) {
    debug_log("User tried to contact themselves, redirecting to home.php");
    header("location: home.php");
    exit();
}

// Check if conversation already exists
$stmt = $pdo->prepare("
    SELECT id FROM conversations 
    WHERE ad_id = ? 
    AND ((user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?))
");
$stmt->execute([
    $ad_id, 
    $_SESSION['id'], 
    $ad['user_id'],
    $ad['user_id'],
    $_SESSION['id']
]);
$conversation = $stmt->fetch();
debug_log("Existing conversation: " . var_export($conversation, true));

// If conversation exists, redirect to it
if($conversation) {
    debug_log("Redirecting to existing conversation chat.php?id=" . $conversation['id']);
    header("location: chat.php?id=" . $conversation['id']);
    exit();
}

// Create new conversation
$stmt = $pdo->prepare("
    INSERT INTO conversations (ad_id, user1_id, user2_id) 
    VALUES (?, ?, ?)
");
$stmt->execute([$ad_id, $_SESSION['id'], $ad['user_id']]);
$conversation_id = $pdo->lastInsertId();
debug_log("Created new conversation with id: " . $conversation_id);

// Insert notification for the seller (ad owner)
$notification_message = "Un acheteur a contacté votre annonce: " . htmlspecialchars($ad['title']);
$notif_stmt = $pdo->prepare("
    INSERT INTO notifications (user_id, conversation_id, message) 
    VALUES (?, ?, ?)
");
$notif_stmt->execute([$ad['user_id'], $conversation_id, $notification_message]);

// Redirect to the new conversation
header("location: chat.php?id=" . $conversation_id);
exit();
