<?php
include "mail_gonder.php";

$uye = "hasan";
$sip_id = "10";
$siparis_no = "123";

$mail = "hmzpmk34@gmail.com";

$mail_icerik = siparisAlindi($uye, $sip_id, $siparis_no);
mailGonder($mail, 'Siparis Deneme', $mail_icerik, 'Nokta');


?>