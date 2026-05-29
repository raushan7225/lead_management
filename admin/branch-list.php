<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

############### Publish / Unpublish #################
if (isset($_POST['publish'])) {
     $BranchID = $_POST['BranchID'];
     $new_status = ($_POST['publish'] == 'on' || $_POST['publish'] == '1') ? 1 : 0;
     $updateStatusQuery = "UPDATE `branches` SET `branch_status` = '$new_status' WHERE `id` = '$BranchID'";
     $PublishBranch = mysqli_query($conn, $updateStatusQuery);

     if ($PublishBranch) {
          echo "<script>window.location.href='branch-list.php';</script>";
     } else {
          $error_message = mysqli_error($conn);
          echo "<script>alert('Error: $error_message')</script>";
     }
}
############### Publish / Unpublish #################



############### Branchs Delete / Edit #################
if (isset($_POST['delete_branch'])) {
     $BranchID = $_POST['BranchID'];
     $branch_name = $_POST['branch_name'];

     $deletebranchQuery = "DELETE FROM `branches` WHERE `id` = '$BranchID'";
     $checkDelete = mysqli_query($conn, $deletebranchQuery);

     if ($checkDelete) {

          ################# Push activity ################
          $activity_id = $guid;
          $activity_content = "Branch " . $branch_name . " has been removed by admin " . $username . " from " . $Company . " company.";
          $activity_type = 'Alert';
          $activity_company = $Company;
          $activity_branch = "";
          $activity_on = date('Y-m-d H:i:s');
          $activity_by = $username;
          $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
          $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
          ################# Push activity ################

          echo "<script>window.location.href='branch-list.php'</script>";
     } else {
          echo "Error: " . mysqli_error($conn);
     }
} elseif (isset($_POST['edit_branch'])) {
     $BranchID = $_POST['BranchID'];
     echo "<script>window.location.href='branch-edit.php?id=$BranchID'</script>";
     exit;
}
############### Branchs Delete / Edit #################



######### Fetch all city from the database #########
$BranchList = "SELECT * FROM `branches` WHERE `branch_of_company` = '$Company' AND `branch_status` = '1' ORDER BY `id` DESC";
$BranchListResult = mysqli_query($conn, $BranchList);

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
               <div class="container-xxl">
                    <div class="row">
                         <div class="col-xl-12">
                              <div class="card">
                                   <div class="d-flex card-header justify-content-between align-items-center">
                                        <div>
                                             <h4 class="card-title">Branch List</h4>
                                        </div>
                                        <div class="dropdown">
                                             <a href="branch-add.php" class="btn btn-primary">Branch Add</a>
                                        </div>
                                   </div>
                                   <div class="card-body">
                                        <div id="table-branch-search"></div>
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
          const BranchsData = [

               <?php
               $counterValue = 1;
               if ($counterValue <= mysqli_num_rows($BranchListResult)) {
                    while ($Branch = mysqli_fetch_assoc($BranchListResult)) { ?>[
                              gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter
                              gridjs.html(`<?php echo $Branch['branch_name']; ?>`),
                              gridjs.html(`<?php echo $Branch['branch_contact']; ?>`),
                              gridjs.html(`<?php echo $Branch['branch_email']; ?>`),
                              gridjs.html(`<?php echo $Branch['branch_address']; ?>`),

                              gridjs.html(`
                                   <form action="#" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="BranchID" value="<?php echo $Branch['id']; ?>">
                                        <input type="hidden" name="publish" value="0">
                                        <div class="form-check form-switch flex-box justify-content-center align-items-center">
                                             <input class="form-check-input" name="publish" type="checkbox" role="switch" id="flexSwitchCheckChecked<?php echo $Branch['id']; ?>" <?php echo ($Branch['branch_status'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                        </div>
                                   </form>`),

                              gridjs.html(`
                                   <div class="row gap-2">
                                        <div class="col-lg-5">
                                        <form action="#" method="post" enctype="multipart/form-data">
                                             <input type="hidden" name="BranchID" value="<?php echo $myid = $Branch['id']; ?>">
                                                  <button type="submit" name="edit_branch" value="<?php echo $Branch['id']; ?>" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#CityUpdate">
                                                       <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                                  </button>
                                             </form>
                                        </div>

                                        <div class="col-lg-5">
                                        <form action="#" method="post" enctype="multipart/form-data">
                                             <input type="hidden" name="BranchID" value="<?php echo $myid = $Branch['id']; ?>">
                                             <input type="hidden" name="branch_name" value="<?php echo $myid = $Branch['branch_name']; ?>">
                                                  <button type="submit" name="delete_branch" value="<?php echo $Branch['id']; ?>" class="btn btn-soft-danger btn-sm" data-bs-toggle="modal" data-bs-target="#CityUpdate">
                                                       <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                                  </button>
                                             </form>
                                        </div>
                                   </div>`)
                         ],
               <?php }
               } ?>
          ];

          if (document.getElementById("table-branch-search")) {
               new gridjs.Grid({
                    columns: [{
                              name: "So.No.",
                              width: "50px"
                         },
                         {
                              name: "Branch Name",
                              width: "200px"
                         },
                         {
                              name: "Branch Contact",
                              width: "100px"
                         },
                         {
                              name: "Branch Email",
                              width: "100px"
                         },
                         {
                              name: "Branch Address",
                              width: "300px"
                         },
                         {
                              name: "Status",
                              width: "50px"
                         },
                         {
                              name: "Actions",
                              width: "100px"
                         }
                    ],
                    data: BranchsData,
                    pagination: {
                         limit: 10
                    },
                    search: true,
                    style: {
                         table: {
                              'min-width': '1600px',
                              'font-size': '15px',
                              'text-align': 'center',
                         },
                         th: {
                              'background-color': '#ff6c2f',
                              'color': '#fff'
                         },
                    }
               }).render(document.getElementById("table-branch-search"));
          }
     </script>

</body>

</html>