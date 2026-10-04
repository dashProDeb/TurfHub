<?php
// my-registrations.php — GET: Tournaments registered by authenticated captain's teams
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('captain', 'player', 'admin', 'owner');
$db = Database::connect();

$stmt = $db->prepare('
    SELECT tt.id AS registration_id, tt.tournament_id, tt.team_id, tt.status AS registration_status, tt.registered_at,
           t.name AS tournament_name, t.sport, t.status AS tournament_status,
           t.start_date, t.end_date, t.entry_fee, t.prize_pool, t.max_teams,
           tg.name AS venue_name, tg.location AS venue_location,
           tm.name AS team_name, tm.logo_url AS team_logo,
           (SELECT COUNT(*) FROM tournament_teams tts WHERE tts.tournament_id = t.id) AS total_registered_teams
    FROM tournament_teams tt
    JOIN tournaments t ON t.id = tt.tournament_id
    LEFT JOIN turf_grounds tg ON tg.id = t.turf_id
    JOIN teams tm ON tm.id = tt.team_id
    WHERE tm.captain_id = ?
    ORDER BY tt.registered_at DESC
');
$stmt->execute([$user['id']]);
$registrations = $stmt->fetchAll();

jsonResponse(['registrations' => $registrations]);
