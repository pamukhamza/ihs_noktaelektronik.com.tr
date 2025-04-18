<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendMail($to, $subject, $content) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'destek@noktaelektronik.com.tr';
        $mail->Password = 'your_password_here';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom('destek@noktaelektronik.com.tr', 'Nokta Elektronik');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $content;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail gönderilemedi: " . $mail->ErrorInfo);
        return false;
    }
}

// Mail gönderme işlemleri
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'cari_odeme':
            $uye = $_POST['uye'];
            $fiyat = $_POST['fiyat'];
            $taksit = $_POST['taksit'];
            $content = cariOdemeMail($uye, $fiyat, $taksit);
            $subject = 'Cari Ödeme Bildirimi';
            break;

        case 'uye_onay':
            $uye = $_POST['uye'];
            $uye_mail = $_POST['uye_mail'];
            $aktivasyon = $_POST['aktivasyon'];
            $content = uyeOnayMail($uye, $uye_mail, $aktivasyon);
            $subject = 'Üyelik Onayı';
            break;

        case 'sifre_degistir':
            $uye = $_POST['uye'];
            $kod = $_POST['kod'];
            $content = sifreDegistimeMail($uye, $kod);
            $subject = 'Şifre Sıfırlama';
            break;

        case 'iletisim_form':
            $adSoyad = $_POST['adSoyad'];
            $email = $_POST['email'];
            $tarih = $_POST['tarih'];
            $mesaj = $_POST['mesaj'];
            $content = iletisimFormMail($adSoyad, $email, $tarih, $mesaj);
            $subject = 'İletişim Formu';
            break;

        case 'teklif_alindi':
            $uye = $_POST['uye'];
            $content = teklifAlindiMail($uye);
            $subject = 'Teklif Alındı';
            break;

        case 'ariza_kayit':
            $uye = $_POST['uye'];
            $takip = $_POST['takip'];
            $content = arizaKayitMail($uye, $takip);
            $subject = 'Arıza Kaydı';
            break;
    }

    if (isset($content) && isset($subject)) {
        $to = $_POST['to'] ?? 'destek@noktaelektronik.com.tr';
        $result = sendMail($to, $subject, $content);
        echo json_encode(['success' => $result]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Geçersiz işlem']);
    }
}