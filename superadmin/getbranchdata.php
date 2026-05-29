<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

################# Only allow Super Admin #################
if ($Role !== 'Super Admin') {
     echo "<script>window.location.href='../index.php';</script>";
     exit;
}

################ Get list of active companies ################
$Companyname = [];
$CompaniesQuery = mysqli_query($conn, "SELECT * FROM `companies` WHERE `company_status` = 1");
if (mysqli_num_rows($CompaniesQuery) > 0) {
     while ($row = mysqli_fetch_assoc($CompaniesQuery)) {
          $Companyname[] = $row;
     }
}

################### If branch is selected, proceed #####################
if (isset($_GET['lead_for_branch'])) {
     $lead_for_branch = $_GET['lead_for_branch'];

     ############ Dates ###############
     $today = date("Y-m-d");
     $oneYearAgo = date("Y-m-d", strtotime("-365 days"));
     $oneYearLater = date("Y-m-d", strtotime("+365 days"));

     ############ Function to get count ###############
     function getCount($conn, $query)
     {
          $result = mysqli_query($conn, $query);
          $row = mysqli_fetch_assoc($result);
          return $row['total'] ?? 0;
     }

     ############ New Leads: Assigned but never updated ###############
     $newleadcount = getCount($conn, "
        SELECT COUNT(*) AS total FROM `leads`
        WHERE `lead_for_branch` = '$lead_for_branch'
        AND `lead_assign_to` != ''
         AND (`lead_updatedby` = '' OR `lead_updatedby` IS NULL)
    ");

     ############ Today's Visit: Assigned, updated, status = 'Client Visit' for today ###############
     $todaysVisitCount = getCount($conn, "
        SELECT COUNT(*) AS total FROM `leads`
        WHERE `lead_for_branch` = '$lead_for_branch'
        AND `lead_next_followup_date` = '$today'
        AND `lead_assign_to` != ''
        AND `lead_updatedby` != ''
        AND `lead_status` = 'Client Visit'
    ");

     ############ Today's Follow-up: Assigned, updated, for today ###############
     $todaysFollowupCount = getCount($conn, "
        SELECT COUNT(*) AS total FROM `leads`
        WHERE `lead_for_branch` = '$lead_for_branch'
        AND `lead_next_followup_date` = '$today'
        AND `lead_assign_to` != ''
        AND `lead_updatedby` != ''
    ");

     ############ Place Order: Assigned, updated, type = 'Place Order' ###############
     $myPlaceOrderCount = getCount($conn, "
        SELECT COUNT(*) AS total FROM `leads`
        WHERE `lead_for_branch` = '$lead_for_branch'
        AND `lead_assign_to` != ''
        AND `lead_updatedby` != ''
        AND `lead_type` = 'Place Order'
    ");

     ############ Delayed Orders: Assigned, not updated, followup date before today and within 1 year ###############
     $delayedOrderCount = getCount($conn, "
        SELECT COUNT(*) AS total FROM `leads`
        WHERE `lead_for_branch` = '$lead_for_branch'
        AND `lead_assign_to` != ''
        AND `lead_updatedby` = ''
        AND `lead_next_followup_date` < '$today'
        AND `lead_next_followup_date` >= '$oneYearAgo'
        AND `lead_type` NOT IN ('Completed', 'Cancel')
    ");

     ############ Upcoming Orders: Assigned, updated, followup date after today and within 1 year ###############
     $upcomingOrderCount = getCount($conn, "
        SELECT COUNT(*) AS total FROM `leads`
        WHERE `lead_for_branch` = '$lead_for_branch'
        AND `lead_assign_to` != ''
        AND `lead_updatedby` != ''
        AND `lead_next_followup_date` > '$today'
        AND `lead_next_followup_date` <= '$oneYearLater'
        AND `lead_type` NOT IN ('Completed', 'Cancel')
    ");

     ############ Cancel Orders: Assigned, updated, type = 'Cancel' ###############
     $CancelOrderCount = getCount($conn, "
        SELECT COUNT(*) AS total FROM `leads`
        WHERE `lead_for_branch` = '$lead_for_branch'
        AND `lead_assign_to` != ''
        AND `lead_updatedby` != ''
        AND `lead_type` = 'Cancel'
    ");

     ############ Completed Orders: Assigned, updated, type = 'Completed' ###############
     $CompletedOrderCount = getCount($conn, " SELECT COUNT(*) AS total FROM `leads` WHERE `lead_for_branch` = '$lead_for_branch' AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` = 'Completed'
    ");
}
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
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
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
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
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
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
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
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
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
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
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
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
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
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
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
                                                            <span class="text-reset fw-semibold fs-12">View List</span>
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
     <?php include("donut-chart1.php"); ?>

</body>

</html>

<?php
$conn->close();
?>