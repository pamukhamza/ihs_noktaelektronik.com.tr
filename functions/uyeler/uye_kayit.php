<?php
include '../functions.php';
include '../../mail/mail_gonder.php';
ini_set('display_errors', 1);  // Hataları ekrana göster
error_reporting(E_ALL);   
$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $response = array();
    
    // Validate required fields
    $required_fields = ['ad', 'soyad', 'eposta', 'parola', 'tel', 'firma_ad', 'vergi_dairesi'];
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
    if (empty($_POST['vergi_no']) && empty($_POST['tc_no'])) {
        $response = [
            'status' => 'error',
            'message' => 'Vergi numarası veya TC kimlik numarasından en az biri doldurulmalıdır.'
        ];
        echo json_encode($response);
        exit;
    }

    // Check if email already exists
    $existing_user = $db->fetch("SELECT id FROM uyeler WHERE email = :email", ['email' => $_POST['eposta']]);

    if ($existing_user) {
        $response = [
            'status' => 'error',
            'message' => 'Bu e-posta adresi zaten kayıtlı.'
        ];
        echo json_encode($response);
        exit;
    }

    $aktivasyon_kodu = bin2hex(random_bytes(16));

    // Hash password
    $hashed_password = password_hash($_POST['parola'], PASSWORD_DEFAULT);
echo "buraya geldi haa";
    // Insert new user
    $success = $db->insert("INSERT INTO uyeler (ad, soyad, email, sifre, tel, firmaUnvani, vergi_dairesi, vergi_no, ulke, il, ilce, adres, posta_kodu, aktivasyon_kodu, aktif, 
            kayit_tarihi, son_giris, fiyat) VALUES (
            :ad, :soyad, :email, :sifre, :tel, :firmaUnvani,:vergi_dairesi, :vergi_no, :ulke, :il, :ilce,:adres, :posta_kodu, :aktivasyon_kodu, 0,NOW(), NOW(), 4)", 
        ['ad' => $_POST['ad'],
        'soyad' => $_POST['soyad'],
        'email' => $_POST['eposta'],
        'sifre' => $hashed_password,
        'tel' => $_POST['tel'],
        'firmaUnvani' => $_POST['firma_ad'],
        'vergi_dairesi' => $_POST['vergi_dairesi'],
        'vergi_no' => $_POST['vergi_no'] ?? null,
        'tc_no' => $_POST['tc_no'] ?? null,
        'ulke' => $_POST['ulke'] ?? 'Türkiye',
        'il' => $_POST['il'] ?? '',
        'ilce' => $_POST['ilce'] ?? '',
        'adres' => $_POST['adres'] ?? '',
        'posta_kodu' => $_POST['posta_kodu'] ?? '',
        'aktivasyon_kodu' => $aktivasyon_kodu
    ]);
    echo "buraya geldi da";
    if ($success) {
        // Get the new user's ID
        $new_user_id = $db->lastInsertId();
        $adsoyad = $_POST['ad'] . " " . $_POST['soyad'];

        $mail_icerik = uyeOnayMail($adsoyad, $_POST['eposta'], $aktivasyon_kodu);
        mailGonder($_POST['eposta'], 'Nokta Elektronik B2B Üyelik Aktivasyonu', $mail_icerik, 'Nokta Elektronik B2B Üyelik Aktivasyonu');
        $response = [
            'status' => 'success',
            'message' => 'Kayıt başarılı. Lütfen e-posta adresinize gönderilen aktivasyon linkini kullanın.'
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Kayıt sırasında bir hata oluştu.'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>