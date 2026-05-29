<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Employee Details Update</title>
    <?php include("head.php"); ?>
</head>

<body>
    <!-- Loader -->
    <div id="loader-wrapper">
        <div class="loader"></div>
    </div>

    <!-- START Wrapper -->
    <div class="wrapper">

        <!-- ========== Topbar Start ========== -->
        <?php include("topnav.php"); ?>
        <!-- ========== Topbar End ========== -->

        <!-- ========== App Menu Start ========== -->
        <?php include("sidenav.php"); ?>
        <!-- ========== App Menu End ========== -->

        <!-- Start right Content here -->
        <div>
            <div class="page-content">

                <!-- Start Container Fluid -->
                <div class="container-xxl">

                    <div class="row">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Update Profile Pic</h4>
                            </div>
                            <form action="#" method="POST" enctype="multipart/form-data">
                                <div class="card-body">
                                    <label for="inputFile" class="form-label">Profile Picture</label>
                                    <input type="file" name="profile_pic" id="inputFile" class="form-control">
                                </div>
                                <div class="card-footer">
                                    <div class="row justify-content-start g-2">
                                        <div class="col-lg-2">
                                            <button type="submit" name="update_employee" class="btn btn-outline-secondary w-100">Update Profile Pic</button>
                                        </div>
                                        <div class="col-lg-2">
                                            <a href="index.php" class="btn btn-primary w-100">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Container Fluid -->

            <!-- Footer -->
            <?php include("../footer.php"); ?>
        </div>
    </div>
    <!-- END Wrapper -->

    <!-- Vendor Javascript (Require in all Page) -->
    <script src="../assets/js/vendor.js"></script>

    <!-- App Javascript (Require in all Page) -->
    <script src="../assets/js/app.js"></script>

</body>

</html>

<?php

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if (isset($_POST['update_employee'])) {
        ############################## FILE UPLOAD ############################
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        $file_extension = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            $target_dir = "../uploads/images/";
            $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);

            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $profilePic = "UPDATE `employees` SET `profile_pic` = '$target_file' WHERE `id` = '$id'";
                $updateProfilePic = mysqli_query($conn, $profilePic);

                ################# Push activity ################
                $activity_id = $guid;
                $activity_content = "The admin of ". $Company ." company has updated their profile picture.";
                $activity_type = 'Alert';
                $activity_company = $Company;
                $activity_branch = "";
                $activity_on = date('Y-m-d H:i:s');
                $activity_by = $username;
                $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
                $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
                ################# Push activity ################

                echo "<script>window.location.href='index.php';</script>";
                exit;
            } else {
                echo "<script>alert('Something went wrong')</script>";
                exit;
            }
        } else {
            echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.')</script>";
        }
    }
} else {
    echo "<script> window.location.href = 'index.php';</script>";
    exit;
}
?>