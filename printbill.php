<?php
require('fpdf/fpdf.php');

include_once 'connectdb.php';

$id = $_GET["id"];

// Fetch the invoice header row
$select = $pdo->prepare("select * from tbl_invoice where invoice_id =$id");
$select->execute();
$row = $select->fetch(PDO::FETCH_OBJ);

// ----- PDF SETUP -----
// A4 width : 219mm
// default margin : 10mm each side
// writable horizontal : 219-(10*2)=199mm

// Create pdf object (receipt size)
$pdf = new FPDF('P', 'mm', array(80, 200));

// Add new page
$pdf->AddPage();

// ---- Header ----
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(60, 8, 'CYBARG INC', 1, 1, 'C');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(60, 5, 'PHONE NUMBER : 0755-245998', 0, 1, 'C');
$pdf->Cell(60, 5, 'WEBSITE : WWW.CYBARG.COM', 0, 1, 'C');

// Line(x1,y1,x2,y2)
$pdf->Line(7, 28, 72, 28);
$pdf->Ln(1);

$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(20, 4, 'Bill No:', 0, 0, '');

$pdf->SetFont('Courier', 'BI', 8);
$pdf->Cell(40, 4, $row->invoice_id, 0, 1, '');

$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(20, 4, 'Date:', 0, 0, '');

$pdf->SetFont('Courier', 'BI', 8);
$pdf->Cell(40, 4, $row->order_date, 0, 1, '');

// ---- Table header ----
$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(34, 5, 'PRODUCT', 1, 0, 'C');
$pdf->Cell(11, 5, 'QTY', 1, 0, 'C');
$pdf->Cell(8, 5, 'PRC', 1, 0, 'C');
$pdf->Cell(12, 5, 'TOTAL', 1, 1, 'C');

// ---- Table rows (invoice items) ----
$select = $pdo->prepare("select * from tbl_invoice_details where invoice_id =$id");
$select->execute();

while ($product = $select->fetch(PDO::FETCH_OBJ)) {

    $pdf->SetX(7);
    $pdf->SetFont('Helvetica', 'B', 8);
    $pdf->Cell(34, 5, $product->product_name, 1, 0, 'L');
    $pdf->Cell(7.5, 5, $product->qty, 1, 0, 'C');
    $pdf->Cell(12.5, 5, $product->rate, 1, 0, 'C');
    $pdf->Cell(12.5, 5, $product->rate * $product->qty, 1, 1, 'C');
}

// ---- Totals & Calculations ----
// SUBTOTAL
$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');     // spacing
$pdf->Cell(25, 5, 'SUBTOTAL(Rs)', 1, 0, 'C');
$pdf->Cell(20, 5, $row->subtotal, 1, 1, 'C');

// DISCOUNT %
$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');     // spacing
$pdf->Cell(25, 5, 'DISCOUNT %', 1, 0, 'C');
$pdf->Cell(20, 5, $row->discount, 1, 1, 'C');

// DISCOUNT (Rs) = subtotal * discount%
$discount_rs = ($row->discount / 100) * $row->subtotal;

$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');     // spacing
$pdf->Cell(25, 5, 'DISCOUNT (Rs)', 1, 0, 'C');
$pdf->Cell(20, 5, $discount_rs, 1, 1, 'C');

// SGST %
$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');     // spacing
$pdf->Cell(25, 5, 'SGST %', 1, 0, 'C');
$pdf->Cell(20, 5, $row->sgst, 1, 1, 'C');

// CGST %
$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');     // spacing
$pdf->Cell(25, 5, 'CGST %', 1, 0, 'C');
$pdf->Cell(20, 5, $row->cgst, 1, 1, 'C');

// SGST (Rs)
$sgst_rs = ($row->sgst / 100) * $row->subtotal;

$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');     // spacing
$pdf->Cell(25, 5, 'SGST(Rs)', 1, 0, 'C');
$pdf->Cell(20, 5, $sgst_rs, 1, 1, 'C');

// CGST (Rs)
$cgst_rs = ($row->cgst / 100) * $row->subtotal;

$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');     // spacing
$pdf->Cell(25, 5, 'CGST(Rs)', 1, 0, 'C');
$pdf->Cell(20, 5, $cgst_rs, 1, 1, 'C');

// GRAND TOTAL
$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 10);
$pdf->Cell(20, 5, '', 0, 0, 'L');     // spacing
$pdf->Cell(25, 5, 'G-TOTAL(Rs)', 1, 0, 'C');
$pdf->Cell(20, 5, $row->total, 1, 1, 'C');

// PAID
$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');     // spacing
$pdf->Cell(25, 5, 'PAID(Rs)', 1, 0, 'C');
$pdf->Cell(20, 5, $row->paid, 1, 1, 'C');

// DUE
$pdf->SetX(7);
$pdf->SetFont('courier', 'B', 8);
$pdf->Cell(20, 5, '', 0, 0, 'L');     // spacing
$pdf->Cell(25, 5, 'DUE(Rs)', 1, 0, 'C');
$pdf->Cell(20, 5, $row->due, 1, 1, 'C');

// Spacer
$pdf->Cell(20, 5, '', 0, 1, '');

// ---- Footer / Notes ----
$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(25, 5, 'Important Notice:', 0, 1, '');

$pdf->SetX(7);
$pdf->SetFont('Arial', '', 5);
$pdf->Cell(75, 5, 'No Product Will Be Replaced Or Refunded If You Dont Have Bill With You', 0, 2, '');

$pdf->SetX(7);
$pdf->SetFont('Arial', '', 5);
$pdf->Cell(75, 5, 'You Can Refund Within 2 Days Of Purchase', 0, 2, '');

// Output the PDF
$pdf->Output();
?>