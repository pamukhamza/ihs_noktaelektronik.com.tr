<?php include "functions.php"; 
session();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">
    <title>Depo Seçimi</title>
</head>
<body>
    <?php adminheaders(); ?>
    <div class="modal modal-sheet position-static d-block bg-body-secondary p-4 py-md-5" tabindex="-1" role="dialog" id="modalSheet">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header border-bottom-0">
                    <h1 class="modal-title fs-5">Depo Seçiniz</h1>
                </div>
                <div class="modal-footer flex-column align-items-stretch w-100 gap-2 pb-3 border-top-0">
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Depolar</label>
                        <select class="form-control" id="exampleFormControlSelect1">
                            <option value="00">Seçiniz</option>
                            <?php $q = $db->prepare("SELECT * FROM depo "); $q -> execute(  );       
                            if ( $d = $q->fetchAll() ){ foreach( $d as $k => $depo ) { ?>
                                <option value="<?php echo  $depo['id']; ?>"><?php echo  $depo['depo_adi']; ?></option>
                            <?php }} ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer flex-column align-items-stretch w-100 gap-2 pb-3 border-top-0">
                    <button type="button" class="btn btn-lg btn-primary" id="buton">Stok Sayımına Devam Et</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/js/bootstrap.bundle.min.js"></script>
    <script>
        var selectedDepoID = null;

        $(document).on('change', '#exampleFormControlSelect1', function() {
            selectedDepoID = $(this).val();
        });

        $(document).on('click', '#buton', function() {
            if (selectedDepoID !== null) {
                // Seçilen depo ID'sini kullanarak detay sayfasına yönlendir.
                window.location.href = 'admin?id=' + selectedDepoID + '&s=0';
            } else {
                alert('Lütfen bir depo seçin.');
            }
        });
    </script>
</body>
</html>
