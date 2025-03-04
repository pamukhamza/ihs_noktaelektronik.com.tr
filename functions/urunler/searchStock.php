<?php
//searchStock.php
// Önce veritabanı bağlantısını sağlayın
require_once '../db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$db = new Database();

if (isset($_POST['searchQuery'])) {
    $search = $_POST['searchQuery'];
    
    // Search products
    $result = $db->fetchAll(" SELECT DISTINCT n.id, n.BLKODU, n.UrunAdiTR, n.stok, 
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
            ['search' => '%' . $search . '%']);

    if (!empty($result)) {
        foreach ($result as $row) {
            ?>
            <tr>
                <td><?= $row['BLKODU'] ?></td>
                <td><?= $row['UrunAdiTR'] ?></td>
                <td><?= $row['marka_adi'] ?></td>
                <td><?= $row['stok'] ?></td>
            </tr>
            <?php
        }
    } else {
        echo '<tr><td colspan="4" class="text-center">Ürün bulunamadı...</td></tr>';
    }
}
?>
