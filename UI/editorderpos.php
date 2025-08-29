<?php
include_once "connectdb.php";
session_start();

include_once "header.php";

function fill_product($pdo)
{
    $output = '';
    $select = $pdo->prepare("SELECT * FROM tbl_product order by product ASC");;
    $select->execute();

    $result = $select->fetchAll();

    foreach ($result as $row) {
        $output .= '<option value="' . $row["product_id"] . '">' . $row["product"] . '</option>';
    }
    return $output;
}

$id = $_GET["id"];
$select=$pdo->prepare("SELECT * FROM tbl_invoice WHERE invoice_id='$id'");
$select->execute();
$row = $select->fetch(PDO::FETCH_ASSOC);

$order_date=date("Y-m-d", strtotime($row['order_date']));

$subtotal=$row['subtotal'];
$sgst=$row['sgst'];
$cgst=$row['cgst'];
$discount=$row['discount'];
$total=$row['total'];
$paid=$row['paid'];
$due=$row['due'];
$payment_type=$row['payment_type'];

$select=$pdo->prepare("SELECT * FROM tbl_invoice_details WHERE invoice_id='$id'");
$select->execute();
$row_invoice_details = $select->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['btnupdateorder'])) {
    $txt_orderdate = date('Y-m-d');
    $txt_subtotal = $_POST['txtsubtotal'];
    $txt_discount = $_POST['txtdiscount'];
    $txt_sgst = $_POST['txtsgst'];
    $txt_cgst = $_POST['txtcgst'];
    $txt_total = $_POST['txttotal'];
    $txt_payment_type = $_POST['rb'];
    $txt_due = $_POST['txtdue'];
    $txt_paid = $_POST['txtpaid'];

    $arr_pid = $_POST['pid_arr'];
    $arr_barcode = $_POST['barcode_arr'];
    $arr_name = $_POST['product_arr'];
    $arr_stock = $_POST['stock_c_arr'];
    $arr_qty = $_POST['quantity_arr'];
    $arr_price = $_POST['price_c_arr'];
    $arr_total = $_POST['netamt_c_arr'];

    foreach ($row_invoice_details as $row_invoice_detail) {
        $updateproduct_stock=$pdo->prepare("update tbl_product set stock=stock+".$row_invoice_detail['qty']." where pid=".$row_invoice_detail['product_id']);
        $updateproduct_stock->execute();
    }

    $delete_invoice_details = $pdo->prepare("DELETE FROM tbl_invoice_details WHERE invoice_id='$id'");
    $delete_invoice_details->execute();

    $update_table_invoice = $pdo->prepare(
        "update tbl_invoice set order_date=:orderdate, subtotal=:subtotal, discount=:discount, sgst=:sgst, cgst=:cgst, total=:total, payment_type=:payment_type, due=:due, paid=:paid where invoice_id = '$id'");

    $update_table_invoice->bindParam(':orderdate',    $txt_orderdate);
    $update_table_invoice->bindParam(':subtotal',     $txt_subtotal);
    $update_table_invoice->bindParam(':discount',     $txt_discount);
    $update_table_invoice->bindParam(':sgst',         $txt_sgst);
    $update_table_invoice->bindParam(':cgst',         $txt_cgst);
    $update_table_invoice->bindParam(':total',        $txt_total);
    $update_table_invoice->bindParam(':payment_type', $txt_payment_type);
    $update_table_invoice->bindParam(':due',          $txt_due);
    $update_table_invoice->bindParam(':paid',         $txt_paid);

    $update_table_invoice->execute();
    $message = "Order placed successfully";

    $invoice_id=$pdo->lastInsertId();

    if($invoice_id != null){
        for($i=0; $i<count($arr_pid); $i++){

            $selectpdt=$pdo->prepare("SELECT * FROM tbl_product WHERE product_id='".$arr_pid[$i]."'");
            $selectpdt->execute();

            while ($rowpdt = $selectpdt->fetch(PDO::FETCH_OBJ)) {

                $db_stock[$i]=$rowpdt->stock;

                $rem_qty=$db_stock[$i]-$arr_qty[$i];

                if($rem_qty<0){
                    $message="Order not placed";
                }else{
                    $update=$pdo->prepare("update tbl_product SET stock='$rem_qty' where product_id='".$arr_pid[$i]."'");
                    $update->execute();
                }
            }


            $insert=$pdo->prepare("insert into tbl_invoice_details (invoice_id, barcode, product_id, product_name, qty, rate, saleprice, order_date) values(:invid, :barcode, :product_id, :name, :qty, :rate, :saleprice, :orderdate)");

            $insert->bindParam(':invid',  $id);
            $insert->bindParam(':barcode', $arr_barcode[$i]);
            $insert->bindParam(':product_id',  $arr_pid[$i]);
            $insert->bindParam(':name', $arr_name[$i]);
            $insert->bindParam(':qty', $arr_qty[$i]);
            $insert->bindParam(':rate', $arr_price[$i]);
            $insert->bindParam(':saleprice', $arr_total[$i]);
            $insert->bindParam(':orderdate', $txt_orderdate);

            if($insert->execute()){
                $message = "Order placed successfully";
            }else{
                $message = "Order not placed";
            }
        }
        header('location:orderlist.php');
    }
}


