<?php
require_once '../db.php';
$db = new Database();

// Fetch data from the banka_pos_listesi table
$data = $db->fetchAll("SELECT id, BLBNHSKODU, BANKA_ADI, TANIMI, TAKSIT_SAYISI FROM banka_pos_listesi");

// Encode the array as JSON and output it
echo json_encode($data);

// Close the database connection
$db = null;

?>
