<?php
ini_set('display_errors', 1);  // Hataları ekrana göster
error_reporting(E_ALL);  
require_once 'db.php';
require_once '../mail/mail_gonder.php';
function controlInput($data) {
    // Veri temizleme işlemi
    $data = trim($data);               // Baş ve son boşlukları temizler
    $data = stripslashes($data);       // Yalnızca kaçış karakterlerini temizler
    $data = htmlspecialchars($data);  // HTML karakterlerini temizler
    return $data;
}
function saveToMailjet($email, $listId) {
    $apikey = '29f750523bec17ec1b06c03b2766d98f';
    $apisecret = '8b52ce1e9ca02de74c0038a0c0c6c270';

    // Mailjet API endpoint URL
    $url = "https://api.mailjet.com/v3/REST/contactslist/$listId/managecontact";

    // Veri dizisi oluşturma
    $data = [
    'Email' => $email,
    'Action' => 'addnoforce', // Ekleme işlemi, varsa zorlama yapmadan ekle
    ];

    // HTTP isteği oluşturma
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_USERPWD, "$apikey:$apisecret");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // İstek gönderme
    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // CURL kapatma
    curl_close($ch);
}
function sepeteFavoriEkle() {
    $urun_id = $_POST['urun_id'];
    $uye_id = $_POST['uye_id'];
    $adet = 1;
    $database = new Database();
    try {
        $checkQuery = "SELECT adet FROM uye_sepet WHERE uye_id = :uye_id AND urun_id = :urun_id";
        $existingAdet = $database->fetchColumn($checkQuery, ['uye_id' => $uye_id,'urun_id' => $urun_id]);

        if ($existingAdet !== false) {
            $newAdet = $existingAdet + 1;
            $updateQuery = "UPDATE uye_sepet SET adet = :adet WHERE uye_id = :uye_id AND urun_id = :urun_id";
            $database->update($updateQuery, ['adet' => $newAdet,'uye_id' => $uye_id,'urun_id' => $urun_id]);
        } else {
            $insertQuery = "INSERT INTO uye_sepet (uye_id, urun_id, adet) VALUES (:uye_id, :urun_id, :adet)";
            $database->insert($insertQuery, ['uye_id' => $uye_id,'urun_id' => $urun_id,'adet' => $adet]);
        }
    } catch (Exception $e) {
        echo "Bir hata oluştu: " . $e->getMessage();
    }
}
function uyeAdresEkle() {
    $adres_basligi = controlInput($_POST['adres_basligi']);
    $ad = controlInput($_POST['ad']);
    $soyad = controlInput($_POST['soyad']);
    $tel = controlInput($_POST['tel']);
    $adres = controlInput($_POST['adres']);
    $ulke = controlInput($_POST['ulke']);
    $il = controlInput($_POST['il']);
    $ilce = controlInput($_POST['ilce']);
    $posta_kodu = controlInput($_POST['posta_kodu']);
    $uyeId = controlInput($_POST['uyeId']);
    $adres_turu = 'teslimat';
    $database = new Database();

    $query = "INSERT INTO adresler (uye_id, adres_turu, adres_basligi, ad, soyad, adres, telefon, ulke, il, ilce, posta_kodu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$uyeId, $adres_turu, $adres_basligi, $ad, $soyad, $adres, $tel, $ulke, $il, $ilce, $posta_kodu]);
}
function editFavori() {
    $productId = $_POST['product_id'];
    $userId = $_POST['uye_id'];
    if (empty($userId)) {
        header("Location: ../tr/giris.php");
        exit(); // Redirect and stop execution
    }
    $database = new Database();
    $checkQuery = "SELECT id FROM nokta_uye_favoriler WHERE uye_id = :uye_id AND urun_id = :urun_id";
    $checkResult = $database->fetch($checkQuery, ['uye_id' => $userId, 'urun_id' => $productId]);
    if ($checkResult) {
        // Product exists, remove it
        $removeQuery = "DELETE FROM nokta_uye_favoriler WHERE uye_id = :uye_id AND urun_id = :urun_id";
        if ($database->delete($removeQuery, ['uye_id' => $userId, 'urun_id' => $productId])) {
            echo "removed";
        } else {
            echo "error";
        }
    } else {
        // Product doesn't exist, add it
        $insertQuery = "INSERT INTO nokta_uye_favoriler (uye_id, urun_id) VALUES (:uye_id, :urun_id)";
        if ($database->insert($insertQuery, ['uye_id' => $userId, 'urun_id' => $productId])) {
            echo "added";
        } else {
            echo "error";
        }
    }
}
function teklif() {
    $database = new Database();
    $mail = $_POST['email'];
    $uye_id = $_POST['uye_id'];
    $teklif_aciklama = $_POST['teklif_nedeni'];
    $urun_id = $_POST['urun_no'];
    $firmaAdi = "";
    if (!empty($uye_id)) {
        $uye = $database->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $uye_id]);
        $firmaAdi = $uye['firmaUnvani'] ?? '';
    }
    if (!empty($uye_id)) {
        $insertQuery = "INSERT INTO b2b_teklif (uye_id, urun_id, mail, aciklama) VALUES (:uye_id, :urun_id, :mail, :aciklama)";
        $database->insert($insertQuery, ['uye_id' => $uye_id, 'urun_id' => $urun_id, 'mail' => $mail, 'aciklama' => $teklif_aciklama]);
    } else {
        $insertQuery = "INSERT INTO b2b_teklif (urun_id, mail, aciklama) VALUES (:urun_id, :mail, :aciklama)";
        $database->insert($insertQuery, ['urun_id' => $urun_id,'mail' => $mail,'aciklama' => $teklif_aciklama]);
    }
    $mail_icerik = teklifAlindiMail($firmaAdi);
    mailGonder($mail, 'Teklif Talebiniz Alınmıştır!', $mail_icerik, 'Nokta Elektronik');
    exit;
}
function sepeteUrunEkle() {
    $database = new Database();
    $urun_id = $_POST['urun_id'];
    $uye_id = $_POST['uye_id'];
    $adet = !empty($_POST['adet']) ? $_POST['adet'] : 1;

    // Ürünün mevcut adetini kontrol et
    $existingAdet = $database->fetchColumn("SELECT adet FROM uye_sepet WHERE uye_id = :uye_id AND urun_id = :urun_id", [
        'uye_id' => $uye_id,
        'urun_id' => $urun_id
    ]);

    // Ürünün stok bilgisi
    $urun_stok = $database->fetchColumn("SELECT stok FROM nokta_urunler WHERE id = :urun_id", [
        'urun_id' => $urun_id
    ]);

    // Sepetteki mevcut adet
    $sepet_adet = $database->fetchColumn("SELECT adet FROM uye_sepet WHERE urun_id = :urun_id AND uye_id = :uye_id", [
        'urun_id' => $urun_id,
        'uye_id' => $uye_id
    ]);

    if ($existingAdet !== false) { // Eğer ürün sepette varsa
        $newAdet = $existingAdet + $adet;
        $database->update("UPDATE uye_sepet SET adet = :adet WHERE uye_id = :uye_id AND urun_id = :urun_id", [
            'adet' => $newAdet,
            'uye_id' => $uye_id,
            'urun_id' => $urun_id
        ]);
    } else { // Ürün sepette yoksa yeni ekle
        $database->insert("INSERT INTO uye_sepet (uye_id, urun_id, adet) VALUES (:uye_id, :urun_id, :adet)", [
            'uye_id' => $uye_id,
            'urun_id' => $urun_id,
            'adet' => $adet
        ]);
    }
}
function ebultenKaydet() {
    $database = new Database();
    if (isset($_POST["ebulten_mail"])) {
        $email = controlInput($_POST["ebulten_mail"]);
        // Şu anki tarih ve saat için MySQL uyumlu formatı al
        $currentDate = date("Y-m-d H:i:s", strtotime("+3 hours"));
        $database->insert("INSERT IGNORE INTO nokta_ebulten (email, create_date, site) VALUES (:email, :create_date, 'comtr')" , ['email' => $email, 'create_date' => $currentDate]);
        // Mail gönderme fonksiyonunu çağır
        saveToMailjet($email, 368582);
        echo json_encode(['cvp' => 'success']);
    }
}
function editAriza() {
    $db = new Database();
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? controlInput($_POST['id']) : null;
    $musteri = controlInput($_POST['musteri']);
    $tel = controlInput($_POST['tel']);
    $email = controlInput($_POST['email']);
    $adres = controlInput($_POST['adres']);
    $urun_kodu_raw = controlInput($_POST['urun_kodu']);
    $seri_no_raw = controlInput($_POST['seri_no']);
    $adet_raw = controlInput($_POST['adet']);
    $aciklama = controlInput($_POST['aciklama']);
    $ad_soyad = controlInput($_POST['ad_soyad']);
    $fatura_no = controlInput($_POST['fatura_no']);
    $teslim_alan = !empty($_POST['teslim_alan']) ? controlInput($_POST['teslim_alan']) : null;
    $kargo_firmasi = controlInput($_POST['kargo_firmasi']);
    $gonderim_sekli = controlInput($_POST['gonderim_sekli']);
    $onay = controlInput($_POST['onay']);

    $durum = '1';
    $gunceltarih = date("ymd");
    $takip_kodu = 'NEB' . $gunceltarih . random_int(1000, 9999);
    $SILINDI = 0;
    $tekniker = 0;

    if (empty($urun_kodu_raw)) {
        http_response_code(400);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(500);
        exit();
    }

    $urun_kodu_array = explode(',', $urun_kodu_raw);
    $seri_no_array = explode(',', $seri_no_raw);
    $adet_array = explode(',', $adet_raw);

    if (!empty($musteri) && !empty($tel) && !empty($email) && !empty($adres) && !empty($ad_soyad) && !empty($aciklama) && ($onay == '1') && !empty($gonderim_sekli)) {
        if ($gonderim_sekli == '1' && empty($kargo_firmasi)) {
            http_response_code(400);
            return;
        }

        $params = [
            'takip_kodu' => $takip_kodu,
            'fatura_no' => $fatura_no,
            'musteri' => $musteri,
            'tel' => $tel,
            'mail' => $email,
            'adres' => $adres,
            'aciklama' => $aciklama,
            'teslim_eden' => $ad_soyad,
            'SILINDI' => $SILINDI,
            'gonderim_sekli' => $gonderim_sekli,
            'kargo_firmasi' => $kargo_firmasi,
            'tekniker' => $tekniker
        ];

        if (!is_null($id)) {
            $params[':uye_id'] = $id;

            $query = "INSERT INTO nokta_teknik_destek 
                (uye_id, takip_kodu, fatura_no, musteri, tel, mail, adres, aciklama, teslim_eden, SILINDI, gonderim_sekli, kargo_firmasi, tekniker) 
                VALUES (:uye_id, :takip_kodu, :fatura_no, :musteri, :tel, :mail, :adres, :aciklama, :teslim_eden, :SILINDI, :gonderim_sekli, :kargo_firmasi, :tekniker)";
        } else {
            $params['teslim_alan'] = $teslim_alan;
            $query = "INSERT INTO nokta_teknik_destek 
                (takip_kodu, fatura_no, musteri, tel, mail, adres, aciklama, teslim_eden, teslim_alan, SILINDI, gonderim_sekli, kargo_firmasi, tekniker) 
                VALUES (:takip_kodu, :fatura_no, :musteri, :tel, :mail, :adres, :aciklama, :teslim_eden, :teslim_alan, :SILINDI, :gonderim_sekli, :kargo_firmasi, :tekniker)";
        }

        $db->insert($query, $params);
        $lastInsertId = $db->lastInsertId();

        // Ürünleri teknik_destek_urunler tablosuna ekle
        foreach ($urun_kodu_array as $index => $urun_kodu) {
            $seri_no = isset($seri_no_array[$index]) ? $seri_no_array[$index] : '';
            $adet = isset($adet_array[$index]) ? $adet_array[$index] : '';

            $urun_params = [
                'tdp_id' => $lastInsertId,
                'urun_kodu' => $urun_kodu,
                'seri_no' => $seri_no,
                'adet' => $adet,
                'urun_durumu' => "1",
                'SILINDI' => $SILINDI
            ];

            $urun_query = "INSERT INTO teknik_destek_urunler 
                (tdp_id, urun_kodu, seri_no, adet, urun_durumu, SILINDI) 
                VALUES (:tdp_id, :urun_kodu, :seri_no, :adet, :urun_durumu, :SILINDI)";

            $db->insert($urun_query, $urun_params);
        }
        echo $takip_kodu;

        // Mail gönder
        //$mail_icerik = arizaKayitMail($musteri, $takip_kodu);
        //mailGonder($email, 'Arıza Kaydınız Alınmıştır!', $mail_icerik, 'Nokta Elektronik');
    } else {
        http_response_code(400);
        exit();
    }
}

