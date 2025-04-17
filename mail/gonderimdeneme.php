<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function mailGonder($alici, $konu, $mesaj_icerik, $mailbaslik){
    include 'Exception.php';
    include 'PHPMailer.php';
    include 'SMTP.php';
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 2; // 0 = off, 2 = full debug output
        $mail->isSMTP();
        $mail->Host = 'mail.noktaelektronik.net';
        $mail->SMTPAuth = true;
        $mail->Username = 'nokta\b2b';
        $mail->Password = 'Nktbb2023*';
        $mail->SMTPSecure = 'tls'; // veya 'tls'
        $mail->Port = 587; // TLS için 587, SSL için 465
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // Recipients
        $mail->setFrom('b2b@noktaelektronik.net', $mailbaslik);
        $mail->addAddress($alici);

        if($konu == "Cari Ödeme Bildirimi"){
            $mail->addBCC("muhasebe@noktaelektronik.net");
        }
        if($konu != "Arızalı Cihaz Durumu!"){
            $mail->addBCC("h.pamuk@noktaelektronik.net");
            $mail->addBCC("kadir@noktaelektronik.net");
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $konu;
        $mail->Body    = $mesaj_icerik;

        $mail->send();
        echo "Mail başarıyla gönderildi.";
    } catch (Exception $e) {
        echo "Mail gönderilemedi. Hata: {$mail->ErrorInfo}";
    }
}

mailGonder(
    'hmzpmk34@gmail.com',
    'Nokta Elektronik B2B Mail Deneme',
    'deneme maildir',
    'Nokta Elektronik B2B Mail Deneme'
);
