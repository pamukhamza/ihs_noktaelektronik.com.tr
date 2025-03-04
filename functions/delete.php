<?php
require_once "db.php";
$db = new Database();

$gid = $_POST['gid'];
$gel = $_POST['gel'] ?? '';
$type = $_POST['type'];
$file = $_POST['file'] ?? '';

if ($type == 'slider') {
    unlink("../assets/images/$gel");
    $db->delete("DELETE FROM noktaslider WHERE id = :id", ['id' => $gid]);
    exit;
} elseif ($type == 'popup') {
    unlink("../assets/images/$gel");
    $db->delete("DELETE FROM popup_kampanya WHERE id = :id", ['id' => $gid]);
    exit;
} elseif ($type == 'dosyaTipi') {
    unlink("../assets/images/icons/$gel");
    $db->delete("DELETE FROM nokta_yukleme_dosya_tipleri WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'blog') {
    unlink("../assets/images/$gel");
    $db->delete("DELETE FROM nokta_blog WHERE id = :id", ['id' => $gid]);
    exit;
} elseif ($type == 'yuklemeSil') {
    $db->delete("DELETE FROM nokta_yuklemeler WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'urun') {
    $db->delete("DELETE FROM nokta_urunler WHERE id = :id", ['id' => $gid]);
    $db->delete("DELETE FROM nokta_urunler_resimler WHERE UrunId = :id", ['id' => $gid]);
    $db->delete("DELETE FROM nokta_urunler_yuklemeler WHERE urun_id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'urun_foto') {
    unlink("../assets/images/urunler/$gel");
    $db->delete("DELETE FROM nokta_urunler_resimler WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'kullanici') {
    $db->delete("DELETE FROM nokta_admin WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'kategori') {
    $db->delete("DELETE FROM nokta_kategoriler WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'favoriKaldır') {
    $db->delete("DELETE FROM nokta_uye_favoriler WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'sss') {
    $db->delete("DELETE FROM sss WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'adres') {
    $db->delete("DELETE FROM nokta_iletisim WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'ebulten') {
    $db->delete("DELETE FROM nokta_ebulten WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'form') {
    $db->delete("DELETE FROM nokta_iletisim_form WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'haber') {
    unlink("../assets/images/$gel");
    $db->delete("DELETE FROM nokta_haber WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'fuarHareket') {
    $db->delete("DELETE FROM fuar WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'ikon') {
    unlink("../assets/images/$gel");
    $db->delete("DELETE FROM anasayfa_ikon WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'proje') {
    unlink("../assets/images/$gel");
    $db->delete("DELETE FROM nokta_proje WHERE id = :id", ['id' => $gid]);
    exit;
} elseif ($type == 'varyasyon') {
    $urunIds = explode(',', $gel);
    // Delete from nokta_urun_varyasyon
    $db->delete("DELETE FROM nokta_urun_varyasyon WHERE id = :id", ['id' => $gid]);
    foreach ($urunIds as $urunId) {
        // Remove specific value from varyasyon_id in nokta_urunler
        $db->update("UPDATE nokta_urunler SET varyasyon_id = TRIM(BOTH ',' FROM REPLACE(varyasyon_id, :gid, '')) WHERE FIND_IN_SET(:gid, varyasyon_id) > 0", [
            'gid' => $gid
        ]);
    }  
    exit;
} elseif($type == 'marka') {
    unlink("../assets/images/$gel");
    $db->delete("DELETE FROM nokta_urun_markalar WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'katalog') {
    unlink("../assets/images/kataloglar/$foto");
    unlink("../assets/uploads/kataloglar/$file");
    $db->delete("DELETE FROM nokta_kataloglar WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'banner') {
    unlink("../assets/images/$gel");
    $db->delete("DELETE FROM nokta_banner WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'kariyer') {
    $db->update("UPDATE nokta_kariyer SET aktif = 0 WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'ilan') {
    $db->delete("DELETE FROM nokta_ilanlar WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'indirme') {
    unlink("../assets$gel");
    $db->delete("DELETE FROM nokta_urunler_yuklemeler WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'filtre') {
    $db->delete("DELETE FROM filtreler WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'filtreKategori') {
    $db->delete("DELETE FROM filtre_kategoriler WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'user') {
    $db->delete("DELETE FROM kullanicilar WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'uye') {
    $db->delete("DELETE FROM uyeler WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'sepetler') {
    $db->delete("DELETE FROM uye_sepet WHERE uye_id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'sepet') {
    $db->delete("DELETE FROM uye_sepet WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'sepetKaldir') {
    $db->delete("DELETE FROM uye_sepet WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'tum_sepetler') {
    $db->delete("DELETE FROM uye_sepet", []);
    exit;
} elseif($type == 'sepeteFavoriEkle') {
    $db->delete("DELETE FROM nokta_uye_favoriler WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'yorumSil') {
    $db->delete("DELETE FROM nokta_uye_yorumlar WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'uyeAdresSil') {
    $db->delete("DELETE FROM adresler WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'bankaBilgisi') {
    $db->delete("DELETE FROM nokta_banka_bilgileri WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'kampanya') {
    $db->delete("DELETE FROM kampanyalar WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'banka') {
    $db->delete("DELETE FROM banka_taksit_eslesme WHERE id = :id", ['id' => $gid]);
    exit;
} elseif ($type == 'teknik-servis') {
    $db->update("UPDATE teknik_destek_urunler SET SILINDI = 1 WHERE id = :id", ['id' => $gid]);
    exit;
}
?>
