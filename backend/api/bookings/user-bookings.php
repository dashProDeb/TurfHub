<?php
// user-bookings.php — GET: Current user's booking receipts
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain', 'owner', 'admin');

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT b.*, 
            COALESCE(tg.name, "Turf Ground") AS turf_name, 
            COALESCE(tg.location, "Dhaka") AS turf_location, 
            COALESCE(tg.sport_type, "Sports") AS sport_type,
            COALESCE(ts.slot_date, DATE(b.created_at)) AS slot_date, 
            COALESCE(ts.start_time, "18:00:00") AS start_time, 
            COALESCE(ts.end_time, "19:00:00") AS end_time
     FROM bookings b
     LEFT JOIN turf_grounds tg ON tg.id = b.turf_id
     LEFT JOIN turf_slots ts ON ts.id = b.slot_id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC'
);
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();

jsonResponse(['bookings' => $bookings]);

