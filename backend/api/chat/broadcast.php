<?php
// broadcast.php — GET/POST: Admin system broadcast messages
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('admin');
$db   = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Return broadcast history + audience counts
    $stmtCounts = $db->query(
        'SELECT role, COUNT(*) as cnt
         FROM users
         WHERE role != "admin"
         GROUP BY role'
    );
    $roleCounts = ['all' => 0, 'owner' => 0, 'captain' => 0, 'player' => 0];
    foreach ($stmtCounts->fetchAll() as $row) {
        $roleCounts[$row['role']] = (int) $row['cnt'];
        $roleCounts['all'] += (int) $row['cnt'];
    }

    // Recent broadcasts (from announcements table + messages reach)
    $stmtHistory = $db->prepare(
        'SELECT a.id, a.title, a.content, a.priority, a.target_roles, a.created_at,
                u.full_name AS author_name,
                (SELECT COUNT(*) FROM messages m WHERE m.sender_id = a.created_by AND m.content = a.content) AS reach_count
         FROM announcements a
         JOIN users u ON u.id = a.created_by
         WHERE a.created_by = ?
         ORDER BY a.created_at DESC
         LIMIT 20'
    );
    $stmtHistory->execute([$user['id']]);
    $history = $stmtHistory->fetchAll();

    jsonResponse([
        'audience'   => $roleCounts,
        'broadcasts' => $history
    ]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input      = json_decode(file_get_contents('php://input'), true);
    $content    = trim($input['content'] ?? $input['message'] ?? '');
    $targetRole = trim($input['target_role'] ?? 'all');
    $title      = trim($input['title'] ?? 'Platform Announcement');

    if (empty($content)) {
        jsonError('Broadcast message content is required');
    }

    // Determine recipient query based on target_role
    if ($targetRole === 'all' || empty($targetRole)) {
        $stmtUsers = $db->prepare(
            'SELECT id, full_name, role FROM users WHERE id != ? AND role != "admin"'
        );
        $stmtUsers->execute([$user['id']]);
    } else {
        $stmtUsers = $db->prepare(
            'SELECT id, full_name, role FROM users WHERE id != ? AND role = ?'
        );
        $stmtUsers->execute([$user['id'], $targetRole]);
    }

    $recipients = $stmtUsers->fetchAll();
    $count = count($recipients);

    if ($count === 0) {
        jsonError('No active recipients found for the selected audience', 400);
    }

    // Insert broadcast message into messages table for each recipient
    $stmtMsg = $db->prepare(
        'INSERT INTO messages (sender_id, receiver_id, content, is_read, created_at)
         VALUES (?, ?, ?, 0, NOW())'
    );

    foreach ($recipients as $rec) {
        $stmtMsg->execute([$user['id'], $rec['id'], $content]);
    }

    // Also record in announcements table
    $stmtAnn = $db->prepare(
        'INSERT INTO announcements (title, content, priority, target_roles, created_by, created_at)
         VALUES (?, ?, "update", ?, ?, NOW())'
    );
    $stmtAnn->execute([$title, $content, $targetRole, $user['id']]);
    $annId = (int) $db->lastInsertId();

    jsonResponse([
        'success'          => true,
        'announcement_id'  => $annId,
        'recipients_count' => $count,
        'target_role'      => $targetRole,
        'content'          => $content,
        'message'          => "Broadcast successfully sent to {$count} users."
    ]);

} else {
    jsonError('Method not allowed', 405);
}
