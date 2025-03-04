<?php
require_once '../db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
if (isset($_POST['searchQuery'])) {
    $db = new Database();
    $search = $_POST['searchQuery'];

    $result = $db->fetchAll("SELECT DISTINCT n.seo_link, n.id, n.UrunAdiTR, n.UrunKodu, m.title as marka_adi 
                            FROM nokta_urunler n 
                            LEFT JOIN nokta_urun_markalar m ON n.MarkaID = m.id 
                            WHERE (n.UrunAdiTR LIKE :search OR n.UrunKodu LIKE :search OR m.title LIKE :search) 
                            AND n.web_comtr = '1' 
                            ORDER BY n.UrunAdiTR ASC 
                            LIMIT 10", ['search' => '%' . $search . '%']);

    $response = [];
    if (!empty($result)) {
        foreach ($result as $row) {
            // Get product image
            $urunResim = $db->fetch("SELECT KResim FROM nokta_urunler_resimler WHERE UrunID = :urun_id LIMIT 1", ['UrunID' => $row['id']]);
            $resim = !empty($urunResim['KResim']) ? $urunResim['KResim'] : 'gorsel_hazirlaniyor.jpg';

            // Push data to the response array
            $response[] = [
                'seo_link' => $row['seo_link'],
                'UrunKodu' => $row['UrunKodu'],  // Now included in the SELECT query
                'UrunAdiTR' => $row['UrunAdiTR'], // Now included in the SELECT query
                'MarkaAdi' => $row['marka_adi'],
                'KResim' => $resim
            ];
        }
    }
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    echo json_encode([]);
}
?>
