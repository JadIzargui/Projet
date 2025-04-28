<?php
session_start();
require("db.php");


$search = $_GET['search'] ?? '';
if ($search) {
    $search_param = '%' . $search . '%';
    if (isset($_SESSION['id'])) {
        // Exclude ads posted by logged-in user, join with users to get username
        $stmt = $pdo->prepare("SELECT ads.*, users.username FROM ads JOIN users ON ads.user_id = users.id WHERE (ads.title LIKE ? OR ads.description LIKE ?) AND ads.user_id != ? ORDER BY ads.created_at DESC");
        $stmt->execute([$search_param, $search_param, $_SESSION['id']]);
    } else {
        $stmt = $pdo->prepare("SELECT ads.*, users.username FROM ads JOIN users ON ads.user_id = users.id WHERE ads.title LIKE ? OR ads.description LIKE ? ORDER BY ads.created_at DESC");
        $stmt->execute([$search_param, $search_param]);
    }
    $ads = $stmt->fetchAll();
} else {
    if (isset($_SESSION['id'])) {
        // Exclude ads posted by logged-in user, join with users to get username
        $stmt = $pdo->prepare("SELECT ads.*, users.username FROM ads JOIN users ON ads.user_id = users.id WHERE ads.user_id != ? ORDER BY ads.created_at DESC");
        $stmt->execute([$_SESSION['id']]);
        $ads = $stmt->fetchAll();
    } else {
        $ads = $pdo->query("SELECT ads.*, users.username FROM ads JOIN users ON ads.user_id = users.id ORDER BY ads.created_at DESC")->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeBonCoin - Annonces</title>
    <?php include 'header.php'; ?>
</head>

<?php include("navbar.php"); ?>
<body class="bg-lbc-light">
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-lbc-orange">Annonces</h1>
<?php if (isset($_SESSION['id'])): ?>
    <a href="add_listing.php" class="btn btn-orange">
        <i class="fas fa-plus me-2"></i>Déposer une annonce
    </a>
<?php else: ?>
    <!-- Removed Connexion button here to avoid duplication with navbar -->
<?php endif; ?>
        </div>

<?php if (!isset($_SESSION['id'])): ?>
    <!-- Removed login form to separate login page -->
<?php endif; ?>

        <div class="annonces-container">
            <?php
            foreach ($ads as $ad) {
                $contact_link = isset($_SESSION['id']) ? 'start_chat.php?ad_id=' . $ad['id'] : 'login.php';
                echo '<div>
                        <div class="card h-100 d-flex flex-column">
                            <a href="view_listing.php?id=' . $ad['id'] . '" class="text-decoration-none text-dark">
                                <img src="' . ($ad['image_path'] ? $ad['image_path'] : 'https://via.placeholder.com/300x200') . '" alt="Listing image" class="card-img-top">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">' . htmlspecialchars($ad['title']) . '</h5>
                                    <p class="card-description flex-grow-1">' . htmlspecialchars($ad['description']) . '</p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="card-price">' . htmlspecialchars($ad['price']) . '€</span>
                                        <small class="text-muted">' . htmlspecialchars($ad['location']) . '</small>
                                    </div>
                                    <div class="mb-2 text-muted fst-italic small">
                                        Publié par : ' . htmlspecialchars($ad['username']) . '
                                    </div>
                                </div>
                            </a>
                            <div class="d-grid gap-2 px-3 pb-3 mt-auto">
                                <a href="' . $contact_link . '" class="btn btn-primary btn-sm">Contacter le vendeur</a>
                                ' . (isset($_SESSION['id']) && $_SESSION['id'] == $ad['user_id'] ? '
                                <div class="d-flex gap-2 mt-2">
                                    <a href="edit_listing.php?id=' . $ad['id'] . '" class="btn btn-outline-primary btn-sm flex-grow-1">Modifier</a>
                                    <form method="POST" action="delete_listing.php" class="flex-grow-1" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer cette annonce?\');">
                                        <input type="hidden" name="ad_id" value="' . $ad['id'] . '">
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">Supprimer</button>
                                    </form>
                                </div>' : '') . '
                            </div>
                            <div class="card-footer bg-white text-center">
                                <small class="text-muted">Publié le ' . date('d/m/Y', strtotime($ad['created_at'])) . '</small>
                            </div>
                        </div>
                    </div>';
            }
            ?>
        </div>
    </div>
<?php include("footer.php"); ?>
</body>
</html>
