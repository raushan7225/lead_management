<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

############### Fetch all Company from the database ##############
$company_list = "SELECT * FROM `companies` ORDER BY `id` DESC";
$company_list_result = mysqli_query($conn, $company_list);
############### Fetch all Company from the database ##############


######################## Publish / Unpublish ###################
if (isset($_POST['publish'])) {

    $companyId = $_POST['companyId'];
    $companyName = $_POST['companyName'];

    ######################## Branch (Publish / Unpublish) ###################
    $new_status = ($_POST['publish'] == 'on' || $_POST['publish'] == '1') ? 1 : 0;
    $updateStatusQuery = "UPDATE `companies` SET `company_status` = '$new_status' WHERE `id` = '$companyId'";
    $Publish = mysqli_query($conn, $updateStatusQuery);

    ######################## Branch (Publish / Unpublish) ###################
    $updateBranchQuery = "UPDATE `branches` SET `branch_status` = '$new_status' WHERE `branch_of_company` = '$companyName'";
    mysqli_query($conn, $updateBranchQuery);

    ######################## Employee (Publish / Unpublish) ###################
    $updateEmployeeQuery = "UPDATE `employees` SET `employee_status` = '$new_status' WHERE `employee_of_company` = '$companyName'";
    mysqli_query($conn, $updateEmployeeQuery);

    ######################## Product (Publish / Unpublish) ###################
    $updateProductQuery = "UPDATE `products` SET `status` = '$new_status' WHERE `product_for_company` = '$companyName'";
    mysqli_query($conn, $updateProductQuery);

    ################# Fetch all references from the database #################
    $updateReferenceList = "UPDATE `references` SET `reference_status`='$new_status' WHERE `reference_for_company`='$companyName'";
    mysqli_query($conn, $updateReferenceList);


    ################### Execute Query ###################
    if ($Publish) {
        echo "<script>window.location.href='company-control.php';</script>";
    } else {
        $error_message = mysqli_error($conn);
        echo "<script>alert('Error: $error_message')</script>";
    }
}
######################## Publish / Unpublish ###################



################# Add Company ###################
if (isset($_POST["add_company"])) {

    ########### Generate Unique ID #############
    function generateId()
    {
        $prefix = '#ID';
        $randomNumber = rand(1, 9999999);
        return $prefix . $randomNumber;
    }
    $uniqueId = generateId();
    ########### Generate Unique ID #############

    $company_id = $uniqueId;
    $company_name = $_POST['company_name'];
    if (empty($_POST['user_limit'])) {
        $user_limit = "Unlimited";
    } else {
        $user_limit = $_POST['user_limit'];
    }
    $created_on = date("d-m-Y");
    $created_time = date("h:i:s A");
    $company_createdby = $_SESSION['employee_username'];
    $company_status = 1;

    $checkCompany = "SELECT `company_name` FROM `companies` WHERE `company_name` = '$company_name'";
    $checkCompanyQuery = mysqli_query($conn, $checkCompany);

    if (mysqli_num_rows($checkCompanyQuery) > 0) {
        echo "<script>window.location.href='company-control.php';</script>";
    } else {
        $add_company = "INSERT INTO `companies`(`company_id`, `company_name`, `user_limit`, `created_on`, `Created_time`, `company_createdby`, `company_status`) VALUES ('$company_id','$company_name','$user_limit','$created_on','$created_time','$company_createdby','$company_status')";
        $add_company_query = mysqli_query($conn, $add_company);

        if ($add_company_query) {
            ################# Push activity ################
            $activity_id = $guid;
            $activity_content = "New company " . $company_name . " has been added by " . $Role . ".";
            $activity_type = 'Alert';
            $activity_company = "";
            $activity_branch = "";
            $activity_on = date('Y-m-d H:i:s');
            $activity_by = $username;
            $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
            $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
            ################# Push activity ################
            echo "<script>window.location.href='company-control.php';</script>";
        } else {
            echo "<script>alert('Error adding company!');</script>";
        }
    }
}
################# Add Company ###################


