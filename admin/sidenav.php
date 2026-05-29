<div class="main-nav">
    <!-- Sidebar Logo -->
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

    <!-- Menu Toggle Button (sm-hover) -->
    <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
        <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone" class="button-sm-hover-icon"></iconify-icon>
    </button>


    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title">General</li>

            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Dashboard </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="branch-list.php">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:buildings-line-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Branch Controller </span>
                </a>
            </li>

            <li class="nav-item">
               <a class="nav-link" href="state-list.php">
                   <span class="nav-icon">
                       <iconify-icon icon="solar:map-point-wave-line-duotone"></iconify-icon>
                   </span>
                   <span class="nav-text"> State List </span>
               </a>
            </li>

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
                            <a class="sub-nav-link" href="employees-list.php"> Employee List </a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="customers-list.php"> Customer List </a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="references-list.php"> References </a>
                        </li>
                    </ul>
                </div>
            </li>

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
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="product-add.php"> Product Add </a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="product-import.php"> Product Import </a>
                        </li>
                    </ul>
                </div>
            </li>


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

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="lead-add.php"> Add Lead </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="lead-transfer.php"> Transfer Lead </a>
                        </li>
                    </ul>
                </div>
            </li>

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
                            <a class="sub-nav-link" href="complete-lead-report.php"> Lead Report </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#SettingsData" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="SettingsData">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:settings-line-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Settings </span>
                </a>

                <div class="collapse" id="SettingsData">
                    <ul class="nav sub-navbar-nav">

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="permission-control.php"> Permission </a>
                        </li>

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="alert-notes.php"> Updates </a>
                        </li>
                        
                    </ul>
                </div>
            </li>

        </ul>
    </div>
</div>