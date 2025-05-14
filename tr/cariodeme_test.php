<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'cariodeme';
$template = new Template('Nokta B2B - Cari Ödeme İşlemler', $currentPage);

$template->head();
$database = new Database();
sessionControl();

$uye_id = $_SESSION["id"];
$BLKODU = $_SESSION['BLKODU'];
$bugun = date('Y-m-d');
?>
<style>
    .bi {vertical-align: -.125em;fill: currentColor;}
</style>
<body>
    <?php $template->header(); ?>
    <!-- Site Haritası -->
    <nav aria-label="breadcrumb" class="container mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;"><symbol id="house-door-fill" viewBox="0 0 16 16">
            <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/></symbol>
        </svg>
        <ol class="breadcrumb ">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="https://www.noktaelektronik.com.tr/tr">
                <svg class="bi" width="15" height="15"><use xlink:href="#house-door-fill"></use></svg>
                <span class="visually-hidden">Anasayfa</span>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Cari Ödeme İşlemleri</li>
        </ol>
    </nav>
    <div class="container">
        <div class="row">
            <?php $template->pageLeftMenu(); ?>
            <div class="float-end col-xs-12 col-sm-12 col-md-9">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 ">
                        <article class="blog-post border rounded-3 px-3 " style="background-color: #EFF4F7;">
                            <h5 class="link-body-emphasis my-4">Cari Ödeme</h5>
                            <form method="post" action="tr/cari_odeme">
                                <div class="p-1">
                                    <?php

                                        // TL işlemleri için sorgu
                                        $rows = $database->fetchAll("SELECT KPB_BTUT FROM uyeler_hareket WHERE BLCRKODU = :BLKODU AND DOVIZ_HES_ISLE = 0 AND SILINDI = 0 AND ISLEM_TURU = 9 AND VADESI > :bugun", [
                                            'BLKODU' => $BLKODU,
                                            'bugun' => $bugun
                                        ]);

                                        $toplamKPB_BTUT = 0;
                                        foreach ($rows as $row) {
                                            $fiyat = str_replace(',', '.', $row['KPB_BTUT']);
                                            $toplamKPB_BTUT += floatval($fiyat);
                                        }

                                        // Döviz işlemleri için sorgu
                                        $rows = $database->fetchAll("SELECT DVZ_BTUT FROM uyeler_hareket WHERE BLCRKODU = :BLKODU AND DOVIZ_HES_ISLE = 1 AND SILINDI = 0 AND ISLEM_TURU = 9 AND VADESI > :bugun", [
                                            'BLKODU' => $BLKODU,
                                            'bugun' => $bugun
                                        ]);

                                        $toplamDVZ_BTUT = 0;
                                        foreach ($rows as $row) {
                                            $fiyat = str_replace(',', '.', $row['DVZ_BTUT']);
                                            $toplamDVZ_BTUT += floatval($fiyat);
                                        }

                                        // Üye grubunu sorgulama
                                        $uye = $database->fetch("SELECT GRUBU FROM uyeler WHERE BLKODU = :BLKODU", [
                                            'BLKODU' => $BLKODU
                                        ]);

                                        // Hesap türünü seçme
                                        if (empty($uye['GRUBU']) || $uye['GRUBU'] == 'USD' || $uye['GRUBU'] == 'EURO') {
                                            ?>
                                            <select class="form-control" name="hesap">
                                                <option value="0">TL Hesabıma İşle</option>
                                                <option value="1">Döviz Hesabıma İşle</option>
                                            </select>
                                            <?php
                                        }
                                    ?>
                                    <div class="input-group mt-1">
                                        <input  type="text" pattern="^\d+(\.\d{1,2})?$" name="fiyat" id="fiyat" class="form-control" placeholder="Lütfen tutar giriniz(TL)" required>
                                        <button type="submit" class="btn btn-outline-success fs-6" name="cariOdemeYap" type="button">Ödeme Yap</button>
                                    </div>
                                </div>
                            </form>
                            <h5 class="link-body-emphasis my-4">Cari İşlemler Dökümü</h5>
                            <div class="table p-2 table-responsive">
                                <table class="rounded-3" style="width: 100%; border-color:#e1e1e1 ">
                                    <thead class="rounded-3" >
                                        <td class="p-2 text-center border">Hesap</td>
                                        <td class="p-2 text-center border text-danger">Vadesi Geçmiş Borç</td>
                                        <td class="p-2 text-center border">Toplam Borç</td>
                                        <td class="p-2 text-center border">Toplam Alacak</td>
                                        <td class="p-2 text-center border">Durum</td>
                                        <td class="p-2 text-center border">Toplam Bakiye</td>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $cari = $database->fetch("SELECT * FROM cari_bakiye WHERE BLKODU = :blk", ["blk" => $BLKODU]);

                                            $todayDate = date('d.m.Y');

                                            $toplam_bakiyesi = str_replace(',', '.', $cari['TPL_BKY']);
                                            $toplam_dvz_bakiyesi = str_replace(',', '.', $cari['DVZ_BAKIYE']);
                                            // Dönüşüm işlemleri
                                            $toplam_bakiyesi = floatval($toplam_bakiyesi);
                                            $toplamKPB_BTUT = floatval($toplamKPB_BTUT);
                                            if(isset($toplam_dvz_bakiyesi)){
                                            //   echo $toplam_dvz_bakiyesi .'toplam_dvz_bakiyesi <br>';
                                                //echo $toplamDVZ_BTUT .'toplamDVZ_BTUT <br>';
                                                $vd_dvz_bky = floatval($toplam_dvz_bakiyesi) - floatval($toplamDVZ_BTUT);
                                                $vd_dvz_bky = formatVirgulluNumber($vd_dvz_bky);
                                                //echo $vd_dvz_bky .'vd_dvz_bky <br>';
                                            }
                                            if(isset($toplam_bakiyesi)) {
                                            //    echo $toplam_bakiyesi . 'toplam_bakiyesi <br>' ;
                                            //    echo $toplamKPB_BTUT .'toplamKPB_BTUT <br>';
                                                $vd_bky = floatval($toplam_bakiyesi) - floatval($toplamKPB_BTUT);
                                                $vd_bky = formatVirgulluNumber($vd_bky);
                                            }

                                            $TPL_BRC = !empty($cari['TPL_BRC']) ? formatVirgulluNumber($cari['TPL_BRC']) : "0,00";
                                            $TPL_ALC = !empty($cari['TPL_ALC']) ? formatVirgulluNumber($cari['TPL_ALC']) : "0,00";
                                            $TPL_BKY = !empty($cari['TPL_BKY']) ? formatVirgulluNumber($cari['TPL_BKY']) : "0,00";
                                            $DVZ_TPLBRC = !empty($cari['DVZ_TPLBRC']) ? formatVirgulluNumber($cari['DVZ_TPLBRC']) : "0,00";
                                            $DVZ_TPLALC = !empty($cari['DVZ_TPLALC']) ? formatVirgulluNumber($cari['DVZ_TPLALC']) : "0,00";
                                            $DVZ_BAKIYE = !empty($cari['DVZ_BAKIYE']) ? formatVirgulluNumber($cari['DVZ_BAKIYE']) : "0,00";
                                            $TPL_BTR = !empty($cari['TPL_BTR']) ? $cari['TPL_BTR'] : "0,00";
                                            $DVZ_BTR = !empty($cari['DVZ_BTR']) ? $cari['DVZ_BTR'] : "0,00";
                                            $DVZ_HESAP = !empty($cari['DVZ_HESAP']) ? $cari['DVZ_HESAP'] : "";
                                            function formatCell($value)
                                            {
                                                return '<td class="p-2 text-center border">' . $value . '</td>';
                                            }
                                            function formatStatusCell($status)
                                            {
                                                if ($status == 1) {
                                                    return '<td class="p-2 text-center border">Borç</td>';
                                                } elseif ($status == 2) {
                                                    return '<td class="p-2 text-center border">Alacak</td>';
                                                } else {
                                                    return '<td class="p-2 text-center border"></td>';
                                                }
                                            }

                                            echo '<tr>' .
                                                formatCell('₺') ./*
                                                formatCell('0,00') .*/
                                                formatCell($TPL_BTR == 1 ? $vd_bky : '0,00') .
                                                formatCell($TPL_BRC) .
                                                formatCell($TPL_ALC) .
                                                formatStatusCell($TPL_BTR) .
                                                formatCell($TPL_BKY) .
                                                '</tr>';

                                            if (!empty($DVZ_HESAP)) {
                                                echo '<tr>' .
                                                    formatCell($DVZ_HESAP) ./*
                                                    formatCell('0,00') .*/
                                                    formatCell($DVZ_BTR == 1 ? $vd_dvz_bky : '0,00') .
                                                    formatCell($DVZ_TPLBRC) .
                                                    formatCell($DVZ_TPLALC) .
                                                    formatStatusCell($DVZ_BTR) .
                                                    formatCell($DVZ_BAKIYE) .
                                                    '</tr>';
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $template->footer(); ?>
</body>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/app.js"></script>
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
<script>
    $(document).ready(function () {
        $('#fiyat').on('input', function () {
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
echo '<div style="display: none">';
foreach ($_POST as $key => $value) {
    echo $key . ': ' . $value . '<br>';
}
echo "</div>";
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
    $stmt = $database->insert("INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)", array(':uye_id' => $uye_id, ':pos_id' => $pos_id, ':islem' => $responseMessage, ':tutar' => $tutar, ':basarili' => $basarili));

}
//Param Pos
//https://posws1.param.com.tr/
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
    $stmt = $database->insert("INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)", array(':uye_id' => $uye_id, ':pos_id' => $pos_id, ':islem' => $sonucStr, ':tutar' => $tutar, ':basarili' => $basarili));

}
//Garanti Pos
/*
if (isset($_POST['errmsg'])) {
    $sonucStr = $_POST['mderrormessage'];
    $tutar = $_POST["txnamount"];
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
    echo "<script>";
    echo "Swal.fire({";
    echo "  title: 'Başarısız İşlem !',";
    echo "  text: '$tutar',";
    echo "  text: '$sonucStr',";
    echo "  icon: 'error',";
    echo "});";
    echo "</script>";
    $pos_id = 2;
    $basarili = 0;
    $stmt = $db->prepare("INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)");
    $stmt->execute(array(':uye_id' => $uye_id, ':pos_id' => $pos_id, ':islem' => $sonucStr, ':tutar' => $tutar, ':basarili' => $basarili));
}*/
//Türkiye Finans Pos
if(isset($_POST['ErrMsg'])) {
    $responseMessage = !empty($_POST['ErrMsg']) ? $_POST['ErrMsg'] : $_POST['mdErrorMsg'];
    $returnCode = !empty($_POST['ProcReturnCode']) ? $_POST['ProcReturnCode'] : '';
    $response = $responseMessage . $returnCode;
    $tutar = $_POST['amount'];
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>";
    echo "<script>";
    echo "Swal.fire({";
    echo "  title: 'Başarısız İşlem !',";
    echo "  text: '$responseMessage',";
    echo "  icon: 'error',";
    echo "});";
    echo "</script>";
    $pos_id = 4;
    $basarili = 0;
    $stmt = $database->insert("INSERT INTO sanal_pos_odemeler (uye_id, pos_id, islem, tutar, basarili) VALUES (:uye_id, :pos_id, :islem, :tutar, :basarili)", array(':uye_id' => $uye_id, ':pos_id' => $pos_id, ':islem' => $response, ':tutar' => $tutar, ':basarili' => $basarili));
}
?>