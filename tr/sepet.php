<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'sepet';
$template = new Template('Nokta - Sepetim', $currentPage);

$template->head();
$database = new Database();
sessionControl();
$uye_id = $_SESSION["id"];
echo '<div style="display: none">';
foreach ($_POST as $key => $value) {
    echo $key . ': ' . $value . '<br>';
}
echo "</div>";

// Fetch data from database
$result = $database->fetchAll("SELECT * FROM uye_sepet");
function formatNumber1($number) {
    // Veritabanından gelen sayı formatı kontrol et
    if (!is_numeric($number)) { return null; }

    // Sayıyı formatla
    $formattedNumber = number_format($number, 4, ',', '.');

    // Noktadan sonra 3. ve 4. karakter sıfır ise 2 haneli olsun, değilse 3 veya 4 haneli olsun
    $decimalPart = substr($formattedNumber, -4, 4);
    if ($decimalPart[2] === '0' && $decimalPart[3] === '0') {
        $formattedNumber = substr($formattedNumber, 0, -2); // Noktadan sonraki kısmı kaldır
    }
    return $formattedNumber;
}
function gelenFiyatDuzenle1($sayi) {
    if (empty($sayi)) {
        return null;
    }

    // Virgül varsa noktaya çevir
    $sayi = str_replace(',', '.', $sayi);

    // Sayının formatını kontrol et
    if (!preg_match('/^\d+(\.\d{1,4})?$/', $sayi)) {
        return null;
    }

    // Sayıyı DECIMAL(13,2) formatına getir
    $sayi = number_format((float)$sayi, 4, '.', '');

    return $sayi;
}

$session_id = $_SESSION['id'];
$uye = $database->fetch("SELECT * FROM uyeler WHERE id = :session_id", ['session_id' => $session_id]);
$uyeFiyat = $uye['fiyat'];