######################### Delete Company ###########################
if (isset($_POST['delete_company'])) {
    $companyId = $_POST['companyId'];
    $company_name = $_POST['company_name'];

    ####################### Company Delete #######################
    $deleteCompany = "DELETE FROM `companies` WHERE `id` = '$companyId'";
    $checkDelete = mysqli_query($conn, $deleteCompany);

    // ####################### Branch Delete #######################
    // $deletebranchQuery = "DELETE FROM `branches` WHERE `branch_of_company` = '$company_name'";
    // mysqli_query($conn, $deletebranchQuery);

    // ####################### Staff Delete #######################
    // $deletebranchQuery = "DELETE FROM `employees` WHERE `employee_of_company` = '$company_name'";
    // mysqli_query($conn, $deletebranchQuery);

    // ####################### Imported Lead Delete #######################
    // $deletebranchQuery = "DELETE FROM `importleads` WHERE `lead_for_company` = '$company_name'";
    // mysqli_query($conn, $deletebranchQuery);

    // ####################### Lead Delete #######################
    // $deletebranchQuery = "DELETE FROM `leads` WHERE `lead_for_company` = '$company_name'";
    // mysqli_query($conn, $deletebranchQuery);

    // ####################### Notifications Delete #######################
    // $deletebranchQuery = "DELETE FROM `notifications` WHERE `notify_company` = '$company_name'";
    // mysqli_query($conn, $deletebranchQuery);

    // ####################### Product Delete #######################
    // $deletebranchQuery = "DELETE FROM `products` WHERE `product_for_company` = '$company_name'";
    // mysqli_query($conn, $deletebranchQuery);

    // ####################### References Delete #######################
    // $deletebranchQuery = "DELETE FROM `references` WHERE `reference_for_company` = '$company_name'";
    // mysqli_query($conn, $deletebranchQuery);

    if ($checkDelete) {
        ################# Push activity ################
        $activity_id = $guid;
        $activity_content = "The company " . $company_name . " has been removed by " . $Role . ".";
        $activity_type = 'Alert';
        $activity_company = "";
        $activity_branch = "";
        $activity_on = date('Y-m-d H:i:s');
        $activity_by = $username;
        $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
        $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
        ################# Push activity ################
        echo "<script>window.location.href='company-control.php'</script>";
    } else {
        echo "<script>alert('Someting went wrong!');</script>";
    }
} elseif (isset($_POST['edit_company'])) {
    $companyId = $_POST['companyId'];
    echo "<script>window.location.href='company-edit.php?id=$companyId';</script>";
    exit;
}
######################### Delete Company ##########################

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
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <h4 class="card-title"> Company List </h4>

                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#CompanyAdd">
                                    Add Company
                                </button>

                                <!-- ===================== Add Company ===================== -->
                                <div class="modal fade" id="CompanyAdd" tabindex="-1" aria-labelledby="CompanyAddTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="#" method="post" enctype="multipart/form-data">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="CompanyAddTitle">Add Company</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Company Name -->
                                                    <div class="col-lg-12">
                                                        <div class="mb-3">
                                                            <label for="company_name" class="form-label text-dark">Company Name <small class="text-success"> ( Required )</small></label>
                                                            <input type="text" id="company_name" name="company_name" class="form-control" placeholder="Company Name" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="user_limit" class="form-label text-dark">User Limit <small class="text-success"> ( Required )</small></label>
                                                            <input type="number" id="user_limit" name="user_limit" class="form-control" placeholder="Set user limit">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" name="add_company" class="btn btn-primary">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- ===========x========= Add Company ============x======== -->
                            </div>

                            <div class="card-body">
                                <div id="table-company-search"></div>
                            </div>
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
        const CompanyData = [
            <?php
            $counterValue = 1; // Initialize the counter for So.No.
            if ($counterValue <= mysqli_num_rows($company_list_result)) {
                while ($myCompany = $company_list_result->fetch_assoc()) { ?>[
                        gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter
                        "<?php echo $myCompany['company_id']; ?>",
                        "<?php echo $myCompany['company_name']; ?>",
                        "<?php echo $myCompany['user_limit']; ?>",
                        "<?php echo $myCompany['created_on']; ?>",
                        "<?php echo $myCompany['created_time']; ?>",

                        gridjs.html(`
                                   <form action="#" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="companyId" value="<?php echo $myCompany['id']; ?>">
                                        <input type="hidden" name="publish" value="0">
                                        <div class="form-check form-switch flex-box justify-content-center align-items-center">
                                             <input class="form-check-input" name="publish" type="checkbox" role="switch" id="flexSwitchCheckChecked<?php echo $myCompany['id']; ?>" <?php echo ($myCompany['company_status'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                        </div>
                                   </form>`),

                        gridjs.html(`
                                   <div class="row gap-1">
                                        <div class="col-5">
                                        <form action="#" method="post" enctype="multipart/form-data">
                                             <input type="hidden" name="companyId" value="<?php echo $myCompany['id']; ?>">
                                                  <button type="submit" name="edit_company" value="<?php echo $myCompany['id']; ?>" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#CompanyEdit">
                                                       <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                                  </button>
                                             </form>
                                        </div>
                                        <div class="col-5">
                                             <form action="#" method="post" enctype="multipart/form-data">
                                                  <input type="hidden" name="companyId" value="<?php echo $myCompany['id']; ?>">
                                                  <input type="hidden" name="company_name" value="<?php echo $myCompany['company_name']; ?>">
                                                  <button type="submit" name="delete_company" value="<?php echo $myCompany['id']; ?>" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                             </form>
                                        </div>    
                                   </div>`)
                    ],
            <?php }
            } ?>
        ];

        if (document.getElementById("table-company-search")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No",
                        width: "50px"
                    },
                    {
                        name: "Company ID",
                        width: "130px"
                    },
                    {
                        name: "Companies",
                        width: "200px"
                    },
                    {
                        name: "User Limit",
                        width: "50px"
                    },
                    {
                        name: "Created On",
                        width: "150px"
                    },
                    {
                        name: "Added Time",
                        width: "150px"
                    },
                    {
                        name: "Status",
                        width: "80px",
                    },
                    {
                        name: "Actions",
                        width: "110px",
                    },
                ],
                data: CompanyData,
                pagination: {
                    limit: 10
                },
                search: true,
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
            }).render(document.getElementById("table-company-search"));
        }
    </script>

</body>

</html>