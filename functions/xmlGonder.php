<?php
echo "<br>xmlgonder.php icerisindesin  <br>";
define("DB_SERVER", "noktanetdb.cbuq6a2265j6.eu-central-1.rds.amazonaws.com");
define("DB_USERNAME", "noktanetdb");
define("DB_PASSWORD", "Dell28736.!");
define("DB_NAME", "nokta");
$newDate = date('Y-m-d H:i:s', strtotime('+3 hours'));
// mb_convert_encoding() kullanarak Türkçe karakterleri düzgün şekilde dönüştürmek
function duzenleString($str) {
    // Diğer işlemleri uygula
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
    $str = strtolower($str);// Büyük harfleri küçük harfe çevir
    $str = trim($str);// Başındaki ve sonundaki boşlukları sil
    $str = preg_replace('/\s+/', '-', $str); // Ortadaki boşlukları - ile değiştir
    $str = str_replace( ['---','--'], ['-','-'], $str );
    return $str;
}
function gelenFiyatDuzenle($sayi) {
    if (empty($sayi)) {
        return null;
    }
    // Virgül varsa noktaya çevir
    $sayi = str_replace(',', '.', $sayi);
    // Sayının formatını kontrol et
    if (!preg_match('/^\d+(\.\d{1,4})?$/', $sayi)) {
        return null;
    }
    // Sayıyı DECIMAL(13,4) formatına getir
    $sayi = number_format((float)$sayi, 4, '.', '');
    return $sayi;
}
function connectToDatabase() {
    $mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_error) {
        echo "Connection failed: " . $mysqli->connect_error;
    }
    $mysqli->set_charset("utf8");
    return $mysqli;
}
function connectToDatabasePDO() {
    $host = 'noktanetdb.cbuq6a2265j6.eu-central-1.rds.amazonaws.com'; // Veritabanı sunucusu
    $dbname = 'noktanetdb'; // Veritabanı adı
    $username = 'nokta'; // Veritabanı kullanıcı adı
    $password = 'Dell28736.!'; // Veritabanı şifresi
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo "Veritabanı bağlantısı başarısız: " . $e->getMessage();
        die();
    }
}
function getStockInventory($xmlData) {
    updateKategoriIDForAllProducts();
    $mysqli = connectToDatabase();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo '<br>' . $newDate . ': Stok Tarama Başladı.<br>';
    if ($xml === false) {
        echo"Failed to parse XML Stok Envanter ENVANTER.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo $newDate . ': Stok Tarama Tamamlandı.</br>';
        return;
    }
    function getMarkaIdByTitle($title, $mysqli) {
        // 'title' alanında arama yaparak 'id'yi al
        $selectQuery = "SELECT id FROM nokta_urun_markalar_1 WHERE title = ?";
        $statement = $mysqli->prepare($selectQuery);
        $statement->bind_param("s", $title);
        $statement->execute();
        $result = $statement->get_result()->fetch_assoc();
        $statement->close();
        return ($result) ? $result['id'] : null;
    }
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $STOKKODU = $mysqli->real_escape_string((string)$row->STOKKODU);
        $STOK_ADI = (string)$row->STOK_ADI;
        $STOK_ADI_YD = $mysqli->real_escape_string((string)$row->STOK_ADI_YD);
        $ARA_GRUBU = $mysqli->real_escape_string((string)$row->ARA_GRUBU);
        $ALT_GRUBU = $mysqli->real_escape_string((string)$row->ALT_GRUBU);
        $GRUBU = $mysqli->real_escape_string((string)$row->GRUBU);
        $MARKASI = $mysqli->real_escape_string((string)$row->MARKASI);
        $BARKODU = $mysqli->real_escape_string((string)$row->BARKODU);
        $KDV_ORANI = $mysqli->real_escape_string((string)$row->KDV_ORANI);
        $KDV_ORANI_SATIS_TPT = $mysqli->real_escape_string((string)$row->KDV_ORANI_SATIS_TPT);
        $ACIKLAMA1 = $mysqli->real_escape_string((string)$row->ACIKLAMA1);
        $ACIKLAMA2 = $mysqli->real_escape_string((string)$row->ACIKLAMA2);
        $ACIKLAMA3 = $mysqli->real_escape_string((string)$row->ACIKLAMA3);
        $OZEL_KODU1 = $mysqli->real_escape_string((string)$row->OZEL_KODU1);
        $OZELALANTANIM_18 = $mysqli->real_escape_string((string)$row->OZELALANTANIM_18);
        $BIRIMLER = $mysqli->real_escape_string((string)$row->BIRIMLER);
        $BIRIMI = $mysqli->real_escape_string((string)$row->BIRIMI);
        $MIKTAR_KULBILIR = $mysqli->real_escape_string((string)$row->MIKAR_KALAN);
        $WEBDE_GORUNSUN = (int)$row->WEBDE_GORUNSUN;
        $DEGISTIRME_TARIHI = date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI));
        $AKTIF = $WEBDE_GORUNSUN;
        $proje = 0;
        $markaId = getMarkaIdByTitle($MARKASI, $mysqli);
        $checkQuery = "SELECT * FROM nokta_urunler WHERE BLKODU = ?";
        $stmt = $mysqli->prepare($checkQuery);
        $stmt->bind_param("i", $BLKODU);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $stmt->close();
        if ($checkResult->num_rows > 0) {
            $existingRow = $checkResult->fetch_assoc();
            $existingDegistirmeTarihi = $existingRow['DEGISTIRME_TARIHI'];
            $suan = date('Y-m-d H:i:s');
            $suan = date('Y-m-d H:i:s', strtotime($suan . ' +3 hours'));
            $currentTimestamp = strtotime($suan);
            $degistirmeTimestamp = strtotime($DEGISTIRME_TARIHI);
            $timeDifference = $currentTimestamp - $degistirmeTimestamp;

            if ($existingDegistirmeTarihi != $DEGISTIRME_TARIHI || ($timeDifference <= 60)) {
                $sGRUBU = duzenleString($GRUBU);
                $sARA_GRUBU = duzenleString($ARA_GRUBU);
                $sALT_GRUBU = duzenleString($ALT_GRUBU);
                $sOZEL_KODU1 = duzenleString($OZEL_KODU1);
                $sSTOK_ADI = duzenleString($STOK_ADI);
                $sSTOKKODU = duzenleString($STOKKODU);
                $seoLink = $sGRUBU .'/'. ($sARA_GRUBU ? $sARA_GRUBU .'/' : '') . ($sALT_GRUBU ? $sALT_GRUBU .'/' : '') . ($sOZEL_KODU1 ? $sOZEL_KODU1 .'/': '') . $sSTOK_ADI.'-'.$sSTOKKODU;
                $updateQuery = "UPDATE nokta_urunler 
                                SET seo_link = ? ,MarkaID = ? ,UrunKodu = ?, UrunAdiTR = ?, UrunAdiEN = ?, ARA_GRUBU = ?, ALT_GRUBU = ?, GRUBU = ?, MARKASI = ?, barkod = ?, kdv = ?, KDV_ORANI_SATIS_TPT = ?,
                                ACIKLAMA1 = ?, ACIKLAMA2 = ?, ACIKLAMA3 = ?, OZEL_KODU1 = ?, OZELALANTANIM_18 = ?, BIRIMLER = ?, BIRIMI = ?, MIKTAR_KULBILIR = ?, WEBDE_GORUNSUN = ?, DEGISTIRME_TARIHI = ?,stok = ?, aktif = ?
                                WHERE BLKODU = ?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("sissssssssssssssssssissii", $seoLink, $markaId, $STOKKODU, $STOK_ADI, $STOK_ADI_YD, $ARA_GRUBU, $ALT_GRUBU, $GRUBU, $MARKASI, $BARKODU, $KDV_ORANI, $KDV_ORANI_SATIS_TPT, $ACIKLAMA1, $ACIKLAMA2,
                    $ACIKLAMA3, $OZEL_KODU1, $OZELALANTANIM_18, $BIRIMLER, $BIRIMI, $MIKTAR_KULBILIR, $WEBDE_GORUNSUN, $DEGISTIRME_TARIHI, $MIKTAR_KULBILIR, $AKTIF,$BLKODU);
                $stmt->execute();
                $stmt->close();
                echo "$newDate: Güncellenen stok kodu: $STOKKODU <br>";
            }
        } else {
            if($WEBDE_GORUNSUN == 1){
                $sGRUBU = duzenleString($GRUBU);
                $sARA_GRUBU = duzenleString($ARA_GRUBU);
                $sALT_GRUBU = duzenleString($ALT_GRUBU);
                $sOZEL_KODU1 = duzenleString($OZEL_KODU1);
                $sSTOK_ADI = duzenleString($STOK_ADI);
                $sSTOKKODU = duzenleString($STOKKODU);
                $seoLink = $sGRUBU .'/'. ($sARA_GRUBU ? $sARA_GRUBU .'/' : '') . ($sALT_GRUBU ? $sALT_GRUBU .'/' : '') . ($sOZEL_KODU1 ? $sOZEL_KODU1 .'/': '') . $sSTOK_ADI.'-'.$sSTOKKODU;
                $insertQuery = "INSERT INTO nokta_urunler (BLKODU, UrunKodu, UrunAdiTR, UrunAdiEN, ARA_GRUBU, ALT_GRUBU, GRUBU, MARKASI, barkod, kdv, KDV_ORANI_SATIS_TPT, ACIKLAMA1, ACIKLAMA2, ACIKLAMA3, OZEL_KODU1,
                OZELALANTANIM_18, BIRIMLER, BIRIMI, MIKTAR_KULBILIR, WEBDE_GORUNSUN, DEGISTIRME_TARIHI, MarkaID, stok, seo_link, aktif,proje)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($insertQuery);
                $stmt->bind_param("issssssssssssssssssissssii", $BLKODU, $STOKKODU, $STOK_ADI, $STOK_ADI_YD, $ARA_GRUBU, $ALT_GRUBU,
                    $GRUBU, $MARKASI, $BARKODU, $KDV_ORANI, $KDV_ORANI_SATIS_TPT, $ACIKLAMA1, $ACIKLAMA2, $ACIKLAMA3,
                    $OZEL_KODU1, $OZELALANTANIM_18, $BIRIMLER, $BIRIMI, $MIKTAR_KULBILIR, $WEBDE_GORUNSUN, $DEGISTIRME_TARIHI, $markaId, $MIKTAR_KULBILIR, $seoLink, $AKTIF, $proje);
                $stmt->execute();
                $stmt->close();
                // Sitemap.xml dosyasını yükle
                $sitemapPath = "sitemap.xml";
                $sitemapXml = simplexml_load_file($sitemapPath);
                // Üst öğeyi oluştur (Eğer yoksa)
                if ($sitemapXml === false) {
                    $sitemapXml = new SimpleXMLElement('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
                }
                // Sitemap e ekle
                $newProdUrl = "https://www.denemeb2b.noktaelektronik.net/urunler/" . $seoLink;
                $newProdElement = $sitemapXml->addChild('url');
                $newProdElement->addChild('loc', $newProdUrl);
                $newProdElement->addChild('lastmod', date('Y-m-d'));
                $newProdElement->addChild('changefreq', 'daily');
                $newProdElement->addChild('priority', '0.8');
                // Sitemap dosyasını kaydet
                $sitemapXml->asXML($sitemapPath);
                echo "$newDate: Yeni Kayıt Eklendi. Stok Kodu: $STOKKODU <br>";
            }
        }
    }
    $mysqli->close();
    echo "$newDate: Stok Tarama Tamamlandı. <br>";
}
function stokMiktar($xmlData) {
    $pdo = connectToDatabasePDO();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo '<br>' . $newDate . ': Stok Miktar Tarama Başladı.<br>';
    if ($xml === false) {
        echo "Failed to parse XML Stok Envanter.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo $newDate . ': Stok Miktar Tarama Tamamlandı.</br>';
        return;
    }
    try {
        // Build the update query with multiple value sets
        $updateQuery = "UPDATE nokta_urunler SET MIKTAR_KULBILIR = CASE BLKODU ";
        $valueSets = array();
        foreach ($xml->table->row as $row) {
            $BLKODU = (int)$row->BLKODU;
            $MIKTAR_KULBILIR = (int)$row->MIKTAR_KULBILIR;
            $MIKTAR_TERMIN = (int)$row->MIKTAR_TERMIN;
            $toplamMiktar = $MIKTAR_KULBILIR - $MIKTAR_TERMIN;
            $valueSets[] = "WHEN $BLKODU THEN '$toplamMiktar'";
        }
        $updateQuery .= implode(' ', $valueSets);
        $updateQuery .= " END, stok = CASE BLKODU ";
        $valueSets = array();
        foreach ($xml->table->row as $row) {
            $BLKODU = (int)$row->BLKODU;
            $MIKTAR_KULBILIR = (int)$row->MIKTAR_KULBILIR;
            $MIKTAR_TERMIN = (int)$row->MIKTAR_TERMIN;
            $toplamMiktar = $MIKTAR_KULBILIR - $MIKTAR_TERMIN;
            $valueSets[] = "WHEN $BLKODU THEN '$toplamMiktar'";
        }
        $updateQuery .= implode(' ', $valueSets);
        $updateQuery .= " END WHERE BLKODU IN (";
        $BLKODUs = array();
        foreach ($xml->table->row as $row) {
            $BLKODUs[] = (int)$row->BLKODU;
        }
        $updateQuery .= implode(',', $BLKODUs) . ")";
        // Execute the update query
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute();
        echo "$newDate: Stok Miktar Tarama Tamamlandı. <br>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
function getStockList($xmlData) {
    $mysqli = connectToDatabase();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo "$newDate: Fiyat Tarama Başladı. <br>";
    if ($xml === false) {
        echo "Failed to parse XML Fiyat Liste.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo "$newDate: Fiyat Tarama Tamamlandı.<br>";
        return;
    }
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $STOKKODU = $mysqli->real_escape_string((string)$row->STOKKODU);
        $DOVIZ_KULLAN = $mysqli->real_escape_string((string)$row->DOVIZ_KULLAN);
        $DOVIZ_BIRIMI = $mysqli->real_escape_string((string)$row->DOVIZ_BIRIMI);
        $KSF1 = $mysqli->real_escape_string((string)$row->KSF1);
        $KSF2 = $mysqli->real_escape_string((string)$row->KSF2);
        $KSF3 = $mysqli->real_escape_string((string)$row->KSF3);
        $KSF4 = $mysqli->real_escape_string((string)$row->KSF4);
        $DSF1 = $mysqli->real_escape_string((string)$row->DSF1);
        $DSF2 = $mysqli->real_escape_string((string)$row->DSF2);
        $DSF3 = $mysqli->real_escape_string((string)$row->DSF3);
        $DSF4 = $mysqli->real_escape_string((string)$row->DSF4);
        $KSF1 = gelenFiyatDuzenle($KSF1);
        $KSF2 = gelenFiyatDuzenle($KSF2);
        $KSF3 = gelenFiyatDuzenle($KSF3);
        $KSF4 = gelenFiyatDuzenle($KSF4);
        $DSF1 = gelenFiyatDuzenle($DSF1);
        $DSF2 = gelenFiyatDuzenle($DSF2);
        $DSF3 = gelenFiyatDuzenle($DSF3);
        $DSF4 = gelenFiyatDuzenle($DSF4);
        $DEGISTIRME_TARIHI = date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI));
        $checkQuery = "SELECT * FROM nokta_urunler WHERE BLKODU = ?";
        $stmt = $mysqli->prepare($checkQuery);
        $stmt->bind_param("i", $BLKODU);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $stmt->close();
        if ($checkResult->num_rows > 0) {
            $existingRow = $checkResult->fetch_assoc();
            $existingDegistirmeTarihi = $existingRow['DEGISTIRME_TARIHI'];
            if ($existingDegistirmeTarihi != $DEGISTIRME_TARIHI) {
                $updateQuery = "UPDATE nokta_urunler 
                                SET DOVIZ_KULLAN = ?, DOVIZ_BIRIMI = ?, KSF1 = ?, KSF2 = ?, KSF3 = ?,KSF4 = ?, DSF1 = ?, DSF2 = ?, DSF3 = ?, DSF4 = ?, DEGISTIRME_TARIHI = ?
                                WHERE BLKODU = ?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("sssssssssssi", $DOVIZ_KULLAN, $DOVIZ_BIRIMI, $KSF1, $KSF2, $KSF3, $KSF4, $DSF1, $DSF2, $DSF3, $DSF4, $DEGISTIRME_TARIHI, $BLKODU);
                $stmt->execute();
                $stmt->close();

                echo "$newDate: Fiyatı güncellenen stok kodu: $STOKKODU <br>";
            }
        }
    }
    $mysqli->close();
    echo "$newDate: Fiyat Tarama Tamamlandı. <br>";
}
function getAccountList($xmlData) {
    $mysqli = connectToDatabase();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo "$newDate: Cari Tarama Başladı. <br>";
    if ($xml === false) {
        echo "Failed to parse XML Cari Liste.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo "$newDate: Cari Tarama Tamamlandı. <br>";
        return;
    }
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $CARIKODU = $mysqli->real_escape_string((string)$row-> CARIKODU);
        $ADI = $mysqli->real_escape_string((string)$row-> ADI);
        $SOYADI = $mysqli->real_escape_string((string)$row-> SOYADI);
        $TICARI_UNVANI = $mysqli->real_escape_string((string)$row-> TICARI_UNVANI);
        $VERGI_DAIRESI = $mysqli->real_escape_string((string)$row-> VERGI_DAIRESI);
        $VERGI_NO = $mysqli->real_escape_string((string)$row-> VERGI_NO);
        $CEP_TEL = $mysqli->real_escape_string((string)$row-> CEP_TEL);
        $AKTIF = $mysqli->real_escape_string((string)$row-> AKTIF);
        $STOK_FIYATI = $mysqli->real_escape_string((string)$row-> STOK_FIYATI);
        $DOVIZ_KULLAN = $mysqli->real_escape_string((string)$row-> DOVIZ_KULLAN);
        $DOVIZ_BIRIMI = $mysqli->real_escape_string((string)$row-> DOVIZ_BIRIMI);
        $MUHKODU_ALIS = $mysqli->real_escape_string((string)$row-> MUHKODU_ALIS);
        $MUHKODU_SATIS = $mysqli->real_escape_string((string)$row-> MUHKODU_SATIS);
        $ADRESI_1 = $mysqli->real_escape_string((string)$row-> ADRESI_1);
        $ILI_1 = $mysqli->real_escape_string((string)$row-> ILI_1);
        $ILCESI_1 = $mysqli->real_escape_string((string)$row-> ILCESI_1);
        $POSTA_KODU_1 = $mysqli->real_escape_string((string)$row-> POSTA_KODU_1);
        $WEB_USER_NAME = $mysqli->real_escape_string((string)$row-> WEB_USER_NAME);
        $WEB_USER_PASSW = $mysqli->real_escape_string((string)$row-> WEB_USER_PASSW);
        $ULKESI_1 = $mysqli->real_escape_string((string)$row-> ULKESI_1);
        $ADI_SOYADI = $mysqli->real_escape_string((string)$row-> ADI_SOYADI);
        $CINSIYETI = $mysqli->real_escape_string((string)$row-> CINSIYETI);
        $TC_KIMLIK_NO = $mysqli->real_escape_string((string)$row-> TC_KIMLIK_NO);
        $PAZ_BLCRKODU = $mysqli->real_escape_string((string)$row-> PAZ_BLCRKODU);
        $OZEL_KODU3 = $mysqli->real_escape_string((string)$row-> OZEL_KODU3);
        $OZELALANTANIM_3 = $mysqli->real_escape_string((string)$row-> OZELALANTANIM_3);
        $OZELALANTANIM_27 = $mysqli->real_escape_string((string)$row-> OZELALANTANIM_27);
        $EFATURA_SENARYO = $mysqli->real_escape_string((string)$row-> EFATURA_SENARYO);
        $EFATURA_KULLAN = $mysqli->real_escape_string((string)$row-> EFATURA_KULLAN);
        $ALICI_GRUBU = $mysqli->real_escape_string((string)$row-> ALICI_GRUBU);
        $EIRSALIYE_KULLAN = $mysqli->real_escape_string((string)$row-> EIRSALIYE_KULLAN);
        $GRUBU = $mysqli->real_escape_string((string)$row-> GRUBU);
        $DEGISTIRME_TARIHI = date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI));
        $currentDateTime = date("Y-m-d H:i:s");
        $KAYIT_TARIHI = date("Y-m-d H:i:s", strtotime($currentDateTime . " +3 hours"));

        $checkQueryIl = "SELECT * FROM iller WHERE il_adi = ?";
        $checkStatementIl = $mysqli->prepare($checkQueryIl);
        $checkStatementIl->bind_param("s", $ILI_1);
        $checkStatementIl->execute();
        $resultIl = $checkStatementIl->get_result();
        $checkStatementIl->close();
        $resultIlRow = $resultIl->fetch_assoc();
            if ($resultIl && $resultIl->num_rows > 0){
                $il = $resultIlRow["il_id"];
            }else {
                $il = "";
            }
        $checkQueryIlce = "SELECT * FROM ilceler WHERE ilce_adi = ? AND il_adi = ?";
        $checkStatementIlce = $mysqli->prepare($checkQueryIlce);
        $checkStatementIlce->bind_param("ss", $ILCESI_1, $ILI_1);
        $checkStatementIlce->execute();
        $resultIlce = $checkStatementIlce->get_result();
        $checkStatementIlce->close();
        $resultIlceRow = $resultIlce->fetch_assoc();
            if ($resultIlce && $resultIlce->num_rows > 0) {
                $ilce = $resultIlceRow["ilce_id"];
            } else {
                $ilce = ""; // Set $il to empty if there is no match
            }
        $aktif = 1;
        $checkQuery = "SELECT * FROM uyeler WHERE BLKODU = ?";
        $stmt = $mysqli->prepare($checkQuery);
        $stmt->bind_param("i", $BLKODU);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $stmt->close();
        if ($checkResult->num_rows > 0) {
            $existingRow = $checkResult->fetch_assoc();
            $existingDegistirmeTarihi = $existingRow['DEGISTIRME_TARIHI'];
            $uyeid = $existingRow["id"];
            if ($existingDegistirmeTarihi != $DEGISTIRME_TARIHI) {
                $updateQuery = "UPDATE uyeler 
                                SET muhasebe_kodu = ?, ad = ?, soyad = ?, firmaUnvani = ?, vergi_dairesi = ?, vergi_no = ?, tel = ?, aktif = ?, fiyat = ?, DOVIZ_KULLAN = ?,
                                    DOVIZ_BIRIMI = ?, MUHKODU_ALIS = ?, MUHKODU_SATIS = ?, adres = ?, il = ?, ilce = ?, posta_kodu = ?, email = ?, ulke = ?, ADI_SOYADI = ?,
                                    cinsiyet = ?, tc_no = ?, satis_temsilcisi = ?, uye_tipi = ?, OZELALANTANIM_3 = ?, OZELALANTANIM_27 = ?, DEGISTIRME_TARIHI = ?, EFATURA_SENARYO = ?,
                                    EFATURA_KULLAN = ?, ALICI_GRUBU = ?, EIRSALIYE_KULLAN = ?, GRUBU = ? WHERE BLKODU = ?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("ssssssssssssssssssssssssssssssssi", $CARIKODU, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $AKTIF, $STOK_FIYATI,
                    $DOVIZ_KULLAN, $DOVIZ_BIRIMI, $MUHKODU_ALIS, $MUHKODU_SATIS, $ADRESI_1, $il, $ilce, $POSTA_KODU_1, $WEB_USER_NAME, $ULKESI_1,
                    $ADI_SOYADI, $CINSIYETI, $TC_KIMLIK_NO, $PAZ_BLCRKODU, $OZEL_KODU3, $OZELALANTANIM_3, $OZELALANTANIM_27, $DEGISTIRME_TARIHI,
                    $EFATURA_SENARYO, $EFATURA_KULLAN, $ALICI_GRUBU, $EIRSALIYE_KULLAN, $GRUBU, $BLKODU);
                $stmt->execute();
                $stmt->close();

                // Check if the record already exists in adresler table
                $checkAddressQuery = "SELECT * FROM adresler WHERE uye_id = ?";
                $stmt = $mysqli->prepare($checkAddressQuery);
                $stmt->bind_param("i", $uyeid);
                $stmt->execute();
                $checkAddressResult = $stmt->get_result();
                $adres_turu = "teslimat";

                if ($checkAddressResult->num_rows > 0) {
                    // Update the existing record in adresler table
                    $updateAddressQuery = "UPDATE adresler SET ad = ?, soyad = ?, adres_turu = ?, firma_adi = ?, il = ?, ilce = ?, vergi_dairesi = ?, vergi_no = ?, telefon = ?, aktif = ?, adres = ?, posta_kodu = ?, ulke = ?, tc_no = ? WHERE uye_id = ?";
                    $stmt = $mysqli->prepare($updateAddressQuery);
                    $stmt->bind_param("ssssiisssissssi", $ADI, $SOYADI, $adres_turu, $TICARI_UNVANI, $il, $ilce, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $aktif, $ADRESI_1, $POSTA_KODU_1, $ULKESI_1, $TC_KIMLIK_NO, $uyeid);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $insertAddressQuery = "INSERT INTO adresler (uye_id, adres_turu, il, ilce, ad, soyad, firma_adi, vergi_dairesi, vergi_no, telefon, aktif, adres, posta_kodu, ulke, tc_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($insertAddressQuery);
                    $stmt->bind_param("isiissssssissss", $uyeid, $adres_turu, $il, $ilce, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $aktif, $ADRESI_1, $POSTA_KODU_1, $ULKESI_1, $TC_KIMLIK_NO);
                    $stmt->execute();
                    $stmt->close();
                }
                echo "$newDate: Güncellenen Cari Kodu: $CARIKODU <br>";
            }
        } else {
                $checkQuery = "SELECT * FROM uyeler WHERE muhasebe_kodu = ?";
                $stmt = $mysqli->prepare($checkQuery);
                $stmt->bind_param("s", $CARIKODU);
                $stmt->execute();
                $checkResult = $stmt->get_result();
                $stmt->close();
            if ($checkResult->num_rows > 0) {
                $existingRow = $checkResult->fetch_assoc();
                $existingDegistirmeTarihi = $existingRow['DEGISTIRME_TARIHI'];
                $uyeid = $existingRow["id"];
                if ($existingDegistirmeTarihi != $DEGISTIRME_TARIHI) {
                    $updateQuery = "UPDATE uyeler 
                                SET BLKODU = ?, ad = ?, soyad = ?, firmaUnvani = ?, vergi_dairesi = ?, vergi_no = ?, tel = ?, aktif = ?, fiyat = ?, DOVIZ_KULLAN = ?,
                                    DOVIZ_BIRIMI = ?, MUHKODU_ALIS = ?, MUHKODU_SATIS = ?, adres = ?, il = ?, ilce = ?, posta_kodu = ?, email = ?,  ulke = ?, ADI_SOYADI = ?,
                                    cinsiyet = ?, tc_no = ?, satis_temsilcisi = ?, uye_tipi = ?, OZELALANTANIM_3 = ?, OZELALANTANIM_27 = ?, DEGISTIRME_TARIHI = ?, EFATURA_SENARYO = ?,
                                    EFATURA_KULLAN = ?, ALICI_GRUBU = ?, EIRSALIYE_KULLAN = ?, GRUBU = ? WHERE muhasebe_kodu = ?";
                    $stmt = $mysqli->prepare($updateQuery);
                    $stmt->bind_param("issssssssssssssssssssssssssssssss", $BLKODU, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $AKTIF, $STOK_FIYATI,
                        $DOVIZ_KULLAN, $DOVIZ_BIRIMI, $MUHKODU_ALIS, $MUHKODU_SATIS, $ADRESI_1, $il, $ilce, $POSTA_KODU_1, $WEB_USER_NAME,  $ULKESI_1,
                        $ADI_SOYADI, $CINSIYETI, $TC_KIMLIK_NO, $PAZ_BLCRKODU, $OZEL_KODU3, $OZELALANTANIM_3, $OZELALANTANIM_27, $DEGISTIRME_TARIHI,
                        $EFATURA_SENARYO, $EFATURA_KULLAN, $ALICI_GRUBU, $EIRSALIYE_KULLAN, $GRUBU, $CARIKODU);
                    $stmt->execute();
                    $stmt->close();

                    // Check if the record already exists in adresler table
                    $checkAddressQuery = "SELECT * FROM adresler WHERE uye_id = ?";
                    $stmt = $mysqli->prepare($checkAddressQuery);
                    $stmt->bind_param("i", $uyeid);
                    $stmt->execute();
                    $checkAddressResult = $stmt->get_result();
                    $adres_turu = "teslimat";

                    if ($checkAddressResult->num_rows > 0) {
                        // Update the existing record in adresler table
                        $updateAddressQuery = "UPDATE adresler SET ad = ?, soyad = ?, adres_turu = ?, firma_adi = ?, il = ?, ilce = ?, vergi_dairesi = ?, vergi_no = ?, telefon = ?, aktif = ?, adres = ?, posta_kodu = ?, ulke = ?, tc_no = ? WHERE uye_id = ?";
                        $stmt = $mysqli->prepare($updateAddressQuery);
                        $stmt->bind_param("ssssiisssissssi", $ADI, $SOYADI, $adres_turu, $TICARI_UNVANI, $il, $ilce, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $aktif, $ADRESI_1, $POSTA_KODU_1, $ULKESI_1, $TC_KIMLIK_NO, $uyeid);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        $insertAddressQuery = "INSERT INTO adresler (uye_id, adres_turu, il, ilce, ad, soyad, firma_adi, vergi_dairesi, vergi_no, telefon, aktif, adres, posta_kodu, ulke, tc_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $mysqli->prepare($insertAddressQuery);
                        $stmt->bind_param("isiissssssissss", $uyeid, $adres_turu, $il, $ilce, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $aktif, $ADRESI_1, $POSTA_KODU_1, $ULKESI_1, $TC_KIMLIK_NO);
                        $stmt->execute();
                        $stmt->close();
                    }
                    echo "$newDate: Güncellenen Cari Kodu: $CARIKODU <br>";
                }
            }
            else {
                /* BURADA MUHASEBE KODUNU KONTROL ET EĞER AYNI İSE GÜNCELLEME YAP VE BLKODUNU DA GÜNCELLE DEĞİLSE INSERT YAP */
                $insertQuery = "INSERT INTO uyeler (BLKODU, muhasebe_kodu, ad, soyad, firmaUnvani, vergi_dairesi, vergi_no, tel, aktif, fiyat, DOVIZ_KULLAN, DOVIZ_BIRIMI, MUHKODU_ALIS,
                MUHKODU_SATIS, adres, il, ilce, posta_kodu, email, parola, ulke, ADI_SOYADI, cinsiyet, tc_no, satis_temsilcisi, uye_tipi, OZELALANTANIM_3, OZELALANTANIM_27,
                DEGISTIRME_TARIHI, EFATURA_SENARYO, EFATURA_KULLAN, ALICI_GRUBU, EIRSALIYE_KULLAN, kayit_tarihi, GRUBU)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($insertQuery);
                $stmt->bind_param("issssssssssssssssssssssssssssssssss", $BLKODU, $CARIKODU, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $AKTIF, $STOK_FIYATI,
                    $DOVIZ_KULLAN, $DOVIZ_BIRIMI, $MUHKODU_ALIS, $MUHKODU_SATIS, $ADRESI_1, $il, $ilce, $POSTA_KODU_1, $WEB_USER_NAME, $WEB_USER_PASSW, $ULKESI_1, $ADI_SOYADI, $CINSIYETI,
                    $TC_KIMLIK_NO, $PAZ_BLCRKODU, $OZEL_KODU3, $OZELALANTANIM_3, $OZELALANTANIM_27, $DEGISTIRME_TARIHI, $EFATURA_SENARYO, $EFATURA_KULLAN, $ALICI_GRUBU, $EIRSALIYE_KULLAN, $KAYIT_TARIHI, $GRUBU);

                if (!$stmt->execute()) {
                    echo "$newDate: INSERT sorgusunu yürütme hatası: " . $stmt->error;
                } else {
                    $lastInsertedID = $mysqli->insert_id;
                }
                $stmt->close();

                $adres_turu = "teslimat";
                // Insert a new record in adresler table
                $insertAddressQuery = "INSERT INTO adresler (uye_id, adres_turu, il, ilce, ad, soyad, firma_adi, vergi_dairesi, vergi_no, telefon, aktif, adres, posta_kodu, ulke, tc_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($insertAddressQuery);
                $stmt->bind_param("isiissssssissss", $lastInsertedID, $adres_turu, $il, $ilce, $ADI, $SOYADI, $TICARI_UNVANI, $VERGI_DAIRESI, $VERGI_NO, $CEP_TEL, $aktif, $ADRESI_1, $POSTA_KODU_1, $ULKESI_1, $TC_KIMLIK_NO);
                $stmt->execute();
                $stmt->close();

                echo "$newDate: Yeni Kayıt Eklendi. Cari Kodu: $CARIKODU <br>";
            }
        }
    }
    $mysqli->close();
    echo "$newDate: Cari Tarama Tamamlandı. <br>";
}
function getAccountTransactionList($xmlData) {
    $mysqli = connectToDatabase();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo "$newDate: Evrak Taraması Başladı. <br>";
    if ($xml === false) {
        echo "Failed to parse XML Cari Hareket Liste.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo "$newDate: Evrak taraması tamamlandı.  <br>";
        return;
    }
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $BLCRKODU = $mysqli->real_escape_string((string)$row-> BLCRKODU);
        $EVRAK_NO = $mysqli->real_escape_string((string)$row-> EVRAK_NO);
        $TARIHI = $mysqli->real_escape_string((string)$row-> TARIHI);
        $MUH_DURUM = $mysqli->real_escape_string((string)$row-> MUH_DURUM);
        $MUH_HESKODU = $mysqli->real_escape_string((string)$row-> MUH_HESKODU);
        $DOVIZ_KULLAN = $mysqli->real_escape_string((string)$row-> DOVIZ_KULLAN);
        $DOVIZ_ALIS = $mysqli->real_escape_string((string)$row-> DOVIZ_ALIS);
        $DOVIZ_SATIS = $mysqli->real_escape_string((string)$row-> DOVIZ_SATIS);
        $KPBDVZ = $mysqli->real_escape_string((string)$row-> KPBDVZ);
        $DOVIZ_BIRIMI = $mysqli->real_escape_string((string)$row-> DOVIZ_BIRIMI);
        $DOVIZ_HES_ISLE = (int)$row->DOVIZ_HES_ISLE;
        $ACIKLAMA = $mysqli->real_escape_string((string)$row-> ACIKLAMA);
        $KASA_ADI = $mysqli->real_escape_string((string)$row-> KASA_ADI);
        $BANKA_ADI = $mysqli->real_escape_string((string)$row-> BANKA_ADI);
        $KKARTI_DETAY = $mysqli->real_escape_string((string)$row-> KKARTI_DETAY);
        $ENTEGRASYON = $mysqli->real_escape_string((string)$row-> ENTEGRASYON);
        $KPB_BTUT = $mysqli->real_escape_string((string)$row-> KPB_BTUT);
        $KPB_ATUT = $mysqli->real_escape_string((string)$row-> KPB_ATUT);
        $DVZ_BTUT = $mysqli->real_escape_string((string)$row-> DVZ_BTUT);
        $DVZ_ATUT = $mysqli->real_escape_string((string)$row-> DVZ_ATUT);
        $ISLEM_TURU = (int)$row->ISLEM_TURU;
        $SILINDI = (int)$row->SILINDI;
        $DEGISTIRME_TARIHI = date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI));
        $VADESI = date('Y-m-d', strtotime((string)$row->VADESI));

        $checkQuery = "SELECT * FROM uyeler_hareket_deneme WHERE EVRAK_NO = ?";
        $stmt = $mysqli->prepare($checkQuery);
        $stmt->bind_param("s", $EVRAK_NO);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $stmt->close();
        if ($checkResult->num_rows > 0) {
            $existingRow = $checkResult->fetch_assoc();
            $existingDegistirmeTarihi = $existingRow['DEGISTIRME_TARIHI'];
            if ($existingDegistirmeTarihi != $DEGISTIRME_TARIHI) {
                $updateQuery = "UPDATE uyeler_hareket_deneme 
                                SET BLCRKODU = ?, EVRAK_NO = ?, TARIHI = ?, VADESI = ?, MUH_DURUM = ?, MUH_HESKODU = ?, DOVIZ_KULLAN = ?, DOVIZ_ALIS = ?, DOVIZ_SATIS = ?, KPBDVZ = ?,
                                    DOVIZ_BIRIMI = ?, DOVIZ_HES_ISLE = ?, ACIKLAMA = ?, KASA_ADI = ?, BANKA_ADI = ?, KKARTI_DETAY = ?, ENTEGRASYON = ?, KPB_BTUT = ?, KPB_ATUT = ?,
                                    DVZ_BTUT = ?, DVZ_ATUT = ?, DEGISTIRME_TARIHI = ?, ISLEM_TURU = ?, SILINDI = ? WHERE EVRAK_NO = ?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("sssssssssssissssssssssiis", $BLCRKODU, $EVRAK_NO, $TARIHI, $VADESI, $MUH_DURUM, $MUH_HESKODU, $DOVIZ_KULLAN, $DOVIZ_ALIS,
                    $DOVIZ_SATIS, $KPBDVZ, $DOVIZ_BIRIMI, $DOVIZ_HES_ISLE, $ACIKLAMA, $KASA_ADI, $BANKA_ADI, $KKARTI_DETAY, $ENTEGRASYON, $KPB_BTUT, $KPB_ATUT, $DVZ_BTUT, $DVZ_ATUT,
                    $DEGISTIRME_TARIHI, $ISLEM_TURU, $SILINDI, $EVRAK_NO);
                $stmt->execute();
                $stmt->close();
                echo "$newDate: Güncellenen Evrak Numarası: $EVRAK_NO <br>";
            }
        } else {
            $insertQuery = "INSERT INTO uyeler_hareket_deneme (BLKODU ,BLCRKODU ,EVRAK_NO ,TARIHI ,VADESI ,MUH_DURUM ,MUH_HESKODU ,DOVIZ_KULLAN ,DOVIZ_ALIS ,DOVIZ_SATIS ,KPBDVZ ,DOVIZ_BIRIMI ,
                           DOVIZ_HES_ISLE ,ACIKLAMA ,KASA_ADI ,BANKA_ADI ,KKARTI_DETAY ,ENTEGRASYON ,KPB_BTUT ,KPB_ATUT ,DVZ_BTUT ,DVZ_ATUT ,DEGISTIRME_TARIHI ,ISLEM_TURU, SILINDI)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $mysqli->prepare($insertQuery);
            $stmt->bind_param("isssssssssssissssssssssii", $BLKODU ,$BLCRKODU ,$EVRAK_NO ,$TARIHI ,$VADESI ,$MUH_DURUM ,$MUH_HESKODU ,$DOVIZ_KULLAN ,
                $DOVIZ_ALIS ,$DOVIZ_SATIS ,$KPBDVZ ,$DOVIZ_BIRIMI ,$DOVIZ_HES_ISLE ,$ACIKLAMA ,$KASA_ADI ,$BANKA_ADI ,$KKARTI_DETAY ,$ENTEGRASYON ,$KPB_BTUT ,$KPB_ATUT ,$DVZ_BTUT ,
                $DVZ_ATUT ,$DEGISTIRME_TARIHI ,$ISLEM_TURU, $SILINDI);
            $stmt->execute();
            $stmt->close();
            echo "$newDate: Yeni Evrak Numarası: $EVRAK_NO <br>";
        }
    }
    $mysqli->close();
    echo "$newDate: Evrak taraması tamamlandı. <br>";
}
function getAccountTransactionSil($xmlData) {
    $pdo = connectToDatabasePDO();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo "$newDate: Evrak Silindi Kontrol Başladı. <br>";

    if ($xml === false) {
        echo "XML Cari Hareket Liste Silindi parse edilemedi.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo "$newDate: Evrak Silindi Kontrol tamamlandı. <br>";
        return;
    }
    try {
        // Start a transaction
        $pdo->beginTransaction();
        // Prepare the update query once
        $updateQuery = "UPDATE uyeler_hareket_deneme SET SILINDI = 1 WHERE EVRAK_NO = :EVRAK_NO";
        $updateStmt = $pdo->prepare($updateQuery);

        foreach ($xml->table->row as $row) {
            $EVRAK_NO = $row->EVRAK_NO;

            $updateStmt->execute([':EVRAK_NO' => $EVRAK_NO]);
        }
        $pdo->commit();
        echo "$newDate: Evrak Silindi Kontrol tamamlandı. <br>";
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        echo "$newDate: Evrak Silindi Kontrol tamamlandı. <br>";
    }
}
function getAccountBalanceList($xmlData) {
    $mysqli = connectToDatabase();
    $xml = simplexml_load_string($xmlData);
    global $newDate;
    echo "$newDate: Hesap Bakiye Taraması Başladı. <br>";
    if ($xml === false) {
        echo "Failed to parse XML Cari Bakiye Liste.";
        return;
    }
    if (!isset($xml->table->row)) {
        echo "$newDate: Hesap Bakiye Taraması Tamamlandı. <br>";
        return;
    }
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $HESAP = $mysqli->real_escape_string((string)$row-> HESAP);
        $TPL_BRC = $mysqli->real_escape_string((string)$row-> TPL_BRC);
        $TPL_ALC = $mysqli->real_escape_string((string)$row-> TPL_ALC);
        $TPL_BKY = $mysqli->real_escape_string((string)$row-> TPL_BKY);
        $TPL_BTR = $mysqli->real_escape_string((string)$row-> TPL_BTR);
        $DVZ_HESAP = $mysqli->real_escape_string((string)$row-> DVZ_HESAP);
        $DVZ_TPLBRC = $mysqli->real_escape_string((string)$row-> DVZ_TPLBRC);
        $DVZ_TPLALC = $mysqli->real_escape_string((string)$row-> DVZ_TPLALC);
        $DVZ_BAKIYE = $mysqli->real_escape_string((string)$row-> DVZ_BAKIYE);
        $DVZ_BTR = $mysqli->real_escape_string((string)$row-> DVZ_BTR);
        $CARIKODU = $mysqli->real_escape_string((string)$row-> CARIKODU);

        $checkQuery = "SELECT * FROM cari_bakiye WHERE BLKODU = ?";
        $stmt = $mysqli->prepare($checkQuery);
        $stmt->bind_param("i", $BLKODU);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        $stmt->close();
        if ($checkResult->num_rows > 0) {
            $existingRow = $checkResult->fetch_assoc();
            $mevcutTplBakiye = $existingRow['TPL_BKY'];
            $mevcutDvzBakiye = $existingRow['DVZ_BAKIYE'];
            if ($mevcutTplBakiye != $TPL_BKY || $mevcutDvzBakiye != $DVZ_BAKIYE) {
                $updateQuery = "UPDATE cari_bakiye 
                            SET HESAP = ?, TPL_BRC = ?, TPL_ALC = ?, TPL_BKY = ?, TPL_BTR = ?, DVZ_HESAP = ?, DVZ_TPLBRC = ?, DVZ_TPLALC = ?, DVZ_BAKIYE = ?, DVZ_BTR = ?, CARIKODU = ? 
                            WHERE BLKODU = ?";
                $stmt = $mysqli->prepare($updateQuery);
                $stmt->bind_param("sssssssssssi",
                    $HESAP, $TPL_BRC, $TPL_ALC, $TPL_BKY, $TPL_BTR, $DVZ_HESAP, $DVZ_TPLBRC, $DVZ_TPLALC, $DVZ_BAKIYE, $DVZ_BTR, $CARIKODU, $BLKODU);
                $stmt->execute();
                $stmt->close();
                echo "$newDate: Güncellenen Hesap Bakiyesi: $CARIKODU <br>";
            }
        } else {
            $insertQuery = "INSERT INTO cari_bakiye (HESAP ,TPL_BRC ,TPL_ALC ,TPL_BKY ,TPL_BTR ,DVZ_HESAP ,DVZ_TPLBRC ,DVZ_TPLALC ,DVZ_BAKIYE ,DVZ_BTR ,BLKODU ,CARIKODU )
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($insertQuery);
            $stmt->bind_param("ssssssssssis", $HESAP ,$TPL_BRC ,$TPL_ALC ,$TPL_BKY ,$TPL_BTR ,$DVZ_HESAP ,$DVZ_TPLBRC ,$DVZ_TPLALC ,$DVZ_BAKIYE ,$DVZ_BTR ,$BLKODU ,$CARIKODU);
            $stmt->execute();
            $stmt->close();
            echo "$newDate: Yeni Hesap Bakiyesi : $CARIKODU <br>";
        }
    }
    $mysqli->close();
    echo "$newDate: Hesap Bakiye Taraması Tamamlandı. <br>";
}
function faturalariGonder() {
    global $newDate;
    $files = scandir("../assets/faturalar/");
    if ($files === false) {
        echo "$newDate: XML dosyaları bulunamadı <br>";
        return;
    }
    $jsonResult = array();
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $xmlData = file_get_contents("https://www.denemeb2b.noktaelektronik.net/assets/faturalar/$file");
        $jsonResult[$file] = $xmlData; // XML verisini JSON'a dönüştür ve dosya adıyla eşleştir
        echo "$newDate: Yeni Sipariş $file gönderildi. <br>";
    }
    echo json_encode($jsonResult);
    // Faturalar klasöründeki dosyaları sil
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $filePath = "../assets/faturalar/$file";
        if (is_file($filePath)) {
            unlink($filePath); // Dosyayı sil
        }
    }
}
function odemeGonder() {
    global $newDate;
    $files = scandir("assets/carihareket/");
    if ($files === false) {
        echo "$newDate: XML dosyaları bulunamadı <br>";
        return;
    }
    $jsonResult = array();
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $xmlData = file_get_contents("https://www.denemeb2b.noktaelektronik.net/assets/carihareket/$file");
        $jsonResult[$file] = $xmlData; // XML verisini JSON'a dönüştür ve dosya adıyla eşleştir
        echo "$newDate: Yeni Cari Hareket $file gönderildi. <br>";
    }
    echo json_encode($jsonResult);
    // Faturalar klasöründeki dosyaları sil
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $filePath = "assets/carihareket/$file";
        if (is_file($filePath)) {
            unlink($filePath); // Dosyayı sil
        }
    }
}
function cariGonder() {
    global $newDate;
    $files = scandir("../assets/cari/");
    if ($files === false) {
        echo "$newDate: XML dosyaları bulunamadı <br>";
        return;
    }
    
    $jsonResult = array();
    print_r($files);
    echo "<br>";
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
    
        // Dosya yolunu belirleme
        $fileUrl = "https://denemeb2b.noktaelektronik.net/assets/cari/$file";
        
        // cURL başlatıyoruz
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fileUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $xmlData = curl_exec($ch);
    
        // Hata kontrolü
        if(curl_errno($ch)) {
            echo "cURL Hatası: " . curl_error($ch);
        }
        
        // Yanıtı kontrol et
        if (!empty($xmlData)) {
            $jsonResult[$file] = $xmlData;
        } else {
            echo "Geçersiz veya boş XML verisi: $file<br>";
        }
    
        // cURL bağlantısını kapat
        curl_close($ch);
    }
    
    // JSON çıktısı
    echo json_encode($jsonResult);
    
    // Dosya silme işlemi
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $filePath = "../assets/cari/$file";
        if (is_file($filePath)) {
            echo "dosya silme işlemi yapıldı: $file<br>";
            //unlink($filePath); // Dosyayı sil
        }
    }
    
}




