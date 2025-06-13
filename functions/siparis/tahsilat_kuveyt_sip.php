<?php
ini_set('display_errors', 1);  // Hataları ekrana göster
error_reporting(E_ALL);   

require_once '../db.php';
require_once '../functions.php';
require_once '../bank/dekont_olustur.php';
require_once '../wolvox/pos_olustur.php';
require_once '../../mail/mail_gonder.php';
$db = new Database();

$dolarKur = $db->fetch("SELECT * FROM b2b_kurlar WHERE id = 2");
$satis_dolar = $dolarKur['satis'];
$satis_dolar = str_replace('.', ',', $satis_dolar);
$alis_dolar = $dolarKur['alis'];
$alis_dolar = str_replace('.', ',', $alis_dolar);
$euroKur = $db->fetch("SELECT * FROM b2b_kurlar WHERE id = 3");
$satis_euro = $euroKur['satis'];
$satis_euro = str_replace('.', ',', $satis_euro);
$alis_euro = $euroKur['alis'];
$alis_euro = str_replace('.', ',', $alis_euro);

function generateUniqueOrderNumber() {
    $prefix = 'WEB';
    $datePart = date('YmdHi');
    $randomPart = mt_rand(1000, 9999);
    $orderNumber = $prefix . $datePart . $randomPart;
    return $orderNumber;
}
$siparisNumarasi = generateUniqueOrderNumber();

$AuthenticationResponse = $_POST["AuthenticationResponse"];
$RequestContent = urldecode($AuthenticationResponse);
$xxml = simplexml_load_string($RequestContent);

