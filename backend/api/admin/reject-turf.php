<?php
// reject-turf.php — POST: Reject or deactivate turf listing
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$admin = guardRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input  = json_decode(file_get_contents('php://input'), true);
$turfId = (int) ($input['turf_id'] ?? 0);
$reason = trim($input['reason'] ?? '');

if (!$turfId) {
    jsonError('turf_id is required');
}

$db = Database::connect();
$stmt = $db->prepare('UPDATE turf_grounds SET is_verified = 0, status = "inactive" WHERE id = ?');
$stmt->execute([$turfId]);

jsonResponse(['success' => true, 'message' => 'Turf listing rejected']);
