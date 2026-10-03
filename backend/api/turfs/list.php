<?php
// list.php — GET: All verified turfs (public browsing)
require_once __DIR__ . '/../config/helpers.php';

$db = Database::connect();

$sport = $_GET['sport'] ?? null;
$district = $_GET['district'] ?? null;

$sql = 'SELECT * FROM turf_grounds WHERE is_verified = 1 AND status = "active"';
$params = [];

if ($sport) {
    $sql .= ' AND sport_type = ?';
    $params[] = $sport;
}
if ($district) {
    $sql .= ' AND district = ?';
    $params[] = $district;
}

$sql .= ' ORDER BY rating DESC, created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$turfs = $stmt->fetchAll();

// Decode JSON fields
foreach ($turfs as &$turf) {
    $turf['amenities'] = json_decode($turf['amenities'] ?? '[]', true);
    $turf['photos']    = json_decode($turf['photos'] ?? '[]', true);
}

jsonResponse(['turfs' => $turfs]);
