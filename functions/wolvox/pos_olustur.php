<?php
function posXmlOlustur(
    $cari_kodu, $doviz_hes_isle, $tarihxml, $vadexml, $toplamxml, $toplamdvzxml, 
    $dvz_alisxml, $dvz_satisxml, $aciklamaxml, $blbnhskoduxml, $banka_adixml, 
    $taksit_sayisixml, $doviz, $tanimi
) {
    // Yeni XML belgesi oluştur
    $xmlDoc = new DOMDocument('1.0', 'UTF-8');
    $xmlDoc->formatOutput = true;
    $root = $xmlDoc->createElement('WCH');
    $xmlDoc->appendChild($root);

    // CDATA içeren bir element oluşturma fonksiyonu
    function appendCdataElement($doc, $parent, $name, $value) {
        $element = $doc->createElement($name);
        $element->appendChild($doc->createCDATASection($value));
        $parent->appendChild($element);
    }

    // AYAR ALANI
    $ayar = $xmlDoc->createElement('AYAR');
    $root->appendChild($ayar);

    $ayarElements = [
        'TRSVER'    => 'ASWCH1.02.03',
        'DBNAME'    => 'WOLVOX',
        'PERSUSER'  => 'sa',
        'SUBE_KODU' => '3402'
    ];

    foreach ($ayarElements as $key => $value) {
        appendCdataElement($xmlDoc, $ayar, $key, $value);
    }

    // CARI HAREKET ALANI
    $cariHareket = $xmlDoc->createElement('CARIHAREKET');
    $root->appendChild($cariHareket);

    $hareket = $xmlDoc->createElement('HAREKET');
    $cariHareket->appendChild($hareket);

    $cariElements = [
        'BLCRKODU'         => $cari_kodu,
        'DOVIZ_HES_ISLE'   => $doviz_hes_isle,
        'ISLEM_TURU'       => '6',
        'TARIHI'           => $tarihxml,
        'DEGISTIRME_TARIHI'=> $tarihxml,
        'VADESI'           => $vadexml,
        'KPBDVZ'           => $doviz_hes_isle,
        'KPB_ATUT'         => $toplamxml,
        'DVZ_ATUT'         => $toplamdvzxml,
        'GM_ENTEGRASYON'   => '1',
        'MUH_DURUM'        => '1',
        'DOVIZ_KULLAN'     => $doviz_hes_isle,
        'DOVIZ_ALIS'       => $dvz_alisxml,
        'DOVIZ_SATIS'      => $dvz_satisxml,
        'DOVIZ_BIRIMI'     => $doviz,
        'ACIKLAMA'         => $aciklamaxml,
        'KASA_ADI'         => '',
        'BLBNHSKODU'       => $blbnhskoduxml,
        'BANKA_ADI'        => $banka_adixml,
        'KAYDEDEN'         => 'B2B Sistem',
        'POS_DETAY'        => $tanimi,
        'SUBE_KODU'        => '3402',
        'TAKSIT_SAYISI'    => $taksit_sayisixml
    ];

    foreach ($cariElements as $key => $value) {
        appendCdataElement($xmlDoc, $hareket, $key, $value);
    }

    // XML dosyasını kaydet
    $xmlFileName = 'CRHRKT_' . $cari_kodu . uniqid(4) . '.xml';
    $filePath = '../../assets/xml/pos/' . $xmlFileName;
    $xmlDoc->save($filePath);

    return $filePath;
}
?>
