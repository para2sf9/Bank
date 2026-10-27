<?php
require_once '../includes/config.php';
requireAuth();

$userId = $_SESSION['user_id'];
$accounts = [];

if ($db) {
    $cursor = $db->accounts->find(['user_id' => new MongoDB\BSON\ObjectId($userId)]);
    $accounts = iterator_to_array($cursor);
} else {
    $accounts = [
        ['account_number'=>'PGI2024A1B2C3D4','account_type'=>'savings','balance'=>25450.00,'status'=>'active','created_at'=>'2024-01-01'],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Accounts - PGi Bankings & Finance</title>
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
            <a href="accounts.php" class="active"><i class="fas fa-wallet"></i> My Accounts</a>
            <a href="transfer.php"><i class="fas fa-exchange-alt"></i> Transfer</a>
            <a href="transactions.php"><i class="fas fa-list"></i> Transactions</a>
            <a href="deposit.php"><i class="fas fa-plus-circle"></i> Deposit</a>
            <a href="withdraw.php"><i class="fas fa-minus-circle"></i> Withdraw</a>
            <a href="loans.php"><i class="fas fa-hand-holding-usd"></i> Loans</a>
            <a href="cards.php"><i class="fas fa-credit-card"></i> Cards</a>
            <a href="support.php"><i class="fas fa-headset"></i> Support</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-footer">v2.0.0 &copy; PGi Bank</div>
    </aside>

    <main class="main-content">
        <header class="form-page-header">
            <h1><i class="fas fa-wallet"></i> My Accounts</h1>
        </header>

        <div class="account-info-section">
            <?php foreach ($accounts as $acc):
                $created = isset($acc['created_at']) && is_object($acc['created_at']) && method_exists($acc['created_at'], 'toDateTime')
                    ? $acc['created_at']->toDateTime()->format('Y-m-d') : ($acc['created_at'] ?? '-');
            ?>
            <div class="section-card" style="margin-bottom: 20px;">
                <div class="section-header-row">
                    <h3><i class="fas fa-university"></i> <?php echo ucfirst($acc['account_type'] ?? 'savings'); ?> Account</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Account Number</span><span class="info-value"><?php echo htmlspecialchars($acc['account_number']); ?></span></div>
                    <div class="info-item"><span class="info-label">Type</span><span class="info-value"><?php echo ucfirst($acc['account_type'] ?? 'savings'); ?></span></div>
                    <div class="info-item"><span class="info-label">Balance</span><span class="info-value" style="color:#059669;font-weight:700;"><?php echo formatCurrency($acc['balance'] ?? 0); ?></span></div>
                    <div class="info-item"><span class="info-label">Status</span><span class="info-value"><span class="status-active"><?php echo ucfirst($acc['status'] ?? 'active'); ?></span></span></div>
                </div>
            </div>
            <?php endforeach; ?>
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
