<?php 
require_once '../db.php';
$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $response = array();
    
    // Validate required fields
    $required_fields = ['uye_id', 'ad', 'soyad', 'email', 'tel', 'firmaUnvani'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $response = [
                'status' => 'error',
                'message' => 'Lütfen tüm zorunlu alanları doldurun.'
            ];
            echo json_encode($response);
            exit;
        }
    }

    $uye_id = $_POST['uye_id'];

    // Check if email exists for another user
    $existing_user = $db->fetch("SELECT id FROM uyeler WHERE email = :email AND id != :id", [
        'email' => $_POST['email'],
        'id' => $uye_id
    ]);

    if ($existing_user) {
        $response = [
            'status' => 'error',
            'message' => 'Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.'
        ];
        echo json_encode($response);
        exit;
    }

    // Prepare update data
    $update_data = [
        'ad' => $_POST['ad'],
        'soyad' => $_POST['soyad'],
        'email' => $_POST['email'],
        'tel' => $_POST['tel'],
        'firmaUnvani' => $_POST['firmaUnvani'],
        'vergi_dairesi' => $_POST['vergi_dairesi'] ?? '',
        'vergi_no' => $_POST['vergi_no'] ?? '',
        'ulke' => $_POST['ulke'] ?? 'Türkiye',
        'il' => $_POST['il'] ?? '',
        'ilce' => $_POST['ilce'] ?? '',
        'adres' => $_POST['adres'] ?? '',
        'posta_kodu' => $_POST['posta_kodu'] ?? '',
        'id' => $uye_id
    ];

    // Update password if provided
    if (!empty($_POST['yeni_sifre'])) {
        if (strlen($_POST['yeni_sifre']) < 6) {
            $response = [
                'status' => 'error',
                'message' => 'Şifre en az 6 karakter olmalıdır.'
            ];
            echo json_encode($response);
            exit;
        }
        $update_data['sifre'] = md5($_POST['yeni_sifre'], PASSWORD_DEFAULT);
    }

    // Build the update query
    $fields = [];
    foreach ($update_data as $key => $value) {
        if ($key !== 'id') {
            $fields[] = "$key = :$key";
        }
    }
    $update_query = "UPDATE uyeler SET " . implode(', ', $fields) . " WHERE id = :id";

    // Execute the update
    $success = $db->update($update_query, $update_data);

    if ($success) {
        // Log the update
        $db->insert("INSERT INTO uye_log (uye_id, islem, tarih) VALUES (:uye_id, :islem, NOW())", [
            'uye_id' => $uye_id,
            'islem' => 'Profil güncellendi'
        ]);

        // Update address if exists
        if (!empty($_POST['adres'])) {
            $existing_address = $db->fetch("SELECT id FROM adresler WHERE uye_id = :uye_id AND aktif = 1", [
                'uye_id' => $uye_id
            ]);

            if ($existing_address) {
                // Update existing address
                $db->update("UPDATE adresler SET 
                    adres = :adres,
                    il = :il,
                    ilce = :ilce,
                    ad = :ad,
                    soyad = :soyad,
                    ulke = :ulke,
                    vergi_no = :vergi_no,
                    vergi_dairesi = :vergi_dairesi,
                    posta_kodu = :posta_kodu
                    WHERE id = :id", [
                    'adres' => $_POST['adres'],
                    'il' => $_POST['il'],
                    'ilce' => $_POST['ilce'],
                    'ad' => $_POST['ad'],
                    'soyad' => $_POST['soyad'],
                    'ulke' => $_POST['ulke'],
                    'vergi_no' => $_POST['vergi_no'],
                    'vergi_dairesi' => $_POST['vergi_dairesi'],
                    'posta_kodu' => $_POST['posta_kodu'],
                    'id' => $existing_address['id']
                ]);
            } else {
                // Insert new address
                $db->insert("INSERT INTO adresler (
                    adres, il, ilce, uye_id, adres_turu, adres_basligi,
                    ad, soyad, ulke, vergi_no, vergi_dairesi, posta_kodu, aktif
                ) VALUES (
                    :adres, :il, :ilce, :uye_id, 'teslimat', 'Teslimat Adresim',
                    :ad, :soyad, :ulke, :vergi_no, :vergi_dairesi, :posta_kodu, 1
                )", [
                    'adres' => $_POST['adres'],
                    'il' => $_POST['il'],
                    'ilce' => $_POST['ilce'],
                    'uye_id' => $uye_id,
                    'ad' => $_POST['ad'],
                    'soyad' => $_POST['soyad'],
                    'ulke' => $_POST['ulke'],
                    'vergi_no' => $_POST['vergi_no'],
                    'vergi_dairesi' => $_POST['vergi_dairesi'],
                    'posta_kodu' => $_POST['posta_kodu']
                ]);
            }
        }

        $response = [
            'status' => 'success',
            'message' => 'Profil bilgileriniz başarıyla güncellendi.'
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Güncelleme sırasında bir hata oluştu.'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


if (isset($_POST['sifre_guncelle'])) {
    include("lang.php");
    $eski_parola = md5(controlInput($_POST['eski_parola']));
    $yeni_parola = controlInput($_POST['yeni_parola']);
    $yeni_parola_tekrar = controlInput($_POST['yeni_parola_tekrar']);
    $user_id = controlInput($_POST['user_id']);
    $user_language = controlInput($_POST['lang']);

    if ($yeni_parola != $yeni_parola_tekrar) {
        echo translate("girilen_sifre_eslesmiyor", $lang, $user_language);
        exit();
    }

    $user = $db->fetch("SELECT parola FROM uyeler WHERE id = :id", [
        'id' => $user_id
    ]);

    if ($user) {
        $stored_md5_password = $user['parola'];

        if ($eski_parola == $stored_md5_password) {
            $yeni_parola = md5($yeni_parola);
            $currentDateTime = date("Y-m-d H:i:s");
            $new_date = date("Y-m-d H:i:s", strtotime($currentDateTime . " +3 hours"));
            
            $success = $db->update("UPDATE uyeler SET parola = :parola, DEGISTIRME_TARIHI = :tarih WHERE id = :id", [
                'parola' => $yeni_parola,
                'tarih' => $new_date,
                'id' => $user_id
            ]);
            
            echo translate("sifre_guncellendi", $lang, $user_language);
        } else {
            echo translate("eski_sifre_hatali", $lang, $user_language);
        }
    } else {
        echo "User not found.";
    }
}

if (isset($_POST['sifre_kaydet'])) {
    $yeni_parola = controlInput($_POST['yeni_parola']);
    $code = controlInput($_POST['code']);

    $reset_request = $db->fetch("SELECT * FROM sifre_degistirme WHERE kod = :kod", [
        'kod' => $code
    ]);

    if ($reset_request) {
        $uye_id = $reset_request['uye_id'];
        $hashed_new_password = md5($yeni_parola);

        $currentDateTime = date("Y-m-d H:i:s");
        $new_date = date("Y-m-d H:i:s", strtotime($currentDateTime . " +3 hours"));

        $success = $db->update("UPDATE uyeler SET parola = :parola, DEGISTIRME_TARIHI = :tarih WHERE id = :id", [
            'parola' => $hashed_new_password,
            'tarih' => $new_date,
            'id' => $uye_id
        ]);

        $db->delete("DELETE FROM sifre_degistirme WHERE kod = :kod", [
            'kod' => $code
        ]);

        echo "Şifre Güncellendi";
    } else {
        echo "Tekrar şifre yenileme talebinde bulununuz.";
    }
}

if (isset($_POST['sifre_unuttum'])) {
    $mail = filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL);

    $user = $db->fetch("SELECT * FROM uyeler WHERE email = :email", [
        'email' => $mail
    ]);

    if ($user) {
        if ($user['email'] == $mail) {
            echo 'success';
            include 'mail/mail_gonder.php';
            $uye_id = $user['id'];
            $ad = $user['ad'];
            $soyad = $user['soyad'];
            $adsoyad = $ad . ' ' . $soyad;

            $uniqKod = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 20);

            $db->insert("INSERT INTO sifre_degistirme (uye_id, kod) VALUES (:uye_id, :kod)", [
                'uye_id' => $uye_id,
                'kod' => $uniqKod
            ]);

            $mail_icerik = sifreDegistimeMail($adsoyad, $uniqKod);
            mailGonder($mail, 'Şifre Sıfırlama!', $mail_icerik, 'Şifre Sıfırlama!');
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
}
?>