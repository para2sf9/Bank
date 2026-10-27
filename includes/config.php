<?php
session_start();

$APP_NAME = "PGi Bankings & Finance";
$APP_VERSION = "2.0.0";
$BASE_URL = "/pgi-bank";
$JWT_SECRET = "226192ad48c2841470a8ac0ca585addc";

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

// ============================================================
// MongoDB Atlas Configuration - FREE TIER
// ============================================================
// STEP 1: Go to https://www.mongodb.com/cloud/atlas
// STEP 2: Create a FREE cluster (M0 Sandbox)
// STEP 3: Database Access -> Create User (remember username & password)
// STEP 4: Network Access -> Add IP Address -> Allow from anywhere: 0.0.0.0/0
// STEP 5: Clusters -> Connect -> Drivers -> PHP -> Copy connection string
// STEP 6: REPLACE THE LINE BELOW with your actual connection string
// ============================================================

// IMPORTANT: Replace "xxxxx" with your actual cluster name from Atlas!
// Example: pgicluster.abc123.mongodb.net
$MONGODB_URI = "mongodb://localhost:27017/";

// For local MongoDB (optional fallback)
// $MONGODB_URI = "mongodb://localhost:27017/pgi_bank";

$DB_NAME = "pgi_bank";

$db = null;
$client = null;
$demoMode = true;
$connectionError = "";
$connectionStatus = "disconnected";

// Step 1: Check if vendor autoload exists
$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
$composerLoaded = false;

if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
    $composerLoaded = true;
}

// Step 2: Check if MongoDB PHP extension is installed
if (!extension_loaded('mongodb')) {
    $connectionError = "MongoDB PHP extension is NOT installed. Please install it first.<br>"
        . "Run the included <strong>install-mongodb.bat</strong> as Administrator, or manually:<br>"
        . "1. Download DLL from https://pecl.php.net/package/mongodb<br>"
        . "2. Copy php_mongodb.dll to C:\xampp\php\ext\<br>"
        . "3. Add 'extension=mongodb' to php.ini<br>"
        . "4. Restart Apache";
    $connectionStatus = "extension_missing";
}
// Step 3: Check if MongoDB PHP library is installed via Composer
elseif (!class_exists('MongoDB\Client')) {
    $connectionError = "MongoDB PHP library not found. Please install it via Composer.<br>"
        . "Run: <strong>composer require mongodb/mongodb</strong><br>"
        . "Or run the included <strong>install-mongodb.bat</strong> as Administrator.";
    $connectionStatus = "library_missing";
}
// Step 4: Try connecting to MongoDB Atlas
else {
    try {
        $client = new MongoDB\Client($MONGODB_URI, [
            'connectTimeoutMS' => 10000,
            'serverSelectionTimeoutMS' => 10000
        ]);

        // Test connection with ping command
        $client->admin->command(['ping' => 1]);

        // Connection successful
        $db = $client->selectDatabase($DB_NAME);
        $demoMode = false;
        $connectionStatus = "connected";

    } catch (MongoDB\Driver\Exception\AuthenticationException $e) {
        $connectionError = "Authentication failed. Check your MongoDB username/password.<br>Details: " . $e->getMessage();
        $connectionStatus = "auth_failed";
    } catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
        $connectionError = "Connection timeout. Check your internet and MongoDB Atlas settings.<br>"
            . "Make sure you replaced 'xxxxx' with your actual cluster name in includes/config.php<br>"
            . "Details: " . $e->getMessage();
        $connectionStatus = "timeout";
    } catch (MongoDB\Driver\Exception\Exception $e) {
        $connectionError = "MongoDB Error: " . $e->getMessage() . "<br>"
            . "Make sure your connection string in includes/config.php is correct.";
        $connectionStatus = "error";
    } catch (Exception $e) {
        $connectionError = "Unexpected Error: " . $e->getMessage();
        $connectionStatus = "error";
    }
}

// If anything failed, enable demo mode
if ($db === null) {
    $demoMode = true;
}

// ============================================================
// Helper Functions
// ============================================================

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isStaff() {
    return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['staff', 'admin']);
}

function isCustomer() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer';
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        header('Location: ../pages/dashboard.php');
        exit;
    }
}

function requireStaff() {
    requireAuth();
    if (!isStaff()) {
        header('Location: ../pages/dashboard.php');
        exit;
    }
}

function generateAccountNumber() {
    return 'PGI' . date('Y') . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
}

function generateTransactionId() {
    return 'TXN' . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10));
}

function generateCardNumber() {
    return '4532' . str_shuffle('0123456789012');
}

function formatCurrency($amount) {
    return '$' . number_format((float)$amount, 2);
}

function showAlert($message, $type = 'success') {
    $icon = $type === 'success' ? 'check-circle' : 'exclamation-circle';
    return '<div class="alert alert-' . $type . '"><i class="fas fa-' . $icon . '"></i> ' . htmlspecialchars($message) . '</div>';
}

function showDemoBanner() {
    global $demoMode, $connectionError;
    if ($demoMode) {
        $html = '<div class="demo-banner">'
              . '<i class="fas fa-exclamation-triangle"></i> '
              . '<strong>Demo Mode:</strong> MongoDB is not connected. Data will NOT be saved. '
              . '<a href="connection-test.php">Check Connection</a>'
              . '</div>';
        return $html;
    }
    return '';
}

function mongoToArray($document) {
    if ($document === null) return null;
    return json_decode(json_encode($document), true);
}

function mongoId($id) {
    if (is_object($id) && method_exists($id, '__toString')) {
        return $id->__toString();
    }
    return (string)$id;
}

function getConnectionStatus() {
    global $connectionStatus, $connectionError, $demoMode, $composerLoaded;
    return [
        'status' => $connectionStatus,
        'demo_mode' => $demoMode,
        'composer_loaded' => $composerLoaded,
        'error' => $connectionError
    ];
}
?>
