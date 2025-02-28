<?php
// Hataları görüntülemeyi devre dışı bırak
error_reporting(0);
// Hata raporlamayı yapılandırma
ini_set('display_errors', 0);
ini_set('log_errors', 0);
ob_start();
session_start();
include('../../../baglanti.php');
@include('validation.php');
@include('env.php');
@include "Auth.php";
@include "TotalPaymentTransaction.php";
@include "GeneralClass.php";
ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

global $env;
if(isset($_POST["cariOdeme"])){
    global $db;
    function isSucceced($value)
    {
        if ($value->TP_Islem_OdemeResult->Sonuc > 0) {
            echo "<script>window.top.location='".$value->TP_Islem_OdemeResult->UCD_URL."'</script>";
        } else {
            $errorUrl = "https://www.noktaelektronik.com.tr/cariodeme?lang=tr&s=31";
            header("Location: $errorUrl");
            exit();
        }
    }
    if ($_POST) {
        $bin = substr($_POST['cardNumber'], 0, 6); // 'cardNumber'ın ilk 6 hanesini al
        $url = 'https://www.noktaelektronik.com.tr/php/bank/param/binSorgula1.php?bin=' . $bin; // 'binsorgula.php' adresine GET isteği yap
        $result = file_get_contents($url); // İstek sonucunu al
        $gelposid = $result;
        $odemetutar = $_POST['odemetutar'];

        $pos_id = 1;
        $basarili = 0;
        $sonucStr = 'Cari ödeme sayfasına giriş yapıldı!';
        $stmt = $db->prepare("INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)");
        $stmt->execute(array(':uye_id' => $_POST["uye_id"], ':pos_id' => $pos_id, ':islem' => $sonucStr, ':tutar' => $_POST["toplam"], ':basarili' => $basarili));


        $verimiz = [
            "cardHolder" => $_POST['cardName'],
            "cardNo" => $_POST['cardNumber'],
            "yantoplam" => $_POST["toplam"],
            "banka_id" => $_POST["banka_id"],
            "hesap" => $_POST["hesap"],
            "taksit" => $_POST["taksit_sayisi"],
            "uye_id" => $_POST["uye_id"],
            "lang" => $_POST["lang"],
        ];
        $verimizB64 = base64_encode(json_encode($verimiz));

        session_regenerate_id(true);
        $ipAdr = $_SERVER['REMOTE_ADDR'];
        $client = new SoapClient($env['URL']);
        $transactionsValueList = [
            "cardType" => $gelposid,
            "spid" => $gelposid,
            "guid" => $env['GUID'],
            "cardHolderName" => $_POST['cardName'],
            "cardNo" => $_POST['cardNumber'],
            "monthOfExpireDate" => $_POST['expMonth'],
            "yearOfExpireDate" => "20" . $_POST['expYear'],
            "creditCardCvc" => $_POST['cvCode'],
            "creditCardOwnerName" => "5372403939",
            "errorUrl" => "https://www.noktaelektronik.com.tr/tr/cariodeme",
            "succesUrl" => "https://www.noktaelektronik.com.tr/php/sip_olustur?cariveri=" . $verimizB64,
            "orderID" => rand(0, 999999),
            "paymentUrl" => "http://localhost/param/index.php",
            "orderExplanation" => date("d-m-Y H:i:s") . " tarihindeki ödeme",
            "installment" => $_POST['odemetaksit'],
            "transactionPayment" => $odemetutar,
            "totalPayment" => $odemetutar,
            "transactionID" => "",
            "ipAdr" => $_SERVER['REMOTE_ADDR']
        ];

        $data = new TotalPaymentTransaction(
            $transactionsValueList["cardType"],
            "",
            $transactionsValueList["guid"],
            $transactionsValueList["cardHolderName"],
            $transactionsValueList["cardNo"],
            $transactionsValueList["monthOfExpireDate"],
            $transactionsValueList["yearOfExpireDate"],
            $transactionsValueList["creditCardCvc"],
            $transactionsValueList["creditCardOwnerName"],
            $transactionsValueList["errorUrl"],
            $transactionsValueList["succesUrl"],
            $transactionsValueList["orderID"],
            $transactionsValueList["orderExplanation"],
            $transactionsValueList["installment"],
            $transactionsValueList["transactionPayment"],
            $transactionsValueList["totalPayment"],
            $transactionsValueList["transactionID"],
            $transactionsValueList["ipAdr"],
            $transactionsValueList["paymentUrl"]
        );

        $authObject = new Auth($transactionSecurityStr = $env['CLIENT_CODE'] .
            $transactionsValueList["guid"] .
            $transactionsValueList["spid"] .
            $transactionsValueList["installment"] .
            $transactionsValueList["transactionPayment"] .
            $transactionsValueList["totalPayment"] .
            $transactionsValueList["orderID"] .
            $transactionsValueList["errorUrl"] .
            $transactionsValueList["succesUrl"]);

        $data->Islem_Hash = $client->SHA2B64($authObject)->SHA2B64Result;
        //print_r($data);
        $response = $client->TP_Islem_Odeme($data);
        print_r($response);
        isSucceced($response);
    }
}
elseif(isset($_POST["adminCariOdeme"])) {
    function isSucceced($value)
    {
        if ($value->TP_Islem_OdemeResult->Sonuc > 0) {
            echo "<script>window.top.location='".$value->TP_Islem_OdemeResult->UCD_URL."'</script>";
        } else {
            $errorUrl = "https://www.noktaelektronik.com.tr/admin/muhasebe/adminManuelOdeme.php";
            header("Location: $errorUrl");
            exit();
        }
    }
    if ($_POST) {
        $bin = substr($_POST['cardNumber'], 0, 6); // 'cardNumber'ın ilk 6 hanesini al
        $url = 'https://www.noktaelektronik.com.tr/php/bank/param/binSorgula1.php?bin=' . $bin; // 'binsorgula.php' adresine GET isteği yap
        $result = file_get_contents($url); // İstek sonucunu al
        $gelposid = $result;
        $odemetutar = $_POST['odemetutar'];


        $verimiz = [
            "cardHolder" => $_POST['cardName'],
            "cardNo" => $_POST['cardNumber'],
            "yantoplam" => $_POST["toplam"],
            "banka_id" => $_POST["banka_id"],
            "hesap" => $_POST["hesap"],
            "taksit" => $_POST["taksit_sayisi"],
            "uye_id" => $_POST["uye_id"],
            "lang" => $_POST["lang"],
        ];
        $verimizB64 = base64_encode(json_encode($verimiz));

        session_regenerate_id(true);
        $ipAdr = $_SERVER['REMOTE_ADDR'];
        $client = new SoapClient($env['URL']);
        $transactionsValueList = [
            "cardType" => $gelposid,
            "spid" => $gelposid,
            "guid" => $env['GUID'],
            "cardHolderName" => $_POST['cardName'],
            "cardNo" => $_POST['cardNumber'],
            "monthOfExpireDate" => $_POST['expMonth'],
            "yearOfExpireDate" => "20" . $_POST['expYear'],
            "creditCardCvc" => $_POST['cvCode'],
            "creditCardOwnerName" => "5372403939",
            "errorUrl" => "https://www.noktaelektronik.com.tr/admin/muhasebe/adminManuelOdeme.php",
            "succesUrl" => "https://www.noktaelektronik.com.tr/admin/muhasebe/manualodeme?cariveri=" . $verimizB64,
            "orderID" => rand(0, 999999),
            "paymentUrl" => "http://localhost/param/index.php",
            "orderExplanation" => date("d-m-Y H:i:s") . " tarihindeki ödeme",
            "installment" => $_POST['odemetaksit'],
            "transactionPayment" => $odemetutar,
            "totalPayment" => $odemetutar,
            "transactionID" => "",
            "ipAdr" => $_SERVER['REMOTE_ADDR']
        ];

        $data = new TotalPaymentTransaction(
            $transactionsValueList["cardType"],
            "",
            $transactionsValueList["guid"],
            $transactionsValueList["cardHolderName"],
            $transactionsValueList["cardNo"],
            $transactionsValueList["monthOfExpireDate"],
            $transactionsValueList["yearOfExpireDate"],
            $transactionsValueList["creditCardCvc"],
            $transactionsValueList["creditCardOwnerName"],
            $transactionsValueList["errorUrl"],
            $transactionsValueList["succesUrl"],
            $transactionsValueList["orderID"],
            $transactionsValueList["orderExplanation"],
            $transactionsValueList["installment"],
            $transactionsValueList["transactionPayment"],
            $transactionsValueList["totalPayment"],
            $transactionsValueList["transactionID"],
            $transactionsValueList["ipAdr"],
            $transactionsValueList["paymentUrl"]
        );

        $authObject = new Auth($transactionSecurityStr = $env['CLIENT_CODE'] .
            $transactionsValueList["guid"] .
            $transactionsValueList["spid"] .
            $transactionsValueList["installment"] .
            $transactionsValueList["transactionPayment"] .
            $transactionsValueList["totalPayment"] .
            $transactionsValueList["orderID"] .
            $transactionsValueList["errorUrl"] .
            $transactionsValueList["succesUrl"]);

        $data->Islem_Hash = $client->SHA2B64($authObject)->SHA2B64Result;
        //print_r($data);
        $response = $client->TP_Islem_Odeme($data);
        print_r($response);
        isSucceced($response);
    }
}
else {
    function isSucceced($value)
    {
        if ($value->TP_Islem_OdemeResult->Sonuc > 0) {
            echo "<script>window.top.location='".$value->TP_Islem_OdemeResult->UCD_URL."'</script>";
        } else {
            $errorUrl = "https://www.noktaelektronik.com.tr/sepet?lang=tr&s=31";
            header("Location: $errorUrl");
            exit();
        }
    }
    if ($_POST) {
        $odemetutar = $_POST['odemetutar'];

        $verimiz = [
            "yanSepetToplami"   => $_POST["araToplam"],
            "yanSepetKdv"       => $_POST["kdv"],
            "yanIndirim"        => $_POST["indirim"],
            "yanKargo"          => $_POST["kargo"],
            "deliveryOption"    => $_POST["deliveryOption"],
            "yantoplam"         => $_POST["toplam"],
            "desi"              => $_POST["desi"],
            "banka_id"          => $_POST["banka_id"],
            "uye_id"            => $_POST["uye_id"],
            "tip"               => $_POST["tip"],
            "lang"              => $_POST["lang"],
            "promosyon_kodu" => $_POST['promosyonKodu']
        ];

        $pos_id = 1;
        $basarili = 0;
        $sonucStr = 'Sipariş ödeme sayfasına giriş yapıldı!';
        $stmt = $db->prepare("INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)");
        $stmt->execute(array(':uye_id' => $_POST["uye_id"], ':pos_id' => $pos_id, ':islem' => $sonucStr, ':tutar' => $_POST["toplam"], ':basarili' => $basarili));

        $verimizB64 = base64_encode(json_encode($verimiz));

        $bin = substr($_POST['cardNumber'], 0, 6); // 'cardNumber'ın ilk 6 hanesini al
        $url = 'https://www.noktaelektronik.com.tr/php/bank/param/binSorgula1.php?bin=' . $bin; // 'binsorgula.php' adresine GET isteği yap
        $result = file_get_contents($url); // İstek sonucunu al
        $gelposid = $result;

        $ipAdr = $_SERVER['REMOTE_ADDR'];
        $client = new SoapClient($env['URL']);
        $transactionsValueList = [
            "cardType" => $gelposid,
            "spid" => $gelposid,
            "guid" => $env['GUID'],
            "cardHolderName" => $_POST['cardName'],
            "cardNo" => $_POST['cardNumber'],
            "monthOfExpireDate" => $_POST['expMonth'],
            "yearOfExpireDate" => "20" . $_POST['expYear'],
            "creditCardCvc" => $_POST['cvCode'],
            "creditCardOwnerName" => "5372403939",
            "errorUrl" => "https://www.noktaelektronik.com.tr/cariodeme?lang=tr",
            "succesUrl" => "https://www.noktaelektronik.com.tr/php/sip_olustur?veri=" .$verimizB64,
            "orderID" => rand(0,999999),
            "paymentUrl" => "http://localhost/param/index.php",
            "orderExplanation" => date("d-m-Y H:i:s") . " tarihindeki ödeme",
            "installment" => $_POST['odemetaksit'],
            "transactionPayment" => $odemetutar,
            "totalPayment" => $odemetutar,
            "transactionID" => "",
            "ipAdr" => $_SERVER['REMOTE_ADDR']
        ];
        $data = new TotalPaymentTransaction(
            $transactionsValueList["cardType"],
            "",
            $transactionsValueList["guid"],
            $transactionsValueList["cardHolderName"],
            $transactionsValueList["cardNo"],
            $transactionsValueList["monthOfExpireDate"],
            $transactionsValueList["yearOfExpireDate"],
            $transactionsValueList["creditCardCvc"],
            $transactionsValueList["creditCardOwnerName"],
            $transactionsValueList["errorUrl"],
            $transactionsValueList["succesUrl"],
            $transactionsValueList["orderID"],
            $transactionsValueList["orderExplanation"],
            $transactionsValueList["installment"],
            $transactionsValueList["transactionPayment"],
            $transactionsValueList["totalPayment"],
            $transactionsValueList["transactionID"],
            $transactionsValueList["ipAdr"],
            $transactionsValueList["paymentUrl"]
        );
        $authObject = new Auth($transactionSecurityStr = $env['CLIENT_CODE'].
            $transactionsValueList["guid"].
            $transactionsValueList["spid"].
            $transactionsValueList["installment"].
            $transactionsValueList["transactionPayment"].
            $transactionsValueList["totalPayment"].
            $transactionsValueList["orderID"].
            $transactionsValueList["errorUrl"].
            $transactionsValueList["succesUrl"]);

        $data->Islem_Hash = $client->SHA2B64($authObject)->SHA2B64Result;
        //print_r($data);
        $response = $client->TP_Islem_Odeme($data);
        //print_r($response);
        isSucceced($response);
    }
}
echo "Ödemeye yönlendiriliyorsunuz...";
?>