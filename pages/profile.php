<?php
require_once '../includes/config.php';
requireAuth();

$userId = $_SESSION['user_id'];
$user = [];

if ($db) {
    $user = $db->users->findOne(['_id' => new MongoDB\BSON\ObjectId($userId)]);
    $user = mongoToArray($user);
} else {
    $user = ['first_name'=>'Demo','last_name'=>'User','email'=>'demo@pgibank.com','phone'=>'9812345678','address'=>'Kolhapur, Maharashtra','dob'=>'1995-06-15','kyc_status'=>'verified'];
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($db) {
        $db->users->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($userId)],
            ['$set' => ['phone' => $phone, 'address' => $address, 'updated_at' => new MongoDB\BSON\UTCDateTime()]]
        );
    }
    $success = 'Profile updated successfully!';
    $user['phone'] = $phone;
    $user['address'] = $address;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - PGi Bankings & Finance</title>
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
            <a href="support.php"><i class="fas fa-headset"></i> Support</a>
            <a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
            <a href="../api/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-footer">v2.0.0 &copy; PGi Bank</div>
    </aside>

    <main class="main-content">
        <header class="form-page-header">
            <h1><i class="fas fa-user"></i> My Profile</h1>
        </header>

        <div class="form-container">
            <div class="form-card">
                <h2><i class="fas fa-id-card" style="color: #0f4c81; margin-right: 8px;"></i> Profile Settings</h2>
                <?php if($success) echo showAlert($success, 'success'); ?>
                <?php if($error) echo showAlert($error, 'error'); ?>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user" style="margin-right: 6px; color: #0f4c81;"></i> First Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" readonly class="readonly">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-user" style="margin-right: 6px; color: #0f4c81;"></i> Last Name</label>
                            <input type="text" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" readonly class="readonly">
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope" style="margin-right: 6px; color: #0f4c81;"></i> Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly class="readonly">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone" style="margin-right: 6px; color: #0f4c81;"></i> Phone</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt" style="margin-right: 6px; color: #0f4c81;"></i> Address</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar" style="margin-right: 6px; color: #0f4c81;"></i> Date of Birth</label>
                        <input type="date" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>" readonly class="readonly">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-shield-alt" style="margin-right: 6px; color: #0f4c81;"></i> KYC Status</label>
                        <input type="text" value="<?php echo ucfirst($user['kyc_status'] ?? 'Pending'); ?>" readonly class="readonly">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Save Changes</button>
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
