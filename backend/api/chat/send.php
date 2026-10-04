<?php
// send.php — POST: Send a message
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain', 'owner', 'admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input      = json_decode(file_get_contents('php://input'), true);
$receiverId = (int) ($input['receiver_id'] ?? 0);
$content    = trim($input['content'] ?? $input['message'] ?? '');

if (!$receiverId || !$content) {
    jsonError('receiver_id and message content are required');
}

// Prevent messaging self
if ($receiverId === $user['id']) {
    jsonError('Cannot send a message to yourself');
}

$db = Database::connect();

// Verify receiver exists and check role
$stmt = $db->prepare('SELECT id, role FROM users WHERE id = ?');
$stmt->execute([$receiverId]);
$recipient = $stmt->fetch();
if (!$recipient) {
    jsonError('Recipient not found', 404);
}

// Prevent non-admin users from sending messages to admin
if ($recipient['role'] === 'admin' && $user['role'] !== 'admin') {
    jsonError('Direct messages to administrators are disabled. Administrators communicate via platform broadcasts.', 403);
}

$stmt = $db->prepare(
    'INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)'
);
$stmt->execute([$user['id'], $receiverId, $content]);

$msgId = (int) $db->lastInsertId();

jsonResponse([
    'success' => true,
    'message' => [
        'id'          => $msgId,
        'sender_id'   => $user['id'],
        'receiver_id' => $receiverId,
        'content'     => $content,
        'is_read'     => 0,
        'created_at'  => date('Y-m-d H:i:s')
    ]
]);
