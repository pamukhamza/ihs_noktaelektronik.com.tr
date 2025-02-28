<div class="col-5">
    <div id="yourDropdownToggleId" data-bs-toggle="dropdown" aria-expanded="false" class="p-2 rounded-3 d-flex justify-content-between" style="width: 100%; height:100%; background-color:#f29720; color:#ffffff; font-weight: 500;">
        <span class="">Hoşgeldin
            <?php if($_SESSION['firma']){
                $firma = $_SESSION["firma"]; 
                if(strlen($firma) > 15) {$firma = substr($firma, 0, 15) . '...';}    ?>
                <br><span class="fw-bold"><?= $firma ?></span>
            <?php } else { ?>
                <br><span class="fw-bold"><?= $_SESSION['ad'] . ' ' . $_SESSION["soyad"]; ?></span>
            <?php } ?>
        </span>
        <span class="my-auto me-2"><i class="fa-solid fa-chevron-down fa-xl" style="color: #ffffff;"></i></span>
    </div>
    <div class="dropdown-menu-wrapper" style="">
        <ul id="yourDropdownMenuId" class="dropdown-menu" style="background-color: #555555;">
            <li><a class="ab dropdown-item c-f text-end" href="tr/cariodeme">Cari İşlemler</a></li>
            <li><a class="ab dropdown-item c-f text-end" href="tr/cari-islem-gecmisi">Cari İşlem Listesi</a></li>
            <li><a class="ab dropdown-item c-f text-end" href="tr/siparisler">Siparişlerim</a></li>
            <li><a class="ab dropdown-item c-f text-end" href="tr/iadeler">İadeler</a></li>
            <li><a class="ab dropdown-item c-f text-end" href="tr/bilgiler">Üyelik Bilgilerim</a></li>
            <li><a class="ab dropdown-item c-f text-end" href="tr/cikis">Çıkış Yap</a></li>
        </ul>
    </div>
</div>
