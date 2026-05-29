<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

############### Publish / Unpublish #################
if (isset($_POST['publish'])) {
     $employee_id = $_POST['employee_id'];
     $new_status = ($_POST['publish'] == 'on' || $_POST['publish'] == '1') ? 1 : 0;
     $updateStatusQuery = "UPDATE `employees` SET `employee_status` = '$new_status' WHERE `id` = '$employee_id'";
     $PublishBranch = mysqli_query($conn, $updateStatusQuery);

     if ($PublishBranch) {
          echo "<script>window.location.href='employees-list.php';</script>";
     } else {
          $error_message = mysqli_error($conn);
          echo "<script>alert('Error: $error_message')</script>";
     }
}
############### Publish / Unpublish #################


############### Employees Delete / Edit  #################
if (isset($_POST['delete_employee'])) {
     $employee_id = $_POST['employee_id'];
     $employee_name = $_POST['employee_name'];
     $deleteEmployeeQuery = "DELETE FROM `employees` WHERE `id` = '$employee_id'";
     $checkDelete = mysqli_query($conn, $deleteEmployeeQuery);


     if ($checkDelete) {
          ################# Push activity ################
          $activity_id = $guid;
          $activity_content = "The employee " . $employee_name . " has been removed by " . $Role . ".";
          $activity_type = 'Alert';
          $activity_company = "";
          $activity_branch = "";
          $activity_on = date('Y-m-d H:i:s');
          $activity_by = $username;
          $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
          $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
          ################# Push activity ################
          echo "<script>window.location.href='employees-list.php'</script>";
     } else {
          echo "Error: " . mysqli_error($conn);
     }
} elseif (isset($_POST['edit_employee'])) {
     $employee_id = $_POST['employee_id'];
     echo "<script>window.location.href='employee-edit.php?id=$employee_id'</script>";
     exit;
}
############### Employees Delete / Edit  #################


############### Fetch all active companies #################
$Companyname = [];
$CompaniesQuery = $conn->query("SELECT * FROM `companies` ORDER BY `company_name` ASC");
if ($CompaniesQuery && $CompaniesQuery->num_rows > 0) {
     $Companyname = $CompaniesQuery->fetch_all(MYSQLI_ASSOC);
}
############### End fetch companies #################


############### Fetch branches based on company filter #################
$branchename = [];
if (isset($_GET['employee_of_company'])) {
     $employee_of_company = $_GET['employee_of_company'];
     $branchQuery = "SELECT * FROM `branches` WHERE `branch_of_company` = '$employee_of_company' ORDER BY `branch_name` ASC";
} else {
     $branchQuery = "SELECT * FROM `branches` ORDER BY `branch_name` ASC";
}

$BranchesQuery = $conn->query($branchQuery);
if ($BranchesQuery && $BranchesQuery->num_rows > 0) {
     $branchename = $BranchesQuery->fetch_all(MYSQLI_ASSOC);
}
############### End fetch branches #################



######### Base lead filter query #########
$lead_filter_query = "SELECT * FROM `employees` WHERE 1";
######### End base lead filter query #########


############ Apply Filter On Lead List ##############
if (isset($_POST['search_employee'])) {
     $filters = [
          'employee_of_company'   => $_POST['employee_of_company'] ?? '',
          'employee_of_branch'    => $_POST['employee_of_branch'] ?? '',
          'employee_user_role'    => $_POST['employee_user_role'] ?? '',
     ];

     $urlParams = [];
     foreach ($filters as $key => $value) {
          if (!empty($value)) $urlParams[] = "$key=$value";
     }

     if (!empty($urlParams)) {
          $queryString = implode('&', $urlParams);
          echo "<script>window.location.href='employees-list.php?$queryString';</script>";
          exit;
     }
}
############ Apply Filter On Lead List ##############


