<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

######### Manage Followup / Create Quotation / Create Report / Delete ###########
if (isset($_POST['mylead_id'])) {
     $mylead_id = $_POST['mylead_id']; // Sanitize input
     $lead_customer_name = $_POST['lead_customer_name'];

     if (isset($_POST['manage_followup'])) {
          echo "<script>window.location.href='manage-followup.php?id=$mylead_id';</script>";
          exit;
     } elseif (isset($_POST['create_qutation'])) {
          echo "<script>window.location.href='create-quotation.php?id=$mylead_id';</script>";
          exit;
     } elseif (isset($_POST['create_report'])) {
          echo "<script>window.location.href='create-report.php?id=$mylead_id';</script>";
          exit;
     } elseif (isset($_POST['delete_lead'])) {
          $deleteLeadQuery = "DELETE FROM `leads` WHERE `id` = $mylead_id";
          $DeleteStatus = mysqli_query($conn, $deleteLeadQuery);

          if ($DeleteStatus) {
               ################# Push activity ################
               $activity_id = $guid;
               $activity_content = "The customer " . $lead_customer_name . " has been removed by " . $Role . ".";
               $activity_type = 'Alert';
               $activity_company = "";
               $activity_branch = "";
               $activity_on = date('Y-m-d H:i:s');
               $activity_by = $username;
               $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
               $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
               ################# Push activity ################
               echo "<script>window.location.href='lead-new.php';</script>";
          } else {
               echo "Error: Lead not deleted. " . mysqli_error($conn);
          }
     }
}
######### End Manage Followup / Create Quotation / Create Report / Delete ###########

######### Fetch all branches from the database #########
$branch_list_result = $conn->query("SELECT * FROM `branches` WHERE `branch_status` = '1'");
$branchename = [];
if ($branch_list_result && mysqli_num_rows($branch_list_result) > 0) {
     while ($branches = $branch_list_result->fetch_assoc()) {
          $branchename[] = $branches;
     }
}
######### Fetch all branches from the database #########


######### Fetch all staff from the database #########
if (isset($_GET["lead_for_branch"])) {
     $lead_for_branch = $_GET["lead_for_branch"];
     $Employee_list_result = $conn->query("SELECT * FROM `employees` WHERE `employee_of_branch` = '$lead_for_branch' AND `employee_user_role` = 'Staff' AND `employee_status` = '1'");
} else {
     $Employee_list_result = $conn->query("SELECT * FROM `employees` WHERE `employee_user_role` = 'Staff' AND `employee_status` = '1'");
}
$Employeename = [];
if ($Employee_list_result) {
     $Employeename[] = mysqli_fetch_all($Employee_list_result, MYSQLI_ASSOC);
}
######### Fetch all staff from the database #########


######### Base lead filter query #########
$lead_filter_query = "SELECT * FROM `leads` WHERE `lead_assign_to`!='' AND `lead_updatedby`='' AND `lead_customer_status` = '1'";
######### End base lead filter query #########


############ Apply Filter On Lead List ##############
if (isset($_POST['search_lead'])) {
     $lead_for_branch = $_POST['lead_for_branch'] ?? '';
     $lead_assign_to = $_POST['lead_assign_to'] ?? '';
     $lead_customer_name = $_POST['lead_customer_name'] ?? '';
     $lead_type = $_POST['lead_type'] ?? '';

     $urlParams = [];

     if (!empty($lead_for_branch)) $urlParams[] = "lead_for_branch=$lead_for_branch";
     if (!empty($lead_assign_to)) $urlParams[] = "lead_assign_to=$lead_assign_to";
     if (!empty($lead_customer_name)) $urlParams[] = "lead_customer_name=$lead_customer_name";
     if (!empty($lead_type)) $urlParams[] = "lead_type=$lead_type";

     if (!empty($urlParams)) {
          $queryString = implode('&', $urlParams);
          echo "<script>window.location.href='lead-new.php?$queryString';</script>";
          exit;
     }
}
############ Apply Filter On Lead List ##############

