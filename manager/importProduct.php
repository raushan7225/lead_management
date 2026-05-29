<?php
include("../config/db.php");
include("../config/session.php");
include("../config/activities.php");
include("../config/fornotification.php");


require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

if (isset($_POST['importProductData']) && !empty($_FILES['file']['name'])) {
    $fileType = $_FILES['file']['type'];
    $allowedTypes = [
        'text/xls',
        'text/xlsx',
        'application/excel',
        'application/vnd.msexcel',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (in_array($fileType, $allowedTypes)) {
        $tmpFile = $_FILES['file']['tmp_name'];

        if (is_uploaded_file($tmpFile)) {
            try {
                $reader = new Xlsx();
                $spreadsheet = $reader->load($tmpFile);
                $rows = $spreadsheet->getActiveSheet()->toArray();
                unset($rows[0]); // remove header

                foreach ($rows as $row) {
                    $product_id = $conn->real_escape_string($row[0] ?? '');
                    if (empty($product_id)) continue;

                    $product_name = $conn->real_escape_string($row[1] ?? '');
                    $product_varient = $conn->real_escape_string($row[2] ?? '');
                    $product_price = $conn->real_escape_string($row[3] ?? '');

                    $checkExist = mysqli_query($conn, "SELECT `id` FROM `products` WHERE `product_id` = '$product_id'");

                    if (mysqli_num_rows($checkExist)) {
                        $updateQuery = "UPDATE `products` SET 
                            `product_name` = '$product_name',
                            `product_varient` = '$product_varient',
                            `product_price` = '$product_price'
                            WHERE `product_id` = '$product_id'";
                        mysqli_query($conn, $updateQuery);
                    } else {
                        $insertQuery = "INSERT INTO `products` (
                            `product_id`, `product_name`, `product_varient`, 
                            `product_for_company`, `product_price`, `status`, `product_createdby`
                        ) VALUES (
                            '$product_id', '$product_name', '$product_varient',
                            '$Company', '$product_price', 1, '$username'
                        )";
                        mysqli_query($conn, $insertQuery);

                        ################# Push activity ################
                        $activity_id = $guid;
                        $activity_content = "Product list imported by manager " . $username . " of " . $Branch . " branch.";
                        $activity_type = 'Alert';
                        $activity_company = $Company;
                        $activity_branch = $Branch;
                        $activity_on = date('Y-m-d H:i:s');
                        $activity_by = $username;
                        $NewActivityAdd = "INSERT INTO `activities`(`activity_id`, `activity_content`, `activity_company`, `activity_branch`, `activity_type`, `activity_on`, `activity_by`) VALUES ('$activity_id','$activity_content','$activity_company','$activity_branch','$activity_type','$activity_on','$activity_by')";
                        $ApplyActivityQuery = mysqli_query($conn, $NewActivityAdd);
                        ################# Push activity ################
                    }
                }

                echo "<script>window.location.href = 'product-list.php?status=succ';</script>";
            } catch (Exception $e) {
                echo "Error reading file: " . $e->getMessage();
            }
        } else {
            echo "<script>window.location.href = 'product-list.php?status=err';</script>";
        }
    } else {
        echo "<script>window.location.href = 'product-list.php?status=invalid_file';</script>";
    }
    exit;
} else {
    echo "<script>window.location.href = 'product-list.php?status=no_file';</script>";
    exit;
}
