<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

// Get the status parameter from the URL
$status = isset($_GET['status']) ? $_GET['status'] : '';

############### Publish / Unpublish #################
if (isset($_POST['publish'])) {
     $productId = $_POST['productId'];
     $new_status = ($_POST['publish'] == 'on' || $_POST['publish'] == '1') ? 1 : 0;
     $updateStatusQuery = "UPDATE `products` SET `status` = '$new_status' WHERE `id` = '$productId'";
     $PublishProduct = mysqli_query($conn, $updateStatusQuery);

     if ($PublishProduct) {
          echo "<script>window.location.href='product-list.php?page=$page'</script>";
     } else {
          $error_message = mysqli_error($conn);
          echo "<script>alert('Error: $error_message')</script>";
     }
}
############### Publish / Unpublish #################


############### Products Delete / Edit  #################
if (isset($_POST['delete_product'])) {
     $productId = $_POST['productId'];
     $product_name = $_POST['product_name'];
     $deleteProductQuery = "DELETE FROM `products` WHERE `id` = '$productId'";
     $checkDelete = mysqli_query($conn, $deleteProductQuery);

     ################# Push activity ################
     $activity_id = $guid;
     $activity_content = "Product " . $product_name . " has been removed from product list by manager " . $username . " of " . $Branch . " branch.";
     $activity_type = 'Alert';
     $activity_company = $Company;
     $activity_branch = $Branch;
     $activity_on = date('Y-m-d H:i:s');
     $activity_by = $username;
     $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
     $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
     ################# Push activity ################

     if ($checkDelete) {
          echo "<script>window.location.href='product-list.php'</script>";
     } else {
          echo "Error: " . mysqli_error($conn);
     }
} elseif (isset($_POST['edit_product'])) {
     $productId = $_POST['productId'];
     echo "<script>window.location.href='product-edit.php?id=$productId'</script>";
     exit;
}
############### Products Delete / Edit  #################


############### Products list #####################
$products_list = "SELECT * FROM `products` WHERE `product_for_company` = '$Company' ORDER BY `id` DESC";
$products_list_result = $conn->query($products_list);
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
     <title>Product List</title>
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
                         <!-- ============= Lead List ============= -->
                         <div class="col-xl-12">
                              <div class="card">
                                   <div class="d-flex card-header justify-content-between align-items-center">
                                        <div>
                                             <h4 class="card-title">Product List </h4>
                                        </div>
                                        <div class="dropdown">
                                             <a href="product-add.php" class="btn btn-primary">Add Product</a>
                                        </div>
                                   </div>
                                   <div class="card-body">
                                        <div id="table-lead-search"></div>
                                   </div>
                              </div>
                         </div>
                         <!-- ============= Lead List ============= -->
                    </div>
               </div>
          </div>
          <!-- ==================================================== -->
          <!-- End Page Content -->
          <!-- ==================================================== -->

          <!-- ========== Footer Start ========== -->
          <?php include("../footer.php"); ?>
          <!-- ========== Footer End ========== -->

     </div>
     <!-- END Wrapper -->

     <!-- Vendor Javascript (Require in all Page) -->
     <script src="../assets/js/vendor.js"></script>

     <!-- App Javascript (Require in all Page) -->
     <script src="../assets/js/app.js"></script>

     <!-- Grid Js -->
     <script src="../assets/vendor/gridjs/gridjs.umd.js"></script>

     <script>
          // Branch Table Data
          const leadData = [
               <?php
               $counterValue = 1;
               if ($counterValue <= $products_list_result->num_rows) {
                    while ($ProductData = $products_list_result->fetch_assoc()) { ?>[
                              gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter
                              gridjs.html(`<?php echo $ProductData['product_id']; ?>`),
                              gridjs.html(`<?php echo $ProductData['product_name']; ?>`),
                              gridjs.html(`<?php echo "₹ " . $ProductData['product_price']; ?>`),

                              gridjs.html(`
                                   <form action="#" method="post">
                                        <input type="hidden" name="productId" value="<?php echo $ProductData['id']; ?>">
                                        <input type="hidden" name="publish" value="0">
                                        <div class="form-check form-switch flex-box justify-content-center align-items-center">
                                                <input class="form-check-input" name="publish" type="checkbox" role="switch" id="flexSwitchCheckChecked<?php echo $ProductData['id']; ?>" <?php echo ($ProductData['status'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                        </div>
                                  </form>`),

                              gridjs.html(`
                                   <div class="row gap-2">
                                        <div class="col-5">
                                             <form action="#" method="post" enctype="multipart/form-data">
                                                  <input type="hidden" name="productId" value="<?php echo $ProductData['id']; ?>">
                                                  <button type="submit" name="edit_product" value="<?php echo $ProductData['id']; ?>" class="btn btn-soft-primary btn-sm">
                                                       <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                                  </button>
                                             </form>
                                        </div>

                                        <div class="col-5">
                                             <form action="#" method="post" enctype="multipart/form-data">
                                                  <input type="hidden" name="productId" value="<?php echo $ProductData['id']; ?>">
                                                  <input type="hidden" name="product_name" value="<?php echo $ProductData['product_name']; ?>">
                                                  <button type="submit" name="delete_product" value="<?php echo $ProductData['id']; ?>" class="btn btn-soft-danger btn-sm">
                                                       <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                                  </button>
                                             </form>
                                        </div>
                                   </div>`)
                         ],
               <?php }
               }  ?>
          ];

          // Initialize Grid.js Table
          if (document.getElementById("table-lead-search")) {
               new gridjs.Grid({
                    columns: [{
                              name: "So.No.",
                              width: "50px"
                         },
                         {
                              name: "Product ID",
                              width: "100px"
                         },
                         "Product Name",
                         {
                              name: "Price",
                              width: "150px"
                         },
                         {
                              name: "Status",
                              width: "80px"
                         },
                         {
                              name: "Actions",
                              width: "150px"
                         }
                    ],
                    pagination: {
                         limit: 10
                    },
                    search: true,
                    data: leadData,
                    style: {
                         table: {
                              'min-width': '1000px',
                              'font-size': '15px',
                              'text-align': 'center',
                         },
                         th: {
                              'background-color': '#ff6c2f',
                              'color': '#fff'
                         },
                    }
               }).render(document.getElementById("table-lead-search"));
          }
     </script>

     <script>
          function filterEmployees() {
               const branchSelect = document.getElementById('lead_for_branch');
               const employeeSelect = document.getElementById('lead_assign_to');
               const selectedBranch = branchSelect.value;

               // Get all employee options
               const employeeOptions = employeeSelect.querySelectorAll('option');

               // Loop through each employee option
               employeeOptions.forEach(option => {
                    const branch = option.getAttribute('data-branch');

                    // Show all employees if no branch is selected
                    if (!selectedBranch || branch === selectedBranch) {
                         option.style.display = 'block';
                    } else {
                         option.style.display = 'none';
                    }
               });

               // Reset employee selection
               employeeSelect.value = '';
          }
     </script>


     <script type="text/javascript">
          // Function to display alert based on the status parameter
          window.onload = function() {
               var status = "<?php echo $status; ?>"; // Retrieve the status from PHP

               // Display alerts based on the status
               if (status === "succ") {
                    alert("Data inserted/updated successfully!");
               } else if (status === "err") {
                    alert("Error uploading the file. Please try again.");
               } else if (status === "invalid_file") {
                    alert("Invalid file type. Please upload an Excel file.");
               } else if (status === "no_file") {
                    alert("No file was submitted. Please choose a file to upload.");
               }
          };
     </script>

</body>

</html>