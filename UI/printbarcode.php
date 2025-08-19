<?php
include_once "connectdb.php";
session_start();
include_once "header.php";
include 'barcode/barcode128.php';
?>
<!--begin::App Main-->
<main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12"><h3 class="mb-0">Generate Barcode Stickers:</h3></div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">

                <?php if (!empty($message)): ?>
                    <div class="card-body p-0"><?php echo $message; ?></div>
                <?php else: ?>
                    <div class="card-header">
                        <div class="card-title">View Product</div>
                    </div>
                <?php endif; ?>

                <div class="card-body">
                    <form class="form-horizontal" method="post" action="barcode/barcode.php" target="_blank">

                        <?php
                        $id = $_GET['id'];
                        $select = $pdo->prepare("select * from tbl_product where pid=$id");
                        $select->execute();

                        while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                            ?>
                            <div class="row">

                                <!-- LEFT: Print Barcode -->
                                <div class="col-md-6">
                                    <div class="list-group mb-3">
                                        <div class="list-group-item list-group-item-info text-center">
                                            <b>Print Barcode</b>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="product">Product:</label>
                                        <div class="col-sm-10">
                                            <input autocomplete="off" type="text" class="form-control" id="product" name="product" value="<?php echo $row->product; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="product_id">Product ID:</label>
                                        <div class="col-sm-10">
                                            <input autocomplete="off" type="text" class="form-control" id="barcode" name="barcode" value="<?php echo $row->barcode; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="rate">Sale Price</label>
                                        <div class="col-sm-10">
                                            <input autocomplete="off" type="text" class="form-control" id="rate" name="rate" value="<?php echo $row->saleprice; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="rate">Stock</label>
                                        <div class="col-sm-10">
                                            <input autocomplete="off" type="text" class="form-control" id="stock" name="stock" value="<?php echo $row->stock; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-2" for="print_qty">Barcode Quantity</label>
                                        <div class="col-sm-10">
                                            <input autocomplete="off" type="number" min="1" class="form-control" id="print_qty" name="print_qty">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-primary">Generate Barcode</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT: Product Image -->
                                <div class="col-md-6">
                                    <div class="list-group mb-3">
                                        <div class="list-group-item list-group-item-info text-center">
                                            <b>PRODUCT IMAGE</b>
                                        </div>
                                    </div>

                                    <?php if (!empty($row->image)) { ?>
                                        <img src="productimages/<?php echo $row->image; ?>" class="img-responsive" alt="Product image" style="max-width:250px;">
                                    <?php } else { ?>
                                        <div class="text-muted">No image available</div>
                                    <?php } ?>
                                </div>

                            </div><!-- /.row -->
                            <?php
                        }
                        ?>

                    </form>
                </div><!-- /.card-body -->
            </div><!-- /.card -->
        </div><!-- /.container-fluid -->
    </div>
</main>
<?php include_once "footer.php"; ?>
