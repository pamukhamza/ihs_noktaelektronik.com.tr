<?php
require '../functions/admin_template.php'; // kendi template dosyan
require '../functions/functions.php';
$currentPage = 'kartvizit';
$template = new Template('Fuar Kartvizit Toplama', $currentPage);

$template->head();

// Kaydetme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firma = htmlspecialchars($_POST['firma']);
    $musteri = htmlspecialchars($_POST['musteri']);
    $telefon = htmlspecialchars($_POST['telefon']);
    $email = htmlspecialchars($_POST['email']);
    $temsilci = htmlspecialchars($_POST['temsilci']);
    $not = htmlspecialchars($_POST['not']);

    // Dosya yolu (aynı klasörde data.txt olarak kaydedecek)
    $file = 'kartvizitler.txt';

    // Görsel yükleme
    $imgPath = '';
    if (isset($_FILES['gorsel']) && $_FILES['gorsel']['error'] === 0) {
        $uploadsDir = 'uploads/';
        if (!file_exists($uploadsDir)) mkdir($uploadsDir, 0777, true);
        $imgPath = $uploadsDir . basename($_FILES['gorsel']['name']);
        move_uploaded_file($_FILES['gorsel']['tmp_name'], $imgPath);
    }

    // Kaydedilecek format: CSV gibi
    $line = [
        date('Y-m-d H:i:s'),
        $firma,
        $musteri,
        $telefon,
        $email,
        $temsilci,
        $not,
        $imgPath
    ];

    file_put_contents($file, implode('|', $line) . PHP_EOL, FILE_APPEND);

    $success = "Kartvizit başarıyla kaydedildi!";
}
?>

<body>
<?php $template->header(); ?>

<section class="container mt-4">
    <h2>Fuar Kartvizit Formu</h2>

    <?php if (isset($success)) { ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php } ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="firma" class="form-label">Firma Adı</label>
            <input type="text" class="form-control" name="firma" id="firma" required>
        </div>
        <div class="mb-3">
            <label for="musteri" class="form-label">Müşteri Adı</label>
            <input type="text" class="form-control" name="musteri" id="musteri" required>
        </div>
        <div class="mb-3">
            <label for="telefon" class="form-label">Telefon</label>
            <input type="text" class="form-control" name="telefon" id="telefon" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" name="email" id="email">
        </div>
        <div class="mb-3">
            <label for="temsilci" class="form-label">Satış Temsilcisi</label>
            <select name="temsilci" id="temsilci" class="form-select" required>
                <option value="">Seçiniz</option>
                <option value="Ahmet">Ahmet</option>
                <option value="Mehmet">Mehmet</option>
                <option value="Ayşe">Ayşe</option>
                <option value="Fatma">Fatma</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="not" class="form-label">Not</label>
            <textarea class="form-control" name="not" id="not" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="gorsel" class="form-label">Görsel Ekle</label>
            <input type="file" class="form-control" name="gorsel" id="gorsel" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Kaydet</button>
    </form>
</section>

<?php $template->footer(); ?>
<script src="bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
