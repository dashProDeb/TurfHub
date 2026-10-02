<?php
// create.php — POST: Generate fixture matches for a tournament
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$tournamentId = (int) ($input['tournament_id'] ?? 0);

if (!$tournamentId) {
    jsonError('tournament_id is required');
}

$db = Database::connect();

// Verify owner has access (through venue turf)
$stmt = $db->prepare(
    'SELECT t.id, t.turf_id FROM tournaments t
     JOIN turf_grounds tg ON tg.id = t.turf_id
     WHERE t.id = ? AND tg.owner_id = ?'
);
$stmt->execute([$tournamentId, $user['id']]);
$tournament = $stmt->fetch();

if (!$tournament) {
    jsonError('Tournament not found or no access', 403);
}

// Get approved teams
$stmt = $db->prepare(
    'SELECT tt.team_id FROM tournament_teams tt
     WHERE tt.tournament_id = ? AND tt.status = "approved"'
);
$stmt->execute([$tournamentId]);
$teams = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (count($teams) < 2) {
    jsonError('At least 2 approved teams are needed to generate fixtures');
}

// Round-robin fixture generation
$fixtures = [];
$roundNum = 1;

for ($i = 0; $i < count($teams); $i++) {
    for ($j = $i + 1; $j < count($teams); $j++) {
        $fixtures[] = [
            'home_team_id' => $teams[$i],
            'away_team_id' => $teams[$j],
            'round_name'   => "Round $roundNum"
        ];
        $roundNum++;
    }
}

// Insert fixtures
$stmt = $db->prepare(
    'INSERT INTO fixtures (tournament_id, home_team_id, away_team_id, venue_id, round_name, status)
     VALUES (?, ?, ?, ?, ?, "upcoming")'
);

foreach ($fixtures as $fix) {
    $stmt->execute([
        $tournamentId,
        $fix['home_team_id'],
        $fix['away_team_id'],
        $tournament['turf_id'],
        $fix['round_name']
    ]);
}

// Update tournament status to in_progress
$stmt = $db->prepare('UPDATE tournaments SET status = "in_progress" WHERE id = ?');
$stmt->execute([$tournamentId]);

jsonResponse(['success' => true, 'fixtures_created' => count($fixtures)]);
