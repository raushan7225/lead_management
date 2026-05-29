<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

################## Fetch all branches from the database #################
$branch_list = "SELECT * FROM `branches` WHERE `branch_of_company`='$Company' AND `branch_status` = '1'";
$branch_list_result = $conn->query($branch_list);
$branchename = [];
if ($branch_list_result && mysqli_num_rows($branch_list_result) > 0) {
    while ($branch = $branch_list_result->fetch_assoc()) {
        $branchename[] = $branch;
    }
}

######### Fetch all employees from the database #########
$Employeename = [];
if (isset($_GET['lead_for_branch']) && !empty($_GET['lead_for_branch'])) {
    $employee_of_branch = $_GET['lead_for_branch'];
    $employee_list_sql = "  SELECT *  FROM `employees`  WHERE `employee_of_company`='$Company'  AND `employee_of_branch`='$employee_of_branch'  AND `employee_status`='1'  AND `employee_user_role`='Staff'  ORDER BY `employee_name` ASC";
} else {
    $employee_list_sql = " SELECT * FROM `employees` WHERE `employee_of_company`='$Company' AND `employee_status`='1' AND `employee_user_role`='Staff' ORDER BY `employee_name` ASC";
}
$employee_list_result = $conn->query($employee_list_sql);
if ($employee_list_result) {
    $Employeename = mysqli_fetch_all($employee_list_result, MYSQLI_ASSOC);
}
######### End employee fetch #########

######### Initialize base lead filter #########
$lead_filter = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company'";

######### Apply filters based on POST request ##########
if (isset($_POST['search_lead'])) {
    $lead_followup_start_date = trim($_POST['lead_followup_start_date']);
    $lead_followup_end_date   = trim($_POST['lead_followup_end_date']);
    $lead_for_branch          = trim($_POST['lead_for_branch']);
    $lead_assign_to           = trim($_POST['lead_assign_to']);
    $lead_customer_name       = trim($_POST['lead_customer_name']);
    $lead_type                = trim($_POST['lead_type']);

    $urlParams = [];

    if (!empty($lead_followup_start_date)) {
        $urlParams[] = "lead_followup_start_date=" . urlencode($lead_followup_start_date);
    }
    if (!empty($lead_followup_end_date)) {
        $urlParams[] = "lead_followup_end_date=" . urlencode($lead_followup_end_date);
    }
    if (!empty($lead_for_branch)) {
        $urlParams[] = "lead_for_branch=" . urlencode($lead_for_branch);
    }
    if (!empty($lead_assign_to)) {
        $urlParams[] = "lead_assign_to=" . urlencode($lead_assign_to);
    }
    if (!empty($lead_customer_name)) {
        $urlParams[] = "lead_customer_name=" . urlencode($lead_customer_name);
    }
    if (!empty($lead_type)) {
        $urlParams[] = "lead_type=" . urlencode($lead_type);
    }

    ######### Redirect with query parameters for dynamic filtering ###########
    if (!empty($urlParams)) {
        $queryString = implode('&', $urlParams);
        echo "<script>window.location.href='complete-lead-report.php?$queryString';</script>";
        exit;
    }
}

############# Apply filters from GET parameters #######################
$lead_followup_start_date = isset($_GET['lead_followup_start_date']) ? $_GET['lead_followup_start_date'] : '';
$lead_followup_end_date   = isset($_GET['lead_followup_end_date']) ? $_GET['lead_followup_end_date'] : '';
$lead_for_branch          = isset($_GET['lead_for_branch']) ? $_GET['lead_for_branch'] : '';
$lead_assign_to           = isset($_GET['lead_assign_to']) ? $_GET['lead_assign_to'] : '';
$lead_customer_name       = isset($_GET['lead_customer_name']) ? $_GET['lead_customer_name'] : '';
$lead_type                = isset($_GET['lead_type']) ? $_GET['lead_type'] : '';

############# Start building the final query based on available filters #######################
$lead_filter .= " AND 1=1"; // Base condition to start filtering

