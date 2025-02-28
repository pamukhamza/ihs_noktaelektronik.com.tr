<?php
require_once '../db.php';
$db = new Database();

// Get products
$products = $db->fetchAll("SELECT n.*, m.title as marka_adi, k.KategoriAdi 
                          FROM nokta_urunler n 
                          LEFT JOIN nokta_urun_markalar_1 m ON n.MarkaID = m.id 
                          LEFT JOIN nokta_kategoriler k ON n.KategoriID = k.id 
                          WHERE n.aktif = '1'");

// Create XML document
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><products></products>');

foreach ($products as $product) {
    $productNode = $xml->addChild('product');
    $productNode->addChild('id', $product['id']);
    $productNode->addChild('name', htmlspecialchars($product['UrunAdiTR']));
    $productNode->addChild('code', $product['BLKODU']);
    $productNode->addChild('brand', htmlspecialchars($product['marka_adi']));
    $productNode->addChild('category', htmlspecialchars($product['KategoriAdi']));
    $productNode->addChild('stock', $product['STOK']);
    
    // Get product images
    $images = $db->fetchAll("SELECT foto FROM nokta_urunler_resimler WHERE urun_id = :urun_id ORDER BY sira ASC", [
        'urun_id' => $product['BLKODU']
    ]);
    
    $imagesNode = $productNode->addChild('images');
    foreach ($images as $image) {
        $imagesNode->addChild('image', $image['foto']);
    }
}

// Set headers and output XML
header('Content-Type: application/xml; charset=utf-8');
echo $xml->asXML();
?>