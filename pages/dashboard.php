<?php
require_once '../includes/config.php';
requireAuth();

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
$userName = $_SESSION['user_name'];

$balance = 25450.00;
$income = 8200.00;
$expense = 3150.00;
$savings = 5100.00;
$accountNumber = $_SESSION['account_number'] ?? 'PGI2024A1B2C3D4';
$accountType = 'Savings Account';
$kycStatus = 'Verified';
$accountStatus = 'Active';
$transactions = [];

if ($db) {
    $account = $db->accounts->findOne(['user_id' => new MongoDB\BSON\ObjectId($userId)]);
    if ($account) {
        $balance = $account['balance'] ?? 0;
        $accountNumber = $account['account_number'];
        $accountType = ucfirst($account['account_type'] ?? 'savings') . ' Account';
    }

    if ($account) {
        $txnCursor = $db->transactions->find(
            ['account_id' => $account['_id']],
            ['sort' => ['created_at' => -1], 'limit' => 10]
        );
        $transactions = iterator_to_array($txnCursor);
    }

    $user = $db->users->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);
    if ($user) {
        $kycStatus = ucfirst($user['kyc_status'] ?? 'pending');
    }
}

if (empty($transactions)) {
    $transactions = [
        ['transaction_id'=>'TXNABC123XYZ','type'=>'deposit','description'=>'Cash Deposit','created_at'=>'2024-01-15','amount'=>5000,'status'=>'completed'],
        ['transaction_id'=>'TXNDEF456UVW','type'=>'withdrawal','description'=>'ATM Withdrawal','created_at'=>'2024-01-14','amount'=>-500,'status'=>'completed'],
        ['transaction_id'=>'TXNGHI789RST','type'=>'transfer','description'=>'Transfer to John Doe','created_at'=>'2024-01-13','amount'=>-1200,'status'=>'completed'],
        ['transaction_id'=>'TXNJKL012OPQ','type'=>'deposit','description'=>'Salary Credit','created_at'=>'2024-01-10','amount'=>8200,'status'=>'completed'],
        ['transaction_id'=>'TXNMNO345LMN','type'=>'payment','description'=>'Electric Bill','created_at'=>'2024-01-08','amount'=>-150,'status'=>'completed'],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PGi Bankings & Finance</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-page">
    <button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">
        <i class="fas fa-bars"></i>
    </button>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <a href="../index.php"><i class="fas fa-university"></i> <span>PGi Bank</span></a>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="accounts.php"><i class="fas fa-wallet"></i> My Accounts</a>
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
        <?php echo showDemoBanner(); ?>
        <header class="dashboard-header">
            <h1>Welcome, <span><?php echo htmlspecialchars($userName); ?></span>!</h1>
            <div class="header-actions">
                <a href="transfer.php" class="btn btn-primary btn-small"><i class="fas fa-paper-plane"></i> Send Money</a>
            </div>
        </header>

        <div class="dashboard-cards">
            <div class="dashboard-card balance-card">
                <div class="card-top">
                    <div class="card-icon"><i class="fas fa-wallet"></i></div>
                </div>
                <div class="card-label">Total Balance</div>
                <div class="card-value"><?php echo formatCurrency($balance); ?></div>
                <div class="card-change positive"><i class="fas fa-arrow-up"></i> +2.5% this month</div>
            </div>
            <div class="dashboard-card income-card">
                <div class="card-top">
                    <div class="card-icon"><i class="fas fa-arrow-down"></i></div>
                </div>
                <div class="card-label">Income</div>
                <div class="card-value"><?php echo formatCurrency($income); ?></div>
                <div class="card-change positive"><i class="fas fa-arrow-up"></i> +12% this month</div>
            </div>
            <div class="dashboard-card expense-card">
                <div class="card-top">
                    <div class="card-icon"><i class="fas fa-arrow-up"></i></div>
                </div>
                <div class="card-label">Expenses</div>
                <div class="card-value"><?php echo formatCurrency($expense); ?></div>
                <div class="card-change negative"><i class="fas fa-arrow-down"></i> -5% this month</div>
            </div>
            <div class="dashboard-card savings-card">
                <div class="card-top">
                    <div class="card-icon"><i class="fas fa-piggy-bank"></i></div>
                </div>
                <div class="card-label">Savings</div>
                <div class="card-value"><?php echo formatCurrency($savings); ?></div>
                <div class="card-change positive"><i class="fas fa-arrow-up"></i> +8% this month</div>
            </div>
        </div>

        <div class="account-info-section">
            <div class="section-card">
                <div class="section-header-row">
                    <h3><i class="fas fa-info-circle"></i> Account Information</h3>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Account Number</span>
                        <span class="info-value"><?php echo htmlspecialchars($accountNumber); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Account Type</span>
                        <span class="info-value"><?php echo htmlspecialchars($accountType); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value"><span class="status-active"><?php echo htmlspecialchars($accountStatus); ?></span></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">KYC Status</span>
                        <span class="info-value"><span class="status-verified"><?php echo htmlspecialchars($kycStatus); ?></span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-row">
            <div class="transactions-section section-card">
                <div class="section-header-row">
                    <h3><i class="fas fa-history"></i> Recent Transactions</h3>
                    <a href="transactions.php" class="btn btn-small btn-outline">View All</a>
                </div>
                <div class="transactions-table-wrapper">
                    <table class="transactions-table">
                        <thead>
                            <tr><th>Transaction ID</th><th>Type</th><th>Description</th><th>Date</th><th>Amount</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $t): 
                                $amount = floatval($t['amount']);
                                $amountClass = $amount >= 0 ? 'amount-positive' : 'amount-negative';
                                $amountDisplay = ($amount >= 0 ? '+' : '') . formatCurrency($amount);
                                $date = isset($t['created_at']) && is_object($t['created_at']) && method_exists($t['created_at'], 'toDateTime') 
                                    ? $t['created_at']->toDateTime()->format('Y-m-d H:i') 
                                    : ($t['created_at'] ?? '-');
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($t['transaction_id'] ?? mongoId($t['_id'])); ?></td>
                                <td><span class="type-badge <?php echo $t['type']; ?>"><?php echo ucfirst($t['type']); ?></span></td>
                                <td><?php echo htmlspecialchars($t['description']); ?></td>
                                <td><?php echo htmlspecialchars($date); ?></td>
                                <td class="<?php echo $amountClass; ?>"><?php echo $amountDisplay; ?></td>
                                <td><span class="status-badge <?php echo $t['status']; ?>"><?php echo ucfirst($t['status']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="quick-actions section-card">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                <div class="actions-grid" style="grid-template-columns: repeat(2, 1fr);">
                    <a href="transfer.php" class="action-card"><i class="fas fa-paper-plane"></i><span>Send Money</span></a>
                    <a href="deposit.php" class="action-card"><i class="fas fa-plus-circle"></i><span>Deposit</span></a>
                    <a href="withdraw.php" class="action-card"><i class="fas fa-minus-circle"></i><span>Withdraw</span></a>
                    <a href="loans.php" class="action-card"><i class="fas fa-hand-holding-usd"></i><span>Apply Loan</span></a>
                    <a href="cards.php" class="action-card"><i class="fas fa-credit-card"></i><span>My Cards</span></a>
                    <a href="profile.php" class="action-card"><i class="fas fa-cog"></i><span>Settings</span></a>
                </div>
            </div>
        </div>
    </main>

    <script>
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.sidebar');
        const toggle = document.querySelector('.mobile-toggle');
        if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        }
    });
    </script>
</body>
</html>
