<?php require_once '../includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - PGi Bank</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo"><i class="fas fa-university"></i><span>PGi Bankings & Finance</span></a>
            <ul class="nav-menu">
                <li><a href="../index.php">Home</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="loans.php">Loans</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php" class="active">Contact</a></li>
            </ul>
            <div class="nav-buttons">
                <?php if(isLoggedIn()): ?>
                    <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
                    <a href="../api/logout.php" class="btn btn-outline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline">Login</a>
                    <a href="register.php" class="btn btn-primary">Open Account</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <section style="padding:120px 20px 60px;background:var(--gray-100);">
        <div class="container">
            <div class="section-header"><h2>Contact Us</h2></div>
            <div style="max-width:500px;margin:0 auto;background:#fff;padding:32px;border-radius:16px;box-shadow:0 4px 15px rgba(0,0,0,0.08);">
                <p><i class="fas fa-phone" style="color:var(--primary);margin-right:10px;"></i> +91 9284327901</p>
                <p><i class="fas fa-envelope" style="color:var(--primary);margin-right:10px;"></i> info@pgibank.com</p>
                <p><i class="fas fa-map-marker-alt" style="color:var(--primary);margin-right:10px;"></i> Kolhapur, Maharashtra</p>
            </div>
        </div>
    </section>
    <footer class="footer"><div class="container"><div class="footer-bottom"><p>&copy; 2024 PGi Bankings & Finance. All rights reserved.</p></div></div></footer>
</body>
</html>
