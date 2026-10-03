<?php
// delete.php — POST: Delete a match fixture
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner', 'admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$fixtureId = (int) ($input['fixture_id'] ?? 0);

if (!$fixtureId) {
    jsonError('fixture_id is required');
}

$db = Database::connect();

// Verify owner has access to this fixture
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

$stmt = $db->prepare('DELETE FROM fixtures WHERE id = ?');
$stmt->execute([$fixtureId]);

jsonResponse(['success' => true, 'message' => 'Fixture deleted successfully']);
