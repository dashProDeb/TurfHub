<?php
// reject-kyc.php — POST: Reject owner with reason
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$admin = guardRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input  = json_decode(file_get_contents('php://input'), true);
$userId = (int) ($input['user_id'] ?? 0);
$reason = trim($input['reason'] ?? '');

if (!$userId) {
    jsonError('user_id is required');
}

$db = Database::connect();
$stmt = $db->prepare('UPDATE users SET kyc_status = "rejected" WHERE id = ?');
$stmt->execute([$userId]);

jsonResponse(['success' => true, 'message' => 'KYC rejected']);
