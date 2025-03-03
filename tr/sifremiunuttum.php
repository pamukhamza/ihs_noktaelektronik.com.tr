<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'sifremiunuttum';
$template = new Template('Nokta B2B - Şifremi Unuttum', $currentPage);

$template->head();
$database = new Database();
?>
<body>
<style>
    body, html {height: 100%;}
    .bg-image {
        background-image: url('https://www.noktaelektronik.com.tr/assets/images/kayitolarkaplan5.jpg'); /* Arka plan resmi buraya */
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        height: 100vh;
    }
    .form-signin {
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    .form-floating input {max-width: 400px;width: 100%;padding: 1rem;border-radius: 0.5rem;}
    .form-check {width: 100%;margin-bottom: 1rem;}
    .form-check input {margin-right: 0.5rem;}
    @media (max-width: 992px) {
        .bg-image { display: none; }
        .form-signin { height: auto; }
        .form-floating input {width: 300px;}
    }
</style>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 d-none d-lg-block bg-image"></div>
            <div class="col-lg-4 col-sm-12">
                <main class="form-signin p-4">
                    <h1 class="mb-5"><a href="https://www.noktaelektronik.com.tr"><img src="assets/images/logo_new.png"></a></h1>
                    <form id="passwordReset" method="post">
                        <p>E-posta adresinizi girerek şifrenizi güncelleyebilirsiniz.</p>
                        <div class="form-floating mt-2">
                            <input type="email" class="form-control" id="mail" name="mail" required>
                            <input type="text" hidden class="form-control" id="lang" name="lang" value="tr">
                            <label for="mail">E-Posta</label>
                        </div>
                        <button class="hover btn btn-primary w-100 py-2 mt-3" type="submit" style="border-color:#f29720; background-color:#f29720; color:#ffffff; font-weight: 500;">Gönder</button>
                    </form>
                    <span class="mt-5">Bizi sosyal medyadan takip edin!</span>
                    <ul class="list-unstyled d-flex mt-1">
                        <?php $row = $database->fetch("SELECT * FROM settings WHERE id = '1'"); ?>
                        <li class=""><a class="link-body-emphasis" href="<?php echo $row['instagram'];?>"><i class="fa-brands fa-instagram fa-xl" ></i></a></li>
                        <li class="ms-3"><a class="link-body-emphasis" href="<?php echo $row['twitter'];?>"><i class="fa-brands fa-x-twitter fa-xl"></i></a></li>
                        <li class="ms-3"><a class="link-body-emphasis" href="<?php echo $row['facebook'];?>"><i class="fa-brands fa-facebook fa-xl" ></i></a></li>
                        <li class="ms-3"><a class="link-body-emphasis" href="<?php echo $row['linkedin'];?>"><i class="fa-brands fa-linkedin-in fa-xl"></i></a></li>
                        <li class="ms-3"><a class="link-body-emphasis" href="<?php echo $row['youtube'];?>"><i class="fa-brands fa-youtube fa-xl" ></i></a></li>
                    </ul>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/splide/splide.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#passwordReset').submit(function(event) {
            event.preventDefault();
            var lang = $('input[name="lang"]').val();
            var mail = $('#mail').val();
            $.ajax({
                type: "POST",
                url: "function.php",
                data: {
                    lang: lang,
                    mail: mail,
                    sifre_unuttum: 'sifre_unuttum'
                },
                success: function(response) {
                    if(response == 'success'){
                        Swal.fire(
                            {position: 'center',
                                icon:'success',
                                title: 'Şifre güncelleme linki e-posta adresinize gönderilmiştir.',
                                showConfirmButton: false,
                                timer:2000
                            }
                        );
                        setTimeout(function() {window.location.href = 'index.php';}, 2000);
                    } else if(response == 'error'){
                        Swal.fire({
                            position: 'center',
                            icon:'error',
                            title: 'E-posta adresi sistemde kayıtlı değil!',
                            showConfirmButton: true
                        });
                    }
                }
            });
        });
    });
</script>
