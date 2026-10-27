<?php
require_once '../includes/config.php';
requireAuth();

$success = '';
$error = '';
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverAccount = trim($_POST['receiver_account'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $pin = $_POST['pin'] ?? '';

    if (empty($receiverAccount) || $amount <= 0) {
        $error = 'Please fill in all required fields';
    } elseif (strlen($pin) !== 4) {
        $error = 'Please enter a valid 4-digit PIN';
    } else {
        if ($db) {
            $senderAccount = $db->accounts->findOne(['user_id' => new MongoDB\BSON\ObjectId($userId), 'status' => 'active']);
            if (!$senderAccount) {
                $error = 'Sender account not found';
            } elseif ($senderAccount['balance'] < $amount) {
                $error = 'Insufficient balance';
            } else {
                $receiver = $db->accounts->findOne(['account_number' => $receiverAccount, 'status' => 'active']);
                if (!$receiver) {
                    $error = 'Receiver account not found';
                } else {
                    $db->accounts->updateOne(['_id' => $senderAccount['_id']], ['$inc' => ['balance' => -$amount]]);
                    $db->accounts->updateOne(['_id' => $receiver['_id']], ['$inc' => ['balance' => $amount]]);
                    $txnId = generateTransactionId();
                    $db->transactions->insertOne([
                        'transaction_id' => $txnId, 'account_id' => $senderAccount['_id'], 'type' => 'transfer',
                        'amount' => -$amount, 'description' => $description, 'receiver_account' => $receiverAccount,
                        'status' => 'completed', 'created_at' => new MongoDB\BSON\UTCDateTime()
                    ]);
                    $success = 'Transfer of ' . formatCurrency($amount) . ' to account ' . htmlspecialchars($receiverAccount) . ' completed successfully!';
                }
            }
        } else {
            $success = 'Transfer of ' . formatCurrency($amount) . ' to account ' . htmlspecialchars($receiverAccount) . ' completed successfully! (Demo Mode)';
        }
    }
}

$accountNumber = $_SESSION['account_number'] ?? 'PGI2024A1B2C3D4';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money - PGi Bankings & Finance</title>
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
            <a href="transfer.php" class="active"><i class="fas fa-exchange-alt"></i> Transfer</a>
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
            <h1><i class="fas fa-exchange-alt"></i> Transfer Money</h1>
        </header>

        <div class="form-container">
            <div class="form-card">
                <h2><i class="fas fa-paper-plane" style="color: #0f4c81; margin-right: 8px;"></i> Send Money</h2>
                <?php if($error) echo showAlert($error, 'error'); ?>
                <?php if($success) echo showAlert($success, 'success'); ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label><i class="fas fa-wallet" style="margin-right: 6px; color: #0f4c81;"></i> From Account</label>
                        <input type="text" value="<?php echo htmlspecialchars($accountNumber); ?> - Savings" readonly class="readonly">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user" style="margin-right: 6px; color: #0f4c81;"></i> Receiver Account Number</label>
                        <input type="text" name="receiver_account" placeholder="Enter receiver's account number" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-dollar-sign" style="margin-right: 6px; color: #0f4c81;"></i> Amount</label>
                            <input type="number" name="amount" placeholder="0.00" min="1" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock" style="margin-right: 6px; color: #0f4c81;"></i> 4-Digit PIN</label>
                            <input type="password" name="pin" placeholder="Enter your PIN" maxlength="4" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-comment" style="margin-right: 6px; color: #0f4c81;"></i> Description (Optional)</label>
                        <input type="text" name="description" placeholder="What's this for?">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Send Money</button>
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
