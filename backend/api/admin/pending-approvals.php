<?php
// pending-approvals.php — GET: KYC approval queue
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$admin = guardRole('admin');

$db = Database::connect();
$stmt = $db->query(
    'SELECT id, email, full_name, phone, role, nid_url, cert_url, kyc_status, created_at
     FROM users
     WHERE kyc_status = "pending" AND role = "owner"
     ORDER BY created_at ASC'
);
$pending = $stmt->fetchAll();

jsonResponse(['pending' => $pending]);
