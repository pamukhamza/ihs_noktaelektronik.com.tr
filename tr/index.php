<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $key => $value) {
        echo $key . ': ' . $value . '<br>';
    }
}
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'index';
$template = new Template('Nokta Elektronik ve Bilişim Sistemleri', $currentPage);

$template->head();

?>
<style>
        .zoom-effect {position: relative;overflow: hidden;}
        .zoom-animation {animation: zoomInOut 0.3s alternate;}
        @keyframes zoomInOut {
            0% {transform: scale(1);}
            50% {transform: scale(1.1);}
            100% {transform: scale(1);}
        }
        .urun-effect {transition: transform 0.3s ease;width: 15rem;}
        .urun-effect:hover {box-shadow: 0px 0px 6px #888888;}
        input {background-color: #D6E0E3;}
        .urun-a {
            text-decoration: none;
            color: black;
            font-size: 14px;
        }
        .favori-icon:hover {color: red;cursor: pointer;}
        .kategori-effect li {transition: transform 0.3s ease;}
        .kategori-effect li:hover {color: purple;}
        .urun-effect {transition: transform 0.3s ease;}
        .urun-effect:hover {box-shadow: 2px;}
        .sepet-style {
        cursor: pointer;
        position: absolute;
        bottom: 30px;
        right: 20px;
        }
        .custom-underline { text-decoration: line-through;}
        #stokArama1:focus {box-shadow: 0 0 0 0 rgba(13, 110, 253, .25);}
        .mobil-banner {display: none;}
        @media(max-width: 992px) {
            .banner-responsive {display: none;}
            .kategoriler {display: none;}
            .mobil-banner {display: block;}
        }
        @media(min-width: 992px) {
            .mobil-arama {display: none;}
        }
        @media(min-width: 1400px) {
            .slider-carousel {float: right;width: 990px;}
        }
        @media(min-width: 992px) and (max-width: 1200px) {
            .slider-carousel {float: right;width: 640px;}
        }
        @media(min-width: 1200px) and (max-width: 1400px) {
            .slider-carousel {float: right;width: 820px;}
        }
        .teklifiste-style{
            position: absolute;
            bottom: 20px;
            width: 203px;
            height: 38px;
        }
        .position-relative {position: relative;}
        .play-button {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.3s;
            z-index: 1; /* Buton video öğesinin üzerinde görünsün */
        }
        .play-button:hover {opacity: 1;}
        .play-button svg {fill: #fff;}
        .kategori-effect li {
            position: relative;
        }
        .kategori-effect li:hover .sub-category {display: block;}
        .sub-category {
            min-width: 200px; /* Genişlik */
            left: 280px; /* Ana kategorilerin genişliğine göre ayarlanmalı */
            top: 0;
            z-index: 10;
        }
        .sub-category a:hover {background-color: #f8f9fa;color: #333;}
    </style>    
<body>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-NE2FRWRNBJ"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-NE2FRWRNBJ');
</script>
<!-- HEADER ALANI -->
<?php 
    $template->header();
?>
<!-- HEADER ALANI SONU -->

    <!-- SLIDER ALANI -->
    <div class="container mt-4 mb-5">
        <form onsubmit="performSearchMobil(); return false;">
            <div class="input-group mb-4 rounded-5 mobil-arama">
                <input type="text" class="form-control rounded-start-pill ps-4" id="stokArama1" placeholder="Ara" style="background-color: white; border-color: #fc9803; outline: none;">
                <button class="btn btn-outline-secondary bg-turuncu rounded-end-circle" type="submit" style="color:white; border-color: transparent">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
        <!-- Kategoriler -->
        <div class="float-start rounded-3 kategoriler" style="width:280px; height:100%;">
            <div class="border-0 rounded-3 shadow" style="background-color: #ffffff;">
                <ul class="list-unstyled ps-0 kategori-effect ">
                    <?php
                        $kategori_rows = $database->fetchAll("SELECT * FROM nokta_kategoriler WHERE parent_id = 0 AND web_comtr = 1 ORDER BY sira ASC LIMIT 11");
                        foreach ($kategori_rows as $kategori_row) {
                            // Alt kategorileri kontrol et
                            $sub_kategori_count = $database->fetchColumn("SELECT COUNT(*) FROM nokta_kategoriler WHERE parent_id = :parent_id AND web_comtr = 1", ['parent_id' => $kategori_row['id']]);
                    ?>
                        <li class="border-bottom position-relative">
                            <a href="tr/urunler?cat=<?= $kategori_row['seo_link'] ?>&brand=&filter=&search=" style="text-align: left !important; font-weight: 500" class="btn d-inline-flex align-items-center rounded border-0 collapsed">
                                <?= $kategori_row['KategoriAdiTr']; ?>
                                <?php if ($sub_kategori_count > 0) { ?>
                                    <i class="fa-solid fa-angle-right fa-2xs" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%);"></i>
                                <?php } ?>
                            </a>
                            <?php if ($sub_kategori_count > 0) { ?>
                                <!-- Alt kategoriler -->
                                <ul class="sub-category list-unstyled position-absolute p-2 bg-white shadow rounded-3" style="top: 0; left: 280px; display: none;">
                                    <?php
                                        $sub_kategori_rows = $database->fetchAll("SELECT * FROM nokta_kategoriler WHERE parent_id = :parent_id AND web_comtr = 1 ORDER BY sira ASC", ['parent_id' => $kategori_row['id']]);
                                        foreach ($sub_kategori_rows as $sub_kategori_row) {
                                    ?>
                                        <li class="mb-1">
                                            <a href="tr/urunler?cat=<?= $sub_kategori_row['seo_link'] ?>&brand=&filter=&search=" style="text-align: left !important;" class="btn d-inline-flex align-items-center rounded border-0">
                                                <?= $sub_kategori_row['KategoriAdiTr']; ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <script>
            document.querySelectorAll('.kategori-effect li').forEach(function(item) {
                item.addEventListener('mouseover', function() {
                    let subMenu = this.querySelector('.sub-category');
                    if (subMenu) { subMenu.style.display = 'block'; }
                });
                item.addEventListener('mouseout', function() {
                    let subMenu = this.querySelector('.sub-category');
                    if (subMenu) { subMenu.style.display = 'none'; }
                });
            });
        </script>
        <!-- Kategoriler Sonu -->
        <!-- SLIDER ALANI BAŞLANGIÇ -->
            <div class="slider-carousel shadow mb-4">
                <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded-2">
                        <?php
                            $d = $database->fetchAll("SELECT * FROM slider WHERE `site` = 'b2b' AND is_active = 1 ORDER BY order_by ASC");
                            $first = true;
                                foreach ($d as $k => $row) {
                                    // Add active class for the first item, don't add for others
                                    $activeClass = $first ? 'active' : '';
                                    echo '<div class="carousel-item ' . $activeClass . '" data-bs-interval="3000">
                                            <a href="' . $row["link"] . '">
                                                <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/slider/' . $row["photo"] . '" class="d-block w-100 rounded-2" alt="...">
                                            </a>
                                        </div>';
                                    // Change the flag after marking the first item
                                    $first = false;
                                }
                        ?>
                    </div>
                    <!-- Carousel Indicators (Dots) -->
                    <div class="carousel-indicators">
                        <?php
                        // No need to iterate over items, just create buttons for each item
                        for ($i = 0; $i < count($d); $i++) {
                            // Add active class for the first item, don't add for others
                            $activeClass = ($i == 0) ? 'active' : '';
                            echo '<button type="button" data-bs-target="#carouselExample" data-bs-slide-to="' . $i . '" class="' . $activeClass . '"></button>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        <!-- SLIDER ALANI SONU -->
    </div>
    <div class="container mt-5">
        <!-- Markalarimiz -->
            <div class="slider rounded-3 mt-5">
                <div class="slide-track" id="slideTrack">
                    <?php
                    $q = "SELECT * FROM nokta_urun_markalar WHERE web_comtr = 1 ORDER BY order_by ASC";
                    $brands = $database->fetchAll($q);

                    foreach($brands as $row) {
                        ?>
                        <div class="slide-marka d-flex justify-content-center">
                            <a href="tr/markalar"><img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/brands/<?= $row['hover_img']; ?>" style="max-height:100px; height:100%;" alt="" /></a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <!-- Markalarimiz Sonu -->
        <!-- BANNER ALANI -->
        <div class="d-flex mt-4">
            <div class="row banner-responsive">
                <div class="col-12 col-md-8 d-flex justify-content-center">
                    <div class="row">
                    <?php
                    $q = "SELECT * FROM b2b_banner WHERE id = '1'";
                    $d = $database->fetch($q);
                    if ($d["aktif"] == 1) { ?>
                        <div class="col-12 col-md-8 mt-2">
                            <a href="<?= htmlspecialchars($d['banner_link']); ?>">
                                <img src="assets/images/<?= htmlspecialchars($d['banner_foto']); ?>" class="shadow-sm rounded" data-banner-id="1" width="100%" alt="">
                            </a>
                        </div>
                    <?php } else {
                        $q = "SELECT * FROM b2b_banner_video WHERE id = '1'";
                        $d = $database->fetch($q); ?>
                        <div class="col-12 col-md-8 mt-2 position-relative">
                            <video id="myVideo" class="rounded shadow-sm" data-banner-video-id="1" style="width: 100%; height: auto;" controls>
                                <source src="assets/uploads/videolar/banner_video1.mp4" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <div class="play-button position-absolute">
                                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 5v14l11-7L8 5z" fill="#fff"/>
                                </svg>
                            </div>
                        </div>
                    <?php } ?>
                    <?php $q = "SELECT * FROM b2b_banner WHERE id = '2'";
                        $d = $database->fetchAll($q);
                        foreach( $d as $k => $row ){?>
                            <div class="col-12 col-md-4 mt-2">
                                <a href="<?=  $row['banner_link']; ?>"><img  src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/<?=  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="2" width="100%" alt=""></a>
                            </div>
                        <?php }
                        $q = "SELECT * FROM b2b_banner WHERE id = '3'";
                        if ( $d = $database->fetchAll($q) ){
                        foreach( $d as $k => $row ){?>
                            <div class="col-12 col-md-4 mt-3">
                                <a href="<?=  $row['banner_link']; ?>"><img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/<?=  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="3" width="100%" height="100%" alt=""></a>
                            </div>
                        <?php }}
                        $q = "SELECT * FROM b2b_banner WHERE id = '4'";
                        if ( $d = $database->fetchAll($q) ){
                        foreach( $d as $k => $row ){?>
                            <div class="col-12 col-md-8 mt-3">
                                <a href="<?=  $row['banner_link']; ?>"><img  src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/<?=  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="4" width="100%" alt=""></a>
                            </div>
                        <?php }}
                        $q = "SELECT * FROM b2b_banner WHERE id = '5'";
                        if ( $d = $database->fetchAll($q) ){
                        foreach( $d as $k => $row ){?>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <a href="<?=  $row['banner_link']; ?>"><img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/<?=  $row['banner_foto']; ?>" class="shadow-sm mt-2 rounded" data-banner-id="5" height="100%" width="100%" alt=""></a>
                </div>
                <?php }} ?>
            </div>
        </div>
        <!-- BANNER ALANI SONU -->
        <!-- MOBIL BANNER ALANI -->
        <div class="d-flex mt-4 ">
            <div class="row banner-responsive mobil-banner">
                    <div class="col-12 d-flex justify-content-center ">
                    <div class="row">
                    <?php $q = "SELECT * FROM b2b_banner WHERE id = '1'";
                    if ( $d = $database->fetchAll($q) ){
                        foreach( $d as $k => $row ){ ?>
                                <!--
                            <div class="col-12 mt-3">
                                <a href="<?=  $row['banner_link']; ?>"><img  src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/<?=  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="1" width="100%" alt=""></a>
                            </div>-->
                            <div class="col-12 mt-3">
                                <video class="rounded shadow-sm" style="width: 100%; height: auto;" controls>
                                    <source src="assets/uploads/videolar/banner_video1.mp4" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        <?php }}
                    $q = "SELECT * FROM b2b_banner WHERE id = '2'";
                    if ( $d = $database->fetchAll($q) ){
                        foreach( $d as $k => $row ){?>
                            <div class="col-6 mt-3">
                                <a href="<?=  $row['banner_link']; ?>"><img  src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/<?=  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="2" width="100%" alt=""></a>
                            </div>
                        <?php }}
                    $q = "SELECT * FROM b2b_banner WHERE id = '3'";
                    if ( $d = $database->fetchAll($q) ){
                        foreach( $d as $k => $row ){?>
                            <div class="col-6 mt-3">
                                <a href="<?=  $row['banner_link']; ?>"><img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/<?=  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="3" width="100%" height="100%" alt=""></a>
                            </div>
                        <?php }}
                    $q = "SELECT * FROM b2b_banner WHERE id = '4'";
                    if ( $d = $database->fetchAll($q) ){
                        foreach( $d as $k => $row ){?>
                            <div class="col-12 mt-3">
                                <a href="<?=  $row['banner_link']; ?>"><img  src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/<?=  $row['banner_foto']; ?>" class="shadow-sm rounded" data-banner-id="4" width="100%" alt=""></a>
                            </div>
                        <?php }} ?>
                            </div>
                            </div>
                        </div>
                    </div>
        </div>
        <!-- MOBIL BANNER ALANI SONU -->
        <!--URUNLER SLIDER -->
        <div class="pb-2">
            <div class="container mt-5">
                <div class="splide mt-0" id="first-splide">
                    <div class="splide__track">
                        <ul class="splide__list">
                        <?php
                            $q = "SELECT u.*, r.KResim, m.title AS MarkaAdi
                                    FROM nokta_urunler u
                                    LEFT JOIN nokta_urunler_resimler r ON u.id = r.UrunID
                                    LEFT JOIN nokta_urun_markalar m ON u.MarkaID = m.id
                                    WHERE u.YeniUrun = 1 AND u.web_comtr = 1
                                    GROUP BY u.id
                                    ORDER BY u.id ASC
                                    LIMIT 10";
                            
                                $d = $database->fetchAll($q);
                                
                                if ($d) {
                                    foreach ($d as $row) {
                                        $resim_yolu = $row['KResim'] ? "https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/" . $row['KResim'] : "assets/images/card-img.jpg";
                                        $text_to_display = $row['UrunAdiTR'];
                                        ?>
                                
                                        <li class="splide__slide d-flex justify-content-center px-3 mb-4 mt-2">
                                            <div class="card p-0 col-lg-3 col-md-3 col-sm-6 col-xs-12 my-2 mx-2 urun-effect" style="width: 15rem;">
                                                <a href="tr/urunler/<?= htmlspecialchars($row['seo_link']); ?>">
                                                    <div class="rounded-3 w-100 d-flex align-items-center" style="height: 245px;">
                                                        <img src="<?= htmlspecialchars($resim_yolu); ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($text_to_display); ?>">
                                                    </div>
                                                </a>
                                                <div class="card-body d-flex flex-column">
                                                    <a style="font-weight:600;" class="mt-2 urun-a">
                                                        <?= (strlen($text_to_display) > 50) ? substr($text_to_display, 0, 49) . '...' : htmlspecialchars($text_to_display); ?>
                                                    </a>
                                                    <a style="font-size:12px;" class="mt-2 urun-a border-bottom"><?= htmlspecialchars($row['MarkaAdi'] ?? 'Marka Yok'); ?></a>
                                                    <a style="font-size:12px;" class="mb-2 urun-a">Stok Kodu<span class="ps-1">:</span><?= htmlspecialchars($row['UrunKodu']); ?></a>
                                
                                                    <?php if ($row['proje'] == 0 && isset($_SESSION['id'])) {
                                                        $uye = $database->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $_SESSION['id']]);
                                                        $uye_fiyat = $uye['fiyat'] ?? 0;
                                
                                                        if ($uye_fiyat != 4) { ?>
                                                            <a style="font-size:14px;" class="urun-a custom-underline">
                                                                <?= !empty($row["DSF4"]) ? $row["DOVIZ_BIRIMI"] : "₺"; ?>
                                                                <?= formatNumber($row["DSF4"] ?? $row["KSF4"]); ?> + KDV
                                                            </a>
                                
                                                            <a style="font-size:14px; color:red;" class="urun-a fw-bold mt-1">Size Özel Fiyat</a>
                                                        <?php } ?>
                                
                                                        <a style="font-size:14px;" class="urun-a fw-bold">
                                                            <?= !empty($row["DSF4"]) ? $row["DOVIZ_BIRIMI"] : "₺"; ?>
                                                            <?= formatNumber($row["DSF$uye_fiyat"] ?? $row["KSF$uye_fiyat"]); ?> + KDV
                                                        </a>
                                                        <?php $urunId = $row['id']; ?>
                                                        <i class="fa-solid fa-cart-shopping fa-xl sepet-style" onclick="sepeteUrunEkle(<?= $row['id']; ?>, <?= $_SESSION['id'] ?? 'null'; ?>);"></i>
                                                    <?php } else { ?>
                                                        <button type="submit" class="btn btn-danger mt-3 teklifOnaybtn">
                                                            <i class="fa-solid fa-reply fa-flip-horizontal"></i> Teklif İste
                                                        </button>
                                                    <?php } ?>
                                                </div>
                                
                                                <a href="#" class="rounded-1 text-decoration-none" style="font-size:13px; color:white; background: rgba(255, 0, 0, 0.6); padding:2px; position: absolute; top: 10px; left: 10px;">
                                                    <i class="fa-solid fa-bullhorn pe-1"></i>Yeni!
                                                </a>
                                            </div>
                                        </li>
                                
                                        <?php
                                    }
                                }
                            ?>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- URUNLER SLIDER SONU -->
        <!--URUNLER SLIDER2 -->
        <div class="pt-2 pb-2 ">
            <div class="container mt-2">
                <div class="splide mt-4" id="second-splide">
                    <div class="splide__track">
                        <ul class="splide__list">
                        <?php
                            $q = "SELECT u.*, r.KResim, m.title AS MarkaAdi
                                    FROM nokta_urunler u
                                    LEFT JOIN nokta_urunler_resimler r ON u.id = r.UrunID
                                    LEFT JOIN nokta_urun_markalar m ON u.MarkaID = m.id
                                    WHERE u.stok > 1 AND u.web_comtr = 1
                                    GROUP BY u.id
                                    ORDER BY u.id ASC
                                    LIMIT 10";
                            
                                $d = $database->fetchAll($q);
                                
                                if ($d) {
                                    foreach ($d as $row) {
                                        $resim_yolu = $row['KResim'] ? "https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/" . $row['KResim'] : "assets/images/card-img.jpg";
                                        $text_to_display = $row['UrunAdiTR'];
                                        ?>
                                
                                        <li class="splide__slide d-flex justify-content-center px-3 mb-4 mt-2">
                                            <div class="card p-0 col-lg-3 col-md-3 col-sm-6 col-xs-12 my-2 mx-2 urun-effect" style="width: 15rem;">
                                                <a href="tr/urunler/<?= htmlspecialchars($row['seo_link']); ?>">
                                                    <div class="rounded-3 w-100 d-flex align-items-center" style="height: 245px;">
                                                        <img src="<?= htmlspecialchars($resim_yolu); ?>" class="card-img-top img-fluid" alt="<?= htmlspecialchars($text_to_display); ?>">
                                                    </div>
                                                </a>
                                                <div class="card-body d-flex flex-column">
                                                    <a style="font-weight:600;" class="mt-2 urun-a">
                                                        <?= (strlen($text_to_display) > 50) ? substr($text_to_display, 0, 49) . '...' : htmlspecialchars($text_to_display); ?>
                                                    </a>
                                                    <a style="font-size:12px;" class="mt-2 urun-a border-bottom"><?= htmlspecialchars($row['MarkaAdi'] ?? 'Marka Yok'); ?></a>
                                                    <a style="font-size:12px;" class="mb-2 urun-a">Stok Kodu<span class="ps-1">:</span><?= htmlspecialchars($row['UrunKodu']); ?></a>
                                
                                                    <?php if ($row['proje'] == 0 && isset($_SESSION['id'])) {
                                                        $uye = $database->fetch("SELECT * FROM uyeler WHERE id = :id", ['id' => $_SESSION['id']]);
                                                        $uye_fiyat = $uye['fiyat'] ?? 0;
                                
                                                        if ($uye_fiyat != 4) { ?>
                                                            <a style="font-size:14px;" class="urun-a custom-underline">
                                                                <?= !empty($row["DSF4"]) ? $row["DOVIZ_BIRIMI"] : "₺"; ?>
                                                                <?= formatNumber($row["DSF4"] ?? $row["KSF4"]); ?> + KDV
                                                            </a>
                                
                                                            <a style="font-size:14px; color:red;" class="urun-a fw-bold mt-1">Size Özel Fiyat</a>
                                                        <?php } ?>
                                
                                                        <a style="font-size:14px;" class="urun-a fw-bold">
                                                            <?= !empty($row["DSF4"]) ? $row["DOVIZ_BIRIMI"] : "₺"; ?>
                                                            <?= formatNumber($row["DSF$uye_fiyat"] ?? $row["KSF$uye_fiyat"]); ?> + KDV
                                                        </a>
                                                        <?php $urunId = $row['id']; ?>
                                                        <i class="fa-solid fa-cart-shopping fa-xl sepet-style" onclick="sepeteUrunEkle(<?= $urunId; ?>, <?= $_SESSION['id'] ?? 'null'; ?>);"></i>
                                                    <?php } else { ?>
                                                        <button type="submit" class="btn btn-danger mt-3 teklifOnaybtn">
                                                            <i class="fa-solid fa-reply fa-flip-horizontal"></i> Teklif İste
                                                        </button>
                                                    <?php } ?>
                                                </div>
                                
                                                <a href="#" class="rounded-1 text-decoration-none" style="font-size:13px; color:white; background: rgba(255, 0, 0, 0.6); padding:2px; position: absolute; top: 10px; left: 10px;">
                                                    <i class="fa-solid fa-bullhorn pe-1"></i>Yeni!
                                                </a>
                                            </div>
                                        </li>
                                
                                        <?php
                                    }
                                }
                            ?>

                        </ul>
                    </div>
                </div>

            </div>
        </div>
        <!-- URUNLER SLIDER2 SONU -->

        <!--OZEL BANNER ALANI-->
        <div class="container mt-1" id="ozel-banner-alani">
            <!-- Banner içeriği buraya yüklenecek -->
        </div>
        <!--OZEL BANNER ALANI SONU-->
    </div>
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
                                <label for="email" class="form-label">E-posta adresi</label>
                                <input type="email" class="form-control" id="email" placeholder="mail@example.com" required>
                                <div class="invalid-feedback">Geçerli e-posta giriniz!</div>
                            </div>
                            <div class="col-sm-12">
                                <label for="teklif_nedeni" class="form-label">Açıklama</label>
                                <input type="text" id="uye_id" value="<?= $_SESSION["id"]; ?>" hidden>
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
<?php
// Check if the modal has been shown today
if (!isset($_SESSION['modal_shown_date']) || $_SESSION['modal_shown_date'] != date('Y-m-d')) {
// If the modal hasn't been shown today, show it and update the session variable
    $_SESSION['modal_shown_date'] = date('Y-m-d');

    $q = "SELECT * FROM b2b_popup_kampanya WHERE aktif = 1";
    $campaign = $database->fetch($q);
    if ($campaign) {
    ?>
    <div class="modal fade" data-backdrop="static" id="popupCampaign" tabindex="-1" role="dialog" aria-labelledby="popupCampaignLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title">Güncel Kampanya</h5> -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <a href="<?= $campaign["link"] ?>">
                        <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/campaigns/<?= $campaign["foto"] ?>" class="img-fluid" alt="Kampanya" width="100%">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php } } ?>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/splide/splide.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('img[data-banner-id]').on('click', function() {
            var bannerId = $(this).data('banner-id');

            $.ajax({
                type: 'POST',
                url: 'php/update_banner_click_count.php',
                data: { banner_id: bannerId },
                success: function(response) {
                    console.log('Click count updated successfully');
                },
                error: function(xhr, status, error) {
                    console.error('Error updating click count:', error);
                }
            });
        });
    });
    $(document).ready(function() {
        $('video[data-banner-video-id]').on('click', function() {
            var bannerId = $(this).data('banner-video-id');

            $.ajax({
                type: 'POST',
                url: 'php/update_banner_click_count.php',
                data: { banner_video_id: bannerId },
                success: function(response) {
                    console.log('Click count updated successfully');
                },
                error: function(xhr, status, error) {
                    console.error('Error updating click count:', error);
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var playButton = document.querySelector('.play-button');
        var video = document.getElementById('myVideo');

        playButton.addEventListener('click', function() {
            if (video.paused) {
                video.play();
                playButton.style.display = 'none';
            } else {
                video.pause();
                playButton.style.display = 'flex';
            }
        });

        video.addEventListener('play', function() {
            playButton.style.display = 'none';
        });

        video.addEventListener('pause', function() {
            playButton.style.display = 'flex';
        });
    });
</script>

<script>
    $(document).ready(function(){
        // Display the modal when the page is loaded
        $('#popupCampaign').modal('show');
    });
</script>

<script>
    var splide = new Splide( '#first-splide', {
        pagination: boolean = true,
        arrows: boolean = false,
        perPage: 5,
        perMove: 2,
        type: 'loop',
        autoplay: true,
        autoplayInterval: 500,
        breakpoints: {
        1420: {
        perPage: 4,
        gap    : '.7rem',
        },
        1200: {
        perPage: 3,
        gap    : '.7rem',
        },
        1000: {
        perPage: 2,
        gap    : '.7rem',
        },
        760: {
        perPage: 1,
        perMove: 1,
        gap    : '.2rem',
        },
},
} );
splide.mount();

var splide1 = new Splide('#second-splide', {
    pagination: true,
    arrows: false,
    perPage: 5,
    perMove: 2,
    type: 'loop',
    autoplay: true,
    autoplayInterval: 500,
    breakpoints: {
        1420: {
            perPage: 4,
            gap: '.7rem',
        },
        1200: {
            perPage: 3,
            gap: '.7rem',
        },
        1000: {
            perPage: 2,
            gap: '.7rem',
        },
        760: {
            perPage: 1,
            perMove: 1,
            gap: '.1rem',
        },
    },
});

splide1.mount();
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slideTrack = document.getElementById('slideTrack');
    const brands = document.querySelectorAll('.slide-marka');

    // Clone the brands to create a seamless loop
    brands.forEach(brand => {
        let clone = brand.cloneNode(true);
        slideTrack.appendChild(clone);
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
                url: 'php/edit_info.php',
                data: {
                    uye_id: uye_id,
                    email: email,
                    teklif_nedeni: teklif_nedeni,
                    urun_no: urun_no,
                    type: 'teklif'
                },
                success: function() {
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
    document.addEventListener("DOMContentLoaded", function() {
        const zoomElement = document.querySelector(".zoom-effect");

        // Function to start the animation
        function startAnimation() {
            zoomElement.classList.add("zoom-animation");
        }

        // Function to handle animation end
        function onAnimationEnd(event) {
            if (event.animationName === "zoomInOut") {
                animationCount++;
                if (animationCount >= 3) {
                    zoomElement.classList.remove("zoom-animation");
                    zoomElement.removeEventListener("animationend", onAnimationEnd);
                } else {
                    // Reset the animation by removing and re-adding the class
                    zoomElement.classList.remove("zoom-animation");
                    void zoomElement.offsetWidth; // Trigger reflow to restart the animation
                    zoomElement.classList.add("zoom-animation");
                }
            }
        }

        // Add animation end event listener
        zoomElement.addEventListener("animationend", onAnimationEnd);

        let animationCount = 0;
        startAnimation(); // Start the animation initially
    });

</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Asenkron olarak banner içeriğini yükle
        fetch('components/index/ozel-banner.php')
            .then(response => response.text())
            .then(html => {
                document.getElementById('ozel-banner-alani').innerHTML = html;
            })
            .catch(error => console.error('Banner yüklenirken hata oluştu:', error));
    });
</script>