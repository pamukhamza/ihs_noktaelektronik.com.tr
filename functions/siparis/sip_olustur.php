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
if (isset($_POST["tip"]) && $_POST["tip"] == 'Havale/EFT') {
    $yanSepetToplami    = $_POST["yanSepetToplami"];
    $yanSepetKdv        = $_POST["yanSepetKdv"];
    $yanIndirim         = $_POST["yanIndirim"];
    $yanKargo           = $_POST["yanKargo"];
    $yantoplam          = $_POST["yantoplam"];
    $desi               = $_POST["desi"];
    $promosyon_kodu     = $_POST["promosyonKodu"];
    $deliveryOption     = $_POST["deliveryOption"];
    $uye_id             = $_POST["uye_id"];
    $tip                = $_POST["tip"];
    $lang               = $_POST["lang"];
    $siparisNumarasi = generateUniqueOrderNumber();

    //Adresler tablosundan adresi çek
    $teslimat = $db->fetch("SELECT * FROM b2b_adresler WHERE uye_id = $uye_id AND aktif = '1'");
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

    //üyeler tablosundan fatura adresini çek
    $uye = $db->fetch("SELECT * FROM uyeler WHERE id = :uye_id", ['uye_id' => $uye_id]);
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

    // Sipariş tablosuna verileri ekle
    $siparisEkleQuery = "INSERT INTO b2b_siparisler 
    (siparis_no, uye_id, durum, odeme_sekli, teslimat_ad, teslimat_soyad, teslimat_firmaadi, teslimat_adres, teslimat_telefon, teslimat_ulke, teslimat_il, teslimat_ilce, teslimat_tcno, 
    teslimat_vergino, teslimat_vergidairesi, teslimat_postakodu, uye_ad, uye_soyad, uye_email, uye_tel, uye_ulke, uye_adres, uye_postakodu, uye_tcno, uye_firmaadi, uye_vergidairesi, 
    uye_vergino, uye_il, uye_ilce, uye_muhasebekodu, sepet_toplami, sepet_kdv, indirim, kargo_ucreti, kargo_firmasi, toplam, desi, tarih) 
    VALUES (:siparisNumarasi, :uye_id, '1', :tip, :teslimat_ad, :teslimat_soyad, :teslimat_firmaadi, :teslimat_adres, :teslimat_telefon, :teslimat_ulke, :teslimat_il, :teslimat_ilce, :teslimat_tcno, 
    :teslimat_vergino, :teslimat_vergidairesi, :teslimat_postakodu, :uye_ad, :uye_soyad, :uye_email, :uye_tel, :uye_ulke, :uye_adres, :uye_postakodu, :uye_tcno, :uye_firmaadi, :uye_vergidairesi, 
    :uye_vergino, :uye_il, :uye_ilce, :uye_muhasebekodu, :yanSepetToplami, :yanSepetKdv, :yanIndirim, :yanKargo, :deliveryOption, :yantoplam, :desi, NOW() + INTERVAL 3 HOUR)";

    $siparisEkleStmt = $db->insert($siparisEkleQuery, [
        'siparisNumarasi' => $siparisNumarasi, 'uye_id' => $uye_id, 'tip' => $tip, 'teslimat_ad' => $teslimat_ad, 'teslimat_soyad' => $teslimat_soyad,
        'teslimat_firmaadi' => $teslimat_firmaadi, 'teslimat_adres' => $teslimat_adres, 'teslimat_telefon' => $teslimat_telefon, 'teslimat_ulke' => $teslimat_ulke,
        'teslimat_il' => $teslimat_il, 'teslimat_ilce' => $teslimat_ilce, 'teslimat_tcno' => $teslimat_tcno, 'teslimat_vergino' => $teslimat_vergino,
        'teslimat_vergidairesi' => $teslimat_vergidairesi, 'teslimat_postakodu' => $teslimat_postakodu, 'uye_ad' => $uye_ad, 'uye_soyad' => $uye_soyad,
        'uye_email' => $uye_email, 'uye_tel' => $uye_tel, 'uye_ulke' => $uye_ulke, 'uye_adres' => $uye_adres, 'uye_postakodu' => $uye_postakodu,
        'uye_tcno' => $uye_tcno, 'uye_firmaadi' => $uye_firmaadi, 'uye_vergidairesi' => $uye_vergidairesi, 'uye_vergino' => $uye_vergino,
        'uye_il' => $uye_il, 'uye_ilce' => $uye_ilce, 'uye_muhasebekodu' => $uye_muhasebekodu, 'yanSepetToplami' => $yanSepetToplami,
        'yanSepetKdv' => $yanSepetKdv, 'yanIndirim' => $yanIndirim, 'yanKargo' => $yanKargo, 'deliveryOption' => $deliveryOption,
        'yantoplam' => $yantoplam, 'desi' => $desi
    ]);
    $siparisId = $db->lastInsertId();

    if ($siparisId) {
        $uyeSepetUrunleriQuery = "SELECT * FROM uye_sepet WHERE uye_id = :uye_id";
        $uyeSepetUrunleri = $db->fetchAll($uyeSepetUrunleriQuery, ['uye_id' => $uye_id]);
    
        if ($uyeSepetUrunleri) {
            foreach ($uyeSepetUrunleri as $row) {
                $urun_id = $row['urun_id'];
                $miktar = $row['adet'];
                $ozel_fiyat = $row['ozel_fiyat'];
    
                $urun = $db->fetch("SELECT * FROM nokta_urunler WHERE id = :urun_id" , ['urun_id' => $urun_id]);
                // Ürünün cok_satan değerini kontrol et ve arttır
                $cok_satan = $urun['cok_satan'];
                if ($cok_satan === null || $cok_satan === '') {
                    $cok_satan = 0;
                }
                $cok_satan++;
    
                // cok_satan değerini güncelle
                $updateQuery = "UPDATE nokta_urunler SET cok_satan = :cok_goren WHERE id = :id";
                $db->update($updateQuery, ['cok_goren' => $cok_satan, 'id' => $urun_id]);
    
                $urun_blkodu = $urun["BLKODU"];
                if (empty($ozel_fiyat)) {
                    $uyenin_fiyati = $urun["DSF" . $uye_gor_fiyat];
                    $uyenin_fiyati = number_format((float)$uyenin_fiyati, 2, '.', '');
                    if ($urun["DSF" . $uye_gor_fiyat] == NULL || $urun["DSF" . $uye_gor_fiyat] == '') {
                        $uyenin_fiyati = $urun["KSF" . $uye_gor_fiyat];
                        $uyenin_fiyati = number_format((float)$uyenin_fiyati, 2, '.', '');
                    }
                } else {
                    $uyenin_fiyati = $ozel_fiyat;
                    $uyenin_fiyati = number_format((float)$uyenin_fiyati, 2, '.', '');
                }
    
                $doviz_satis_fiyati = ($urun['DOVIZ_BIRIMI'] == '$') ? $satis_dolar : $satis_euro;
    
                $siparisUrunEkleQuery = "INSERT INTO b2b_siparis_urunler (sip_id, urun_id, BLKODU, adet, birim_fiyat, dolar_satis) VALUES (:siparisId, :urun_id, :urun_blkodu, :miktar, :uyenin_fiyati, :doviz_satis_fiyati)";
                $db->insert($siparisUrunEkleQuery, [
                    'siparisId' => $siparisId, 'urun_id' => $urun_id, 'urun_blkodu' => $urun_blkodu, 'miktar' => $miktar, 'uyenin_fiyati' => $uyenin_fiyati, 'doviz_satis_fiyati' => $doviz_satis_fiyati
                ]);
    
                if (!$db->lastInsertId()) {
                    echo "Ürün eklerken hata oluştu: " ;
                    break; // Hata durumunda döngüyü sonlandırabilirsiniz
                }
            }
            // Üye sepetindeki ürünleri sildiğinizden emin olun (bu adımı dikkatlice kullanın)
            $uyeSepetSilQuery = "DELETE FROM uye_sepet WHERE uye_id = :uye_id";
            $db->delete($uyeSepetSilQuery, ['uye_id' => $uye_id]);

        } else { echo "Üye sepeti sorgulama hatası: "; }
    } else { echo "Sipariş oluşturma hatası: "; }

    $currentDateTime = date("d.m.Y H:i:s");
    $degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));

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
    foreach ($elements as $elementName => $elementValue) {
        $element = $xmlDoc->createElement($elementName);
        $element->appendChild($xmlDoc->createCDATASection($elementValue));
        $fatura->appendChild($element);
    }
    // CARI BILGI ALANI SON
    // URUNLER ALANI BASLANGICI
    $faturaHareket = $xmlDoc->createElement('FATURAHAREKET');
    $root->appendChild($faturaHareket);
    $uyeSiparisUrunleriStmt = $db->fetchAll("SELECT * FROM b2b_siparis_urunler WHERE sip_id = $siparisId");
    foreach ($uyeSiparisUrunleriStmt as $row) {
        $urun_id = $row['urun_id'];
        $urun_adet = $row['adet'];
        $birim_fiyat = $row['birim_fiyat'];

        $noktaurun = $db->fetch("SELECT * FROM nokta_urunler WHERE id = $urun_id");
        $urun_blkodu = $noktaurun['BLKODU'];
        $hareket = $xmlDoc->createElement('HAREKET');
        $faturaHareket->appendChild($hareket);
        $blstkodu = $xmlDoc->createElement('BLSTKODU');
        $blstkodu->appendChild($xmlDoc->createCDATASection($urun_blkodu));
        $hareket->appendChild($blstkodu);
        $MIKTARI_2 = $xmlDoc->createElement('MIKTARI_2');
        $MIKTARI_2->appendChild($xmlDoc->createCDATASection($urun_adet));
        $hareket->appendChild($MIKTARI_2);
        $BIRIMI_2 = $xmlDoc->createElement('BIRIMI_2');
        $BIRIMI_2->appendChild($xmlDoc->createCDATASection($noktaurun['birim']));
        $hareket->appendChild($BIRIMI_2);
        $MIKTARI = $xmlDoc->createElement('MIKTARI');
        $MIKTARI->appendChild($xmlDoc->createCDATASection($urun_adet));
        $hareket->appendChild($MIKTARI);
        $BIRIMI = $xmlDoc->createElement('BIRIMI');
        $BIRIMI->appendChild($xmlDoc->createCDATASection($noktaurun['birim']));
        $hareket->appendChild($BIRIMI);
        $KDV_ORANI = $xmlDoc->createElement('KDV_ORANI');
        $KDV_ORANI->appendChild($xmlDoc->createCDATASection($noktaurun['kdv']));
        $hareket->appendChild($KDV_ORANI);

        $dovizimiz = '';
        if($noktaurun['DSF' . $uye_gor_fiyat] == NULL || $noktaurun['DSF' . $uye_gor_fiyat] == ''){
            $dovizimiz = 1;
            $gonderFiyat = $birim_fiyat;
        }else{
            if ($noktaurun['DOVIZ_BIRIMI'] == '$') {
                $dovizimiz = $satis_dolar;
            } elseif ($noktaurun['DOVIZ_BIRIMI'] == '€') {
                $dovizimiz = $satis_euro;
            }
            $KPBDVZ = $xmlDoc->createElement('KPBDVZ');
            $KPBDVZ->appendChild($xmlDoc->createCDATASection($noktaurun['DOVIZ_KULLAN']));
            $hareket->appendChild($KPBDVZ);
            $gonderFiyat = $birim_fiyat;

            $DVZ_FIYATI = $xmlDoc->createElement('DVZ_FIYATI');
            $DVZ_FIYATI->appendChild($xmlDoc->createCDATASection($gonderFiyat));
            $hareket->appendChild($DVZ_FIYATI);
        }
        $tlFiyat = str_replace(',', '.', $gonderFiyat);
        $tlFiyat = floatval($tlFiyat); // Convert to float
        $fiyati = $tlFiyat * floatval($dovizimiz); // Convert $dovizimiz to float as well
        if (!empty($yanIndirim) && $yanIndirim != 0) {
            $ISK_KDVSZ_TTR = 5 * $yanIndirim / 6; //kdvsiz iskonto tutar
            $ISK_SKNT_TPL = $fiyati * $urun_adet * 1.20; //ürün için satır toplamı iskontoda kullanıcak

            if (!empty($yanKargo) && $yanKargo != 0) {
                $spt_yn_tpl_kdvli = $yanIndirim + $yantoplam - $yanKargo;
            } else {
                $spt_yn_tpl_kdvli = $yanIndirim + $yantoplam;
            }
            $UYGL_ISK_FIYATI = $ISK_KDVSZ_TTR * ($ISK_SKNT_TPL / $spt_yn_tpl_kdvli);
            $formatted_UYGL_ISK_FIYATI = number_format($UYGL_ISK_FIYATI, 4, ',', '');

            $ISK_OZEL = $xmlDoc->createElement('ISK_OZEL');
            $ISK_OZEL->appendChild($xmlDoc->createCDATASection($formatted_UYGL_ISK_FIYATI));
            $hareket->appendChild($ISK_OZEL);
        }
        $birim_fiyat_tl = str_replace('.', ',', $fiyati);

        $KPB_FIYATI = $xmlDoc->createElement('KPB_FIYATI');
        $KPB_FIYATI->appendChild($xmlDoc->createCDATASection($birim_fiyat_tl));
        $hareket->appendChild($KPB_FIYATI);


        $DEPO_ADI = $xmlDoc->createElement('DEPO_ADI');
        $DEPO_ADI->appendChild($xmlDoc->createCDATASection('PERPA M01'));
        $hareket->appendChild($DEPO_ADI);

        if($noktaurun['DOVIZ_BIRIMI'] == '$'){
            $DOVIZ_ALIS = $xmlDoc->createElement('DOVIZ_ALIS');
            $DOVIZ_ALIS->appendChild($xmlDoc->createCDATASection($alis_dolar));
            $hareket->appendChild($DOVIZ_ALIS);
            $DOVIZ_SATIS = $xmlDoc->createElement('DOVIZ_SATIS');
            $DOVIZ_SATIS->appendChild($xmlDoc->createCDATASection($satis_dolar));
            $hareket->appendChild($DOVIZ_SATIS);
        }elseif($noktaurun['DOVIZ_BIRIMI'] == '€'){
            $DOVIZ_ALIS = $xmlDoc->createElement('DOVIZ_ALIS');
            $DOVIZ_ALIS->appendChild($xmlDoc->createCDATASection($alis_euro));
            $hareket->appendChild($DOVIZ_ALIS);
            $DOVIZ_SATIS = $xmlDoc->createElement('DOVIZ_SATIS');
            $DOVIZ_SATIS->appendChild($xmlDoc->createCDATASection($satis_euro));
            $hareket->appendChild($DOVIZ_SATIS);
        }
        $ISK_ORAN_1 = $xmlDoc->createElement('ISK_ORAN_1');
        $ISK_ORAN_1->appendChild($xmlDoc->createCDATASection('0'));
        $hareket->appendChild($ISK_ORAN_1);
        $OZEL_KODU = $xmlDoc->createElement('OZEL_KODU');
        $OZEL_KODU->appendChild($xmlDoc->createCDATASection(''));
        $hareket->appendChild($OZEL_KODU);
        $EKBILGI_1 = $xmlDoc->createElement('EKBILGI_1');
        $EKBILGI_1->appendChild($xmlDoc->createCDATASection(''));
        $hareket->appendChild($EKBILGI_1);
        $PAZ_PERS_BLKODU = $xmlDoc->createElement('PAZ_PERS_BLKODU');
        $PAZ_PERS_BLKODU->appendChild($xmlDoc->createCDATASection(''));
        $hareket->appendChild($PAZ_PERS_BLKODU);
        $PAZ_PERSONEL = $xmlDoc->createElement('PAZ_PERSONEL');
        $PAZ_PERSONEL->appendChild($xmlDoc->createCDATASection(''));
        $hareket->appendChild($PAZ_PERSONEL);
        $PAZ_URUN_ORANI = $xmlDoc->createElement('PAZ_URUN_ORANI');
        $PAZ_URUN_ORANI->appendChild($xmlDoc->createCDATASection(''));
        $hareket->appendChild($PAZ_URUN_ORANI);
        $PAZ_URUN_TUTARI = $xmlDoc->createElement('PAZ_URUN_TUTARI');
        $PAZ_URUN_TUTARI->appendChild($xmlDoc->createCDATASection(''));
        $hareket->appendChild($PAZ_URUN_TUTARI);
        $PAZ_ISC_ORANI = $xmlDoc->createElement('PAZ_ISC_ORANI');
        $PAZ_ISC_ORANI->appendChild($xmlDoc->createCDATASection(''));
        $hareket->appendChild($PAZ_ISC_ORANI);
        $PAZ_ISC_TUTARI = $xmlDoc->createElement('PAZ_ISC_TUTARI');
        $PAZ_ISC_TUTARI->appendChild($xmlDoc->createCDATASection(''));
        $hareket->appendChild($PAZ_ISC_TUTARI);
    }
    if ($yanKargo == '0' || $yanKargo == '0,00') {
    }else{
        $hareket1 = $xmlDoc->createElement('HAREKET');
        $faturaHareket->appendChild($hareket1);
        $blstkodu1 = $xmlDoc->createElement('BLSTKODU');
        $blstkodu1->appendChild($xmlDoc->createCDATASection('-1'));
        $hareket1->appendChild($blstkodu1);
        $blstkodu = $xmlDoc->createElement('STOK_ADI');
        $blstkodu->appendChild($xmlDoc->createCDATASection('Kargo Gönderim Ücreti'));
        $hareket1->appendChild($blstkodu);
        $MIKTARI_2 = $xmlDoc->createElement('MIKTARI_2');
        $MIKTARI_2->appendChild($xmlDoc->createCDATASection('1'));
        $hareket1->appendChild($MIKTARI_2);
        $BIRIMI_2 = $xmlDoc->createElement('BIRIMI_2');
        $BIRIMI_2->appendChild($xmlDoc->createCDATASection('ADET'));
        $hareket1->appendChild($BIRIMI_2);
        $MIKTARI = $xmlDoc->createElement('MIKTARI');
        $MIKTARI->appendChild($xmlDoc->createCDATASection('1'));
        $hareket1->appendChild($MIKTARI);
        $BIRIMI = $xmlDoc->createElement('BIRIMI');
        $BIRIMI->appendChild($xmlDoc->createCDATASection('ADET'));
        $hareket1->appendChild($BIRIMI);
        $KDV_ORANI = $xmlDoc->createElement('KDV_ORANI');
        $KDV_ORANI->appendChild($xmlDoc->createCDATASection('20'));
        $hareket1->appendChild($KDV_ORANI);
        $MUH_GENEL_KODU = $xmlDoc->createElement('MUH_KODU_GENEL');
        $MUH_GENEL_KODU->appendChild($xmlDoc->createCDATASection('770 03 22'));
        $hareket1->appendChild($MUH_GENEL_KODU);
        $yanKargo = str_replace('.', ',', $yanKargo);
        $KPB_FIYATI = $xmlDoc->createElement('KPB_FIYATI');
        $KPB_FIYATI->appendChild($xmlDoc->createCDATASection($yanKargo));
        $hareket1->appendChild($KPB_FIYATI);
    }
    // URUNLER ALANI SONU
    // DOVIZ ALANI BASLANGICI
    $faturaKur = $xmlDoc->createElement('FATURAKUR');
    $root->appendChild($faturaKur);
    $kurhareket = $xmlDoc->createElement('HAREKET');
    $faturaKur->appendChild($kurhareket);
    $doviz_birimi = $xmlDoc->createElement('DOVIZ_BIRIMI');
    $doviz_birimi->appendChild($xmlDoc->createCDATASection('$'));
    $kurhareket->appendChild($doviz_birimi);
    $doviz_alis = $xmlDoc->createElement('DOVIZ_ALIS');
    $doviz_alis->appendChild($xmlDoc->createCDATASection($alis_dolar));
    $kurhareket->appendChild($doviz_alis);
    $doviz_satis = $xmlDoc->createElement('DOVIZ_SATIS');
    $doviz_satis->appendChild($xmlDoc->createCDATASection($satis_dolar));
    $kurhareket->appendChild($doviz_satis);
    $kurhareket1 = $xmlDoc->createElement('HAREKET');
    $faturaKur->appendChild($kurhareket1);
    $doviz_birimi1 = $xmlDoc->createElement('DOVIZ_BIRIMI');
    $doviz_birimi1->appendChild($xmlDoc->createCDATASection('€'));
    $kurhareket1->appendChild($doviz_birimi1);
    $doviz_alis1 = $xmlDoc->createElement('DOVIZ_ALIS');
    $doviz_alis1->appendChild($xmlDoc->createCDATASection($alis_euro));
    $kurhareket1->appendChild($doviz_alis1);
    $doviz_satis1 = $xmlDoc->createElement('DOVIZ_SATIS');
    $doviz_satis1->appendChild($xmlDoc->createCDATASection($satis_euro));
    $kurhareket1->appendChild($doviz_satis1);
    // DOVIZ ALANI SONU
    $xmlFileName = 'fatura_' . $siparisNumarasi . '.xml';
    $xmlDoc->save('../../assets/faturalar/' . $xmlFileName);

    function updateUyeId($db, $promosyon_kodu, $uye_id, $promosyon_kullanim_sayisi, $kullanildi) {
        $uyeIdResult = "SELECT uye_id FROM b2b_promosyon WHERE promosyon_kodu = $promosyon_kodu";
        if ($uyeIdResult) {
            if (empty($uyeIdResult['uye_id'])) { // uye_id boşsa, direkt $uye_id'yi yaz
                $newUyeId = $uye_id;
            } else { // uye_id doluysa, mevcut değere $uye_id'yi virgülle ekle
                $newUyeId = $uyeIdResult['uye_id'] . ',' . $uye_id;
            }
            // Promosyonu güncelle
            $updatePromosyonQuery = "UPDATE b2b_promosyon SET kullanim_sayisi = :kullanim_sayisi, kullanildi = :kullanildi, uye_id = :uye_id WHERE promosyon_kodu = :promosyon_kodu";
            $db ->update($updatePromosyonQuery,['kullanim_sayisi' => $promosyon_kullanim_sayisi,'kullanildi' => $kullanildi,'uye_id' => $newUyeId,'promosyon_kodu' => $promosyon_kodu]);
        }
    }

    if (!empty($promosyon_kodu)) {
        $promosyon = $db->fetch("SELECT * FROM b2b_promosyon WHERE promosyon_kodu = $promosyon_kodu");
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
    $mail_icerik = siparisAlindi($uyeAdSoyad, $siparisId, $siparisNumarasi);
    mailGonder($uye_email, 'Siparişiniz Alınmıştır!', $mail_icerik, 'Nokta Elektronik');

    header("Location: ../../tr/onay?siparis-numarasi=$siparisNumarasi");
    exit;
}