$select = $pdo->prepare("SELECT * FROM tbl_taxdis where taxdis_id=1");
$select->execute();
$row = $select->fetch(PDO::FETCH_OBJ);

?>

<style type="text/css">
    .tableFixHead {
        overflow: scroll;
        height: 520px;
    }

    .tableFixHead thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    table {
        border-collapse: collapse;
        width: 100px;
    }

    th, td {
        padding: 8px 16px;
    }

    th {
        background: #eee;
    }
</style>

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
                        <div class="card-title">POS</div>
                    </div>
                <?php endif; ?>
                <!--end::Header-->

                <div class="card-body">

                    <label for="txtbarcode_id" class="form-label">Scan Barcode</label>
                    <div class="input-group has-validation">
                                    <span class="input-group-text" id="inputGroupPrepend">
                                        <i class="bi bi-upc-scan"></i>
                                    </span>
                        <input type="text" class="form-control" id="txtbarcode_id" name="txtbarcode"/>
                    </div>

                    <form action="" method="post" name="">
                        <div class="row">
                            <div class="col-md-8">

                                <label for="selectproduct" class="form-label">Select Product</label>
                                <select class="form-select" id="selectproduct" style="width: 100%;">
                                    <option selected disabled value="">Choose...</option>
                                    <?php echo fill_product($pdo); ?>
                                </select>

                                <br>

                                <div class="tableFixHead">
                                    <table id="producttable" class="table table-hover table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Stock</th>
                                            <th>Price</th>
                                            <th>QTY</th>
                                            <th>Total</th>
                                            <th>Delete</th>
                                        </tr>
                                        </thead>
                                        <tbody class="details" id="itemtable"></tbody>
                                    </table>
                                </div>

                            </div> <!-- /.col-md-8 -->

                            <div class="col-md-4">

                                <label for="subtotal" class="form-label">SUBTOTAL</label>
                                <div class="input-group has-validation">
                                    <input type="text" name="txtsubtotal" id="txtsubtotal_id" class="form-control"
                                           readonly value="<?php echo $subtotal;?>"/>
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <label for="txtdiscount_id" class="form-label">DISCOUNT</label>
                                <div class="input-group has-validation">
                                    <input type="text" name="txtdiscount" id="txtdiscount_id" class="form-control"
                                           value="<?php echo $row->discount; ?>"/>
                                    <span class="input-group-text" id="inputGroupPrepend">%</span>
                                </div>

                                <label for="txtdiscount_n" class="form-label">DISCOUNT</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txtdiscount_n" class="form-control" readonly/>
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <label for="txtsgst_id" class="form-label">SGST</label>
                                <div class="input-group has-validation">
                                    <input type="text" name="txtsgst" id="txtsgst_id" class="form-control"
                                           value="<?php echo $row->sgst; ?>" readonly/>
                                    <span class="input-group-text" id="inputGroupPrepend">%</span>
                                </div>

                                <label for="txtcgst_id" class="form-label">CGST</label>
                                <div class="input-group has-validation">
                                    <input type="text" name="txtcgst" id="txtcgst_id" class="form-control"
                                           value="<?php echo $row->cgst; ?>" readonly/>
                                    <span class="input-group-text" id="inputGroupPrepend">%</span>
                                </div>

                                <label for="txtsgst_id_n" class="form-label">SGST $</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txtsgst_id_n" class="form-control" readonly/>
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <label for="txtcgst_id_n" class="form-label">CGST $</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txtcgst_id_n" class="form-control" readonly/>
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <hr style="height:2px; border-width:0; color:black; background-color:black">

                                <label for="txttotal" class="form-label">Total</label>
                                <div class="input-group has-validation">
                                    <input type="text" name="txttotal" id="txttotal"
                                           class="form-control form-control-lg total" value="<?php echo $total;?>" readonly/>
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <!-- Payment radios -->
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rb" id="gridRadios1" value="Cash"<?php echo ($payment_type=='Cash')?'checked':'';?>>
                                    <label class="form-check-label" for="gridRadios1">Cash</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rb" id="gridRadios2"
                                           value="Card"<?php echo ($payment_type=='Card')?'checked':'';?>>
                                    <label class="form-check-label" for="gridRadios2">Card</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rb" id="gridRadios3"
                                           value="Check"<?php echo ($payment_type=='Check')?'checked':'';?>>
                                    <label class="form-check-label" for="gridRadios3">Check</label>
                                </div>

                                <hr style="height:2px; border-width:0; color:black; background-color:black">

                                <label for="txtdue" class="form-label">DUE $</label>
                                <div class="input-group has-validation">
                                    <input type="text" name="txtdue" id="txtdue" class="form-control" value="<?php echo $due;?>" readonly/>
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <label for="txtpaid" class="form-label">PAID $</label>
                                <div class="input-group has-validation">
                                    <input type="text" name="txtpaid" id="txtpaid" class="form-control" value="<?php echo $paid;?>" required/>
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <hr style="height:2px; border-width:0; color:black; background-color:black">

                                <div class="card-footer">
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary" name="btnupdateorder"
                                                value="Update Order">Update Order
                                        </button>
                                    </div>
                                </div>

                            </div> <!-- /.col-md-4 -->
                        </div> <!-- /.row -->
                    </form>
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->
        </div> <!-- /.container-fluid -->
        <!--end::Container-->
    </div>
    <!--end::App Content-->