function cariBLKODU($gelenveri){
    preg_match('/MBLKODU=(\d+)/', $gelenveri, $matches);
    if (isset($matches[1])) {
        $blkodu = $matches[1];
        $mysqli = connectToDatabase();
        $updateQuery = "UPDATE uyeler SET BLKODU = ? WHERE id = (SELECT MAX(id) FROM uyeler)";
        $stmt = $mysqli->prepare($updateQuery);
        $stmt->bind_param("i", $blkodu);
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
    } else {
        echo "MBLKODU bulunamadı.";
    }
}
function cariGonderUpdate($xmlData) {
    global $newDate;
    $mysqli = connectToDatabase();
    $date = date("Y-m-d H:i:s", strtotime("-24 hour"));
    $xml = simplexml_load_string($xmlData);
    $dataArray = [];
    if ($xml === false) {
        echo "Failed to parse XML Cari Güncelle Liste. <br>";
        return;
    }
    if (!isset($xml->table->row)) {
        echo "Gönderilecek veri yok. (Cari Liste) <br>";
        return;
    }
    foreach ($xml->table->row as $row) {
        $BLKODU = (int)$row->BLKODU;
        $DEGISTIRME_TARIHI = date('Y-m-d H:i:s', strtotime((string)$row->DEGISTIRME_TARIHI));
        $dataArray[] = ["BLKODU" => $BLKODU, "DEGISTIRME_TARIHI" => $DEGISTIRME_TARIHI];
    }
    $checkQuery = "SELECT BLKODU, DEGISTIRME_TARIHI FROM uyeler WHERE DEGISTIRME_TARIHI >= ?";
    $stmt = $mysqli->prepare($checkQuery);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $checkResult = $stmt->get_result();
    $unmatchedBLKODUs = [];
    while ($row = $checkResult->fetch_assoc()) {
        $BLKODU_db = (int)$row['BLKODU'];
        $DEGISTIRME_TARIHI_db = $row['DEGISTIRME_TARIHI'];
        $found = false;
        foreach ($dataArray as $data) {
            if ($data["BLKODU"] === $BLKODU_db && $data["DEGISTIRME_TARIHI"] === $DEGISTIRME_TARIHI_db) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $unmatchedBLKODUs[] = ["BLKODU" => $BLKODU_db, "DEGISTIRME_TARIHI" => $DEGISTIRME_TARIHI_db];
        }
    }
    foreach ($unmatchedBLKODUs as $unmatchedBLKODU) {
        $gelsinBL = $unmatchedBLKODU["BLKODU"];

        $checkQuery = "SELECT * FROM uyeler WHERE BLKODU = ?";
        $stmt = $mysqli->prepare($checkQuery);
        $stmt->bind_param("s", $gelsinBL);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        if ($row = $checkResult->fetch_assoc()) {

            $il = $row['il'];
            $ilce = $row['ilce'];

            $query1 = "SELECT * FROM iller WHERE il_id = ?";
            $stmt1 = $mysqli->prepare($query1);
            $stmt1->bind_param("i", $il);
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            if ($result1->num_rows > 0) {
                $row1 = $result1->fetch_assoc();
                $il_adi = $row1['il_adi'];
            }

            $query2 = "SELECT * FROM ilceler WHERE ilce_id = ? AND il_id = ?";
            $stmt2 = $mysqli->prepare($query2);
            $stmt2->bind_param("ii", $ilce, $il);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result2->num_rows > 0) {
                $row2 = $result2->fetch_assoc();
                $ilce_adi = $row2['ilce_adi'];
            }

            $query3 = "SELECT * FROM users WHERE id = ?";
            $stmt3 = $mysqli->prepare($query3);
            $stmt3->bind_param("i", $row["satis_temsilcisi"]);
            $stmt3->execute();
            $result3 = $stmt3->get_result();

            if ($result3->num_rows > 0) {
                $row3 = $result3->fetch_assoc();
                $satis_temsilcisi = $row3['full_name'] ;
            }

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
                'SUBE_KODU' => '3402'                ];
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
            $xmlDoc->save('../assets/cari_guncelle/' . $xmlFileName);
        }
        $stmt->close();
    }
    $files = scandir("assets/cari_guncelle/");
    if ($files === false) {
        echo "$newDate: XML dosyaları bulunamadı <br>";
        return;
    }
    $jsonResult = array();
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $xmlData = file_get_contents("https://denemeb2b.noktaelektronik.net/assets/cari_guncelle/$file");
        $jsonResult[$file] = $xmlData; // XML verisini JSON'a dönüştür ve dosya adıyla eşleştir
        echo "$newDate: Güncellenen Cari $file gönderildi. <br>";
    }
    echo json_encode($jsonResult);
    // Faturalar klasöründeki dosyaları sil
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $filePath = "assets/cari_guncelle/$file";
        if (is_file($filePath)) {
            unlink($filePath); // Dosyayı sil
        }
    }
    $mysqli->close();
}

