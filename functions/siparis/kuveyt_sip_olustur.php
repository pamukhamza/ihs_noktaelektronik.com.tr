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
    $promosyon_kodu = $_POST["promosyonKodu"] ?? '';
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml', 'Content-length: '. strlen($xml)));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL,'https://sanalpos.kuveytturk.com.tr/ServiceGateWay/Home/ThreeDModelProvisionGate');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $xml = simplexml_load_string($data);

        if ($xml->ResponseCode == "00") {
            $banka = $db->fetch("SELECT * FROM b2b_banka_taksit_eslesme WHERE id = :banka_id ", ['banka_id' => $banka_id]);
            $ticariProgram = $banka["ticari_program"];

            $banka_pos = $db->fetch("SELECT * FROM b2b_banka_pos_listesi WHERE id = :ticariProgram ", ['ticariProgram' => $ticariProgram]);
            $blbnhskodu = $banka_pos["BLBNHSKODU"];
            $banka_adi = $banka_pos["BANKA_ADI"];
            $banka_tanimi = $banka_pos["TANIMI"];

            $uye = $db->fetch("SELECT * FROM uyeler WHERE id = :uye_id " , ['uye_id' => $uye_id]);
            $uyecarikod = $uye['BLKODU'];

            $doviz_kur = $db->fetch("SELECT * FROM b2b_kurlar WHERE id = 2");
            $dov_al = str_replace('.', ',', $doviz_kur["alis"]);
            $dov_sat = str_replace('.', ',', $doviz_kur["satis"]);

            $currentDateTime = date("d.m.Y H:i:s");
            $degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));

            posXmlOlustur($uyecarikod, $hesap, $degistirme_tarihi,$degistirme_tarihi,$yantoplam,'',$dov_al,$dov_sat,$siparisNumarasi,$blbnhskodu,$banka_adi,$taksit_sayisi, 'TL', $banka_tanimi);

            //Adresler tablosundan adresi çek
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

            $uyecarikod = $uye['BLKODU'];
            $uye_gor_fiyat = $uye['fiyat'];
            $uye_ad = $uye['ad'];
            $uye_soyad = $uye['soyad'];
            $uyeAdSoyad = $uye_ad. ' ' . $uye_soyad;
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
            // Sipariş tablosuna verileri ekle
            $siparisEkleQuery = "INSERT INTO b2b_siparisler
                (siparis_no, uye_id, durum, odeme_sekli, teslimat_ad, teslimat_soyad, teslimat_firmaadi, teslimat_adres, teslimat_telefon, teslimat_ulke, teslimat_il, teslimat_ilce, teslimat_tcno,
                teslimat_vergino, teslimat_vergidairesi, teslimat_postakodu, uye_ad, uye_soyad, uye_email, uye_tel, uye_ulke, uye_adres, uye_postakodu, uye_tcno, uye_firmaadi, uye_vergidairesi,
                uye_vergino, uye_il, uye_ilce, uye_muhasebekodu, sepet_toplami, sepet_kdv, indirim, kargo_ucreti, kargo_firmasi, toplam, desi, tarih) VALUES
                (:siparisNumarasi, :uye_id, '1', :tip, :teslimat_ad, :teslimat_soyad, :teslimat_firmaadi, :teslimat_adres, :teslimat_telefon, :teslimat_ulke, :teslimat_il, :teslimat_ilce, :teslimat_tcno,
                :teslimat_vergino, :teslimat_vergidairesi, :teslimat_postakodu, :uye_ad, :uye_soyad, :uye_email, :uye_tel, :uye_ulke, :uye_adres, :uye_postakodu, :uye_tcno, :uye_firmaadi, :uye_vergidairesi,
                :uye_vergino, :uye_il, :uye_ilce, :uye_muhasebekodu, :yanSepetToplami, :yanSepetKdv, :yanIndirim, :yanKargo, :deliveryOption, :yantoplam, :desi, NOW() + INTERVAL 3 HOUR)";
            $sippar = ['siparisNumarasi'=> $siparisNumarasi,'uye_id'=> $uye_id,'tip'=> $tip,'teslimat_ad'=> $teslimat_ad,'teslimat_soyad'=> $teslimat_soyad,'teslimat_firmaadi'=> $teslimat_firmaadi,
            'teslimat_adres'=> $teslimat_adres,'teslimat_telefon'=> $teslimat_telefon,'teslimat_ulke'=> $teslimat_ulke,'teslimat_il'=> $teslimat_il,'teslimat_ilce'=> $teslimat_ilce,
            'teslimat_tcno'=> $teslimat_tcno,'teslimat_vergino'=> $teslimat_vergino,'teslimat_vergidairesi'=> $teslimat_vergidairesi,'teslimat_postakodu'=> $teslimat_postakodu,'uye_ad'=> $uye_ad,
            'uye_soyad'=> $uye_soyad,'uye_email'=> $uye_email,'uye_tel'=> $uye_tel,'uye_ulke'=> $uye_ulke,'uye_adres'=> $uye_adres,'uye_postakodu'=> $uye_postakodu,'uye_tcno'=> $uye_tcno,
            'uye_firmaadi'=> $uye_firmaadi,'uye_vergidairesi'=> $uye_vergidairesi,'uye_vergino'=> $uye_vergino,'uye_il'=> $uye_il,'uye_ilce'=> $uye_ilce,'uye_muhasebekodu'=> $uye_muhasebekodu,
            'yanSepetToplami'=> $yanSepetToplami,'yanSepetKdv'=> $yanSepetKdv,'yanIndirim'=> $yanIndirim,'yanKargo'=> $yanKargo,'deliveryOption'=> $deliveryOption,'yantoplam'=> $yantoplam,'desi'=> $desi];
            $siparisEkleStatement = $db->insert($siparisEkleQuery, $sippar);
            if ($siparisEkleStatement) {
                $siparisId = $db->lastInsertId(); // Eklenen siparişin ID'sini al

                // Üye sepetinden ürünleri al
                $uyeSepetUrunleriStatement = $db->fetchAll("SELECT * FROM uye_sepet WHERE uye_id = :uye_id" , ['uye_id' => $uye_id]);
                if ($uyeSepetUrunleriStatement) {
                    foreach($uyeSepetUrunleriStatement as $row){
                        $urun_id = $row['urun_id'];
                        $miktar = $row['adet'];
                        $ozel_fiyat = $row['ozel_fiyat'];

                        $urun = $db->fetch("SELECT * FROM nokta_urunler WHERE id = :urun_id", ['urun_id' => $urun_id]);
                        $urun_blkodu = $urun["BLKODU"];

                        if(empty($ozel_fiyat)){
                            $uyenin_fiyati = $urun["DSF" . $uye_gor_fiyat];
                            $uyenin_fiyati = number_format((float)$uyenin_fiyati, 2, '.', '');
                            if ($urun["DSF" . $uye_gor_fiyat] == NULL || $urun["DSF" . $uye_gor_fiyat] = '') {
                                $uyenin_fiyati = $urun["KSF" . $uye_gor_fiyat];
                                $uyenin_fiyati = number_format((float)$uyenin_fiyati, 2, '.', '');
                            }
                        }else{
                            $uyenin_fiyati = $ozel_fiyat;
                            $uyenin_fiyati = number_format((float)$uyenin_fiyati, 2, '.', '');
                        }

                        if ($urun['DOVIZ_BIRIMI'] == '$') {
                            $doviz_satis_fiyati = $satis_dolar;
                        } elseif ($urun['DOVIZ_BIRIMI'] == '€') {
                            $doviz_satis_fiyati = $satis_euro;
                        }

                        // Ürünün cok_satan değerini kontrol et ve arttır
                        $cok_satan = $urun['cok_satan'];
                        if ($cok_satan === null || $cok_satan === '') {
                            $cok_satan = 0;
                        }
                        $cok_satan++;
                        // cok_satan değerini güncelle
                        $db->update("UPDATE nokta_urunler SET cok_satan = :cok_satan WHERE id = :id" , ['cok_satan' => $cok_satan, 'id' => $urun_id]);

                        $querysipur = "INSERT INTO b2b_siparis_urunler (sip_id, urun_id, adet, BLKODU, birim_fiyat, dolar_satis) VALUES (:siparisId, :urun_id, :miktar, :urun_blkodu, :uyenin_fiyati, :doviz_satis_fiyati)";
                        $siparisUrunEkleStatement = $db->insert($querysipur, ['siparisId'=> $siparisId, 'urun_id'=> $urun_id, 'miktar'=> $miktar, 'urun_blkodu'=> $urun_blkodu, 
                                                                    'uyenin_fiyati'=> $uyenin_fiyati, 'doviz_satis_fiyati'=> $doviz_satis_fiyati]);

                        if (!$siparisUrunEkleStatement) {
                            echo "Ürün eklerken hata oluştu: ";
                            break; // Hata durumunda döngüyü sonlandırabilirsiniz
                        }
                    }
                    // Üye sepetindeki ürünleri sildiğinizden emin olun (bu adımı dikkatlice kullanın)
                    $uyeSepetSilStatement = $db->delete("DELETE FROM uye_sepet WHERE uye_id = :uye_id" , ['uye_id' => $uye_id]);

                    if (!$uyeSepetSilStatement) {
                        echo "Üye sepetini temizlerken hata oluştu: ";
                    }
                } else {
                    echo "Üye sepeti sorgulama hatası: ";
                }
            } else {
                echo "Sipariş oluşturma hatası: ";
            }

            // Create a new XML document
            $xmlDoc = new DOMDocument('1.0', 'UTF-8');
            $xmlDoc->formatOutput = true;
            $root = $xmlDoc->createElement('WFT');
            $xmlDoc->appendChild($root);
            // AYAR ALANI BASLANGIC
            $ayar = $xmlDoc->createElement('AYAR');
            $root->appendChild($ayar);
            $trsver = $xmlDoc->createElement('TRSVER');
            $trsver->appendChild($xmlDoc->createCDATASection('ASWFT1.02.03'));
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
            $fatura = $xmlDoc->createElement('FATURA');
            $root->appendChild($fatura);
            $elements = [
                'FATURA_DURUMU' => '1',
                'BLCRKODU' => $uyecarikod,
                'DEGISTIRME_TARIHI' => $degistirme_tarihi,
                'KDV_DURUMU' => '0',
                'KPBDVZ_CARI' => '1',
                'ISK_KUL_CARI' => '0',
                'ISK_KUL_1' => '0',
                'ISK_KUL_STOK' => '0',
                'ISK_KUL_OZEL' => '0',
                'ISK_KUL_ALT' => '0',
                'ISK_KUL_CARI' => '0',
                'ISK_ORAN_CARI' => '0',
                'ISK_ORAN_1' => '5',
                'ISK_TUTAR_CARI' => '0,00',
                'ISK_TUTAR_1' => '0,00',
                'ISK_TUTAR_STOK' => '0,00',
                'ISK_TUTAR_OZEL' => '0,00',
                'DOVIZ_KULLAN' => '0',
                'DVZ_HSISLE_CARI' => '0',
                'DVZ_HSISLE_STOK' => '0',
                'IPTAL' => '0',
                'ACIKLAMA' => $siparisNumarasi . ' numaralı internet siparişine ait faturadır.',
                'PAZ_DURUMU' => '0',
                'PAZ_PERS_BLKODU' => '0',
                'PAZ_PERSONEL' => '',
                'PAZ_URUN_ORANI' => '0',
                'PAZ_URUN_TUTARI' => '0',
                'PAZ_ISC_ORANI' => '0',
                'PAZ_ISC_TUTARI' => '0'
            ];
            foreach ($elements as $elementName => $elementValue) {
                $element = $xmlDoc->createElement($elementName);
                $element->appendChild($xmlDoc->createCDATASection($elementValue));
                $fatura->appendChild($element);
            }
            // CARI BILGI ALANI SON
            // URUNLER ALANI BASLANGICI
            $faturaHareket = $xmlDoc->createElement('FATURAHAREKET');
            $root->appendChild($faturaHareket);
            
            $uyeSiparisUrunleriStatement = $db->fetchAll("SELECT * FROM b2b_siparis_urunler WHERE sip_id = :siparisId", ['siparisId' => $siparisId]);
            foreach($uyeSiparisUrunleriStatement as $row){
                $urun_id = $row['urun_id'];
                $urun_adet = $row['adet'];
                $birim_fiyat = $row['birim_fiyat'];
    
                $noktaurun = $db->fetch("SELECT * FROM nokta_urunler WHERE id = :urun_id", ['urun_id' => $urun_id]);
                $dovizimiz = '';
    
                if($noktaurun['DSF' . $uye_gor_fiyat] == NULL || $noktaurun['DSF' . $uye_gor_fiyat] == ''){
                    $dovizimiz = 1;
                    $gonderFiyat = $birim_fiyat;
                }else{
                    if ($noktaurun['DOVIZ_BIRIMI'] == '$') {
                        $dovizimiz = $satis_dolar;
                        $aliskuru = $alis_dolar;
                        $satiskuru = $satis_dolar;
                    } elseif ($noktaurun['DOVIZ_BIRIMI'] == '€') {
                        $dovizimiz = $satis_euro;
                        $aliskuru = $alis_euro;
                        $satiskuru = $satis_euro;
                    }
                    $gonderFiyat = $birim_fiyat;
                }
                $tlFiyat = str_replace(',', '.', $gonderFiyat);
                $tlFiyat = floatval($tlFiyat); // Convert to float
    
                $fiyati = $tlFiyat * floatval($dovizimiz); // Convert $dovizimiz to float as well
                $birim_fiyat_tl = str_replace('.', ',', $fiyati);
                $formatted_UYGL_ISK_FIYATI = '';
                if (!empty($yanIndirim) && $yanIndirim != 0) {
                    $ISK_KDVSZ_TTR = 5 * $yanIndirim / 6; //kdvsiz iskonto tutar
                    $ISK_SKNT_TPL = $fiyati * $urun_adet * 1.20; //ürün için satır toplamı iskontoda kullanıcak
    
                    if (!empty($yanKargo) && $yanKargo != 0) {
                        $spt_yn_tpl_kdvli = $yanIndirim + $yantoplamxml - $yanKargo;
                    } else {
                        $spt_yn_tpl_kdvli = $yanIndirim + $yantoplamxml;
                    }
                    $UYGL_ISK_FIYATI = $ISK_KDVSZ_TTR * ($ISK_SKNT_TPL / $spt_yn_tpl_kdvli);
                    $formatted_UYGL_ISK_FIYATI = number_format($UYGL_ISK_FIYATI, 4, ',', '');
                }
                $hareket = $xmlDoc->createElement('HAREKET');
                $faturaHareket->appendChild($hareket);
                $elements = [
                    'BLSTKODU' => $noktaurun['BLKODU'],
                    'MIKTARI_2' => $urun_adet,
                    'BIRIMI_2' => $noktaurun['BIRIMI'],
                    'MIKTARI' => $urun_adet,
                    'BIRIMI' => $noktaurun['BIRIMI'],
                    'KDV_ORANI' => $noktaurun['kdv'],
                    'KPBDVZ' => $noktaurun['DOVIZ_KULLAN'],
                    'DVZ_FIYATI' => $gonderFiyat,
                    'ISK_OZEL' => $formatted_UYGL_ISK_FIYATI,
                    'KPB_FIYATI' => $birim_fiyat_tl,
                    'DEPO_ADI' => 'PERPA M01',
                    'DOVIZ_ALIS' => $aliskuru,
                    'DOVIZ_SATIS' => $satiskuru,
                    'ISK_ORAN_1' => '0',
                    'OZEL_KODU' => '',
                    'EKBILGI_1' => '',
                    'PAZ_PERS_BLKODU' => '',
                    'PAZ_PERSONEL' => '',
                    'PAZ_URUN_ORANI' => '',
                    'PAZ_URUN_TUTARI' => '',
                    'PAZ_ISC_ORANI' => '',
                    'PAZ_ISC_TUTARI' => '',
                ];
                foreach ($elements as $elementName => $elementValue) {
                    $element = $xmlDoc->createElement($elementName);
                    $element->appendChild($xmlDoc->createCDATASection($elementValue));
                    $hareket->appendChild($element);
                }
            } 
            if ($yanKargo != '0' && $yanKargo != '0,00') {
                $hareket1 = $xmlDoc->createElement('HAREKET');
                $faturaHareket->appendChild($hareket1);
                $elements = [
                    'BLSTKODU' => '-1',
                    'STOK_ADI' => 'Kargo Gönderim Ücreti',
                    'MIKTARI_2' => '1',
                    'BIRIMI_2' => 'ADET',
                    'MIKTARI' => '1',
                    'BIRIMI' => 'ADET',
                    'KDV_ORANI' => '20',
                    'MUH_KODU_GENEL' => '770 03 22',
                    'KPB_FIYATI' => str_replace('.', ',', $yanKargo)
                ];
            
                foreach ($elements as $tag => $value) {
                    $elem = $xmlDoc->createElement($tag);
                    $elem->appendChild($xmlDoc->createCDATASection($value));
                    $hareket1->appendChild($elem);
                }
            }
            // URUNLER ALANI SONU
            // DOVIZ ALANI BASLANGICI
            $currencies = [
                ['symbol' => '$', 'alis' => $alis_dolar, 'satis' => $satis_dolar],
                ['symbol' => '€', 'alis' => $alis_euro, 'satis' => $satis_euro]
            ];
            $faturaKur = $xmlDoc->createElement('FATURAKUR');
            $root->appendChild($faturaKur);
            foreach ($currencies as $currency) {
                $kurhareket = $xmlDoc->createElement('HAREKET');
                $faturaKur->appendChild($kurhareket);
                $doviz_birimi = $xmlDoc->createElement('DOVIZ_BIRIMI');
                $doviz_birimi->appendChild($xmlDoc->createCDATASection($currency['symbol']));
                $kurhareket->appendChild($doviz_birimi);
                $doviz_alis = $xmlDoc->createElement('DOVIZ_ALIS');
                $doviz_alis->appendChild($xmlDoc->createCDATASection($currency['alis']));
                $kurhareket->appendChild($doviz_alis);
                $doviz_satis = $xmlDoc->createElement('DOVIZ_SATIS');
                $doviz_satis->appendChild($xmlDoc->createCDATASection($currency['satis']));
                $kurhareket->appendChild($doviz_satis);
            }
            // DOVIZ ALANI SONU
            $xmlFileName = 'fatura_' . $siparisNumarasi . '.xml';
            $xmlDoc->save('../../assets/faturalar/' . $xmlFileName);

            function updateUyeId($db, $promosyon_kodu, $uye_id, $promosyon_kullanim_sayisi, $kullanildi) {
                // uye_id sütununun boş olup olmadığını kontrol et
                $uyeIdResult = $db->fetch("SELECT uye_id FROM b2b_promosyon WHERE promosyon_kodu = :promosyon_kodu", ['promosyon_kodu' => $promosyon_kodu]);

                if ($uyeIdResult) {
                    if (empty($uyeIdResult['uye_id'])) { // uye_id boşsa, direkt $uye_id'yi yaz
                        $newUyeId = $uye_id;
                    } else { // uye_id doluysa, mevcut değere $uye_id'yi virgülle ekle
                        $newUyeId = $uyeIdResult['uye_id'] . ',' . $uye_id;
                    }

                    // Promosyonu güncelle
                    $db->update("UPDATE promosyon SET kullanim_sayisi = :kullanim_sayisi, kullanildi = :kullanildi, uye_id = :uye_id WHERE promosyon_kodu = :promosyon_kodu", 
                                ['kullanim_sayisi' => $promosyon_kullanim_sayisi, 'kullanildi' => $kullanildi, 'uye_id' => $newUyeId, 'promosyon_kodu' => $promosyon_kodu ]);
                }
            }

            if (!empty($promosyon_kodu)) {
                $promosyon = $db->fetch("SELECT * FROM b2b_promosyon WHERE promosyon_kodu = :promosyon_kodu", ['promosyon_kodu' => $promosyon_kodu]);
                $maxKullanim = $promosyon["max_kullanim_sayisi"];
                $promosyonKullanildi = $promosyon["kullanildi"];
                $promosyon_kullanim_sayisi = $promosyon["kullanim_sayisi"] ?? 0;

                $promosyon_kullanim_sayisi += 1;

                if ($promosyonKullanildi == 1) {
                    echo "Promosyon kullanildi";
                    exit;
                } elseif ($promosyon_kullanim_sayisi > $maxKullanim) {
                    echo "Promosyon kullanim maksimumu geçti";
                    exit;
                } elseif ($promosyon_kullanim_sayisi == $maxKullanim) {
                    updateUyeId($db, $promosyon_kodu, $uye_id, $promosyon_kullanim_sayisi, 1);
                } else {
                    updateUyeId($db, $promosyon_kodu, $uye_id, $promosyon_kullanim_sayisi, 0);
                }
            }

            header("Location: ../../tr/onay?siparis-numarasi=$siparisNumarasi");
            $pos_id = 4;
            $basarili = 1;
            $sonucStr = "Sipariş ödeme işlemi başarılı: " . $xmlResponse->Response . ' Kod= ' . $xmlResponse->ProcReturnCode;
            $stmt = "INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, islem_turu, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :islem_turu, :tutar, :basarili)";
            $db->insert($stmt, ['uye_id' => $uye_id, 'pos_id' => $pos_id, 'islem' => $sonucStr, 'islem_turu' => $siparisOdeme, 'tutar' => $yantoplam1, 'basarili' => $basarili]);
   
            $mail_icerik = siparisAlindi($uyeAdSoyad, $siparisId, $siparisNumarasi);
            mailGonder($uye_email, 'Siparişiniz Alınmıştır!', $mail_icerik, 'Nokta Elektronik');
      
        } else {
            $pos_id = 3;
            $basarili = 0;
            $sonucStr = "Sipariş ödeme işlemi başarısız: " . $xmlResponse->ErrMsg . ' Kod= ' . $xmlResponse->ProcReturnCode;
            $stmt = "INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, islem_turu, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :islem_turu, :tutar, :basarili)";
            $db->insert($stmt , ['uye_id' => $uye_id, 'pos_id' => $pos_id, 'islem' => $sonucStr, 'islem_turu' => $siparisOdeme, 'tutar' => $yantoplam1, 'basarili' => $basarili]);
            header("Location: ../../tr/sepet?code=".$xmlResponse->ProcReturnCode."&message=".$xmlResponse->ErrMsg);
         }
        curl_close($ch);
    }
    catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}

