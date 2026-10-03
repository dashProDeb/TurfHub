<?php
// guard.php — Include at the top of any protected API endpoint
// Usage: require_once __DIR__ . '/../auth/guard.php';
//        guardRole('owner');           // single role
//        guardRole('captain', 'player'); // multiple allowed roles

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

function guardRole(string ...$allowedRoles): array {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }

    $db = Database::connect();
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    if (count($allowedRoles) > 0 && !in_array($user['role'], $allowedRoles)) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden: insufficient role']);
        exit;
    }

    unset($user['password_hash']);
    return $user;
}
