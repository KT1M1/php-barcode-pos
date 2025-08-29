<?php
include_once "connectdb.php";
session_start();

if($_SESSION['useremail'] == "" || $_SESSION['userrole'] == "User") {
    header('location:../index.php');
} else{
    include_once "header.php";
}

$message = "";

$id = $_GET['id'];

if(isset($id)){
    $delete = $pdo->prepare("DELETE FROM tbl_user WHERE userid=".$id);

    if($delete->execute()){
        $message = '<div class="alert alert-danger mb-0">User deleted.</div>';
    }else{
        $message = '<div class="alert alert-danger mb-0">User not deleted.</div>';
    }
}



if(isset($_POST['btnsave'])){
    $username = $_POST['txtname'];
    $useremail = $_POST['txtemail'];
    $userpassword = $_POST['txtpassword'];
    $userrole = $_POST['txtselect_option'];

    if(isset($_POST['txtemail'])){
        $select = $pdo->prepare("SELECT * FROM tbl_user WHERE useremail = :useremail");
        $select->bindParam(":useremail", $useremail);
        $select->execute();

        if($select->rowCount() > 0){
            $message = '<div class="alert alert-danger mb-0">User already exists.</div>';
        } else {
            $insert = $pdo->prepare("insert into tbl_user(username,useremail,userpassword,userrole) values(:name,:email,:password,:role)");

            $insert->bindParam(':name', $username);
            $insert->bindParam(':email', $useremail);
            $insert->bindParam(':password', $userpassword);
            $insert->bindParam(':role', $userrole);
            if($insert->execute()){
                $message = '<div class="alert alert-success mb-0">User Created</div>';
            }else{
                $message = '<div class="alert alert-danger mb-0">Error.</div>';
            }
        }
    }
}

?>
<!--begin::App Main-->
<main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Registration</h3></div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
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
                    <div class="card-header"><div class="card-title">Registration</div></div>
                <?php endif; ?>
                <!--end::Header-->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <form action="" method="post">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="txtname" required/>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                                    <input type="email" class="form-control" name="txtemail" required/>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputPassword1" class="form-label">Password</label>
                                    <input type="password" class="form-control" name="txtpassword" required/>
                                </div>

                                <div class="form-group">
                                    <label>Role</label>
                                    <select class="form-control" name="txtselect_option" required>
                                        <option value="" disabled selected>Select Role</option>
                                        <option value="Admin">Admin</option>
                                        <option value="User">User</option>
                                    </select>
                                </div>

                                <!--begin::Footer-->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" name="btnsave">Save</button>
                                </div>
                                <!--end::Footer-->
                            </form>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>Name</td>
                                        <td>Email</td>
                                        <td>Role</td>
                                        <td>Delete</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $select = $pdo->prepare("SELECT * FROM tbl_user order by userid desc");
                                        $select->execute();

                                        while($row = $select->fetch(PDO::FETCH_OBJ)) {
                                            echo '
                                                <tr>
                                                <td>'.$row->userid.'</td>
                                                <td>'.$row->username.'</td>
                                                <td>'.$row->useremail.'</td>
                                                <td>'.$row->userrole.'</td>
                                                <td><a href="registration.php?id='.$row->userid.'" class="btn btn-danger"><i class="bi bi-trash"></i></a></td>
                                                </tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
</main>
<!--end::App Main-->
<?php include_once "footer.php"; ?>
