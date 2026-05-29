<?php
// Fast inclusion (suppress warnings if files might not exist)
@include("config/db.php");
@include("config/session.php");

// Predefined role-to-path mapping (fast hash table lookup)
$role_redirects = [
    'Staff'       => 'staff/index.php',
    'Technician'  => 'technician/index.php',
    'Manager'     => 'manager/index.php',
    'Admin'       => 'admin/index.php',
    'Super Admin' => 'superadmin/index.php'
];

// Immediate redirection without conditions
if (isset($role_redirects[$Role])) {
    header("Location: " . $role_redirects[$Role]);
    exit;
}

// Fallback for unknown roles (optional)
header("Location: auth-signin.php");
exit;

?>