//////////////////////////////////////////////////
//////////KULLANILANLAR YUKARIDA//////////////////
//////////////////////////////////////////////////
if(isset($_POST["ozelbanner1"])) {
    global $db;
    $image11 = $_FILES["image1-1"]['name'];
    $image12 = $_FILES["image1-2"]['name'];
    $image21 = $_FILES["image2-1"]['name'];
    $image22 = $_FILES["image2-2"]['name'];
    $image31 = $_FILES["image3-1"]['name'];
    $image32 = $_FILES["image3-2"]['name'];
    $image33 = $_FILES["image3-3"]['name'];
    $image41 = $_FILES["image4-1"]['name'];
    $image42 = $_FILES["image4-2"]['name'];
    $image43 = $_FILES["image4-3"]['name'];
    $image51 = $_FILES["image5-1"]['name'];
    $image52 = $_FILES["image5-2"]['name'];
    $image53 = $_FILES["image5-3"]['name'];
    $image61 = $_FILES["image6-1"]['name'];
    $image62 = $_FILES["image6-2"]['name'];
    $image63 = $_FILES["image6-3"]['name'];

    $link11 = $_POST["link1-1"];
    $link12 = $_POST["link1-2"];
    $link21 = $_POST["link2-1"];
    $link22 = $_POST["link2-2"];
    $link31 = $_POST["link3-1"];
    $link32 = $_POST["link3-2"];
    $link33 = $_POST["link3-3"];
    $link41 = $_POST["link4-1"];
    $link42 = $_POST["link4-2"];
    $link43 = $_POST["link4-3"];
    $link51 = $_POST["link5-1"];
    $link52 = $_POST["link5-2"];
    $link53 = $_POST["link5-3"];
    $link61 = $_POST["link6-1"];
    $link62 = $_POST["link6-2"];
    $link63 = $_POST["link6-3"];

    if ($image11) {
        $image1_1 = validateAndSaveImage($_FILES["image1-1"], '../assets/images/banner/');
        if ($image1_1 !== false) {
            $query = "UPDATE banner_modal SET foto1 = ?, link1 = ? WHERE id = 1";
            $stmt = $db->prepare($query);
            $stmt->execute([$image1_1, $link11]);
        }
    }else{
        $query = "UPDATE banner_modal SET link1 = ? WHERE id = 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$link11]);
    }
    if ($image12) {
        $image1_2 = validateAndSaveImage($_FILES["image1-2"], '../assets/images/banner/');
        if ($image1_2 !== false) {
            $query = "UPDATE banner_modal SET foto2 = ?, link2 = ?  WHERE id = 1";
            $stmt = $db->prepare($query);
            $stmt->execute([$image1_2, $link12]);}
    }else{
        $query = "UPDATE banner_modal SET link2 = ? WHERE id = 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$link12]);
    }
    if ($image21) {
        $image2_1 = validateAndSaveImage($_FILES["image2-1"], '../assets/images/banner/');
        if ($image2_1 !== false) {
            $query = "UPDATE banner_modal SET foto1 = ?, link1 = ? WHERE id = 2";
            $stmt = $db->prepare($query);
            $stmt->execute([$image2_1, $link21]);
        }
    }else{
        $query = "UPDATE banner_modal SET link1 = ? WHERE id = 2";
        $stmt = $db->prepare($query);
        $stmt->execute([$link21]);
    }
    if ($image22) {
        $image2_2 = validateAndSaveImage($_FILES["image2-2"], '../assets/images/banner/');
        if ($image2_2 !== false) {
            $query = "UPDATE banner_modal SET foto2 = ?, link2 = ?  WHERE id = 2";
            $stmt = $db->prepare($query);
            $stmt->execute([$image2_2, $link22]);}
    }else{
        $query = "UPDATE banner_modal SET link2 = ? WHERE id = 2";
        $stmt = $db->prepare($query);
        $stmt->execute([$link22]);
    }
    if ($image31) {
        $image3_1 = validateAndSaveImage($_FILES["image3-1"], '../assets/images/banner/');
        if ($image3_1 !== false) {
            $query = "UPDATE banner_modal SET foto1 = ?, link1 = ? WHERE id = 3";
            $stmt = $db->prepare($query);
            $stmt->execute([$image3_1, $link31]);
        }
    }else{
        $query = "UPDATE banner_modal SET link1 = ? WHERE id = 3";
        $stmt = $db->prepare($query);
        $stmt->execute([$link31]);
    }
    if ($image32) {
        $image3_2 = validateAndSaveImage($_FILES["image3-2"], '../assets/images/banner/');
        if ($image3_2 !== false) {
            $query = "UPDATE banner_modal SET foto2 = ?, link2 = ? WHERE id = 3";
            $stmt = $db->prepare($query);
            $stmt->execute([$image3_2, $link32]);
        }
    }else{
        $query = "UPDATE banner_modal SET link2 = ? WHERE id = 3";
        $stmt = $db->prepare($query);
        $stmt->execute([$link32]);
    }
    if ($image33) {
        $image3_3 = validateAndSaveImage($_FILES["image3-3"], '../assets/images/banner/');
        if ($image3_3 !== false) {
            $query = "UPDATE banner_modal SET foto3 = ?, link3 = ? WHERE id = 3";
            $stmt = $db->prepare($query);
            $stmt->execute([$image3_3, $link33]);
        }
    }else{
        $query = "UPDATE banner_modal SET link3 = ? WHERE id = 3";
        $stmt = $db->prepare($query);
        $stmt->execute([$link33]);
    }
    if ($image41) {
        $image4_1 = validateAndSaveImage($_FILES["image4-1"], '../assets/images/banner/');
        if ($image4_1 !== false) {
            $query = "UPDATE banner_modal SET foto1 = ?, link1 = ? WHERE id = 4";
            $stmt = $db->prepare($query);
            $stmt->execute([$image4_1, $link41]);
        }
    }else{
        $query = "UPDATE banner_modal SET link1 = ? WHERE id = 4";
        $stmt = $db->prepare($query);
        $stmt->execute([$link41]);
    }
    if ($image42) {
        $image4_2 = validateAndSaveImage($_FILES["image4-2"], '../assets/images/banner/');
        if ($image4_2 !== false) {
            $query = "UPDATE banner_modal SET foto2 = ?, link2 = ? WHERE id = 4";
            $stmt = $db->prepare($query);
            $stmt->execute([$image4_2, $link42]);
        }
    }else{
        $query = "UPDATE banner_modal SET link2 = ? WHERE id = 4";
        $stmt = $db->prepare($query);
        $stmt->execute([$link42]);
    }
    if ($image43) {
        $image4_3 = validateAndSaveImage($_FILES["image4-3"], '../assets/images/banner/');
        if ($image4_3 !== false) {
            $query = "UPDATE banner_modal SET foto3 = ?, link3 = ? WHERE id = 4";
            $stmt = $db->prepare($query);
            $stmt->execute([$image4_3, $link43]);
        }
    }else{
        $query = "UPDATE banner_modal SET link3 = ? WHERE id = 4";
        $stmt = $db->prepare($query);
        $stmt->execute([$link43]);
    }
    if ($image51) {
        $image5_1 = validateAndSaveImage($_FILES["image5_1"], '../assets/images/banner/');
        if ($image5_1 !== false) {
            $query = "UPDATE banner_modal SET foto1 = ?, link1 = ? WHERE id = 5";
            $stmt = $db->prepare($query);
            $stmt->execute([$image5_1, $link51]);
        }
    }else{
        $query = "UPDATE banner_modal SET link1 = ? WHERE id = 5";
        $stmt = $db->prepare($query);
        $stmt->execute([$link51]);
    }
    if ($image52) {
        $image5_2 = validateAndSaveImage($_FILES["image5_2"], '../assets/images/banner/');
        if ($image5_2 !== false) {
            $query = "UPDATE banner_modal SET foto2 = ?, link2 = ? WHERE id = 5";
            $stmt = $db->prepare($query);
            $stmt->execute([$image5_2, $link52]);
        }
    }else{
        $query = "UPDATE banner_modal SET link2 = ? WHERE id = 5";
        $stmt = $db->prepare($query);
        $stmt->execute([$link52]);
    }
    if ($image53) {
        $image5_3 = validateAndSaveImage($_FILES["image5_3"], '../assets/images/banner/');
        if ($image5_3 !== false) {
            $query = "UPDATE banner_modal SET foto3 = ?, link3 = ? WHERE id = 5";
            $stmt = $db->prepare($query);
            $stmt->execute([$image5_3, $link53]);
        }
    }else{
        $query = "UPDATE banner_modal SET link3 = ? WHERE id = 5";
        $stmt = $db->prepare($query);
        $stmt->execute([$link53]);
    }
    if ($image61) {
        $image6_1 = validateAndSaveImage($_FILES["image6_1"], '../assets/images/banner/');
        if ($image6_1 !== false) {
            $query = "UPDATE banner_modal SET foto1 = ?, link1 = ? WHERE id = 6";
            $stmt = $db->prepare($query);
            $stmt->execute([$image6_1, $link61]);
        }
    }else{
        $query = "UPDATE banner_modal SET link1 = ? WHERE id = 5";
        $stmt = $db->prepare($query);
        $stmt->execute([$link61]);
    }
    if ($image62) {
        $image6_2 = validateAndSaveImage($_FILES["image6_2"], '../assets/images/banner/');
        if ($image6_2 !== false) {
            $query = "UPDATE banner_modal SET foto2 = ?, link2 = ? WHERE id = 6";
            $stmt = $db->prepare($query);
            $stmt->execute([$image6_2, $link62]);
        }
    }else{
        $query = "UPDATE banner_modal SET link2 = ? WHERE id = 6";
        $stmt = $db->prepare($query);
        $stmt->execute([$link62]);
    }
    if ($image63) {
        $image6_3 = validateAndSaveImage($_FILES["image6_3"], '../assets/images/banner/');
        if ($image6_3 !== false) {
            $query = "UPDATE banner_modal SET foto3 = ?, link3 = ? WHERE id = 6";
            $stmt = $db->prepare($query);
            $stmt->execute([$image6_3, $link63]);
        }
    }else{
        $query = "UPDATE banner_modal SET link3 = ? WHERE id = 6";
        $stmt = $db->prepare($query);
        $stmt->execute([$link63]);
    }
    header("Location:../admin/siteduzenleme/adminOzelBanner");
}

