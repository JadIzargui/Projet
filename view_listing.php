<?php
session_start();
require("db.php");

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$ad_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT ads.*, users.username, users.email FROM ads JOIN users ON ads.user_id = users.id WHERE ads.id = ?");
$stmt->execute([$ad_id]);
$ad = $stmt->fetch();

if (!$ad) {
    echo "Annonce non trouvée.";
    exit();
}

$image_paths = [];
if (!empty($ad['image_paths'])) {
    $image_paths = explode(',', $ad['image_paths']);
} else if (!empty($ad['image_path'])) {
    $image_paths[] = $ad['image_path'];
} else {
    $image_paths[] = 'https://via.placeholder.com/600x400';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($ad['title']); ?> - LeBonCoin</title>
    <?php include 'header.php'; ?>
</head>
<?php include("navbar.php"); ?>
<body class="bg-lbc-light">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-md-7">
                <div class="border rounded shadow-sm overflow-hidden bg-white p-3">
                    <div id="adCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($image_paths as $index => $img): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo htmlspecialchars(trim($img)); ?>" class="d-block w-100 rounded" alt="Image annonce" style="object-fit: cover; height: 400px;" />
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($image_paths) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#adCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Précédent</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#adCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Suivant</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="border rounded shadow-sm bg-white p-4">
                    <h2 class="mb-3"><?php echo htmlspecialchars($ad['title']); ?></h2>
                    <h4 class="text-lbc-orange fw-bold mb-3"><?php echo htmlspecialchars($ad['price']); ?> €</h4>
                    <p class="mb-3"><strong>Description :</strong><br><?php echo nl2br(htmlspecialchars($ad['description'])); ?></p>
                    <p class="mb-3"><i class="fas fa-map-marker-alt text-muted me-2"></i><strong>Localisation :</strong> <?php echo htmlspecialchars($ad['location']); ?></p>
                    <p class="text-muted small">Publié le <?php echo date('d/m/Y', strtotime($ad['created_at'])); ?></p>
                </div>

                <div class="border rounded shadow-sm bg-white p-4 mt-4">
                    <h5 class="mb-3"><i class="fas fa-user-circle me-2"></i>Informations du vendeur</h5>
                    <p class="mb-2"><i class="fas fa-user me-2 text-secondary"></i><strong>Nom d'utilisateur :</strong> <?php echo htmlspecialchars($ad['username']); ?></p>
                    <p class="mb-3"><i class="fas fa-envelope me-2 text-secondary"></i><strong>Email :</strong> <?php echo htmlspecialchars($ad['email']); ?></p>

                    <?php if (isset($_SESSION['id']) && $_SESSION['id'] != $ad['user_id']): ?>
                        <a href="start_chat.php?ad_id=<?php echo $ad['id']; ?>" class="btn btn-primary w-100 mt-2">
                            <i class="fas fa-comments me-2"></i>Contacter le vendeur
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
