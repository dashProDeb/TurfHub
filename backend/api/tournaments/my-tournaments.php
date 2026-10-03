<?php
// my-tournaments.php — GET: Tournaments owned or hosted by authenticated owner
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner', 'captain', 'admin');
$db = Database::connect();

$userId = $user['id'];
$statusFilter = $_GET['status'] ?? 'all';

// Fetch tournaments created by user OR hosted at user's turfs
$sql = 'SELECT t.*, tg.name AS venue_name, tg.location AS venue_location,
               u.full_name AS organizer_name,
               (SELECT COUNT(*) FROM tournament_teams tt WHERE tt.tournament_id = t.id) AS registered_teams,
               (SELECT COUNT(*) FROM tournament_teams tt WHERE tt.tournament_id = t.id AND tt.status = "approved") AS approved_teams,
               (SELECT COUNT(*) FROM fixtures f WHERE f.tournament_id = t.id) AS matches_count,
               (SELECT COUNT(*) FROM fixtures f WHERE f.tournament_id = t.id AND f.status = "completed") AS completed_matches,
               (SELECT COUNT(*) FROM fixtures f WHERE f.tournament_id = t.id AND f.status IN ("live", "upcoming")) AS active_matches
        FROM tournaments t
        LEFT JOIN turf_grounds tg ON tg.id = t.turf_id
        LEFT JOIN users u ON u.id = t.created_by
        WHERE (t.created_by = ? OR tg.owner_id = ?)';
$params = [$userId, $userId];

if ($statusFilter !== 'all' && !empty($statusFilter)) {
    $sql .= ' AND t.status = ?';
    $params[] = $statusFilter;
}

$sql .= ' ORDER BY t.created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$tournaments = $stmt->fetchAll();

// Calculate KPIs
$totalTournaments = count($tournaments);
$activeTournaments = 0;
$completedTournaments = 0;
$totalRegisteredTeams = 0;
$totalRevenue = 0;
$totalMatches = 0;
$upcomingMatches = 0;

foreach ($tournaments as $t) {
    if (in_array($t['status'], ['open', 'in_progress'])) {
        $activeTournaments++;
    } elseif ($t['status'] === 'completed') {
        $completedTournaments++;
    }
    $regCount = (int) ($t['registered_teams'] ?? 0);
    $totalRegisteredTeams += $regCount;
    $totalRevenue += $regCount * (int) ($t['entry_fee'] ?? 0);
    $totalMatches += (int) ($t['matches_count'] ?? 0);
    $upcomingMatches += (int) ($t['active_matches'] ?? 0);
}

// Fetch recent registrations for this owner's tournaments
$regStmt = $db->prepare(
    'SELECT tt.*, t.name AS tournament_name, tm.name AS team_name, tm.sport, tm.logo_url,
            u.full_name AS captain_name, u.phone AS captain_phone
     FROM tournament_teams tt
     JOIN tournaments t ON t.id = tt.tournament_id
     LEFT JOIN turf_grounds tg ON tg.id = t.turf_id
     JOIN teams tm ON tm.id = tt.team_id
     JOIN users u ON u.id = tm.captain_id
     WHERE (t.created_by = ? OR tg.owner_id = ?)
     ORDER BY tt.registered_at DESC
     LIMIT 8'
);
$regStmt->execute([$userId, $userId]);
$recentRegistrations = $regStmt->fetchAll();

jsonResponse([
    'tournaments'          => $tournaments,
    'kpis'                 => [
        'total_tournaments'       => $totalTournaments,
        'active_tournaments'      => $activeTournaments,
        'completed_tournaments'   => $completedTournaments,
        'total_registered_teams'  => $totalRegisteredTeams,
        'total_revenue'           => $totalRevenue,
        'total_matches'           => $totalMatches,
        'upcoming_matches'        => $upcomingMatches,
    ],
    'recent_registrations' => $recentRegistrations
]);
