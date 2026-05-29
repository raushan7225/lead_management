<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");


################# Check Technician #####################
if ($Role !== "Staff") {
     echo "<script>window.location.href='../index.php';</script>";
     exit;
}
################# Check Technician #####################

################# check maintain mode #####################
if ($ModeStatus == 1) {
    header("Location: ../maintenance.php");
    exit;
}
################# check maintain mode #####################

############## Current date setup ###############
$today = (new DateTime())->format('Y-m-d');
############## Current date setup ###############


############## Initialize counts array ###############
$counts = [
     'NewLead' => 0,
     'TodaysVisit' => 0,
     'todaysFollowup' => 0,
     'delayedOrder' => 0,
     'upcomingOrder' => 0,
     'MyPlaceOrder' => 0,
     'CancelOrder' => 0,
     'CompletedOrder' => 0,
];
############## Initialize counts array ###############


############## Function to safely get count from database ##############
function getCount($conn, $query, $params, $types)
{
     $count = 0;
     if ($stmt = $conn->prepare($query)) {
          $stmt->bind_param($types, ...$params);
          $stmt->execute();
          $result = $stmt->get_result();
          if ($row = $result->fetch_assoc()) {
               $count = (int)$row['count'];
          }
          $stmt->close();
     }
     return $count;
}
############## Function to safely get count from database ##############

############## Define all count queries with parameters ###############
$queries = [
     'NewLead' => [
          'query' => "SELECT COUNT(*) AS count FROM leads 
                    WHERE lead_for_company = ? 
                      AND lead_for_branch = ? 
                      AND lead_assign_to = ? 
                      AND lead_updatedby = ''
                      AND `lead_customer_status`='1'",
          'params' => [$Company, $Branch, $username],
          'types' => 'sss'
     ],
     'TodaysVisit' => [
          'query' => "SELECT COUNT(*) AS count FROM leads 
                    WHERE lead_for_company = ? 
                      AND lead_for_branch = ? 
                      AND lead_next_followup_date = ? 
                      AND lead_assign_to = ? 
                      AND lead_updatedby = ? 
                      AND lead_status = 'Client Visit'
                      AND `lead_customer_status`='1'",
          'params' => [$Company, $Branch, $today, $username, $username],
          'types' => 'sssss'
     ],
     'todaysFollowup' => [
          'query' => "SELECT COUNT(*) AS count FROM leads 
                    WHERE lead_for_company = ? 
                      AND lead_for_branch = ? 
                      AND lead_next_followup_date = ? 
                      AND lead_assign_to = ? 
                      AND lead_updatedby = ? 
                      AND lead_type NOT IN ('Completed', 'Cancel')
                      AND `lead_customer_status`='1'",
          'params' => [$Company, $Branch, $today, $username, $username],
          'types' => 'sssss'
     ],
     'MyPlaceOrder' => [
          'query' => "SELECT COUNT(*) AS count FROM leads 
               WHERE lead_for_company = ? 
                 AND lead_for_branch = ? 
                 AND lead_assign_to = ? 
                 AND lead_updatedby = ? 
                 AND lead_type = 'Place Order'
                 AND lead_customer_status = '1'",
          'params' => [$Company, $Branch, $username, $username],
          'types' => 'ssss'
     ],
     'CancelOrder' => [
          'query' => "SELECT COUNT(*) AS count FROM leads 
                    WHERE lead_for_company = ? 
                      AND lead_for_branch = ? 
                      AND lead_type = 'Cancel' 
                      AND `lead_customer_status`='1'
                      AND lead_assign_to = ? 
                      AND lead_updatedby = ?",
          'params' => [$Company, $Branch, $username, $username],
          'types' => 'ssss'
     ],
     'CompletedOrder' => [
          'query' => "SELECT COUNT(*) AS count FROM leads 
                    WHERE lead_for_company = ? 
                      AND lead_for_branch = ? 
                      AND lead_assign_to = ? 
                      AND lead_updatedby = ? 
                      AND `lead_customer_status`='1'
                      AND lead_type = 'Completed'",
          'params' => [$Company, $Branch, $username, $username],
          'types' => 'ssss'
     ],
     'delayedOrder' => [
          'query' => "SELECT COUNT(*) AS count FROM leads 
                    WHERE lead_for_company = ? 
                      AND lead_for_branch = ? 
                      AND lead_next_followup_date < ? 
                      AND lead_assign_to = ? 
                      AND lead_updatedby = '' 
                      AND `lead_customer_status`='1'
                      AND lead_type NOT IN ('Completed', 'Cancel')",
          'params' => [$Company, $Branch, $today, $username],
          'types' => 'ssss'
     ],
     'upcomingOrder' => [
          'query' => "SELECT COUNT(*) AS count FROM leads 
                    WHERE lead_for_company = ? 
                      AND lead_for_branch = ? 
                      AND lead_next_followup_date > ? 
                      AND lead_assign_to = ? 
                      AND lead_updatedby = ? 
                      AND `lead_customer_status`='1'
                      AND lead_type NOT IN ('Completed', 'Cancel')",
          'params' => [$Company, $Branch, $today, $username, $username],
          'types' => 'sssss'
     ]
];
############## Define all count queries with parameters ###############

