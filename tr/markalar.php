<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'markalar';
$template = new Template('Nokta - Markalar', $currentPage);

$template->head();
$database = new Database();
?>
<style>
    .marka-effect {transition: transform 0.3s ease; /* Add a transition for the transform property */}
    .marka-effect:hover {transform: translateY(-8px); /* Move the button 8 pixels up */}
    .bi {vertical-align: -.125em;fill: currentColor;}
    .adres:hover{color: #4c0066;}
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
                <li class="breadcrumb-item active" aria-current="page">Markalar</li>
            </ol>
        </nav>
        <section class="container mb-5">
            <div class="row">
                <?php
                    $d = $database->fetchAll("SELECT * FROM nokta_urun_markalar WHERE web_comtr = '1' ORDER BY order_by ASC ");
                    foreach( $d as $k => $row ) {
                ?>
                    <div class=" mt-4 col-sm-4 col-md-3 col-lg-2 col-6">
                        <a href="tr/urunler?cat=&brand=<?php echo $row['seo_link'] ?>&filter=&search=">
                            <div class="card h-100 marka-effect" style="background-color: #f6f6f6;">
                                <img class="card-img-top rounded" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/brands/<?= $row['hover_img'];?>"/>
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