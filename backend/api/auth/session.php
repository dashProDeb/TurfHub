<?php
// session.php — GET: Check active session + return profile
require_once __DIR__ . '/../config/helpers.php';

if (!isset($_SESSION['user_id'])) {
    jsonError('Not authenticated', 401);
}

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT id, email, full_name, role, phone, avatar_url, kyc_status, created_at
     FROM users WHERE id = ?'
);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    jsonError('User not found', 401);
}

jsonResponse(['user' => $user]);
