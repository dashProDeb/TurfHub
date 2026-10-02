<?php
// create.php — POST: Create team
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('captain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['name']) || empty($input['sport'])) {
    jsonError('Team name and sport are required');
}

$db = Database::connect();
$stmt = $db->prepare(
    'INSERT INTO teams (captain_id, name, sport, logo_url, formation, status)
     VALUES (?, ?, ?, ?, ?, ?)'
);
$stmt->execute([
    $user['id'],
    $input['name'],
    $input['sport'],
    $input['logo_url'] ?? null,
    $input['formation'] ?? '4-3-3',
    $input['status'] ?? 'active'
]);

$teamId = (int) $db->lastInsertId();

// Auto-add captain as a member
$stmt = $db->prepare(
    'INSERT INTO team_members (team_id, player_id, jersey_number, position, status)
     VALUES (?, ?, 10, "Captain", "active")'
);
$stmt->execute([$teamId, $user['id']]);

jsonResponse(['success' => true, 'team_id' => $teamId]);
