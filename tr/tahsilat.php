<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'vadesi_gecmis';
$template = new Template('Nokta B2B - Vadesi Geçmiş Borç Ödeme', $currentPage);

$template->head();
$database = new Database();

// Base64 kontrolü ve veri çözümleme
if (!isset($_GET['l']) || empty($_GET['l'])) {
    header('Location: /');
    exit;
}

$decoded_data = base64_decode($_GET['l']);
if ($decoded_data === false) {
    header('Location: /');
    exit;
}

$borc = json_decode($decoded_data, true);
if (!$borc || !isset($borc['cari_kodu'])) {
    header('Location: /');
    exit;
}

$veri = [
    'cari_kodu'      => $borc['cari_kodu'],
    'ticari_unvani'  => $borc['ticari_unvani'],
    'geciken_tutar'  => $borc['geciken_tutar'],
    'borc_bakiye'    => $borc['borc_bakiye'],
    'bilgi_kodu'     => $borc['bilgi_kodu']
];

$uye_id = $database->fetch("SELECT id FROM uyeler WHERE muhasebe_kodu = :cari_kodu", ['cari_kodu' => $borc['cari_kodu']]);
$uye_ids = $uye_id['id'];
$duzenlifiyat = number_format($veri['geciken_tutar'], 2, ',', '.');
$toplam = str_replace(['.', ','], ['', '.'], $duzenlifiyat); // PHP tarafında sayısal değere çevirme
?>
<body>
    <?php $template->header(); ?>
    <!-- Site Haritası -->
    <nav aria-label="breadcrumb" class="container mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;"><symbol id="house-door-fill" viewBox="0 0 16 16">
            <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/></symbol>
        </svg>
        <ol class="breadcrumb ">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="index">
                <svg class="bi" width="15" height="15"><use xlink:href="#house-door-fill"></use></svg>
                <span class="visually-hidden">Anasayfa</span>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Vadesi Geçmiş Borç Ödeme</li>
        </ol>
    </nav>
    <section class="container mb-3">
        <div class="row">
            <?php $template->pageLeftMenu(); ?>
            <div class="float-end col-xs-12 col-sm-8 col-md-9 rounded-3">
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <h4 class="alert-heading">Sayın <?php echo htmlspecialchars($veri['ticari_unvani']); ?></h4>
                            <p class="mb-0">Toplam Borcunuz: <strong><?php echo number_format($veri['borc_bakiye'], 2, ',', '.'); ?> TL</strong></p>
                            <p class="mb-0">Vadesi Geçmiş Borcunuz: <strong><?php echo $duzenlifiyat; ?> TL</strong></p>
                        </div>

                        <form id="paymentForm" method="POST" action="../functions/bank/kuveyt/2_OdemeTahsilat.php">
                            <input type="hidden" name="cari_kodu" value="<?php echo htmlspecialchars($veri['cari_kodu']); ?>">
                            <input type="hidden" name="bilgi_kodu" value="<?php echo htmlspecialchars($veri['bilgi_kodu']); ?>">
                            <input type="hidden" name="cariOdeme" value="cariOdeme">
                            <input type="hidden" name="odemetaksit" value="1">
                            <input type="hidden" name="odemetutar" value="<?php echo $toplam; ?>">
                            <input type="hidden" name="uye_id" value="<?php echo $uye_ids; ?>">
                            <input type="hidden" name="toplam" value="<?php echo $toplam; ?>">
                            <input type="hidden" name="banka_id" value="8">
                            <input type="hidden" name="hesap" value="TL">
                            <input type="hidden" name="tip" value="Sanal Pos">
                            <input type="hidden" name="lang" value="tr">
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="cardNumber" class="form-label">Kart Numarası</label>
                                    <input type="text" class="form-control" id="cardNumber" name="cardNumber" maxlength="19" placeholder="1234 5678 9012 3456" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cardName" class="form-label">Kart Sahibi</label>
                                    <input type="text" class="form-control" id="cardName" name="cardName" placeholder="Ad Soyad" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="expMonth" class="form-label">Son Kullanma Ay</label>
                                    <select class="form-select" id="expMonth" name="expMonth" required>
                                        <?php for($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="expYear" class="form-label">Son Kullanma Yıl</label>
                                    <select class="form-select" id="expYear" name="expYear" required>
                                        <?php 
                                        $currentYear = date('Y');
                                        for($i = $currentYear; $i <= $currentYear + 10; $i++): 
                                        ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cvCode" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvCode" name="cvCode" maxlength="3" placeholder="123" required>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Ödemeyi Tamamla</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> 
        </div> 
    </section>
    <?php $template->footer(); ?>
</body>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/app.js"></script>

<script>
// Kart numarası formatlaması
document.getElementById('cardNumber').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    let formattedValue = '';
    for(let i = 0; i < value.length; i++) {
        if(i > 0 && i % 4 === 0) {
            formattedValue += ' ';
        }
        formattedValue += value[i];
    }
    e.target.value = formattedValue;
});

// CVV sadece rakam
document.getElementById('cvCode').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});
</script>

