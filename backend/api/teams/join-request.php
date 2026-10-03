<?php
// join-request.php — POST: Player requests to join team
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input  = json_decode(file_get_contents('php://input'), true);
$teamId = (int) ($input['team_id'] ?? 0);

if (!$teamId) {
    jsonError('team_id is required');
}

$db = Database::connect();

// Check if already a member
$stmt = $db->prepare('SELECT id FROM team_members WHERE team_id = ? AND player_id = ?');
$stmt->execute([$teamId, $user['id']]);
if ($stmt->fetch()) {
    jsonError('Already a member or pending request', 409);
}

$stmt = $db->prepare(
    'INSERT INTO team_members (team_id, player_id, status) VALUES (?, ?, "pending")'
);
$stmt->execute([$teamId, $user['id']]);

jsonResponse(['success' => true, 'message' => 'Join request sent']);
