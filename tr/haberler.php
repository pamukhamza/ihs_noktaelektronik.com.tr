<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'haberler';
$template = new Template('Nokta - Haberler', $currentPage);

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
        <li class="breadcrumb-item active" aria-current="page">Haberler</li>
    </ol>
</nav>
<section class="container mb-5">
    <div class="row">
        <!-- Sol Menü -->
        <?php $template->pageLeftMenu(); ?>
        <div class="float-end col-xs-12 col-sm-8 col-md-9 rounded-3 ">
            <?php
                $d = $database->fetchAll("SELECT * FROM nokta_blog WHERE aktif = '1' ");
                foreach( $d as $k => $row ) {
            ?>
                <div class="col-12">
                    <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                        <div class="col p-4 d-flex flex-column position-static">
                            <h3 class="mb-0"><?= $row["blog_baslik"]; ?></h3>
                            <div class="mb-1 text-body-secondary"><?= $row["tarih"]; ?></div>
                            <p class="card-text mb-auto"></p>
                            <a href="haber-detay?id=<?= $row["id"]; ?>" class="icon-link gap-1 icon-link-hover stretched-link fst-italic">Devamını Oku...<svg class="bi"><use xlink:href="#chevron-right"/></svg></a>
                        </div>
                        <div class="col-auto d-none d-lg-block">
                            <img src="assets/images/<?= $row["blog_foto"]; ?>" width="350" height="250" alt="">
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
<div style="clear:both"></div>
<?php $template->footer(); ?>
</body>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-1.7.2.min.js"></script>
<script src="assets/js/alert.js"></script>