<?php
// cancel.php — POST: Cancel a booking and release its slot
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain', 'owner', 'admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$bookingId = (int) ($input['booking_id'] ?? 0);

if (!$bookingId) {
    jsonError('booking_id is required');
}

$db = Database::connect();
$db->beginTransaction();

try {
    // Check permission
    $stmt = $db->prepare('SELECT * FROM bookings WHERE id = ? FOR UPDATE');
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();

    if (!$booking) {
        $db->rollBack();
        jsonError('Booking not found', 404);
    }

    if ($user['role'] !== 'admin' && (int) $booking['user_id'] !== (int) $user['id']) {
        // If owner, check if booking is on their turf
        if ($user['role'] === 'owner') {
            $tstmt = $db->prepare('SELECT id FROM turf_grounds WHERE id = ? AND owner_id = ?');
            $tstmt->execute([$booking['turf_id'], $user['id']]);
            if (!$tstmt->fetch()) {
                $db->rollBack();
                jsonError('Unauthorized', 403);
            }
        } else {
            $db->rollBack();
            jsonError('Unauthorized', 403);
        }
    }

    // Cancel booking
    $stmt = $db->prepare('UPDATE bookings SET status = "cancelled" WHERE id = ?');
    $stmt->execute([$bookingId]);

    // Release slot back to available
    $stmt = $db->prepare('UPDATE turf_slots SET status = "available", booked_by = NULL WHERE id = ?');
    $stmt->execute([$booking['slot_id']]);

    $db->commit();
    jsonResponse(['success' => true, 'message' => 'Booking cancelled and slot released']);
} catch (Exception $e) {
    $db->rollBack();
    jsonError('Cancellation failed: ' . $e->getMessage(), 500);
}
