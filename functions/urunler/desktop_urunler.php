<?php
require_once '../db.php';
$db = new Database();
?>
<!-- Kategoriler -->
<div class="border rounded-3" style="background-color: #ffffff;">
    <h5 class="border p-2"><?php echo translate("kategori", $lang, $user_language); ?></h5>
    <ul class="list-unstyled ps-0 kategori-effect ">
        <?php
        if (empty($_GET['cat'])) {
            if (empty($_GET['brand'])) {
                // Get top-level categories
                $kategoriler = $db->fetchAll("SELECT * FROM nokta_kategoriler WHERE parent_id = 0");
                foreach ($kategoriler as $kategori) {
                    $kategori_adi = $kategori['kategori_adi'];
                    $kategori_seo_link = $kategori['seo_link'];
                    ?>
                    <li class="">
                        <a href="urunler?lang=<?= $user_language ?>&cat=<?= $kategori_seo_link ?>&brand=<?= $_GET['brand'] ?>&filter=&search=" 
                           style="text-align: left !important;" 
                           class="btn d-inline-flex align-items-center rounded border-0 collapsed">
                            <?= $kategori_adi ?>
                        </a>
                    </li>
                    <?php
                }
            } else {
                $eklenen_en_ust_kategoriler_adi = array();
                foreach ($result2 as $row) {
                    $kategoriID = $row['KategoriID'];
                    $en_ust_kategori_id = $kategoriID;
                    $en_ust_kategori_adi = "";
                    
                    while ($en_ust_kategori_id != 0) {
                        $ust_kategori = $db->fetch("SELECT * FROM nokta_kategoriler WHERE id = :id", [
                            'id' => $en_ust_kategori_id
                        ]);
                        
                        if ($ust_kategori) {
                            $en_ust_kategori_id = $ust_kategori['parent_id'];
                            $en_ust_kategori_adi = $ust_kategori['kategori_adi'];
                            $kategori_seo_link = $ust_kategori['seo_link'];
                        } else {
                            $en_ust_kategori_id = 0;
                        }
                    }
                    
                    if (!in_array($en_ust_kategori_adi, $eklenen_en_ust_kategoriler_adi)) {
                        ?>
                        <li class="">
                            <a href="urunler?lang=<?= $user_language ?>&cat=<?= $kategori_seo_link ?>&brand=<?= $_GET['brand'] ?>&filter=&search=" 
                               style="text-align: left !important;" 
                               class="btn d-inline-flex align-items-center rounded border-0 collapsed">
                                <?= $en_ust_kategori_adi ?>
                            </a>
                        </li>
                        <?php
                        $eklenen_en_ust_kategoriler_adi[] = $en_ust_kategori_adi;
                    }
                }
            }
        } else {
            if (empty($_GET['brand'])) {
                // Get subcategories
                $subcategories = $db->fetchAll("SELECT * FROM nokta_kategoriler WHERE parent_id = :kategori_id", [
                    'kategori_id' => $kategori_id
                ]);
                
                foreach ($subcategories as $subcat) {
                    ?>
                    <li class="mb-1">
                        <a href="urunler?lang=<?= $user_language ?>&cat=<?= $subcat['seo_link'] ?>&brand=<?= $_GET['brand'] ?>&filter=&search=" 
                           style="text-align: left !important;" 
                           class="btn d-inline-flex align-items-center rounded border-0 collapsed">
                            <?= $subcat['kategori_adi'] ?>
                        </a>
                    </li>
                    <?php
                }
                
                if (empty($subcategories)) {
                    // Get parent category info
                    $parent_cat = $db->fetch("SELECT parent_id, kategori_adi FROM nokta_kategoriler WHERE id = :kategori_id", [
                        'kategori_id' => $kategori_id
                    ]);
                    
                    if ($parent_cat) {
                        $parent_id = $parent_cat['parent_id'];
                        $cat_adi = $parent_cat['kategori_adi'];
                        
                        // Get sibling categories
                        $sibling_cats = $db->fetchAll("SELECT * FROM nokta_kategoriler WHERE parent_id = :parent_id", [
                            'parent_id' => $parent_id
                        ]);
                        
                        foreach ($sibling_cats as $sibling) {
                            $style = ($sibling['kategori_adi'] == $cat_adi) ? 'transform: translateX(8px);color:purple;font-weight:bold;' : '';
                            ?>
                            <li class="mb-1">
                                <a href="urunler?lang=<?= $user_language ?>&cat=<?= $sibling['seo_link'] ?>&brand=<?= $_GET['brand'] ?>&filter=&search=" 
                                   style="text-align: left !important; <?= $style ?>" 
                                   class="btn d-inline-flex align-items-center rounded border-0 collapsed">
                                    <?= $sibling['kategori_adi'] ?>
                                </a>
                            </li>
                            <?php
                        }
                    }
                }
            } else {
                $eklenen_en_ust_kategoriler_adi = array();
                foreach ($result2 as $row) {
                    $kategoriID = $row['KategoriID'];
                    $en_ust_kategori_id = $kategoriID;
                    $en_ust_kategori_adi = "";
                    
                    while ($en_ust_kategori_id != $kategori_id) {
                        $ust_kategori = $db->fetch("SELECT * FROM nokta_kategoriler WHERE id = :id", [
                            'id' => $en_ust_kategori_id
                        ]);
                        
                        if ($ust_kategori) {
                            $en_ust_kategori_id = $ust_kategori['parent_id'];
                            $en_ust_kategori_adi = $ust_kategori['kategori_adi'];
                            $kategori_seo_link = $ust_kategori['seo_link'];
                        } else {
                            $en_ust_kategori_id = $kategori_id;
                        }
                    }
                    
                    if (!in_array($en_ust_kategori_adi, $eklenen_en_ust_kategoriler_adi)) {
                        ?>
                        <li class="">
                            <a href="urunler?lang=<?= $user_language ?>&cat=<?= $kategori_seo_link ?>&brand=<?= $_GET['brand'] ?>&filter=&search=" 
                               style="text-align: left !important;" 
                               class="btn d-inline-flex align-items-center rounded border-0 collapsed">
                                <?= $en_ust_kategori_adi ?>
                            </a>
                        </li>
                        <?php
                        $eklenen_en_ust_kategoriler_adi[] = $en_ust_kategori_adi;
                    }
                }
            }
        }
        ?>
    </ul>
