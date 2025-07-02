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
    //Recipients
    $mail->setFrom('b2b@noktaelektronik.net', $mailbaslik);
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
        error_log("Mail başarıyla gönderildi - Alıcı: " . $alici);
    } catch (Exception $e) {
        error_log("Mail gönderimi başarısız - Hata: " . $e->getMessage());
        throw $e; // Hatayı yukarı ilet
    }
}
function siparisAlindi($uye, $sip_id, $siparis_no){
    ob_start();
    ?>
    <table style=" margin-left: auto; margin-right: auto;  height:auto; width: 100%; min-width: 350px; max-width: 750px; font-family: &quot;Source Sans Pro&quot;, Arial, Tahoma, Geneva, sans-serif;">
    </table>

    <table style="max-width: 750px; width: 100%; background-color: rgb(72,4,102);">
        <tbody>
        <tr>
            <td align="center" style="padding-top: 20px; padding-bottom: 20px; width: 100%; max-width: 750px; background-color: rgb(72,4,102);"><a href="www.noktaelektronik.com.tr" style="text-decoration: none; align-items: center; width: 250px;" target="_blank"><img src="https://www.noktaelektronik.com.tr/assets/images/nokta-logo-beyaz.png" width="30%"/></a></td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr>
            <td align="center" style="width: 100%; height: 35px; line-height: 35px; max-width: 750px;"><img src="https://ci5.googleusercontent.com/proxy/F8CvHq6tqRXdMWR2SJ6TZ4mgz1ToO4x4hjadwMx9DJPdylF_gApmvzsh_p2z5APOkhEb3iMwfDSaxatv3BSgr8mp9XaMJZSvPcjR96Bz1r4g1hU144Gej1sWUA=s0-d-e1-ft#https://images.hepsiburada.net/banners/0/imageUrl2089_20200917121500.png" width="48" /></td>
        </tr>
        <tr>
            <td align="center" style="width: 100%; height: 35px; line-height: 35px; max-width: 750px; text-align: center; min-width: 350px; margin-top: 25px; font-size: 30px;"><strong>Siparişiniz için Teşekkürler!</strong></td>
        </tr>
        <tr>
            <td align="center" style="width: 100%; height: 30px; line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 15px;">Siparişiniz kargoya verildiğinde e-posta ile sizi bilgilendireceğiz.</td>
        </tr>
        <tr style="margin-top: 20px;">
            <td align="center" style="margin-top: 20px; width: 100%; height: 30px; line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 20px;"><span style="font-size:20px;"><strong>Sayın; <?= $uye ?> </strong></span></td>
        </tr>
        <tr>
            <td align="center" style="width: 100%;  line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 20px;"><span style="font-size:20px;"><?= $siparis_no ?> numaralı siparişiniz başarıyla alınmıştır.<br />
			Siparişinizi kargoya verdiğimizde size bir e-posta göndereceğiz.</span></td>
        </tr>
        </tbody>
    </table>
    <?php
    $db = new Database();
    $urunlar = $db->fetchAll("SELECT su.*, nu.*, 
            (SELECT nr.KResim FROM nokta_urunler_resimler AS nr WHERE nr.UrunID = nu.id LIMIT 1) AS foto 
        FROM b2b_siparis_urunler AS su 
        LEFT JOIN nokta_urunler AS nu ON su.urun_id = nu.id
        WHERE su.sip_id = :sip_id" , ['sip_id' => $sip_id]);

    ?>
    <table style="margin-top: 10px; width: 100%; max-width: 750px; border: 1px solid #ccc; border-collapse: collapse; background-color: #f9f9f9;">
        <thead>
        <tr style="background-color: #430666">
            <th style="border: 1px solid #ccc; color:white">Fotoğraf</th>
            <th style="border: 1px solid #ccc; color:white">Ürün Adı</th>
            <th style="border: 1px solid #ccc; color:white">Stok Kodu</th>
            <th style="border: 1px solid #ccc; color:white">Miktar</th>
            <th style="border: 1px solid #ccc; color:white">Birim Fiyat</th>
            <th style="border: 1px solid #ccc; color:white">Toplam Fiyat</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($urunlar as $urun): ?>
            <tr>
                <td style="border: 1px solid #ccc; width: 60px; padding: 4px; text-align: center;">
                    <?php
                    $imageUrl = "https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/" . $urun["foto"];
                    $imageData = @file_get_contents($imageUrl);
                    $base64Image = '';

                    if ($imageData !== false) {
                        $imageInfo = getimagesizefromstring($imageData);
                        $imageType = $imageInfo['mime'] ?? '';

                        if ($imageType === 'image/jpeg') {
                            $base64Image = 'data:image/jpeg;base64,' . base64_encode($imageData);
                        } elseif ($imageType === 'image/webp') {
                            $image = @imagecreatefromstring($imageData);
                            if ($image !== false) {
                                ob_start();
                                imagejpeg($image, null, 75);
                                $jpegData = ob_get_contents();
                                ob_end_clean();
                                $base64Image = 'data:image/jpeg;base64,' . base64_encode($jpegData);
                                imagedestroy($image);
                            } else {
                                echo 'Resim oluşturulamadı.';
                            }
                        } else {
                            echo 'Desteklenmeyen resim türü.';
                        }
                    } else {
                        echo 'Resim verisi alınamadı.';
                    }
                    ?>

                    <?php if (!empty($base64Image)): ?>
                        <div>
                            <div style="width: 60px; height: 60px; overflow: hidden;">
                                <img src="<?= $base64Image ?>" alt="Ürün Resmi" style="width: 60px !important; height: 60px !important; object-fit: cover !important;" />

                            </div>
                        </div>
                    <?php else: ?>
                        <div>Image not found</div>
                    <?php endif; ?>
                </td>
                <td style="border: 1px solid #ccc; text-align: center;"><?= $urun["UrunAdiTR"] ?></td>
                <td style="border: 1px solid #ccc; text-align: center;"><?= $urun["UrunKodu"] ?></td>
                <td style="border: 1px solid #ccc; text-align: center;"><?= $urun["adet"] ?></td>
                <td style="border: 1px solid #ccc; text-align: center;">
                    <?php
                    if(!empty($urun["DSF1"] || !empty($urun["DSF2"]) || !empty($urun["DSF3"]) || !empty($urun["DSF4"]) )){
                    $birim_fiyat = $urun["birim_fiyat"] * $urun["dolar_satis"];
                    $toplam_birim_fiyat = $urun["birim_fiyat"] * $urun["dolar_satis"] * $urun["adet"];
                    $formatted_birim_fiyat = number_format($birim_fiyat, 2, ',', '.');
                    $formatted_toplam_birim_fiyatDvz = number_format($toplam_birim_fiyat, 2, ',', '.');
                    echo $formatted_birim_fiyat . "₺ + KDV";
                    }else{
                    $birim_fiyat = $urun["birim_fiyat"];
                    $toplam_birim_fiyat = $urun["birim_fiyat"] * $urun["adet"];
                    $formatted_birim_fiyat = number_format($birim_fiyat, 2, ',', '.');
                    $formatted_toplam_birim_fiyatTL = number_format($toplam_birim_fiyat, 2, ',', '.');
                    echo $formatted_birim_fiyat . "₺ + KDV";
                    }
                    ?>
                </td>
                <td style="border: 1px solid #ccc; text-align: center;">
                    <?php
                    if(!empty($urun["DSF1"] || !empty($urun["DSF2"]) || !empty($urun["DSF3"]) || !empty($urun["DSF4"]) )){
                        echo $formatted_toplam_birim_fiyatDvz . "₺ + KDV";
                    }else{
                        echo $formatted_toplam_birim_fiyatTL . "₺ + KDV";
                    }
                    ?>

                </td>
            </tr>
        <?php endforeach; 
        $siparisler = $db->fetch("SELECT * FROM b2b_siparisler WHERE id = :id " , ['id' => $sip_id]);

        if($siparisler["indirim"] != '0.00'){ ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="border: 1px solid #ccc; text-align: center; font-weight: bold;">İndirim :</td>
                <td style="border: 1px solid #ccc; text-align: center; font-weight: bold;">
                    <?php
                    $indirim = $siparisler["indirim"];
                    $indirim_float = (float) str_replace(',', '.', $indirim); // Replace ',' with '.' for correct float conversion
                    $formatted_indirim = number_format($indirim_float, 2, ',', '.');
                    ?>
                    <?= $formatted_indirim ?>₺
                </td>
            </tr>
        <?php } 
        if($siparisler["kargo_ucreti"] != '0.00'){ ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="border: 1px solid #ccc; text-align: center; font-weight: bold;">Kargo Ücreti :</td>
                <td style="border: 1px solid #ccc; text-align: center; font-weight: bold;">
                    <?php
                    $kargo = $siparisler["kargo_ucreti"];
                    $kargo_float = (float) str_replace(',', '.', $kargo); // Replace ',' with '.' for correct float conversion
                    $formatted_kargo = number_format($kargo_float, 2, ',', '.');
                    ?>
                    <?= $formatted_kargo ?>₺
                </td>
            </tr>
        <?php } ?>
            <tr style="background-color: #430666">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="border: 1px solid #ccc; text-align: center; font-weight: bold; color:white">KDV Dahil Toplam :</td>
                <td style="border: 1px solid #ccc; text-align: center; font-weight: bold; color:white">
                    <?php
                        $toplam_fiyat = $siparisler["toplam"];
                        $toplam_fiyat_float = (float) str_replace(',', '.', $toplam_fiyat);
                        $formatted_fiyat = number_format($toplam_fiyat_float, 2, ',', '.');
                    ?>
                    <?= $formatted_fiyat ?>₺
                </td>
            </tr>
        </tbody>
    </table>
    <table style="margin-top: 30px; width: 100%; max-width: 750px;" width="750">
        <tbody>
        <tr>
            <td align="center" style="background-color: rgb(70, 70, 70);" valign="top">&nbsp;
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100% !important; min-width: 100%; max-width: 100%;" width="100%">
                    <tbody>
                    <tr>
                        <td align="center" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td align="center" valign="top">
                                        <div style="height: 34px; line-height: 34px; font-size: 14px;">&nbsp;</div>
                                        <span style="font-size:12px;"><span style="font-family:tahoma,geneva,sans-serif;"><font color="#f1f1f1" style="font-size: 17px; line-height: 16px;"><span style="line-height: 16px;"><a href="mailto:b2b@noktaelektronik.net" style="text-decoration: none; color: #f1f1f1;">b2b@noktaelektronik.net</a> &nbsp; &nbsp;|&nbsp; <a href="tel:08503330208" style="text-decoration: none; color: #f1f1f1;">0850 333 02 08</a> &nbsp; |&nbsp; &nbsp;<a href="https://noktaelektronik.com.tr/" style="text-decoration: none; color: #f1f1f1;">www.noktaelektronik.com.tr</a></span> </font></span></span>

                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top"><a href="https://twitter.com/NEBSIS" style="display: block; max-width: 20px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/25_x.png" width="30" style="display: block; width: 20px; " /></a></td>
                                                <td style="width: 10px; max-width: 10px; min-width: 10px;" width="10">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.facebook.com/nebsis" style="display: block; max-width: 20px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/02_facebook.png" width="30" style="display: block; width: 20px; " /></a></td>
                                                <td style="width: 10px; max-width: 10px; min-width: 10px;" width="10">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.youtube.com/c/NoktaElektronikLTD" style="display: block; max-width: 20px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/03_youtube.png" width="30" style="display: block; width: 20px;" /></a></td>
                                                <td style="width: 10px; max-width: 10px; min-width: 10px;" width="10">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.instagram.com/noktaelektronik/" style="display: block; max-width: 20px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/10_instagram.png" width="30" style="display: block; width: 20px;" /></a></td>
                                                <td style="width: 10px; max-width: 10px; min-width: 10px;" width="10">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.linkedin.com/in/nokta-elektronik-57107b128/" style="display: block; max-width: 20px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/07_linkedin.png" width="30" style="display: block; width: 20px;" /></a></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <?php
    $content = ob_get_contents(); // Tamponlanan içeriği al
    ob_end_clean(); // Tamponlamayı temizle ve kapat

    return $content;
}
function cariOdeme($uye,$fiyat,$taksit){
    ob_start();
    ?>
    <table style=" margin-left: auto; margin-right: auto;  height:auto; width: 100%; min-width: 350px; max-width: 750px; font-family: &quot;Source Sans Pro&quot;, Arial, Tahoma, Geneva, sans-serif;">
    </table>

    <table style="max-width: 750px; width: 100%; background-color: rgb(72,4,102);">
        <tbody>
        <tr>
            <td align="center" style="padding-top: 20px; padding-bottom: 20px; width: 100%; max-width: 750px; background-color: rgb(72,4,102);"><a href="www.noktaelektronik.com.tr" style="text-decoration: none; align-items: center; width:250px;" target="_blank"><img src="https://www.noktaelektronik.com.tr/assets/images/nokta-logo-beyaz.png" width="30%" /></a></td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr>
            <td align="center" style="width: 100%; height: 35px; line-height: 35px; max-width: 750px;"><img src="https://ci5.googleusercontent.com/proxy/F8CvHq6tqRXdMWR2SJ6TZ4mgz1ToO4x4hjadwMx9DJPdylF_gApmvzsh_p2z5APOkhEb3iMwfDSaxatv3BSgr8mp9XaMJZSvPcjR96Bz1r4g1hU144Gej1sWUA=s0-d-e1-ft#https://images.hepsiburada.net/banners/0/imageUrl2089_20200917121500.png" width="48" /></td>
        </tr>
        <tr>
            <td align="center" style="width: 100%; height: 35px; line-height: 35px; max-width: 750px; text-align: center; min-width: 350px; margin-top: 25px; font-size: 30px;"><strong>Cari Ödeme Bildirimi</strong></td>
        </tr>
        <tr style="margin-top: 20px;">
            <td align="center" style="margin-top: 20px; width: 100%; height: 30px; line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 20px;"><span style="font-size:20px;">Sayın<strong> <?= $uye ?> </strong>,</span></td>
        </tr>
        <tr>
            <td align="center" style="width: 100%;  line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 20px;"><span style="font-size:20px;">Ödemeniz için teşekkür ederiz.<br />
        </tr>
        <tr>
            <td align="center" style="width: 100%;  line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 20px;"><span style="font-size:20px;">Ödenen Tutar: <?= $fiyat ?> <br/>
        </tr>
        <tr>
            <td align="center" style="width: 100%;  line-height: 30px; max-width: 750px; text-align: center; min-width: 350px; font-size: 20px;"><span style="font-size:20px;">Taksit: <?= $taksit ?> <br />
        </tr>
        </tbody>
    </table>
    <table style="margin-top: 20px; width: 100%; max-width: 750px;" width="750">
        <tbody>
        <tr>
            <td align="center" style="background-color: rgb(70, 70, 70);" valign="top">&nbsp;
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100% !important; min-width: 100%; max-width: 100%;" width="100%">
                    <tbody>
                    <tr>
                        <td align="center" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td align="center" valign="top">
                                        <div style="height: 34px; line-height: 34px; font-size: 14px;">&nbsp;</div>
                                        <span style="font-size:12px;"><span style="font-family:tahoma,geneva,sans-serif;"><font color="#f1f1f1" style="font-size: 17px; line-height: 16px;"><span style="line-height: 16px;"><a href="mailto:b2b@noktaelektronik.net" style="text-decoration: none; color: #f1f1f1;">b2b@noktaelektronik.net</a> &nbsp; &nbsp;|&nbsp; <a href="tel:08503330208" style="text-decoration: none; color: #f1f1f1;">0850 333 02 08</a> &nbsp; |&nbsp; &nbsp;<a href="https://noktaelektronik.com.tr/" style="text-decoration: none; color: #f1f1f1;">www.noktaelektronik.com.tr</a></span> </font></span></span>

                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top"><a href="https://twitter.com/NEBSIS" style="display: block; max-width: 30px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/25_x.png" width="30" style="display: block; width: 30px; " /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.facebook.com/nebsis" style="display: block; max-width: 30px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/02_facebook.png" width="30" style="display: block; width: 30px; " /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.youtube.com/c/NoktaElektronikLTD" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/03_youtube.png" width="30" style="display: block; width: 30px;" /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.instagram.com/noktaelektronik/" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/10_instagram.png" width="30" style="display: block; width: 30px;" /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.linkedin.com/in/nokta-elektronik-57107b128/" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/07_linkedin.png" width="30" style="display: block; width: 30px;" /></a></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <?php
    $content = ob_get_contents(); // Tamponlanan içeriği al
    ob_end_clean(); // Tamponlamayı temizle ve kapat

    return $content;
}
function iadeAlindiMail($uye, $siparis_no){
    ob_start();
    ?>
    <table style=" margin-left: auto; margin-right: auto;  height:auto; width: 100%; min-width: 350px; max-width: 750px; font-family: &quot;Source Sans Pro&quot;, Arial, Tahoma, Geneva, sans-serif;">
    </table>

    <table style="max-width: 750px; width: 100%; background-color: rgb(72,4,102);">
        <tbody>
        <tr>
            <td align="center" style="padding-top: 20px; padding-bottom: 20px; width: 100%; max-width: 750px; background-color: rgb(72,4,102);"><a href="www.noktaelektronik.com.tr" style="text-decoration: none; align-items: center; width: 250px;" target="_blank"><img src="https://www.noktaelektronik.com.tr/assets/images/nokta-logo-beyaz.png" width="30%"/></a></td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr>
            <td style="width: 100%; line-height: 30px; max-width: 750px; min-width: 350px; font-size: 16px;">
                <p>Sayın&nbsp; <strong><?= $uye ?></strong>,<br />
                    <strong><?= $siparis_no ?>&nbsp;</strong>sipariş numaralı iade talebiniz alınmıştır. İade takibinizi paneldeki iadelerim sayfasında takip edebilirsiniz.&nbsp;</p>

                <p>&nbsp;</p>
            </td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 16px; width: 100%; max-width: 750px;" width="750">
        <tbody>
        <tr>
            <td align="center" style="background-color: rgb(70, 70, 70);" valign="top">&nbsp;
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100% !important; min-width: 100%; max-width: 100%;" width="100%">
                    <tbody>
                    <tr>
                        <td align="center" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td align="center" valign="top">
                                        <div style="height: 34px; line-height: 34px; font-size: 14px;">&nbsp;</div>
                                        <span style="font-size:12px;"><span style="font-family:tahoma,geneva,sans-serif;"><font color="#f1f1f1" style="font-size: 17px; line-height: 16px;"><span style="line-height: 16px;"><a href="mailto:b2b@noktaelektronik.net" style="text-decoration: none; color: #f1f1f1;">b2b@noktaelektronik.net</a> &nbsp; &nbsp;|&nbsp; <a href="tel:08503330208" style="text-decoration: none; color: #f1f1f1;">0850 333 02 08</a> &nbsp; |&nbsp; &nbsp;<a href="https://noktaelektronik.com.tr/" style="text-decoration: none; color: #f1f1f1;">www.noktaelektronik.com.tr</a></span> </font></span></span>

                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top"><a href="https://twitter.com/NEBSIS" style="display: block; max-width: 30px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/25_x.png" width="30" style="display: block; width: 30px; " /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.facebook.com/nebsis" style="display: block; max-width: 30px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/02_facebook.png" width="30" style="display: block; width: 30px; " /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.youtube.com/c/NoktaElektronikLTD" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/03_youtube.png" width="30" style="display: block; width: 30px;" /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.instagram.com/noktaelektronik/" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/10_instagram.png" width="30" style="display: block; width: 30px;" /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.linkedin.com/in/nokta-elektronik-57107b128/" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/07_linkedin.png" width="30" style="display: block; width: 30px;" /></a></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <?php
    $content = ob_get_contents(); // Tamponlanan içeriği al
    ob_end_clean(); // Tamponlamayı temizle ve kapat

    return $content;
}
function uyeOnayMail($uye, $uye_mail, $aktivasyon){
    ob_start();
    ?>
    <table style=" margin-left: auto; margin-right: auto;  height:auto; width: 100%; min-width: 350px; max-width: 750px; font-family: &quot;Source Sans Pro&quot;, Arial, Tahoma, Geneva, sans-serif;">
    </table>

    <table style="max-width: 750px; width: 100%; background-color: rgb(72,4,102);">
        <tbody>
        <tr>
            <td align="center" style="padding-top: 20px; padding-bottom: 20px; width: 100%; max-width: 750px; background-color: rgb(72,4,102);"><a href="www.noktaelektronik.com.tr" style="text-decoration: none; align-items: center; width:250px;" target="_blank"><img src="https://www.noktaelektronik.com.tr/assets/images/nokta-logo-beyaz.png" width="30%" /></a></td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr style="margin-top: 20px;">
            <td style="margin-top: 20px; width: 100%; height: 30px; line-height: 30px; max-width: 750px;  min-width: 350px; font-size: 20px;">Sayın <?= $uye ?>;</td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr>
            <td style="width: 100%;  line-height: 30px; max-width: 750px;  min-width: 350px; font-size: 20px;"><strong>Nokta Elektronik&#39;e Hoş Geldiniz.</strong></td>
        </tr>
        <tr>
            <td style="width: 100%;  line-height: 30px; max-width: 750px;  min-width: 350px; font-size: 20px;"><strong>Üyeliğiniz Başarıyla Tamamlanmıştır.</strong></td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr>
            <td style="width: 100%;  line-height: 30px; max-width: 750px;  min-width: 350px; font-size: 20px;">Ad Soyad: <?= $uye ?></td>
        </tr>
        <tr>
            <td style="width: 100%;  line-height: 30px; max-width: 750px;  min-width: 350px; font-size: 20px;">E-Posta Adresi: <?= $uye_mail ?></td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr>
            <td style="display: block; width: 100%; height: 45px;  font-family: &quot;Source Sans Pro&quot;, Arial, Verdana, Tahoma, Geneva, sans-serif; background-color: #27cbcc;  font-size: 20px; line-height: 45px; text-align: center; text-decoration-line: none; white-space: nowrap; font-weight: 600;">
                <a href="https://www.noktaelektronik.com.tr/tr/aktivasyon?id=<?= $aktivasyon ?>" style="text-decoration-line: none; color: rgb(255, 255, 255); white-space: nowrap;">Üyeliğinizi aktif etmek için tıklayınız.</a>
            </td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 16px; width: 100%; max-width: 750px;" width="750">
        <tbody>
        <tr>
            <td align="center" style="background-color: rgb(70, 70, 70);" valign="top">&nbsp;
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100% !important; min-width: 100%; max-width: 100%;" width="100%">
                    <tbody>
                    <tr>
                        <td align="center" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td align="center" valign="top">
                                        <div style="height: 34px; line-height: 34px; font-size: 14px;">&nbsp;</div>
                                        <span style="font-size:12px;"><span style="font-family:tahoma,geneva,sans-serif;"><font color="#f1f1f1" style="font-size: 17px; line-height: 16px;"><span style="line-height: 16px;"><a href="mailto:b2b@noktaelektronik.net" style="text-decoration: none; color: #f1f1f1;">b2b@noktaelektronik.net</a> &nbsp; &nbsp;|&nbsp; <a href="tel:08503330208" style="text-decoration: none; color: #f1f1f1;">0850 333 02 08</a> &nbsp; |&nbsp; &nbsp;<a href="https://noktaelektronik.com.tr/" style="text-decoration: none; color: #f1f1f1;">www.noktaelektronik.com.tr</a></span> </font></span></span>

                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top"><a href="https://twitter.com/NEBSIS" style="display: block; max-width: 30px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/25_x.png" width="30" style="display: block; width: 30px; " /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.facebook.com/nebsis" style="display: block; max-width: 30px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/02_facebook.png" width="30" style="display: block; width: 30px; " /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.youtube.com/c/NoktaElektronikLTD" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/03_youtube.png" width="30" style="display: block; width: 30px;" /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.instagram.com/noktaelektronik/" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/10_instagram.png" width="30" style="display: block; width: 30px;" /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.linkedin.com/in/nokta-elektronik-57107b128/" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/07_linkedin.png" width="30" style="display: block; width: 30px;" /></a></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <?php
    $content = ob_get_contents(); // Tamponlanan içeriği al
    ob_end_clean(); // Tamponlamayı temizle ve kapat

    return $content;
}
function sifreDegistimeMail($uye, $kod){
    ob_start();
    ?>
    <table style=" margin-left: auto; margin-right: auto;  height:auto; width: 100%; min-width: 350px; max-width: 750px; font-family: &quot;Source Sans Pro&quot;, Arial, Tahoma, Geneva, sans-serif;">
    </table>

    <table style="max-width: 750px; width: 100%; background-color: rgb(72,4,102);">
        <tbody>
        <tr>
            <td align="center" style="padding-top: 20px; padding-bottom: 20px; width: 100%; max-width: 750px; background-color: rgb(72,4,102);"><a href="www.noktaelektronik.com.tr" style="text-decoration: none; align-items: center; width:250px;" target="_blank"><img src="https://www.noktaelektronik.com.tr/assets/images/nokta-logo-beyaz.png" width="30%" /></a></td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr style="margin-top: 20px;">
            <td style="margin-top: 20px; width: 100%; height: 30px; line-height: 30px; max-width: 750px;  min-width: 350px; font-size: 18px;"><strong>Sayın <?= $uye ?>,</strong></td>
        </tr>
        <tr>
            <td style="width: 100%;  line-height: 30px; max-width: 750px;  min-width: 350px; font-size: 18px;">noktaelektronik.com.tr sitemizden yeni bir şifre almak için aşağıdaki linke tıklayınız.</td>
        </tr>
        <tr>
            <td style="width: 100%;  line-height: 30px; max-width: 750px;  min-width: 350px; font-size: 18px;">
                <a href="https://www.noktaelektronik.com.tr/tr/sifreguncelle.php?code=<?= $kod ?>" style="text-decoration-line: none; color: rgb(0, 0, 255); white-space: nowrap;">Şifre sıfırlama linki</a>
            </td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 16px; width: 100%; max-width: 750px;" width="750">
        <tbody>
        <tr>
            <td align="center" style="background-color: rgb(70, 70, 70);" valign="top">&nbsp;
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100% !important; min-width: 100%; max-width: 100%;" width="100%">
                    <tbody>
                    <tr>
                        <td align="center" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td align="center" valign="top">
                                        <div style="height: 34px; line-height: 34px; font-size: 14px;">&nbsp;</div>
                                        <span style="font-size:12px;"><span style="font-family:tahoma,geneva,sans-serif;"><font color="#f1f1f1" style="font-size: 17px; line-height: 16px;"><span style="line-height: 16px;"><a href="mailto:b2b@noktaelektronik.net" style="text-decoration: none; color: #f1f1f1;">b2b@noktaelektronik.net</a> &nbsp; &nbsp;|&nbsp; <a href="tel:08503330208" style="text-decoration: none; color: #f1f1f1;">0850 333 02 08</a> &nbsp; |&nbsp; &nbsp;<a href="https://noktaelektronik.com.tr/" style="text-decoration: none; color: #f1f1f1;">www.noktaelektronik.com.tr</a></span> </font></span></span>

                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top"><a href="https://twitter.com/NEBSIS" style="display: block; max-width: 30px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/25_x.png" width="30" style="display: block; width: 30px; " /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.facebook.com/nebsis" style="display: block; max-width: 30px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/02_facebook.png" width="30" style="display: block; width: 30px; " /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.youtube.com/c/NoktaElektronikLTD" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/03_youtube.png" width="30" style="display: block; width: 30px;" /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.instagram.com/noktaelektronik/" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/10_instagram.png" width="30" style="display: block; width: 30px;" /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.linkedin.com/in/nokta-elektronik-57107b128/" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/07_linkedin.png" width="30" style="display: block; width: 30px;" /></a></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <?php
    $content = ob_get_contents(); // Tamponlanan içeriği al
    ob_end_clean(); // Tamponlamayı temizle ve kapat

    return $content;
}
function iletisimFormMail($adSoyad, $email, $tarih, $mesaj){
    ob_start();
    ?>
    <div style="background:#f4f4f4; font-family:Trebuchet MS; font-size:10pt; padding:10px; border-radius:5px; border:solid 1px #ddd; width:800px;">
        <h2 style="text-align: center; font-size:13pt; color:#555;">İSTEK ÖNERİ TALEBİ</h2>

        <h3 style="border-top:solid 1px #fff;border-bottom:solid 1px #d2d2d2;margin:0;padding:10px 10px 12px;color:#c31c09;background-color:#fff;">GÖNDERİCİ BİLGİLERİ</h3>

        <div>&nbsp;<span style="font-size:11pt; color:#555; padding-left:15px;">Adı Soyadı: &nbsp;<?= $adSoyad?></span></div>

        <div>&nbsp;<span style="font-size:11pt; color:#555; padding-left:15px;">Mail: &nbsp;<?= $email ?></span></div>
        &nbsp;

        <h3 style="border-top:solid 1px #fff;border-bottom:solid 1px #d2d2d2;margin:0;padding:10px 10px 12px;color:#c31c09;background-color:#fff;">MESAJ DETAYLARI</h3>

        <div>&nbsp;<span style="font-size:11pt; color:#555; padding-left:15px;">Tarih: &nbsp;<?= $tarih ?></span></div>

        <div>&nbsp;<span style="font-size:11pt; color:#555; padding-left:15px;">İçerik: &nbsp;<?= $mesaj ?></span></div>
    </div>

    <?php
    $content = ob_get_contents(); // Tamponlanan içeriği al
    ob_end_clean(); // Tamponlamayı temizle ve kapat
    return $content;
}
function teklifAlindiMail($uye){
    ob_start();
    ?>
    <table style="   height:auto; width: 100%; min-width: 350px; max-width: 750px; font-family: &quot;Source Sans Pro&quot;, Arial, Tahoma, Geneva, sans-serif;">
    </table>

    <table style="max-width: 750px; width: 100%; background-color: rgb(72,4,102);">
        <tbody>
        <tr>
            <td align="center" style="padding-top: 20px; padding-bottom: 20px; width: 100%; max-width: 750px; background-color: rgb(72,4,102);"><a href="www.noktaelektronik.com.tr" style="text-decoration: none; align-items: center; width: 250px;" target="_blank"><img src="https://www.noktaelektronik.com.tr/assets/images/nokta-logo-beyaz.png" width="30%"/></a></td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr>
            <td style="width: 100%; line-height: 30px; max-width: 750px; min-width: 350px; font-size: 16px;">
                <p>
                <?php if(!empty($uye)){ ?>
                Sayın&nbsp; <strong><?= $uye ?></strong>,<br />
                <?php } ?>
                    Teklifiniz tarafımıza ulaşmıştır. En kısa sürede tarafınıza dönüş sağlanacaktır.&nbsp;</p>

                <p>&nbsp;</p>
            </td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 30px; width: 100%; max-width: 750px;" width="750">
        <tbody>
        <tr>
            <td align="center" style="background-color: rgb(70, 70, 70);" valign="top">&nbsp;
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100% !important; min-width: 100%; max-width: 100%;" width="100%">
                    <tbody>
                    <tr>
                        <td align="center" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td align="center" valign="top">
                                        <div style="height: 34px; line-height: 34px; font-size: 14px;">&nbsp;</div>
                                        <span style="font-size:12px;"><span style="font-family:tahoma,geneva,sans-serif;"><font color="#f1f1f1" style="font-size: 17px; line-height: 16px;"><span style="line-height: 16px;"><a href="mailto:b2b@noktaelektronik.net" style="text-decoration: none; color: #f1f1f1;">b2b@noktaelektronik.net</a> &nbsp; &nbsp;|&nbsp; <a href="tel:08503330208" style="text-decoration: none; color: #f1f1f1;">0850 333 02 08</a> &nbsp; |&nbsp; &nbsp;<a href="https://noktaelektronik.com.tr/" style="text-decoration: none; color: #f1f1f1;">www.noktaelektronik.com.tr</a></span> </font></span></span>

                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top"><a href="https://twitter.com/NEBSIS" style="display: block; max-width: 20px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/25_x.png" width="30" style="display: block; width: 20px; " /></a></td>
                                                <td style="width: 10px; max-width: 10px; min-width: 10px;" width="10">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.facebook.com/nebsis" style="display: block; max-width: 20px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/02_facebook.png" width="30" style="display: block; width: 20px; " /></a></td>
                                                <td style="width: 10px; max-width: 10px; min-width: 10px;" width="10">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.youtube.com/c/NoktaElektronikLTD" style="display: block; max-width: 20px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/03_youtube.png" width="30" style="display: block; width: 20px;" /></a></td>
                                                <td style="width: 10px; max-width: 10px; min-width: 10px;" width="10">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.instagram.com/noktaelektronik/" style="display: block; max-width: 20px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/10_instagram.png" width="30" style="display: block; width: 20px;" /></a></td>
                                                <td style="width: 10px; max-width: 10px; min-width: 10px;" width="10">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.linkedin.com/in/nokta-elektronik-57107b128/" style="display: block; max-width: 20px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/07_linkedin.png" width="30" style="display: block; width: 20px;" /></a></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <?php
    $content = ob_get_contents(); // Tamponlanan içeriği al
    ob_end_clean(); // Tamponlamayı temizle ve kapat

    return $content;
}
function arizaKayitMail($uye, $takip){
    ob_start();
    ?>
    <table style=" margin-left: auto; margin-right: auto;  height:auto; width: 100%; min-width: 350px; max-width: 750px; font-family: &quot;Source Sans Pro&quot;, Arial, Tahoma, Geneva, sans-serif;">
    </table>

    <table style="max-width: 750px; width: 100%; background-color: rgb(72,4,102);">
        <tbody>
        <tr>
            <td align="center" style="padding-top: 20px; padding-bottom: 20px; width: 100%; max-width: 750px; background-color: rgb(72,4,102);"><a href="www.noktaelektronik.com.tr" style="text-decoration: none; align-items: center; width:250px;" target="_blank"><img src="https://www.noktaelektronik.com.tr/assets/images/nokta-logo-beyaz.png" width="30%" /></a></td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr style="margin-top: 20px;">
            <td style="margin-top: 20px; width: 100%; height: 30px; line-height: 30px; max-width: 750px;  min-width: 350px; font-size: 20px;">Sayın <?= $uye ?>;</td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr>
            <td style="width: 100%;  line-height: 30px; max-width: 750px;  min-width: 350px; font-size: 20px;"><strong>Arıza Kaydınız Oluşturulmuştur.</strong></td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; max-width: 750px;">
        <tbody>
        <tr>
            <td style="width: 100%;  line-height: 30px; max-width: 750px;  min-width: 350px; font-size: 20px;">Takip Kodu: <?= $takip ?><br><a href="https://www.noktaelektronik.com.tr/tr/tdp">Buraya tıklayarak</a> ürünlerinizin durumunu takip edebilirsiniz.</td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 16px; width: 100%; max-width: 750px;" width="750">
        <tbody>
        <tr>
            <td align="center" style="background-color: rgb(70, 70, 70);" valign="top">&nbsp;
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100% !important; min-width: 100%; max-width: 100%;" width="100%">
                    <tbody>
                    <tr>
                        <td align="center" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td align="center" valign="top">
                                        <div style="height: 34px; line-height: 34px; font-size: 14px;">&nbsp;</div>
                                        <span style="font-size:12px;"><span style="font-family:tahoma,geneva,sans-serif;"><font color="#f1f1f1" style="font-size: 17px; line-height: 16px;"><span style="line-height: 16px;"><a href="mailto:b2b@noktaelektronik.net" style="text-decoration: none; color: #f1f1f1;">b2b@noktaelektronik.net</a> &nbsp; &nbsp;|&nbsp; <a href="tel:08503330208" style="text-decoration: none; color: #f1f1f1;">0850 333 02 08</a> &nbsp; |&nbsp; &nbsp;<a href="https://noktaelektronik.com.tr/" style="text-decoration: none; color: #f1f1f1;">www.noktaelektronik.com.tr</a></span> </font></span></span>

                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top"><a href="https://twitter.com/NEBSIS" style="display: block; max-width: 30px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/25_x.png" width="30" style="display: block; width: 30px; " /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.facebook.com/nebsis" style="display: block; max-width: 30px; text-decoration: none; color:#f1f1f1;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/02_facebook.png" width="30" style="display: block; width: 30px; " /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.youtube.com/c/NoktaElektronikLTD" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/03_youtube.png" width="30" style="display: block; width: 30px;" /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.instagram.com/noktaelektronik/" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/10_instagram.png" width="30" style="display: block; width: 30px;" /></a></td>
                                                <td style="width: 20px; max-width: 20px; min-width: 20px;" width="20">&nbsp;</td>
                                                <td align="center" valign="top"><a href="https://www.linkedin.com/in/nokta-elektronik-57107b128/" style="display: block; max-width: 30px; text-decoration: none; color:#ffffff;" target="_blank"><img alt="img" src="https://www.noktaelektronik.com.tr/assets/images/icons/07_linkedin.png" width="30" style="display: block; width: 30px;" /></a></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <?php
    $content = ob_get_contents(); // Tamponlanan içeriği al
    ob_end_clean(); // Tamponlamayı temizle ve kapat

    return $content;
}
?>