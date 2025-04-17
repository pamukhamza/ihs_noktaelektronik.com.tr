<?php
include '../mail/mail_gonder.php';

$mail_icerik = uyeOnayMail('hamza Pamuk', 'hmzpmk34@gmail.com', '111111111111');
mailGonder('hmzpmk34@gmail.com', 'Nokta Elektronik B2B Mail Deneme', 'deneme maildir', 'Nokta Elektronik B2B Mail Deneme');

