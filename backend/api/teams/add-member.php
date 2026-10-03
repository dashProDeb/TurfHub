<?php
// add-member.php — POST: Add player to squad
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('captain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input    = json_decode(file_get_contents('php://input'), true);
$teamId   = (int) ($input['team_id'] ?? 0);
$playerId = (int) ($input['player_id'] ?? 0);

if (!$teamId || !$playerId) {
    jsonError('team_id and player_id are required');
}

$db = Database::connect();

// Verify captain owns the team
$stmt = $db->prepare('SELECT id FROM teams WHERE id = ? AND captain_id = ?');
$stmt->execute([$teamId, $user['id']]);
if (!$stmt->fetch()) {
    jsonError('You are not the captain of this team', 403);
}

// Check if already a member
$stmt = $db->prepare('SELECT id FROM team_members WHERE team_id = ? AND player_id = ?');
$stmt->execute([$teamId, $playerId]);
if ($stmt->fetch()) {
    jsonError('Player is already a member of this team', 409);
}

$stmt = $db->prepare(
    'INSERT INTO team_members (team_id, player_id, jersey_number, position, status)
     VALUES (?, ?, ?, ?, "active")'
);
$stmt->execute([
    $teamId,
    $playerId,
    $input['jersey_number'] ?? null,
    $input['position'] ?? null
]);

jsonResponse(['success' => true, 'member_id' => (int) $db->lastInsertId()]);
