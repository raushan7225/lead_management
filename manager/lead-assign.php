<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");


####################  Manage Followup / Delete  ######################
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $myleadid = $_POST['myleadid'];
    $lead_customer_name = $_POST['lead_customer_name'];

    if (isset($_POST['delete_lead'])) {
        $deleteLeadQuery = "DELETE FROM `importleads` WHERE `id` = '$myleadid'";
        $applyDevLeadQuery = mysqli_query($conn, $deleteLeadQuery);
        if ($applyDevLeadQuery) {
            ################# Push activity ################
            $activity_id = $guid;
            $activity_content = "Some leads removed from imported lead lists by manager " . $username . " of " . $Branch . " branch.";
            $activity_type = 'Alert';
            $activity_company = $Company;
            $activity_branch = $Branch;
            $activity_on = date('Y-m-d H:i:s');
            $activity_by = $username;
            $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
            $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
            ################# Push activity ################
            echo "<script>window.location.href='lead-assign.php'</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['transfer_lead'])) {
        $lead_ids = $_POST['lead_id'] ?? [];
        $lead_assign_to = $_POST['lead_assign_to'];
        $lead_delivery_date = date('Y-m-d');

        foreach ($lead_ids as $lead_id) {
            $lead_uid = '#ID' . mt_rand(1, 9999999);

            // Fetch lead data from importleads
            $sql = "SELECT * FROM importleads WHERE id = '$lead_id'";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);

                // Insert data into leads
                $insert = "INSERT INTO leads (
                lead_id, lead_customer_name, lead_customer_contact, lead_for_company,
                lead_for_branch, lead_delivery_address, lead_email, lead_followup_date,
                lead_next_followup_date, lead_next_followup_time, lead_delivery_date,
                lead_whatsapp, lead_assign_to, lead_status, lead_createdby,
                lead_type, lead_customer_type, lead_customer_status
            ) VALUES (
                '$lead_uid', '{$row['lead_customer_name']}', '{$row['lead_customer_contact']}',
                '{$row['lead_for_company']}', '{$row['lead_for_branch']}',
                '{$row['lead_delivery_address']}', '{$row['lead_email']}',
                '{$row['lead_followup_date']}', '{$row['lead_next_followup_date']}',
                '{$row['lead_next_followup_time']}', '$lead_delivery_date',
                '{$row['lead_whatsapp']}', '$lead_assign_to', '{$row['lead_status']}',
                '{$row['lead_createdby']}', '{$row['lead_type']}',
                '{$row['lead_customer_type']}', '{$row['lead_customer_status']}'
            )";

                if (mysqli_query($conn, $insert)) {
                    // Update importleads
                    $update = "UPDATE importleads SET
                    lead_createdby = '{$row['lead_createdby']}',
                    lead_assign_to = '$lead_assign_to'
                    WHERE id = '$lead_id'";
                    mysqli_query($conn, $update);

                    ################# Push activity ################
                    $activity_id = $guid;
                    $activity_content = "Some lead assigned to staff " . $lead_assign_to . " by manager " . $username . " of " . $Branch . " branch.";
                    $activity_type = 'Alert';
                    $activity_company = $Company;
                    $activity_branch = $Branch;
                    $activity_on = date('Y-m-d H:i:s');
                    $activity_by = $username;
                    $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
                    $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
                    ################# Push activity ################
                    echo "<script>window.location.href='lead-assign.php'</script>";
                }
            }
        }

        echo "<script>window.location.href='lead-assign.php'</script>";
    }
}
####################  Manage Followup / Delete  ######################


############# Fetch all employees from the database #########
$Employee_list = "SELECT * FROM `employees` WHERE `employee_of_company`='$Company' AND `employee_of_branch` = '$Branch' AND `employee_user_role` = 'Staff' AND `employee_status` = '1'";
$Employee_list_result = $conn->query($Employee_list);
$Employeename = $Employee_list_result ? mysqli_fetch_all($Employee_list_result, MYSQLI_ASSOC) : [];
################## Lead Assign TO Staff ####################

