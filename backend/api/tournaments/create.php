<?php
// create.php — POST: Create tournament
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('captain', 'owner');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['name']) || empty($input['sport'])) {
    jsonError('Tournament name and sport are required');
}

$db = Database::connect();
$stmt = $db->prepare(
    'INSERT INTO tournaments (created_by, name, sport, turf_id, entry_fee, prize_pool, max_teams, start_date, end_date, rules, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([
    $user['id'],
    $input['name'],
    $input['sport'],
    $input['turf_id'] ?? null,
    (int) ($input['entry_fee'] ?? 0),
    (int) ($input['prize_pool'] ?? 0),
    (int) ($input['max_teams'] ?? 8),
    $input['start_date'] ?? null,
    $input['end_date'] ?? null,
    $input['rules'] ?? null,
    $input['status'] ?? 'draft'
]);

jsonResponse(['success' => true, 'tournament_id' => (int) $db->lastInsertId()]);
