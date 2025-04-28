<?php


    require("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $sexe = $_POST["sexe"];
    $password = $_POST["password"];
    $confirm_password = $_POST["password_conf"];


    if(strlen($password) < 8){
        die("Le mot de passe doit contenir au moins 8 caractères");
    }

    // Vérifier si les mots de passe correspondent
    if ($password !== $confirm_password) {
        die("Les mots de passe ne correspondent pas !");
    }

    // Hacher le mot de passe pour plus de sécurité
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    //check id user already exists
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if($user){
        die("Cet utilisateur existe déjà !");
    }


    try {
        // Insérer les données dans la base de données
        $sql = "INSERT INTO users (name, username, email, phone, sexe, password, access) VALUES (:name, :username, :email, :phone, :sexe, :password, :access)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'name' => $name,
            'username' => $username,
            'phone' => $phone,
            'sexe' => $sexe,
            'password' => $hashed_password,
            'access' => 0
        ]);
        header("location: index.php");
    } catch (PDOException $e) {
        die("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
}

    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <?php include("link.php"); ?>
</head>

<?php include("navbar.php"); ?>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <form action="" class="lbc-form" method="post">
            <h1 class="text-center text-lbc-orange mb-4">Créer un compte</h1>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input required type="text" class="form-control" id="username" name="username" placeholder="Votre pseudo">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nom complet</label>
                    <input required type="text" class="form-control" id="name" name="name" placeholder="Votre nom">
                </div>
            </div>

            <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input required type="email" class="form-control" id="email" name="email" placeholder="Votre email">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input required type="password" class="form-control" id="password" name="password" placeholder="8 caractères minimum">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_conf" class="form-label">Confirmation</label>
                    <input required type="password" class="form-control" id="password_conf" name="password_conf" placeholder="Retapez votre mot de passe">
                </div>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Téléphone</label>
                <input required type="text" class="form-control" id="phone" name="phone" placeholder="Votre numéro">
            </div>

            <div class="mb-3">
                <label for="sexe" class="form-label">Genre</label>
                <select name="sexe" id="sexe" class="form-control">
                    <option value="homme">Homme</option>
                    <option value="femme">Femme</option>
                    <option value="autre">Autre</option>    
                </select>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </div>

            <div class="text-center mt-3">
                <a href="index.php" class="text-lbc-orange">Déjà un compte ? Connectez-vous</a>
            </div>
        </form>
    </div>
<?php include("footer.php"); ?>
</body>
</html>
