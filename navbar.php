<nav class="navbar navbar-expand-lg navbar-lbc">
  <div class="container d-flex align-items-center justify-content-between gap-4">
    <a class="navbar-brand" href="index.php">
      <img src="logo.png" alt="LeBonCoin Logo" class="navbar-logo">
    </a>
    <div class="flex-grow-1">
      <form method="GET" action="index.php">
        <div class="input-group">
          <input type="text" class="form-control" name="search" placeholder="Rechercher..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
          <button class="btn" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </form>
    </div>
    <div class="d-flex align-items-center gap-2">
      <?php if (isset($_SESSION['username'])): ?>
        <a href="notifications.php" class="btn btn-outline-secondary position-relative">
          <i class="fas fa-bell"></i>
          <?php
          // Afficher un point rouge si une notification non lue existe
          require_once("db.php");
          if (isset($_SESSION['id'])) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
            $stmt->execute([$_SESSION['id']]);
            $unread = $stmt->fetchColumn();
            if ($unread > 0) {
              echo '<span style="position:absolute;top:5px;right:5px;width:10px;height:10px;background:red;border-radius:50%;display:inline-block;"></span>';
            }
          }
          ?>
        </a>
        <a href="my_ads.php" class="btn btn-outline-secondary" title="Mes annonces">
          <i class="fas fa-user"></i>
        </a>
        <a href="logout.php" class="btn btn-orange">
          <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
        </a>
      <?php else: ?>
        <a href="register.php" class="btn btn-orange">
          <i class="fas fa-user-plus me-1"></i> S'inscrire
        </a>
        <a href="login.php" class="btn btn-outline-secondary">
          <i class="fas fa-sign-in-alt me-1"></i> Connexion
        </a>
      <?php endif; ?>
    </div>
  </div>
</nav>
