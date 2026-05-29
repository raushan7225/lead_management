<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

################# Login Rules #################
if ($Role !== 'Super Admin') {
     header("Location: ../index.php");
     exit;
}
################# Login Rules #################


############### Fetch all Company from the database ##############
$companies_lists = "SELECT * FROM `companies`";
$companies_lists_result = mysqli_query($conn, $companies_lists);
$companiesCounts = mysqli_num_rows($companies_lists_result);
############### Fetch all Company from the database ##############

####################### Fetch Branch ########################
$BranchList = "SELECT `branch_id` FROM `branches`";
$BranchListResult = mysqli_query($conn, $BranchList);
$BranchCount = mysqli_num_rows($BranchListResult);
####################### Fetch Branch ########################

##################### Fetch employees #####################
$Admins_list = "SELECT `employee_user_role` FROM `employees` WHERE `employee_user_role` = 'Admin'";
$Admins_list_results = $conn->query($Admins_list);
$AdminsCount = mysqli_num_rows($Admins_list_results);
##################### Fetch employees #####################

##################### Fetch employees #####################
$Managers_list = "SELECT `employee_user_role` FROM `employees` WHERE `employee_user_role` = 'Manager'";
$Managers_list_results = $conn->query($Managers_list);
$ManagerCount = mysqli_num_rows($Managers_list_results);
##################### Fetch employees #####################

##################### Fetch employees #####################
$Staffs_list = "SELECT `employee_user_role` FROM `employees` WHERE `employee_user_role` = 'Staff'";
$Staffs_list_results = $conn->query($Staffs_list);
$StaffCount = mysqli_num_rows($Staffs_list_results);
##################### Fetch employees #####################

##################### Fetch employees #####################
$Technicians_list = "SELECT `employee_user_role` FROM `employees` WHERE `employee_user_role` = 'Technician'";
$Technicians_list_results = $conn->query($Technicians_list);
$TechniciansCount = mysqli_num_rows($Technicians_list_results);
##################### Fetch employees #####################


#################### Fetch Leads Count ####################
$leads_count = "SELECT * FROM `leads`";
$leads_count_result = $conn->query($leads_count);
$leadsCount = $leads_count_result->num_rows;
#################### Fetch Leads Count ####################


############### Products list #####################
$products_list = "SELECT * FROM `products`";
$products_list_result = $conn->query($products_list);
$ProductCount = $products_list_result->num_rows;
############### Products list #####################


############### Fetch all active companies #################
$Companyname = [];
$CompaniesQuery = $conn->query("SELECT * FROM `companies`");
if ($CompaniesQuery->num_rows > 0) {
     $Companyname = $CompaniesQuery->fetch_all(MYSQLI_ASSOC);
}
############### Fetch all active companies #################


############### Fetch branches based on filter #######################
$branchename = [];
$branchQuery = "SELECT * FROM `branches` WHERE `branch_status` = 1";
if (isset($_GET['lead_for_company'])) {
     $lead_for_company = $_GET['lead_for_company'];
     $branchQuery .= " AND `branch_of_company` = '$lead_for_company'";
}
$BranchesQuery = $conn->query($branchQuery);
if ($BranchesQuery->num_rows > 0) {
     $branchename = $BranchesQuery->fetch_all(MYSQLI_ASSOC);
}
############### Fetch branches based on filter #######################


################ Handle lead actions ###################
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     ################# Handle filters ####################
     if (isset($_POST['lead_for_company'])) {
          header("Location: getcompanydata.php?lead_for_company=" . $_POST['lead_for_company']);
          exit;
     } else {
          header("Location: index.php");
          exit;
     }
}
################ Handle lead actions ###################


