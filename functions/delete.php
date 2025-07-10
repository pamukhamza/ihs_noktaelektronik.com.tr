<?php
require_once "db.php";
$db = new Database();

$gid = $_POST['gid'];
$gel = $_POST['gel'] ?? '';
$type = $_POST['type'];
$file = $_POST['file'] ?? '';

if($type == 'favoriKaldır') {
    $db->delete("DELETE FROM nokta_uye_favoriler WHERE id = :id", ['id' => $gid]);
    exit;
}elseif($type == 'adres') {
    $db->delete("DELETE FROM nokta_iletisim WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'form') {
    $db->delete("DELETE FROM nokta_iletisim_form WHERE id = :id", ['id' => $gid]);
    exit;
} elseif($type == 'kariyer') {
    $db->update("UPDATE nokta_kariyer SET aktif = 0 WHERE id = :id", ['id' => $gid]);
    exit;
}elseif($type == 'sepetler') {
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
    $db->delete("DELETE FROM b2b_adresler WHERE id = :id", ['id' => $gid]);
    exit;
}
?>
