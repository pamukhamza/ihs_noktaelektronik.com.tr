<?php
// Include the TCPDF library
require_once '../../vendor/tcpdf/tcpdf.php';
include_once '../db.php';

// Function to generate PDF receipt
function dekontOlustur($uye_id, $odeme_id, $ad_soyad, $cardNo, $cardHolder, $taksit_sayisi, $odenentutar, $date) {
    try {
        $database = new Database();
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

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('dejavusans', '', 12);

        // Add company logo
        $logo_path = __DIR__ . '/../../assets/images/logo_new.png';
        if (file_exists($logo_path)) {
            $pdf->Image($logo_path, 10, 20, 40);
        }

        // Company name and document title
        $pdf->SetXY(10, 32);
        $pdf->Cell(0, 10, 'Nokta Elektronik ve Bilişim Sistemleri San. Tic. A.Ş', 0, 0, 'L');
        $pdf->Cell(0, 10, 'DEKONT', 0, 1, 'R');

        // Draw a horizontal line
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

        // Add date
        $pdf->SetXY(150, 50);
        $pdf->Cell(0, 10, $date, 0, 1, 'R');

        // Generate unique filename
        $dekont_adi = "dekont" . uniqid() . ".pdf";
        
        // Set the complete file path
        $upload_dir = __DIR__ . '/../../assets/uploads/dekontlar/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $dekont_yolu = $upload_dir . $dekont_adi;

        // Output the PDF
        $pdf->Output($dekont_yolu, 'F');

        // Generate unique transaction number
        $islem_no = "COD_" . uniqid();

        // Save to database
        $query = "INSERT INTO b2b_dekontlar (uye_id, pos_odeme_id, islem_no, tutar, dekont, tarih) 
                 VALUES (:uye_id, :pos_odeme_id, :islem_no, :tutar, :dekont, :tarih)";
        $params = [
            'uye_id' => $uye_id,
            'pos_odeme_id' => $odeme_id,
            'islem_no' => $islem_no,
            'tutar' => $odenentutar,
            'dekont' => $dekont_adi,
            'tarih' => $date
        ];
        $database->insert($query, $params);

        return true;
    } catch (Exception $e) {
        error_log('Dekont oluşturma hatası: ' . $e->getMessage());
        throw new Exception('Dekont oluşturulurken bir hata oluştu: ' . $e->getMessage());
    }
}
?>