$urun123 = $database->fetchAll("SELECT DISTINCT f.id AS sepet_id, f.uye_id, f.urun_id, u.stok
                                FROM uye_sepet AS f JOIN nokta_urunler AS u ON f.urun_id = u.id
                                WHERE f.uye_id = :session_id", [ 'session_id' => $session_id ]);

foreach($urun123 as $row){
    $id = $row["sepet_id"]; // uye_sepet tablosundaki sepet_id
    if($row["stok"] < 1) {
        // Stok 1'in altındaysa sepetten o ürünü çıkar
        $q = $database->delete("DELETE FROM uye_sepet WHERE id = :id", [
            'id' => $id
        ]);
    }
}
?>
<style>
    @media (max-width: 992px) {#sepet_yonlendirmeleri{display: none}}
    .mavi-arkaplan{background-color: #430666;color: white;font-weight: unset;}
</style>
<body>
<?php $template->header(); 
if(isset($_GET['hata']) && $_GET['hata'] == 0) {
    echo"<script>
            Swal.fire({
                icon: 'error',
                title: 'Ödeme alınırken hata oluştu.',
                showConfirmButton: false,
                timer: 2000
            });
        </script>";
}
?>
<!-- Content -->
<div class="container flex-grow-1 container-p-y mt-5">
<!-- Checkout Wizard -->
  <div id="wizard-checkout" class="bs-stepper wizard-icons wizard-icons-example mb-5">
    <div class="bs-stepper-header m-auto border-0 py-4" id="sepet_yonlendirmeleri" >
      <div class="step" >
          <button type="button" class="step-trigger"  style="color: #3498db !important;">
              <span class="bs-stepper-icon"><i class="fas fa-shopping-cart"></i></span>
              <span class="bs-stepper-label">Sepet</span>
          </button>
      </div>
      <div class="line"><i class="bx bx-chevron-right"  style="color: #3498db !important;"></i></div>
      <div class="step" >
          <button type="button" class="step-trigger">
              <span class="bs-stepper-icon"><i class="fas fa-credit-card"></i></span>
              <span class="bs-stepper-label">Ödeme</span>
          </button>
      </div>
      <div class="line"><i class="bx bx-chevron-right"></i></div>
      <div class="step" >
          <button type="button" class="step-trigger">
              <span class="bs-stepper-icon"><i class="fas fa-check"></i></span>
              <span class="bs-stepper-label">Onay</span>
          </button>
      </div>
    </div>
  <div class="bs-stepper-content border-top">
        <!-- Cart -->
        <div >
          <div class="row">
            <!-- Cart left -->
            <div class="col-xl-8 mb-3 mb-xl-0">
              <!-- Shopping bag -->
              <h6 class="rounded p-2 mavi-arkaplan mb-3"><i class="fa-solid fa-boxes-stacked me-2"></i>Ürünlerim</h6>
                <ul class="list-group mb-3" id="urunler_listeleme_alani">
                    <?php
                    $urun = $database->fetchAll("
                                SELECT DISTINCT f.id AS sepet_id, f.uye_id, f.urun_id, f.adet, f.sepet_ozel_indirim, f.ozel_fiyat, u.*, nm.title AS marka_adi
                                FROM uye_sepet AS f
                                JOIN nokta_urunler AS u ON f.urun_id = u.id
                                LEFT JOIN nokta_urun_markalar AS nm ON u.MarkaID = nm.id
                                WHERE f.uye_id = :session_id
                            ", ['session_id' => $session_id]);
                    if (empty($urun)) {?>
                        <input hidden id="sepet_bos" value="1">
                        <div class="alert alert-info text-center" role="alert">Sepetiniz boş!</div>
                    <?php
                    } else {
                        $BLKODU_array = [];
                        $kategori_array = [];
                        $marka_array = [];
                        $adet_array = [];
                    foreach($urun as $row){

                        $id = $row["id"];
                        $BLKODU = $row["BLKODU"];
                        $BLKODU_array[] = $id;
                        $kategori = $row["KategoriID"];
                        $kategori_array[] = $kategori;
                        $marka = $row["marka_adi"];
                        $marka_array[] = $marka;
                        $adetler = $row["adet"];
                        $adet_array[] = $adetler;

                        $result = $database->fetch("SELECT KResim FROM nokta_urunler_resimler WHERE UrunID = :urun_id LIMIT 1", ['urun_id' => $id]);
                        if (!empty($result['KResim'])) {
                            $imageSrc = 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/' . $result["KResim"];
                        }else {
                            $imageSrc = 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/gorsel_hazirlaniyor.jpg';
                        }

                        if ($urun) {
                            $sepet_id = $row["sepet_id"];
                            $urunDesi = str_replace(',', '.', $row["desi"]);
                            ?>
                            <li id="sepet_<?= $sepet_id; ?>" class="list-group-item p-2">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0 d-flex align-items-center">
                                        <div>
                                            <img src="<?= $imageSrc; ?>" alt="<?= $row["UrunAdiTR"] ?>" width="100px">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <p class="me-3"><a href="tr/urun-detay?product_id=<?= $row['seo_link']; ?>" style="" class="text-decoration-none text-body fw-bold"><?= $row["UrunAdiTR"] ?></a></p>
                                                <div class="text-muted d-flex flex-wrap">
                                                    <span class="me-1">Stok Kodu:</span>
                                                    <a href="javascript:void(0)" class="me-3 text-decoration-none"><?= $row["UrunKodu"] ?></a>
                                                </div>
                                                <div class="text-muted mb-2 d-flex flex-wrap">
                                                    <span class="me-1">Marka:</span>
                                                    <a href="javascript:void(0)" class="me-3 text-decoration-none"><?= $marka ?></a>
                                                </div>
                                                <div class="read-only-ratings mb-3" data-rateyo-read-only="true"></div>
                                                <input hidden id="sepet_id" value="<?= $sepet_id ?>">
                                                <input hidden id="stok_adet" value="<?= $row['stok'] ?>">

                                                <?php if(empty($row['miktar_seciniz'])) { ?>
                                                    <input type="number" class="form-control form-control-sm w-px-100 mt-2 sepet_adet" value="<?= $row['adet'] ?>" min="1" max="<?= $row['stok'] ?>" oninput="validateQuantity(<?= $sepet_id; ?>, this.value, <?= $row['stok']; ?>);">
                                                <?php } else {
                                                    $miktarlar = $row['miktar_seciniz'];
                                                    // Virgülle ayrılmış değerleri diziye çevir
                                                    $miktarDizisi = explode(",", $miktarlar);
                                                    ?>
                                                    <select class="form-control form-control-sm w-px-100 mt-2 sepet_adet" id="output" onchange="validateQuantity(<?= $sepet_id; ?>, this.value, <?= $row['stok']; ?>);">
                                                        <?php $adetOptionDisplayed = false; ?>
                                                        <?php foreach ($miktarDizisi as $miktar): ?>
                                                            <?php if ($miktar <= $row["stok"]): ?>
                                                                <?php if (!$adetOptionDisplayed && $row['adet'] < $miktar): ?>
                                                                    <option value="<?= $miktar ?>"><?= $miktar ?></option>
                                                                    <?php $adetOptionDisplayed = true; ?>
                                                                <?php endif; ?>
                                                                <option value="<?= $miktar; ?>" <?php if ($miktar == $row['adet']) echo "selected"; ?>><?= $miktar; ?></option>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php } ?>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-md-end">
                                                    <div class="my-2 my-md-4 mb-md-5">
                                                        <span class="" style="color: #f29720">
                                                            <?php
                                                            $fiyat1 = !empty($row['ozel_fiyat']) ? $row['ozel_fiyat'] : (!empty($row["DSF".$uyeFiyat]) ? $row["DSF".$uyeFiyat] : $row["KSF".$uyeFiyat]);
                                                            ?>
                                                            <span class="birim_doviz"><?= !empty($row["DSF4"]) || !empty($row["DSF3"]) ? $row["DOVIZ_BIRIMI"] : "₺"; ?></span>
                                                            <span class="birim_adet"><?= formatNumber1($fiyat1); ?></span> + KDV
                                                        </span>
                                                        <?php if(!empty($row["DSF4"])){ ?>
                                                            /
                                                        <s class="text-muted">
                                                            <?= !empty($row["DSF4"]) || !empty($row["DSF3"]) ? $row["DOVIZ_BIRIMI"] : "₺";
                                                            $fiyat = !empty($row["DSF4"]) ? $row["DSF4"] : $row["KSF4"];
                                                            echo formatNumber1($fiyat);  ?>
                                                        </s>
                                                        <?php } ?>
                                                    </div>
                                                    <?php
                                                    $fiyat1 = gelenFiyatDuzenle1($fiyat1);
                                                    $kdvsi = 0.01 * (float)$row['kdv'] * (float)$fiyat1; ?>

                                                    <input type="text" hidden class="birim_kdv" value="<?= $kdvsi ; ?>">
                                                    <input type="text" hidden class="birim_desi" value="<?= $urunDesi ; ?>">
                                                    <button type="button" class="btn btn-sm btn-label-primary mt-md-2 border" onclick="sepetKaldir(<?= $sepet_id; ?>);">Sil</button>
                                                    <button type="button" class="btn btn-sm btn-label-primary mt-md-2 border favori-buton" data-product-id="<?= $id ?>">Favorilere Ekle</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                    <?php }}} ?>
                    <input hidden id="blkodu_dizi" value="<?= json_encode($BLKODU_array) ?>">
                    <input hidden id="kategori_dizi" value="<?= json_encode($kategori_array) ?>">
                    <input hidden id="marka_dizi" value="<?= json_encode($marka_array) ?>">
                    <input hidden id="adet_dizi" value="<?= json_encode($adet_array) ?>">
                </ul>
               <!-- Select address -->
                  <h6 class="rounded p-2 mavi-arkaplan mb-3 mt-4"><i class="fa-solid fa-map-location-dot me-2"></i>Adreslerim</h6>
                  <!--<div class="col-md-3"><button type="button" class="btn btn-outline-dark mb-4" data-bs-toggle="modal" data-bs-target="#chooseNewAddress">Yeni Adres Ekle</button></div>-->

                <div class="row mb-3">
                  <?php
                    $session_id = $_SESSION['id'];
                    $uye = $database->fetchAll("
                    SELECT u.*, il.*, ilce.*, ad.*
                    FROM uyeler AS u
                    JOIN b2b_adresler AS ad ON u.id = ad.uye_id
                    JOIN iller AS il ON ad.il = il.il_id
                    JOIN ilceler AS ilce ON ad.ilce = ilce.ilce_id
                    WHERE u.id = :session_id AND ad.adres_turu = 'Teslimat' AND ad.aktif = '1'
                    ", ['session_id' => $session_id]);
                    foreach($uye as $row){
                  ?>
                  <div class="col-md mb-md-0 mb-2">
                    <div class="form-check custom-option custom-option-basic">
                      <label class="form-check-label custom-option-content" for="customRadioAddress1">
                        <span class="custom-option-header mb-2">
                          <span class="fw-medium mb-0"><?= $row["ad"]?> <?= $row["soyad"]?> <span class="fw-semibold">(Teslimat Adresi)</span></span>
                        </span>
                        <span class="custom-option-body">
                          <small>
                            <?= $row["adres"] ?><br>Telefon : <?= $row["tel"] ?><br>
                            <?= $row["il_adi"] ?> / <?= $row["ilce_adi"] ?>
                          </small>
                          <span class="my-2 border-bottom d-block"></span>
                          <span class="d-flex">
                            <a class="me-2 text-decoration-none" style="font-size: 14px" href="https://www.noktaelektronik.com.tr/tr/bilgiler"><i class="fa-solid fa-pen-to-square me-1"></i>Düzenle</a>
                            <a class="ms-2 text-decoration-none adres-btn" style="font-size: 14px"><i class="fa-solid fa-circle-plus me-1"></i>Yeni Adres Ekle</a>
                          </span>
                        </span>
                      </label>
                    </div>
                  </div>
                  <?php } ?>
                  <?php
                    $session_id = $_SESSION['id'];
                    $uye = $database->fetchAll(" SELECT u.*, il.*, ilce.* FROM uyeler AS u
                        JOIN iller AS il ON u.il = il.il_id
                        JOIN ilceler AS ilce ON u.ilce = ilce.ilce_id
                        WHERE u.id = :session_id ", ['session_id' => $session_id]);
                    foreach($uye as $row){
                  ?>
                  <div class="col-md">
                    <div class="form-check custom-option custom-option-basic">
                        <label class="form-check-label custom-option-content" for="customRadioAddress2">
                            <span class="custom-option-header mb-2">
                                <span class="fw-medium mb-0"><?= $row["ad"]?> <?= $row["soyad"]?> <span class="fw-semibold">(Fatura Adresi)</span></span>
                            </span>
                            <span class="custom-option-body">
                            <small>
                                <?= $row["adres"] ?><br>Telefon : <?= $row["tel"] ?><br>
                                <?= $row["il_adi"] ?> / <?= $row["ilce_adi"] ?>
                            </small>
                            <span class="my-2 border-bottom d-block"></span>
                            <span class="d-flex">
                                <i style="font-size: 20px" class="fa-solid fa-circle-info" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="*Fatura bilgilerinizde değişiklik yapmak isterseniz satış temsilcinize danışınız veya destek@noktaelektronik.com.tr adresine mail atabilirsiniz.">
                                </i>
                            </span>
                        </label>
                    </div>
                  </div>
                  <?php } ?>
                </div>
                <!-- Choose Delivery -->
                <h6 class="rounded p-2 mavi-arkaplan mb-3 mt-4"><i class="fa-solid fa-truck-ramp-box me-2"></i>Kargo Seçiniz</h6>
                <div class="row mt-2">
                    <input type="text" hidden name="toplamDesiBirimi" id="toplamDesiBirimi" value="">
                    <div class="col-md mb-md-0 mb-2">
                        <div class="form-check custom-option custom-option-icon position-relative ">
                            <label class="form-check-label custom-option-content" for="customRadioDelivery1">
                                <span class="custom-option-body">
                                    <i class="bx bx-user bx-lg"></i>
                                    <span class="custom-option-title mb-1">Mağazadan Teslim Alacağım</span>
                                    <small class="kargo_ucreti">Ücretsiz</small>
                                </span>
                                <input name="customRadioIcon" class="form-check-input" type="radio" value="0.00" id="customRadioDelivery1" onchange="updateKargoUcreti(this)">
                            </label>
                        </div>
                    </div>
                    <?php
                        $idsi = 3;
                        $kar1 = $database->fetch("SELECT * FROM b2b_kargo WHERE id = :id ", ['id' => $idsi]);
                        $yayinDurum = $kar1['yayin_durumu'];
                        if ($yayinDurum == 1) {
                        ?>
                            <div class="col-md mb-md-0 mb-2">
                                <div class="form-check custom-option custom-option-icon position-relative">
                                    <label class="form-check-label custom-option-content" for="customRadioDelivery2">
                                        <span class="custom-option-body">
                                            <i class="bx bx-crown bx-lg"></i>
                                            <span class="custom-option-title mb-1">Özel Kargo</span>
                                            <small>₺</small>
                                            <small class="kargo_ucreti">0.00</small>
                                        </span>
                                        <input name="customRadioIcon" class="form-check-input" type="radio" value="0.00" id="customRadioDelivery2" onchange="updateKargoUcreti(this)">
                                    </label>
                                </div>
                            </div>
                        <?php } ?>
                    <?php
                        $idsi = 1;
                        $kargo = $database->fetch("SELECT * FROM b2b_kargo WHERE id = :id", [
                            'id' => $idsi
                        ]);
                        $yayinDurum = $kargo['yayin_durumu'];
                        if ($yayinDurum == 1) {

                    ?>
                        <div class="col-md">
                            <div class="form-check custom-option custom-option-icon position-relative">
                                <label class="form-check-label custom-option-content" for="customRadioDelivery3">
                                    <span class="custom-option-body">
                                        <!--<i class="bx bxl-telegram bx-lg"></i>-->
                                        <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/ikons/kargo-icon.svg" width="140px">
                                        <span class="custom-option-title mb-1 mt-3">Yurtiçi Kargo</span>
                                        <small>₺</small>
                                        <small class="kargo_ucreti">0.00</small>
                                    </span>
                                    <input name="customRadioIcon" class="form-check-input" type="radio" value="0.00" id="customRadioDelivery3" onchange="updateKargoUcreti(this)">
                                </label>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            </div>
            <!-- Cart right -->
            <div class="col-xl-4">
              <div class="border rounded p-4 mb-3 pb-3">
                <!-- Offer -->
                <h6><i class="fa-solid fa-tags me-2"></i>İndirim Kodu</h6>
                <div class="row g-3 mb-3">
                  <div class="col-8 col-xxl-8 col-xl-12">
                    <input type="text" class="form-control" name="promosyon_kodu_gir" id="promosyon_kodu_gir" placeholder="Promosyon kodu gir" aria-label="Promosyon kodu gir">
                  </div>
                  <div class="col-4 col-xxl-4 col-xl-12">
                    <div class="d-grid">
                      <button type="button" class="btn btn-outline-dark promosyon_onayla">Uygula</button>
                    </div>
                  </div>
                </div>
                  <?php
                  $uye_id = $_SESSION['id'];
                  $conditions = ["1"];
                  $params = [];
                  
                  if (isset($uye_id) && trim($uye_id) !== '') {
                      $conditions[] = "(kullanacak_uye_id IS NULL OR kullanacak_uye_id = :uye_id OR FIND_IN_SET(:uye_id, kullanacak_uye_id) > 0)";
                      $conditions[] = "(uye_id IS NULL OR uye_id != :uye_id AND !(FIND_IN_SET(:uye_id, uye_id) > 0))";
                      $conditions[] = "aktif = 1";
                      $params['uye_id'] = $uye_id;
                  }
                  
                  $query = "SELECT * FROM b2b_promosyon WHERE " . implode(" AND ", $conditions);
                  $promotions = $database->fetchAll($query, $params);

                  if ($promotions) { ?>
                      <h5 class="fst-italic text-decoration-underline"><a href="tr/kuponlarim" class="text-decoration-none text-black">Aktif İndirim Kuponlarım</a></h5>
                      <?php
                      foreach ($promotions as $row) {
                          ?>
                          <a><span style="font-size: 14px" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?= $row["aciklama"] ?>">İndirim Kodu: <?= $row["promosyon_kodu"] ?></span></a><br>
                      <?php } } ?>
                <hr class="mx-n4">
                <!-- Price Details -->
                <h6>Döviz Kurları</h6>
                    <?php
                    $dolar_id = 2;
                    $euro_id = 3;

                    $dolar = $database->fetch("SELECT * FROM b2b_kurlar WHERE id = :id", ['id' => $dolar_id]);
                    $euro = $database->fetch("SELECT * FROM b2b_kurlar WHERE id = :id", ['id' => $euro_id]); ?>
                    <dl class="row mb-0">
                        <dt class="col-6 fw-normal">Dolar Kuru</dt>
                        <dd id="dolar_satis" class="col-6 text-end "><?php $dolarDuzgun = formatNumber1($dolar['satis']); echo $dolarDuzgun; ?></dd>

                        <dt class="col-sm-6 fw-normal">Euro Kuru</dt>
                        <dd id="euro_satis" class="col-sm-6 text-end "><?php $euroDuzgun = formatNumber1($euro['satis']); echo $euroDuzgun;  ?></dd>
                    </dl>

                    <hr class="mx-n4">
                    <dl class="row mb-0">
                        <dt class="col-6 fw-normal">Sepet Toplamı</dt>
                        <dd id="yanSepetToplami" class="col-6 text-end yanSepetToplami">0,00</dd>

                        <dt class="col-sm-6 fw-normal">KDV</dt>
                        <dd id="yanSepetKDVToplami" class="col-sm-6 text-end yanSepetKdv">0,00</dd>

                        <dt class="col-sm-6 fw-normal">İndirim</dt>
                        <dd id="yanIndirim" class="col-sm-6 text-end indirim-alani yanIndirim">0,00</dd>

                        <dt class="col-6 fw-normal">Kargo Ücreti</dt>
                        <dd id="yanKargo" class="col-6 text-end yanKargo">0,00</dd>
                        <form id="paymentForm" method="post" action="tr/odeme">
                            <input type="hidden" name="promosyonKodu" id="hiddenPromosyonKodu">
                            <input type="hidden" name="yanSepetToplami" id="hiddenYanSepetToplami" value="0,00">
                            <input type="hidden" name="yanSepetKdv" id="hiddenYanSepetKdv" value="₺0,00">
                            <input type="hidden" name="yanIndirim" class="hiddenindirim-alani" value="₺0,00">
                            <input type="hidden" name="yanKargo" id="hiddenYanKargo" value="₺0,00">
                            <input type="hidden" name="selectedDeliveryOption" id="selectedDeliveryOption" value="">
                            <input type="hidden" name="toplamDesiBirimi1" id="toplamDesiBirimi1" value="">
                        </dl>
                        <hr class="mx-n4">
                        <dl class="row mb-0">
                        <dt class="col-6">Toplam</dt>
                        <dd id="yanToplam" class="col-6 fw-medium text-end mb-0">$00,00</dd>

                        <input type="hidden" name="yanToplam" id="hiddenYanToplam" value="₺0,00">
                        <input type="hidden" name="hiddenUyeID" id="hiddenUyeID" value="<?= $uye_id ?>">
                    </dl>
              </div>
                    <input type="hidden" id="devamEt" name="devamEt">
                </form>
              <div class="d-grid devam_et_btn">
                  <button style="background-color: #f29720; color: white" class="btn" onclick="odemeye_gec()">Ödemeye Geç<i class="fa-solid fa-circle-right ms-2"></i></button>
              </div>
            </div>
          </div>
        </div>
  </div>
</div>
<!--/ Checkout Wizard -->
</div>
<!-- Modal Adres Ekleme Formu -->
<div class="modal fade" data-bs-backdrop="static" id="basvuruModal" tabindex="-1" role="dialog" aria-labelledby="basvuruModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="basvuruModalLabel">Teslimat Adresi Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="applicationForm" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <label for="adres_basligi" class="form-label">Adres Başlığı*</label>
                            <input type="text" class="form-control" id="adres_basligi" required>
                            <input style="display: none;" type="text" id="adresId" name="adresId">
                            <input style="display: none;" type="text" id="uyeId" name="uyeId" value="<?= $session_id ?>">
                        </div>
                        <div class="col-sm-6">
                            <label for="ad" class="form-label">Ad*</label>
                            <input type="text" class="form-control" id="ad1" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="soyad" class="form-label">Soyad*</label>
                            <input type="text" class="form-control" id="soyad1" required>
                        </div>
                        <div class="col-sm-12">
                            <label for="tel" class="form-label">Telefon*</label>
                            <input type="text" class="form-control" id="tel1">
                        </div>
                        <div class="col-sm-12">
                            <label for="adres" class="form-label">Adres* </label>
                            <input type="text" class="form-control" id="adres1">
                        </div>
                        <div class="col-sm-6">
                            <label for="ulke" class="form-label">Ülke*  </label>
                            <select class="form-control form-control-sm">
                                <option>Türkiye</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="il" class="form-label">İl*  </label>
                            <select id="il" name="il" class="form-control form-control-sm">

                                <?php $iller = $database->fetchAll("SELECT * FROM iller");
                                    if ($iller) {
                                    foreach ($iller as $row) { ?>
                                        <option id="ilce_id" hidden></option>
                                        <option value="<?= $row['il_id'] ?>"><?= $row["il_adi"] ?></option>
                                    <?php }} ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="ilce" class="form-label">İlçe*</label>
                            <select class="form-control form-control-sm" id="ilce" name="ilce"></select>
                        </div>
                        <div class="col-sm-6">
                            <label for="posta_kodu" class="form-label">Posta Kodu* </label>
                            <input type="text" class="form-control" id="posta_kodu1">
                        </div>
                    </div>
                    <button class="w-100 btn btn-primary btn-lg my-4" style="background-color:#f29720; border-color:#f29720" type="submit">Gönder</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $template->footer(); ?>
</body>
</html>
<script src="assets1/js/wizard-ex-checkout.js"></script>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/app.js"></script>
<script>
    $(document).ready(function () {
        $('.favori-buton').on('click', function (event) {
            event.preventDefault();
            var productId = $(this).data('product-id');
            var uye_id = <?= $_SESSION["id"] ?>;
            var type = 'favori';
            var that = $(this);
            $.ajax({
                type: 'POST',
                url: 'functions/edit_info.php',
                data: {
                    product_id: productId,
                    uye_id: uye_id,
                    type: type
                },
                success: function (response) {
                    // Handle the response, you can update the UI as needed
                    if (response.includes('added')) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Ürün Favorilere Eklendi!',
                            toast: true,
                            position: 'top-start',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else if (response.includes('removed')) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Ürün Favorilerden Kaldırıldı!',
                            toast: true,
                            position: 'top-start',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            });
        });
    });
</script>
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
<!-- adres başlangıcı -->
<script>
    function loadIlceler() {
        var il_id = $('#il').val();
        var ilce = $('#ilce_id').val();
        $.ajax({
            url: "functions/adres/ile_gore_ilce.php",
            type: "POST",
            data: {
                il_id: il_id,
                ilce: ilce
            },
            cache: false,
            success: function(result) {
                $("#ilce").html(result);
            }
        });
    }
    // Execute the function when the page loads
    $(document).ready(function() {
        // Check if il is not empty, then load ilceler
        if ($('#il').val() !== '') {
            loadIlceler();
        }
        // Attach the function to the 'change' event of #il
        $('#il').on('change', loadIlceler);
    });
</script>
<script>
    $(document).ready(function() {
        var modalMode = ''; // Variable to track modal mode: 'update' or 'insert'
        $('.adres-btn').click(function() {
            var adresId = $(this).data('adres-id');
            $('#applicationForm')[0].reset();
            // Determine modal mode based on whether an address ID is provided
            modalMode = 'insert';
            // Clear certain form fields for insert mode
            $('#il').val('');
            $('#ilce').val('');
            // Show the modal dialog
            $('#basvuruModal').modal('show');
        });
        // Event listener for form submission
        $('#applicationForm').submit(function(e) {
            e.preventDefault();
            // Retrieve form field values
            var adres_basligi = $('#adres_basligi').val();
            var ad = $('#ad1').val();
            var soyad = $('#soyad1').val();
            var tel = $('#tel1').val();
            var adres = $('#adres1').val();
            var ulke = $('#ulke').val();
            var il = $('#il').val();
            var ilce = $('#ilce').val();
            var posta_kodu = $('#posta_kodu1').val();
            var adresId = $('#adresId').val();
            var uyeId = $('#uyeId').val();
            // Check if all required fields are filled
            if (adres_basligi && ad && soyad && tel && adres && posta_kodu) {
                // Prepare form data for submission
                var formData = new FormData();
                formData.append('adres_basligi', adres_basligi);
                formData.append('ad', ad);
                formData.append('soyad', soyad);
                formData.append('tel', tel);
                formData.append('adres', adres);
                formData.append('ulke', ulke);
                formData.append('il', il);
                formData.append('ilce', ilce);
                formData.append('posta_kodu', posta_kodu);
                formData.append('adresId', adresId);
                formData.append('uyeId', uyeId);
                formData.append('type', 'adresEkle'); // Use modalMode to determine action
                // Perform AJAX request to update or insert address
                $.ajax({
                    type: 'POST',
                    url: 'php/edit_info.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // On successful submission, hide the modal dialog
                        $('#basvuruModal').modal('hide');
                        // Show success message
                        Swal.fire({
                            title: "Adresiniz Eklenmiştir",
                            icon: "success",
                            showConfirmButton: false
                        });
                        // Reload the page after a short delay
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    }
                });
            } else {
                // If any required field is empty, show an error message
                Swal.fire({
                    title: "Lütfen tüm alanları doldurunuz!",
                    icon: "error",
                    showConfirmButton: true,
                    timer: 3000
                });
            }
        });
    });
</script>
<!-- adres sonu -->
<script>
    function promosyon() {
        var promosyonKodu = $('#promosyon_kodu_gir').val();
        var tumToplam = $("#hiddenYanToplam").val();
        var uyeID = $("#hiddenUyeID").val();
        var blkodu_dizi = $("#blkodu_dizi").val();
        var kategori_dizi = $("#kategori_dizi").val();
        var indirim = $(".hiddenindirim-alani").val();
        var marka_dizi = $("#marka_dizi").val();
        var adet_dizi = [];

        // .sepet_adet sınıfına sahip tüm elemanları döngüye al
        $('.sepet_adet').each(function() {
            var adet = $(this).val();
            adet_dizi.push(parseInt(adet, 10)); // Adet değerlerini tam sayı olarak ekle
        });
        $.ajax({
            type: 'POST',
            url: 'functions/functions.php',
            data: {promosyon_kodu: promosyonKodu,
                promosyonKontrol: 'promosyonKontrol',
                indirim: indirim,
                adet_dizi: JSON.stringify(adet_dizi),
                toplam: tumToplam, blkodu_dizi: blkodu_dizi,
                uyeID: uyeID,
                kategori_dizi: kategori_dizi,
                marka_dizi: marka_dizi},
            dataType: 'json',
            success: function(response) {
                console.log('fiyat:' + response.fiyat);
                console.log('fiyat_islem:' + response.fiyat_islem);
                console.log('tplfyt: ' + response.tplfyt);
                console.log('dvz: ' + response.doviz);
                console.log('response: ' + response.response);
                console.log('message: ' + response.message);
                if (response.response !== '0.00') {
                    $('.indirim-alani').html('₺' + response.response);
                    $('.hiddenindirim-alani').val(response.response);
                    toplamFiyatVeKDV();
                    Swal.fire({
                        icon: 'success',
                        title: 'İndirim Uygulandı!',
                        toast: true,
                        position: 'center',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }else{
                    $('.indirim-alani').html('₺' + response.response);
                    $('.hiddenindirim-alani').val(response.response);
                    Swal.fire({
                        icon: 'error',
                        title: response.message,
                        toast: true,
                        position: 'center',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        });
    }
  $(document).ready(function() {
      $('.promosyon_onayla').on('click', function() {
          var promosyonKodu = $('#promosyon_kodu_gir').val();
          $("#hiddenPromosyonKodu").val(promosyonKodu);
          promosyon();
      });
  });

</script>
<script>
    function validateQuantity(sepetId, adet, stok) {
        $.ajax({
            type: 'POST',
            url: 'functions/sepet/sepet_fonksiyonlar.php',
            data: {
                'sepetId': sepetId,
                'adet': adet,
                'stok': stok,
                'sepetAdetGuncelle': 'sepetAdetGuncelle'
            },
            success: function (response) {
                if (response.status === 1) {
                    // Success, reload the page
                    window.location.reload();
                }
            },
            error: function (xhr, status, error) {
                console.error('Hata: ' + error);
            }
        });
    }
    $(document).ready(function() {
        var sepetId = $('#sepet_id').val();
        var adet = $('.sepet_adet').val();
        var stok = $('#stok_adet').val();
        validateQuantity(sepetId, adet, stok);
    });
</script>
<script>
    function odemeye_gec() {
        var sepetBosElement = document.getElementById('sepet_bos');

        var radioButtons = document.getElementsByName('customRadioIcon');
        var selectedIndex = -1; // Initialize with an invalid index
        var selected = false;
        for (var i = 0; i < radioButtons.length; i++) {
            if (radioButtons[i].checked) {
                selected = true;
                selectedIndex = i;
                break;
            }
        }
        // Now selectedIndex contains the index of the selected radio button
        document.getElementById('selectedDeliveryOption').value = selectedIndex;

        if (sepetBosElement) {
            Swal.fire({
                icon: 'error',
                title: 'Sepetiniz Boş!'
            });
            return false;
        }else if (!selected) {
            Swal.fire({
                icon: 'error',
                title: 'Kargo firması seçiniz!',
                text: 'Ödeme sayfasına geçebilmek için lütfen bir kargo firması seçiniz!',
            });
            return false;
        } else {
            // Formu gönder
            document.getElementById('paymentForm').submit();
            // Uncheck radio buttons after submission (assuming they have ids or classes)
            for (var i = 0; i < radioButtons.length; i++) {
                radioButtons[i].checked = false;
            }
        }
    }
</script>
<script>
    function formatNumber1(number) {
        if (isNaN(number)) {
            return null;
        }
        var parts = number.toFixed(4).toString().split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return parts.join(',');
    }
    var dolarKuru = <?= $dolar['satis']; ?>;
    var euroKuru = <?= $euro['satis']; ?>;
    function updateToplamDesi() {
        var toplamDesi = 0;

        $(".list-group-item").each(function () {
            var birimDesi = parseFloat($(this).find(".birim_desi").val());
            var sepetAdet = parseInt($(this).find(".sepet_adet").val());

            if (isNaN(birimDesi) || isNaN(sepetAdet)) {
                //console.error('Invalid data found:', birimAdet, sepetAdet, birimKDV);
                return; // Skip this iteration if data is invalid
            }
            toplamDesi += birimDesi * sepetAdet;
        });

        $("#toplamDesiBirimi").val(toplamDesi);
        $("#toplamDesiBirimi1").val(toplamDesi);
        $.ajax({
            url: 'functions/kargo/kargo_desi_hesapla.php', // Veriyi alacak PHP dosyasının yolu
            type: 'POST',
            data: { toplamDesi: toplamDesi },
            success: function(response) {
                var kargoUcretleri = JSON.parse(response);
                // İlgili HTML öğelerine kargo ücretlerini yazdır
                $(".kargo_ucreti").each(function(index) {
                    var id = index; // Kargo ID'leri 1'den başlıyor
                    if (id in kargoUcretleri) {
                        $(this).text(kargoUcretleri[id].replace('.', ','));
                    }
                });
                $("#customRadioDelivery2").val(kargoUcretleri[1].replace('.', ','));
                $("#customRadioDelivery3").val(kargoUcretleri[2].replace('.', ','));
                $("#customRadioDelivery1").val(kargoUcretleri[3].replace('.', ','));
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
    function toplamFiyatVeKDV() {
        var toplamFiyat = 0;
        var toplamKDV = 0;
        var kurRates = {
            '$': dolarKuru,
            '€': euroKuru,
            '₺': 1
        };

        $(".list-group-item").each(function () {
            var $item = $(this);
            var birimAdet = parseFloat($item.find(".birim_adet").text().replace(/\./g, '').replace(',', '.'));
            var sepetAdet = parseInt($item.find(".sepet_adet").val(), 10); // Specify radix 10 for parseInt
            var birimDoviz = $item.find(".birim_doviz").text();
            var birimKDV = parseFloat($item.find(".birim_kdv").val());

            if (isNaN(birimAdet) || isNaN(sepetAdet) || isNaN(birimKDV)) {
                //console.error('Invalid data found:', birimAdet, sepetAdet, birimKDV);
                return; // Skip this iteration if data is invalid
            }

            var kur = kurRates[birimDoviz] || 1; // Default to 1 if currency not found

            toplamFiyat += birimAdet * sepetAdet * kur;
            toplamKDV += birimKDV * sepetAdet * kur;
        });

        $("#yanSepetToplami").text('₺' + formatNumber1(toplamFiyat));
        $("#yanSepetKDVToplami").text('₺' + formatNumber1(toplamKDV));

        updateToplam();
        updateToplamDesi();

    }
    function updateKargoUcreti(selectedRadio) {
        var kargoUcreti = parseFloat($(selectedRadio).val());

        if (isNaN(kargoUcreti)) {
            kargoUcreti = 0;
        }

        $("#yanKargo").text('₺' + kargoUcreti.toFixed(2));
        toplamFiyatVeKDV();
        updateToplamDesi();
    }
    function updateToplam() {
        var sepetToplami = parseFloat($("#yanSepetToplami").text().replace('₺', '').replace(/\./g, '').replace(',', '.'));
        var kdvToplami = parseFloat($("#yanSepetKDVToplami").text().replace('₺', '').replace(/\./g, '').replace(',', '.'));

        var indirim = parseFloat($("#yanIndirim").text().replace('₺', ''));
        var kargoUcreti = parseFloat($("#yanKargo").text().replace('₺', ''));

        var toplam = sepetToplami + kdvToplami + kargoUcreti - indirim;
        $("#yanToplam").text('₺' + formatNumber1(toplam));

        $("#hiddenYanSepetToplami").val(sepetToplami.toFixed(2));
        $("#hiddenYanSepetKdv").val(kdvToplami.toFixed(2));
        $(".hiddenindirim-alani").val(indirim.toFixed(2));
        $("#hiddenYanKargo").val(kargoUcreti.toFixed(2));
        $("#hiddenYanToplam").val(toplam.toFixed(2));
        updateToplamDesi();
    }
    $(document).ready(function () {
        toplamFiyatVeKDV();
    });
    $(".sepet_adet, .btn-pinned").on("input click", function () {
        toplamFiyatVeKDV();
    });
    $(".sepet_adet").on("input click", function () {
        if ($('#promosyon_kodu_gir').val() !== null && $('#promosyon_kodu_gir').val() !== '') {
            promosyon();
        }
    });
    $('input[name="customRadioIcon"]').on('change', function () {
        updateKargoUcreti(this);
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const code = urlParams.get('code');
        const message = urlParams.get('message');

        if (code && message) {
            Swal.fire({
                icon: 'error',
                title: 'İşlem Başarısız ',
                text: message + ' ' + code,
            });
        }
    });
</script>
<?php
//Kuveyt POS
//https://sanalpos.kuveytturk.com.tr/
if(isset($_POST['AuthenticationResponse'])) {
    $data = urldecode($_POST['AuthenticationResponse']);
    $xml = simplexml_load_string($data);
    $responseMessage = (string) $xml->ResponseMessage;
    $tutar = $xml->VPosMessage->Amount;
    $tutar = $tutar / 100;
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
    echo "<script>";
    echo "Swal.fire({";
    echo "  title: 'Başarısız İşlem !',";
    echo "  text: '$responseMessage',";
    echo "  icon: 'error',";
    echo "});";
    echo "</script>";
    $pos_id = 3;
    $basarili = 0;
    $database->insert("INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)", 
                                        ['uye_id' => $uye_id,'pos_id' => $pos_id,'islem' => $responseMessage,'tutar' => $tutar,'basarili' => $basarili]);
}
//Param Pos
//https://posws1.param.com.tr/
if (isset($_GET['error']) && !empty($_GET['error'])) echo '<div class="alert alert-danger" role="alert">' . urldecode($_GET['error']) . '</div>';
if (isset($_POST['TURKPOS_RETVAL_Sonuc_Str'])) {
    $sonucStr = $_POST['TURKPOS_RETVAL_Sonuc_Str'];
    $dekont = $_POST['TURKPOS_RETVAL_Dekont_ID'];
    $tutar = $_POST['TURKPOS_RETVAL_Tahsilat_Tutari'];
    $tutar = str_replace(',', '.', $tutar);
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
    echo "<script>";
    echo "Swal.fire({";
    echo "  title: 'Başarısız İşlem !',";
    echo "  text: '$sonucStr',";
    echo "  icon: 'error',";
    echo "});";
    echo "</script>";
    $pos_id = 1;
    $basarili = 0;
    $database->insert("INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)", 
                                                ['uye_id' => $uye_id,'pos_id' => $pos_id,'islem' => $sonucStr,'tutar' => $tutar,'basarili' => $basarili]);
}
//Garanti Pos
if (isset($_POST['errmsg'])) {
    $sonucStr = $_POST['mderrormessage'];
    $tutar = $_POST["txnamount"];
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
    echo "<script>";
    echo "Swal.fire({";
    echo "  title: 'Başarısız İşlem !',";
    echo "  text: '$sonucStr',";
    echo "  icon: 'error',";
    echo "});";
    echo "</script>";
    $pos_id = 2;
    $basarili = 0;
    $database->insert("INSERT INTO b2b_sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)", 
                                                ['uye_id' => $uye_id,'pos_id' => $pos_id,'islem' => $sonucStr,'tutar' => $tutar,'basarili' => $basarili]);
}

?>