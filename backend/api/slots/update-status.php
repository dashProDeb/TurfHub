<?php
// update-status.php — POST: Reserve / block / maintenance / make available (Owner)
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input     = json_decode(file_get_contents('php://input'), true);
$slotId    = (int) ($input['slot_id'] ?? 0);
$turfId    = (int) ($input['turf_id'] ?? 0);
$slotDate  = $input['slot_date'] ?? '';
$startTime = $input['start_time'] ?? '';
$endTime   = $input['end_time'] ?? '';
$status    = $input['status'] ?? '';

if (!in_array($status, ['available', 'maintenance', 'reserved'])) {
    jsonError('Valid status (available, maintenance, reserved) is required');
}

$db = Database::connect();

if ($slotId > 0) {
    // Verify the slot belongs to owner's turf
    $stmt = $db->prepare(
        'SELECT ts.id, ts.status AS current_status FROM turf_slots ts
         JOIN turf_grounds tg ON tg.id = ts.turf_id
         WHERE ts.id = ? AND tg.owner_id = ?'
    );
    $stmt->execute([$slotId, $user['id']]);
    $existing = $stmt->fetch();
    if (!$existing) {
        jsonError('Slot not found or not on your turf', 403);
    }

    $bookedBy = ($status === 'available') ? null : $user['id'];
    $stmt = $db->prepare('UPDATE turf_slots SET status = ?, booked_by = ? WHERE id = ?');
    $stmt->execute([$status, ($status === 'reserved' ? $user['id'] : null), $slotId]);

    jsonResponse(['success' => true, 'slot_id' => $slotId, 'status' => $status]);
} else {
    // Lookup or upsert by turf_id, slot_date, start_time
    if (!$turfId || !$slotDate || !$startTime) {
        jsonError('Either slot_id or (turf_id, slot_date, start_time) is required');
    }

    // Verify turf ownership
    $stmt = $db->prepare('SELECT id, price_per_hour FROM turf_grounds WHERE id = ? AND owner_id = ?');
    $stmt->execute([$turfId, $user['id']]);
    $turf = $stmt->fetch();
    if (!$turf) {
        jsonError('Turf not found or not owned by you', 403);
    }

    // Normalize start and end time
    if (strpos($startTime, ':') !== false && strlen($startTime) <= 5) {
        $startTime .= ':00';
    }
    if (empty($endTime)) {
        $startH = (int) explode(':', $startTime)[0];
        $endTime = sprintf('%02d:00:00', $startH + 1);
    } elseif (strpos($endTime, ':') !== false && strlen($endTime) <= 5) {
        $endTime .= ':00';
    }

    // Check if slot exists
    $stmt = $db->prepare('SELECT id FROM turf_slots WHERE turf_id = ? AND slot_date = ? AND start_time = ?');
    $stmt->execute([$turfId, $slotDate, $startTime]);
    $existingSlot = $stmt->fetch();

    $bookedByVal = ($status === 'reserved') ? $user['id'] : null;

    if ($existingSlot) {
        $stmt = $db->prepare('UPDATE turf_slots SET status = ?, booked_by = ? WHERE id = ?');
        $stmt->execute([$status, $bookedByVal, $existingSlot['id']]);
        $targetSlotId = $existingSlot['id'];
    } else {
        $price = (int) ($turf['price_per_hour'] ?? 800);
        $stmt = $db->prepare(
            'INSERT INTO turf_slots (turf_id, slot_date, start_time, end_time, status, booked_by, price)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$turfId, $slotDate, $startTime, $endTime, $status, $bookedByVal, $price]);
        $targetSlotId = (int) $db->lastInsertId();
    }

    jsonResponse(['success' => true, 'slot_id' => $targetSlotId, 'status' => $status]);
}
