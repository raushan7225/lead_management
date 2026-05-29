<?php
include("../config/db.php");
include("../config/session.php");
include("../config/fornotification.php");

######################## Publish / Unpublish ###################
if (isset($_POST['publish'])) {
     $state_id = $_POST['state_id'];
     $new_status = ($_POST['publish'] == 'on' || $_POST['publish'] == '1') ? 1 : 0;
     $updateStatusQuery = "UPDATE `states` SET `state_status` = '$new_status' WHERE `id` = '$state_id'";
     $Publish = mysqli_query($conn, $updateStatusQuery);
     if ($Publish) {
          echo "<script>window.location.href='state-list.php';</script>";
     } else {
          $error_message = mysqli_error($conn);
          echo "<script>alert('Error: $error_message')</script>";
     }
}
######################## Publish / Unpublish ###################


// ################# Add State ###################
// if (isset($_POST["add_state"])) {
//      $state_name = $_POST['state_name'];
//      $state_status = 1;

//      $checkState = "SELECT * FROM `states` WHERE `state_name` = '$state_name'";
//      $checkStateQuery = mysqli_query($conn, $checkState);

//      if (mysqli_num_rows($checkStateQuery) > 0) {
//           echo "<script>window.location.href='state-list.php';</script>";
//      } else {
//           $add_state = "INSERT INTO `states` (`state_name`, `state_status`) VALUES ('$state_name', '$state_status')";
//           $add_state_query = mysqli_query($conn, $add_state);

//           if ($add_state_query) {
//                echo "<script>window.location.href='state-list.php';</script>";
//           } else {
//                echo "<script>alert('Error adding state!');</script>";
//           }
//      }
// }
// ################# Add State ###################


############### Fetch all states from the database ##############
$state_list = "SELECT * FROM `states` ORDER BY `id` DESC";
$state_list_result = mysqli_query($conn, $state_list);
?>


<!DOCTYPE html>
<html lang="en-US">

<head>
     <title> State Controller </title>
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
               <div class="container-xxl">

                    <div class="row">
                         <div class="col-xl-12">
                              <div class="card">
                                   <div class="d-flex card-header justify-content-between align-items-center">
                                        <h4 class="card-title"> State List </h4>

                                        <!-- ===================== Add State ===================== -->
                                        <div class="modal fade" id="StateAdd" tabindex="-1" aria-labelledby="StateAddTitle" aria-hidden="true">
                                             <div class="modal-dialog modal-dialog-centered">
                                                  <div class="modal-content">
                                                       <form action="#" method="post" enctype="multipart/form-data">
                                                            <div class="modal-header">
                                                                 <h5 class="modal-title" id="StateAddTitle">Add New State</h5>
                                                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                 <!-- State Name -->
                                                                 <div class="col-lg-12">
                                                                      <div class="mb-3">
                                                                           <label for="state-name" class="form-label text-dark">State Name</label>
                                                                           <input type="text" id="state-name" name="state_name" class="form-control" placeholder="State Name" required>
                                                                      </div>
                                                                 </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                 <button type="submit" name="add_state" class="btn btn-primary">Submit</button>
                                                            </div>
                                                       </form>
                                                  </div>
                                             </div>
                                        </div>
                                        <!-- ===========x========= Add State ============x======== -->
                                   </div>

                                   <div class="card-body">
                                        <div id="table-state-search"></div>
                                   </div>
                              </div>
                         </div>
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

     <!-- Grid Js -->
     <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

     <script>
          const stateData = [
               <?php
               $counterValue = 1; // Initialize the counter for So.No.
               if ($counterValue <= mysqli_num_rows($state_list_result)) {
                    while ($mystate = $state_list_result->fetch_assoc()) { ?>[
                              gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter
                              gridjs.html(`<?php echo $mystate['state_name']; ?>`)
                         ],
               <?php }
               } ?>
          ];

          if (document.getElementById("table-state-search")) {
               new gridjs.Grid({
                    columns: [{
                              name: "So.No.",
                              width: "50px",
                         },
                         "State Name"
                    ],
                    data: stateData,
                    pagination: {
                         limit: 10
                    },
                    search: true,
                    style: {
                         table: {
                              'min-width': '600px',
                              'font-size': '15px',
                              'text-align': 'center',
                         },
                         th: {
                              'background-color': '#ff6c2f',
                              'color': '#fff'
                         },
                    }
               }).render(document.getElementById("table-state-search"));
          }
     </script>

</body>

</html>