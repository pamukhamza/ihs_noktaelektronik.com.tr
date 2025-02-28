<?php
// Path to the session directory
$sessionDir = '/var/cpanel/php/sessions/ea-php82';

// Define the maximum age of session files to keep (in seconds)
$maxAge = 432000; // Example: keep files younger than 1 hour

// Delete old session files
$currentTime = time();
$files = glob("$sessionDir/sess_*");
foreach ($files as $file) {
    if (is_file($file) && ($currentTime - filemtime($file)) > $maxAge) {
        unlink($file);
    }
}
?>
