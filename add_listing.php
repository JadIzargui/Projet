<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require("db.php");

// Redirect if not logged in
if(!isset($_SESSION['id'])){
    header("location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $price = trim($_POST["price"]);
    $location = trim($_POST["location"]);
    $category = trim($_POST["category"]);
    
    // Basic validation
    if(empty($title) || empty($description) || empty($price) || empty($location) || empty($category)) {
        $error = "Tous les champs sont obligatoires";
    } else {
        // Handle file upload
        $image_path = null;
        if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
            $target_dir = "uploads/";
            if(!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $file_ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $allowed_ext = array("jpg", "jpeg", "png", "gif");
            
            if(in_array($file_ext, $allowed_ext)) {
                $new_filename = uniqid() . '.' . $file_ext;
                $target_file = $target_dir . $new_filename;
                
                if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = $target_file;
                }
            }
        }
        
        // Insert ad into database
        $stmt = $pdo->prepare("INSERT INTO ads (user_id, title, description, price, location, category, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if($stmt->execute([$_SESSION['id'], $title, $description, $price, $location, $category, $image_path])) {
            // Redirect to home page after posting
            header("location: index.php");
            exit();
        } else {
            $error = "Erreur lors de la création de l'annonce";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déposer une annonce - LeBonCoin</title>
    <?php include("link.php"); ?>
</head>
<body class="bg-lbc-light">
    <?php include("navbar.php"); ?>
    
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="text-lbc-orange mb-4">Déposer une annonce</h1>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre de l'annonce *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Prix (€) *</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="location" class="form-label">Localisation *</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="category" class="form-label">Catégorie *</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Choisir une catégorie</option>
                                <option value="Immobilier">Immobilier</option>
                                <option value="Véhicules">Véhicules</option>
                                <option value="Emploi">Emploi</option>
                                <option value="Multimédia">Multimédia</option>
                                <option value="Maison">Maison</option>
                                <option value="Loisirs">Loisirs</option>
                                <option value="Matériel Professionnel">Matériel Professionnel</option>
                                <option value="Autres">Autres</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Image (optionnel)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-orange">Publier l'annonce</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include("footer.php"); ?>
</body>
</html>
