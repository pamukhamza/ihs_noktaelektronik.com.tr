<?php
require '../functions/admin_template.php';
require '../functions/functions.php';
sessionControl();
$currentPage = 'cari_odeme';
$template = new Template('Nokta - Ödeme', $currentPage);

$template->head();
$database = new Database();

if (isset($_POST['fiyat']) && isset($_POST['hesap'])) {
    $toplam = controlInput($_POST['fiyat']);
    $hesap =  controlInput($_POST['hesap']);
}
    $dolar = $database->fetch("SELECT satis FROM b2b_kurlar WHERE id = 2 ");
    $satis_dolar_kuru = $dolar['satis'];

    $euro = $database->fetch("SELECT * FROM b2b_kurlar WHERE id = 3 ");
    $satis_euro_kuru = $euro['satis'];
?>
<body>
<?php $template->header(); ?>
<div class="container flex-grow-1 container-p-y mt-5">
    <div id="wizard-checkout" class="bs-stepper wizard-icons wizard-icons-example mb-5">
        <form id="havaleGonder" method="post" action="php/sip_olustur.php">
            <input type="hidden" name="yantoplam" id="hiddenYanToplam" value="<?= $toplam ;?>">
            <input type="hidden" name="hesap" id="hesap" value="<?= $hesap ;?>">
            <input type="hidden" name="uye_id" id="hiddenuye_id" value="<?= $_SESSION['id']; ?>">
            <input type="hidden" name="tip1" id="tip1" value="Sanal Pos">
            <input type="hidden" name="lang" id="lang" value="tr">
        </form>

        <div class="bs-stepper-content border-top">
            <form id="wizard-checkout-form" onSubmit="return false">
                <div class="row">
                    <div class="col-xl-6 col-xxl-6 mb-3 mb-xl-0">
                        <div class="col-xxl-12 col-lg-12">
                            <ul class="nav nav-pills mb-3" id="paymentTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active btn-outline-dark" id="pills-cc-tab" data-bs-toggle="pill" data-bs-target="#pills-cc" type="button" role="tab" aria-controls="pills-cc" aria-selected="true">Kredi Kartı</button>
                                </li>
                            </ul>
                            <div class="tab-content px-0 border-0" id="paymentTabsContent">
                                <!-- Credit card -->
                                <div class="tab-pane fade show active" id="pills-cc" role="tabpanel" aria-labelledby="pills-cc-tab">
                                    <div class="row">
                                        <div class="col-xl-12 col-xxl-12">
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
                                                <div class="col-6 col-sm-6 col-md-3">
                                                    <label class="form-label" for="paymentCardExpiryDate">SKT</label>
                                                    <div class="d-flex">
                                                        <input type="text" id="paymentCardExpiryMonth" class="form-control expiry-date-mask me-2" placeholder="Ay" autocomplete="off" required MAXLENGTH="2" />
                                                        <input type="text" id="paymentCardExpiryYear" class="form-control expiry-date-mask" placeholder="Yıl" autocomplete="off" required MAXLENGTH="2" />
                                                    </div>
                                                </div>
                                                <div class="col-5 col-sm-6 col-md-3">
                                                    <label class="form-label" for="paymentCardCvv">CVV</label>
                                                    <div class="input-group input-group-merge">
                                                        <input type="text" id="paymentCardCvv" class="form-control cvv-code-mask" placeholder="000" autocomplete="off" required MAXLENGTH="3"/>
                                                        <span class="input-group-text cursor-pointer" id="paymentCardCvv2"><i class="bx bx-help-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Card Verification Value"></i></span>
                                                    </div>
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
                                                <button type="button" id="kartlaOdemeyeGec" name="cariOdeme" class="btn btn-primary btn-next me-sm-3 me-1">Ödeme Yap</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Address right -->
                    <div class="col-xl-1 col-xxl-1"></div>
                    <div class="col-xl-5 col-xxl-5">
                        <div class="border rounded px-4 pt-4">
                            <!-- Price Details -->
                            <h6 class="fw-medium">Fiyat Detay</h6>
                            <hr>
                            <?php
                            if(isset($_POST["cariOdemeYap"])){
                                $fiyat = controlInput($_POST["fiyat"]);
                                $dlr_fiyat = $fiyat / $satis_dolar_kuru;
                                $euro_fiyat = $fiyat / $satis_euro_kuru;
                                $fiyat =formatNumber($fiyat);
                                ?>
                                <dl class="row">
                                    <dt class="col-6 ">Ara Toplam</dt>
                                    <dd class="col-6 text-end" id="toplam">₺<?= $fiyat ?></dd>
                                    <dd class="col-12 text-end" style="font-size: 14px">$<?= formatNumber($dlr_fiyat)?> </dd>
                                    <dd class="col-12 text-end" style="font-size: 14px">€<?= formatNumber($euro_fiyat)?></dd>
                                </dl>
                            <?php } ?>
                            <dl class="row">
                                <dt class="col-6 ">Vade Farkı</dt>
                                <dd class="col-6 text-end" id="vadesi" >₺0,00</dd>
                            </dl>
                            <dl class="row">
                                <dt class="col-6 ">Toplam</dt>
                                <dd class="col-6 text-end" id="son_sonuc" >₺<?= $fiyat ?></dd>
                            </dl>
                        </div>
                    </div>
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
                                <td>₺<?= $fiyat ?></td>
                                <td>₺<?= $fiyat ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </form>
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
            console.error('Düğme bulunamadı!');
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
        $('#paymentCard').on('input', function() {/*
            var bin = $(this).val().substr(0, 6);
            if (bin.length >= 6) {
                console.log(bin);
                $.ajax({
                    url: 'functions/bank/binSorgula.php',
                    method: 'POST',
                    data: { bin: bin },
                    success: function(response) {
                        console.log(response);
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
                                            var formattedSonuc1 = sonuc1;
                                            var taksitMetni = taksit == 1 ? 'Tek Çekim' : taksit + ' Taksit';
                                            var checked = i === 0 ? 'checked' : ''; // İlk öğeyi seçili yapmak için kontrol

                                            $('#taksit_alanlari').append('<tr>'+
                                                '<td><div class="form-check">'+
                                                '<input name="customRadioIcon" class="form-check-input" type="radio" value="' + formattedSonuc1 + '" id="customRadioDelivery' + i + '" onchange="taksitGonder(\''+ formattedSonuc1 +'\','+ taksit +','+ pos_id +','+ vade +','+ id +')" ' + checked + '>' +
                                                '</div></td>'+
                                                '<td>'+ kampImg  +'</td>'+
                                                '<td>' + taksitMetni + '</td>'+
                                                '<td>'+ vade +'%</td>'+
                                                '<td>₺'+ formattedSonuc1 +'</td>'+
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
            }*/
        });
    });
