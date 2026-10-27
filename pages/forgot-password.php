<?php
require_once '../includes/config.php';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $error = 'Please enter your email address';
    } else {
        $success = 'If an account exists with this email, password reset instructions have been sent.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - PGi Bank</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-left">
            <div class="auth-brand"><a href="../index.php"><i class="fas fa-university"></i> PGi Bankings & Finance</a></div>
            <div class="auth-welcome">
                <h1>Reset Password</h1>
                <p>Enter your email address and we will send you instructions to reset your password.</p>
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-card">
                <h2>Forgot Password</h2>
                <?php if($error) echo showAlert($error, 'error'); ?>
                <?php if($success) echo showAlert($success, 'success'); ?>
                <form method="POST">
                    <div class="form-group"><label><i class="fas fa-envelope"></i> Email Address</label><input type="email" name="email" placeholder="Enter your email" required></div>
                    <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                </form>
                <p class="auth-footer"><a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
