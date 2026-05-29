<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

################### Show Branches ######################
$Companies_list = "SELECT * FROM `companies` WHERE `company_status` = 1";
$CompaniesQuery = mysqli_query($conn, $Companies_list);
$company_name = [];
if (mysqli_num_rows($CompaniesQuery) > 0) {
    while ($companylist = mysqli_fetch_array($CompaniesQuery)) {
        $company_name[] = $companylist;
    }
}
################### Show Branches ######################

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title> Create Product </title>
    <?php include("head.php"); ?>
</head>

<body>
    <!-- Loader -->
    <div id="loader-wrapper">
        <div class="loader"></div>
    </div>

    <!-- START Wrapper -->
    <div class="wrapper">

        <!-- =========== Topbar Start ============= -->
        <?php include("topnav.php"); ?>
        <!-- ========== End Topbar Start ========== -->

        <!-- =========== App Menu Start =========== -->
        <?php include("sidenav.php"); ?>
        <!-- =========== App Menu End ============= -->

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
                                    <div class="row">
                                        <!-- ============= Product Name ============= -->
                                        <div class="col-lg-8">
                                            <div class="mb-3">
                                                <label for="product-name" class="form-label">Product Name <small class="text-success">(Required)</small></label>
                                                <input type="text" id="product-name" name="product_name" class="form-control" placeholder="Product's Full Name" required>
                                            </div>
                                        </div>
                                        <!-- =======x===== Product Name =======x===== -->


                                        <!-- ============= company ============= -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="product_for_company" class="form-label">Company <small class="text-success">(Required)</small></label>
                                                <select class="form-control" id="product_for_company" name="product_for_company" data-choices required>
                                                    <option value="">Select Company</option>
                                                    <?php foreach ($company_name as $mycompany) { ?>
                                                        <option value="<?php echo $mycompany['company_name']; ?>">
                                                            <?php echo $mycompany['company_name']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- ============= company ============= -->


                                        <!-- =============== Product ID =============== -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="product_id" class="form-label"> Product Unique ID <small class="text-success">(Required)</small></label>
                                                <input type="text" id="product_id" name="product_id" class="form-control" placeholder="Unique ID of Product" required>
                                                <small><span class="text-danger">Note:</span> Ex. Name = "<strong class="text-success">P</strong>aper <strong class="text-success">P</strong>late <strong class="text-success">M</strong>achine", Varient = "<strong class="text-success">D</strong>ouble Die" and Date = "12/<strong class="text-success">01</strong>/<strong class="text-success">2025</strong>" then Your Product ID = "PPMD012025"</small>
                                            </div>
                                        </div>
                                        <!-- =======x======== Product ID ========x======= -->


                                        <!-- =============== Product Varient =============== -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="product_varient" class="form-label"> Varient </label>
                                                <input type="text" id="product_varient" name="product_varient" class="form-control" placeholder="Product Varient (Single Die / Double Die etc.)">
                                                <small><span class="text-danger">Note:</span> Ex. Varient = Single, Double, Manual, etc...</small>
                                            </div>
                                        </div>
                                        <!-- =======x======== Product Varient ========x======= -->


                                        <!-- ============= Product Price ============= -->
                                        <div class="col-lg-4">
                                            <label for="Price" class="form-label"> Price <small class="text-success">(Required)</small></label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text fs-20"><i class='bx bx-rupee'></i></span>
                                                <input type="text" id="Price" name="product_price" class="form-control" placeholder="Price" required>
                                            </div>
                                        </div>
                                        <!-- ============= Product Price ============= -->


                                        <!-- ================= Product description =================== -->
                                        <div class="col-lg-12">
                                            <label for="short-description" class="form-label"> Description</label>
                                            <div id="short-description-editor" class="editor" style="height: 100px;">
                                            </div>
                                            <textarea name="product_short_description" id="short-description-content" style="display: none;"></textarea>
                                        </div>
                                        <!-- ================= Product description =================== -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-light mb-3 rounded">
                            <div class="row justify-content-end g-2">
                                <div class="col-lg-2">
                                    <button type="submit" name="new_product" class="btn btn-outline-secondary w-100">Create Product</button>
                                </div>
                                <div class="col-lg-2">
                                    <a href="product-list.php" class="btn btn-primary w-100">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
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
################## Start Handle product form submission ################# 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new_product'])) {
        // Collecting input data
        $product_id = trim($_POST['product_id']);
        $$temp_product_pic = "logo-sm.png";
        $product_name = $_POST['product_name'];
        $product_varient = $_POST['product_varient'];
        $product_price = trim($_POST['product_price']);
        $product_short_description = $_POST['product_short_description'];
        $product_for_company = $_POST['product_for_company'];
        $product_status = 1;
        $product_createdby = $username;

        ###################### Add Product into Database ############################
        $checkProductQuery = "SELECT * FROM `products` WHERE `product_id` = '$product_id' OR `product_name` = '$product_name'";
        $checkProduct = mysqli_query($conn, $checkProductQuery);

        if (mysqli_num_rows($checkProduct) > 0) {
            while ($result = mysqli_fetch_assoc($checkProduct)) {
                if ($result['product_id'] == $product_id) {
                    echo "<script>alert('Product ID already exists. Please try another.');</script>";
                    exit;
                } elseif ($result['product_name'] == $product_name) {
                    echo "<script>alert('Product Name already exists. Please try another.');</script>";
                    exit;
                }
            }
        } else {
            $NewProductQuery = "INSERT INTO `products`(`product_id`, `product_name`, `product_varient`, `product_short_description`, `product_price`, `status`, `product_for_company`, `product_createdby`) 
                VALUES ('$product_id', '$product_name', '$product_varient', '$product_short_description', '$product_price', '$product_status', '$product_for_company', '$product_createdby')";

            if (mysqli_query($conn, $NewProductQuery)) {
                ################# Push activity ################
                $activity_id = $guid;
                $activity_content = "The new product " . $product_name . " has been added by " . $Role . ".";
                $activity_type = 'Alert';
                $activity_company = "";
                $activity_branch = "";
                $activity_on = date('Y-m-d H:i:s');
                $activity_by = $username;
                $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
                $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
                ################# Push activity ################
                echo "<script>window.location.href='product-list.php'</script>";
            } else {
                echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
            }
        }
        ###################### End Add Product into Database ########################

    } else {
        echo "<script>alert('Fill all the required fields.');</script>";
    }
}
$conn->close();
##################### End Handle product form submission ################# 
?>