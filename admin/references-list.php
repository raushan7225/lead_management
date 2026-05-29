<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

$error = "";  // Initialize the error user
################# References Publish / Unpublish ###################
if (isset($_POST['publish'])) {
     $reference_id = $_POST['reference_id'];
     $new_status = ($_POST['publish'] == 'on' || $_POST['publish'] == '1') ? 1 : 0;
     $updateStatusQuery = "UPDATE `references` SET `reference_status` = '$new_status' WHERE `reference_for_company`='$Company' AND `id` = '$reference_id'";
     $Publish = mysqli_query($conn, $updateStatusQuery);
     if ($Publish) {
          echo "<script>window.location.href='references-list.php';</script>";
     } else {
          $error = mysqli_error($conn);
     }
}
################# References Publish / Unpublish ###################


############### Delete Reference #################
if (isset($_POST['delete_reference'])) {
     $reference_id = $_POST['reference_id'];
     $reference_name = $_POST['reference_name'];
     $deletereferenceQuery = "DELETE FROM `references` WHERE `reference_for_company`='$Company' AND `id` = '$reference_id'";
     $checkDelete = mysqli_query($conn, $deletereferenceQuery);

     if ($checkDelete) {
          ################# Push activity ################
          $activity_id = $guid;
          $activity_content = "Referrence " . $reference_name . " has been removed by admin " . $username . " of " . $Company . " company.";
          $activity_type = 'Alert';
          $activity_company = $Company;
          $activity_branch = "";
          $activity_on = date('Y-m-d H:i:s');
          $activity_by = $username;
          $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
          $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
          ################# Push activity ################

          echo "<script>window.location.href='references-list.php'</script>";
     } else {
          $error = "Something went wrong!";
     }
} elseif (isset($_POST['edit_reference'])) {
     $reference_id = $_POST['reference_id'];
     echo "<script>window.location.href='reference-edit.php?id=$reference_id'</script>";
     exit;
}
############### Delete Reference #################


######### Fetch all references from the database #########
$reference_list = "SELECT * FROM `references` WHERE `reference_for_company`='$Company' AND `reference_status`='1'";
$reference_list_result = mysqli_query($conn, $reference_list);

?>
<!DOCTYPE html>
<html lang="en-US">

<head>
     <title> Reference Controller </title>
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
                    <!-- Show error message here -->
                    <?php if ($error): ?>
                         <div class="alert alert-danger alert-dismissible fade show" role="alert">
                              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              <?php $error; ?>
                         </div>
                    <?php endif; ?>

                    <div class="row">
                         <div class="col-xl-12">
                              <div class="card">
                                   <div class="d-flex card-header justify-content-between align-items-center">
                                        <h4 class="card-title"> Reference List </h4>

                                        <div class="dropdown">
                                             <a href="reference-add.php" class="btn btn-primary">Add Reference</a>
                                        </div>
                                   </div>

                                   <div class="card-body">
                                        <div id="table-Reference-search"></div>
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
          const ReferenceData = [
               <?php
               $counterValue = 1;
               if ($counterValue <= mysqli_num_rows($reference_list_result)) {
                    while ($reference = $reference_list_result->fetch_assoc()) {
                         $id =  $reference['id'];
                         $reference_name = $reference['reference_name']; ?>[
                              gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter
                              gridjs.html(`<?php echo $reference['reference_id']; ?>`),
                              gridjs.html(`<?php echo $reference['reference_name']; ?>`),
                              gridjs.html(`<?php echo $reference['reference_contact']; ?>`),
                              gridjs.html(`
                                   <form action="#" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="reference_id" value="<?php echo $reference['id']; ?>">
                                        <input type="hidden" name="publish" value="0">
                                        <div class="form-check form-switch flex-box justify-content-center align-items-center">
                                             <input class="form-check-input" name="publish" type="checkbox" role="switch" id="flexSwitchCheckChecked<?php echo $reference['id']; ?>" <?php echo ($reference['reference_status'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                        </div>
                                   </form>`),
                              gridjs.html(`
                                   <div class="row gap-2">
                                        <div class="col-5">
                                        <form action="#" method="post" enctype="multipart/form-data">
                                             <input type="hidden" name="reference_id" value="<?php echo $reference['id']; ?>">
                                                  <button type="submit" name="edit_reference" value="<?php echo $reference['id']; ?>" class="btn btn-soft-primary btn-sm">
                                                       <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                                  </button>
                                             </form>
                                        </div>
                                        <div class="col-5">
                                             <form action="#" method="post" enctype="multipart/form-data">
                                                  <input type="hidden" name="reference_id" value="<?php echo $reference['id']; ?>">
                                                  <input type="hidden" name="reference_name" value="<?php echo $reference['reference_name']; ?>">
                                                  <button type="submit" name="delete_reference" value="<?php echo $reference['id']; ?>" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>
                                             </form>
                                        </div>    
                                   </div>`)
                         ],
               <?php }
               } ?>
          ];

          if (document.getElementById("table-Reference-search")) {
               new gridjs.Grid({
                    columns: [{
                              name: "So.No.",
                              width: "50px"
                         },
                         {
                              name: "Reference ID",
                              width: "100px"
                         },
                         "References",
                         {
                              name: "Contact",
                              width: "150px",
                         },
                         {
                              name: "Status",
                              width: "80px",
                         },
                         {
                              name: "Actions",
                              width: "140px",
                         },
                    ],
                    data: ReferenceData,
                    pagination: {
                         limit: 10
                    },
                    search: true,
                    style: {
                         table: {
                              'min-width': '800px',
                              'font-size': '15px',
                              'text-align': 'center',
                         },
                         th: {
                              'background-color': '#ff6c2f',
                              'color': '#fff'
                         },
                    }
               }).render(document.getElementById("table-Reference-search"));
          }
     </script>

</body>

</html>

<?php
$conn->close();
?>