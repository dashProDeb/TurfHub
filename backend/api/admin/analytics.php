<?php
// analytics.php — GET: Heatmaps, sport volume, regional breakdowns & revenue trends
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('admin');
$db = Database::connect();

// 1. Sport popularity (all sport types with booking counts & revenue)
$stmt = $db->query(
    'SELECT tg.sport_type, COUNT(b.id) AS booking_count, COALESCE(SUM(b.total_price), 0) AS total_revenue
     FROM turf_grounds tg
     LEFT JOIN bookings b ON b.turf_id = tg.id AND b.status = "confirmed"
     GROUP BY tg.sport_type
     ORDER BY booking_count DESC'
);
$sportPopularity = $stmt->fetchAll();

// Total confirmed bookings count for percentage calculation
$stmt = $db->query('SELECT COUNT(*) AS cnt, COALESCE(SUM(total_price), 0) AS gmv FROM bookings WHERE status = "confirmed"');
$totals = $stmt->fetch();
$totalConfirmedBookings = (int) $totals['cnt'];
$totalGMV = (int) $totals['gmv'];

// 2. Regional Breakdown (area / district distribution with GMV & turf counts)
$stmt = $db->query(
    'SELECT COALESCE(NULLIF(tg.location, ""), tg.district, "Dhaka") AS area,
            COUNT(DISTINCT tg.id) AS turf_count,
            COUNT(b.id) AS booking_count,
            COALESCE(SUM(CASE WHEN b.status = "confirmed" THEN b.total_price ELSE 0 END), 0) AS revenue
     FROM turf_grounds tg
     LEFT JOIN bookings b ON b.turf_id = tg.id
     GROUP BY area
     ORDER BY revenue DESC'
);
$regionalBreakdown = $stmt->fetchAll();

// 3. Peak Hour / Day-of-Week Occupancy Matrix
// Day of week: 1=Sun, 2=Mon, 3=Tue, 4=Wed, 5=Thu, 6=Fri, 7=Sat (MySQL DAYOFWEEK)
$stmt = $db->query(
    'SELECT DAYOFWEEK(ts.slot_date) AS day_of_week,
            HOUR(ts.start_time) AS hour,
            COUNT(*) AS booking_count
     FROM bookings b
     JOIN turf_slots ts ON ts.id = b.slot_id
     WHERE b.status = "confirmed"
     GROUP BY day_of_week, hour
     ORDER BY day_of_week, hour'
);
$dayHourMatrix = $stmt->fetchAll();

// 4. Hourly booking count
$stmt = $db->query(
    'SELECT HOUR(ts.start_time) AS hour, COUNT(*) AS booking_count
     FROM bookings b
     JOIN turf_slots ts ON ts.id = b.slot_id
     WHERE b.status = "confirmed"
     GROUP BY HOUR(ts.start_time)
     ORDER BY hour ASC'
);
$hourlyHeatmap = $stmt->fetchAll();

// 5. Monthly revenue trend (last 12 months)
$stmt = $db->query(
    'SELECT DATE_FORMAT(created_at, "%Y-%m") AS month,
            SUM(total_price) AS revenue,
            COUNT(*) AS bookings
     FROM bookings
     WHERE status = "confirmed"
     GROUP BY DATE_FORMAT(created_at, "%Y-%m")
     ORDER BY month DESC
     LIMIT 12'
);
$revenueTrend = $stmt->fetchAll();

// 6. User registration trend
$stmt = $db->query(
    'SELECT role, COUNT(*) AS count FROM users GROUP BY role'
);
$usersByRole = $stmt->fetchAll();

jsonResponse([
    'total_confirmed_bookings' => $totalConfirmedBookings,
    'total_gmv'                => $totalGMV,
    'sport_popularity'         => $sportPopularity,
    'regional_breakdown'       => $regionalBreakdown,
    'day_hour_matrix'          => $dayHourMatrix,
    'hourly_heatmap'           => $hourlyHeatmap,
    'revenue_trend'            => $revenueTrend,
    'users_by_role'            => $usersByRole
]);
