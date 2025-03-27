<?php
ini_set('display_errors', 1);  // Hataları ekrana göster
error_reporting(E_ALL);   
include '../functions.php';
include '../../mail/mail_gonder.php';
require '../../vendor/autoload.php';
require '../wolvox/uye_kayit.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
$config = require '../../aws-config.php';

if (!isset($config['s3']['region']) || !isset($config['s3']['key']) || !isset($config['s3']['secret']) || !isset($config['s3']['bucket'])) {
    die('Missing required S3 configuration values.');
}

$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => $config['s3']['region'],
    'credentials' => [
        'key'    => $config['s3']['key'],
        'secret' => $config['s3']['secret'],
    ]
]);

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
    if (!empty($_FILES['vergi_levhasi']['name'])) {
        $file = uploadDenemeFileToS3($_FILES['vergi_levhasi'], 'uploads/vergi_levhalari/', $s3Client, $config['s3']['bucket']);
        if ($file === false) {
            echo "File upload failed.";
            exit;
        }
        $vergi_levhasi_url = $file; // S3'ten dönen URL
    } else {
        $vergi_levhasi_url = null;
    }
    $uyetipi = 'Bayi';
    $cari_kodu = "WEB" . uniqid(6);
    $satis_temsilcisi = 86732;

    $hashed_password = password_hash($_POST['parola'], PASSWORD_DEFAULT);
    // Kullanıcı ekleme işlemi sırasında S3'teki dosya yolunu da kaydet
    $success = $db->insert("INSERT INTO uyeler (ad, soyad, email, parola, tel, sabit_tel, firmaUnvani, vergi_dairesi, vergi_no, tc_no, ulke, il, ilce, adres, posta_kodu, aktivasyon, aktif, 
    kayit_tarihi, son_giris, fiyat, vergi_levhasi, uye_tipi, muhasebe_kodu) VALUES (
    :ad, :soyad, :email, :parola, :tel, :sabit_tel, :firmaUnvani, :vergi_dairesi, :vergi_no, :tc_no,:ulke, :il, :ilce, :adres, :posta_kodu, :aktivasyon_kodu, :aktif, NOW(), NOW(), 4, :vergi_levhasi, :uye_tipi, :muhasebe_kodu)", 
    [
        'ad' => $_POST['ad'],
        'soyad' => $_POST['soyad'],
        'email' => $_POST['eposta'],
        'parola' => $hashed_password,
        'tel' => $_POST['tel'],
        'sabit_tel' => $_POST['sabit_tel'],
        'firmaUnvani' => $_POST['firma_ad'],
        'vergi_dairesi' => $_POST['vergi_dairesi'],
        'vergi_no' => $_POST['vergi_no'] ?? null,
        'tc_no' => $_POST['tc_no'] ?? null,
        'ulke' => $_POST['ulke'] ?? 'Türkiye',
        'il' => $_POST['il'] ?? '',
        'ilce' => $_POST['ilce'] ?? '',
        'adres' => $_POST['adres'] ?? '',
        'posta_kodu' => $_POST['posta_kodu'] ?? '',
        'aktivasyon_kodu' => '0',
        'aktif' => '0',
        'vergi_levhasi' => $vergi_levhasi_url,
        'uye_tipi' => $uyetipi,
        'muhasebe_kodu' => $cari_kodu
    ]);
    
    if ($success) {
        // Get the new user's ID
        $new_user_id = $db->lastInsertId();
        $adsoyad = $_POST['ad'] . " " . $_POST['soyad'];

        $mail_icerik = uyeOnayMail($adsoyad, $_POST['eposta'], $new_user_id);
        mailGonder($_POST['eposta'], 'Nokta Elektronik B2B Üyelik Aktivasyonu', $mail_icerik, 'Nokta Elektronik B2B Üyelik Aktivasyonu');
        $response = [
            'status' => 'success',
            'message' => 'Kayıt başarılı. Lütfen e-posta adresinize gönderilen aktivasyon linkini kullanın.'
        ];

        $iller = $db->fetch("SELECT * FROM iller WHERE il_id = :id", ['id' => $_POST['il']]);
        $il_adi = $iller['il_adi'];

        $ilceler = $db->fetch("SELECT * FROM ilceler WHERE ilce_id = :ilce_id AND il_id = :id", ['ilce_id' => $_POST['ilce'], 'id' => $_POST['il']]);
        $ilce_adi = $ilceler['ilce_adi'];
        $param = [
            'ad' => $_POST['ad'],
            'soyad' => $_POST['soyad'],
            'email' => $_POST['eposta'],
            'parola' => $hashed_password,
            'tel' => $_POST['tel'],
            'sabit_tel' => $_POST['sabit_tel'],
            'firma_unvani' => $_POST['firma_ad'],
            'vergi_dairesi' => $_POST['vergi_dairesi'],
            'vergi_no' => $_POST['vergi_no'] ?? null,
            'tc_no' => $_POST['tc_no'] ?? null,
            'ulke' => $_POST['ulke'] ?? 'Türkiye',
            'il_adi' => $_POST['il_adi'] ?? '',
            'ilce_adi' => $_POST['ilce_adi'] ?? '',
            'adres' => $_POST['adres'] ?? '',
            'posta_kodu' => $_POST['posta_kodu'] ?? '',
            'aktivasyon_kodu' => '0',
            'aktif' => '0',
            'cari_kodu' => $cari_kodu,
            'uye_tipi' => $uyetipi,
            'degistirme_tarihi' => date('Y-m-d H:i:s'),
            'PAZ_BLCRKODU' => $satis_temsilcisi
        ];
        uyeXmlOlustur($param);

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