function editBannerVideo() {
    $bId = $_POST['id'];
    $bLink = $_POST['bannerLink'];
    $bGorsel = $_FILES['bannerVideo']['name'];
    $aktif = 1;

    global $db;
    if ($bId) {
        if (!empty($bGorsel) && $image = validateAndSaveVideo($_FILES['bannerVideo'], '../assets/uploads/videolar/')) {
            $query = "UPDATE nokta_banner_video SET banner_link = ?, banner_video = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$bLink, $image, $bId]);
        } else {
            $query = "UPDATE nokta_banner_video SET banner_link = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$bLink, $bId]);
        }
    } else {
        if (!empty($bGorsel) && $image = validateAndSaveVideo($_FILES['bannerVideo'], '../assets/uploads/videolar/')) {
            // File upload successful
        } else {
            $image = ''; // If no file was uploaded or there was an error, set it to an empty string
        }
        $query = "INSERT INTO nokta_banner_video (banner_link, banner_video, aktif) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$bLink, $image, $aktif]);
    }
}

function uyeAdresDuzenle() {
    $adresId = controlInput($_POST['adresId']);
    $adres_basligi = controlInput($_POST['adres_basligi']);
    $ad = controlInput($_POST['ad']);
    $soyad = controlInput($_POST['soyad']);
    $tel = controlInput($_POST['tel']);
    $adres = controlInput($_POST['adres']);
    $ulke = controlInput($_POST['ulke']);
    $il = controlInput($_POST['il']);
    $ilce = controlInput($_POST['ilce']);
    $posta_kodu = controlInput($_POST['posta_kodu']);
    $uyeId = controlInput($_POST['uyeId']);
    $adres_turu = 'teslimat';

    global $db;
        if ($adresId) {
            $query = "UPDATE adresler SET uye_id = ?, adres_turu = ?, adres_basligi = ?, ad = ? , soyad = ? , adres = ? , telefon = ? , ulke = ? , il = ? , ilce = ? , posta_kodu = ?  WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$uyeId, $adres_turu, $adres_basligi, $ad, $soyad, $adres, $tel, $ulke, $il, $ilce, $posta_kodu, $adresId]);
        } else {
            $query = "INSERT INTO adresler (uye_id, adres_turu, adres_basligi, ad, soyad, adres, telefon, ulke, il, ilce, posta_kodu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$uyeId, $adres_turu, $adres_basligi, $ad, $soyad, $adres, $tel, $ulke, $il, $ilce, $posta_kodu]);
        }
}