</div>

<!--Markalar filterleme -->
<div class="border mt-3 rounded-3" style="background-color: #ffffff;">
    <h5 class="border p-2"><?php echo translate("marka", $lang, $user_language); ?></h5>
    <ul class="list-unstyled ps-1" style="overflow-y: scroll; max-height:280px">
        <?php
        if (!empty($alt_kategori_ids_str)) {
            // Get brands for specific categories
            $markalar = $db->fetchAll("SELECT DISTINCT m.title AS marka_adi, m.seo_link AS marka_seo
                                     FROM nokta_urunler u
                                     LEFT JOIN nokta_urun_markalar_1 m ON u.MarkaID = m.id
                                     WHERE u.KategoriID IN ($alt_kategori_ids_str)");
        } else {
            // Get all active brands
            $markalar = $db->fetchAll("SELECT title AS marka_adi, seo_link AS marka_seo 
                                     FROM nokta_urun_markalar_1 
                                     WHERE aktif = 1");
        }

        foreach ($markalar as $marka) {
            $checked = '';
            $selected_brands = !empty($_GET['brand']) ? explode(',', $_GET['brand']) : array();
            if (in_array($marka['marka_seo'], $selected_brands)) {
                $checked = 'checked';
            }
            ?>
            <div class="form-check">
                <input class="form-check-input brand-checkbox" type="checkbox" 
                       id="marka-<?= $marka['marka_adi'] ?>" 
                       name="marka[]" 
                       value="<?= $marka['marka_seo'] ?>" 
                       <?= $checked ?>>
                <label class="form-check-label" for="marka-<?= $marka['marka_adi'] ?>">
                    <?= $marka['marka_adi'] ?>
                </label>
            </div>
            <?php
        }
        ?>
    </ul>
</div>

<!--Özellikler filterleme -->
<?php
// Get unique filter IDs from products
$filtre_ids = array();
foreach ($result as $rowff) {
    if (!empty($rowff['filtre'])) {
        $filtre_ids = array_merge($filtre_ids, explode(',', $rowff['filtre']));
    }
}
$filtre_ids = array_unique($filtre_ids);
?>

<div class="border mt-3 mb-5" style="background-color: #ffffff; <?= empty($filtre_ids) ? 'display: none;' : '' ?>">
    <h5 class="border p-2"><?php echo translate("filter", $lang, $user_language); ?></h5>
    <ul class="list-unstyled ps-1" style="overflow-y: scroll; max-height:280px">
        <?php
        foreach ($filtre_ids as $filtre_id) {
            $filtre = $db->fetch("SELECT DISTINCT filtre_adi FROM filtreler WHERE id = :id", [
                'id' => $filtre_id
            ]);
            
            if ($filtre) {
                $selected_filters = !empty($_GET['filter']) ? explode(',', $_GET['filter']) : array();
                $checked = in_array($filtre_id, $selected_filters) ? 'checked' : '';
                ?>
                <div class="form-check">
                    <input class="form-check-input filter-checkbox" type="checkbox" 
                           id="filtre-<?= $filtre_id ?>" 
                           name="filtre[]" 
                           value="<?= $filtre_id ?>" 
                           <?= $checked ?>>
                    <label class="form-check-label" for="filtre-<?= $filtre_id ?>">
                        <?= $filtre['filtre_adi'] ?>
                    </label>
                </div>
                <?php
            }
        }
        ?>
    </ul>
</div>