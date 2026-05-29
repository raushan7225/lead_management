<?php
include("../config/db.php");
include("../config/session.php");
include("../config/fornotification.php");

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
     $deleteProductQuery = "DELETE FROM `products` WHERE `id` = '$productId'";
     $checkDelete = mysqli_query($conn, $deleteProductQuery);

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
               $counterValue = 1; // Initialize the counter for So.No.
               if ($counterValue <= $products_list_result->num_rows) {
                    while ($ProductData = $products_list_result->fetch_assoc()) { ?>[
                              gridjs.html(`<?php echo $counterValue++; ?>`), // This increments the counter
                              gridjs.html(`<?php echo $ProductData['product_id']; ?>`),
                              gridjs.html(`<?php echo $ProductData['product_name']; ?>`),
                              gridjs.html(`<?php echo "₹ " . $ProductData['product_price'] . " /- " . $ProductData['product_unit']; ?>`)
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
                         }
                    ],
                    pagination: {
                         limit: 10
                    },
                    search: true,
                    data: leadData,
                    style: {
                         table: {
                              'min-width': '700px',
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

</body>

</html>