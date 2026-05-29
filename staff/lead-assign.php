<?php
include("../config/db.php");
include("../config/session.php");
include("../config/fornotification.php");

####################  Manage Followup / Delete  ######################
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $myleadid = $_POST['myleadid'] ?? null;

    if (isset($_POST['update_followup'])) {
        echo "<script>window.location.href='followup-edit3.php?id=$myleadid';</script>";
        exit;
    } elseif (isset($_POST['delete_lead'])) {
        $deleteLeadQuery = "DELETE FROM `importleads` WHERE `id` = '$myleadid'";
        if (mysqli_query($conn, $deleteLeadQuery)) {

            ################# Push activity ################
            $activity_id = $guid;
            $activity = "Some customer list has been removed from imported leads by staff " . $username . " of " . $Branch . " branch.";
            $activity_type = 'Alert';
            $activity_company = $Company;
            $activity_branch = $Branch;
            $activity_on = date('Y-m-d');
            $activity_by = $username;
            $PushAddactivity = "INSERT INTO `activities`(`activity_id`, `activity`, `activity_type`, `activity_company`, `activity_branch`, `activity_on`, `notify_by`, `activity_by`) VALUES ('$activity_id','$activity','$activity_type','$activity_company','$activity_branch','$activity_on','$notify_by','$activity_by')";
            $ApplynifyQuery = mysqli_query($conn, $PushAddactivity);
            ################# Push activity ################

            echo "<script>window.location.href='lead-upload.php'</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['transfer_lead'])) {
        transferLead($conn);
    }
}
####################  Manage Followup / Delete  ######################

############# Fetch all employees from the database #########
$Employee_list = "SELECT * FROM `employees` WHERE `employee_of_branch` = '$Branch' AND `employee_user_role` = 'Staff' AND `employee_status` = '1'";
$Employee_list_result = $conn->query($Employee_list);
$Employeename = $Employee_list_result ? mysqli_fetch_all($Employee_list_result, MYSQLI_ASSOC) : [];
################## Lead Assign TO Staff ####################

function transferLead($conn)
{
    $lead_ids = $_POST['lead_id'] ?? [];
    $lead_assign_to = $_POST['lead_assign_to'];
    $lead_delivery_date = date('Y-m-d');

    foreach ($lead_ids as $lead_id) {
        $lead_uid = generateId();
        $leadData = getLeadData($conn, $lead_id);

        if ($leadData) {
            $addLeadStatus = insertLead($conn, $lead_uid, $leadData, $lead_delivery_date, $lead_assign_to);
            if ($addLeadStatus) {
                updateLead($conn, $lead_id, $leadData['lead_createdby'], $lead_assign_to);
            }
        }
    }
    echo "<script>window.location.href = 'lead-assign.php';</script>";
}

function generateId()
{
    return '#ID' . mt_rand(1, 9999999);
}

function getLeadData($conn, $lead_id)
{
    $GetLeadData = "SELECT * FROM `importleads` WHERE `id` = '$lead_id'";
    $GetLeadDataQuery = $conn->query($GetLeadData);
    return $GetLeadDataQuery && mysqli_num_rows($GetLeadDataQuery) > 0 ? $GetLeadDataQuery->fetch_assoc() : null;
}

function insertLead($conn, $lead_uid, $leadData, $lead_delivery_date, $lead_assign_to)
{
    $addLead = "INSERT INTO `leads`(`lead_id`, `lead_customer_name`, `lead_customer_contact`, `lead_for_company`, `lead_for_branch`, `lead_delivery_address`, `lead_email`, `lead_followup_date`, `lead_next_followup_date`, `lead_next_followup_time`, `lead_delivery_date`, `lead_whatsapp`, `lead_assign_to`, `lead_status`, `lead_createdby`, `lead_type`, `lead_customer_type`, `lead_customer_status`) 
    VALUES ('$lead_uid', '{$leadData['lead_customer_name']}', '{$leadData['lead_customer_contact']}', '{$leadData['lead_for_company']}', '{$leadData['lead_for_branch']}', '{$leadData['lead_delivery_address']}', '{$leadData['lead_email']}', '{$leadData['lead_followup_date']}', '{$leadData['lead_next_followup_date']}', '{$leadData['lead_next_followup_time']}', '$lead_delivery_date', '{$leadData['lead_whatsapp']}', '$lead_assign_to', '{$leadData['lead_status']}', '{$leadData['lead_createdby']}', '{$leadData['lead_type']}', '{$leadData['lead_customer_type']}', '{$leadData['lead_customer_status']}')";

    return mysqli_query($conn, $addLead);
}

function updateLead($conn, $lead_id, $lead_createdby, $lead_assign_to)
{
    $lead_transfer = "UPDATE `importleads` SET `lead_createdby`='$lead_createdby', `lead_assign_to`='$lead_assign_to' WHERE `id` = '$lead_id'";
    if (!mysqli_query($conn, $lead_transfer)) {
        echo "Error updating lead: " . mysqli_error($conn);
    }
}

// ################# Push activity ################
// $activity_id = $guid;
// $activity = "Some customer / leads list has been assigned to another staff by staff " . $username . " of " . $Branch . " branch.";
// $activity_type = 'Alert';
// $activity_company = $Company;
// $activity_branch = $Branch;
// $activity_on = date('Y-m-d H:i:s');
// $activity_by = $username;
// $PushAddactivity = "INSERT INTO `activities`(`activity_id`, `activity`, `activity_type`, `activity_company`, `activity_branch`, `activity_on`, `notify_by`, `activity_by`) VALUES ('$activity_id','$activity','$activity_type','$activity_company','$activity_branch','$activity_on','$notify_by','$activity_by')";
// $ApplynifyQuery = mysqli_query($conn, $PushAddactivity);
// ################# Push activity ################

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

    <div class="wrapper">
        <?php include("topnav.php"); ?>
        <?php include("sidenav.php"); ?>

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
                             <div class="row gap-2">
                                  <div class="col-5">
                                       <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="success-tooltip" data-bs-title="View the lead details.">
                                            <input type="hidden" name="myleadid" value="<?php echo $lead['id']; ?>">
                                            <button type="submit" name="update_followup" value="<?php echo $lead['id']; ?>" class="btn btn-soft-success btn-sm"><iconify-icon icon="solar:eye-scan-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                       </form>
                                  </div>

                                  <div class="col-5">
                                       <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="danger-tooltip" data-bs-title="Delete the lead.">
                                            <input type="hidden" name="myleadid" value="<?php echo $lead['id']; ?>">
                                            <button type="submit" name="delete_lead" value="<?php echo $lead['id']; ?>" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                       </form>
                                  </div>   
                             </div>
                        </div>`),
                        "<?php echo $lead['lead_customer_name']; ?>",
                        "<?php echo $lead['lead_customer_contact'] ?>",
                        "<?php echo $lead['lead_for_branch'] ?>",
                        "<?php echo $lead['lead_whatsapp']; ?>",
                        "<?php echo $lead['lead_email']; ?>",
                        "<?php echo date('d-m-Y', strtotime($lead['lead_next_followup_date'])); ?>",
                        "<?php echo date('H:i', strtotime($lead['lead_next_followup_time'])); ?>",
                        "<?php echo $lead['lead_type']; ?>",
                        "<?php echo $lead['lead_status']; ?>",
                        "<?php echo $lead['lead_assign_to']; ?>"
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
                        width: "135px"
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