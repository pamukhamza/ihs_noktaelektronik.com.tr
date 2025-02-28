<?php
require_once '../db.php';
$db = new Database();

session_name("user_session");
session_start();
$userId = $_SESSION['id'];

// Fetch favorite products
$favoriteProducts = $db->fetchColumn("SELECT urun_id FROM nokta_uye_favoriler WHERE uye_id = :user_id", [
    'user_id' => $userId
]);

echo json_encode($favoriteProducts);
?>