<?php
// contacts.php — GET: Available contacts for messaging
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain', 'owner', 'admin');
$db   = Database::connect();
$role = $user['role'];
$uid  = $user['id'];

// Depending on role, return relevant people:
// Owner: Captains, Admin, and Customers who booked their turfs
// Captain: Turf owners, Admin, teammates, players
// Player: Captains, Turf owners, Admin
// Admin: Everyone

if ($role === 'owner') {
    $stmt = $db->prepare(
        'SELECT DISTINCT u.id, u.full_name, u.role, u.avatar_url, u.email, u.phone
         FROM users u
         WHERE u.id != ?
           AND (
             u.role IN ("admin", "captain")
             OR u.id IN (
                 SELECT b.user_id FROM bookings b
                 JOIN turf_grounds tg ON tg.id = b.turf_id
                 WHERE tg.owner_id = ?
             )
           )
         ORDER BY u.role = "admin" DESC, u.full_name ASC
         LIMIT 30'
    );
    $stmt->execute([$uid, $uid]);
} else {
    $stmt = $db->prepare(
        'SELECT u.id, u.full_name, u.role, u.avatar_url, u.email, u.phone
         FROM users u
         WHERE u.id != ?
         ORDER BY u.role = "admin" DESC, u.role = "owner" DESC, u.full_name ASC
         LIMIT 30'
    );
    $stmt->execute([$uid]);
}

$contacts = $stmt->fetchAll();

jsonResponse(['contacts' => $contacts]);
