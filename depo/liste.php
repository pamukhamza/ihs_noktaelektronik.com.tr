<?php include "functions.php";
session();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.4/b-3.1.1/b-colvis-3.1.1/b-html5-3.1.1/b-print-3.1.1/cr-2.0.4/date-1.5.3/fc-5.0.1/fh-4.0.1/r-3.0.2/rg-1.5.0/rr-1.5.0/datatables.min.css" rel="stylesheet">
    <title>Stok Listesi</title>
</head>
<body>
<?php adminheaders(); ?>
<div class="container mt-4">
    <div class="table-responsive">
        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
            <tr>
                <th>Stok Kodu</th>
                <th>Adet</th>
                <th>Açıklama</th>
                <th>Depo Adı</th>
                <th>Kaydeden</th>
                <th>Tarih</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $q = $db->prepare("SELECT stoklar.*, depo.depo_adi FROM stoklar INNER JOIN depo ON stoklar.depo_id = depo.id ORDER BY time DESC");
            $q->execute();
            if ($d = $q->fetchAll()) {
                foreach ($d as $row) {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['stok_kodu']); ?></td>
                        <td><?php echo htmlspecialchars($row['stok_adedi']); ?></td>
                        <td><?php echo htmlspecialchars($row['aciklama']); ?></td>
                        <td><?php echo htmlspecialchars($row['depo_adi']); ?></td>
                        <td><?php echo htmlspecialchars($row['stok_kaydeden']); ?></td>
                        <td><?php echo htmlspecialchars($row['time']); ?></td>
                    </tr>
                <?php  } } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.4/b-3.1.1/b-colvis-3.1.1/b-html5-3.1.1/b-print-3.1.1/cr-2.0.4/date-1.5.3/fc-5.0.1/fh-4.0.1/r-3.0.2/rg-1.5.0/rr-1.5.0/datatables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel olarak indir',
                    title: 'Stok Listesi'
                }
            ],
            "language": {
                "search": "Ara:",
                "lengthMenu": "Göster _MENU_ kayıt",
                "info": "_START_ - _END_ / _TOTAL_ kayıt",
                "infoEmpty": "Kayıt bulunamadı",
                "zeroRecords": "Eşleşen kayıt bulunamadı",
                "paginate": {
                    "first": "İlk",
                    "last": "Son",
                    "next": "Sonraki",
                    "previous": "Önceki"
                }
            }
        });
    });
</script>
</body>
</html>
