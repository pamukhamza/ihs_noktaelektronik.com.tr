<?php
require_once '../db.php';
$db = new Database();

if (isset($_POST['kategori_id'])) {
    $kategori_id = $_POST['kategori_id'];
    $marka_id = $_POST['marka_id'];
    $uye_id = $_POST['uye_id'];

    // Get user price level
    $uye = $db->fetch("SELECT fiyat FROM uyeler WHERE id = :uye_id", [
        'uye_id' => $uye_id
    ]);
    $uyeFiyat = $uye['fiyat'];

    // Build query conditions
    $conditions = [];
    $params = [];

    if (!empty($kategori_id)) {
        $conditions[] = "n.KategoriID = :kategori_id";
        $params['kategori_id'] = $kategori_id;
    }

    if (!empty($marka_id)) {
        $conditions[] = "n.MarkaID = :marka_id";
        $params['marka_id'] = $marka_id;
    }

    $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    // Get filtered products
    $result = $db->fetchAll("SELECT n.*, m.title as marka_adi 
                            FROM nokta_urunler n 
                            LEFT JOIN nokta_urun_markalar_1 m ON n.MarkaID = m.id 
                            $whereClause", $params);

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
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<div class="col-12"><p class="text-center">Ürün bulunamadı...</p></div>';
    }
}
?>
