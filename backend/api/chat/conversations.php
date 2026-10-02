<?php
// conversations.php — GET: List of chat threads
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain', 'owner', 'admin');

$db = Database::connect();

// Get distinct conversation partners with last message
$stmt = $db->prepare(
    'SELECT
        other_id,
        u.full_name AS other_name,
        u.avatar_url AS other_avatar,
        u.role AS other_role,
        sub.last_content,
        sub.last_time,
        COALESCE(unread.cnt, 0) AS unread_count
     FROM (
        SELECT
            CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END AS other_id,
            content AS last_content,
            created_at AS last_time,
            ROW_NUMBER() OVER (
                PARTITION BY CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END
                ORDER BY created_at DESC
            ) AS rn
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
     ) sub
     JOIN users u ON u.id = sub.other_id
     LEFT JOIN (
        SELECT sender_id, COUNT(*) AS cnt
        FROM messages
        WHERE receiver_id = ? AND is_read = 0
        GROUP BY sender_id
     ) unread ON unread.sender_id = sub.other_id
     WHERE sub.rn = 1
     ORDER BY sub.last_time DESC'
);
$uid = $user['id'];
$stmt->execute([$uid, $uid, $uid, $uid, $uid]);
$conversations = $stmt->fetchAll();

jsonResponse(['conversations' => $conversations]);
