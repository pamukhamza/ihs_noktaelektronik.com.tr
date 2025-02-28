<?php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'kuponlarim';
$template = new Template('Nokta B2B - Kuponlarım', $currentPage);

$template->head();
$database = new Database();

$uye_id = $_SESSION['id'];
?>
<style>
    .bi {vertical-align: -.125em;fill: currentColor;}
    .loading {
        /* Yükleme efekti için istediğiniz stil özelliklerini burada tanımlayabilirsiniz */
        opacity: 0.5; /* Örnek olarak, opaklık düşürme */
        pointer-events: none; /* Buton üzerinde tıklamayı geçici olarak devre dışı bırakma */
    }
    .loader {
        position: fixed;
        top: 50%;
        right: 50%;
        z-index: 10;
        width: 50px;
        padding: 8px;
        aspect-ratio: 1;
        border-radius: 50%;
        background: #25b09b;
        --_m:
                conic-gradient(#0000 10%,#000),
                linear-gradient(#000 0 0) content-box;
        -webkit-mask: var(--_m);
        mask: var(--_m);
        -webkit-mask-composite: source-out;
        mask-composite: subtract;
        animation: l3 1s infinite linear;
    }
    @keyframes l3 {to{transform: rotate(1turn)}}
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
        <li class="breadcrumb-item active" aria-current="page">Kuponlarım</li>
    </ol>
</nav>
<div class="loader" style="display: none"></div>
<div class="container">
    <div class="row">
        <?php $template->leftMenuProfile(); ?>
        <div class="float-end col-xs-12 col-sm-12 col-md-9">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 ">
                    <article class="blog-post border rounded-3 px-3 " style="background-color: white;">
                        <div class="row d-flex justify-content-between">
                            <div class="col-8">
                                <h5 class="link-body-emphasis my-3">Kuponlarım</h5>
                            </div>
                            <div class="col-4 mt-3 mb-2 d-flex justify-content-end">
                                <a id="sepetbutton" class="btn btn-primary">Sepete Git</a>
                            </div>
                            <hr style="margin: 0; padding: 0;" class="text-muted">
                        </div>
                        <div class="row mt-2">
                            <?php
                                $query = "SELECT * FROM b2b_promosyon WHERE 1";
                                $param = "";
                                if (isset($uye_id) && trim($uye_id) !== '') {
                                    // If kullanacak_uye_id is NULL, include all rows (no additional condition)
                                    // If kullanacak_uye_id is a single ID, match that specific ID
                                    // If kullanacak_uye_id contains multiple IDs, match any of those IDs
                                    $query .= " AND (kullanacak_uye_id IS NULL OR kullanacak_uye_id = :uye_id OR FIND_IN_SET(:uye_id, kullanacak_uye_id) > 0)";
                                    $query .= "AND aktif = 1";
                                    $param = ['uye_id' => $uye_id] ;
                                }
                                $d = $database->fetchAll($query, $param);
                                foreach ($d as $k => $row) {
                            ?>
                            <div class="card col-lg-4 col-xs-12 col-sm-12 col-md-4 shadow-sm p-0 m-2" style="border:solid 1px #c2bcc6">
                                <div class="card-body">
                                    <h6 class=""><?= $row["aciklama"] ?></h6>
                                    <small class="card-subtitle my-1 text-muted">Geçerlilik Tarihi: <span style="color:#0a90eb"><?= $row["gecerlilik_tarihi"] ?></span></small>
                                    <hr style="margin: 0; padding: 0; border-style: dashed">
                                    <div class="row">
                                        <div class="col-12"><small class="card-text m-0">İndirim Bilgileri: <span style="color:#0a90eb; font-weight: bold"><?= $row["tutar"] ?> TL</span></small></div>
                                        <div class="col-6"><small class="card-text m-0">Kod: <span style="color:#0a90eb; font-weight: bold"><?= $row["promosyon_kodu"] ?></span></small></div>
                                        <div class="col-6 text-align-right"><small><a></a></small></div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $template->footer(); ?>
</body>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/app.js"></script>
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
        $('#sepetbutton').click(function() {
            $('#sepetbutton').addClass('loading');
            $('.loader').fadeIn();
            setTimeout(function() {
                $('#sepetbutton').removeClass('loading');
                $('.loader').fadeOut();
                window.location.href = "tr/sepet";
            }, 1000); // 4000 milliseconds = 4 seconds

            // Prevent default form submission behavior
            return false;
        });
    });
</script>