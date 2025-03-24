<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'bankabilgileri';
$template = new Template('Nokta B2B - Banka Bilgileri', $currentPage);

$template->head();
$database = new Database();
$bankalar = $database->fetchAll("SELECT hesap_adi, banka_adi, hesap, sube_adi, iban, kolay_adres, swift FROM nokta_banka_bilgileri WHERE aktif = 1");
?>
<style>
      .bi {vertical-align: -.125em;fill: currentColor;}
</style>
<body>
    <?php $template->header(); ?>
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
            <li class="breadcrumb-item active" aria-current="page">Hakkımızda</li>
        </ol>
    </nav>
    <section class="container mb-3">
        <div class="row">
            <?php $template->pageLeftMenu(); ?>
            <div class="float-end col-xs-12 col-sm-8 col-md-9 rounded-3">
                <div class="card">
                    <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/site/hakkimizda.png" alt="hakkimizda">
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Hesap Adı</th>
                                    <th>Banka Adı</th>
                                    <th>Hesap No</th>
                                    <th>Şube Adı</th>
                                    <th>IBAN</th>
                                    <th>Kolay Adres</th>
                                    <th>SWIFT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bankalar as $banka) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($banka['hesap_adi']) ?></td>
                                        <td><?= htmlspecialchars($banka['banka_adi']) ?></td>
                                        <td><?= htmlspecialchars($banka['hesap']) ?></td>
                                        <td><?= htmlspecialchars($banka['sube_adi']) ?></td>
                                        <td><?= htmlspecialchars($banka['iban']) ?></td>
                                        <td><?= htmlspecialchars($banka['kolay_adres']) ?></td>
                                        <td><?= htmlspecialchars($banka['swift']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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