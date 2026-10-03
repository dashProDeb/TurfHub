<?php
// approve-kyc.php — POST: Approve owner verification
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$admin = guardRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input  = json_decode(file_get_contents('php://input'), true);
$userId = (int) ($input['user_id'] ?? 0);

if (!$userId) {
    jsonError('user_id is required');
}

$db = Database::connect();
$db->beginTransaction();

try {
    // Verify user's KYC
    $stmt = $db->prepare('UPDATE users SET kyc_status = "verified" WHERE id = ?');
    $stmt->execute([$userId]);

    // Also verify their turfs
    $stmt = $db->prepare('UPDATE turf_grounds SET is_verified = 1, status = "active" WHERE owner_id = ?');
    $stmt->execute([$userId]);

    $db->commit();
    jsonResponse(['success' => true, 'message' => 'KYC approved and turfs verified']);
} catch (Exception $e) {
    $db->rollBack();
    jsonError('Approval failed: ' . $e->getMessage(), 500);
}
