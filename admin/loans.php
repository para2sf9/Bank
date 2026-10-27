<?php
require_once '../includes/config.php';
requireAdmin();

$loans = [];
if ($db) {
    $cursor = $db->loans->find([], ['sort'=>['applied_at'=>-1]]);
    foreach ($cursor as $l) {
        $user = $db->users->findOne(['_id'=>$l['user_id']]);
        $l['user_name'] = $user ? ($user['first_name'].' '.$user['last_name']) : 'Unknown';
        $loans[] = $l;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Requests - PGi Bankings & Finance Admin</title>
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
            <a href="accounts.php"><i class="fas fa-wallet"></i> Accounts</a>
            <a href="transactions.php"><i class="fas fa-list"></i> Transactions</a>
            <a href="loans.php" class="active"><i class="fas fa-hand-holding-usd"></i> Loan Requests</a>
            <a href="support.php"><i class="fas fa-headset"></i> Support Tickets</a>
            <a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-footer">Admin v2.0.0 &copy; PGi Bank</div>
    </aside>

    <main class="main-content">
        <header class="form-page-header">
            <h1><i class="fas fa-hand-holding-usd"></i> Loan Requests</h1>
        </header>

        <div class="section-card">
            <div class="transactions-table-wrapper">
                <table class="transactions-table">
                    <thead><tr><th>ID</th><th>Customer</th><th>Type</th><th>Amount</th><th>Rate</th><th>Duration</th><th>Status</th><th>Applied</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($loans as $l):
                            $applied = isset($l['applied_at']) && is_object($l['applied_at']) && method_exists($l['applied_at'], 'toDateTime')
                                ? $l['applied_at']->toDateTime()->format('Y-m-d') : '-';
                        ?>
                        <tr>
                            <td><?php echo mongoId($l['_id']); ?></td>
                            <td><?php echo htmlspecialchars($l['user_name']); ?></td>
                            <td><?php echo ucfirst($l['loan_type']); ?></td>
                            <td><?php echo formatCurrency($l['amount']); ?></td>
                            <td><?php echo $l['interest_rate']; ?>%</td>
                            <td><?php echo $l['duration_months']; ?> mo</td>
                            <td><span class="status-badge <?php echo $l['status']; ?>"><?php echo ucfirst($l['status']); ?></span></td>
                            <td><?php echo $applied; ?></td>
                            <td>
                                <?php if ($l['status'] === 'pending'): ?>
                                <a href="loan-action.php?id=<?php echo mongoId($l['_id']); ?>&action=approve" class="btn btn-small" style="background:#059669;color:#fff;margin-right:4px;">Approve</a>
                                <a href="loan-action.php?id=<?php echo mongoId($l['_id']); ?>&action=reject" class="btn btn-small" style="background:#dc2626;color:#fff;">Reject</a>
                                <?php else: echo '-'; endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($loans)): ?>
                        <tr><td colspan="9" style="text-align:center;color:#94a3b8;padding:40px;">No loan requests found. Connect to MongoDB Atlas to see data.</td></tr>
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
