<?php
include '../baglanti.php';
include '../f.php';
include '../function.php';
include 'dekont_olustur.php';
include 'kargo_barkod.php';

$uyeAdSoyad = "Deneme Siparişi";
$siparisNumarasi = "123456";
$sipId = "34";

$uye_email = "ghasankececi@gmail.com";

$mail_icerik = siparisAlindi($uyeAdSoyad, $sipId, $siparisNumarasi);
//mailGonder($uye_email, 'Siparişiniz Alınmıştır!', $mail_icerik, 'Siparişiniz Alınmıştır!');
echo $mail_icerik;
/*$uye_id = 748;
$odeme_id = 999;
$ad_soyad = 'Şükür Elektronik Bilişim Güvenlik Dış Ticaret Limited Şirketi';
$cardNo = '5298********9514';
$cardHolder = 'ABDÜLKERİM KÖSE';
$taksit_sayisi = '0';
$odenentutar = '1.693,00';
$date = '2024-05-16 09:23:47';
dekontOlustur($uye_id, $odeme_id, $ad_soyad, $cardNo, $cardHolder, $taksit_sayisi, $odenentutar, $date);


$uye_id = 1860; // Example user ID
$sip_id = 30; // Example order ID
$cargoKey = '123456789'; // Example cargo key

kargopdf($uye_id, $sip_id, $cargoKey);*/
?>