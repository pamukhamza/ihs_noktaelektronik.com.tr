<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'iletisim';
$template = new Template('Nokta - İletişim', $currentPage);

$template->head();
$database = new Database();

$categorizedAddresses = [
    'Satış-Mağazaları' => [
        [
            'title' => 'İstanbul Satış Mağazası 1',
            'address' => 'Perpa Ticaret Merkezi B Blok Kat 8 No. 906-907 34384 Şişli / İstanbul',
            'addressMaps' => 'Perpa Ticaret Merkezi, Şişli, İstanbul'
        ],
        [
            'title' => 'İstanbul Satış Mağazası 2',
            'address' => 'Perpa Ticaret Merkezi A Blok Kat 8 No. 841 34384 Şişli / İstanbul',
            'addressMaps' => 'Perpa Ticaret Merkezi, Şişli, İstanbul'
        ],
        [
            'title' => 'Ankara Satış Mağazası 2',
            'address' => 'Timko İş Yerleri Sitesi Timko Sk. E Blok No. 4 06200 Yenimahalle / Ankara',
            'addressMaps' => 'Nokta Elektronik - Ankara'
        ],
    ],
    'ofisler' => [
        [
            'title' => 'Genel Merkez',
            'address' => 'Perpa Ticaret Merkezi A Blok Kat 2 No.1 34384 Şişli / İstanbul',
            'addressMaps' => 'Nokta Elektronik ve Bilişim Sistemleri San. Tic. A.Ş.'
        ],
        [
            'title' => 'Ankara Bölge Ofisi',
            'address' => 'Timko İş Yerleri Sitesi Timko Sk. E Blok No.4 06200 Yenimahalle / Ankara',
            'addressMaps' => 'Nokta Elektronik - Ankara'
        ],
    ],
    'Arge-Üretim-Merkezi' => [
        [
            'title' => 'Arge Üretim Merkezi (İzmir)',
            'address' => 'Tuna Mah. Sanat Cad. No. 17/220 Bornova / İzmir',
            'addressMaps' => 'Nokta Elektronik İzmir Fabrika'
        ],
        [
            'title' => 'Arge Üretim Merkezi (Ankara)',
            'address' => 'Çamlıca Mah. Anadolu Bulvarı 28/10 Gimat / Yenimahalle / Ankara',
            'addressMaps' => 'Çamlıca, Anadolu Blv No:28, 06200 Yenimahalle/Ankara, Türkiye'
        ],
    ],
    'Teknik-Servisler' => [
        [
            'title' => 'Teknik Servis',
            'address' => 'Perpa Ticaret Merkezi B Blok Kat 8 No. 906-907 34384 Şişli / İstanbul',
            'addressMaps' => 'Perpa Ticaret Merkezi, Şişli, İstanbul'
        ],
    ],
];

?>
<style>
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
            <li class="breadcrumb-item active" aria-current="page">İletişim</li>
        </ol>
    </nav>
    <section class="container mb-5">
        <div class="row">
        <!-- Sol Menü -->
        <?php $template->pageLeftMenu(); ?>
        <div class="float-end col-xs-12 col-sm-8 col-md-9">
            <div class="rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative rounded-0">
                <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/site/iletisim.png" alt="" width="100%" height="auto">
            </div>
            <div class="">
                <?php foreach ($categorizedAddresses as $category => $addresses): ?>
                    <h3 class="mb-3"><?= ucfirst(str_replace('-', ' ', $category)); ?></h2>
                    <div class="row">
                        <?php foreach ($addresses as $address): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title "><?= $address['title']; ?></h5>
                                        <a href="https://www.google.com/maps?q=<?= urlencode($address['addressMaps']); ?>" target="_blank" class="">
                                            <p class="card-text"> <i class="fa-solid fa-location-dot pe-1"></i>Adres: <?= $address['address']; ?></p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="row row-cols-1 row-cols-md-2 g-6 mt-5 shadow-sm" style="background-color: #ffffff;">
                <div class="col border ps-5 pe-5 pb-4 pt-4 ">
                    <form action="function.php" method="post">
                        <h4 class="pt-2 pb-2">İletişim Formu</h4>
                        <div class="form-floating mb-3">
                            <input type="text" value="tr" name="lang" hidden>
                            <input type="text" class="form-control" id="adsoyad" name="adsoyad" placeholder="" required>
                            <label for="adsoyad" class="form-label">Ad / Soyad</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="mail" name="mail" placeholder="" required>
                            <label for="mail">E-Posta</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" name="text" placeholder="" id="text" style="height: 100px" required></textarea>
                            <label for="text">Nasıl yardımcı olabiliriz?</label>
                        </div>
                        <button type="submit" name="iletisim_form_btn" class="btn w-100" style="background-color: black; color:white">Gönder</button>
                    </form>
                </div>
                <div class="col border ps-5 pe-5 pb-5 pt-4">
                    <div class="adres p-5">
                        <i class="fas fa-phone fa-2xl pe-4"></i>
                        <a href="telno:02122228780" class="text-dark text-decoration-none fs-5">+90 212 222 87 80</a>
                    </div>
                    <div class="e-posta p-5 pe-2">
                        <i class="fa-regular fa-envelope fa-2xl pe-4"></i>
                        <a href="#" class="text-dark text-decoration-none fs-5">nokta@noktaelektronik.net</a>
                    </div>
                    <div class="telefon ps-5 pt-5 pe-5">
                        <div class="col">
                        <?php
                            $d = $database->fetchAll("SELECT * FROM settings WHERE id = '1'");     
                            foreach( $d as $k => $row ) {
                        ?>
                            <ul class="list-unstyled d-flex pt-2">
                                <li class=""><a class="link-body-emphasis" href="<?= $row["instagram"]; ?>"><i class="fa-brands fa-instagram fa-2xl" ></i></a></li>
                                <li class="ms-3"><a class="link-body-emphasis" href="<?= $row["twitter"]; ?>"><i class="fa-brands fa-x-twitter fa-2xl"></i></a></li>
                                <li class="ms-3"><a class="link-body-emphasis" href="<?= $row["facebook"]; ?>"><i class="fa-brands fa-facebook fa-2xl" ></i></a></li>
                                <li class="ms-3"><a class="link-body-emphasis" href="<?= $row["linkedin"]; ?>"><i class="fa-brands fa-linkedin-in fa-2xl"></i></a></li>
                                <li class="ms-3"><a class="link-body-emphasis" href="<?= $row["youtube"]; ?>"><i class="fa-brands fa-youtube fa-2xl" ></i></a></li>
                            </ul>
                        <?php }?>
                        </div>
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
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/app.js"></script>