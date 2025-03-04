<?php
    $database = new Database();
    if($_SESSION['id']){
        // Kullanıcı kimliği, sayfa adı ve IP adresini al
        $userId = $_SESSION['id']; // Örneğin, oturum kimliğinden kullanıcı kimliğini alabilirsiniz
        $pageName = $_SERVER['REQUEST_URI']; // Kullanıcının bulunduğu sayfa adını alabilirsiniz
        $ipAddress = $_SERVER['REMOTE_ADDR']; // Kullanıcının IP adresini alabilirsiniz

        // Sayfa bilgisini ve IP adresini güncelle
        updateUserPage($userId, $pageName, $ipAddress);
    }
?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap');
        body {
            font-family: 'Roboto Condensed', sans-serif;
            font-style: normal;
            font-weight: normal;
            background-color: #fafafa;
        }
        button:focus:not(:focus-visible) {color: #fc9803;}
        .btn-icon-kalp:hover{color:red;}
        .bg-turuncu{background-color: #fc9803;}
        .hidden{display: none}
        .a-hvr{color:white;}
        .a-hvr:hover{color: #fc9803;}
        .a-hvr1:hover{color: white;}
        .sticky-padding {border-top-right-radius: 0;transition: padding 0.6s ease;}
        .currency-switcher{z-index: 2000;}
        #stokArama:focus{box-shadow: 0 0 0 0 rgba(13,110,253,.25);}
        @media(min-width: 992px){
            .mobil-sepet-favori{display: none;}
            .mobil-menu {display: none !important;}
        }
        @media(max-width: 992px){
            .desktop-menu{display: none;}
            .alt-menu{display: none}
            .mobil-sepet-favori{display: block;}
            .mobil-sepet-favori a{color:black;}
            .menu-border{border-bottom: 1px solid lightslategray;}
        }
        @media(max-width: 1200px){
            .currency-switcher{z-index: 1;}
        }
        .odeme_btn_style{color: white;background-color: #f29720;}
        .odeme_btn_style:hover{background-color: white;color: #f29720;border: solid 1px;}
    </style>
    <div style="background-color: white">
        <div class="container pt-2" style="background-color: white">
            <div class="row">
                <div class="col-sm-6 justify-content-start">
                    <div class="header-slogan">
                        <h1 style="color: #56778f; font-size: 14px;"></h1>
                    </div>
                </div>
                <?php
                    $dolar = $database->fetch("SELECT * FROM b2b_kurlar WHERE id = '2' ");
                    $satis_dolar_kuru = number_format($dolar['satis'], 2, ',', '.');

                    $euro = $database->fetch("SELECT * FROM b2b_kurlar WHERE id = '3' ");
                    $satis_euro_kuru = number_format($euro['satis'], 2, ',', '.');
                ?>
                <div class="col-sm-6 d-flex justify-content-end align-items-center ">
                    <div class="header-login">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm" style="font-weight: 500; color: #56778f;" data-bs-toggle="dropdown" aria-expanded="false">
                                Dolar ($): <?= $satis_dolar_kuru ?>₺
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm" style="font-weight: 500; color: #56778f;" data-bs-toggle="dropdown" aria-expanded="false">
                                Euro (€): <?= $satis_euro_kuru ?>₺
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sticky-top">
        <header class="p-1 bg-white menu-border">
            <nav class="navbar navbar-expand-lg ">
                <div class="container">
                    <a href="https://www.noktaelektronik.com.tr/tr" class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto link-body-emphasis text-decoration-none">
                        <span class="fs-4"><img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/site/logo_new.png" alt=""></span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="justify-content-start col-10 position-relative">
                            <form class="dropdown" onsubmit="performSearch(); return false;">
                                <div class="input-group ms-2 ps-2 rounded-5">
                                    <input type="text" class="form-control rounded-start-pill ps-4" id="stokArama" placeholder="Ara" style="background-color: white; border-color: #fc9803; outline: none;" onkeyup="liveSearch(this.value)">
                                    <button class="btn btn-outline-secondary bg-turuncu rounded-end-circle" type="submit" style="color:white; border-color: transparent">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="dropdown-menu ms-2 ps-2 pe-2 rounded-5" id="liveSearchResults" style="width: 100%; border-color: #fc9803;"></div>
                            </form>
                        </div>

                        <div class="desktop-menu ms-4">
                            <?php
                            if (isset($_SESSION['ad'])) { ?>
                                <div class="ps-1 btn-group justify-content-end">
                                    <button type="button" class="btn user-btn dropdown-toggle" style="color: #56778f;" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-user me-1"></i>
                                        <?php $firma = $_SESSION["firma"];
                                        if ($_SESSION['firma'] && strlen($firma) > 16){$firma = substr($firma, 0, 16) . '...';}
                                        $ad_soyad = $_SESSION["ad"] . ' ' . $_SESSION["soyad"];
                                        if($_SESSION['firma']){ echo $firma;} elseif(!$_SESSION['firma']){ echo $ad_soyad;}
                                        ?>
                                    </button>
                                    <button type="button" class="btn user-btn-mobile dropdown-toggle" style="color: #56778f; display: none" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-user me-1"></i>
                                    </button>
                                    <ul class="dropdown-menu ms-2 text-end dropdown-menu-end" style="font-size: 14px;">
                                        <li><a class="dropdown-item border-bottom" href="tr/siparisler">Siparişlerim</a></li>
                                        <li><a class="dropdown-item border-bottom" href="tr/bilgiler">Üyelik Bilgilerim</a></li>
                                        <li><a class="dropdown-item border-bottom" href="tr/cariodeme">Cari İşlemler</a></li>
                                        <li><a class="dropdown-item border-bottom" href="tr/cari-islem-gecmisi">Cari İşlem Listesi</a></li>
                                        <li><a class="dropdown-item border-bottom" href="tr/tdp">Teknik Destek Programı</a></li>
                                        <li><a class="dropdown-item" href="tr/cikis">Çıkış Yap</a></li>
                                        <?php

                                        $uye = $database->fetch("SELECT satis_temsilcisi FROM uyeler WHERE id = :id ", array('id' => $_SESSION['id']));
                                        

                                        if(!empty($uye['satis_temsilcisi'])){
                                            $temsilci = $database->fetch("SELECT * FROM users WHERE id = :id ", ['id' => $uye['satis_temsilcisi']]);
                                            ?>
                                            <li class="text-white dropdown-item fw-bold mt-1" style="background-color: #a2a2a2;font-size:16px;">Satış Temsilciniz</li>
                                            <li class="dropdown-item border-bottom"> <?= $temsilci['full_name']; ?> </li>
                                            <li class="dropdown-item border-bottom" ><?= $temsilci['email'] ?></li>
                                            <li class="dropdown-item" ><?= $temsilci['tel'] ?></li> <?php } ?>
                                    </ul>
                                </div>
                            <?php } else {
                                ?><a href="tr/giris" class="btn  ms-2" style="color: #56778f;"><i class="fa-solid fa-user me-1"></i>Giriş Yap</a>
                            <?php } ?>
                        </div>
                        <div class="mobil-menu" id="navbarNavAltMarkup" >
                            <ul class="navbar-nav ms-3">
                                <li class="nav-item">
                                    <a class="nav-link" href="tr/urunler?cat=&brand=&filter=&search=">Ürünler</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="tr/markalar">Markalar</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="tr/cariodeme">Cari İşlemler</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="tr/hakkimizda">Hakkımızda</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="tr/iletisim">İletişim</a>
                                </li>
                            </ul>
                        </div>
                        <div class="mobil-sepet-favori">
                            <?php
                            if (isset($_SESSION['ad'])) {
                                $favorilerLink = "tr/favoriler";
                                $sepetLink = "tr/sepet";
                            } else {
                                $favorilerLink = "tr/giris";
                                $sepetLink = "tr/giris";
                            }?>
                            <nav class="nav mt-3">
                                <a class="justify-content-end nav-item border-none nav-link a-hvr" href="<?php echo $favorilerLink; ?>"><i class="fa-solid fa-heart me-1"></i>Favoriler</a>
                                <a href="tr/sepet" class="nav-item nav-link a-hvr me-2" style="position: relative;">
                                    <i class="fa-solid fa-cart-shopping me-1"></i>Sepetim
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-turuncu" id="sepetCountM">
                                    <?php
                                        if (isset($_SESSION['id'])) {
                                            $uye_id = $_SESSION['id'];
                                            $rowCountResult = $database->fetch("SELECT COUNT(*) AS row_count FROM uye_sepet WHERE uye_id = :uye_id" ,array('uye_id' => $uye_id));
                                            $row_count = $rowCountResult['row_count'];
                                            echo $row_count;
                                        } else {
                                            echo "0";
                                        }
                                    ?>
                                </span>
                                </a>
                                <?php
                                if (isset($_SESSION['ad'])) { ?>
                                    <div class="ps-1 btn-group justify-content-end">
                                        <button type="button" class="btn user-btn dropdown-toggle" style="color: #56778f;" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-user me-1"></i>
                                            <?php $firma = $_SESSION["firma"];
                                            if ($_SESSION['firma'] && strlen($firma) > 15){$firma = substr($firma, 0, 15) . '...';}
                                            $ad_soyad = $_SESSION["ad"] . ' ' . $_SESSION["soyad"];
                                            if($_SESSION['firma']){ echo $firma;} elseif(!$_SESSION['firma']){ echo $ad_soyad;}
                                            ?>
                                        </button>
                                        <button type="button" class="btn user-btn-mobile dropdown-toggle" style="color: #56778f; display: none" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-user me-1"></i>
                                        </button>
                                        <ul class="dropdown-menu ms-2 text-end dropdown-menu-end" style="font-size: 14px;">
                                            <li><a class="dropdown-item border-bottom" href="tr/siparisler">Siparişlerim</a></li>
                                            <li><a class="dropdown-item border-bottom" href="tr/kuponlarim">Kuponlarım</a></li>
                                            <li><a class="dropdown-item border-bottom" href="tr/bilgiler">Üyelik Bilgilerim</a></li>
                                            <li><a class="dropdown-item border-bottom" href="tr/cariodeme">Cari İşlemler</a></li>
                                            <li><a class="dropdown-item border-bottom" href="tr/cari-islem-gecmisi">Cari İşlem Listesi</a></li>
                                            <li><a class="dropdown-item border-bottom" href="tr/favoriler">Favoriler</a></li>
                                            <li><a class="dropdown-item border-bottom" href="tr/iadeler">İadeler</a></li>
                                            <li><a class="dropdown-item" href="tr/cikis">Çıkış Yap</a></li>
                                            <?php
                                                if(!empty($uye['satis_temsilcisi'])){
                                            ?>
                                                <li class="text-white dropdown-item fw-bold mt-1" style="background-color: #a2a2a2;font-size:16px;">Satış Temsilciniz</li>
                                                <li class="dropdown-item border-bottom"> <?= $temsilci['kullanici_ad'].' '.$temsilci['kullanici_soyad']; ?> </li>
                                                <li class="dropdown-item border-bottom" ><?= $temsilci['kullanici_mail'] ?></li>
                                                <li class="dropdown-item" ><?= $temsilci['kullanici_tel'] ?></li> 
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } else {
                                    ?><a href="tr/giris" class="btn ms-2" style="color: #56778f;"><i class="fa-solid fa-user me-1"></i>Giriş Yap</a>
                                <?php } ?>
                            </nav>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        <div style="background-color: #430666;">
            <div class="container alt-menu">
                <div class="nav-scroller p-3 " >
                    <div class="d-flex justify-content-between">
                        <nav class="nav justify-content-start">
                            <?php
                            if ($_SERVER['PHP_SELF'] == '/index.php') { ?>
                                <a class="nav-item nav-link a-hvr" href="tr/urunler?cat=&brand=&filter=&search=">Ürünler</a>
                            <?php }else{ ?>
                                <a class="nav-item nav-link dropdown-toggle a-hvr" data-bs-toggle="dropdown" aria-expanded="false" >Ürünler</a>
                            <?php } ?>
                            <?php
                            // Check if the current page is not index.php
                            if ($_SERVER['PHP_SELF'] !== '/index.php') {
                                ?>
                                <ul class="dropdown-menu">
                                    <?php 
                                    $d = $database->fetchAll("SELECT * FROM nokta_kategoriler WHERE parent_id = 0 AND web_net = 1 ORDER BY sira ASC");
                                    foreach ($d as $k => $row) { ?>
                                        <li><a class="dropdown-item" href="tr/urunler?cat=<?= $row['seo_link'];?>&brand=&filter=&search="><?= $row['KategoriAdiTr'] ; ?></a></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            <a class="nav-item nav-link a-hvr" href="tr/markalar">Markalar</a>
                            <a class="nav-item nav-link a-hvr" href="tr/kampanyalar">Kampanyalar</a>
                            <a class="nav-item nav-link a-hvr" href="tr/hakkimizda">Hakkımızda</a>
                            <a class="nav-item nav-link a-hvr" href="tr/iletisim">İletişim</a>
                        </nav>
                        <?php
                        if (isset($_SESSION['ad'])) {
                            $favorilerLink = "tr/favoriler";
                            $sepetLink = "tr/sepet";
                            $cariodemeLink = "tr/cariodeme";
                        } else {
                            $favorilerLink = "tr/giris";
                            $sepetLink = "tr/giris";
                            $cariodemeLink = "tr/giris";
                        }?>
                        <nav class="nav justify-content-end">
                            <a class="btn odeme_btn_style zoom-effect" href="<?= $cariodemeLink; ?>" ><i class="fa-regular fa-credit-card me-2"></i>Online Ödeme</a>

                            <a class="nav-item nav-link a-hvr" href="<?= $favorilerLink; ?>"><i class="fa-solid fa-heart me-1"></i>Favoriler</a>
                            <button type="button" id="cartDropdown" class="nav-item nav-link a-hvr me-2 dropdown-toggle" style="position: relative" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-cart-shopping me-1"></i>Sepetim
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-turuncu" id="sepetCount">
                                    <?php
                                   if (isset($_SESSION['id'])) {
                                    $uye_id = $_SESSION['id'];
                                    $rowCountResult = $database->fetch("SELECT COUNT(*) AS row_count FROM uye_sepet WHERE uye_id = :uye_id", array('uye_id' => $uye_id));
                                    $row_count = $rowCountResult['row_count'] ?? 0; // Eğer sonuç null dönerse 0 ata
                                    echo $row_count;
                                } else {
                                    echo "0";
                                }
                                    ?>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="card border-0">
                                    <h5 class="ps-2">Sepetim</h5>
                                    <div id="cart"></div>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery-3.7.0.min.js"></script>
    <script>
function liveSearch(searchQuery) {
    if (searchQuery.length >= 3) {
        $.ajax({
            url: 'functions/urunler/searchlive.php',
            method: 'POST',
            data: { searchQuery: searchQuery },
            success: function(response) {
                var results = response;
                var dropdownMenu = $('#liveSearchResults');
                dropdownMenu.empty(); // Clear previous results
                if (results.length > 0) {
                    results.forEach(function(result) {
                        dropdownMenu.append(`
                            <a class="dropdown-item rounded-5" href="tr/urunler/${result.seo_link}">
                                <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/${result.KResim}" alt="" style="max-width: 50px; margin-right: 10px;">
                                <span style="font-weight:bold;">${result.UrunKodu}</span> - ${result.UrunAdiTR}
                            </a>
                        `);
                    });
                    dropdownMenu.show(); // Show results
                } else {
                    dropdownMenu.hide(); // Hide if no results
                }
            }
        });
    } else {
        $('#liveSearchResults').empty().hide(); // Clear and hide if search query is too short
    }
}

    </script>
    <script>
        function sepetGuncelle(){
            var session_id = '<?= $_SESSION['id']; ?>';
            var language = 'tr';
            $.ajax({
                type: 'POST',
                url: 'functions/sepet/cart_display_function.php',
                data: {
                    'session_id': session_id,
                    'language': language
                },
                success: function (data) {
                    $('#cart').html(data);
                    $('#cartDropdown').dropdown('show');
                }
            });
        }
        function sepetGuncelle1(){
            var session_id = '<?= $_SESSION['id']; ?>';
            var language = 'tr';
            $.ajax({
                type: 'POST',
                url: 'functions/sepet/cart_display_function.php',
                data: {
                    'session_id': session_id,
                    'language': language
                },
                success: function (data) {
                    $('#cart').html(data);
                }
            });
        }
        sepetGuncelle1();
        window.onscroll = function() { addPaddingOnScroll() };

        function addPaddingOnScroll() {
            var header = document.querySelector('.sticky-top');
            if (window.pageYOffset > 0) {header.classList.add('sticky-padding');}
            else {header.classList.remove('sticky-padding');}
        }
    </script>