<?php
// Configuration for database connection
define("DB_SERVER", getenv('DB_SERVER') ?: "localhost");
define("DB_USERNAME", getenv('DB_USERNAME') ?: "noktaelektronik");
define("DB_PASSWORD", getenv('DB_PASSWORD') ?: "Dell28736.!");
define("DB_NAME", getenv('DB_NAME') ?: "noktaelektronik_nokta");

$host = DB_SERVER;
$dbname = DB_NAME;
$username = DB_USERNAME;
$password = DB_PASSWORD;
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ]);
    return $pdo;
} catch (PDOException $e) {
    echo "Veritabanı bağlantısı başarısız: " . $e->getMessage();
    die();
}

$newDate = date('Y-m-d H:i:s', strtotime('+3 hours'));

// Secure string manipulation function
function duzenleString($str) {
    $replaceChars = array(
        'ç' => 'c', 'ğ' => 'g', 'ı' => 'i', 'i' => 'i',
        'ö' => 'o', 'ş' => 's', 'ü' => 'u', 'Ç' => 'C',
        'Ğ' => 'G', 'I' => 'I', 'İ' => 'I', 'Ö' => 'O',
        'Ş' => 'S', 'Ü' => 'U', ' ' => '-', '"' => '',
        "'" => '', '`' => '', '.' => '', ',' => '',
        ':' => '', ';' => '', '(' => '', ')' => '',
        '[' => '', ']' => '', '{' => '', '}' => '',
        '+' => '', '&' => '', '\\' => ''
    );
    $str = strtr($str, $replaceChars);
    $str = strtolower($str);
    $str = trim($str);
    $str = preg_replace('/\s+/', '-', $str);
    $str = str_replace(['---', '--'], ['-', '-'], $str);
    return $str;
}

// Validate and format prices
function gelenFiyatDuzenle($sayi) {
    if (empty($sayi)) {
        return null;
    }
    $sayi = str_replace(',', '.', $sayi);
    if (!preg_match('/^\d+(\.\d{1,4})?$/', $sayi)) {
        return null;
    }
    return number_format((float)$sayi, 4, '.', '');
}

// Connect to the database using PDO

// Log messages to the api_log table
function logToApiLog($message) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO api_log (log) VALUES (:logMessage)");
    $stmt->execute(['logMessage' => $message]);
}

// Process the stock inventory XML data
function getStockInventory($xmlData) {
    updateKategoriIDForAllProducts();
    global $pdo;
    global $newDate;
    echo '<br>' . $newDate . ': Stok Tarama Başladı.<br>';
    try {
        $xml = new SimpleXMLElement($xmlData);
    } catch (Exception $e) {
        logToApiLog("Failed to parse XML Stok Envanter: " . $e->getMessage());
        return;
    }

    if (!isset($xml->table->row)) {
        logToApiLog("No 'row' elements found in the XML Stok Envanter.");
        echo $newDate . ': Stok Tarama Tamamlandı.</br>';
        return;
    }

    $pdo->beginTransaction();
    $stmt = $pdo->prepare("REPLACE INTO nokta_urunler (BLKODU, UrunKodu, UrunAdiTR, UrunAdiEN, ARA_GRUBU, ALT_GRUBU, GRUBU, MARKASI, barkod, kdv, KDV_ORANI_SATIS_TPT, ACIKLAMA1, ACIKLAMA2, ACIKLAMA3, OZEL_KODU1, OZELALANTANIM_18, BIRIMLER, BIRIMI, MIKTAR_KULBILIR, WEBDE_GORUNSUN, DEGISTIRME_TARIHI, MarkaID, stok, seo_link, aktif,proje) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $STOKKODU = (string)$row->STOKKODU;
        $STOK_ADI = (string)$row->STOK_ADI;
        $STOK_ADI_YD = (string)$row->STOK_ADI_YD;
        $ARA_GRUBU = (string)$row->ARA_GRUBU;
        $ALT_GRUBU = (string)$row->ALT_GRUBU;
        $GRUBU = (string)$row->GRUBU;
        $MARKASI = (string)$row->MARKASI;
        $BARKODU = (string)$row->BARKODU;
        $KDV_ORANI = (string)$row->KDV_ORANI;
        $KDV_ORANI_SATIS_TPT = (string)$row->KDV_ORANI_SATIS_TPT;
        $ACIKLAMA1 = (string)$row->ACIKLAMA1;
        $ACIKLAMA2 = (string)$row->ACIKLAMA2;
        $ACIKLAMA3 = (string)$row->ACIKLAMA3;
        $OZEL_KODU1 = (string)$row->OZEL_KODU1;
        $OZELALANTANIM_18 = (string)$row->OZELALANTANIM_18;
        $BIRIMLER = (string)$row->BIRIMLER;
        $BIRIMI = (string)$row->BIRIMI;
        $MIKTAR_KULBILIR = (string)$row->MIKAR_KALAN;
        $WEBDE_GORUNSUN = (int)$row->WEBDE_GORUNSUN;
        $DEGISTIRME_TARIHI = date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI));
        $AKTIF = 1;
        $proje = 0;

        // Generating SEO link
        $sGRUBU = duzenleString($GRUBU);
        $sARA_GRUBU = duzenleString($ARA_GRUBU);
        $sALT_GRUBU = duzenleString($ALT_GRUBU);
        $sOZEL_KODU1 = duzenleString($OZEL_KODU1);
        $sSTOK_ADI = duzenleString($STOK_ADI);
        $sSTOKKODU = duzenleString($STOKKODU);
        $seoLink = $sGRUBU . '/' . ($sARA_GRUBU ? $sARA_GRUBU . '/' : '') . ($sALT_GRUBU ? $sALT_GRUBU . '/' : '') . ($sOZEL_KODU1 ? $sOZEL_KODU1 . '/' : '') . $sSTOK_ADI . '-' . $sSTOKKODU;

        // Get MarkaID
        $markaId = getMarkaIdByTitle($MARKASI, $pdo);

        $stmt->execute([$BLKODU, $STOKKODU, $STOK_ADI, $STOK_ADI_YD, $ARA_GRUBU, $ALT_GRUBU, $GRUBU, $MARKASI, $BARKODU, $KDV_ORANI, $KDV_ORANI_SATIS_TPT, $ACIKLAMA1, $ACIKLAMA2, $ACIKLAMA3, $OZEL_KODU1, $OZELALANTANIM_18, $BIRIMLER, $BIRIMI, $MIKTAR_KULBILIR, $WEBDE_GORUNSUN, $DEGISTIRME_TARIHI, $markaId, $MIKTAR_KULBILIR, $seoLink, $AKTIF, $proje]);

        logToApiLog("$newDate: İşlenen stok kodu: $STOKKODU");
        echo "$newDate: İşlenen stok kodu: $STOKKODU <br>";
    }
    $pdo->commit();
    echo "$newDate: Stok Tarama Tamamlandı. <br>";
}