################ Fetch Leads From DB ################
$lead_list = "SELECT * FROM `importleads` WHERE `lead_for_branch` = '$Branch' AND `lead_createdby` = '$username' AND `lead_assign_to` = '' ORDER BY `id` DESC";
$lead_list_result = $conn->query($lead_list);
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Lead Assign</title>
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
                    <form action="#" method="POST" enctype="multipart/form-data" id="lead_transfer">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Lead Assign</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="lead_assign_to" class="form-label">Assign To</label>
                                                    <select class="form-control" id="lead_assign_to" name="lead_assign_to" data-choices>
                                                        <option value="">All Employees</option>
                                                        <?php foreach ($Employeename as $employee): ?>
                                                            <option value="<?php echo $employee['employee_username']; ?>" <?php echo (isset($lead_assign_to) && $employee['employee_username'] == $lead_assign_to) ? 'selected' : ''; ?>>
                                                                <?php echo $employee['employee_name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="row justify-content-between g-2">
                                                    <div class="col-lg-2">
                                                        <button type="submit" name="transfer_lead" class="btn btn-soft-success w-100"> Lead Assigning</button>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <a href="lead-transfer.php" class="btn btn-primary w-100"> Cancel </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="d-flex card-header justify-content-between align-items-center">
                                <h4 class="card-title">All Lead List </h4>
                                <div class="text-center">
                                    <span class="mx-6"><?php echo date('d-m-Y'); ?> | <span id="time"></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="table-lead-search"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- footer -->
        <?php include("../footer.php"); ?>
        <!-- enf footer -->

    </div>

    <!-- Linked Js Files -->
    <script src="../assets/js/vendor.js"></script>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

    <script>
        const leadData = [
            <?php
            $counterValue = 1;
            if ($lead_list_result && $lead_list_result->num_rows > 0) {
                while ($lead = $lead_list_result->fetch_assoc()) { ?>[
                        gridjs.html(`<?php echo $counterValue++; ?>`),

                        gridjs.html(`<div class="row gap-2"><div class="col-12">
                        <input type="checkbox" class="form-check-input" name="lead_id[]" value="<?php echo $lead['id']; ?>" form="lead_transfer">
                        </div></div>`),

                        gridjs.html(`
                        <div class="col-sm-12">
                            <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="danger-tooltip" data-bs-title="Delete the lead.">
                                <input type="hidden" name="myleadid" value="<?php echo $lead['id']; ?>">
                                <input type="hidden" name="lead_customer_name" value="<?php echo $lead['lead_customer_name']; ?>">
                                <button type="submit" name="delete_lead" value="<?php echo $lead['id']; ?>" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
                            </form>
                        </div>`),

                        gridjs.html(`<?php echo $lead['lead_customer_name']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_customer_contact'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_for_branch'] ?>`),
                        gridjs.html(`<?php echo $lead['lead_whatsapp']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_email']; ?>`),
                        gridjs.html(`<?php echo date('d-m-Y', strtotime($lead['lead_next_followup_date'])); ?>`),
                        gridjs.html(`<?php echo date('H:i', strtotime($lead['lead_next_followup_time'])); ?>`),
                        gridjs.html(`<?php echo $lead['lead_type']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_status']; ?>`),
                        gridjs.html(`<?php echo $lead['lead_assign_to']; ?>`)
                    ],
            <?php }
            } ?>
        ];

        if (document.getElementById("table-lead-search")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No.",
                        width: "50px"
                    },
                    {
                        name: "Select",
                        width: "50px"
                    },
                    {
                        name: "Action",
                        width: "25px"
                    },
                    {
                        name: "Org/Client Name",
                        width: "100px"
                    },
                    {
                        name: "Contact",
                        width: "100px"
                    },
                    {
                        name: "Branch",
                        width: "300px"
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
                        name: "Lead Assign To",
                        width: "100px"
                    }
                ],
                pagination: {
                    limit: 10
                },
                search: true,
                data: leadData,
                style: {
                    table: {
                        'min-width': '1900px',
                        'font-size': '15px',
                        'text-align': 'center'
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