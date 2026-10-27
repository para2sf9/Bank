<?php
require_once '../includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        if ($db) {
            $user = $db->users->findOne(['email' => $email, 'status' => 'active']);
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = mongoId($user['_id']);
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                $account = $db->accounts->findOne(['user_id' => $user['_id']]);
                $_SESSION['account_number'] = $account['account_number'] ?? '';

                if ($user['role'] === 'admin') {
                    header('Location: ../admin/dashboard.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            if ($email === 'demo@pgibank.com' && $password === 'demo123') {
                $_SESSION['user_id'] = 'demo001';
                $_SESSION['user_name'] = 'Demo User';
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'customer';
                $_SESSION['account_number'] = 'PGI2024A1B2C3D4';
                header('Location: dashboard.php');
                exit;
            } else if ($email === 'admin@pgibank.com' && $password === 'admin123') {
                $_SESSION['user_id'] = 'admin001';
                $_SESSION['user_name'] = 'Admin User';
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'admin';
                header('Location: ../admin/dashboard.php');
                exit;
            } else {
                $error = 'Invalid email or password. Try demo@pgibank.com / demo123';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PGi Bankings & Finance</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-decoration auth-decoration-1"></div>
    <div class="auth-decoration auth-decoration-2"></div>
    <div class="auth-decoration auth-decoration-3"></div>

    <div class="auth-container">
        <div class="auth-left">
            <div class="auth-brand">
                <a href="../index.php"><i class="fas fa-university"></i> PGi Bankings & Finance</a>
            </div>
            <div class="auth-welcome">
                <h1>Welcome Back!</h1>
                <p>Login to access your account, manage your finances, and explore our banking services.</p>
                <div class="auth-features">
                    <div class="auth-feature"><i class="fas fa-shield-alt"></i> Secure Banking</div>
                    <div class="auth-feature"><i class="fas fa-clock"></i> 24/7 Access</div>
                    <div class="auth-feature"><i class="fas fa-exchange-alt"></i> Easy Transfers</div>
                </div>
            </div>
            <div class="auth-left-footer">
                <p><i class="fas fa-phone-alt"></i> +91 9284327901</p>
                <p><i class="fas fa-map-marker-alt"></i> Kolhapur, Maharashtra</p>
            </div>
        </div>
        <div class="auth-right auth-right-scrollable">
            <div class="auth-card">
                <div class="auth-card-header">
                    <h2>Sign In</h2>
                    <p>Enter your credentials to access your account</p>
                </div>
                <?php if($error) echo showAlert($error, 'error'); ?>
                <form method="POST" action="" id="loginForm" autocomplete="off">
                    <div class="form-group">
                        <label>Email Address</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email" placeholder="name@example.com" required autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" placeholder="Enter your password" required autocomplete="off">
                            <button type="button" class="toggle-password" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-options">
                        <label class="checkbox">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            <span class="label-text">Remember me</span>
                        </label>
                        <a href="forgot-password.php">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="btn-text">Sign In</span>
                        <i class="fas fa-arrow-right btn-icon"></i>
                    </button>
                </form>
                <div class="auth-divider"><span>or continue with demo</span></div>
                <div class="demo-accounts">
                    <div class="demo-row">
                        <i class="fas fa-user-circle"></i>
                        <div>
                            <strong>Customer:</strong> demo@pgibank.com / demo123
                        </div>
                    </div>
                    <div class="demo-row">
                        <i class="fas fa-user-shield"></i>
                        <div>
                            <strong>Admin:</strong> admin@pgibank.com / admin123
                        </div>
                    </div>
                </div>
                <p class="auth-footer">Don't have an account? <a href="register.php">Create Account</a></p>
            </div>
        </div>
    </div>

    <script src="../assets/js/auth.js"></script>
</body>
</html>
