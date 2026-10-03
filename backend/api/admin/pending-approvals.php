<?php
// pending-approvals.php — GET: Complete approval queue (turfs, owners KYC, tournaments)
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$admin = guardRole('admin');
$db = Database::connect();

// 1. Pending Turfs
$stmt = $db->query(
    'SELECT tg.*, u.full_name AS owner_name, u.email AS owner_email, u.phone AS owner_phone
     FROM turf_grounds tg
     JOIN users u ON u.id = tg.owner_id
     WHERE tg.status = "pending_review" OR tg.is_verified = 0
     ORDER BY tg.created_at ASC'
);
$pendingTurfs = $stmt->fetchAll();
foreach ($pendingTurfs as &$turf) {
    $turf['amenities'] = json_decode($turf['amenities'] ?? '[]', true);
    $turf['photos']    = json_decode($turf['photos'] ?? '[]', true);
}

// 2. Pending Owner KYC
$stmt = $db->query(
    'SELECT id, email, full_name, phone, role, nid_url, cert_url, kyc_status, kyc_data, created_at
     FROM users
     WHERE kyc_status = "pending" AND role = "owner"
     ORDER BY created_at ASC'
);
$pendingOwners = $stmt->fetchAll();
foreach ($pendingOwners as &$owner) {
    $owner['kyc_data'] = json_decode($owner['kyc_data'] ?? '{}', true);
}

// 3. Pending Tournaments
$stmt = $db->query(
    'SELECT t.*, u.full_name AS organizer_name, u.email AS organizer_email, tg.name AS turf_name
     FROM tournaments t
     JOIN users u ON u.id = t.created_by
     LEFT JOIN turf_grounds tg ON tg.id = t.turf_id
     WHERE t.status = "draft"
     ORDER BY t.created_at ASC'
);
$pendingTournaments = $stmt->fetchAll();

jsonResponse([
    'pending_turfs'       => $pendingTurfs,
    'pending_owners'      => $pendingOwners,
    'pending_tournaments' => $pendingTournaments,
    'counts' => [
        'turfs'       => count($pendingTurfs),
        'owners'      => count($pendingOwners),
        'tournaments' => count($pendingTournaments),
        'total'       => count($pendingTurfs) + count($pendingOwners) + count($pendingTournaments)
    ]
]);
