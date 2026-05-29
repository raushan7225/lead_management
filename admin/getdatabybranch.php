<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

############## Get Current Date & Time ##############
$date = date("Y-m-d");
$currentDate = new DateTime();
################## Get Branch Data and Count Queries ##################
if (isset($_GET['lead_for_branch'])) {
     $lead_for_branch = $_GET['lead_for_branch'] ?? null;

     if (!$lead_for_branch && isset($_POST['lead_for_branch'])) {
          $lead_for_branch = $_POST['lead_for_branch'];
          echo "<script>window.location.href='index.php?lead_for_branch=$lead_for_branch';</script>";
          exit;
     }

     ################ if branch not found the redirect to home ###################
     if (!$lead_for_branch) {
          echo "<script>window.location.href='index.php';</script>";
          exit;
     }
     ################ if branch not found the redirect to home ###################

     function fetchAllAssoc($conn, $sql, $params = [])
     {
          $stmt = mysqli_prepare($conn, $sql);
          if (!empty($params)) {
               mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);
          }
          mysqli_stmt_execute($stmt);
          $result = mysqli_stmt_get_result($stmt);
          return mysqli_fetch_all($result, MYSQLI_ASSOC);
     }

     function getCount($conn, $sql, $params = [])
     {
          $stmt = mysqli_prepare($conn, $sql);
          if (!empty($params)) {
               mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);
          }
          mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt, $count);
          mysqli_stmt_fetch($stmt);
          return $count;
     }

     ######################## Branches ########################
     $branchename = fetchAllAssoc($conn, "SELECT * FROM `branches` WHERE `branch_of_company`=? AND `branch_status`=1", [$Company]);

     ######################## Technicians ########################
     $Technicianname = fetchAllAssoc($conn, "SELECT * FROM `employees` WHERE `employee_of_company`=? AND `employee_user_role`='Technician' AND `employee_status`=1", [$Company]);

     ######################## Edit / Delete ########################
     if (isset($_POST['delete_lead'], $_POST['mylead_id'])) {
          $stmt = mysqli_prepare($conn, "DELETE FROM `leads` WHERE `id` = ?");
          mysqli_stmt_bind_param($stmt, "i", $_POST['mylead_id']);
          if (mysqli_stmt_execute($stmt)) {
               echo "<script>window.location.href='lead-list.php'</script>";
          } else {
               echo "Error: " . mysqli_error($conn);
          }
     } elseif (isset($_POST['edit_lead'], $_POST['mylead_id'])) {
          echo "<script>window.location.href='followup-edit.php?id={$_POST['mylead_id']}';</script>";
          exit;
     }
     ######################## Edit / Delete ########################

     ################ Count queries (all indexed fields) ##################
     $newleadcount = getCount($conn, "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company`=? AND `lead_for_branch`=? AND `lead_assign_to`!='' AND `lead_updatedby`=''", [$Company, $lead_for_branch]);
     $todaysVisitCount = getCount($conn, "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company`=? AND `lead_for_branch`=? AND `lead_next_followup_date`=? AND `lead_assign_to`!='' AND `lead_updatedby`!='' AND `lead_status`='Client Visit'", [$Company, $lead_for_branch, $date]);
     $todaysFollowupCount = getCount($conn, "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company`=? AND `lead_for_branch`=? AND `lead_next_followup_date`=? AND `lead_assign_to`!='' AND `lead_updatedby`!=''", [$Company, $lead_for_branch, $date]);
     $myPlaceOrderCount = getCount($conn, "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company`=? AND `lead_for_branch`=? AND `lead_assign_to`!='' AND `lead_updatedby`!='' AND `lead_type`='Place Order'", [$Company, $lead_for_branch]);
     $CancelOrderCount = getCount($conn, "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company`=? AND `lead_for_branch`=? AND `lead_assign_to`!='' AND `lead_updatedby`!='' AND `lead_type`='Cancel'", [$Company, $lead_for_branch]);
     $CompletedOrderCount = getCount($conn, "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company`=? AND `lead_for_branch`=? AND `lead_assign_to`!='' AND `lead_updatedby`!='' AND `lead_type`='Completed'", [$Company, $lead_for_branch]);
     ################ Count queries (all indexed fields) ##################

     ################ Delayed / Upcoming in one optimized query ##################
     $delayedDates = $upcomingDates = [];
     for ($i = 1; $i <= 365; $i++) {
          $delayedDates[] = $currentDate->modify("-1 day")->format("Y-m-d");
     }
     $currentDate = new DateTime(); // reset date
     for ($i = 1; $i <= 365; $i++) {
          $upcomingDates[] = $currentDate->modify("+1 day")->format("Y-m-d");
     }

     $datePlaceholders = implode(',', array_fill(0, count($delayedDates), '?'));
     $delayedOrderCount = getCount(
          $conn,
          "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company`=? AND `lead_for_branch`=? AND `lead_next_followup_date` IN ($datePlaceholders) AND `lead_assign_to`!='' AND `lead_updatedby`='' AND `lead_type` NOT IN ('Completed', 'Cancel')",
          array_merge([$Company, $lead_for_branch], $delayedDates)
     );

     $datePlaceholders = implode(',', array_fill(0, count($upcomingDates), '?'));
     $upcomingOrderCount = getCount(
          $conn,
          "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company`=? AND `lead_for_branch`=? AND `lead_next_followup_date` IN ($datePlaceholders) AND `lead_assign_to`!='' AND `lead_updatedby`!='' AND `lead_type` NOT IN ('Completed', 'Cancel')",
          array_merge([$Company, $lead_for_branch], $upcomingDates)
     );
} else {
     echo "<script>window.location.href='index.php'</script>";
     exit;
}
################## Get Branch Data and Count Queries ##################
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
                         <div class="col-xl-6">
                              <div class="card">
                                   <div class="card-body">
                                        <div dir="ltr">
                                             <div id="simple-donut" class="apex-charts"></div>
                                        </div>
                                   </div> <!-- end card body -->
                              </div> <!-- end card -->
                         </div> <!-- end col -->


                         <div class="col-lg-6">
                              <div class="row">
                                   <div class="col-lg-12">
                                        <div class="card">
                                             <div class="card-body">
                                                  <div class="d-flex justify-content-between align-items-center">
                                                       <div class="card-title">Hi! <?php echo $fullname; ?>. <br><small class="text-muted">Welcome back buddy!!</small></div>
                                                       <a href="lead-add.php" class="btn btn-soft-primary px-lg-5">Add Lead</a>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>

                                   <div class="col-lg-6">
                                        <a href="lead-new.php?lead_for_branch=<?php echo $lead_for_branch; ?>">
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
                                                            <a href="lead-new.php?lead_for_branch=<?php echo $lead_for_branch; ?>" class="text-reset fw-semibold fs-12">View List</a>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->


                                   <div class="col-lg-6">
                                        <a href="followup_today_visit.php?lead_for_branch=<?php echo $lead_for_branch; ?>">
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
                                                            <a href="followup_today_visit.php?lead_for_branch=<?php echo $lead_for_branch; ?>" class="text-reset fw-semibold fs-12">View List</a>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->



                                   <div class="col-lg-6">
                                        <a href="followup-todays-list.php?lead_for_branch=<?php echo $lead_for_branch; ?>">
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
                                                            <a href="followup-todays-list.php?lead_for_branch=<?php echo $lead_for_branch; ?>" class="text-reset fw-semibold fs-12">View List</a>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->


                                   <div class="col-lg-6">
                                        <a href="followup-upcoming-list.php?lead_for_branch=<?php echo $lead_for_branch; ?>">
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
                                                            <a href="followup-upcoming-list.php?lead_for_branch=<?php echo $lead_for_branch; ?>" class="text-reset fw-semibold fs-12">View List</a>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->
                              </div>
                         </div>
                    </div>


                    <div class="row">
                         <div class="col-xl-12">
                              <div class="row">
                                   <div class="col-lg-3">
                                        <a href="followup-delayed-list.php?lead_for_branch=<?php echo $lead_for_branch; ?>">
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
                                                            <a href="followup-delayed-list.php?lead_for_branch=<?php echo $lead_for_branch; ?>" class="text-reset fw-semibold fs-12">View List</a>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->


                                   <div class="col-lg-3">
                                        <a href="followup-place-order.php?lead_for_branch=<?php echo $lead_for_branch; ?>">
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
                                                            <a href="followup-place-order.php?lead_for_branch=<?php echo $lead_for_branch; ?>" class="text-reset fw-semibold fs-12">View List</a>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->


                                   <div class="col-lg-3">
                                        <a href="followup-order-cancel.php?lead_for_branch=<?php echo $lead_for_branch; ?>">
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
                                                            <a href="followup-order-cancel.php?lead_for_branch=<?php echo $lead_for_branch; ?>" class="text-reset fw-semibold fs-12">View List</a>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div> <!-- end col -->


                                   <div class="col-lg-3">
                                        <a href="followup-order-completed.php?lead_for_branch=<?php echo $lead_for_branch; ?>">
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
                                                            <a href="followup-order-completed.php?lead_for_branch=<?php echo $lead_for_branch; ?>" class="text-reset fw-semibold fs-12">View List</a>
                                                       </div>
                                                  </div> <!-- end card body -->
                                             </div> <!-- end card -->
                                        </a>
                                   </div>
                              </div>
                         </div> <!-- end col -->
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
     <?php include("donut_chart1.php"); ?>

</body>

</html>