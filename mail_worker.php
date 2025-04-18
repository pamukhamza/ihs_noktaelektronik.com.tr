<?php
require_once 'functions/functions.php';

// Komut satırından gelen parametreleri al
$to = $argv[1];
$subject = $argv[2];
$message = $argv[3];
$from = $argv[4];

// Mail gönder
mailGonder($to, $subject, $message, $from);
?> 