</script>
<script>
    function taksitGonder(formattedSonuc1, taksit, pos_id, vade, id){
        $('#son_sonuc').html('₺' + formattedSonuc1);
        var ensonsonuc = parseFloat(formattedSonuc1.replace(/\./g, '').replace(',', '.'));
        var toplamText = $('#toplam').text();
        toplamText = toplamText.replace('₺', ''); // ₺ işaretini kaldır
        var toplam = parseFloat(toplamText.replace(/\./g, '').replace(',', '.')); // Noktaları kaldır ve virgülü noktaya çevir
        var vadefarki = parseFloat(ensonsonuc - toplam);
        vadefarki = formatNumber(vadefarki);

        $('#vadesi').html('₺' + vadefarki);
        $('#taksit_1').val(taksit);
        $('#pos_1').val(pos_id);
        $('#id_1').val(id);
        $('#sonuc_1').val(formattedSonuc1);
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
        const elements = {
            toplam: 'hiddenYanToplam',
            tip: 'tip1',
            lang: 'lang',
            uye_id: 'hiddenuye_id',
            hesap: 'hesap',
            card: 'paymentCard',
            cardName: 'paymentCardName',
            expMonth: 'paymentCardExpiryMonth',
            expYear: 'paymentCardExpiryYear',
            cvv: 'paymentCardCvv'
        };

        const values = Object.fromEntries(
            Object.entries(elements).map(([key, id]) => [key, document.getElementById(id).value])
        );
        values.pos = document.getElementById('pos_1')?.value || '4';
        values.banka_id = document.getElementById('id_1')?.value || '57';
        values.taksit = document.getElementById('taksit_1')?.value || '1';
        values.vade = document.getElementById('vade_1')?.value || '1';
        values.sonuc = document.getElementById('sonuc_1')?.value || "<?= $fiyat ?>";
        values.sonuc = values.sonuc.replace('.', ',');

        const requiredFields = ['pos', 'taksit', 'sonuc', 'card', 'cardName', 'expMonth', 'expYear', 'cvv'];
        if (requiredFields.every(field => values[field]) && values.card.length >= 16) {
            event.target.disabled = true;
            event.target.innerText = "Ödeme İşleniyor...";

            const posConfig = {
                '1': { url: 'functions/bank/param/payment.php' },
                '2': { url: 'functions/bank/garantipos/gpos.php', extra: { tip: values.tip } },
                '3': { url: 'functions/bank/kuveyt/2_Odeme.php', extra: { tip: values.tip } },
                '4': { url: 'functions/bank/turkiye_finans/request.php', extra: { tip: values.tip } }
            };

            const config = posConfig[values.pos];
            if (config) {
                const form = createForm(config.url, {
                    hesap: values.hesap,
                    cariOdeme: '',
                    taksit_sayisi: values.taksit,
                    banka_id: values.banka_id,
                    toplam: values.toplam,
                    lang: values.lang,
                    uye_id: values.uye_id,
                    odemetaksit: values.taksit,
                    vade: values.vade,
                    cardNumber: values.card,
                    cardName: values.cardName,
                    expMonth: values.expMonth,
                    expYear: values.expYear,
                    cvCode: values.cvv,
                    odemetutar: values.sonuc,
                    ...config.extra
                });
                document.body.appendChild(form);
                form.submit();
            }
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Uyarı',
                text: values.card.length < 16 
                    ? 'Kart numarası 16 karakter olmalıdır!' 
                    : 'Lütfen tüm alanları doldurunuz!'
            });
        }
    }

    function createForm(action, fields) {
        const form = document.createElement('form');
        form.method = 'post';
        form.action = action;
        for (const [name, value] of Object.entries(fields)) {
            if (value !== undefined) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            }
        }
        return form;
    }
</script>