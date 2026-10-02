<?php
// detail.php — GET: Team with roster members
require_once __DIR__ . '/../config/helpers.php';

$teamId = (int) ($_GET['team_id'] ?? 0);
if (!$teamId) {
    jsonError('team_id is required');
}

$db = Database::connect();

// Get team
$stmt = $db->prepare('SELECT t.*, u.full_name AS captain_name FROM teams t JOIN users u ON u.id = t.captain_id WHERE t.id = ?');
$stmt->execute([$teamId]);
$team = $stmt->fetch();

if (!$team) {
    jsonError('Team not found', 404);
}

// Get members
$stmt = $db->prepare(
    'SELECT tm.*, u.full_name, u.avatar_url, u.email
     FROM team_members tm
     JOIN users u ON u.id = tm.player_id
     WHERE tm.team_id = ?
     ORDER BY tm.jersey_number ASC'
);
$stmt->execute([$teamId]);
$team['members'] = $stmt->fetchAll();

jsonResponse(['team' => $team]);
