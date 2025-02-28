<?php
require_once '../db.php';
$db = new Database();

if(isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];

    // Get brands for the selected category
    $brands = $db->fetchAll("SELECT * FROM nokta_kategoriler_markalar_rel WHERE kat_id = :category_id", [
        'category_id' => $category_id
    ]);

    // Return brands as JSON
    echo json_encode($brands);
}
?>