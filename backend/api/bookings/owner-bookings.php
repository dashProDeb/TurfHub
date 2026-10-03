<?php
// owner-bookings.php — GET: Bookings on owner's turfs
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT b.*, u.full_name AS customer_name, u.phone AS customer_phone, u.email AS customer_email,
            tg.name AS turf_name, tg.sport_type, tg.location, tg.district,
            ts.slot_date, ts.start_time, ts.end_time
     FROM bookings b
     JOIN users u ON u.id = b.user_id
     JOIN turf_grounds tg ON tg.id = b.turf_id
     LEFT JOIN turf_slots ts ON ts.id = b.slot_id
     WHERE tg.owner_id = ?
     ORDER BY b.created_at DESC'
);
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();

jsonResponse(['bookings' => $bookings]);

