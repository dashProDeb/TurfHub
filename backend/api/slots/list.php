<?php
// list.php — GET: Slots for turf on date range
require_once __DIR__ . '/../config/helpers.php';

$turfId   = (int) ($_GET['turf_id'] ?? 0);
$startDate = $_GET['start_date'] ?? date('Y-m-d');
$endDate   = $_GET['end_date'] ?? date('Y-m-d', strtotime('+6 days'));

if (!$turfId) {
    jsonError('turf_id is required');
}

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT ts.*, u.full_name AS booked_by_name
     FROM turf_slots ts
     LEFT JOIN users u ON u.id = ts.booked_by
     WHERE ts.turf_id = ? AND ts.slot_date BETWEEN ? AND ?
     ORDER BY ts.slot_date ASC, ts.start_time ASC'
);
$stmt->execute([$turfId, $startDate, $endDate]);
$slots = $stmt->fetchAll();

jsonResponse(['slots' => $slots]);
