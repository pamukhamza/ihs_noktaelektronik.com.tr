<?php
include "../db.php";
$database = new Database();

$bank = $_POST['bank'] ?? null;
$kamp = $_POST['kamp'] ?? null;

if ($kamp !== null && $kamp !== 'Diğer Banka Kartları' && $kamp !== 'Yurt Dışı Kartları') {
    $query = "SELECT id FROM b2b_banka_kart_eslesme WHERE banka = :bank AND grup_tanim = :kamp";
    $params = ['bank' => $bank, 'kamp' => $kamp];
    $result = $database->fetch($query, $params);
    $id = $result ? $result['id'] : 19; // hangi pos kullanılacak ise onun id'sini yaz
} else {
    $query = "SELECT id FROM b2b_banka_kart_eslesme WHERE banka = :bank";
    $params = ['bank' => $bank];
    $result = $database->fetch($query, $params);
    $id = $result ? $result['id'] : 19; // hangi pos kullanılacak ise onun id'sini yaz
}

echo $id;
?>
