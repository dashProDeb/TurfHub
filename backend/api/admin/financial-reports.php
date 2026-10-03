<?php
// financial-reports.php — GET: Booking financial ledger
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('admin');
$db = Database::connect();

$startDate = $_GET['start_date'] ?? date('Y-m-01'); // Default: first of current month
$endDate   = $_GET['end_date'] ?? date('Y-m-d');

$stmt = $db->prepare(
    'SELECT b.id, b.total_price, b.payment_method, b.trx_id, b.ref_id, b.status, b.created_at,
            u.full_name AS customer_name, u.email AS customer_email,
            tg.name AS turf_name, tg.sport_type,
            ts.slot_date, ts.start_time, ts.end_time
     FROM bookings b
     JOIN users u ON u.id = b.user_id
     JOIN turf_grounds tg ON tg.id = b.turf_id
     JOIN turf_slots ts ON ts.id = b.slot_id
     WHERE DATE(b.created_at) BETWEEN ? AND ?
     ORDER BY b.created_at DESC'
);
$stmt->execute([$startDate, $endDate]);
$bookings = $stmt->fetchAll();

// Summary stats
$stmt = $db->prepare(
    'SELECT
        COUNT(*) AS total_bookings,
        COALESCE(SUM(total_price), 0) AS total_revenue,
        COALESCE(SUM(CASE WHEN status = "confirmed" THEN total_price ELSE 0 END), 0) AS confirmed_revenue,
        COALESCE(SUM(CASE WHEN status = "cancelled" THEN total_price ELSE 0 END), 0) AS cancelled_revenue
     FROM bookings
     WHERE DATE(created_at) BETWEEN ? AND ?'
);
$stmt->execute([$startDate, $endDate]);
$summary = $stmt->fetch();

jsonResponse([
    'bookings' => $bookings,
    'summary'  => $summary,
    'filters'  => ['start_date' => $startDate, 'end_date' => $endDate]
]);
