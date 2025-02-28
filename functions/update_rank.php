<?php
include("../function.php");

$type = $_POST['type'];

if(isset($_POST['newOrder']) && $type == "slider_tr") {
   $newOrder = $_POST['newOrder'];
   // Loop through the new order and update the database
   foreach($newOrder as $index => $itemId) {
      $sql = "UPDATE noktaslider SET sira = ? WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$index + 1, $itemId]);
   }
    exit();
}elseif(isset($_POST['newOrder']) && $type == "slider_en") {
    $newOrder = $_POST['newOrder'];
 
    // Loop through the new order and update the database
    foreach($newOrder as $index => $itemId) {
       $sql = "UPDATE noktaslider SET sira = ? WHERE id = ?";
       $stmt = $db->prepare($sql);
       $stmt->execute([$index + 1, $itemId]);
    }
    exit();
 }elseif(isset($_POST['newOrder']) && $type == "proje_tr") {
    $newOrder = $_POST['newOrder'];
 
    // Loop through the new order and update the database
    foreach($newOrder as $index => $itemId) {
       $sql = "UPDATE nokta_proje SET sira = ? WHERE id = ?";
       $stmt = $db->prepare($sql);
       $stmt->execute([$index + 1, $itemId]);
    }
    exit();
 }elseif(isset($_POST['newOrder']) && $type == "proje_en") {
    $newOrder = $_POST['newOrder'];
 
    // Loop through the new order and update the database
    foreach($newOrder as $index => $itemId) {
       $sql = "UPDATE nokta_proje SET sira = ? WHERE id = ?";
       $stmt = $db->prepare($sql);
       $stmt->execute([$index + 1, $itemId]);
    }
    exit();
 }elseif(isset($_POST['newOrder']) && $type == "katalog") {
    $newOrder = $_POST['newOrder'];
 
    // Loop through the new order and update the database
    foreach($newOrder as $index => $itemId) {
       $sql = "UPDATE nokta_kataloglar SET sira = ? WHERE id = ?";
       $stmt = $db->prepare($sql);
       $stmt->execute([$index + 1, $itemId]);
    }
    exit();
 }elseif(isset($_POST['newOrder']) && $type == "marka") {
   $newOrder = $_POST['newOrder'];

   // Loop through the new order and update the database
   foreach($newOrder as $index => $itemId) {
      $sql = "UPDATE nokta_urun_markalar_1 SET sira = ? WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$index + 1, $itemId]);
   }
   exit();
}elseif(isset($_POST['newOrder']) && $type == "yukleme_baslik") {
   $newOrder = $_POST['newOrder'];

   // Loop through the new order and update the database
   foreach($newOrder as $index => $itemId) {
      $sql = "UPDATE nokta_yuklemeler SET sira = ? WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$index + 1, $itemId]);
   }
   exit();
}elseif(isset($_POST['newOrder']) && $type == "urun_resim") {
   $newOrder = $_POST['newOrder'];

   // Loop through the new order and update the database
   foreach($newOrder as $index => $itemId) {
      $sql = "UPDATE nokta_urunler_resimler SET sira = ? WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$index + 1, $itemId]);
   }
   exit();
}elseif(isset($_POST['newOrder']) && $type == "kategorisirala") {
   $newOrder = $_POST['newOrder'];

   // Loop through the new order and update the database
   foreach($newOrder as $index => $itemId) {
      $sql = "UPDATE nokta_kategoriler SET sira = ? WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$index + 1, $itemId]);
   }
   exit();
}elseif(isset($_POST['newOrder']) && $type == "urunKategoriSirala") {
   $newOrder = $_POST['newOrder'];

   // Loop through the new order and update the database
   foreach($newOrder as $index => $itemId) {
      $sql = "UPDATE nokta_urunler SET sira = ? WHERE id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$index + 1, $itemId]);
   }
   exit();
}


?>
