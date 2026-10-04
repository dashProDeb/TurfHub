<?php
// announcements.php — GET/POST/DELETE: System announcements with Target Audience Segmentation
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('admin');
$db   = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 1. Audience Counts per segment
    $stmtCounts = $db->query(
        'SELECT role, COUNT(*) as cnt
         FROM users
         WHERE role != "admin"
         GROUP BY role'
    );
    $audience = ['all' => 0, 'owner' => 0, 'captain' => 0, 'player' => 0];
    foreach ($stmtCounts->fetchAll() as $row) {
        $r = $row['role'];
        if (isset($audience[$r])) {
            $audience[$r] = (int) $row['cnt'];
        }
        $audience['all'] += (int) $row['cnt'];
    }

    // 2. Announcements list with recipient reach
    $stmt = $db->prepare(
        'SELECT a.*, u.full_name AS author_name
         FROM announcements a
         LEFT JOIN users u ON u.id = a.created_by
         ORDER BY a.created_at DESC'
    );
    $stmt->execute();
    $announcements = $stmt->fetchAll();

    // Attach estimated reach count based on target_roles
    foreach ($announcements as &$ann) {
        $t = strtolower(trim($ann['target_roles'] ?? 'all'));
        if (in_array($t, ['owner', 'owners'])) $t = 'owner';
        elseif (in_array($t, ['captain', 'captains'])) $t = 'captain';
        elseif (in_array($t, ['player', 'players'])) $t = 'player';
        else $t = 'all';

        $ann['reach_count'] = $audience[$t] ?? $audience['all'];
    }
    unset($ann);

    jsonResponse([
        'announcements' => $announcements,
        'audience'      => $audience
    ]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $title   = trim($input['title'] ?? '');
    $content = trim($input['content'] ?? $input['body'] ?? '');
    if (empty($title) || empty($content)) {
        jsonError('Title and content are required');
    }

    // Normalize Target Audience Segment
    $rawAudience = strtolower(trim($input['target_roles'] ?? $input['target_audience'] ?? 'all'));
    if (in_array($rawAudience, ['owner', 'owners'])) {
        $targetRole = 'owner';
    } elseif (in_array($rawAudience, ['captain', 'captains'])) {
        $targetRole = 'captain';
    } elseif (in_array($rawAudience, ['player', 'players'])) {
        $targetRole = 'player';
    } else {
        $targetRole = 'all';
    }

    $priority = trim($input['priority'] ?? 'update');
    if (!in_array($priority, ['critical', 'update', 'maintenance'])) {
        $priority = 'update';
    }

    // 1. Insert into announcements table
    $stmt = $db->prepare(
        'INSERT INTO announcements (title, content, priority, target_roles, created_by, created_at)
         VALUES (?, ?, ?, ?, ?, NOW())'
    );
    $stmt->execute([
        $title,
        $content,
        $priority,
        $targetRole,
        $user['id']
    ]);
    $annId = (int) $db->lastInsertId();

    // 2. Dispatch to target audience messages inbox
    if ($targetRole === 'all') {
        $stmtUsers = $db->prepare('SELECT id, role FROM users WHERE id != ? AND role != "admin"');
        $stmtUsers->execute([$user['id']]);
    } else {
        $stmtUsers = $db->prepare('SELECT id, role FROM users WHERE id != ? AND role = ?');
        $stmtUsers->execute([$user['id'], $targetRole]);
    }
    $recipients = $stmtUsers->fetchAll();
    $reachCount = count($recipients);

    $msgBody = "📢 [{$title}]\n\n{$content}";
    $stmtMsg = $db->prepare(
        'INSERT INTO messages (sender_id, receiver_id, content, is_read, created_at)
         VALUES (?, ?, ?, 0, NOW())'
    );
    foreach ($recipients as $rec) {
        $stmtMsg->execute([$user['id'], $rec['id'], $msgBody]);
    }

    jsonResponse([
        'success'          => true,
        'announcement_id'  => $annId,
        'target_audience'  => $targetRole,
        'recipients_count' => $reachCount,
        'message'          => "Announcement broadcasted successfully to {$reachCount} recipients in target segment."
    ]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $annId = (int) ($input['id'] ?? 0);
    if (!$annId) {
        jsonError('Announcement ID required for deletion');
    }

    $stmt = $db->prepare('DELETE FROM announcements WHERE id = ?');
    $stmt->execute([$annId]);

    jsonResponse(['success' => true, 'message' => 'Announcement deleted']);
} else {
    jsonError('Method not allowed', 405);
}
