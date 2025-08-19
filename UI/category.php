<?php
include_once "connectdb.php";
session_start();

include_once "header.php";

$message = "";

if(isset($_POST['btnsave'])){
    $category = $_POST['txtcategory'];

    if(empty($category)){
        $message = '<div class="alert alert-danger mb-0">Category field is empty.</div>';
    }else{
        $insert = $pdo->prepare("INSERT INTO tbl_category (category) VALUES (:cat)");
        $insert->bindParam(":cat", $category);

        if($insert->execute()){
            $message = '<div class="alert alert-success mb-0">Category added.</div>';
        }else{
            $message = '<div class="alert alert-danger mb-0">Category not added.</div>';
        }
    }
}

if(isset($_POST['btnupdate'])){
    $category = $_POST['txtcategory'];
    $id = $_POST['txtcatid'];

    if(empty($category)){
        $message = '<div class="alert alert-danger mb-0">Category field is empty.</div>';
    }else{
        $update = $pdo->prepare("update tbl_category set category=:cat where catid=".$id);
        $update->bindParam(":cat", $category);

        if($update->execute()){
            $message = '<div class="alert alert-success mb-0">Category updated.</div>';
        }else{
            $message = '<div class="alert alert-danger mb-0">Category not updated.</div>';
        }
    }
}

if(isset($_POST['btndelete'])){
    $delete = $pdo->prepare("DELETE FROM tbl_category WHERE catid=".$_POST['btndelete']);

    if($delete->execute()){
        $message = '<div class="alert alert-success mb-0">Category deleted.</div>';
    }else{
        $message = '<div class="alert alert-danger mb-0">Category not deleted.</div>';
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
                    <div class="card-header"><div class="card-title">Category</div></div>
                <?php endif; ?>
                <!--end::Header-->
                <div class="card-body">
                    <form action="" method="post">
                    <div class="row">

                        <?php
                        if(isset($_POST['btnedit'])){
                            $select = $pdo->prepare("SELECT * FROM tbl_category WHERE catid =".$_POST['btnedit']);
                            $select->execute();

                            if($select){
                                $row = $select->fetch(PDO::FETCH_OBJ);

                                echo'
                                <div class="col-md-4">
                                
                                    <label for="exampleInputEmail1" class="form-label">Category</label>
                                
                                    <div class="mb-3">
                                        <input type="hidden" class="form-control" name="txtcatid" value="'.$row->catid.'"/>
                                    </div>
                                
                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="txtcategory" value="'.$row->category.'"/>
                                    </div>
                                    <!--begin::Footer-->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary" name="btnupdate">Update</button>
                                    </div>
                                    <!--end::Footer-->
                                </div>';
                            }

                        }else{
                            echo'
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="exampleInputEmail1" class="form-label">Category</label>
                                        <input type="text" class="form-control" name="txtcategory"/>
                                    </div>
                                    <!--begin::Footer-->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary" name="btnsave">Save</button>
                                    </div>
                                    <!--end::Footer-->
                                </div>';
                        }

                        ?>

                        <div class="col-md-8">
                            <table id="table_category" class="table table-hover">
                                <thead>
                                <tr>
                                    <td>#</td>
                                    <td>Category</td>
                                    <td>Edit</td>
                                    <td>Delete</td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $select = $pdo->prepare("SELECT * FROM tbl_category order by catid desc");
                                $select->execute();

                                while($row = $select->fetch(PDO::FETCH_OBJ)) {
                                    echo '
                                                <tr>
                                                <td>'.$row->catid.'</td>
                                                <td>'.$row->category.'</td>
                                                <td><button type="submit" class="btn btn-primary" value="'.$row->catid.'" name="btnedit">Edit</button></td>
                                                <td><button type="submit" class="btn btn-danger" value="'.$row->catid.'" name="btndelete">Delete</button></td>
                                                </tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
</main>
<!--end::App Main-->
<?php include_once "footer.php"; ?>

<script>
    $(document).ready( function () {
        $('#table_category').DataTable();
    } );

</script>
