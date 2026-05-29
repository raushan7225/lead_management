<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

if (isset($_GET['id'])) {
     $id = $_GET['id'];
     ################# Fetch all products #################
     $show_product = "SELECT * FROM `products` WHERE `product_for_company` = '$Company' AND `id` = '$id'";
     $show_product_query = mysqli_query($conn, $show_product);
     ################# Fetch all products #################
?>

     <!DOCTYPE html>
     <html lang="en">

     <head>
          <title> Product Edit </title>
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

                         <div class="col-xl-12 col-lg-12 ">
                              <form action="#" method="POST" enctype="multipart/form-data" onsubmit="return submitForm()">
                                   <div class="col-xl-12 col-lg-12 ">
                                        <div class="card">
                                             <div class="card-header">
                                                  <h4 class="card-title"> Product Information </h4>
                                             </div>
                                             <div class="card-body">
                                                  <?php if (mysqli_num_rows($show_product_query) > 0) {
                                                       while ($productDatas = $show_product_query->fetch_assoc()) { ?>
                                                            <div class="row">
                                                                 <!-- ============= Product Name ============= -->
                                                                 <div class="col-lg-12">
                                                                      <div class="mb-3">
                                                                           <label for="product-name" class="form-label">Product Name <small class="text-success">(Required)</small></label>
                                                                           <input type="text" id="product-name" name="product_name" class="form-control" placeholder="Product's Full Name" value="<?php echo $productDatas['product_name']; ?>" required>
                                                                      </div>
                                                                 </div>
                                                                 <!-- =======x===== Product Name =======x===== -->


                                                                 <!-- =============== Product ID =============== -->
                                                                 <div class="col-lg-4">
                                                                      <div class="mb-3">
                                                                           <label for="product_id" class="form-label"> Product Unique ID <small class="text-success">(Required)</small></label>
                                                                           <input type="text" id="product_id" name="product_id" class="form-control" placeholder="Unique ID of Product" value="<?php echo $productDatas['product_id']; ?>" required>
                                                                           <small><span class="text-danger">Note:</span> Ex. Product Unique ID = "<strong class="text-success">P</strong>aper <strong class="text-success">P</strong>late <strong class="text-success">M</strong>achine", Varient = "<strong class="text-success">D</strong>ouble Die", Date = "12/01/<strong class="text-success">2025</strong>" and Company = "<strong class="text-success">XYZ</strong> Pvt. Ltd." then Your Product ID = "<strong class="text-success">PPMD2025XYZ</strong>".</small>
                                                                      </div>
                                                                 </div>
                                                                 <!-- =======x======== Product ID ========x======= -->


                                                                 <!-- =============== Product Varient =============== -->
                                                                 <div class="col-lg-4">
                                                                      <div class="mb-3">
                                                                           <label for="product_varient" class="form-label"> Varient </label>
                                                                           <input type="text" id="product_varient" name="product_varient" class="form-control" placeholder="Product Varient (Single Die / Double Die etc.)" value="<?php echo $productDatas['product_varient']; ?>">
                                                                           <small><span class="text-danger">Note:</span> Ex. Varient = Single, Double, Manual, etc...</small>
                                                                      </div>
                                                                 </div>
                                                                 <!-- =======x======== Product Varient ========x======= -->


                                                                 <!-- ============= Product Price ============= -->
                                                                 <div class="col-lg-4">
                                                                      <label for="Price" class="form-label"> Price <small class="text-success">(Required)</small></label>
                                                                      <div class="input-group mb-3">
                                                                           <span class="input-group-text fs-20"><i class='bx bx-rupee'></i></span>
                                                                           <input type="text" id="Price" name="product_price" class="form-control" placeholder="Price" value="<?php echo $productDatas['product_price']; ?>" required>
                                                                      </div>
                                                                 </div>
                                                                 <!-- ============= Product Price ============= -->


                                                                 <!-- ================= Product description =================== -->
                                                                 <div class="col-lg-12">
                                                                      <div class="mb-3">
                                                                           <label for="short-description" class="form-label">Product Short Description</label>
                                                                           <div id="short-description-editor" class="editor" style="height: 100px;">
                                                                                <?php echo isset($productDatas['product_short_description']) ? $productDatas['product_short_description'] : ''; ?>
                                                                           </div>
                                                                           <textarea name="product_short_description" id="short-description-content" style="display: none;"></textarea>
                                                                      </div>
                                                                 </div>
                                                                 <!-- ================= Product description =================== -->
                                                            </div>
                                                  <?php }
                                                  } ?>
                                             </div>
                                        </div>
                                   </div>

                                   <div class="p-3 bg-light mb-3 rounded">
                                        <div class="row justify-content-end g-2">
                                             <div class="col-lg-2">
                                                  <button type="submit" name="update_product" class="btn btn-outline-secondary w-100">Update Product</button>
                                             </div>
                                             <div class="col-lg-2">
                                                  <a href="product-list.php" class="btn btn-primary w-100">Cancel</a>
                                             </div>
                                        </div>
                                   </div>
                              </form>
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

          <!-- Vendor Javascript (Require in all Page) -->
          <script src="../assets/js/vendor.js"></script>

          <!-- App Javascript (Require in all Page) -->
          <script src="../assets/js/app.js"></script>

          <!-- Quill Editor js -->
          <script>
               // Short Description Editor
               var shortDescriptionQuill = new Quill('#short-description-editor', {
                    theme: 'snow',
                    modules: {
                         toolbar: [
                              [{
                                   'header': [false, 1, 2, 3, 4, 5, 6]
                              }, 'blockquote', 'code-block'],
                              ['bold', 'italic', 'underline', 'strike'],
                              [{
                                   'color': []
                              }, {
                                   'background': []
                              }],
                              [{
                                   'script': 'super'
                              }, {
                                   'script': 'sub'
                              }],
                              [{
                                   'list': 'ordered'
                              }, {
                                   'list': 'bullet'
                              }],
                              ['direction', {
                                   'align': []
                              }],
                              ['link'],
                              ['clean']
                         ]
                    }
               });

               function submitForm() {
                    var shortDescriptionContent = shortDescriptionQuill.root.innerHTML;
                    document.getElementById('short-description-content').value = shortDescriptionContent;
                    return true;
               }
          </script>

     </body>

     </html>

<?php
     ################# Update Products #################
     if (isset($_POST['update_product'])) {
          // Collect basic product data
          $product_id = $_POST['product_id'];
          $product_name = $_POST['product_name'];
          $product_varient = $_POST['product_varient'];
          $product_price = $_POST['product_price'];
          $product_short_description = $_POST['product_short_description'];
          $product_updatedby = $username;

          ################## Start Update Product Data in Database #################
          $UpdateProduct  = "UPDATE `products` SET 
               `product_id`='$product_id',
               `product_name`='$product_name',
               `product_varient`='$product_varient',
               `product_short_description`='$product_short_description',
               `product_price`='$product_price',
               `product_updatedby`='$product_updatedby' WHERE `id` = '$id'";

          $checkProduct = mysqli_query($conn, $UpdateProduct);

          if ($checkProduct) {
               ################# Push activity ################
               $activity_id = $guid;
               $activity_content = "Product " . $product_name . " details has been updated by manager " . $username . " of " . $Branch . " branch.";
               $activity_type = 'Alert';
               $activity_company = $Company;
               $activity_branch = $Branch;
               $activity_on = date('Y-m-d H:i:s');
               $activity_by = $username;
               $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
               $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
               ################# Push activity ################
               echo "<script>window.location.href='product-list.php'</script>";
          } else {
               echo "<script>alert('Error updating product: " . mysqli_error($conn) . "');</script>";
          }
          ################## End Update Product Data in Database #################
     }
     ##################### End Handle product form submission ################# 
} else {
     echo "<script>window.location.href='product-list.php'</script>";
     exit;
}

$conn->close();
?>