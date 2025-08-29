<?php
include_once "connectdb.php";
session_start();

include_once "header.php";

$message = "";
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
                        <div class="card-title">Order List</div>
                    </div>
                <?php endif; ?>
                <!--end::Header-->
                <div class="card-body">
                    <table class="table table-hover" id="table_orderlist">
                        <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Order Date</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Payment Type</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $select = $pdo->prepare("SELECT * FROM tbl_invoice ORDER BY invoice_id DESC");
                        $select->execute();

                        while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                            echo '
                                <tr>
                                    <td>' . $row->invoice_id . '</td>
                                    <td>' . $row->order_date . '</td>
                                    <td>' . $row->total . '</td>
                                    <td>' . $row->paid . '</td>
                                    <td>' . $row->due . '</td>';

                            if($row->payment_type == 'Cash'){
                                echo '<td><span class="btn btn-success">'.$row->payment_type.'</span></td>';
                            }else if($row->payment_type == 'Card'){
                                echo '<td><span class="btn btn-primary">'.$row->payment_type.'</span></td>';
                            }else{
                                echo '<td><span class="btn btn-danger">'.$row->payment_type.'</span></td>';
                            }

                            echo'
                                     <td>
                                        <div class="btn-group" role="group">
                                            <a href="printbill.php?id=' . $row->invoice_id . '" class="btn btn-primary" data-toggle="tooltip" title="Print Bill">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            <a href="editorderpos.php?id=' . $row->invoice_id . '" class="btn btn-info" data-toggle="tooltip" title="Edit Order">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" id=' . $row->invoice_id . ' class="btn btn-danger delete-btn btndelete" data-toggle="tooltip" title="Delete Order">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                    <!--begin::Footer-->
                    <div class="card-footer">
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" name="btnsave">Save Product</button>
                        </div>
                    </div>
                    <!--end::Footer-->
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
                url: 'orderdelete.php',
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