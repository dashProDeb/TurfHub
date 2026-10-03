<?php
// my-turfs.php — GET: Owner's turfs
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

$db = Database::connect();
$stmt = $db->prepare('SELECT * FROM turf_grounds WHERE owner_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$turfs = $stmt->fetchAll();

foreach ($turfs as &$turf) {
    $turf['amenities'] = json_decode($turf['amenities'] ?? '[]', true);
    $turf['photos']    = json_decode($turf['photos'] ?? '[]', true);
}

jsonResponse(['turfs' => $turfs]);
