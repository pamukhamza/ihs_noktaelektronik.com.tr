<?php
require_once "../db.php";
$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];
    $desiUst = $_POST["desi_ust"];
    $desiAlt = $_POST["desi_alt"];
    $fiyat = $_POST["fiyat"];

    // Update the data in the database
    $success = $db->update("UPDATE kargo_desi SET desi_ust = :desiUst, desi_alt = :desiAlt, fiyat = :fiyat WHERE id = :id", [
        'desiUst' => $desiUst,
        'desiAlt' => $desiAlt,
        'fiyat' => $fiyat,
        'id' => $id
    ]);

    echo $success ? 'Success' : 'Error';
} else {
    echo 'Error';
}
?>
