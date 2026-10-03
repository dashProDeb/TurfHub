<?php
// remove-member.php — POST: Remove player from team
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('captain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input    = json_decode(file_get_contents('php://input'), true);
$memberId = (int) ($input['member_id'] ?? 0);

if (!$memberId) {
    jsonError('member_id is required');
}

$db = Database::connect();

// Verify captain owns the team this member belongs to
$stmt = $db->prepare(
    'SELECT tm.id, tm.player_id, t.captain_id FROM team_members tm
     JOIN teams t ON t.id = tm.team_id
     WHERE tm.id = ? AND t.captain_id = ?'
);
$stmt->execute([$memberId, $user['id']]);
$member = $stmt->fetch();

if (!$member) {
    jsonError('Member not found or not on your team', 403);
}

// Prevent captain from removing themselves
if ((int)$member['player_id'] === $user['id']) {
    jsonError('Cannot remove yourself (captain) from the team');
}

$stmt = $db->prepare('DELETE FROM team_members WHERE id = ?');
$stmt->execute([$memberId]);

jsonResponse(['success' => true]);
