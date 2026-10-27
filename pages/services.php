<?php require_once '../includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Services - PGi Bank</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo"><i class="fas fa-university"></i><span>PGi Bankings & Finance</span></a>
            <ul class="nav-menu">
                <li><a href="../index.php">Home</a></li>
                <li><a href="services.php" class="active">Services</a></li>
                <li><a href="loans.php">Loans</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
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
    <section class="services" style="padding-top:120px;">
        <div class="container">
            <div class="section-header">
                <h2>Our Banking Services</h2>
                <p>Comprehensive financial solutions tailored to your needs.</p>
            </div>
            <div class="services-grid">
                <div class="service-card"><div class="service-icon"><i class="fas fa-piggy-bank"></i></div><h3>Savings Account</h3><p>Earn competitive interest on your savings with zero minimum balance.</p></div>
                <div class="service-card"><div class="service-icon"><i class="fas fa-money-check-alt"></i></div><h3>Current Account</h3><p>Business-friendly current accounts with unlimited transactions.</p></div>
                <div class="service-card"><div class="service-icon"><i class="fas fa-credit-card"></i></div><h3>Debit & Credit Cards</h3><p>Worldwide accepted cards with cashback and rewards.</p></div>
                <div class="service-card"><div class="service-icon"><i class="fas fa-mobile-alt"></i></div><h3>Mobile Banking</h3><p>24/7 access to your accounts via our secure mobile app.</p></div>
                <div class="service-card"><div class="service-icon"><i class="fas fa-exchange-alt"></i></div><h3>Money Transfer</h3><p>Instant domestic and international transfers at low fees.</p></div>
                <div class="service-card"><div class="service-icon"><i class="fas fa-shield-alt"></i></div><h3>Safe Deposit Boxes</h3><p>Secure storage for your valuables and important documents.</p></div>
            </div>
        </div>
    </section>
    <footer class="footer"><div class="container"><div class="footer-bottom"><p>&copy; 2024 PGi Bankings & Finance. All rights reserved.</p></div></div></footer>
</body>
</html>
