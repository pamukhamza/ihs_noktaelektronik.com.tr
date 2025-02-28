<?php
include "../db.php";

// Create a new Database instance
$database = new Database();
$kartId = $_POST['kartId'];

// Prepare the query and fetch results using PDO
$query = "SELECT id, pos_id, taksit, vade, ticari_program FROM b2b_banka_taksit_eslesme WHERE kart_id = :kartId AND aktif = 1 ORDER BY taksit ASC";
$params = ['kartId' => $kartId];

// Fetch all results
$rows = $database->fetchAll($query, $params);

// Return the results as JSON
echo json_encode($rows);
?>
