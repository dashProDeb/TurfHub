<?php
// update.php — POST: Update tournament details or status
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner', 'captain', 'admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$tournamentId = (int) ($input['tournament_id'] ?? 0);

if (!$tournamentId) {
    jsonError('tournament_id is required');
}

$db = Database::connect();

// Verify user owns the tournament or the turf where it is hosted
$stmt = $db->prepare(
    'SELECT t.id FROM tournaments t
     LEFT JOIN turf_grounds tg ON tg.id = t.turf_id
     WHERE t.id = ? AND (t.created_by = ? OR tg.owner_id = ?)'
);
$stmt->execute([$tournamentId, $user['id'], $user['id']]);
if (!$stmt->fetch()) {
    jsonError('Tournament not found or no permission to edit', 403);
}

$updates = [];
$params  = [];

if (isset($input['name']) && trim($input['name']) !== '') {
    $updates[] = 'name = ?';
    $params[]  = trim($input['name']);
}
if (isset($input['sport'])) {
    $updates[] = 'sport = ?';
    $params[]  = trim($input['sport']);
}
if (isset($input['turf_id'])) {
    $updates[] = 'turf_id = ?';
    $params[]  = (int) $input['turf_id'] ?: null;
}
if (isset($input['entry_fee'])) {
    $updates[] = 'entry_fee = ?';
    $params[]  = (int) $input['entry_fee'];
}
if (isset($input['prize_pool'])) {
    $updates[] = 'prize_pool = ?';
    $params[]  = (int) $input['prize_pool'];
}
if (isset($input['max_teams'])) {
    $updates[] = 'max_teams = ?';
    $params[]  = (int) $input['max_teams'];
}
if (isset($input['start_date'])) {
    $updates[] = 'start_date = ?';
    $params[]  = $input['start_date'] ?: null;
}
if (isset($input['end_date'])) {
    $updates[] = 'end_date = ?';
    $params[]  = $input['end_date'] ?: null;
}
if (isset($input['rules'])) {
    $updates[] = 'rules = ?';
    $params[]  = $input['rules'] ?: null;
}
if (isset($input['status'])) {
    $status = $input['status'];
    if (!in_array($status, ['draft', 'open', 'in_progress', 'completed'])) {
        jsonError('Invalid status value');
    }
    $updates[] = 'status = ?';
    $params[]  = $status;
}

if (empty($updates)) {
    jsonError('No fields to update');
}

$params[] = $tournamentId;
$sql = 'UPDATE tournaments SET ' . implode(', ', $updates) . ' WHERE id = ?';
$stmt = $db->prepare($sql);
$stmt->execute($params);

jsonResponse(['success' => true, 'message' => 'Tournament updated successfully']);
