<?php

##################### Fetch Permissions for Technician #####################
$FetchPermission = "SELECT `permission_options`, `permission_assign_to` 
                    FROM `permissions` 
                    WHERE `permission_status` = '1'
                    AND `permission_assign_to` LIKE '%Technician%'";
$FetchPermissionResult = $conn->query($FetchPermission);

// Initialize all permissions as false by default
$States_Control = $User_Control = $Product_Control = $Leads_Control = $Report_Control = false;

if ($FetchPermissionResult->num_rows > 0) {
    while ($PermissionRow = $FetchPermissionResult->fetch_assoc()) {
        $assigned_roles = explode(", ", $PermissionRow["permission_assign_to"]);

        if (in_array('Technician', $assigned_roles)) {
            $permission = strtolower($PermissionRow["permission_options"]);

            // Set permission variables based on what's found
            if (strpos($permission, 'state') !== false) $States_Control = true;
            if (strpos($permission, 'user') !== false) $User_Control = true;
            if (strpos($permission, 'product') !== false) $Product_Control = true;
            if (strpos($permission, 'lead') !== false) $Leads_Control = true;
            if (strpos($permission, 'report') !== false) $Report_Control = true;
        }
    }
}
####################### Fetch Permissions for Technician #####################
?>

<div class="main-nav">
    <!--=================== Sidebar Logo ===================-->
    <div class="logo-box">
        <a href="index.php" class="logo-dark">
            <img src="../assets/images/others/logo-sm.png" class="logo-sm" style="height: 50px;" alt="logo sm">
            <img src="../assets/images/others/logo-light.png" class="logo-lg" style="height: 50px;" alt="logo dark">
        </a>

        <a href="index.php" class="logo-light">
            <img src="../assets/images/others/logo-sm.png" class="logo-sm" style="height: 50px;" alt="logo sm">
            <img src="../assets/images/others/logo-dark.png" class="logo-lg" style="height: 50px;" alt="logo light">
        </a>
    </div>
    <!--========x========== Sidebar Logo ==========x========-->


    <!--============== Menu Toggle Button (sm-hover) ==============-->
    <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
        <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone" class="button-sm-hover-icon"></iconify-icon>
    </button>
    <!--======x======= Menu Toggle Button (sm-hover) =======x======-->


    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Dashboard </span>
                </a>
            </li>

            <!-- State Control (rarely for technicians) -->
            <?php if ($States_Control): ?>
                <li class="nav-item">
                    <a class="nav-link" href="state-list.php">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:map-point-wave-line-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> State Lists </span>
                    </a>
                </li>
            <?php endif; ?>


            <!-- User Control (limited) -->
            <?php if ($User_Control): ?>
                <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarUsersControl" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUsersControl">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:shield-user-line-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> Users Control </span>
                    </a>
                    <div class="collapse" id="sidebarUsersControl">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="customers-list.php"> Customer List </a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>


            <!-- Product Control (view only) -->
            <?php if ($Product_Control): ?>
                <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarProductControl" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProductControl">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:bag-4-line-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> Product Control </span>
                    </a>

                    <div class="collapse" id="sidebarProductControl">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="product-list.php"> Product List </a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>


            <!-- Leads Control (limited) -->
            <?php if ($Leads_Control): ?>
                <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarLeadsControl" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLeadsControl">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:user-id-line-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text"> Leads Control </span>
                    </a>
                    <div class="collapse" id="sidebarLeadsControl">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="lead-list.php"> Lead List </a>
                            </li>

                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="lead-new.php"> New Lead </a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>


            <!-- Installations -->
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarManageFollowups" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarManageFollowups">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:sledgehammer-line-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Installations </span>
                </a>

                <div class="collapse" id="sidebarManageFollowups">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="installations.php"> All Installations </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="todays-installations.php"> Today's Installations </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="upcomming-installations.php"> Upcomming Installations </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="missed-installations.php"> Missed Installations </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="completed-installations.php"> Completed Installations </a>
                        </li>

                    </ul>
                </div>
            </li>

            <!-- Reports (technician performance) -->
            <?php if ($Report_Control): ?>
                <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#leadreport" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="leadreport">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:notebook-broken"></iconify-icon>
                        </span>
                        <span class="nav-text"> Reports </span>
                    </a>

                    <div class="collapse" id="leadreport">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="customer-lead-report.php"> Lead Report </a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>