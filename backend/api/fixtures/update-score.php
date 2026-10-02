<?php
// update-score.php — POST: Update match score + scorers
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input     = json_decode(file_get_contents('php://input'), true);
$fixtureId = (int) ($input['fixture_id'] ?? 0);

if (!$fixtureId) {
    jsonError('fixture_id is required');
}

$db = Database::connect();

// Verify owner has access to this fixture's tournament venue
$stmt = $db->prepare(
    'SELECT f.id FROM fixtures f
     JOIN tournaments t ON t.id = f.tournament_id
     JOIN turf_grounds tg ON tg.id = t.turf_id
     WHERE f.id = ? AND tg.owner_id = ?'
);
$stmt->execute([$fixtureId, $user['id']]);
if (!$stmt->fetch()) {
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
    $updates[] = 'status = ?';
    $params[]  = $input['status'];
}
if (isset($input['scorers'])) {
    $updates[] = 'scorers = ?';
    $params[]  = json_encode($input['scorers']);
}
if (isset($input['match_date'])) {
    $updates[] = 'match_date = ?';
    $params[]  = $input['match_date'];
}

if (empty($updates)) {
    jsonError('No fields to update');
}

$params[] = $fixtureId;
$sql = 'UPDATE fixtures SET ' . implode(', ', $updates) . ' WHERE id = ?';
$stmt = $db->prepare($sql);
$stmt->execute($params);

jsonResponse(['success' => true]);
