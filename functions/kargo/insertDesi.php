<?php
require_once "../db.php";
$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["kargoID"])) {
    $kargoID = $_POST["kargoID"];

    // Insert new row and get the ID
    $success = $db->insert("INSERT INTO kargo_desi (kargo_id, desi_alt, desi_ust, fiyat) VALUES (:kargoID, '0', '0', '0')", [
        'kargoID' => $kargoID
    ]);

    if ($success) {
        // Get the last inserted ID
        $lastInsertId = $db->lastInsertId();

        // Get the inserted row data
        $desiData = $db->fetch("SELECT * FROM kargo_desi WHERE id = :id", [
            'id' => $lastInsertId
        ]);

        // Create response array
        $newRowData = array(
            'id' => $lastInsertId,
            'desi_alt' => $desiData['desi_alt'],
            'desi_ust' => $desiData['desi_ust'],
            'fiyat' => $desiData['fiyat']
        );

        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode($newRowData);
        exit;
    }
}

echo 'Error';
exit;
?>
