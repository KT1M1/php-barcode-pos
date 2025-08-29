<?php
include_once "connectdb.php";
session_start();

include_once "header.php";

$message = "";
?>

<!-- Tempus Dominus (Bootstrap 4 build) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css">

<!-- Date Range Picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">

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
                        <div class="card-title">Table Report</div>
                    </div>
                <?php endif; ?>
                <!--end::Header-->

                <form action="" method="post" name="">

                <div class="card-body">
                    <div class="row">

                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Date:</label>
                                <div class="input-group date" id="date_1" data-target-input="nearest">
                                    <input type="text" class="form-control date_1"
                                           data-target="#date_1" name="date_1"/>
                                    <div class="input-group-append" data-target="#date_1"
                                         data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="bi bi-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Date:</label>
                            <div class="input-group date" id="date_2" data-target-input="nearest">
                                <input type="text" class="form-control date_2"
                                       data-target="#date_2" name="date_2"/>
                                <div class="input-group-append date_2" data-target="#date_2"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="bi bi-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" name="btnfilter">Filter Records</button>
                        </div>
                    </div>
                </div> <!-- card body end -->

                    <table class="table table-hover" id="table_report">
                        <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Order Date</th>
                            <th>Subtotal</th>
                            <th>SGST</th>
                            <th>CGST</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Payment Type</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $select = $pdo->prepare("SELECT * FROM tbl_invoice where order_date between :fromdate AND :todate");
                        $select->bindParam(':fromdate', $_POST['date_1']);
                        $select->bindParam(':todate', $_POST['date_2']);
                        $select->execute();

                        while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                            echo '
                                <tr>
                                    <td>' . $row->invoice_id . '</td>
                                    <td>' . $row->order_date . '</td>
                                    <td>' . $row->subtotal . '</td>
                                    <td>' . $row->sgst . '</td>
                                    <td>' . $row->cgst . '</td>
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

                </form>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>

<!-- Tempus Dominus (Bootstrap 4 jQuery plugin) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Date Range Picker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<!--end::App Main-->
<?php include_once "footer.php"; ?>

<script>
    $(function () {
        $('#date_1').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $('#date_2').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });
</script>

<script>
    $(document).ready(function () {
        $("#table_report").DataTable({
            "order": [[0, "desc"]]
        });
    });
</script>