$xml_data_stock_inventory = isset($_POST['xml_data_stok_envanter']) ? $_POST['xml_data_stok_envanter'] : '';
$xml_data_stock = isset($_POST['xml_data_stok_adet']) ? $_POST['xml_data_stok_adet'] : '';
$xml_data_stock_list = isset($_POST['xml_data_stok_liste']) ? $_POST['xml_data_stok_liste'] : '';
$xml_data_account_list = isset($_POST['xml_data_cari_liste']) ? $_POST['xml_data_cari_liste'] : '';
$xml_data_account_transaction_list = isset($_POST['xml_data_cari_hareket_liste']) ? $_POST['xml_data_cari_hareket_liste'] : '';
$xml_data_account_transaction_sil = isset($_POST['xml_data_cari_hareket_sil']) ? $_POST['xml_data_cari_hareket_sil'] : '';
$xml_data_account_balance_list = isset($_POST['xml_data_cari_bakiye_liste']) ? $_POST['xml_data_cari_bakiye_liste'] : '';
$xml_siparis_gonder = isset($_POST['xml_siparis_gonder']) ? $_POST['xml_siparis_gonder'] : '';
$xml_odeme_sorgula = isset($_POST['xml_odeme_sorgula']) ? $_POST['xml_odeme_sorgula'] : '';
$xml_cari_gonder = isset($_POST['xml_cari_gonder']) ? $_POST['xml_cari_gonder'] : '';
$xml_cari_blkodu = isset($_POST['xml_cari_blkodu']) ? $_POST['xml_cari_blkodu'] : '';
$xml_cari_gonder_guncelle = isset($_POST['xml_cari_gonder_guncelle']) ? $_POST['xml_cari_gonder_guncelle'] : '';

