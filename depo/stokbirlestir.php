<?php
include "functions.php";
session();

if (isset($_POST['tablo_temizle'])) {
    // Tabloyu temizle (Truncate)
    $truncateQuery = $db->prepare("TRUNCATE TABLE stoklar_duzgun");
    $truncateQuery->execute();
    echo "Tablo temizlendi.";
}

if (isset($_POST['stok_birlestir'])) {
    // Stokları birleştir
    $birlestirQuery = $db->prepare("INSERT INTO stoklar_duzgun (depo_id, stok_kodu, stok_adedi)
                                    SELECT depo_id, stok_kodu, SUM(stok_adedi)
                                    FROM stoklar
                                    GROUP BY depo_id, stok_kodu");
    $birlestirQuery->execute();
    echo "Stoklar birleştirildi ve stoklar_duzgun tablosuna kaydedildi.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">
    <title>Stok Birleştir</title>
</head>
<body>
    <?php adminheaders(); ?>
    <div class="modal modal-sheet position-static d-block bg-body-secondary p-4 py-md-5" tabindex="-1" role="dialog" id="modalSheet">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header border-bottom-0">
                    <h1 class="modal-title fs-5">Stokları Birleştirme Fonksiyonları</h1>
                </div>
                <form method="POST">
                    <div class="modal-footer flex-column align-items-stretch w-100 gap-2 pb-3 border-top-0">
                        <button type="submit" class="btn btn-lg btn-primary" name="tablo_temizle">Tabloyu Temizle</button>
                    </div>
                    <div class="modal-footer flex-column align-items-stretch w-100 gap-2 pb-3 border-top-0">
                        <button type="submit" class="btn btn-lg btn-primary" name="stok_birlestir">Stokları Birleştir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
