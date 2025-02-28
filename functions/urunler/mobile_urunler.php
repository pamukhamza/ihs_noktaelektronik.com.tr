<button class="btn btn-primary mobile-menu" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltre" aria-controls="offcanvasFiltre">
    Filtreler
</button>
<div class="offcanvas offcanvas-start w-75" tabindex="-1" id="offcanvasFiltre" aria-labelledby="offcanvasFiltreLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasFiltreLabel">Filtreler</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Kategoriler -->
        <div class="border rounded-3" style="background-color: #ffffff;">
            <h5 class="border p-2"><?php echo translate("kategori", $lang, $user_language); ?></h5>
            <ul class="list-unstyled ps-0 kategori-effect ">
                <?php
                if (empty($_GET['cat'])) {
                    if (empty($_GET['brand'])) {
                        $kategori_sql = "SELECT * FROM nokta_kategoriler WHERE parent_id = 0";
                        $kategori_result = mysqli_query($connection, $kategori_sql);
                        while ($kategori_row = mysqli_fetch_assoc($kategori_result)) {
                            $kategori_id = $kategori_row['id'];
                            $kategori_adi = $kategori_row['kategori_adi'];
                            $kategori_seo_link = $kategori_row['seo_link']; ?>
                            <li class="">
                                <a href="urunler?lang=<?php echo $user_language ?>&cat=<?php echo $kategori_seo_link ?>&brand=<?php echo $_GET['brand']; ?>&filter=&search=" style="text-align: left !important;" class="btn d-inline-flex align-items-center rounded border-0 collapsed">
                                    <?php echo $kategori_adi; ?>
                                </a>
                            </li>
                        <?php }
                    } else {
                        $eklenen_en_ust_kategoriler_adi = array(); // Eklenen en üst kategorilerin adlarının listesi
                        while ($row = mysqli_fetch_assoc($result2)) {
                            $kategoriID = $row['KategoriID'];

                            // En üst kategoriyi bulma
                            $en_ust_kategori_id = $kategoriID;
                            $en_ust_kategori_adi = "";
                            while ($en_ust_kategori_id != 0) {
                                $ust_kategori_sql = "SELECT * FROM nokta_kategoriler WHERE id = ?";
                                $stmt_ust_kategori = $connection->prepare($ust_kategori_sql);
                                $stmt_ust_kategori->bind_param("i", $en_ust_kategori_id);
                                $stmt_ust_kategori->execute();
                                $ust_kategori_result = $stmt_ust_kategori->get_result();
                                if ($ust_kategori_result->num_rows > 0) {
                                    $ust_kategori_row = $ust_kategori_result->fetch_assoc();
                                    $en_ust_kategori_id = $ust_kategori_row['parent_id'];
                                    $en_ust_kategori_adi = $ust_kategori_row['kategori_adi'];
                                    $kategori_seo_link = $ust_kategori_row['seo_link'];
                                } else {
                                    $en_ust_kategori_id = 0;
                                }
                            }
                            // En üst kategoriyi ekrana yazdırma
                            if (!in_array($en_ust_kategori_adi, $eklenen_en_ust_kategoriler_adi)) {
                                ?>
                                <li class="">
                                    <a href="urunler?lang=<?php echo $user_language ?>&cat=<?php echo $kategori_seo_link ?>&brand=<?php echo $_GET['brand']; ?>&filter=&search=" style="text-align: left !important;" class="btn d-inline-flex align-items-center rounded border-0 collapsed">
                                        <?php echo $en_ust_kategori_adi; ?>
                                    </a>
                                </li>
                                <?php
                                $eklenen_en_ust_kategoriler_adi[] = $en_ust_kategori_adi; // Kategori adını eklenenler listesine ekle
                            }
                        }
                    }
                }
                else {
                    if(empty($_GET['brand'])){
                        $parent_cat_sql = "SELECT * FROM nokta_kategoriler WHERE parent_id = $kategori_id ";
                        $parent_cat_result = mysqli_query($connection, $parent_cat_sql);
                        while ($parent_cat_row = mysqli_fetch_assoc($parent_cat_result)) {
                            $parent_cat_adi = $parent_cat_row['kategori_adi'];
                            $parent_cat_seo_link = $parent_cat_row['seo_link'];
                            ?>
                            <li class="mb-1">
                                <a href="urunler?lang=<?php echo $user_language ?>&cat=<?php echo $parent_cat_seo_link ?>&brand=<?php echo $_GET['brand']; ?>&filter=&search="  style="text-align: left !important;" class="btn  d-inline-flex align-items-center rounded border-0 collapsed">
                                    <?php echo $parent_cat_adi; ?>
                                </a>
                            </li>
                        <?php }
                        if (mysqli_num_rows($parent_cat_result) == 0) {
                            $parent_cat_sql1 = "SELECT parent_id, kategori_adi FROM nokta_kategoriler WHERE id = $kategori_id ";
                            $parent_cat_result1 = mysqli_query($connection, $parent_cat_sql1);
                            $parent_cat_row1 = mysqli_fetch_assoc($parent_cat_result1);
                            $parent_id1 = $parent_cat_row1['parent_id'];
                            $cat_adi1 = $parent_cat_row1['kategori_adi'];
                            $parent_cat_sql2 = "SELECT * FROM nokta_kategoriler WHERE parent_id = $parent_id1 ";
                            $parent_cat_result3 = mysqli_query($connection, $parent_cat_sql2);
                            while ($parent_cat_row4 = mysqli_fetch_assoc($parent_cat_result3)) {
                                $parent_cat_adi1 = $parent_cat_row4['kategori_adi'];
                                $parent_cat_seo_link1 = $parent_cat_row4['seo_link'];
                                $style = '';
                                if ($parent_cat_adi1 == $cat_adi1) {$style = 'transform: translateX(8px);color:purple;font-weight:bold;';}
                                ?>
                                <li class="mb-1">
                                    <a href="urunler?lang=<?php echo $user_language ?>&cat=<?php echo $parent_cat_seo_link1 ?>&brand=<?php echo $_GET['brand']; ?>&filter=&search="  style="text-align: left !important; <?php echo $style; ?>" class="btn  d-inline-flex align-items-center rounded border-0 collapsed">
                                        <?php echo $parent_cat_adi1; ?>
                                    </a>
                                </li>
                            <?php }
                        }
                    }else{
                        $eklenen_en_ust_kategoriler_adi = array(); // Eklenen en üst kategorilerin adlarının listesi
                        while ($row = mysqli_fetch_assoc($result2)) {
                            $kategoriID = $row['KategoriID'];
                            // En üst kategoriyi bulma
                            $en_ust_kategori_id = $kategoriID;
                            $en_ust_kategori_adi = "";
                            while ($en_ust_kategori_id != $kategori_id) {
                                $ust_kategori_sql = "SELECT * FROM nokta_kategoriler WHERE id = ?";
                                $stmt_ust_kategori = $connection->prepare($ust_kategori_sql);
                                $stmt_ust_kategori->bind_param("i", $en_ust_kategori_id);
                                $stmt_ust_kategori->execute();
                                $ust_kategori_result = $stmt_ust_kategori->get_result();
                                if ($ust_kategori_result->num_rows > 0) {
                                    $ust_kategori_row = $ust_kategori_result->fetch_assoc();
                                    $en_ust_kategori_id = $ust_kategori_row['parent_id'];
                                    $en_ust_kategori_adi = $ust_kategori_row['kategori_adi'];
                                    $kategori_seo_link = $ust_kategori_row['seo_link'];
                                } else {
                                    $en_ust_kategori_id = $kategori_id;
                                }
                            }
                            // En üst kategoriyi ekrana yazdırma
                            if (!in_array($en_ust_kategori_adi, $eklenen_en_ust_kategoriler_adi)) {
                                ?>
                                <li class="">
                                    <a href="urunler?lang=<?php echo $user_language ?>&cat=<?php echo $kategori_seo_link ?>&brand=<?php echo $_GET['brand']; ?>&filter=&search=" style="text-align: left !important;" class="btn d-inline-flex align-items-center rounded border-0 collapsed">
                                        <?php echo $en_ust_kategori_adi; ?>
                                    </a>
                                </li>
                                <?php
                                $eklenen_en_ust_kategoriler_adi[] = $en_ust_kategori_adi; // Kategori adını eklenenler listesine ekle
                            }
                        }
                    }
                }
                ?>
            </ul>
        </div>
        <!--Markalar filterleme -->
        <div class=" border mt-3 rounded-3" style="background-color: #ffffff;">
            <h5 class="border p-2"><?php echo translate("marka", $lang, $user_language); ?></h5>
            <ul class="list-unstyled ps-1" style="overflow-y: scroll; max-height:280px">
                <?php
                if (!empty($alt_kategori_ids_str)) {
                    $markalar_sql = "SELECT DISTINCT m.title AS marka_adi ,m.seo_link AS marka_seo
                                     FROM nokta_urunler u
                                     LEFT JOIN nokta_urun_markalar_1 m ON u.MarkaID = m.id
                                     WHERE u.KategoriID IN ($alt_kategori_ids_str)";
                    $markalar_result = mysqli_query($connection, $markalar_sql);

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
                            <input class="form-check-input brand-checkbox" type="checkbox" id="marka-<?php echo $marka_adi; ?>" name="marka[]" value="<?php echo $marka_seo; ?>" <?php echo $checked; ?>>
                            <label class="form-check-label" for="marka-<?php echo $marka_adi; ?>"><?php echo $marka_adi; ?></label>
                        </div>
                    <?php }
                }else{
                    $marka1_sql = "SELECT * FROM nokta_urun_markalar_1 WHERE aktif = 1";
                    $markalar_result1 = mysqli_query($connection, $marka1_sql);
                    while ($marka_row = mysqli_fetch_assoc($markalar_result1)) {
                        $marka_adi = $marka_row['title'];
                        $marka_seo = $marka_row['seo_link'];
                        $checked = '';
                        $selected_brands = !empty($_GET['brand']) ? explode(',', $_GET['brand']) : array();
                        if (in_array($marka_seo, $selected_brands)) {
                            $checked = 'checked';
                        }
                        ?>
                        <div class="form-check">
                            <input class="form-check-input brand-checkbox" type="checkbox" id="marka-<?php echo $marka_adi; ?>" name="marka[]" value="<?php echo $marka_seo; ?>" <?php echo $checked; ?>>
                            <label class="form-check-label" for="marka-<?php echo $marka_adi; ?>"><?php echo $marka_adi; ?></label>
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
        while ($rowff = mysqli_fetch_assoc($result)) {
            if (!empty($rowff['filtre'])) {
                $filtre_ids = array_merge($filtre_ids, explode(',', $rowff['filtre'])); // Her üründeki filtreleri birleştir
            }
        }
        $filtre_ids = array_unique($filtre_ids); // Tekrar eden değerleri kaldır
        ?>
        <div class="border mt-3 mb-5" style="background-color: #ffffff; <?php if (empty($filtre_ids)) {echo "display: none;";} ?>">
            <h5 class="border p-2"><?php echo translate("filter", $lang, $user_language); ?></h5>
            <ul class="list-unstyled ps-1 " style="overflow-y: scroll; max-height:280px">
                <?php
                foreach ($filtre_ids as $filtre_id) {
                    $sql_filtre = "SELECT DISTINCT filtre_adi FROM filtreler WHERE id = '$filtre_id'";
                    $result_filtre = mysqli_query($connection, $sql_filtre);
                    $row_filtre = mysqli_fetch_assoc($result_filtre);
                    if ($row_filtre) {
                        $filtre_adi = $row_filtre['filtre_adi'];
                        $selected_filters = !empty($_GET['filter']) ? explode(',', $_GET['filter']) : array();
                        $checked1 = '';
                        if (in_array($filtre_id, $selected_filters)) {
                            $checked1 = 'checked';
                        }
                        ?>
                        <div class="form-check">
                            <input class="form-check-input filter-checkbox" type="checkbox" id="filtre-<?php echo $filtre_id; ?>" name="filtre[]" value="<?php echo $filtre_id; ?>" <?php echo $checked1; ?>>
                            <label class="form-check-label" for="filtre-<?php echo $filtre_id; ?>"><?php echo $filtre_adi; ?></label>
                        </div>
                    <?php }
                }
                ?>

            </ul>
        </div>
    </div>
</div>