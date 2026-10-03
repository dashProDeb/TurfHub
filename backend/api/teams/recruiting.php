<?php
// recruiting.php — GET: Teams with 'recruiting' status
require_once __DIR__ . '/../config/helpers.php';

$db = Database::connect();
$sport = $_GET['sport'] ?? null;

$sql = 'SELECT t.*, u.full_name AS captain_name,
        (SELECT COUNT(*) FROM team_members tm WHERE tm.team_id = t.id AND tm.status = "active") AS member_count
        FROM teams t
        JOIN users u ON u.id = t.captain_id
        WHERE t.status = "recruiting"';
$params = [];

if ($sport) {
    $sql .= ' AND t.sport = ?';
    $params[] = $sport;
}

$sql .= ' ORDER BY t.created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$teams = $stmt->fetchAll();

jsonResponse(['teams' => $teams]);
