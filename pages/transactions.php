<?php
require_once '../includes/config.php';
requireAuth();

$userId = $_SESSION['user_id'];
$transactions = [];

if ($db) {
    $account = $db->accounts->findOne(['user_id' => new MongoDB\BSON\ObjectId($userId)]);
    if ($account) {
        $cursor = $db->transactions->find(
            ['account_id' => $account['_id']],
            ['sort' => ['created_at' => -1]]
        );
        $transactions = iterator_to_array($cursor);
    }
} else {
    $transactions = [
        ['transaction_id'=>'TXNABC123XYZ','type'=>'deposit','description'=>'Cash Deposit','created_at'=>'2024-01-15 10:30:00','amount'=>5000,'status'=>'completed'],
        ['transaction_id'=>'TXNDEF456UVW','type'=>'withdrawal','description'=>'ATM Withdrawal','created_at'=>'2024-01-14 14:20:00','amount'=>-500,'status'=>'completed'],
        ['transaction_id'=>'TXNGHI789RST','type'=>'transfer','description'=>'Transfer to John Doe','created_at'=>'2024-01-13 09:15:00','amount'=>-1200,'status'=>'completed'],
        ['transaction_id'=>'TXNJKL012OPQ','type'=>'deposit','description'=>'Salary Credit','created_at'=>'2024-01-10 08:00:00','amount'=>8200,'status'=>'completed'],
        ['transaction_id'=>'TXNMNO345LMN','type'=>'payment','description'=>'Electric Bill','created_at'=>'2024-01-08 16:45:00','amount'=>-150,'status'=>'completed'],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - PGi Bankings & Finance</title>
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
            <a href="transactions.php" class="active"><i class="fas fa-list"></i> Transactions</a>
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
            <h1><i class="fas fa-list"></i> All Transactions</h1>
        </header>

        <div class="section-card">
            <div class="transactions-table-wrapper">
                <table class="transactions-table">
                    <thead><tr><th>Transaction ID</th><th>Type</th><th>Description</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($transactions as $t):
                            $amount = floatval($t['amount']);
                            $amountClass = $amount >= 0 ? 'amount-positive' : 'amount-negative';
                            $amountDisplay = ($amount >= 0 ? '+' : '') . formatCurrency($amount);
                            $date = isset($t['created_at']) && is_object($t['created_at']) && method_exists($t['created_at'], 'toDateTime')
                                ? $t['created_at']->toDateTime()->format('Y-m-d H:i') : ($t['created_at'] ?? '-');
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
