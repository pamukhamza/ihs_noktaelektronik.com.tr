<?php
session_start();
session_regenerate_id(true);
include('../../db.php');
if(isset($_POST["cariOdeme"])){
	$odemetaksit = $_POST['odemetaksit'];
	if ($odemetaksit == 1 || $odemetaksit == 0) {
		$odemetaksit = 0;
	}
	$odemetutar = $_POST['odemetutar'];
	$odemetutar_float = str_replace(',', '', $odemetutar);

	$pos_id = 3;
	$basarili = 0;
	$sonucStr = 'Cari ödeme sayfasına giriş yapıldı!';
	$stmt = "INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)";
	$db->insert($stmt, ['uye_id' => $_POST["uye_id"], 'pos_id' => $pos_id, 'islem' => $sonucStr, 'tutar' => $_POST["toplam"], 'basarili' => $basarili]);

	$verimiz = [
		"cardHolder" => $_POST["cardName"],
		"cardNo" => $_POST["cardNumber"],
		"yantoplam" => $_POST["toplam"],
		"banka_id" => $_POST["banka_id"],
		"hesap"  => $_POST["hesap"],
		"uye_id" => $_POST["uye_id"],
		"tip" => $_POST["tip"],
		"lang" => $_POST["lang"],
	];
	$verimizB64 = base64_encode(json_encode($verimiz));
	$gidesun = "https://denemeb2b.noktaelektronik.net/functions/siparis/kuveyt_sip_olustur.php?cariveri=" .$verimizB64;
	$Name=$_POST["cardName"];
	$CardNumber=$_POST["cardNumber"]; // 16 haneli olarak
	$CardExpireDateMonth=$_POST["expMonth"]; // iki hane olarak kartın ay bilgisi
	$CardExpireDateYear=$_POST["expYear"]; // kartın vade tarihinde yıl alanı, iki hane
	$CardCVV2=$_POST["cvCode"]; // kartın arka yüzündeki CVV2 kodu
	$MerchantOrderId = uniqid();// İşyerinin belirlediği sipariş numarası
	$Amount = $odemetutar_float; //Islem Tutari // Ornegin 1.00 TL için 100 kati yani 100 yazilmali
	$CustomerId = "93981545";// Bankadaki müsteri numarası
	$MerchantId = "61899"; //Sanal pos mağaza numarası, başvuru onayıyla işyerine gönderilir.
	$OkUrl = $gidesun; //Basarili sonuç alinirsa, yönledirelecek sayfa
	$FailUrl = "https://denemeb2b.noktaelektronik.net/tr/cariodeme";//Basarisiz sonuç alinirsa, yönledirelecek sayfa
	$UserName="kadirbabur"; // https://kurumsal.kuveytturk.com.tr adresinde Kullanıcı İşlemleri - Kullanıcı Ekle alanında işyeri tarafından olusturulan api rolünde kullanici adı
	$Password="Dell28736.!";// api rolünde kullanici adının sifresi
	$HashedPassword = base64_encode(sha1($Password,"ISO-8859-9")); //md5($Password);
	$HashData = base64_encode(sha1($MerchantId.$MerchantOrderId.$Amount.$OkUrl.$FailUrl.$UserName.$HashedPassword , "ISO-8859-9"));
	$xml = '<KuveytTurkVPosMessage xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">'
		.'<APIVersion>TDV2.0.0</APIVersion>'
		.'<OkUrl>'.$OkUrl.'</OkUrl>'
		.'<FailUrl>'.$FailUrl.'</FailUrl>'
		.'<HashData>'.$HashData.'</HashData>'
		.'<MerchantId>'.$MerchantId.'</MerchantId>'
		.'<CustomerId>'.$CustomerId.'</CustomerId>'
		.'<UserName>'.$UserName.'</UserName>'
		.'<CardNumber>'.$CardNumber.'</CardNumber>'
		.'<CardExpireDateYear>'.$CardExpireDateYear.'</CardExpireDateYear>'
		.'<CardExpireDateMonth>'.$CardExpireDateMonth.'</CardExpireDateMonth>'
		.'<CardCVV2>'.$CardCVV2.'</CardCVV2>'
		.'<CardHolderName>'.$Name.'</CardHolderName>'
		.'<CardType>MasterCard</CardType>'
		.'<BatchID>0</BatchID>'
		.'<TransactionType>Sale</TransactionType>'
		.'<InstallmentCount>'.$odemetaksit.'</InstallmentCount>'
		.'<Amount>'.$Amount.'</Amount>'
		.'<DisplayAmount>'.$Amount.'</DisplayAmount>'
		.'<CurrencyCode>0949</CurrencyCode>'
		.'<MerchantOrderId>'.$MerchantOrderId.'</MerchantOrderId>'
		.'<TransactionSecurity>3</TransactionSecurity>'
		.'<DeviceData>'
		.'<DeviceChannel>02</DeviceChannel>'
		.'<ClientIP>'.$_SERVER['REMOTE_ADDR'].'</ClientIP>'
		.'</DeviceData>'
		.'</KuveytTurkVPosMessage>';

	try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		//curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_MAX_TLSv1_2);
		//curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_0 | CURL_SSLVERSION_TLSv1_1 | CURL_SSLVERSION_TLSv1_2); // php 5.5.19+
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml', 'Content-length: '. strlen($xml)) );
		curl_setopt($ch, CURLOPT_POST, true); //POST Metodu kullanarak verileri g�nder
		curl_setopt($ch, CURLOPT_HEADER, false); //Serverdan gelen Header bilgilerini �nemseme.
		curl_setopt($ch, CURLOPT_URL,'https://sanalpos.kuveytturk.com.tr/ServiceGateWay/Home/ThreeDModelPayGate'); //Baglanacagi URL
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Transfer sonu�larini al.
		$data = curl_exec($ch);
		curl_close($ch);
	}
	catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	$filename = 'kuveyt_turk_request.xml';
	file_put_contents($filename, $xml);
	echo($data);
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

}
else {
    $odemetaksit = $_POST['odemetaksit'];
    if ($odemetaksit == 1 || $odemetaksit == 0) {
        $odemetaksit = 0;
    }
    $odemetutar = $_POST['odemetutar'];
    $odemetutar_float = str_replace(',', '', $odemetutar);
	$verimiz = [
		"yanSepetToplami" => $_POST["araToplam"],
		"yanSepetKdv" => $_POST["kdv"],
		"yanIndirim" => $_POST["indirim"],
		"yanKargo" => $_POST["kargo"],
		"deliveryOption" => $_POST["deliveryOption"],
		"yantoplam" => $_POST["toplam"],
		"desi"      => $_POST["desi"],
		"banka_id" => $_POST["banka_id"],
		"uye_id" => $_POST["uye_id"],
		"tip" => $_POST["tip"],
		"lang" => $_POST["lang"],
		"promosyon_kodu" => $_POST['promosyonKodu']
	];

	$pos_id = 3;
	$basarili = 0;
	$sonucStr = 'Sipariş ödeme sayfasına giriş yapıldı!';
	$stmt = $db->prepare("INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)");
	$stmt->execute(array(':uye_id' => $_POST["uye_id"], ':pos_id' => $pos_id, ':islem' => $sonucStr, ':tutar' => $_POST["toplam"], ':basarili' => $basarili));

	$verimizB64 = base64_encode(json_encode($verimiz));
	$gidesun = "https://denemeb2b.noktaelektronik.net/functions/siparis/kuveyt_sip_olustur.php?veri=" .$verimizB64;
		$Name=$_POST["cardName"];
		$CardNumber=$_POST["cardNumber"]; // 16 haneli olarak
		$CardExpireDateMonth=$_POST["expMonth"]; // iki hane olarak kartın ay bilgisi
		$CardExpireDateYear=$_POST["expYear"]; // kartın vade tarihinde yıl alanı, iki hane
		$CardCVV2=$_POST["cvCode"]; // kartın arka yüzündeki CVV2 kodu
        $MerchantOrderId = uniqid();// İşyerinin belirlediği sipariş numarası
		$Amount = $odemetutar_float; //Islem Tutari // Ornegin 1.00 TL için 100 kati yani 100 yazilmali
        $CustomerId = "93981545";// Bankadaki müsteri numarası
        $MerchantId = "61899"; //Sanal pos mağaza numarası, başvuru onayıyla işyerine gönderilir. 
        $OkUrl = $gidesun; //Basarili sonuç alinirsa, yönledirelecek sayfa
		$FailUrl = "https://denemeb2b.noktaelektronik.net/tr/sepet";//Basarisiz sonuç alinirsa, yönledirelecek sayfa
        $UserName="kadirbabur"; // https://kurumsal.kuveytturk.com.tr adresinde Kullanıcı İşlemleri - Kullanıcı Ekle alanında işyeri tarafından olusturulan api rolünde kullanici adı
		$Password="Dell28736.!";// api rolünde kullanici adının sifresi
		$HashedPassword = base64_encode(sha1($Password,"ISO-8859-9")); //md5($Password);	
        $HashData = base64_encode(sha1($MerchantId.$MerchantOrderId.$Amount.$OkUrl.$FailUrl.$UserName.$HashedPassword , "ISO-8859-9"));
		$xml= '<KuveytTurkVPosMessage xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">'
			.'<APIVersion>TDV2.0.0</APIVersion>'
			.'<OkUrl>'.$OkUrl.'</OkUrl>'
			.'<FailUrl>'.$FailUrl.'</FailUrl>'
			.'<HashData>'.$HashData.'</HashData>'
			.'<MerchantId>'.$MerchantId.'</MerchantId>'
			.'<CustomerId>'.$CustomerId.'</CustomerId>'
			.'<UserName>'.$UserName.'</UserName>'
			.'<CardNumber>'.$CardNumber.'</CardNumber>'
			.'<CardExpireDateYear>'.$CardExpireDateYear.'</CardExpireDateYear>'
			.'<CardExpireDateMonth>'.$CardExpireDateMonth.'</CardExpireDateMonth>'
			.'<CardCVV2>'.$CardCVV2.'</CardCVV2>'
			.'<CardHolderName>'.$Name.'</CardHolderName>'
			.'<CardType>MasterCard</CardType>'
			.'<BatchID>0</BatchID>'
			.'<TransactionType>Sale</TransactionType>'
			.'<InstallmentCount>'.$odemetaksit.'</InstallmentCount>'
			.'<Amount>'.$Amount.'</Amount>'
			.'<DisplayAmount>'.$Amount.'</DisplayAmount>'
			.'<CurrencyCode>0949</CurrencyCode>'
			.'<MerchantOrderId>'.$MerchantOrderId.'</MerchantOrderId>'
			.'<TransactionSecurity>3</TransactionSecurity>'
			.'<DeviceData>'
			.'<DeviceChannel>02</DeviceChannel>'
			.'<ClientIP>'.$_SERVER['REMOTE_ADDR'].'</ClientIP>'
			.'</DeviceData>'
			.'</KuveytTurkVPosMessage>';
			
	 try {
			$ch = curl_init();  
			curl_setopt($ch, CURLOPT_SSLVERSION, 6);
			//curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_MAX_TLSv1_2); 
			//curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_0 | CURL_SSLVERSION_TLSv1_1 | CURL_SSLVERSION_TLSv1_2); // php 5.5.19+
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/xml', 'Content-length: '. strlen($xml)) );
			curl_setopt($ch, CURLOPT_POST, true); //POST Metodu kullanarak verileri g�nder  
			curl_setopt($ch, CURLOPT_HEADER, false); //Serverdan gelen Header bilgilerini �nemseme.  
			curl_setopt($ch, CURLOPT_URL,'https://sanalpos.kuveytturk.com.tr/ServiceGateWay/Home/ThreeDModelPayGate'); //Baglanacagi URL  
			curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
	
		 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Transfer sonu�larini al.
			$data = curl_exec($ch);  
			curl_close($ch);
		 }
		 catch (Exception $e) {
		 echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		 echo($data);
		 error_reporting(E_ALL); 
		 ini_set("display_errors", 1);
}
?>