// Get MarkaID by title
function getMarkaIdByTitle($title, $pdo) {
    $stmt = $pdo->prepare("SELECT id FROM nokta_urun_markalar_1 WHERE title = :title");
    $stmt->execute(['title' => $title]);
    $result = $stmt->fetch();
    return $result ? $result['id'] : null;
}

// Process stock list XML data
function getStockList($xmlData) {
    global $pdo;
    global $newDate;
    echo "$newDate: Fiyat Tarama Başladı. <br>";
    try {
        $xml = new SimpleXMLElement($xmlData);
    } catch (Exception $e) {
        logToApiLog("Failed to parse XML Fiyat Liste: " . $e->getMessage());
        return;
    }

    if (!isset($xml->table->row)) {
        logToApiLog("No 'row' elements found in the XML Fiyat Liste.");
        echo "$newDate: Fiyat Tarama Tamamlandı. <br>";
        return;
    }

    $pdo->beginTransaction();
    $stmt = $pdo->prepare("UPDATE nokta_urunler SET DOVIZ_KULLAN = ?, DOVIZ_BIRIMI = ?, KSF1 = ?, KSF2 = ?, KSF3 = ?, KSF4 = ?, DSF1 = ?, DSF2 = ?, DSF3 = ?, DSF4 = ?, DEGISTIRME_TARIHI = ? WHERE BLKODU = ?");
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $STOKKODU = (string)$row->STOKKODU;
        $DOVIZ_KULLAN = (string)$row->DOVIZ_KULLAN;
        $DOVIZ_BIRIMI = (string)$row->DOVIZ_BIRIMI;
        $KSF1 = gelenFiyatDuzenle((string)$row->KSF1);
        $KSF2 = gelenFiyatDuzenle((string)$row->KSF2);
        $KSF3 = gelenFiyatDuzenle((string)$row->KSF3);
        $KSF4 = gelenFiyatDuzenle((string)$row->KSF4);
        $DSF1 = gelenFiyatDuzenle((string)$row->DSF1);
        $DSF2 = gelenFiyatDuzenle((string)$row->DSF2);
        $DSF3 = gelenFiyatDuzenle((string)$row->DSF3);
        $DSF4 = gelenFiyatDuzenle((string)$row->DSF4);
        $DEGISTIRME_TARIHI = date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI));

        $stmt->execute([$DOVIZ_KULLAN, $DOVIZ_BIRIMI, $KSF1, $KSF2, $KSF3, $KSF4, $DSF1, $DSF2, $DSF3, $DSF4, $DEGISTIRME_TARIHI, $BLKODU]);

        logToApiLog("$newDate: Fiyatı güncellenen stok kodu: $STOKKODU");
        echo "$newDate: Fiyatı güncellenen stok kodu: $STOKKODU <br>";
    }
    $pdo->commit();
    echo "$newDate: Fiyat Tarama Tamamlandı. <br>";
}

