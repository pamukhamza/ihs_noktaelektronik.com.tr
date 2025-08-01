<?php 
error_reporting(0);
ini_set('display_errors', 0);
$database = new Database();
require_once __DIR__ . '/../../functions/uyeler/uye_giris.php';
// Start the "Remember Me" functionality
handleRememberMe($database);
// If session is not started, start the session
if (session_status() == PHP_SESSION_NONE) {
    session_name("user_session");
    session_start();
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="https://noktaelektronik.com.tr/">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="bootstrap/bootstrap.min.css"> 
    <link rel="stylesheet" href="assets/css/ozel.css">
    <link rel="stylesheet" href="bootstrap/sidebars.css">
    <link rel="stylesheet" href="assets/splide/splide.min.css">
    <link rel="stylesheet" href="assets1/vendor/fonts/boxicons.css">
    <link rel="stylesheet" href="assets1/vendor/fonts/fontawesome.css">
    <link rel="stylesheet" href="assets1/vendor/fonts/flag-icons.css">
    <link rel="stylesheet" href="assets1/vendor/libs/bs-stepper/bs-stepper.css">
    <link rel="stylesheet" href="assets/css/sepet.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Scripts -->
    <script src="assets1/vendor/js/helpers.js"></script>
    <script src="assets1/vendor/js/template-customizer.js"></script>
    <script src="assets1/js/config.js"></script>
</head>