function sepetAdres() {
    global $db;
    $adresId = $_POST['id'];
    $adres_basligi = $_POST['adres_basligi'];
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $tel = $_POST['telefon'];
    $adres = $_POST['adres'];
    $ulke = $_POST['ulke'];
    $il = $_POST['il'];
    $ilce = $_POST['ilce'];
    $posta_kodu = $_POST['posta_kodu'];
    $uyeId = $_POST['uye_id'];
    $adres_turu = 'teslimat';

    $q = $db->prepare("SELECT * FROM uyeler WHERE id = ?");
    $q->execute([$uyeId]);
    $uyeler = $q->fetchColumn();
    $firma_adi = $uyeler["firmaUnvani"];
    $tc_no = $uyeler["tc_no"];
    $vergi_no = $uyeler["vergi_no"];
    $vergi_dairesi = $uyeler["vergi_dairesi"];

    global $db;
    if ($adresId) {
        $query = "UPDATE adresler SET uye_id = ?, adres_turu = ?, adres_basligi = ?, ad = ? , soyad = ? , adres = ? , telefon = ? , ulke = ? , il = ? , ilce = ? , posta_kodu = ?  WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$uyeId, $adres_turu, $adres_basligi, $ad, $soyad, $adres, $tel, $ulke, $il, $ilce, $posta_kodu, $adresId]);
    } else {
        $query = "INSERT INTO adresler (firma_adi, tc_no, vergi_no, vergi_dairesi, uye_id, adres_turu, adres_basligi, ad, soyad, adres, telefon, ulke, il, ilce, posta_kodu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$firma_adi, $tc_no, $vergi_no, $vergi_dairesi, $uyeId, $adres_turu, $adres_basligi, $ad, $soyad, $adres, $tel, $ulke, $il, $ilce, $posta_kodu]);
    }
}
function adresSec() {
    global $db;
    $addressId = $_POST['addressId'];
    $sessionId = $_POST['sessionId'];
    $aktif1 = 1;
    $aktif0 = 0;

    // Set active to 1 for the selected address
    $updateSelectedQuery = $db->prepare("UPDATE adresler SET aktif = :aktif1 WHERE id = :addressId AND uye_id = :sessionId");
    $updateSelectedQuery->bindParam(':aktif1', $aktif1, PDO::PARAM_INT);
    $updateSelectedQuery->bindParam(':addressId', $addressId, PDO::PARAM_INT);
    $updateSelectedQuery->bindParam(':sessionId', $sessionId, PDO::PARAM_INT);
    $updateSelectedQuery->execute();

    // Set active to 0 for other addresses
    $updateOthersQuery = $db->prepare("UPDATE adresler SET aktif = :aktif0 WHERE id != :addressId AND uye_id = :sessionId");
    $updateOthersQuery->bindParam(':aktif0', $aktif0, PDO::PARAM_INT);
    $updateOthersQuery->bindParam(':addressId', $addressId, PDO::PARAM_INT);
    $updateOthersQuery->bindParam(':sessionId', $sessionId, PDO::PARAM_INT);
    $updateOthersQuery->execute();
}

