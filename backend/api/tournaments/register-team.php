<?php
// register-team.php — POST: Register team for tournament
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('captain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$tournamentId = (int) ($input['tournament_id'] ?? 0);
$teamId       = (int) ($input['team_id'] ?? 0);

if (!$tournamentId || !$teamId) {
    jsonError('tournament_id and team_id are required');
}

$db = Database::connect();

// Verify captain owns the team
$stmt = $db->prepare('SELECT id FROM teams WHERE id = ? AND captain_id = ?');
$stmt->execute([$teamId, $user['id']]);
if (!$stmt->fetch()) {
    jsonError('You are not the captain of this team', 403);
}

// Check if already registered
$stmt = $db->prepare('SELECT id FROM tournament_teams WHERE tournament_id = ? AND team_id = ?');
$stmt->execute([$tournamentId, $teamId]);
if ($stmt->fetch()) {
    jsonError('Team already registered', 409);
}

// Check max teams not exceeded
$stmt = $db->prepare(
    'SELECT t.max_teams, (SELECT COUNT(*) FROM tournament_teams tt WHERE tt.tournament_id = t.id) AS current_teams
     FROM tournaments t WHERE t.id = ?'
);
$stmt->execute([$tournamentId]);
$tourn = $stmt->fetch();

if (!$tourn) {
    jsonError('Tournament not found', 404);
}

if ((int)$tourn['current_teams'] >= (int)$tourn['max_teams']) {
    jsonError('Tournament is full');
}

$stmt = $db->prepare(
    'INSERT INTO tournament_teams (tournament_id, team_id, status) VALUES (?, ?, "pending")'
);
$stmt->execute([$tournamentId, $teamId]);

jsonResponse(['success' => true, 'message' => 'Registration submitted']);
