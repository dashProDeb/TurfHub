<?php
// list.php — GET: Tournaments with optional filters
require_once __DIR__ . '/../config/helpers.php';

$db = Database::connect();

$status = $_GET['status'] ?? 'all';
$sport  = $_GET['sport'] ?? null;
$turfId = (int) ($_GET['turf_id'] ?? 0);
$userId = (int) ($_GET['created_by'] ?? 0);

$sql = 'SELECT t.*, tg.name AS venue_name, tg.location AS venue_location,
               u.full_name AS organizer_name,
               (SELECT COUNT(*) FROM tournament_teams tt WHERE tt.tournament_id = t.id) AS registered_teams
        FROM tournaments t
        LEFT JOIN turf_grounds tg ON tg.id = t.turf_id
        LEFT JOIN users u ON u.id = t.created_by
        WHERE 1=1';
$params = [];

if ($status !== 'all' && $status !== '') {
    $sql .= ' AND t.status = ?';
    $params[] = $status;
}
if ($sport) {
    $sql .= ' AND t.sport = ?';
    $params[] = $sport;
}
if ($turfId) {
    $sql .= ' AND t.turf_id = ?';
    $params[] = $turfId;
}
if ($userId) {
    $sql .= ' AND t.created_by = ?';
    $params[] = $userId;
}

$sql .= ' ORDER BY t.created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$tournaments = $stmt->fetchAll();

jsonResponse(['tournaments' => $tournaments]);
