<?php
require_once '../includes/config.php';
requireAuth();

$userId = $_SESSION['user_id'];
$cards = [];

if ($db) {
    $cursor = $db->cards->find(['user_id' => new MongoDB\BSON\ObjectId($userId)]);
    $cards = iterator_to_array($cursor);
} else {
    $cards = [
        ['card_number'=>'4532123456789012','card_type'=>'visa','expiry_date'=>'12/28','status'=>'active','daily_limit'=>5000],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cards - PGi Bankings & Finance</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-page">
    <button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">
        <i class="fas fa-bars"></i>
    </button>

    <aside class="sidebar">
        <div class="sidebar-brand"><a href="../index.php"><i class="fas fa-university"></i> <span>PGi Bank</span></a></div>
        <nav class="sidebar-nav">
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="accounts.php"><i class="fas fa-wallet"></i> My Accounts</a>
            <a href="transfer.php"><i class="fas fa-exchange-alt"></i> Transfer</a>
            <a href="transactions.php"><i class="fas fa-list"></i> Transactions</a>
            <a href="deposit.php"><i class="fas fa-plus-circle"></i> Deposit</a>
            <a href="withdraw.php"><i class="fas fa-minus-circle"></i> Withdraw</a>
            <a href="loans.php"><i class="fas fa-hand-holding-usd"></i> Loans</a>
            <a href="cards.php" class="active"><i class="fas fa-credit-card"></i> Cards</a>
            <a href="support.php"><i class="fas fa-headset"></i> Support</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-footer">v2.0.0 &copy; PGi Bank</div>
    </aside>

    <main class="main-content">
        <header class="form-page-header">
            <h1><i class="fas fa-credit-card"></i> My Cards</h1>
        </header>

        <div class="cards-grid">
            <?php foreach ($cards as $card):
                $masked = '**** **** **** ' . substr($card['card_number'], -4);
                $cardIcon = $card['card_type'] === 'visa' ? 'fa-cc-visa' : 'fa-cc-mastercard';
            ?>
            <div class="bank-card">
                <div class="bank-card-header">
                    <div class="bank-card-logo">PGi Bank</div>
                    <div class="bank-card-chip"><i class="fas fa-microchip"></i></div>
                </div>
                <div class="bank-card-number"><?php echo $masked; ?></div>
                <div class="bank-card-footer">
                    <div class="bank-card-holder"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                    <div class="bank-card-expiry">
                        <div class="bank-card-expiry-label">Valid Thru</div>
                        <div class="bank-card-expiry-value"><?php echo htmlspecialchars($card['expiry_date']); ?></div>
                    </div>
                </div>
                <div class="bank-card-type"><i class="fab <?php echo $cardIcon; ?>"></i></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="section-card" style="margin-top: 24px;">
            <div class="section-header-row">
                <h3><i class="fas fa-info-circle"></i> Card Details</h3>
            </div>
            <div class="card-details-list">
                <?php foreach ($cards as $card): ?>
                <div class="card-detail-item">
                    <span class="card-detail-label">Daily Limit</span>
                    <span class="card-detail-value"><?php echo formatCurrency($card['daily_limit'] ?? 5000); ?></span>
                </div>
                <div class="card-detail-item">
                    <span class="card-detail-label">Status</span>
                    <span class="card-detail-value"><span class="status-badge <?php echo $card['status']; ?>"><?php echo ucfirst($card['status']); ?></span></span>
                </div>
                <div class="card-detail-item">
                    <span class="card-detail-label">Card Type</span>
                    <span class="card-detail-value"><?php echo ucfirst($card['card_type']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.sidebar');
        const toggle = document.querySelector('.mobile-toggle');
        if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) sidebar.classList.remove('open');
        }
    });
    </script>
</body>
</html>
