<?php
include("../config/db.php");
include("../config/session.php");
include("../config/fornotification.php");

#########  Manage Followup / Create Quotation / Create Report / Delete  ###########
if (isset($_POST['manage_followup'])) {
     $mylead_id = $_POST['mylead_id'];
     echo "<script>window.location.href='manage-followup.php?id=$mylead_id';</script>";
     exit;
} elseif (isset($_POST['edit_lead'])) {
     $mylead_id = $_POST['mylead_id'];
     echo "<script>window.location.href='followup-edit2.php?id=$mylead_id';</script>";
     exit;
} elseif (isset($_POST['create_qutation'])) {
     $mylead_id = $_POST['mylead_id'];
     echo "<script>window.location.href='create-quotation.php?id=$mylead_id';</script>";
     exit;
} elseif (isset($_POST['create_report'])) {
     $mylead_id = $_POST['mylead_id'];
     echo "<script>window.location.href='create-report.php?id=$mylead_id';</script>";
     exit;
}
#########  Manage Followup / Create Quotation / Create Report / Delete  ###########


######### Fetch all employees from the database #########
$Employee_list = "SELECT * FROM `employees` WHERE `employee_of_company` = '$Company' AND `employee_of_branch` = '$Branch' AND `employee_user_role` = 'Staff' AND `employee_status` = '1'";
$Employee_list_result = $conn->query($Employee_list);
$Employeename = [];
if (mysqli_num_rows($Employee_list_result) > 0) {
     while ($Employee = $Employee_list_result->fetch_assoc()) {
          $Employeename[] = $Employee;
     }
}
######### Fetch all employees from the database #########


############# Filter Applied on Lead #################
$lead_list = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `lead_assign_to` = '$username' ORDER BY `id` DESC";
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
                         <!-- ============= Lead List ============= -->
                         <div class="col-xl-12">
                              <div class="card">
                                   <div class="d-flex card-header justify-content-between align-items-center">
                                        <div>
                                             <h4 class="card-title"> Lead List </h4>
                                        </div>
                                        <div class="dropdown">
                                             <a href="leads-add.php" class="btn btn-primary">Add Lead</a>
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
               $counterValue = 1; // Initialize the counter for So.No.
               if ($counterValue <= $lead_list_result->num_rows) {
                    while ($lead = $lead_list_result->fetch_assoc()) { ?>[
                              gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter

                              gridjs.html(`
                              <div clss="col-sm-12">
                                   <div class="row gap-2">
                                        <div class="col-3">
                                             <?php if ($lead['lead_type'] !== "Cancel") { ?>
                                                  <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="success-tooltip" data-bs-title="Edit followups">
                                                       <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                                       <button type="submit" name="edit_lead" value="<?php echo $lead['id']; ?>" class="btn btn-soft-success btn-sm"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                                  </form>
                                             <?php } else { ?>
                                                   <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="danger-tooltip" data-bs-title="Edit followups disabled">
                                                       <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>" disabled>
                                                       <button type="submit" name="edit_lead" value="<?php echo $lead['id']; ?>" class="btn btn-soft-danger btn-sm" disabled><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                                  </form>
                                             <?php } ?>
                                        </div>

                                        <div class="col-3">
                                             <?php if ($lead['lead_type'] !== "Cancel") { ?>
                                                  <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="primary-tooltip" data-bs-title="View the lead quotation.">
                                                       <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                                       <button type="submit" name="create_qutation" value="<?php echo $lead['id']; ?>" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:tag-price-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                                  </form>
                                             <?php } else { ?>
                                                  <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="danger-tooltip" data-bs-title="View the lead quotation disabled.">
                                                       <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>" disabled>
                                                       <button type="submit" name="create_qutation" value="<?php echo $lead['id']; ?>" class="btn btn-soft-danger btn-sm" disabled><iconify-icon icon="solar:tag-price-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                                  </form>
                                             <?php } ?>
                                        </div>

                                        <div class="col-3">
                                             <form action="#" method="post" enctype="multipart/form-data" data-bs-toggle="tooltip" data-bs-custom-class="info-tooltip" data-bs-title="View the lead report.">
                                                  <input type="hidden" name="mylead_id" value="<?php echo $lead['id']; ?>">
                                                  <button type="submit" name="create_report" value="<?php echo $lead['id']; ?>" class="btn btn-soft-info btn-sm"><iconify-icon icon="solar:graph-line-duotone" class="align-middle fs-18"></iconify-icon></button>
                                             </form>
                                        </div>
                                   </div>
                              </div>`),

                              gridjs.html(`<?php echo $lead['lead_id']; ?>`),
                              gridjs.html(`<?php echo $lead['lead_customer_name']; ?>`),
                              gridjs.html(`<?php echo $lead['lead_customer_contact'] ?>`),
                              gridjs.html(`<?php echo $lead['lead_alternate_contact'] ?>`),
                              gridjs.html(`<?php echo $lead['lead_email']; ?>`),
                              gridjs.html(`<?php echo $lead['lead_whatsapp']; ?>`),
                              gridjs.html(`<?php echo ($lead['lead_next_followup_date'] === '0000-00-00') ? '00-00-0000' : date('d-m-Y', strtotime($lead['lead_next_followup_date'])); ?>`),
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
                         }, {
                              name: "Actions",
                              width: "190px"
                         },
                         "Unique ID",
                         "Customer Name",
                         {
                              name: "Contact",
                              width: "100px"
                         },
                         {
                              name: "2nd Contact",
                              width: "100px"
                         },
                         {
                              name: "Email",
                              width: "300px"
                         },
                         {
                              name: "Whatsapp",
                              width: "100px"
                         },
                         {
                              name: "Next Folloup Date",
                              width: "100px",
                         },
                         {
                              name: "Next Folloup Time",
                              width: "100px",
                         },
                         {
                              name: "Lead Type",
                              width: "100px",
                         },
                         {
                              name: "Lead Status",
                              width: "100px",
                         },
                         {
                              name: "Lead Assign To",
                              width: "100px",
                         },
                         {
                              name: "Handled By",
                              width: "100px",
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