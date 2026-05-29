<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
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


################# Fetch Leads From DB ###################
$ProductList = "SELECT * FROM `products` WHERE `product_for_company`='$Company' AND `status` = '0' ORDER BY `id` DESC";
$ProductList_Result = $conn->query($ProductList);
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Upload Leads</title>
    <?php include("head.php"); ?>
</head>

<body>
    <!-- Loader -->
    <div id="loader-wrapper">
        <div class="loader"></div>
    </div>

    <!-- START Wrapper -->
    <div class="wrapper">

        <div>
            <!-- ========== Topbar Start ========== -->
            <?php include("topnav.php"); ?>
            <!-- ========== Topbar End ========== -->

            <!-- ========== App Menu Start ========== -->
            <?php include("sidenav.php"); ?>
            <!-- ========== App Menu End ========== -->

            <!-- Start right Content here -->
            <div class="page-content">

                <!-- Start Container Fluid -->
                <div class="container-xxl">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Upload Product List</div>
                                </div>
                                <div class="card-body">
                                    <!-- Excel file upload form -->
                                    <div class="col-md-12">
                                        <form class="row" action="importProduct.php" method="post" enctype="multipart/form-data">
                                            <div class="col-lg-10">
                                                <div class="mb-3">
                                                    <label for="fileInput" class="visually-hidden">File</label>
                                                    <input type="file" class="form-control" name="file" id="fileInput" required />
                                                </div>
                                                <small class="text-muted"><strong>Note 1: </strong> <a href="../files/product-sample.xlsx" download>Download Formate Sample.</a> You can import same as sample data.</small><br>
                                                <small class="text-muted"><strong>Note 2: </strong> We are accepting only .xlsx file. </small><br>
                                                <small class="text-muted"><strong>Note 3: </strong> Befoure inserting product, please check your product list. </small><br>
                                                <small class="text-muted"><strong>Note 4: </strong> Don't upload existed product. </small><br>
                                                <small class="text-muted"><strong>Note 5: </strong> The file size must be less than 5MB. </small><br>
                                                <small class="text-muted"><strong>Note 6: </strong> Menstion Company at last in product id "XYZ" if your company name is "XYZ Pvt. Ltd.". </small><br>
                                            </div>
                                            <div class="col-lg-2">
                                                <input type="submit" class="btn btn-primary mb-3 w-100" name="importProductData" value="Import Product">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include("../footer.php"); ?>
        </div>
    </div>
    <!-- Vendor Javascript (Required on all pages) -->
    <script src="../assets/js/vendor.js"></script>

    <!-- App Javascript (Required on all pages) -->
    <script src="../assets/js/app.js"></script>

    <!-- Import XLSX / XLS File Import JS -->
    <script src="../assets/js/vendor/xlxs/xlsx.full.min.js"></script>


</body>

</html>