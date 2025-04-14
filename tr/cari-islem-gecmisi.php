<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

sessionControl();
$currentPage = 'cari-islem-gecmisi';
$template = new Template('Nokta B2B - Cari İşlem Geçmişi', $currentPage);

$template->head();
$database = new Database();
$BLKODU_S = $_SESSION['BLKODU'];
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
            <a class="link-body-emphasis" href="index">
                <svg class="bi" width="15" height="15"><use xlink:href="#house-door-fill"></use></svg>
                <span class="visually-hidden">Anasayfa</span>
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Cari İşlem Listesi</li>
    </ol>
</nav>
<div class="container">
    <div class="row">
        <?php $template->leftMenuProfile(); ?>
        <div class="float-end col-xs-12 col-sm-12 col-md-9">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 ">
                    <article class="blog-post border rounded-3 px-3 " style="background-color: #EFF4F7;">
                        <div class="row d-flex justify-content-between">
                            <div class="col-4">
                                <h5 class="link-body-emphasis my-4">Cari İşlem Listesi</h5>
                            </div>
                            <div class="col-2">
                                <a class="btn btn-primary my-4" href="tr/dekontlar" role="button">Dekontlarım</a>
                            </div>
                        </div>

                        <div class="table p-2 table-responsive">
                            <table class="rounded-3" style="width: 100%; border-color:#e1e1e1 ">
                                <thead class="rounded-3" >
                                <td class="p-2 text-center border">İşlem Tarihi</td>
                                <td class="p-2 text-center border">Vade Tarihi</td>
                                <td class="p-2 text-center border">İşlem Türü</td>
                                <td class="p-2 text-center border">Hesap</td>
                                <td class="p-2 text-center border">Borç Tutarı</td>
                                <td class="p-2 text-center border">Alacak Tutarı</td>
                                <td class="p-2 text-center border">Dvz. Borç Tutarı</td>
                                <td class="p-2 text-center border">Dvz. Alacak Tutarı</td>
                                <td class="p-2 text-center border">Evrak No</td>
                                </thead>
                                <tbody>
                                <?php
                                $islemTurleri = array(
                                    1 => "DEVİR",
                                    2 => "EVRAK",
                                    3 => "NAKİT",
                                    4 => "DEKONT",
                                    5 => "KREDİ KARTI",
                                    6 => "POS",
                                    7 => "ÇEK",
                                    8 => "SENET",
                                    9 => "FATURA",
                                    10 => "İRSALİYE",
                                    12 => "VİRMAN",
                                    13 => "TAHAKKUK",
                                    14 => "BONUS",
                                    15 => "SERVİS",
                                    16 => "SİPARİŞ",
                                );
                                $hesapTurleri = array(
                                    1 => "₺",
                                    0 => "$"
                                );
                                $d = $database->fetchAll("SELECT * FROM uyeler_hareket WHERE BLCRKODU = :BLKODU_S AND SILINDI = 0 ORDER BY STR_TO_DATE(TARIHI, '%d.%m.%Y %H:%i:%s') DESC"
                                                    , ['BLKODU_S' => $BLKODU_S]);
                                    foreach ($d as $k => $row) {
                                        if (isset($row['ISLEM_TURU'])) { $islemTuru = $row['ISLEM_TURU']; }
                                        if (isset($row['KPBDVZ'])) { $hesapTuru = $row['KPBDVZ']; }
                                        ?>
                                        <tr>
                                            <td class="p-2 text-center border"><?= $row['TARIHI'] ?></td>
                                            <td class="p-2 text-center border"><?= $row['VADESI'] ?></td>
                                            <td class="p-2 text-center border"><?php if ($row['ISLEM_TURU']) { echo $islemTurleri[$islemTuru]; } ?></td>
                                            <td class="p-2 text-center border"><?php if (isset($row['KPBDVZ']) && array_key_exists($hesapTuru, $hesapTurleri)) { echo $hesapTurleri[$hesapTuru]; } ?></td>
                                            <td class="p-2 text-center border"><?= formatVirgulluNumber($row['KPB_BTUT']) ?></td>
                                            <td class="p-2 text-center border"><?= formatVirgulluNumber($row['KPB_ATUT']) ?></td>
                                            <td class="p-2 text-center border"><?= formatVirgulluNumber($row['DVZ_BTUT']) ?></td>
                                            <td class="p-2 text-center border"><?= formatVirgulluNumber($row['DVZ_ATUT']) ?></td>
                                            <td class="p-2 text-center border"><?= $row['EVRAK_NO'] ?></td>
                                        </tr>
                                    <?php } ?>
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