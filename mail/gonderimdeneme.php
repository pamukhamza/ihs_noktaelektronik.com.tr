<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function mailGonder($alici, $konu, $mesaj_icerik, $mailbaslik){
    include 'Exception.php';
    include 'PHPMailer.php';
    include 'SMTP.php';
    $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = 0; // Enable verbose debug output (set to 2 for maximum detail)
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'mail.noktaelektronik.net'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'noktab2b@noktaelektronik.net'; // SMTP username
        $mail->Password = 'Nktbb2023*'; // SMTP password
        $mail->SMTPSecure = 'ssl'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465; // TCP port to connect to veya 587
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64'; 
        //Recipients
        $mail->setFrom('noktab2b@noktaelektronik.net', $mailbaslik);
        $mail->addAddress($alici); // Add a recipient
        if($konu == "Cari Ödeme Bildirimi"){
            $mail->addBCC("muhasebe@noktaelektronik.net");
        }
        if($konu != "Arızalı Cihaz Durumu!"){
            $mail->addBCC("h.pamuk@noktaelektronik.net");
            $mail->addBCC("kadir@noktaelektronik.net");
        }
        //Content
        $mail->Subject = $konu;
        $mail->Body = "$mesaj_icerik";
        // Set email format to HTML
        $mail->isHTML(true);
        // Try to send the email
        try {
            $mail->send();
        } catch (Exception $e) {
        }
}
mailGonder('hmzpmk34@gmail.com', 'Nokta Elektronik B2B Mail Deneme', 'deneme maildir', 'Nokta Elektronik B2B Mail Deneme');

