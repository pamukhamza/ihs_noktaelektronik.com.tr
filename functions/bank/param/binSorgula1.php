<?php
$gelen = $_GET['bin'];
$binXml = '<?xml version="1.0" encoding="utf-8"?> <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"> <soap:Body>
<BIN_SanalPos xmlns="https://turkpos.com.tr/">
<G>
<CLIENT_CODE>10738</CLIENT_CODE>
<CLIENT_USERNAME>Test</CLIENT_USERNAME>
<CLIENT_PASSWORD>Test</CLIENT_PASSWORD>
</G>
<BIN>'. $gelen .'</BIN>
</BIN_SanalPos>
</soap:Body>
</soap:Envelope>';

$posURL = 'https://test-dmz.param.com.tr/turkpos.ws/service_turkpos_test.asmx';

$ch = curl_init($posURL);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/xml',
        'SOAPAction: "https://turkpos.com.tr/BIN_SanalPos"')
);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $binXml);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
$responseXml = new DOMDocument();
$responseXml->loadXML($response);

$xpath = new DOMXPath($responseXml);
$xpath->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
$xpath->registerNamespace('', 'https://turkpos.com.tr/');


$SanalPOS_ID = $xpath->evaluate('string(//SanalPOS_ID)');
echo "$SanalPOS_ID";


curl_close($ch);

?>
