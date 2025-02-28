<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'haber-detay';
$template = new Template('Nokta - Haber Detay', $currentPage);

$template->head();
$database = new Database();

$blog = $database->fetch("SELECT * FROM nokta_blog WHERE `id` = :id ", ['id' => $_GET['id']]);
$blogId = $blog['id'];
$tiklanma = $blog['tiklanma'];
if ($tiklanma === null || $tiklanma === '') {
    $tiklanma = 0;
}
$tiklanma++;
// cok_goren değerini güncelle
$updateStatement = $database->update("UPDATE nokta_blog SET tiklanma = :tiklanma WHERE id = :id", ['tiklanma' => $tiklanma, 'id' => $blogId]);
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
    <div class="row g-5">
        <div class="col-md-8">
            <article class="blog-post">
                <h2 class="display-5 link-body-emphasis mb-1"><?= $blog['blog_baslik'] ?></h2>
                <p class="blog-post-meta"><?= $blog['tarih'] ?></p>
                <hr>
                <p><?= $blog['blog_yazi'] ?></p>
            </article>
        </div>
        <div class="col-md-4">
            <div class="position-sticky" style="top: 2rem;">
                <div>
                    <h4 class="fst-italic">Son Gönderiler</h4>
                    <ul class="list-unstyled">
                        <?php
                            $d = $database->fetch("SELECT * FROM nokta_blog WHERE aktif = 1 ORDER BY tarih DESC LIMIT 3");
                            foreach( $d as $k => $row ) {
                        ?>
                            <li>
                                <a class="d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center py-3 link-body-emphasis text-decoration-none border-top" href="bilgibankasi-detay?lang=<?= $user_language; ?>&id=<?= $row["id"]; ?>">
                                    <img src="assets/images/<?= $row["blog_foto"]; ?>" width="200" height="150"  alt="">
                                    <div class="col-lg-8">
                                        <h6 class="mb-0"><?= $row["blog_baslik"]; ?></h6>
                                        <small class="text-body-secondary"><?= $row["tarih"]; ?></small>
                                    </div>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
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