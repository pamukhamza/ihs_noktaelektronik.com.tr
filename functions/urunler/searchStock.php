<?php
// searchStock.php
require_once '../db.php';
$db = new Database();

header('Content-Type: application/json');

if (isset($_POST['searchQuery'])) {
    $search = $_POST['searchQuery'];

    // Ürünleri ara
    $result = $db->fetchAll("
        SELECT DISTINCT n.BLKODU, n.UrunAdiTR, n.stok, n.resim, n.id, m.title as marka_adi 
        FROM nokta_urunler n 
        LEFT JOIN nokta_urun_markalar m ON n.MarkaID = m.id 
        WHERE (n.UrunAdiTR LIKE :search OR n.BLKODU LIKE :search OR m.title LIKE :search) 
        AND n.web_comtr = '1' 
        ORDER BY n.UrunAdiTR ASC 
        LIMIT 10", 
        ['search' => '%' . $search . '%']
    );

    echo json_encode($result);
    exit;
}
echo json_encode([]);
?>
