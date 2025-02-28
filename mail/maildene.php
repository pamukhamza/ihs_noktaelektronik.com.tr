<?php
include "../f.php";
include "../function.php";

$uye = "hasan";
$sip_id = "10";
$siparis_no = "123";

$mail = "ghasankececi@gmail.com";

$mail_icerik = siparisAlindi($uye, $sip_id, $siparis_no);
mailGonder($mail, 'Siparis Deneme', $mail_icerik, 'Nokta');


?>