// Sipariş işleme fonksiyonları
function updateUyeId($db, $promosyon_kodu, $uye_id, $promosyon_kullanim_sayisi, $kullanildi) {
    $uyeIdResult = $db->fetch("SELECT uye_id FROM b2b_promosyon WHERE promosyon_kodu = :kod", ['kod' => $promosyon_kodu]);
    if ($uyeIdResult) {
        if (empty($uyeIdResult['uye_id'])) {
            $newUyeId = $uye_id;
        } else {
            $newUyeId = $uyeIdResult['uye_id'] . ',' . $uye_id;
        }
        $db->update("UPDATE b2b_promosyon SET kullanim_sayisi = :kullanim_sayisi, kullanildi = :kullanildi, uye_id = CONCAT(uye_id, :uye_id, ',') WHERE promosyon_kodu = :kod", 
                    ['kullanim_sayisi' => $promosyon_kullanim_sayisi, 'kullanildi' => $kullanildi, 'uye_id' => $uye_id, 'kod' => $promosyon_kodu]);
    }
}

function createOrderXML($xmlDoc, $uyecarikod, $siparisNumarasi, $degistirme_tarihi) {
    $root = $xmlDoc->createElement('WFT');
    $xmlDoc->appendChild($root);
    
    // AYAR ALANI
    $ayar = $xmlDoc->createElement('AYAR');
    $root->appendChild($ayar);
    $elements = [
        'TRSVER' => 'ASWFT1.02.03',
        'DBNAME' => 'WOLVOX',
        'PERSUSER' => 'sa',
        'SUBE_KODU' => '3402'
    ];
    foreach ($elements as $key => $value) {
        $element = $xmlDoc->createElement($key);
        $element->appendChild($xmlDoc->createCDATASection($value));
        $ayar->appendChild($element);
    }
    
    // FATURA ALANI
    $fatura = $xmlDoc->createElement('FATURA');
    $root->appendChild($fatura);
    $elements = [
        'FATURA_DURUMU' => '1',
        'BLCRKODU' => $uyecarikod,
        'KDV_DURUMU' => '0',
        'KPBDVZ_CARI' => '1',
        'DEGISTIRME_TARIHI' => $degistirme_tarihi,
        'ISK_KUL_CARI' => '0',
        'ISK_KUL_1' => '0',
        'ISK_KUL_STOK' => '0',
        'ISK_KUL_OZEL' => '1',
        'ISK_KUL_ALT' => '0',
        'ISK_ORAN_CARI' => '0',
        'ISK_ORAN_1' => '5',
        'ISK_TUTAR_CARI' => '0,00',
        'ISK_TUTAR_1' => '0,00',
        'ISK_TUTAR_STOK' => '0,00',
        'ISK_TUTAR_OZEL' => '100,00',
        'DOVIZ_KULLAN' => '0',
        'DVZ_HSISLE_CARI' => '0',
        'DVZ_HSISLE_STOK' => '0',
        'IPTAL' => '0',
        'ACIKLAMA' => $siparisNumarasi . ' numaralı internet siparişine ait faturadır.',
        'PAZ_DURUMU' => '0',
        'PAZ_PERS_BLKODU' => '0',
        'PAZ_PERSONEL' => '',
        'PAZ_URUN_ORANI' => '0',
        'PAZ_URUN_TUTARI' => '0',
        'PAZ_ISC_ORANI' => '0',
        'PAZ_ISC_TUTARI' => '0'
    ];
    foreach ($elements as $key => $value) {
        $element = $xmlDoc->createElement($key);
        $element->appendChild($xmlDoc->createCDATASection($value));
        $fatura->appendChild($element);
    }
    
    return $root;
}

