<?php

if (!isset($_SESSION["access"]) || $_SESSION["access"] != 1) {
    header("location: index.php");
    exit();
}

$id = $_GET["id"] ?? null;
if ($id) {
    require("db.php");
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    header("location: users.php");
} else {
    header("location: users.php");
}