<?php
// list.php — GET: Open tournaments
require_once __DIR__ . '/../config/helpers.php';

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT t.*, tg.name AS venue_name, tg.location AS venue_location,
            u.full_name AS organizer_name,
            (SELECT COUNT(*) FROM tournament_teams tt WHERE tt.tournament_id = t.id) AS registered_teams
     FROM tournaments t
     LEFT JOIN turf_grounds tg ON tg.id = t.turf_id
     LEFT JOIN users u ON u.id = t.created_by
     WHERE t.status = "open"
     ORDER BY t.start_date ASC'
);
$stmt->execute();
$tournaments = $stmt->fetchAll();

jsonResponse(['tournaments' => $tournaments]);