// Process account list XML data
function getAccountList($xmlData) {
    global $pdo;
    global $newDate;
    echo "$newDate: Cari Tarama Başladı. <br>";
    try {
        $xml = new SimpleXMLElement($xmlData);
    } catch (Exception $e) {
        logToApiLog("Failed to parse XML Cari Liste: " . $e->getMessage());
        return;
    }

    if (!isset($xml->table->row)) {
        logToApiLog("No 'row' elements found in the XML Cari Liste.");
        echo "$newDate: Cari Tarama Tamamlandı. <br>";
        return;
    }

    $pdo->beginTransaction();
    $stmt = $pdo->prepare("REPLACE INTO uyeler (BLKODU, muhasebe_kodu, ad, soyad, firmaUnvani, vergi_dairesi, vergi_no, tel, aktif, fiyat, DOVIZ_KULLAN, DOVIZ_BIRIMI, MUHKODU_ALIS, MUHKODU_SATIS, adres, il, ilce, posta_kodu, email, parola, ulke, ADI_SOYADI, cinsiyet, tc_no, satis_temsilcisi, uye_tipi, OZELALANTANIM_3, OZELALANTANIM_27, DEGISTIRME_TARIHI, EFATURA_SENARYO, EFATURA_KULLAN, ALICI_GRUBU, EIRSALIYE_KULLAN, kayit_tarihi, GRUBU) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $CARIKODU = (string)$row->CARIKODU;
        $ADI = (string)$row->ADI;
        $SOYADI = (string)$row->SOYADI;
        $TICARI_UNVANI = (string)$row->TICARI_UNVANI;
        $VERGI_DAIRESI = (string)$row->VERGI_DAIRESI;
        $VERGI_NO = (string)$row->VERGI_NO;
        $CEP_TEL = (string)$row->CEP_TEL;
        $AKTIF = (int)$row->AKTIF;
        $STOK_FIYATI = (string)$row->STOK_FIYATI;
        $DOVIZ_KULLAN = (string)$row->DOVIZ_KULLAN;
        $DOVIZ_BIRIMI = (string)$row->DOVIZ_BIRIMI;
        $MUHKODU_ALIS = (string)$row->MUHKODU_ALIS;
        $MUHKODU_SATIS = (string)$row->MUHKODU_SATIS;
        $ADRESI_1 = (string)$row->ADRESI_1;
        $ILI_1 = (string)$row->ILI_1;
        $ILCESI_1 = (string)$row->ILCESI_1;
        $POSTA_KODU_1 = (string)$row->POSTA_KODU_1;
        $WEB_USER_NAME = (string)$row->WEB_USER_NAME;
        $WEB_USER_PASSW = (string)$row->WEB_USER_PASSW;
        $ULKESI_1 = (string)$row->ULKESI_1;
        $ADI_SOYADI = (string)$row->ADI_SOYADI;
        $CINSIYETI = (string)$row->CINSIYETI;
        $TC_KIMLIK_NO = (string)$row->TC_KIMLIK_NO;
        $PAZ_BLCRKODU = (string)$row->PAZ_BLCRKODU;
        $OZEL_KODU3 = (string)$row->OZEL_KODU3;
        $OZELALANTANIM_3 = (string)$row->OZELALANTANIM_3;
        $OZELALANTANIM_27 = (string)$row->OZELALANTANIM_27;
        $EFATURA_SENARYO = (string)$row->EFATURA_SENARYO;
        $EFATURA_KULLAN = (int)$row->EFATURA_KULLAN;
        $ALICI_GRUBU = (string)$row->ALICI_GRUBU;
        $EIRSALIYE_KULLAN = (int)$row->EIRSALIYE_KULLAN;
        $GRUBU = (string)$row->GRUBU;
        $DEGISTIRME_TARIHI = date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI));
        $KAYIT_TARIHI = date('Y-m-d H:i:s', strtotime('+3 hours'));

        $stmt->execute([$BLKODU, $CARIKODU, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $AKTIF, $STOK_FIYATI, $DOVIZ_KULLAN, $DOVIZ_BIRIMI, $MUHKODU_ALIS, $MUHKODU_SATIS, $ADRESI_1, $ILI_1, $ILCESI_1, $POSTA_KODU_1, $WEB_USER_NAME, $WEB_USER_PASSW, $ULKESI_1, $ADI_SOYADI, $CINSIYETI, $TC_KIMLIK_NO, $PAZ_BLCRKODU, $OZEL_KODU3, $OZELALANTANIM_3, $OZELALANTANIM_27, $DEGISTIRME_TARIHI, $EFATURA_SENARYO, $EFATURA_KULLAN, $ALICI_GRUBU, $EIRSALIYE_KULLAN, $KAYIT_TARIHI, $GRUBU]);

        logToApiLog("$newDate: Güncellenen Cari Kodu: $CARIKODU");
        echo "$newDate: Güncellenen Cari Kodu: $CARIKODU <br>";
    }
    $pdo->commit();
    echo "$newDate: Cari Tarama Tamamlandı. <br>";
}

