<?php
// detail.php — GET: Single turf ground details with owner info and active slots
require_once __DIR__ . '/../config/helpers.php';

$turfId = (int) ($_GET['id'] ?? $_GET['turf_id'] ?? 0);
if (!$turfId) {
    jsonError('id or turf_id is required');
}

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT tg.*, u.full_name AS owner_name, u.phone AS owner_phone, u.email AS owner_email
     FROM turf_grounds tg
     JOIN users u ON u.id = tg.owner_id
     WHERE tg.id = ?'
);
$stmt->execute([$turfId]);
$turf = $stmt->fetch();

if (!$turf) {
    jsonError('Turf ground not found', 404);
}

// Decode JSON fields
$turf['amenities'] = json_decode($turf['amenities'] ?? '[]', true);
$turf['photos']    = json_decode($turf['photos'] ?? '[]', true);

// Fetch next 7 days available slots
$stmt = $db->prepare(
    'SELECT * FROM turf_slots
     WHERE turf_id = ? AND slot_date >= CURDATE()
     ORDER BY slot_date ASC, start_time ASC'
);
$stmt->execute([$turfId]);
$turf['slots'] = $stmt->fetchAll();

jsonResponse(['turf' => $turf]);
