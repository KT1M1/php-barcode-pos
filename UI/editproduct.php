<?php
include_once "connectdb.php";
session_start();

include_once "header.php";

$id = $_GET['id'];

$select = $pdo->prepare("SELECT * FROM tbl_product WHERE pid = $id");
$select->execute();

$row = $select->fetch(PDO::FETCH_ASSOC);

$id_db = $row['pid'];

$message = "";

$barcode_db = $row['barcode'];
$productname_db = $row['product'];
$category_db = $row['category'];
$description_db = $row['description'];
$stock_db = $row['stock'];
$purchaseprice_db = $row['purchaseprice'];
$saleprice_db = $row['saleprice'];
$image_db = $row['image'];

if (isset($_POST['btneditproduct'])) {
    $product_txt = $_POST['txtproductname'];
    $category_txt = $_POST['txtselect_option'];
    $description_txt = $_POST['txtdescription'];
    $stock_txt = $_POST['txtstock'];
    $purchaseprice_txt = $_POST['txtpurchaseprice'];
    $saleprice_txt = $_POST['txtsaleprice'];

    $f_name = $_FILES['myfile']['name'];

    if (!empty($f_name)) {

        $f_tmp = $_FILES['myfile']['tmp_name'];
        $f_size = $_FILES['myfile']['size'];
        $extParts = explode('.', $f_name);
        $f_extension = strtolower(end($extParts));
        $f_newfile = uniqid() . '.' . $f_extension;
        $store = "productimages/" . $f_newfile;

        if (in_array($f_extension, ['jpg', 'jpeg', 'png', 'gif'], true)) {
            if ($f_size > 1000000) {
                $message = '<div class="alert alert-danger mb-0">Max 1MB file size allowed.</div>';
            } else {
                if (move_uploaded_file($f_tmp, $store)) {
                    $f_newfile;

                    $update = $pdo->prepare("UPDATE tbl_product set product=:product, category=:category, description=:description, stock=:stock, purchaseprice=:purchaseprice, saleprice=:saleprice, image=:image where pid=$id");

                    $update->bindParam(":product", $product_txt);
                    $update->bindParam(":category", $category_txt);
                    $update->bindParam(":description", $description_txt);
                    $update->bindParam(":stock", $stock_txt);
                    $update->bindParam(":purchaseprice", $purchaseprice_txt);
                    $update->bindParam(":saleprice", $saleprice_txt);
                    $update->bindParam(":image", $f_newfile);

                    if ($update->execute()) {
                        $message = '<div class="alert alert-success mb-0">Update successful with new Image</div>';
                    } else {
                        $message = '<div class="alert alert-danger mb-0">Error</div>';
                    }
                }
            }
        }
    } else {
        $update = $pdo->prepare("UPDATE tbl_product set product=:product, category=:category, description=:description, stock=:stock, purchaseprice=:purchaseprice, saleprice=:saleprice, image=:image where pid=$id");

        $update->bindParam(":product", $product_txt);
        $update->bindParam(":category", $category_txt);
        $update->bindParam(":description", $description_txt);
        $update->bindParam(":stock", $stock_txt);
        $update->bindParam(":purchaseprice", $purchaseprice_txt);
        $update->bindParam(":saleprice", $saleprice_txt);
        $update->bindParam(":image", $image_db);

        if ($update->execute()) {
            $message = '<div class="alert alert-success mb-0">Update success</div>';
        } else {
            $message = '<div class="alert alert-danger mb-0">Error</div>';
        }
    }

}

$select = $pdo->prepare("SELECT * FROM tbl_product WHERE pid = $id");
$select->execute();

$row = $select->fetch(PDO::FETCH_ASSOC);

$id_db = $row['pid'];

$message = "";

$barcode_db = $row['barcode'];
$productname_db = $row['product'];
$category_db = $row['category'];
$description_db = $row['description'];
$stock_db = $row['stock'];
$purchaseprice_db = $row['purchaseprice'];
$saleprice_db = $row['saleprice'];
$image_db = $row['image'];

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
                        <div class="card-title">Edit Product</div>
                    </div>
                <?php endif; ?>
                <!--end::Header-->
                <div class="card-body">
                    <form action="" method="post" name="formeditproduct" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Barcode</label>
                                    <input type="text" class="form-control" name="txtbarcode"
                                           value="<?php echo $barcode_db; ?>" placeholder="<?php echo $barcode_db; ?>"
                                           disabled/>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Product Name</label>
                                    <input type="text" class="form-control" name="txtproductname" required
                                           value="<?php echo $productname_db; ?>"/>
                                </div>

                                <div class="form-group">
                                    <label>Category</label>
                                    <select class="form-control" name="txtselect_option" required>
                                        <option value="" disabled selected>Select Category</option>
                                        <?php
                                        $select = $pdo->prepare("SELECT * FROM tbl_category ORDER BY catid DESC");
                                        $select->execute();
                                        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
                                            extract($row);
                                            ?>
                                            <option <?php if ($row['category'] == $category_db) { ?> selected="selected" <?php } ?>>
                                                <?php echo $row['category']; ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="txtdescription" class="form-label">Description</label>
                                    <textarea class="form-control" name="txtdescription"
                                              rows="4"><?php echo $description_db; ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" min="1" step="any" class="form-control" name="txtstock"
                                           required value="<?php echo $stock_db; ?>"/>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Purchase Price</label>
                                    <input type="number" min="1" step="any" class="form-control" name="txtpurchaseprice"
                                           required value="<?php echo $purchaseprice_db; ?>"/>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sale Price</label>
                                    <input type="number" min="1" step="any" class="form-control" name="txtsaleprice"
                                           required value="<?php echo $saleprice_db; ?>"/>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Product Image</label>
                                    <img src="productimages/<?php echo $image_db; ?>" class="img-rounded" width="50"
                                         height="50"/>
                                    <input type="file" class="input-group" name="myfile"/>
                                    <p>Upload image</p>
                                </div>
                            </div>
                        </div>
                        <!--begin::Footer-->
                        <div class="card-footer">
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary" name="btneditproduct">Edit Product
                                </button>
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