function iade() {
    global $db;

    $iade = 1;
    $uye_id = $_POST['uye_id'];
    $sip_id = $_POST['sip_urun_id'];
    $iade_nedeni = $_POST['iade_nedeni'];
    $siparisNumarasi = $_POST['sip_no'];

    $q = $db->prepare("SELECT * FROM uyeler WHERE id = ?");
    $q->execute([$uye_id]);
    $uye = $q->fetch(PDO::FETCH_ASSOC);

    $uyeAdSoyad = $uye["ad"] . ' ' . $uye["soyad"];
    $uye_email = $uye["email"];

    $insertQuery = "INSERT INTO iadeler (sip_urun_id, uye_id, iade_nedeni, durum) VALUES (?, ?, ?, ?)";
    $insertStatement = $db->prepare($insertQuery);
    $insertStatement->execute([$sip_id, $uye_id, $iade_nedeni, $iade]);

    $updateQuery = "UPDATE siparis_urunler SET iade = ? WHERE id = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([$iade, $sip_id]);

    $mail_icerik = iadeAlindiMail($uyeAdSoyad, $siparisNumarasi);
    mailGonder($uye_email, 'İade Talebiniz Alınmıştır!', $mail_icerik, 'Nokta Elektronik');
    exit;
}
function adresAktif(){
    global $db;
    $adresId = $_POST['adres_id'];
    $aktif = $_POST['aktif'];
    $uye_id = $_POST['uye_id'];

    // Güncelleme sorgusu örneği (PDO kullanarak)
    $q = $db->prepare("UPDATE adresler SET aktif = :aktif WHERE id = :id AND uye_id = :uye_id");
    $q->execute(array(':aktif' => $aktif, ':id' => $adresId, ':uye_id' => $uye_id));

    $q = $db->prepare("UPDATE adresler SET aktif = '0' WHERE id != :id AND uye_id = :uye_id");
    $q->execute(array(':id' => $adresId, ':uye_id' => $uye_id));

    // Başarı durumunu kontrol edebilirsiniz
    if ($q->rowCount() > 0) {
        echo "Adres aktif durumu güncellendi.";
    } else {
        echo "Adres güncellenemedi.";
    }
}
function loglar(){
    global $db;
    $q = $db->prepare("SELECT * FROM api_log ORDER BY id DESC LIMIT 50");
    $q->execute();
    $d = $q->fetchAll();
    $db = null;
    // Tablo oluştur
    $table_html = '<table class="table" >
                        <tbody>';
    foreach ($d as $row) {
        $table_html .= '<tr>
                            <td style="font-size: 12px">' . $row['log'] . '</td>
                        </tr>';
    }
    $table_html .= '</tbody></table>';
    echo $table_html;
}

