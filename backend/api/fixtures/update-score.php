<?php
// update-score.php — POST: Update match score + scorers
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner', 'admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input     = json_decode(file_get_contents('php://input'), true);
$fixtureId = (int) ($input['fixture_id'] ?? 0);

if (!$fixtureId) {
    jsonError('fixture_id is required');
}

$db = Database::connect();

// Verify owner has access to this fixture's tournament or venue
$stmt = $db->prepare(
    'SELECT f.id FROM fixtures f
     JOIN tournaments t ON t.id = f.tournament_id
     LEFT JOIN turf_grounds tg ON tg.id = t.turf_id
     WHERE f.id = ? AND (tg.owner_id = ? OR t.created_by = ? OR f.venue_id IN (SELECT id FROM turf_grounds WHERE owner_id = ?))'
);
$stmt->execute([$fixtureId, $user['id'], $user['id'], $user['id']]);
if (!$stmt->fetch() && $user['role'] !== 'admin') {
    jsonError('No access to this fixture', 403);
}

// Build dynamic UPDATE
$updates = [];
$params  = [];

if (isset($input['home_score'])) {
    $updates[] = 'home_score = ?';
    $params[]  = (int) $input['home_score'];
}
if (isset($input['away_score'])) {
    $updates[] = 'away_score = ?';
    $params[]  = (int) $input['away_score'];
}
if (isset($input['status'])) {
    $status = $input['status'];
    if (!in_array($status, ['upcoming', 'live', 'completed'])) {
        jsonError('Invalid status value (upcoming, live, completed)');
    }
    $updates[] = 'status = ?';
    $params[]  = $status;
}
if (isset($input['scorers'])) {
    $updates[] = 'scorers = ?';
    $params[]  = is_string($input['scorers']) ? $input['scorers'] : json_encode($input['scorers']);
}
if (isset($input['match_date'])) {
    $updates[] = 'match_date = ?';
    $params[]  = $input['match_date'] ?: null;
}
if (isset($input['round_name'])) {
    $updates[] = 'round_name = ?';
    $params[]  = $input['round_name'];
}
if (isset($input['home_team_id'])) {
    $updates[] = 'home_team_id = ?';
    $params[]  = (int) $input['home_team_id'] ?: null;
}
if (isset($input['away_team_id'])) {
    $updates[] = 'away_team_id = ?';
    $params[]  = (int) $input['away_team_id'] ?: null;
}

if (empty($updates)) {
    jsonError('No fields to update');
}

$params[] = $fixtureId;
$sql = 'UPDATE fixtures SET ' . implode(', ', $updates) . ' WHERE id = ?';
$stmt = $db->prepare($sql);
$stmt->execute($params);

jsonResponse(['success' => true, 'message' => 'Score and match details updated successfully']);
