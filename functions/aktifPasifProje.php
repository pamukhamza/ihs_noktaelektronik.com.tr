<?php
if ($_POST) {
    require_once "../db.php";
    $db = new Database();

    $id = (int)$_POST['id'];
    $durum = (int)$_POST['durum'];

    // Güncelleme sorgusunu çalıştır
    $success = $db->update("UPDATE nokta_urunler SET proje = :durum WHERE id = :id", [
        'durum' => $durum,
        'id' => $id
    ]);

    echo $id . " nolu kayıt değiştirildi";
}
?>