<?php
// list.php — GET: Match fixtures with team names, scores, and venue info
require_once __DIR__ . '/../config/helpers.php';

$db = Database::connect();

$tournamentId = (int) ($_GET['tournament_id'] ?? 0);
$status       = $_GET['status'] ?? null;
$teamId       = (int) ($_GET['team_id'] ?? 0);
$limit        = (int) ($_GET['limit'] ?? 50);
if ($limit <= 0 || $limit > 100) $limit = 50;

$sql = 'SELECT f.*,
               t.name AS tournament_name, t.sport AS sport_type,
               ht.name AS home_team_name, ht.logo_url AS home_team_logo,
               at.name AS away_team_name, at.logo_url AS away_team_logo,
               tg.name AS venue_name, tg.location AS venue_location
        FROM fixtures f
        JOIN tournaments t ON t.id = f.tournament_id
        LEFT JOIN teams ht ON ht.id = f.home_team_id
        LEFT JOIN teams at ON at.id = f.away_team_id
        LEFT JOIN turf_grounds tg ON tg.id = f.venue_id
        WHERE 1=1';
$params = [];

if ($tournamentId) {
    $sql .= ' AND f.tournament_id = ?';
    $params[] = $tournamentId;
}
if ($status && $status !== 'all') {
    $sql .= ' AND f.status = ?';
    $params[] = $status;
}
if ($teamId) {
    $sql .= ' AND (f.home_team_id = ? OR f.away_team_id = ?)';
    $params[] = $teamId;
    $params[] = $teamId;
}

if ($status === 'completed') {
    $sql .= ' ORDER BY f.match_date DESC, f.id DESC';
} else {
    $sql .= ' ORDER BY f.match_date ASC, f.id ASC';
}

$sql .= " LIMIT $limit";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$fixtures = $stmt->fetchAll();

// Decode scorers JSON
foreach ($fixtures as &$f) {
    if (is_string($f['scorers'])) {
        $decoded = json_decode($f['scorers'], true);
        $f['scorers'] = is_array($decoded) ? $decoded : [];
    } else if (!is_array($f['scorers'])) {
        $f['scorers'] = [];
    }
}

jsonResponse(['fixtures' => $fixtures]);
