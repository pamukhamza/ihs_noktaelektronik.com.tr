// Mail gönderimi
$mail_command = "php " . escapeshellarg($root_path . "/mail/mail_worker.php") . " " . 
    escapeshellarg($mail) . " " . 
    escapeshellarg($konu) . " " . 
    escapeshellarg($mesaj) . " " . 
    escapeshellarg($mailbaslik) . " > NUL 2>&1 &";

error_log("Mail gönderim komutu: " . $mail_command);
$result = exec($mail_command, $output, $return_var);

if ($return_var !== 0) {
    error_log("Mail gönderimi başlatılamadı. Hata kodu: " . $return_var);
    error_log("Çıktı: " . implode("\n", $output));
} 