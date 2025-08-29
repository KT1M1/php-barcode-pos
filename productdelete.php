<?php
include_once 'connectdb.php';

$id=$_POST['pidd'];
$sql="DELETE FROM tbl_product WHERE pid=$id";

$delete=$pdo->prepare($sql);

$delete->bindParam(":id",$id);

if($delete->execute()){

}else{
    $message = '<div class="alert alert-danger mb-0">Error in deleting product.</div>';
}
?>