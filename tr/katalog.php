<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'katalog';
$template = new Template('Nokta - Online Katalog', $currentPage);

$template->head();
$database = new Database();
?>
<style>
    .bi {vertical-align: -.125em;fill: currentColor;}
    .adres:hover{color: #4c0066;}
    .katalog-card .card {position: relative;overflow: hidden;}
    .katalog-card .card-img {transition: transform 0.3s ease;display: block;width: 100%;height: auto;}
    .katalog-card .download-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: #ffffff;
        opacity: 0;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    .katalog-card .download-icon .fa-download {font-size: 30px;}
    .katalog-card .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Yarı saydam karartma */
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .katalog-card:hover .card-img {transform: scale(1.1);}
    .katalog-card:hover .download-icon {opacity: 1;transform: translate(-50%, -50%) scale(1.2);}
    .katalog-card:hover .overlay {opacity: 1;}
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
                <li class="breadcrumb-item active" aria-current="page">Online Katalog</li>
            </ol>
        </nav>
        <section class="container mb-5">
            <div class="row">
                <?php
                    $result = $database->fetchAll("SELECT * FROM catalogs WHERE web_comtr = '1' ORDER BY sira ASC ");
                    foreach($result as $row){  
                ?>
                        <div class="mt-4 mb-4 col-4 col-md-3 col-lg-2">
                            <a href="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/catalogs/<?= $row['file']; ?>" class="d-grid gap-2 mt-2 text-decoration-none katalog-card">
                                <div class="card position-relative overflow-hidden">
                                    <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/catalogs/<?= $row['img']; ?>" class="card-img">
                                    <div class="overlay"></div> <!-- Karartma katmanı -->
                                    <div class="download-icon">
                                        <div class="text-center" style="font-size: 12px;"><?= $row['title']; ?></div>
                                        <i class="fa fa-download mt-2"></i>
                                    </div>
                                </div>
                             </a>
                        </div>

                <?php } ?>
            </div>
        </section>
      <div style="clear:both"></div>
      <?php $template->footer(); ?>
</body>
</html>

<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-1.7.2.min.js"></script>
<script src="assets/js/alert.js"></script>
