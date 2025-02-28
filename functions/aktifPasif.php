<?php
if ($_POST) {
    require_once "../db.php";
    $db = new Database();

    $konum = $_POST['konum'];
    $id = (int)$_POST['id'];
    $durum = (int)$_POST['durum'];

    // Tablo adını kontrol etmek ve sınırlamak için doğrulama yapın
    $allowedTables = array("nokta_urunler","nokta_kategoriler", "nokta_urun_markalar_1", "popup_kampanya", "nokta_banka_bilgileri", "kampanyalar" ,"banka_taksit_eslesme", "uyeler", "nokta_kataloglar", "nokta_haber", "noktaslider", "nokta_banner", "nokta_blog", "nokta_proje", "nokta_iletisim","nokta_banner","nokta_ilanlar, promosyon");
    if (!in_array($konum, $allowedTables)) {
        echo "Geçersiz tablo adı";
        exit;
    }

    // Güncelleme sorgusunu çalıştır
    $success = $db->update("UPDATE $konum SET aktif = :durum WHERE id = :id", [
        'durum' => $durum,
        'id' => $id
    ]);

    echo $id . " nolu kayıt değiştirildi";
}   
?>