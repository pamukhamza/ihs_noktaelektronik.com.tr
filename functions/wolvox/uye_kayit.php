<?php
function uyeXmlOlustur($params) {
    $xmlDoc = new DOMDocument('1.0', 'UTF-8');
    $xmlDoc->formatOutput = true;
    $root = $xmlDoc->createElement('WCR');
    $xmlDoc->appendChild($root);

    $ayar = $xmlDoc->createElement('AYAR');
    $root->appendChild($ayar);
    $ayarElements = ['TRSVER' => 'ASWCR1.02.03', 'DBNAME' => 'WOLVOX', 'PERSUSER' => 'sa', 'SUBE_KODU' => '3402'];
    foreach ($ayarElements as $key => $value) {
        $el = $xmlDoc->createElement($key);
        $el->appendChild($xmlDoc->createCDATASection($value));
        $ayar->appendChild($el);
    }

    $cari = $xmlDoc->createElement('CARI');
    $root->appendChild($cari);
    $cariElements = [
        'CARIKODU' => $params['cari_kodu'], 
        'OZEL_KODU1' => 'B2B', 
        'OZEL_KODU2' => 'İnternet Satış', 
        'OZEL_KODU3' => $params['uye_tipi'], 
        'MUHKODU_ALIS' => '120 01 50000', 
        'MUHKODU_SATIS' => '320 01 50000',
        'STOK_FIYATI' => '3', 
        'PAZ_BLCRKODU' => $params['PAZ_BLCRKODU'], 
        'ADI' => $params['ad'], 
        'SOYADI' => $params['soyad'], 
        'E_MAIL' => $params['email'], 
        'WEB_USER_NAME' => $params['email'], 
        'WEB_USER_PASSW' => $params['parola'], 
        'TC_KIMLIK_NO' => $params['tc_no'], 
        'ULKESI_1' => $params['ulke'], 
        'ILI_1' => $params['il_adi'], 
        'ILCESI_1' => $params['ilce_adi'], 
        'POSTA_KODU_1' => $params['posta_kodu'], 
        'CEP_TEL' => $params['tel'], 
        'ADRESI_1' => $params['adres'], 
        'TICARI_UNVANI' => $params['firma_unvani'], 
        'VERGI_NO' => $params['vergi_no'], 
        'VERGI_DAIRESI' => $params['vergi_dairesi'], 
        'TEL1' => $params['sabit_tel'], 
        'DEGISTIRME_TARIHI' => $params['degistirme_tarihi']
    ];
    foreach ($cariElements as $key => $value) {
        $el = $xmlDoc->createElement($key);
        $el->appendChild($xmlDoc->createCDATASection($value));
        $cari->appendChild($el);
    }

    $xmlFileName = $params['dosya_ad'] . $params['cari_kodu'] . '.xml';
    $xmlDoc->save('../../assets/cari/' . $xmlFileName);
}
