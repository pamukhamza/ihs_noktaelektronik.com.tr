<?php
require_once '../db.php';
$db = new Database();

if (isset($_POST['query'])) {
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

    if (!empty($result)) {
        foreach ($result as $row) {
            $fiyat = !empty($row["DSF" . $uyeFiyat]) ? $row["DSF" . $uyeFiyat] : $row["KSF" . $uyeFiyat];
            $doviz = !empty($row["DSF" . $uyeFiyat]) ? $row["DOVIZ_BIRIMI"] : "₺";
            
            // Get product image
            $urunResim = $db->fetch("SELECT KResim FROM nokta_urunler_resimler WHERE UrunID = :urun_id LIMIT 1", ['UrunID' => $row['id']]);
            $resim = !empty($urunResim['KResim']) ? $urunResim['KResim'] : 'gorsel_hazirlaniyor.jpg';
            ?>
            <a href="tr/urunler/<?= $row['seo_link'] ?>" class="list-group-item list-group-item-action">
                <div class="row">
                    <div class="col-2">
                        <img src="assets/images/urunler/<?= $resim ?>" style="width: 50px">
                    </div>
                    <div class="col-7">
                        <p class="mb-1"><?= $row['UrunAdiTR'] ?></p>
                        <small class="text-muted"><?= $row['marka_adi'] ?></small>
                    </div>
                    <div class="col-3 text-end">
                        <p class="mb-1"><?= number_format($fiyat, 2, ',', '.') ?> <?= $doviz ?></p>
                    </div>
                </div>
            </a>
            <?php
        }
    } else {
        echo '<p class="list-group-item">Ürün bulunamadı...</p>';
    }
}
?>
