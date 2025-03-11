<?php
require '../functions/admin_template.php';
require '../functions/functions.php';
ini_set('display_errors', 1);  // Hataları ekrana göster
error_reporting(E_ALL);   
$currentPage = 'onay';
$template = new Template('Nokta Elektronik - Onay', $currentPage);

$template->head();
$database = new Database();
date_default_timezone_set('Europe/Istanbul');

if (isset($_GET["siparis-numarasi"])){
    $siparis_no = $_GET['siparis-numarasi'];
    $sip = $database->fetch("SELECT * FROM b2b_siparisler WHERE siparis_no = '$siparis_no' ");
    $sip_id = $sip['id'];
    $havale = $sip["odeme_sekli"];

    if ($havale == "Havale/EFT") {
        echo "<script>";
        echo "Swal.fire({";
        echo "  title: 'Hatırlatma',";
        echo "  icon: 'info',";
        echo "  text: '1 saat içerisinde ücreti ödenmeyen Havale/EFT siparişleri iptal edilir.',";
        echo "});";
        echo "</script>";
    }
}

$uye_id = $_SESSION["id"];

echo '<div style="display: none">';
foreach ($_POST as $key => $value) {
    echo $key . ': ' . $value . '<br>';
}
echo "</div>";

?>

<body>
<style>
    .bs-stepper-header .step.active-step button {
    color: #697a8d; /* Etkin adımın metin rengi */
}

/* İsteğe bağlı olarak diğer stilleri de güncelleyebilirsiniz */
.bs-stepper-header .step button {
    color: red; /* Diğer adımların metin rengi */
}
</style>
<?php $template->header(); ?>
        <div class="container flex-grow-1 container-p-y mt-5">
            <!-- Checkout Wizard -->
            <div id="wizard-checkout" class="bs-stepper wizard-icons wizard-icons-example mb-5">
                <?php if(isset($_GET["siparis-numarasi"])){ ?>
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
                        <button type="button" class="step-trigger" style="color: #3498db !important;" >
                            <span class="bs-stepper-icon"><i class="fas fa-check"></i></span>
                            <span class="bs-stepper-label">Onay</span>
                        </button>
                    </div>
                </div>
                <div class="bs-stepper-content border-top">
                    <form id="wizard-checkout-form" onSubmit="return false">
                        <!-- Confirmation -->
                  <div >
                    <div class="row mb-3">
                      <div class="col-12 col-lg-8 mx-auto text-center mb-3">
                        <h4 class="mt-2">Teşekkürler! 😇</h4>
                          <?php if(isset($_GET["siparis-numarasi"])){ ?>
                        <p><a href="javascript:void(0)">#<?= $sip['siparis_no']?></a> Numaralı siparişiniz alınmıştır!</p>
                        <p><a href="mailto:john.doe@example.com"><?= $sip['uye_email']?></a>mail adresinize sipariş bilgilerinizi gönderdik. Mail 2 dakika içerinde size ulaşmadıysa lütfen spam klasörünü kontrol ediniz.</p>
                        <p><span class="fw-medium"><i class="bx bx-time-five me-1"></i> Sipariş Tarihi:&nbsp;</span><?= $sip['tarih']?></p>
                          <?php } ?>
                      </div>
                      <!-- Confirmation details -->
                      <?php if(isset($_GET["siparis-numarasi"])){ ?>
                      <div class="col-12">
                        <ul class="list-group list-group-horizontal-md">
                          <li class="list-group-item flex-fill p-4 text-heading">
                            <h6 class="d-flex align-items-center gap-1"><i class="bx bx-map"></i> Adres</h6>
                            <address class="mb-0">
                            <?= $sip['teslimat_ad']?> <?= $sip['teslimat_soyad']?> <br />
                            <?= $sip['teslimat_adres']?><br />
                            </address>
                            <p class="mb-0 mt-3">
                                <?= $sip['teslimat_telefon']?>
                            </p>
                          </li>
                          <li class="list-group-item flex-fill p-4 text-heading">
                            <h6 class="d-flex align-items-center gap-1"><i class="bx bx-credit-card"></i> Fatura Adresi</h6>
                            <address class="mb-0">
                            <?php  echo $sip['uye_ad'];?> <?php echo $sip['uye_soyad']; ?> <br />
                                <?php  echo $sip['uye_adres']; ?><br />
                            </address>
                            <p class="mb-0 mt-3">
                                <?php  echo $sip['uye_tel'];?>
                            </p>
                          </li>
                          <li class="list-group-item flex-fill p-4 text-heading">
                            <h6 class="d-flex align-items-center gap-1"><i class="bx bxs-ship"></i> Kargo Firması</h6>
                            <p class="fw-medium mb-3">
                                <?php
                                if($sip["kargo_firmasi"] == 0){
                                    echo "Mağazadan Teslim Alınacak";
                                }elseif ($sip["kargo_firmasi"] == 1){
                                    echo "Özel Kargo";
                                }elseif ($sip["kargo_firmasi"] == 2){
                                    echo "Yurtiçi Kargo";
                                }
                                ?>
                            </p>
                          </li>
                        </ul>
                      </div>
                    </div>

                    <div class="row">
                      <!-- Confirmation items -->
                      <div class="col-xl-9 mb-3 mb-xl-0">
                        <ul class="list-group">
                            <?php
                                while ($surun = $database->fetch("SELECT * FROM b2b_siparis_urunler WHERE sip_id = $sip_id")) {
                                    $urun_id = $surun['urun_id'];

                                    $urun = $database->fetch("SELECT * FROM nokta_urunler WHERE id = '$urun_id' ");
                                    $blkodu = $urun['BLKODU'];

                                    $foto = $database->fetch("SELECT foto FROM nokta_urunler_resimler WHERE urun_id = '$blkodu' LIMIT 1");
                                ?>
                              <li class="list-group-item p-4">
                                <div class="d-flex gap-3">
                                  <div class="flex-shrink-0">
                                    <img src="assets/images/urunler/<?php echo $foto['foto']; ?>" alt="google home" class="w-px-75">
                                  </div>
                                  <div class="flex-grow-1">
                                    <div class="row">
                                      <div class="col-md-8">
                                        <a href="javascript:void(0)" class="text-body">
                                          <p><?= $urun['UrunAdiTR'] ?></p>
                                        </a>
                                        <div class="text-muted mb-1 d-flex flex-wrap"><span class="me-1">Adet:</span> <a  class="me-3"><?= $surun['adet'] ?></a></div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="text-md-end">
                                            <div class="my-2 my-lg-4">
                                                <span class="text-primary">

                                                    <?php
                                                        $birimi = $urun["DOVIZ_BIRIMI"];
                                                        if($urun['DSF4'] == NULL || $urun['DSF4'] == '' ){$birimi = '₺';}
                                                        echo $birimi;
                                                    ?>
                                                    <?= $surun['birim_fiyat'] ?>
                                                </span>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </li>
                            <?php  } ?>
                        </ul>
                      </div>
                      <!-- Confirmation total -->
                      <div class="col-xl-3">
                        <div class="border rounded p-4 pb-3">
                          <!-- Price Details -->
                          <h6>Fiyat Detay</h6>
                          <dl class="row mb-0">

                            <dt class="col-6 fw-normal">Sepet Toplamı</dt>
                            <dd class="col-6 text-end">₺<?php echo $sip['sepet_toplami'] ; ?></dd>

                            <dt class="col-sm-6 fw-normal">KDV</dt>
                            <dd class="col-sm-6 text-end">₺<?php echo $sip['sepet_kdv'];?></dd>

                            <dt class="col-sm-6 fw-normal">İndirim</dt>
                            <dd class="col-sm-6 text-end"><?php echo $sip['indirim'];?></dd>

                            <dt class="col-sm-6 fw-normal">Kargo Ücreti</dt>
                            <dd class="col-sm-6 text-end">₺<?php echo $sip['kargo_ucreti'];?></dd>
                          </dl>
                          <hr class="mx-n4">
                          <dl class="row mb-0">
                            <dt class="col-6">Toplam</dt>
                            <dd class="col-6 fw-medium text-end mb-0">₺<?php echo $sip['toplam']; }?></dd>
                          </dl>
                        </div>
                      </div>
                    </div>
                  </div>
                    </form>
                </div>
                <?php } ?>
                <?php if(isset($_GET["cari_odeme"])){ ?>
                    <div class="bs-stepper-content border-top">
                        <form id="wizard-checkout-form" onSubmit="return false">
                            <!-- Confirmation -->
                            <div >
                                <div class="row mb-3">
                                    <div class="col-12 col-lg-8 mx-auto text-center mb-3">
                                        <h4 class="mt-2">Teşekkürler! 😇</h4>
                                            <p>Ödemeniz onaylanmıştır.  Sağlıklı günler dileriz...</p>
                                            <p><span class="fw-medium"><i class="bx bx-time-five me-1"></i> Ödeme Tarihi:&nbsp;</span><?php echo date('d.m.Y');?></p>
                                    </div>
                                    <!-- Confirmation details -->
                                </div>
                            </div>
                        </form>
                    </div>
                <?php } ?>
            </div>
        </div>
      <?php $template->footer(); ?>