// Process account transaction list XML data
function getAccountTransactionList($xmlData) {
    global $pdo;
    global $newDate;
    echo "$newDate: Evrak Taraması Başladı. <br>";
    try {
        $xml = new SimpleXMLElement($xmlData);
    } catch (Exception $e) {
        logToApiLog("Failed to parse XML Cari Hareket Liste: " . $e->getMessage());
        return;
    }

    if (!isset($xml->table->row)) {
        logToApiLog("No 'row' elements found in the XML Cari Hareket Liste.");
        echo "$newDate: Evrak taraması tamamlandı.  <br>";
        return;
    }

    $pdo->beginTransaction();
    $stmt = $pdo->prepare("REPLACE INTO uyeler_hareket_deneme (BLKODU, BLCRKODU, EVRAK_NO, TARIHI, VADESI, MUH_DURUM, MUH_HESKODU, DOVIZ_KULLAN, DOVIZ_ALIS, DOVIZ_SATIS, KPBDVZ, DOVIZ_BIRIMI, DOVIZ_HES_ISLE, ACIKLAMA, KASA_ADI, BANKA_ADI, KKARTI_DETAY, ENTEGRASYON, KPB_BTUT, KPB_ATUT, DVZ_BTUT, DVZ_ATUT, DEGISTIRME_TARIHI, ISLEM_TURU, SILINDI) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $BLCRKODU = (string)$row->BLCRKODU;
        $EVRAK_NO = (string)$row->EVRAK_NO;
        $TARIHI = (string)$row->TARIHI;
        $MUH_DURUM = (string)$row->MUH_DURUM;
        $MUH_HESKODU = (string)$row->MUH_HESKODU;
        $DOVIZ_KULLAN = (string)$row->DOVIZ_KULLAN;
        $DOVIZ_ALIS = (string)$row->DOVIZ_ALIS;
        $DOVIZ_SATIS = (string)$row->DOVIZ_SATIS;
        $KPBDVZ = (string)$row->KPBDVZ;
        $DOVIZ_BIRIMI = (string)$row->DOVIZ_BIRIMI;
        $DOVIZ_HES_ISLE = (int)$row->DOVIZ_HES_ISLE;
        $ACIKLAMA = (string)$row->ACIKLAMA;
        $KASA_ADI = (string)$row->KASA_ADI;
        $BANKA_ADI = (string)$row->BANKA_ADI;
        $KKARTI_DETAY = (string)$row->KKARTI_DETAY;
        $ENTEGRASYON = (string)$row->ENTEGRASYON;
        $KPB_BTUT = (string)$row->KPB_BTUT;
        $KPB_ATUT = (string)$row->KPB_ATUT;
        $DVZ_BTUT = (string)$row->DVZ_BTUT;
        $DVZ_ATUT = (string)$row->DVZ_ATUT;
        $ISLEM_TURU = (int)$row->ISLEM_TURU;
        $SILINDI = (int)$row->SILINDI;
        $DEGISTIRME_TARIHI = date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI));
        $VADESI = date('Y-m-d', strtotime((string)$row->VADESI));

        $stmt->execute([$BLKODU, $BLCRKODU, $EVRAK_NO, $TARIHI, $VADESI, $MUH_DURUM, $MUH_HESKODU, $DOVIZ_KULLAN, $DOVIZ_ALIS, $DOVIZ_SATIS, $KPBDVZ, $DOVIZ_BIRIMI, $DOVIZ_HES_ISLE, $ACIKLAMA, $KASA_ADI, $BANKA_ADI, $KKARTI_DETAY, $ENTEGRASYON, $KPB_BTUT, $KPB_ATUT, $DVZ_BTUT, $DVZ_ATUT, $DEGISTIRME_TARIHI, $ISLEM_TURU, $SILINDI]);

        logToApiLog("$newDate: Güncellenen Evrak Numarası: $EVRAK_NO");
        echo "$newDate: Güncellenen Evrak Numarası: $EVRAK_NO <br>";
    }
    $pdo->commit();
    echo "$newDate: Evrak Taraması Tamamlandı. <br>";
}

