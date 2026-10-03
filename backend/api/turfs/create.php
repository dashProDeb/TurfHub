<?php
// create.php — POST: Create new turf ground
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['name']) || empty($input['location']) || empty($input['sport_type']) || empty($input['price_per_hour'])) {
    jsonError('name, location, sport_type, and price_per_hour are required');
}

$db = Database::connect();
$stmt = $db->prepare(
    'INSERT INTO turf_grounds (owner_id, name, location, district, sport_type, price_per_hour, surface_type, field_size, amenities)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([
    $user['id'],
    $input['name'],
    $input['location'],
    $input['district'] ?? null,
    $input['sport_type'],
    (int) $input['price_per_hour'],
    $input['surface_type'] ?? null,
    $input['field_size'] ?? null,
    json_encode($input['amenities'] ?? [])
]);

$turfId = (int) $db->lastInsertId();

jsonResponse(['success' => true, 'turf_id' => $turfId]);
