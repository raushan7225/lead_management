<?php
include("../config/db.php");
include("../config/session.php");
include("../config/fornotification.php");

if (isset($_GET['id'])) {
     $id = $_GET['id'];

     // Fetch all leads from the database
     $lead_list = "SELECT * FROM `leads` WHERE `lead_for_company` = '$Company' AND `id` = '$id'";
     $lead_list_result = $conn->query($lead_list);

     // Prepare the data for JSON output
     $leads_data = [];
     if ($lead_list_result->num_rows > 0) {
          while ($lead = $lead_list_result->fetch_assoc()) {
               $leads_data[] = $lead;
          }
     } else {
          echo "<script>alert('No Lead Found!');</script>";
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

          .bootstrap-table .table thead th:nth-child(2) .th-inner {
               width: 300px !important;
          }

          .bootstrap-table .table thead th:nth-child(5) .th-inner {
               width: 200px !important;
          }

          .bootstrap-table .table thead th:nth-child(6) .th-inner {
               width: 300px !important;
          }

          .bootstrap-table .table thead th:nth-child(11) .th-inner {
               width: 300px !important;
          }

          .bootstrap-table .table thead th:nth-child(12) .th-inner {
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

     <div class="wrapper">
          <?php include("topnav.php"); ?>
          <?php include("sidenav.php"); ?>

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

               $('#table').bootstrapTable({
                    pagination: true,
                    search: true,
                    columns: [{
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
          });
     </script>
</body>

</html>