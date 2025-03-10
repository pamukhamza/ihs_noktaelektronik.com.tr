<?php
require '../functions/admin_template.php';
require '../functions/functions.php';
ini_set('display_errors', 1);  // Hataları ekrana göster
error_reporting(E_ALL);  
$currentPage = 'urunler';
$template = new Template('Nokta - Ürünler', $currentPage);

$template->head();
$database = new Database();

$kategori = $_GET['cat'] ?? '';
$cat = $kategori;
$marka = $_GET['brand'] ?? '';
$brand = $marka;
$ozellikler = $_GET['filter'] ?? '';
$filter = $ozellikler;
$arama = $_GET['search'] ?? '';
$search = $arama;
$stkParam = isset($_GET['stk']) ? '&stk=' . $_GET['stk'] : '';

$sql = "SELECT u.*, m.title AS marka_adi, r.KResim
        FROM nokta_urunler u
        LEFT JOIN nokta_urun_markalar m ON u.MarkaID = m.id 
        LEFT JOIN (
            SELECT UrunID, MIN(KResim) AS KResim
            FROM nokta_urunler_resimler
            WHERE sira = 1
            GROUP BY UrunID
        ) r ON u.id = r.UrunID
        WHERE u.web_comtr = 1";

$params = [];

if (!empty($kategori)) {
    // Kategorinin ID'sini al
    $kategori_id = $database->fetchColumn("SELECT id FROM nokta_kategoriler WHERE web_comtr = 1 AND seo_link = :seoLink", ['seoLink' => $kategori]);

    // Alt kategorileri bulmak için recursive bir fonksiyon
    function getAltKategoriler($database, $kategori_id) {
        $alt_kategori_ids = [];
        $sql = "SELECT id FROM nokta_kategoriler WHERE parent_id = :kategori_id";
        $alt_kategoriler = $database->fetchAll($sql, ['kategori_id' => $kategori_id]);

        foreach ($alt_kategoriler as $alt_kategori) {
            $alt_kategori_ids[] = $alt_kategori['id'];
            // Alt kategorilerin altındaki kategorilere de bak
            $alt_kategori_ids = array_merge($alt_kategori_ids, getAltKategoriler($database, $alt_kategori['id']));
        }
        return $alt_kategori_ids;
    }

    // Alt kategori ID'lerini al
    $alt_kategori_ids = getAltKategoriler($database, $kategori_id);
    $alt_kategori_ids[] = $kategori_id; // Ana kategori de eklenmeli

    // SQL sorgusuna dahil et
    $sql .= " AND KategoriID IN (" . implode(',', array_map('intval', $alt_kategori_ids)) . ")";
}

if (!empty($marka)) {
    $marka = !empty($marka) ? explode(',', $marka) : [];
    $marka_ids = [];
    foreach ($marka as $marka_seo) {
        $marka_id = $database->fetchColumn("SELECT id FROM nokta_urun_markalar WHERE web_comtr = 1 AND seo_link = :seoLink",['seoLink' => trim($marka_seo)]);
        if ($marka_id) {
            $marka_ids[] = $marka_id;
        }
    }
    if (!empty($marka_ids)) {
        $sql .= " AND u.MarkaID IN (" . implode(',', array_map('intval', $marka_ids)) . ")";
    }
}

if (!empty($ozellikler)) {
    $ozellikler_array = explode(',', $ozellikler);
    $ozellik_conditions = [];
    foreach ($ozellikler_array as $ozellik_id) {
        $ozellik_conditions[] = "FIND_IN_SET(:ozellik_$ozellik_id, filtre)";
        $params["ozellik_$ozellik_id"] = $ozellik_id;
    }
    $sql .= " AND (" . implode(' OR ', $ozellik_conditions) . ")";
}

if (!empty($arama)) {
    $sql .= " AND (UrunKodu LIKE :arama OR UrunAdiTR LIKE :arama OR m.title LIKE :arama)";
    $params['arama'] = "%$arama%";
}

if (isset($_GET['stk']) && $_GET['stk'] === '1') {
    $sql .= " AND stok REGEXP '^-?[0-9]+$' AND CAST(stok AS SIGNED) > 0";
}

$sortOptions = [
    'fasc' => 'DSF4 ASC',
    'fdesc' => 'DSF4 DESC',
    'gdesc' => 'cok_goren DESC',
    'hdesc' => 'cok_satan DESC',
    'ydesc' => 'id DESC'
];
$sort = $_GET['sort'] ?? '';
$sql .= isset($sortOptions[$sort]) ? " ORDER BY " . $sortOptions[$sort] : " ORDER BY u.sira ASC";

$limit = 20;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$countSql = "SELECT COUNT(*) FROM ($sql) AS count_table";
$totalRecords = $database->fetchColumn($countSql, $params);
$totalPages = ceil($totalRecords / $limit);

$sql .= " LIMIT :start, :limit";
$params['start'] = $start;
$params['limit'] = $limit;

$result = $database->fetchAll($sql, $params);

$breadcrumbs = getBreadcrumbs($kategori, $database);

