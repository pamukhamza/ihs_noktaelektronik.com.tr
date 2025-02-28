<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'kvkk';
$template = new Template('Nokta - Kişisel Verileri Koruma Kanunu', $currentPage);

$template->head();
$database = new Database();
?>
<style>
      .bi {vertical-align: -.125em;fill: currentColor;}
      .scrollable-text {max-height: 500px;overflow-y: auto;  /* Dikey kaydırma çubuğu */}
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
                <li class="breadcrumb-item active" aria-current="page">Kişisel Verileri Koruma Kanunu</li>
            </ol>
        </nav>
    <section class="container mb-5">
        <div class="row">
        <!-- Sol Menü -->
        <?php $template->pageLeftMenu(); ?>
        <div class="float-end col-xs-12 col-sm-8 col-md-9 rounded-3 ">
            <?php $row = $database->fetch("SELECT * FROM documents WHERE `type` = 'kvkk' AND `site` = 'b2b'"); ?>
            <div class="rounded-3 p-2 text-white mb-4" style="background-color: #4c0066; font-weight:600"><?= $row["title"]; ?></div>
            <div class="card">
                <div class="card-body">
                    <div class="scrollable-text">
                        <p><?= $row["text"]; ?></p>
                    </div>
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