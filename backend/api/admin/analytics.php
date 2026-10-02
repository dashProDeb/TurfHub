<?php
// analytics.php — GET: Heatmaps & revenue trends
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('admin');
$db = Database::connect();

// Hourly booking heatmap (which hours are most popular)
$stmt = $db->query(
    'SELECT HOUR(ts.start_time) AS hour, COUNT(*) AS booking_count
     FROM bookings b
     JOIN turf_slots ts ON ts.id = b.slot_id
     WHERE b.status = "confirmed"
     GROUP BY HOUR(ts.start_time)
     ORDER BY hour ASC'
);
$hourlyHeatmap = $stmt->fetchAll();

// Sport popularity
$stmt = $db->query(
    'SELECT tg.sport_type, COUNT(*) AS booking_count
     FROM bookings b
     JOIN turf_grounds tg ON tg.id = b.turf_id
     WHERE b.status = "confirmed"
     GROUP BY tg.sport_type
     ORDER BY booking_count DESC'
);
$sportPopularity = $stmt->fetchAll();

// Monthly revenue trend (last 12 months)
$stmt = $db->query(
    'SELECT DATE_FORMAT(created_at, "%Y-%m") AS month, SUM(total_price) AS revenue, COUNT(*) AS bookings
     FROM bookings
     WHERE status = "confirmed"
     GROUP BY DATE_FORMAT(created_at, "%Y-%m")
     ORDER BY month DESC
     LIMIT 12'
);
$revenueTrend = $stmt->fetchAll();

// User registration trend
$stmt = $db->query(
    'SELECT role, COUNT(*) AS count FROM users GROUP BY role'
);
$usersByRole = $stmt->fetchAll();

jsonResponse([
    'hourly_heatmap'   => $hourlyHeatmap,
    'sport_popularity' => $sportPopularity,
    'revenue_trend'    => $revenueTrend,
    'users_by_role'    => $usersByRole
]);
