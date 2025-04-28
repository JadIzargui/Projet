<?php
session_start();
require("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if(empty($email) || empty($password)){
        die("Veuillez remplir tous les champs !");
    }

    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])){
        $_SESSION['username'] = $user['username'];
        $_SESSION['id'] = $user['id'];
        $_SESSION['access'] = $user['access'];

        // Redirect to index.php after login to show listings !!PAS DE REDIRECTION!!
        header("location: index.php");
        exit();
    }else{
        die("Nom d'utilisateur ou mot de passe incorrect !");
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion - LeBonCoin</title>
    <?php include 'header.php'; ?>
</head>
<body class="bg-lbc-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="" method="post" class="lbc-form">
                    <h1 class="text-center text-lbc-orange mb-4">Connexion</h1>
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input required type="text" class="form-control" id="email" name="email" placeholder="Votre email" />
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input required type="password" class="form-control" id="password" name="password" placeholder="Votre mot de passe" />
                    </div>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" id="remember" />
                        <label class="form-check-label" for="remember">Se souvenir de moi</label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                    <div class="text-center mt-3">
        <a href="register.php" class="text-lbc-orange">Pas encore inscrit ? Créez un compte</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include("footer.php"); ?>
</body>
</html>
