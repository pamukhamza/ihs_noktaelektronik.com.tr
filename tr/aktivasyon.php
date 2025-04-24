<?php
include '../functions/db.php';
$uye_id = $_GET['id'];
$aktif = '1';
$db = new Database();
$update = $db->update("UPDATE uyeler SET aktivasyon = :aktif, aktif = :aktif WHERE id = :id" , ['aktif' => $aktif, 'id' => $uye_id]);
header("Location: giris?s=26");

?>