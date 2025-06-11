<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/logger.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['takip_kodu'])) {
        throw new Exception('Takip kodu gerekli');
    }

    $takip_kodu = controlInput($_POST['takip_kodu']);
    
    // Get repair details
    $repair = $db->fetch("SELECT * FROM nokta_teknik_destek WHERE takip_kodu = :takip_kodu", ['takip_kodu' => $takip_kodu]);
    
    if (!$repair) {
        throw new Exception('Onarım kaydı bulunamadı');
    }

    // Get products for this repair
    $products = $db->fetchAll("
        SELECT t.*, d.durum 
        FROM teknik_destek_urunler t 
        LEFT JOIN nokta_teknik_durum d ON t.urun_durumu = d.id 
        WHERE t.tdp_id = :tdp_id AND t.SILINDI = 0
    ", ['tdp_id' => $repair['id']]);

    // Log successful retrieval
    Logger::info("Onarım detayları getirildi", [
        'takip_kodu' => $takip_kodu,
        'product_count' => count($products)
    ]);

    echo json_encode([
        'success' => true,
        'repair' => $repair,
        'products' => $products
    ]);

} catch (Exception $e) {
    Logger::error("Onarım detayları alınırken hata oluştu", [
        'error' => $e->getMessage(),
        'takip_kodu' => $takip_kodu ?? null
    ]);

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
} 