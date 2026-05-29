<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");


######### Manage Followup / Create Quotation / Create Report / Delete ###########
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mylead_id = $_POST['mylead_id'];

    if (isset($_POST['manage_followup'])) {
        echo "<script>window.location.href='manage-followup.php?id=$mylead_id';</script>";
        exit;
    } elseif (isset($_POST['create_qutation'])) {
        echo "<script>window.location.href='create-quotation.php?id=$mylead_id';</script>";
        exit;
    } elseif (isset($_POST['create_report'])) {
        echo "<script>window.location.href='create-report.php?id=$mylead_id';</script>";
        exit;
    }
}
######### Manage Followup / Create Quotation / Create Report / Delete ###########


######### Fetch all employees from the database #########
$Employee_list_query = "SELECT * FROM `employees` WHERE `employee_of_company` = '$Company' AND `employee_of_branch` = '$Branch' AND `employee_user_role` = 'Staff' AND `employee_status` = '1'";
$Employee_list_result = $conn->query($Employee_list_query);
$Employeename = [];
if ($Employee_list_result->num_rows > 0) {
    while ($employee = $Employee_list_result->fetch_assoc()) {
        $Employeename[] = $employee;
    }
}
######### Fetch all employees from the database #########


######### Fetch all leads from the database #########
$lead_filter_query = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch'";
######### Fetch all leads from the database #########


############ Apply Filter On Lead List ##############
if (isset($_POST['search_lead'])) {
    $lead_assign_to = $_POST['lead_assign_to'] ?? '';
    $lead_customer_name = $_POST['lead_customer_name'] ?? '';
    $lead_type = $_POST['lead_type'] ?? '';

    $urlParams = [];

    if (!empty($lead_assign_to)) {
        $urlParams[] = "lead_assign_to=$lead_assign_to";
    }
    if (!empty($lead_customer_name)) {
        $urlParams[] = "lead_customer_name=$lead_customer_name";
    }
    if (!empty($lead_type)) {
        $urlParams[] = "lead_type=$lead_type";
    }

    if (!empty($urlParams)) {
        $queryString = implode('&', $urlParams);
        echo "<script>window.location.href='lead-list.php?$queryString';</script>";
        exit;
    }
}
############ Apply Filter On Lead List ##############


############# Filter Applied on Lead #################
$lead_assign_to = $_GET['lead_assign_to'] ?? null;
$lead_customer_name = $_GET['lead_customer_name'] ?? null;
$lead_type = $_GET['lead_type'] ?? null;
######### Filter all leads from the database #########

$lead_filter_query .= " AND `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `lead_customer_status` = '1'";

if (!empty($lead_assign_to)) {
    $lead_filter_query .= " AND `lead_assign_to` = '$lead_assign_to'";
}
if (!empty($lead_customer_name)) {
    $lead_filter_query .= " AND `lead_customer_name` = '$lead_customer_name'";
}
if (!empty($lead_type)) {
    $lead_filter_query .= " AND `lead_type` = '$lead_type'";
}

$lead_filter_result = $conn->query($lead_filter_query);
$lead_data = [];
if ($lead_filter_result->num_rows > 0) {
    while ($leadfilter = $lead_filter_result->fetch_assoc()) {
        $lead_data[] = $leadfilter;
    }
}
######### Filter all leads from the database #########


######### Fetch all filtered leads from the database #########
$lead_list_query = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `lead_customer_status` = '1'";

if (!empty($lead_assign_to)) {
    $lead_list_query .= " AND `lead_assign_to` = '$lead_assign_to'";
}
if (!empty($lead_customer_name)) {
    $lead_list_query .= " AND `lead_customer_name` = '$lead_customer_name'";
}
if (!empty($lead_type)) {
    $lead_list_query .= " AND `lead_type` = '$lead_type'";
}

