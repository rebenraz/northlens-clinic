<?php
require('../fpdf/fpdf.php'); // Ensure fpdf.php is available

  include '../db/db.php';

$range = $_GET['range'] ?? 'this_month';
$status = $_GET['status'] ?? 'ALL';

// --- Filter logic ---
switch ($range) {
    case 'this_day':
        $whereDate = "DATE(appointments.date_selected) = CURDATE()";
        break;
    case 'this_week':
        $whereDate = "YEARWEEK(appointments.date_selected, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'this_month':
        $whereDate = "MONTH(appointments.date_selected) = MONTH(CURDATE()) 
                      AND YEAR(appointments.date_selected) = YEAR(CURDATE())";
        break;
    case 'this_year':
        $whereDate = "YEAR(appointments.date_selected) = YEAR(CURDATE())";
        break;
    default:
        $whereDate = "1=1";
        break;
}

$whereStatus = ($status != 'ALL') ? "AND appointments.status = '$status'" : "";

$query = "
  SELECT appointments.price, patients.firstname, patients.lastname, services.name, appointments.date_selected, appointments.status
  FROM appointments
  INNER JOIN patients ON appointments.patient_id = patients.id
  INNER JOIN services ON appointments.service_id = services.id
  WHERE $whereDate $whereStatus
  ORDER BY appointments.date_selected DESC
";

$result = $conn->query($query);

// --- PDF setup ---
class PDF extends FPDF {
    function Header() {
        $logoPath = '../images/northlens-eye.png'; 
        $logoWidth = 25; 

        $pageWidth = $this->GetPageWidth();
        $x = ($pageWidth - $logoWidth) / 2;

        $this->Image($logoPath, $x, 8, $logoWidth);

        $this->Ln(15); 
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 5, 'Northlens Optical Clinic', 0, 1, 'C');

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'Calbayog City, Samar', 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Date: ' . date('F d, Y'), 0, 1, 'C');

        $this->Ln(8);

        $this->SetDrawColor(0, 0, 0); 
        $this->SetLineWidth(0.3); 
        $this->Line(10, $this->GetY(), $pageWidth - 10, $this->GetY()); 

        $this->Ln(5); 
    }

    function TableHeader() {
        $this->SetFont('Arial','B',12);
        $this->SetFillColor(220, 230, 241);
        $this->Cell(40,10,'Name',1,0,'C',true);
        $this->Cell(50,10,'Type',1,0,'C',true);
        $this->Cell(25,10,'Price',1,0,'C',true);
        $this->Cell(35,10,'Date',1,0,'C',true);
        $this->Cell(35,10,'Status',1,1,'C',true);
    }

    // custom row with wrapping text
    function Row($data) {
        $lineHeight = 8;
        $cellWidths = [40, 50, 25, 35, 35];

        // Compute max line count
        $nb = 0;
        for($i=0;$i<count($data);$i++)
            $nb = max($nb, $this->NbLines($cellWidths[$i], $data[$i]));
        $h = $lineHeight * $nb;

        $this->CheckPageBreak($h);

        for($i=0;$i<count($data);$i++) {
            $w = $cellWidths[$i];
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, 8, $data[$i], 0, 'L');
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
        if($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt) {
        $cw = &$this->CurrentFont['cw'];
        if($w==0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while($i < $nb) {
            $c = $s[$i];
            if($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if($l > $wmax) {
                if($sep == -1) {
                    if($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->TableHeader();
$pdf->SetFont('Arial','',11);

while ($row = $result->fetch_assoc()) {
    $pdf->Row([
        ucwords($row['firstname'].' '.$row['lastname']),
        $row['name'], 
        number_format((float)$row['price'], 2),
        date('m/d/Y', strtotime($row['date_selected'])),
        $row['status']
    ]);
}

$pdf->Output('I', 'appointment-history.pdf');
exit;
?>
