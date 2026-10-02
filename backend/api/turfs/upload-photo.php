<?php
// upload-photo.php — POST: Upload turf images
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

$turfId = (int) ($_POST['turf_id'] ?? 0);

if (!$turfId || !isset($_FILES['photo'])) {
    jsonError('turf_id and photo file are required');
}

// Verify ownership
$db = Database::connect();
$stmt = $db->prepare('SELECT id, photos FROM turf_grounds WHERE id = ? AND owner_id = ?');
$stmt->execute([$turfId, $user['id']]);
$turf = $stmt->fetch();

if (!$turf) {
    jsonError('Turf not found or not owned by you', 403);
}

// Validate file
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$fileType = $_FILES['photo']['type'];
if (!in_array($fileType, $allowedTypes)) {
    jsonError('Invalid file type. Allowed: JPEG, PNG, WebP, GIF');
}

$maxSize = 5 * 1024 * 1024; // 5MB
if ($_FILES['photo']['size'] > $maxSize) {
    jsonError('File too large. Maximum 5MB');
}

// Handle upload
$uploadDir = __DIR__ . '/../uploads/turf-photos/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
$filename = 'turf_' . $turfId . '_' . time() . '.' . $ext;
$filepath = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['photo']['tmp_name'], $filepath)) {
    jsonError('Failed to save file', 500);
}

$photoUrl = 'backend/api/uploads/turf-photos/' . $filename;

// Append to photos JSON array
$photos = json_decode($turf['photos'] ?? '[]', true);
$photos[] = $photoUrl;

$stmt = $db->prepare('UPDATE turf_grounds SET photos = ? WHERE id = ?');
$stmt->execute([json_encode($photos), $turfId]);

jsonResponse(['success' => true, 'photo_url' => $photoUrl]);
