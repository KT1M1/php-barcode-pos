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
                        <div class="card-title">Blank</div>
                    </div>
                <?php endif; ?>
                <!--end::Header-->
                <div class="card-body">
                    <div class="row">

                    </div>
                    <!--end::row-->
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
