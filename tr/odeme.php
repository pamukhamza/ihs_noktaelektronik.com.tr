<?php

require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'odeme';
$template = new Template('Nokta Elektronik - Ödeme', $currentPage);

$template->head();
$database = new Database();

if(isset($_POST["devamEt"])) {
    $promosyon_kodu = isset($_POST['promosyonKodu']) ? $_POST['promosyonKodu'] : "";
    $ara_toplam = controlInput($_POST['yanSepetToplami']);
    $kdv = controlInput($_POST['yanSepetKdv']);
    $indirim = controlInput($_POST['yanIndirim']);
    $kargo = controlInput($_POST['yanKargo']);
    $toplam = controlInput($_POST['yanToplam']);
    $desi = controlInput($_POST['toplamDesiBirimi1']);
    $deliveryOption = controlInput($_POST['selectedDeliveryOption']);
}
?>
<body>
<?php $template->header(); ?>
        <div class="container flex-grow-1 container-p-y mt-5">
            <div id="wizard-checkout" class="bs-stepper wizard-icons wizard-icons-example mb-5">
                <?php if(isset($_POST["devamEt"])){ ?>
                <div class="bs-stepper-header m-auto border-0 py-4">
                    <div class="step " >
                        <button type="button" class="step-trigger" style="color: #3498db !important;">
                            <span class="bs-stepper-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </span> 
                            <span class="bs-stepper-label">Sepet</span>
                        </button>
                    </div>
                    <div class="line">
                        <i class="bx bx-chevron-right" style="color: #3498db !important;"></i>
                    </div>
                    
                    <div class="step active-step" >
                        <button type="button" class="step-trigger" style="color: #3498db !important;">
                            <span class="bs-stepper-icon"><i class="fas fa-credit-card"></i></span>
                            <span class="bs-stepper-label">Ödeme</span>
                        </button>
                    </div>
                    <div class="line"><i class="bx bx-chevron-right" style="color: #3498db !important;"></i></div>
                    <div class="step" >
                        <button type="button" class="step-trigger"  >
                            <span class="bs-stepper-icon"><i class="fas fa-check"></i></span>
                            <span class="bs-stepper-label">Onay</span>
                        </button>
                    </div>
                </div>
    <?php } ?>
                <form id="havaleGonder" method="post" action="functions/siparis/sip_olustur.php">
                    <input type="hidden" name="promosyonKodu" id="hiddenPromosyonKodu" value="<?= $promosyon_kodu; ?>">
                    <input type="hidden" name="yanSepetToplami" id="hiddenYanSepetToplami" value="<?= $ara_toplam; ?>">
                    <input type="hidden" name="yanSepetKdv" id="hiddenYanSepetKdv" value="<?= $kdv ;?>">
                    <input type="hidden" name="yanIndirim" id="hiddenindirim" class="hiddenindirim-alani" value="<?= $indirim; ?>">
                    <input type="hidden" name="yanKargo" id="hiddenYanKargo" value="<?= $kargo; ?>">
                    <input type="hidden" name="yantoplam" id="hiddenYanToplam" value="<?= $toplam ;?>">
                    <input type="hidden" name="deliveryOption" id="hiddendeliveryOption" value="<?= $deliveryOption; ?>">
                    <input type="hidden" name="desi" id="hiddenDesi" value="<?= $desi; ?>">
                    <input type="hidden" name="uye_id" id="hiddenuye_id" value="<?= $_SESSION['id']; ?>">
                    <input type="hidden" name="tip" id="tip" value="Havale/EFT">
                    <input type="hidden" name="tip1" id="tip1" value="Sanal Pos">
                    <input type="hidden" name="lang" id="lang" value="tr">
                </form>
                <div class="bs-stepper-content border-top">
                    <form id="wizard-checkout-form" onSubmit="return false">
                        <div class="row">
                            <div class="col-xl-8 col-xxl-9 mb-3 mb-xl-0">
                                <div class="col-xxl-12 col-lg-12">
                                    <ul class="nav nav-pills mb-3" id="paymentTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active btn-outline-dark" id="pills-cc-tab" data-bs-toggle="pill" data-bs-target="#pills-cc" type="button" role="tab" aria-controls="pills-cc" aria-selected="true">Kredi Kartı</button>
                                        </li>
                                        <?php if(isset($_POST["devamEt"])){ ?>

                                        <li class="nav-item ms-2" role="presentation">
                                            <button class="nav-link btn-outline-dark" id="pills-cod-tab" data-bs-toggle="pill" data-bs-target="#pills-cod" type="button" role="tab" aria-controls="pills-cod" aria-selected="false">EFT / HAVALE</button>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                    <div class="tab-content px-0 border-0" id="paymentTabsContent">
                                        <!-- Credit card -->
                                        <div class="tab-pane fade show active" id="pills-cc" role="tabpanel" aria-labelledby="pills-cc-tab">
                                            <div class="row">
                                                <div class="col-xl-6 col-xxl-6">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label class="form-label w-100" for="paymentCard">Kart Numarası</label>
                                                            <div class="input-group input-group-merge">
                                                                <input id="paymentCard" name="paymentCard" class="form-control credit-card-mask" type="text" placeholder="1356 3215 6548 7898" aria-describedby="paymentCard2" autocomplete="off" required autofocus MAXLENGTH="16" />
                                                                <span class="input-group-text cursor-pointer p-1" id="paymentCard2">
                                                                    <span class="card-type" id="card-img">
                                                                    </span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label class="form-label" for="paymentCardName">Kart Üzerindeki İsim</label>
                                                            <input type="text" id="paymentCardName" class="form-control" required MAXLENGTH="25" />
                                                        </div>
                                                        <div class="col-8 col-md-3">
                                                            <label class="form-label" for="paymentCardExpiryDate">Son Kullanma Tarihi</label>
                                                            <div class="d-flex">
                                                                <input type="text" id="paymentCardExpiryMonth" class="form-control expiry-date-mask me-2" placeholder="Ay" autocomplete="off" required MAXLENGTH="2" />
                                                                <input type="text" id="paymentCardExpiryYear" class="form-control expiry-date-mask" placeholder="Yıl" autocomplete="off" required MAXLENGTH="2" />
                                                            </div>
                                                        </div>

                                                        <div class="col-2 col-md-3">
                                                            <label class="form-label" for="paymentCardCvv">CVV</label>
                                                            <div class="input-group input-group-merge">
                                                                <input type="text" id="paymentCardCvv" class="form-control cvv-code-mask" placeholder="000" autocomplete="off" required MAXLENGTH="3"/>
                                                                <span class="input-group-text cursor-pointer" id="paymentCardCvv2"><i class="bx bx-help-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Card Verification Value"></i></span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="container flex-grow-1 container-p-y mt-5">
                                                    <div id="wizard-checkout" class="bs-stepper wizard-icons wizard-icons-example mb-5">
                                                        <div class="table">
                                                            <table class="table table-responsive">
                                                                <thead>
                                                                <tr>
                                                                    <th>Seç.</th>
                                                                    <th>Kartlar</th>
                                                                    <th>Taksit</th>
                                                                    <th>Banka Komisyonu</th>
                                                                    <th>Karttan Çekilecek Tutar</th>
                                                                    <th>Hesaba Geçecek Tutar</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody id="taksit_alanlari">
                                                                <?php
                                                                ?>
                                                                <tr>
                                                                    <td></td>
                                                                    <td>Tüm Bankalar</td>
                                                                    <td>Tek Çekim</td>
                                                                    <td>0%</td>
                                                                    <td>₺<?php echo formatNumber($toplam); ?></span></td>
                                                                    <td>₺<?php echo formatNumber($toplam); ?></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="taksit_1" value="">
                                                <input type="hidden" id="pos_1" value="">
                                                <input type="hidden" id="id_1" value="">
                                                <input type="hidden" id="sonuc_1" value="">
                                                <input type="hidden" id="vade_1" value="">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <button type="button" id="kartlaOdemeyeGec" class="btn btn-primary btn-next me-sm-3 me-1">Ödeme Yap</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(isset($_POST["devamEt"])){ ?>
                                        <!-- EFT-HAVALE -->
                                        <div class="tab-pane fade" id="pills-cod" role="tabpanel" aria-labelledby="pills-cod-tab">
                                            <div class="table table-responsive">
                                                <table class="table border">
                                                    <thead>
                                                        <th class="py-3 border text-center">Hesap Sahibi</th>
                                                        <th class="py-3 border text-center">Banka Adı</th>
                                                        <th class="py-3 border text-center">Hesap</th>
                                                        <th class="py-3 border text-center">Şube Adı</th>
                                                        <th class="py-3 border text-center">IBAN</th>
                                                        <th class="py-3 border text-center">Kolay Adres</th>
                                                        <th class="py-3 border text-center">SWIFT</th>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                             $uye = $database->fetchAll("SELECT * FROM nokta_banka_bilgileri");
                                                             foreach($uye as $row){ ?>
                                                                <tr>
                                                                    <td class="py-3 border text-center"><?= $row["hesap_adi"] ; ?></td>
                                                                    <td class="py-3 border text-center"><?= $row["banka_adi"] ; ?></td>
                                                                    <td class="py-3 border text-center"><?= $row["hesap"] ; ?></td>
                                                                    <td class="py-3 border text-center"><?= $row["sube_adi"] ; ?></td>
                                                                    <td class="py-3 border text-center"><?= $row["iban"] ; ?></td>
                                                                    <td class="py-3 border text-center"><?= $row["kolay_adres"] ; ?></td>
                                                                    <td class="py-3 border text-center"><?= $row["swift"] ; ?></td>
                                                                </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button type="button" class="btn btn-primary" id="odemeYapButton">Ödeme Yap</button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Address right -->
                            <div class="col-xl-4 col-xxl-3">
                                <div class="border rounded px-4 pt-4">
                                    <!-- Price Details -->
                                    <h6 class="fw-medium">Fiyat Detay</h6>
                                    <hr>
                                        <?php if(isset($_POST["devamEt"])){ ?>
                                            <dl class="row mb-0">
                                                <dt class="col-6 fw-normal">Sepet Toplamı</dt>
                                                <dd id="yanSepetToplami" class="col-6 text-end yanSepetToplami">₺<?php echo formatNumber($ara_toplam); ?></dd>

                                                <dt class="col-sm-6 fw-normal">KDV</dt>
                                                <dd id="yanSepetKdv" class="col-sm-6 text-end yanSepetKdv">₺<?php echo formatNumber($kdv); ?></dd>

                                                <dt class="col-sm-6 fw-normal">İndirim</dt>
                                                <dd id="yanIndirim" class="col-sm-6 text-end indirim-alani yanIndirim">₺<?php echo formatNumber($indirim); ?></dd>

                                                <dt class="col-6 fw-normal">Kargo Ücreti</dt>
                                                <dd id="yanKargo" class="col-6 text-end yanKargo">₺<?php echo formatNumber($kargo); ?></dd>
                                            </dl>
                                            <hr>
                                            <dl class="row">
                                                <dt class="col-6 ">Toplam</dt>
                                                <dd class="col-6 text-end" id="toplam">₺<?php echo formatNumber($toplam); ?></dd>
                                            </dl>
                                        <?php }
                                            if(isset($_POST["cariOdemeYap"])){
                                                $fiyat = controlInput($_POST["fiyat"]);
                                                $fiyat = number_format((float)$fiyat, 2, '.', ''); ?>
                                                <dl class="row">
                                                    <dt class="col-6 ">Toplam</dt>
                                                    <dd class="col-6 text-end" id="toplam">₺<?= $fiyat ?></dd>
                                                </dl>
                                        <?php } ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php $template->footer(); ?>
</body>
</html>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/app.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Düğmeyi bul
        var odemeYapButton = document.getElementById('odemeYapButton');

        // Düğmeye tıklama olayını ekle
        if (odemeYapButton) {
            odemeYapButton.addEventListener('click', onayaGec);
        } else {
            //console.error('Düğme bulunamadı!');
        }
    });

    function onayaGec(event) {
        event.target.disabled = true;
        event.target.innerText = "Ödeme İşleniyor...";
        var myForm = document.getElementById('havaleGonder');
        if (myForm) {
            myForm.submit();
        } else {
            console.error('Form bulunamadı!');
        }
    }
</script>
<script>
    function formatNumber(number) {
        if (isNaN(number)) {
            return null;
        }
        var parts = number.toFixed(2).toString().split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return parts.join(',');
    }
    $(document).ready(function() {
        $('#paymentCard').on('input', function() {
            var bin = $(this).val().substr(0, 6);
            if (bin.length >= 6) {
                
                $.ajax({
                    url: 'functions/bank/binSorgula.php',
                    method: 'POST',
                    data: { bin: bin },
                    success: function(response) {
                        var data = response.split(',');
                        var kartOrg = data[2].trim();
                        var kamp = data[1].trim();
                        var bank = data[0].trim();
                        console.log(kamp);
                        console.log(bank);
                        console.log(kartOrg);
                        var imgSrc = '';
                        switch (kartOrg) {
                            case 'TROY':
                                imgSrc = 'troy.png';
                                break;
                            case 'MASTER CARD':
                                imgSrc = 'mastercard.png';
                                break;
                            case 'MasterCard':
                                imgSrc = 'mastercard.png';
                                break;
                            case 'Visa':
                                imgSrc = 'visa.png';
                                break;
                            case 'VISA':
                                imgSrc = 'visa.png';
                                break;
                            case 'AMEX':
                                imgSrc = 'amex.png';
                                break;
                            case 'UNION PAY':
                                imgSrc = 'union.png';
                                break;
                            default:
                                imgSrc = 'default.jpg';
                                break;
                        }
                        switch (bank) {
                            case 'TÜRKİYE GARANTİ BANKASI A.Ş.':
                                bank = 'Garanti';
                                break;
                            case 'AKBANK T.A.Ş.':
                                bank = 'Akbank';
                                break;
                            case 'TÜRKİYE İŞ BANKASI A.Ş.':
                                bank = 'Türkiye İş Bankası';
                                break;
                            case 'QNB Finansbank A.Ş.':
                                bank = 'QNB Finansbank';
                                break;
                            case 'TÜRKİYE VAKIFLAR BANKASI T.A.O.':
                                bank = 'VakıfBank';
                                break;
                            case 'T.C. ZİRAAT BANKASI A.Ş.':
                                bank = 'Ziraat Bankası';
                                break;
                            case 'TÜRKİYE FİNANS KATILIM BANKASI A.Ş.':
                                bank = 'Türkiye Finans';
                                break;
                            case 'TÜRKİYE HALK BANKASI A.Ş.':
                                bank = 'Halkbank';
                                break;
                            case 'TÜRK EKONOMİ BANKASI A.Ş.':
                                bank = 'Teb';
                                break;
                            case 'Şekerbank T.A.Ş.':
                                bank = 'ŞekerBank';
                                break;
                            case 'YAPI VE KREDİ BANKASI A.Ş.':
                                bank = 'Yapıkredi';
                                break;
                            case 'ING BANK A.Ş.':
                                bank = 'ING Bank';
                                break;
                            case 'Denizbank A.Ş.':
                                bank = 'DenizBank';
                                break;
                            case 'Anadolubank A.Ş.':
                                bank = 'AnadoluBank';
                                break;
                            case 'AL BARAKA TÜRK KATILIM BANKASI A.Ş.':
                                bank = 'Albaraka';
                                break;
                            case 'KUVEYT TÜRK KATILIM BANKASI A.Ş.':
                                bank = 'KuveytTürk';
                                break;
                            case 'HSBC BANK A.Ş.':
                                bank = 'HSBC';
                                break;
                            case 'Türkiye Finans Katılım Bankası A.Ş.':
                                bank = 'Türkiye Finans';
                                break;
                        }
                        $('#card-img').html('<img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banka/' + imgSrc + '" alt="' + kartOrg + '" style="max-height: 35px;">');
                        $.ajax({
                            url: 'functions/bank/kartSorgula.php',
                            method: 'POST',
                            data: { bank: bank, kamp:kamp },
                            success: function(response) {
                                kartId = response;
                                $.ajax({
                                    url: 'functions/bank/taksitSorgula.php',
                                    method: 'POST',
                                    data: { kartId: kartId},
                                    success: function(response) {
                                        var data = JSON.parse(response);
                                        $('#taksit_alanlari').empty();
                                        for (var i = 0; i < data.length; i++) {
                                            var taksit = data[i].taksit;
                                            var vade = parseFloat(data[i].vade.replace(',', '.'));
                                            var pos_id = data[i].pos_id;
                                            var id = data[i].id;
                                            var imgWidth = "60px";
                                            var kampImg = '';
                                            if (kamp === "Bonus") {
                                                kampImg = '<img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banka/bonus.png" style="width: ' + imgWidth + ';" />';
                                            } else if(kamp === "Axess"){
                                                kampImg = '<img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banka/axess.png" style="width: ' + imgWidth + ';" />';
                                            } else if(kamp === "World"){
                                                kampImg = '<img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banka/world.png" style="width: ' + imgWidth + ';" />';
                                            } else if(kamp === "Maximum"){
                                                kampImg = '<img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banka/maximum.png" style="width: ' + imgWidth + ';" />';
                                            } else if(kamp === ""){
                                                kampImg = 'Diğer Banka Kartları';
                                            }  else{
                                                kampImg = kamp; // Özel bir seçenek değilse, kamp değişkenini doğrudan metin olarak göster
                                            }
                                            var toplamText = $('#toplam').text();
                                            toplamText = toplamText.replace('₺', ''); // ₺ işaretini kaldır
                                            var toplam = parseFloat(toplamText.replace(/\./g, '').replace(',', '.')); // Noktaları kaldır ve virgülü noktaya çevir
                                            var sonuc = (toplam * vade / 100) + toplam;
                                            sonuc1 = formatNumber(sonuc);
                                            var formattedSonuc1 = sonuc1.replace(/\./g, '').replace(',', '.');
                                            var taksitMetni = taksit == 1 ? 'Tek Çekim' : taksit + ' Taksit';
                                            var checked = i === 0 ? 'checked' : ''; // İlk öğeyi seçili yapmak için kontrol

                                            $('#taksit_alanlari').append('<tr>'+
                                                '<td><div class="form-check">'+
                                                '<input name="customRadioIcon" class="form-check-input" type="radio" value="' + formattedSonuc1 + '" id="customRadioDelivery' + i + '" onchange="taksitGonder(\''+ formattedSonuc1 +'\','+ taksit +','+ pos_id +','+ vade +','+ id +')" ' + checked + '>' +
                                                '</div></td>'+
                                                '<td>'+ kampImg +'</td>'+
                                                '<td>' + taksitMetni + '</td>'+
                                                '<td>'+ vade +'%</td>'+
                                                '<td>₺'+ sonuc1 +'</td>'+
                                                '<td>₺'+ toplamText +'</td>'+
                                                '</tr>');
                                            if (i === 0) {
                                                taksitGonder(formattedSonuc1, taksit, pos_id, vade, id);
                                            }
                                        }

                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('#hiddenYanToplam').on('input', function () {
            var value = $(this).val();
            value = value.replace(',', '.'); // Virgülü noktaya çevir
            $(this).val(value);

            // Noktadan sonra en fazla 2 karaktere izin verme
            var dotIndex = value.indexOf('.');
            if (dotIndex !== -1) {
                var afterDot = value.substring(dotIndex + 1);
                if (afterDot.length > 2) {
                    $(this).val(value.substring(0, dotIndex + 3));
                }
            }
        });
    });
</script>
<script>
    function taksitGonder(sonuc, taksit, pos_id, vade,id){
        $('#taksit_1').val(taksit);
        $('#pos_1').val(pos_id);
        $('#id_1').val(id);
        $('#sonuc_1').val(sonuc);
        $('#sonuc_2').val(sonuc);
        $('#vade_1').val(vade);
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var kartlaOdemeyeGec = document.getElementById('kartlaOdemeyeGec');

        if (kartlaOdemeyeGec) {
            kartlaOdemeyeGec.addEventListener('click', posagit);
        }else {
            console.error('Düğme bulunamadı!');
        }
    });
    function posagit(event) {
        var promosyon_kodu = document.getElementById('hiddenPromosyonKodu').value;
        var ara_toplam = document.getElementById('hiddenYanSepetToplami').value;
        var kdv = document.getElementById('hiddenYanSepetKdv').value;
        var indirim = document.getElementById('hiddenindirim').value;
        var kargo = document.getElementById('hiddenYanKargo').value;
        var toplam = document.getElementById('hiddenYanToplam').value;
        var deliveryOption = document.getElementById('hiddendeliveryOption').value;
        var desi = document.getElementById('hiddenDesi').value;
        var tip = document.getElementById('tip1').value;
        var lang = document.getElementById('lang').value;
        var uye_id = document.getElementById('hiddenuye_id').value;
        var pos = document.getElementById('pos_1').value;
        var banka_id = document.getElementById('id_1').value;
        var taksit = document.getElementById('taksit_1').value;
        var sonuc = document.getElementById('sonuc_1').value;
        var vade = document.getElementById('vade_1').value;
        sonuc = sonuc.replace('.', ',');
        var paymentCard = document.getElementById('paymentCard').value;
        var paymentCardName = document.getElementById('paymentCardName').value;
        var paymentCardExpiryMonth = document.getElementById('paymentCardExpiryMonth').value;
        var paymentCardExpiryYear = document.getElementById('paymentCardExpiryYear').value;
        var paymentCardCvv = document.getElementById('paymentCardCvv').value;

        if (pos && taksit && sonuc && paymentCard && paymentCardName && paymentCardExpiryMonth && paymentCardExpiryYear && paymentCardCvv) {
            event.target.disabled = true;
            event.target.innerText = "Ödeme İşleniyor...";
            if (pos === '1') {//Param Pos
                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', 'functions/bank/param/payment.php');

                var araToplamField = document.createElement('input');
                araToplamField.setAttribute('type', 'hidden');
                araToplamField.setAttribute('name', 'araToplam');
                araToplamField.setAttribute('value', ara_toplam);
                form.appendChild(araToplamField);

                var desiField = document.createElement('input');
                desiField.setAttribute('type', 'hidden');
                desiField.setAttribute('name', 'desi');
                desiField.setAttribute('value', desi);
                form.appendChild(desiField);

                var promosyonKodu = document.createElement('input');
                promosyonKodu.setAttribute('type', 'hidden');
                promosyonKodu.setAttribute('name', 'promosyonKodu');
                promosyonKodu.setAttribute('value', promosyon_kodu);
                form.appendChild(promosyonKodu);

                var secilen_kargo = document.createElement('input');
                secilen_kargo.setAttribute('type', 'hidden');
                secilen_kargo.setAttribute('name', 'deliveryOption');
                secilen_kargo.setAttribute('value', deliveryOption);
                form.appendChild(secilen_kargo);

                var bankaId = document.createElement('input');
                bankaId.setAttribute('type', 'hidden');
                bankaId.setAttribute('name', 'banka_id');
                bankaId.setAttribute('value', banka_id);
                form.appendChild(bankaId);

                var kdvField = document.createElement('input');
                kdvField.setAttribute('type', 'hidden');
                kdvField.setAttribute('name', 'kdv');
                kdvField.setAttribute('value', kdv);
                form.appendChild(kdvField);

                var indirimField = document.createElement('input');
                indirimField.setAttribute('type', 'hidden');
                indirimField.setAttribute('name', 'indirim');
                indirimField.setAttribute('value', indirim);
                form.appendChild(indirimField);

                var kargoField = document.createElement('input');
                kargoField.setAttribute('type', 'hidden');
                kargoField.setAttribute('name', 'kargo');
                kargoField.setAttribute('value', kargo);
                form.appendChild(kargoField);

                var toplamField = document.createElement('input');
                toplamField.setAttribute('type', 'hidden');
                toplamField.setAttribute('name', 'toplam');
                toplamField.setAttribute('value', toplam);
                form.appendChild(toplamField);

                var tipField = document.createElement('input');
                tipField.setAttribute('type', 'hidden');
                tipField.setAttribute('name', 'tip');
                tipField.setAttribute('value', tip);
                form.appendChild(tipField);

                var langField = document.createElement('input');
                langField.setAttribute('type', 'hidden');
                langField.setAttribute('name', 'lang');
                langField.setAttribute('value', lang);
                form.appendChild(langField);

                var uye_idField = document.createElement('input');
                uye_idField.setAttribute('type', 'hidden');
                uye_idField.setAttribute('name', 'uye_id');
                uye_idField.setAttribute('value', uye_id);
                form.appendChild(uye_idField);

                var taksitField = document.createElement('input');
                taksitField.setAttribute('type', 'hidden');
                taksitField.setAttribute('name', 'odemetaksit');
                taksitField.setAttribute('value', taksit);
                form.appendChild(taksitField);

                var vadeOrani = document.createElement('input');
                vadeOrani.setAttribute('type', 'hidden');
                vadeOrani.setAttribute('name', 'vade');
                vadeOrani.setAttribute('value', vade);
                form.appendChild(vadeOrani);

                var paymentCardNo = document.createElement('input');
                paymentCardNo.setAttribute('type', 'hidden');
                paymentCardNo.setAttribute('name', 'cardNumber');
                paymentCardNo.setAttribute('value', paymentCard);
                form.appendChild(paymentCardNo);

                var cartHolderName = document.createElement('input');
                cartHolderName.setAttribute('type', 'hidden');
                cartHolderName.setAttribute('name', 'cardName');
                cartHolderName.setAttribute('value', paymentCardName);
                form.appendChild(cartHolderName);

                var cardExpiryMonth = document.createElement('input');
                cardExpiryMonth.setAttribute('type', 'hidden');
                cardExpiryMonth.setAttribute('name', 'expMonth');
                cardExpiryMonth.setAttribute('value', paymentCardExpiryMonth);
                form.appendChild(cardExpiryMonth);

                var cardExpiryYear = document.createElement('input');
                cardExpiryYear.setAttribute('type', 'hidden');
                cardExpiryYear.setAttribute('name', 'expYear');
                cardExpiryYear.setAttribute('value', paymentCardExpiryYear);
                form.appendChild(cardExpiryYear);

                var cardCvv = document.createElement('input');
                cardCvv.setAttribute('type', 'hidden');
                cardCvv.setAttribute('name', 'cvCode');
                cardCvv.setAttribute('value', paymentCardCvv);
                form.appendChild(cardCvv);

                var sonucField = document.createElement('input');
                sonucField.setAttribute('type', 'hidden');
                sonucField.setAttribute('name', 'odemetutar');
                sonucField.setAttribute('value', sonuc);
                form.appendChild(sonucField);

                document.body.appendChild(form);
                form.submit();
            }
            else if (pos === '2') {//Garanti Pos
                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', 'functions/bank/garantipos/gpos.php');

                var araToplamField = document.createElement('input');
                araToplamField.setAttribute('type', 'hidden');
                araToplamField.setAttribute('name', 'araToplam');
                araToplamField.setAttribute('value', ara_toplam);
                form.appendChild(araToplamField);

                var promosyonKodu = document.createElement('input');
                promosyonKodu.setAttribute('type', 'hidden');
                promosyonKodu.setAttribute('name', 'promosyon_kodu');
                promosyonKodu.setAttribute('value', promosyon_kodu);
                form.appendChild(promosyonKodu);

                var desiField = document.createElement('input');
                desiField.setAttribute('type', 'hidden');
                desiField.setAttribute('name', 'desi');
                desiField.setAttribute('value', desi);
                form.appendChild(desiField);

                var secilen_kargo = document.createElement('input');
                secilen_kargo.setAttribute('type', 'hidden');
                secilen_kargo.setAttribute('name', 'deliveryOption');
                secilen_kargo.setAttribute('value', deliveryOption);
                form.appendChild(secilen_kargo);

                var bankaId = document.createElement('input');
                bankaId.setAttribute('type', 'hidden');
                bankaId.setAttribute('name', 'banka_id');
                bankaId.setAttribute('value', banka_id);
                form.appendChild(bankaId);

                var kdvField = document.createElement('input');
                kdvField.setAttribute('type', 'hidden');
                kdvField.setAttribute('name', 'kdv');
                kdvField.setAttribute('value', kdv);
                form.appendChild(kdvField);

                var indirimField = document.createElement('input');
                indirimField.setAttribute('type', 'hidden');
                indirimField.setAttribute('name', 'indirim');
                indirimField.setAttribute('value', indirim);
                form.appendChild(indirimField);

                var kargoField = document.createElement('input');
                kargoField.setAttribute('type', 'hidden');
                kargoField.setAttribute('name', 'kargo');
                kargoField.setAttribute('value', kargo);
                form.appendChild(kargoField);

                var toplamField = document.createElement('input');
                toplamField.setAttribute('type', 'hidden');
                toplamField.setAttribute('name', 'toplam');
                toplamField.setAttribute('value', toplam);
                form.appendChild(toplamField);

                var tipField = document.createElement('input');
                tipField.setAttribute('type', 'hidden');
                tipField.setAttribute('name', 'tip');
                tipField.setAttribute('value', tip);
                form.appendChild(tipField);

                var langField = document.createElement('input');
                langField.setAttribute('type', 'hidden');
                langField.setAttribute('name', 'lang');
                langField.setAttribute('value', lang);
                form.appendChild(langField);

                var uye_idField = document.createElement('input');
                uye_idField.setAttribute('type', 'hidden');
                uye_idField.setAttribute('name', 'uye_id');
                uye_idField.setAttribute('value', uye_id);
                form.appendChild(uye_idField);

                var taksitField = document.createElement('input');
                taksitField.setAttribute('type', 'hidden');
                taksitField.setAttribute('name', 'odemetaksit');
                taksitField.setAttribute('value', taksit);
                form.appendChild(taksitField);

                var vadeOrani = document.createElement('input');
                vadeOrani.setAttribute('type', 'hidden');
                vadeOrani.setAttribute('name', 'vade');
                vadeOrani.setAttribute('value', vade);
                form.appendChild(vadeOrani);

                var paymentCardNo = document.createElement('input');
                paymentCardNo.setAttribute('type', 'hidden');
                paymentCardNo.setAttribute('name', 'cardNumber');
                paymentCardNo.setAttribute('value', paymentCard);
                form.appendChild(paymentCardNo);

                var cartHolderName = document.createElement('input');
                cartHolderName.setAttribute('type', 'hidden');
                cartHolderName.setAttribute('name', 'cardName');
                cartHolderName.setAttribute('value', paymentCardName);
                form.appendChild(cartHolderName);

                var cardExpiryMonth = document.createElement('input');
                cardExpiryMonth.setAttribute('type', 'hidden');
                cardExpiryMonth.setAttribute('name', 'expMonth');
                cardExpiryMonth.setAttribute('value', paymentCardExpiryMonth);
                form.appendChild(cardExpiryMonth);

                var cardExpiryYear = document.createElement('input');
                cardExpiryYear.setAttribute('type', 'hidden');
                cardExpiryYear.setAttribute('name', 'expYear');
                cardExpiryYear.setAttribute('value', paymentCardExpiryYear);
                form.appendChild(cardExpiryYear);

                var cardCvv = document.createElement('input');
                cardCvv.setAttribute('type', 'hidden');
                cardCvv.setAttribute('name', 'cvCode');
                cardCvv.setAttribute('value', paymentCardCvv);
                form.appendChild(cardCvv);

                var sonucField = document.createElement('input');
                sonucField.setAttribute('type', 'hidden');
                sonucField.setAttribute('name', 'odemetutar');
                sonucField.setAttribute('value', sonuc);
                form.appendChild(sonucField);

                document.body.appendChild(form);
                form.submit();
            }
            else if (pos === '3') {// Kuveyt Pos
                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', 'functions/bank/kuveyt/2_Odeme.php');

                var araToplamField = document.createElement('input');
                araToplamField.setAttribute('type', 'hidden');
                araToplamField.setAttribute('name', 'araToplam');
                araToplamField.setAttribute('value', ara_toplam);
                form.appendChild(araToplamField);

                var promosyonKodu = document.createElement('input');
                promosyonKodu.setAttribute('type', 'hidden');
                promosyonKodu.setAttribute('name', 'promosyon_kodu');
                promosyonKodu.setAttribute('value', promosyon_kodu);
                form.appendChild(promosyonKodu);

                var desiField = document.createElement('input');
                desiField.setAttribute('type', 'hidden');
                desiField.setAttribute('name', 'desi');
                desiField.setAttribute('value', desi);
                form.appendChild(desiField);

                var secilen_kargo = document.createElement('input');
                secilen_kargo.setAttribute('type', 'hidden');
                secilen_kargo.setAttribute('name', 'deliveryOption');
                secilen_kargo.setAttribute('value', deliveryOption);
                form.appendChild(secilen_kargo);

                var bankaId = document.createElement('input');
                bankaId.setAttribute('type', 'hidden');
                bankaId.setAttribute('name', 'banka_id');
                bankaId.setAttribute('value', banka_id);
                form.appendChild(bankaId);

                var kdvField = document.createElement('input');
                kdvField.setAttribute('type', 'hidden');
                kdvField.setAttribute('name', 'kdv');
                kdvField.setAttribute('value', kdv);
                form.appendChild(kdvField);

                var indirimField = document.createElement('input');
                indirimField.setAttribute('type', 'hidden');
                indirimField.setAttribute('name', 'indirim');
                indirimField.setAttribute('value', indirim);
                form.appendChild(indirimField);

                var kargoField = document.createElement('input');
                kargoField.setAttribute('type', 'hidden');
                kargoField.setAttribute('name', 'kargo');
                kargoField.setAttribute('value', kargo);
                form.appendChild(kargoField);

                var toplamField = document.createElement('input');
                toplamField.setAttribute('type', 'hidden');
                toplamField.setAttribute('name', 'toplam');
                toplamField.setAttribute('value', toplam);
                form.appendChild(toplamField);

                var tipField = document.createElement('input');
                tipField.setAttribute('type', 'hidden');
                tipField.setAttribute('name', 'tip');
                tipField.setAttribute('value', tip);
                form.appendChild(tipField);

                var langField = document.createElement('input');
                langField.setAttribute('type', 'hidden');
                langField.setAttribute('name', 'lang');
                langField.setAttribute('value', lang);
                form.appendChild(langField);

                var uye_idField = document.createElement('input');
                uye_idField.setAttribute('type', 'hidden');
                uye_idField.setAttribute('name', 'uye_id');
                uye_idField.setAttribute('value', uye_id);
                form.appendChild(uye_idField);

                var taksitField = document.createElement('input');
                taksitField.setAttribute('type', 'hidden');
                taksitField.setAttribute('name', 'odemetaksit');
                taksitField.setAttribute('value', taksit);
                form.appendChild(taksitField);

                var vadeOrani = document.createElement('input');
                vadeOrani.setAttribute('type', 'hidden');
                vadeOrani.setAttribute('name', 'vade');
                vadeOrani.setAttribute('value', vade);
                form.appendChild(vadeOrani);

                var paymentCardNo = document.createElement('input');
                paymentCardNo.setAttribute('type', 'hidden');
                paymentCardNo.setAttribute('name', 'cardNumber');
                paymentCardNo.setAttribute('value', paymentCard);
                form.appendChild(paymentCardNo);

                var cartHolderName = document.createElement('input');
                cartHolderName.setAttribute('type', 'hidden');
                cartHolderName.setAttribute('name', 'cardName');
                cartHolderName.setAttribute('value', paymentCardName);
                form.appendChild(cartHolderName);

                var cardExpiryMonth = document.createElement('input');
                cardExpiryMonth.setAttribute('type', 'hidden');
                cardExpiryMonth.setAttribute('name', 'expMonth');
                cardExpiryMonth.setAttribute('value', paymentCardExpiryMonth);
                form.appendChild(cardExpiryMonth);

                var cardExpiryYear = document.createElement('input');
                cardExpiryYear.setAttribute('type', 'hidden');
                cardExpiryYear.setAttribute('name', 'expYear');
                cardExpiryYear.setAttribute('value', paymentCardExpiryYear);
                form.appendChild(cardExpiryYear);

                var cardCvv = document.createElement('input');
                cardCvv.setAttribute('type', 'hidden');
                cardCvv.setAttribute('name', 'cvCode');
                cardCvv.setAttribute('value', paymentCardCvv);
                form.appendChild(cardCvv);

                var sonucField = document.createElement('input');
                sonucField.setAttribute('type', 'hidden');
                sonucField.setAttribute('name', 'odemetutar');
                sonucField.setAttribute('value', sonuc);
                form.appendChild(sonucField);

                document.body.appendChild(form);
                form.submit();
            }
            else if (pos === '4') {// Türkiye Finans Pos
                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', 'functions/bank/turkiye_finans/request.php');

                var araToplamField = document.createElement('input');
                araToplamField.setAttribute('type', 'hidden');
                araToplamField.setAttribute('name', 'araToplam');
                araToplamField.setAttribute('value', ara_toplam);
                form.appendChild(araToplamField);

                var promosyonKodu = document.createElement('input');
                promosyonKodu.setAttribute('type', 'hidden');
                promosyonKodu.setAttribute('name', 'promosyon_kodu');
                promosyonKodu.setAttribute('value', promosyon_kodu);
                form.appendChild(promosyonKodu);

                var desiField = document.createElement('input');
                desiField.setAttribute('type', 'hidden');
                desiField.setAttribute('name', 'desi');
                desiField.setAttribute('value', desi);
                form.appendChild(desiField);

                var secilen_kargo = document.createElement('input');
                secilen_kargo.setAttribute('type', 'hidden');
                secilen_kargo.setAttribute('name', 'deliveryOption');
                secilen_kargo.setAttribute('value', deliveryOption);
                form.appendChild(secilen_kargo);

                var bankaId = document.createElement('input');
                bankaId.setAttribute('type', 'hidden');
                bankaId.setAttribute('name', 'banka_id');
                bankaId.setAttribute('value', banka_id);
                form.appendChild(bankaId);

                var kdvField = document.createElement('input');
                kdvField.setAttribute('type', 'hidden');
                kdvField.setAttribute('name', 'kdv');
                kdvField.setAttribute('value', kdv);
                form.appendChild(kdvField);

                var indirimField = document.createElement('input');
                indirimField.setAttribute('type', 'hidden');
                indirimField.setAttribute('name', 'indirim');
                indirimField.setAttribute('value', indirim);
                form.appendChild(indirimField);

                var kargoField = document.createElement('input');
                kargoField.setAttribute('type', 'hidden');
                kargoField.setAttribute('name', 'kargo');
                kargoField.setAttribute('value', kargo);
                form.appendChild(kargoField);

                var toplamField = document.createElement('input');
                toplamField.setAttribute('type', 'hidden');
                toplamField.setAttribute('name', 'toplam');
                toplamField.setAttribute('value', toplam);
                form.appendChild(toplamField);

                var tipField = document.createElement('input');
                tipField.setAttribute('type', 'hidden');
                tipField.setAttribute('name', 'tip');
                tipField.setAttribute('value', tip);
                form.appendChild(tipField);

                var langField = document.createElement('input');
                langField.setAttribute('type', 'hidden');
                langField.setAttribute('name', 'lang');
                langField.setAttribute('value', lang);
                form.appendChild(langField);

                var uye_idField = document.createElement('input');
                uye_idField.setAttribute('type', 'hidden');
                uye_idField.setAttribute('name', 'uye_id');
                uye_idField.setAttribute('value', uye_id);
                form.appendChild(uye_idField);

                var taksitField = document.createElement('input');
                taksitField.setAttribute('type', 'hidden');
                taksitField.setAttribute('name', 'odemetaksit');
                taksitField.setAttribute('value', taksit);
                form.appendChild(taksitField);

                var vadeOrani = document.createElement('input');
                vadeOrani.setAttribute('type', 'hidden');
                vadeOrani.setAttribute('name', 'vade');
                vadeOrani.setAttribute('value', vade);
                form.appendChild(vadeOrani);

                var paymentCardNo = document.createElement('input');
                paymentCardNo.setAttribute('type', 'hidden');
                paymentCardNo.setAttribute('name', 'cardNumber');
                paymentCardNo.setAttribute('value', paymentCard);
                form.appendChild(paymentCardNo);

                var cartHolderName = document.createElement('input');
                cartHolderName.setAttribute('type', 'hidden');
                cartHolderName.setAttribute('name', 'cardName');
                cartHolderName.setAttribute('value', paymentCardName);
                form.appendChild(cartHolderName);

                var cardExpiryMonth = document.createElement('input');
                cardExpiryMonth.setAttribute('type', 'hidden');
                cardExpiryMonth.setAttribute('name', 'expMonth');
                cardExpiryMonth.setAttribute('value', paymentCardExpiryMonth);
                form.appendChild(cardExpiryMonth);

                var cardExpiryYear = document.createElement('input');
                cardExpiryYear.setAttribute('type', 'hidden');
                cardExpiryYear.setAttribute('name', 'expYear');
                cardExpiryYear.setAttribute('value', paymentCardExpiryYear);
                form.appendChild(cardExpiryYear);

                var cardCvv = document.createElement('input');
                cardCvv.setAttribute('type', 'hidden');
                cardCvv.setAttribute('name', 'cvCode');
                cardCvv.setAttribute('value', paymentCardCvv);
                form.appendChild(cardCvv);

                var sonucField = document.createElement('input');
                sonucField.setAttribute('type', 'hidden');
                sonucField.setAttribute('name', 'odemetutar');
                sonucField.setAttribute('value', sonuc);
                form.appendChild(sonucField);

                document.body.appendChild(form);
                form.submit();
            }
            else {// pos değeri 1, 2 veya 3 değilse
                 }
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Uyarı',
                text: 'Lütfen tüm alanları doldurunuz!',
            });
        }
        if (paymentCard.length < 16) {
            Swal.fire({
                icon: 'warning',
                title: 'Uyarı',
                text: 'Kart numarası 16 karakter olmalıdır!',
            });
        }
    }
</script>