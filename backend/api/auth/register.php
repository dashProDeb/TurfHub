<?php
// register.php — POST: Sign up (email, password, full_name, role)
require_once __DIR__ . '/../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$fullName = trim($input['full_name'] ?? '');
$role     = $input['role'] ?? 'player';
$phone    = trim($input['phone'] ?? '');

// Validation
if (!$email || !$password || !$fullName) {
    jsonError('Email, password, and name are required');
}

if (strlen($password) < 6) {
    jsonError('Password must be at least 6 characters');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonError('Invalid email format');
}

if (!in_array($role, ['player', 'captain', 'owner', 'admin'])) {
    jsonError('Invalid role');
}

$db = Database::connect();

// Check if email already exists
$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    jsonError('Email already registered', 409);
}

// Hash password and insert
$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $db->prepare(
    'INSERT INTO users (email, password_hash, full_name, role, phone) VALUES (?, ?, ?, ?, ?)'
);
$stmt->execute([$email, $hash, $fullName, $role, $phone ?: null]);
$userId = (int) $db->lastInsertId();

// Start session
session_regenerate_id(true);
$_SESSION['user_id'] = $userId;
$_SESSION['role']    = $role;

jsonResponse([
    'success' => true,
    'user' => [
        'id'        => $userId,
        'email'     => $email,
        'full_name' => $fullName,
        'role'      => $role,
        'phone'     => $phone ?: null
    ]
]);
