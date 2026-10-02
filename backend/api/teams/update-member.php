<?php
// update-member.php — POST: Update jersey / position / status
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
    'SELECT tm.id FROM team_members tm
     JOIN teams t ON t.id = tm.team_id
     WHERE tm.id = ? AND t.captain_id = ?'
);
$stmt->execute([$memberId, $user['id']]);
if (!$stmt->fetch()) {
    jsonError('Member not found or not on your team', 403);
}

$updates = [];
$params  = [];

if (isset($input['jersey_number'])) {
    $updates[] = 'jersey_number = ?';
    $params[]  = (int) $input['jersey_number'];
}
if (isset($input['position'])) {
    $updates[] = 'position = ?';
    $params[]  = $input['position'];
}
if (isset($input['status'])) {
    $updates[] = 'status = ?';
    $params[]  = $input['status'];
}

if (empty($updates)) {
    jsonError('No fields to update');
}

$params[] = $memberId;
$sql = 'UPDATE team_members SET ' . implode(', ', $updates) . ' WHERE id = ?';
$stmt = $db->prepare($sql);
$stmt->execute($params);

jsonResponse(['success' => true]);
