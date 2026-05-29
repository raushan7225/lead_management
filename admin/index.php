<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

################## Redirect non-admins ######################
if ($Role !== 'Admin') {
     echo "<script>window.location.href='../index.php';</script>";
     exit;
}
################## Redirect non-admins ######################

################# check maintain mode #####################
if ($ModeStatus == 1) {
     header("Location: ../maintenance.php");
     exit;
}
################# check maintain mode #####################

// ####################### Fetch Branch ########################
// $BranchList = "SELECT `branch_id` FROM `branches` WHERE `branch_of_company` = '$Company' AND `branch_status` = '1'";
// $BranchListResult = mysqli_query($conn, $BranchList);
// $BranchCount = mysqli_num_rows($BranchListResult);
// ####################### Fetch Branch ########################


##################### Fetch employees #####################
$employees_list = "SELECT `employee_user_role` FROM `employees` WHERE `employee_of_company`='$Company' AND `employee_user_role` NOT IN ('Admin', 'Super Admin') AND `employee_status` = '1'";
$employees_list_results = $conn->query($employees_list);
$EmployeeCount = mysqli_num_rows($employees_list_results);
##################### Fetch employees #####################


##################### Fetch employees #####################
$manager_list = "SELECT `employee_user_role` FROM `employees` WHERE `employee_of_company`='$Company' AND `employee_user_role`='Manager' AND `employee_status` = '1'";
$manager_list_results = $conn->query($manager_list);
$ManagerCount = mysqli_num_rows($manager_list_results);
##################### Fetch manager #####################


#################### Fetch Leads Count ####################
$leads_count = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_customer_status` = '1'";
$leads_count_result = $conn->query($leads_count);
$leadsCount = $leads_count_result->num_rows;
#################### Fetch Leads Count ####################


############### Products list #####################
$products_list = "SELECT * FROM `products` WHERE `product_for_company` = '$Company' AND `status` = '1'";
$products_list_result = $conn->query($products_list);
$ProductCount = $products_list_result->num_rows;
############### Products list #####################

################## Utility: Count from query ######################
function getCount($conn, $sql)
{
     $res = mysqli_query($conn, $sql);
     return $res ? (int)mysqli_fetch_array($res)[0] : 0;
}
################## Utility: Count from query ######################

################## Load branches ######################
$BranchesQuery = mysqli_query($conn, "SELECT * FROM `branches` WHERE `branch_of_company`='$Company' AND `branch_status` = 1");
$branchename = ($BranchesQuery && mysqli_num_rows($BranchesQuery) > 0) ? mysqli_fetch_all($BranchesQuery, MYSQLI_ASSOC) : [];
################## Load branches ######################

################## Load technicians ######################
$TechnicianQuery = mysqli_query($conn, "SELECT * FROM `employees` WHERE `employee_of_company`='$Company' AND `employee_user_role`='Technician' AND `employee_status`=1");
$Technicianname = ($TechnicianQuery && mysqli_num_rows($TechnicianQuery) > 0) ? mysqli_fetch_all($TechnicianQuery, MYSQLI_ASSOC) : [];
################## Load technicians ######################

################## Handle delete ######################
if (isset($_POST['delete_lead'], $_POST['mylead_id'])) {
     $id = $_POST['mylead_id'];
     mysqli_query($conn, "DELETE FROM `leads` WHERE `id`='$id'");
     echo "<script>window.location.href='lead-list.php';</script>";
     exit;
}
################## Handle delete ######################

################## Handle edit ######################
if (isset($_POST['edit_lead'], $_POST['mylead_id'])) {
     $id = $_POST['mylead_id'];
     echo "<script>window.location.href='followup-edit.php?id=$id';</script>";
     exit;
}
################## Handle edit ######################

################## Handle filter ######################
if (isset($_POST['lead_for_branch'])) {
     $branch = $_POST['lead_for_branch'];
     echo "<script>window.location.href='getdatabybranch.php?lead_for_branch=$branch';</script>";
     exit;
}
################## Handle filter ######################

################## Current date ######################
$date = date("Y-m-d");
################## Current date ######################

################## New Leads Count ######################
$newleadcount = getCount($conn, " SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_assign_to` != '' AND `lead_updatedby` = '' AND `lead_customer_status` = '1'");
################## New Leads Count ######################

################## Today's Visit Count ######################
$todaysVisitCount = getCount($conn, " SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_next_followup_date` = '$date' AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_status` = 'Client Visit' AND `lead_customer_status` = '1'");
################## Today's Visit Count ######################

################## Today's Followup Count ######################
$todaysFollowupCount = getCount($conn, " SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_next_followup_date` = '$date' AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_customer_status` = '1'");
################## Today's Followup Count ######################

################## Place Order Count ######################
$myPlaceOrderCount = getCount($conn, " SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` = 'Place Order' AND `lead_customer_status` = '1'");
################## Place Order Count ######################

