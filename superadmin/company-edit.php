<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    ############### Find Company Data ##################
    $CheckCompany = "SELECT * FROM `companies` WHERE `id`='$id' LIMIT 1";
    $CheckCompanyQuery = mysqli_query($conn, $CheckCompany);
    if (mysqli_num_rows($CheckCompanyQuery) > 0) {
        while ($CompanyData = mysqli_fetch_assoc($CheckCompanyQuery)) {
            $company_name = trim($CompanyData['company_name']);
            $user_limit = $CompanyData['$user_limit'];
        }
    }
    ############### Find Company Data ################## 
?>


    <!DOCTYPE html>
    <html lang="en-US">

    <head>
        <title> Company Controller </title>
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

            <!-- ==================================================== -->
            <!-- Start right Content here -->
            <!-- ==================================================== -->
            <div class="page-content">

                <!-- Start Container Fluid -->
                <div class="container-xxl">

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit Company</h4>
                                </div>
                                <form action="#" method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div class="row">

                                            <!-- Contact Number -->
                                            <div class="col-lg-9">
                                                <div class="mb-3">
                                                    <label for="company_name" class="form-label text-dark">Company Name <small class="text-success">(Required)</small></label>
                                                    <input type="text" id="company_name" name="company_name" class="form-control" placeholder="Company Name" value="<?php echo $company_name; ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label for="user_limit" class="form-label text-dark">User Limit </label>
                                                    <input type="text" id="user_limit" name="user_limit" class="form-control" placeholder="User Limit in number" value="<?php echo $user_limit; ?>" pattern="[0-9]*">
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="card-footer border-top">
                                        <button type="submit" name="update_company" class="btn btn-primary">Update Company</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End Container Fluid -->

                <!-- ========== Footer Start ========== -->
                <?php include("../footer.php"); ?>
                <!-- ========== Footer End ========== -->

            </div>
            <!-- ==================================================== -->
            <!-- End Page Content -->
            <!-- ==================================================== -->

        </div>
        <!-- END Wrapper -->

        <!-- Vendor Javascript (Require in all Page) -->
        <script src="../assets/js/vendor.js"></script>

        <!-- App Javascript (Require in all Page) -->
        <script src="../assets/js/app.js"></script>

    </body>

    </html>

<?php
    ################# Update Company ###################
    if (isset($_POST["update_company"])) {
        $update_company = trim($_POST['company_name']);
        if (empty($_POST['user_limit'])) {
            $user_limit = "Unlimited";
        } else {
            $user_limit = $_POST['user_limit'];
        }
        $company_updatedby = $username;
        $updataCompanyData = "UPDATE `companies` SET `company_name`='$update_company', `user_limit`='$user_limit' WHERE `id` = '$id'";
        $updataCompanyDataQuery = mysqli_query($conn, $updataCompanyData);

        if ($updataCompanyDataQuery) {
            ################# Push activity ################
            $activity_id = $guid;
            $activity_content = "The company " . $company_name . " has been updated by " . $Role . ".";
            $activity_type = 'Alert';
            $activity_company = "";
            $activity_branch = "";
            $activity_on = date('Y-m-d H:i:s');
            $activity_by = $username;
            $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
            $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
            ################# Push activity ################
            echo "<script>window.location.href='company-control.php'</script>";
            exit;
        } else {
            echo "<script>window.location.href='company-edit.php'</script>";
            exit;
        }
    }
    ################# Add Company ###################
} else {
    echo "<script>window.location.href='company-control.php'</script>";
    exit;
}
$conn->close();
?>