############# Filter Applied on Lead #################
$lead_for_company = $_GET['lead_for_company'] ?? null;
$lead_for_branch = $_GET['lead_for_branch'] ?? null;
$lead_assign_to = $_GET['lead_assign_to'] ?? null;
$lead_customer_name = $_GET['lead_customer_name'] ?? null;
$lead_type = $_GET['lead_type'] ?? null;

######### Build filtered query #########
if (!empty($lead_for_company)) {
     $lead_filter_query .= " AND `lead_for_company` = '$lead_for_company'";
}
if (!empty($lead_for_branch)) {
     $lead_filter_query .= " AND `lead_for_branch` = '$lead_for_branch'";
}
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
if ($lead_filter_result && mysqli_num_rows($lead_filter_result) > 0) {
     while ($leadfilter = $lead_filter_result->fetch_assoc()) {
          $lead_data[] = $leadfilter;
     }
}
######### End filtered lead fetch #########

######### Fetch all filtered leads from the database (for lead list table) #########
$lead_list_query = "SELECT * FROM `leads` WHERE `lead_assign_to`!='' AND `lead_updatedby`='' AND `lead_customer_status` = '1'";

if (!empty($lead_for_company)) {
     $lead_list_query .= " AND `lead_for_branch` = '$lead_for_company'";
}
if (!empty($lead_for_branch)) {
     $lead_list_query .= " AND `lead_for_branch` = '$lead_for_branch'";
}
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
######### End lead list #########
?>


<!DOCTYPE html>
<html lang="en-US">

