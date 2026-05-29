<header class="topbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="d-flex align-items-center">

                <!-- Menu Toggle Button -->
                <div class="topbar-item">
                    <button type="button" class="button-toggle-menu me-2">
                        <iconify-icon icon="solar:hamburger-menu-broken" class="fs-24 align-middle"></iconify-icon>
                    </button>
                </div>

                <!-- Website Name and Role -->
                <div class="topbar-item">
                    <a href="index.php">
                        <h5 class="fw-bold topbar-button d-block d-md-none pe-none text-uppercase mb-0 fs-18 text-primary">
                            <iconify-icon icon="solar:home-line-duotone" class="fs-22 align-middle"></iconify-icon>
                        </h5>
                        <h5 class="fw-bold topbar-button d-none d-md-block pe-none text-uppercase mb-0 fs-18 text-primary">
                            <?php
                            if (!empty($fullname)) {
                                echo $fullname . " <small class='text-secondary fs-10'>( " . $Role . " )</small>";
                            } else {
                                echo "Welcome";
                            }
                            ?>
                        </h5>
                    </a>
                </div>
            </div>


            <!-- Topbar Menu -->
            <div class="d-flex align-items-center gap-1">

                <!-- Theme Toggle -->
                <div class="topbar-item">
                    <button type="button" class="topbar-button" id="light-dark-mode">
                        <iconify-icon icon="solar:moon-bold-duotone" class="fs-22 align-middle"></iconify-icon>
                    </button>
                </div>

                <!-- Activities -->
                <div class="topbar-item">
                    <a href="activities.php" class="topbar-button position-relative">
                        <iconify-icon icon="solar:pulse-line-duotone" class="fs-22 align-middle"></iconify-icon>
                        <span class="position-absolute topbar-badge fs-9 translate-middle badge bg-danger rounded-pill">
                            <?php echo ($AllActivitiesCount > 50) ? '50+' : $AllActivitiesCount; ?>
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    </a>
                </div>

                <!-- Notifications -->
                <div class="dropdown topbar-item">
                    <button type="button" class="topbar-button position-relative" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <iconify-icon icon="solar:bell-bing-line-duotone" class="fs-22 align-middle"></iconify-icon>
                        <span class="position-absolute topbar-badge fs-9 translate-middle badge bg-danger rounded-pill">
                            <div id="notificationCount" style="font-weight: bold;"></div>
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    </button>

                    <div class="dropdown-menu py-0 dropdown-lg dropdown-menu-end" aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-2 px-3 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold">Notifications</h6>
                                </div>
                            </div>
                        </div>

                        <div data-simplebar style="min-height: 40px; max-height: 240px; padding: 10px;">
                            <div id="notificationContainer"></div>
                        </div>

                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown topbar-item">
                    <a type="button" class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle" width="32" height="32" src="<?php echo !empty($Profile_pic) ? $Profile_pic : '../assets/images/users/dummy-avatar.jpg'; ?>" alt="User Avatar">
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" class="fw-bold topbar-button text-uppercase align-middle me-1 text-primary d-block d-md-none">
                            <?php
                            if (!empty($fullname)) {
                                echo $fullname . " <small class='text-secondary fs-10'><br>( " . $Role . " )</small>";
                            } else {
                                echo "Welcome";
                            }
                            ?>
                        </a>
                        
                        <div class="dropdown-divider my-0"></div>

                        <a class="dropdown-item" href="profile-update.php?id=<?php echo $uid; ?>">
                            <i class="bx bx-user-circle text-muted fs-18 align-middle me-1"></i><span class="align-middle">Profile</span>
                        </a>
                        <a class="dropdown-item" href="messages.php">
                            <i class="bx bx-message-detail text-muted fs-18 align-middle me-1"></i><span class="align-middle">Messages</span>
                        </a>

                        <!-- <div class="dropdown-divider my-1"></div> -->

                        <a class="dropdown-item text-danger" href="../logout.php">
                            <i class="bx bx-log-out fs-18 align-middle me-1"></i><span class="align-middle">Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>