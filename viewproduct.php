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
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">View Product</h3></div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <!--begin::Container-->
        <div class="card card-primary card-outline">
            <!--begin::Header-->
            <?php if (!empty($message)): ?>
                <div class="card-body p-0">
                    <?php echo $message; ?>
                </div>
            <?php else: ?>
                <div class="card-header">
                    <div class="card-title">View Product</div>
                </div>
            <?php endif; ?>
            <!--end::Header-->
            <div class="card-body">

                <?php
                $id = $_GET['id'];

                $select = $pdo->prepare("SELECT * FROM tbl_product WHERE pid = $id");
                $select->execute();

                while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                    echo'
                    <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group">
                            <center><p class="list-group-item list-group-item-info"><b>Product Details</b></p></center>
                            <li class="list-group-item">barcode<span class="badge text-black">'.bar128($row->barcode).'</span></li>
                            <li class="list-group-item">product<span class="badge text-black">'.$row->product.'</span></li>
                            <li class="list-group-item">category<span class="badge text-black">'.$row->category.'</span></li>
                            <li class="list-group-item">description<span class="badge text-black">'.$row->description.'</span></li>
                            <li class="list-group-item">stock<span class="badge text-black">'.$row->stock.'</span></li>
                            <li class="list-group-item">purchaseprice<span class="badge text-black">'.$row->purchaseprice.'</span></li>
                            <li class="list-group-item">saleprice<span class="badge text-black">'.$row->saleprice.'</span></li>
                            <li class="list-group-item">profit<span class="badge text-black">'.($row->saleprice - $row->purchaseprice).'</span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group">
                            <center><p class="list-group-item list-group-item-info"><b>Product Image</b></p></center>
                            <img src="productimages/'.$row->image.'" class="img-responsive" alt="img" style="max-width: 250px">
                        </ul>
                    </div>
                </div>
                    ';
                }

                ?>

            </div>
        </div>
    </div>
    <!--end::Container-->
</main>
<!--end::App Main-->
<?php include_once "footer.php"; ?>