function addProductsToXML($xmlDoc, $root, $db, $siparisId, $uye_gor_fiyat, $yanIndirim, $yanKargo, $yantoplam, $satis_dolar, $satis_euro, $alis_dolar, $alis_euro) {
    $faturaHareket = $xmlDoc->createElement('FATURAHAREKET');
    $root->appendChild($faturaHareket);
    
    $uyeSiparisUrunleri = $db->fetchAll("SELECT * FROM b2b_siparis_urunler WHERE sip_id = :sip_id", ['sip_id' => $siparisId]);
    foreach ($uyeSiparisUrunleri as $row) {
        $urun_id = $row['urun_id'];
        $urun_adet = $row['adet'];
        $birim_fiyat = $row['birim_fiyat'];
        
        $noktaurun = $db->fetch("SELECT * FROM nokta_urunler WHERE id = :urun_id", ['urun_id' => $urun_id]);
        
        // Döviz hesaplamaları
        if($noktaurun['DSF' . $uye_gor_fiyat] == NULL || $noktaurun['DSF' . $uye_gor_fiyat] == '') {
            $dovizimiz = 1;
            $gonderFiyat = $birim_fiyat;
            $aliskuru = '';
            $satiskuru = '';
        } else {
            if ($noktaurun['DOVIZ_BIRIMI'] == '$') {
                $dovizimiz = $satis_dolar;
                $aliskuru = $alis_dolar;
                $satiskuru = $satis_dolar;
            } elseif ($noktaurun['DOVIZ_BIRIMI'] == '€') {
                $dovizimiz = $satis_euro;
                $aliskuru = $alis_euro;
                $satiskuru = $satis_euro;
            }
            $gonderFiyat = $birim_fiyat;
        }
        
        $tlFiyat = str_replace(',', '.', $gonderFiyat);
        $tlFiyat = floatval($tlFiyat);
        $fiyati = $tlFiyat * floatval($dovizimiz);
        $birim_fiyat_tl = str_replace('.', ',', $fiyati);
        
        // İskonto hesaplaması
        $formatted_UYGL_ISK_FIYATI = '';
        if (!empty($yanIndirim) && $yanIndirim != 0) {
            $ISK_KDVSZ_TTR = 5 * $yanIndirim / 6;
            $ISK_SKNT_TPL = $fiyati * $urun_adet * 1.20;
            
            $spt_yn_tpl_kdvli = !empty($yanKargo) && $yanKargo != 0 
                ? $yanIndirim + $yantoplam - $yanKargo 
                : $yanIndirim + $yantoplam;
                
            $UYGL_ISK_FIYATI = $ISK_KDVSZ_TTR * ($ISK_SKNT_TPL / $spt_yn_tpl_kdvli);
            $formatted_UYGL_ISK_FIYATI = number_format($UYGL_ISK_FIYATI, 4, ',', '');
        }
        
        // Ürün hareketini XML'e ekle
        $hareket = $xmlDoc->createElement('HAREKET');
        $faturaHareket->appendChild($hareket);
        
        $elements = [
            'BLSTKODU' => $noktaurun['BLKODU'],
            'MIKTARI_2' => $urun_adet,
            'BIRIMI_2' => $noktaurun['BIRIMI'],
            'MIKTARI' => $urun_adet,
            'BIRIMI' => $noktaurun['BIRIMI'],
            'KDV_ORANI' => $noktaurun['kdv'],
            'KPBDVZ' => $noktaurun['DOVIZ_KULLAN'],
            'DVZ_FIYATI' => $gonderFiyat,
            'ISK_OZEL' => $formatted_UYGL_ISK_FIYATI,
            'KPB_FIYATI' => $birim_fiyat_tl,
            'DEPO_ADI' => 'PERPA M01'
        ];
        
        if (!empty($aliskuru) && !empty($satiskuru)) {
            $elements['DOVIZ_ALIS'] = $aliskuru;
            $elements['DOVIZ_SATIS'] = $satiskuru;
        }
        
        foreach ($elements as $key => $value) {
            $element = $xmlDoc->createElement($key);
            $element->appendChild($xmlDoc->createCDATASection($value));
            $hareket->appendChild($element);
        }
    }
    
    // Kargo ücreti ekleme
    if ($yanKargo != '0' && $yanKargo != '0,00') {
        $hareket = $xmlDoc->createElement('HAREKET');
        $faturaHareket->appendChild($hareket);
        
        $elements = [
            'BLSTKODU' => '-1',
            'STOK_ADI' => 'Kargo Gönderim Ücreti',
            'MIKTARI_2' => '1',
            'BIRIMI_2' => 'ADET',
            'MIKTARI' => '1',
            'BIRIMI' => 'ADET',
            'KDV_ORANI' => '20',
            'MUH_KODU_GENEL' => '770 03 22',
            'KPB_FIYATI' => str_replace('.', ',', $yanKargo)
        ];
        
        foreach ($elements as $key => $value) {
            $element = $xmlDoc->createElement($key);
            $element->appendChild($xmlDoc->createCDATASection($value));
            $hareket->appendChild($element);
        }
    }
}

function addCurrencyToXML($xmlDoc, $root, $alis_dolar, $satis_dolar, $alis_euro, $satis_euro) {
    $faturaKur = $xmlDoc->createElement('FATURAKUR');
    $root->appendChild($faturaKur);
    
    $currencies = [
        ['symbol' => '$', 'alis' => $alis_dolar, 'satis' => $satis_dolar],
        ['symbol' => '€', 'alis' => $alis_euro, 'satis' => $satis_euro]
    ];
    
    foreach ($currencies as $currency) {
        $kurhareket = $xmlDoc->createElement('HAREKET');
        $faturaKur->appendChild($kurhareket);
        
        $elements = [
            'DOVIZ_BIRIMI' => $currency['symbol'],
            'DOVIZ_ALIS' => $currency['alis'],
            'DOVIZ_SATIS' => $currency['satis']
        ];
        
        foreach ($elements as $key => $value) {
            $element = $xmlDoc->createElement($key);
            $element->appendChild($xmlDoc->createCDATASection($value));
            $kurhareket->appendChild($element);
        }
    }
}
?>