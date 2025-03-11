<?php
include '../functions.php';
require_once '../db.php';

function createXMLDocument($cari_kodu, $ad, $soyad, $email, $parola, $tc_no, $ulke, $il_adi, $ilce_adi, $posta_kodu, $tel, $adres, $firma_unvani, $vergi_no, $vergi_dairesi, $sabit_tel, $uye_tipi, $dosya_ad, $degistirme_tarihi) {
    // Create a new XML document
    $xmlDoc = new DOMDocument('1.0', 'UTF-8');
    $xmlDoc->formatOutput = true;
    $root = $xmlDoc->createElement('WCR');
    $xmlDoc->appendChild($root);

    // AYAR ALANI BASLANGIC
    $ayar = $xmlDoc->createElement('AYAR');
    $root->appendChild($ayar);
    $trsver = $xmlDoc->createElement('TRSVER');
    $trsver->appendChild($xmlDoc->createCDATASection('ASWCR1.02.03'));
    $ayar->appendChild($trsver);
    $dbname = $xmlDoc->createElement('DBNAME');
    $dbname->appendChild($xmlDoc->createCDATASection('WOLVOX'));
    $ayar->appendChild($dbname);
    $peruser = $xmlDoc->createElement('PERSUSER');
    $peruser->appendChild($xmlDoc->createCDATASection('sa'));
    $ayar->appendChild($peruser);
    $sube_kodu = $xmlDoc->createElement('SUBE_KODU');
    $sube_kodu->appendChild($xmlDoc->createCDATASection('3402'));
    $ayar->appendChild($sube_kodu);
    //AYAR ALANI SON

    // CARI BILGI ALANI BASLANGIC
    $cari = $xmlDoc->createElement('CARI');
    $root->appendChild($cari);

    $carikoduXML = $xmlDoc->createElement('CARIKODU');
    $carikoduXML->appendChild($xmlDoc->createCDATASection($cari_kodu));
    $cari->appendChild($carikoduXML);
    $ozel_kodu1 = $xmlDoc->createElement('OZEL_KODU1');
    $ozel_kodu1->appendChild($xmlDoc->createCDATASection('B2B'));
    $cari->appendChild($ozel_kodu1);
    $ozel_kodu2 = $xmlDoc->createElement('OZEL_KODU2');
    $ozel_kodu2->appendChild($xmlDoc->createCDATASection('İnternet'));
    $cari->appendChild($ozel_kodu2);
    $ozel_kodu3 = $xmlDoc->createElement('OZEL_KODU3');
    $ozel_kodu3->appendChild($xmlDoc->createCDATASection($uye_tipi));
    $cari->appendChild($ozel_kodu3);
    $muhkodualis = $xmlDoc->createElement('MUHKODU_ALIS');
    $muhkodualis->appendChild($xmlDoc->createCDATASection('120 01 50000'));
    $cari->appendChild($muhkodualis);
    $muhkodusatis = $xmlDoc->createElement('MUHKODU_SATIS');
    $muhkodusatis->appendChild($xmlDoc->createCDATASection('320 01 50000'));
    $cari->appendChild($muhkodusatis);
    $stokfiyati = $xmlDoc->createElement('STOK_FIYATI');
    $stokfiyati->appendChild($xmlDoc->createCDATASection('3'));
    $cari->appendChild($stokfiyati);
    $paz_blcrkodu = $xmlDoc->createElement('PAZ_BLCRKODU');
    $paz_blcrkodu->appendChild($xmlDoc->createCDATASection('86732'));
    $cari->appendChild($paz_blcrkodu);
    $adi = $xmlDoc->createElement('ADI');
    $adi->appendChild($xmlDoc->createCDATASection($ad));
    $cari->appendChild($adi);
    $soyadi = $xmlDoc->createElement('SOYADI');
    $soyadi->appendChild($xmlDoc->createCDATASection($soyad));
    $cari->appendChild($soyadi);
    $emailxml = $xmlDoc->createElement('E_MAIL');
    $emailxml->appendChild($xmlDoc->createCDATASection($email));
    $cari->appendChild($emailxml);
    $kullaniciadixml = $xmlDoc->createElement('WEB_USER_NAME');
    $kullaniciadixml->appendChild($xmlDoc->createCDATASection($email));
    $cari->appendChild($kullaniciadixml);
    $parolaxml = $xmlDoc->createElement('WEB_USER_PASSW');
    $parolaxml->appendChild($xmlDoc->createCDATASection($parola));
    $cari->appendChild($parolaxml);
    $tcxml = $xmlDoc->createElement('TC_KIMLIK_NO');
    $tcxml->appendChild($xmlDoc->createCDATASection($tc_no));
    $cari->appendChild($tcxml);
    $ulkexml = $xmlDoc->createElement('ULKESI_1');
    $ulkexml->appendChild($xmlDoc->createCDATASection($ulke));
    $cari->appendChild($ulkexml);
    $ilxml = $xmlDoc->createElement('ILI_1');
    $ilxml->appendChild($xmlDoc->createCDATASection($il_adi));
    $cari->appendChild($ilxml);
    $ilcexml = $xmlDoc->createElement('ILCESI_1');
    $ilcexml->appendChild($xmlDoc->createCDATASection($ilce_adi));
    $cari->appendChild($ilcexml);
    $postakoduxml = $xmlDoc->createElement('POSTA_KODU_1');
    $postakoduxml->appendChild($xmlDoc->createCDATASection($posta_kodu));
    $cari->appendChild($postakoduxml);
    $cep_tel = $xmlDoc->createElement('CEP_TEL');
    $cep_tel->appendChild($xmlDoc->createCDATASection($tel));
    $cari->appendChild($cep_tel);
    $adresxml = $xmlDoc->createElement('ADRESI_1');
    $adresxml->appendChild($xmlDoc->createCDATASection($adres));
    $cari->appendChild($adresxml);
    $firmaUnvani = $xmlDoc->createElement('TICARI_UNVANI');
    $firmaUnvani->appendChild($xmlDoc->createCDATASection($firma_unvani));
    $cari->appendChild($firmaUnvani);
    $verginoxml = $xmlDoc->createElement('VERGI_NO');
    $verginoxml->appendChild($xmlDoc->createCDATASection($vergi_no));
    $cari->appendChild($verginoxml);
    $vergidairesixml = $xmlDoc->createElement('VERGI_DAIRESI');
    $vergidairesixml->appendChild($xmlDoc->createCDATASection($vergi_dairesi));
    $cari->appendChild($vergidairesixml);
    $telxml = $xmlDoc->createElement('TEL1');
    $telxml->appendChild($xmlDoc->createCDATASection($sabit_tel));
    $cari->appendChild($telxml);
    $degistirme_tarihxml = $xmlDoc->createElement('DEGISTIRME_TARIHI');
    $degistirme_tarihxml->appendChild($xmlDoc->createCDATASection($degistirme_tarihi));
    $cari->appendChild($degistirme_tarihxml);

    $xmlFileName = $dosya_ad . $cari_kodu . '.xml';
    $xmlDoc->save('../../assets/cari/' . $xmlFileName);
}

