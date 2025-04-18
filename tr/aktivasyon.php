<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../functions/db.php';
$uye_id = $_GET['id'];
$aktif = '1';
$update = $db->update("UPDATE uyeler SET aktivasyon = :aktif WHERE id = :id" , ['aktif' => $aktif, 'id' => $uye_id]);
header("Location: giris?s=26");

?>