$lead_list_query .= " ORDER BY `id` DESC";
$lead_list_result = $conn->query($lead_list_query);
######### Fetch all filtered leads from the database #########
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

                                            <!-- Handled By Employee Dropdown -->
                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label for="lead_assign_to" class="form-label">Handled by</label>
                                                    <select class="form-control" id="lead_assign_to" name="lead_assign_to" data-choices>
                                                        <option value="">All Employees</option>
                                                        <?php foreach ($Employeename as $employee): ?>
                                                            <option value="<?php echo $employee['employee_username']; ?>"
                                                                <?php echo (isset($lead_assign_to) && $employee['employee_username'] == $lead_assign_to) ? 'selected' : ''; ?>>
                                                                <?php echo $employee['employee_name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Customer Name Input -->
                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label for="lead_customer_name" class="form-label text-dark">Customer Name</label>
                                                    <input type="text" id="lead_customer_name" name="lead_customer_name" class="form-control" placeholder="Contact Person" value="<?php echo htmlspecialchars($lead_customer_name); ?>">
                                                </div>
                                            </div>

                                            <!-- Lead Type Dropdown -->
                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label for="lead_type" class="form-label text-dark">Lead Status</label>
                                                    <select class="form-control" name="lead_type" id="lead_type" data-choices data-choices-search-false>
                                                        <option value="">Select Lead Status</option>
                                                        <option value="Hot" <?php echo ($lead_type == 'Hot') ? 'selected' : ''; ?>>1. Hot</option>
                                                        <option value="Cold" <?php echo ($lead_type == 'Cold') ? 'selected' : ''; ?>>2. Cold</option>
                                                        <option value="Warm" <?php echo ($lead_type == 'Warm') ? 'selected' : ''; ?>>3. Warm</option>
                                                        <option value="Place Order" <?php echo ($lead_type == 'Place Order') ? 'selected' : ''; ?>>5. Place Order</option>
                                                        <option value="Cancel" <?php echo ($lead_type == 'Cancel') ? 'selected' : ''; ?>>6. Cancel</option>
                                                        <option value="Completed" <?php echo ($lead_type == 'Completed') ? 'selected' : ''; ?>>7. Completed</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Buttons -->
                                            <div class="col-lg-12">
                                                <div class="row justify-content-between g-2">
                                                    <div class="col-lg-2">
                                                        <button type="submit" name="search_lead" class="btn btn-soft-success w-100">Search Now</button>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <a href="lead-list.php" class="btn btn-primary w-100">Reset</a>
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
        function filterEmployees() {
            const companySelect = document.getElementById('company_name');
            const branchSelect = document.getElementById('lead_for_branch');
            const employeeSelect = document.getElementById('lead_assign_to');
            const selectedCompany = companySelect.value;
            const selectedBranch = branchSelect.value;

            const employeeOptions = employeeSelect.querySelectorAll('option');

            employeeOptions.forEach(option => {
                const branch = option.getAttribute('data-branch');
                const company = option.getAttribute('data-company');

                if ((!selectedBranch || branch === selectedBranch) && (!selectedCompany || company === selectedCompany)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });

            employeeSelect.value = '';
        }
    </script>

    <script>
        const leadData = [
            <?php
            $counterValue = 1;  // Initialize the counter for So.No.
            if ($lead_list_result && $lead_list_result->num_rows > 0) {
                while ($lead = $lead_list_result->fetch_assoc()) { ?>[
                        gridjs.html(`<?php echo $counterValue++; ?>`),

                        gridjs.html(`
                            <div class="col-sm-12">
                                <div class="row gap-2">
                                    <div class="col-3">
                                        <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="success-tooltip" data-bs-title="View the lead details.">
                                            <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                            <button type="submit" name="manage_followup" class="btn btn-soft-success btn-sm"><iconify-icon icon="solar:eye-scan-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                        </form>
                                    </div>
                                    <div class="col-3">
                                        <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="primary-tooltip" data-bs-title="View the lead quotation.">
                                            <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                            <button type="submit" name="create_qutation" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:tag-price-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                        </form>
                                    </div>
                                    <div class="col-3">
                                        <form action="#" method="post" enct="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="info-tooltip" data-bs-title="View the lead report.">
                                            <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                            <button type="submit" name="create_report" class="btn btn-soft-info btn-sm"><iconify-icon icon="solar:graph-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                        </form>
                                    </div>
                                </div>
                            </div>`),

                        gridjs.html(`<?php echo $lead['lead_customer_name']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_delivery_address']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_contact'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_alternate_contact'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_whatsapp'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_email']; ?>`),
                        gridjs.html(`<?php echo date('d-m-Y', strtotime($lead['lead_next_followup_date'])); ?>`),
                        gridjs.html(`<?php echo date('H:i', strtotime($lead['lead_next_followup_time'])); ?>`),
                        gridjs.html(`<?php echo $lead['lead_type']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_status']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_assign_to']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_updatedby']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_remark']; ?>`)
                    ],
            <?php }
            }  ?>
        ];

        // Initialize Grid.js Table
        if (document.getElementById("table-lead-search")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No.",
                        width: "50px"
                    },
                    {
                        name: "Actions",
                        width: "150px"
                    },
                    {
                        name: "Customer Name",
                        width: "150px"
                    },
                    {
                        name: "Address",
                        width: "200px"
                    },
                    {
                        name: "Contact",
                        width: "100px"
                    },
                    {
                        name: "2nd Contact",
                        width: "100px"
                    },
                    {
                        name: "Whatsapp",
                        width: "100px"
                    },
                    {
                        name: "Email",
                        width: "200px"
                    },
                    {
                        name: "Next Followup Date",
                        width: "100px"
                    },
                    {
                        name: "Next Followup Time",
                        width: "100px"
                    },
                    {
                        name: "Lead Type",
                        width: "100px"
                    },
                    {
                        name: "Lead Status",
                        width: "100px"
                    },
                    {
                        name: "Assign To",
                        width: "100px"
                    },
                    {
                        name: "Handled By",
                        width: "100px"
                    },
                    {
                        name: "Remarks",
                        width: "200px"
                    }
                ],
                pagination: {
                    limit: 10
                },
                search: true,
                data: leadData,
                style: {
                    table: {
                        'min-width': '2550px',
                        'font-size': '15px',
                        'text-align': 'center',
                    },
                    th: {
                        'background-color': '#ff6c2f',
                        'color': '#fff'
                    }
                }
            }).render(document.getElementById("table-lead-search"));
        }
    </script>
</body>

</html>