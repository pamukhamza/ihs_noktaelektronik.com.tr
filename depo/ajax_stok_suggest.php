<?php
include "functions.php";
if (isset($_POST['stok_kodu'])) {
    /* */
    $stok_kodu = $_POST['stok_kodu'];
    // Veritabanından benzer stok kodlarını sorgula ve sınırla
    $q = $db->prepare("SELECT DISTINCT stok_kodu FROM products WHERE stok_kodu LIKE :stok_kodu LIMIT 5");
    $q->bindValue(':stok_kodu', '%'. $stok_kodu . '%', PDO::PARAM_STR);
    $q->execute();
    // Benzer stok kodlarını ekrana yazdır
    $count = 0;
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        echo '<div class="form-control">' . $row['stok_kodu'] . '</div>';
        $count++;
    }
    // Eğer 10'dan fazla sonuç varsa "Daha fazla sonuç bulunuyor" gibi bir mesaj da ekleyebilirsiniz
    if ($count >= 5) {
        echo '<div>Daha fazla sonuç bulunuyor...</div>';
    }
}
?>
