<?php
session_start();

// Reset and destroy all session variables
session_unset();
session_destroy();

// Redirect to signin form
echo "<script>window.location.href='index.php';</script>";
exit;
?>