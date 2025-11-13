<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../functions/admin_template.php';
require '../functions/functions.php';

$currentPage = 'kartvizit_listesi';
$template = new Template('Kartvizit Listesi', $currentPage);
$template->head();

// --- Dosyadan verileri oku ---
$file = 'kartvizitler.txt';
$records = [];

if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode('|', $line);
        // Boş alanlar da gösterilecek
        $records[] = [
            'tarih'    => $parts[0] ?? '',
            'firma'    => $parts[1] ?? '',
            'musteri'  => $parts[2] ?? '',
            'telefon'  => $parts[3] ?? '',
            'email'    => $parts[4] ?? '',
            'temsilci' => $parts[5] ?? '',
            'not'      => $parts[6] ?? '',
            'gorsel'   => $parts[7] ?? ''
        ];
    }
}

// Satış temsilcilerini benzersiz şekilde listele
$temsilciler = array_unique(array_filter(array_column($records, 'temsilci')));
sort($temsilciler);
?>

<body>
<?php $template->header(); ?>

<section class="container mt-4">
    <h2>Satış Temsilcilerine Göre Kartvizit Listesi</h2>

    <div class="mb-3 mt-3">
        <label for="temsilciSec" class="form-label">Satış Temsilcisi Seç</label>
        <select id="temsilciSec" class="form-select">
            <option value="">Tümünü Göster</option>
            <?php foreach ($temsilciler as $t) { ?>
                <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
            <?php } ?>
        </select>
    </div>

    <table class="table table-bordered table-striped mt-4" id="kartvizitTablo">
        <thead class="table-dark table-responsive">
            <tr>
                <th>Tarih</th>
                <th>Firma</th>
                <th>Müşteri</th>
                <th>Telefon</th>
                <th>E-mail</th>
                <th>Temsilci</th>
                <th>Not</th>
                <th>Görsel</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $r) { ?>
                <tr data-temsilci="<?= htmlspecialchars($r['temsilci']) ?>">
                    <td><?= htmlspecialchars($r['tarih']) ?></td>
                    <td><?= htmlspecialchars($r['firma']) ?></td>
                    <td><?= htmlspecialchars($r['musteri']) ?></td>
                    <td><?= htmlspecialchars($r['telefon']) ?></td>
                    <td><?= htmlspecialchars($r['email']) ?></td>
                    <td><?= htmlspecialchars($r['temsilci']) ?></td>
                    <td><?= htmlspecialchars($r['not']) ?></td>
                    <td>
                        <?php if (!empty($r['gorsel'])) { ?>
                            <a href="<?= htmlspecialchars($r['gorsel']) ?>" target="_blank">
                                <img src="<?= htmlspecialchars($r['gorsel']) ?>" alt="Görsel" style="width:60px; height:auto;">
                            </a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>

<?php $template->footer(); ?>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
<script>
// Satış temsilcisine göre filtreleme
document.getElementById('temsilciSec').addEventListener('change', function() {
    const secilen = this.value.toLowerCase();
    document.querySelectorAll('#kartvizitTablo tbody tr').forEach(tr => {
        const temsilci = tr.getAttribute('data-temsilci').toLowerCase();
        tr.style.display = (secilen === '' || temsilci === secilen) ? '' : 'none';
    });
});
</script>

</body>
</html>
