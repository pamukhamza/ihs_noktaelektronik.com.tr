<?php
    require_once 'db.php';
    $database = new Database();

    $currentTime = time();
    $userId = $_POST['uye_id'];

    $database->update("UPDATE uyeler SET son_aktif = $currentTime WHERE id = $userId");
?>