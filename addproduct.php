<?php
include_once "connectdb.php";
session_start();

include_once "header.php";

$message = "";

if (isset($_POST['btnsave'])) {
    $barcode       = $_POST['txtbarcode'];
    $productname   = $_POST['txtproductname'];
    $category      = $_POST['txtselect_option'];
    $description   = $_POST['txtdescription'];
    $stock         = $_POST['txtstock'];
    $purchaseprice = $_POST['txtpurchaseprice'];
    $saleprice     = $_POST['txtsaleprice'];

    // File Upload
    if (!empty($_FILES['myfile']['name']) && $_FILES['myfile']['error'] === UPLOAD_ERR_OK) {
        $f_name      = $_FILES['myfile']['name'];
        $f_tmp       = $_FILES['myfile']['tmp_name'];
        $f_size      = $_FILES['myfile']['size'];
        $extParts    = explode('.', $f_name);
        $f_extension = strtolower(end($extParts));
        $f_newfile   = uniqid() . '.' . $f_extension;
        $store       = "productimages/" . $f_newfile;

        if (in_array($f_extension, ['jpg', 'jpeg', 'png', 'gif'], true)) {
            if ($f_size > 1000000) {
                $message = '<div class="alert alert-danger mb-0">Max 1MB file size allowed.</div>';
            } else {
                if (move_uploaded_file($f_tmp, $store)) {
                    $productimage = $f_newfile;

                    if (empty($barcode)) {

                        $insert = $pdo->prepare("
                             INSERT INTO tbl_product (product, category, description, stock, purchaseprice, saleprice, image) 
                            VALUES (:product, :category, :description, :stock, :purchaseprice, :saleprice, :image)");

                        $insert->bindParam(':product', $productname);
                        $insert->bindParam(':category', $category);
                        $insert->bindParam(':description', $description);
                        $insert->bindParam(':stock', $stock);
                        $insert->bindParam(':purchaseprice', $purchaseprice);
                        $insert->bindParam(':saleprice', $saleprice);
                        $insert->bindParam(':image', $productimage);

                        $insert->execute();

                        $pid = $pdo->lastInsertId();

                            date_default_timezone_set("Asia/Calcutta");
                            $newbarcode = $pid . date('his');

                            $update = $pdo->prepare("UPDATE tbl_product SET barcode ='$newbarcode' WHERE pid ='".$pid."'");

                            if ($update->execute()) {
                                $message = '<div class="alert alert-success mb-0">Barcode added successfully.</div>';
                            } else {
                                $message = '<div class="alert alert-danger mb-0">Something went wrong, please try again.</div>';
                            }


                    } else {
                        $insert = $pdo->prepare("
                            INSERT INTO tbl_product (barcode, product, category, description, stock, purchaseprice, saleprice, image) 
                            VALUES (:barcode, :product, :category, :description, :stock, :purchaseprice, :saleprice, :image)");

                        $insert->bindParam(':barcode', $barcode);
                        $insert->bindParam(':product', $productname);
                        $insert->bindParam(':category', $category);
                        $insert->bindParam(':description', $description);
                        $insert->bindParam(':stock', $stock);
                        $insert->bindParam(':purchaseprice', $purchaseprice);
                        $insert->bindParam(':saleprice', $saleprice);
                        $insert->bindParam(':image', $productimage);

                        if ($insert->execute()) {
                            $message = '<div class="alert alert-success mb-0">Product added successfully.</div>';
                        } else {
                            $message = '<div class="alert alert-danger mb-0">Failed to add product to database.</div>';
                        }
                    }
                } else {
                    $message = '<div class="alert alert-danger mb-0">Failed to move uploaded file.</div>';
                }
            }
        } else {
            $message = '<div class="alert alert-danger mb-0">Invalid file type. Only JPG, JPEG, PNG, GIF allowed.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger mb-0">No file uploaded or upload error occurred.</div>';
    }
}
?>

<!--begin::App Main-->
<main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
    </div>
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
                    <div class="card-header">
                        <div class="card-title">Add Product</div>
                    </div>
                <?php endif; ?>
                <!--end::Header-->
                <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Barcode</label>
                                    <input type="text" class="form-control" name="txtbarcode"/>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Product Name</label>
                                    <input type="text" class="form-control" name="txtproductname" required/>
                                </div>
                                <div class="form-group">
                                    <label>Category</label>
                                    <select class="form-control" name="txtselect_option" required>
                                        <option value="" disabled selected>Select Category</option>
                                        <?php
                                        $select = $pdo->prepare("SELECT * FROM tbl_category ORDER BY catid DESC");
                                        $select->execute();
                                        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<option>' . htmlspecialchars($row['category']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="txtdescription" class="form-label">Description</label>
                                    <textarea class="form-control" name="txtdescription" rows="4"></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" min="1" step="any" class="form-control" name="txtstock" required/>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Purchase Price</label>
                                    <input type="number" min="1" step="any" class="form-control" name="txtpurchaseprice" required/>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sale Price</label>
                                    <input type="number" min="1" step="any" class="form-control" name="txtsaleprice" required/>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Product Image</label>
                                    <input type="file" class="input-group" name="myfile" required/>
                                    <p>Upload image</p>
                                </div>
                            </div>
                        </div>
                        <!--begin::Footer-->
                        <div class="card-footer">
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary" name="btnsave">Save Product</button>
                            </div>
                        </div>
                        <!--end::Footer-->
                    </form>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
</main>
<!--end::App Main-->
<?php include_once "footer.php"; ?>
