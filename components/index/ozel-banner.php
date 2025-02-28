<?php
require_once '../../functions/db.php'; // Veritabanı bağlantınızı içeren dosya
$db = new Database();
// Önbellek kontrolü
$cache_file = 'ozel_banner_cache.html';
$cache_time = 3600; // 1 saat

if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
    // Önbellekteki içeriği kullan
    readfile($cache_file);
    exit;
}

ob_start(); // Çıktı tamponlamayı başlat
?>
<!--OZEL BANNER ALANI-->
<div class="mt-1" >
    <?php
        $bnr = $db->fetch("SELECT * FROM banner_modal WHERE aktif = 1");
        $id = $bnr["id"];

        // id'ye göre hangi tasarımın geleceğini belirle
        switch ($id) {
            case 1:
                $htmlbnr = '
                        <div class="row p-2">
                            <div class="col-12 col-md-10 rounded-3 p-2">
                                    <a href="' . $bnr['link1'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/' . $bnr['foto1'] . '" style="width: 100%" alt=""></a>
                            </div>
                            <div class="col-12 col-md-2 rounded-3 p-2">
                                    <a href="' . $bnr['link2'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/'. $bnr['foto2'] . '" style="width: 100%" alt=""></a>
                            </div>
                        </div>';
                echo $htmlbnr; // echo buraya taşındı
                break;
            case 2:
                $htmlbnr = '
                        <div class="row p-2">
                            <div class="col-12 col-md-2 rounded-3 p-2">
                                    <a href="' . $bnr['link1'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/' . $bnr['foto1'] . '" style="width: 100%" alt=""></a>
                            </div>
                            <div class="col-12 col-md-10 rounded-3 p-2">
                                    <a href="' . $bnr['link2'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/'. $bnr['foto2'] . '" style="width: 100%" alt=""></a>
                            </div>
                        </div>';
                echo $htmlbnr; // echo buraya taşındı
                break;
            case 3:
                $htmlbnr = '
                        <div class="row p-2">
                            <div class="col-12 col-md-6 rounded-3 p-2">
                                    <a href="' . $bnr['link1'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/' . $bnr['foto1'] . '" style="width: 100%" alt=""></a>
                            </div>
                            <div class="col-6 col-md-3 rounded-3 p-2">
                                    <a href="' . $bnr['link2'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/'. $bnr['foto2'] . '" style="width: 100%" alt=""></a>
                            </div>
                            <div class="col-6 col-md-3 rounded-3 p-2">
                                    <a href="' . $bnr['link3'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/'. $bnr['foto3'] . '" style="width: 100%" alt=""></a>
                            </div>
                        </div>';
                echo $htmlbnr; // echo buraya taşındı
                break;
            case 4:
                $htmlbnr = '
                        <div class="row p-2">
                            <div class="col-6 col-md-3 rounded-3 p-2">
                                    <a href="' . $bnr['link1'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/' . $bnr['foto1'] . '" style="width: 100%" alt=""></a>
                            </div>
                            <div class="col-6 col-md-3 rounded-3 p-2">
                                    <a href="' . $bnr['link2'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/'. $bnr['foto2'] . '" style="width: 100%" alt=""></a>
                            </div>
                            <div class="col-12 col-md-6 rounded-3 p-2">
                                    <a href="' . $bnr['link3'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/'. $bnr['foto3'] . '" style="width: 100%" alt=""></a>
                            </div>
                        </div>';
                echo $htmlbnr; // echo buraya taşındı
                break;
            case 5:
                $htmlbnr = '
                        <div class="row p-2">
                            <div class="col-12 col-md-3 rounded-3 p-2">
                                    <a href="' . $bnr['link1'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/' . $bnr['foto1'] . '" style="width: 100%" alt=""></a>
                            </div>
                            <div class="col-12 col-md-6 rounded-3 p-2">
                                    <a href="' . $bnr['link2'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/'. $bnr['foto2'] . '" style="width: 100%" alt=""></a>
                            </div>
                            <div class="col-12 col-md-3 rounded-3 p-2">
                                    <a href="' . $bnr['link3'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/'. $bnr['foto3'] . '" style="width: 100%" alt=""></a>
                            </div>
                        </div>';
                echo $htmlbnr; // echo buraya taşındı
                break;
            case 6:
                $htmlbnr = '
                        <div class="row p-2">
                            <div class="col-12 col-md-4 rounded-3 p-2">
                                    <a href="' . $bnr['link1'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/' . $bnr['foto1'] . '" style="width: 100%" alt=""></a>
                            </div>
                            <div class="col-12 col-md-4 rounded-3 p-2">
                                    <a href="' . $bnr['link2'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/'. $bnr['foto2'] . '" style="width: 100%" alt=""></a>
                            </div>
                            <div class="col-12 col-md-4 rounded-3 p-2">
                                    <a href="' . $bnr['link3'] . '"><img class="rounded-3" src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/banner/'. $bnr['foto3'] . '" style="width: 100%" alt=""></a>
                            </div>
                        </div>';
                echo $htmlbnr; // echo buraya taşındı
                break;
            default:
                // Varsayılan bir tasarım belirlemek isterseniz buraya yazabilirsiniz
                break;
        }
    ?>
</div>
<!--OZEL BANNER ALANI SONU-->
<?php
$output = ob_get_clean(); // Tamponu al ve temizle

// Çıktıyı önbelleğe kaydet
file_put_contents($cache_file, $output);

// Çıktıyı göster
echo $output;
?>