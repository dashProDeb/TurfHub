<?php
// delete.php — POST/DELETE: Remove / delete turf ground
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'])) {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$turfId = (int) ($input['turf_id'] ?? ($_GET['turf_id'] ?? 0));

if (!$turfId) {
    jsonError('turf_id is required');
}

$db = Database::connect();

// Verify ownership
$stmt = $db->prepare('SELECT id, name FROM turf_grounds WHERE id = ? AND owner_id = ?');
$stmt->execute([$turfId, $user['id']]);
$turf = $stmt->fetch();

if (!$turf) {
    jsonError('Turf not found or not owned by you', 403);
}

// Delete turf (foreign keys ON DELETE CASCADE will handle slots, or delete explicitly)
$db->beginTransaction();
try {
    // 1. Delete slots for this turf
    $stmt = $db->prepare('DELETE FROM turf_slots WHERE turf_id = ?');
    $stmt->execute([$turfId]);

    // 2. Delete turf ground
    $stmt = $db->prepare('DELETE FROM turf_grounds WHERE id = ? AND owner_id = ?');
    $stmt->execute([$turfId, $user['id']]);

    $db->commit();
    jsonResponse([
        'success' => true,
        'message' => "Turf '{$turf['name']}' deleted successfully",
        'turf_id' => $turfId
    ]);
} catch (Exception $e) {
    $db->rollBack();
    jsonError('Failed to delete turf: ' . $e->getMessage(), 500);
}
