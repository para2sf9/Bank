<?php
require_once '../includes/config.php';
requireAuth();

$error = '';
$success = '';
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0);
    $method = $_POST['method'] ?? 'cash';

    if ($amount <= 0) {
        $error = 'Please enter a valid amount';
    } else {
        if ($db) {
            $account = $db->accounts->findOne(['user_id' => new MongoDB\BSON\ObjectId($userId)]);
            if ($account) {
                $db->accounts->updateOne(['_id' => $account['_id']], ['$inc' => ['balance' => $amount]]);
                $txnId = generateTransactionId();
                $db->transactions->insertOne([
                    'transaction_id' => $txnId, 'account_id' => $account['_id'], 'type' => 'deposit',
                    'amount' => $amount, 'description' => 'Deposit via ' . ucfirst($method),
                    'status' => 'completed', 'created_at' => new MongoDB\BSON\UTCDateTime()
                ]);
                $success = 'Deposit of ' . formatCurrency($amount) . ' completed successfully!';
            }
        } else {
            $success = 'Deposit of ' . formatCurrency($amount) . ' completed successfully! (Demo Mode)';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Funds - PGi Bankings & Finance</title>
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
            <a href="deposit.php" class="active"><i class="fas fa-plus-circle"></i> Deposit</a>
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
            <h1><i class="fas fa-plus-circle"></i> Deposit Funds</h1>
        </header>

        <div class="form-container">
            <div class="form-card">
                <h2><i class="fas fa-wallet" style="color: #0f4c81; margin-right: 8px;"></i> Add Money</h2>
                <?php if($error) echo showAlert($error, 'error'); ?>
                <?php if($success) echo showAlert($success, 'success'); ?>
                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-dollar-sign" style="margin-right: 6px; color: #0f4c81;"></i> Amount</label>
                        <input type="number" name="amount" min="1" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-university" style="margin-right: 6px; color: #0f4c81;"></i> Deposit Method</label>
                        <select name="method">
                            <option value="cash">Cash Deposit</option>
                            <option value="check">Check Deposit</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-plus-circle"></i> Deposit</button>
                    </div>
                </form>
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
