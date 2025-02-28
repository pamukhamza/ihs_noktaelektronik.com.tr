<?php
require_once "../db.php";
$db = new Database();
$ilID = $_POST["il_id"];
$uye_ilce = $_POST["ilce"];
$lang = $_POST["lang"];

// Fetch all ilceler for the given il_id
$result = $db->fetchAll("SELECT * FROM ilceler WHERE il_id = :il_id", [
    'il_id' => $ilID
]);

if (empty($uye_ilce)) {
    ?><option value="">İlçe *</option><?php
} else {
    // Fetch the selected ilce_adi
    $ilceResult = $db->fetch("SELECT ilce_adi FROM ilceler WHERE ilce_id = :ilce_id", [
        'ilce_id' => $uye_ilce
    ]);
    $uye_ilce_adi = $ilceResult['ilce_adi'];
    ?><option value="<?= $uye_ilce ?>"><?= $uye_ilce_adi ?></option><?php
}

// Loop through the result set and output options
foreach ($result as $row) {
    ?><option value="<?= $row["ilce_id"] ?>"><?= $row["ilce_adi"] ?></option><?php
}

?>