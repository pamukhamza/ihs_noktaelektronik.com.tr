<?php
require '../functions/admin_template.php';
require '../functions/functions.php';
/*
ini_set('display_errors', 1);  // Hataları ekrana göster
error_reporting(E_ALL);   
*/
$currentPage = 'urundetay';
$template = new Template('Nokta - Ürün Detay', $currentPage);

$template->head();
$database = new Database();
$urun_seo = $_GET['id'];
$urun = $database->fetch("SELECT u.*, m.seo_link AS marka_seo, m.title AS marka_adi 
                            FROM nokta_urunler u
                            LEFT JOIN nokta_urun_markalar m ON u.MarkaID = m.id
                            WHERE u.seo_link = :urunid", ['urunid' => $urun_seo]);
$urunId = $urun['id'];
$BLKODU = $urun['BLKODU'];
////////////////////////////////////////////////
// Ürünün cok_goren değerini kontrol et ve arttır
$cok_goren = $urun['cok_goren'];
if ($cok_goren === null || $cok_goren === '') {
    $cok_goren = 0;
}
$cok_goren++;
// cok_goren değerini güncelle
$database ->update("UPDATE nokta_urunler SET cok_goren = $cok_goren WHERE id = $urunId");
///////////////////////////////////////////////////////////////////////////////
$kategoriIDs = explode(',', $urun['KategoriID']);
$categoryId = $kategoriIDs[0]; // İlk kategori ID'sini alır
$breadcrumbs = getBreadcrumbs($categoryId, $database);
// Breadcrumb kodunu buraya ekleyin
function getBreadcrumbs($categoryId, $database) {
    $breadcrumbs = array();
    $currentCategory = $categoryId;
    while ($currentCategory > 0) {
        $categoryData = $database->fetch("SELECT * FROM nokta_kategoriler WHERE id = $currentCategory");
        if ($categoryData) {
            $breadcrumbs[] = array(
                'id' => $categoryData['seo_link'],
                'name' => $categoryData['KategoriAdiTr']
            );
            $currentCategory = $categoryData['parent_id'];
        } else {
            break;
        }
    }
    $breadcrumbs = array_reverse($breadcrumbs); // Breadcrumbs'ları sırala
    return $breadcrumbs;
}
if(isset($_SESSION['id'])) {
    $uye_id = $_SESSION['id'];
    $uye = $database->fetch("SELECT fiyat, satis_temsilcisi FROM uyeler WHERE id = $uye_id");
    $uye_fiyat = $uye['fiyat'];
    $uye_satis_temsilci = $uye['satis_temsilcisi'];
}
?>
<style>
    .table-light.table-bordered.second th,
    .table-light.table-bordered.second td {font-size: 15px; /* Adjust font size */padding: 5px; /* Adjust padding */}
    .urun-sosyal{/* Add any other styles you want here */transition: transform 0.3s ease; /* Add a transition for the transform property */}
    .urun-sosyal:hover {transform: translateY(-4px); /* Move the button 8 pixels up */}
    .bi {vertical-align: -.125em;fill: currentColor;}
    .clr{color:black;}
    .clr:hover{color:black;background-color: #ffffff;}
    .bg_fff{background-color: #ffffff;}
    .icon-btn-kalp:hover{color:red;}
    .rating {display: inline-block;cursor: pointer;}
    .rating input {display: none;}
    .rating label {font-size: 1.2rem;color: #ccc;padding: 0 0.01rem;}
    .rating input:checked ~ label,
    .rating input:checked ~ label:hover,
    .rating label:hover ~ label {color: #ffc107;}
    /* Soldan sağa doğru sıralama için eklenen stil */
    .rating label {order: 1;}
    .rating label:nth-child(2) {order: 2;}
    .rating label:nth-child(3) {order: 3;}
    .rating label:nth-child(4) {order: 4;}
    .rating label:nth-child(5) {order: 5;}
    .sepet-style{cursor:pointer;position: absolute;bottom: 30px;right: 20px;}
    .custom-underline {text-decoration: line-through;}
    .urun-a{text-decoration: none;color:black;font-size:14px;}
    .sepetEkleBtn{background-color: #FC9803; color:white;}
    .sepetEkleBtn:hover{background-color: #430666;color:white;}
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
<body>
<?php $template->header(); ?>
<nav aria-label="breadcrumb" class="mt-3 container">
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
        <?php
        foreach ($breadcrumbs as $index => $breadcrumb) {
            echo '<li class="breadcrumb-item active">';
            $shortName = strlen($breadcrumb['name']) > 28 ? substr($breadcrumb['name'], 0, 27) . '...' : $breadcrumb['name'];

            if ($index < count($breadcrumbs) - 1) {
                echo '<a class="text-decoration-none" style="color:black; font-size:14px" href="tr/urunler?cat=' . $breadcrumb['id'] . '&brand=&filter=&search=" title="' . $breadcrumb['name'] . '">' . $shortName . '</a>';
            } else {
                echo '<a class="text-decoration-none" style="color:black; font-size:14px" href="tr/urunler?cat=' . $breadcrumb['id'] . '&brand=&filter=&search=" title="' . $breadcrumb['name'] . '">' . $shortName . '</a>';
            }
            echo '</li>';
        }
        ?>
        <li class="breadcrumb-item text-info-emphasis active" style="font-size:14px" aria-current="page"><?= $urun['UrunKodu'] ?> </li>
    </ol>
</nav>
<section class="container">
    <div class="row">
        <!-- Urun Bilgileri -->
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6" >
                    <div class="col-md-12">
                        <div class="splide" id="main-slider">
                            <div class="splide__track bg_fff shadow-sm border">
                                <ul class="splide__list">
                                    <!-- Your product images here -->
                                    <?php
                                    $d = $database->fetchAll("SELECT * FROM nokta_urunler_resimler WHERE UrunID = $urunId ORDER BY sira ASC");
                                    if (empty($d)) {// No rows found, display the placeholder image
                                        ?>
                                        <li class="splide__slide d-flex align-items-center justify-content-center">
                                            <div>
                                                <a href="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/gorsel_hazirlaniyor.jpg" data-lightbox="product-images">
                                                    <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/gorsel_hazirlaniyor.jpg" height="auto" width="100%">
                                                </a>
                                            </div>
                                        </li>
                                        <?php
                                    } else {
                                        // Rows found, iterate over them and display the images
                                        foreach($d as $k => $row) {
                                            ?>
                                            <li class="splide__slide d-flex align-items-center justify-content-center">
                                                <a href="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/<?= $row["KResim"]; ?>" data-lightbox="product-images">
                                                    <?php if(empty($row['KResim'])){ ?>
                                                        <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/gorsel_hazirlaniyor.jpg" height="auto" width="100%">
                                                    <?php }else{ ?>
                                                        <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/<?= $row["KResim"]; ?>" height="auto" width="100%">
                                                    <?php } ?>
                                                </a>
                                            </li>
                                            <?php
                                        }
                                    }?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 ">
                    <div class="bg_fff h-100 shadow-sm ps-4 pt-4 pe-4 pb-2 border">
                        <h2 class="fs-4 mb-3"><span class="me-2" style="color:#0a90eb"><a class="text-decoration-none" href="tr/urunler?cat=&brand=<?= $urun['marka_seo'] ?>&filter=&search="><?= $urun['marka_adi'] ?></a></span><?= $urun['UrunAdiTR']; ?></h2>
                        <!--<h5 class="text-body-tertiary fs-6" ><div style="width: 130px; float:left">Ürün Barkodu</div>: <?= $urun['barkod'] ?></h5>-->
                        <!--<h5 class="text-body-tertiary fs-6 " ><div style="width: 130px; float:left">Ürün Markası</div>: <span class="fw-bold fw-italic"></span></h5>-->
                        <h5 class="text-body-tertiary fs-6" ><div style="width: 90px; float:left;">Stok Kodu</div>:<span class="text-black"> <?= $urun['UrunKodu'] ?></span></h5>
                        <h5 class="text-body-tertiary fs-6" ><div style="width: 90px; float:left">Stok Adedi</div>:<span class="text-black"> <?php
                                $stokDurumu = ($urun['stok'] == 0) ? 'Stok Yok' : (($urun['stok'] > 9) ? '9+' : $urun['stok']);
                                echo $stokDurumu;
                                ?></span>
                        </h5>
                        <?php if ($urun["stok"] < 10 && $urun["stok"] >= 1) { ?>
                            <span class="text-danger fs-6">Tükenmeden Al! Bu üründen yalnızca <?= $urun["stok"] ?> adet kaldı!</span>
                        <?php } ?>

                        <?php if($urun['proje'] == 0 && $urun['stok'] > 0){ ?>
                            <div class="d-flex align-items-center">
                            <h2 class="fw-bold mb-0 me-2" style="color:#f29720">
                                <?php
                                $dovizBirimi = "₺"; // Varsayılan değer
                                $fiyat = null;

                                // Session kontrolü
                                $uye_fiyat = isset($_SESSION['id']) ? $uye_fiyat : 4;

                                // DSF değerleri için döngü
                                for ($i = 4; $i >= 1; $i--) {
                                    if (!empty($urun["DSF$i"])) {
                                        $dovizBirimi = $urun["DOVIZ_BIRIMI"];
                                        $fiyat = !empty($urun["DSF" . $uye_fiyat]) ? $urun["DSF" . $uye_fiyat] : null;
                                        break; // İlk bulunan değeri al ve döngüyü kır
                                    }
                                }

                                // Eğer fiyat bulunamadıysa, KSF kontrolü yap
                                if (is_null($fiyat)) {
                                    for ($i = 4; $i >= 1; $i--) {
                                        if (!empty($urun["KSF$i"])) {
                                            $fiyat = $urun["KSF" . $uye_fiyat];
                                            break; // İlk bulunan KSF değerini al ve döngüyü kır
                                        }
                                    }
                                }

                                // Fiyat ve döviz birimini göster
                                echo $dovizBirimi;
                                echo number_format((float) $fiyat, 2, ',', '');
                                ?> + KDV
                            </h2>

                            </div>
                            <?php if ($fiyat ) {
                                ?>
                                <div class="table table-responsive mt-2 fiyat-tablo">
                                    <table class="text-center w-100 border table-bordered">
                                        <thead>
                                        <td class="fs-10 fw-bold px-2 py-1 bg-secondary text-light" >PB</td>
                                        <td class="fs-10 fw-bold px-2 py-1 bg-danger text-light" ><?php if($uye_fiyat != '4'){ ?>Liste <?php } ?>Fiyatı</td>
                                        <?php if($uye_fiyat != '4'){ ?>
                                            <td class="fs-10 fw-bold px-2 py-1 bg-success text-light" >Size Özel Fiyat</td>
                                        <?php }?>
                                        <td class="fs-10 fw-bold px-2 py-1 bg-secondary text-light" >KDV</td>
                                        <td class="fs-10 fw-bold px-2 py-1 bg-secondary text-light" >KDV Tutarı</td>
                                        <td class="fs-10 fw-bold px-2 py-1 bg-success text-light" ><?php if($uye_fiyat != '4'){ ?>Size  Özel<?php } ?>Toplam</td>
                                        </thead>
                                        <tbody>
                                            <?php
                                            function formatDovizListelemeNumber($number) {
                                                $formatted_number = number_format((float) $number, 2, ',', '.');
                                                return $formatted_number;
                                            }
                                            $dolar = $database->fetch("SELECT * FROM b2b_kurlar WHERE id = 2");
                                            $satis_dolar_kuru1 = $dolar['satis'];
                                            $euro = $database->fetch("SELECT * FROM b2b_kurlar WHERE id = 3");
                                            $satis_euro_kuru1 = $euro['satis'];

                                            if (!empty($urun["DSF".$uye_fiyat])) {
                                                if ($urun["DOVIZ_BIRIMI"] == '$') {
                                                    $uodf =  $urun["DSF".$uye_fiyat];
                                                    $udf =  $urun["DSF4"];
                                                    $uof = $uodf * $satis_dolar_kuru1;
                                                    $uf = $udf * $satis_dolar_kuru1;
                                                    $uoef = $uof / $satis_euro_kuru1;
                                                    $uef = $uf / $satis_euro_kuru1;
                                                    $uodf1 = formatDovizListelemeNumber($uodf);
                                                    $udf1 = formatDovizListelemeNumber($udf);
                                                    $uof1 = formatDovizListelemeNumber($uof);
                                                    $uf1 = formatDovizListelemeNumber($uf);
                                                    $uoef1 = formatDovizListelemeNumber($uoef);
                                                    $uef1 = formatDovizListelemeNumber($uef);
                                                }elseif ($urun["DOVIZ_BIRIMI"] == '€'){
                                                    $uoef =  $urun["DSF".$uye_fiyat];
                                                    $uef =  $urun["DSF4"];
                                                    $uof = $uoef * $satis_euro_kuru1;
                                                    $uf = $uef * $satis_euro_kuru1;
                                                    $uodf = $uof / $satis_dolar_kuru1;
                                                    $udf = $uf / $satis_dolar_kuru1;
                                                    $uodf1 = formatDovizListelemeNumber($uodf);
                                                    $udf1 = formatDovizListelemeNumber($udf);
                                                    $uof1 = formatDovizListelemeNumber($uof);
                                                    $uf1 = formatDovizListelemeNumber($uf);
                                                    $uoef1 = formatDovizListelemeNumber($uoef);
                                                    $uef1 = formatDovizListelemeNumber($uef);
                                                }
                                            }else{
                                                $uof =  $urun["KSF".$uye_fiyat];
                                                $uf =  $urun["KSF4"];
                                                $uodf = $uof / $satis_dolar_kuru1;
                                                $udf = $uf / $satis_dolar_kuru1;
                                                $uoef = $uof / $satis_euro_kuru1;
                                                $uef = $uf / $satis_euro_kuru1;
                                                $uodf1 = formatDovizListelemeNumber($uodf);
                                                $udf1 = formatDovizListelemeNumber($udf);
                                                $uof1 = formatDovizListelemeNumber($uof);
                                                $uf1 = formatDovizListelemeNumber($uf);
                                                $uoef1 = formatDovizListelemeNumber($uoef);
                                                $uef1 = formatDovizListelemeNumber($uef);
                                            }
                                            $urunKdv = $urun['kdv'];
                                            $kdvOrani = 0.01 * (float)$urunKdv; // KDV oranını doğru bir şekilde hesaplamak için 0.01 ile çarptım
                                            $kuof = $uof * $kdvOrani;
                                            $tuof = $kuof + $uof;
                                            $kuodf = $uodf * $kdvOrani;
                                            $tuodf = $kuodf + $uodf;
                                            $kuoef = $uoef * $kdvOrani;
                                            $tuoef = $kuoef + $uoef;
                                            $kuof = formatDovizListelemeNumber($kuof);
                                            $tuof = formatDovizListelemeNumber($tuof);
                                            $kuodf = formatDovizListelemeNumber($kuodf);
                                            $tuodf = formatDovizListelemeNumber($tuodf);
                                            $kuoef = formatDovizListelemeNumber($kuoef);
                                            $tuoef = formatDovizListelemeNumber($tuoef);
                                            ?>
                                            <tr>
                                                <td class="fs-10 px-2 py-1" >TL</td>
                                                <td class="fs-10 px-2 py-1" ><?= $uf1 ?></td>
                                                <?php if($uye_fiyat != '4'){ ?>
                                                    <td class="fs-10 px-2 py-1" ><?= $uof1 ?></td>
                                                <?php } ?>
                                                <td class="fs-10 px-2 py-1" >% <?= $urun['kdv'] ?></td>
                                                <td class="fs-10 px-2 py-1" ><?= $kuof ?></td>
                                                <td class="fs-10 px-2 py-1 fw-bold" ><?= $tuof ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fs-10 px-2 py-1" >USD</td>
                                                <td class="fs-10 px-2 py-1" ><?= $udf1 ?></td>
                                                <?php if($uye_fiyat != '4'){ ?>
                                                    <td class="fs-10 px-2 py-1" ><?= $uodf1 ?></td>
                                                <?php } ?>
                                                <td class="fs-10 px-2 py-1" >% <?= $urun['kdv'] ?></td>
                                                <td class="fs-10 px-2 py-1" ><?= $kuodf ?></td>
                                                <td class="fs-10 px-2 py-1 fw-bold" ><?= $tuodf ?></td>
                                            </tr>
                                            <tr>
                                                <td class="fs-10 px-2 py-1" >EURO</td>
                                                <td class="fs-10 px-2 py-1" ><?= $uef1 ?></td>
                                                <?php if($uye_fiyat != '4'){ ?>
                                                    <td class="fs-10 px-2 py-1" ><?= $uoef1 ?></td>
                                                <?php } ?>
                                                <td class="fs-10 px-2 py-1" >% <?= $urun['kdv'] ?></td>
                                                <td class="fs-10 px-2 py-1" ><?= $kuoef ?></td>
                                                <td class="fs-10 px-2 py-1 fw-bold" ><?= $tuoef ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } ?>
                        <?php }
                        $checkResult = $database->fetchAll("SELECT * FROM nokta_urun_varyasyon WHERE FIND_IN_SET(:urunId, urun_id) > 0", ['urunId' => $urunId]  );
                        
                        if (count($checkResult) > 0) {
                            foreach ($checkResult as $row) {
                                $uniqueUrunIds = array();
                                $ad = $row['ad'];
                                $idsString = $row['urun_id'];
                                $idsArray = explode(',', $idsString);

                                // Sadece benzersiz urun_id'leri diziye ekle
                                $uniqueUrunIds = array_merge($uniqueUrunIds, array_unique($idsArray));

                                // Her bir ad için sadece bir kere echo yap
                                echo '<div class="mt-3">' . $ad . ':</div>';
                                // Benzersiz urun_id'leri kullanarak işlemleri gerçekleştir
                                foreach ($uniqueUrunIds as $imageId) {
                                    $urunAdiResult = $database->fetchAll("SELECT UrunAdiTR, renk, beden, seo_link, id FROM nokta_urunler WHERE id = :imageId LIMIT 1", ['imageId' => $imageId]);
                                    foreach ($urunAdiResult as $urunAdiRow) {
                                        $blkodu_grosel = $urunAdiRow['id'];
                                        if ($ad == 'Renk') {
                                            ?>
                                            <a href="tr/urunler/<?= $urunAdiRow['seo_link'] ?>" data-bs-toggle="tooltip" title="<?= $urunAdiRow['renk'] ?>">
                                                <?php
                                                $fot = $database->fetchAll("SELECT * FROM nokta_urunler_resimler WHERE urun_id = :gorsell LIMIT 1", ['gorsell' => $blkodu_grosel]);
                                                foreach ($fot as $k => $resim) {
                                                    if (empty($resim['KResim'])) {
                                                        ?>
                                                        <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/gorsel_hazirlaniyor.jpg" width="50px" height="50px" class="img-fluid rounded-2 mt-1 border rounded-2">
                                                        <?php
                                                    } else {
                                                        $renkler = [
                                                            'Mavi' => 'blue',
                                                            'Yeşil' => 'green',
                                                            'Kırmızı' => 'red',
                                                            'Sarı' => 'yellow',
                                                            'Siyah' => 'black',
                                                            'Turuncu' => 'orange',
                                                            'Mor' => 'purple',
                                                            // Diğer renkler için buraya ekleme yapabilirsiniz
                                                        ];
                                                        $renk = $urunAdiRow['renk'];
                                                        $arkaPlanRenk = isset($renkler[$renk]) ? $renkler[$renk] : 'gray'; // Varsayılan olarak gri
                                                        //echo '<a class="rounded-0 mt-1 me-1 border-0 p-2 text-decoration-none btn btn-sm btn-secondary" style="background-color: ' . $arkaPlanRenk . ';" href="assets/images/urunler/' . $resim['foto'] . '">' . $renk . '</a>';
                                                        ?>

                                                        <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/<?= $resim['KResim'] ?>" width="50px" height="50px" class="img-fluid rounded-2 mt-1 border rounded-2">
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </a>
                                            <?php
                                        } elseif ($ad == 'Metre') {
                                            if (!empty($urunAdiRow['beden'])) {
                                                ?>
                                                <a class="rounded-0 mt-1 me-1 border-0 p-2 text-decoration-none btn btn-sm btn-secondary" style="width: 55px;" href="tr/urunler/<?= $urunAdiRow['seo_link'] ?>"><?= $urunAdiRow['beden'] ?></a>
                                                <?php
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if($urun['proje'] == 0){ ?>
                        <div class="d-flex align-items-center mb-3 mt-4"><?php 
                            if($urun["stok"] > 0) {
                                if (empty($urun['miktar_seciniz'])) { ?>
                                    <div class="input-group me-3" style="width: 130px;">
                                        <button class="btn btn-lg btn-outline-secondary" type="button" id="decrementBtn">-</button>
                                        <input style="width: 45px" type="text" class="form-control text-center" id="quantityInput" value="1">
                                        <input type="hidden" id="maxStock" value="<?= $urun["stok"]; ?>">
                                        <button class="btn btn-lg btn-outline-secondary" type="button" id="incrementBtn">+</button>
                                    </div>
                                    <input type="text" id="output" hidden>
                                <?php 
                                } else {
                                    $miktarlar = $urun['miktar_seciniz'];
                                    $miktarDizisi = explode(",", $miktarlar);
                                    ?>
                                    <div class="input-group me-3" style="width: 130px;">
                                        <select class="form-select" id="output" aria-label="Miktar Seçiniz">
                                            <?php foreach ($miktarDizisi as $miktar): ?>
                                                <?php if($miktar <= $urun["stok"]){ ?>
                                                    <option value="<?= $miktar; ?>"><?= $miktar; ?></option>
                                                <?php } ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php 
                                }
                            } ?>
                        </div>
                            <?php if ($urun['stok'] < 1 || $urun['stok'] < 1){
                                $temsilci = $database->fetch("SELECT * FROM users WHERE id = :id", ['id' => $uye_satis_temsilci]);
                                ?>
                                <script>
                                    function openTemsilci() {
                                        Swal.fire({
                                            title: 'Satış Temsilciniz',
                                            html: '<div style="text-align: left;">' +
                                                '<p>Ad Soyad: <?= $temsilci['kullanici_ad']; ?>  <?= $temsilci['kullanici_soyad']; ?></p>' +
                                                '<p>Mail:  <a href="mailto: <?= $temsilci['kullanici_mail']; ?>"><?= $temsilci['kullanici_mail']; ?></a></p>' +
                                                '<p>Telefon Numarası: <?= $temsilci['kullanici_tel']; ?></p>' +
                                                '</div>',
                                            confirmButtonText: 'Tamam',
                                            customClass: {
                                                popup: 'custom-popup-class',
                                                title: 'custom-title-class',
                                                htmlContainer: 'custom-html-container-class'
                                            }
                                        });
                                    }
                                </script>
                                <?php
                                if (!empty($uye_satis_temsilci)) { ?>
                                    <button class="btn btn-lg me-1" style="background-color: #FC9803; color:white;" onclick="openTemsilci()">
                                        <i class="fa-solid fa-pen me-1"></i>Satış Temsilcinize Danışınız
                                    </button>
                                <?php } else { ?>
                                    <button class="btn btn-lg me-1 rounded-0" style="background-color: #FC9803; color:white;" onclick="openTemsilciAlert()">
                                        <i class="fa-solid fa-pen me-1"></i>Satış Temsilcinize Danışınız
                                    </button>
                                <?php } ?>
                            <?php } elseif(!empty($fiyat)) { ?>
                                <button type="submit" class="btn btn-lg sepetEkleBtn rounded-0"
                                        onclick="<?php if (isset($_SESSION['id'])): ?>
                                                sepeteUrunEkle(<?= $urunId; ?>, <?= isset($_SESSION['id']) ? $_SESSION['id'] : 'default_value'; ?>, document.getElementById('output').value);
                                        <?php else: ?>
                                                window.location.href = 'tr/giris';
                                        <?php endif; ?>">
                                    <i class="fa-solid fa-cart-shopping fa-lg me-2"></i><span class="fs-5">Sepete Ekle</span>
                                </button>

                            <?php }
                            } else { ?>
                                <button type="submit" class="btn btn-lg btn-danger me-1 teklifOnaybtn"><i class="fa-solid fa-reply fa-flip-horizontal me-1"></i>Teklif İste</button>
                            <?php } ?>

                        <div class="me-3 mt-3">
                            <div>
                                <a class="favori-buton text-decoration-none" style="cursor:pointer; color:black" data-product-id="<?= $urunId ?>">
                                    <i class="fa-regular fa-heart me-1"></i>Favorilerine Ekle
                                </a>
                            </div>
                            <div>
                                <a id="paylas"  class="text-decoration-none" style="cursor:pointer; color:black">
                                    <i class="fa-solid fa-share me-1"></i></i>Paylaş
                                </a>
                            </div>
                        </div>
                        <?php if (!empty($urun['birlikte_al'])) { ?>
                            <ul class="list-group row list-group-horizontal my-2 d-flex justify-content-start align-items-end">
                                <?php
                                $birlikte_al_ids = explode(',', $urun['birlikte_al']);
                                foreach($birlikte_al_ids as $birlikte_al_id) {
                                    $sepet = $database->fetch("SELECT DISTINCT nr.KResim, um.title, nu.*
                                            FROM nokta_urunler_resimler nr
                                            LEFT JOIN nokta_urunler nu ON nr.urun_id = nu.BLKODU
                                            LEFT JOIN nokta_urun_markalar um ON nu.MarkaID = um.id
                                            WHERE nu.id = :urun_id LIMIT 1", ['urun_id' => $birlikte_al_id]);
                                    foreach($sepet as $sepeturun) {
                                        ?>
                                        <li class="list-group-item rounded-0 col-5 mt-1 me-1 border">
                                            <div class="row">
                                                <?php if(empty($sepeturun['KResim'])){ ?>
                                                    <a href="tr/urunler/<?= $sepeturun['seo_link'] ; ?>" class="text-body text-decoration-none">
                                                        <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/gorsel_hazirlaniyor.jpg" style="width: 70px; border:1px solid lightgrey; border-radius: 6px">
                                                    </a>
                                                <?php }else{ ?>
                                                    <div class="col-3">
                                                        <a href="tr/urunler/<?= $sepeturun['seo_link'] ; ?>" class="text-body text-decoration-none">
                                                            <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/<?= $sepeturun["KResim"]; ?>" style="width: 50px; border:1px solid lightgrey; border-radius: 6px">
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                                <div class="col-9">
                                                    <a href="tr/urunler/<?= $sepeturun['seo_link'] ; ?>" class="text-body text-decoration-none">
                                                        <ul class="list-unstyled">
                                                            <li style="font-size:11pt"><?= $sepeturun["title"] ?> <?php $text = $sepeturun['UrunAdiTR']; echo (strlen($text) > 15) ? substr($text, 0, 15) . '...' : $text;?></li>
                                                            <li style="color:#FC9803; font-weight: bold"><?php $fiyat1 = !empty($sepeturun["DSF".$uyeFiyat]) ? $sepeturun["DSF".$uyeFiyat] : $sepeturun["KSF".$uyeFiyat];
                                                                $fiyat1_formatted = number_format((float) $fiyat1, 2, ',', '.');?>
                                                                <?= $fiyat1_formatted ?><?= !empty($sepeturun["DSF4"]) ? $sepeturun["DOVIZ_BIRIMI"] : "₺"; ?>
                                                            </li>
                                                        </ul>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <nav class="mt-5">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link clr active rounded-0" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-genel" type="button" role="tab" aria-controls="nav-genel" aria-selected="true">Genel Özellikler</button>
                    <button class="nav-link clr rounded-0" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-teknik" type="button" role="tab" aria-controls="nav-teknik" aria-selected="false">Teknik Özellikler</button>
                    <button class="nav-link clr rounded-0" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-indirmeler" type="button" role="tab" aria-controls="nav-indirmeler" aria-selected="false">İndirmeler</button>
                    <?php if(!empty($fiyat)) { ?>
                    <button class="nav-link clr rounded-0" id="nav-taksit-tab" data-bs-toggle="tab" data-bs-target="#nav-taksit" type="button" role="tab" aria-controls="nav-taksit" aria-selected="false">Taksit Seçenekleri</button>
                    <?php } ?>
                </div>
            </nav>
            <div class="tab-content bg-light" id="nav-tabContent">
                <div class="p-4 border tab-pane fade show active bg_fff" id="nav-genel" role="tabpanel" aria-labelledby="nav-genel-tab" tabindex="0" style="text-align: justify;">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="col-md-12 col-lg-9 text-center"><?= $urun['OzelliklerTR']; ?></div>
                    </div>
                </div>
                <div class="p-4 border tab-pane fade bg_fff" id="nav-teknik" role="tabpanel" aria-labelledby="nav-teknik-tab" tabindex="0">
                    <div class="col-12">
                        <div class="" style="overflow-x:auto;">
                            <?= $urun['BilgiTR']; ?>
                        </div>
                    </div>
                </div>
                <div class="p-4 border tab-pane fade bg_fff" id="nav-indirmeler" role="tabpanel" aria-labelledby="nav-indirmeler-tab" tabindex="0">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="w-100">
                            <?php
                            // Yukleme basliklarini cekmek icin bir sorgu yapabilirsiniz.
                            $yuklemeBasliklari = $database->fetchAll("SELECT * FROM nokta_yuklemeler WHERE is_active = 1");
                            foreach ($yuklemeBasliklari as $baslik) {
                                $yuklemeID = $baslik['id'];
                                $baslikAdi = $baslik['baslik'];

                                $yuklemeler = $database->fetchAll("SELECT * FROM nokta_urunler_yuklemeler WHERE urun_id = $urunId AND yukleme_id = $yuklemeID");

                                if (!empty($yuklemeler)) {
                                    ?>
                                    <table class="table table-light table-striped table-bordered second mt-4">
                                        <thead class="bg-light">
                                        <h5></h5>
                                        <tr>
                                            <td colspan="5" class="p-2 text-light fw-bold" style="background-color: #430666;"><?= $baslikAdi; ?></td>
                                        </tr>
                                        <tr class="">
                                            <th style="width: 10%; background-color: #f8f9fa;">ID</th>
                                            <th style="width: 10%; background-color: #f8f9fa;">Tarih</th>
                                            <th style="width: 10%; background-color: #f8f9fa;">Sürüm</th>
                                            <th style="width: 60%; background-color: #f8f9fa;">Açıklama</th>
                                            <th class="text-center" style="width: 10%; background-color: #f8f9fa">İndir</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($yuklemeler as $row) {
                                            ?>
                                            <tr>
                                                <td><?= $row['id']; ?></td>
                                                <td><?= date("Y-m-d", strtotime($row['datetime'])); ?></td>
                                                <td><?= $row['version']; ?></td>
                                                <td><?= $row['aciklama']; ?></td>
                                                <?php
                                                $dYol = $row["url_path"];
                                                $dUzanti = pathinfo($dYol, PATHINFO_EXTENSION);
                                                $dUrunAdi = duzenleString1($urun['UrunKodu']);
                                                $dBaslik =duzenleString1($baslikAdi);
                                                ?>
                                                <td class="text-center">
                                                    <a href="https://noktanet.s3.eu-central-1.amazonaws.com<?= $row["url_path"]; ?>">
                                                        <?php
                                                        $dUzanti = pathinfo($dYol, PATHINFO_EXTENSION);
                                                        switch ($dUzanti) {
                                                            case "pdf":
                                                                $iconClass = "fa-solid fa-file-pdf fa-lg text-danger";
                                                                break;
                                                            case "doc":
                                                            case "docx":
                                                                $iconClass = "fa-solid fa-file-word fa-lg";
                                                                break;
                                                            case "xls":
                                                            case "xlsx":
                                                                $iconClass = "fa-solid fa-file-excel fa-lg";
                                                                break;
                                                            case "jpg":
                                                            case "jpeg":
                                                            case "png":
                                                            case "gif":
                                                                $iconClass = "fa-solid fa-file-image fa-lg";
                                                                break;
                                                            case "zip":
                                                            case "rar":
                                                                $iconClass = "fa-solid fa-file-zipper fa-lg";
                                                                break;
                                                            default:
                                                                $iconClass = "fa-solid fa-file fa-lg";
                                                        }
                                                        // Output the Font Awesome icon
                                                        echo "<i class='$iconClass'></i>";
                                                        ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } // Eğer yüklemeler yoksa tabloyu oluşturma ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php if(!empty($fiyat)) { ?>
                <div class="p-4 border tab-pane fade bg_fff" id="nav-taksit" role="tabpanel" aria-labelledby="nav-taksit-tab" tabindex="0" style="text-align: justify;">
                    <div class="col-12 d-flex justify-content-center mt-3">
                        <div class="col-12">
                            <div class="row">
                                <?php
                                    function renderCard($database, $kart_id, $tuof, $img_src, $bg_color = '#fafafa') {
                                        $d = $database->fetchAll("SELECT * FROM b2b_banka_taksit_eslesme WHERE aktif = 1 AND kart_id = :kartid ORDER BY taksit ASC", ['kartid' => $kart_id]);
                                        ?>
                                        <div class="col-sm-6 col-md-6 col-lg-3 card rounded-0 border-0 p-0" style="background-color: <?= $bg_color ?>;">
                                            <div class="d-flex align-items-center justify-content-center" style="width: 100%; height: 40px;background-color: #ecedee;">
                                                <img src="<?= $img_src ?>" style="color:white" width="25%">
                                            </div>
                                            <div class="mt-3 mb-3 p-3">
                                                <table class="table table-light">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-end">Taksit</th>
                                                            <th class="text-end">Taksit Tutarı</th>
                                                            <th class="text-end">Toplam Tutar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($d as $row): 
                                                            $tuof1 = str_replace(['.', ','], ['', '.'], $tuof);
                                                            $vadee1 = str_replace(',', '.', $row["vade"]);
                                                            $top_fiy = $tuof1 + ($tuof1 * $vadee1 / 100);
                                                            $taksit = $row["taksit"] ?: 1;
                                                            $aylik_fiy = $top_fiy / $taksit;
                                                        ?>
                                                            <tr>
                                                                <th class="text-end"><?= $taksit ?></th>
                                                                <td class="text-end"><?= number_format($aylik_fiy, 2, ',', '.') ?></td>
                                                                <td class="text-end"><?= number_format($top_fiy, 2, ',', '.') ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <?php
                                    }

                                    $cards = [
                                        ['kart_id' => 3, 'img_src' => 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banka/axess.svg'],
                                        ['kart_id' => 5, 'img_src' => 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banka/world.svg'],
                                        ['kart_id' => 7, 'img_src' => 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banka/maximum.svg'],
                                        ['kart_id' => 1, 'img_src' => 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banka/bonus-kart.svg'],
                                        ['kart_id' => 2, 'img_src' => 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banka/bank-kart.svg']
                                    ];

                                    foreach ($cards as $card) {
                                        renderCard($database, $card['kart_id'], $tuof, $card['img_src']);
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <!--URUNLER SLIDER -->
        <div class="pt-2 pb-2">
            <div class="mt-5 mb-5 bg_fff border">
                <h4 class="py-2 ps-4 border-bottom">Benzer Ürünler</h4>
                <div class="splide" id="benzerurun">
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php
                                $d = $database->fetchAll(
                                    "SELECT * FROM nokta_urunler WHERE web_comtr = 1 AND KategoriID = :KategoriID AND id != :urunId ORDER BY id ASC LIMIT 15;", 
                                    ['KategoriID' => $categoryId, 'urunId' => $urunId]
                                );
                                foreach ($d as $row) {
                                    // Ürün resmini getirmek için sorgu
                                    $resim = $database->fetch("SELECT * FROM nokta_urunler_resimler WHERE UrunID = :urun_id ORDER BY sira ASC LIMIT 1", ['urun_id' => $row['id']]);
                                    $resim_yolu = $resim ? "https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/" . $resim["KResim"] 
                                    : "https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/gorsel_hazirlaniyor.jpg";

                                    // Marka bilgisi
                                    $marka = $database->fetch("SELECT * FROM nokta_urun_markalar WHERE id = :id", ['id' => $row['MarkaID']]);
                                    ?>

                                    <li class="splide__slide d-flex justify-content-center mb-4 mt-2">
                                        <div class="card border-0 p-0 col-lg-3 col-md-3 col-sm-6 col-xs-12 urun-effect" style="width: 15rem;">
                                            <a href="tr/urunler/<?= htmlspecialchars($row['seo_link']) ?>">
                                                <div class="rounded-3 w-100 d-flex align-items-center" style="height: 245px;">
                                                    <img src="<?= htmlspecialchars($resim_yolu) ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($row['UrunAdiTR']) ?>">
                                                </div>
                                            </a>
                                            <div class="card-body d-flex flex-column">
                                                <a href="tr/urunler/<?= htmlspecialchars($row['seo_link']) ?>" style="font-weight:600;" class="mt-2 urun-a">
                                                    <?= (strlen($row['UrunAdiTR']) > 52) ? htmlspecialchars(substr($row['UrunAdiTR'], 0, 51)) . '...' : htmlspecialchars($row['UrunAdiTR']); ?>
                                                </a>
                                                <a style="font-size:12px; color:#0a90eb;" class="mt-2 urun-a border-bottom">
                                                    <?= htmlspecialchars($marka['title'] ?? '') ?>
                                                </a>
                                                <a style="font-size:12px;" class="mb-2 urun-a">Stok Kodu<span class="ps-1">:</span><?= htmlspecialchars($row['UrunKodu']) ?></a>

                                                <?php if (isset($_SESSION['id'])){ ?>
                                                    <a style="font-size:14px;" class="urun-a custom-underline">
                                                        <?= htmlspecialchars($row["DOVIZ_BIRIMI"] ?? "₺") . formatNumber($row["DSF4"] ?? $row["KSF4"] ?? 0) ?> + KDV
                                                    </a>
                                                    <a style="font-size:14px; color:#0a90eb;" class="urun-a fw-bold mt-1">Size Özel Fiyat</a>
                                                    <a style="font-size:14px; color:#f29720;" class="urun-a fw-bold">
                                                        <?= htmlspecialchars($row["DOVIZ_BIRIMI"] ?? "₺") . formatNumber($row["DSF" . ($uye_fiyat ?? "4")] ?? $row["KSF" . ($uye_fiyat ?? "4")] ?? 0) ?> + KDV
                                                    </a>
                                                    <i class="fa-solid fa-cart-shopping fa-xl sepet-style sepet-hover" onclick="<?php echo isset($_SESSION['id']) ? 'sepeteUrunEkle(' . htmlspecialchars($row['id']) . ', ' . htmlspecialchars($_SESSION['id']) . ');' : 'window.location.href = \"tr/giris\";'; ?>"></i>
                                                <?php }else{ ?>
                                                        <a style="font-size:14px; color:#f29720;" class="urun-a fw-bold">
                                                            <?= !empty($row["DSF4"]) ? $row["DOVIZ_BIRIMI"] : "₺";
                                                            $fiyat1 = !empty($row["DSF4"]) ? $row["DSF4"]: $row["KSF4"];
                                                            echo formatNumber($fiyat1);?> + KDV
                                                        </a><?php
                                                    }  ?>
                                            </div>
                                        </div>
                                    </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- URUNLER SLIDER SONU -->
    </div>
</section>
<div style="clear:both"></div>
<?php $template->footer(); ?>
</body>
<!-- TEKLIF MODAL -->
<div class="modal fade" data-bs-backdrop="static" id="teklifOnayModal" role="dialog" aria-labelledby="teklifOnayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="teklifOnayModalLabel">Teklif Formu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="applicationForm" method="post" class="needs-validation">
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <div class="col-sm-12 pb-2">
                                <p class="border-bottom pb-3">Teklifiniz ile ilgili detayları aşağıda açıklayarak bize iletebilirsiniz.</br> Teklifiniz en kısa sürede yanıtlanacaktır.</p>
                            </div>
                            <div class="col-sm-12">
                                <label for="email" class="form-label">E-Posta</label>
                                <input type="email" class="form-control" id="email" placeholder="mail@example.com" required>
                                <div class="invalid-feedback">Geçerli e-posta giriniz!</div>
                            </div>
                            <div class="col-sm-12">
                                <label for="teklif_nedeni" class="form-label">Açıklama</label>
                                <?php $uye_idip = isset($_SESSION['id']) ? $_SESSION['id'] : $_SERVER['REMOTE_ADDR']; ?>
                                <input type="text" id="uye_id" value="<?= htmlspecialchars($uye_idip); ?>" hidden>
                                <input type="text" id="urun_no" value="<?= $urunId ?>" hidden>
                                <textarea type="text" class="form-control" id="teklif_nedeni" required></textarea>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <button class="w-100 btn btn-primary teklifOnayDevambtn" id="teklifOnayBtn" type="submit">Devam Et</button>
                </form>
            </div>
        </div>
    </div>
</div>
</html>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/splide/splide.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/lightbox.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var benzerurunSlider = document.getElementById('benzerurun');

        // Check if the element exists
        if (benzerurunSlider !== null) {
            var splide = new Splide(benzerurunSlider, {
                pagination: false,
                arrows: false,
                perPage: 5,
                perMove: 2,
                lazyLoad: 'nearby',
                breakpoints: {
                    1420: {
                        perPage: 5,
                        gap: '.7rem',
                    },
                    1399: {
                        perPage: 3,
                        gap: '.5rem',
                    },
                    992: {
                        perPage: 2,
                        gap: '.7rem',
                    }
                },
            });

            splide.mount();
        } else {
            console.error("Element with id 'benzerurun' not found. Splide initialization aborted.");
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize main slider
        var mainSlider = new Splide('#main-slider', {
            fixedWidth: '100%',
            heightRatio: 1,
            arrows:false,
            pagination: true,
            cover: true,
            focus: 'center'
        }).mount();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const decrementBtn = document.getElementById('decrementBtn');
        const incrementBtn = document.getElementById('incrementBtn');
        const quantityInput = document.getElementById('quantityInput');
        const maxRakam = document.getElementById('maxStock');
        let maxValue = parseInt(maxRakam.value, 10);

        // Set min and max values
        const minValue = 1;

        decrementBtn.addEventListener('click', function () {
            updateQuantity(-1);
        });

        incrementBtn.addEventListener('click', function () {
            updateQuantity(1);
        });

        // Add input event listener to restrict user input
        quantityInput.addEventListener('input', function () {
            restrictInputRange();
            // Display the current value
            displayCurrentValue();
        });

        function updateQuantity(amount) {
            let currentValue = parseInt(quantityInput.value, 10);
            if (!isNaN(currentValue)) {
                const newValue = Math.max(Math.min(currentValue + amount, maxValue), minValue);
                quantityInput.value = newValue;
                restrictInputRange(); // Restrict input range after updating the value
                // Display the current value
                displayCurrentValue();
            }
        }

        function restrictInputRange() {
            // Ensure the input value is within the specified range
            let currentValue = parseInt(quantityInput.value, 10);
            if (isNaN(currentValue)) {
                quantityInput.value = minValue;
            } else {
                quantityInput.value = Math.max(Math.min(currentValue, maxValue), minValue);
            }
        }
        function displayCurrentValue() {
            // Display the current value
            const currentValue = quantityInput.value;
            document.getElementById('output').value = currentValue;
        }
    });
</script>
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
<script>
    $(document).ready(function () {
        $('.favori-buton').on('click', function (event) {
            event.preventDefault();
            var productId = $(this).data('product-id');
            var uye_id = <?= $_SESSION["id"] ?>;
            var type = 'favori';
            // Store the reference to $(this) in a variable for later use
            var that = $(this);
            // Send an AJAX request to the server to add the product to the favorites
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
    $(document).ready(function() {
        $('.teklifOnaybtn').click(function() {
            $('#teklifOnayModal').modal('show');
        });
        $('#applicationForm').submit(function(e) {
            e.preventDefault();


            var uye_id = $('#uye_id').val();
            var teklif_nedeni = $('#teklif_nedeni').val();
            var urun_no = $('#urun_no').val();
            var email = $('#email').val();
            $.ajax({
                type: 'POST',
                url: 'functions/edit_info.php',
                data: {
                    uye_id: uye_id,
                    email: email,
                    teklif_nedeni: teklif_nedeni,
                    urun_no: urun_no,
                    type: 'teklif'
                },
                success: function(don) {
                    console.log(don);
                    $('#teklifOnayModal').modal('hide');
                    Swal.fire({
                        title: "Teklifiniz Alınmıştır!",
                        icon: "success",
                        showConfirmButton: false
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
    document.getElementById("paylas").addEventListener("click", function() {
        // Paylaşım işlemini başlat
        if (navigator.share) {
            navigator.share({
                title: 'Paylaş',
                url: window.location.href
            })
        } else {
            // Tarayıcı paylaşım API'si desteklenmiyorsa, alternatif bir mesaj gösterin veya işlem yapın
            alert('Üzgünüz, tarayıcınız paylaşım işlemini desteklemiyor.');
        }
    });
</script>
<script>
    function openTemsilciAlert() {
        Swal.fire({
            title: 'İletişim Bilgileri',
            html: '<div style="text-align: left;">' +
                '<p>Mail:  <a href="mailto:destek@noktaelektronik.com.tr">destek@noktaelektronik.com.tr</a></p>' +
                '<p>Telefon Numarası: 0850 333 02 08</p>' +
                '</div>',
            confirmButtonText: 'Tamam',
            customClass: {
                popup: 'custom-popup-class',
                title: 'custom-title-class',
                htmlContainer: 'custom-html-container-class'
            }
        });
    }
</script>