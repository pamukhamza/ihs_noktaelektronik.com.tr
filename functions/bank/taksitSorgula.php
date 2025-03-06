<?php
include "../db.php";
$database = new Database();
$kartId = $_POST['kartId'];

$query = "SELECT id, pos_id, taksit, vade, ticari_program FROM b2b_banka_taksit_eslesme WHERE kart_id = :kartId AND aktif = 1 ORDER BY taksit ASC";
$params = ['kartId' => $kartId];

$rows = $database->fetchAll($query, $params);

echo json_encode($rows);
?>