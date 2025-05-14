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
            header("Location: ../../tr/onay?cari_odeme=");
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
if (isset($_GET['veri']) && $xxml->ResponseCode == "00" && $xxml->ResponseMessage == "Kart doğrulandı.") {
    $veri = base64_decode($_GET['veri']);
    $decodedVeri = json_decode($veri, true);
    $yanSepetToplami = $decodedVeri["yanSepetToplami"];
    $yanSepetKdv = $decodedVeri["yanSepetKdv"];
    $yanIndirim = $decodedVeri["yanIndirim"];
    $yanKargo = $decodedVeri["yanKargo"];
    $promosyon_kodu = $_POST["promosyonKodu"];
    $siparisOdeme = "siparis";
    $desi = $decodedVeri["desi"];
    $deliveryOption = $decodedVeri["deliveryOption"];
    $hesap = "0";
    $yantoplam = $decodedVeri["yantoplam"];
    $yantoplam1 = floatval($yantoplam);
    $yantoplam = str_replace('.', ',', $yantoplam);
    $banka_id = $decodedVeri["banka_id"];
    $uye_id = $decodedVeri["uye_id"];
    $tip = $decodedVeri["tip"];
    $lang = $decodedVeri["lang"];

    $MerchantOrderId = $xxml->VPosMessage->MerchantOrderId;
    $Amount = $xxml->VPosMessage->Amount;
    $MD = $xxml->MD;
    $taksit_sayisi = "0";
    $CustomerId = "93981545";
    $MerchantId = "61899";
    $UserName="kadirbabur";
    $Password="Dell28736.!";
    $HashedPassword = base64_encode(sha1($Password,"ISO-8859-9"));
    $HashData = base64_encode(sha1($MerchantId.$MerchantOrderId.$Amount.$UserName.$HashedPassword , "ISO-8859-9"));

    $xml='<KuveytTurkVPosMessage xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
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
        //curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_MAX_TLSv1_2); // alternatif
        //curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_0 | CURL_SSLVERSION_TLSv1_1 | CURL_SSLVERSION_TLSv1_2); // php 5.5.19+ destekler
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml', 'Content-length: '. strlen($xml)) );
        curl_setopt($ch, CURLOPT_POST, true); //POST Metodu kullanarak verileri g�nder
        curl_setopt($ch, CURLOPT_HEADER, false); //Serverdan gelen Header bilgilerini �nemseme.
        curl_setopt($ch, CURLOPT_URL,'https://sanalpos.kuveytturk.com.tr/ServiceGateWay/Home/ThreeDModelProvisionGate'); //Baglanacagi URL
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Transfer sonu�larini al.
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

                    $doviz_kur = $db->fetch("SELECT * FROM b2b_kurlar WHERE id = 2");
                    $dov_al = str_replace('.', ',', $doviz_kur["alis"]);
                    $dov_sat = str_replace('.', ',', $doviz_kur["satis"]);

                    $currentDateTime = date("d.m.Y H:i:s");
                    $degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));

                    posXmlOlustur($uyecarikod, $hesap, $degistirme_tarihi,$degistirme_tarihi,$yantoplam,'',$dov_al,$dov_sat,$siparisNumarasi,$blbnhskodu,$banka_adi,$taksit_sayisi, 'TL',$banka_tanimi);

                    // Get delivery address
                    $teslimat = $db->fetch("SELECT * FROM b2b_adresler WHERE uye_id = :uye_id AND aktif = '1'", ['uye_id' => $uye_id]);
                    $teslimat_ad = $teslimat['ad'];
                    $teslimat_soyad = $teslimat['soyad'];
                    $teslimat_firmaadi = $teslimat['firma_adi'];
                    $teslimat_adres = $teslimat['adres'];
                    $teslimat_telefon = $teslimat['telefon'];
                    $teslimat_ulke = $teslimat['ulke'];
                    $teslimat_il = $teslimat['il'];
                    $teslimat_ilce = $teslimat['ilce'];
                    $teslimat_tcno = $teslimat['tc_no'];
                    $teslimat_vergino = $teslimat['vergi_no'];
                    $teslimat_vergidairesi = $teslimat['vergi_dairesi'];
                    $teslimat_postakodu = $teslimat['posta_kodu'];

                    // Get user billing details
                    $uye = $db->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $uye_id]);
                    $uyecarikod = $uye['BLKODU'];
                    $uye_gor_fiyat = $uye['fiyat'];
                    $uye_ad = $uye['ad'];
                    $uye_soyad = $uye['soyad'];
                    $uyeAdSoyad = $uye_ad . ' ' . $uye_soyad;
                    $uye_email = $uye['email'];
                    $uye_tel = $uye['tel'];
                    $uye_ulke = $uye['ulke'];
                    $uye_adres = $uye['adres'];
                    $uye_postakodu = $uye['posta_kodu'];
                    $uye_tcno = $uye['tc_no'];
                    $uye_firmaadi = $uye['firmaUnvani'];
                    $uye_vergidairesi = $uye['vergi_dairesi'];
                    $uye_vergino = $uye['vergi_no'];
                    $uye_il = $uye['il'];
                    $uye_ilce = $uye['ilce'];
                    $uye_muhasebekodu = $uye['muhasebe_kodu'];

                    // Insert order into database
                    $success = $db->insert("INSERT INTO b2b_siparisler 
                        (siparis_no, uye_id, durum, odeme_sekli, teslimat_ad, teslimat_soyad, teslimat_firmaadi, teslimat_adres, 
                        teslimat_telefon, teslimat_ulke, teslimat_il, teslimat_ilce, teslimat_tcno, teslimat_vergino, 
                        teslimat_vergidairesi, teslimat_postakodu, uye_ad, uye_soyad, uye_email, uye_tel, uye_ulke, uye_adres, 
                        uye_postakodu, uye_tcno, uye_firmaadi, uye_vergidairesi, uye_vergino, uye_il, uye_ilce, uye_muhasebekodu, 
                        sepet_toplami, sepet_kdv, indirim, kargo_ucreti, kargo_firmasi, toplam, desi, tarih) 
                        VALUES (:siparisNumarasi, :uye_id, '1', :tip, :teslimat_ad, :teslimat_soyad, :teslimat_firmaadi, :teslimat_adres,
                        :teslimat_telefon, :teslimat_ulke, :teslimat_il, :teslimat_ilce, :teslimat_tcno, :teslimat_vergino,
                        :teslimat_vergidairesi, :teslimat_postakodu, :uye_ad, :uye_soyad, :uye_email, :uye_tel, :uye_ulke, :uye_adres,
                        :uye_postakodu, :uye_tcno, :uye_firmaadi, :uye_vergidairesi, :uye_vergino, :uye_il, :uye_ilce, :uye_muhasebekodu,
                        :yanSepetToplami, :yanSepetKdv, :yanIndirim, :yanKargo, :deliveryOption, :yantoplam, :desi, NOW())", 
                        ['siparisNumarasi' => $siparisNumarasi,'uye_id' => $uye_id,'tip' => $tip,'teslimat_ad' => $teslimat_ad,'teslimat_soyad' => $teslimat_soyad,'teslimat_firmaadi' => $teslimat_firmaadi,
                        'teslimat_adres' => $teslimat_adres,'teslimat_telefon' => $teslimat_telefon,'teslimat_ulke' => $teslimat_ulke,'teslimat_il' => $teslimat_il,
                        'teslimat_ilce' => $teslimat_ilce,'teslimat_tcno' => $teslimat_tcno,'teslimat_vergino' => $teslimat_vergino,'teslimat_vergidairesi' => $teslimat_vergidairesi,
                        'teslimat_postakodu' => $teslimat_postakodu,'uye_ad' => $uye_ad,'uye_soyad' => $uye_soyad,'uye_email' => $uye_email,'uye_tel' => $uye_tel,'uye_ulke' => $uye_ulke,
                        'uye_adres' => $uye_adres,'uye_postakodu' => $uye_postakodu,'uye_tcno' => $uye_tcno,'uye_firmaadi' => $uye_firmaadi,'uye_vergidairesi' => $uye_vergidairesi,
                        'uye_vergino' => $uye_vergino,'uye_il' => $uye_il,'uye_ilce' => $uye_ilce,'uye_muhasebekodu' => $uye_muhasebekodu,'yanSepetToplami' => $yanSepetToplami,
                        'yanSepetKdv' => $yanSepetKdv,'yanIndirim' => $yanIndirim,'yanKargo' => $yanKargo,'deliveryOption' => $deliveryOption,'yantoplam' => $yantoplam,'desi' => $desi]);

                    if ($success) {
                        $siparisId = $db->lastInsertId();

                        // Get cart items
                        $urunler = $db->fetchAll("SELECT * FROM b2b_uye_sepet WHERE uye_id = :uye_id", ['uye_id' => $uye_id]);

                        foreach ($urunler as $row) {
                            $urun_id = $row['urun_id'];
                            $miktar = $row['adet'];
                            
                            $urun = $db->fetch("SELECT * FROM nokta_urunler WHERE id = :urun_id", ['urun_id' => $urun_id]);
                            
                            $urun_blkodu = $urun["BLKODU"];
                            $uyenin_fiyati = ($urun["DSF".$uye_gor_fiyat] == NULL || $urun["DSF".$uye_gor_fiyati] == '') 
                                ? $urun["KSF".$uye_gor_fiyat] : $urun["DSF".$uye_gor_fiyat];
                            $uyenin_fiyati = number_format((float)$uyenin_fiyati, 2, '.', '');

                            $doviz_satis_fiyati = ($urun['DOVIZ_BIRIMI'] == '$') ? $satis_dolar : $satis_euro;

                            // Insert order products
                            $success = $db->insert("INSERT INTO b2b_siparis_urunler 
                                (sip_id, urun_id, urun_blkodu, adet, birim_fiyat, toplam_fiyat, doviz_birimi, doviz_kuru) 
                                VALUES (:sip_id, :urun_id, :urun_blkodu, :adet, :birim_fiyat, :toplam_fiyat, :doviz_birimi, :doviz_kuru)", [
                                'sip_id' => $siparisId,'urun_id' => $urun_id,'urun_blkodu' => $urun_blkodu,'adet' => $miktar,
                                'birim_fiyat' => $uyenin_fiyati,'toplam_fiyat' => $uyenin_fiyati * $miktar,'doviz_birimi' => $urun['DOVIZ_BIRIMI'],'doviz_kuru' => $doviz_satis_fiyati]);
                            if (!$success) {
                                echo "Ürün eklerken hata oluştu: Veritabanı işlemi başarısız.";
                                error_log("Product insertion failed for user ID: " . $uye_id . " - Order number: " . $siparisNumarasi);
                                break;
                            }
                            // Delete from cart after successful order
                            $db->delete("DELETE FROM b2b_uye_sepet WHERE uye_id = :uye_id AND urun_id = :urun_id", ['uye_id' => $uye_id, 'urun_id' => $urun_id]);

                            // Ürünün cok_satan değerini kontrol et ve arttır
                            $cok_satan = $urun['cok_satan'];
                            if ($cok_satan === null || $cok_satan === '') { 
                                $cok_satan = 0;
                            }
                            $cok_satan++;

                            // cok_satan değerini güncelle
                            $db->update("UPDATE nokta_urunler SET cok_satan = :cok_satan WHERE id = :id", ['cok_satan' => $cok_satan,'id' => $urun_id]);
                        }
                        // Update promotion code usage if exists
                        if (!empty($promosyon_kodu)) {
                            $promosyon = $db->fetch("SELECT * FROM b2b_promosyon WHERE promosyon_kodu = :kod", ['kod' => $promosyon_kodu]);

                            if ($promosyon) {
                                $maxKullanim = $promosyon['max_kullanim'];
                                $promosyonKullanildi = $promosyon['kullanildi'];
                                $promosyon_kullanim_sayisi = $promosyon['kullanim_sayisi'];
                                $promosyon_kullanim_sayisi += 1;

                                if ($promosyonKullanildi == 1) {
                                    echo "Promosyon kullanildi";
                                    exit;
                                } elseif ($promosyon_kullanim_sayisi > $maxKullanim) {
                                    echo "Promosyon kullanim maksimumu geçti";
                                    exit;
                                } elseif ($promosyon_kullanim_sayisi == $maxKullanim) {
                                    $db->update("UPDATE b2b_promosyon SET kullanim_sayisi = :kullanim_sayisi, kullanildi = 1, uye_id = CONCAT(uye_id, :uye_id, ',') WHERE promosyon_kodu = :kod", 
                                                ['kullanim_sayisi' => $promosyon_kullanim_sayisi,'uye_id' => $uye_id,'kod' => $promosyon_kodu]);
                                } else {
                                    $db->update("UPDATE b2b_promosyon SET kullanim_sayisi = :kullanim_sayisi, uye_id = CONCAT(uye_id, :uye_id, ',') WHERE promosyon_kodu = :kod", 
                                                ['kullanim_sayisi' => $promosyon_kullanim_sayisi,'uye_id' => $uye_id,'kod' => $promosyon_kodu]);
                                }
                            }
                        }
                        
                            // Send order confirmation email
                        $mail_icerik = siparisAlindi($uyeAdSoyad, $siparisId, $siparisNumarasi);
                        mailGonder($uye_email, 'Siparişiniz Alınmıştır!', $mail_icerik, 'Nokta Elektronik');

                        // Record payment success
                        $pos_id = 3;
                        $basarili = 1;
                        $sonucStr = "Sipariş ödeme işlemi başarılı: " . $xml->ResponseMessage . ' Kod= ' . $xml->ResponseCode;
                        
                        $db->insert("INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, islem_turu, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :islem_turu, :tutar, :basarili)", 
                            ['uye_id' => $uye_id,'pos_id' => $pos_id,'islem' => $sonucStr,'islem_turu' => $siparisOdeme,'tutar' => $yantoplam1,'basarili' => $basarili]);
                        header("Location: ../../tr/onay?siparis-numarasi=$siparisNumarasi");
                    } else {
                        echo "Sipariş oluşturma hatası: Veritabanı işlemi başarısız.";
                        error_log("Order creation failed for user ID: " . $uye_id . " - Order number: " . $siparisNumarasi);
                    }
                } else {
                    $pos_id = 3;
                    $basarili = 0;
                    $sonucStr = "Sipariş ödeme işlemi başarısız: " . $xml->ResponseMessage . ' Kod= ' . $xml->ResponseCode;
                    $db->insert("INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, islem_turu, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :islem_turu, :tutar, :basarili)", 
                                ['uye_id' => $uye_id,'pos_id' => $pos_id,'islem' => $sonucStr,'islem_turu' => $siparisOdeme,'tutar' => $yantoplam1,'basarili' => $basarili]);
                    header("Location: ../../tr/sepet?code=".$xml->ResponseCode."&message=".$xml->ResponseMessage);
                }
        curl_close($ch);
    }
    catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}
?>