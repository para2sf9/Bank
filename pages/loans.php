<?php
require_once '../includes/config.php';
requireAuth();

$error = '';
$success = '';
$userId = $_SESSION['user_id'];

$loanTypes = [
    'home' => ['rate'=>7.5, 'min'=>50000, 'max'=>1000000],
    'auto' => ['rate'=>8.5, 'min'=>5000, 'max'=>100000],
    'education' => ['rate'=>6.0, 'min'=>1000, 'max'=>50000],
    'personal' => ['rate'=>12.0, 'min'=>500, 'max'=>25000],
    'sme' => ['rate'=>10.0, 'min'=>10000, 'max'=>500000],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loanType = $_POST['loan_type'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $duration = intval($_POST['duration'] ?? 0);
    $purpose = trim($_POST['purpose'] ?? '');

    if (empty($loanType) || $amount <= 0 || $duration <= 0) {
        $error = 'Please fill in all required fields';
    } else {
        if ($db) {
            $rate = $loanTypes[$loanType]['rate'];
            $monthlyRate = $rate / 1200;
            $monthlyPayment = $amount * $monthlyRate / (1 - pow(1 + $monthlyRate, -$duration));
            $totalPayable = $monthlyPayment * $duration;
            $db->loans->insertOne([
                'user_id' => new MongoDB\BSON\ObjectId($userId),
                'loan_type' => $loanType, 'amount' => $amount, 'interest_rate' => $rate,
                'duration_months' => $duration, 'monthly_payment' => $monthlyPayment,
                'total_payable' => $totalPayable, 'purpose' => $purpose,
                'status' => 'pending', 'applied_at' => new MongoDB\BSON\UTCDateTime(), 'approved_at' => null
            ]);
        }
        $success = 'Loan application submitted successfully! We will review and get back to you.';
    }
}

$myLoans = [];
if ($db) {
    $cursor = $db->loans->find(['user_id' => new MongoDB\BSON\ObjectId($userId)], ['sort' => ['applied_at' => -1]]);
    $myLoans = iterator_to_array($cursor);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loans - PGi Bankings & Finance</title>
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
            <a href="loans.php" class="active"><i class="fas fa-hand-holding-usd"></i> Loans</a>
            <a href="cards.php"><i class="fas fa-credit-card"></i> Cards</a>
            <a href="support.php"><i class="fas fa-headset"></i> Support</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-footer">v2.0.0 &copy; PGi Bank</div>
    </aside>

    <main class="main-content">
        <header class="form-page-header">
            <h1><i class="fas fa-hand-holding-usd"></i> Loans</h1>
        </header>

        <div class="form-container">
            <div class="form-card">
                <h2><i class="fas fa-file-signature" style="color: #0f4c81; margin-right: 8px;"></i> Apply for a Loan</h2>
                <?php if($error) echo showAlert($error, 'error'); ?>
                <?php if($success) echo showAlert($success, 'success'); ?>
                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-tag" style="margin-right: 6px; color: #0f4c81;"></i> Loan Type</label>
                        <select name="loan_type" required>
                            <option value="">Select Loan Type</option>
                            <option value="home">Home Loan (7.5%)</option>
                            <option value="auto">Auto Loan (8.5%)</option>
                            <option value="education">Education Loan (6.0%)</option>
                            <option value="personal">Personal Loan (12.0%)</option>
                            <option value="sme">SME Loan (10.0%)</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-dollar-sign" style="margin-right: 6px; color: #0f4c81;"></i> Amount</label>
                            <input type="number" name="amount" min="500" step="0.01" placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-clock" style="margin-right: 6px; color: #0f4c81;"></i> Duration (Months)</label>
                            <input type="number" name="duration" min="6" max="360" placeholder="e.g. 24" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-comment" style="margin-right: 6px; color: #0f4c81;"></i> Purpose</label>
                        <textarea name="purpose" rows="3" placeholder="Purpose of loan"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Apply</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($myLoans)): ?>
        <div class="section-card" style="margin-top: 24px;">
            <div class="section-header-row">
                <h3><i class="fas fa-list"></i> My Loan Applications</h3>
            </div>
            <div class="transactions-table-wrapper">
                <table class="transactions-table">
                    <thead><tr><th>Type</th><th>Amount</th><th>Rate</th><th>Duration</th><th>Status</th><th>Applied</th></tr></thead>
                    <tbody>
                        <?php foreach ($myLoans as $loan):
                            $applied = isset($loan['applied_at']) && is_object($loan['applied_at']) && method_exists($loan['applied_at'], 'toDateTime')
                                ? $loan['applied_at']->toDateTime()->format('Y-m-d H:i') : ($loan['applied_at'] ?? '-');
                        ?>
                        <tr>
                            <td><?php echo ucfirst($loan['loan_type']); ?></td>
                            <td><?php echo formatCurrency($loan['amount']); ?></td>
                            <td><?php echo $loan['interest_rate']; ?>%</td>
                            <td><?php echo $loan['duration_months']; ?> months</td>
                            <td><span class="status-badge <?php echo $loan['status']; ?>"><?php echo ucfirst($loan['status']); ?></span></td>
                            <td><?php echo $applied; ?></td>
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
