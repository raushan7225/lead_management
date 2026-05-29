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

                <!-- Menu Toggle Button -->
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

            <div class="d-flex align-items-center gap-1">

                                <!-- Theme Toggle -->
                <div class="topbar-item">
                    <button type="button" class="topbar-button" id="light-dark-mode">
                        <iconify-icon icon="solar:moon-bold-duotone" class="fs-22 align-middle"></iconify-icon>
                    </button>
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