################ Count queries optimization ##################
$countQueries = [
     'newleadcount' => "SELECT COUNT(*) AS count FROM `leads` WHERE `lead_assign_to` != '' AND `lead_updatedby` = ''",
     'todaysVisitCount' => "SELECT COUNT(*) AS count FROM `leads` WHERE `lead_next_followup_date` = CURDATE() AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_status` = 'Client Visit'",
     'todaysFollowupCount' => "SELECT COUNT(*) AS count FROM `leads` WHERE `lead_next_followup_date` = CURDATE() AND `lead_assign_to` != '' AND `lead_updatedby` != ''",
     'myPlaceOrderCount' => "SELECT COUNT(*) AS count FROM `leads` WHERE `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` = 'Place Order'",
     'delayedOrderCount' => "SELECT COUNT(*) AS count FROM `leads` WHERE `lead_next_followup_date` BETWEEN CURDATE() - INTERVAL 365 DAY AND CURDATE() - INTERVAL 1 DAY AND `lead_assign_to` != '' AND `lead_updatedby` = '' AND `lead_type` NOT IN ('Completed', 'Cancel')",
     'upcomingOrderCount' => "SELECT COUNT(*) AS count FROM `leads` WHERE `lead_next_followup_date` BETWEEN CURDATE() + INTERVAL 1 DAY AND CURDATE() + INTERVAL 365 DAY AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` NOT IN ('Completed', 'Cancel')",
     'CancelOrderCount' => "SELECT COUNT(*) AS count FROM `leads` WHERE `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` = 'Cancel'",
     'CompletedOrderCount' => "SELECT COUNT(*) AS count FROM `leads` WHERE `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` = 'Completed'"
];

$countResults = [];
foreach ($countQueries as $key => $query) {
     $result = $conn->query($query);
     $countResults[$key] = $result ? $result->fetch_assoc()['count'] : 0;
}

extract($countResults);
################ Count queries optimization ##################

?>


<!DOCTYPE html>
<html lang="en-US">

