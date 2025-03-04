<?php
require_once '../db.php';
if (isset($_POST['searchQuery'])) {
    $db = new Database();
    $search = $_POST['searchQuery'];
    $result = $db->fetchAll("SELECT DISTINCT n.seo_link, n.id, n.UrunAdiTR, n.UrunKodu, m.title as marka_adi 
                            FROM nokta_urunler n 
                            LEFT JOIN nokta_urun_markalar m ON n.MarkaID = m.id 
                            WHERE (n.UrunAdiTR LIKE '%$search%' OR n.UrunKodu LIKE '%$search%' OR m.title LIKE '%$search%') 
                            AND n.web_comtr = '1' 
                            ORDER BY n.UrunAdiTR ASC 
                            LIMIT 5");

    $response = [];
    if (!empty($result)) {
        foreach ($result as $row) {
            $urun_id = $row['id'];
            $urunResim = $db->fetch("SELECT KResim FROM nokta_urunler_resimler WHERE UrunID = $urun_id LIMIT 1");
            $resim = !empty($urunResim['KResim']) ? $urunResim['KResim'] : 'gorsel_hazirlaniyor.jpg';

            // Push data to the response array
            $response[] = [
                'id' => $row['id'],
                'UrunKodu' => $row['UrunKodu'],  // Now included in the SELECT query
                'UrunAdiTR' => $row['UrunAdiTR'], 
                'KResim' => $resim
            ];
        }
    }
    header('Content-Type: application/json');
    echo json_encode($response);
}else {
    echo json_encode([]);
}
?>