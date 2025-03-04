<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once '../db.php';
$db = new Database();

if (isset($_POST['searchQuery'])) {
    $search = trim($_POST['searchQuery']);

    try {
        $result = $db->fetchAll("
            SELECT DISTINCT n.id, n.BLKODU, n.UrunAdiTR, n.stok, 
                            (SELECT r.KResim 
                             FROM nokta_urunler_resimler r 
                             WHERE r.UrunID = n.id 
                             ORDER BY r.id ASC 
                             LIMIT 1) as KResim,
                            m.title as marka_adi 
            FROM nokta_urunler n 
            LEFT JOIN nokta_urun_markalar m ON n.MarkaID = m.id 
            WHERE (n.UrunAdiTR LIKE :search OR n.BLKODU LIKE :search OR m.title LIKE :search) 
            AND n.web_comtr = '1' 
            ORDER BY n.UrunAdiTR ASC 
            LIMIT 10",
            ['search' => '%' . $search . '%']
        );

        echo json_encode($result);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['error' => 'Invalid request']);
?>
