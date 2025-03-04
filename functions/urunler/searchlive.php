<?php
require_once '../db.php';
$db = new Database();

if (isset($_POST['searchQuery']) && isset($_POST['uye_id'])) {
    $search = $_POST['query'];
    $uye_id = $_POST['uye_id'];

    // Get user price level
    $uye = $db->fetch("SELECT fiyat FROM uyeler WHERE id = :uye_id", ['uye_id' => $uye_id]);
    $uyeFiyat = $uye['fiyat'];

    // Search products
    $result = $db->fetchAll("SELECT DISTINCT n.DSF4, n.DSF3, n.DSF2, n.DSF1, n.KSF4, n.KSF3, n.KSF2, n.KSF1, n.DOVIZ_BIRIMI, n.seo_link, n.id, m.title as marka_adi 
                            FROM nokta_urunler n 
                            LEFT JOIN nokta_urun_markalar m ON n.MarkaID = m.id 
                            WHERE (n.UrunAdiTR LIKE :search OR n.UrunKodu LIKE :search OR m.title LIKE :search) 
                            AND n.web_comtr = '1' 
                            ORDER BY n.UrunAdiTR ASC 
                            LIMIT 10", ['search' => '%' . $search . '%']);

    // Prepare the response array
    $response = [];

    if (!empty($result)) {
        foreach ($result as $row) {
            $fiyat = !empty($row["DSF" . $uyeFiyat]) ? $row["DSF" . $uyeFiyat] : $row["KSF" . $uyeFiyat];
            $doviz = !empty($row["DSF" . $uyeFiyat]) ? $row["DOVIZ_BIRIMI"] : "₺";

            // Get product image
            $urunResim = $db->fetch("SELECT KResim FROM nokta_urunler_resimler WHERE UrunID = :urun_id LIMIT 1", ['UrunID' => $row['id']]);
            $resim = !empty($urunResim['KResim']) ? $urunResim['KResim'] : 'gorsel_hazirlaniyor.jpg';

            // Push data to the response array
            $response[] = [
                'seo_link' => $row['seo_link'],
                'UrunKodu' => $row['UrunKodu'],  // Add this if needed for search result
                'UrunAdiTR' => $row['UrunAdiTR'],
                'MarkaAdi' => $row['marka_adi'],
                'fiyat' => number_format($fiyat, 2, ',', '.'),
                'doviz' => $doviz,
                'KResim' => $resim
            ];
        }
    }

    // Output the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // If query or user ID are not set, return an empty array
    echo json_encode([]);
}
?>