if (isset($_GET['cariveri']) || isset($_GET['cariveriFinans'])) {
    if(isset($_GET['cariveri'])) {
        $veri = base64_decode($_GET['cariveri']);
    } elseif(isset($_GET['cariveriFinans'])) {
        $veri = base64_decode($_GET['cariveriFinans']);
    }
    $decodedVeri = json_decode($veri, true);
    $yantoplam = $decodedVeri["yantoplam"];
    $cardNo = $decodedVeri["cardNo"];
    $cariOdeme = "cari";
    $maskedCardNo = substr($cardNo, 0, 4) . str_repeat('*', strlen($cardNo) - 8) . substr($cardNo, -4);
    $cardHolder = $decodedVeri["cardHolder"];
    $banka_id = $decodedVeri["banka_id"];
    $hesap = $decodedVeri["hesap"];
    $taksit_sayisi = $decodedVeri["taksit"];
    $uye_id = $decodedVeri["uye_id"];
    $lang = $decodedVeri["lang"];
    if($hesap == 1){$doviz = "$";}else{$doviz = "TL";}
    $banka = $db->fetch("SELECT * FROM b2b_banka_taksit_eslesme WHERE id = $banka_id ");
    $ticariProgram = $banka["ticari_program"];
    $banka_pos = $db->fetch("SELECT * FROM b2b_banka_pos_listesi WHERE id = $ticariProgram ");
    $blbnhskodu = $banka_pos["BLBNHSKODU"];
    $banka_adi = $banka_pos["BANKA_ADI"];
    $banka_tanimi = $banka_pos["TANIMI"];
    $uye = $db->fetch("SELECT * FROM uyeler WHERE id = $uye_id ");
    $uyecarikod = $uye['BLKODU'];
    $uye_mail = $uye['email'];
    $firmaUnvani = $uye['firmaUnvani'];
    $dov_al =  $alis_dolar;
    $dov_sat = $satis_dolar;

    $currentDateTime = date("d.m.Y H:i:s");
    $degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));
    if(isset($_GET['cariveri'])) {
        //Param Pos
        $sonucStr = $_POST['TURKPOS_RETVAL_Sonuc_Str'];
        $dekont = $_POST['TURKPOS_RETVAL_Dekont_ID'];
        $tutar = $_POST['TURKPOS_RETVAL_Tahsilat_Tutari'];
        $tutar = str_replace(',', '.', $tutar);
        $pos_id = 1;
        $basarili = 1;
        $stmt = "INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, islem_turu, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :islem_turu, :tutar, :basarili)";
        $db->insert($stmt , ['uye_id' => $uye_id, 'pos_id' => $pos_id, 'islem' => $sonucStr, 'islem_turu' => $cariOdeme, 'tutar' => $tutar, 'basarili' => $basarili]);
        $inserted_id = $db->lastInsertId();
        dekontOlustur($uye_id, $inserted_id, $firmaUnvani,$maskedCardNo, $cardHolder ,$taksit_sayisi,$yantoplam,$degistirme_tarihi);
        posXmlOlustur($uyecarikod, $hesap, $degistirme_tarihi,$degistirme_tarihi,$yantoplam,'',$dov_al,$dov_sat,$siparisNumarasi,$blbnhskodu,$banka_adi,$taksit_sayisi, $doviz, $banka_tanimi);

        $mail_icerik = cariOdeme($firmaUnvani,$yantoplam,$taksit_sayisi);
        mailGonder($uye_mail, 'Cari Ödeme Bildirimi', $mail_icerik, 'Nokta Elektronik');

        header("Location: ../../tr/onay?cari_odeme=");

    }elseif(isset($_GET['cariveriFinans']) && $_POST["mdStatus"] == "1") {
        $username = 'noktaadmin';
        $password = 'NEBsis28736.!';
        if($taksit_sayisi == 1 || $taksit_sayisi == 0){
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <CC5Request>
            <Name>' . $username . '</Name>
            <Password>' . $password . '</Password>
            <ClientId>' . $_POST['clientid'] . '</ClientId>
            <OrderId>' . $_POST['oid'] . '</OrderId>
            <Type>Auth</Type>
            <Number>' . $_POST['md'] . '</Number>
            <Total>' . $_POST['amount'] . '</Total>
            <Currency>949</Currency>
            <PayerTxnId>' . $_POST['xid'] . '</PayerTxnId>
            <PayerSecurityLevel>' . $_POST['eci'] . '</PayerSecurityLevel>
            <PayerAuthenticationCode>' . $_POST['cavv'] . '</PayerAuthenticationCode>
            </CC5Request>';
        }else{
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <CC5Request>
            <Name>' . $username . '</Name>
            <Password>' . $password . '</Password>
            <ClientId>' . $_POST['clientid'] . '</ClientId>
            <OrderId>' . $_POST['oid'] . '</OrderId>
            <Type>Auth</Type>
            <Number>' . $_POST['md'] . '</Number>
            <Total>' . $_POST['amount'] . '</Total>
            <Mode>P</Mode>
            <Taksit>'. $taksit_sayisi .'</Taksit>
            <Currency>949</Currency>
            <PayerTxnId>' . $_POST['xid'] . '</PayerTxnId>
            <PayerSecurityLevel>' . $_POST['eci'] . '</PayerSecurityLevel>
            <PayerAuthenticationCode>' . $_POST['cavv'] . '</PayerAuthenticationCode>
            </CC5Request>';
            file_put_contents('gidenxml.xml', $xml);
        }

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSLVERSION, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml', 'Content-length: ' . strlen($xml)));
            curl_setopt($ch, CURLOPT_POST, true); //POST Metodu kullanarak verileri gönder
            curl_setopt($ch, CURLOPT_HEADER, false); //Serverdan gelen Header bilgilerini önemseme.
            curl_setopt($ch, CURLOPT_URL, 'https://sanalpos.turkiyefinans.com.tr/fim/api'); //Bağlanacağı URL
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Transfer sonuçlarını al.
            $data = curl_exec($ch);
            $xmlResponse = simplexml_load_string($data);
            file_put_contents(sys_get_temp_dir() . '/gelenxml.xml', $xmlResponse);

            if ($xmlResponse->ProcReturnCode == "00") {
                $yonetici_maili = 'h.pamuk@noktaelektronik.net';

                $dov_al = $alis_dolar;
                $dov_sat = $satis_dolar;

                $currentDateTime = date("d.m.Y H:i:s");
                $degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));

                $yantoplam1 = floatval($yantoplam);
                $yantoplam = number_format($yantoplam1, 2, ',', '.');

                $pos_id = 4;
                $basarili = 1;
                $sonucStr = "Ödeme işlemi başarılı: " . $xmlResponse->Response . ' Kod= ' . $xmlResponse->ProcReturnCode;
                $oid = $xmlResponse->ReturnOid;
                $transid = $xmlResponse->TransId;
                $stmt = "INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, islem_turu, tutar, basarili, transid, siparis_no) VALUES (:uye_id, :pos_id, :islem, :islem_turu, :tutar, :basarili, :transid, :siparis_no)";
                $db->insert($stmt, ['uye_id' => $uye_id, 'pos_id' => $pos_id, 'islem' => $sonucStr, 'islem_turu' => $cariOdeme, 'tutar' => $yantoplam1, 'basarili' => $basarili, 'transid' => $transid, 'siparis_no' => $oid]);

                $inserted_id = $db->lastInsertId();
                dekontOlustur($uye_id, $inserted_id, $firmaUnvani, $maskedCardNo, $cardHolder, $taksit_sayisi, $yantoplam, $degistirme_tarihi);
                posXmlOlustur($uyecarikod, $hesap, $degistirme_tarihi,$degistirme_tarihi,$yantoplam,'',$dov_al,$dov_sat,$siparisNumarasi,$blbnhskodu,$banka_adi,$taksit_sayisi, $doviz, $banka_tanimi);

                header("Location: ../../tr/onay?cari_odeme=");
                $mail_icerik = cariOdeme($firmaUnvani,$yantoplam,$taksit_sayisi);
                mailGonder($uye_mail, 'Cari Ödeme Bildirimi', $mail_icerik,'Nokta Elektronik');
                exit();

            } else {
                $yantoplam1 = floatval($yantoplam);
                // ProcReturnCode 00 değilse hata mesajı göster veya başka bir işlem yap
                $pos_id = 4;
                $basarili = 0;
                $sonucStr = "Ödeme işlemi başarısız: " . $xmlResponse->ErrMsg . ' Kod= ' . $xmlResponse->ProcReturnCode;
                $stmt = "INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)";
                $db->insert($stmt ,['uye_id' => $uye_id, 'pos_id' => $pos_id, 'islem' => $sonucStr, 'tutar' => $yantoplam1, 'basarili' => $basarili]);

                header("Location: ../../tr/cariodeme?code=".$xmlResponse->ProcReturnCode."&message=".$xmlResponse->ErrMsg);
            }
            curl_close($ch);
        }
        catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}