############### Fetch employees based on company/branch #################
$employees_list = $lead_filter_query;
if (!empty($_GET['employee_of_company'])) {
     $employee_of_company = $_GET['employee_of_company'];
     $employees_list .= " AND `employee_of_company` = '$employee_of_company'";
}
if (!empty($_GET['employee_of_branch'])) {
     $employee_of_branch = $_GET['employee_of_branch'];
     $employees_list .= " AND `employee_of_branch` = '$employee_of_branch'";
}

if (!empty($_GET['employee_user_role'])) {
     $employee_user_role = $_GET['employee_user_role'];
     $employees_list .= " AND `employee_user_role` = '$employee_user_role'";
}

$employees_list .= " ORDER BY `id` DESC";
$employees_list_result = $conn->query($employees_list);
############### End fetch employees #################
?>




<!DOCTYPE html>
<html lang="en-US">

<head>
     <title> Employees List </title>
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
                                                  <h4 class="card-title">Filter Request</h4>
                                             </div>

                                             <div class="card-body">
                                                  <div class="row">
                                                       <!--=============== Handled By Employee Dropdown ===============-->
                                                       <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                 <label for="employee_of_company" class="form-label">Companies</label>
                                                                 <select class="form-control" id="employee_of_company" name="employee_of_company" data-choices>
                                                                      <option value="">Select Company</option>
                                                                      <?php foreach ($Companyname as $myCompany) { ?>
                                                                           <option value="<?php echo $myCompany['company_name']; ?>"
                                                                                <?php echo ($myCompany['company_name'] == $employee_of_company) ? 'selected' : ''; ?>>
                                                                                <?php echo $myCompany['company_name']; ?>
                                                                           </option>
                                                                      <?php } ?>
                                                                 </select>
                                                            </div>
                                                       </div>

                                                       <!--=============== Employee Branch ===============-->
                                                       <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                 <label for="employee_of_branch" class="form-label">Branch</label>
                                                                 <select class="form-control" id="employee_of_branch" name="employee_of_branch" data-choices>
                                                                      <option value="">All Branches</option>
                                                                      <?php foreach ($branchename as $mybranch) { ?>
                                                                           <option value="<?php echo $mybranch['branch_name']; ?>"
                                                                                <?php echo ($mybranch['branch_name'] == $employee_of_branch) ? 'selected' : ''; ?>>
                                                                                <?php echo $mybranch['branch_name']; ?>
                                                                           </option>
                                                                      <?php } ?>
                                                                 </select>
                                                            </div>
                                                       </div>

                                                       <!--=============== Buttons ===============-->
                                                       <div class="col-lg-12">
                                                            <div class="row justify-content-between g-2">
                                                                 <div class="col-lg-2">
                                                                      <button type="submit" name="search_employee" class="btn btn-soft-success w-100">Search Now</button>
                                                                 </div>
                                                                 <div class="col-lg-2">
                                                                      <a href="employees-list.php" class="btn btn-primary w-100">Reset</a>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </form>

                         <!-- ============= Employees List ============= -->
                         <div class="col-xl-12">
                              <div class="card">
                                   <div class="d-flex card-header justify-content-between align-items-center">
                                        <div>
                                             <h4 class="card-title">Employees List</h4>
                                        </div>
                                        <div class="dropdown">
                                             <a href="employee-add.php" class="btn btn-primary">Add Employee</a>
                                        </div>
                                   </div>
                                   <div class="card-body">
                                        <div id="table-employee-search"></div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
          <!-- ==================================================== -->
          <!-- End Page Content -->
          <!-- ==================================================== -->

          <!-- Footer -->
          <?php include("../footer.php"); ?>

     </div>
     <!-- END Wrapper -->

     <!-- Vendor Javascript (Require in all Page) -->
     <script src="../assets/js/vendor.js"></script>

     <!-- App Javascript (Require in all Page) -->
     <script src="../assets/js/app.js"></script>

     <!-- Grid Js -->
     <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

     <script>
          // Employee Table Data
          const EmployeeData = [
               <?php
               $counterValue = 1;
               if ($counterValue <= $employees_list_result->num_rows) {
                    while ($empdata = $employees_list_result->fetch_assoc()) { ?>[
                              gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter
                              gridjs.html(`<?php echo $empdata['employee_id']; ?>`),
                              gridjs.html(`<?php echo $empdata['employee_name']; ?>`),
                              gridjs.html(`<?php echo $empdata['employee_of_company']; ?>`),
                              gridjs.html(`<?php echo $empdata['employee_of_branch']; ?>`),
                              gridjs.html(`<?php echo $empdata['employee_contact']; ?>`),
                              gridjs.html(`<?php echo $empdata['employee_email']; ?>`),
                              gridjs.html(`<?php echo $empdata['employee_address']; ?>`),
                              gridjs.html(`<?php echo $empdata['employee_username']; ?>`),
                              gridjs.html(`<?php echo $empdata['employee_user_password']; ?>`),
                              gridjs.html(`<?php echo $empdata['employee_user_role']; ?>`),

                              gridjs.html(`
                               <form action="#" method="post">
                                   <input type="hidden" name="employee_id" value="<?php echo $empdata['id']; ?>">
                                   <input type="hidden" name="publish" value="0">
                                   <div class="form-check form-switch flex-box justify-content-center align-items-center">
                                        <input class="form-check-input" name="publish" type="checkbox" role="switch" id="flexSwitchCheckChecked<?php echo $empdata['id']; ?>" <?php echo ($empdata['employee_status'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                   </div>
                              </form>`),

                              gridjs.html(`
                              <div class="row gap-1">
                                   <div class="col-5">
                                        <form action="#" method="post" enctype="multipart/form-data">
                                             <input type="hidden" name="employee_id" value="<?php echo $empdata['id']; ?>">
                                             <button type="submit" name="edit_employee" value="<?php echo $empdata['id']; ?>" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                        </form>
                                   </div>

                                   <div class="col-5">
                                        <form action="#" method="post" enctype="multipart/form-data">
                                             <input type="hidden" name="employee_id" value="<?php echo $empdata['id']; ?>">
                                             <input type="hidden" name="employee_name" value="<?php echo $empdata['employee_name']; ?>">
                                             <button type="submit" name="delete_employee" value="<?php echo $empdata['id']; ?>" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                        </form>
                                   </div>    
                              </div>`)
                         ],
               <?php }
               } ?>
          ];

          // Initialize Grid.js Table
          if (document.getElementById("table-employee-search")) {
               new gridjs.Grid({
                    columns: [{
                              name: "So.No.",
                              width: "50px"
                         },
                         {
                              name: "Employee ID",
                              width: "100px"
                         },
                         {
                              name: "Employee Name",
                              width: "200px"
                         },
                         {
                              name: "Company",
                              width: "300px"
                         },
                         {
                              name: "Branch",
                              width: "300px"
                         },

                         "Contact No.",
                         {
                              name: "Email",
                              width: "200px"
                         },
                         {
                              name: "Address",
                              width: "300px"
                         },
                         {
                              name: " Username",
                              width: "180px"
                         },
                         {
                              name: "Password ",
                              width: "180px"
                         },
                         {
                              name: "Role",
                              width: "180px"
                         },
                         {
                              name: "Status",
                              width: "50px"
                         },
                         {
                              name: "Actions",
                              width: "140px"
                         }
                    ],
                    pagination: {
                         limit: 10
                    },
                    search: true,
                    data: EmployeeData,
                    style: {
                         table: {
                              'min-width': '2500px',
                              'font-size': '15px',
                              'text-align': 'center',
                         },
                         th: {
                              'background-color': '#ff6c2f',
                              'color': '#fff'
                         },
                    }
               }).render(document.getElementById("table-employee-search"));
          }
     </script>

</body>

</html>