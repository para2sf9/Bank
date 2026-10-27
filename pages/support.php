<?php
require_once '../includes/config.php';
requireAuth();

$success = '';
$error = '';
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $category = $_POST['category'] ?? 'general';

    if (empty($subject) || empty($message)) {
        $error = 'Please fill in all fields';
    } else {
        if ($db) {
            $db->tickets->insertOne([
                'user_id' => new MongoDB\BSON\ObjectId($userId),
                'subject' => $subject, 'message' => $message, 'category' => $category,
                'status' => 'open', 'created_at' => new MongoDB\BSON\UTCDateTime()
            ]);
        }
        $success = 'Support ticket submitted successfully! We will respond shortly.';
    }
}

$tickets = [];
if ($db) {
    $cursor = $db->tickets->find(['user_id' => new MongoDB\BSON\ObjectId($userId)], ['sort' => ['created_at' => -1]]);
    $tickets = iterator_to_array($cursor);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - PGi Bankings & Finance</title>
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
            <a href="cards.php"><i class="fas fa-credit-card"></i> Cards</a>
            <a href="support.php" class="active"><i class="fas fa-headset"></i> Support</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-footer">v2.0.0 &copy; PGi Bank</div>
    </aside>

    <main class="main-content">
        <header class="form-page-header">
            <h1><i class="fas fa-headset"></i> Support Center</h1>
        </header>

        <div class="form-container">
            <div class="form-card">
                <h2><i class="fas fa-ticket-alt" style="color: #0f4c81; margin-right: 8px;"></i> Create Ticket</h2>
                <?php if($success) echo showAlert($success, 'success'); ?>
                <?php if($error) echo showAlert($error, 'error'); ?>
                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-tag" style="margin-right: 6px; color: #0f4c81;"></i> Category</label>
                        <select name="category">
                            <option value="general">General Inquiry</option>
                            <option value="technical">Technical Issue</option>
                            <option value="billing">Billing</option>
                            <option value="loan">Loan</option>
                            <option value="card">Card Issue</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-heading" style="margin-right: 6px; color: #0f4c81;"></i> Subject</label>
                        <input type="text" name="subject" placeholder="What is your issue about?" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-comment" style="margin-right: 6px; color: #0f4c81;"></i> Message</label>
                        <textarea name="message" rows="4" placeholder="Describe your issue in detail..." required></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Submit Ticket</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($tickets)): ?>
        <div class="section-card" style="margin-top: 24px;">
            <div class="section-header-row">
                <h3><i class="fas fa-history"></i> My Tickets</h3>
            </div>
            <div class="transactions-table-wrapper">
                <table class="transactions-table">
                    <thead><tr><th>Subject</th><th>Category</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($tickets as $t):
                            $date = isset($t['created_at']) && is_object($t['created_at']) && method_exists($t['created_at'], 'toDateTime')
                                ? $t['created_at']->toDateTime()->format('Y-m-d H:i') : ($t['created_at'] ?? '-');
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t['subject']); ?></td>
                            <td><?php echo ucfirst($t['category']); ?></td>
                            <td><span class="status-badge <?php echo $t['status']; ?>"><?php echo ucfirst($t['status']); ?></span></td>
                            <td><?php echo $date; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
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