function getBreadcrumbs($kategori, $database) {
    $breadcrumbs = [];
    $categoryData = $database->fetch("SELECT * FROM nokta_kategoriler WHERE seo_link = :seoLink", ['seoLink' => $kategori]);
    while ($categoryData) {
        $categoryName =  $categoryData['KategoriAdiTr'] ;
        $breadcrumbs[] = ['id' => $categoryData['seo_link'], 'name' => $categoryName];
        if ($categoryData['parent_id'] != 0) {
            $categoryData = $database->fetch("SELECT * FROM nokta_kategoriler WHERE id = :parentId", ['parentId' => $categoryData['parent_id']]);
        } else {
            break;
        }
    }
    return array_reverse($breadcrumbs);
}
?>
<style>
    .filtre-btn{font-size: 14px;}
    .filtre-btn:hover {background-color: white;border: solid 1px;}
    .hızlı-teslimat{
        font-size:13px;
        color:white;
        background: rgba(51, 170, 51, .6);
        padding:2px;
        position: absolute;
        top: 10px;
        left: 10px;
    }
    .favori-style{position: absolute;top: 28px;right: 10px;}
    .sepet-style{
        cursor:pointer;
        position: absolute;
        bottom: 30px;
        right: 20px;
    }
    .buton-style{
        position: absolute;
        bottom: 20px;
        background-color: #FC9803;
        color:white;
        width: 203px;
        height: 38px;
    }
    .teklifiste-style{
        position: absolute;
        bottom: 20px;
        width: 203px;
        height: 38px;
    }
    .urun-a{text-decoration: none;color:black;font-size:14px;}
    .favori-icon:hover{cursor: pointer;}
    .kategori-effect li{transition: transform 0.3s ease;}
    .kategori-effect li:hover{transform: translateX(8px);color:purple;}
    ::-webkit-scrollbar {width: 7px;}
    ::-webkit-scrollbar-track {background: #f1f1f1;}/* Track */
    ::-webkit-scrollbar-thumb {background: #888;}/* Handle */
    ::-webkit-scrollbar-thumb:hover {background: #555;}/* Handle on hover */
    .bi {vertical-align: -.125em;fill: currentColor;}
    .form-check{margin: 5px;}
    .urunler:hover{box-shadow: 0px 0px 10px #888888;}
    .custom-underline {text-decoration: line-through;}
    @media (min-width: 992px) {
        .mobile-menu{display: none;}
        .urunler-desktop{float:right; width: 75%;}
        .deskop-menu{float:left; width: 25%;}
        .urun-card{width: 32%;}
    }
    @media (max-width: 992px) {
        .mobile-menu{display: block;}
        .deskop-menu{display: none; }
        .urunler-desktop{width: 100%;}
        .urun-card{width: 47%;}
    }
    @media (min-width: 992px) and (max-width:1200px) {
        .urun-card{width: 32%;}
    }
    @media (min-width:1200px) {
        .urun-card{width: 24%;}
    }
</style>
<body>
    <input type="hidden" value="<?= $kategori;?>" id="get_kategori_seo">
<?php $template->header(); ?>
<!-- Site Haritası -->
<nav aria-label="breadcrumb" class="container mt-3 mb-2">
    <svg xmlns="http://www.w3.org/2800/svg" style="display: none;"><symbol id="house-door-fill" viewBox="0 0 16 16">
            <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/></symbol>
    </svg>
    <ol class="breadcrumb ">
        <li class="breadcrumb-item">
            <a class="link-body-emphasis" href="index">
                <svg class="bi" width="15" height="15"><use xlink:href="#house-door-fill"></use></svg><span class="visually-hidden">Anasayfa</span>
            </a>
        </li>
        <li class="breadcrumb-item">
            <a class="link-body-emphasis text-decoration-none" href="tr/urunler?cat=&brand=&filter=&search=">Ürünler</a>
        </li>
        <?php
        foreach ($breadcrumbs as $index => $breadcrumb) {
            echo '<li class="breadcrumb-item active">';
            if ($index < count($breadcrumbs) - 1) {
                echo '<a style="color:black; text-decoration:none;" href="tr/urunler?cat=' . $breadcrumb['id'] . '&brand='.$_GET['brand'].'&filter='.$_GET['filter'].'&search='.$arama.'">' . $breadcrumb['name'] . '</a>';
            } else {
                echo $breadcrumb['name'];
            }
            echo '</li>';
        }
        ?>
    </ol>
</nav>
<section class="container">
    <!-- Sol Menü -->
    <div class="row">
        <div class="mb-5 deskop-menu" style="height:100%">
            <!-- Kategoriler -->
            <div class="border shadow-sm p-3" style="background-color: #ffffff;">
                <h5 class="border-bottom p-2">Kategori</h5>
                <ul class="list-unstyled ps-0 kategori-effect">
                    <?php
                        if (empty($cat)) {
                            if (empty($brand)) {
                                // Kategori yok, marka yok
                                $kategori_sql = "SELECT * FROM nokta_kategoriler WHERE web_comtr = 1 AND parent_id = 0 ORDER BY sira";
                                $kategori_result = $database->fetchAll($kategori_sql);
                                foreach ($kategori_result as $kategori_row) {
                                    $kategori_id = $kategori_row['id'];
                                    $kategori_adi = $kategori_row['KategoriAdiTr'];
                                    $kategori_seo_link = $kategori_row['seo_link'];
                                    ?>
                                    <li>
                                        <a href="tr/urunler?cat=<?= $kategori_seo_link ?>&brand=<?= $brand ?>&filter=<?= $filter ?>&search=<?= $search ?>" 
                                           class="btn d-inline-flex align-items-center rounded border-0 collapsed" 
                                           style="text-align: left !important;">
                                            <?= htmlspecialchars($kategori_adi) ?>
                                        </a>
                                    </li>
                                <?php }
                            } else {
                                $eklenen_kategoriler = [];

                                // Marka id'sini almak için nokta_urun_markalar tablosunda seo_link ile arama
                                $marka_sql = "SELECT id FROM nokta_urun_markalar WHERE seo_link = :seo_link";
                                $marka_row = $database->fetch($marka_sql, ['seo_link' => $brand]);

                                if ($marka_row) {
                                    $marka_id = $marka_row['id'];

                                    // category_brand_rel tablosundan kat_id'leri çekiyoruz
                                    $kategori_sql = "SELECT kat_id FROM category_brand_rel WHERE marka_id = :marka_id";
                                    $kategori_rows = $database->fetchAll($kategori_sql, ['marka_id' => $marka_id]);

                                    foreach ($kategori_rows as $kategori_row) {
                                        $kategoriID = $kategori_row['kat_id'];
                                        $en_ust_kategori_id = $kategoriID;

                                        // Kategorileri üst kategorilere kadar çıkıyoruz
                                        while ($en_ust_kategori_id != 0) {
                                            $ust_kategori_sql = "SELECT * FROM nokta_kategoriler WHERE web_comtr = 1 AND id = :id";
                                            $ust_kategori_row = $database->fetch($ust_kategori_sql, ['id' => $en_ust_kategori_id]);

                                            if ($ust_kategori_row) {
                                                $en_ust_kategori_id = $ust_kategori_row['parent_id'];
                                                $kategori_adi = $ust_kategori_row['KategoriAdiTr'];
                                                $kategori_seo_link = $ust_kategori_row['seo_link'];
                                            } else {
                                                break;
                                            }
                                        }

                                        // Kategori daha önce eklenmediyse ekle
                                        if (!in_array($kategori_adi, $eklenen_kategoriler)) {
                                            ?>
                                            <li>
                                                <a href="tr/urunler?cat=<?= $kategori_seo_link ?>&brand=<?= $brand ?>&filter=<?= $filter ?>&search=<?= $search ?>"
                                                class="btn d-inline-flex align-items-center rounded border-0 collapsed"
                                                style="text-align: left !important;">
                                                    <?= htmlspecialchars($kategori_adi) ?>
                                                </a>
                                            </li>
                                            <?php
                                            $eklenen_kategoriler[] = $kategori_adi;
                                        }
                                    }
                                }
                            }
                        } else {
                          // Seçili kategori bilgilerini al
                            $kategori_sql = "SELECT * FROM nokta_kategoriler WHERE web_comtr = 1 AND seo_link = :cat";
                            $kategori = $database->fetch($kategori_sql, ['cat' => $cat]);
                            $kategori_id = $kategori['id'] ?? 0;
                            $selected_kategori_adi = $kategori['KategoriAdiTr'] ?? '';

                            // Alt kategorileri çek
                            $alt_kategori_sql = "SELECT * FROM nokta_kategoriler WHERE web_comtr = 1 AND parent_id = :parent_id ORDER BY sira";
                            $alt_kategori_result = $database->fetchAll($alt_kategori_sql, ['parent_id' => $kategori_id]);

                            // Eğer alt kategori bulunamazsa üst kategoriyi bul
                            if (!$alt_kategori_result) {
                                $ust_kategori_sql = "SELECT parent_id FROM nokta_kategoriler WHERE web_comtr = 1 AND id = :id";
                                $ust_kategori = $database->fetch($ust_kategori_sql, ['id' => $kategori_id]);
                                $ust_kategori_id = $ust_kategori['parent_id'] ?? 0;

                                // Üst kategorinin alt kategorilerini getir
                                $alt_kategori_sql = "SELECT * FROM nokta_kategoriler WHERE web_comtr = 1 AND parent_id = :parent_id ORDER BY sira";
                                $alt_kategori_result = $database->fetchAll($alt_kategori_sql, ['parent_id' => $ust_kategori_id]);
                            }

                            if ($alt_kategori_result) {
                                foreach ($alt_kategori_result as $alt_kategori) {
                                    // Seçili kategoriye özel stil
                                    $style = ($alt_kategori['seo_link'] === $cat) ? 'color: purple; font-weight: bold;' : '';
                                    ?>
                                    <li>
                                        <a href="tr/urunler?cat=<?= $alt_kategori['seo_link'] ?>&brand=<?= $brand ?>&filter=<?= $filter ?>&search=<?= $search ?>" 
                                        class="btn d-inline-flex align-items-center rounded border-0 collapsed" 
                                        style="text-align: left !important; <?= $style ?>">
                                            <?= htmlspecialchars($alt_kategori['KategoriAdiTr']) ?>
                                        </a>
                                    </li>
                                <?php }
                            } else {
                                echo '<li>Herhangi bir alt kategori bulunamadı.</li>';
                            }
                        } 
                    ?>
                </ul>
            </div>
            <!--Stok filterleme -->
            <?php $stkParam = $_GET['stk'] ?? null; // stk parametresini al, eğer yoksa null olarak ayarla

            // stk parametresi var ve değeri 1 ise, checked değeri true olacak, değilse false olacak
            $isChecked = $stkParam === '1' ? 'checked' : ''; ?>
            <div class=" border mt-3 shadow-sm p-3" style="background-color: #ffffff;">
                <h5 class="border-bottom p-2">Stok Durumu</h5>
                <ul class="list-unstyled ps-1">
                    <li class="">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="stokta_olanlar" name="stokta_olanlar" value="1" <?= $isChecked ?>>
                            <label class="form-check-label" for="stokta_olanlar">Stokta Olanlar</label>
                        </div>
                    </li>
                </ul>
            </div>
            <!--Markalar filterleme -->
            <div class=" border mt-3 shadow-sm p-3" style="background-color: #ffffff;">
                <h5 class="border-bottom p-2">Marka</h5>
                <ul class="list-unstyled ps-1" style="overflow-y: scroll; max-height:280px">
                    <?php
                        if (!empty($kategori)) {
                            $kategori_ids = $kategori_id ;
                            // category_brand_rel tablosundan ilgili kategoriye ait marka ID'lerini al
                            $marka_ids = $database->fetchAll("SELECT marka_id FROM category_brand_rel WHERE kat_id = :kategori_id", ['kategori_id' => $kategori_ids]);

                            // Marka ID'lerini array haline getir
                            $marka_id_list = array_column($marka_ids, 'marka_id');
                            $marka_id_str = implode(',', array_map('intval', $marka_id_list));

                            if (!empty($marka_id_str)) {
                                // nokta_urun_markalar tablosundan ilgili markaları al
                                $markalar_result = $database->fetchAll("SELECT title AS marka_adi, seo_link AS marka_seo FROM nokta_urun_markalar WHERE id IN ($marka_id_str) AND web_comtr = 1 ORDER BY marka_adi");
                            }
                        } else { // Kategori boş ise tüm markaları getir
                            $markalar_result = $database->fetchAll("SELECT title AS marka_adi, seo_link AS marka_seo FROM nokta_urun_markalar WHERE web_comtr = 1 ORDER BY marka_adi");
                        }
                        // Markaları checkbox olarak göster
                        if (!empty($markalar_result)) {
                            foreach ($markalar_result as $marka_row) {
                                $marka_adi = $marka_row['marka_adi'];
                                $marka_seo = $marka_row['marka_seo'];
                                $checked = '';
                                $selected_brands = !empty($_GET['brand']) ? explode(',', $_GET['brand']) : [];
                                if (in_array($marka_seo, $selected_brands)) {
                                    $checked = 'checked';
                                }
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input brand-checkbox" type="checkbox" id="marka-<?= $marka_adi; ?>" name="marka[]" value="<?= $marka_seo; ?>" <?= $checked; ?>>
                                    <label class="form-check-label" for="marka-<?= $marka_adi; ?>"><?= $marka_adi; ?></label>
                                </div> <?php
                            }
                        }
                    ?>
                </ul>
            </div>
            <!--Özellikler filterleme -->
            <?php
            // Filtreleri ve kategorileri çek
            $filtre_ids = array(); // Filtreler için boş bir dizi oluşturulur
            $kategori_ids = array(); // Kategori ID'leri için boş bir dizi oluşturulur
            foreach ($result as $rowff) {
                if (!empty($rowff['filtre'])) {
                    $filtre_ids = array_merge($filtre_ids, explode(',', $rowff['filtre'])); // Her üründeki filtreleri birleştir
                }
            }
            $filtre_ids = array_unique($filtre_ids); // Tekrar eden değerleri kaldır

            // Kategori ID'lerini almak için filtreleri kullan
            $kategori_ids = array();
            if (!empty($filtre_ids)) {
                $filtre_ids_str = implode(',', array_map('intval', $filtre_ids));
                $result_kategoriler = $database->fetchAll("SELECT DISTINCT kategori_id FROM filtreler WHERE id IN ($filtre_ids_str)");
                foreach ($result_kategoriler as $row_kategori) {
                    if (!empty($row_kategori['kategori_id'])) {
                        $kategori_ids = array_merge($kategori_ids, explode(',', $row_kategori['kategori_id']));
                    }
                }
                $kategori_ids = array_unique($kategori_ids);
            }
            // Kategoriler ve filtreler için hazırlık
            $kategoriler = array();
            foreach ($kategori_ids as $kategori_id) {
                $row_kategori = $database->fetch("SELECT KategoriAdiTr FROM filtre_kategoriler WHERE id = $kategori_id");
                if ($row_kategori) {
                    $kategoriler[$kategori_id] = $row_kategori['KategoriAdiTr'];
                }
            }
            // Her kategori için filtreleri listele
            ?>
            <div class="border mt-3 shadow-sm p-3" style="background-color: #ffffff; <?php if (empty($filtre_ids)) {echo "display: none;";} ?>">
                <?php foreach ($kategoriler as $kategori_id => $kategori_adi): ?>
                    <h5 class="border-bottom p-2"><?= htmlspecialchars($kategori_adi); ?></h5>
                    <ul class="list-unstyled ps-1" style="overflow-y: scroll; max-height:280px">
                        <?php
                        // Her kategori için filtreleri al
                        $result_filtreler = $database->fetchAll("SELECT id, filtre_adi FROM filtreler WHERE FIND_IN_SET($kategori_id, kategori_id) > 0 AND id IN (" . implode(',', array_map('intval', $filtre_ids)) . ")");
                        $selected_filters = !empty($_GET['filter']) ? explode(',', $_GET['filter']) : array();
                        foreach ($result_filtreler as $row_filtre) {
                            $filtre_id = $row_filtre['id'];
                            $filtre_adi = htmlspecialchars($row_filtre['filtre_adi']);
                            $checked1 = in_array($filtre_id, $selected_filters) ? 'checked' : '';
                            ?>
                            <div class="form-check">
                                <input class="form-check-input filter-checkbox" type="checkbox" id="filtre-<?= htmlspecialchars($filtre_id); ?>" name="filtre[]" value="<?= htmlspecialchars($filtre_id); ?>" <?= $checked1; ?>>
                                <label class="form-check-label" for="filtre-<?= htmlspecialchars($filtre_id); ?>"><?= $filtre_adi; ?></label>
                            </div>
                        <?php } ?>
                    </ul>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Ürün listeleme Bölümü -->
        <div class="urunler-desktop">
            <!-- SLIDER ALANI BAŞLANGIÇ -->
            <div class="slider-carousel">
                <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php
                        $d = $database->fetchAll("SELECT * FROM slider WHERE `site` = 'b2b' AND is_active = 1 ORDER BY order_by ASC");
                        $first = true; // Define a fla
                        foreach ($d as $k => $row) {
                            // Add the active class to the first item only
                            $activeClass = $first ? 'active' : '';
                            echo '<div class="carousel-item ' . $activeClass . '" data-bs-interval="3000">
                                        <a href="' . $row["link"] . '">
                                            <img src="https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/slider/' . $row["photo"] . '" class="d-block w-100"  height="auto" style="max-height: 300px;" alt="...">
                                        </a>
                                    </div>';
                            // Update the flag after marking the first item
                            $first = false;
                        }
                        ?>
                    </div>
                    <!-- Navigation dots -->
                    <div class="carousel-indicators">
                        <?php
                        // Generate navigation dots
                        for ($i = 0; $i < count($d); $i++) {
                            $activeDot = ($i == 0) ? 'active' : ''; // Add active class to the first dot
                            echo '<button type="button" data-bs-target="#carouselExample" data-bs-slide-to="' . $i . '" class="' . $activeDot . '" aria-label="Slide ' . ($i + 1) . '"></button>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <!-- SLIDER ALANI SONU -->
            <div class="row">
                <div class="col-2 mt-3">
                    <button class="btn btn-primary mobile-menu" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltre" aria-controls="offcanvasFiltre">
                        <a style="display: flex; align-items: center;">
                            <i class="fa-solid fa-filter" style="margin-right: 5px;"></i>
                            <span>Filtreler</span>
                        </a>
                    </button>
                    <div class="offcanvas offcanvas-start w-75" tabindex="-1" id="offcanvasFiltre" aria-labelledby="offcanvasFiltreLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasFiltreLabel">Filtreler</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <!-- Kategoriler -->
                            <div class="border shadow-sm" style="background-color: #ffffff;">
                                <h5 class="border p-2">Kategori</h5>
                                <ul class="list-unstyled ps-0 kategori-effect ">
                                    <?php
                                        function getKategoriLink($seo_link, $brand) {
                                            return "tr/urunler?cat=$seo_link&brand=" . htmlspecialchars($brand) . "&filter=&search=";
                                        }

                                        function fetchTopCategory($database, $kategoriID) {
                                            while ($kategoriID != 0) {
                                                $ust_kategori = $database->fetch("SELECT parent_id, KategoriAdiTr, seo_link FROM nokta_kategoriler WHERE web_comtr = 1 AND id = :id", ['id' => $kategoriID]);
                                                if ($ust_kategori) {
                                                    $kategoriID = $ust_kategori['parent_id'];
                                                } else {
                                                    break;
                                                }
                                            }
                                            return $ust_kategori ?? null;
                                        }
                                        $eklenen_kategoriler = [];

                                        if (empty($cat)) {
                                            $query = empty($brand) ? "SELECT * FROM nokta_kategoriler WHERE web_comtr = 1 AND parent_id = 0" : $result;
                                            $kategoriler = empty($brand) ? $database->fetchAll($query) : $result;

                                            foreach ($kategoriler as $row) {
                                                $kategori = empty($brand) ? $row : fetchTopCategory($database, $row['KategoriID']);
                                                if ($kategori && !in_array($kategori['KategoriAdiTr'], $eklenen_kategoriler)) {
                                                    echo "<li><a href='" . getKategoriLink($kategori['seo_link'], $brand) . "' class='btn d-inline-flex align-items-center rounded border-0 collapsed' style='text-align: left !important;'>" . htmlspecialchars($kategori['KategoriAdiTr']) . "</a></li>";
                                                    $eklenen_kategoriler[] = $kategori['KategoriAdiTr'];
                                                }
                                            }
                                        } else {
                                            $alt_kategoriler = $database->fetchAll("SELECT * FROM nokta_kategoriler WHERE web_comtr = 1 AND parent_id = 
                                                                (SELECT id FROM nokta_kategoriler WHERE seo_link = :seo_link)", ['seo_link' => $cat]);

                                            if ($alt_kategoriler) {
                                                foreach ($alt_kategoriler as $kategori) {
                                                    echo "<li><a href='" . getKategoriLink($kategori['seo_link'], $brand) . "' class='btn d-inline-flex align-items-center rounded border-0 collapsed' style='text-align: left !important;'>" . htmlspecialchars($kategori['KategoriAdiTr']) . "</a></li>";
                                                }
                                            } else {
                                                $parent = $database->fetch("SELECT parent_id, KategoriAdiTr FROM nokta_kategoriler WHERE seo_link = :seo_link", ['seo_link' => $cat]);
                                                $sibling_kategoriler = $database->fetchAll("SELECT * FROM nokta_kategoriler WHERE web_comtr = 1 AND parent_id = :parent_id", ['parent_id' => $parent['parent_id']]);
                                                
                                                foreach ($sibling_kategoriler as $kategori) {
                                                    $style = ($kategori['KategoriAdiTr'] === $parent['KategoriAdiTr']) ? 'transform: translateX(8px);color:purple;font-weight:bold;' : '';
                                                    echo "<li><a href='" . getKategoriLink($kategori['seo_link'], $brand) . "' class='btn d-inline-flex align-items-center rounded border-0 collapsed' style='text-align: left !important; $style'>" . htmlspecialchars($kategori['KategoriAdiTr']) . "</a></li>";
                                                }
                                            }
                                        }
                                        ?>
                                </ul>
                            </div>
                            <!--Markalar filterleme -->
                            <div class=" border mt-3 shadow-sm" style="background-color: #ffffff;">
                                <h5 class="border p-2">Marka</h5>
                                <ul class="list-unstyled ps-1" style="overflow-y: scroll; max-height:280px">
                                    <?php
                                    if (!empty($alt_kategori_ids_str)) {
                                        $markalar_result = $database -> fetchAll("SELECT DISTINCT m.title AS marka_adi ,m.seo_link AS marka_seo
                                            FROM nokta_urunler u
                                            LEFT JOIN nokta_urun_markalar m ON u.MarkaID = m.id
                                            WHERE u.KategoriID IN ($alt_kategori_ids_str)");
                                     
                                        while ($marka_row = mysqli_fetch_assoc($markalar_result)) {
                                            $marka_adi = $marka_row['marka_adi'];
                                            $marka_seo = $marka_row['marka_seo'];
                                            $checked = '';
                                            $selected_brands = !empty($_GET['brand']) ? explode(',', $_GET['brand']) : array();
                                            if (in_array($marka_seo, $selected_brands)) {
                                                $checked = 'checked';
                                            }
                                            ?>
                                            <div class="form-check">
                                                <input class="form-check-input brand-checkbox1" type="checkbox" id="marka-<?= $marka_adi; ?>" name="marka[]" value="<?= $marka_seo; ?>" <?= $checked; ?>>
                                                <label class="form-check-label" for="marka-<?= $marka_adi; ?>"><?= $marka_adi; ?></label>
                                            </div>
                                        <?php }
                                    }else{
                                        $markalar_result1 = $database -> fetchAll("SELECT * FROM nokta_urun_markalar WHERE web_comtr = 1");
                                        foreach ($markalar_result1 as $k => $marka_row) {
                                            $marka_adi = $marka_row['title'];
                                            $marka_seo = $marka_row['seo_link'];
                                            $checked = '';
                                            $selected_brands = !empty($_GET['brand']) ? explode(',', $_GET['brand']) : array();
                                            if (in_array($marka_seo, $selected_brands)) {
                                                $checked = 'checked';
                                            }
                                            ?>
                                            <div class="form-check">
                                                <input class="form-check-input brand-checkbox1" type="checkbox" id="marka-<?= $marka_adi; ?>" name="marka[]" value="<?= $marka_seo; ?>" <?= $checked; ?>>
                                                <label class="form-check-label" for="marka-<?= $marka_adi; ?>"><?= $marka_adi; ?></label>
                                            </div>
                                        <?php }
                                    }
                                    ?>
                                </ul>
                            </div>
                            <!--Özellikler filterleme -->
                            <?php
                            // Filtreleri çek
                            $filtre_ids = array(); // Filtreler için boş bir dizi oluşturulur
                            foreach ($result as $rowff) {
                                if (!empty($rowff['filtre'])) {
                                    $filtre_ids = array_merge($filtre_ids, explode(',', $rowff['filtre'])); // Her üründeki filtreleri birleştir
                                }
                            }
                            $filtre_ids = array_unique($filtre_ids); // Tekrar eden değerleri kaldır                            
                            ?>
                            <div class="border mt-3 mb-5" style="background-color: #ffffff; <?php if (empty($filtre_ids)) {echo "display: none;";} ?>">
                                <h5 class="border p-2">Filtre</h5>
                                <ul class="list-unstyled ps-1 " style="overflow-y: scroll; max-height:280px">
                                    <?php
                                    foreach ($filtre_ids as $filtre_id) {
                                        $row_filtre = $database ->fetch("SELECT DISTINCT filtre_adi FROM filtreler WHERE id = '$filtre_id'");
                                        if ($row_filtre) {
                                            $filtre_adi = $row_filtre['filtre_adi'];
                                            $selected_filters = !empty($_GET['filter']) ? explode(',', $_GET['filter']) : array();
                                            $checked1 = '';
                                            if (in_array($filtre_id, $selected_filters)) {
                                                $checked1 = 'checked';
                                            }
                                            ?>
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox1" type="checkbox" id="filtre-<?= $filtre_id; ?>" name="filtre[]" value="<?= $filtre_id; ?>" <?= $checked1; ?>>
                                                <label class="form-check-label" for="filtre-<?= $filtre_id; ?>"><?= $filtre_adi; ?></label>
                                            </div>
                                        <?php }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-10 mt-3 text-end">
                    <a class="btn filtre-btn" href="tr/urunler?<?= http_build_query(array_merge($_GET, ['sort' => 'fasc'])); ?>">Fiyata Göre Artan<i class="fa-solid fa-arrow-up ms-1"></i></a>
                    <a class="btn filtre-btn" href="tr/urunler?<?= http_build_query(array_merge($_GET, ['sort' => 'fdesc'])); ?>">Fiyata Göre Azalan<i class="fa-solid fa-arrow-down ms-1"></i></a>
                    <a class="btn filtre-btn" href="tr/urunler?<?= http_build_query(array_merge($_GET, ['sort' => 'hdesc'])); ?>">En Çok Satanlar<i class="fa-solid fa-fire ms-1 text-danger"></i></a>
                    <a class="btn filtre-btn" href="tr/urunler?<?= http_build_query(array_merge($_GET, ['sort' => 'gdesc'])); ?>">Çok Gezilenler<i class="fa-solid fa-eye ms-1 text-primary"></i></a>
                    <a class="btn filtre-btn" href="tr/urunler?<?= http_build_query(array_merge($_GET, ['sort' => 'ydesc'])); ?>">Yeni<i class="fa-solid fa-truck-fast ms-1" style="color: #ff3838;"></i></a>
                </div>
                <!--<div class="col-2 justify-content-end d-flex p-0 mt-3">
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle me-3" data-bs-toggle="dropdown" aria-expanded="false">Sıralama</button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="urunler?<?= http_build_query(array_merge($_GET, ['sort' => 'fasc'])); ?>">Fiyata Göre Artan</a></li>
                            <li><a class="dropdown-item" href="urunler?<?= http_build_query(array_merge($_GET, ['sort' => 'fdesc'])); ?>">Fiyata Göre Azalan</a></li>
                        </ul>
                    </div>
                </div>-->
            </div>
            <div class="row mt-2">
                <?php if ($result > 0) {
                    foreach ($result as $row) {
                        ?>
                        <div class="card urun-card rounded-0 shadow-sm p-0 mx-1 mt-1 mb-1">
                            <a href="tr/urunler/<?= $row['seo_link'] ; ?>">
                                <div class="w-100 d-flex align-items-center" style="height: 245px;overflow: hidden">
                                    <img src="<?= !empty($row['KResim']) ? 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/'.$row['KResim'] : 'https://noktanet.s3.eu-central-1.amazonaws.com/uploads/images/products/gorsel_hazirlaniyor.jpg'; ?>" class="card-img-top img-fluid">
                                </div>
                            </a>
                            <div class="card-body d-flex flex-column"><!--
                            <div class="mb-2 mt-auto" style="font-size: 12px;"><a href="" class="rounded-1 text-decoration-none" style="color:black; background:rgba(255, 40, 18, 0.4); padding:2px"><i class="fa-solid fa-tags"></i>Birlikte Al Kazan</a>
                            <a href="" class="rounded-1 text-decoration-none" style="color:black; background: rgba(0, 98, 255, 0.4); padding:2px"><i class="fa-solid fa-circle-play"></i>Videolu Ürün</a></div>-->
                                <a href="tr/urunler/<?= $row['seo_link'] ; ?>" style="font-weight:600; color:#555555;" class="mt-2 urun-a"><strong><?= (strlen($row['UrunAdiTR']) > 55) ? substr($row['UrunAdiTR'], 0, 54) . '...' : $row['UrunAdiTR'];?></strong></a>
                                <a style="font-size:12px; color:#0a90eb;" class="mt-2 border-bottom urun-a"><?= $row['marka_adi'] ; ?></a>
                                <a style="font-size:12px;" class=" urun-a">Stok Kodu:<span style="font-weight: bold"> <?= $row['UrunKodu'] ; ?></span></a>
                                <?php 
                                if($row['proje'] == 0){ ?>
                                    <?php if (isset($_SESSION['id'])) {
                                        $uye = $database->fetch("SELECT fiyat, satis_temsilcisi FROM uyeler WHERE id =:id", ['id' => $_SESSION['id']]);
                                        $uye_fiyat = $uye['fiyat'];
                                        $uye_satis_temsilci = $uye['satis_temsilcisi'];
                                        if(!empty($row["stok"])){
                                            if($uye_fiyat != 4){ ?>
                                                <a style="font-size:14px; color:#555555;" class="urun-a fw-bold">
                                                    <?= !empty($row["DSF4"]) ? $row["DOVIZ_BIRIMI"] : "₺";
                                                    $fiyat1 = !empty($row["DSF4"]) ? $row["DSF4"]: $row["KSF4"];
                                                    echo formatNumber($fiyat1);?> + KDV
                                                </a>
                                                <a style="font-size:14px; color:#0a90eb;" class="urun-a fw-bold mt-1">Size Özel Fiyat</a><?php 
                                            } ?>
                                                <a style="font-size:14px;color:#f29720;" class="urun-a fw-bold">
                                                    <?= !empty($row["DSF4"]) ? $row["DOVIZ_BIRIMI"] : "₺";
                                                    $fiyat = !empty($row["DSF".$uye_fiyat]) ? $row["DSF".$uye_fiyat] : $row["KSF".$uye_fiyat];
                                                    echo formatNumber($fiyat); ?> + KDV
                                                </a><?php 
                                            if(!empty($fiyat) && $row["stok"] > 0){ ?>
                                                <i class="fa-solid fa-cart-shopping fa-xl sepet-style"
                                                    onclick="<?php 
                                                    $urunId = $row['id'];
                                                    if (isset($_SESSION['id'])) {
                                                        echo "sepeteUrunEkle($urunId, " . (isset($_SESSION['id']) ? $_SESSION['id'] : 'default_value') . ");";
                                                    } else {
                                                        echo "window.location.href = 'tr/giris';";
                                                    }
                                                    ?>">
                                                </i><?php 
                                            }
                                        } else{ 
                                            $temsilci = $database->fetch("SELECT * FROM users WHERE id = :id" ,  ['id' => $uye_satis_temsilci]);
                                            if (!empty($uye_satis_temsilci)) { ?>
                                                <span class="mt-5"></span>
                                                <button class="btn buton-style" onclick="openTemsilciAlert()">
                                                    <i class="fa-solid fa-box-open me-1"></i><span style="font-size: 14px;">Stok Sorunuz</span>
                                                </button>
                                                <script>
                                                    function openTemsilciAlert() {
                                                        Swal.fire({
                                                            title: 'Satış Temsilciniz',
                                                            html: '<div style="text-align: left;">' +
                                                                '<p>Ad Soyad: <?= $temsilci['full_name']; ?> </p>' +
                                                                '<p>Mail:  <a href="mailto: <?= $temsilci['email']; ?>"><?= $temsilci['email']; ?></a></p>' +
                                                                '<p>Telefon Numarası: <?= $temsilci['tel']; ?></p>' +
                                                                '</div>',
                                                            confirmButtonText: 'Tamam',
                                                            customClass: {
                                                                popup: 'custom-popup-class',
                                                                title: 'custom-title-class',
                                                                htmlContainer: 'custom-html-container-class'
                                                            }
                                                        });
                                                    }
                                                </script><?php 
                                            } else { ?>
                                                <button class="btn buton-style" onclick="openTemsilciAlert()">
                                                    <i class="fa-solid fa-universal-access me-1"></i><span style="font-size: 14px;">Satış Temsilcinize Danışınız</span>
                                                </button>
                                                <script>
                                                    function openTemsilciAlert() {
                                                        Swal.fire({
                                                            title: 'İletişim Bilgileri',
                                                            html: '<div style="text-align: left;">' +
                                                                '<p>Mail:  <a href="mailto:destek@noktaelektronik.com.tr">destek@noktaelektronik.com.tr</a></p>' +
                                                                '<p>Telefon Numarası: 0850 333 02 08</p>' +
                                                                '</div>',
                                                            confirmButtonText: 'Tamam',
                                                            customClass: {
                                                                popup: 'custom-popup-class',
                                                                title: 'custom-title-class',
                                                                htmlContainer: 'custom-html-container-class'
                                                            }
                                                        });
                                                    }
                                                </script><?php 
                                            } 
                                        } 
                                    }else{ ?>
                                        <a style="font-size:14px; color:#f29720;" class="urun-a fw-bold">
                                            <?= !empty($row["DSF4"]) ? $row["DOVIZ_BIRIMI"] : "₺";
                                            $fiyat1 = !empty($row["DSF4"]) ? $row["DSF4"]: $row["KSF4"];
                                            echo formatNumber($fiyat1);?> + KDV
                                        </a><?php
                                    } 
                                } else{ ?>
                                    <span class="mt-5"></span>
                                    <button type="submit" class="btn btn-danger buton-style teklifOnaybtn" style="background-color: #DC3545;">
                                        <i class="fa-solid fa-reply fa-flip-horizontal"></i> Teklif İste
                                    </button><?php 
                                } ?>
                            </div>
                            <i class="fa-regular fa-heart fa-xl favori-icon favori-buton favori-style" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Favorilere Ekle!" data-product-id="<?= $row['id']; ?>"></i>
                        </div><?php 
                    } 
                } else { ?>
                    <div class="alert alert-danger text-center px-5" role="alert">Ürün Bulunamadı!</div><?php 
                } ?>
            </div>
            <div class="col-12 mt-3">
                <nav aria-label="Page navigation example">
                    <ul class=" pagination justify-content-center">
                        <?php
                        $startPage = max($page - 3, 1);
                        $endPage = min($page + 3, $totalPages);

                        if ($startPage > 1) {
                            echo '<li class="page-item"><a class="page-link" href="tr/urunler?'.http_build_query(array_merge($_GET, ['page' => 1])).'">1</a></li>';
                            if ($startPage > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                        }

                        for ($i = $startPage; $i <= $endPage; $i++) {
                            echo '<li class="page-item '.($i == $page ? 'active' : '').'"><a class="page-link" href="tr/urunler?'.http_build_query(array_merge($_GET, ['page' => $i])).'">'.$i.'</a></li>';
                        }

                        if ($endPage < $totalPages) {
                            if ($endPage < $totalPages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="tr/urunler?'.http_build_query(array_merge($_GET, ['page' => $totalPages])).'">'.$totalPages.'</a></li>';
                        }
                        ?>
                    </ul>

                </nav>
            </div>
        </div>
        <!-- Ürün listeleme Bölümü Sonu -->
    </div>
</section>
<div style="clear:both"></div>
<!-- TEKLIF MODAL -->
<div class="modal fade" data-bs-backdrop="static" id="teklifOnayModal" role="dialog" aria-labelledby="teklifOnayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="teklifOnayModalLabel">Teklif Formu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="applicationForm" method="post" class="needs-validation">
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <div class="col-sm-12 pb-2">
                                <p class="border-bottom pb-3">Teklifiniz ile ilgili detayları aşağıda açıklayarak bize iletebilirsiniz.</br> Teklifiniz en kısa sürede yanıtlanacaktır.</p>
                            </div>
                            <div class="col-sm-12">
                                <label for="email" class="form-label">E-Posta</label>
                                <input type="email" class="form-control" id="email" placeholder="mail@example.com" required>
                                <div class="invalid-feedback">Geçerli e-posta giriniz!</div>
                            </div>
                            <div class="col-sm-12">
                                <label for="teklif_nedeni" class="form-label">Açıklama</label>
                                <input type="text" id="uye_id" value="<?= $_SESSION["id"]; ?>" hidden>
                                <input type="text" id="urun_no" value="<?= $row['id']; ?>" hidden>
                                <textarea type="text" class="form-control" id="teklif_nedeni" required></textarea>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <button class="w-100 btn btn-primary teklifOnayDevambtn" id="teklifOnayBtn" type="submit">Devam Et</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $template->footer(); ?>
</body>
</html>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.7.0.min.js"></script>
<script src="assets/js/alert.js"></script>
<script src="assets/js/app.js"></script>
<script>
    $(document).ready(function() {
        $('.teklifOnaybtn').click(function() {
            $('#teklifOnayModal').modal('show');
        });
        $('#applicationForm').submit(function(e) {
            e.preventDefault();

            var uye_id = $('#uye_id').val();
            var teklif_nedeni = $('#teklif_nedeni').val();
            var urun_no = $('#urun_no').val();
            var email = $('#email').val();
            $.ajax({
                type: 'POST',
                url: 'functions/edit_info.php',
                data: {
                    uye_id: uye_id,
                    email: email,
                    teklif_nedeni: teklif_nedeni,
                    urun_no: urun_no,
                    type: 'teklif'
                },
                success: function() {
                    $('#teklifOnayModal').modal('hide');
                    Swal.fire({
                        title: "Teklifiniz Alınmıştır!",
                        icon: "success",
                        showConfirmButton: false
                    });
                },
                error: function(response) {
                    // Hata durumunda yapılacak işlemler
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function(){
        // Marka seçildiğinde
        $('.brand-checkbox').change(function() {
            updateFiltersInUrl();
        });

        // Filtre seçildiğinde
        $('.filter-checkbox').change(function() {
            updateFiltersInUrl();
        });

        // Stok durumu filtresi için
        var stokCheckbox = document.getElementById('stokta_olanlar');
        if (stokCheckbox) {
            stokCheckbox.addEventListener('change', function() {
                updateFiltersInUrl();
            });
        }

        function updateFiltersInUrl() {
            kategorisiymis = document.getElementById('get_kategori_seo').value
            var selectedBrands = [];
            $('.brand-checkbox:checked').each(function() {
                selectedBrands.push($(this).val());
            });

            var selectedFilters = [];
            $('.filter-checkbox:checked').each(function() {
                selectedFilters.push($(this).val());
            });

            var brandParam = selectedBrands.join(',');
            var filterParam = selectedFilters.join(',');

            var currentUrl = window.location.href.split('?')[0];
            var stkParam = stokCheckbox.checked ? '1' : '0';
            var newUrl = currentUrl + '?cat='+ kategorisiymis + '&brand=' + brandParam + '&filter=' + filterParam + '&search=<?= $arama; ?>&stk=' + stkParam;
            window.location.href = newUrl;
        }
        if ($(window).width() <= 992) {
            // Marka seçildiğinde
            $('.brand-checkbox1').change(function() {
                updateFiltersInUrl1();
            });

            // Filtre seçildiğinde
            $('.filter-checkbox1').change(function() {
                updateFiltersInUrl1();
            });

            function updateFiltersInUrl1() {

                var selectedBrands = [];
                $('.brand-checkbox1:checked').each(function() {
                    selectedBrands.push($(this).val());
                });

                var selectedFilters = [];
                $('.filter-checkbox1:checked').each(function() {
                    selectedFilters.push($(this).val());
                });

                var brandParam = selectedBrands.join(',');
                var filterParam = selectedFilters.join(',');

                var currentUrl = window.location.href.split('?')[0];
                var newUrl = currentUrl + '?cat=<?= $kategori; ?>&brand=' + brandParam + '&filter=' + filterParam + '&search=<?= $arama; ?>';
                window.location.href = newUrl;
            }
        }
    });
</script>
<script>
    $(document).ready(function () {
        $('.favori-buton').on('click', function (event) {
            event.preventDefault();
            var iconElement = $(this).find('.favori-icon'); // Assuming the icon is inside the favori-buton
            var uye_id = <?= $_SESSION["id"] ?>;
            var productId = $(this).data('product-id');
            var type = 'favori';
            // Store the reference to $(this) in a variable for later use
            var that = $(this);
            // Send an AJAX request to the server to add the product to the favorites
            $.ajax({
                type: 'POST',
                url: 'functions/favori/edit_favori.php',
                data: {
                    product_id: productId,
                    uye_id: uye_id,
                    type: type
                },
                success: function (response) {
                    // Handle the response, you can update the UI as needed
                    if (response.includes('added')) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Ürün Favorilere Eklendi!',
                            toast: true,
                            position: 'top-start',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        iconElement.removeClass("fa-regular").addClass("fa-solid");
                        that.css("color", "red");
                        window.location.reload();
                    } else if (response.includes('removed')) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Ürün Favorilerden Kaldırıldı!',
                            toast: true,
                            position: 'top-start',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        iconElement.removeClass("fa-solid").addClass("fa-regular");
                        window.location.reload();
                    }
                }
            });
        });
    });
    $(document).ready(function () {
        $.ajax({
            type: 'GET',
            url: 'functions/get/get_favorites.php',
            dataType: 'json',
            success: function (favoriteProducts) {
                // Iterate through the favorite products and update their classes
                favoriteProducts.forEach(function (productId) {
                    var selector = '.favori-icon[data-product-id="' + productId + '"]';
                    $(selector).removeClass("fa-regular").addClass("fa-solid");
                });
                // Add hover effect to both products in favorites and not in favorites
                $(".favori-icon.fa-regular").hover(
                    function () {
                        // Hover in
                        $(this).removeClass("fa-regular").addClass("fa-solid");
                        $(this).css("color", "red");
                    },
                    function () {
                        // Hover out
                        $(this).removeClass("fa-solid").addClass("fa-regular");

                        $(this).css("color", "");
                    }
                );
            }
        });
    });
</script>

<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>