if (isset($_GET['veri'])) {
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
    $yantoplamParam = $decodedVeri["yantoplam"];
    $yantoplam = $decodedVeri["yantoplam"];
    $yantoplam = number_format($yantoplam / 100, 2, ',', '.');
    $yantoplamxml = str_replace('.', ',', $yantoplam);
    $taksit_sayisi = $decodedVeri["taksit"];
    $banka_id = $decodedVeri["banka_id"];
    $uye_id = $decodedVeri["uye_id"];
    $tip = $decodedVeri["tip"];
    $lang = $decodedVeri["lang"];

    $banka = $db->fetch("SELECT * FROM b2b_banka_taksit_eslesme WHERE id = $banka_id ");
    $ticariProgram = $banka["ticari_program"];

    $banka_pos = $db->fetch("SELECT * FROM b2b_banka_pos_listesi WHERE id = $ticariProgram ");
    $blbnhskodu = $banka_pos["BLBNHSKODU"];
    $banka_adi = $banka_pos["BANKA_ADI"];
    $banka_tanimi = $banka_pos["TANIMI"];

    $uye = $db->fetch("SELECT * FROM uyeler WHERE id = $uye_id ");
    $uyecarikod = $uye['BLKODU'];

    $doviz_kur = $db->fetch("SELECT * FROM b2b_kurlar WHERE id = 2");

    $dov_al = str_replace('.', ',', $doviz_kur["alis"]);
    $dov_sat = str_replace('.', ',', $doviz_kur["satis"]);

    $currentDateTime = date("d.m.Y H:i:s");
    $degistirme_tarihi = date("d.m.Y H:i:s", strtotime($currentDateTime . " +3 hours"));

    posXmlOlustur($uyecarikod, $hesap, $degistirme_tarihi,$degistirme_tarihi,$yantoplamxml,'',$dov_al,$dov_sat,$siparisNumarasi,$blbnhskodu,$banka_adi,$taksit_sayisi, 'TL', $banka_tanimi);

    //Adresler tablosundan adresi çek
    $teslimat = $db->fetch("SELECT * FROM b2b_adresler WHERE uye_id = $uye_id AND aktif = '1'");
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

    //üyeler tablosundan fatura adresini çek
    $uye = $db->fetch("SELECT * FROM uyeler WHERE id = $uye_id ");
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
    $siparisEkleQuery = "INSERT INTO b2b_siparisler 
    (siparis_no, uye_id, durum, odeme_sekli, teslimat_ad, teslimat_soyad, teslimat_firmaadi, teslimat_adres, teslimat_telefon, teslimat_ulke, teslimat_il, teslimat_ilce, teslimat_tcno, 
     teslimat_vergino, teslimat_vergidairesi, teslimat_postakodu, uye_ad, uye_soyad, uye_email, uye_tel, uye_ulke, uye_adres, uye_postakodu, uye_tcno, uye_firmaadi, uye_vergidairesi, 
     uye_vergino, uye_il, uye_ilce, uye_muhasebekodu, sepet_toplami, sepet_kdv, indirim, kargo_ucreti, kargo_firmasi, toplam, desi, tarih) VALUES                                                                                                                  
    (:siparisNumarasi, :uye_id, '1', :tip, :teslimat_ad, :teslimat_soyad, :teslimat_firmaadi, :teslimat_adres, :teslimat_telefon, :teslimat_ulke, :teslimat_il, :teslimat_ilce, :teslimat_tcno, 
     :teslimat_vergino, :teslimat_vergidairesi, :teslimat_postakodu, :uye_ad, :uye_soyad, :uye_email, :uye_tel, :uye_ulke, :uye_adres, :uye_postakodu, :uye_tcno, :uye_firmaadi, :uye_vergidairesi, 
     :uye_vergino, :uye_il, :uye_ilce, :uye_muhasebekodu, :yanSepetToplami, :yanSepetKdv, :yanIndirim, :yanKargo, :deliveryOption, :yantoplam, :desi, NOW() + INTERVAL 3 HOUR)";
    $params =['siparisNumarasi'=> $siparisNumarasi, 'uye_id'=> $uye_id, 'tip'=> $tip, 'teslimat_ad'=> $teslimat_ad, 'teslimat_soyad'=> $teslimat_soyad, 
    'teslimat_firmaadi'=> $teslimat_firmaadi, 'teslimat_adres'=> $teslimat_adres, 'teslimat_telefon'=> $teslimat_telefon, 'teslimat_ulke'=> $teslimat_ulke, 
    'teslimat_il'=> $teslimat_il, 'teslimat_ilce'=> $teslimat_ilce, 'teslimat_tcno'=> $teslimat_tcno, 'teslimat_vergino'=> $teslimat_vergino, 
    'teslimat_vergidairesi'=> $teslimat_vergidairesi, 'teslimat_postakodu'=> $teslimat_postakodu, 'uye_ad'=> $uye_ad, 'uye_soyad'=> $uye_soyad, 
    'uye_email'=> $uye_email, 'uye_tel'=> $uye_tel, 'uye_ulke'=> $uye_ulke, 'uye_adres'=> $uye_adres, 'uye_postakodu'=> $uye_postakodu, 'uye_tcno'=> $uye_tcno, 
    'uye_firmaadi'=> $uye_firmaadi, 'uye_vergidairesi'=> $uye_vergidairesi, 'uye_vergino'=> $uye_vergino, 'uye_il'=> $uye_il, 'uye_ilce'=> $uye_ilce, 
    'uye_muhasebekodu'=> $uye_muhasebekodu, 'yanSepetToplami'=> $yanSepetToplami, 'yanSepetKdv'=> $yanSepetKdv, 'yanIndirim'=> $yanIndirim, 'yanKargo'=> $yanKargo, 
    'deliveryOption'=> $deliveryOption, 'yantoplam'=> $yantoplamParam, 'desi'=> $desi];
    $siparisEkleStatement = $db->insert($siparisEkleQuery, $params);

    if ($siparisEkleStatement) {
        $siparisId = $db->lastInsertId(); // Eklenen siparişin ID'sini al
        // Üye sepetinden ürünleri al
        $uyeSepetUrunleri = $db->fetchAll("SELECT * FROM uye_sepet WHERE uye_id = :uye_id", ['uye_id' => $uye_id]);

        if ($uyeSepetUrunleri) {
            foreach ($uyeSepetUrunleri as $row) {
                $urun_id = $row['urun_id'];
                $miktar = $row['adet'];
                $ozel_fiyat = $row['ozel_fiyat'];
                
                $urun = $db->fetch("SELECT * FROM nokta_urunler WHERE id = :urun_id", ['urun_id' => $urun_id]);
                $urun_blkodu = $urun['BLKODU'];

                if (empty($ozel_fiyat)) {
                    $uyenin_fiyati = $urun["DSF" . $uye_gor_fiyat] ?? $urun["KSF" . $uye_gor_fiyat];
                    $uyenin_fiyati = number_format((float)$uyenin_fiyati, 2, '.', '');
                } else {
                    $uyenin_fiyati = number_format((float)$ozel_fiyat, 2, '.', '');
                }

                $doviz_satis_fiyati = match ($urun['DOVIZ_BIRIMI']) {
                    '$' => $satis_dolar,
                    '€' => $satis_euro,
                    default => 0,
                };

                $cok_satan = ($urun['cok_satan'] ?? 0) + 1;
                $db->update("UPDATE nokta_urunler SET cok_satan = :cok_satan WHERE id = :urun_id", [
                    'cok_satan' => $cok_satan,
                    'urun_id' => $urun_id
                ]);

                $db->insert("INSERT INTO b2b_siparis_urunler (sip_id, urun_id, BLKODU, adet, birim_fiyat, dolar_satis) 
                            VALUES (:siparisId, :urun_id, :urun_blkodu, :miktar, :uyenin_fiyati, :doviz_satis_fiyati)", [ 'siparisId' => $siparisId, 'urun_id' => $urun_id, 
                            'urun_blkodu' => $urun_blkodu, 'miktar' => $miktar,'uyenin_fiyati' => $uyenin_fiyati, 'doviz_satis_fiyati' => $doviz_satis_fiyati ]);
            }
            if (!$db->delete("DELETE FROM uye_sepet WHERE uye_id = :uye_id", ['uye_id' => $uye_id])) {
                echo "Üye sepetini temizlerken hata oluştu.";
            }
        } else {
            echo "Üye sepetinde ürün bulunamadı.";
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
    }
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
            $updQuery = "UPDATE promosyon SET kullanim_sayisi = :kullanim_sayisi, kullanildi = :kullanildi, uye_id = :uye_id WHERE promosyon_kodu = :promosyon_kodu";
            $db->update($updQuery, ['kullanim_sayisi' => $promosyon_kullanim_sayisi, 'kullanildi' => $kullanildi, 'uye_id' => $newUyeId, 'promosyon_kodu' => $promosyon_kodu]);
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

    $mail_icerik = siparisAlindi($uyeAdSoyad, $siparisId, $siparisNumarasi);
    mailGonder($uye_email, 'Siparişiniz Alınmıştır!', $mail_icerik, 'Nokta Elektronik');

    //Param Pos
    $sonucStr = $_POST['TURKPOS_RETVAL_Sonuc_Str'];
    $dekont = $_POST['TURKPOS_RETVAL_Dekont_ID'];
    $tutar = $_POST['TURKPOS_RETVAL_Tahsilat_Tutari'];
    $tutar = str_replace(',', '.', $tutar);
    $pos_id = 1;
    $basarili = 1;
    $stmt = ("INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, islem_turu, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :islem_turu, :tutar, :basarili)");
    $db->insert($stmt, ['uye_id' => $uye_id, 'pos_id' => $pos_id, 'islem' => $sonucStr, 'islem_turu' => $siparisOdeme, 'tutar' => $tutar, 'basarili' => $basarili]);

    header("Location: ../../tr/onay?siparis-numarasi=$siparisNumarasi");
}

