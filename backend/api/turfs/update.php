<?php
// update.php — POST: Update turf details
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$turfId = (int) ($input['turf_id'] ?? 0);

if (!$turfId) {
    jsonError('turf_id is required');
}

$db = Database::connect();

// Verify ownership
$stmt = $db->prepare('SELECT id FROM turf_grounds WHERE id = ? AND owner_id = ?');
$stmt->execute([$turfId, $user['id']]);
if (!$stmt->fetch()) {
    jsonError('Turf not found or not owned by you', 403);
}

// Build dynamic update
$updates = [];
$params  = [];

$fields = ['name', 'location', 'district', 'sport_type', 'surface_type', 'field_size'];
foreach ($fields as $field) {
    if (isset($input[$field])) {
        $updates[] = "$field = ?";
        $params[]  = $input[$field];
    }
}
if (isset($input['price_per_hour'])) {
    $updates[] = 'price_per_hour = ?';
    $params[]  = (int) $input['price_per_hour'];
}
if (isset($input['amenities'])) {
    $updates[] = 'amenities = ?';
    $params[]  = json_encode($input['amenities']);
}

if (empty($updates)) {
    jsonError('No fields to update');
}

$params[] = $turfId;
$sql = 'UPDATE turf_grounds SET ' . implode(', ', $updates) . ' WHERE id = ?';
$stmt = $db->prepare($sql);
$stmt->execute($params);

jsonResponse(['success' => true]);
