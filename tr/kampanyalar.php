<?php
require '../functions/admin_template.php';
require '../functions/functions.php';
ini_set('display_errors', 1);  // Hataları ekrana göster
error_reporting(E_ALL);   
$currentPage = 'kampanyalar';
$template = new Template('Nokta - Kampanyalar', $currentPage);

$template->head();

$db = new Database();
if (isset($_GET['camp'])) {
    $camp = $_GET['camp'];
    
    // Kampanya bilgilerini al
    $kampanya_sql = "SELECT ad, urun_id FROM kampanyalar WHERE link = :link";
    $kampanya_row = $db->fetch($kampanya_sql, ['link' => $camp]);

    $urun_id_list = $kampanya_row['urun_id'] ?? '';
    $urun_adi_kamp = $kampanya_row['ad'] ?? '';

    // Ürünleri al
    $nokta_urunler_sql = "SELECT u.*, m.title AS marka_adi, r.foto
                          FROM nokta_urunler u
                          LEFT JOIN nokta_urun_markalar m ON u.MarkaID = m.id 
                          LEFT JOIN (
                              SELECT urun_id, MIN(foto) AS foto
                              FROM nokta_urunler_resimler
                              WHERE sira = 1
                              GROUP BY urun_id
                          ) r ON u.BLKODU = r.urun_id
                          WHERE u.aktif = 1
                          AND FIND_IN_SET(u.id, :urun_id_list)";
    
    $nokta_urunler = $db->fetchAll($nokta_urunler_sql, ['urun_id_list' => $urun_id_list]);
} else {
    // Tüm kampanya ürün ID'lerini al
    $kampanyalar_sql = "SELECT urun_id FROM kampanyalar";
    $kampanyalar = $db->fetchAll($kampanyalar_sql);

    $urun_idler = array_column($kampanyalar, 'urun_id');
    $urun_id_list = implode(',', $urun_idler);
    // Kampanya ürünlerini al
    $nokta_urunler_sql = "SELECT u.*, m.title AS marka_adi, r.foto
                      FROM nokta_urunler u
                      LEFT JOIN nokta_urun_markalar m ON u.MarkaID = m.id 
                      LEFT JOIN (
                            SELECT urun_id, MIN(foto) AS foto
                            FROM nokta_urunler_resimler
                            WHERE sira = 1
                            GROUP BY urun_id
                        ) r ON u.BLKODU = r.urun_id
                      WHERE u.web_comtr = 1
                      AND FIND_IN_SET(u.id, :urun_id_list)";
    // 'Kampanyalı Ürünler' olanları al
    $ozel_kodu1_sql = "SELECT DISTINCT u.*, m.title AS marka_adi, r.foto
                   FROM nokta_urunler u
                   LEFT JOIN nokta_urun_markalar m ON u.MarkaID = m.id 
                   LEFT JOIN (
                       SELECT urun_id, MIN(foto) AS foto
                       FROM nokta_urunler_resimler
                       WHERE sira = 1
                       GROUP BY urun_id
                   ) r ON u.BLKODU = r.urun_id
                   WHERE u.web_comtr = 1
                   AND u.OZEL_KODU1 = 'Kampanyalı Ürünler'";

    $nokta_urunler_sql = "$nokta_urunler_sql UNION $ozel_kodu1_sql";
    $nokta_urunler = $db->fetchAll($nokta_urunler_sql, ['urun_id_list' => $urun_id_list]);
}
?>
<style>
    .hızlı-teslimat{font-size:13px;color:white;background: rgba(51, 170, 51, .6);padding:2px;position: absolute;top: 10px;left: 10px;}
    .favori-style{position: absolute;top: 28px;right: 10px;}
    .sepet-style{cursor:pointer;position: absolute;bottom: 30px;right: 20px;}
    .urun-a{text-decoration: none;color:black;font-size:14px;}
    .favori-icon:hover{cursor: pointer;}
    .kategori-effect li{transition: transform 0.3s ease;}
    .kategori-effect li:hover{transform: translateX(8px);color:purple;}
    ::-webkit-scrollbar {width: 7px;}
    ::-webkit-scrollbar-track {background: #f1f1f1;}/* Track */
    ::-webkit-scrollbar-thumb {background: #888;}/* Handle */
    ::-webkit-scrollbar-thumb:hover {background: #555;}/* Handle on hover */
    .bi {vertical-align: -.125em;fill: currentColor;}
    .form-check{margin: 5px;}
    .urunler:hover{box-shadow: 0px 0px 10px #888888;}
    .custom-underline {text-decoration: line-through;}
    @media (min-width: 992px) {
        .mobile-menu{display: none;}
        .urunler-desktop{float:right; width: 75%;}
        .deskop-menu{float:left; width: 25%;}
        .urun-card{width: 32%;}
    }
    @media (max-width: 992px) {
        .mobile-menu{display: block;}
        .deskop-menu{display: none; }
        .urunler-desktop{width: 100%;}
        .urun-card{width: 47%;}
    }
    @media (min-width: 992px) and (max-width:1200px) {.urun-card{width: 32%;}}
    @media (min-width:1200px) {.urun-card{width: 24%;}}
</style>
<body>
<?php $template->header(); ?>
<nav aria-label="breadcrumb" class="container mt-3 mb-2">
    <svg xmlns="http://www.w3.org/2800/svg" style="display: none;"><symbol id="house-door-fill" viewBox="0 0 16 16">
        <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/></symbol>
    </svg>
    <ol class="breadcrumb ">
        <li class="breadcrumb-item">
            <a class="link-body-emphasis" href="index">
                <svg class="bi" width="15" height="15"><use xlink:href="#house-door-fill"></use></svg><span class="visually-hidden">Anasayfa</span>
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="tr/kampanyalar" class="link-body-emphasis text-decoration-none" >Kampanyalar</a>
        </li>
        <?php if(isset($urun_adi_kamp)){ ?>
            <li class="breadcrumb-item">
                <a class="link-body-emphasis text-decoration-none" ><?= $urun_adi_kamp ?></a>
            </li>
        <?php } ?>
    </ol>
</nav>
<section class="container">
    <div class="row">
        <div class="mb-5 deskop-menu" style="height:100%">
            <!-- Kategoriler -->
            <div class="border shadow-sm p-3" style="background-color: #ffffff;">
                <h5 class="border-bottom p-2">Ürünler</h5>
                <ul class="list-unstyled ps-0 kategori-effect">
                    <?php
                        $kategori_sql = $database->fetchAll("SELECT * FROM nokta_kategoriler WHERE web_comtr = 1 AND parent_id = 0 ORDER BY sira");
                        foreach ($kategori_sql as $kategori_row) {
                            $kategori_id = $kategori_row['id'];
                            $kategori_adi = $kategori_row['kategori_adi'];
                            $kategori_seo_link = $kategori_row['seo_link']; ?>
                        <li class="">
                            <a href="tr/urunler?cat=<?= $kategori_seo_link; ?>&brand=&filter=&search=" style="text-align: left !important;" class="btn d-inline-flex align-items-center rounded border-0 collapsed">
                                <?= $kategori_adi; ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="urunler-desktop">
            <div class="row">
                <?php 
                    if($nokta_urunler){
                        foreach ($nokta_urunler as $row) { ?>
                        <div class="card urun-card rounded-0 shadow-sm p-0 mx-1 mt-1 mb-1">
                            <a href="tr/urunler/<?= $row['seo_link']; ?>">
                                <div class="w-100 d-flex align-items-center" style="height: 245px;overflow: hidden">
                                    <img src="<?php echo !empty($row['foto']) ? 'assets/images/urunler/'.$row['foto'] : 'assets/images/urunler/gorsel_hazirlaniyor.jpg'; ?>" class="card-img-top img-fluid">
                                </div>
                            </a>
                            <div class="card-body d-flex flex-column"><!--
                            <div class="mb-2 mt-auto" style="font-size: 12px;"><a href="" class="rounded-1 text-decoration-none" style="color:black; background:rgba(255, 40, 18, 0.4); padding:2px"><i class="fa-solid fa-tags"></i>Birlikte Al Kazan</a>
                            <a href="" class="rounded-1 text-decoration-none" style="color:black; background: rgba(0, 98, 255, 0.4); padding:2px"><i class="fa-solid fa-circle-play"></i>Videolu Ürün</a></div>-->
                                <a href="tr/urunler/<?= $row['seo_link']; ?>" style="font-weight:600; color:#555555;" class="mt-2 urun-a"><strong><?php echo (strlen($row['UrunAdiTR']) > 65) ? substr($row['UrunAdiTR'], 0, 64) . '...' : $row['UrunAdiTR'];?></strong></a>
                                <a style="font-size:12px; color:#0a90eb;" class="mt-2 border-bottom urun-a"><?= $row['marka_adi'] ; ?></a>
                                <a style="font-size:12px;" class=" urun-a">Stok Kodu:<span style="font-weight: bold"> <?= $row['UrunKodu'] ; ?></span></a>
                                <?php if($row['proje'] == 0){ ?>
                                    <?php if (isset($_SESSION['id'])) {
                                    $q = $db->prepare("SELECT * FROM uyeler WHERE id =:id");
                                    $q->execute(array('id' => $_SESSION['id']));
                                    $uye = $q->fetch(PDO::FETCH_ASSOC);
                                    $uye_fiyat = $uye['fiyat'];
                                if(!empty($row["stok"])){
                                if($uye_fiyat != 4){
                                    ?>
                                    <a style="font-size:14px; color:#555555;" class="urun-a fw-bold">
                                        <?php echo !empty($row["DSF4"]) ? $row["DOVIZ_BIRIMI"] : "₺";
                                        $fiyat1 = !empty($row["DSF4"]) ? $row["DSF4"]: $row["KSF4"];
                                        echo formatNumber($fiyat1);?> + KDV
                                    </a>
                                    <a style="font-size:14px; color:#0a90eb;" class="urun-a fw-bold mt-1">Size Özel Fiyat</a>
                                <?php } ?>
                                    <a style="font-size:14px;color:#f29720;" class="urun-a fw-bold">
                                        <?php echo !empty($row["DSF4"]) ? $row["DOVIZ_BIRIMI"] : "₺";
                                        $fiyat = !empty($row["DSF".$uye_fiyat]) ? $row["DSF".$uye_fiyat] : $row["KSF".$uye_fiyat];
                                        echo formatNumber($fiyat); ?> + KDV
                                    </a>
                                    <?php if(!empty($fiyat) && $row["stok"] > 0){ ?>
                                    <i class="fa-solid fa-cart-shopping fa-xl sepet-style"
                                       onclick="<?php
                                       $urunId = $row['id'];
                                       if (isset($_SESSION['id'])) {
                                           echo "sepeteUrunEkle($urunId, " . (isset($_SESSION['id']) ? $_SESSION['id'] : 'default_value') . ");";
                                       } else {
                                           echo "window.location.href = 'tr/giris';";
                                       }
                                       ?>">
                                    </i>

                                <?php }
                                } else{ $q = $db->prepare("SELECT * FROM uyeler WHERE id = :id ");
                                $q->execute(array('id' => $_SESSION['id']));
                                $uye = $q->fetch(PDO::FETCH_ASSOC);
                                $uye_satis_temsilci = $uye['satis_temsilcisi'];
                                $q = $db->prepare("SELECT * FROM kullanicilar WHERE id = :id ");
                                $q->execute(array('id' => $uye_satis_temsilci));
                                $temsilci = $q->fetch(PDO::FETCH_ASSOC);
                                if (!empty($uye_satis_temsilci)) { ?>
                                    <button class="btn me-1 mt-3" style="background-color: #FC9803; color:white;" onclick="openTemsilciAlert()">
                                        <i class="fa-solid fa-box-open me-1"></i><span style="font-size: 14px;">Stok Sorunuz</span>
                                    </button>
                                    <script>
                                        function openTemsilciAlert() {
                                            Swal.fire({
                                                title: 'Satış Temsilciniz',
                                                html: '<div style="text-align: left;">' +
                                                    '<p>Ad Soyad: <?php echo $temsilci['kullanici_ad']; ?>  <?php echo $temsilci['kullanici_soyad']; ?></p>' +
                                                    '<p>Mail:  <a href="mailto: <?php echo $temsilci['kullanici_mail']; ?>"><?php echo $temsilci['kullanici_mail']; ?></a></p>' +
                                                    '<p>Telefon Numarası: <?php echo $temsilci['kullanici_tel']; ?></p>' +
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
                                <?php } else { ?>
                                    <button class="btn me-1 mt-2" style="background-color: #FC9803; color:white;" onclick="openTemsilciAlert()">
                                        <i class="fa-solid fa-universal-access me-1"></i><span style="font-size: 14px;">Satış Temsilcinize Danışınız</span>
                                    </button>
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
                                <?php } ?>
                                <?php
                                }
                                }
                                }else{ ?>
                                    <button type="submit" class="btn btn-danger mt-3 teklifOnaybtn"><i class="fa-solid fa-reply fa-flip-horizontal"></i> Teklif İste</button>
                                <?php } ?>
                            </div>
                            <i class="fa-regular fa-heart fa-xl favori-icon favori-buton favori-style" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Favorilere Ekle!" data-product-id="<?= $urunId ?>"></i>
                        </div>
                    <?php } } else { ?>
                    <div class="alert alert-danger text-center px-5" role="alert">Ürün Bulunamadı!</div>
                <?php } ?>
            </div>
        </div>
        <!-- Ürün listeleme Bölümü Sonu -->
    </div>
</section>
<div style="clear:both"></div>
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
<?php $template->header(); ?>
</body>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/app.js"></script>
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
    $(document).ready(function () {
        $('.favori-buton').on('click', function (event) {
            event.preventDefault();
            var iconElement = $(this).find('.favori-icon'); // Assuming the icon is inside the favori-buton
            var uye_id = <?= $_SESSION["id"] ?>;
            var productId = $(this).data('product-id');
            var type = 'favori';
            // Store the reference to $(this) in a variable for later use
            var that = $(this);
            // Send an AJAX request to the server to add the product to the favorites
            $.ajax({
                type: 'POST',
                url: 'php/edit_info.php',
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
                        iconElement.removeClass("fa-regular").addClass("fa-solid");
                        that.css("color", "red");
                        window.location.reload();
                    } else if (response.includes('removed')) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Ürün Favorilerden Kaldırıldı!',
                            toast: true,
                            position: 'top-start',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        iconElement.removeClass("fa-solid").addClass("fa-regular");
                        window.location.reload();
                    }
                }
            });
        });
    });
    $(document).ready(function () {
        // Fetch the list of favorite products using AJAX
        $.ajax({
            type: 'GET',
            url: 'php/get_favorites.php',
            dataType: 'json',
            success: function (favoriteProducts) {
                // Iterate through the favorite products and update their classes
                favoriteProducts.forEach(function (productId) {
                    var selector = '.favori-icon[data-product-id="' + productId + '"]';
                    $(selector).removeClass("fa-regular").addClass("fa-solid");
                });
                // Add hover effect to both products in favorites and not in favorites
                $(".favori-icon.fa-regular").hover(
                    function () {
                        // Hover in
                        $(this).removeClass("fa-regular").addClass("fa-solid");
                        $(this).css("color", "red");
                    },
                    function () {
                        // Hover out
                        $(this).removeClass("fa-solid").addClass("fa-regular");

                        $(this).css("color", "");
                    }
                );
            }
        });
    });
</script>
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>