<?php
session_start();
session_regenerate_id(true);
if(isset($_POST["cariOdeme"])) {
    $odemetaksit = $_POST['odemetaksit'];
    if ($odemetaksit == 1 || $odemetaksit == 0) {
        $odemetaksit = 0;
    }
    $odemetutar = $_POST['odemetutar'];
    $odemetutar_float = str_replace('.', '', $odemetutar);

    $verimiz = [
        "yantoplam" => $_POST["toplam"],
        "banka_id" => $_POST["banka_id"],
        "hesap"   => $_POST["hesap"],
        "taksit" => $_POST["taksit_sayisi"],
        "uye_id" => $_POST["uye_id"],
        "lang" => $_POST["lang"],
    ];
    $verimizB64 = base64_encode(json_encode($verimiz));
    // Pos tanımları, sipariş bilgileri ve ödeme bilgileri burada tanımlanıyor
    $params = array(
        // Pos tanımları (Pos panelinde tanımlanıp buraya girilecek)
        'mode' => "PROD", // Pos modu, test için: "TEST", production için: "PROD"
        'merchantID' => "7000679", // Merchant numarası
        'terminalID' => "10236773", // Terminal numarası
        'provUserID' => "PROVAUT", // Provision kullanıcı adı
        'provUserPassword' => "3A*jXW42**xn", // Provision kullanıcı parolası
        'garantiPayProvUserID' => "PROVOOS", // GarantiPay için provision kullanıcı adı
        'garantiPayProvUserPassword' => "XXXXX", // GarantiPay için provision kullanıcı parolası
        'storeKey' => "414b494e534f4654343434343038304b4f4e594134323432", // 24byte hex 3D secure anahtarı
        'successUrl' => "https://www.noktaelektronik.com.tr/php/sip_olustur?cariveri=" .$verimizB64, // Başarılı ödeme sonrası dönülecek adres
        'errorUrl' => "https://www.noktaelektronik.com.tr/cariodeme?lang=tr", // Hatalı ödeme sonrası dönülecek adres
        'companyName' => "NOKTA ELEKTRONIK", // Firma adı
        'paymentType' => "creditcard", // Ödeme tipi - kredi kartı için: "creditcard", GarantiPay için: "garantipay"

        // Müşteri tanımları
        'orderNo' => uniqid(), // Sipariş numarası
        'amount' => $odemetutar_float, // Çekilecek tutar (ondalıklı olarak değil tam sayı olarak gönderilmeli, örn. 1.20tl için 120 gönderilmeli)
        'installmentCount' => $odemetaksit, // Tek çekim olacaksa boş bırakılmalıdır
        'currencyCode' => 949, // Döviz cinsi kodu(varsayılan:949): TRY=949, USD=840, EUR=978, GBP=826, JPY=392
        'customerIP' => $_SERVER['REMOTE_ADDR'], // Müşteri IP adresi
        'customerEmail' => "", // Müşteri e-mail adresi

        // Kart bilgisi tanımları (GarantiPay ile ödemede bu alanların doldurulması zorunlu değildir)
        'cardName' => $_POST['cardName'], // Kart üzerindeki ad soyad
        'cardNumber' => $_POST['cardNumber'], // Kart numarası (16 haneli boşluksuz)
        'cardExpiredMonth' => $_POST['expMonth'], // Kart geçerlilik tarihi ay
        'cardExpiredYear' => $_POST['expYear'], // Kart geçerlilik tarihi yıl (yılın son 2 hanesi)
        'cardCvv' => $_POST['cvCode'], // Kartın arka yüzündeki son 3 numara(CVV kodu)
    );


    // GarantiPos sınıfı tanımlanıyor
    require_once("GarantiPos.php");
    $garantipos = new GarantiPos();
    $garantipos->debugMode = false;
    $params['paymentType'] = isset($_POST['paymenttype']) ? $_POST['paymenttype'] : $params['paymentType'];
    $garantipos->setParams($params);

    $action = isset($_GET['action']) ? $_GET['action'] : false;
    if ($action) {
        $result = $garantipos->callback($action);
        if ($result['success'] == 'success') {

        }
        var_dump($result);
    } else {
        $garantipos->debugUrlUse = false; // Parametre değerlerinin check edildiği adrese gönderilmesi

        $garantipos->pay(); // 3D doğrulama için bankaya yönlendiriliyor
    }
}else{
    $odemetaksit = $_POST['odemetaksit'];
    if ($odemetaksit == 1 || $odemetaksit == 0) {
        $odemetaksit = 0;
    }
    $odemetutar = $_POST['odemetutar'];
    $odemetutar_float = str_replace(',', '', $odemetutar);

    if ($_POST) {
        $verimiz = [
            "yanSepetToplami" => $_POST["araToplam"],
            "yanSepetKdv" => $_POST["kdv"],
            "yanIndirim" => $_POST["indirim"],
            "yanKargo" => $_POST["kargo"],
            "yantoplam" => $_POST["toplam"],
            "banka_id" => $_POST["banka_id"],
            "uye_id" => $_POST["uye_id"],
            "tip" => $_POST["tip"],
            "lang" => $_POST["lang"],
            "promosyon_kodu" => $_POST['promosyon_kodu']
        ];
        $verimizB64 = base64_encode(json_encode($verimiz));

        // Pos tanımları, sipariş bilgileri ve ödeme bilgileri burada tanımlanıyor
        $params = array(
            // Pos tanımları (Pos panelinde tanımlanıp buraya girilecek)
            'mode' => "PROD", // Pos modu, test için: "TEST", production için: "PROD"
            'merchantID' => "7000679", // Merchant numarası
            'terminalID' => "10236773", // Terminal numarası
            'provUserID' => "PROVAUT", // Provision kullanıcı adı
            'provUserPassword' => "3A*jXW42**xn", // Provision kullanıcı parolası
            'garantiPayProvUserID' => "PROVOOS", // GarantiPay için provision kullanıcı adı
            'garantiPayProvUserPassword' => "XXXXX", // GarantiPay için provision kullanıcı parolası
            'storeKey' => "414b494e534f4654343434343038304b4f4e594134323432", // 24byte hex 3D secure anahtarı
            'successUrl' => "https://www.noktaelektronik.com.tr/php/sip_olustur?veri=" . $verimizB64, // Başarılı ödeme sonrası dönülecek adres
            'errorUrl' => "https://www.noktaelektronik.com.tr/sepet?lang=tr", // Hatalı ödeme sonrası dönülecek adres
            'companyName' => "NOKTA ELEKTRONIK", // Firma adı
            'paymentType' => "creditcard", // Ödeme tipi - kredi kartı için: "creditcard", GarantiPay için: "garantipay"

            // Müşteri tanımları
            'orderNo' => uniqid(), // Sipariş numarası
            'amount' => $odemetutar_float, // Çekilecek tutar (ondalıklı olarak değil tam sayı olarak gönderilmeli, örn. 1.20tl için 120 gönderilmeli)
            'installmentCount' => $odemetaksit, // Tek çekim olacaksa boş bırakılmalıdır
            'currencyCode' => 949, // Döviz cinsi kodu(varsayılan:949): TRY=949, USD=840, EUR=978, GBP=826, JPY=392
            'customerIP' => $_SERVER['REMOTE_ADDR'], // Müşteri IP adresi
            'customerEmail' => "", // Müşteri e-mail adresi

            // Kart bilgisi tanımları (GarantiPay ile ödemede bu alanların doldurulması zorunlu değildir)
            'cardName' => $_POST['cardName'], // Kart üzerindeki ad soyad
            'cardNumber' => $_POST['cardNumber'], // Kart numarası (16 haneli boşluksuz)
            'cardExpiredMonth' => $_POST['expMonth'], // Kart geçerlilik tarihi ay
            'cardExpiredYear' => $_POST['expYear'], // Kart geçerlilik tarihi yıl (yılın son 2 hanesi)
            'cardCvv' => $_POST['cvCode'], // Kartın arka yüzündeki son 3 numara(CVV kodu)
        );


        // GarantiPos sınıfı tanımlanıyor
        require_once("GarantiPos.php");
        $garantipos = new GarantiPos();
        $garantipos->debugMode = false;
        $params['paymentType'] = isset($_POST['paymenttype']) ? $_POST['paymenttype'] : $params['paymentType'];
        $garantipos->setParams($params);

        $action = isset($_GET['action']) ? $_GET['action'] : false;
        if ($action) {
            $result = $garantipos->callback($action);
            if ($result['success'] == 'success') {
            }

            var_dump($result);
        } else {
            $garantipos->debugUrlUse = false; // Parametre değerlerinin check edildiği adrese gönderilmesi

            $garantipos->pay(); // 3D doğrulama için bankaya yönlendiriliyor
        }
    }
}
?>