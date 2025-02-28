<?php
require_once '../db.php';
$db = new Database();

if (isset($_POST['query'])) {
    $search = $_POST['query'];
    $lang = $_POST['lang'];
    $uye_id = $_POST['uye_id'];

    // Get user price level
    $uye = $db->fetch("SELECT fiyat FROM uyeler WHERE id = :uye_id", [
        'uye_id' => $uye_id
    ]);
    $uyeFiyat = $uye['fiyat'];

    // Search products
    $result = $db->fetchAll("SELECT DISTINCT n.*, m.title as marka_adi 
                            FROM nokta_urunler n 
                            LEFT JOIN nokta_urun_markalar_1 m ON n.MarkaID = m.id 
                            WHERE (n.UrunAdiTR LIKE :search OR n.BLKODU LIKE :search OR m.title LIKE :search) 
                            AND n.aktif = '1' 
                            ORDER BY n.UrunAdiTR ASC 
                            LIMIT 10", [
        'search' => '%' . $search . '%'
    ]);

    if (!empty($result)) {
        foreach ($result as $row) {
            $fiyat = !empty($row["DSF" . $uyeFiyat]) ? $row["DSF" . $uyeFiyat] : $row["KSF" . $uyeFiyat];
            $doviz = !empty($row["DSF" . $uyeFiyat]) ? $row["DOVIZ_BIRIMI"] : "₺";
            
            // Get product image
            $urunResim = $db->fetch("SELECT foto FROM nokta_urunler_resimler WHERE urun_id = :urun_id LIMIT 1", [
                'urun_id' => $row['BLKODU']
            ]);
            $resim = !empty($urunResim['foto']) ? $urunResim['foto'] : 'gorsel_hazirlaniyor.jpg';
            ?>
            <a href="<?= $lang ?>/urunler/<?= $row['seo_link'] ?>" class="list-group-item list-group-item-action">
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