if (!empty($xml_data_stock_inventory)) {
    getStockInventory($xml_data_stock_inventory);
    insertCategoriesFromDatabase();
    updateKategoriIDForAllProducts();
    getMarka($xml_data_stock_inventory);
}
elseif (!empty($xml_data_stock_list)) { getStockList($xml_data_stock_list);}
elseif (!empty($xml_data_account_list)) { getAccountList($xml_data_account_list); }
elseif (!empty($xml_data_account_transaction_list)) { getAccountTransactionList($xml_data_account_transaction_list); }
elseif (!empty($xml_data_account_transaction_sil)) { getAccountTransactionSil($xml_data_account_transaction_sil); }
elseif (!empty($xml_data_account_balance_list)) { getAccountBalanceList($xml_data_account_balance_list); }
elseif (!empty($xml_siparis_gonder)) { faturalariGonder(); }
elseif (!empty($xml_odeme_sorgula)) { odemeGonder(); }
elseif (!empty($xml_cari_gonder)) { cariGonder(); }
elseif (!empty($xml_cari_blkodu)) { cariBLKODU($xml_cari_blkodu); }
elseif (!empty($xml_cari_gonder_guncelle)) { cariGonderUpdate($xml_cari_gonder_guncelle); }
elseif (!empty($xml_data_stock)) { stokMiktar($xml_data_stock); }
?>
