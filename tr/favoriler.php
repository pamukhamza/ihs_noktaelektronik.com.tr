<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'favoriler';
$template = new Template('Nokta - Favoriler', $currentPage);

$template->head();
$database = new Database();
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
            <li class="breadcrumb-item active" aria-current="page">Favoriler</li>
        </ol>
    </nav>
    <section class="container mb-5">
        <div class="row mt-2">
            <div class="col-md-12 col-12 mx-auto">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <article class="blog-post row">
                            <h4 class="link-body-emphasis mb-1">Favoriler</h4><hr>
                            <div class="col-xl-8 mb-3 mb-xl-0">
                                <!-- Shopping bag -->
                                <ul class="list-group mb-3">
                                    <?php
                                        $session_id = $_SESSION['id'];
                                        $urun = $database->fetchAll("
                                            SELECT f.id AS favori_id, f.uye_id, f.urun_id, f.tarih, u.*, mar.title
                                            FROM nokta_uye_favoriler AS f
                                            JOIN nokta_urunler AS u ON f.urun_id = u.id
                                            LEFT JOIN nokta_urun_markalar AS mar ON mar.id = u.MarkaID
                                            WHERE f.uye_id = :session_id
                                        ", ['session_id' => $session_id]);
                                     
                                        if (empty($urun)) {?>
                                            <div class="alert alert-info text-center" role="alert">
                                                Favorileriniz boş!
                                            </div>
                                            <?php
                                        } else {
                                            foreach($urun as $row){
                                                $id = $row["id"];
                                                $BLKODU = $row["BLKODU"];

                                                $result = $database->fetch("SELECT KResim FROM nokta_urunler_resimler WHERE UrunID = :id LIMIT 1", ['id' => $id]);
                                          
                                                // Eğer fotoğraf varsa, onu kullan
                                                if (!empty($result['KResim'])) {
                                                    $imageSrc = 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/' . $result["KResim"];
                                                } else {
                                                    $imageSrc = 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/gorsel_hazirlaniyor.jpg';
                                                }
                                                
                                                if ($result) {
                                                ?>
                                    <li id="favori_<?= $row['favori_id']; ?>" class="list-group-item p-3">
                                        <div class="d-flex gap-3">
                                            <div class="flex-shrink-0 d-flex align-items-center">
                                                <a href="tr/urunler/<?= $row['seo_link'] ; ?>">
                                                    <img src="<?= $imageSrc; ?>" alt="<?= $row["UrunAdiTR"] ?>" width="150px">
                                                </a>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <p class="me-3"><a href="tr/urunler/<?= $row['seo_link'] ; ?>" class="text-body text-decoration-none"><?= $row["UrunAdiTR"] ?></a></p>
                                                        <div class="text-muted d-flex flex-wrap"><span class="me-1">Marka:</span> <a href="javascript:void(0)" class="me-3 text-decoration-none"><?= $row["title"] ?></a> </div>
                                                        <div class="read-only-ratings" data-rateyo-read-only="true"></div>
                                                        <div class=""><span class="text-primary"><span class="me-1 text-muted">Fiyat:</span> <?= isset($row["DSF4"]) ? $row["DOVIZ_BIRIMI"] : "₺";
                                                                $fiyat = isset($row["DSF4"]) ? $row["DSF4"] : $row["KSF4"];
                                                                echo $fiyat; ?> + KDV</div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="text-md-end">
                                                            <button type="button" class="btn-close btn-pinned" onclick="favoriKaldır(<?= $row['favori_id']; ?>); return false;"></button>
                                                        </div>
                                                    </div>
                                                    <div class="text-md-end">
                                                        <button type="button" class="btn btn-primary " onclick="sepeteFavoriEkle(<?= $row['favori_id']; ?>, <?= $row['urun_id']; ?>, <?= $session_id; ?>);"><i class="fa-solid fa-cart-shopping me-1"></i>Sepete Ekle</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <?php }}} ?>
                                </ul>
                            </div>
                            <div class="col-xl-4 mb-3 mb-xl-0">
                                <div class="border rounded p-4 pb-3 mb-3 bg-white">
                                    <h6>Sepetim</h6><hr>
                                    <ul class="list-unstyled">
                                    <?php
                                        $session_id = $_SESSION['id'];



                                        $urun = $database->fetchAll("
                                            SELECT s.id AS sepet_id, s.uye_id, s.adet, s.urun_id, s.tarih, u.*, um.title
                                            FROM uye_sepet AS s
                                            JOIN nokta_urunler AS u ON s.urun_id = u.id
                                            JOIN nokta_urun_markalar AS um ON u.MarkaID = um.id
                                            WHERE s.uye_id = :session_id
                                        " , ['session_id' => $session_id]);
                                     
                                        if (empty($urun)) {?>
                                            <div class="alert alert-info text-center" role="alert">
                                                Sepetiniz boş!
                                            </div><?php
                                        } else {
                                        foreach($urun as $row){
                                            $id = $row["id"];
                                            $BLKODU = $row["BLKODU"];
                                            $result = $database->fetchAll("SELECT DISTINCT KResim FROM nokta_urunler_resimler WHERE UrunID = :urun_id LIMIT 1", ['urun_id' => $id]);
                                        foreach ($result as $results) {
                                    ?>
                                        <li class="d-flex gap-3 align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/<?= $results["KResim"]; ?>" alt="<?= $row["UrunAdiTR"] ?>" width="50px">
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0"><a class="text-body" href="tr/urunler/<?= $row['seo_link'] ; ?>"><?= $row["UrunAdiTR"] ?></a></p>
                                                <p class="fw-medium"><?= $row["adet"] ?>Adet</p>
                                            </div>
                                        </li>
                                    <?php }}} ?>
                                    </ul>
                                    <hr class="mx-n4">
                                    <div class="d-grid">
                                        <a href="tr/sepet" class="btn btn-primary btn-next">Sepete Git</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php $template->footer(); ?>
</body>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/app.js"></script>