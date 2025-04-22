<?php
require_once '../functions/db.php';
require '../functions/admin_template.php';

$currentPage = 'index';
$template = new Template('Nokta Elektronik Bayi Portalı', $currentPage);

$template->head();
$db = new Database();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["girisyap"])) {
    $result = validateGirisForm($db);

    if ($result === true) {
        header('Location: /tr');
        exit();
    } else {
        // Display error message using SweetAlert
        echo "<script>";
        echo "window.onload = function() {";
        echo "  Swal.fire({";
        echo "    icon: 'error',";
        echo "    title: 'Hata!',";
        echo "    text: '" . $result . "',";
        echo "  });";
        echo "}";
        echo "</script>";
    }
}
?>

<style>
    body, html {
        height: 100%;
        font-family: "Manrope", sans-serif;
        font-optical-sizing: auto;
        font-style: normal;
    }
    .form-floating input {
        max-width: 400px;
        width: 100%;
        padding: 1rem;
        border-radius: 0.5rem;
    }
    .form-check {width: 100%;margin-bottom: 1rem;}
    .form-check input {margin-right: 0.5rem;}
    @media (max-width: 992px) {
        .bg-image { display: none; }
        .anafoto{display:none !important;}
        .form-floating input {width: 300px;}
    }
    @media (min-width: 992px) and (max-width:1200px) {
        .anafoto{display:none !important;}
    }
    .box{height: auto;}
    .box-1{
        height: auto;
        background: rgb(243,142,10);
        background: -moz-linear-gradient(21deg, rgba(243,142,10,1) 0%, rgba(49,105,137,1) 55%, rgba(1,96,169,1) 100%);
        background: -webkit-linear-gradient(21deg, rgba(243,142,10,1) 0%, rgba(49,105,137,1) 55%, rgba(1,96,169,1) 100%);
        background: linear-gradient(21deg, rgba(243,142,10,1) 0%, rgba(49,105,137,1) 55%, rgba(1,96,169,1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#f38e0a",endColorstr="#0160a9",GradientType=1);
        /*background-image: url('https://www.noktaelektronik.com.tr/assets/images/pattern-7451714_1920.jpg');
        background-size: cover;
        background-repeat: no-repeat;*/
}
</style>
<body class="d-flex align-items-center">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-NE2FRWRNBJ"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-NE2FRWRNBJ');
</script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="row shadow">
                <div class="col-md-6 box-1 ">
                    <div class="d-flex flex-wrap align-content-between">
                        <div class="py-4 ps-4">
                            <a><img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/site/logo_new.png" width="225px"></a>
                            <h2 class="my-2 text-white">Bayi Girişi</h2>
                            <p class="text-white h6 my-4">Merhaba! Bayimiz olmak için lütfen <a href="tr/kayitol" class="text-white fw-bold">tıklayınız.</a></p>
                            <p class="text-white h6 my-4"></p>
                            <span class="mt-5 text-white">Bizi sosyal medyadan takip edin!</span>
                            <ul class="list-unstyled d-flex mt-1">
                                <?php $d = $db->fetchAll("SELECT * FROM settings WHERE id = '1'");
                                    foreach( $d as $k => $row ){ ?>
                                        <li class=""><a class="link-body-emphasis text-white" href="<?= $row['instagram'];?>"><i class="fa-brands fa-instagram fa-xl" ></i></a></li>
                                        <li class="ms-3"><a class="link-body-emphasis text-white" href="<?= $row['twitter'];?>"><i class="fa-brands fa-x-twitter fa-xl"></i></a></li>
                                        <li class="ms-3"><a class="link-body-emphasis text-white" href="<?= $row['facebook'];?>"><i class="fa-brands fa-facebook fa-xl" ></i></a></li>
                                        <li class="ms-3"><a class="link-body-emphasis text-white" href="<?= $row['linkedin'];?>"><i class="fa-brands fa-linkedin-in fa-xl"></i></a></li>
                                        <li class="ms-3"><a class="link-body-emphasis text-white" href="<?= $row['youtube'];?>"><i class="fa-brands fa-youtube fa-xl" ></i></a></li>
                                    <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 box mb-5">
                    <ul class="nav nav-pills mt-5 ms-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Giriş Yap</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Şifremi Unuttum</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-teknik-tab" data-bs-toggle="pill" data-bs-target="#pills-teknik" type="button" role="tab" aria-controls="pills-teknik" aria-selected="false">Teknik Servis</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="tr/kayitol" class="text-decoration-none">
                            <button class="nav-link" id="pills-kayit" type="button" role="tab" aria-selected="false">Kayıt Ol</button>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active mt-3 ms-3" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                            <div>Merhaba, sisteme kayıtlı e-posta ve şifreniz ile giriş yapabilirsiniz.</div>
                            <form method="post">
                                <div class="form-group mt-3">
                                    <label for="giris-mail" style="color:black;font-weight: bold">E-posta</label>
                                    <input type="text" class="form-control mt-1" name="email" placeholder="E-Posta Adresiniz" required autofocus style="border: 1px solid rgba(0, 123, 255,0.4) !important;">
                                </div>
                                <div class="form-group mt-3">
                                    <label for="parola" style="color:black;font-weight: bold">Şifre</label>
                                    <input type="password" class="form-control mt-1" name="parola" placeholder="Şifreniz" required autofocus style="border: 1px solid rgba(0, 123, 255,0.4) !important;">
                                </div>
                                <div class="form-check text-start my-3">
                                    <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me">
                                    <label class="form-check-label" for="remember_me">Beni Hatırla</label>
                                </div>
                                <button type="submit" name="girisyap" class="btn btn-block btn-general btn-primary py-4 mt-4 w-100">GİRİŞ YAP</button>
                            </form>
                        </div>
                        <div class="tab-pane fade ms-3" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                            <div class="mt-3">Kayıtlı e-posta adresinizi girip 'Gönder' dediğinizde yeni şifreniz e-posta adresinize gönderilecektir.</div>
                            <form id="passwordReset">
                                <div class="form-group mt-3">
                                    <label for="giris-mail" style="color:black;font-weight: bold">E-posta</label>
                                    <input type="email" class="form-control mt-1" id="mail" name="mail" placeholder="E-Posta Adresiniz" required autofocus style="border: 1px solid rgba(0, 123, 255,0.4) !important;">
                                </div>
                                <input type="submit" name="submit" value="GÖNDER" class="btn btn-block btn-general btn-primary py-4 mt-5 w-100">
                            </form>
                        </div>
                        <div class="tab-pane fade ms-3" id="pills-teknik" role="tabpanel" aria-labelledby="pills-teknik-tab" tabindex="0">
                            <div class="mt-3" style="text-align: justify"><span>Yeni arıza kaydı veya mevcut kaydınızın durumu öğrenmek için aşağıdaki butona tıklayınız.
                                    Eğer bayimiz iseniz giriş yaparak teknik servis sayfasına erişmeniz önerilir.</span></div>
                            <div class="form-group mt-3">
                                Sorularınız için bize aşağıdaki mail adresinden ulaşabilirsiniz.
                                <span style="color:black;font-weight: bold">destek@noktaelektronik.com.tr</span><br>
                            </div>
                            <a href="tr/tdp" target="_blank" class="btn btn-block btn-general btn-primary py-4 mt-5 w-100">Teknik Destek Sayfası</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/app.js"></script>

<script>
    $(document).ready(function() {
        $('#passwordReset').submit(function(event) {
            event.preventDefault();
            var mail = $('#mail').val();

            $('input[type="submit"]').prop('disabled', true);
            $.ajax({
                type: "POST",
                url: "../functions/functions.php",
                data: {
                    mail: mail,
                    sifre_unuttum: 'sifre_unuttum'
                },
                success: function(response) {
                    console.log('Response:', response);
                    if(response == 'success'){
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Şifre güncelleme linki e-posta adresinize gönderilmiştir.',
                            showConfirmButton: false,
                            timer: 5000
                        });
                    } else if(response == 'error'){
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'E-posta adresi sistemde kayıtlı değil!',
                            showConfirmButton: true
                        });
                    } else if(response == 'invalid_email'){
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'Geçersiz e-posta adresi!',
                            showConfirmButton: true
                        });
                    } else if(response == 'db_error'){
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'Veritabanı hatası oluştu. Lütfen tekrar deneyin.',
                            showConfirmButton: true
                        });
                    } else {
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'Beklenmeyen bir hata oluştu: ' + response,
                            showConfirmButton: true
                        });
                    }
                    $('input[type="submit"]').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('input[type="submit"]').prop('disabled', false);
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Bir hata oluştu: ' + error,
                        showConfirmButton: true
                    });
                }
            });
        });
    });
</script>
