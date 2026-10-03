<?php
// create.php — POST: Create booking + mark slot booked (transactional)
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain', 'owner', 'admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$turfId        = (int) ($input['turf_id'] ?? 0);
$slotId        = (int) ($input['slot_id'] ?? 0);
$totalPrice    = (int) ($input['total_price'] ?? $input['price'] ?? 0);
$rawMethod     = strtolower(trim((string)($input['payment_method'] ?? 'bkash')));
$slotDate      = trim((string)($input['slot_date'] ?? $input['date'] ?? date('Y-m-d')));
$startTimeRaw  = trim((string)($input['start_time'] ?? $input['time'] ?? '18:00:00'));
$endTimeRaw    = trim((string)($input['end_time'] ?? ''));

// Map payment method to schema enum: 'bkash', 'nagad', 'rocket', 'upay', 'card', 'net_banking'
$paymentMethod = 'bkash';
if (strpos($rawMethod, 'nagad') !== false) {
    $paymentMethod = 'nagad';
} elseif (strpos($rawMethod, 'rocket') !== false) {
    $paymentMethod = 'rocket';
} elseif (strpos($rawMethod, 'upay') !== false) {
    $paymentMethod = 'upay';
} elseif (strpos($rawMethod, 'card') !== false || strpos($rawMethod, 'visa') !== false || strpos($rawMethod, 'mastercard') !== false) {
    $paymentMethod = 'card';
} elseif (strpos($rawMethod, 'net') !== false || strpos($rawMethod, 'bank') !== false || strpos($rawMethod, 'city') !== false) {
    $paymentMethod = 'net_banking';
}

if (!$turfId && !$slotId) {
    jsonError('turf_id or slot_id is required');
}

$db = Database::connect();
$db->beginTransaction();

try {
    // If turf_id is given, verify the turf exists and get price if not provided
    if ($turfId) {
        $stmt = $db->prepare('SELECT id, name, price_per_hour FROM turf_grounds WHERE id = ?');
        $stmt->execute([$turfId]);
        $turf = $stmt->fetch();
        if (!$turf) {
            $db->rollBack();
            jsonError('Turf ground not found', 404);
        }
        if (!$totalPrice) {
            $totalPrice = (int) $turf['price_per_hour'];
        }
    }

    // Parse start and end time
    if (strpos($startTimeRaw, '–') !== false || strpos($startTimeRaw, '-') !== false) {
        $parts = preg_split('/[–-]/', $startTimeRaw);
        $startTime = date('H:i:s', strtotime(trim($parts[0])));
        $endTime   = isset($parts[1]) && trim($parts[1]) !== '' 
                     ? date('H:i:s', strtotime(trim($parts[1]))) 
                     : date('H:i:s', strtotime($startTime . ' +1 hour'));
    } else {
        $startTime = date('H:i:s', strtotime($startTimeRaw));
        if ($endTimeRaw !== '') {
            $endTime = date('H:i:s', strtotime($endTimeRaw));
        } else {
            $endTime = date('H:i:s', strtotime($startTime . ' +1 hour'));
        }
    }

    // If slotId is not provided, find or create slot
    if (!$slotId) {
        // Try to find existing slot for this turf, date, and start time
        $stmt = $db->prepare('SELECT * FROM turf_slots WHERE turf_id = ? AND slot_date = ? AND start_time = ? FOR UPDATE');
        $stmt->execute([$turfId, $slotDate, $startTime]);
        $slot = $stmt->fetch();

        if (!$slot) {
            // Create the slot
            $stmt = $db->prepare(
                'INSERT INTO turf_slots (turf_id, slot_date, start_time, end_time, status, price)
                 VALUES (?, ?, ?, ?, "available", ?)'
            );
            $stmt->execute([$turfId, $slotDate, $startTime, $endTime, $totalPrice]);
            $slotId = (int) $db->lastInsertId();
        } else {
            $slotId = (int) $slot['id'];
        }
    }

    // Verify slot is available and lock row
    $stmt = $db->prepare('SELECT * FROM turf_slots WHERE id = ? FOR UPDATE');
    $stmt->execute([$slotId]);
    $slot = $stmt->fetch();

    if (!$slot) {
        $db->rollBack();
        jsonError('Slot not found', 404);
    }

    if ($slot['status'] === 'booked') {
        $db->rollBack();
        jsonError('This slot has already been booked by another user. Please choose another time.', 409);
    }

    if ($slot['status'] === 'maintenance') {
        $db->rollBack();
        jsonError('This slot is under scheduled maintenance. Please choose another time.', 409);
    }

    if (!$turfId) {
        $turfId = (int) $slot['turf_id'];
    }
    if (!$totalPrice) {
        $totalPrice = (int) ($slot['price'] ?: 800);
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

    // Fetch turf details for response
    $stmt = $db->prepare('SELECT name, location, sport_type FROM turf_grounds WHERE id = ?');
    $stmt->execute([$turfId]);
    $turfDetails = $stmt->fetch() ?: [];

    $db->commit();

    jsonResponse([
        'success' => true,
        'message' => 'Turf booking confirmed successfully',
        'booking' => [
            'id'             => $bookingId,
            'trx_id'         => $trxId,
            'ref_id'         => $refId,
            'total_price'    => $totalPrice,
            'payment_method' => $paymentMethod,
            'status'         => 'confirmed',
            'turf_id'        => $turfId,
            'turf_name'      => $turfDetails['name'] ?? 'Turf Ground',
            'turf_location'  => $turfDetails['location'] ?? '',
            'sport_type'     => $turfDetails['sport_type'] ?? '',
            'slot_id'        => $slotId,
            'slot_date'      => $slotDate,
            'start_time'     => $startTime,
            'end_time'       => $endTime,
            'created_at'     => date('Y-m-d H:i:s')
        ]
    ]);
} catch (Exception $e) {
    $db->rollBack();
    jsonError('Booking failed: ' . $e->getMessage(), 500);
}

