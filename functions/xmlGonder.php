<?php
define("DB_SERVER", "noktanetdb.cbuq6a2265j6.eu-central-1.rds.amazonaws.com");
define("DB_USERNAME", "nokta");
define("DB_PASSWORD", "Dell28736.!");
define("DB_NAME", "noktanetdb");
$newDate = date('Y-m-d H:i:s', strtotime('+3 hours'));
function connectToDatabase() {
    $mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_error) {
        echo "Connection failed: " . $mysqli->connect_error;
    }
    $mysqli->set_charset("utf8");
    return $mysqli;
}
function faturalariGonder() {
    global $newDate;
    $folderPath = "../assets/faturalar/";
    $files = scandir($folderPath);

    if ($files === false) {
        echo json_encode(["hata" => "$newDate: XML dosyaları bulunamadı"]);
        return;
    }

    $xmlArray = array();

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;

        $filePath = $folderPath . $file;

        if (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'xml') {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_file($filePath);

            if ($xml === false) {
                $hatalar = [];
                foreach (libxml_get_errors() as $error) {
                    $hatalar[] = htmlspecialchars($error->message);
                }
                libxml_clear_errors();
                $xmlArray[$file] = ["hata" => $hatalar];
            } else {
                $xmlArray[$file] = $xml->asXML(); // RAW XML string dön
                $filePath = "../assets/faturalar/$file";
                if (is_file($filePath)) {
                    unlink($filePath); // Dosyayı sil
                }
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($xmlArray);
}
function odemeGonder() {
    global $newDate;
    $folderPath = "../assets/pos/";
    $files = scandir($folderPath);

    if ($files === false) {
        echo json_encode(["hata" => "$newDate: XML dosyaları bulunamadı"]);
        return;
    }

    $xmlArray = array();

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;

        $filePath = $folderPath . $file;

        if (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'xml') {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_file($filePath);

            if ($xml === false) {
                $hatalar = [];
                foreach (libxml_get_errors() as $error) {
                    $hatalar[] = htmlspecialchars($error->message);
                }
                libxml_clear_errors();
                $xmlArray[$file] = ["hata" => $hatalar];
            } else {
                $xmlArray[$file] = $xml->asXML(); // RAW XML string dön
                $filePath = "../assets/pos/$file";
                if (is_file($filePath)) {
                    unlink($filePath); // Dosyayı sil
                }
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($xmlArray);
}

function cariGonder() {
    global $newDate;
    $folderPath = "../assets/cari/";
    $files = scandir($folderPath);

    if ($files === false) {
        echo json_encode(["hata" => "$newDate: XML dosyaları bulunamadı"]);
        return;
    }

    $xmlArray = array();

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;

        $filePath = $folderPath . $file;

        if (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'xml') {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_file($filePath);

            if ($xml === false) {
                $hatalar = [];
                foreach (libxml_get_errors() as $error) {
                    $hatalar[] = htmlspecialchars($error->message);
                }
                libxml_clear_errors();
                $xmlArray[$file] = ["hata" => $hatalar];
            } else {
                $xmlArray[$file] = $xml->asXML(); // RAW XML string dön
                $filePath = "../assets/cari/$file";
                if (is_file($filePath)) {
                    unlink($filePath); // Dosyayı sil
                }
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($xmlArray);
}
function cariBLKODU($newBlkodu, $cariKodu) {
    $mysqli = connectToDatabase();
    if (!$mysqli) {
        echo "Veritabanına bağlanılamadı.<br>";
        return;
    }
    // Sorgu hazırlama
    $selectQuery = "SELECT id, BLKODU FROM uyeler WHERE muhasebe_kodu = ? LIMIT 1";
    $stmt = $mysqli->prepare($selectQuery);
    if (!$stmt) {
        echo "Sorgu hazırlanamadı: " . $mysqli->error . "<br>";
        return;
    }
    if (!$stmt->bind_param("s", $cariKodu)) {
        echo "Parametre bağlama hatası: " . $stmt->error . "<br>";
        return;
    }
    // Sorguyu çalıştır
    if (!$stmt->execute()) {
        echo "Sorgu çalıştırılamadı: " . $stmt->error . "<br>";
        return;
    }
    $result = $stmt->get_result();
    if (!$result) {
        echo "Sonuç alınamadı: " . $stmt->error . "<br>";
        return;
    }

    if ($row = $result->fetch_assoc()) {
        if (empty($row['BLKODU'])) {
            $updateQuery = "UPDATE uyeler SET BLKODU = ? WHERE id = ?";
            $updateStmt = $mysqli->prepare($updateQuery);
            if (!$updateStmt) {
                echo "Güncelleme sorgusu hazırlanamadı: " . $mysqli->error . "<br>";
                return;
            }
            if (!$updateStmt->bind_param("si", $newBlkodu, $row['id'])) {
                echo "Güncelleme parametre bağlama hatası: " . $updateStmt->error . "<br>";
                return;
            }
            if (!$updateStmt->execute()) {
                echo "Güncelleme sorgusu çalıştırılamadı: " . $updateStmt->error . "<br>";
            }
            $updateStmt->close();
        } else {
            echo "BLKODU zaten mevcut, güncellenmedi.<br>";
        }
    } else {
        echo "muhasebe_kodu ile eşleşen kayıt bulunamadı.<br>";
    }
    $stmt->close();
    $mysqli->close();
}
$xml_siparis_gonder = isset($_POST['xml_siparis_gonder']) ? $_POST['xml_siparis_gonder'] : '';
$xml_odeme_sorgula = isset($_POST['xml_odeme_sorgula']) ? $_POST['xml_odeme_sorgula'] : '';
$xml_cari_gonder = isset($_POST['xml_cari_gonder']) ? $_POST['xml_cari_gonder'] : '';
$xml_cari_blkodu = isset($_POST['xml_cari_blkodu']) ? $_POST['xml_cari_blkodu'] : '';
$xml_cari_kodu   = isset($_POST['xml_cari_kodu']) ? $_POST['xml_cari_kodu'] : '';

if (!empty($xml_siparis_gonder)) { faturalariGonder(); }
elseif (!empty($xml_odeme_sorgula)) { odemeGonder(); }
elseif (!empty($xml_cari_gonder)) { cariGonder(); }
elseif (!empty($xml_cari_blkodu) && !empty($xml_cari_kodu)) {cariBLKODU($xml_cari_blkodu, $xml_cari_kodu);}

?>