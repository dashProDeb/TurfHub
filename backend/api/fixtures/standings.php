<?php
// standings.php — GET: Computed league standings
require_once __DIR__ . '/../config/helpers.php';

$tournamentId = (int) ($_GET['tournament_id'] ?? 0);
$db = Database::connect();

// If no tournament_id provided, pick first active tournament
if (!$tournamentId) {
    $stmt = $db->query('SELECT id FROM tournaments ORDER BY id ASC LIMIT 1');
    $row = $stmt->fetch();
    if ($row) {
        $tournamentId = (int) $row['id'];
    }
}

$teamsMap = [];

if ($tournamentId) {
    // Get all registered teams
    $stmt = $db->prepare(
        'SELECT t.id, t.name, t.logo_url FROM teams t
         JOIN tournament_teams tt ON tt.team_id = t.id
         WHERE tt.tournament_id = ?'
    );
    $stmt->execute([$tournamentId]);
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
            'points'        => 0,
            'form'          => []
        ];
    }

    // Get completed matches
    $stmt = $db->prepare(
        'SELECT home_team_id, away_team_id, home_score, away_score
         FROM fixtures
         WHERE tournament_id = ? AND status = "completed"
         ORDER BY match_date ASC'
    );
    $stmt->execute([$tournamentId]);
    $matches = $stmt->fetchAll();
} else {
    $matches = [];
}

// Ensure all teams in matches exist in map
foreach ($matches as $m) {
    $homeId = $m['home_team_id'];
    $awayId = $m['away_team_id'];
    $hg = (int) $m['home_score'];
    $ag = (int) $m['away_score'];

    if ($homeId && !isset($teamsMap[$homeId])) {
        $tstmt = $db->prepare('SELECT id, name, logo_url FROM teams WHERE id = ?');
        $tstmt->execute([$homeId]);
        $trow = $tstmt->fetch();
        if ($trow) {
            $teamsMap[$homeId] = [
                'team_id' => $trow['id'], 'team_name' => $trow['name'], 'logo_url' => $trow['logo_url'],
                'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0,
                'goals_for' => 0, 'goals_against' => 0, 'goal_diff' => 0, 'points' => 0, 'form' => []
            ];
        }
    }
    if ($awayId && !isset($teamsMap[$awayId])) {
        $tstmt = $db->prepare('SELECT id, name, logo_url FROM teams WHERE id = ?');
        $tstmt->execute([$awayId]);
        $trow = $tstmt->fetch();
        if ($trow) {
            $teamsMap[$awayId] = [
                'team_id' => $trow['id'], 'team_name' => $trow['name'], 'logo_url' => $trow['logo_url'],
                'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0,
                'goals_for' => 0, 'goals_against' => 0, 'goal_diff' => 0, 'points' => 0, 'form' => []
            ];
        }
    }

    if (isset($teamsMap[$homeId]) && isset($teamsMap[$awayId])) {
        $teamsMap[$homeId]['played']++;
        $teamsMap[$homeId]['goals_for']     += $hg;
        $teamsMap[$homeId]['goals_against'] += $ag;

        $teamsMap[$awayId]['played']++;
        $teamsMap[$awayId]['goals_for']     += $ag;
        $teamsMap[$awayId]['goals_against'] += $hg;

        if ($hg > $ag) {
            $teamsMap[$homeId]['won']++;
            $teamsMap[$homeId]['points'] += 3;
            $teamsMap[$homeId]['form'][] = 'W';

            $teamsMap[$awayId]['lost']++;
            $teamsMap[$awayId]['form'][] = 'L';
        } elseif ($hg < $ag) {
            $teamsMap[$awayId]['won']++;
            $teamsMap[$awayId]['points'] += 3;
            $teamsMap[$awayId]['form'][] = 'W';

            $teamsMap[$homeId]['lost']++;
            $teamsMap[$homeId]['form'][] = 'L';
        } else {
            $teamsMap[$homeId]['drawn']++;
            $teamsMap[$homeId]['points'] += 1;
            $teamsMap[$homeId]['form'][] = 'D';

            $teamsMap[$awayId]['drawn']++;
            $teamsMap[$awayId]['points'] += 1;
            $teamsMap[$awayId]['form'][] = 'D';
        }
    }
}

// Calculate goal diff & sort
$standings = array_values($teamsMap);
foreach ($standings as &$row) {
    $row['goal_diff'] = $row['goals_for'] - $row['goals_against'];
    $row['form']      = array_slice($row['form'], -5); // Last 5 matches
}

usort($standings, function($a, $b) {
    if ($b['points'] !== $a['points']) return $b['points'] <=> $a['points'];
    if ($b['goal_diff'] !== $a['goal_diff']) return $b['goal_diff'] <=> $a['goal_diff'];
    return $b['goals_for'] <=> $a['goals_for'];
});

// Assign ranks
foreach ($standings as $i => &$row) {
    $row['rank'] = $i + 1;
}

jsonResponse(['standings' => $standings, 'tournament_id' => $tournamentId]);
