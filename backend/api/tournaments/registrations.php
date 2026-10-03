<?php
// registrations.php — GET: Tournament registrations
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner', 'captain');

$tournamentId = (int) ($_GET['tournament_id'] ?? 0);
if (!$tournamentId) {
    jsonError('tournament_id is required');
}

$db = Database::connect();

$stmt = $db->prepare(
    'SELECT tt.*, t.name AS team_name, t.sport, t.logo_url,
            u.full_name AS captain_name,
            (SELECT COUNT(*) FROM team_members tm WHERE tm.team_id = t.id AND tm.status = "active") AS member_count
     FROM tournament_teams tt
     JOIN teams t ON t.id = tt.team_id
     JOIN users u ON u.id = t.captain_id
     WHERE tt.tournament_id = ?
     ORDER BY tt.registered_at ASC'
);
$stmt->execute([$tournamentId]);
$registrations = $stmt->fetchAll();

jsonResponse(['registrations' => $registrations]);
