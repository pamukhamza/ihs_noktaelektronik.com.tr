<?php
require '../functions/admin_template.php';
require '../functions/functions.php';
ini_set('display_errors', 1);  // Hataları ekrana göster
error_reporting(E_ALL);   
$currentPage = 'siparis-detay';
$template = new Template('Nokta Elektronik - Sipariş Detayı', $currentPage);

$template->head();
$database = new Database();

$sip_id = filter_var($_GET['s_id'], FILTER_VALIDATE_INT);

?>
<body>
<style>
        .font-size-14px {
            font-size:14px;
        }
    </style>
    <?php $template->header(); ?>
    <nav aria-label="breadcrumb" class="container mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;"><symbol id="house-door-fill" viewBox="0 0 16 16">
            <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/></symbol>
        </svg>
        <ol class="breadcrumb ">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="index"><svg class="bi" width="15" height="15"><use xlink:href="#house-door-fill"></use></svg><span class="visually-hidden">Anasayfa</span></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Siparişlerim</li>
        </ol>
    </nav>
    <div class="container">
        <div class="row">
            <div class="col-12 col-xs-12 col-sm-12 col-md-12">
                <div class="row justify-content-between">
                    <div class="col-4">
                        <h1 class="h3 mb-3 fw-normal">Siparişlerim</h1>
                    </div>
                    Üyelik Bilgilerim
                </div>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 mx-auto mb-5 mt-3 p-4 border rounded-3 " style="background-color: white;">
                <div class="col-12 col-xs-12 col-sm-12 col-md-12">
                    <div class="row">
                        <div class="col-4">
                            <h5>Sipariş Detayı</h5>
                            <?php
                                $session_id = $_SESSION['id'];
                                $siparis = $database->fetchAll("
                                    SELECT s.*, sd.*, til.il_adi AS teslimat_il_adi, tilce.ilce_adi AS teslimat_ilce_adi,
                                    uil.il_adi AS uye_il_adi, uilce.ilce_adi AS uye_ilce_adi
                                    FROM b2b_siparisler AS s
                                    JOIN b2b_siparis_durum AS sd ON sd.id = s.durum
                                    LEFT JOIN iller AS til ON til.il_id = s.teslimat_il
                                    LEFT JOIN ilceler AS tilce ON tilce.ilce_id = s.teslimat_ilce
                                    LEFT JOIN iller AS uil ON uil.il_id = s.uye_il
                                    LEFT JOIN ilceler AS uilce ON uilce.ilce_id = s.uye_ilce
                                    WHERE s.id = :sip_id
                                ", ['sip_id' => $sip_id]); 
                            foreach($siparis as $row){
                                $siparis_no = $row["siparis_no"];?>
                            <div class="row font-size-14px pt-1"><div class="col-md-4"><strong>Sipariş No:</strong></div><div class="col-md-8"><?= $row["siparis_no"]; ?></div></div>
                            <div class="row font-size-14px pt-1"><div class="col-md-4"><strong>Tarih:</strong></div><div class="col-md-8"><?= $row["tarih"]; ?></div></div>
                            <div class="row font-size-14px pt-1"><div class="col-md-4"><strong>Durum:</strong></div><div class="col-md-8"><?= $row["durum"]; ?></div></div>
                            <div class="row font-size-14px pt-1"><div class="col-md-4"><strong>Ödeme Şekli:</strong></div><div class="col-md-8"><?= $row["odeme_sekli"]; ?></div></div>
                            <div class="row font-size-14px pt-1"><div class="col-md-4"><strong>Kargo Firması:</strong></div><div class="col-md-8"><?= $row["kargo_firmasi"]; ?></div></div>
                            <div class="row font-size-14px pt-1"><div class="col-md-4"><strong>Kargo No:</strong></div><div class="col-md-8"></div></div>
                            <div class="row font-size-14px pt-1"><div class="col-md-4"><strong>Fatura Url:</strong></div><div class="col-md-8"><a href="#">Görüntülemek için tıklayınız...</a></div></div>
                            <?php } ?>
                        </div>
                        <div class="col-4">
                        <h5>Teslimat Adresi</h5>
                            <div class="row font-size-14px"><div class="col-md-9"><?= $row["teslimat_firmaadi"]; ?></div></div>
                            <div class="row font-size-14px"><div class="col-md-9"><?= $row["teslimat_ad"]; ?> <?= $row["teslimat_soyad"]; ?></div></div>
                            <div class="row font-size-14px"><div class="col-md-9"><?= $row["teslimat_telefon"]; ?></div></div>
                            <div class="row font-size-14px"><div class="col-md-9"><?= $row["teslimat_adres"]; ?></div></div>
                            <div class="row font-size-14px"><div class="col-md-9"><?= $row["teslimat_ilce_adi"]; ?></div></div>
                            <div class="row font-size-14px"><div class="col-md-9"><?= $row["teslimat_il_adi"]; ?></div></div>
                        </div>
                        <div class="col-4">
                        <h5>Fatura Adresi</h5>
                            <div class="row font-size-14px"><div class="col-md-9"><?= $row["uye_firmaadi"]; ?></div></div>
                            <div class="row font-size-14px"><div class="col-md-9"><?= $row["uye_ad"]; ?> <?= $row["uye_soyad"]; ?></div></div>
                            <div class="row font-size-14px"><div class="col-md-9"><?= $row["uye_tel"]; ?></div></div>
                            <div class="row font-size-14px"><div class="col-md-9"><?= $row["uye_adres"]; ?></div></div>
                            <div class="row font-size-14px"><div class="col-md-9"><?= $row["uye_ilce_adi"]; ?></div></div>
                            <div class="row font-size-14px"><div class="col-md-9"><?= $row["uye_il_adi"]; ?></div></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table p-2 table-responsive">
                            <table class="border" style="width: 100%; border-color:#e1e1e1">
                                <thead class="border">
                                    <td class="p-2 text-center border fs-10">İşlemler</td>
                                    <td class="p-2 text-center border fs-10">Fotoğraf</td>
                                    <td class="p-2 text-center border fs-10">Stok Kodu</td>
                                    <td class="p-2 text-center border fs-10">Ürün Adı</td>
                                    <td class="p-2 text-center border fs-10">Miktarı</td>
                                    <td class="p-2 text-center border fs-10">Birim Fiyatı</td>
                                    <td class="p-2 text-center border fs-10">KDV Durumu</td>
                                    <td class="p-2 text-center border fs-10">KDV siz toplam</td>
                                    <td class="p-2 text-center border fs-10">KDV</td>
                                    <td class="p-2 text-center border fs-10">KDV li Toplam</td>
                                </thead>
                                <tbody>
                                    <?php
                                    $sepet = $database->fetchAll("
                                    SELECT s.id, su.*, nu.*, MIN(ns.KResim) AS first_photo, nu.id AS urunid, su.id AS suId
                                    FROM b2b_siparisler AS s
                                    LEFT JOIN b2b_siparis_urunler AS su ON su.sip_id = s.id
                                    LEFT JOIN nokta_urunler AS nu ON nu.id = su.urun_id
                                    LEFT JOIN nokta_urunler_resimler AS ns ON nu.id = ns.UrunID
                                    WHERE s.uye_id = :session_id AND s.id = :sip_id
                                    GROUP BY s.id, su.urun_id;
                                    ", ['session_id' => $session_id, 'sip_id' => $sip_id]);
                                    $kdvsizSepetTopla = 0;

                                    foreach($sepet as $sep){
                                        $kdvsizToplam1 = $sep["adet"] * $sep["birim_fiyat"];
                                        $kdvsizToplam = number_format($kdvsizToplam1, 2, '.', '');
                                        $kdv_tutari = (float)$sep["adet"] * (float)$sep["birim_fiyat"] * (float)$sep["kdv"] / 100;
                                        $kdv_tutari_formatli = number_format($kdv_tutari, 2, '.', '');
                                        $kdvliToplam = $kdvsizToplam1 + $kdv_tutari;
                                        $kdvliToplam = number_format($kdvliToplam, 2, '.', '');
                                        $sip_urun_id = $sep["suId"];
                                        $tarih = $sep["tarih"];
                                        $gecenZaman = strtotime($tarih . " +15 days");
                                        $simdikiZaman = time();
                                        $kdvsizSepetTopla += $sep["birim_fiyat"]; // her bir ürünün fiyatlarını topla
                                        $kdvsizSepetToplam = number_format($kdvsizSepetTopla, 2, '.', '') * $sep["adet"];
                                        $kdvsizSepetToplamTL = $kdvsizSepetToplam * $sep["dolar_satis"];
                                        $sepetToplamKDV = $kdvsizSepetToplam * 0.2;
                                        $sepetToplamKDVTL = $sepetToplamKDV * $sep["dolar_satis"];
                                        ?>
                                        <tr class="border">
                                            <td class="p-2 text-center border fs-10">
                                                <a href="tr/urunler/<?= $sep["seo_link"] ?>" class="btn btn-primary mb-2" style="font-size:12px; width:110px"><i class="fa-solid fa-box pe-1"></i>Ürüne Git</a>
                                                </br>
                                                <?php
                                                // 15 gün eklenmiş tarih, şu anki tarihten küçükse 15 gün geçmiştir
                                                if ($gecenZaman > $simdikiZaman) {
                                                    if ($sep["iade"] == 1 || $sep["iade"] == 2 || $sep["iade"] == 3) { ?>
                                                        <button class="btn btn-warning iadeIptalbtn" style="font-size:12px; width:110px"><i class="fa-solid fa-truck pe-1"></i>İade Aşamasında</button>
                                                    <?php } elseif ($sep["iade"] == 4) { ?>
                                                        <button class="btn btn-success" style="font-size:12px; width:110px"><i class="fa-regular fa-circle-check pe-1"></i>İade Edildi</button>
                                                    <?php } elseif ($sep["iade"] == 5) { ?>
                                                        <button class="btn btn-danger" style="font-size:12px; width:110px"><i class="fa-regular fa-circle-check pe-1"></i>İade Reddedildi</button>
                                                    <?php } else { ?>
                                                        <button data-toggle="modal" data-target="#iadeOnayModal" data-sip-id="<?= $sip_urun_id ?>" class="btn btn-warning iadeOnaybtn iade-btn-gizle" style="font-size:12px; width:110px"><i class="fa-solid fa-rotate-left pe-1"></i>İade Et</button>
                                                    <?php }
                                                } ?>
                                            </td>
                                        <td class="p-2 text-center border fs-10"><img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/<?= $sep["first_photo"] ?>" width="75px" alt=""></td>
                                        <td class="p-2 text-center border fs-10"><?= $sep["UrunKodu"] ?></td>
                                        <td class="p-2 text-center border fs-10"><?= $sep["UrunAdiTR"] ?></td>
                                        <td class="p-2 text-center border fs-10"><?= $sep["adet"] ?></td>
                                        <td class="p-2 text-center border fs-10"><?= number_format($sep["birim_fiyat"], 2, ',', '.') ?><span class="fw-bold"><?= !empty($sep["DSF4"]) || !empty($sep["DSF3"]) ? $sep["DOVIZ_BIRIMI"] : '₺' ?></span></td>
                                        <td class="p-2 text-center border fs-10">%<?= $sep["kdv"] ?></td>
                                        <td class="p-2 text-center border fs-10"><?= number_format($kdvsizToplam, 2, ',', '.') ?><span class="fw-bold"><?= !empty($sep["DSF4"]) || !empty($sep["DSF3"]) ? $sep["DOVIZ_BIRIMI"] : '₺' ?></span></td>
                                        <td class="p-2 text-center border fs-10"><?= number_format($kdv_tutari_formatli, 2, ',', '.')?><span class="fw-bold"><?= !empty($sep["DSF4"]) || !empty($sep["DSF3"]) ? $sep["DOVIZ_BIRIMI"] : '₺' ?></span></td>
                                        <td class="p-2 text-center border fs-10"><?= number_format($kdvliToplam, 2, ',', '.')?><span class="fw-bold"><?= !empty($sep["DSF4"]) || !empty($sep["DSF3"]) ? $sep["DOVIZ_BIRIMI"] : '₺' ?></span></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xs-12 col-sm-12 col-md-12">
                    <div class="row">
                        <div class="col-4">
                            <h5>Özet</h5>
                            <?php
                            $kur = $database->fetch("SELECT dolar_satis, urun_id FROM b2b_siparis_urunler WHERE sip_id = :sip_id", ['sip_id' => $sip_id]);

                            $siparisler = $database->fetch("SELECT * FROM b2b_siparisler WHERE id = :sip_id", ['sip_id' => $sip_id]);

                            $toplamFiyat = $siparisler["toplam"];
                            $toplamFiyat = str_replace(',', '.', $toplamFiyat);
                            $toplamFiyatDoviz = $toplamFiyat / $kur["dolar_satis"];
                            $toplamKdv = $siparisler["sepet_kdv"];
                            $toplamKdvDoviz = $toplamKdv / $kur["dolar_satis"];
                            $araToplam = $siparisler["sepet_toplami"];
                            $araToplamDoviz = $araToplam / $kur["dolar_satis"];


                            $dolar_satis = $kur["dolar_satis"];
                            $dolarindirim = 0.00;
                            $dolarkargo = 0.00;
                            if($row["indirim"] > 0) {
                              $dolarindirim = $row["indirim"] / $dolar_satis;
                            }elseif($row["kargo_ucreti"] > 0){
                                $dolarkargo = $row["kargo_ucreti"] / $dolar_satis;
                            } ?>
                            <div class="row font-size-14px pt-1">
                                <div class="col-md-4"><strong>Ara Toplam:</strong></div>
                                <div class="col-md-3"><?= number_format($araToplamDoviz, 2, ',', '.')?><span class="fw-bold">$</span></div>
                                <div class="col-md-4"><?= number_format($araToplam, 2, ',', '.')?><span class="fw-bold">₺</span></div>
                            </div>
                            <div class="row font-size-14px pt-1">
                                <div class="col-md-4"><strong>KDV:</strong></div>
                                <div class="col-md-3"><?= number_format($toplamKdvDoviz, 2, ',', '.')?><span class="fw-bold">$</span></div>
                                <div class="col-md-4"><?= number_format($toplamKdv, 2, ',', '.')?><span class="fw-bold">₺</span></div>
                            </div>
                            <div class="row font-size-14px pt-1">
                                <div class="col-md-4"><strong>İndirim:</strong></div>
                                <div class="col-md-3"><?= number_format($dolarindirim, 2, ',', '.')?><span class="fw-bold">$</span></div>
                                <div class="col-md-4"><?= number_format($row["indirim"], 2, ',', '.')?><span class="fw-bold">₺</span></div>
                            </div>
                            <div class="row font-size-14px pt-1">
                                <div class="col-md-4"><strong>Kargo:</strong></div>
                                <div class="col-md-3">
                                    <?= number_format($dolarkargo, 2, ',', '.')?><span class="fw-bold">$</span>
                                </div>
                                <div class="col-md-4"><?= number_format($row["kargo_ucreti"], 2, ',', '.')?><span class="fw-bold">₺</span></div>
                            </div>
                            <hr>
                            <div class="row font-size-14px pt-1">
                                <div class="col-md-4"><strong>Genel Toplam:</strong></div>
                                <div class="col-md-3">
                                    <?= number_format($toplamFiyatDoviz, 2, ',', '.'); ?>
                                    <span class="fw-bold">$</span>
                                </div>
                                <div class="col-md-3">
                                    <?= number_format($toplamFiyat, 2, ',', '.'); ?>
                                    <span class="fw-bold">₺</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $template->footer(); ?>
<!-- Modal Onay -->
<div class="modal fade" data-bs-backdrop="static" id="iadeOnayModal" role="dialog" aria-labelledby="iadeOnayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="iadeOnayModalLabel">İade Formu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="applicationForm" method="post" class="needs-validation">
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <div class="col-sm-12 pb-2">
                                <p class="border-bottom pb-3">İade işleminizi tamamlamak için iade nedeninizi yazdıktan sonra devam et butonuna tıklayınız.</br> Karşınıza çıkacak ekranda iade için kargo bilgilerini görüntüleyebileceksiniz.</p>
                            </div>
                            <div class="col-sm-12">
                                <label for="iade_nedeni" class="form-label">İade Nedeni</label>
                                <input type="text" id="uye_id" value="<?= $_SESSION["id"]; ?>" hidden>
                                <input type="text" id="sip_no" value="<?= $siparis_no ?>" hidden>
                                <textarea type="text" class="form-control" id="iade_nedeni" required></textarea>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <button class="w-100 btn btn-primary iadeOnayDevambtn" id="iadeOnayBtn" type="submit">Devam Et</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Kargo -->
<div class="modal fade" data-bs-backdrop="static" id="kargoBilgiModal" role="dialog" aria-labelledby="kargoBilgiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kargoBilgiModalLabel">Kargo Bilgileri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-sm-12">
                            <div class="col-sm-12 text-center pb-3 text-success">
                                <label for="iade_nedeni" class="form-label"><b><i class="fa-regular fa-circle-check fa-xl pe-2"></i>İadeniz talebiniz alınmıştır.</b></label>
                            </div>
                            <div class="col-sm-12">
                                <p class="border-bottom pb-3">Ürünleri herhangi bir kargo firması ile tarafımıza gönderebilirsiniz.</br>
                                <b>İade edilen ürünlerin tarafımıza gönderici ödemeli olarak gönderilmesi gerekmektedir.</b></p>
                            </div>
                            <div class="col-sm-12">
                                <label for="iade_nedeni" class="form-label"><b>Bilgiler</b></label>
                                <p>Adres: Perpa Ticaret Merkezi B Blok Kat 8 No.906-907 34384 Şişli / İstanbul</p>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/splide/splide.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.iadeOnayDevambtn').click(function() {
            $('#kargoBilgiModal').modal('show');
            $('#iadeOnayModal').modal('hide');
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.iadeIptalBtn').click(function() {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your file has been deleted.",
                        icon: "success"
                    });
                }
            });
            $.ajax({

                type: 'POST',
                url: 'functions/edit_info.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function() {

                },
                error: function(response) {

                }
            });
        });
    });
