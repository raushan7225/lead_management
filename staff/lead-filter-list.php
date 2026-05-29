<?php
include("../config/db.php");
include("../config/session.php");
include("../config/fornotification.php");

#########  Manage Followup / Create Quotation / Create Report / Delete  ###########
if (isset($_POST['manage_followup'])) {
    $mylead_id = $_POST['mylead_id'];
    echo "<script>window.location.href='manage-followup.php?id=$mylead_id';</script>";
    exit;
} elseif (isset($_POST['create_qutation'])) {
    $mylead_id = $_POST['mylead_id'];
    echo "<script>window.location.href='create-quotation.php?id=$mylead_id';</script>";
    exit;
} elseif (isset($_POST['create_report'])) {
    $mylead_id = $_POST['mylead_id'];
    echo "<script>window.location.href='create-report.php?id=$mylead_id';</script>";
    exit;
} elseif (isset($_POST['delete_lead'])) {
    $mylead_id = $_POST['mylead_id'];
    $deleteLeadQuery = "DELETE FROM `leads` WHERE `id` = '$mylead_id'";
    $checkDelete = mysqli_query($conn, $deleteLeadQuery);

    if ($checkDelete) {
        echo "<script>window.location.href='lead-list.php'</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
#########  Manage Followup / Create Quotation / Create Report / Delete  ###########



######### Fetch all branches from the database #########
$branch_list = "SELECT * FROM `branches`";
$branch_list_result = $conn->query($branch_list);
$branchename = [];
if (mysqli_num_rows($branch_list_result) > 0) {
    while ($branch = $branch_list_result->fetch_assoc()) {
        $branchename[] = $branch;
    }
}
######### Fetch all branches from the database #########


################### Filter Applied ###################
if (isset($_GET['lead_for_branch']) && isset($_GET['lead_assign_to']) && isset($_GET['lead_for_person']) && isset($_GET['lead_status'])) {
    $lead_for_branch = $_GET['lead_for_branch'];
    $lead_assign_to = $_GET['lead_assign_to'];
    $lead_for_person = $_GET['lead_for_person'];
    $lead_status = $_GET['lead_status'];

    ######### Fetch all leads from the database #########
    $lead_filter = "SELECT * FROM `leads` WHERE `lead_for_branch` = '$lead_for_branch' AND `lead_updatedby` = '$lead_updatedby' AND `lead_for_person` = '$lead_for_person' AND `lead_status` = '$lead_status'";
    $lead_filter_result = $conn->query($lead_filter);
    if (mysqli_num_rows($lead_filter_result) > 0) {
        while ($leadfilter = $lead_filter_result->fetch_assoc()) {
            $lead_for_branch = $leadfilter['lead_for_branch'];
            $lead_updatedby = $leadfilter['lead_updatedby'];
            $lead_for_person = $leadfilter['lead_for_person'];
            $lead_status = $leadfilter['lead_status'];
        }
    }
    ######### Fetch all leads from the database #########


    ######### Fetch all leads from the database #########
    $lead_list = "SELECT * FROM `leads` WHERE `lead_for_branch` = '$lead_for_branch' AND `lead_updatedby` = '$lead_updatedby' AND `lead_for_person` = '$lead_for_person' AND `lead_status` = '$lead_status'";
    $lead_list_result = $conn->query($lead_list);
?>

    <!DOCTYPE html>
    <html lang="en-US">

    <head>
        <title>Lead List</title>
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
                <div class="container-xxl">
                    <div class="row">
                        <form action="#" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Lead Search Request</h4>
                                        </div>

                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Employee Branch -->
                                                <div class="col-lg-3">
                                                    <div class="mb-3">
                                                        <label for="lead_for_branch" class="form-label"> Branch <small class="text-success">(Required)</small></label>
                                                        <select class="form-control" id="lead_for_branch" name="lead_for_branch" data-choices required>
                                                            <option value="">Select Branch </option>
                                                            <?php foreach ($branchename as $mybranch) { ?>
                                                                <option value="<?php echo $mybranch['branch_name']; ?>" <?php echo ($mybranch['branch_name'] == $lead_for_branch) ? ' selected' : ''; ?>><?php echo $mybranch['branch_name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Employee Name -->
                                                <div class="col-lg-3">
                                                    <div class="mb-3">
                                                        <label for="lead_assign_to" class="form-label"> Employee </label>
                                                        <select class="form-lead_assign_to" id="lead_assign_to" name="lead_assign_to" data-choices>
                                                            <option value="<?php echo  $employeename['employee_name']; ?>"> All </option>
                                                            <?php foreach ($employeename as $employe) { ?>
                                                                <option value="<?php echo  $employe['employee_name']; ?>" <?php echo ($employe['employee_name'] == $lead_updatedby) ? ' selected' : ''; ?>><?php echo  $employe['employee_name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Contact Person -->
                                                <div class="col-lg-3">
                                                    <div class="mb-3">
                                                        <label for="lead_person" class="form-label text-dark"> Contact Person </label>
                                                        <input type="text" id="lead_person" name="lead_person" class="form-control" placeholder="Contact Person" value="<?php echo $lead_for_person; ?>">
                                                    </div>
                                                </div>

                                                <!-- Lead Type  -->
                                                <div class="col-lg-3">
                                                    <div class="mb-3">
                                                        <label for="lead_type" class="form-label text-dark"> Status </label>
                                                        <select class="form-control" name="lead_type" id="lead_type" data-choices data-choices-search-false>
                                                            <option value=""> Lead Status </option>
                                                            <option value="Hot" <?php echo ($lead_status == 'Hot') ? ' selected' : ''; ?>>1. Hot</option>
                                                            <option value="Cold" <?php echo ($lead_status == 'Cold') ? ' selected' : ''; ?>>2. Cold</option>
                                                            <option value="Warm" <?php echo ($lead_status == 'Warm') ? ' selected' : ''; ?>>3. Warm</option>
                                                            <option value="Cancel" <?php echo ($lead_status == 'Cancel') ? ' selected' : ''; ?>>5. Cancel</option>
                                                            <option value="Completed" <?php echo ($lead_status == 'Completed') ? ' selected' : ''; ?>>6. Completed</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="row justify-content-between g-2">
                                                        <div class="col-lg-2">
                                                            <button type="submit" name="search_lead" class="btn btn-soft-success w-100">Search Now</button>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <a href="lead-list.php" class="btn btn-primary w-100">Cancel</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>


                        <!-- ============= Lead List ============= -->
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="d-flex card-header justify-content-between align-items-center">
                                    <div>
                                        <h4 class="card-title"> Lead List </h4>
                                    </div>
                                    <div class="dropdown">
                                        <a href="lead-add.php" class="btn btn-primary">Add Lead</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="table-lead-search"></div>
                                </div>
                            </div>
                        </div>
                        <!-- ============= Lead List ============= -->
                    </div>
                </div>
            </div>
            <!-- ==================================================== -->
            <!-- End Page Content -->
            <!-- ==================================================== -->

            <!-- ========== Footer Start ========== -->
            <?php include("../footer.php"); ?>
            <!-- ========== Footer End ========== -->

        </div>
        <!-- END Wrapper -->

        <!-- Vendor Javascript (Require in all Page) -->
        <script src="../assets/js/vendor.js"></script>

        <!-- App Javascript (Require in all Page) -->
        <script src="../assets/js/app.js"></script>

        <!-- Grid Js -->
        <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

        <script>
            // Branch Table Data
            const leadData = [
                <?php if ($lead_list_result->num_rows > 0) {
                    while ($lead = $lead_list_result->fetch_assoc()) { ?>[
                            gridjs.html(`
                              <div clss="col-sm-12">
                                   <div class="row gap-2">
                                        <div class="col-lg-2">
                                             <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="success-tooltip" data-bs-title="View the lead details.">
                                                  <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                                  <button type="submit" name="manage_followup" value="<?php echo $lead['id']; ?>" class="btn btn-soft-success btn-sm"><iconify-icon icon="solar:eye-scan-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                             </form>
                                        </div>
                                        <div class="col-lg-2">
                                             <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="primary-tooltip" data-bs-title="View the lead quotation.">
                                                  <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                                  <button type="submit" name="create_qutation" value="<?php echo $lead['id']; ?>" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:tag-price-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                             </form>
                                        </div>
                                        <div class="col-lg-2">
                                             <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="info-tooltip" data-bs-title="View the lead report.">
                                                  <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                                  <button type="submit" name="create_report" value="<?php echo $lead['id']; ?>" class="btn btn-soft-info btn-sm"><iconify-icon icon="solar:graph-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                             </form>
                                        </div>
                                        <div class="col-lg-2">
                                             <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="danger-tooltip" data-bs-title="Delete the lead.">
                                                  <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                                  <button type="submit" name="delete_lead" value="<?php echo $lead['id']; ?>" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                             </form>
                                        </div>    
                                   </div>
                              </div>`),

                            "<?php echo $lead['lead_customer_name']; ?>",
                            "<?php echo $lead['lead_customer_contact'] ?>",
                            "<?php echo $lead['lead_email']; ?>",
                            "<?php echo $lead['lead_whatsapp']; ?>",
                            "<?php echo $lead['lead_updatedby']; ?>"
                        ],
                <?php }
                } else {
                    echo "<script>alert('No Lead Found!');</script>";
                } ?>
            ];

            // Initialize Grid.js Table
            if (document.getElementById("table-lead-search")) {
                new gridjs.Grid({
                    columns: [{
                            name: "Actions",
                            width: "210px"
                        },
                        "Person Name",
                        {
                            name: "Contact",
                            width: "200px"
                        },
                        {
                            name: "Email",
                            width: "300px"
                        },
                        {
                            name: "Whatsapp",
                            width: "100px"
                        },
                        "Handle By",
                    ],
                    pagination: {
                        limit: 10
                    },
                    search: true,
                    data: leadData
                }).render(document.getElementById("table-lead-search"));
            }
        </script>

    </body>

    </html>
<?php } ?>