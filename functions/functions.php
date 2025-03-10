<?php
require_once "db.php";
$db = new Database();
// uploadImageToS3 fonksiyonunu dosya yolu ile yükleme için düzenleyin
function uploadImageToS3($file_path, $upload_path, $s3Client, $bucket) {
    try {
        // S3 yükleme yolu
        $s3_file_path = $upload_path . basename($file_path); // Dosyanın basename'ini S3'e koyuyoruz

        // Dosyayı S3'e yükleyin
        $result = $s3Client->putObject([
            'Bucket' => $bucket,
            'Key'    => $s3_file_path,
            'SourceFile' => $file_path // SourceFile için dosya yolunu geçiyoruz
        ]);

        // Yükleme başarılı ise dosya adını veya URL'yi döndürüyoruz
        return basename($file_path); // veya $result['ObjectURL'] dönebilirsiniz, S3 URL'si için
    } catch (AwsException $e) {
        error_log('S3 yükleme hatası: ' . $e->getMessage());
        return false;
    }
}
function IP(){
    if(getenv("HTTP_CLIENT_IP")){
        $ip = getenv("HTTP_CLIENT_IP");
    }
    elseif(getenv("HTTP_X_FORWARDED_FOR")){
        $ip = getenv("HTTP_X_FORWARDED_FOR");
        if(strstr($ip, ',')){
            $tmp = explode (',',$ip);
            $ip = trim($tmp[0]);
        }
    }
    else{
        $ip = getenv("REMOTE_ADDR");
    }
    return $ip;
}
function controlInput($input) {
    $input = trim($input); // Boşlukları temizle (en başta ve en sonda)
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8'); // HTML ve script etiketlerini temizle
    return $input;
}
/* Iletisim Formu*/
if (isset($_POST["iletisim_form_btn"])) {

    include '../mail/mail_gonder.php';

    $adsoyad = controlInput($_POST['adsoyad']);
    $mail = controlInput($_POST['mail']);
    $text = controlInput($_POST['text']);
    $lang = controlInput($_POST['lang']);
    $now = date("Y-m-d H:i:s");
    $query = $db->prepare("
    INSERT  IGNORE INTO nokta_iletisim_form SET 
                        ad_soyad         = :d1,
                        eposta           = :d2,  
                        aciklama         = :d3
            ");
    $insert = $query->execute(array(
                        "d1"            => "{$adsoyad}",
                        "d2"            => "{$mail}",
                        "d3"            => "{$text}"
    ));
    header("Location: iletisim?lang=$lang&s=20");
    $mail_icerik = iletisimFormMail($adsoyad, $mail, $now, $text);
    mailGonder('b2b@noktaelektronik.com.tr', 'İletişim formu mesaj', $mail_icerik, 'Nokta Elektronik');
}
/* ebulten Formu*/
function ebultenKaydet() {
    global $db;

    if (isset($_POST["ebulten_mail"])) {
        $email = controlInput($_POST["ebulten_mail"]);

        // Şu anki tarih ve saat için MySQL uyumlu formatı al
        $currentDate = date("Y-m-d H:i:s", strtotime("+3 hours"));

            $query = $db->prepare("
                INSERT IGNORE INTO nokta_ebulten (email, create_date) 
                VALUES (:email, :create_date)
            ");

            $query->bindParam(":email", $email);
            $query->bindParam(":create_date", $currentDate);
            $query->execute();

            // Mail gönderme fonksiyonunu çağır
            saveToMailjet($email, 368582);

            echo json_encode(['cvp' => 'success']);
    }
}
if (isset($_POST['takip_kodu'])) {
    $takip_kodu = controlInput($_POST['takip_kodu']);

    $q = $db->prepare("SELECT * FROM nokta_teknik_destek WHERE takip_kodu = :takip_kodu");
    $q->execute(['takip_kodu' => $takip_kodu]);
    $results = $q->fetchAll();


    if ($results) {

        echo '<div class="card-body">';
        foreach ($results as $row) {
            $tdp_id = $row["id"];
            // Display takip_kodu and tarih above the table
            echo '<div class="mb-3">';
            echo '<strong>Takip Kodu:</strong> ' . $row['takip_kodu'] . '<br>';
            echo '<strong>Kayıt Tarihi:</strong> ' . $row['tarih'] . '<br>';
            echo '</div>';

            // Create the table
            echo '<table class="table table-stripped">';
            echo '<thead><tr><th>Ürün Kodu</th><th>Durumu</th><th>Yapılan İşlemler</th></tr></thead>';
            echo '<tbody>';

            $q = $db->prepare("SELECT * FROM teknik_destek_urunler WHERE tdp_id = :tdp_id");
            $q->execute(['tdp_id' => $tdp_id]);
            $results = $q->fetchAll();

            // Iterate through each product code
            foreach ($results as $key => $result) {
                // Fetch the status
                $stmt = $db->prepare("SELECT durum FROM nokta_teknik_durum WHERE id = :id");
                $stmt->execute(['id' => $result["urun_durumu"]]);
                $durum = $stmt->fetch(PDO::FETCH_ASSOC);

                // Display the row
                if ($durum) {
                    // Display the row
                    echo '<tr>';
                    echo '<td>' . $result["urun_kodu"] . '</td>';
                    echo '<td>' . $durum['durum'] . '</td>';
                    echo '<td>' . $result["yapilan_islemler"] . '</td>';
                    if (!empty($result['foto'])) {
                        $images = explode(',', $result['foto']);
                        echo '<strong>Fotoğraflar:</strong><br>';
                        foreach ($images as $image) {
                            echo '<td><img src="assets/images/teknik-destek/' . trim($image) . '" class="img-thumbnail" style="max-width: 150px; margin: 5px;"></td>';
                        }
                        echo '<br>';
                    }
                    echo '</tr>';
                } else {
                    // Handle the case where $durum is empty or false
                    echo '<tr>';
                    echo '<td>' . $result["urun_kodu"] . '</td>';
                    echo '<td>Bilinmiyor</td>'; // or any default value you want to show
                    echo '</tr>';
                }
            }

            echo '</div>';

            echo '</tbody>';
            echo '</table>';
        }
        echo '</div>';
    } else {
        echo '<div class="card-body">';
        echo '<p>Hatalı Arıza Takip Kodu!</p>';
        echo '</div>';
    }
}
function validateAndSaveImage($file, $upload_path) {
    // Dosya Türü Doğrulama
    $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }

    // Maks. Dosya boyutu
    $max_file_size = 6 * 1024 * 1024; // 6MB in bytes
    if ($file["size"] > $max_file_size) {
        return false;
    }

    // Özel isim oluşturma
    $unique_filename = uniqid() . '.webp';
    $uploadPath = $upload_path . $unique_filename;

    // Görüntüyü oluştur
    switch ($file['type']) {
        case 'image/jpeg':
        case 'image/jpg':
            $image = @imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($file['tmp_name']);
            break;
        case 'image/gif':
            $image = @imagecreatefromgif($file['tmp_name']);
            break;
        default:
            return false; // Desteklenmeyen dosya türü
    }

    if (!$image) {
        return false; // Görüntü oluşturulamadı
    }

    // WebP olarak kaydet (kaliteyi ayarlayın, 0-100 arasında bir değer)
    $quality = 80; // Örneğin, %80 kalite
    if (imagewebp($image, $uploadPath, $quality)) {
        imagedestroy($image); // Temizle
        return $unique_filename; // Başarılı ise dosya adını döndür
    }

    imagedestroy($image); // Temizle
    return false; // Kaydetme başarısız oldu
}
function validateAndSaveFile($file, $upload_path) {

        // Dosya Türü Doğrulama
        $allowedTypes = array('application/pdf', 'application/msword', 'text/plain');
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        // Maks. Dosya boyutu
        $max_file_size = 20 * 1024 * 1024; // 6MB in bytes
        if ($file["size"] > $max_file_size) {
            return false; 
        }
        // Özel isim oluşturma
        $unique_filename = uniqid() . '_' . $file['name'];

        // Save the file to a folder
        $uploadPath = $upload_path . $unique_filename;
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $unique_filename; // Return the unique filename with extension to be stored in the database
        }else{
            return false;
        }
}
function validateAndSaveVideo($file, $upload_path) {
    // Video Dosyası Türlerini Doğrulama
    $allowedTypes = array(
        'video/mp4',
        'video/avi',
        'video/mpeg',
        'video/quicktime',
        'video/x-msvideo'
    );
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }

    // Maks. Dosya Boyutu
    $max_file_size = 100 * 1024 * 1024; // 100MB in bytes
    if ($file["size"] > $max_file_size) {
        return false;
    }

    // Özel İsim Oluşturma
    $unique_filename = uniqid() . '_' . basename($file['name']);

    // Dosyayı Klasöre Kaydetme
    $uploadPath = $upload_path . $unique_filename;
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return $unique_filename; // Unique filename with extension to be stored in the database
    } else {
        return false;
    }
}
/////////////////////////////////////
////ALTTAKİLER DÜZGÜN//////////
function formatVirgulluNumber($number){
    // String'i float'a dönüştür
    $number = str_replace(',', '.', $number);
    $number = (float) $number;
    // Virgülü ekleyerek düzenle
    $formatted_number = number_format($number, 2, ',', '.');
    return $formatted_number;
}
function formatNumber($number) {
    // Veritabanından gelen sayı formatı kontrol et
    if (!is_numeric($number)) {
        return null;
    }
    // Sayıyı formatla
    $formattedNumber = number_format($number, 2, ',', '.');
    return $formattedNumber;
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
    // Sayıyı DECIMAL(13,2) formatına getir
    $sayi = number_format((float)$sayi, 2, '.', '');
    return $sayi;
}
function duzenleString1($str) {
    $replaceChars = array(
        'ç' => 'c', 'ğ' => 'g',
        'ı' => 'i', 'i' => 'i',
        'ö' => 'o', 'ş' => 's',
        'ü' => 'u', 'Ç' => 'C',
        'Ğ' => 'G', 'I' => 'I',
        'İ' => 'I', 'Ö' => 'O',
        'Ş' => 'S', 'Ü' => 'U',
        ' ' => '-', '"' => '',
        "'" => '', '`' => '',
        '.' => '', ',' => '',
        ':' => '', ';' => '',
        '(' => '', ')' => '',
        '[' => '', ']' => '',
        '{' => '', '}' => '',
        '+' => '', '&' => '',
        '\\' => ''
    );
    $str = strtr($str, $replaceChars);
    // Türkçe harfleri İngilizce harflere çevir
    // $str = str_replace(
    //    ['ç', 'ğ', 'ı', 'i', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'I', 'İ', 'Ö', 'Ş', 'Ü', '+', '-', '/', '(', ')', '\\', ',' ,'"'],
    //    ['c', 'g', 'i', 'i', 'o', 's', 'u', 'C', 'G', 'I', 'I', 'O', 'S', 'U', '', '', '-', '', '', '-', '', ''],
    //    $str
    //);
    $str = strtolower($str);// Büyük harfleri küçük harfe çevir
    $str = trim($str);// Başındaki ve sonundaki boşlukları sil
    $str = preg_replace('/\s+/', '-', $str); // Ortadaki boşlukları - ile değiştir
    $str = str_replace( ['---','--'], ['-','-'], $str );
    return $str;
}
// Kullanıcının hangi sayfada olduğunu ve IP adresini güncelle
function updateUserPage($userId, $pageName, $ipAddress) {
    $db = new Database();

    $countResult1 = $db->fetch("SELECT satis_temsilcisi FROM uyeler WHERE id = :user_id", ['user_id' => $userId]);
    $satis_temsilcisi = $countResult1['satis_temsilcisi'];

    // Kullanıcı, sayfa ve IP bilgilerini güncelle
    $stmt = $db->insert("REPLACE INTO user_pages (user_id, page_name, ip_address, satis_temsilcisi) VALUES (:user_id, :page_name, :ip_address, :st)" ,
     ['user_id' => $userId, 'page_name' => $pageName, 'ip_address' => $ipAddress, 'st' => $satis_temsilcisi]);
}
if (isset($_POST['sifre_unuttum'])) {
    // Validate email input
    $mail = filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL);

    if (!$mail) {
        echo 'invalid_email';  // Return an error if email is not valid
        exit();
    }
    include '../mail/mail_gonder.php';

    // Fetch user data
    $userData = $db->fetch("SELECT * FROM uyeler WHERE email = :email", ['email' => $mail]);

    // Check if user exists
    if ($userData) {
        // Proceed with password reset logic
        $uye_id = $userData['id'];
        $ad = $userData['ad'];
        $soyad = $userData['soyad'];
        $adsoyad = $ad . ' ' . $soyad;
        
        // Generate a unique code for password reset
        $uniqKod = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 20);

        // Insert the reset code into the database
        $insertResult = $db->insert("INSERT INTO sifre_degistirme (uye_id, kod) VALUES (:uye_id, :kod)", 
                                    ['uye_id' => $uye_id, 'kod' => $uniqKod]);

        if ($insertResult) {
            // Send reset email
            $mail_icerik = sifreDegistimeMail($adsoyad, $uniqKod);
            mailGonder($mail, 'Şifre Sıfırlama!', $mail_icerik, 'Şifre Sıfırlama!');
            echo 'success';  // Return success message
        } else {
            echo 'db_error';  // Return an error if inserting into the database fails
        }
    } else {
        echo 'error';  // User not found in database
    }
}





?>