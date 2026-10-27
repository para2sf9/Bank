<?php
require_once '../includes/config.php';
requireAdmin();

$accounts = [];
if ($db) {
    $cursor = $db->accounts->find([], ['sort'=>['created_at'=>-1]]);
    foreach ($cursor as $acc) {
        $user = $db->users->findOne(['_id'=>$acc['user_id']]);
        $acc['user_name'] = $user ? ($user['first_name'].' '.$user['last_name']) : 'Unknown';
        $accounts[] = $acc;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts - PGi Bankings & Finance Admin</title>
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
            <a href="users.php"><i class="fas fa-users"></i> Customers</a>
            <a href="accounts.php" class="active"><i class="fas fa-wallet"></i> Accounts</a>
            <a href="transactions.php"><i class="fas fa-list"></i> Transactions</a>
            <a href="loans.php"><i class="fas fa-hand-holding-usd"></i> Loan Requests</a>
            <a href="support.php"><i class="fas fa-headset"></i> Support Tickets</a>
            <a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-footer">Admin v2.0.0 &copy; PGi Bank</div>
    </aside>

    <main class="main-content">
        <header class="form-page-header">
            <h1><i class="fas fa-wallet"></i> All Accounts</h1>
        </header>

        <div class="section-card">
            <div class="transactions-table-wrapper">
                <table class="transactions-table">
                    <thead><tr><th>ID</th><th>Account Number</th><th>Customer</th><th>Type</th><th>Balance</th><th>Status</th><th>Created</th></tr></thead>
                    <tbody>
                        <?php foreach ($accounts as $a):
                            $created = isset($a['created_at']) && is_object($a['created_at']) && method_exists($a['created_at'], 'toDateTime')
                                ? $a['created_at']->toDateTime()->format('Y-m-d') : '-';
                        ?>
                        <tr>
                            <td><?php echo mongoId($a['_id']); ?></td>
                            <td><?php echo htmlspecialchars($a['account_number']); ?></td>
                            <td><?php echo htmlspecialchars($a['user_name']); ?></td>
                            <td><?php echo ucfirst($a['account_type'] ?? 'savings'); ?></td>
                            <td><?php echo formatCurrency($a['balance'] ?? 0); ?></td>
                            <td><span class="status-badge <?php echo $a['status']; ?>"><?php echo ucfirst($a['status']); ?></span></td>
                            <td><?php echo $created; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($accounts)): ?>
                        <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:40px;">No accounts found. Connect to MongoDB Atlas to see data.</td></tr>
                        <?php endif; ?>
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
