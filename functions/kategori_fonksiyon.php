<?php
require_once __DIR__ . '/../baglanti.php';

// Function to retrieve all categories from the database
function getCategories() {
    global $db;

    $query = "SELECT * FROM nokta_kategoriler ORDER BY sira";
    $statement = $db->query($query);
    $categories = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $categories;
}

function getSubcategories($parentId) {
    global $db;
    $parentId = (int)$parentId;

    $query = "SELECT * FROM nokta_kategoriler WHERE parent_id = :parentId ORDER BY sira";
    $statement = $db->prepare($query);
    $statement->bindParam(':parentId', $parentId, PDO::PARAM_INT);
    $statement->execute();
    $subcategories = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $subcategories;
}
?>