<head>
     <title> Dashboard </title>
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
               <div class="container-fluid">

                    <div class="row">
                         <div class="col-xl-4">
                              <div class="col-md-12">
                                   <div class="card overflow-x-hidden" style="max-height: 470px;">
                                        <div class="card-title py-2 px-3 bg-primary text-white">
                                             Companies
                                        </div>
                                        <div class="list-group overflow-y-scroll rounded-0">
                                             <form action="#" method="post" enctype="multipart/form-data">
                                                  <?php foreach ($Companyname as $myCompany) { ?>
                                                       <div class="d-flex justify-content-start align-items-center">
                                                            <iconify-icon icon="solar:buildings-2-bold-duotone" class="avatar-title fs-32 text-primary" style="width: 70px"></iconify-icon> <input type="submit" name="lead_for_company" class="list-group-item list-group-item-action" value="<?php echo $myCompany['company_name']; ?>">
                                                       </div>
                                                  <?php } ?>
                                             </form>
                                        </div>
                                   </div> <!-- end card -->
                                   
                                   <div class="card">
                                       <div class="card-body p-2">
                                            <a href="todays-report.php" class="btn btn-primary w-100">Today's Report</a>
                                       </div>
                                    </div>
                              </div> <!-- end col -->
                         </div>

                         <div class="col-xl-5">
                              <div class="card">
                                   <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                             <div class="card-title">Hi! <?php echo $fullname; ?>. <br><small class="text-muted">Welcome back buddy!!</small></div>
                                             <a href="lead-add.php" class="btn btn-soft-primary px-lg-5">Add Lead</a>
                                        </div>
                                   </div>
                              </div>

                              <div class="card">
                                   <div class="card-body">
                                        <div dir="ltr">
                                             <div id="simple-donut" class="apex-charts"></div>
                                        </div>
                                   </div> <!-- end card body -->
                              </div> <!-- end card -->
                         </div> <!-- end col -->

                         <div class="col-xl-3">
                              <div class="row">
                                   <!-- ===================== Assigned Manager ================= -->
                                   <div class="col-6 col-xl-12 col-sm-12 col-md-6">
                                        <a href="company-control.php">
                                             <div class="card">
                                                  <div class="card-body">
                                                       <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                 <h4 class="card-title mb-2 d-flex align-items-center gap-2">Companies</h4>
                                                                 <p class="text-muted fw-medium fs-22 mb-0"><?php echo $companiesCounts; ?></p>
                                                            </div>
                                                            <div class="d-none d-sm-block">
                                                                 <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                                      <iconify-icon icon="solar:buildings-2-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </a>
                                   </div>
                                   <!-- ==========x========== Assigned Manager =======x========= -->

                                   <!-- ===================== Assigned Manager ================= -->
                                   <div class="col-6 col-xl-12 col-sm-12 col-md-6">
                                        <a href="branch-list.php">
                                             <div class="card">
                                                  <div class="card-body">
                                                       <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                 <h4 class="card-title mb-2 d-flex align-items-center gap-2">Branches</h4>
                                                                 <p class="text-muted fw-medium fs-22 mb-0"><?php echo $BranchCount; ?></p>
                                                            </div>
                                                            <div class="d-none d-sm-block">
                                                                 <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                                      <iconify-icon icon="solar:buildings-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </a>
                                   </div>
                                   <!-- ==========x========== Assigned Manager =======x========= -->

                                   <!-- ===================== Total Employee ================= -->
                                   <div class="col-6 col-xl-12 col-sm-12 col-md-6">
                                        <a href="employees-list.php?employee_user_role=Admin">
                                             <div class="card">
                                                  <div class="card-body">
                                                       <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                 <h4 class="card-title mb-2 d-flex align-items-center gap-2">Admin</h4>
                                                                 <p class="text-muted fw-medium fs-22 mb-0"><?php echo $AdminsCount; ?></p>
                                                            </div>
                                                            <div class="d-none d-sm-block">
                                                                 <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                                      <iconify-icon icon="solar:shield-user-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </a>
                                   </div>
                                   <!-- ==========x========== Total Employee ========x======== -->

                                   <!-- ===================== Assigned Manager ================= -->
                                   <div class="col-6 col-xl-12 col-sm-12 col-md-6">
                                        <a href="employees-list.php?employee_user_role=Manager">
                                             <div class="card">
                                                  <div class="card-body">
                                                       <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                 <h4 class="card-title mb-2 d-flex align-items-center gap-2">Managers</h4>
                                                                 <p class="text-muted fw-medium fs-22 mb-0"><?php echo $ManagerCount; ?></p>
                                                            </div>
                                                            <div class="d-none d-sm-block">
                                                                 <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                                      <iconify-icon icon="solar:user-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </a>
                                   </div>
                                   <!-- ==========x========== Assigned Manager =======x========= -->
                              </div>
                         </div>

                         <div class="col-xl-12">
                              <div class="row">
                                   <!-- ===================== Assigned Manager ================= -->
                                   <div class="col-6 col-xl-3 col-md-6">
                                        <a href="employees-list.php?employee_user_role=Staff">
                                             <div class="card">
                                                  <div class="card-body">
                                                       <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                 <h4 class="card-title mb-2 d-flex align-items-center gap-2">Staff</h4>
                                                                 <p class="text-muted fw-medium fs-22 mb-0"><?php echo $StaffCount; ?></p>
                                                            </div>
                                                            <div class="d-none d-sm-block">
                                                                 <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                                      <iconify-icon icon="solar:user-id-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </a>
                                   </div>
                                   <!-- ==========x========== Assigned Manager =======x========= -->


                                   <!-- ====================== All Technicians ==================== -->
                                   <div class="col-6 col-xl-3 col-md-6">
                                        <a href="employees-list.php?employee_user_role=Technician">
                                             <div class="card">
                                                  <div class="card-body">
                                                       <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                 <h4 class="card-title mb-2 d-flex align-items-center gap-2">Technicians</h4>
                                                                 <p class="text-muted fw-medium fs-22 mb-0"><?php echo $TechniciansCount; ?></p>
                                                            </div>
                                                            <div class="d-none d-sm-block">
                                                                 <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                                      <iconify-icon icon="solar:people-nearby-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </a>
                                   </div>
                                   <!-- ==========x=========== All Technicians ==========x========= -->


                                   <!-- ======================= All Leads ====================== -->
                                   <div class="col-6 col-xl-3 col-md-6">
                                        <a href="customers-list.php">
                                             <div class="card">
                                                  <div class="card-body">
                                                       <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                 <h4 class="card-title mb-2 d-flex align-items-center gap-2">Customers</h4>
                                                                 <p class="text-muted fw-medium fs-22 mb-0"><?php echo $leadsCount; ?></p>
                                                            </div>
                                                            <div class="d-none d-sm-block">
                                                                 <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                                      <iconify-icon icon="solar:notebook-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </a>
                                   </div>
                                   <!-- ===========x=========== All Leads ===========x========== -->


                                   <!-- ====================== All Products ==================== -->
                                   <div class="col-6 col-xl-3 col-md-6">
                                        <a href="product-list.php">
                                             <div class="card">
                                                  <div class="card-body">
                                                       <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                 <h4 class="card-title mb-2 d-flex align-items-center gap-2">Products</h4>
                                                                 <p class="text-muted fw-medium fs-22 mb-0"><?php echo $ProductCount; ?></p>
                                                            </div>
                                                            <div class="d-none d-sm-block">
                                                                 <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                                      <iconify-icon icon="solar:box-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </a>
                                   </div>
                                   <!-- ==========x=========== All Products ==========x========= -->
                              </div>
                         </div>

                         <div class="col-xl-12">
                              <div class="card">
                                   <div class="card-header border-0">
                                        <h4 class="card-title mb-0">Followups Details</h4>
                                   </div>
                              </div>
                         </div>


                         <div class="col-xl-12">
                              <div class="row">
                                   <div class="col-lg-3">
                                        <a href="lead-new.php">
                                             <div class="card overflow-hidden">
                                                  <div class="card-body">
                                                       <div class="row">
                                                            <div class="col-4">
                                                                 <div class="avatar-md bg-soft-primary rounded">
                                                                      <iconify-icon icon="solar:box-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                                                 </div>
                                                            </div> <!-- end col -->
                                                            <div class="col-8 text-end">
                                                                 <p class="text-muted mb-0 text-truncate">New Leads</p>
                                                                 <h3 class="text-dark mt-1 mb-0"><?php echo $newleadcount; ?></h3>
                                                            </div> <!-- end col -->
                                                       </div> <!-- end row-->
                                                  </div> <!-- end card body -->
                                                  <div class="card-footer py-2 bg-light bg-opacity-50">
                                                       <div class="d-flex align-items-center justify-content-end">
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->


                                   <div class="col-lg-3">
                                        <a href="followup_today_visit.php">
                                             <div class="card overflow-hidden">
                                                  <div class="card-body">
                                                       <div class="row">
                                                            <div class="col-4">
                                                                 <div class="avatar-md bg-soft-primary rounded">
                                                                      <iconify-icon icon="solar:bicycling-round-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                                                 </div>
                                                            </div> <!-- end col -->
                                                            <div class="col-8 text-end">
                                                                 <p class="text-muted mb-0 text-truncate">Today's Visits</p>
                                                                 <h3 class="text-dark mt-1 mb-0"><?php echo $todaysVisitCount; ?></h3>
                                                            </div> <!-- end col -->
                                                       </div> <!-- end row-->
                                                  </div> <!-- end card body -->
                                                  <div class="card-footer py-2 bg-light bg-opacity-50">
                                                       <div class="d-flex align-items-center justify-content-end">
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->



                                   <div class="col-lg-3">
                                        <a href="followup-todays-list.php">
                                             <div class="card overflow-hidden">
                                                  <div class="card-body">
                                                       <div class="row">
                                                            <div class="col-4">
                                                                 <div class="avatar-md bg-soft-primary rounded">
                                                                      <iconify-icon icon="solar:clipboard-list-line-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                                                 </div>
                                                            </div> <!-- end col -->
                                                            <div class="col-8 text-end">
                                                                 <p class="text-muted mb-0 text-truncate">Today's Followups</p>
                                                                 <h3 class="text-dark mt-1 mb-0"><?php echo $todaysFollowupCount; ?></h3>
                                                            </div> <!-- end col -->
                                                       </div> <!-- end row-->
                                                  </div> <!-- end card body -->
                                                  <div class="card-footer py-2 bg-light bg-opacity-50">
                                                       <div class="d-flex align-items-center justify-content-end">
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->


                                   <div class="col-lg-3">
                                        <a href="followup-upcoming-list.php">
                                             <div class="card overflow-hidden">
                                                  <div class="card-body">
                                                       <div class="row">
                                                            <div class="col-4">
                                                                 <div class="avatar-md bg-soft-primary rounded">
                                                                      <iconify-icon icon="solar:calendar-broken" class="avatar-title fs-32 text-primary"></iconify-icon>
                                                                 </div>
                                                            </div> <!-- end col -->
                                                            <div class="col-8 text-end">
                                                                 <p class="text-muted mb-0 text-truncate">Upcoming Leads</p>
                                                                 <h3 class="text-dark mt-1 mb-0"><?php echo $upcomingOrderCount; ?></h3>
                                                            </div> <!-- end col -->
                                                       </div> <!-- end row-->
                                                  </div> <!-- end card body -->
                                                  <div class="card-footer py-2 bg-light bg-opacity-50">
                                                       <div class="d-flex align-items-center justify-content-end">
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->



                                   <div class="col-lg-3">
                                        <a href="followup-delayed-list.php">
                                             <div class="card overflow-hidden">
                                                  <div class="card-body">
                                                       <div class="row">
                                                            <div class="col-4">
                                                                 <div class="avatar-md bg-soft-primary rounded">
                                                                      <iconify-icon icon="solar:calendar-mark-line-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                                                 </div>
                                                            </div> <!-- end col -->
                                                            <div class="col-8 text-end">
                                                                 <p class="text-muted mb-0 text-truncate">Delayed Leads</p>
                                                                 <h3 class="text-dark mt-1 mb-0"><?php echo $delayedOrderCount; ?></h3>
                                                            </div> <!-- end col -->
                                                       </div> <!-- end row-->
                                                  </div> <!-- end card body -->
                                                  <div class="card-footer py-2 bg-light bg-opacity-50">
                                                       <div class="d-flex align-items-center justify-content-end">
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->


                                   <div class="col-lg-3">
                                        <a href="followup-place-order.php">
                                             <div class="card overflow-hidden">
                                                  <div class="card-body">
                                                       <div class="row">
                                                            <div class="col-4">
                                                                 <div class="avatar-md bg-soft-primary rounded">
                                                                      <iconify-icon icon="solar:streets-map-point-line-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                                                 </div>
                                                            </div> <!-- end col -->
                                                            <div class="col-8 text-end">
                                                                 <p class="text-muted mb-0 text-truncate">Place Orders</p>
                                                                 <h3 class="text-dark mt-1 mb-0"><?php echo $myPlaceOrderCount; ?></h3>
                                                            </div> <!-- end col -->
                                                       </div> <!-- end row-->
                                                  </div> <!-- end card body -->
                                                  <div class="card-footer py-2 bg-light bg-opacity-50">
                                                       <div class="d-flex align-items-center justify-content-end">
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->


                                   <div class="col-lg-3">
                                        <a href="followup-order-cancel.php">
                                             <div class="card overflow-hidden">
                                                  <div class="card-body">
                                                       <div class="row">
                                                            <div class="col-4">
                                                                 <div class="avatar-md bg-soft-primary rounded">
                                                                      <iconify-icon icon="solar:cart-cross-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                                                 </div>
                                                            </div> <!-- end col -->
                                                            <div class="col-8 text-end">
                                                                 <p class="text-muted mb-0 text-truncate">Order Cancelled</p>
                                                                 <h3 class="text-dark mt-1 mb-0"><?php echo $CancelOrderCount; ?></h3>
                                                            </div> <!-- end col -->
                                                       </div> <!-- end row-->
                                                  </div> <!-- end card body -->
                                                  <div class="card-footer py-2 bg-light bg-opacity-50">
                                                       <div class="d-flex align-items-center justify-content-end">
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->


                                   <div class="col-lg-3">
                                        <a href="followup-order-completed.php">
                                             <div class="card overflow-hidden">
                                                  <div class="card-body">
                                                       <div class="row">
                                                            <div class="col-4">
                                                                 <div class="avatar-md bg-soft-primary rounded">
                                                                      <iconify-icon icon="solar:golf-bold-duotone" class="avatar-title fs-32 text-primary"></iconify-icon>
                                                                 </div>
                                                            </div> <!-- end col -->
                                                            <div class="col-8 text-end">
                                                                 <p class="text-muted mb-0 text-truncate">Order Completed</p>
                                                                 <h3 class="text-dark mt-1 mb-0"><?php echo $CompletedOrderCount; ?></h3>
                                                            </div> <!-- end col -->
                                                       </div> <!-- end row-->
                                                  </div> <!-- end card body -->
                                                  <div class="card-footer py-2 bg-light bg-opacity-50">
                                                       <div class="d-flex align-items-center justify-content-end">
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div>
                              </div>
                         </div> <!-- end col -->
                    </div> <!-- end row -->

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

     <!-- Apex Chart Pie js -->
     <?php include("donut-chart.php"); ?>

</body>

</html>

<?php
$conn->close();
?>