if (!empty($lead_followup_start_date)) {
    $lead_filter .= " AND `lead_next_followup_date` >= '$lead_followup_start_date'";
}
if (!empty($lead_followup_end_date)) {
    $lead_filter .= " AND `lead_next_followup_date` <= '$lead_followup_end_date'";
}
if (!empty($lead_for_branch)) {
    $lead_filter .= " AND `lead_for_branch` = '$lead_for_branch'";
}
if (!empty($lead_assign_to)) {
    $lead_filter .= " AND `lead_assign_to` = '$lead_assign_to'";
}
if (!empty($lead_customer_name)) {
    $lead_filter .= " AND `lead_customer_name` LIKE '%$lead_customer_name%'";
}
if (!empty($lead_type)) {
    $lead_filter .= " AND `lead_type` = '$lead_type'";
}

############## Execute the final query to fetch filtered leads ################
// $lead_filter_result = $conn->query($lead_filter);

// Check if at least one GET filter is applied
if (!empty($lead_filter_result) || !empty($lead_followup_end_date) || !empty($lead_for_branch) || !empty($lead_updatedby) || !empty($lead_customer_name) || !empty($lead_type)) {
    $lead_filter_result = $conn->query($lead_filter);
} else {
    $lead_filter_result = false; // No filters applied, so don't show any data
}

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Followup Leads Report</title>
    <?php include("head.php"); ?>

    <style>
        #table th {
            background-color: #ff6c2f;
            color: #fff;
        }

        #table td {
            text-align: center;
        }

        .keep-open .dropdown-toggle::after,
        .export .dropdown-toggle::after {
            border-top: none;
            top: 0;
            font-weight: 400;
            margin: 0;
            font-size: 15px;
        }

        .keep-open .dropdown-toggle::after {
            content: "\2611" !important;
            transform: scale(1.5);
        }

        .export .dropdown-toggle::after {
            padding: 2px 2px;
            font-size: 8px;
            transform: scale(1.1);
            border: 1px solid;
        }

        .bootstrap-table .table thead th:nth-child(2) .th-inner {
            width: 120px !important;
        }

        .bootstrap-table .table thead th:nth-child(3) .th-inner {
            width: 200px !important;
        }

        .bootstrap-table .table thead th:nth-child(4) .th-inner,
        .bootstrap-table .table thead th:nth-child(5) .th-inner {
            width: 180px !important;
        }

        .bootstrap-table .table thead th:nth-child(6) .th-inner {
            width: 300px !important;
        }

        .bootstrap-table .table thead th:nth-child(7) .th-inner {
            width: 150px !important;
        }

        .bootstrap-table .table thead th:nth-child(8) .th-inner {
            width: 250px !important;
        }

        .bootstrap-table .table thead th:nth-child(9) .th-inner {
            width: 300px !important;
        }

        .bootstrap-table .table thead th:nth-child(10) .th-inner {
            width: 180px !important;
        }

        .bootstrap-table .table thead th:nth-child(11) .th-inner {
            width: 180px !important;
        }

        .bootstrap-table .table thead th:nth-child(12) .th-inner {
            width: 170px !important;
        }

        .bootstrap-table .table thead th:nth-child(13) .th-inner {
            width: 180px !important;
        }

        .bootstrap-table .table thead th:nth-child(14) .th-inner {
            width: 180px !important;
        }

        .bootstrap-table .table thead th:nth-child(15) .th-inner {
            width: 150px !important;
        }

        .bootstrap-table .table thead th:nth-child(16) .th-inner {
            width: 150px !important;
        }

        .bootstrap-table .table thead th:nth-child(17) .th-inner {
            width: 350px !important;
        }

        .bootstrap-table .table thead th:nth-child(18) .th-inner {
            width: 100px !important;
        }

        .bootstrap-table .table thead th:nth-child(19) .th-inner {
            width: 150px !important;
        }

        .bootstrap-table .table thead th:last-child .th-inner {
            width: 300px !important;
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

        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">
            <div class="container-xxl">
                <div class="row">
                    <div class="col-xl-12">
                        <form action="#" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Followup Leads Filter</h4>
                                        </div>

                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Starting Date -->
                                                <div class="col-lg-3">
                                                    <div class="mb-3">
                                                        <label for="lead_followup_start_date" class="form-label text-dark">Starting Date</label>
                                                        <input type="date" id="lead_followup_start_date" name="lead_followup_start_date" placeholder="Starting Date" class="form-control" value="<?php echo htmlspecialchars($lead_followup_start_date); ?>">
                                                    </div>
                                                </div>

                                                <!-- Ending Date -->
                                                <div class="col-lg-3">
                                                    <div class="mb-3">
                                                        <label for="lead_followup_end_date" class="form-label text-dark">Ending Date</label>
                                                        <input type="date" id="lead_followup_end_date" name="lead_followup_end_date" placeholder="Ending Date" class="form-control" value="<?php echo htmlspecialchars($lead_followup_end_date); ?>">
                                                    </div>
                                                </div>

                                                <!-- Employee Branch -->
                                                <div class="col-lg-3">
                                                    <div class="mb-3">
                                                        <label for="lead_for_branch" class="form-label">Branch</label>
                                                        <select class="form-control" id="lead_for_branch" name="lead_for_branch" data-choices>
                                                            <option value="">Select Branch</option>
                                                            <?php foreach ($branchename as $mybranch) { ?>
                                                                <option value="<?php echo $mybranch['branch_name']; ?>"
                                                                    <?php echo ($lead_for_branch === $mybranch['branch_name']) ? 'selected' : ''; ?>>
                                                                    <?php echo $mybranch['branch_name']; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Handled By Employee Dropdown -->
                                                <div class="col-lg-3">
                                                    <div class="mb-3">
                                                        <label for="lead_assign_to" class="form-label">Handled by</label>
                                                        <select class="form-control" id="lead_assign_to" name="lead_assign_to" data-choices>
                                                            <option value="">All Employees</option>
                                                            <?php foreach ($Employeename as $employee): ?>
                                                                <option value="<?php echo $employee['employee_username']; ?>"
                                                                    <?php echo ($lead_assign_to === $employee['employee_username']) ? 'selected' : ''; ?>>
                                                                    <?php echo $employee['employee_name']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Contact Person -->
                                                <div class="col-lg-3">
                                                    <div class="mb-3">
                                                        <label for="lead_customer_name" class="form-label text-dark">Customer Name</label>
                                                        <input type="text" id="lead_customer_name" name="lead_customer_name" class="form-control" placeholder="Contact Person" value="<?php echo htmlspecialchars($lead_customer_name); ?>">
                                                    </div>
                                                </div>

                                                <!-- Lead Type  -->
                                                <div class="col-lg-3">
                                                    <div class="mb-3">
                                                        <label for="lead_type" class="form-label text-dark">Status</label>
                                                        <select class="form-control" name="lead_type" id="lead_type" data-choices data-choices-search-false>
                                                            <option value="">Lead Status</option>
                                                            <option value="Hot" <?php echo ($lead_type === 'Hot') ? 'selected' : ''; ?>>1. Hot</option>
                                                            <option value="Cold" <?php echo ($lead_type === 'Cold') ? 'selected' : ''; ?>>2. Cold</option>
                                                            <option value="Warm" <?php echo ($lead_type === 'Warm') ? 'selected' : ''; ?>>3. Warm</option>
                                                            <option value="Place Order" <?php echo ($lead_type === 'Place Order') ? 'selected' : ''; ?>>5. Place Order</option>
                                                            <option value="Cancel" <?php echo ($lead_type === 'Cancel') ? 'selected' : ''; ?>>6. Cancel</option>
                                                            <option value="Completed" <?php echo ($lead_type === 'Completed') ? 'selected' : ''; ?>>7. Completed</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="row justify-content-between g-2">
                                                        <div class="col-lg-2">
                                                            <button type="submit" name="search_lead" class="btn btn-soft-success w-100">Search Now</button>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <a href="complete-lead-report.php" class="btn btn-primary w-100">Reset</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <h4 class="card-title">Followup Leads Report</h4>
                            </div>
                            <div class="card-body">
                                <table id="table"
                                    data-show-columns="true"
                                    data-show-export="true"
                                    data-show-toggle="true"
                                    width="100%">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include("../footer.php"); ?>
    </div>

    <script src="../assets/js/vendor.js"></script>
    <script src="../assets/js/app.js"></script>

    <!-- Flatepicker Demo Js -->
    <script src="../assets/js/components/form-flatepicker.js"></script>

    <script>
        document.getElementById('lead_followup_start_date').flatpickr();
        document.getElementById('lead_followup_end_date').flatpickr();
    </script>

    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../assets/vendor/bootstrap-table-master/js/bootstrap-table.min.js"></script>
    <script src="../assets/vendor/bootstrap-table-master/js/tableExport.min.js"></script>
    <script src="../assets/vendor/bootstrap-table-master/js/bootstrap-table-mobile.js"></script>
    <script src="../assets/vendor/bootstrap-table-master/js/bootstrap-table-export.js"></script>

    <script>
        $(function() {
             // Fetch lead data from PHP dynamically
            const tableData = <?php echo json_encode($lead_filter_result->fetch_all(MYSQLI_ASSOC)); ?>;

            // Function to format the date to 'dd-mm-yyyy'
            function formatDate(date) {
                if (!date || isNaN(new Date(date))) { // Check if date is invalid or missing
                    return '00-00-0000'; // Return fallback date
                }

                const d = new Date(date);
                let day = d.getDate();
                let month = d.getMonth() + 1; // Months are zero-based
                const year = d.getFullYear();

                // Pad single digit day and month with a leading zero
                day = (day < 10 ? '0' : '') + day;
                month = (month < 10 ? '0' : '') + month;

                return day + '-' + month + '-' + year;
            }

            // Add a counter to each row and format date fields
            tableData.forEach((row, index) => {
                row.SoNo = index + 1;

                // Format date fields here
                row.lead_followup_date = formatDate(row.lead_followup_date);
                row.lead_next_followup_date = formatDate(row.lead_next_followup_date);
                row.lead_instalation_date = formatDate(row.lead_instalation_date);
            });

            $('#table').bootstrapTable({
                pagination: true,
                search: true,
                columns: [{
                        field: 'SoNo',
                        title: "So.No."
                    },
                    {
                        field: 'lead_id',
                        title: 'Customer ID'
                    },
                    {
                        field: 'lead_customer_name',
                        title: 'Customer Name'
                    },
                    {
                        field: 'lead_customer_contact',
                        title: 'Customer Contact'
                    },
                    {
                        field: 'lead_alternate_contact',
                        title: 'Alternate Contact'
                    },
                    {
                        field: 'lead_for_branch',
                        title: 'Branch'
                    },
                    {
                        field: 'lead_customer_type',
                        title: 'Customer Type'
                    },
                    {
                        field: 'lead_state',
                        title: 'State'
                    },
                    {
                        field: 'lead_delivery_address',
                        title: 'Address'
                    },
                    {
                        field: 'lead_next_followup_date',
                        title: 'Next Followup Date'
                    },
                    {
                        field: 'lead_next_followup_time',
                        title: 'Next Followup Time'
                    },
                    {
                        field: 'lead_type',
                        title: 'Lead Type'
                    },
                    {
                        field: 'lead_status',
                        title: 'Lead Status'
                    },
                    {
                        field: 'lead_updatedby',
                        title: 'Handled by'
                    },
                    {
                        field: 'assign_technician',
                        title: 'Assigned Technician'
                    },
                    {
                        field: 'lead_instalation_date',
                        title: 'Installation Date'
                    },
                    {
                        field: 'lead_product_services',
                        title: 'Machine Name'
                    },
                    {
                        field: 'lead_product_quantity',
                        title: 'Quantity'
                    },
                    {
                        field: 'lead_sub_total',
                        title: 'Sub Total'
                    },
                    {
                        field: 'lead_other_charges',
                        title: 'Other Charges'
                    },
                    {
                        field: 'lead_grand_total',
                        title: 'Grand Total'
                    },
                    {
                        field: 'lead_remark',
                        title: 'Remark'
                    }
                ],
                data: tableData,
                showExport: true,
                exportTypes: ['csv', 'excel'],
                exportOptions: {
                    fileName: 'leads-report'
                }
            });
            $('#table').css({
                'min-width': '2900px',
                'font-size': '15px',
                'text-align': 'center'
            });

            $('#table th').css({
                'background-color': '#ff6c2f',
                'color': '#fff'
            });
        });
    </script>
</body>

</html>