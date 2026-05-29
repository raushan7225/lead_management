<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");

// Include PhpSpreadsheet library autoloader
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

if (isset($_POST['importProductData'])) {
    $product_for_company = $Company;
    $product_createdby = $username;
    $status = 1;

    // Allowed mime types for Excel files
    $excelMimes = array(
        'text/xls',
        'text/xlsx',
        'application/excel',
        'application/vnd.msexcel',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );

    // Validate whether selected file is an Excel file
    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $excelMimes)) {

        // If the file is uploaded
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $reader = new Xlsx();
            $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $worksheet_arr = $worksheet->toArray();

            // Remove header row
            unset($worksheet_arr[0]);

            // Process each row in the spreadsheet
            foreach ($worksheet_arr as $row) {
                $product_id = $conn->real_escape_string($row[0]);
                $product_name = $conn->real_escape_string($row[1]);
                $product_varient = $conn->real_escape_string($row[2]);
                $product_price = $conn->real_escape_string($row[3]);

                // Check whether the product already exists in the database with the same product_id
                $prevQuery = "SELECT `id` FROM `products` WHERE `product_id` = '$product_id'";
                $prevResult = $conn->query($prevQuery);

                if ($prevResult->num_rows > 0) {
                    // Update existing product data in the database
                    $updateQuery = "UPDATE `products` SET 
                        `product_name` = '$product_name', 
                        `product_varient` = '$product_varient', 
                        `product_price` = '$product_price'
                        WHERE `product_id` = '$product_id'";
                    if (!$conn->query($updateQuery)) {
                        echo "Error updating product data.";
                    }
                } else {
                    // Insert new product data into the database
                    $NewProductQuery = "INSERT INTO `products`(`product_id`, `product_name`, `product_varient`, `product_for_company`, `product_price`, `status`, `product_createdby`) 
                        VALUES ('$product_id', '$product_name', '$product_varient', '$product_for_company', '$product_price', '$status', '$product_createdby')";

                    if (mysqli_query($conn, $NewProductQuery)) {
                        ################# Push activity ################
                        $activity_id = $guid;
                        $activity_content = "Product list imported by admin " . $username . " of " . $Company . " company.";
                        $activity_type = 'Alert';
                        $activity_company = $Company;
                        $activity_branch = "";
                        $activity_on = date('Y-m-d H:i:s');
                        $activity_by = $username;
                        $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
                        $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
                        ################# Push activity ################
                        header("Location: product-list.php");
                    } else {
                        echo "Error inserting product data.";
                    }
                }
            }

            // Set success status for redirect
            $qstring = '?status=succ';
        } else {
            // Error uploading file
            $qstring = '?status=err';
        }
    } else {
        // Invalid file type
        $qstring = '?status=invalid_file';
    }
} else {
    // No file submitted
    $qstring = '?status=no_file';
}

// Redirect to the listing page
header("Location: product-list.php" . $qstring);
exit; // Ensuring no further code is executed after the redirect
