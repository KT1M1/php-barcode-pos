<?php
include_once "connectdb.php";
session_start();

if($_SESSION['useremail'] == "") {
    header('location:../index.php');
}

if($_SESSION['userrole'] == "Admin") {
    include_once "header.php";
} else{
    include_once "headeruser.php";
}



$message = "";

if(isset($_POST['btn-update'])){
    $oldpassword = $_POST['txt_oldpassword'];
    $newpassword = $_POST['txt_newpassword'];
    $repeatpassword = $_POST['txt_repeatpassword'];

    $email = $_SESSION['useremail'];

    $select = $pdo->prepare("SELECT * FROM tbl_user WHERE useremail='$email'");
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);

    $useremail_db = $row['useremail'];
    $userpassword_db= $row['userpassword'];

    if (!$row) {
        $message = '<div class="alert alert-danger mb-0">User not found.</div>';
    } elseif ($oldpassword !== $row['userpassword']) {
        $message = '<div class="alert alert-danger mb-0">Wrong old password.</div>';
    } elseif ($newpassword !== $repeatpassword) {
        $message = '<div class="alert alert-danger mb-0">New passwords do not match.</div>';
    } else {
        $update = $pdo->prepare("UPDATE tbl_user SET userpassword = :pass WHERE useremail = :email");

        $set = $update->execute([':pass' => $newpassword, ':email' => $email]);

        $message = $set
            ? '<div class="alert alert-success mb-0">Password updated.</div>'
            : '<div class="alert alert-danger mb-0">Update failed. Please try again.</div>';
    }
}

?>
<!--begin::App Main-->
<main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Horizontal Form-->
            <div class="card card-warning card-outline mb-4">
                <!--begin::Header-->
                <?php if (!empty($message)): ?>
                    <?php echo $message; ?>
                <?php else: ?>
                    <div class="card-header"><div class="card-title">Change Password</div></div>
                <?php endif; ?>
                <!--end::Header-->
                <!--begin::Form-->
                <form action="" method="post">
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="row mb-3">
                            <label for="inputPassword3" class="col-sm-2 col-form-label">Old Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="inputPassword3" name="txt_oldpassword" placeholder="Old Password"/>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputPassword3" class="col-sm-2 col-form-label">New Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="inputPassword3" name="txt_newpassword" placeholder="New Password"/>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputPassword3" class="col-sm-2 col-form-label">Repeat New Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="inputPassword3" name="txt_repeatpassword" placeholder="Repeat New Password"/>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning" name="btn-update">Update Password</button>
                    </div>
                    <!--end::Footer-->
                </form>
                <!--end::Form-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
</main>
<!--end::App Main-->
<?php include_once "footer.php"; ?>
