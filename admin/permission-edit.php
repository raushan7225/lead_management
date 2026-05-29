<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

if (isset($_GET["id"])) {
    $PermissionId = $_GET["id"];

    ################### Get All Permissions Data ###################
    $GetAllPermissions = "SELECT * FROM `permissions` WHERE `id` = '$PermissionId' ORDER BY `id` ASC";
    $GetAllPermissionsResult = $conn->query($GetAllPermissions);
    $permissions = [];
    if ($GetAllPermissionsResult->num_rows > 0) {
        while ($ArrayPermission = $GetAllPermissionsResult->fetch_assoc()) {
            $permissions_option = $ArrayPermission["permission_options"];
            $permission_assign_to = explode(', ', ($ArrayPermission["permission_assign_to"]));
        }
    }
    ################### Get All Permissions Data ###################

    ################### Update Permission ###################
    if (isset($_POST["update_permissions"])) {
        $permission_options = $_POST["permission_options"];
        $permission_assign_to = implode(', ', $_POST["permission_assign_to"]);

        $PermissionUpdate = "UPDATE `permissions` SET `permission_options` = '$permission_options', `permission_assign_to` = '$permission_assign_to' WHERE `id` = '$PermissionId'";
        $PermissionUpdateQuery = $conn->query($PermissionUpdate);
        if ($PermissionUpdateQuery) {
            ################# Push activity ################
            $activity_id = $guid;
            $activity_content = "Permission " . $permission_options . " has been assigned to " . $permission_assign_to . " by admin of " . $Company . " company.";
            $activity_type = 'Alert';
            $activity_company = $Company;
            $activity_branch = "";
            $activity_on = date('Y-m-d H:i:s');
            $activity_by = $username;
            $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
            $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
            ################# Push activity ################
            header("Location: permission-control.php");
            exit;
        } else {
            echo "Error: " . $PermissionUpdate . "<br>" . $conn->error;
        }
    }
    ################### Update Permission ###################
}
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Add Permission</title>
    <?php include("head.php"); ?>
    <style>
        #services-container .choices {
            width: 97%;
        }
    </style>
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
        <div class="page-content">

            <!-- Start Container Fluid -->
            <div class="container-xxl">
                <form action="#" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Create Permission</h4>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <!-- ============== Permission Options =============== -->
                                        <div class="col-lg-8">
                                            <div class="mb-3">
                                                <label for="permission_options" class="form-label text-dark"> Options <small class="text-success">(Required)</small></label>
                                                <select name="permission_options" id="permission_options" data-choices required>
                                                    <option value="">Select Options</option>
                                                    <option value="States Lists" <?php echo ($permissions_option == 'States Lists') ? 'selected' : ''; ?>> States Lists </option>
                                                    <option value="Users Control" <?php echo ($permissions_option == 'Users Control') ? 'selected' : ''; ?>> Users Control </option>
                                                    <option value="Products Control" <?php echo ($permissions_option == 'Products Control') ? 'selected' : ''; ?>> Products Control </option>
                                                    <option value="Leads Control" <?php echo ($permissions_option == 'Leads Control') ? 'selected' : ''; ?>> Leads Control </option>
                                                    <option value="Report" <?php echo ($permissions_option == 'Report') ? 'selected' : ''; ?>> Report </option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- ======x======= Permission Options =======x======= -->

                                        <!-- ============== Permission Options =============== -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="permission_assign_to" class="form-label text-dark"> Assign To <small class="text-success">(Required)</small></label>
                                                <select class="form-control" name="permission_assign_to[]" id="choices-multiple-remove-button" data-choices data-choices-removeItem multiple required>
                                                    <option value=""> Select Options </option>
                                                    <option value="Manager" <?php echo (in_array('Manager', $permission_assign_to)) ? 'selected' : ''; ?>> Manager </option>
                                                    <option value="Staff" <?php echo (in_array('Staff', $permission_assign_to)) ? 'selected' : ''; ?>> Staff </option>
                                                    <option value="Technician" <?php echo (in_array('Technician', $permission_assign_to)) ? 'selected' : ''; ?>> Technician </option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- ======x======= Permission Options =======x======= -->

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="p-3 bg-light mb-3 rounded">
                        <div class="row justify-content-end g-2">
                            <div class="col-lg-2">
                                <button type="submit" name="update_permissions" class="btn btn-outline-secondary w-100">Update Permission</button>
                            </div>
                            <div class="col-lg-2">
                                <a href="permission-control.php" class="btn btn-primary w-100">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Container Fluid -->

        <!-- ========== Footer Start ========== -->
        <?php include("../footer.php"); ?>
        <!-- ========== Footer End ========== -->

    </div>
    <!-- END Wrapper -->

    <!-- Vendor Javascript (Require in all Page) -->
    <script src="../assets/js/vendor.js"></script>

    <!-- App Javascript (Require in all Page) -->
    <script src="../assets/js/app.js"></script>

</body>

</html>