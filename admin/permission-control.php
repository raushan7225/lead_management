<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

################# References Publish / Unpublish ###################
if (isset($_POST['publish'])) {
    $permissionId = $_POST["permissionId"];
    $new_status = ($_POST['publish'] == 'on' || $_POST['publish'] == '1') ? 1 : 0;
    $updateStatusQuery = "UPDATE `permissions` SET `permission_status` = '$new_status' WHERE `id` = '$permissionId'";
    $Publish = mysqli_query($conn, $updateStatusQuery);
    if ($Publish) {
        echo "<script>window.location.href='permission-control.php';</script>";
    } else {
        echo "Error updating status: " . $conn->error;
    }
}
################# References Publish / Unpublish ###################


################# Update / Delete Permission ###################
// if (isset($_POST["delete_permission"])) {
//     $permissionId = $_POST["permissionId"];
//     $DeletePermission = "DELETE FROM `permissions` WHERE `id` = '$permissionId'";
//     $DeletePermissionQuery = $conn->query($DeletePermission);
//     if ($DeletePermissionQuery) {
//         header("Location: permission-control.php");
//         exit;
//     } else {
//         echo "Error deleting permission: " . $conn->error;
//     }
// } 
if (isset($_POST["edit_permission"])) {
    $permissionId = $_POST["permissionId"];
    header("Location: permission-edit.php?id=$permissionId");
    exit;
}
################# Update / Delete Permission ###################


// ################# Add Permission ###################
// if (isset($_POST["add_permissions"])) {

//     ########### Generate Unique ID #############
//     function generateId()
//     {
//         $prefix = '#PER';
//         $randomNumber = rand(1, 9999999999);
//         return $prefix . $randomNumber;
//     }
//     $uniqueId = generateId();
//     ########### Generate Unique ID #############

//     ########### Set All Values #############
//     $permission_id = $uniqueId;
//     $permission_options = $_POST["permission_options"];
//     $permission_assign_to = implode(', ', $_POST["permission_assign_to"]);
//     $permission_assined_by = $username;
//     $permission_status = '1';
//     ########### Set All Values #############

//     ########### Insert Permission Value #############    
//     $AddPermission = "INSERT INTO `permissions` (`permission_id`, `permission_options`, `permission_assign_to`, `permission_assined_by`, `permission_status`) VALUES ('$permission_id','$permission_options','$permission_assign_to','$permission_assined_by','$permission_status')";
//     ########### Insert Permission Value ############# 

//     ################# Execute Permission Query ###################
//     $AddPermissionQuery = $conn->query($AddPermission);
//     if ($AddPermissionQuery) {
//         header("Location: permission-control.php");
//         exit;
//     } else {
//         echo "Error: " . $AddPermission . "<br>" . $conn->error;
//     }
//     ################# Execute Permission Query ###################
// }
// ################# close Add Permission ###################


####################### Fetch Branch ########################
$BranchList = "SELECT `branch_id` FROM `branches` WHERE `branch_of_company` = '$Company' AND `branch_status` = '1'";
$BranchListResult = mysqli_query($conn, $BranchList);
$BranchCount = mysqli_num_rows($BranchListResult);
####################### Fetch Branch ########################


##################### Fetch employees #####################
$employees_list = "SELECT `employee_user_role` FROM `employees` WHERE `employee_of_company`='$Company' AND `employee_user_role` NOT IN ('Admin', 'Super Admin') AND `employee_status` = '1'";
$employees_list_results = $conn->query($employees_list);
$EmployeeCount = mysqli_num_rows($employees_list_results);
##################### Fetch employees #####################


