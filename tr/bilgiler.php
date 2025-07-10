<?php
require_once '../functions/admin_template.php';
require_once '../functions/functions.php';
$currentPage = 'bilgiler';
$template = new Template('Nokta B2B - Bilgiler', $currentPage);
$template->head();
$database = new Database();
sessionControl();

$session_id = $_SESSION['id'];
?>
<body>
<?php $template->header(); ?>
<nav aria-label="breadcrumb" class="container mt-4">
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;"><symbol id="house-door-fill" viewBox="0 0 16 16">
            <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/></symbol>
    </svg>
    <ol class="breadcrumb ">
        <li class="breadcrumb-item">
            <a class="link-body-emphasis" href="index"><svg class="bi" width="15" height="15"><use xlink:href="#house-door-fill"></use></svg><span class="visually-hidden">Anasayfa</span></a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Hesap Bilgilerim</li>
    </ol>
</nav>
<section class="container">
    <div class="row">
        <?php $template->leftMenuProfile(); ?>
        <div class="float-end col-xs-12 col-sm-12 col-md-9">
            <div class="tab-regular">
                <ul class="nav nav-tabs nav-fill" id="myTab7" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="uyelik-bilgiler-tab-justify" data-bs-toggle="tab" href="#uyelik-bilgiler-justify" role="tab" aria-controls="uyelik-bilgiler" aria-selected="true">Üyelik Bilgilerim</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="adresler-tab-justify" data-bs-toggle="tab" href="#adresler-justify" role="tab" aria-controls="adresler" aria-selected="false">Teslimat Adreslerim</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="sifre-degistirme-tab-justify" data-bs-toggle="tab" href="#sifre-degistirme-justify" role="tab" aria-controls="sifre-degistirme" aria-selected="false">Şifre Değiştirme</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent7">
                    <!-- Üyelik Bilgilerim -->
                    <div class="tab-pane fade show active" id="uyelik-bilgiler-justify" role="tabpanel" aria-labelledby="uyelik-bilgiler-tab-justify">
                        <div class="row bg-white p-5 border rounded-2">
                            <div class="form-floating p-1">
                                <p style="color: red">*Fatura bilgilerinizde değişiklik yapmak isterseniz satış temsilcinize danışınız.
                                    <br>   veya <a href="mailto:destek@noktaelektronik.com.tr">destek@noktaelektronik.com.tr</a> adresine mail atabilirsiniz.</p>
                            </div>
                            <?php
                                $uye = $database->fetchAll("SELECT u.*, il.*, ilce.*
                                            FROM uyeler AS u
                                            JOIN iller AS il ON u.il = il.il_id
                                            JOIN ilceler AS ilce ON u.ilce = ilce.ilce_id
                                            WHERE u.id = :session_id
                                        ", ['session_id' => $session_id]);
                                foreach($uye as $row){
                                ?>
                                <div class="form-floating col-6 p-1">
                                    <input type="text" class="form-control" id="ad" name="ad" placeholder="" value="<?= $row["ad"]; ?>" disabled>
                                    <label for="ad">Adınız</label>
                                </div>
                                <div class="form-floating col-6 p-1">
                                    <input type="text" class="form-control" id="soyad" name="soyad" placeholder="" value="<?= $row["soyad"]; ?>" disabled>
                                    <label for="soyad">Soyadınız</label>
                                </div>
                                <div class="form-floating col-12 p-1">
                                    <input type="text" class="form-control" id="firma_ad" name="firma_ad" placeholder="" value="<?= $row["firmaUnvani"]; ?>"disabled>
                                    <label for="firma_ad">Firma Ünvanı</label>
                                </div>
                                <div class="form-floating col-6 p-1" id="vergiNo">
                                    <input type="text" class="form-control" id="vergi_no" name="vergi_no" placeholder="" value="<?= $row["vergi_no"]; ?>" disabled>
                                    <label for="vergi_no">Vergi Numarası</label>
                                </div>
                                <div class="form-floating col-6 p-1" id="tcNo">
                                    <input type="text" class="form-control" id="tc_no" name="tc_no" placeholder="" value="<?= $row["tc_no"]; ?>" disabled>
                                    <label for="tc_no">T.C. Kimlik No</label>
                                </div>
                                <div class="form-floating col-6 p-1">
                                    <input type="text" class="form-control" id="vergi_dairesi" name="vergi_dairesi" placeholder="" value="<?= $row["vergi_dairesi"]; ?>" disabled>
                                    <label for="vergi_dairesi">Vergi Dairesi</label>
                                </div>
                                <div class="form-floating col-6 p-1">
                                    <input type="email" readonly class="form-control" id="eposta" name="eposta" placeholder="" value="<?= $row["email"]; ?>" disabled>
                                    <label for="eposta">E-Posta</label>
                                </div>
                                <div class="form-floating col-6 p-1">
                                    <input type="text" class="form-control" id="tel" name="tel" placeholder="" value="<?= $row["tel"]; ?>" disabled>
                                    <label for="text">Telefon Numarası</label>
                                </div>
                                <div class="form-floating col-6 p-1">
                                    <input type="text" class="form-control" id="sabit_tel" name="sabit_tel" placeholder="" value="<?= $row["sabit_tel"]; ?>" disabled>
                                    <label for="sabit_tel">Sabit Telefon</label>
                                </div>
                                <div class="form-floating col-6 p-1">
                                    <select id="ulke" name="ulke" class="form-control" disabled>
                                        <option value="<?= $row["ulke"]; ?>"><?= $row["ulke"]; ?></option>
                                    </select>
                                    <label for="ulke">Ülke</label>
                                </div>
                                <div class="form-floating col-6 p-1">
                                    <select required name="il" class="form-control il1" disabled>
                                        <?php if($row["il"] == NULL){ ?>
                                            <option>İl</option>
                                        <?php }else { ?>
                                            <option value="<?= $row["il_id"]; ?>"><?= $row["il_adi"]; ?></option>
                                        <?php } ?>
                                        <option class="ilce_id1" value="<?= $row["ilce_id"] ?>" hidden></option>
                                        <option class="ilce_adi1" value="<?= $row["ilce_adi"] ?>" hidden></option>
                                        <option class="dilturu1" value="<?= $_GET['lang']; ?>" hidden></option>
                                        <?php $iller = $database->fetchAll("SELECT * FROM iller");
                                            foreach ($iller as $k => $row1) { ?>
                                                <option value="<?= $row1['il_id'] ?>"><?= $row1["il_adi"] ?></option>
                                        <?php } ?>
                                    </select>
                                    <label for="il1">İl</label>
                                </div>
                                <div class="form-floating col-6 p-1">
                                    <select required class="form-control ilce1" name="ilce" disabled></select>
                                    <label for="ilce1">İlçe</label>
                                </div>
                                <div class="form-floating col-6 p-1">
                                    <input type="text" class="form-control" id="posta_kodu" name="posta_kodu" placeholder="" value="<?= $row["posta_kodu"]; ?>" disabled>
                                    <label for="posta_kodu">Posta Kodu</label>
                                </div>
                                <div class="form-floating col-12 p-1">
                                    <textarea name="adres" id="adres" class="form-control" disabled><?= $row["adres"]; ?></textarea>
                                    <label for="adres">Adres</label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <!-- Adreslerim -->
                    <div class="tab-pane fade" id="adresler-justify" role="tabpanel" aria-labelledby="adresler-tab-justify">
                        <div class="row bg-white border rounded-2 p-1">
                            <div class="col-12 mt-3">
                                <div class="table table-responsive">
                                    <table class=" border" style="width: 100%; border-color:#e1e1e1 ">
                                        <thead class="border" >
                                        <td class="p-2 text-center border fs-10">Adres Başlığı</td>
                                        <td class="p-2 text-center border fs-10">Adres</td>
                                        <td class="p-2 text-center border fs-10">Telefon</td>
                                        <td class="p-2 text-center border fs-10">İşlem</td>
                                        <td class="p-2 text-center border fs-10">Varsayılan Adres</td>
                                        </thead>
                                        <tbody class="table-group-divider">
                                        <?php
                                        $d = $database->fetchAll("SELECT * FROM b2b_adresler WHERE uye_id = $session_id");
                                        foreach($d as $row) {
                                            ?>
                                            <tr class="border">
                                                <td class="p-2 text-center border fs-10"><?= $row["adres_basligi"]; ?></td>
                                                <td class="p-2 text-center border fs-10"><?= $row["adres"]; ?></td>
                                                <td class="p-2 text-center border fs-10"><?= $row["telefon"]; ?></td>
                                                <td class="p-2 text-center border fs-10" style="background-color: grey;">
                                                    <a class="ps-3 pe-2 adres-btn" data-adres-id="<?= $row['id']; ?>"><i class="fa-solid fa-edit fa-lg" style="color: white;" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Adresi düzenle!"></i></a>
                                                    <a class="pe-3 ps-2" onclick="dynamicSil('<?= $row['id'] ?>', '', 'uyeAdresSil', 'Adresiniz başarıyla silinmiştir.', 'tr/bilgiler',)"><i class="fa-solid fa-trash fa-lg" style="color: white;" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Adresi Sil!"></i></a>
                                                </td>
                                                <td class="p-2 text-center border fs-10">
                                                    <input type="checkbox" class="form-check-input adres-aktif-checkbox" data-adres-id="<?= $row['id']; ?>" <?= $row['aktif'] ? 'checked' : ''; ?>>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Şifre Değiştirme -->
                    <div class="tab-pane fade" id="sifre-degistirme-justify" role="tabpanel" aria-labelledby="sifre-degistirme-tab-justify">
                        <div class="row bg-white p-5 border rounded-2">
                            <main class="form-signin col-12 m-auto " style="background-color: white;">
                                <form id="passwordForm" method="post">
                                    <div class="form-floating mt-2">
                                        <input type="text" name="user_id" hidden value="<?= $_SESSION['id']; ?>">
                                        <input type="text" name="lang" hidden value="tr">
                                        <input type="password" class="form-control" id="eski_parola" name="eski_parola" required>
                                        <label for="eski_parola">Eski Parola</label>
                                    </div>
                                    <div class="form-floating mt-2">
                                        <input type="password" class="form-control" id="yeni_parola" name="yeni_parola" required>
                                        <label for="yeni_parola">Yeni Parola</label>
                                    </div>
                                    <div class="form-floating mt-2">
                                        <input type="password" class="form-control" id="yeni_parola_tekrar" name="yeni_parola_tekrar" required>
                                        <label for="yeni_parola_tekrar">Yeni Parola Tekrarı</label>
                                        <div id="password-match-message" style="color: red;"></div>
                                    </div>
                                    <button class="hover btn btn-primary w-100 py-2 mt-3" type="submit" name="sifre_guncelle" style="border-color:#f29720; background-color:#f29720; color:#ffffff; font-weight: 500;">Güncelle</button>
                                </form>
                            </main>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
</section>
<!-- Modal Adres Formu -->
<div class="modal fade" data-bs-backdrop="static" id="basvuruModal" tabindex="-1" role="dialog" aria-labelledby="basvuruModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="basvuruModalLabel">Adres Düzenleme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="applicationForm" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <label for="adres_basligi" class="form-label">Adres Başlığı*</label>
                            <input type="text" class="form-control" id="adres_basligi" required>
                            <input style="display: none;" type="text" id="adresId" name="adresId">
                            <input style="display: none;" type="text" id="uyeId" name="uyeId" value="<?= $session_id ?>">
                        </div>
                        <div class="col-sm-6">
                            <label for="ad" class="form-label">Ad*</label>
                            <input type="text" class="form-control" id="ad1" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="soyad" class="form-label">Soyad*</label>
                            <input type="text" class="form-control" id="soyad1" required>
                        </div>
                        <div class="col-sm-12">
                            <label for="tel" class="form-label">Telefon*</label>
                            <input type="text" class="form-control" id="tel1">
                        </div>
                        <div class="col-sm-12">
                            <label for="adres" class="form-label">Adres*</label>
                            <input type="text" class="form-control" id="adres1">
                        </div>
                        <div class="col-sm-6">
                            <label for="ulke" class="form-label">Ülke*</label>
                          <select class="form-control form-control-sm">
                                <option>Türkiye</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="il" class="form-label">İl*</label>
                            <select id="il" name="il" class="form-control form-control-sm">

                                <?php 
                                    foreach ($iller as $k => $row) { ?>
                                        <option id="ilce_id" hidden></option>
                                        <option value="<?= $row['il_id'] ?>"><?= $row["il_adi"] ?></option>
                                    <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label for="ilce" class="form-label">İlçe*</label>
                            <select class="form-control form-control-sm" id="ilce" name="ilce"></select>
                        </div>
                        <div class="col-sm-6">
                            <label for="posta_kodu" class="form-label">Posta Kodu*</label>
                            <input type="text" class="form-control" id="posta_kodu1">
                        </div>
                    </div>
                    <button class="w-100 btn btn-primary btn-lg my-4" style="background-color:#f29720; border-color:#f29720" type="submit">Gönder</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $template->footer(); ?>
</body>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/splide/splide.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/app.js"></script>
<script>
    $(document).ready(function() {
        $('.adres-aktif-checkbox').change(function() {
            var adresId = $(this).data('adres-id');
            var isChecked = $(this).prop('checked') ? 1 : 0;
            var uyeId = $('#uyeId').val();
            $('.adres-aktif-checkbox').not(this).prop('checked', false);
            $.ajax({
                type: 'POST',
                url: 'functions/edit_info.php', // Aktif durumu güncelleyecek PHP dosyanızın adını ve yolunu buraya yazın
                data: { adres_id: adresId, aktif: isChecked, uye_id: uyeId, type:"adresAktif" },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Teslimat adresi değiştirildi.',
                        toast: true,
                        position: 'center',
                        timer: 3000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata',
                        toast: true,
                        position: 'center',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });
    });
</script>
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
<script>
    $(document).ready(function () {
        var dropdownToggle = $('#yourDropdownToggleId');
        var dropdownMenu = $('#yourDropdownMenuId');

        // Set the width of the dropdown menu to match the width of the parent div
        dropdownMenu.width(dropdownToggle.outerWidth());
    });
</script>
<script>
    $(document).ready(function() {
        $('#passwordForm').submit(function(event) {
            event.preventDefault(); // Formun varsayılan submit işlemini engelle
            var submitButton = $('button[name="sifre_guncelle"]');
            submitButton.prop('disabled', true).text('Güncelleniyor...'); // Butonu devre dışı bırak

            var user_id = $('input[name="user_id"]').val();
            var eski_parola = $('#eski_parola').val();
            var yeni_parola = $('#yeni_parola').val();
            var yeni_parola_tekrar = $('#yeni_parola_tekrar').val();
            $.ajax({
                type: "POST",
                url: "functions/functions.php",
                data: {
                    user_id: user_id,
                    eski_parola: eski_parola,
                    yeni_parola: yeni_parola,
                    yeni_parola_tekrar: yeni_parola_tekrar,
                    sifre_guncelle: 'sifre_guncelle'
                },
                success: function(response) {
                    $('#password-match-message').html(response);
                    if (response.includes("successfully")) {
                        $('#passwordForm')[0].reset(); // Formu sıfırla
                    }
                },
                error: function() {
                    alert("An error occurred during the AJAX request.");
                },
                complete: function() {
                    submitButton.prop('disabled', false).text('Güncelle'); // İşlem bitince butonu etkinleştir
                }
            });
        });
        $('#yeni_parola, #yeni_parola_tekrar').on('input', function() {
            var password1 = $('#yeni_parola').val();
            var password2 = $('#yeni_parola_tekrar').val();
            var messageElement = $('#password-match-message');

            if (password1 === '' && password2 === '') {
                messageElement.text('').css('color', 'transparent');
            } else {
                if (password1 === password2) {
                    messageElement.text('Şifreler Eşleşiyor').css('color', 'green');
                } else {
                    messageElement.text('Şifreler Eşleşmiyor!').css('color', 'red');
                }
            }
        });
    });
</script>
<!-- adres başlangıcı -->
<script>
        function loadIlceler() {
            var il_id = $('#il').val();
            var ilce = $('#ilce_id').val();
            $.ajax({
                url: "functions/adres/ile_gore_ilce.php",
                type: "POST",
                data: {
                    il_id: il_id,
                    ilce: ilce
                },
                cache: false,
                success: function(result) {
                    $("#ilce").html(result);
                }
            });
        }
        // Execute the function when the page loads
        $(document).ready(function() {
            // Check if il is not empty, then load ilceler
            if ($('#il').val() !== '') {
                loadIlceler();
            }
            // Attach the function to the 'change' event of #il
            $('#il').on('change', loadIlceler);
        });
</script>
<script>
    $(document).ready(function() {
        var modalMode = ''; // Variable to track modal mode: 'update' or 'insert'
        $('.adres-btn').click(function() {
            var adresId = $(this).data('adres-id');
            $('#applicationForm')[0].reset();
            // Determine modal mode based on whether an address ID is provided
            modalMode = adresId ? 'update' : 'insert';
            // Reset or populate form fields based on modal mode
            if (modalMode === 'update') {
                $.ajax({
                    url: 'php/get_info.php',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        id: adresId,
                        type: 'adresGetir'
                    },
                    success: function(response) {
                        // Populate the modal with the fetched address details
                        $('#adres_basligi').val(response.adres_basligi);
                        $('#ad1').val(response.ad);
                        $('#soyad1').val(response.soyad);
                        $('#tel1').val(response.telefon);
                        $('#adres1').val(response.adres);
                        $('#ulke').val(response.ulke);
                        $('#il').val(response.il);
                        $('#ilce_id').val(response.ilce);
                        $('#posta_kodu1').val(response.posta_kodu);
                        $('#adresId').val(response.id);
                        loadIlceler();
                    }
                });
            } else {
                // Clear certain form fields for insert mode
                $('#il').val('');
                $('#ilce').val('');
            }
            // Show the modal dialog
            $('#basvuruModal').modal('show');
        });
        // Event listener for form submission
        $('#applicationForm').submit(function(e) {
            e.preventDefault();
            // Retrieve form field values
            var adres_basligi = $('#adres_basligi').val();
            var ad = $('#ad1').val();
            var soyad = $('#soyad1').val();
            var tel = $('#tel1').val();
            var adres = $('#adres1').val();
            var ulke = $('#ulke').val();
            var il = $('#il').val();
            var ilce = $('#ilce').val();
            var posta_kodu = $('#posta_kodu1').val();
            var adresId = $('#adresId').val();
            var uyeId = $('#uyeId').val();
            // Check if all required fields are filled
            if (adres_basligi && ad && soyad && tel && adres && posta_kodu) {
                // Prepare form data for submission
                var formData = new FormData();
                formData.append('adres_basligi', adres_basligi);
                formData.append('ad', ad);
                formData.append('soyad', soyad);
                formData.append('tel', tel);
                formData.append('adres', adres);
                formData.append('ulke', ulke);
                formData.append('il', il);
                formData.append('ilce', ilce);
                formData.append('posta_kodu', posta_kodu);
                formData.append('adresId', adresId);
                formData.append('uyeId', uyeId);
                formData.append('type', modalMode === 'update' ? 'adresGuncelle' : 'adresEkle'); // Use modalMode to determine action
                // Perform AJAX request to update or insert address
                $.ajax({
                    type: 'POST',
                    url: 'php/edit_info.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // On successful submission, hide the modal dialog
                        $('#basvuruModal').modal('hide');
                        // Show success message
                        Swal.fire({
                            title: "Adresiniz Güncellenmiştir",
                            icon: "success",
                            showConfirmButton: false
                        });
                        // Reload the page after a short delay
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    }
                });
            } else {
                // If any required field is empty, show an error message
                Swal.fire({
                    title: "Lütfen tüm alanları doldurunuz!",
                    icon: "error",
                    showConfirmButton: true,
                    timer: 3000
                });
            }
        });
    });
</script>
<!-- adres sonu -->
<!-- Bilgiler başlangıcı -->
<script>
    $(document).ready(function() {
        function loadIlceler1() {
            var il_id = $('.il1').val();
            var ilce_id = $('.ilce_id1').val();
            var ilce_adi = $('.ilce_adi1').val();
            var lang = $('.dilturu1').val();
            $.ajax({
                url: "functions/adres/ile_gore_ilce_bilgiler.php",
                type: "POST",
                data: {
                    il_id: il_id,
                    ilce_id: ilce_id,
                    ilce_adi : ilce_adi,
                    lang: lang
                },
                cache: false,
                success: function(result) {
                    $(".ilce1").html(result);
                }
            });
        }
        // Execute the function when the page loads
        $(document).ready(function() {
            // Check if il is not empty, then load ilceler
            if ($('.il1').val() !== '') {
                loadIlceler1();
            }
            // Attach the function to the 'change' event of #il
            $('.il1').on('change', loadIlceler1);
        });
    });
</script>