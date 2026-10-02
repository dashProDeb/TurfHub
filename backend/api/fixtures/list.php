<?php
// list.php — GET: Match fixtures for tournament
require_once __DIR__ . '/../config/helpers.php';

$tournamentId = (int) ($_GET['tournament_id'] ?? 0);
if (!$tournamentId) {
    jsonError('tournament_id is required');
}

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT f.*,
            ht.name AS home_team_name, ht.logo_url AS home_team_logo,
            at.name AS away_team_name, at.logo_url AS away_team_logo,
            tg.name AS venue_name
     FROM fixtures f
     LEFT JOIN teams ht ON ht.id = f.home_team_id
     LEFT JOIN teams at ON at.id = f.away_team_id
     LEFT JOIN turf_grounds tg ON tg.id = f.venue_id
     WHERE f.tournament_id = ?
     ORDER BY f.match_date ASC, f.round_name ASC'
);
$stmt->execute([$tournamentId]);
$fixtures = $stmt->fetchAll();

// Decode scorers JSON
foreach ($fixtures as &$f) {
    $f['scorers'] = json_decode($f['scorers'] ?? '[]', true);
}

jsonResponse(['fixtures' => $fixtures]);
