<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'dekontlar';
$template = new Template('Nokta B2B - Dekontlar', $currentPage);

$template->head();
$database = new Database();
sessionControl();

$uye_id = $_SESSION["id"];
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
        <li class="breadcrumb-item active" aria-current="page">Dekontlarım</li>
    </ol>
</nav>
<div class="container">
    <div class="row">
        <?php $template->leftMenuProfile(); ?>
        <div class="float-end col-xs-12 col-sm-12 col-md-9">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 ">
                    <article class="blog-post border rounded-3 px-3 " style="background-color: #EFF4F7;">
                        <h5 class="link-body-emphasis my-4">Dekontlarım</h5>
                        <div class="table p-2 table-responsive">
                            <table class="rounded-3" style="width: 100%; border-color:#e1e1e1 ">
                                <thead class="rounded-3" >
                                <td class="p-2 text-center border">İşlem No</td>
                                <td class="p-2 text-center border">Tutar</td>
                                <td class="p-2 text-center border">Dekont</td>
                                <td class="p-2 text-center border">Tarih</td>
                                </thead>
                                <tbody>
                                <?php
                                $d = $database->fetchAll("SELECT * FROM b2b_dekontlar WHERE uye_id = :uye_id ORDER BY tarih DESC" , ['uye_id' => $uye_id]);
                                    foreach ($d as $k => $row) {
                                        ?>
                                        <tr>
                                            <td class="p-2 text-center border"><?= $row['islem_no'] ?></td>
                                            <td class="p-2 text-center border"><?= $row['tutar'] ?></td>
                                            <td class="p-2 text-center border">
                                                <a href="assets/uploads/dekontlar/<?= $row["dekont"]; ?>"><i class="fa-regular fa-file-pdf"></i></a>
                                            </td>
                                            <td class="p-2 text-center border"><?= $row['tarih'] ?></td>
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