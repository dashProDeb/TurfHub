<?php
// standings.php — GET: Computed league standings
require_once __DIR__ . '/../config/helpers.php';

$tournamentId = (int) ($_GET['tournament_id'] ?? 0);
if (!$tournamentId) {
    jsonError('tournament_id is required');
}

$db = Database::connect();

// Get all completed fixtures for this tournament
$stmt = $db->prepare(
    'SELECT home_team_id, away_team_id, home_score, away_score
     FROM fixtures
     WHERE tournament_id = ? AND status = "completed"'
);
$stmt->execute([$tournamentId]);
$matches = $stmt->fetchAll();

// Get all team names in this tournament
$stmt = $db->prepare(
    'SELECT t.id, t.name, t.logo_url FROM teams t
     JOIN tournament_teams tt ON tt.team_id = t.id
     WHERE tt.tournament_id = ? AND tt.status = "approved"'
);
$stmt->execute([$tournamentId]);
$teamsMap = [];
foreach ($stmt->fetchAll() as $team) {
    $teamsMap[$team['id']] = [
        'team_id'       => $team['id'],
        'team_name'     => $team['name'],
        'logo_url'      => $team['logo_url'],
        'played'        => 0,
        'won'           => 0,
        'drawn'         => 0,
        'lost'          => 0,
        'goals_for'     => 0,
        'goals_against' => 0,
        'goal_diff'     => 0,
        'points'        => 0
    ];
}

// Compute standings from match results
foreach ($matches as $m) {
    $homeId = $m['home_team_id'];
    $awayId = $m['away_team_id'];
    $hg = (int) $m['home_score'];
    $ag = (int) $m['away_score'];

    if (!isset($teamsMap[$homeId]) || !isset($teamsMap[$awayId])) continue;

    $teamsMap[$homeId]['played']++;
    $teamsMap[$homeId]['goals_for']     += $hg;
    $teamsMap[$homeId]['goals_against'] += $ag;

    $teamsMap[$awayId]['played']++;
    $teamsMap[$awayId]['goals_for']     += $ag;
    $teamsMap[$awayId]['goals_against'] += $hg;

    if ($hg > $ag) {
        $teamsMap[$homeId]['won']++;
        $teamsMap[$homeId]['points'] += 3;
        $teamsMap[$awayId]['lost']++;
    } elseif ($hg < $ag) {
        $teamsMap[$awayId]['won']++;
        $teamsMap[$awayId]['points'] += 3;
        $teamsMap[$homeId]['lost']++;
    } else {
        $teamsMap[$homeId]['drawn']++;
        $teamsMap[$homeId]['points'] += 1;
        $teamsMap[$awayId]['drawn']++;
        $teamsMap[$awayId]['points'] += 1;
    }
}

// Calculate goal difference & sort
$standings = array_values($teamsMap);
foreach ($standings as &$s) {
    $s['goal_diff'] = $s['goals_for'] - $s['goals_against'];
}
unset($s);

usort($standings, function($a, $b) {
    if ($b['points'] !== $a['points']) return $b['points'] - $a['points'];
    if ($b['goal_diff'] !== $a['goal_diff']) return $b['goal_diff'] - $a['goal_diff'];
    return $b['goals_for'] - $a['goals_for'];
});

jsonResponse(['standings' => $standings]);
