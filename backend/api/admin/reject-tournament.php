<?php
// reject-tournament.php — POST: Reject tournament application
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$admin = guardRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input  = json_decode(file_get_contents('php://input'), true);
$tournId = (int) ($input['tournament_id'] ?? 0);
$reason = trim($input['reason'] ?? '');

if (!$tournId) {
    jsonError('tournament_id is required');
}

$db = Database::connect();
$stmt = $db->prepare('UPDATE tournaments SET status = "draft" WHERE id = ?');
$stmt->execute([$tournId]);

jsonResponse(['success' => true, 'message' => 'Tournament application rejected']);
