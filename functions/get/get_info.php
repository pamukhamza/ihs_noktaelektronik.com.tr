<?php

require_once '../db.php';
$db = new Database();

function getBlogById($bId) {
    global $db;
    return $db->fetch("SELECT * FROM nokta_blog WHERE id = :id", ['id' => $bId]);
}

function getsssById($sId) {
    global $db;
    return $db->fetch("SELECT * FROM sss WHERE id = :id", ['id' => $sId]);
}

function getSliderById($sId) {
    global $db;
    return $db->fetch("SELECT * FROM noktaslider WHERE id = :id", ['id' => $sId]);
}

function getPopupById($sId) {
    global $db;
    return $db->fetch("SELECT * FROM popup_kampanya WHERE id = :id", ['id' => $sId]);
}

function getIkonById($iId) {
    global $db;
    return $db->fetch("SELECT * FROM anasayfa_ikon WHERE id = :id", ['id' => $iId]);
}

function getBankaBilgisiById($bId) {
    global $db;
    return $db->fetch("SELECT * FROM nokta_banka_bilgileri WHERE id = :id", ['id' => $bId]);
}

function getAdresGetirById($adId) {
    global $db;
    return $db->fetch("SELECT a.*, il.*, ilce.* 
                      FROM adresler AS a
                      LEFT JOIN iller AS il ON a.il = il.il_id
                      LEFT JOIN ilceler AS ilce ON a.ilce = ilce.ilce_id
                      WHERE a.id = :id", ['id' => $adId]);
}

function getIletisimById($bId) {
    global $db;
    return $db->fetch("SELECT * FROM nokta_iletisim_form WHERE id = :id", ['id' => $bId]);
}

function getHaberById($bId) {
    global $db;
    return $db->fetch("SELECT * FROM haber WHERE id = :id", ['id' => $bId]);
}

function getBannerById($bId) {
    global $db;
    return $db->fetch("SELECT * FROM nokta_banner WHERE id = :id", ['id' => $bId]);
}

function getBannerVideoById($bId) {
    global $db;
    return $db->fetch("SELECT * FROM nokta_banner_video WHERE id = :id", ['id' => $bId]);
}

function getCategoryById($categoryId) {
    global $db;
    return $db->fetch("SELECT * FROM nokta_kategoriler WHERE id = :id", ['id' => $categoryId]);
}

function getAdresById($adresId) {
    global $db;
    return $db->fetch("SELECT * FROM nokta_iletisim WHERE id = :id", ['id' => $adresId]);
}

function getKatalogById($kId) {
    global $db;
    return $db->fetch("SELECT * FROM nokta_kataloglar WHERE id = :id", ['id' => $kId]);
}

function getMarkaById($mId) {
    global $db;
    return $db->fetch("SELECT * FROM nokta_urun_markalar WHERE id = :id", ['id' => $mId]);
}

function getIlanById($iId) {
    global $db;
    return $db->fetch("SELECT * FROM nokta_ilanlar WHERE id = :id", ['id' => $iId]);
}

function getFormById($iId) {
    global $db;
    return $db->fetch("SELECT * FROM nokta_iletisim_form WHERE id = :id", ['id' => $iId]);
}

function getTeklif($iId) {
    global $db;
    return $db->fetch("SELECT bt.*, u.* FROM b2b_teklif AS bt
                      LEFT JOIN uyeler AS u ON bt.uye_id = u.id
                      WHERE bt.id = :id", ['id' => $iId]);
}

function getTdpById($tId) {
    global $db;
    return $db->fetch("SELECT u.*, t.uye_id, t.takip_kodu, t.tarih, t.fatura_no, t.musteri, 
                             t.tel, t.mail, t.adres, t.teslim_eden, t.teslim_alan, t.gonderim_sekli, 
                             t.kargo_firmasi, t.aciklama
                      FROM teknik_destek_urunler u
                      LEFT JOIN nokta_teknik_destek t ON u.tdp_id = t.id
                      WHERE u.id = :id", ['id' => $tId]);
}

function getTdpurunById($tId) {
    global $db;
    return $db->fetch("SELECT * FROM teknik_destek_urunler WHERE tdp_id = :id", ['id' => $tId]);
}

function getFiltreById($tId) {
    global $db;
    return $db->fetch("SELECT * FROM filtreler WHERE id = :id", ['id' => $tId]);
}

function getFiltreKategoriById($tId) {
    global $db;
    return $db->fetch("SELECT * FROM filtre_kategoriler WHERE id = :id", ['id' => $tId]);
}

function getVaryasyonById($vId) {
    global $db;
    return $db->fetch("SELECT * FROM nokta_urun_varyasyon WHERE id = :id", ['id' => $vId]);
}

function getKampanyaById($vId) {
    global $db;
    return $db->fetch("SELECT * FROM kampanyalar WHERE id = :id", ['id' => $vId]);
}

function getTaksit($vId) {
    global $db;
    return $db->fetch("SELECT * FROM banka_taksit_eslesme WHERE id = :id", ['id' => $vId]);
}

function getKargo($vId) {
    global $db;
    $result = $db->fetch("SELECT dosya FROM kargo_pdf WHERE sip_id = :id", ['id' => $vId]);
    return $result['dosya'];
}

function getFuar($vId) {
    global $db;
    return $db->fetch("SELECT * FROM fuar WHERE id = :id", ['id' => $vId]);
}

function getiadeDuzenle($vId) {
    global $db;
    return $db->fetch("SELECT i.durum, u.ad, u.soyad, u.email, u.tel, i.id, i.sip_urun_id 
                      FROM iadeler AS i
                      LEFT JOIN uyeler AS u ON u.id = i.uye_id 
                      WHERE i.id = :id", ['id' => $vId]);
}

if (isset($_POST['bankaID'])) {
    $bankaID = $_POST['bankaID'];
    $data = $db->fetchAll("SELECT * FROM banka_taksit_eslesme WHERE kart_id = :id ORDER BY taksit ASC", [
        'id' => $bankaID
    ]);
    
    foreach ($data as $row) {
        ?>
        <input type="text" id="kart_id" name="kart_id" hidden value="<?= $row['kart_id']; ?>" >
        <tr class="border-0">
            <td>[<?= $row['taksit']; ?>]</td>
            <td><?php
                switch ($row['pos_id']) {
                    case 1:
                        echo "Param Pos";
                        break;
                    case 2:
                        echo "Garanti Pos";
                        break;
                    case 3:
                        echo "Kuveyt Pos";
                        break;
                    case 4:
                        echo "Finans Pos";
                        break;
                    default:
                        echo "Boş";
                }
                ?>
            </td>
            <td><?= $row['vade']; ?></td>
            <td><?php 
                $ticProg = $row['ticari_program'];
                $result = $db->fetch("SELECT * FROM banka_pos_listesi WHERE id = :id", [
                    'id' => $ticProg
                ]);
                if ($result) {
                    $yazi = $result['id'] . '-' . $result['BANKA_ADI'] . ' - ' . $result['TANIMI'] . ' - Taksit Sayısı: ' . $result['TAKSIT_SAYISI'];
                    echo $yazi;
                }
                ?>
            </td>
            <td>
                <label class='switch-button switch-button-success'>
                    <input type="checkbox" id="<?= $row['id']; ?>" name="<?= $row['id']; ?>" class="aktifPasifBanka" <?= ($row['aktif'] == 1 ? 'checked' : ''); ?> />
                    <span><label for="<?= $row['id']; ?>"></label></span>
                </label>
            </td>
            <td>
                <button type="button" name="bankaDuzenle" value="Düzenle" class="btn btn-sm btn-outline-light edit-taksit" data-taksit-id="<?= $row['id']; ?>"><i class="far fa-edit"></i></button>
                <button type="button" class="btn btn-sm btn-outline-light" onclick="dynamicSil(<?= $row['id']; ?>, '', 'banka', 'Taksit Tanımı silindi.', 'admin/muhasebe/adminBankaKomisyonlari.php');"><i class='far fa-trash-alt'></i></button>
            </td>
        </tr>
        <?php
    }
}

if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];
    
    $data = null;
    switch ($type) {
        case 'slider':
            $data = getSliderById($id);
            break;
        case 'category':
            $data = getCategoryById($id);
            break;
        case 'fuar':
            $data = getFuar($id);
            break;
        case 'popup':
            $data = getPopupById($id);
            break;
        case 'adres':
            $data = getAdresById($id);
            break;
        case 'sss':
            $data = getsssById($id);
            break;
        case 'ikon':
            $data = getIkonById($id);
            break;
        case 'bankaBilgisi':
            $data = getBankaBilgisiById($id);
            break;
        case 'adresGetir':
            $data = getAdresGetirById($id);
            break;
        case 'haber':
            $data = getHaberById($id);
            break;
        case 'banner':
            $data = getBannerById($id);
            break;
        case 'bannerVideo':
            $data = getBannerVideoById($id);
            break;
        case 'katalog':
            $data = getKatalogById($id);
            break;
        case 'marka':
            $data = getMarkaById($id);
            break;
        case 'ilan':
            $data = getIlanById($id);
            break;
        case 'form':
            $data = getFormById($id);
            break;
        case 'tdp':
            $data = getTdpById($id);
            break;
        case 'tdp_urunler':
            $data = getTdpurunById($id);
            break;
        case 'filtre':
            $data = getFiltreById($id);
            break;
        case 'filtreKategori':
            $data = getFiltreKategoriById($id);
            break;
        case 'iletisim':
            $data = getIletisimById($id);
            break;
        case 'varyasyon':
            $data = getVaryasyonById($id);
            break;
        case 'kampanya':
            $data = getKampanyaById($id);
            break;
        case 'iadeDuzenle':
            $data = getiadeDuzenle($id);
            break;
        case 'taksit':
            $data = getTaksit($id);
            break;
        case 'kargo_barkod':
            $data = getKargo($id);
            break;
        case 'blog':
            $data = getBlogById($id);
            break;
        case 'teklif':
            $data = getTeklif($id);
            break;
    }
    
    echo json_encode($data ?? ['error' => 'Invalid request type']);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
