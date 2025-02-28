<?php
// Include the TCPDF library
require_once('../assets/tcpdf/tcpdf.php');
require_once '../db.php';
$db = new Database();

// Function to generate PDF receipt
function dekontOlustur($uye_id, $odeme_id, $ad_soyad, $cardNo, $cardHolder, $taksit_sayisi, $odenentutar, $date) {
    global $db;
    // Create new PDF instance
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('Your Company');
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Receipt');
    $pdf->SetSubject('Payment Receipt');
    $pdf->SetKeywords('Receipt, Payment');

    // Set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('dejavusans', '', 12);

    // Add company logo and name
    $pdf->Image('../assets/images/logo_new.png', 10, 20, 40, '', 'PNG');
    $pdf->SetXY(10, 32); // Move to the right of the logo
    // İlk hücreyi yazdır
    $pdf->Cell(0, 10, 'Nokta Elektronik ve Bilişim Sistemleri San. Tic. A.Ş', 0, 0, 'L');

    // İlk hücrenin sonundaki X konumunu al
    $firstCellX = $pdf->GetX();

    // İkinci hücreyi, ilk hücrenin sonundan başlayarak yazdır
    $pdf->SetX($firstCellX);
    $pdf->Cell(0, 10, 'DEKONT', 0, 1, 'R');

    // Draw a horizontal line
    $pdf->SetLineWidth(0.1);
    $pdf->Line(10, 45, $pdf->getPageWidth() - 10, 45);

    // Payer information and paid amount
    $pdf->SetXY(10, 70); // Move down below the line
    $pdf->SetX(10);
    $pdf->Cell(35, 10, 'Firma', 0, 0, 'L');
    $pdf->Cell(0, 10, ': ' . $ad_soyad, 0, 1, 'L');
    $pdf->SetX(10);
    $pdf->Cell(35, 10, 'Kart Sahibi', 0, 0, 'L');
    $pdf->Cell(0, 10, ': ' . $cardHolder, 0, 1, 'L');
    $pdf->SetX(10);
    $pdf->Cell(35, 10, 'Kart No', 0, 0, 'L');
    $pdf->Cell(0, 10, ': ' . $cardNo, 0, 1, 'L');
    $pdf->SetX(10);
    $pdf->Cell(35, 10, 'Taksit Sayısı', 0, 0, 'L');
    $pdf->Cell(0, 10, ': ' . $taksit_sayisi, 0, 1, 'L');
    $pdf->SetX(10);
    $pdf->Cell(35, 10, 'Ödenen Tutar', 0, 0, 'L');
    $pdf->Cell(0, 10, ': ' . $odenentutar . ' TL', 0, 1, 'L');

    // Add today's date and hour
    $pdf->SetXY(150, 50); // Move to the top-right corner
    $pdf->Cell(0, 10, $date, 0, 1, 'R');

    $dekont_adi = "dekont" . uniqid() . ".pdf";
    $dekont_yolu = realpath("../assets/uploads/dekontlar/") . "/" . $dekont_adi;

    // Output the PDF as a file (you can also use other output methods)
    $pdf->Output($dekont_yolu, 'F');

    $islem_no = "COD_" . uniqid();

    $db->insert("INSERT INTO dekontlar (uye_id, pos_odeme_id, islem_no, tutar, dekont, tarih) VALUES (:uye_id, :pos_odeme_id, :islem_no, :tutar, :dekont, :tarih)", [
        'uye_id' => $uye_id,
        'pos_odeme_id' => $odeme_id,
        'islem_no' => $islem_no,
        'tutar' => $odenentutar,
        'dekont' => $dekont_adi,
        'tarih' => $date
    ]);
}
?>
