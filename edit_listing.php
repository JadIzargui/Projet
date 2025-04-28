<?php
session_start();
require_once 'db.php';

// Vérifier si l'ID de l'annonce est fourni
if(!isset($_GET['id']) || !isset($_SESSION['id'])) {
    header("Location: home.php");
    exit();
}

$listing_id = $_GET['id'];

// Récupérer les détails de l'annonce
try {
    $stmt = $pdo->prepare("SELECT * FROM ads WHERE id = ?");
    $stmt->execute([$listing_id]);
    $listing = $stmt->fetch();

    // Vérifier que l'utilisateur est propriétaire de l'annonce
    if(!$listing || $_SESSION['id'] != $listing['user_id']) {
        header("Location: home.php");
        exit();
    }
} catch(PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données";
    header("Location: home.php");
    exit();
}

// Traitement du formulaire de modification
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération et validation des données
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $price = floatval($_POST['price']);
    $remove_image = isset($_POST['remove_image']);
    $image_path = $listing['image_path'];
    
    try {
        // Gestion de l'image
        if($remove_image && $image_path) {
            // Supprimer l'image du serveur
            if(file_exists($image_path)) {
                unlink($image_path);
            }
            $image_path = null;
        }
        
        // Traitement de la nouvelle image
        if(isset($_FILES['new_image']) && $_FILES['new_image']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Supprimer l'ancienne image si elle existe
            if($image_path && file_exists($image_path)) {
                unlink($image_path);
            }
            
            // Générer un nom de fichier unique
            $extension = pathinfo($_FILES['new_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid().'.'.$extension;
            $destination = $upload_dir.$filename;
            
            if(move_uploaded_file($_FILES['new_image']['tmp_name'], $destination)) {
                $image_path = $destination;
            }
        }
        
        // Mise à jour dans la base de données
        $update_stmt = $pdo->prepare("UPDATE ads SET title = ?, description = ?, price = ?, image_path = ? WHERE id = ?");
        if($update_stmt->execute([$title, $description, $price, $image_path, $listing_id])) {
            $_SESSION['success'] = "Annonce modifiée avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la modification";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données: " . $e->getMessage();
    }
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Modifier l'annonce - Leboncoin</title>
    <?php include 'header.php'; ?>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Modifier votre annonce</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?= htmlspecialchars($listing['title']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="5" required><?= htmlspecialchars($listing['description']) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Prix (€)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           step="0.01" value="<?= htmlspecialchars($listing['price']) ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Photo actuelle</label>
                                <?php if($listing['image_path']): ?>
                                    <div class="text-center mb-3">
                                        <img src="<?= htmlspecialchars($listing['image_path']) ?>" 
                                             class="img-fluid rounded border" style="max-height: 200px;">
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" 
                                               name="remove_image" id="remove_image">
                                        <label class="form-check-label" for="remove_image">
                                            <i class="fas fa-trash-alt me-1"></i>Supprimer cette photo
                                        </label>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted"><i class="fas fa-image me-1"></i>Aucune photo</p>
                                <?php endif; ?>
                            </div>

                            <div class="mb-4">
                                <label for="new_image" class="form-label">Nouvelle photo</label>
                                <input type="file" class="form-control" id="new_image" 
                                       name="new_image" accept="image/*">
                                <div class="form-text">Formats acceptés: JPG, PNG (max 2MB)</div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="home.php" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
