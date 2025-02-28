<?php
require_once '../db.php';
$db = new Database();

if (isset($_POST['urun_id'])) {
    $urun_id = $_POST['urun_id'];
    $uye_id = $_POST['uye_id'];

    // Get user price level
    $uye = $db->fetch("SELECT fiyat FROM uyeler WHERE id = :uye_id", [
        'uye_id' => $uye_id
    ]);
    $uyeFiyat = $uye['fiyat'];

    // Get related products
    $result = $db->fetchAll("SELECT n.*, m.title as marka_adi 
                            FROM nokta_urunler n 
                            LEFT JOIN nokta_urun_markalar_1 m ON n.MarkaID = m.id 
                            WHERE n.birlikte_al = :urun_id 
                            AND n.aktif = '1'", [
        'urun_id' => $urun_id
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
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="assets/images/urunler/<?= $resim ?>" class="card-img-top" alt="<?= $row['UrunAdiTR'] ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= $row['UrunAdiTR'] ?></h5>
                        <p class="card-text"><?= $row['marka_adi'] ?></p>
                        <p class="card-text"><?= number_format($fiyat, 2, ',', '.') ?> <?= $doviz ?></p>
                        <button class="btn btn-primary add-to-cart" data-urun-id="<?= $row['id'] ?>">Sepete Ekle</button>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<div class="col-12"><p class="text-center">Bu ürün için öneri bulunmamaktadır.</p></div>';
    }
}
?>
