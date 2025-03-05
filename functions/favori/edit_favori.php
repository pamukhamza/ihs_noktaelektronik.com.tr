<?php
require_once '../db.php';
$database = new Database();

    $productId = $_POST['product_id'];
    $userId = $_POST['uye_id'];
    if(empty($userId)){
        header("Location:../../tr/giris");
        exit(); // Terminate script after redirect
    }
    else{
        $checkStatement = $database->fetch("SELECT id FROM nokta_uye_favoriler WHERE uye_id = $userId AND urun_id = $productId");
        if (!empty($checkStatement)) {
            $database ->delete("DELETE FROM nokta_uye_favoriler WHERE uye_id = $userId AND urun_id = $productId");
            echo "removed";
        } else {
            $database ->insert("INSERT INTO nokta_uye_favoriler (uye_id, urun_id) VALUES ($userId, $productId)");
            echo "added";
        }
    }
?>