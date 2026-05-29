<?php
error_reporting(0);

################## LOCAL SERVER #################
$conn = new mysqli("localhost", "root", "", "crm");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}
################## LOCAL SERVER #################
?>