// Process account balance list XML data
function getAccountBalanceList($xmlData) {
    global $pdo;
    global $newDate;
    echo "$newDate: Hesap Bakiye Taraması Başladı. <br>";
    try {
        $xml = new SimpleXMLElement($xmlData);
    } catch (Exception $e) {
        logToApiLog("Failed to parse XML Cari Bakiye Liste: " . $e->getMessage());
        return;
    }

    if (!isset($xml->table->row)) {
        logToApiLog("No 'row' elements found in the XML Cari Bakiye Liste.");
        echo "$newDate: Hesap Bakiye Taraması Tamamlandı. <br>";
        return;
    }

    $pdo->beginTransaction();
    $stmt = $pdo->prepare("REPLACE INTO cari_bakiye (HESAP, TPL_BRC, TPL_ALC, TPL_BKY, TPL_BTR, DVZ_HESAP, DVZ_TPLBRC, DVZ_TPLALC, DVZ_BAKIYE, DVZ_BTR, BLKODU, CARIKODU) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $HESAP = (string)$row->HESAP;
        $TPL_BRC = (string)$row->TPL_BRC;
        $TPL_ALC = (string)$row->TPL_ALC;
        $TPL_BKY = (string)$row->TPL_BKY;
        $TPL_BTR = (string)$row->TPL_BTR;
        $DVZ_HESAP = (string)$row->DVZ_HESAP;
        $DVZ_TPLBRC = (string)$row->DVZ_TPLBRC;
        $DVZ_TPLALC = (string)$row->DVZ_TPLALC;
        $DVZ_BAKIYE = (string)$row->DVZ_BAKIYE;
        $DVZ_BTR = (string)$row->DVZ_BTR;
        $CARIKODU = (string)$row->CARIKODU;

        $stmt->execute([$HESAP, $TPL_BRC, $TPL_ALC, $TPL_BKY, $TPL_BTR, $DVZ_HESAP, $DVZ_TPLBRC, $DVZ_TPLALC, $DVZ_BAKIYE, $DVZ_BTR, $BLKODU, $CARIKODU]);

        logToApiLog("$newDate: Güncellenen Hesap Bakiyesi: $CARIKODU");
        echo "$newDate: Güncellenen Hesap Bakiyesi: $CARIKODU <br>";
    }
    $pdo->commit();
    echo "$newDate: Hesap Bakiye Taraması Tamamlandı. <br>";
}

// Insert categories from database
function insertCategoriesFromDatabase() {
    global $pdo;
    $query = "SELECT GRUBU, ARA_GRUBU, ALT_GRUBU, OZEL_KODU1 FROM nokta_urunler WHERE DEGISTIRME_TARIHI >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
    $stmt = $pdo->query($query);
    while ($row = $stmt->fetch()) {
        $GRUBU = $row['GRUBU'];
        $ARA_GRUBU = $row['ARA_GRUBU'];
        $ALT_GRUBU = $row['ALT_GRUBU'];
        $OZEL_KODU1 = $row['OZEL_KODU1'];

        $parent_id = 0; // Default parent_id for top level categories
        $grupid = insertCategory($GRUBU, $parent_id, $pdo);

        if (!empty($ARA_GRUBU)) {
            $araid = insertCategory($ARA_GRUBU, $grupid, $pdo);
            if (!empty($ALT_GRUBU)) {
                $altid = insertCategory($ALT_GRUBU, $araid, $pdo);
                if (!empty($OZEL_KODU1)) {
                    insertCategory($OZEL_KODU1, $altid, $pdo);
                }
            }
        }
    }
}

