<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'sifremiunuttum';
$template = new Template('Nokta B2B - Şifre Güncelleme', $currentPage);

$template->head();
$database = new Database();
$code = $_GET['code'] ?? '';

// Kodu kontrol et
if (empty($code)) {
    header('Location: giris.php');
    exit();
}

$row = $database->fetch("SELECT uye_id FROM b2b_sifre_degistirme WHERE kod = :code", ['code' => $code]);
if (!$row) {
    header('Location: giris.php');
    exit();
}
?>
<body>
<?php $template->header(); ?>

<nav aria-label="breadcrumb" class="container mt-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Anasayfa</a></li>
        <li class="breadcrumb-item active">Şifre Güncelleme</li>
    </ol>
</nav>

<section class="container mb-5">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">Şifre Güncelleme</h4>
                    <form id="passwordForm" method="post">
                        <input type="hidden" id="code" name="code" value="<?= htmlspecialchars($code) ?>">
                        
                        <div class="mb-3">
                            <label for="yeni_parola" class="form-label">Yeni Şifre</label>
                            <input type="password" class="form-control" id="yeni_parola" name="yeni_parola" required 
                                   minlength="6" placeholder="En az 6 karakter">
                        </div>
                        
                        <div class="mb-3">
                            <label for="yeni_parola_tekrar" class="form-label">Yeni Şifre (Tekrar)</label>
                            <input type="password" class="form-control" id="yeni_parola_tekrar" name="yeni_parola_tekrar" 
                                   required minlength="6" placeholder="Şifrenizi tekrar girin">
                        </div>
                        
                        <div id="password-match-message" class="mb-3"></div>
                        <div id="response-message" class="mb-3"></div>
                        
                        <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                            Şifreyi Güncelle
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $template->footer(); ?>

<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/sweetalert2.min.js"></script>

<script>
$(document).ready(function() {
    let isSubmitting = false;
    
    // Şifre eşleşme kontrolü
    $('#yeni_parola, #yeni_parola_tekrar').on('input', function() {
        const password = $('#yeni_parola').val();
        const confirmPassword = $('#yeni_parola_tekrar').val();
        
        if (password && confirmPassword) {
            if (password !== confirmPassword) {
                $('#password-match-message').html('<div class="alert alert-danger">Şifreler eşleşmiyor.</div>');
                $('#submitBtn').prop('disabled', true);
            } else {
                $('#password-match-message').html('<div class="alert alert-success">Şifreler eşleşiyor.</div>');
                $('#submitBtn').prop('disabled', false);
            }
        } else {
            $('#password-match-message').empty();
            $('#submitBtn').prop('disabled', false);
        }
    });
    
    // Form gönderimi
    $('#passwordForm').submit(function(e) {
        e.preventDefault();
        
        if (isSubmitting) return;
        isSubmitting = true;
        
        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gönderiliyor...');
        
        $.ajax({
            type: 'POST',
            url: '../functions/functions.php',
            data: {
                code: $('#code').val(),
                yeni_parola: $('#yeni_parola').val(),
                yeni_parola_tekrar: $('#yeni_parola_tekrar').val(),
                sifre_kaydet: 'sifre_kaydet'
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: result.message,
                            confirmButtonText: 'Tamam'
                        }).then(() => {
                            window.location.href = 'giris.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: result.message
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Beklenmeyen bir hata oluştu.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Sunucu ile iletişim kurulamadı.'
                });
            },
            complete: function() {
                isSubmitting = false;
                submitBtn.prop('disabled', false).text('Şifreyi Güncelle');
            }
        });
    });
});
</script>
</body>
</html>

