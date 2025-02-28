<?php
require 'baglanti.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="urunler.xml"');

try {
    if (!isset($_GET['api_key']) || $_GET['api_key'] !== 'SizinAPIanahtariniz') {
        http_response_code(401);
        echo "<h1>Yetkisiz erişim</h1>";
        exit;
    }

    global $db;

    $query = $db->prepare("SELECT id, UrunKodu, UrunAdiTR, genel_ozellikler_TR, teknik_ozellikler_TR, KSF4, DSF4, DOVIZ_BIRIMI, stok, 
       OZEL_KODU1, ARA_GRUBU, ALT_GRUBU, GRUBU, MARKASI, OZELALANTANIM_18, BIRIMI FROM nokta_urunler WHERE aktif = 1");
    $query->execute();

    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;

    $root = $xml->createElement('Products'); // 'Urunler' yerine 'Products'
    $xml->appendChild($root);

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $product = $xml->createElement('Product'); // 'Urun' yerine 'Product'

        $id = $xml->createElement('ProductID', $row['id']);
        $code = $xml->createElement('ProductCode', $row['UrunKodu']);
        $name = $xml->createElement('ProductName', $row['UrunAdiTR']);
        $general_features = $xml->createElement('GeneralFeatures', $row['genel_ozellikler_TR']);
        $technical_features = $xml->createElement('TechnicalFeatures', $row['teknik_ozellikler_TR']);
        $price_ksf4 = $xml->createElement('PriceTL', $row['KSF4']);
        $price_dsf4 = $xml->createElement('PriceCurrency', $row['DSF4']);
        $currency = $xml->createElement('Currency', $row['DOVIZ_BIRIMI']);
        $stock = $xml->createElement('Stock', $row['stok']);
        $special_code1 = $xml->createElement('FourthGroup', $row['OZEL_KODU1']);
        $sub_group = $xml->createElement('ThirdGroup', $row['ARA_GRUBU']);
        $alt_group = $xml->createElement('SecondGroup', $row['ALT_GRUBU']);
        $group = $xml->createElement('FirstGroup', $row['GRUBU']);
        $brand = $xml->createElement('Brand', $row['MARKASI']);
        $special_field_18 = $xml->createElement('PackageWeight', $row['OZELALANTANIM_18']);
        $unit = $xml->createElement('Unit', $row['BIRIMI']);

        $product->appendChild($id);
        $product->appendChild($code);
        $product->appendChild($name);
        $product->appendChild($general_features);
        $product->appendChild($technical_features);
        $product->appendChild($price_ksf4);
        $product->appendChild($price_dsf4);
        $product->appendChild($currency);
        $product->appendChild($stock);
        $product->appendChild($special_code1);
        $product->appendChild($sub_group);
        $product->appendChild($alt_group);
        $product->appendChild($group);
        $product->appendChild($brand);
        $product->appendChild($special_field_18);
        $product->appendChild($unit);
        // 'Products' kök elemanına ekleyin
        $root->appendChild($product);
    }
    echo $xml->saveXML();
} catch (PDOException $e) {
    error_log("Veritabanı hatası: " . $e->getMessage());
    http_response_code(500);
    echo "<h1>Sunucu hatası. Lütfen daha sonra tekrar deneyiniz.</h1>";
}

$db = null;
?>
