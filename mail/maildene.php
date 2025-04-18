<?php
include "mail_gonder.php";

$uye = "hasan";
$sip_id = "10";
$siparis_no = "123";

$mail = "hmzpmk34@gmail.com";

$mail_icerik = teklifAlindiMail($uye);
mailGonder($mail, 'DENEME', $mail_icerik, 'Nokta');


?>