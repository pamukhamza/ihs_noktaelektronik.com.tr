<?php
require_once '../db.php';
$db = new Database();

if (isset($_POST['urun_id']) && isset($_FILES['image'])) {
    $urun_id = $_POST['urun_id'];
    $uploadDir = '../../assets/images/urunler/';
    $response = array();

    // Get the file extension
    $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

    if (!in_array($fileExtension, $allowedExtensions)) {
        $response['status'] = 'error';
        $response['message'] = 'Geçersiz dosya formatı. Sadece JPG, JPEG, PNG ve GIF dosyaları yüklenebilir.';
        echo json_encode($response);
        exit;
    }

    // Generate unique filename
    $fileName = uniqid() . '.' . $fileExtension;
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        // Insert image record into database
        $success = $db->insert("INSERT INTO nokta_urunler_resimler (urun_id, foto, sira) VALUES (:urun_id, :foto, :sira)", [
            'urun_id' => $urun_id,
            'foto' => $fileName,
            'sira' => 1
        ]);

        if ($success) {
            $response['status'] = 'success';
            $response['message'] = 'Resim başarıyla yüklendi.';
            $response['image_path'] = 'assets/images/urunler/' . $fileName;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Veritabanına kayıt sırasında bir hata oluştu.';
            // Delete uploaded file if database insert fails
            unlink($uploadPath);
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Dosya yükleme sırasında bir hata oluştu.';
    }

    echo json_encode($response);
}
?>