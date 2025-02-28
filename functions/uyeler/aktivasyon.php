<?php
require_once '../db.php';
$db = new Database();

$success = $db->update("UPDATE uyeler SET aktif = 1 WHERE aktivasyon_kodu = :kod", [
    'kod' => $_GET['kod']
]);

header("Location: ../../index.php");
?>