<?php
session_name("user_session");
session_start();
require_once '../db.php';
$db = new Database();

if (isset($_SESSION['id'])) {
    $uye_id = $_SESSION['id'];
    
    // Get user information
    $uye = $db->fetch("SELECT * FROM uyeler WHERE id = :id", [
        'id' => $uye_id
    ]);
    
    if ($uye) {
        $response = [
            'success' => true,
            'user' => [
                'id' => $uye['id'],
                'ad' => $uye['ad'],
                'soyad' => $uye['soyad'],
                'email' => $uye['email']
            ]
        ];
    } else {
        $response = ['success' => false, 'message' => 'User not found'];
    }
} else {
    $response = ['success' => false, 'message' => 'No active session'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