#################### Execute all count queries ########################
foreach ($queries as $key => $data) {
     $counts[$key] = getCount($conn, $data['query'], $data['params'], $data['types']);
}
#################### Execute all count queries ########################

#################### Handle edit lead action #######################
if (isset($_POST['edit_lead'], $_POST['mylead_id'])) {
     $mylead_id = $_POST['mylead_id'];
     $page = empty($lead_updatedby) ? 'manage-followup.php' : 'followup-edit2.php';
     header("Location: $page?id=" . urlencode($mylead_id));
     exit;
}
#################### Handle edit lead action #######################
?>



<!DOCTYPE html>
<html lang="en-US">

<head>
     <title>Dashboard</title>
     <?php include("head.php"); ?>

     <head>
          <title>Dashboard</title>
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
                                   </div>
                              </div>
                         </div>

                         <div class="col-xl-6">
                              <div class="card">
                                   <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                             <div class="card-title">Hi! <?php echo $fullname; ?>. <br><small class="text-muted">Welcome back buddy!!</small></div>
                                             <a href="leads-add.php" class="btn btn-soft-primary px-lg-5">Add Lead</a>
                                        </div>
                                   </div>
                              </div>
                              <div class="row">
                                   <div class="col-lg-6">
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
                                                                 <h3 class="text-dark mt-1 mb-0"><?php echo $counts['NewLead']; ?></h3>
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


                                   <div class="col-lg-6">
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
                                                                 <h3 class="text-dark mt-1 mb-0"><?php echo $counts['TodaysVisit']; ?></h3>
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



                                   <div class="col-lg-6">
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
                                                                 <h3 class="text-dark mt-1 mb-0"><?php echo $counts['todaysFollowup']; ?></h3>
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


                                   <div class="col-lg-6">
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
                                                                 <h3 class="text-dark mt-1 mb-0"><?php echo $counts['upcomingOrder']; ?></h3>
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
                              </div>
                         </div>

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
                                                       <h3 class="text-dark mt-1 mb-0"><?php echo $counts['delayedOrder']; ?></h3>
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
                                                       <h3 class="text-dark mt-1 mb-0"><?php echo $counts['MyPlaceOrder']; ?></h3>
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
                                                       <h3 class="text-dark mt-1 mb-0"><?php echo $counts['CancelOrder']; ?></h3>
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
                                                       <h3 class="text-dark mt-1 mb-0"><?php echo $counts['CompletedOrder']; ?></h3>
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

                    </div>
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
     <?php include("donut_chart.php"); ?>

</body>

</html>