// Insert a single category
function insertCategory($kategori_adi, $parent_id, $pdo) {
    global $newDate;
    $parent_id = (int)$parent_id;
    $seo_link = duzenleString($kategori_adi);

    // Build SEO link from all parent categories
    $parent_categories = array();
    while ($parent_id != 0) {
        $stmt = $pdo->prepare("SELECT kategori_adi, parent_id FROM nokta_kategoriler WHERE id = ?");
        $stmt->execute([$parent_id]);
        $result = $stmt->fetch();
        if ($result) {
            $parent_categories[] = $result['kategori_adi'];
            $parent_id = $result['parent_id'];
        } else {
            break;
        }
    }
    $parent_categories = array_reverse($parent_categories);
    $seo_link = implode('/', array_map('duzenleString', $parent_categories)) . '/' . duzenleString($kategori_adi);

    $stmt = $pdo->prepare("SELECT id FROM nokta_kategoriler WHERE kategori_adi = ? AND parent_id = ?");
    $stmt->execute([$kategori_adi, $parent_id]);
    $result = $stmt->fetch();

    if ($result) {
        // Update existing category
        $stmt = $pdo->prepare("UPDATE nokta_kategoriler SET seo_link = ? WHERE id = ?");
        $stmt->execute([$seo_link, $result['id']]);
        logToApiLog("$newDate: Kategori: $kategori_adi, Parent ID updated to: $parent_id");
        return $result['id'];
    } else {
        // Insert new category
        $stmt = $pdo->prepare("INSERT INTO nokta_kategoriler (kategori_adi, parent_id, seo_link) VALUES (?, ?, ?)");
        $stmt->execute([$kategori_adi, $parent_id, $seo_link]);
        $new_category_id = $pdo->lastInsertId();
        logToApiLog("$newDate: Kategori: $kategori_adi, Parent ID set to: $parent_id");
        return $new_category_id;
    }
}

// Update KategoriID for all products
function updateKategoriIDForAllProducts() {
    global $pdo;
    global $newDate;
    // Tüm ürünleri seç
    $selectQuery = "SELECT id, GRUBU, ARA_GRUBU, ALT_GRUBU, OZEL_KODU1 FROM nokta_urunler WHERE DEGISTIRME_TARIHI >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
    $stmt = $pdo->query($selectQuery);
    while ($row = $stmt->fetch()) {
        $urunId = $row['id'];
        $grubu = $row['GRUBU'];
        $araGrubu = $row['ARA_GRUBU'];
        $altGrubu = $row['ALT_GRUBU'];
        $ozelKodu1 = $row['OZEL_KODU1'];
        $kategoriAdlari = array_filter([$grubu, $araGrubu, $altGrubu, $ozelKodu1]);

        $seoLinks = array_map('duzenleString', $kategoriAdlari);
        $seo_link = implode('/', $seoLinks);

        $stmt2 = $pdo->prepare("SELECT id FROM nokta_kategoriler WHERE seo_link = ?");
        $stmt2->execute([$seo_link]);
        $result = $stmt2->fetch();

        if ($result) {
            $kategoriId = $result['id'];
            $stmt3 = $pdo->prepare("UPDATE nokta_urunler SET KategoriID = ? WHERE id = ?");
            $stmt3->execute([$kategoriId, $urunId]);
            logToApiLog("$newDate; KategoriID güncellendi. Urun ID: $urunId, KategoriID: $kategoriId");
        } else {
            logToApiLog("$newDate; Kategori bulunamadı. Urun ID: $urunId");
        }
    }
}

// Send invoices
function faturalariGonder() {
    global $newDate;
    $files = array_diff(scandir("assets/faturalar/"), ['.', '..']);
    if (empty($files)) {
        logToApiLog("$newDate: XML dosyaları bulunamadı.");
        echo "$newDate: XML dosyaları bulunamadı <br>";
        return;
    }

    $jsonResult = [];
    foreach ($files as $file) {
        $xmlData = file_get_contents("assets/faturalar/$file");
        $jsonResult[$file] = $xmlData;
        logToApiLog("$newDate: Yeni Sipariş $file gönderildi.");
    }
    echo json_encode($jsonResult);

    // Faturalar klasöründeki dosyaları sil
    foreach ($files as $file) {
        unlink("assets/faturalar/$file");
    }
}

// Send payments
function odemeGonder() {
    global $newDate;
    $files = array_diff(scandir("assets/carihareket/"), ['.', '..']);
    if (empty($files)) {
        logToApiLog("$newDate: XML dosyaları bulunamadı.");
        echo "$newDate: XML dosyaları bulunamadı <br>";
        return;
    }

    $jsonResult = [];
    foreach ($files as $file) {
        $xmlData = file_get_contents("assets/carihareket/$file");
        $jsonResult[$file] = $xmlData;
        logToApiLog("$newDate: Yeni Cari Hareket $file gönderildi.");
    }
    echo json_encode($jsonResult);

    // Faturalar klasöründeki dosyaları sil
    foreach ($files as $file) {
        unlink("assets/carihareket/$file");
    }
}

