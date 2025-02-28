<?php
require_once "../db.php";
$db = new Database();
$ilceID = $_POST["ilce_id"];
$mahalle = $_POST["mahalle"];

// Fetch all mahalleler for the given ilce_id
$result = $db->fetchAll("SELECT * FROM mahalleler WHERE ilce_id = :ilce_id", [
    'ilce_id' => $ilceID
]);

if ($mahalle == '') {
    ?><option value="">Seçiniz..</option><?php
} else {
    // Fetch the selected mahalle_adi
    $mahResult = $db->fetch("SELECT mahalle_adi FROM mahalleler WHERE mahalle_id = :mahalle_id", [
        'mahalle_id' => $mahalle
    ]);
    $mahalle_adi = $mahResult['mahalle_adi'];
    ?><option value="<?= $mahalle ?>"><?= $mahalle_adi ?></option><?php
}

// Loop through the result set and output options
foreach ($result as $row) {
    ?><option value="<?= $row["mahalle_id"] ?>"><?= $row["mahalle_adi"] ?></option><?php
}

?>