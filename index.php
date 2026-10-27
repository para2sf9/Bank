<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PGi Bankings & Finance - Putting fun in funds since 1969</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-university"></i>
                <span>PGi Bankings & Finance</span>
            </a>
            <ul class="nav-menu">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="pages/services.php">Services</a></li>
                <li><a href="pages/loans.php">Loans</a></li>
                <li><a href="pages/about.php">About</a></li>
                <li><a href="pages/contact.php">Contact</a></li>
            </ul>
            <div class="nav-buttons">
                <?php if(isLoggedIn()): ?>
                    <a href="pages/dashboard.php" class="btn btn-primary">Dashboard</a>
                    <a href="api/logout.php" class="btn btn-outline">Logout</a>
                <?php else: ?>
                    <a href="pages/login.php" class="btn btn-outline">Login</a>
                    <a href="pages/register.php" class="btn btn-primary">Open Account</a>
                <?php endif; ?>
            </div>
            <div class="hamburger">
                <span></span><span></span><span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>PGi Bankings & Finance</h1>
            <p class="tagline">Putting fun in funds since 1969</p>
            <p class="hero-desc">We do not just manage your money, we meme it. We are not your stuffy, suit-and-tie bankers. We are the bank that gets you.</p>
            <div class="hero-buttons">
                <a href="pages/register.php" class="btn btn-large btn-primary">Open Account</a>
                <a href="pages/services.php" class="btn btn-large btn-outline-light">Explore Services</a>
            </div>
            <div class="contact-info">
                <i class="fas fa-phone"></i>
                <span>+91 9284327901</span>
            </div>
        </div>
        <div class="hero-image">
            <div class="hero-card">
                <div class="card-chip"><i class="fas fa-microchip"></i></div>
                <div class="card-number">**** **** **** 1234</div>
                <div class="card-holder">PGi Bank</div>
                <div class="card-logo"><i class="fas fa-university"></i></div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <div class="section-header">
                <h2>Our Services</h2>
                <p>Tired of banking apps that look like they were designed by a hamster on acid? Ditch the snooze and join the financial fiesta at PGi Bank!</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-percentage"></i></div>
                    <h3>Interest Rates</h3>
                    <p>Competitive interest rates that make your savings grow faster than ever before.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-piggy-bank"></i></div>
                    <h3>Savings</h3>
                    <p>Multiple savings account options tailored to your financial goals and lifestyle.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-mobile-alt"></i></div>
                    <h3>Digital Services</h3>
                    <p>24/7 online banking, mobile app, and digital wallet for seamless transactions.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-lock"></i></div>
                    <h3>Safe Deposit</h3>
                    <p>Secure vault facilities for your valuable documents, jewelry, and assets.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-credit-card"></i></div>
                    <h3>Credit/Debit Card</h3>
                    <p>Worldwide accepted cards with rewards, cashback, and exclusive privileges.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon"><i class="fas fa-hand-holding-usd"></i></div>
                    <h3>Loans</h3>
                    <p>Home, auto, education, personal, and SME loans with flexible repayment options.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Loan Types Section -->
    <section class="loan-types">
        <div class="container">
            <h2>Our Loan Products</h2>
            <div class="loan-grid">
                <div class="loan-card home-loan">
                    <div class="loan-icon"><i class="fas fa-home"></i></div>
                    <h3>DREAM Home Loan</h3>
                    <p>Make your dream home a reality with our affordable home loan options.</p>
                    <a href="pages/loans.php?type=home" class="btn btn-small">Learn More</a>
                </div>
                <div class="loan-card auto-loan">
                    <div class="loan-icon"><i class="fas fa-car"></i></div>
                    <h3>Auto Loan</h3>
                    <p>Get behind the wheel of your dream car with our quick auto loan approval.</p>
                    <a href="pages/loans.php?type=auto" class="btn btn-small">Learn More</a>
                </div>
                <div class="loan-card edu-loan">
                    <div class="loan-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h3>Education Loan</h3>
                    <p>Invest in your future with our education loans for students.</p>
                    <a href="pages/loans.php?type=education" class="btn btn-small">Learn More</a>
                </div>
                <div class="loan-card personal-loan">
                    <div class="loan-icon"><i class="fas fa-user"></i></div>
                    <h3>Personal Loan</h3>
                    <p>Quick personal loans for any purpose with minimal documentation.</p>
                    <a href="pages/loans.php?type=personal" class="btn btn-small">Learn More</a>
                </div>
                <div class="loan-card sme-loan">
                    <div class="loan-icon"><i class="fas fa-briefcase"></i></div>
                    <h3>SME Loan</h3>
                    <p>Fuel your business growth with our SME and business loan solutions.</p>
                    <a href="pages/loans.php?type=sme" class="btn btn-small">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="section-header">
                <h2>Our Best Results for the Year</h2>
                <p>We've got all the boring essentials (checking, savings, loans) but served with a side of meme-tastic names.</p>
            </div>
            <div class="stats-grid">
                <div class="stat-card"><div class="stat-number">57.6 bn</div><div class="stat-label">Loan Portfolio</div></div>
                <div class="stat-card"><div class="stat-number">0.95%</div><div class="stat-label">Classified Loan Portfolio</div></div>
                <div class="stat-card"><div class="stat-number">388.5%</div><div class="stat-label">Classified Loan Coverage</div></div>
                <div class="stat-card"><div class="stat-number">50.4 bn</div><div class="stat-label">Deposit</div></div>
                <div class="stat-card"><div class="stat-number">6.1 bn</div><div class="stat-label">Shareholders Equity</div></div>
                <div class="stat-card"><div class="stat-number">18.51%</div><div class="stat-label">Capital Adequacy Ratio</div></div>
                <div class="stat-card"><div class="stat-number">8.5 bn</div><div class="stat-label">Market Capitalization</div></div>
                <div class="stat-card"><div class="stat-number">AAA</div><div class="stat-label">Credit Rating</div></div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Start Your Financial Journey?</h2>
            <p>Join thousands of satisfied customers who trust PGi Bankings & Finance for their banking needs.</p>
            <a href="pages/register.php" class="btn btn-large btn-primary">Open an Account Today</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <h3><i class="fas fa-university"></i> PGi Bankings & Finance</h3>
                    <p>Putting fun in funds since 1969. Your trusted partner for all banking and financial needs.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="pages/services.php">Services</a></li>
                        <li><a href="pages/loans.php">Loans</a></li>
                        <li><a href="pages/about.php">About Us</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="pages/services.php">Savings Account</a></li>
                        <li><a href="pages/services.php">Current Account</a></li>
                        <li><a href="pages/loans.php">Loans</a></li>
                        <li><a href="pages/services.php">Cards</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Contact Us</h4>
                    <p><i class="fas fa-phone"></i> +91 9284327901</p>
                    <p><i class="fas fa-envelope"></i> info@pgibank.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> Kolhapur, Maharashtra</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 PGi Bankings & Finance. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
