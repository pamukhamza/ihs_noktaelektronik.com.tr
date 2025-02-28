<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'siparişler';
$template = new Template('Nokta B2B - Siparişler', $currentPage);

$template->head();
$database = new Database();
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
            <li class="breadcrumb-item active" aria-current="page">Siparişlerim</li>
        </ol>
    </nav>
    <div class="container">
        <div class="row">
            <?php $template->leftMenuProfile(); ?>
            <div class="float-end col-xs-12 col-sm-12 col-md-9">
                <div class="row">
                    <div class="col-12">
                        <div class="table table-responsive">
                            <table class="table table-striped table-hover" style="width: 100%; border-color:#e1e1e1 ">
                                <thead class="">
                                    <td class="p-2 text-center border fs-10">Sipariş No</td>
                                    <td class="p-2 text-center border fs-10">Ödeme Şekli / Durum</td>
                                    <td class="p-2 text-center border fs-10">Kargo</td>
                                    <td class="p-2 text-center border fs-10">Durum</td>
                                    <td class="p-2 text-center border fs-10">Sipariş Tarihi</td>
                                    <td class="p-2 text-center border fs-10">#</td>
                                </thead>
                                <tbody class="table-group-divider">
                                <?php
                                $session_id = $_SESSION['id'];
                                $sepet = $database->fetchAll("
                                        SELECT s.id AS sipar_id, s.odeme_sekli, s.kargo_firmasi, s.siparis_no, s.tarih , s.durum, u.*
                                        FROM b2b_siparisler AS s
                                        JOIN b2b_siparis_durum AS u ON s.durum = u.id
                                        WHERE s.uye_id = :session_id
                                        ", ['session_id' => $session_id]);
                                foreach($sepet as $row){
                                ?>
                                    <tr class="border">
                                        <td class="p-2 text-center border fs-10"><?= $row["siparis_no"] ?></td>
                                        <td class="p-2 text-center border fs-10"><?= $row["odeme_sekli"] ?></td>
                                        <td class="p-2 text-center border fs-10"><?= $row["kargo_firmasi"] ?></td>
                                        <td class="p-2 text-center border fs-10"><?= $row["durum"] ?></td>
                                        <td class="p-2 text-center border fs-10"><?= $row["tarih"] ?></td>
                                        <td class="p-2 text-center border fs-10" style="background-color: grey;">
                                            <a href="tr/siparis-detay?s_id=<?php echo $row['sipar_id']; ?>" class="px-3">
                                                <i class="fa-solid fa-clipboard-list fa-lg" style="color: white;" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Sipariş Detayı Görüntüle!"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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