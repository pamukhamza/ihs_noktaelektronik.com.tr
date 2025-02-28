<?php
if ($_POST) {
    include("../f.php"); // veritabanına bağlan

    $id = (int)$_POST['id'];
    $durum = $_POST['durum'];

    // Güncelleme sorgusunu hazırla ve çalıştır
    $query = "UPDATE nokta_urunler SET YeniUrun = :durum WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':durum', $durum, PDO::PARAM_INT);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    echo $durum;
    echo $id . " nolu kayıt değiştirildi" ;
}   
?>