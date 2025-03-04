<?php
require_once '../db.php';

if (isset($_POST['searchQuery'])) {
    $db = new Database();
    $search = $_POST['searchQuery'];
    $uye_id = $_POST['uye_id'];

    // Search products
    $result = $db->fetchAll("SELECT DISTINCT n.seo_link, n.id, m.title as marka_adi 
                            FROM nokta_urunler n 
                            LEFT JOIN nokta_urun_markalar m ON n.MarkaID = m.id 
                            WHERE (n.UrunAdiTR LIKE :search OR n.UrunKodu LIKE :search OR m.title LIKE :search) 
                            AND n.web_comtr = '1' 
                            ORDER BY n.UrunAdiTR ASC 
                            LIMIT 10", ['search' => '%' . $search . '%']);
    echo $result;
    // Prepare the response array
    $response = [];

    if (!empty($result)) {
        foreach ($result as $row) {
            // Get product image
            $urunResim = $db->fetch("SELECT KResim FROM nokta_urunler_resimler WHERE UrunID = :urun_id LIMIT 1", ['UrunID' => $row['id']]);
            $resim = !empty($urunResim['KResim']) ? $urunResim['KResim'] : 'gorsel_hazirlaniyor.jpg';

            // Push data to the response array
            $response[] = [
                'seo_link' => $row['seo_link'],
                'UrunKodu' => $row['UrunKodu'],  // Add this if needed for search result
                'UrunAdiTR' => $row['UrunAdiTR'],
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
