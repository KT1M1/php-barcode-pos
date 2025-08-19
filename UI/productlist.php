<?php
include_once "connectdb.php";
session_start();
include_once "header.php";
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
                        <div class="card-title">Product List</div>
                    </div>
                <?php endif; ?>
                <!--end::Header-->
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Stock</th>
                            <th>Purchase Price</th>
                            <th>Sale Price</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $select = $pdo->prepare("SELECT * FROM tbl_product ORDER BY pid DESC");
                        $select->execute();

                        while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                            echo '
                                <tr>
                                    <td>' . $row->barcode . '</td>
                                    <td>' . $row->product . '</td>
                                    <td>' . $row->category . '</td>
                                    <td>' . $row->description . '</td>
                                    <td>' . $row->stock . '</td>
                                    <td>' . $row->purchaseprice . '</td>
                                    <td>' . $row->saleprice . '</td>
                                    <td><img src="productimages/' . $row->image . '" class="img-rounded" width="40" height="40" /></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="printbarcode.php?id=' . $row->pid . '" class="btn btn-primary btn-xs" data-toggle="tooltip" title="Print Barcode">
                                                <i class="bi bi-upc"></i>
                                            </a>
                                            <a href="viewproduct.php?id=' . $row->pid . '" class="btn btn-warning btn-xs" data-toggle="tooltip" title="View Product">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="editproduct.php?id=' . $row->pid . '" class="btn btn-info btn-xs" data-toggle="tooltip" title="Edit Product">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" id=' . $row->pid . ' class="btn btn-danger btn-xs delete-btn btndelete" data-toggle="tooltip" title="Delete Product">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
</main>
<!--end::App Main-->
<?php include_once "footer.php"; ?>

<script>
    $(document).ready(function(){
        $('.btndelete').click(function(){
            var tdh = $(this);
            var id = $(this).attr("id");

            $.ajax({
                url: 'productdelete.php',
                type: "POST",
                data: {
                    pidd: id
                },
                success: function(data){
                    tdh.parents('tr').hide();
                }
            });
        });
    });

</script>