</body>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/app.js"></script>

<?php 
//Param Pos
if (isset($_POST['TURKPOS_RETVAL_Sonuc_Str'])) {
  $sonucStr = $_POST['TURKPOS_RETVAL_Sonuc_Str'];
  $dekont = $_POST['TURKPOS_RETVAL_Dekont_ID'];
  $tutar = $_POST['TURKPOS_RETVAL_Odeme_Tutari'];
  $tutar = $tutar / 100;
  $pos_id = 1;
  $basarili = 1;
  $stmt = "INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)";
  $params = [
    ':uye_id' => $uye_id,
    ':pos_id' => $pos_id,
    ':islem' => $sonucStr,
    ':tutar' => $tutar,
    ':basarili' => $basarili
  ];
  $database->insert($stmt, $params);

}
//Garanti Pos
if (isset($_POST['errmsg'])) {
  $sonucStr = $_POST['mderrormessage'];
  $tutar = $_POST["txnamount"];
  $tutar = $tutar / 100;
  $pos_id = 2;
  $basarili = 1;
  $stmt = "INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)";
  $params = [
    ':uye_id' => $uye_id,
    ':pos_id' => $pos_id,
    ':islem' => $sonucStr,
    ':tutar' => $tutar,
    ':basarili' => $basarili
  ];
  $database->insert($stmt, $params);
}
//Kuveyt POS
if(isset($_POST['AuthenticationResponse'])) {
  $data = urldecode($_POST['AuthenticationResponse']);
  $xml = simplexml_load_string($data);
  $responseMessage = (string) $xml->ResponseMessage;
  $tutar = $xml->VPosMessage->Amount;
  $tutar = $tutar / 100;
  $pos_id = 3;
  $basarili = 1;
  $stmt = "INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)";
  $params = [
    ':uye_id' => $uye_id,
    ':pos_id' => $pos_id,
    ':islem' => $responseMessage,
    ':tutar' => $tutar,
    ':basarili' => $basarili
  ];
  $database->insert($stmt, $params);
}

?>