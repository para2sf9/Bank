<?php
require_once '../includes/config.php';
requireAdmin();

$id = $_GET['id'] ?? '';
$action = $_GET['action'] ?? '';

if ($id && in_array($action, ['approve','reject']) && $db) {
    $status = $action === 'approve' ? 'approved' : 'rejected';
    $db->loans->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($id)],
        ['$set' => ['status' => $status, 'approved_at' => new MongoDB\BSON\UTCDateTime()]]
    );
}
header('Location: dashboard.php');
exit;
?>