</script>
<script>$(document).ready(function() {
        var sip_urun_id;
        $('.iadeOnaybtn').click(function() {
            sip_urun_id = $(this).data('sip-id');
            $('#iadeOnayModal').modal('show');
        });
        $('#applicationForm').submit(function(e) {
            e.preventDefault();
            
            var uye_id = $('#uye_id').val();
            var iade_nedeni = $('#iade_nedeni').val();
            var sip_no = $('#sip_no').val();
            $.ajax({
                type: 'POST',
                url: 'functions/edit_info.php',
                data: {
                    sip_urun_id: sip_urun_id,
                    uye_id: uye_id,
                    iade_nedeni: iade_nedeni,
                    sip_no: sip_no,
                    type: 'iade'
                },
                success: function() {
                    var modal = $('#kargoBilgiModal');
                    modal.modal('show');

                    modal.on('hidden.bs.modal', function (e) {
                        location.reload();
                    });
                },
                error: function(response) {
                    // Hata durumunda yapılacak işlemler
                }
            });
        });
    });

</script>

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
<script>
    $(document).ready(function () {
        var dropdownToggle = $('#yourDropdownToggleId');
        var dropdownMenu = $('#yourDropdownMenuId');

        // Set the width of the dropdown menu to match the width of the parent div
        dropdownMenu.width(dropdownToggle.outerWidth());
    });
</script>
