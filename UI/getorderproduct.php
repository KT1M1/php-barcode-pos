<?php

include_once 'connectdb.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT
          a.id,
          a.invoice_id,
          a.barcode,
          a.product_id,
          a.product_name,
          a.qty,
          a.rate,           -- unit price
          a.saleprice,      -- line total (rate * qty)
          a.order_date,
          COALESCE(p.stock, 0) AS stock
        FROM tbl_invoice_details a
        LEFT JOIN tbl_product p
          ON a.product_id = p.product_id
        WHERE a.invoice_id = :id
        ORDER BY a.id ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows);
