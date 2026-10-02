<?php
// update-status.php — POST: Reserve / block / maintenance (Owner)
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input  = json_decode(file_get_contents('php://input'), true);
$slotId = (int) ($input['slot_id'] ?? 0);
$status = $input['status'] ?? '';

if (!$slotId || !in_array($status, ['available', 'maintenance', 'reserved'])) {
    jsonError('Valid slot_id and status (available, maintenance, reserved) are required');
}

// Verify the slot belongs to owner's turf
$db = Database::connect();
$stmt = $db->prepare(
    'SELECT ts.id FROM turf_slots ts
     JOIN turf_grounds tg ON tg.id = ts.turf_id
     WHERE ts.id = ? AND tg.owner_id = ?'
);
$stmt->execute([$slotId, $user['id']]);
if (!$stmt->fetch()) {
    jsonError('Slot not found or not on your turf', 403);
}

$stmt = $db->prepare('UPDATE turf_slots SET status = ? WHERE id = ?');
$stmt->execute([$status, $slotId]);

jsonResponse(['success' => true]);
