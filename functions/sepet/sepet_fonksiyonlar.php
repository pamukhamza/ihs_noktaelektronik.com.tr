<?php 
require_once "../db.php";
$db = new Database();

function generateUniqueCode($length = 10) {
    return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
}

if (isset($_POST['promosyonKontrol'])) {
    $promosyonKodu = $_POST['promosyon_kodu'];
    $toplam = $_POST['toplam'];
    $uye_id = $_POST['uyeID'];
    $gelen_urunler = $_POST['blkodu_dizi'];
    $gelen_urunler = trim($gelen_urunler, '[]');
    $gelen_kategoriler = $_POST['kategori_dizi'];
    $gelen_kategoriler = trim($gelen_kategoriler, '[]');
    $gelen_markalar = $_POST['marka_dizi'];
    $gelen_markalar = trim($gelen_markalar, '[]');
    $gelen_adetler = $_POST['adet_dizi'];
    $gelen_adetler = trim($gelen_adetler, '[]');

    // Check promotion code
    $result = $db->fetch("SELECT * FROM promosyon WHERE promosyon_kodu = :kod", [
        'kod' => $promosyonKodu
    ]);

    if ($result) {
        $promosyonUyeId = $result['uye_id'];
        $kullanacak_uye_id = $result['kullanacak_uye_id'];
        $kategoriler = $result['kategoriler'];
        $markalar = $result['markalar'];
        $urunlerVT = $result['urunler'];

        if ($result['kullanildi'] == 1) {
            echo json_encode(['response' => '0.00', 'message' => 'Bu kuponun kullanım limiti dolmuştur.']);
            exit;
        }
        if ($result['aktif'] == 0) {
            echo json_encode(['response' => '0.00', 'message' => 'Bu kupon artık geçerli deil.']);
            exit;
        }

        function convertAndCheckArray($data, $checkValue) {
            if ($data[0] == '[' || $data[0] == '{') {
                $data = json_decode($data, true);
            } else {
                $data = explode(',', $data);
            }
            if (!is_array($data)) {
                $data = [$data];
            }
            return in_array($checkValue, $data);
        }

        if (!empty($promosyonUyeId) && convertAndCheckArray($promosyonUyeId, $uye_id)) {
            echo json_encode(['response' => '0.00', 'message' => 'Bu kuponu 1 kez kullanabilirsiniz.']);
            exit;
        }
        if (!empty($kullanacak_uye_id) && !convertAndCheckArray($kullanacak_uye_id, $uye_id)) {
            echo json_encode(['response' => '0.00', 'message' => 'Bu kuponu kullanamazsınız.']);
            exit;
        }

        function checkValuesInArray($values, $checkArray) {
            if ($checkArray[0] == '[' || $checkArray[0] == '{') {
                $checkArray = json_decode($checkArray, true);
            } else {
                $checkArray = explode(',', $checkArray);
            }
            if (!is_array($checkArray)) {
                $checkArray = [$checkArray];
            }
            foreach ($values as $value) {
                if (in_array($value, $checkArray)) {
                    return true;
                }
            }
            return false;
        }

        if (!empty($markalar) && !checkValuesInArray(explode(',', $gelen_markalar), $markalar)) {
            echo json_encode(['response' => '0.00', 'message' => 'Bu markalarda indirim geçerli değildir.']);
            exit;
        }
        if (!empty($kategoriler) && !checkValuesInArray(explode(',', $gelen_kategoriler), $kategoriler)) {
            echo json_encode(['response' => '0.00', 'message' => 'Bu kategorilerde indirim geerli değildir.']);
            exit;
        }
        if (!empty($urunler) && !checkValuesInArray(explode(',', $gelen_urunler), $urunler)) {
            echo json_encode(['response' => '0.00', 'message' => 'Bu ürünlerde indirim geçerli değildir.']);
            exit;
        }

        if (!empty($result['minSepetTutar']) && (!empty($urunler) || !empty($markalar) || !empty($kategoriler))) {
            $kur = $db->fetch("SELECT * FROM kurlar WHERE id = 2");
            $dolar = floatval($kur["satis"]);

            $kurE = $db->fetch("SELECT * FROM kurlar WHERE id = 3");
            $euro = floatval($kurE["satis"]);

            $uye_fiyat = $db->fetchColumn("SELECT fiyat FROM uyeler WHERE id = :id", [
                'id' => $uye_id
            ]);

            $sepetTutari = 0;
            $urunler = explode(',', $gelen_urunler);
            $adetler = explode(',', $gelen_adetler);

            foreach ($urunler as $key => $urun_id) {
                $adet = isset($adetler[$key]) ? trim($adetler[$key]) : '';
                
                $urun_detay = $db->fetch("SELECT id, DOVIZ_BIRIMI, DSF4, DSF3, DSF2, DSF1, KSF4, KSF3, KSF2, KSF1, MarkaID, KategoriID 
                                        FROM nokta_urunler WHERE id = :id", [
                    'id' => $urun_id
                ]);

                if (
                    (!empty($markalar) && (in_array($urun_detay['MarkaID'], explode(',', $markalar)))) ||
                    (!empty($kategoriler) && (in_array($urun_detay['KategoriID'], explode(',', $kategoriler)))) ||
                    (!empty($urunlerVT) && (in_array($urun_detay['id'], explode(',', $urunlerVT))))
                ) {
                    if (!empty($urun_detay['DSF4'])) {
                        $doviz = $urun_detay['DOVIZ_BIRIMI'] == '$' ? $dolar : $euro;
                        $urun_fiyat = $urun_detay['DSF' . $uye_fiyat];
                        $fiyat_islem = $urun_fiyat * $doviz * $adet;
                        $fiyat_son = $fiyat_islem * 1.20;
                        $sepetTutari += $fiyat_son;
                    } else {
                        $urun_fiyat = $urun_detay['KSF' . $uye_fiyat];
                        $fiyat_son = $urun_fiyat * 1.20 * $adet;
                        $sepetTutari += $fiyat_son;
                    }
                }
            }

            if ($sepetTutari < $result['minSepetTutar']) {
                echo json_encode(['response' => '0.00', 'message' => 'Minimum alışveriş tutarını sağlayamadınız.', 'tplfyt' => $sepetTutari]);
                exit;
            }
        }

        if (!empty($result['minSepetTutar']) && $toplam < $result['minSepetTutar']) {
            echo json_encode(['response' => '0.00', 'message' => 'Minimum alışveriş tutarını sağlayamadınız.']);
            exit;
        }

        echo json_encode(['response' => $result['tutar']]);
    } else {
        echo json_encode(['response' => '0.00', 'message' => 'Kupon bulunamadı.']);
    }
}

if(isset($_POST['sepetAdetGuncelle'])) {
    $sepetId = isset($_POST["sepetId"]) ? $_POST["sepetId"] : 0;
    $adet = isset($_POST["adet"]) ? $_POST["adet"] : 0;
    $stok = isset($_POST["stok"]) ? $_POST["stok"] : 0;

    $response = ['status' => 1];

    if($sepetId > 0) {
        if($adet > $stok) {
            $db->update("UPDATE uye_sepet SET adet = :stok WHERE id = :sepetId", [
                'stok' => $stok,
                'sepetId' => $sepetId
            ]);
            echo json_encode($response);
            exit;
        } elseif($adet < 1) {
            if($stok < 1) {
                $db->delete("DELETE FROM uye_sepet WHERE id = :sepetId", [
                    'sepetId' => $sepetId
                ]);
                echo json_encode($response);
                exit;
            }
        }

        $db->update("UPDATE uye_sepet SET adet = :adet WHERE id = :sepetId", [
            'adet' => $adet,
            'sepetId' => $sepetId
        ]);

        echo json_encode(["status" => "success"]);
        exit;
    } else {
        // sepetId yoksa direkt success döndür
        echo json_encode($response);
        exit;
    }
}

?>