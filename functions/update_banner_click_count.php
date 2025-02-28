<?php
include '../baglanti.php'; // Veritabanı bağlantı dosyanızı dahil edin

if (isset($_POST['banner_id'])) {
    global $connection;
    $banner_id = $_POST['banner_id'];

    if ($banner_id > 0) {
        // Banner ID'ye göre tıklama sayısını güncelle
        $sql = "UPDATE nokta_banner SET tiklanma = tiklanma + 1 WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $banner_id);

        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid banner ID";
    }
}elseif (isset($_POST['banner_video_id'])){
    global $connection;
    $banner_id = $_POST['banner_video_id'];

    if ($banner_id > 0) {
        // Banner ID'ye göre tıklama sayısını güncelle
        $sql = "UPDATE nokta_banner_video SET tiklanma = tiklanma + 1 WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $banner_id);

        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid banner ID";
    }
}

$connection->close();
?>
