<?php
require '../admin_template.php';
require '../functions.php';
$db = new Database();

$userId = $_SESSION['id'];

// Fetch favorite products
$favoriteProducts = $db->fetchColumn("SELECT urun_id FROM nokta_uye_favoriler WHERE uye_id = :user_id", ['user_id' => $userId]);

echo json_encode($favoriteProducts);
?>