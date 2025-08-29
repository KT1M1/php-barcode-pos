<?php

include_once 'connectdb.php';

$id=$_POST['pidd'];

$select=$pdo->prepare("select * from tbl_invoice_details where invoice_id=$id");
$select->execute();
$row_invoice_details=$select->fetchAll(PDO::FETCH_ASSOC);

foreach ($row_invoice_details as $row_invoice_detail) {
    $updateproduct_stock=$pdo->prepare("update tbl_product set stock=stock+".$row_invoice_detail['qty']." where pid=".$row_invoice_detail['product_id']);
    $updateproduct_stock->execute();
}

$sql="delete tbl_invoice,tbl_invoice_details from tbl_invoice 
INNER JOIN tbl_invoice_details ON tbl_invoice.invoice_id = tbl_invoice_details.invoice_id 
where tbl_invoice.invoice_id=$id";

$delete=$pdo->prepare($sql);

if($delete->execute()){

}else{
    echo "Error deleting record";
}
?>