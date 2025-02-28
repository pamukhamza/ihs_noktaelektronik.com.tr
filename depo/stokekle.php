<?php include "functions.php";
session();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">
    <title>Olmayan Ürün Ekle</title>
    <style>
        .table-wrapper{
            max-height: 600px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
<?php adminheaders(); ?>
<div class="modal modal-sheet position-static d-block bg-body-secondary p-4 py-md-5" tabindex="-1" role="dialog" id="modalSheet">
    <div class="modal-dialog" role="document">
        <div class="modal-content rounded-4 shadow">
            <form action="functions.php" method="post">
                <div class="modal-header border-bottom-0">
                    <h1 class="modal-title fs-5">Yeni Stok Girişi</h1>
                </div>
                <div class="modal-footer flex-column align-items-stretch w-100 gap-2 pb-3 border-top-0"> 
                
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Stok Kodu</label>
                        <input type="text" class="form-control" id="stok_kodu" name="stok_kodu">
                    </div>
                    <?php if( $_GET && $_GET['s'] == 1 ) echo "<p style='color:green'>Stok Kaydedildi</p>"?>
                </div>
                <div class="modal-footer flex-column align-items-stretch w-100 gap-2 pb-3 border-top-0">
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Stok Adeti</label>
                        <input type="number" class="form-control" id="stok_adedi" name="stok_adedi">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Açıklama</label>
                        <input type="text" class="form-control" id="aciklama" name="aciklama">
                        <p>Türkçe karakter kullanmayın!</p>
                    </div>
                </div>
                <div class="modal-footer flex-column align-items-stretch w-100 gap-2 pb-3 border-top-0">
                    <button type="submit" class="btn btn-lg btn-primary" name="yeni_stok_kaydet">Yeni Stok Kaydet</button>
                </div>
            </form>

            <div class="modal-header border-bottom-0">
                <h1 class="modal-title fs-5">Yeni Stok Girişleri</h1>
            </div>
            <div class=""> 
                <div class="table-responsive table-wrapper">
                    <table id="example" class="table table-striped table-bordered second" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Stok Kodu</th>
                                    <th>Adet</th>
                                    <th>Kaydeden</th>
                                    <th>Tarih</th>
                                    <th>Açıklama</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php session();
                                    $kullanicisi = $_SESSION['k_adi'];
                                    $q = $db->prepare("SELECT * FROM yeni_stoklar WHERE stok_kaydeden = '$kullanicisi' ORDER BY time DESC "); $q -> execute(  );
                                    if ( $d = $q->fetchAll() ){ foreach( $d as $k => $row ) { 
                                        ?>
                                        <tr>
                                            <td><?php echo  $row['stok_kodu']; ?></td>
                                            <td><?php echo  $row['stok_adedi']; ?></td>
                                            <td><?php echo  $row['stok_kaydeden']; ?></td>
                                            <td><?php echo  $row['time']; ?></td>
                                            <td><?php echo  $row['aciklama']; ?></td>
                                        </tr>
                                <?php } } ?>
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/js/bootstrap.bundle.min.js"></script>