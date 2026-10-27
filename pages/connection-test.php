<?php
require_once '../includes/config.php';

$status = getConnectionStatus();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection Test - PGi Bank</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background: linear-gradient(135deg, #0a192f, #1d3557);
            font-family: var(--font-body);
            padding: 20px;
        }
        .test-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 30px 80px rgba(0,0,0,0.3);
        }
        .test-card h1 {
            font-family: var(--font-display);
            color: #1e293b;
            margin-bottom: 8px;
            font-size: 1.8rem;
        }
        .test-card .subtitle {
            color: #64748b;
            margin-bottom: 28px;
            font-size: 0.95rem;
        }
        .status-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 12px;
            font-size: 0.92rem;
        }
        .status-item.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .status-item.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .status-item.warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .status-item i { font-size: 1.1rem; }
        .status-label { font-weight: 600; min-width: 160px; }
        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
            color: #991b1b;
            font-size: 0.88rem;
            line-height: 1.7;
            white-space: pre-wrap;
        }
        .success-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
            color: #166534;
            font-size: 0.88rem;
            line-height: 1.7;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #0f4c81, #1d3557);
            color: #fff;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.92rem;
            transition: all 0.3s ease;
        }
        .btn-back:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15,76,129,0.3); }
        code {
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.85em;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="test-card">
        <h1><i class="fas fa-database" style="color: #0f4c81;"></i> MongoDB Connection Test</h1>
        <p class="subtitle">Check your MongoDB Atlas connection status</p>

        <div class="status-item <?php echo $status['status'] === 'connected' ? 'success' : 'error'; ?>">
            <i class="fas fa-<?php echo $status['status'] === 'connected' ? 'check-circle' : 'times-circle'; ?>"></i>
            <span class="status-label">Connection Status:</span>
            <span><?php echo ucfirst($status['status']); ?></span>
        </div>

        <div class="status-item <?php echo $status['composer_loaded'] ? 'success' : 'error'; ?>">
            <i class="fas fa-<?php echo $status['composer_loaded'] ? 'check-circle' : 'times-circle'; ?>"></i>
            <span class="status-label">Composer Autoload:</span>
            <span><?php echo $status['composer_loaded'] ? 'Loaded' : 'Not Found'; ?></span>
        </div>

        <div class="status-item <?php echo extension_loaded('mongodb') ? 'success' : 'error'; ?>">
            <i class="fas fa-<?php echo extension_loaded('mongodb') ? 'check-circle' : 'times-circle'; ?>"></i>
            <span class="status-label">MongoDB Extension:</span>
            <span><?php echo extension_loaded('mongodb') ? 'Installed' : 'Not Installed'; ?></span>
        </div>

        <div class="status-item <?php echo $status['demo_mode'] ? 'warning' : 'success'; ?>">
            <i class="fas fa-<?php echo $status['demo_mode'] ? 'info-circle' : 'check-circle'; ?>"></i>
            <span class="status-label">Demo Mode:</span>
            <span><?php echo $status['demo_mode'] ? 'Active (Fallback)' : 'Inactive'; ?></span>
        </div>

        <?php if ($status['status'] === 'connected'): ?>
        <div class="success-box">
            <strong><i class="fas fa-check-circle"></i> Connected successfully!</strong><br>
            Your MongoDB Atlas database is ready to use. You can now register real accounts and store data.
        </div>
        <?php else: ?>
        <div class="error-box">
            <strong><i class="fas fa-exclamation-triangle"></i> Connection Error:</strong><br>
            <?php echo nl2br(htmlspecialchars($status['error'])); ?>
        </div>
        <div style="margin-top: 16px; font-size: 0.85rem; color: #64748b; line-height: 1.7;">
            <strong>How to fix:</strong><br>
            1. Install MongoDB PHP extension: <code>sudo pecl install mongodb</code><br>
            2. Add to php.ini: <code>extension=mongodb.so</code><br>
            3. Install library: <code>composer require mongodb/mongodb</code><br>
            4. Update <code>includes/config.php</code> with your Atlas connection string.<br>
            5. In Atlas, add <code>0.0.0.0/0</code> to Network Access.
        </div>
        <?php endif; ?>

        <a href="login.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Login</a>
    </div>
</body>
</html>