// Send account data
function cariGonder() {
    global $newDate;
    $files = array_diff(scandir("assets/cari/"), ['.', '..']);
    if (empty($files)) {
        logToApiLog("$newDate: XML dosyaları bulunamadı.");
        echo "$newDate: XML dosyaları bulunamadı <br>";
        return;
    }

    $jsonResult = [];
    foreach ($files as $file) {
        $xmlData = file_get_contents("assets/cari/$file");
        $jsonResult[$file] = $xmlData;
        logToApiLog("$newDate: Yeni Cari $file gönderildi.");
    }
    echo json_encode($jsonResult);

    // Faturalar klasöründeki dosyaları sil
    foreach ($files as $file) {
        unlink("assets/cari/$file");
    }
}

// Update BLKODU for an account
function cariBLKODU($gelenveri) {
    if (preg_match('/MBLKODU=(\d+)/', $gelenveri, $matches)) {
        $blkodu = $matches[1];
        global $pdo;
        $stmt = $pdo->prepare("UPDATE uyeler SET BLKODU = ? WHERE id = (SELECT MAX(id) FROM uyeler)");
        $stmt->execute([$blkodu]);
    } else {
        echo "MBLKODU bulunamadı.";
    }
}

// Update account data from XML
function cariGonderUpdate($xmlData) {
    global $newDate;
    global $pdo;
    $date = date("Y-m-d H:i:s", strtotime("-24 hour"));
    try {
        $xml = new SimpleXMLElement($xmlData);
    } catch (Exception $e) {
        logToApiLog("Failed to parse XML Cari Güncelle Liste: " . $e->getMessage());
        return;
    }

    if (!isset($xml->table->row)) {
        logToApiLog("No 'row' elements found in the XML Cari Güncelle Liste.");
        return;
    }

    $dataArray = [];
    foreach ($xml->table->row as $row) {
        $dataArray[] = ["BLKODU" => (int)$row->BLKODU, "DEGISTIRME_TARIHI" => date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI))];
    }

    $stmt = $pdo->prepare("SELECT BLKODU, DEGISTIRME_TARIHI FROM uyeler WHERE DEGISTIRME_TARIHI >= ?");
    $stmt->execute([$date]);
    $unmatchedBLKODUs = [];
    while ($row = $stmt->fetch()) {
        $found = false;
        foreach ($dataArray as $data) {
            if ($data["BLKODU"] === (int)$row['BLKODU'] && $data["DEGISTIRME_TARIHI"] === $row['DEGISTIRME_TARIHI']) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $unmatchedBLKODUs[] = $row['BLKODU'];
        }
    }

    foreach ($unmatchedBLKODUs as $gelsinBL) {
        $stmt = $pdo->prepare("SELECT * FROM uyeler WHERE BLKODU = ?");
        $stmt->execute([$gelsinBL]);
        $row = $stmt->fetch();

        if ($row) {
            $il = $row['il'];
            $ilce = $row['ilce'];
            $stmt1 = $pdo->prepare("SELECT il_adi FROM iller WHERE il_id = ?");
            $stmt1->execute([$il]);
            $il_adi = $stmt1->fetchColumn();

            $stmt2 = $pdo->prepare("SELECT ilce_adi FROM ilceler WHERE ilce_id = ? AND il_id = ?");
            $stmt2->execute([$ilce, $il]);
            $ilce_adi = $stmt2->fetchColumn();

            $stmt3 = $pdo->prepare("SELECT CONCAT(kullanici_ad, ' ', kullanici_soyad) AS satis_temsilcisi FROM kullanicilar WHERE id = ?");
            $stmt3->execute([$row["satis_temsilcisi"]]);
            $satis_temsilcisi = $stmt3->fetchColumn();

            $xmlDoc = new DOMDocument('1.0', 'UTF-8');
            $xmlDoc->formatOutput = true;
            $root = $xmlDoc->createElement('WCR');
            $xmlDoc->appendChild($root);

            // AYAR ALANI
            $ayar = $xmlDoc->createElement('AYAR');
            $root->appendChild($ayar);
            $ayarElements = [
                'TRSVER' => 'ASWCR1.02.03',
                'DBNAME' => 'WOLVOX',
                'PERSUSER' => 'sa',
                'SUBE_KODU' => '3402'
            ];
            foreach ($ayarElements as $tag => $value) {
                $element = $xmlDoc->createElement($tag);
                $element->appendChild($xmlDoc->createCDATASection($value));
                $ayar->appendChild($element);
            }

            // CARI BILGI ALANI
            $cari = $xmlDoc->createElement('CARI');
            $root->appendChild($cari);
            $cariElements = [
                'BLKODU' => $row['BLKODU'],
                'CARIKODU' => $row['muhasebe_kodu'],
                'OZEL_KODU1' => 'B2B',
                'OZEL_KODU2' => $satis_temsilcisi,
                'OZEL_KODU3' => $row['uye_tipi'],
                'MUHKODU_ALIS' => $row['MUHKODU_ALIS'],
                'MUHKODU_SATIS' => $row['MUHKODU_SATIS'],
                'STOK_FIYATI' => $row['fiyat'],
                'PAZ_BLCRKODU' => $row['satis_temsilcisi'],
                'ADI' => $row['ad'],
                'SOYADI' => $row['soyad'],
                'E_MAIL' => $row['email'],
                'WEB_USER_NAME' => $row['email'],
                'WEB_USER_PASSW' => $row['parola'],
                'TC_KIMLIK_NO' => $row['tc_no'],
                'ILI_1' => $il_adi,
                'ILCESI_1' => $ilce_adi,
                'POSTA_KODU_1' => $row['posta_kodu'],
                'CEP_TEL' => $row['tel'],
                'ADRESI_1' => $row['adres'],
                'TICARI_UNVANI' => $row['firmaUnvani'],
                'VERGI_NO' => $row['vergi_no'],
                'VERGI_DAIRESI' => $row['vergi_dairesi'],
                'TEL1' => $row['sabit_tel']
            ];
            foreach ($cariElements as $tag => $value) {
                $element = $xmlDoc->createElement($tag);
                $element->appendChild($xmlDoc->createCDATASection($value ?? ''));
                $cari->appendChild($element);
            }
            $xmlFileName = 'cari_guncelle_' . $row['BLKODU'] . '.xml';
            $xmlDoc->save('assets/cari_guncelle/' . $xmlFileName);
        }
    }

    $files = array_diff(scandir("assets/cari_guncelle/"), ['.', '..']);
    if (empty($files)) {
        logToApiLog("$newDate: XML dosyaları bulunamadı.");
        echo "$newDate: XML dosyaları bulunamadı <br>";
        return;
    }

    $jsonResult = [];
    foreach ($files as $file) {
        $xmlData = file_get_contents("assets/cari_guncelle/$file");
        $jsonResult[$file] = $xmlData;
        logToApiLog("$newDate: Güncellenen Cari $file gönderildi.");
    }
    echo json_encode($jsonResult);

    // Faturalar klasöründeki dosyaları sil
    foreach ($files as $file) {
        unlink("assets/cari_guncelle/$file");
    }
}