#################### Fetch Leads Count ####################
$leads_count = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_customer_status` = '1'";
$leads_count_result = $conn->query($leads_count);
$leadsCount = $leads_count_result->num_rows;
#################### Fetch Leads Count ####################


############### Products list #####################
$products_list = "SELECT * FROM `products` WHERE `product_for_company` = '$Company' AND `status` = '1'";
$products_list_result = $conn->query($products_list);
$ProductCount = $products_list_result->num_rows;
############### Products list #####################


################### Get All Permissions Data ###################
$GetAllPermissions = "SELECT * FROM `permissions` ORDER BY `id` ASC";
$GetAllPermissionsResult = $conn->query($GetAllPermissions);
$permissions = [];
if ($GetAllPermissionsResult->num_rows > 0) {
    while ($ArrayPermission = $GetAllPermissionsResult->fetch_assoc()) {
        $permissions[] = $ArrayPermission;
    }
}
################### Get All Permissions Data ###################
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Permissions Control</title>
    <?php include("head.php"); ?>
</head>

<body>

    <!-- Loader -->
    <div id="loader-wrapper">
        <div class="loader"></div>
    </div>

    <!-- START Wrapper -->
    <div class="wrapper">

        <!-- =========== Topbar Start ============= -->
        <?php include("topnav.php"); ?>
        <!-- ========== End Topbar Start ========== -->

        <!-- =========== App Menu Start =========== -->
        <?php include("sidenav.php"); ?>
        <!-- =========== App Menu End ============= -->

        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">

            <!-- Start Container Fluid -->
            <div class="container-xxl">

                <div class="row">

                    <!-- ===================== Assigned Manager ================= -->
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h4 class="card-title mb-2 d-flex align-items-center gap-2">Managers</h4>
                                        <p class="text-muted fw-medium fs-22 mb-0"><?php echo $BranchCount; ?></p>
                                    </div>
                                    <div>
                                        <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                            <iconify-icon icon="solar:backpack-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ==========x========== Assigned Manager =======x========= -->


                    <!-- ===================== Total Employee ================= -->
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h4 class="card-title mb-2 d-flex align-items-center gap-2">Employees</h4>
                                        <p class="text-muted fw-medium fs-22 mb-0"><?php echo $EmployeeCount; ?></p>
                                    </div>
                                    <div>
                                        <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                            <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ==========x========== Total Employee ========x======== -->


                    <!-- ======================= All Leads ====================== -->
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h4 class="card-title mb-2 d-flex align-items-center gap-2">Customers</h4>
                                        <p class="text-muted fw-medium fs-22 mb-0"><?php echo $leadsCount; ?></p>
                                    </div>
                                    <div>
                                        <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                            <iconify-icon icon="solar:notebook-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ===========x=========== All Leads ===========x========== -->

                    <!-- ====================== All Products ==================== -->
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h4 class="card-title mb-2 d-flex align-items-center gap-2">Products</h4>
                                        <p class="text-muted fw-medium fs-22 mb-0"><?php echo $ProductCount; ?></p>
                                    </div>
                                    <div>
                                        <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                            <iconify-icon icon="solar:box-line-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ==========x=========== All Products ==========x========= -->
                </div>


                <!--================= Permission Add Modal ================= ->
                <div class="modal fade" id="PermissionModal" tabindex="-1" aria-labelledby="PermissionModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="#" method="POST" enctype="multipart/form-data">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="PermissionModalTitle"> Create Permission </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="col-12">
                                        <!- ============== Permission Options =============== ->
                                        <div class="mb-3">
                                            <label for="permission_options" class="form-label text-dark"> Options <small class="text-success">(Required)</small></label>
                                            <select name="permission_options" id="permission_options" data-choices required>
                                                <option value=""> Select Options </option>
                                                <option value="States Lists"> States Lists </option>
                                                <option value="Users Control"> Users Control </option>
                                                <option value="Products Control"> Products Control </option>
                                                <option value="Leads Control"> Leads Control </option>
                                                <option value="Report"> Report </option>
                                            </select>
                                        </div>
                                        <!- ======x======= Permission Options =======x======= ->

                                        <!- ============== Permission Options =============== ->
                                        <div class="mb-0">
                                            <label for="permission_assign_to" class="form-label text-dark"> Assign To <small class="text-success">(Required)</small></label>
                                            <select class="form-control" name="permission_assign_to[]" id="choices-multiple-remove-button" data-choices data-choices-removeItem multiple required>
                                                <option value=""> Select Options </option>
                                                <option value="Manager"> Manager </option>
                                                <option value="Staff"> Staff </option>
                                                <option value="Technician"> Technician </option>
                                            </select>
                                        </div>
                                        <!- ======x======= Permission Options =======x======= ->
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <div class="card-body">
                                        <div class="row justify-content-between align-items-center gap-2">
                                            <div class="col-6">
                                                <button type="submit" name="add_permissions" class=" btn btn-outline-secondary w-100">Add Permission</button>
                                            </div>
                                            <div class="col-4">
                                                <a href="permission-control.php" class=" btn btn-primary w-100">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <!- ========x========= Permission Add Modal =========x========-->


                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <!-- =============== Permissions Header =============== -->
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <h4 class="card-title">All Permissions List</h4>
                                 <!--<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#PermissionModal">Add Permission</button> -->
                            </div>
                            <!-- ======x======== Permissions Header ======x======== -->

                            <!-- =============== Permissions Table ================ -->
                            <div class="card-body">
                                <div id="table-lead-search"></div>
                            </div>
                            <!-- ======x======== Permissions Table =======x======== -->
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

    <!-- Grid Js -->
    <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

    <script>
        // Permissions Table
        const leadData = [
            <?php
            $counterValue = 1;
            foreach ($permissions as $myPermission) { ?>[
                    gridjs.html(`<?php echo $counterValue++; ?>`),
                    gridjs.html(`<?php echo $myPermission['permission_options']; ?>`),
                    gridjs.html(`<?php echo $myPermission['permission_assign_to']; ?>`),

                    gridjs.html(`
                    <form action="#" method="post">
                        <input type="hidden" name="permissionId" value="<?php echo $myPermission['id']; ?>">
                        <input type="hidden" name="publish" value="0">
                        <div class="form-check form-switch flex-box justify-content-center align-items-center">
                            <input class="form-check-input" name="publish" type="checkbox" role="switch" id="flexSwitchCheckChecked<?php echo $myPermission['id']; ?>" <?php echo ($myPermission['permission_status'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                        </div>
                    </form>`),

                    gridjs.html(`
                                <form action="#" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="permissionId" value="<?php echo $myPermission['id']; ?>">
                                    <button type="submit" name="edit_permission" value="<?php echo $myPermission['id']; ?>" class="btn btn-soft-primary btn-sm">
                                        <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                    </button>
                                </form>
                            `)
                ],
            <?php } ?>
        ];

        // Initialize Grid.js Table
        if (document.getElementById("table-lead-search")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No.",
                        width: "50px"
                    },
                    "Options",
                    {
                        name: "Assigned To",
                        width: "250px"
                    },
                    {
                        name: "Status",
                        width: "80px"
                    },
                    {
                        name: "Actions",
                        width: "50px"
                    }
                ],
                pagination: {
                    limit: 10
                },
                search: true,
                data: leadData,
                style: {
                    table: {
                        'min-width': '1200px',
                        'font-size': '15px',
                        'text-align': 'center',
                    },
                    th: {
                        'background-color': '#ff6c2f',
                        'color': '#fff'
                    },
                }
            }).render(document.getElementById("table-lead-search"));
        }
    </script>
</body>

</html>