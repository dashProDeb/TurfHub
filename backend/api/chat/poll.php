<?php
// poll.php — GET: Short polling for new messages
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user  = guardRole('player', 'captain', 'owner', 'admin');
$since = $_GET['since'] ?? date('Y-m-d H:i:s', strtotime('-10 seconds'));

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT m.*, u.full_name AS sender_name, u.avatar_url AS sender_avatar
     FROM messages m
     JOIN users u ON u.id = m.sender_id
     WHERE m.receiver_id = ? AND m.created_at > ?
     ORDER BY m.created_at ASC'
);
$stmt->execute([$user['id'], $since]);
$newMessages = $stmt->fetchAll();

jsonResponse(['messages' => $newMessages]);
