<?php
require_once '../includes/config.php';
requireAdmin();

$stats = ['users'=>0,'accounts'=>0,'transactions'=>0,'loans'=>0,'balance'=>0];
$recentUsers = [];
$recentTransactions = [];
$pendingLoans = [];

if ($db) {
    $stats['users'] = $db->users->countDocuments(['role'=>'customer']);
    $stats['accounts'] = $db->accounts->countDocuments();
    $stats['transactions'] = $db->transactions->countDocuments();
    $stats['loans'] = $db->loans->countDocuments(['status'=>'pending']);

    $balanceResult = $db->accounts->aggregate([
        ['$group' => ['_id' => null, 'total' => ['$sum' => '$balance']]]
    ]);
    $balanceData = iterator_to_array($balanceResult);
    $stats['balance'] = $balanceData[0]['total'] ?? 0;

    $recentUsers = iterator_to_array($db->users->find(['role'=>'customer'], ['sort'=>['created_at'=>-1], 'limit'=>5]));
    $recentTransactions = iterator_to_array($db->transactions->find([], ['sort'=>['created_at'=>-1], 'limit'=>5]));
    $pendingLoans = iterator_to_array($db->loans->find(['status'=>'pending'], ['sort'=>['applied_at'=>-1], 'limit'=>5]));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PGi Bankings & Finance</title>
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
            <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="users.php"><i class="fas fa-users"></i> Customers</a>
            <a href="accounts.php"><i class="fas fa-wallet"></i> Accounts</a>
            <a href="transactions.php"><i class="fas fa-list"></i> Transactions</a>
            <a href="loans.php"><i class="fas fa-hand-holding-usd"></i> Loan Requests</a>
            <a href="support.php"><i class="fas fa-headset"></i> Support Tickets</a>
            <a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-footer">Admin v2.0.0 &copy; PGi Bank</div>
    </aside>

    <main class="main-content">
        <header class="form-page-header">
            <h1><i class="fas fa-shield-alt"></i> Admin Dashboard</h1>
            <span style="color:#64748b;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        </header>

        <div class="dashboard-cards">
            <div class="dashboard-card balance-card">
                <div class="card-top"><div class="card-icon"><i class="fas fa-users"></i></div></div>
                <div class="card-label">Total Customers</div>
                <div class="card-value"><?php echo number_format($stats['users']); ?></div>
            </div>
            <div class="dashboard-card income-card">
                <div class="card-top"><div class="card-icon"><i class="fas fa-wallet"></i></div></div>
                <div class="card-label">Total Accounts</div>
                <div class="card-value"><?php echo number_format($stats['accounts']); ?></div>
            </div>
            <div class="dashboard-card expense-card">
                <div class="card-top"><div class="card-icon"><i class="fas fa-list"></i></div></div>
                <div class="card-label">Total Transactions</div>
                <div class="card-value"><?php echo number_format($stats['transactions']); ?></div>
            </div>
            <div class="dashboard-card savings-card">
                <div class="card-top"><div class="card-icon"><i class="fas fa-dollar-sign"></i></div></div>
                <div class="card-label">Total Deposits</div>
                <div class="card-value"><?php echo formatCurrency($stats['balance']); ?></div>
            </div>
        </div>

        <div class="section-card" style="margin-bottom: 24px;">
            <div class="section-header-row">
                <h3><i class="fas fa-hand-holding-usd"></i> Pending Loan Requests (<?php echo $stats['loans']; ?>)</h3>
            </div>
            <?php if (empty($pendingLoans)): ?>
                <div class="empty-state"><i class="fas fa-check-circle"></i><p>No pending loan requests.</p></div>
            <?php else: ?>
            <div class="transactions-table-wrapper">
                <table class="transactions-table">
                    <thead><tr><th>Customer</th><th>Type</th><th>Amount</th><th>Rate</th><th>Duration</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($pendingLoans as $loan):
                            $user = $db ? $db->users->findOne(['_id'=>$loan['user_id']]) : null;
                            $userName = $user ? ($user['first_name'].' '.$user['last_name']) : 'Unknown';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($userName); ?></td>
                            <td><?php echo ucfirst($loan['loan_type']); ?></td>
                            <td><?php echo formatCurrency($loan['amount']); ?></td>
                            <td><?php echo $loan['interest_rate']; ?>%</td>
                            <td><?php echo $loan['duration_months']; ?> mo</td>
                            <td><span class="status-badge pending">Pending</span></td>
                            <td>
                                <a href="loan-action.php?id=<?php echo mongoId($loan['_id']); ?>&action=approve" class="btn btn-small" style="background:#059669;color:#fff;margin-right:4px;">Approve</a>
                                <a href="loan-action.php?id=<?php echo mongoId($loan['_id']); ?>&action=reject" class="btn btn-small" style="background:#dc2626;color:#fff;">Reject</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <div class="dashboard-row" style="grid-template-columns: 1fr 1fr;">
            <div class="section-card">
                <div class="section-header-row">
                    <h3><i class="fas fa-users"></i> Recent Customers</h3>
                </div>
                <?php if (empty($recentUsers)): ?>
                    <div class="empty-state"><i class="fas fa-users"></i><p>No customers found.</p></div>
                <?php else: ?>
                <div class="transactions-table-wrapper">
                    <table class="transactions-table">
                        <thead><tr><th>Name</th><th>Email</th><th>Status</th><th>Joined</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentUsers as $u):
                                $joined = isset($u['created_at']) && is_object($u['created_at']) && method_exists($u['created_at'], 'toDateTime')
                                    ? $u['created_at']->toDateTime()->format('Y-m-d') : '-';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['first_name'].' '.$u['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><span class="status-badge <?php echo $u['status']; ?>"><?php echo ucfirst($u['status']); ?></span></td>
                                <td><?php echo $joined; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="section-card">
                <div class="section-header-row">
                    <h3><i class="fas fa-list"></i> Recent Transactions</h3>
                </div>
                <?php if (empty($recentTransactions)): ?>
                    <div class="empty-state"><i class="fas fa-list"></i><p>No transactions found.</p></div>
                <?php else: ?>
                <div class="transactions-table-wrapper">
                    <table class="transactions-table">
                        <thead><tr><th>ID</th><th>Type</th><th>Amount</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentTransactions as $t):
                                $date = isset($t['created_at']) && is_object($t['created_at']) && method_exists($t['created_at'], 'toDateTime')
                                    ? $t['created_at']->toDateTime()->format('Y-m-d H:i') : '-';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($t['transaction_id'] ?? mongoId($t['_id'])); ?></td>
                                <td><?php echo ucfirst($t['type']); ?></td>
                                <td class="<?php echo $t['amount']>=0?'amount-positive':'amount-negative'; ?>"><?php echo ($t['amount']>=0?'+':'').formatCurrency($t['amount']); ?></td>
                                <td><?php echo $date; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
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
