<?php
require '../functions/admin_template.php';
require '../functions/functions.php';
require '../functions/logger.php';

$currentPage = 'tdp';
$template = new Template('Nokta - Teknik Destek Programı', $currentPage);

$template->head();
$database = new Database();

try {
    $uye_id = isset($_SESSION['id']) ? (int)$_SESSION['id'] : null;
    Logger::info("Sayfa yüklendi", ['user_id' => $uye_id]);
} catch (Exception $e) {
    Logger::error("Sayfa yüklenirken hata oluştu", ['error' => $e->getMessage()]);
}

?>
<style>
      .bi {vertical-align: -.125em;fill: currentColor;}
      .zorunlu{color:red}
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
            <li class="breadcrumb-item active" aria-current="page">Teknik Destek Programı</li>
        </ol>
    </nav>
    <section class="container mb-5">
        <div class="row">
            <?php $template->leftMenuProfile(); ?>
            <div class="float-end col-xs-12 col-sm-8 col-md-9 rounded-3 ">
                <h5>Teknik Destek</h5>
                <?php
                    $tdpp = $database->fetch("SELECT * FROM documents WHERE `type` = 'teknikdestek1' AND `site` = 'b2b'");
                ?>
                <p class="mt-3"><?= $tdpp["title"]; ?></p>
                <button class="form-control p-3 basvur-btn" style=" background-color:#f29720; color:#ffffff; font-weight: 500;" data-toggle="modal" data-target="#basvuruModal" data-basvur-id="1">
                    Onarım Talep Formu Oluşturmak İçin Tıklayınız!<i class="fa-solid fa-angle-right ps-1"></i>
                </button>
                <h5 class="mt-5">Onarım Takibi</h5>
                <p class="mt-3"><?= $tdpp["text"]; ?></p>
                <div class="input-group mb-3 mt-4">
                    <input type="text" class="form-control" id="takip_kodu" placeholder="Onarım Takip Kodu" aria-label="Onarım Takip Kodu" aria-describedby="button-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-secondary " id="ara" >Sorgula</button>
                    </div>
                </div>
                <div class="card my-5" id="bilgi"></div>
                <h5>Geçmiş Onarım Kayıtları</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered second">
                        <thead class="bg-light">
                        <th class="p-2 border-right text-center">Takip Kodu</th>
                        <th class="p-2 border-right text-center">Ürün Kodu</th>
                        <th class="p-2 border-right text-center">Yapılan İşlemler</th>
                        <th class="p-2 border-right text-center">Tarih</th>
                        </thead>
                        <tbody>
                        <?php
                        try {
                            if($uye_id !== null){
                                $c = $database->fetchAll("SELECT * FROM nokta_teknik_destek WHERE uye_id = :uye_id AND SILINDI = 0", ['uye_id' => $uye_id]);
                                Logger::info("Kullanıcı kayıtları getirildi", ['user_id' => $uye_id, 'record_count' => count($c)]);
                                foreach($c as $k => $row) {
                        ?>
                                <tr>
                                    <td class="text-center border-right"><?= htmlspecialchars($row['takip_kodu']); ?></td>
                                    <td class="text-center border-right"><?= htmlspecialchars($row['urun_kodu']); ?></td>
                                    <td class="text-center border-right"><?= htmlspecialchars($row['yapilan_islemler']); ?></td>
                                    <td class="text-center border-right"><?= htmlspecialchars($row['tarih']); ?></td>
                                </tr>
                        <?php 
                                }
                            } else {
                                Logger::info("Giriş yapılmamış kullanıcı kayıt görüntüleme denemesi");
                                echo '<tr><td colspan="4" class="text-center">Görüntülenecek kayıt bulunmamaktadır.</td></tr>';
                            }
                        } catch (Exception $e) {
                            Logger::error("Kayıtlar getirilirken hata oluştu", ['error' => $e->getMessage()]);
                            echo '<tr><td colspan="4" class="text-center text-danger">Kayıtlar getirilirken bir hata oluştu.</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <h5 class="mt-5">Teknik Servis Noktalarımız</h5>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <div class="col ">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-center pb-3 border-bottom">Teknik Servis - İstanbul</h5>
                                <div><i class="fa-solid fa-location-dot pe-1"></i>Adres: 
                                    <a href="https://maps.app.goo.gl/M6TW5wqvMntodJh76" target="_blank" class="adres text-body-secondary text-decoration-none">
                                        Perpa Ticaret Merkezi B Blok Kat8 No.906-907 34384 - Şişli / İstanbul
                                    </a> 
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </div> 
        </div>
    </section>
    <div style="clear:both"></div>
    <?php $template->footer(); ?>
    <!-- Modal Basvuru Formu -->
    <div class="modal fade" data-bs-backdrop="static" id="basvuruModal" tabindex="-1" role="dialog" aria-labelledby="basvuruModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="basvuruModalLabel">Teknik Destek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="applicationForm" class="needs-validation" novalidate>
                        <small>Lütfen kırmızı renkli zorunlu alanları doldurunuz!</small>
                        <div class="row g-3">
                            <div class="col-sm-12">
                                <label for="musteri" class="form-label zorunlu">Müşteri(Firma Bilgisi)</label>
                                <input type="text" class="form-control" id="musteri_id" hidden value="<?= $uye_id ?? '' ?>">
                                <input type="text" class="form-control" id="musteri" value="<?= isset($_SESSION['firma']) ? htmlspecialchars($_SESSION['firma']) : '' ?>" required>
                                <div class="invalid-feedback">Geçerli ad giriniz!</div>
                            </div>
                            <div class="col-sm-6">
                                <label for="tel" class="form-label zorunlu">Telefon*</label>
                                <input type="tel" class="form-control" id="tel" placeholder="0(xxx)xxx xx xx" required>
                                <div class="invalid-feedback">Geçerli Telefon giriniz!</div>
                            </div>
                            <div class="col-sm-6">
                                <label for="email" class="form-label zorunlu">E-Posta*</label>
                                <input type="email" class="form-control" id="email" placeholder="mail@example.com" required>
                                <div class="invalid-feedback">Geçerli e-posta giriniz!</div>
                            </div>
                            <div class="col-sm-12">
                                <label for="adres" class="form-label zorunlu">Adres*</label>
                                <input type="text" class="form-control" id="adres" required>
                                <div class="invalid-feedback">Geçerli Adres giriniz!</div>
                            </div>
                            <div id="input-row-template" style="display: none;">
                                <div class="row mb-2">
                                    <div class="col-sm-4">
                                        <label for="urun_kodu" class="form-label zorunlu">Ürün Kodu*</label>
                                        <input type="text" class="form-control urun_kodu" required>
                                        <div class="invalid-feedback">Geçerli Ürün Kodu giriniz!</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="seri_no" class="form-label">Seri Numarası</label>
                                        <input type="text" class="form-control seri_no">
                                        <div class="invalid-feedback">Geçerli Seri Numarası giriniz!</div>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="adet" class="form-label zorunlu">Adet*</label>
                                        <input type="text" class="form-control adet" required>
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn mt-4 remove-row-btn"><i class="fa-solid fa-circle-minus fa-lg"></i></button>
                                    </div>
                                </div>
                            </div>
                            <small>Birden fazla ürün girmek için + işaretine tıklayınız !</small>
                            <div id="input-rows-container">
                                <div class="row mb-2">
                                    <div class="col-sm-4">
                                        <label for="urun_kodu" class="form-label zorunlu">Ürün Kodu*</label>
                                        <input type="text" class="form-control urun_kodu" required>
                                        <div class="invalid-feedback">Geçerli Ürün Kodu giriniz!</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="seri_no" class="form-label">Seri Numarası</label>
                                        <input type="text" class="form-control seri_no">
                                        <div class="invalid-feedback">Geçerli Seri Numarası giriniz!</div>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="adet" class="form-label zorunlu">Adet*</label>
                                        <input type="text" class="form-control adet" required>
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn mt-4 add-row-btn"><i class="fa-solid fa-circle-plus fa-lg"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="fatura_no" class="form-label">Fatura No</label>
                                <input type="text" class="form-control" id="fatura_no" >
                                <div class="invalid-feedback">Geçerli fatura numarası giriniz!</div>
                            </div>
                            <div class="col-sm-6">
                                <label for="ad_soyad" class="form-label zorunlu">Formu Dolduran Bilgileri*</label>
                                <input type="text" class="form-control" id="ad_soyad" required placeholder="Ad / Soyad...">
                                <div class="invalid-feedback">Geçerli bilgi giriniz!</div>
                            </div>
                            <div class="col-sm-12">
                                <label for="aciklama" class="form-label zorunlu">Açıklama*</label>
                                <textarea name="aciklama" id="aciklama"  class="form-control" required></textarea>
                                <div class="invalid-feedback">Geçerli Ürün Kodu giriniz!</div>
                            </div>
                            <div class="row mb-2 mt-2">
                                <div class="col-6">
                                    <label for="gonderim_sekli" class="form-label zorunlu">Gönderim Şekli*</label>
                                    <select class="form-control" id="gonderim_sekli" name="gonderim_sekli" required>
                                        <option value="1">Kargo ile Gönderim</option>
                                        <option value="2">Elden Teslim</option>
                                    </select>
                                </div>
                                <div class="col-6" id="kargo_firmasi_div">
                                    <label for="kargo_firmasi" class="form-label zorunlu">Kargo Firması*</label>
                                    <select class="form-control" id="kargo_firmasi" name="kargo_firmasi" required>
                                        <option value="">Kargo Firması Seçiniz</option>
                                        <option value="Yurtiçi Kargo">Yurtiçi Kargo</option>
                                        <option value="MNG Kargo">MNG Kargo</option>
                                        <option value="Aras Kargo">Aras Kargo</option>
                                        <option value="Sürat Kargo">Sürat Kargo</option>
                                        <option value="PTT Kargo">PTT Kargo</option>
                                        <option value="Diğer Kargo Firmaları">Diğer Kargo Firmaları</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="checkbox">
                                    <input type="checkbox" id="onay" name="onay" required class="form-check-input"/>
                                    <a class="sozBtn" data-toggle="modal" data-target="#sozlesmeModal">
                                        Arıza Kayıt Sözleşmesini onaylıyorum.
                                    </a>
                                </div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <button class="w-100 btn btn-primary btn-lg" style="background-color:#f29720; border-color:#f29720" type="submit">Gönder</button>
                    </form>
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
                            <p>1. Servis süresi en fazla 30 iş günüdür ve 90 gün takibi yapılmayan ürünler için şirketimiz hiçbir sorumluluk kabul etmez.
                                <br>2. Garanti süreleri, Fatura düzenleme tarihinden itibaren başlar. Bunun dışında belirtilen Üretici Garantisi ancak üreticinin tespit ettiği koşullar çerçevesinde geçerlidir. Nokta Elektronik bu koşulları aynen müşteriye yansıtır.
                                <br>3. Kurulum sırasında oluşan fiziksel ve elektriksel hatalar veya müşteriden kaynaklanan diğer donanım arızalarından dolayı servise gelmiş ürün garanti dışıdır ve servis ücreti alınır.
                                <br>4. Teknik servis ücreti cari hesaba dahil olmayıp peşin olarak tahsil edilir.
                                <br>5. Garanti harici tamir edilen ürünler teslimden itibaren 3 ay garantilidir.
                                <br>6. Nokta Elektronik arızalı ürün servise geldiği anda, eğer kullanıcı hatasını tanımlayabiliyorsa, bunu belirtir ancak ürün daha sonraki test aşamaların da garanti dışı tutulabilir. İstenildiğinde Nokta Elektronik bu tür arızalar için Teknik Rapor verir.
                                <br>7. Bu formu imzalayarak teslim eden şirket ve birey bu koşulları kabul eder. Bu ürünler firmamızın stoğundan çıktığı andan itibaren her türlü risk müşteriye aittir.
                            </p>
                        </div>
                    </div>
                    <hr class="my-4">
                    <button class="w-100 btn btn-primary btn-lg sozOnay" style="background-color:#f29720; border-color:#f29720">sozlesmeyi_okudum_onayliyorum</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Success Modal -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="successModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" id="yazdirButton" class="btn btn-primary"><i class="fa-solid fa-print me-2"></i>Yazdır</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/alert.js"></script>
<script>
     $(document).ready(function() {
        $('.sozBtn').click(function() {
            $('#sozlesmeModal').modal('show');
        });
        $('.sozOnay').click(function() {
            $("#onay").prop("checked", true);
            $('#sozlesmeModal').modal('hide');
        });
     });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var gonderimSekliSelect = document.getElementById("gonderim_sekli");
        var kargoFirmasiDiv = document.getElementById("kargo_firmasi_div");
        var kargoFirmasiInput = document.getElementById("kargo_firmasi");

        // Initial check
        toggleKargoFirmasiVisibility();

        // Event listener for gonderim_sekli change
        gonderimSekliSelect.addEventListener("change", function() {
            toggleKargoFirmasiVisibility();
        });

        // Function to toggle kargo_firmasi visibility
        function toggleKargoFirmasiVisibility() {
            if (gonderimSekliSelect.value === "1") {
                kargoFirmasiDiv.style.display = "block";
                kargoFirmasiInput.setAttribute("required", "required");
            } else {
                kargoFirmasiDiv.style.display = "none";
                kargoFirmasiInput.removeAttribute("required");
            }
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('.basvur-btn').click(function() {
            $('#basvuruModal').modal('show');
        });
        // Yeni giriş satırı ekleme işlevi
        function addInputRow() {
            var newRow = $('#input-row-template').clone().removeAttr('id').removeAttr('style');
            $('#input-rows-container').append(newRow);
        }
        // İlk satır ekleme olayı dinleyicisi
        $(document).on('click', '.add-row-btn', function() {
            addInputRow();
        });
        // Satır silme olayı dinleyicisi
        $(document).on('click', '.remove-row-btn', function() {
            $(this).closest('.row').remove();
        });
        $('#applicationForm').submit(function(e) {
            e.preventDefault();

            // Zorunlu alanları al
            var requiredFields = [ '#musteri', '#tel', '#email', '#adres', '#ad_soyad', '#aciklama' ];

            var allFilled = true;
            requiredFields.forEach(function(selector) {
                var value = $(selector).val().trim();
                if (value === '') {allFilled = false;}
            });

            // Gönderim şekli 1 ise kargo firması zorunlu
            var gonderimSekli = $('#gonderim_sekli').val();
            var kargoFirmasi = $('#kargo_firmasi').val().trim();
            if (gonderimSekli === "1" && kargoFirmasi === "") {
                allFilled = false;
            }

            // E-posta kontrolü
            var email = $('#email').val().trim();
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var validEmail = emailRegex.test(email);

            if (!allFilled) {
                alert("Lütfen tüm zorunlu alanları doldurunuz.");
                return;
            }

            if (!validEmail) {
                alert("Lütfen geçerli bir e-posta adresi giriniz.");
                return;
            }

            // Her şey tamamsa AJAX gönderimi
            $('#basvuruModal').modal('hide');
            Swal.fire({
                title: 'İşlem yapılıyor...',
                text: 'Lütfen bekleyiniz',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            var urun_kodu_array = [];
            var seri_no_array = [];
            var adet_array = [];

            $('#input-rows-container .row').each(function() {
                var urun_kodu = $(this).find('.urun_kodu').val();
                var seri_no = $(this).find('.seri_no').val();
                var adet = $(this).find('.adet').val();

                urun_kodu_array.push(urun_kodu);
                seri_no_array.push(seri_no);
                adet_array.push(adet);
            });

            var formData = new FormData();
            formData.append('urun_kodu', urun_kodu_array.join(','));
            formData.append('seri_no', seri_no_array.join(','));
            formData.append('adet', adet_array.join(','));
            formData.append('musteri_id', $('#musteri_id').val());
            formData.append('musteri', $('#musteri').val());
            formData.append('tel', $('#tel').val());
            formData.append('email', $('#email').val());
            formData.append('adres', $('#adres').val());
            formData.append('fatura_no', $('#fatura_no').val());
            formData.append('aciklama', $('#aciklama').val());
            formData.append('ad_soyad', $('#ad_soyad').val());
            formData.append('onay', $('#onay').is(':checked') ? 1 : 0);
            formData.append('gonderim_sekli', $('#gonderim_sekli').val());
            formData.append('kargo_firmasi', $('#kargo_firmasi').val());
            formData.append('type', 'ariza');

            $.ajax({
                type: 'POST',
                url: 'functions/edit_info.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(gelen) {
                    Swal.close();
                    const response = JSON.parse(gelen);
                    $('#modalTitle').text("Başvurunuz Alınmıştır!");
                    $('#modalBody').html('Arıza Takip Kodunuz: ' + response.takip_kodu);
                    $('#successModal').modal('show');
                },
                error: function(response) {
                    if (response.status === 400) {
                        alert("Lütfen zorunlu alanları doldurunuz !");
                    }
                    if (response.status === 500) {
                        alert("Hatalı e-posta adresi !");
                    }
                    if (response.status === 600) {
                        alert("Lütfen Kargo Firmasını Doldurunuz !");
                    }
                }
            });
        });
        
    });
</script>

<script>
    $(document).ready(function() {
        $("#ara").click(function() {
            var takip_kodu = $("#takip_kodu").val();
            var originalText = $("#ara").text();
            $("#ara").text("Sorgulanıyor...").prop("disabled", true);
            $.ajax({
                type: "POST",
                url: "functions/functions.php",
                data: { takip_kodu: takip_kodu},
                success: function(response) {
                    $("#bilgi").html(response);
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Form validasyonu
        const form = document.getElementById('applicationForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                Logger::warning("Form validasyon hatası");
            }
            form.classList.add('was-validated');
        });

        // AJAX istekleri için hata yönetimi
        $.ajaxSetup({
            error: function(xhr, status, error) {
                Logger::error("AJAX hatası", {
                    'status': status,
                    'error': error,
                    'response': xhr.responseText
                });
                alert('Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.');
            }
        });

        // Form gönderimi
        $('#applicationForm').submit(function(e) {
            e.preventDefault();
            
            if (!this.checkValidity()) {
                return;
            }

            var formData = new FormData(this);
            formData.append('type', 'ariza2');

            $.ajax({
                type: 'POST',
                url: 'functions/edit_info.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            Logger::info("Form başarıyla gönderildi", {'takip_kodu': data.takip_kodu});
                            showSuccessModal(data.takip_kodu);
                            form.reset();
                            form.classList.remove('was-validated');
                        } else {
                            Logger::warning("Form gönderimi başarısız", {'message': data.message});
                            showErrorModal(data.message);
                        }
                    } catch (e) {
                        Logger::error("Form yanıtı işlenirken hata", {'error': e.message, 'response': response});
                        showErrorModal('Beklenmeyen bir hata oluştu');
                    }
                }
            });
        });

        function showSuccessModal(takipKodu) {
            $('#modalTitle').text("Başvurunuz Alınmıştır!");
            $('#modalBody').html('Arıza Takip Kodunuz: ' + takipKodu);
            $('#successModal').modal('show');
        }

        function showErrorModal(message) {
            $('#modalTitle').text("Hata!");
            $('#modalBody').html(message);
            $('#successModal').modal('show');
        }
    });
</script>

<script>
    $(document).ready(function() {
        // Print button click handler
        $('#yazdirButton').click(function() {
            var takipKodu = $('#modalBody').text().split(': ')[1].trim();
            
            // Create a new window for printing
            var printWindow = window.open('', '_blank');
            
            // Show loading message
            printWindow.document.write('<html><head><title>Yazdırılıyor...</title>');
            printWindow.document.write('<style>body { font-family: Arial, sans-serif; } .loading { text-align: center; margin-top: 50px; }</style>');
            printWindow.document.write('</head><body><div class="loading">Yazdırma hazırlanıyor...</div></body></html>');
            
            // Fetch repair details
            $.ajax({
                url: 'functions/get_repair_details.php',
                method: 'POST',
                data: { takip_kodu: takipKodu },
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            // Create print-friendly HTML
                            printWindow.document.write(`
                                <html>
                                <head>
                                    <title>Onarım Detayları - ${data.repair.takip_kodu}</title>
                                    <style>
                                        body { font-family: Arial, sans-serif; margin: 20px; }
                                        .header { text-align: center; margin-bottom: 30px; }
                                        .section { margin-bottom: 20px; }
                                        .section-title { font-weight: bold; margin-bottom: 10px; }
                                        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                                        th { background-color: #f5f5f5; }
                                        .footer { margin-top: 30px; text-align: center; font-size: 12px; }
                                    </style>
                                </head>
                                <body>
                                    <div class="header">
                                        <h2>Onarım Detayları</h2>
                                        <p>Takip Kodu: ${data.repair.takip_kodu}</p>
                                        <p>Tarih: ${data.repair.tarih}</p>
                                    </div>
                                    
                                    <div class="section">
                                        <div class="section-title">Müşteri Bilgileri</div>
                                        <p>Müşteri: ${data.repair.musteri}</p>
                                        <p>Telefon: ${data.repair.tel}</p>
                                        <p>E-posta: ${data.repair.mail}</p>
                                        <p>Adres: ${data.repair.adres}</p>
                                    </div>
                                    
                                    <div class="section">
                                        <div class="section-title">Ürün Bilgileri</div>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Ürün Kodu</th>
                                                    <th>Seri No</th>
                                                    <th>Adet</th>
                                                    <th>Durum</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${data.products.map(product => `
                                                    <tr>
                                                        <td>${product.urun_kodu}</td>
                                                        <td>${product.seri_no || '-'}</td>
                                                        <td>${product.adet}</td>
                                                        <td>${product.durum}</td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="section">
                                        <div class="section-title">Açıklama</div>
                                        <p>${data.repair.aciklama}</p>
                                    </div>
                                    
                                    <div class="footer">
                                        <p>Bu belge ${new Date().toLocaleString('tr-TR')} tarihinde oluşturulmuştur.</p>
                                    </div>
                                </body>
                                </html>
                            `);
                            
                            // Print the window
                            printWindow.document.close();
                            printWindow.focus();
                            setTimeout(() => {
                                printWindow.print();
                                printWindow.close();
                            }, 500);
                        } else {
                            printWindow.document.write('<div style="color: red; text-align: center; margin-top: 50px;">Onarım detayları alınamadı.</div>');
                        }
                    } catch (e) {
                        printWindow.document.write('<div style="color: red; text-align: center; margin-top: 50px;">Bir hata oluştu.</div>');
                    }
                },
                error: function() {
                    printWindow.document.write('<div style="color: red; text-align: center; margin-top: 50px;">Sunucu hatası oluştu.</div>');
                }
            });
        });
    });
</script>
