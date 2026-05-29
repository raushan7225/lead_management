<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

if (isset($_GET['id'])) {
     $id = $_GET['id'];

     // Fetch all leads from the database
     $lead_list = "SELECT * FROM `leads` WHERE `id` = '$id'";
     $lead_list_result = $conn->query($lead_list);

     // Prepare the data for JSON output
     $leads_data = [];
     if ($lead_list_result->num_rows > 0) {
          while ($lead = $lead_list_result->fetch_assoc()) {
               $leads_data[] = $lead;
          }
     }
} else {
     echo "<script>window.location.href='lead-list.php';</script>";
     exit;
}
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
     <title>Lead List</title>
     <?php include("head.php"); ?>

     <style>
          #table th {
               background-color: #ff6c2f;
               color: #fff;
          }

          #table td {
               text-align: center;
          }

          .keep-open .dropdown-toggle::after,
          .export .dropdown-toggle::after {
               border-top: none;
               top: 0;
               font-weight: 400;
               margin: 0;
               font-size: 15px;
          }

          .keep-open .dropdown-toggle::after {
               content: "\2611" !important;
               transform: scale(1.5);
          }

          .export .dropdown-toggle::after {
               padding: 2px 2px;
               font-size: 8px;
               transform: scale(1.1);
               border: 1px solid;
          }

          .bootstrap-table .table thead th:first-child .th-inner {
               width: 80px !important;
          }


          .bootstrap-table .table thead th:nth-child(2) .th-inner {
               width: 150px !important;
          }

          .bootstrap-table .table thead th:nth-child(3) .th-inner,
          .bootstrap-table .table thead th:nth-child(4) .th-inner,
          .bootstrap-table .table thead th:nth-child(5) .th-inner {
               width: 160px !important;
          }

          .bootstrap-table .table thead th:nth-child(6) .th-inner {
               width: 200px !important;
          }

          .bootstrap-table .table thead th:nth-child(7) .th-inner {
               width: 300px !important;
          }

          .bootstrap-table .table thead th:nth-child(11) .th-inner {
               width: 200px !important;
          }

          .bootstrap-table .table thead th:nth-child(12) .th-inner {
               width: 200px !important;
          }

          .bootstrap-table .table thead th:nth-child(13) .th-inner {
               width: 300px !important;
          }

          .bootstrap-table .table thead th:last-child .th-inner {
               width: 300px !important;
          }
     </style>
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
                         <div class="col-xl-12">
                              <div class="card">
                                   <div class="d-flex card-header justify-content-between align-items-center">
                                        <h4 class="card-title">Report</h4>
                                   </div>
                                   <div class="card-body">
                                        <table id="table" data-show-columns="true" data-show-export="true" data-show-toggle="true" width="100%"></table>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>

          <!-- Footer -->
          <?php include("../footer.php"); ?>
     </div>

     <script src="../assets/js/vendor.js"></script>
     <script src="../assets/js/app.js"></script>
     <script src="../assets/js/jquery-3.7.1.min.js"></script>
     <script src="../assets/vendor/bootstrap-table-master/js/bootstrap-table.min.js"></script>
     <script src="../assets/vendor/bootstrap-table-master/js/tableExport.min.js"></script>
     <script src="../assets/vendor/bootstrap-table-master/js/bootstrap-table-mobile.js"></script>
     <script src="../assets/vendor/bootstrap-table-master/js/bootstrap-table-export.js"></script>

     <script>
          $(function() {
               // Fetch lead data from PHP dynamically
               const tableData = <?php echo json_encode($leads_data); ?>;

               // Add a counter to each row
               tableData.forEach((row, index) => {
                    row.SoNo = index + 1;
               });

               $('#table').bootstrapTable({
                    pagination: true,
                    search: true,
                    columns: [{
                              field: 'SoNo',
                              title: "So.No.",
                         },
                         {
                              field: 'lead_id',
                              title: 'Customer ID'
                         },
                         {
                              field: 'lead_customer_name',
                              title: 'Customer Name'
                         },
                         {
                              field: 'lead_customer_contact',
                              title: 'Customer Contact'
                         },
                         {
                              field: 'lead_alternate_contact',
                              title: 'Alternate Contact'
                         },
                         {
                              field: 'lead_state',
                              title: 'State'
                         },
                         {
                              field: 'lead_delivery_address',
                              title: 'Address'
                         },
                         {
                              field: 'lead_followup_date',
                              title: 'Followup Date'
                         },
                         {
                              field: 'lead_next_followup_time',
                              title: 'Followup Time'
                         },
                         {
                              field: 'lead_next_followup_date',
                              title: 'Next Followup Date'
                         },
                         {
                              field: 'lead_next_followup_time',
                              title: 'Next Followup Time'
                         },
                         {
                              field: 'lead_status',
                              title: 'Lead Status'
                         },
                         {
                              field: 'lead_product_services',
                              title: 'Machine Name'
                         },
                         {
                              field: 'lead_product_quantity',
                              title: 'Quantity'
                         },
                         {
                              field: 'lead_sub_total',
                              title: 'Sub Total'
                         },
                         {
                              field: 'lead_other_charges',
                              title: 'Other Charges'
                         },
                         {
                              field: 'lead_grand_total',
                              title: 'Grand Total'
                         },
                         {
                              field: 'lead_remark',
                              title: 'Remark'
                         }
                    ],
                    data: tableData
               });

               $('#table').css({
                    'min-width': '2400px',
                    'font-size': '15px',
                    'text-align': 'center'
               });

               $('#table th').css({
                    'background-color': '#ff6c2f',
                    'color': '#fff'
               });
          });
     </script>
</body>

</html>