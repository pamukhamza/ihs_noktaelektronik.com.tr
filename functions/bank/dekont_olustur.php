<?php
// Include the TCPDF library
require_once '../../vendor/tcpdf/tcpdf.php';
include_once '../db.php';
require '../../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$config = require '../../aws-config.php';

if (!isset($config['s3']['region']) || !isset($config['s3']['key']) || !isset($config['s3']['secret']) || !isset($config['s3']['bucket'])) {
    die('Missing required S3 configuration values.');
}

$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => $config['s3']['region'],
    'credentials' => [
        'key'    => $config['s3']['key'],
        'secret' => $config['s3']['secret'],
    ]
]);

function dekontOlustur($uye_id, $odeme_id, $ad_soyad, $cardNo, $cardHolder, $taksit_sayisi, $odenentutar, $date) {
    try {
        $database = new Database();
        global $s3Client, $config;

        // Create new PDF instance
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('Nokta Elektronik');
        $pdf->SetAuthor('Nokta Elektronik');
        $pdf->SetTitle('Ödeme Dekontu');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('dejavusans', '', 12);

        // Add company logo
        $logo_path = __DIR__ . '/../../assets/images/logo_new.png';
        if (file_exists($logo_path)) {
            $pdf->Image($logo_path, 10, 20, 40);
        }

        // Document title
        $pdf->SetXY(10, 32);
        $pdf->Cell(0, 10, 'Nokta Elektronik ve Bilişim Sistemleri San. Tic. A.Ş', 0, 0, 'L');
        $pdf->Cell(0, 10, 'DEKONT', 0, 1, 'R');
        $pdf->Line(10, 45, $pdf->getPageWidth() - 10, 45);

        // Payment details
        $pdf->SetXY(10, 70);
        $details = [
            'Firma' => $ad_soyad,
            'Kart Sahibi' => $cardHolder,
            'Kart No' => $cardNo,
            'Taksit Sayısı' => $taksit_sayisi,
            'Ödenen Tutar' => $odenentutar . ' TL'
        ];

        foreach ($details as $label => $value) {
            $pdf->SetX(10);
            $pdf->Cell(35, 10, $label, 0, 0, 'L');
            $pdf->Cell(0, 10, ': ' . $value, 0, 1, 'L');
        }

        $pdf->SetXY(150, 50);
        $pdf->Cell(0, 10, $date, 0, 1, 'R');

        // File name
        $dekont_adi = "dekont_" . uniqid() . ".pdf";

        // Get PDF content as a string (no local saving)
        $pdf_content = $pdf->Output('', 'S'); // 'S' returns PDF as a string

        // Upload to S3
        $bucket = $config['s3']['bucket'];
        $s3_key = 'uploads/dekontlar/' . $dekont_adi;

        $result = $s3Client->putObject([
            'Bucket' => $bucket,
            'Key'    => $s3_key,
            'Body'   => $pdf_content, // Sending PDF content directly
            'ACL'    => 'public-read'
        ]);

        // S3 URL
        $s3_url = $result['ObjectURL'];

        // Save to database
        $islem_no = "COD_" . uniqid();
        $query = "INSERT INTO b2b_dekontlar (uye_id, pos_odeme_id, islem_no, tutar, dekont, dekont_url, tarih) 
                 VALUES (:uye_id, :pos_odeme_id, :islem_no, :tutar, :dekont, :dekont_url, :tarih)";
        $params = [
            'uye_id' => $uye_id,
            'pos_odeme_id' => $odeme_id,
            'islem_no' => $islem_no,
            'tutar' => $odenentutar,
            'dekont' => $dekont_adi,
            'dekont_url' => $s3_url,
            'tarih' => $date
        ];
        $database->insert($query, $params);

        return true;
    } catch (Exception $e) {
        error_log('Dekont oluşturma veya S3 yükleme hatası: ' . $e->getMessage());
        throw new Exception('Dekont oluşturulurken bir hata oluştu: ' . $e->getMessage());
    }
}
?>