if (isset($_GET['cariveri']) && $xxml->ResponseCode == "00" && $xxml->ResponseMessage == "Kart doğrulandı.") {
    $veri = base64_decode($_GET['cariveri']);
    $decodedVeri = json_decode($veri, true);
    $yantoplam = $decodedVeri["yantoplam"];
    // Convert to decimal
    $yantoplam1 = floatval($yantoplam);
    // Format according to specified format
    $yantoplam = number_format($yantoplam1, 2, ',', '.');
    $banka_id = $decodedVeri["banka_id"];
    $hesap = $decodedVeri["hesap"];
    $uye_id = $decodedVeri["uye_id"];
    $cariOdeme = "cari";
    $tip = $decodedVeri["tip"];
    $lang = $decodedVeri["lang"];
    $cardNo = $decodedVeri["cardNo"];
    $maskedCardNo = substr($cardNo, 0, 4) . str_repeat('*', strlen($cardNo) - 8) . substr($cardNo, -4);
    $cardHolder = $decodedVeri["cardHolder"];

    $MerchantOrderId = $xxml->VPosMessage->MerchantOrderId;
    $Amount = $xxml->VPosMessage->Amount;
    $MD = $xxml->MD;
    $taksit_sayisi = "0";
    $CustomerId = "93981545";
    $MerchantId = "61899";
    $UserName = "kadirbabur";
    $Password = "Dell28736.!";
    $HashedPassword = base64_encode(sha1($Password, "ISO-8859-9"));
    $HashData = base64_encode(sha1($MerchantId.$MerchantOrderId.$Amount.$UserName.$HashedPassword, "ISO-8859-9"));

    $doviz = ($hesap == 1) ? "$" : "TL";

    $xml = '<KuveytTurkVPosMessage xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
				<APIVersion>TDV2.0.0</APIVersion>
				<HashData>'.$HashData.'</HashData>
				<MerchantId>'.$MerchantId.'</MerchantId>
				<CustomerId>'.$CustomerId.'</CustomerId>
				<UserName>'.$UserName.'</UserName>
				<TransactionType>Sale</TransactionType>
				<InstallmentCount>'.$taksit_sayisi.'</InstallmentCount>
				<CurrencyCode>0949</CurrencyCode>
				<Amount>'.$Amount.'</Amount>
				<MerchantOrderId>'.$MerchantOrderId.'</MerchantOrderId>
				<TransactionSecurity>3</TransactionSecurity>
				<KuveytTurkVPosAdditionalData>
				<AdditionalData>
					<Key>MD</Key>
					<Data>'.$MD.'</Data>
				</AdditionalData>
			</KuveytTurkVPosAdditionalData>
			</KuveytTurkVPosMessage>';

    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml', 'Content-length: '. strlen($xml)));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, 'https://sanalpos.kuveytturk.com.tr/ServiceGateWay/Home/ThreeDModelProvisionGate');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);
        $xml = simplexml_load_string($data);

        if ($xml->ResponseCode == "00") {
            $banka = $db->fetch("SELECT * FROM b2b_banka_taksit_eslesme WHERE id = :banka_id", ['banka_id' => $banka_id]);
            $ticariProgram = $banka["ticari_program"];

            $banka_pos = $db->fetch("SELECT * FROM b2b_banka_pos_listesi WHERE id = :id", ['id' => $ticariProgram]);
            $blbnhskodu = $banka_pos["BLBNHSKODU"];
            $banka_adi = $banka_pos["BANKA_ADI"];
            $banka_tanimi = $banka_pos["TANIMI"];

            $uye = $db->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $uye_id]);
            $uyecarikod = $uye['BLKODU'];
            $firma_unvani = $uye['firmaUnvani'];
            $cariMail = $uye['email'];
            $yonetici_maili = 'h.pamuk@noktaelektronik.net';

            // Get currency rates
            $doviz_kur = $db->fetch("SELECT * FROM b2b_kurlar WHERE id = 2");
            $dov_al = str_replace('.', ',', $doviz_kur["alis"]);
            $dov_sat = str_replace('.', ',', $doviz_kur["satis"]);

            $currentDateTime = date("d.m.Y H:i:s");
            $degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));

            // Record payment
            $pos_id = 3;
            $basarili = 1;
            $sonucStr = "Ödeme işlemi başarılı: " . $xml->ResponseMessage . ' Kod= ' . $xml->ResponseCode;
            
            $success = $db->insert("INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, islem_turu, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :islem_turu, :tutar, :basarili)",
                ['uye_id' => $uye_id,'pos_id' => $pos_id,'islem' => $sonucStr,'islem_turu' => $cariOdeme,'tutar' => $yantoplam1,'basarili' => $basarili]);

            $inserted_id = $db->lastInsertId();

            dekontOlustur($uye_id, $inserted_id, $firma_unvani, $maskedCardNo, $cardHolder, $taksit_sayisi, $yantoplam, $degistirme_tarihi);
            posXmlOlustur($uyecarikod, $hesap, $degistirme_tarihi,$degistirme_tarihi,$yantoplam,'',$dov_al,$dov_sat,$siparisNumarasi,$blbnhskodu,$banka_adi,$taksit_sayisi, $doviz,$banka_tanimi);
            $mail_icerik = cariOdeme($firma_unvani,$yantoplam,$taksit_sayisi);
            mailGonder($cariMail, 'Cari Ödeme Bildirimi', $mail_icerik,'Nokta Elektronik');
             ("Location: ../../tr/onay?cari_odeme=");
            exit();
        } else {
            // ResponseCode 00 değilse hata mesajı göster veya başka bir işlem yap
            $pos_id = 3;
            $basarili = 0;
            $sonucStr = "Ödeme işlemi başarısız: " . $xml->ResponseMessage . ' Kod= ' . $xml->ResponseCode;
            $db->insert("INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)", [
                'uye_id' => $uye_id,'pos_id' => $pos_id,'islem' => $sonucStr,'tutar' => $yantoplam1,'basarili' => $basarili]);

            header("Location: ../../tr/cariodeme?code=".$xml->ResponseCode."&message=".$xml->ResponseMessage);
        }
        curl_close($ch);
    }
    catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}

?>