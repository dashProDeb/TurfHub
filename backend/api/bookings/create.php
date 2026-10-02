<?php
// create.php — POST: Create booking + mark slot booked (transactional)
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$turfId        = (int) ($input['turf_id'] ?? 0);
$slotId        = (int) ($input['slot_id'] ?? 0);
$totalPrice    = (int) ($input['total_price'] ?? 0);
$paymentMethod = $input['payment_method'] ?? 'bkash';

if (!$turfId || !$slotId || !$totalPrice) {
    jsonError('turf_id, slot_id, and total_price are required');
}

$db = Database::connect();

// Verify slot is available (use row lock to prevent double-booking)
$stmt = $db->prepare('SELECT * FROM turf_slots WHERE id = ? AND status = "available" FOR UPDATE');
$db->beginTransaction();

try {
    $stmt->execute([$slotId]);
    $slot = $stmt->fetch();

    if (!$slot) {
        $db->rollBack();
        jsonError('Slot is no longer available', 409);
    }

    // Generate unique IDs
    $trxId = 'SSL-TH-' . random_int(100000, 999999);
    $refId = 'TH-' . date('Y') . '-' . date('md') . '-' . random_int(100, 999);

    // Insert booking
    $stmt = $db->prepare(
        'INSERT INTO bookings (user_id, turf_id, slot_id, total_price, payment_method, trx_id, ref_id, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, "confirmed")'
    );
    $stmt->execute([$user['id'], $turfId, $slotId, $totalPrice, $paymentMethod, $trxId, $refId]);
    $bookingId = (int) $db->lastInsertId();

    // Mark slot as booked
    $stmt = $db->prepare('UPDATE turf_slots SET status = "booked", booked_by = ? WHERE id = ?');
    $stmt->execute([$user['id'], $slotId]);

    $db->commit();

    jsonResponse([
        'success' => true,
        'booking' => [
            'id'             => $bookingId,
            'trx_id'         => $trxId,
            'ref_id'         => $refId,
            'total_price'    => $totalPrice,
            'payment_method' => $paymentMethod,
            'status'         => 'confirmed'
        ]
    ]);
} catch (Exception $e) {
    $db->rollBack();
    jsonError('Booking failed: ' . $e->getMessage(), 500);
}