<head>
     <title>New Lead</title>
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
                                                       <!-- Branch Dropdown -->
                                                       <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                 <label for="lead_for_branch" class="form-label">Branch</label>
                                                                 <select class="form-control" id="lead_for_branch" name="lead_for_branch" data-choices>
                                                                      <option value="">All Branches</option>
                                                                      <?php foreach ($branchename as $mybranch): ?>
                                                                           <option value="<?php echo $mybranch['branch_name']; ?>"
                                                                                <?php echo (isset($lead_for_branch) && $mybranch['branch_name'] == $lead_for_branch) ? 'selected' : ''; ?>>
                                                                                <?php echo $mybranch['branch_name']; ?>
                                                                           </option>
                                                                      <?php endforeach; ?>
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
                                                                                <?php echo (isset($lead_assign_to) && $employee['employee_username'] == $lead_assign_to) ? 'selected' : ''; ?>>
                                                                                <?php echo $employee['employee_name']; ?>
                                                                           </option>
                                                                      <?php endforeach; ?>
                                                                 </select>
                                                            </div>
                                                       </div>

                                                       <!-- Customer Name Input -->
                                                       <div class="col-lg-3">
                                                            <div class="mb-3">
                                                                 <label for="lead_customer_name" class="form-label text-dark">Customer Name</label>
                                                                 <input type="text" id="lead_customer_name" name="lead_customer_name" class="form-control" placeholder="Contact Person" value="<?php echo htmlspecialchars($lead_customer_name); ?>">
                                                            </div>
                                                       </div>

                                                       <!-- Lead Type Dropdown -->
                                                       <div class="col-lg-3">
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
                                                                      <a href="lead-new.php" class="btn btn-primary w-100">Reset</a>
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
                                             <h4 class="card-title">New Leads </h4>
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
               const branchSelect = document.getElementById('lead_for_branch');
               const employeeSelect = document.getElementById('lead_assign_to');
               const selectedBranch = branchSelect.value;

               // Get all employee options
               const employeeOptions = employeeSelect.querySelectorAll('option');

               // Loop through each employee option
               employeeOptions.forEach(option => {
                    const branch = option.getAttribute('data-branch');

                    // Show all employees if no branch is selected
                    if (!selectedBranch || branch === selectedBranch) {
                         option.style.display = 'block';
                    } else {
                         option.style.display = 'none';
                    }
               });

               // Reset employee selection
               employeeSelect.value = '';
          }
     </script>


     <script>
          // Branch Table Data
          const leadData = [
               <?php
               $counterValue = 1;  // Initialize the counter for So.No.
               foreach ($lead_data as $lead) { ?>[
                         gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter
                         gridjs.html(`
                              <div clss="col-sm-12">
                                   <div class="row gap-2">
                                        <div class="col-2">
                                             <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="success-tooltip" data-bs-title="View the lead details.">
                                                  <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                                  <button type="submit" name="manage_followup" value="<?php echo $lead['id']; ?>" class="btn btn-soft-success btn-sm"><iconify-icon icon="solar:eye-scan-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                             </form>
                                        </div>
                                        <div class="col-2">
                                             <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="primary-tooltip" data-bs-title="View the lead quotation.">
                                                  <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                                  <button type="submit" name="create_qutation" value="<?php echo $lead['id']; ?>" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:tag-price-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                             </form>
                                        </div>
                                        <div class="col-2">
                                             <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="info-tooltip" data-bs-title="View the lead report.">
                                                  <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                                  <button type="submit" name="create_report" value="<?php echo $lead['id']; ?>" class="btn btn-soft-info btn-sm"><iconify-icon icon="solar:graph-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                             </form>
                                        </div>
                                         <div class="col-2">
                                            <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="danger-tooltip" data-bs-title="Delete the lead.">
                                                <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                                <input type="hidden" name="lead_customer_name" value="<?php echo $lead['lead_customer_name']; ?>">
                                                <button type="submit" name="delete_lead" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                            </form>
                                        </div>
                                   </div>
                              </div>`),

                         "<?php echo $lead['lead_customer_name']; ?>",
                         "<?php echo $lead['lead_customer_contact'] ?>",
                         "<?php echo $lead['lead_for_company'] ?>",
                         "<?php echo $lead['lead_for_branch'] ?>",
                         "<?php echo $lead['lead_email']; ?>",
                         "<?php echo $lead['lead_whatsapp']; ?>",
                         "<?php echo date('d-m-Y', strtotime($lead['lead_next_followup_date'])); ?>",
                         "<?php echo date('H:i', strtotime($lead['lead_next_followup_time'])); ?>",
                         "<?php echo $lead['lead_type']; ?>",
                         "<?php echo $lead['lead_status']; ?>",
                         "<?php echo $lead['lead_assign_to']; ?>",
                         "<?php echo $lead['lead_updatedby']; ?>"
                    ],
               <?php }  ?>
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
                              width: "210px"
                         },
                         {
                              name: "Customer Name",
                              width: "180px"
                         },
                         {
                              name: "Contact",
                              width: "100px"
                         },
                         {
                              name: "Company",
                              width: "300px"
                         },
                         {
                              name: "Branch",
                              width: "300px"
                         },
                         {
                              name: "Email",
                              width: "200px"
                         },
                         {
                              name: "Whatsapp",
                              width: "100px"
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
                    ],
                    pagination: {
                         limit: 10
                    },
                    search: true,
                    data: leadData,
                    style: {
                         table: {
                              'min-width': '2400px',
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



     <script>
          function filterEmployees() {
               const branchSelect = document.getElementById('lead_for_branch');
               const employeeSelect = document.getElementById('lead_assign_to');
               const selectedBranch = branchSelect.value;

               // Get all employee options
               const employeeOptions = employeeSelect.querySelectorAll('option');

               // Loop through each employee option
               employeeOptions.forEach(option => {
                    const branch = option.getAttribute('data-branch');

                    // Show all employees if no branch is selected
                    if (!selectedBranch || branch === selectedBranch) {
                         option.style.display = 'block';
                    } else {
                         option.style.display = 'none';
                    }
               });

               // Reset employee selection
               employeeSelect.value = '';
          }
     </script>

</body>

</html>