$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $response = array();
    
    // Validate required fields
    $required_fields = ['ad', 'soyad', 'email', 'sifre', 'tel', 'firmaUnvani', 'vergi_dairesi'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $response = [
                'status' => 'error',
                'message' => 'Lütfen tüm zorunlu alanları doldurun.'
            ];
            echo json_encode($response);
        exit;
        }
    }
    if (empty($_POST['vergi_no']) && empty($_POST['tc_no'])) {
        $response = [
            'status' => 'error',
            'message' => 'Vergi numarası veya TC kimlik numarasından en az biri doldurulmalıdır.'
        ];
        echo json_encode($response);
        exit;
    }

    // Check if email already exists
    $existing_user = $db->fetch("SELECT id FROM uyeler WHERE email = :email", [
        'email' => $_POST['email']
    ]);

    if ($existing_user) {
        $response = [
            'status' => 'error',
            'message' => 'Bu e-posta adresi zaten kayıtlı.'
        ];
        echo json_encode($response);
        exit;
    }

    $aktivasyon_kodu = bin2hex(random_bytes(16));

    // Hash password
    $hashed_password = password_hash($_POST['sifre'], PASSWORD_DEFAULT);

    // Insert new user
    $success = $db->insert("INSERT INTO uyeler (
            ad, soyad, email, sifre, tel, firmaUnvani, 
            vergi_dairesi, vergi_no, ulke, il, ilce, 
            adres, posta_kodu, aktivasyon_kodu, aktif, 
            kayit_tarihi, son_giris, fiyat
        ) VALUES (
            :ad, :soyad, :email, :sifre, :tel, :firmaUnvani,
            :vergi_dairesi, :vergi_no, :ulke, :il, :ilce,
            :adres, :posta_kodu, :aktivasyon_kodu, 0,
            NOW(), NOW(), 4
        )", [
        'ad' => $_POST['ad'],
        'soyad' => $_POST['soyad'],
        'email' => $_POST['email'],
        'sifre' => $hashed_password,
        'tel' => $_POST['tel'],
        'firmaUnvani' => $_POST['firmaUnvani'],
        'vergi_dairesi' => $_POST['vergi_dairesi'],
        'vergi_no' => $_POST['vergi_no'],
        'ulke' => $_POST['ulke'] ?? 'Türkiye',
        'il' => $_POST['il'] ?? '',
        'ilce' => $_POST['ilce'] ?? '',
        'adres' => $_POST['adres'] ?? '',
        'posta_kodu' => $_POST['posta_kodu'] ?? '',
        'aktivasyon_kodu' => $aktivasyon_kodu
    ]);

    if ($success) {
        // Get the new user's ID
        $new_user_id = $db->lastInsertId();

        // Log the registration
        $db->insert("INSERT INTO uye_log (uye_id, islem, tarih) VALUES (:uye_id, :islem, NOW())", [
            'uye_id' => $new_user_id,
            'islem' => 'Kayıt yapıldı'
        ]);

        // Send activation email
        $activation_link = "https://www.noktaelektronik.com/aktivasyon?kod=" . $aktivasyon_kodu;
        $to = $_POST['email'];
        $subject = "Nokta Elektronik - Hesap Aktivasyonu";
        $message = "Sayın " . $_POST['ad'] . " " . $_POST['soyad'] . ",\n\n";
        $message .= "Hesabınızı aktifleştirmek için aşağıdaki linke tıklayın:\n";
        $message .= $activation_link . "\n\n";
        $message .= "Saygılarımızla,\nNokta Elektronik";
        $headers = "From: info@noktaelektronik.com";

        mail($to, $subject, $message, $headers);

        $response = [
            'status' => 'success',
            'message' => 'Kayıt başarılı. Lütfen e-posta adresinize gönderilen aktivasyon linkini kullanın.'
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Kayıt sırasında bir hata oluştu.'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>