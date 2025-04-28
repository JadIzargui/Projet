<?php
session_start();
require("db.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Fetch ads posted by the logged-in user
$stmt = $pdo->prepare("SELECT * FROM ads WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$ads = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mes annonces - LeBonCoin</title>
    <?php include 'header.php'; ?>
</head>
<body class="bg-lbc-light">
    <?php include 'navbar.php'; ?>

    <div class="container py-4">
        <h1 class="text-lbc-orange mb-4">Mes annonces</h1>

        <?php if (count($ads) === 0): ?>
            <p>Vous n'avez pas encore publié d'annonces.</p>
            <a href="add_listing.php" class="btn btn-orange">Déposer une annonce</a>
        <?php else: ?>
            <div class="row">
                <?php foreach ($ads as $ad): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card-annonce">
                            <img src="<?= $ad['image_path'] ? htmlspecialchars($ad['image_path']) : 'https://via.placeholder.com/300x200' ?>" alt="Listing image" class="card-image" />
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($ad['title']) ?></h5>
                                <p class="card-description"><?= htmlspecialchars($ad['description']) ?></p>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="card-price"><?= htmlspecialchars($ad['price']) ?>€</span>
                                    <small class="text-muted"><?= htmlspecialchars($ad['location']) ?></small>
                                </div>
                                <div class="d-grid">
                                    <div class="d-flex gap-2">
                                        <a href="edit_listing.php?id=<?= $ad['id'] ?>" class="btn btn-sm btn-outline-primary flex-grow-1">Modifier</a>
                                        <form method="POST" action="delete_listing.php" class="flex-grow-1" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce?');">
                                            <input type="hidden" name="ad_id" value="<?= $ad['id'] ?>" />
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <small class="text-muted">Publié le <?= date('d/m/Y', strtotime($ad['created_at'])) ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
