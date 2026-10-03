USE turfhub;

-- ── 1. Demo Users (passwords: "password123" hashed with bcrypt) ──
-- Hash: $2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K

INSERT INTO users (id, email, password_hash, full_name, role, phone, avatar_url, nid_url, cert_url, kyc_status) VALUES
(1, 'admin@turfhub.com',   '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Admin User',      'admin',   '01700000000', 'turf_champions.jpg', NULL, NULL, 'verified'),
(2, 'rafiqul@turfhub.com',  '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Rafiqul Islam',    'owner',   '01711111111', 'turf_champions.jpg', 'nid_front.jpg', 'councilor_cert.jpg', 'verified'),
(3, 'tanvir@turfhub.com',   '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Tanvir Ahmed',     'captain', '01722222222', 'turf_football.jpg', NULL, NULL, 'verified'),
(4, 'sabbir@turfhub.com',   '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Sabbir Ahmed',     'player',  '01733333333', 'turf_football.jpg', NULL, NULL, 'verified'),
(5, 'zahid@turfhub.com',    '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Zahid Karim',     'owner',   '01744444444', 'turf_champions.jpg', 'nid_front.jpg', 'councilor_cert.jpg', 'pending')
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), role=VALUES(role), kyc_status=VALUES(kyc_status);

-- ── 2. Demo Turf Grounds ──
INSERT INTO turf_grounds (id, owner_id, name, location, district, sport_type, price_per_hour, surface_type, field_size, amenities, photos, is_verified, status, rating) VALUES
(1, 2, 'The Green Arena',     'Gulshan, Dhaka',     'Dhaka',    'Football',   800,  'Artificial Grass (FIFA 2-Star)', '5-a-side', '["Floodlights","AC Lounge","Parking","Washroom","Changing Room"]', '["turf_football.jpg","turf_champions.jpg"]', 1, 'active', 4.8),
(2, 2, 'Champions Ground',    'Banani, Dhaka',      'Dhaka',    'Football',   1200, 'Natural Grass',                  '7-a-side', '["Floodlights","Gallery","CCTV","First Aid","Showers"]',         '["turf_champions.jpg","turf_football.jpg"]', 1, 'active', 4.5),
(3, 2, 'Thunder Court',       'Dhanmondi, Dhaka',   'Dhaka',    'Basketball', 600,  'Hardwood Indoor',                'Full Court','["AC Indoor","Scoreboard","Parking","Locker Room"]',             '["turf_basketball.jpg"]', 1, 'active', 4.2),
(4, 5, 'Uttara Sports Arena', 'Sector 4, Uttara',   'Dhaka',    'Cricket',    1000, 'AstroTurf Pitch',                'Box Pitch', '["Floodlights","Net Cages","Equipment Rental"]',                '["turf_champions.jpg"]', 0, 'pending_review', 4.0)
ON DUPLICATE KEY UPDATE name=VALUES(name), price_per_hour=VALUES(price_per_hour), is_verified=VALUES(is_verified);

-- ── 3. Demo Sport Categories ──
INSERT INTO sport_categories (id, name, icon, field_size, equipment, is_active) VALUES
(1, 'Football',   '⚽', '5-a-side / 7-a-side / 11-a-side', '["Football","Boots","Shin Guards","Goalkeeper Gloves"]', 1),
(2, 'Cricket',    '🏏', '22 yards pitch / Box Cricket',   '["Cricket Bat","Ball","Stumps","Pads","Gloves","Helmet"]', 1),
(3, 'Basketball', '🏀', 'Full Court / Half Court',          '["Basketball","Jerseys","Court Shoes"]', 1),
(4, 'Badminton',  '🏸', 'Standard BWF Court',              '["Badminton Rackets","Shuttlecock","Court Shoes"]', 1)
ON DUPLICATE KEY UPDATE name=VALUES(name), is_active=VALUES(is_active);

