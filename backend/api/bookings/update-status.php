<?php
// update-status.php — POST: Update booking status (Owner / Admin)
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$user = guard();
if (!in_array($user['role'], ['owner', 'admin'])) {
    jsonError('Unauthorized', 403);
}

$input = json_decode(file_get_contents('php://input'), true);
$bookingId = (int) ($input['booking_id'] ?? 0);
$status = strtolower(trim($input['status'] ?? ''));

$allowedStatuses = ['confirmed', 'rejected', 'cancelled', 'completed', 'pending'];
if (!$bookingId || !in_array($status, $allowedStatuses)) {
    jsonError('Valid booking_id and status (confirmed, rejected, cancelled, completed, pending) are required');
}

$db = Database::connect();

// Fetch booking and check permissions
$stmt = $db->prepare(
    'SELECT b.id, b.user_id, b.turf_id, b.slot_id, b.status AS current_status, tg.owner_id
     FROM bookings b
     JOIN turf_grounds tg ON tg.id = b.turf_id
     WHERE b.id = ?'
);
$stmt->execute([$bookingId]);
$booking = $stmt->fetch();

if (!$booking) {
    jsonError('Booking not found', 404);
}

if ($user['role'] === 'owner' && (int)$booking['owner_id'] !== (int)$user['id']) {
    jsonError('You do not own the turf for this booking', 403);
}

$db->beginTransaction();
try {
    // 1. Update booking status
    $stmt = $db->prepare('UPDATE bookings SET status = ? WHERE id = ?');
    $stmt->execute([$status, $bookingId]);

    // 2. Synchronize turf slot status
    $slotId = (int) ($booking['slot_id'] ?? 0);
    if ($slotId > 0) {
        if ($status === 'confirmed') {
            $stmt = $db->prepare('UPDATE turf_slots SET status = "booked", booked_by = ? WHERE id = ?');
            $stmt->execute([$booking['user_id'], $slotId]);
        } elseif (in_array($status, ['rejected', 'cancelled'])) {
            $stmt = $db->prepare('UPDATE turf_slots SET status = "available", booked_by = NULL WHERE id = ?');
            $stmt->execute([$slotId]);
        }
    }

    $db->commit();
    jsonResponse([
        'success' => true,
        'message' => 'Booking status updated successfully',
        'booking_id' => $bookingId,
        'status' => $status
    ]);
} catch (Exception $e) {
    $db->rollBack();
    jsonError('Failed to update booking status: ' . $e->getMessage(), 500);
}
