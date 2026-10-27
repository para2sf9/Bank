<?php
require_once '../includes/config.php';
requireAdmin();

$tickets = [];
if ($db) {
    $cursor = $db->tickets->find([], ['sort'=>['created_at'=>-1]]);
    foreach ($cursor as $t) {
        $user = $db->users->findOne(['_id'=>$t['user_id']]);
        $t['user_name'] = $user ? ($user['first_name'].' '.$user['last_name']) : 'Unknown';
        $tickets[] = $t;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets - PGi Bankings & Finance Admin</title>
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
            <a href="loans.php"><i class="fas fa-hand-holding-usd"></i> Loan Requests</a>
            <a href="support.php" class="active"><i class="fas fa-headset"></i> Support Tickets</a>
            <a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-footer">Admin v2.0.0 &copy; PGi Bank</div>
    </aside>

    <main class="main-content">
        <header class="form-page-header">
            <h1><i class="fas fa-headset"></i> Support Tickets</h1>
        </header>

        <div class="section-card">
            <div class="transactions-table-wrapper">
                <table class="transactions-table">
                    <thead><tr><th>ID</th><th>Customer</th><th>Subject</th><th>Category</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($tickets as $t):
                            $date = isset($t['created_at']) && is_object($t['created_at']) && method_exists($t['created_at'], 'toDateTime')
                                ? $t['created_at']->toDateTime()->format('Y-m-d H:i') : '-';
                        ?>
                        <tr>
                            <td><?php echo mongoId($t['_id']); ?></td>
                            <td><?php echo htmlspecialchars($t['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($t['subject']); ?></td>
                            <td><?php echo ucfirst($t['category']); ?></td>
                            <td><span class="status-badge <?php echo $t['status']; ?>"><?php echo ucfirst($t['status']); ?></span></td>
                            <td><?php echo $date; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($tickets)): ?>
                        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:40px;">No support tickets found. Connect to MongoDB Atlas to see data.</td></tr>
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
