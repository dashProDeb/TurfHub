<?php
// messages.php — GET: Messages between two users
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user    = guardRole('player', 'captain', 'owner', 'admin');
$otherId = (int) ($_GET['other_user_id'] ?? $_GET['user_id'] ?? 0);

if (!$otherId) {
    jsonError('other_user_id or user_id is required');
}

$db = Database::connect();

// Fetch messages between the two users
$stmt = $db->prepare(
    'SELECT m.*, u.full_name AS sender_name, u.avatar_url AS sender_avatar
     FROM messages m
     JOIN users u ON u.id = m.sender_id
     WHERE (m.sender_id = ? AND m.receiver_id = ?)
        OR (m.sender_id = ? AND m.receiver_id = ?)
     ORDER BY m.created_at ASC'
);
$stmt->execute([$user['id'], $otherId, $otherId, $user['id']]);
$messages = $stmt->fetchAll();

// Mark received messages as read
$stmt = $db->prepare(
    'UPDATE messages SET is_read = 1
     WHERE sender_id = ? AND receiver_id = ? AND is_read = 0'
);
$stmt->execute([$otherId, $user['id']]);

jsonResponse(['messages' => $messages]);
