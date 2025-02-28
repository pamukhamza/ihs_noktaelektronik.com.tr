<?php
require_once "../db.php";
$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];

    // Delete desi record
    $success = $db->delete("DELETE FROM kargo_desi WHERE id = :id", [
        'id' => $id
    ]);

    echo $success ? 'Success' : 'Error';
} else {
    echo 'Error';
}
?>
