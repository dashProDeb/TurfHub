<?php
// create.php — POST: Create single match fixture OR generate round-robin schedule
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

// Verify owner has access (through venue turf or creator)
$stmt = $db->prepare(
    'SELECT t.id, t.turf_id FROM tournaments t
     LEFT JOIN turf_grounds tg ON tg.id = t.turf_id
     WHERE t.id = ? AND (tg.owner_id = ? OR t.created_by = ?)'
);
$stmt->execute([$tournamentId, $user['id'], $user['id']]);
$tournament = $stmt->fetch();

if (!$tournament && $user['role'] !== 'admin') {
    jsonError('Tournament not found or no access', 403);
}

$venueId = !empty($input['venue_id']) ? (int) $input['venue_id'] : ($tournament['turf_id'] ?? null);

// Case 1: Manual Single Match Fixture Creation
if (!empty($input['home_team_id']) && !empty($input['away_team_id'])) {
    $homeId = (int) $input['home_team_id'];
    $awayId = (int) $input['away_team_id'];

    if ($homeId === $awayId) {
        jsonError('Home and Away teams must be different');
    }

    $roundName = trim($input['round_name'] ?? 'Group Match');
    $matchDate = !empty($input['match_date']) ? $input['match_date'] : null;

    $stmt = $db->prepare(
        'INSERT INTO fixtures (tournament_id, home_team_id, away_team_id, venue_id, match_date, round_name, status)
         VALUES (?, ?, ?, ?, ?, ?, "upcoming")'
    );
    $stmt->execute([
        $tournamentId,
        $homeId,
        $awayId,
        $venueId,
        $matchDate,
        $roundName
    ]);

    $fixtureId = (int) $db->lastInsertId();

    // Ensure tournament status is in_progress if it was draft or open
    $db->prepare('UPDATE tournaments SET status = "in_progress" WHERE id = ? AND status IN ("draft", "open")')
       ->execute([$tournamentId]);

    jsonResponse([
        'success'    => true,
        'fixture_id' => $fixtureId,
        'message'    => 'Match fixture scheduled successfully'
    ]);
}

// Case 2: Auto Round-Robin Generation for Approved Teams
$stmt = $db->prepare(
    'SELECT tt.team_id FROM tournament_teams tt
     WHERE tt.tournament_id = ? AND tt.status = "approved"'
);
$stmt->execute([$tournamentId]);
$teams = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (count($teams) < 2) {
    jsonError('At least 2 approved teams are required to generate tournament schedule');
}

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

$stmt = $db->prepare(
    'INSERT INTO fixtures (tournament_id, home_team_id, away_team_id, venue_id, round_name, status)
     VALUES (?, ?, ?, ?, ?, "upcoming")'
);

foreach ($fixtures as $fix) {
    $stmt->execute([
        $tournamentId,
        $fix['home_team_id'],
        $fix['away_team_id'],
        $venueId,
        $fix['round_name']
    ]);
}

// Update tournament status to in_progress
$stmt = $db->prepare('UPDATE tournaments SET status = "in_progress" WHERE id = ?');
$stmt->execute([$tournamentId]);

jsonResponse([
    'success'          => true,
    'fixtures_created' => count($fixtures),
    'message'          => count($fixtures) . ' round-robin fixtures generated successfully'
]);
