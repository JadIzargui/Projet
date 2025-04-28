<?php
session_start();
require("db.php");

if(!isset($_SESSION['id']) || !isset($_POST['ad_id'])) {
header("location: index.php");
exit();
}

$ad_id = $_POST['ad_id'];

try {
    // Vérifier que l'utilisateur est bien le propriétaire de l'annonce
    $stmt = $pdo->prepare("SELECT user_id FROM ads WHERE id = ?");
    $stmt->execute([$ad_id]);
    $ad = $stmt->fetch();

    if($ad && $ad['user_id'] == $_SESSION['id']) {
        // Supprimer l'annonce
        $delete_stmt = $pdo->prepare("DELETE FROM ads WHERE id = ?");
        if($delete_stmt->execute([$ad_id])) {
            $_SESSION['success'] = "Annonce supprimée avec succès";
            
            // Supprimer aussi les conversations liées à cette annonce
            // $pdo->prepare("DELETE FROM conversations WHERE ad_id = ?")->execute([$ad_id]);
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression de l'annonce";
        }
    } else {
        $_SESSION['error'] = "Vous n'avez pas les droits pour supprimer cette annonce";
    }
} catch(PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données: " . $e->getMessage();
}

header("location: index.php");
exit();
?>