if (isset($_GET['sipFinans']) && $_POST["mdStatus"] == "1") {
    $veri = base64_decode($_GET['sipFinans']);
    $decodedVeri = json_decode($veri, true);
    $taksit_sayisi = $decodedVeri["odemeTaksit"];
    $yanSepetToplami = $decodedVeri["yanSepetToplami"];
    $yanSepetKdv = $decodedVeri["yanSepetKdv"];
    $yanIndirim = $decodedVeri["yanIndirim"];
    $deliveryOption = $decodedVeri["deliveryOption"];
    $yanKargo = $decodedVeri["yanKargo"];
    $promosyon_kodu = $decodedVeri["promosyon_kodu"];
    $desi = $decodedVeri["desi"];
    $siparisOdeme = "siparis";
    $hesap = "0";
    $yantoplam = $decodedVeri["yantoplam"];
    $yantoplam1 = floatval($yantoplam);
    $yantoplam = str_replace('.', ',', $yantoplam);
    $banka_id = $decodedVeri["banka_id"];
    $uye_id = $decodedVeri["uye_id"];
    $tip = $decodedVeri["tip"];
    $lang = $decodedVeri["lang"];

    $username = 'noktaadmin';
    $password = 'NEBsis28736.!';

    $xml ='<?xml version="1.0" encoding="UTF-8"?>
        <CC5Request>
        <Name>'.$username.'</Name>
        <Password>'.$password.'</Password>
        <ClientId>'.$_POST['clientid'].'</ClientId>
        <OrderId>'.$_POST['oid'].'</OrderId>
        <Type>Auth</Type>
        <Number>'.$_POST['md'].'</Number>
        <Total>'.$_POST['amount'].'</Total>
        <Currency>949</Currency>
        <PayerTxnId>'.$_POST['xid'].'</PayerTxnId>
        <PayerSecurityLevel>'.$_POST['eci'].'</PayerSecurityLevel>
        <PayerAuthenticationCode>'.$_POST['cavv'].'</PayerAuthenticationCode>
        </CC5Request>';

    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml', 'Content-length: ' . strlen($xml)));
        curl_setopt($ch, CURLOPT_POST, true); //POST Metodu kullanarak verileri gönder
        curl_setopt($ch, CURLOPT_HEADER, false); //Serverdan gelen Header bilgilerini önemseme.
        curl_setopt($ch, CURLOPT_URL, 'https://sanalpos.turkiyefinans.com.tr/fim/api'); //Bağlanacağı URL
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Transfer sonuçlarını al.
        $data = curl_exec($ch);
        $xmlResponse = simplexml_load_string($data);

        if ($xmlResponse->ProcReturnCode == "00") {
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
       }
        else {
            $pos_id = 4;
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
?>