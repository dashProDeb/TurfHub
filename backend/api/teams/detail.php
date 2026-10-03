<?php
// detail.php — GET: Team with roster members
require_once __DIR__ . '/../config/helpers.php';

$teamId = (int) ($_GET['team_id'] ?? 0);
$db = Database::connect();

// If no team_id provided, check session user's team
if (!$teamId) {
    $userId = $_SESSION['user_id'] ?? 0;
    if ($userId) {
        // Check if captain of a team
        $stmt = $db->prepare('SELECT id FROM teams WHERE captain_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if ($row) {
            $teamId = (int) $row['id'];
        } else {
            // Check if member of a team
            $stmt = $db->prepare('SELECT team_id FROM team_members WHERE player_id = ? LIMIT 1');
            $stmt->execute([$userId]);
            $row = $stmt->fetch();
            if ($row) {
                $teamId = (int) $row['team_id'];
            }
        }
    }
}

// Fallback to first active team if still no teamId found
if (!$teamId) {
    $stmt = $db->query('SELECT id FROM teams ORDER BY id ASC LIMIT 1');
    $row = $stmt->fetch();
    if ($row) {
        $teamId = (int) $row['id'];
    }
}

if (!$teamId) {
    jsonResponse(['team' => null, 'message' => 'No team found']);
}

// Get team
$stmt = $db->prepare(
    'SELECT t.*, u.full_name AS captain_name, u.phone AS captain_phone
     FROM teams t
     JOIN users u ON u.id = t.captain_id
     WHERE t.id = ?'
);
$stmt->execute([$teamId]);
$team = $stmt->fetch();

if (!$team) {
    jsonError('Team not found', 404);
}

// Get members
$stmt = $db->prepare(
    'SELECT tm.*, u.full_name, u.avatar_url, u.email, u.phone
     FROM team_members tm
     JOIN users u ON u.id = tm.player_id
     WHERE tm.team_id = ?
     ORDER BY tm.jersey_number ASC'
);
$stmt->execute([$teamId]);
$team['members'] = $stmt->fetchAll();

jsonResponse(['team' => $team]);
