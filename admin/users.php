<?php
require_once '../includes/config.php';
requireAdmin();

$users = [];
if ($db) {
    $users = iterator_to_array($db->users->find(['role'=>'customer'], ['sort'=>['created_at'=>-1]]));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - PGi Bankings & Finance Admin</title>
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
            <a href="users.php" class="active"><i class="fas fa-users"></i> Customers</a>
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
            <h1><i class="fas fa-users"></i> All Customers</h1>
        </header>

        <div class="section-card">
            <div class="transactions-table-wrapper">
                <table class="transactions-table">
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>KYC</th><th>Joined</th></tr></thead>
                    <tbody>
                        <?php foreach ($users as $u):
                            $joined = isset($u['created_at']) && is_object($u['created_at']) && method_exists($u['created_at'], 'toDateTime')
                                ? $u['created_at']->toDateTime()->format('Y-m-d') : '-';
                        ?>
                        <tr>
                            <td><?php echo mongoId($u['_id']); ?></td>
                            <td><?php echo htmlspecialchars($u['first_name'].' '.$u['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><?php echo htmlspecialchars($u['phone']); ?></td>
                            <td><span class="status-badge <?php echo $u['status']; ?>"><?php echo ucfirst($u['status']); ?></span></td>
                            <td><span class="status-badge <?php echo $u['kyc_status']; ?>"><?php echo ucfirst($u['kyc_status']); ?></span></td>
                            <td><?php echo $joined; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                        <tr><td colspan="7" class="empty-state" style="text-align:center;color:#94a3b8;padding:40px;">No customers found. Connect to MongoDB Atlas to see data.</td></tr>
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
