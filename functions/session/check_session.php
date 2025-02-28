<?php
// Function to ensure the user is redirected only once
function redirectOnce($redirectPage) {
    // Check if the user is already redirected, based on a flag
    if (!isset($_SESSION['redirected_once'])) {
        // Set the flag to prevent multiple redirects
        $_SESSION['redirected_once'] = true;
        
        // Redirect the user
        header('Location: ' . $redirectPage);
        exit;
    }
}
// Check if there is an active session
if (!isset($_SESSION["ad"])) {
    // If no session, redirect to login page, only once
    redirectOnce("http://192.168.16.63/b2b/tr/giris");
}
?>
