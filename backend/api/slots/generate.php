<?php
// generate.php — POST: Generate hourly slots for a day (Owner)
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$turfId = (int) ($input['turf_id'] ?? 0);
$date   = $input['date'] ?? '';
$price  = (int) ($input['price_per_hour'] ?? 0);

if (!$turfId || !$date || !$price) {
    jsonError('turf_id, date, and price_per_hour are required');
}

// Verify ownership
$db = Database::connect();
$stmt = $db->prepare('SELECT id FROM turf_grounds WHERE id = ? AND owner_id = ?');
$stmt->execute([$turfId, $user['id']]);
if (!$stmt->fetch()) {
    jsonError('Turf not owned by you', 403);
}

// Generate slots from 06:00 to 23:00 (INSERT IGNORE to skip duplicates)
$stmt = $db->prepare(
    'INSERT IGNORE INTO turf_slots (turf_id, slot_date, start_time, end_time, status, price)
     VALUES (?, ?, ?, ?, "available", ?)'
);

$generated = 0;
for ($h = 6; $h <= 23; $h++) {
    $start = sprintf('%02d:00:00', $h);
    $end   = sprintf('%02d:00:00', $h + 1);
    $stmt->execute([$turfId, $date, $start, $end, $price]);
    $generated += $stmt->rowCount();
}

jsonResponse(['success' => true, 'message' => "Generated $generated slots for $date"]);
