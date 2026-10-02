<?php
// announcements.php — GET/POST: System announcements CRUD
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('admin');
$db = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->prepare(
        'SELECT a.*, u.full_name AS author_name
         FROM announcements a
         LEFT JOIN users u ON u.id = a.created_by
         ORDER BY a.created_at DESC'
    );
    $stmt->execute();
    jsonResponse(['announcements' => $stmt->fetchAll()]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['title']) || empty($input['content'])) {
        jsonError('Title and content are required');
    }

    $stmt = $db->prepare(
        'INSERT INTO announcements (title, content, priority, target_roles, created_by)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $input['title'],
        $input['content'],
        $input['priority'] ?? 'update',
        $input['target_roles'] ?? '',
        $user['id']
    ]);

    jsonResponse(['success' => true, 'announcement_id' => (int) $db->lastInsertId()]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $annId = (int) ($input['id'] ?? 0);
    if ($annId) {
        $stmt = $db->prepare('DELETE FROM announcements WHERE id = ?');
        $stmt->execute([$annId]);
    }
    jsonResponse(['success' => true]);
}
