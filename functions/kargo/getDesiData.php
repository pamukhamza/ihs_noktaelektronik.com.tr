<?php
require_once "../db.php";
$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["kargoID"])) {
    $kargoID = $_POST["kargoID"];

    // Get all desi data
    $desiData = $db->fetchAll("SELECT * FROM kargo_desi WHERE kargo_id = :kargoID", [
        'kargoID' => $kargoID
    ]);

    // Create table
    echo '<table class="table table-bordered" id="desi-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Desi Alt</th>';
    echo '<th>Desi Üst</th>';
    echo '<th>Fiyat</th>';
    echo '<th> <button type="button" class="btn btn-sm btn-outline-light" onclick="yeniDesiEkle('. $kargoID. ')"><i class="fa-solid fa-circle-plus fa-lg"></i></button></th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($desiData as $row) {
        echo '<tr>';
        echo "<td><input type='text' name='desi_alt' data-id='{$row['id']}' value='{$row['desi_alt']}' /></td>";
        echo "<td><input type='text' name='desi_ust' data-id='{$row['id']}' value='{$row['desi_ust']}' /></td>";
        echo "<td><input type='text' name='fiyat' data-id='{$row['id']}' value='{$row['fiyat']}' /></td>";
        echo "<td>
        <button type='button' class='btn btn-sm btn-outline-light' onclick='kargoDesiKaydet({$row['id']});'><i class='far fa-save'></i></button>
        <button type='button' class='btn btn-sm btn-outline-light' onclick='kargoDesiSil({$row['id']});'><i class='far fa-trash-alt'></i></button></td>";
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}
?>