if (isset($_POST['type'])) {
  $type = $_POST['type'];
  if ($type === 'ariza') {
    editAriza();
      exit;
  }elseif ($type === 'bannerVideo') {
      editBannerVideo();
    exit;
  }
  elseif ($type === 'favori') {
    editFavori();
    exit;
  }
  elseif ($type === 'sepeteFavoriEkle') {
    sepeteFavoriEkle();
    exit;
  }
  elseif ($type === 'adresGuncelle') {
    uyeAdresDuzenle();
    exit;
  }
  elseif ($type === 'adresEkle') {
      uyeAdresEkle();
      exit;
  }
  elseif ($type === 'sepeteUrunEkle') {
    sepeteUrunEkle();
    exit;
  }
  elseif ($type === 'sepetAdres') {
    sepetAdres();
    exit;
  }elseif ($type === 'adresSec') {
    adresSec();
    exit;
  }elseif ($type === 'iade') {
      iade();
      exit;
  }elseif ($type === 'teklif') {
      teklif();
      exit;
  }elseif ($type === 'loglar') {
      loglar();
      exit;
  }elseif ($type === 'adresAktif') {
        adresAktif();
        exit;
  }elseif ($type === 'ebulten_kaydet') {
        ebultenKaydet();
    exit;
}
}
  ?>