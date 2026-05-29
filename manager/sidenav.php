<?php

// Fetch and process permissions
$FetchPermission = "SELECT `permission_options`, `permission_assign_to` FROM `permissions` WHERE `permission_status` = '1' AND `permission_assign_to` LIKE '%Manager%'";
$FetchPermissionResult = $conn->query($FetchPermission);

// Initialize all permissions as false by default
$States_Control = $User_Control = $Product_Control = $Leads_Control = $Report_Control = false;

if ($FetchPermissionResult->num_rows > 0) {
    while ($PermissionRow = $FetchPermissionResult->fetch_assoc()) {
        $assigned_roles = explode(", ", $PermissionRow["permission_assign_to"]);

        if (in_array('Manager', $assigned_roles)) {
            $permission = strtolower($PermissionRow["permission_options"]);

            // Set the corresponding permission variable to true if found
            if (strpos($permission, 'state') !== false) $States_Control = true;
            if (strpos($permission, 'user') !== false) $User_Control = true;
            if (strpos($permission, 'product') !== false) $Product_Control = true;
            if (strpos($permission, 'lead') !== false) $Leads_Control = true;
            if (strpos($permission, 'report') !== false) $Report_Control = true;
        }
    }
}
?>


<div class="main-nav">
    <!--============= Sidebar Logo =============-->
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
    <!--======x====== Sidebar Logo =======x=====-->


    <!--================== Menu Toggle Button (sm-hover) ==================-->
    <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
        <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone" class="button-sm-hover-icon"></iconify-icon>
    </button>
    <!--========x========= Menu Toggle Button (sm-hover) =========x========-->


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


            <!-- State Control Menu -->
            <?php if ($States_Control): ?>
                <li class="nav-item">
                    <a class="nav-link" href="state-list.php">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:map-point-wave-line-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text">State Lists</span>
                    </a>
                </li>
            <?php endif; ?>


            <!-- User Control Menu -->
            <?php if ($User_Control): ?>
                <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarUsersControl" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUsersControl">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:shield-user-line-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text">Users Control</span>
                    </a>
                    <div class="collapse" id="sidebarUsersControl">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="employees-list.php">Employee List</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="customers-list.php">Customer List</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="references-list.php">References</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>


            <!-- Product Control Menu ---->
            <?php if ($Product_Control): ?>
                <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarProductControl" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProductControl">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:bag-4-line-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text">Product Control</span>
                    </a>
                    <div class="collapse" id="sidebarProductControl">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="product-list.php">Product List</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="product-add.php">Product Add</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="product-import.php">Product Import</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>


            <!-- Leads Control Menu -->
            <?php if ($Leads_Control): ?>
                <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarLeadsControl" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLeadsControl">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:user-id-line-duotone"></iconify-icon>
                        </span>
                        <span class="nav-text">Leads Control</span>
                    </a>
                    <div class="collapse" id="sidebarLeadsControl">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="lead-list.php">Lead List</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="lead-new.php">New Lead</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="lead-add.php">Add Lead</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="lead-transfer.php">Transfer Lead</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>


            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarLeadImport" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLeadImport">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:book-bookmark-line-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Leads Import </span>
                </a>
                <div class="collapse" id="sidebarLeadImport">
                    <ul class="nav sub-navbar-nav">

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="lead-upload.php"> Lead Upload</a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="lead-assign.php"> Lead Assign </a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarManageFollowups" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarManageFollowups">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:calendar-line-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Manage Followups </span>
                </a>

                <div class="collapse" id="sidebarManageFollowups">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="followup-list.php"> All Followups </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="followup_today_visit.php"> Today's Visits </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="followup-todays-list.php"> Today's Followups </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="followup-upcoming-list.php"> Upcoming Followups </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="followup-delayed-list.php"> Delayed Followups </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="followup-place-order.php"> Place Orders </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="followup-order-cancel.php"> Cancelled Followups </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="followup-order-completed.php"> Completed Followups </a>
                        </li>

                    </ul>
                </div>
            </li>


            <!-- Report Control Menu -->
            <?php if ($Report_Control): ?>
                <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#leadreport" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="leadreport">
                        <span class="nav-icon">
                            <iconify-icon icon="solar:notebook-broken"></iconify-icon>
                        </span>
                        <span class="nav-text">Reports</span>
                    </a>
                    <div class="collapse" id="leadreport">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="complete-lead-report.php">Lead Report</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>