</main>
<!--end::App Main-->
<?php include_once "footer.php"; ?>

<script>
    var productarr = [];

    function addRow(product_id, product_name, qty, rate, saleprice, stock, barcode) {
        var tr =
            '<tr data-widget="expendable-table" aria-expanded="false" id="row_' + product_id + '">' +
            '<input type="hidden" class="form-control barcode" name="barcode_arr[]" id="barcode_id' + barcode + '" value="'+barcode+'">' +
            '<td style="text-align:left; vertical-align:middle; font-size:17px;">' +
            '<span>' + product_name + '</span>' +
            '<input type="hidden" class="form-control product_id" name="pid_arr[]" value="' + product_id + '">' +
            '<input type="hidden" class="form-control pname" name="product_arr[]" value="' + product_name + '">' +
            '</td>' +
            '<td style="text-align:left; vertical-align:middle; font-size:17px;">' +
            '<span class="stocklbl" id="stock_id' + product_id + '">' + stock + '</span>' +
            '<input type="hidden" class="form-control stock_c" name="stock_c_arr[]" id="stock_idd' + product_id + '" value="' + stock + '">' +
            '</td>' +
            '<td style="text-align:left; vertical-align:middle; font-size:17px;">' +
            '<span class="price" id="price_id' + product_id + '">' + (saleprice*1).toFixed(2) + '</span>' +
            '<input type="hidden" class="form-control price_c" name="price_c_arr[]" id="price_idd' + product_id + '" value="' + (saleprice*1).toFixed(2) + '">' +
            '</td>' +
            '<td>' +
            '<input type="text" class="form-control qty" name="quantity_arr[]" id="qty_id' + product_id + '" value="'+qty+'" size="1">' +
            '</td>' +
            '<td style="text-align:left; vertical-align:middle; font-size:17px;">' +
            '<span class="totalamt" id="saleprice_id' + product_id + '">' + (rate * qty).toFixed(2) + '</span>' +
            '<input type="hidden" class="form-control saleprice" name="netamt_c_arr[]" id="saleprice_idd' + product_id + '" value="' + (rate * qty).toFixed(2) + '">' +
            '</td>' +
            '<td><center><button type="button" name="remove" class="btn btn-danger btn-sm btnremove" data-id="' + product_id + '"><span class="bi bi-trash"></span></button></center></td>' +
            '</tr>';

        $('.details').append(tr);
        calculate(0, 0);
        $("#txtpaid").val('');
        $("#txtdue").val('');
    }
    //end adddrow



    function upsertLineFromProduct(data) {
        if (!data || !data.product_id) return;

        var product_id = String(data.product_id);
        var unitPrice = parseFloat(data.saleprice) || 0;

        if ($.inArray(product_id, productarr) !== -1) {
            var $qty = $("#qty_id" + product_id);
            var actualqty = (parseInt($qty.val(), 10) || 0) + 1;
            $qty.val(actualqty);

            var lineTotal = actualqty * unitPrice;
            $("#saleprice_id" + product_id).text(lineTotal.toFixed(2));
            $("#saleprice_idd" + product_id).val(lineTotal.toFixed(2));
            calculate(0, 0);
            $("#txtpaid").val('');
            $("#txtdue").val('');
        } else {
            addRow(product_id, data.product_name, data.qty || 1, data.rate || unitPrice, unitPrice, data.stock || 0, data.barcode || '');

            $("#price_id" + product_id).text(unitPrice.toFixed(2));
            $("#price_idd" + product_id).val(unitPrice.toFixed(2));
            $("#saleprice_id" + product_id).text(unitPrice.toFixed(2));
            $("#saleprice_idd" + product_id).val(unitPrice.toFixed(2));

            productarr.push(product_id);
        }
    }

    function fetchProductByIdOrBarcode(id) {
        if (!id) return;
        $.ajax({
            url: "getorderproduct.php",
            method: "GET",
            dataType: "json",
            data: {id: <?php echo $_GET['id']?>},
            success: function (data) {
                upsertLineFromProduct(data);
            }
        });
    }

    $(function () {
        $('#txtbarcode_id').on('change', function () {
            var barcode = $(this).val();
            fetchProductByIdOrBarcode(barcode);
            $(this).val('');
        });

        $('#selectproduct').on('change', function () {
            var product_id = $(this).val();
            fetchProductByIdOrBarcode(product_id);
        });
    });

    $("#itemtable").on("keyup change", ".qty", function () {
        var tr = $(this).closest("tr");
        var quantity = $(this);

        if ((quantity.val() > 0) > (tr.find(".stock_c").val() > 0)) {
            quantity.val(1);

            tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());
            tr.find(".saleprice").val(quantity.val() * tr.find(".price").text());

            calculate(0, 0);
            $("#txtpaid").val();
            $("#txtdue").val();

        } else {
            tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());
            tr.find(".saleprice").val(quantity.val() * tr.find(".price").text());

            calculate(0, 0);

            $("#txtpaid").val();
            $("#txtdue").val();
        }
    });

    function calculate(dis, paid) {
        var subtotal = 0;
        var discount = dis;
        var sgst = 0;
        var cgst = 0;
        var total = 0;
        var paid_amt = paid;
        var due = 0;

        $(".saleprice").each(function () {
            subtotal = subtotal + ($(this).val() * 1);
        });

        $("#txtsubtotal_id").val(subtotal.toFixed(2));

        sgst = parseFloat($("#txtsgst_id").val());

        cgst = parseFloat($("#txtcgst_id").val());

        discount = parseFloat($("#txtdiscount_id").val());

        sgst = sgst / 100;
        sgst = sgst * subtotal;

        cgst = cgst / 100;
        cgst = cgst * subtotal;

        discount = discount / 100;
        discount = discount * subtotal;

        $("#txtsgst_id_n").val(sgst.toFixed(2));
        $("#txtcgst_id_n").val(cgst.toFixed(2));
        $("#txtdiscount_n").val(discount.toFixed(2));

        total = sgst + cgst + subtotal - discount;
        due = total - paid_amt;

        $("#txttotal").val(total.toFixed(2));

        paid_db=parseFloat($("#txtpaid").val());
        due_db=paid_db - total;

        $("#txtdue").val(due.toFixed(2));

    } // end calculate function

    $("#txtdiscount_id").keyup(function () {

        var discount = $(this).val();

        calculate(discount, 0);

    });

    $("#txtpaid").keyup(function () {

        var paid = $(this).val();
        var discount = $("#txtdiscount_id").val();
        calculate(discount, paid);

    });

    $(document).on('click', '.btnremove', function () {
        var removed = $(this).attr("data-id");
        productarr = jQuery.grep(productarr, function (value) {
            return value != removed;
        });

        $(this).closest('tr').remove();
    calculate(0, 0);

        $("#txtpaid").val();
        $("#txtdue").val();
    });


</script>