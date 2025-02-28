<?php
include('baglanti.php'); // Veritabanı bağlantı bilgilerini içeren dosya
if(isset($_GET['kategori_id'])) {
    $kategori_id = $_GET['kategori_id'];
    $alt_kategoriler_sql = "SELECT * FROM nokta_kategoriler WHERE parent_id = $kategori_id";
    $alt_kategoriler_result = mysqli_query($connection, $alt_kategoriler_sql);

    $alt_kategoriler = array();
    while($row = mysqli_fetch_assoc($alt_kategoriler_result)) {
        $alt_kategoriler[] = $row;
    }
    echo json_encode($alt_kategoriler);
}
?>

