<?php
include_once "connectdb.php";
session_start();

include_once "header.php";

function fill_product($pdo) {
    $output='';
    $select = $pdo->prepare("SELECT * FROM tbl_product order by product ASC");;
    $select->execute();

    $result = $select->fetchAll();

    foreach($result as $row) {
        $output .= '<option value="'.$row["pid"].'">'.$row["product"].'</option>';
    }
    return $output;
}

$select=$pdo->prepare("SELECT * FROM tbl_taxdis where taxdis_id=1");
$select->execute();
$row=$select->fetch(PDO::FETCH_OBJ);

$message = "";
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
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-md-8">

                                <label for="txtbarcode_id" class="form-label">Scan Barcode</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text" id="inputGroupPrepend">
                                        <i class="bi bi-upc-scan"></i>
                                    </span>
                                    <input
                                            type="text"
                                            class="form-control"
                                            id="txtbarcode_id"
                                            aria-describedby="inputGroupPrepend"
                                    />
                                </div>

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
                                        <tbody class="details" id="itemtable">
                                            <tr data-widget="expendable-table" aria-expanded="false">
                <!-- data should be here-->
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div> <!-- /.col-md-8 -->

                            <div class="col-md-4">

                                <label for="subtotal" class="form-label">SUBTOTAL</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txtsubtotal_id" class="form-control" readonly />
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <label for="txtdiscount_id" class="form-label">DISCOUNT</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txtdiscount_id" class="form-control" value="<?php echo $row->discount; ?>"/>
                                    <span class="input-group-text" id="inputGroupPrepend">%</span>
                                </div>

                                <label for="txtdiscount_n" class="form-label">DISCOUNT</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txtdiscount_n" class="form-control" readonly />
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <label for="txtsgst_id" class="form-label">SGST</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txtsgst_id" class="form-control" value="<?php echo $row->sgst; ?>" readonly />
                                    <span class="input-group-text" id="inputGroupPrepend">%</span>
                                </div>

                                <label for="txtcgst_id" class="form-label">CGST</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txtcgst_id" class="form-control" value="<?php echo $row->cgst; ?>" readonly />
                                    <span class="input-group-text" id="inputGroupPrepend">%</span>
                                </div>

                                <label for="txtsgst_id_n" class="form-label">SGST $</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txtsgst_id_n" class="form-control" readonly />
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <label for="txtcgst_id_n" class="form-label">CGST $</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txtcgst_id_n" class="form-control" readonly />
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <hr style="height:2px; border-width:0; color:black; background-color:black">

                                <label for="txttotal" class="form-label">Total</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txttotal" class="form-control form-control-lg total" readonly />
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <!-- Payment radios -->
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="cash" checked>
                                    <label class="form-check-label" for="gridRadios1">Cash</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="card">
                                    <label class="form-check-label" for="gridRadios2">Card</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="check">
                                    <label class="form-check-label" for="gridRadios3">Check</label>
                                </div>

                                <hr style="height:2px; border-width:0; color:black; background-color:black">

                                <label for="txtdue" class="form-label">DUE $</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txtdue" class="form-control" readonly />
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <label for="txtpaid" class="form-label">PAID $</label>
                                <div class="input-group has-validation">
                                    <input type="text" id="txtpaid" class="form-control" />
                                    <span class="input-group-text" id="inputGroupPrepend">$</span>
                                </div>

                                <hr style="height:2px; border-width:0; color:black; background-color:black">

                                <div class="card-footer">
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary" name="btnsave" value="Save Order">Save Order</button>
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

    function addRow(pid, product, saleprice, stock) {
        var tr =
            '<tr data-widget="expendable-table" aria-expanded="false" id="row_' + pid + '">' +

            '<td style="text-align:left; vertical-align:middle; font-size:17px;">' +
            '<span>' + product + '</span>' +
            '<input type="hidden" class="form-control pid" name="pid_arr[]" value="' + pid + '">' +
            '</td>' +

            '<td style="text-align:left; vertical-align:middle; font-size:17px;">' +
            '<span class="stocklbl" name="stock_arr[]" id="stock_id' + pid + '">' + stock + '</span>' +
            '<input type="hidden" class="form-control stock_c" name="stock_c_arr[]" id="stock_idd' + pid + '" value="' + stock + '">' +
            '</td>' +

            '<td style="text-align:left; vertical-align:middle; font-size:17px;">' +
            '<span class="price" name="price_arr[]" id="price_id' + pid + '">' + saleprice.toFixed(2) + '</span>' +
            '<input type="hidden" class="form-control price_c" name="price_c_arr[]" id="price_idd' + pid + '" value="' + saleprice.toFixed(2) + '">' +
            '</td>' +

            '<td>' +
            '<input type="text" class="form-control qty" name="quantity_arr[]" id="qty_id' + pid + '" value="1" size="1">' +
            '</td>' +

            '<td style="text-align:left; vertical-align:middle; font-size:17px;">' +
            '<span class="totalamt" name="netamt_arr[]" id="saleprice_id' + pid + '">' + saleprice.toFixed(2) + '</span>' +
            '<input type="hidden" class="form-control saleprice" name="netamt_c_arr[]" id="saleprice_idd' + pid + '" value="' + saleprice.toFixed(2) + '">' +
            '</td>' +

            '<td style="text-align:left; vertical-align:middle;">' +
            '<a href="#" name="remove" class="btnremove" data-id="' + pid + '"><span class="bi bi-trash"></span></a>' +
            '</td>' +

            '</tr>';

        $('.details').append(tr);

        calculate();
    }

    function upsertLineFromProduct(data) {
        if (!data || !data.pid) return;

        var pid = String(data.pid);
        var unitPrice = parseFloat(data.saleprice) || 0;

        if ($.inArray(pid, productarr) !== -1) {
            var $qty = $("#qty_id" + pid);
            var actualqty = (parseInt($qty.val(), 10) || 0) + 1;
            $qty.val(actualqty);

            var lineTotal = actualqty * unitPrice;

            $("#saleprice_id" + pid).text(lineTotal.toFixed(2));
            $("#saleprice_idd" + pid).val(lineTotal.toFixed(2));

            calculate();
        } else {
            addRow(pid, data.product, unitPrice, data.stock);


            $("#price_id" + pid).text(unitPrice.toFixed(2));
            $("#price_idd" + pid).val(unitPrice.toFixed(2));
            $("#saleprice_id" + pid).text(unitPrice.toFixed(2));
            $("#saleprice_idd" + pid).val(unitPrice.toFixed(2));

            productarr.push(pid);
        }
    }

    function fetchProductByIdOrBarcode(id) {
        if (!id) return;
        $.ajax({
            url: "getproduct.php",
            method: "GET",
            dataType: "json",
            data: { id: id },
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
            var pid = $(this).val();
            fetchProductByIdOrBarcode(pid);
        });
    });

    $("#itemtable").on("keyup change", ".qty", function () {
        var tr = $(this).closest("tr");
        var quantity = $(this);

        if ((quantity.val() > 0) > (tr.find(".stock_c").val() > 0)) {
            quantity.val(1);

            tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());
            tr.find(".saleprice").val(quantity.val() * tr.find(".price").text());
        } else {
            tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());
            tr.find(".saleprice").val(quantity.val() * tr.find(".price").text());
        }

        calculate();
    });


    function calculate(){
        var subtotal=0;
        var discount=0;
        var sgst=0;
        var cgst=0;
        var total=0;
        var paid_amt=0;
        var due=0;

        $(".saleprice").each(function(){
            subtotal=subtotal+($(this).val()*1);
        });

        $("#txtsubtotal_id").val(subtotal.toFixed(2));

        sgst=parseFloat($("#txtsgst_id").val());

        cgst=parseFloat($("#txtcgst_id").val());

        discount=parseFloat($("#txtdiscount_id").val());

        sgst=sgst/100;
        sgst=sgst*subtotal;

        cgst=cgst/100;
        cgst=cgst*subtotal;

        discount=discount/100;
        discount=discount*subtotal;

        $("#txtsgst_id_n").val(sgst.toFixed(2));
        $("#txtcgst_id_n").val(cgst.toFixed(2));
        $("#txtdiscount_n").val(discount.toFixed(2));

        total=sgst+cgst+subtotal-discount;
        due=total-paid_amt;

        $("#txttotal").val(total.toFixed(2));
        $("#txtdue").val(due.toFixed(2));

    }


</script>






