<?php
// user-bookings.php — GET: Current user's booking receipts
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain');

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT b.*, tg.name AS turf_name, tg.location AS turf_location, tg.sport_type,
            ts.slot_date, ts.start_time, ts.end_time
     FROM bookings b
     JOIN turf_grounds tg ON tg.id = b.turf_id
     JOIN turf_slots ts ON ts.id = b.slot_id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC'
);
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();

jsonResponse(['bookings' => $bookings]);
