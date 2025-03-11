<?php
require '../functions/admin_template.php';
require '../functions/functions.php';
ini_set('display_errors', 1);  // Hataları ekrana göster
error_reporting(E_ALL);   
$currentPage = 'kayitol';
$template = new Template('Nokta Elektronik - Kayıt Ol', $currentPage);

$template->head();

$database = new Database();
?>
<body>
<style>
    body {
        background-color: #ddd8e1;
        font-family: "Manrope", sans-serif;
        font-optical-sizing: auto;
        font-style: normal;
    }
</style>
<?php $zorunlu = "<span style='color: red;'>*</span>"?>
<div class="container">
    <div class="py-3">
        <div class="row justify-content-center">
            <div class="col-md-8 box-type-2">
                <div class="row">
                    <div class="header w-100 d-flex align-items-center justify-content-center" style="height: 125px; background-color: #ddd8e1">
                        <a href="https://www.noktaelektronik.com.tr">
                            <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/site/logo_new.png" alt="Logo">
                        </a>
                    </div>
                    <div class="py-3 bg-white shadow rounded">
                        <h5>Online Kayıt Formu</h5>
                        <form class="row" action="functions/uyeler/uye_kayit.php" id="kurumsalkayitol" method="post" enctype="multipart/form-data">
                            <div class="col-md-6 col-sm-6 my-2">
                                <input type="text" class="form-control" id="ad" name="ad" placeholder="Adınız" required>
                                <input type="text" hidden name="lang" value="tr">
                            </div>
                            <div class="col-md-6 col-sm-6 my-2">
                                <input type="text" class="form-control" id="soyad" name="soyad" placeholder="Soyadınız" required>
                            </div>
                            <div class="col-md-6 col-sm-6 my-3">
                                <input type="text" class="form-control" id="firma_ad" name="firma_ad" placeholder="Firma Adı" required>
                            </div>
                            <div class="form-check ms-2">
                                <div class="checkbox">
                                    <input type="checkbox" id="vrgtc" name="vrgtc" class="form-check-input">Vergi numarası yerine TC Kimlik numarası kullan.
                                </div>
                            </div>
                            <div class="col-md-6 my-2" id="vergiNo">
                                <input type="text" class="form-control" id="vergi_no" name="vergi_no" placeholder="Vergi Numaranız" maxlength="10" minlength="10">
                            </div>
                            <div class="col-md-6 my-2" id="tcNo">
                                <input type="text" class="form-control" id="tc_no" name="tc_no" maxlength="11" placeholder="Tc Kimlik Numaranız" minlength="11">
                            </div>
                            <div class="col-md-6 my-2">
                                <input type="text" class="form-control" id="vergi_dairesi" name="vergi_dairesi" placeholder="Vergi Dairesi">
                            </div>
                            <div class="col-md-6 my-2">
                                <input type="email" class="form-control" id="eposta" required name="eposta" placeholder="E-posta adresiniz">
                            </div>
                            <div class="col-md-6 my-2">
                                <input type="tel" class="form-control" id="tel" required name="tel" oninput="validatePhoneNumber(this)" placeholder="Cep Telefonu Numarası">
                            </div>
                            <div class="col-md-6 my-2">
                                <input type="tel" class="form-control" id="sabit_tel" name="sabit_tel" placeholder="Sabit Telefon Numarası">
                            </div>
                            <div class="col-md-6 my-2">
                                <input type="password" class="form-control" id="parola4" required name="parola4" placeholder="Şifre" minlength="6">
                            </div>
                            <div class="col-md-6 my-2">
                                <input type="password" class="form-control" id="parola3" required name="parola3" placeholder="Şifre Tekrar" minlength="6">
                                <div id="password-match-message2" style="color: lightgreen;"></div>
                            </div>
                            <div class="col-md-6 my-2">
                                <select name="ulke" required class="form-select">
                                    <option>Türkiye</option>
                                </select>
                            </div>
                            <div class="col-md-6 my-2">
                                <select  name="il" required class="form-control il1">
                                    <option>İl <?= $zorunlu ?></option>
                                    <?php 
                                    if ($d = $database->fetchAll("SELECT * FROM iller")) {
                                        foreach ($d as $k => $row) { ?>
                                            <option class="ilce_id1" value="" hidden></option>
                                            <option value="<?= $row['il_id'] ?>"><?= $row["il_adi"] ?></option>
                                        <?php }} ?>
                                </select>
                            </div>
                            <div class="col-md-6 my-2">
                                <select required class="form-control ilce1" name="ilce"></select>
                            </div>
                            <div class="col-md-6 my-2">
                                <input type="text" class="form-control" id="posta_kodu" name="posta_kodu" placeholder="Posta Kodu">
                            </div>
                            <div class="col-md-6 my-2">
                                <textarea name="adres" id="adres" class="form-control" placeholder="Adres" required></textarea>
                            </div>
                            <div class="col-md-6 my-2">
                                <label for="vergi_levhasi" class="form-label">Vergi Levhası*</label>
                                <input type="file" name="vergi_levhasi" id="vergi_levhasi" class="form-control" required>
                            </div>
                            <div class="col-12 my-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="onay" name="onay" required>
                                    <label class="form-check-label" for="gridCheck">
                                        <a class="sozBtn text-decoration-none text-black" data-toggle="modal" data-target="#sozlesmeModal">Kayıt Ol butonuna basarak <span style="color: #0d6efd; cursor: pointer;">Üyelik Sözleşmesi ’ni</span> okuduğumu ve kabul ettiğimi onaylıyorum.</a>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 my-2">
                                <button name="kurumsalkayitol" type="submit" class="btn btn-primary">Kayıt Ol</button>
                            </div>
                        </form>
                        <div id="responseMessage"></div>
                    </div>
                </div>
            </div>

            <div>

            </div>
        </div>
    </div>
