<?php
// login.php — POST: Sign in (email, password)
require_once __DIR__ . '/../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (!$email || !$password) {
    jsonError('Email and password are required');
}

$db = Database::connect();
$stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    jsonError('Invalid email or password', 401);
}

// Start session
session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['role']    = $user['role'];

// Return profile (exclude password hash)
unset($user['password_hash']);
jsonResponse(['success' => true, 'user' => $user]);
