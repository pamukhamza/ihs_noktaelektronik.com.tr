<?php
// ajax/sepet_count.php
session_name("user_session");
session_start();
require_once '../db.php';
$db = new Database();

$response = array();

if (isset($_SESSION['id'])) {
    $uye_id = $_SESSION['id'];
    
    $rowCount = $db->fetchColumn("SELECT COUNT(*) AS row_count FROM uye_sepet WHERE uye_id = :uye_id", [
        'uye_id' => $uye_id
    ]);
    
    $response['sepetCount'] = $rowCount;
    $response['success'] = true;
} else {
    $response['sepetCount'] = 0;
    $response['success'] = true;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