// Main process for handling incoming XML data
$xml_data_stock_inventory = $_POST['xml_data_stok_envanter'] ?? '';
$xml_data_stock = $_POST['xml_data_stok_adet'] ?? '';
$xml_data_stock_list = $_POST['xml_data_stok_liste'] ?? '';
$xml_data_account_list = $_POST['xml_data_cari_liste'] ?? '';
$xml_data_account_transaction_list = $_POST['xml_data_cari_hareket_liste'] ?? '';
$xml_data_account_balance_list = $_POST['xml_data_cari_bakiye_liste'] ?? '';
$xml_data_kategori = $_POST['xml_data_kategori'] ?? '';
$xml_data_marka = $_POST['xml_data_marka'] ?? '';
$xml_siparis_gonder = $_POST['xml_siparis_gonder'] ?? '';
$xml_odeme_sorgula = $_POST['xml_odeme_sorgula'] ?? '';
$xml_cari_gonder = $_POST['xml_cari_gonder'] ?? '';
$xml_cari_blkodu = $_POST['xml_cari_blkodu'] ?? '';
$xml_cari_gonder_guncelle = $_POST['xml_cari_gonder_guncelle'] ?? '';

if (!empty($xml_data_stock_inventory)) {
    insertCategoriesFromDatabase();
    updateKategoriIDForAllProducts();
    getStockInventory($xml_data_stock_inventory);
} elseif (!empty($xml_data_stock_list)) { getStockList($xml_data_stock_list);
} elseif (!empty($xml_data_account_list)) { getAccountList($xml_data_account_list);
} elseif (!empty($xml_data_account_transaction_list)) { getAccountTransactionList($xml_data_account_transaction_list);
} elseif (!empty($xml_data_account_balance_list)) { getAccountBalanceList($xml_data_account_balance_list);
} elseif (!empty($xml_siparis_gonder)) { faturalariGonder();
} elseif (!empty($xml_odeme_sorgula)) { odemeGonder();
} elseif (!empty($xml_cari_gonder)) { cariGonder();
} elseif (!empty($xml_cari_blkodu)) { cariBLKODU($xml_cari_blkodu);
} elseif (!empty($xml_cari_gonder_guncelle)) { cariGonderUpdate($xml_cari_gonder_guncelle);
} elseif (!empty($xml_data_stock)) { stokMiktar($xml_data_stock); }
?>
