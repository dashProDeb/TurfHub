<?php
// rsvp.php — POST: Submit player match attendance RSVP
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$status = $input['status'] ?? 'active'; // 'active' (Attending), 'bench', 'unavailable'
$teamId = (int) ($input['team_id'] ?? 0);

$db = Database::connect();

if ($teamId) {
    $stmt = $db->prepare('UPDATE team_members SET status = ? WHERE team_id = ? AND player_id = ?');
    $stmt->execute([$status, $teamId, $user['id']]);
} else {
    $stmt = $db->prepare('UPDATE team_members SET status = ? WHERE player_id = ?');
    $stmt->execute([$status, $user['id']]);
}

jsonResponse([
    'success' => true,
    'message' => 'RSVP status updated to ' . $status,
    'status'  => $status
]);
