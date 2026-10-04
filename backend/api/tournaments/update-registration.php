<?php
// update-registration.php — POST: Approve/reject team entry
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner', 'captain', 'admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input          = json_decode(file_get_contents('php://input'), true);
$registrationId = (int) ($input['registration_id'] ?? 0);
$status         = $input['status'] ?? '';

if (!$registrationId || !in_array($status, ['approved', 'rejected'])) {
    jsonError('registration_id and valid status (approved/rejected) are required');
}

$db = Database::connect();

// Verify owner has access to this tournament (through venue turf or creator)
$stmt = $db->prepare(
    'SELECT tt.id FROM tournament_teams tt
     JOIN tournaments t ON t.id = tt.tournament_id
     LEFT JOIN turf_grounds tg ON tg.id = t.turf_id
     WHERE tt.id = ? AND (tg.owner_id = ? OR t.created_by = ?)'
);
$stmt->execute([$registrationId, $user['id'], $user['id']]);
if (!$stmt->fetch()) {
    jsonError('Registration not found or no access', 403);
}

$stmt = $db->prepare('UPDATE tournament_teams SET status = ? WHERE id = ?');
$stmt->execute([$status, $registrationId]);

jsonResponse(['success' => true, 'message' => "Registration $status"]);