################## Delayed Order Count (past 365 days) ######################
$past365 = (new DateTime())->modify("-365 days")->format("Y-m-d");
$delayedOrderCount = getCount($conn, " SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_next_followup_date` >= '$past365' AND `lead_next_followup_date` < CURDATE() AND `lead_assign_to` != '' AND `lead_updatedby` = '' AND `lead_type` NOT IN ('Completed', 'Cancel') AND `lead_customer_status` = '1'");
################## Delayed Order Count (past 365 days) ######################

################## Upcoming Order Count (next 365 days) ######################
$future365 = (new DateTime())->modify("+365 days")->format("Y-m-d");
$upcomingOrderCount = getCount($conn, " SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_next_followup_date` > CURDATE() AND `lead_next_followup_date` <= '$future365' AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` NOT IN ('Completed', 'Cancel') AND `lead_customer_status` = '1'");
################## Upcoming Order Count (next 365 days) ######################

################## Cancelled Orders Count ######################
$CancelOrderCount = getCount($conn, " SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` = 'Cancel' AND `lead_customer_status` = '1'");
################## Cancelled Orders Count ######################

################## Completed Orders Count ######################
$CompletedOrderCount = getCount($conn, " SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` = 'Completed' AND `lead_customer_status` = '1'");
################## Completed Orders Count ######################
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
                                   <div class="card overflow-hidden" style="max-height: 470px;">
                                        <div class="card-title py-2 px-3 bg-primary text-white">
                                             Branch Lists
                                        </div>
                                        <div class="list-group overflow-y-scroll rounded-0">
                                             <form action="#" method="post" enctype="multipart/form-data">
                                                  <?php foreach ($branchename as $myyBranch) { ?>
                                                       <div class="d-flex justify-content-start align-items-center">
                                                            <iconify-icon icon="solar:buildings-bold-duotone" class="avatar-title fs-32 text-primary" style="width: 70px"></iconify-icon> <input type="submit" name="lead_for_branch" class="list-group-item list-group-item-action" value="<?php echo $myyBranch['branch_name']; ?>">
                                                       </div>
                                                  <?php } ?>
                                             </form>
                                        </div>
                                   </div> <!-- end card -->
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
                                                                      <iconify-icon icon="solar:backpack-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
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
                                        <a href="employees-list.php">
                                             <div class="card">
                                                  <div class="card-body">
                                                       <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                 <h4 class="card-title mb-2 d-flex align-items-center gap-2">Employees</h4>
                                                                 <p class="text-muted fw-medium fs-22 mb-0"><?php echo $EmployeeCount; ?></p>
                                                            </div>
                                                            <div class="d-none d-sm-block">
                                                                 <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                                                      <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </a>
                                   </div>
                                   <!-- ==========x========== Total Employee ========x======== -->


                                   <!-- ======================= All Leads ====================== -->
                                   <div class="col-6 col-xl-12 col-sm-12 col-md-6">
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
                                   <div class="col-6 col-xl-12 col-sm-12 col-md-6">
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
                                                                      <iconify-icon icon="solar:box-line-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
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
                                                            <a href="lead-new.php" class="text-reset fw-semibold fs-12">View List</a>
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
                                                            <a href="followup_today_visit.php" class="text-reset fw-semibold fs-12">View List</a>
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
                                                            <a href="followup-todays-list.php" class="text-reset fw-semibold fs-12">View List</a>
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
                                                            <a href="followup-upcoming-list.php" class="text-reset fw-semibold fs-12">View List</a>
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
                                                            <a href="followup-delayed-list.php" class="text-reset fw-semibold fs-12">View List</a>
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
                                                            <a href="followup-place-order.php" class="text-reset fw-semibold fs-12">View List</a>
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
                                                            <a href="followup-order-cancel.php" class="text-reset fw-semibold fs-12">View List</a>
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
                                                            <a href="followup-order-completed.php" class="text-reset fw-semibold fs-12">View List</a>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div>
                              </div>
                         </div> <!-- end col -->
                         
                          <?php
                          if ($AlertNoteDate == date('Y-m-d')) { ?>
                              <!-- =============== Show Alert Notes ================ -->
                              <div class="alert alert-secondary alertNote p-2 mb-0" role="alert">
                                   <div class="d-flex justify-content-between align-items-center position-relative">
                                        <h4 class="alert-heading text-soft-preimary"><?php echo $AlertNoteTitle; ?></h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                   </div>
                                   <div class="alert-body pt-2 border-top">
                                        <?php echo $AlertNoteMessage; ?>
                                   </div>
                              </div>
                              <!-- ==============x====== Show Alert Notes ========x====== -->
                         <?php } ?>
                         
                    </div> <!-- end row -->
               </div><!-- End Container Fluid -->


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
     <?php include("donut_chart.php"); ?>

</body>

</html>