<?php
require_once "../db.php";
$db = new Database();

// Fetch and return the cart content
$session_id = $_POST["session_id"];
$user_language = $_POST["language"];



// Get user price level
$uye = $db->fetch("SELECT * FROM uyeler WHERE id = :session_id", [
    'session_id' => $session_id
]);
$uyeFiyat = $uye['fiyat'];

// Get cart items with product details
$urun = $db->fetchAll("
    SELECT f.id AS sepet_id, f.uye_id, f.urun_id, f.adet, f.sepet_ozel_indirim, u.*, um.title
    FROM uye_sepet AS f
    JOIN nokta_urunler AS u ON f.urun_id = u.id
    JOIN nokta_urun_markalar AS um ON um.id = u.MarkaID
    WHERE f.uye_id = :session_id", [
    'session_id' => $session_id
]);

$product_count = 0; // Counter variable for tracking product count
$output = '';

if (empty($urun)) {
    $output .= '<input hidden id="sepet_bos" value="1">';
    $output .= '<div class="alert alert-info text-center" role="alert">Sepetiniz boş!</div>';
} else {
    $output .= '<ul class="list-group mb-2">';
    foreach ($urun as $row) {
        // Limit the number of products displayed to 4
        if ($product_count >= 4) {
            break;
        }
        $sepet = $db->fetch("SELECT DISTINCT KResim FROM nokta_urunler_resimler WHERE UrunID = :urun_id LIMIT 1", ['urun_id' => $row["id"]]);

        $output .= '<li class="list-group-item border-start-0 border-end-0 rounded-0">';
        $output .= "<div class='row' style='min-width: 300px'>";

        // Product photo
        $output .= '<div class="col-3">';
        $output .= '<a href="' . $user_language . '/urunler/' . $row['seo_link'] . '" class="text-body text-decoration-none">';
        $output .= '<img src="' . (empty($sepet['KResim']) ? 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/gorsel_hazirlaniyor.jpg' : 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/' . $sepet['KResim']) . '" style="width: 70px; border:1px solid lightgrey; border-radius: 6px">';
        $output .= '</a>';
        $output .= '</div>';

        // Product details
        $output .= '<div class="col-9">';
        $output .= '<ul class="list-unstyled ms-1">';
        $output .= '<li style="font-size:11pt">' . (strlen($row['UrunAdiTR']) > 30 ? substr($row['UrunAdiTR'], 0, 29) . '...' : $row['UrunAdiTR']) . '</li>';
        $output .= '<li style="color:grey; font-size:11pt"><span style="color: #0d6efd">' . $row["title"] . '</span> - ' . $row["adet"] . ' Adet</li>';
        $fiyat1 = !empty($row["DSF" . $uyeFiyat]) ? $row["DSF" . $uyeFiyat] : $row["KSF" . $uyeFiyat];
        $fiyat1_formatted = number_format((float) $fiyat1, 2, ',', '.');
        $output .= '<li style="color:#FC9803; font-weight: bold">' . $fiyat1_formatted . (!empty($row["DSF4"]) || !empty($row["DSF3"]) ? $row["DOVIZ_BIRIMI"] : "₺") . '</li>';
        $output .= '</ul>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</li>';

        $product_count++;
    }

    // If there are more than 4 products, show alert
    if (count($urun) > 4) {
        $output .= '<div class="alert alert-info text-center" role="alert">Tüm ürünleri görmek için sepete gidiniz.</div>';
    }

    $output .= '</ul>';
    $output .= '<a href="' . $user_language . '/sepet" class="btn btn-light bg-turuncu ms-2" style="color:white">Sepete Git</a>';
}

echo $output;
?>
