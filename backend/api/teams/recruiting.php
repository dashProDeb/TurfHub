<?php
// recruiting.php — GET: Teams with 'recruiting' status
require_once __DIR__ . '/../config/helpers.php';

$db = Database::connect();
$sport = $_GET['sport'] ?? null;
$userId = $_SESSION['user']['id'] ?? 0;

$sql = 'SELECT t.*, u.full_name AS captain_name,
        (SELECT COUNT(*) FROM team_members tm WHERE tm.team_id = t.id AND tm.status = "active") AS member_count,
        (SELECT status FROM team_members tm WHERE tm.team_id = t.id AND tm.player_id = ?) AS user_membership_status
        FROM teams t
        JOIN users u ON u.id = t.captain_id
        WHERE t.status = "recruiting"';
$params = [$userId];

if ($sport && strtolower($sport) !== 'all' && strtolower($sport) !== 'all sports') {
    $sql .= ' AND LOWER(t.sport) = LOWER(?)';
    $params[] = $sport;
}

$sql .= ' ORDER BY t.created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$teams = $stmt->fetchAll();

jsonResponse(['teams' => $teams]);

