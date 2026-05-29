<?php
include("../config/db.php");
include("../config/session.php");
include("../config/fornotification.php");

// Fetch technicians using prepared statement
$Technicianname = [];
$stmt = $conn->prepare("SELECT * FROM `employees` WHERE `employee_of_company`=? AND `employee_of_branch`=? AND `employee_user_role`='Technician' AND `employee_status`='1'");
$stmt->bind_param("ss", $Company, $Branch);
$stmt->execute();
$Technicianname = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mylead_id = $conn->real_escape_string($_POST['mylead_id'] ?? '');

    if (isset($_POST['delete_lead'])) {
        if ($conn->query("DELETE FROM `leads` WHERE `id`='$mylead_id'")) {
            header("Location: followup-todays-list.php");
            exit;
        }
    } elseif (isset($_POST['edit_lead'])) {
        header("Location: followup-edit2.php?id=" . urlencode($mylead_id));
        exit;
    }
}

// Optimized lead query - single query instead of 365
$leadData = [];
$query = "SELECT * FROM `leads` 
          WHERE `lead_for_company`='$Company' 
          AND `lead_for_branch`='$Branch' 
          AND `lead_next_followup_date` BETWEEN DATE_SUB(CURDATE(), INTERVAL 365 DAY) AND DATE_SUB(CURDATE(), INTERVAL 1 DAY)
          AND `lead_assign_to`='$username' 
          AND `lead_updatedby`='' 
          AND `lead_type` NOT IN ('Completed', 'Cancel') 
          ORDER BY `lead_next_followup_date` DESC, `id` DESC";
$leadData = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
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

    <div class="wrapper">
        <?php include("topnav.php"); ?>
        <?php include("sidenav.php"); ?>

        <div class="page-content">
            <div class="container-xxl">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Delayed Followup List</h4>
                        <div class="text-center">
                            <span class="mx-6"><?= date('d-m-Y') ?> | <span id="time"></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="table-lead-search"></div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("../footer.php"); ?>
    </div>

    <script src="../assets/js/vendor.js"></script>
    <script src="../assets/js/app.js"></script>
    <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

    <script>
        // Prepare lead data for Grid.js
        const leadData = [
            <?php foreach ($leadData as $index => $lead): ?>[
                    gridjs.html(`<?= $index + 1 ?>`),
                    gridjs.html(`
                    <div class="row gap-2">
                        <div class="col-5">
                            <form method="post">
                                <input type="hidden" name="mylead_id" value="<?= htmlspecialchars($lead['id']) ?>">
                                <button name="edit_lead" class="btn btn-soft-primary btn-sm" title="Edit followups">
                                    <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                </button>
                            </form>
                        </div>
                        <div class="col-5">
                            <?php if (!empty($lead['lead_customer_contact'])): ?>
                                <a href="tel:<?= htmlspecialchars($lead['lead_customer_contact']) ?>" class="btn btn-soft-success btn-sm" title="Call Now">
                                    <iconify-icon icon="solar:outgoing-call-line-duotone" class="align-middle fs-18"></iconify-icon>
                                </a>
                            <?php else: ?>
                                <button class="btn btn-soft-success btn-sm" disabled title="Call Now">
                                    <iconify-icon icon="solar:call-cancel-line-duotone" class="align-middle fs-18"></iconify-icon>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                `),
                    gridjs.html(`<?= htmlspecialchars($lead['lead_id']) ?>`),
                    gridjs.html(`<?= htmlspecialchars($lead['lead_customer_name']) ?>`),
                    gridjs.html(`<?= htmlspecialchars($lead['lead_customer_contact']) ?>`),
                    gridjs.html(`<?= htmlspecialchars($lead['lead_alternate_contact']) ?>`),
                    gridjs.html(`<?= htmlspecialchars($lead['lead_whatsapp']) ?>`),
                    gridjs.html(`<?= htmlspecialchars($lead['lead_email']) ?>`),
                    gridjs.html(`<?php echo ($lead['lead_next_followup_date'] === '0000-00-00') ? '00-00-0000' : date('d-m-Y', strtotime($lead['lead_next_followup_date'])); ?>`),
                    gridjs.html(`<?= date('H:i', strtotime($lead['lead_next_followup_time'])) ?>`),
                    gridjs.html(`<?= htmlspecialchars($lead['lead_type']) ?>`),
                    gridjs.html(`<?= htmlspecialchars($lead['lead_status']) ?>`),
                    gridjs.html(`<?= htmlspecialchars($lead['lead_assign_to']) ?>`),
                    gridjs.html(`<?= htmlspecialchars($lead['lead_updatedby']) ?>`),
                    gridjs.html(`<?= htmlspecialchars($lead['lead_remark']) ?>`)
                ],
            <?php endforeach; ?>
        ];

        // Initialize Grid.js
        if (document.getElementById("table-lead-search")) {
            new gridjs.Grid({
                columns: [{
                        name: "So.No.",
                        width: "50px"
                    },
                    {
                        name: "Actions",
                        width: "135px"
                    },
                    {
                        name: "Unique ID",
                        width: "100px"
                    },
                    {
                        name: "Org/Client Name",
                        width: "180px"
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
                        name: "Lead Assign To",
                        width: "100px"
                    },
                    {
                        name: "Handled By",
                        width: "100px"
                    },
                    {
                        name: "Remarks",
                        width: "300px"
                    }
                ],
                pagination: {
                    limit: 10
                },
                search: true,
                data: leadData,
                style: {
                    table: {
                        'min-width': '2300px',
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