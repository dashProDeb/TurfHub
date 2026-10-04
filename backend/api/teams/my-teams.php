<?php
// my-teams.php — GET: All teams managed by authenticated captain (or where user is a member)
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('captain', 'player', 'admin', 'owner');
$db = Database::connect();

$stmt = $db->prepare('
    SELECT t.*, 
           (SELECT COUNT(*) FROM team_members tm WHERE tm.team_id = t.id AND tm.status = "active") AS active_members,
           (SELECT COUNT(*) FROM tournament_teams tt WHERE tt.team_id = t.id) AS registered_tournaments
    FROM teams t 
    WHERE t.captain_id = ? 
    ORDER BY t.id ASC
');
$stmt->execute([$user['id']]);
$teams = $stmt->fetchAll();

jsonResponse(['teams' => $teams]);
