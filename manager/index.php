<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");


################# Check role ###################
if ($Role !== 'Manager') {
     header("Location: ../index.php");
     exit;
}
################# Check role ###################

################# check maintain mode #####################
if ($ModeStatus == 1) {
    header("Location: ../maintenance.php");
    exit;
}
################# check maintain mode #####################

##################### Handle edit action ################
if (isset($_POST['edit_lead'], $_POST['mylead_id'])) {
     $mylead_id = $conn->real_escape_string($_POST['mylead_id']);
     header("Location: followup-edit.php?id=" . urlencode($mylead_id));
     exit;
}
 
##################### Current date ################
$date = date("Y-m-d");

##################### Optimized counting queries - single query for each count ################
$countQueries = [
     'newleadcount' => "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `lead_assign_to`!='' AND `lead_updatedby` = ''",
     'todaysVisitCount' => "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `lead_next_followup_date`='$date' AND `lead_assign_to` != '' AND `lead_updatedby` !='' AND `lead_status` = 'Client Visit'",
     'todaysFollowupCount' => "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `lead_next_followup_date` = '$date' AND `lead_assign_to`!='' AND `lead_updatedby` !='' AND `lead_type` NOT IN ('Completed', 'Cancel')",
     'myPlaceOrderCount' => "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` = 'Place Order'",
     'CancelOrderCount' => "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` = 'Cancel'",
     'CompletedOrderCount' => "SELECT COUNT(*) FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `lead_assign_to` !='' AND `lead_updatedby` != '' AND `lead_type` = 'Completed'"
];

##################### Execute count queries ################
foreach ($countQueries as $var => $query) {
     $$var = $conn->query($query)->fetch_row()[0] ?? 0;
}