</div>
<!-- Modal Sözleşme -->
<div class="modal fade" data-bs-backdrop="static" id="sozlesmeModal" tabindex="-1" role="dialog" aria-labelledby="sozlesmeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sozlesmeModalLabel">Şartlar ve Koşullar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-sm-12">
                        <h5>Üyelik Sözleşmesi</h5>
                        <p>Üyelik Sözleşmesi metni buraya gelecek.</p>
                    </div>
                </div>
                <hr class="my-4">
                <button class="w-100 btn btn-primary btn-lg sozOnay" style="background-color:#f29720; border-color:#f29720">Sözleşmeyi okudum, onaylıyorum.</button>
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
        function loadIlceler() {
            var il_id = $('.il1').val();
            var ilce = $('.ilce_id1').val();
            $.ajax({
                url: "functions/adres/ile_gore_ilce.php",
                type: "POST",
                data: {
                    il_id: il_id,
                    ilce: ilce
                },
                cache: false,
                success: function(result) {
                    $(".ilce1").html(result);
                }
            });
        }
        // Check if il is not empty, then load ilceler
        if ($('.il1').val() !== '') {
            loadIlceler();
        }
        // Attach the function to the 'change' event of #il
        $('.il1').on('change', loadIlceler);

        $('.sozBtn').click(function() {
            $('#sozlesmeModal').modal('show');
        });
        $('.sozOnay').click(function() {
            $("#onay").prop("checked", true);
            $('#sozlesmeModal').modal('hide');
        });

        // Initially hide the fields
        $("#vergiNo").show();
        $("#tcNo").hide();

        // Add change event listener to the checkbox
        $("#vrgtc").change(function() {
            // If the checkbox is checked, hide tcNo and show vergiNo
            if ($(this).is(":checked")) {
                $("#vergiNo").hide();
                $("#tcNo").show();
            } else {
                // If the checkbox is not checked, show tcNo and hide vergiNo
                $("#vergiNo").show();
                $("#tcNo").hide();
            }
        });
   
        $('#parola, #parola2').on('input', function() {
            // Get the values of both password fields
            var password1 = $('#parola').val();
            var password2 = $('#parola2').val();
            var messageElement = $('#password-match-message');

            if (password1 === '' && password2 === '') {
                messageElement.text('').css('color', 'transparent');
            } else {
                if (password1 === password2) {
                    messageElement.text('Şifreler eşleşiyor').css('color', 'green');
                } else {
                    messageElement.text('Şifreler eşleşmiyor').css('color', 'red');
                }
            }
        });
    });
    function validatePhoneNumber(input) {
        // Sadece sayıları içeren bir regex
        var regex = /^[0-9]*$/;
        var inputValue = input.value;
        
        if (regex.test(inputValue) && inputValue.length <= 10) {// Eğer giriş geçerli bir sayı ise ve en fazla 10 karakter içeriyorsa
            input.setCustomValidity('');// Doğrulama başarılı, hata mesajını temizle
        } else {
            input.setCustomValidity('Sadece sayı ve en fazla 10 karakter girebilirsiniz. Ör:5xxxxxxxxx ');// Doğrulama başarısız, hata mesajını ayarla
        }
    }
</script>
<script>
$(document).ready(function() {
    $('#kurumsalkayitol').on('submit', function(e) {
        e.preventDefault(); // Sayfanın yenilenmesini engelle

        var formData = new FormData(this); // Form verilerini al

        $.ajax({
            url: $(this).attr('action'), // form action değerini kullan
            type: $(this).attr('method'), // form method değerini kullan
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json', // JSON yanıt bekleniyor
            success: function(response) {
                if (response.success) {
                    $('#responseMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                } else {
                    $('#responseMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#responseMessage').html('<div class="alert alert-danger">Bir hata oluştu.</div>');
            }
        });
    });
});

</script>