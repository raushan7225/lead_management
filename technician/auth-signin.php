<?php
session_start();
include("../config/db.php");

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signin'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT `employee_username`, `employee_user_password` FROM `employees` WHERE `employee_username` = ? AND `employee_status` = '1' LIMIT 1");
        
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows === 1) {
                $stmt->bind_result($db_username, $db_password);
                $stmt->fetch();
                
                if (password_verify($password, $db_password)) {
                    $_SESSION["employee_username"] = $db_username;
                    $stmt->close();
                    $conn->close();
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Wrong username or password!";
                }
            } else {
                $error = "Wrong username or password!";
            }
            $stmt->close();
        } else {
            $error = "Database error. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <title> Signin </title>
    <?php include("head.php"); ?>
</head>
<body>
    <div class="d-flex flex-column p-2">
        <div class="d-flex flex-column flex-grow-1">
            <div class="col-xxl-12">
                <div class="row justify-content-center">
                    <div class="col-lg-4 py-lg-4-3">
                        <div class="d-flex flex-column justify-content-center full-height p-2">
                            <div class="auth-logo mb-2 text-center">
                                <a href="index.php" class="logo-dark">
                                    <img src="../assets/images/others/logo-dark.png" height="50" alt="logo dark">
                                </a>
                                <a href="index.php" class="logo-light">
                                    <img src="../assets/images/others/logo-light.png" height="50" alt="logo light">
                                </a>
                            </div>
                            <h2 class="mb-2 fw-bold fs-24 text-center">Welcome Back!</h2>
                            <p class="text-muted text-center mb-3">Login to access your Technician Dashboard</p>
                            
                            <div class="mb-3">
                                <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <?php echo htmlspecialchars($error); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <form action="" method="POST" class="authentication-form" id="loginForm">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="username">
                                            <i class="fas fa-user me-1"></i> Username
                                        </label>
                                        <input type="text" id="username" name="username" class="form-control form-control-lg" 
                                               placeholder="Enter technician username" 
                                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                               required autofocus>
                                        <small class="text-muted">Use your employee username</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="password">
                                            <i class="fas fa-lock me-1"></i> Password
                                        </label>
                                        <div class="input-group">
                                            <input type="password" id="password" name="password" class="form-control form-control-lg" 
                                                   placeholder="Enter your password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" title="Show/Hide password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                                            <label class="form-check-label" for="remember-me">Remember me</label>
                                        </div>
                                        <a href="#" class="text-decoration-none small" onclick="alert('Contact your manager to reset password'); return false;">
                                            Forgot Password?
                                        </a>
                                    </div>
                                    
                                    <div class="mb-3 text-center d-grid">
                                        <button type="submit" name="signin" class="btn btn-primary btn-lg w-100" id="loginBtn">
                                            <i class="fas fa-sign-in-alt me-2"></i> Sign In
                                        </button>
                                    </div>
                                    
                                    <div class="text-center">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Need help? Contact your manager
                                        </small>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/vendor.js"></script>
    <script src="../assets/js/app.js"></script>
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
        
        // Show/hide password checkbox (if exists)
        const showPasswordCheckbox = document.getElementById('show-password');
        if (showPasswordCheckbox) {
            showPasswordCheckbox.addEventListener('change', () => {
                passwordInput.type = showPasswordCheckbox.checked ? 'text' : 'password';
            });
        }
        
        // Loading state on form submit
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        
        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Signing In...';
                loginBtn.disabled = true;
            });
        }
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
<?php
$conn->close();
?>