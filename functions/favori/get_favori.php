<?php
require_once '../db.php';
$database = new Database();
$userId =  $_SESSION['id'];
$favoriteProducts = $database->fetchAll("SELECT urun_id FROM nokta_uye_favoriler WHERE uye_id = ?", ['uye_id' => $userId]);
echo json_encode($favoriteProducts);
?>