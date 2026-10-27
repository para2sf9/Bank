<?php
require_once '../includes/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $accountType = $_POST['accountType'] ?? 'savings';

    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        if ($db) {
            $existing = $db->users->findOne(['email' => $email]);
            if ($existing) {
                $error = 'Email already registered';
            } else {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $userResult = $db->users->insertOne([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => $phone,
                    'password_hash' => $passwordHash,
                    'address' => $address,
                    'dob' => $dob,
                    'role' => 'customer',
                    'status' => 'active',
                    'kyc_status' => 'pending',
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ]);
                $userId = $userResult->getInsertedId();

                $accountNumber = generateAccountNumber();
                $db->accounts->insertOne([
                    'user_id' => $userId,
                    'account_number' => $accountNumber,
                    'account_type' => $accountType,
                    'balance' => 0.00,
                    'status' => 'active',
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ]);

                $success = 'Account created successfully! Please login with your credentials.';
            }
        } else {
            $success = 'Account created successfully! Please login with demo@pgibank.com / demo123';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PGi Bankings & Finance</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-decoration auth-decoration-1"></div>
    <div class="auth-decoration auth-decoration-2"></div>
    <div class="auth-decoration auth-decoration-3"></div>

    <div class="auth-container auth-container-register">
        <div class="auth-left">
            <div class="auth-brand">
                <a href="../index.php"><i class="fas fa-university"></i> PGi Bankings & Finance</a>
            </div>
            <div class="auth-welcome">
                <h1>Join PGi Bank!</h1>
                <p>Open an account today and start your journey towards financial freedom with us.</p>
                <div class="auth-features">
                    <div class="auth-feature"><i class="fas fa-gift"></i> Free Account Opening</div>
                    <div class="auth-feature"><i class="fas fa-wallet"></i> Zero Minimum Balance</div>
                    <div class="auth-feature"><i class="fas fa-bolt"></i> Instant Approval</div>
                </div>
            </div>
            <div class="auth-left-footer">
                <p><i class="fas fa-phone-alt"></i> +91 9284327901</p>
                <p><i class="fas fa-map-marker-alt"></i> Kolhapur, Maharashtra</p>
            </div>
        </div>
        <div class="auth-right auth-right-scrollable">
            <div class="auth-card">
                <div class="auth-card-header">
                    <h2>Create Account</h2>
                    <p>Fill in your details to open a new account</p>
                </div>
                <?php if($error) echo showAlert($error, 'error'); ?>
                <?php if($success) echo showAlert($success, 'success'); ?>
                <form method="POST" action="" id="registerForm" autocomplete="off">
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="firstName" placeholder="Paras" required autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="lastName" placeholder="Chavan" required autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email Address</label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email" placeholder="paras@pgibank.com" required autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" name="phone" max="10" min="10" placeholder="+91 9284327901" required autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Full Address</label>
                        <div class="input-wrapper">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <input type="text" name="address" placeholder="Kolhapur, Maharashtra" required autocomplete="off">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <div class="input-wrapper">
                                <i class="fas fa-calendar input-icon"></i>
                                <input type="date" name="dob" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Account Type</label>
                            <div class="input-wrapper">
                                <i class="fas fa-university input-icon"></i>
                                <select name="accountType" required>
                                    <option value="savings">Savings Account</option>
                                    <option value="current">Current Account</option>
                                    <option value="business">Business Account</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" name="password" id="passwordInput" placeholder="Min. 6 characters" required autocomplete="off">
                                <button type="button" class="toggle-password" onclick="togglePassword(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength" id="passwordStrength">
                                <div class="password-strength-bar" id="strengthBar"></div>
                            </div>
                            <div class="password-hint" id="passwordHint">Use 6+ chars with letters and numbers</div>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" name="confirmPassword" id="confirmPasswordInput" placeholder="Re-enter password" required autocomplete="off">
                            </div>
                            <div class="password-match" id="passwordMatch"></div>
                        </div>
                    </div>
                    <div class="form-group terms-group">
                        <label class="checkbox">
                            <input type="checkbox" name="terms" required>
                            <span class="checkmark"></span>
                            <span class="label-text">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <span class="btn-text">Create Account</span>
                        <i class="fas fa-arrow-right btn-icon"></i>
                    </button>
                </form>
                <p class="auth-footer">Already have an account? <a href="login.php">Sign In</a></p>
            </div>
        </div>
    </div>

    <script src="../assets/js/auth.js"></script>
</body>
</html>
