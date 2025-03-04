<?php
//indirmeler.php
require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'indirmeler';
$template = new Template('Nokta - İndirmeler', $currentPage);

$template->head();
$database = new Database();
?>
<style>
      .bi {vertical-align: -.125em;fill: currentColor;}
</style>
<body>
    <?php $template->header(); ?>
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
            <li class="breadcrumb-item active" aria-current="page">İndirmeler</li>
        </ol>
    </nav>
    <section class="container mb-5">
        <div class="row">
            <!-- Sol Menü -->
            <?php $template->pageLeftMenu(); ?>
            <div class="float-end col-xs-12 col-sm-8 col-md-9 rounded-3 ">
                <div class="row">
                    <div class="col-xs-12 col-sm-10 col-md-10">
                        <div class="input-group ms-2 ps-2 rounded-3 ">
                            <input type="text" class="form-control rounded-start-pill ps-4" id="stokArama" onkeyup="livestock(this.value)" placeholder="Ürün indirmeleri ara" style="background-color: white; border-color: #fc9803; outline: none;">
                            <button class="btn btn-outline-secondary bg-turuncu rounded-end-circle" type="submit" style="color:white; border-color: transparent">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="dropdown-menu ms-2 ps-2 pe-2 rounded-5" id="liveStockResults" style="border-color: #fc9803;"></div>
                    </div>
                </div>
                <div class="row">
                    <?php if (isset($_GET['urun'])) {
                        $urunId = $_GET['urun'];
                        $urunData = $database->fetch("SELECT UrunAdiTR, UrunKodu FROM nokta_urunler WHERE `id` = :id" , ['id' => $urunId]); 
                    ?>
                        <div class="col-xs-12 col-sm-10 col-md-10 mt-5 ms-2">
                            <div class="card">
                                <div class="card-header font-weight-bold"><?=$urunData['UrunAdiTR']?> - <?=$urunData['UrunKodu']?></div>
                                <div class="card-body">
                                    <div class="table-responsive" id="employee_table">
                                        <?php
                                        $yuklemeBasliklari = $database->fetchAll("SELECT * FROM nokta_yuklemeler WHERE `is_active` = 1");
                                        foreach ($yuklemeBasliklari as $baslik) {
                                            $yuklemeID = $baslik['id'];
                                            $baslikAdi = $baslik['baslik'];

                                            $yuklemeler = $database->fetchAll("SELECT * FROM nokta_urunler_yuklemeler WHERE urun_id = $urunId AND yukleme_id = $yuklemeID"); 
                                            if (!empty($yuklemeler)) {
                                        ?>
                                                <table class="table table-striped table-bordered second" style="width:100%">
                                                    <thead class="bg-light">
                                                    <h4 class="text-center "><?php echo $baslikAdi; ?></h4>
                                                    <tr class="border-0">
                                                        <th class="border-0">ID</th>
                                                        <th class="border-0">Tarih</th>
                                                        <th class="border-0">Version</th>
                                                        <th class="border-0">Açıklama</th>
                                                        <th class="border-0">Dosya</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    foreach ($yuklemeler as $row) {
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $row['id']; ?></td>
                                                            <td><?php echo $row['datetime']; ?></td>
                                                            <td><?php echo $row['version']; ?></td>
                                                            <td><?php echo $row['aciklama']; ?></td>
                                                            <?php
                                                            $dYol = $row["url_path"];
                                                            $dUzanti = pathinfo($dYol, PATHINFO_EXTENSION);
                                                            $dUrunAdi = duzenleString1($urunData['UrunKodu']);
                                                            $dBaslik = duzenleString1($baslikAdi);
                                                            ?>
                                                            <td>
                                                                <a href="javascript:void(0);" onclick="downloadFile('https://www.noktaelektronik.com.tr/assets<?php echo $row["url_path"]; ?>', '<?= $dUrunAdi ?>-<?=$dBaslik?>-<?= $row['id'] ?>.<?= $dUzanti?>')">
                                                                    İndir
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div> 
        </div>
    </section>
      <div style="clear:both"></div>
      <?php $template->footer(); ?>
</body>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/alert.js"></script>
<script>
    function livestock(searchQuery) {
        if (searchQuery.length >= 3) {
            $.ajax({
                url: 'functions/urunler/searchStock.php',
                method: 'POST',
                data: { searchQuery: searchQuery },
                success: function(response) {
                    console.log(response);
                    var results = JSON.parse(response);
                    var dropdownMenu1 = $('#liveStockResults');
                    dropdownMenu1.empty(); // Önceki sonuçları temizle
                    if (results.length > 0) {
                        results.forEach(function(result) {
                            dropdownMenu1.append(`
                                    <a class="dropdown-item rounded-5" href="tr/indirmeler?urun=${result.id}">
                                        <img src="assets/images/urunler/${result.KResim}" alt="" style="max-width: 50px; margin-right: 10px;"> ${result.UrunAdiTR} - ${result.UrunKodu}
                                    </a>
                                `);
                        });
                        dropdownMenu1.show(); // Sonuçları göster
                    } else {
                        dropdownMenu1.hide(); // Sonuç yoksa gizle
                    }
                }
            });
        } else {
            $('#liveStockResults').empty().hide(); // 3 harften az ise sonuçları temizle ve gizle
        }
    }
</script>
<script>
    function downloadFile(url, newFilename) {
        var xhr = new XMLHttpRequest();
        xhr.responseType = 'blob';
        xhr.onload = function() {
            var a = document.createElement('a');
            a.href = window.URL.createObjectURL(xhr.response);
            a.download = newFilename;
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        };
        xhr.open('GET', url);
        xhr.send();
    }
</script>
