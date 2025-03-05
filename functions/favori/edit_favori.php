<?php
require_once '../db.php';
$database = new Database();

$productId = $_POST['product_id'] ?? null;
$userId = $_POST['uye_id'] ?? null;

if (empty($userId)) {
    header("Location: ../../tr/giris");
    exit();
}

$checkStatement = $database->fetch("SELECT id FROM nokta_uye_favoriler WHERE uye_id = :userId AND urun_id = :productId", ['userId' => $userId,'productId' => $productId]);

if ($checkStatement) { // fetch() sonucu null dönerse zaten favoriye eklenmemiştir
    $database->delete("DELETE FROM nokta_uye_favoriler WHERE uye_id = :userId AND urun_id = :productId", ['userId' => $userId,'productId' => $productId]);
    echo "removed";
} else {
    $database->insert("INSERT INTO nokta_uye_favoriler (uye_id, urun_id) VALUES (:userId, :productId)", ['userId' => $userId,'productId' => $productId]);
    echo "added";
}
?>
