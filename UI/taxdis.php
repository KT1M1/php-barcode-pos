<?php
include_once "connectdb.php";
session_start();

include_once "header.php";

$message = "";

if(isset($_POST['btnsave'])){
    $sgst = $_POST['txtsgst'];
    $cgst = $_POST['txtcgst'];
    $discount = $_POST['txtdiscount'];

    if(empty($sgst)){
        $message = '<div class="alert alert-danger mb-0">SGST field is empty.</div>';
    }else{
        $insert = $pdo->prepare("INSERT INTO tbl_taxdis (sgst, cgst, discount) VALUES (:sgst,:cgst,:discount )");
        $insert->bindParam(":sgst", $sgst);
        $insert->bindParam(":cgst", $cgst);
        $insert->bindParam(":discount", $discount);

        if($insert->execute()){
            $message = '<div class="alert alert-success mb-0">Tax and Discount added.</div>';
        }else{
            $message = '<div class="alert alert-danger mb-0">Fail.</div>';
        }
    }
}

if(isset($_POST['btnupdate'])){
    $sgst = $_POST['txtsgst'];
    $cgst = $_POST['txtcgst'];
    $discount = $_POST['txtdiscount'];

    $id = $_POST['txtid'];

    if(empty($sgst)){
        $message = '<div class="alert alert-danger mb-0">Field is empty.</div>';
    }else{
        $update = $pdo->prepare("update tbl_taxdis set sgst=:sgst, cgst=:cgst, discount=:dis where taxdis_id=".$id);

        $update->bindParam(":sgst", $sgst);
        $update->bindParam(":cgst", $cgst);
        $update->bindParam(":dis", $discount);

        if($update->execute()){
            $message = '<div class="alert alert-success mb-0">Tax updated.</div>';
        }else{
            $message = '<div class="alert alert-danger mb-0">Tax not updated.</div>';
        }
    }
}
?>
<!--begin::App Main-->
<main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <div class="app-content">
            <!--begin::Container-->
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <!--begin::Header-->
                    <?php if (!empty($message)): ?>
                        <div class="card-body p-0">
                            <?php echo $message; ?>
                        </div>
                    <?php else: ?>
                        <div class="card-header"><div class="card-title">Tax and Discount</div></div>
                    <?php endif; ?>
                    <!--end::Header-->
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row">

                                <?php
                                if(isset($_POST['btnedit'])){
                                    $select = $pdo->prepare("SELECT * FROM tbl_taxdis WHERE taxdis_id =".$_POST['btnedit']);
                                    $select->execute();

                                    if($select){
                                        $row = $select->fetch(PDO::FETCH_OBJ);

                                        echo '
                                        <div class="col-md-4">
                                            <label for="exampleInputEmail1" class="form-label">Tax Form</label>

                                            <input type="hidden" name="txtid" value="'.$row->taxdis_id.'"/>

                                            <div class="mb-3">
                                                <label for="sgst" class="form-label">SGST(%)</label>
                                                <input id="sgst" type="text" class="form-control" name="txtsgst" value="'.$row->sgst.'"/>
                                            </div>

                                            <div class="mb-3">
                                                <label for="cgst" class="form-label">CGST(%)</label>
                                                <input id="cgst" type="text" class="form-control" name="txtcgst" value="'.$row->cgst.'"/>
                                            </div>

                                            <div class="mb-3">
                                                <label for="discount" class="form-label">Discount(%)</label>
                                                <input id="discount" type="text" class="form-control" name="txtdiscount" value="'.$row->discount.'"/>
                                            </div>

                                            <div class="card-footer">
                                                <button type="submit" class="btn btn-primary" name="btnupdate">Update</button>
                                            </div>
                                        </div>';
                                    }
                                } else {
                                    echo '
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="sgst_new" class="form-label">SGST(%)</label>
                                            <input id="sgst_new" type="text" class="form-control" name="txtsgst"/>
                                        </div>
                                        <div class="mb-3">
                                            <label for="cgst_new" class="form-label">CGST(%)</label>
                                            <input id="cgst_new" type="text" class="form-control" name="txtcgst"/>
                                        </div>
                                        <div class="mb-3">
                                            <label for="discount_new" class="form-label">Discount(%)</label>
                                            <input id="discount_new" type="text" class="form-control" name="txtdiscount"/>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary" name="btnsave">Save</button>
                                        </div>
                                    </div>';
                                }
                                ?>

                                <div class="col-md-8">
                                    <table id="table_tax" class="table table-hover">
                                        <thead>
                                        <tr>
                                            <td>#</td>
                                            <td>SGST</td>
                                            <td>CGST</td>
                                            <td>DISCOUNT</td>
                                            <td>EDIT</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $select = $pdo->prepare("SELECT * FROM tbl_taxdis order by taxdis_id desc");
                                        $select->execute();

                                        while($row = $select->fetch(PDO::FETCH_OBJ)) {
                                            echo '
                                                <tr>
                                                    <td>'.$row->taxdis_id.'</td>
                                                    <td>'.$row->sgst.'</td>
                                                    <td>'.$row->cgst.'</td>
                                                    <td>'.$row->discount.'</td>
                                                    <td><button type="submit" class="btn btn-primary" value="'.$row->taxdis_id.'" name="btnedit">Edit</button></td>
                                                </tr>';
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--end::Container-->
        </div>
        <!--end::App Content-->
    </div>
</main>
<!--end::App Main-->
<?php include_once "footer.php"; ?>

<script>
    $(document).ready(function () {
        $('#table_tax').DataTable();
    });
</script>
