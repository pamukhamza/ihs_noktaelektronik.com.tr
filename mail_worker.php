<?php
// Hata ayıklama için log dosyasına yaz
error_log("Mail worker başlatıldı");

// Dosya yollarını doğru şekilde ayarla
$root_path = dirname(__FILE__);
require_once $root_path . '/functions/db.php';
require_once $root_path . '/functions/functions.php';
require_once $root_path . '/mail/mail_gonder.php';

// Komut satırından gelen parametreleri al
$to = $argv[1];
$subject = $argv[2];
$message = $argv[3];
$from = $argv[4];

error_log("Mail gönderimi başlatılıyor - Alıcı: " . $to);

try {
    // Mail gönder
    mailGonder($to, $subject, $message, $from);
    error_log("Mail başarıyla gönderildi - Alıcı: " . $to);
} catch (Exception $e) {
    error_log("Mail gönderimi başarısız - Hata: " . $e->getMessage());
}
?> 