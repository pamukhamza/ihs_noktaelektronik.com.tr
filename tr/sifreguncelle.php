<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'sifremiunuttum';
$template = new Template('Nokta B2B - Şifremi Unuttum', $currentPage);

$template->head();
$database = new Database();
$code = $_GET['code'] ?? '';
?>
<body>
<?php $template->header(); ?>

<nav aria-label="breadcrumb" class="container mt-4">
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="house-door-fill" viewBox="0 0 16 16">
            <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/>
        </symbol>
    </svg>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a class="link-body-emphasis" href="/">
                <svg class="bi" width="15" height="15"><use xlink:href="#house-door-fill"></use></svg>
                <span class="visually-hidden">Anasayfa</span>
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Şifre Yenileme</li>
    </ol>
</nav>

<section class="container mb-5">
    <div class="row">
        <main class="form-signin col-10 col-xs-10 col-sm-8 col-md-4 m-auto mb-5 mt-5 p-4 border rounded-3" style="background-color: white;">
            <form id="passwordForm" method="post">
                <p>Lütfen yeni şifrenizi giriniz.</p>
                <input type="hidden" id="code" name="code" value="<?= htmlspecialchars($code) ?>">
                <div class="form-floating mt-2">
                    <input type="password" class="form-control" id="yeni_parola" name="yeni_parola" required>
                    <label for="yeni_parola">Yeni Parola</label>
                </div>
                <div class="form-floating mt-2">
                    <input type="password" class="form-control" id="yeni_parola_tekrar" name="yeni_parola_tekrar" required>
                    <label for="yeni_parola_tekrar">Yeni Parola Tekrarı</label>
                    <div id="password-match-message" class="mt-1"></div>
                </div>
                <div id="response-message" class="mt-2"></div>
                <button class="btn btn-primary w-100 py-2 mt-3" type="submit" name="sifre_guncelle" style="border-color:#f29720; background-color:#f29720; color:#ffffff; font-weight: 500;">Gönder</button>
            </form>
        </main>
    </div>
</section>

<?php $template->footer(); ?>
</body>
</html>

<script src="assets/js/jquery-3.7.0.min.js"></script>

<script>
$(document).ready(function () {
    // Şifre eşleşme kontrolü
    $('#yeni_parola, #yeni_parola_tekrar').on('input', function () {
        var pass1 = $('#yeni_parola').val();
        var pass2 = $('#yeni_parola_tekrar').val();
        var msg = $('#password-match-message');

        if (pass1 === '' && pass2 === '') {
            msg.text('').css('color', 'transparent');
        } else if (pass1 === pass2) {
            msg.text('Şifreler Eşleşiyor').css('color', 'green');
        } else {
            msg.text('Şifreler Eşleşmiyor!').css('color', 'red');
        }
    });

    // AJAX gönderimi
    $('#passwordForm').submit(function (e) {
        e.preventDefault();

        var pass1 = $('#yeni_parola').val();
        var pass2 = $('#yeni_parola_tekrar').val();
        var code = $('#code').val();
        var msgBox = $('#response-message');
        var button = $('button[name="sifre_guncelle"]');

        if (pass1 !== pass2) {
            msgBox.text('Şifreler aynı değil!').css('color', 'red');
            return;
        }

        button.prop('disabled', true).text('Gönderiliyor...');

        $.ajax({
            type: 'POST',
            url: 'functions/functions.php',
            data: {
                code: code,
                yeni_parola: pass1,
                sifre_kaydet: 'sifre_kaydet'
            },
            success: function (response) {
                msgBox.html(response).css('color', response.includes('başarıyla') ? 'green' : 'red');
                if (response.includes('başarıyla')) {
                    $('#passwordForm')[0].reset();
                    $('#password-match-message').text('');
                }
            },
            error: function () {
                msgBox.text('Bir hata oluştu. Lütfen tekrar deneyin.').css('color', 'red');
            },
            complete: function () {
                button.prop('disabled', false).text('Gönder');
            }
        });
    });
});
</script>