-- ── 4. Demo Teams ──
INSERT INTO teams (id, captain_id, name, sport, formation, status) VALUES
(1, 3, 'Dhaka Warriors FC',   'Football', '4-3-3',  'active'),
(2, 3, 'Banani United',        'Football', '3-5-2',  'recruiting'),
(3, 1, 'Thunder FC',           'Football', '4-2-3-1','active'),
(4, 1, 'Blaze United',         'Football', '4-4-2',  'active')
ON DUPLICATE KEY UPDATE name=VALUES(name), status=VALUES(status);

-- ── 5. Demo Team Members ──
INSERT INTO team_members (id, team_id, player_id, jersey_number, position, status) VALUES
(1, 1, 3, 10, 'Forward',    'active'),
(2, 1, 4, 7,  'Midfielder', 'active')
ON DUPLICATE KEY UPDATE jersey_number=VALUES(jersey_number), status=VALUES(status);

-- ── 6. Demo Tournaments ──
INSERT INTO tournaments (id, created_by, name, sport, turf_id, entry_fee, prize_pool, max_teams, start_date, end_date, rules, status) VALUES
(1, 2, 'Inter-City Premier League', 'Football', 1, 3000, 25000, 8, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Standard FIFA 5-a-side rules. 20 min halves. Rolling substitutions.', 'open'),
(2, 3, 'Dhaka Cup Championship',    'Football', 2, 2500, 20000, 16, DATE_ADD(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 20 DAY), 'Knockout format. Yellow cards carry over.', 'open')
ON DUPLICATE KEY UPDATE name=VALUES(name), status=VALUES(status);

-- ── 7. Demo Tournament Registrations ──
INSERT INTO tournament_teams (id, tournament_id, team_id, status) VALUES
(1, 1, 1, 'approved'),
(2, 1, 2, 'approved'),
(3, 1, 3, 'approved'),
(4, 1, 4, 'approved')
ON DUPLICATE KEY UPDATE status=VALUES(status);

-- ── 8. Demo Fixtures ──
INSERT INTO fixtures (id, tournament_id, home_team_id, away_team_id, match_date, venue_id, round_name, home_score, away_score, scorers, status) VALUES
(1, 1, 1, 2, NOW(), 1, 'Group A - Match 1', 3, 1, '[{"name":"Tanvir Ahmed","minute":"14\'","team":"Dhaka Warriors FC"},{"name":"Sabbir Ahmed","minute":"28\'","team":"Dhaka Warriors FC"}]', 'live'),
(2, 1, 3, 4, DATE_ADD(NOW(), INTERVAL 1 DAY), 1, 'Group A - Match 2', 2, 2, '[]', 'upcoming'),
(3, 1, 1, 3, DATE_SUB(NOW(), INTERVAL 2 DAY), 1, 'Group A - Match 0', 4, 2, '[{"name":"Tanvir Ahmed","minute":"10\'","team":"Dhaka Warriors FC"}]', 'completed')
ON DUPLICATE KEY UPDATE home_score=VALUES(home_score), away_score=VALUES(away_score), status=VALUES(status);

-- ── 9. Demo Announcements ──
INSERT INTO announcements (id, title, content, priority, target_roles, created_by) VALUES
(1, 'Welcome to TurfHub!', 'TurfHub is now live. Book turfs, manage teams, and compete in tournaments!', 'update', 'player,captain,owner,admin', 1),
(2, 'Monsoon Maintenance Notice', 'Pitch 2 at The Green Arena is under scheduled maintenance on Sunday.', 'maintenance', 'player,captain,owner', 1),
(3, 'Championship Registration Open', 'Registrations are now open for the Inter-City Premier League. Prize pool ৳25,000!', 'critical', 'captain,player', 1)
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- ── 10. Demo Messages ──
INSERT INTO messages (id, sender_id, receiver_id, content, is_read, created_at) VALUES
(1, 3, 2, 'Hi Rafiqul bhai, is Pitch 1 available tonight at 8 PM for team training?', 1, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(2, 2, 3, 'Yes Tanvir, Pitch 1 is open! You can book via the app or I can reserve it for you.', 1, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(3, 4, 3, 'Captain, confirmed for tonight match! Wearing jersey #7.', 0, DATE_SUB(NOW(), INTERVAL 30 MINUTE))
ON DUPLICATE KEY UPDATE content=VALUES(content);
