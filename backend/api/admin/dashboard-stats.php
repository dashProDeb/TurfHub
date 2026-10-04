<?php
// dashboard-stats.php — GET: Platform KPI metrics
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('admin');

$db = Database::connect();

// Total revenue (confirmed bookings)
$stmt = $db->query('SELECT COALESCE(SUM(total_price), 0) AS total FROM bookings WHERE status = "confirmed"');
$totalRevenue = (int) $stmt->fetch()['total'];

// Total bookings count
$stmt = $db->query('SELECT COUNT(*) AS cnt FROM bookings');
$totalBookings = (int) $stmt->fetch()['cnt'];

// Total users
$stmt = $db->query('SELECT COUNT(*) AS cnt FROM users');
$totalUsers = (int) $stmt->fetch()['cnt'];

// Total turfs
$stmt = $db->query('SELECT COUNT(*) AS cnt FROM turf_grounds');
$totalTurfs = (int) $stmt->fetch()['cnt'];

// Pending KYC approvals
$stmt = $db->query('SELECT COUNT(*) AS cnt FROM users WHERE kyc_status = "pending" AND role = "owner"');
$pendingKyc = (int) $stmt->fetch()['cnt'];

// Pending turf listings
$stmt = $db->query('SELECT COUNT(*) AS cnt FROM turf_grounds WHERE status = "pending_review" OR is_verified = 0');
$pendingTurfs = (int) $stmt->fetch()['cnt'];

// Pending tournaments
$stmt = $db->query('SELECT COUNT(*) AS cnt FROM tournaments WHERE status = "draft"');
$pendingTournaments = (int) $stmt->fetch()['cnt'];

$pendingApprovals = $pendingKyc + $pendingTurfs + $pendingTournaments;

// This month's revenue
$stmt = $db->prepare(
    'SELECT COALESCE(SUM(total_price), 0) AS total FROM bookings
     WHERE status = "confirmed" AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())'
);
$stmt->execute();
$monthlyRevenue = (int) $stmt->fetch()['total'];

// Active tournaments
$stmt = $db->query('SELECT COUNT(*) AS cnt FROM tournaments WHERE status IN ("open", "in_progress")');
$activeTournaments = (int) $stmt->fetch()['cnt'];

// Recent bookings
$stmt = $db->query(
    'SELECT b.id, b.total_price, b.status, b.created_at, b.trx_id,
            u.full_name AS customer_name, tg.name AS turf_name
     FROM bookings b
     JOIN users u ON u.id = b.user_id
     JOIN turf_grounds tg ON tg.id = b.turf_id
     ORDER BY b.created_at DESC LIMIT 10'
);
$recentBookings = $stmt->fetchAll();

jsonResponse([
    'total_revenue'       => $totalRevenue,
    'monthly_revenue'     => $monthlyRevenue,
    'total_bookings'      => $totalBookings,
    'total_users'         => $totalUsers,
    'total_turfs'         => $totalTurfs,
    'pending_approvals'   => $pendingApprovals,
    'pending_kyc'         => $pendingKyc,
    'pending_turfs'       => $pendingTurfs,
    'pending_tournaments' => $pendingTournaments,
    'active_tournaments'  => $activeTournaments,
    'recent_bookings'     => $recentBookings
]);
