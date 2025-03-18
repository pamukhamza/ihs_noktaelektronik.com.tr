<?php
session_start();
session_regenerate_id(true);
include('../../db.php');
if(isset($_POST["cariOdeme"])){
    $verimiz = [
        "cardHolder" => $_POST['cardName'],
        "cardNo" => $_POST['cardNumber'],
        "yantoplam" => $_POST["toplam"],
        "banka_id" => $_POST["banka_id"],
        "hesap" => $_POST["hesap"],
        "taksit" => $_POST["odemetaksit"],
        "uye_id" => $_POST["uye_id"],
        "lang" => $_POST["lang"]
    ];
    $verimizB64 = base64_encode(json_encode($verimiz));

    $odemetutar = $_POST['odemetutar'];
    $odemetutar = str_replace(',', '', $odemetutar);
    // Separate last two digits with a dot
    $odemetutar = substr_replace($odemetutar, '.', -2, 0);

    $orgClientId  =   "280624575";
    $orgAmount = $odemetutar;
    $orgOkUrl =  "https://denemeb2b.noktaelektronik.net/functions/siparis/sip_olustur.php?cariveriFinans=" . $verimizB64;
    $orgFailUrl = "https://denemeb2b.noktaelektronik.net/tr/cariodeme?lang=tr";
    $orgTransactionType = "Auth";
    $orgInstallment = $_POST['odemetaksit'];
    $orgRnd =  microtime();
    $orgCallbackUrl = "https://denemeb2b.noktaelektronik.net/functions/bank/turkiye_finans/callback.php";
    $orgCurrency = "949";
    ?>
    <form id="cariOdemeForm" method="post" action="https://denemeb2b.noktaelektronik.net/functions/bank/turkiye_finans/GenericVer3RequestHashHandler.php">
        <input type="hidden" name="Ecom_Payment_Card_ExpDate_Month" value="<?= $_POST['expMonth'] ;?>">
        <input type="hidden" name="Ecom_Payment_Card_ExpDate_Year" value="<?= $_POST['expYear'] ;?>">
        <input type="hidden" name="cv2" value="<?= $_POST['cvCode'] ;?>">
        <input type="hidden" name="pan" value="<?= $_POST['cardNumber'] ;?>">
        <input type="hidden" name="name" value="noktaadmin">
        <input type="hidden" name="password" value="HLAD95796637">
        <input type="hidden" name="clientid" value="<?= $orgClientId ;?>">
        <input type="hidden" name="amount" value="<?= $orgAmount ;?>">
        <input type="hidden" name="okurl" value="<?= $orgOkUrl ;?>">
        <input type="hidden" name="failUrl" value="<?= $orgFailUrl ;?>">
        <input type="hidden" name="TranType" value="<?= $orgTransactionType ;?>">
        <input type="hidden" name="Instalment" value="<?= $orgInstallment ;?>">
        <input type="hidden" name="callbackUrl" value="<?= $orgCallbackUrl ;?>">
        <input type="hidden" name="currency" value="<?= $orgCurrency ;?>">
        <input type="hidden" name="rnd" value="<?= $orgRnd ;?>">
        <input type="hidden" name="storetype" value="3d">
        <input type="hidden" name="hashAlgorithm" value="ver3">
        <input type="hidden" name="lang" value="tr">
    </form>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("cariOdemeForm").submit();
        });
    </script><?php 
} else {
    $db = new Database();
    $odemetaksit = $_POST['odemetaksit'];
    if ($odemetaksit == 1 || $odemetaksit == 0) {
        $odemetaksit = 0;
    }
    $verimiz = [
        "odemeTaksit" => $_POST['odemetaksit'],
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
        "promosyon_kodu" => $_POST['promosyon_kodu']
    ];

    $pos_id = 4;
    $basarili = 0;
    $sonucStr = 'Sipariş ödeme sayfasına giriş yapıldı!';
    $stmt = "INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)";
    $db->insert($stmt ,['uye_id' => $_POST["uye_id"], 'pos_id' => $pos_id, 'islem' => $sonucStr, 'tutar' => $_POST["toplam"], 'basarili' => $basarili]);

    $verimizB64 = base64_encode(json_encode($verimiz));

    $odemetutar = $_POST['odemetutar'];
    $odemetutar = str_replace(',', '', $odemetutar);
    // Separate last two digits with a dot
    $odemetutar = substr_replace($odemetutar, '.', -2, 0);

    $orgClientId  =   "280624575";
    $orgAmount = $odemetutar;
    $orgOkUrl =  "https://denemeb2b.noktaelektronik.net/functions/siparis/sip_olustur.php?sipFinans=" . $verimizB64;
    $orgFailUrl = "https://denemeb2b.noktaelektronik.net/tr/sepet";
    $orgTransactionType = "Auth";
    $orgInstallment = $_POST['odemetaksit'];
    $orgRnd =  microtime();
    $orgCallbackUrl = "https://denemeb2b.noktaelektronik.net/tr/sepet";
    $orgCurrency = "949";
?>
<form id="cariOdemeForm" method="post" action="https://denemeb2b.noktaelektronik.net/functions/bank/turkiye_finans/GenericVer3RequestHashHandler.php">
    <input type="hidden" name="Ecom_Payment_Card_ExpDate_Month" value="<?= $_POST['expMonth'] ;?>">
    <input type="hidden" name="Ecom_Payment_Card_ExpDate_Year" value="<?= $_POST['expYear'] ;?>">
    <input type="hidden" name="cv2" value="<?= $_POST['cvCode'] ;?>">
    <input type="hidden" name="pan" value="<?= $_POST['cardNumber'] ;?>">
    <input type="hidden" name="name" value="noktaadmin">
    <input type="hidden" name="password" value="HLAD95796637">
    <input type="hidden" name="clientid" value="<?= $orgClientId ;?>">
    <input type="hidden" name="amount" value="<?= $orgAmount ;?>">
    <input type="hidden" name="okurl" value="<?= $orgOkUrl ;?>">
    <input type="hidden" name="failUrl" value="<?= $orgFailUrl ;?>">
    <input type="hidden" name="TranType" value="<?= $orgTransactionType ;?>">
    <input type="hidden" name="Instalment" value="<?= $orgInstallment ;?>">
    <input type="hidden" name="callbackUrl" value="<?= $orgCallbackUrl ;?>">
    <input type="hidden" name="currency" value="<?= $orgCurrency ;?>">
    <input type="hidden" name="rnd" value="<?= $orgRnd ;?>">
    <input type="hidden" name="storetype" value="3d">
    <input type="hidden" name="hashAlgorithm" value="ver3">
    <input type="hidden" name="lang" value="tr">
</form>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("cariOdemeForm").submit();
    });
</script>
<?php } ?>