##################### Optimized delayed and upcoming order counts (replaced 365 queries with 2) ################
$delayedOrderCount = $conn->query("
    SELECT COUNT(*) FROM `leads` 
    WHERE `lead_for_company`='$Company' 
    AND `lead_for_branch`='$Branch' 
    AND `lead_next_followup_date` BETWEEN DATE_SUB('$date', INTERVAL 365 DAY) AND DATE_SUB('$date', INTERVAL 1 DAY)
    AND `lead_assign_to`!='' 
    AND `lead_updatedby`='' 
    AND `lead_type` NOT IN ('Completed', 'Cancel')
")->fetch_row()[0] ?? 0;


$currentDate = new DateTime();
$endDate = clone $currentDate;
$endDate->modify('+365 days');
$start = $currentDate->format('Y-m-d');
$end = $endDate->format('Y-m-d');
$leadData = [];
$lead_list_query = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `lead_for_branch` = '$Branch' AND `lead_assign_to` != '' AND `lead_updatedby` != '' AND `lead_type` NOT IN ('Completed', 'Cancel') AND `lead_next_followup_date` BETWEEN '$start' AND '$end'ORDER BY `lead_next_followup_date` ASC, `id` DESC";
$lead_list_result = $conn->query($lead_list_query);
$upcomingOrderCount = $lead_list_result->num_rows;

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
                                             <div class="card-title">Hi! <?= htmlspecialchars($fullname) ?>. <br><small class="text-muted">Welcome back buddy!!</small></div>
                                             <a href="lead-add.php" class="btn btn-soft-primary px-lg-5">Add Lead</a>
                                        </div>
                                   </div>
                              </div>

                              <div class="row">
                                   <!-- Dashboard Cards -->
                                   <?php
                                   $cards = [
                                        [
                                             'icon' => 'solar:box-bold-duotone',
                                             'title' => 'New Leads',
                                             'count' => $newleadcount,
                                             'link' => 'lead-new.php'
                                        ],
                                        [
                                             'icon' => 'solar:bicycling-round-bold-duotone',
                                             'title' => "Today's Visits",
                                             'count' => $todaysVisitCount,
                                             'link' => 'followup_today_visit.php'
                                        ],
                                        [
                                             'icon' => 'solar:clipboard-list-line-duotone',
                                             'title' => "Today's Followups",
                                             'count' => $todaysFollowupCount,
                                             'link' => 'followup-todays-list.php'
                                        ],
                                        [
                                             'icon' => 'solar:calendar-broken',
                                             'title' => 'Upcoming Leads',
                                             'count' => $upcomingOrderCount,
                                             'link' => 'followup-upcoming-list.php'
                                        ]
                                   ];

                                   foreach ($cards as $card): ?>
                                        <div class="col-lg-6">
                                             <a href="<?= $card['link'] ?>">
                                                  <div class="card overflow-hidden">
                                                       <div class="card-body">
                                                            <div class="row">
                                                                 <div class="col-4">
                                                                      <div class="avatar-md bg-soft-primary rounded">
                                                                           <iconify-icon icon="<?= $card['icon'] ?>" class="avatar-title fs-32 text-primary"></iconify-icon>
                                                                      </div>
                                                                 </div>
                                                                 <div class="col-8 text-end">
                                                                      <p class="text-muted mb-0 text-truncate"><?= $card['title'] ?></p>
                                                                      <h3 class="text-dark mt-1 mb-0"><?= $card['count'] ?></h3>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                       <div class="card-footer py-2 bg-light bg-opacity-50">
                                                            <div class="d-flex align-items-center justify-content-end">
                                                                 <a href="<?= $card['link'] ?>" class="text-reset fw-semibold fs-12">View List</a>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </a>
                                        </div>
                                   <?php endforeach; ?>
                              </div>
                         </div>

                         <!-- Bottom Row Cards -->
                         <?php
                         $bottomCards = [
                              [
                                   'icon' => 'solar:calendar-mark-line-duotone',
                                   'title' => 'Delayed Leads',
                                   'count' => $delayedOrderCount,
                                   'link' => 'followup-delayed-list.php'
                              ],
                              [
                                   'icon' => 'solar:streets-map-point-line-duotone',
                                   'title' => 'Place Orders',
                                   'count' => $myPlaceOrderCount,
                                   'link' => 'followup-place-order.php'
                              ],
                              [
                                   'icon' => 'solar:cart-cross-bold-duotone',
                                   'title' => 'Order Cancelled',
                                   'count' => $CancelOrderCount,
                                   'link' => 'followup-order-cancel.php'
                              ],
                              [
                                   'icon' => 'solar:golf-bold-duotone',
                                   'title' => 'Order Completed',
                                   'count' => $CompletedOrderCount,
                                   'link' => 'followup-order-completed.php'
                              ]
                         ];

                         foreach ($bottomCards as $card): ?>
                              <div class="col-lg-3">
                                   <a href="<?= $card['link'] ?>">
                                        <div class="card overflow-hidden">
                                             <div class="card-body">
                                                  <div class="row">
                                                       <div class="col-4">
                                                            <div class="avatar-md bg-soft-primary rounded">
                                                                 <iconify-icon icon="<?= $card['icon'] ?>" class="avatar-title fs-32 text-primary"></iconify-icon>
                                                            </div>
                                                       </div>
                                                       <div class="col-8 text-end">
                                                            <p class="text-muted mb-0 text-truncate"><?= $card['title'] ?></p>
                                                            <h3 class="text-dark mt-1 mb-0"><?= $card['count'] ?></h3>
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="card-footer py-2 bg-light bg-opacity-50">
                                                  <div class="d-flex align-items-center justify-content-end">
                                                       <a href="<?= $card['link'] ?>" class="text-reset fw-semibold fs-12">View List</a>
                                                  </div>
                                             </div>
                                        </div>
                                   </a>
                              </div>
                         <?php endforeach; ?>
                    </div>
                    
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

               <?php include("../footer.php"); ?>
          </div>
     </div>

     <script src="../assets/js/vendor.js"></script>
     <script src="../assets/js/app.js"></script>
     <?php include("manager-donut-chart.php"); ?>
</body>

</html>