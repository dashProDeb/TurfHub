-- ══════════════════════════════════════════════════════════
-- TurfHub MySQL Seed Data — Comprehensive Multi-Role Demo Dataset
-- Run this in phpMyAdmin or MySQL CLI after 001_schema.sql
-- ══════════════════════════════════════════════════════════

USE turfhub;

SET FOREIGN_KEY_CHECKS = 0;

-- ── 1. Demo Users (passwords: "password123" hashed with bcrypt) ──
-- Hash: $2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K
INSERT INTO users (id, email, password_hash, full_name, role, phone, avatar_url, nid_url, cert_url, kyc_status) VALUES
(1,  'admin@turfhub.com',   '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Admin User',      'admin',   '01700000000', 'turf_champions.jpg', NULL, NULL, 'verified'),
(2,  'rafiqul@turfhub.com',  '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Rafiqul Islam',    'owner',   '01711111111', 'turf_champions.jpg', 'nid_front.jpg', 'councilor_cert.jpg', 'verified'),
(3,  'tanvir@turfhub.com',   '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Tanvir Ahmed',     'captain', '01722222222', 'turf_football.jpg', NULL, NULL, 'verified'),
(4,  'sabbir@turfhub.com',   '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Sabbir Ahmed',     'player',  '01733333333', 'turf_football.jpg', NULL, NULL, 'verified'),
(5,  'zahid@turfhub.com',    '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Zahid Karim',     'owner',   '01744444444', 'turf_champions.jpg', 'nid_front.jpg', 'councilor_cert.jpg', 'pending'),
(6,  'kamal@turfhub.com',    '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Kamal Hossain',    'owner',   '01755555555', 'turf_champions.jpg', 'nid_front.jpg', 'councilor_cert.jpg', 'verified'),
(7,  'prottay@turfhub.com',  '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Prottay Debnath',  'captain', '01711149537', 'turf_football.jpg', NULL, NULL, 'verified'),
(8,  'shakib@turfhub.com',   '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Shakib Al Hasan',  'captain', '01777777777', 'turf_champions.jpg', NULL, NULL, 'verified'),
(9,  'arif@turfhub.com',     '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Arif Hossain',     'player',  '01788888888', 'turf_football.jpg', NULL, NULL, 'verified'),
(10, 'delwar@turfhub.com',   '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Delwar Khan',     'player',  '01799999999', 'turf_football.jpg', NULL, NULL, 'verified'),
(11, 'rakib@turfhub.com',    '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Rakib Hasan',     'player',  '01712345678', 'turf_football.jpg', NULL, NULL, 'verified'),
(12, 'mahfuz@turfhub.com',   '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Mahfuz Rahman',   'player',  '01787654321', 'turf_football.jpg', NULL, NULL, 'verified'),
(13, 'tamim@turfhub.com',    '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Tamim Iqbal',     'player',  '01711223344', 'turf_football.jpg', NULL, NULL, 'verified'),
(14, 'mushfiq@turfhub.com',  '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Mushfiqur Rahim', 'player',  '01755667788', 'turf_football.jpg', NULL, NULL, 'verified')
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), role=VALUES(role), kyc_status=VALUES(kyc_status);

-- ── 2. Demo Sport Categories ──
INSERT INTO sport_categories (id, name, icon, field_size, equipment, is_active) VALUES
(1, 'Football',   '⚽', '5-a-side / 7-a-side / 11-a-side', '["Football","Boots","Shin Guards","Goalkeeper Gloves"]', 1),
(2, 'Cricket',    '🏏', '22 yards pitch / Box Cricket',   '["Cricket Bat","Ball","Stumps","Pads","Gloves","Helmet"]', 1),
(3, 'Basketball', '🏀', 'Full Court / Half Court',          '["Basketball","Jerseys","Court Shoes"]', 1),
(4, 'Badminton',  '🏸', 'Standard BWF Court',              '["Badminton Rackets","Shuttlecock","Court Shoes"]', 1),
(5, 'Tennis',     '🎾', 'Standard Tennis Court',           '["Tennis Rackets","Tennis Balls","Tennis Shoes"]', 1),
(6, 'Futsal',     '🥅', 'Indoor Futsal Arena',              '["Low-bounce Ball","Indoor Shoes","Bibs"]', 1)
ON DUPLICATE KEY UPDATE name=VALUES(name), is_active=VALUES(is_active);

-- ── 3. Demo Turf Grounds ──
INSERT INTO turf_grounds (id, owner_id, name, location, district, sport_type, price_per_hour, surface_type, field_size, amenities, photos, is_verified, status, rating) VALUES
(1, 2, 'The Green Arena',            'Gulshan, Dhaka',          'Dhaka', 'Football',   800,  'Artificial Grass (FIFA 2-Star)', '5-a-side',   '["Floodlights","AC Lounge","Parking","Washroom","Changing Room"]', '["turf_football.jpg","turf_champions.jpg"]', 1, 'active', 4.8),
(2, 2, 'Champions Ground',           'Banani, Dhaka',           'Dhaka', 'Football',   1200, 'Natural Grass',                  '7-a-side',   '["Floodlights","Gallery","CCTV","First Aid","Showers"]',         '["turf_champions.jpg","turf_football.jpg"]', 1, 'active', 4.5),
(3, 2, 'Thunder Court',              'Dhanmondi, Dhaka',        'Dhaka', 'Basketball', 600,  'Hardwood Indoor',                'Full Court', '["AC Indoor","Scoreboard","Parking","Locker Room"]',             '["turf_basketball.jpg"]', 1, 'active', 4.2),
(4, 5, 'Uttara Sports Arena',        'Sector 4, Uttara',        'Dhaka', 'Cricket',    1000, 'AstroTurf Pitch',                'Box Pitch',  '["Floodlights","Net Cages","Equipment Rental"]',                '["turf_champions.jpg"]', 0, 'pending_review', 4.0),
(5, 6, 'Smash Zone Arena',           'Mirpur 10, Dhaka',        'Dhaka', 'Badminton',  500,  'Synthetic BWF Mat',              'Standard',   '["Air Conditioned","Yonex Equipment","Locker Rooms"]',           '["turf_champions.jpg"]', 1, 'active', 4.6),
(6, 6, 'Bashundhara Futsal Pitch',   'Block I, Bashundhara R/A', 'Dhaka', 'Football',   1500, 'Shock-pad Turf',                 '6-a-side',   '["Floodlights","Cafe Lounge","WiFi","Free Parking","CCTV"]',    '["turf_football.jpg"]', 1, 'active', 4.9)
ON DUPLICATE KEY UPDATE name=VALUES(name), price_per_hour=VALUES(price_per_hour), is_verified=VALUES(is_verified);

-- ── 4. Demo Teams ──
INSERT INTO teams (id, captain_id, name, sport, logo_url, formation, status) VALUES
(1, 3, 'Dhaka Warriors FC', 'Football',   '', '4-3-3',   'active'),
(2, 3, 'Banani United',     'Football',   '', '3-5-2',   'recruiting'),
(3, 7, 'Thunder FC',        'Football',   '', '4-2-3-1', 'active'),
(4, 7, 'Blaze United',      'Football',   '', '4-4-2',   'recruiting'),
(5, 3, 'Court Kings',       'Basketball', '', 'Standard','recruiting'),
(6, 8, 'Gulshan Strikers',  'Cricket',    '', 'T20',     'recruiting'),
(7, 8, 'Mirpur Smashers',   'Badminton',  '', 'Doubles', 'recruiting')
ON DUPLICATE KEY UPDATE name=VALUES(name), status=VALUES(status);

-- ── 5. Demo Team Members ──
INSERT INTO team_members (id, team_id, player_id, jersey_number, position, status) VALUES
(1,  1, 3,  10,   'Forward',    'active'),
(2,  1, 4,  7,    'Midfielder', 'active'),
(3,  1, 9,  8,    'Midfielder', 'active'),
(4,  1, 10, 4,    'Defender',   'active'),
(5,  1, 11, 1,    'Goalkeeper', 'active'),
(6,  1, 12, 11,   'Forward',    'bench'),
(7,  3, 7,  10,   'Midfielder', 'active'),
(8,  3, 13, 9,    'Forward',    'active'),
(9,  3, 14, 1,    'Goalkeeper', 'active'),
(10, 2, 9,  NULL, 'Forward',    'pending'),
(11, 5, 10, NULL, 'Guard',      'pending')
ON DUPLICATE KEY UPDATE jersey_number=VALUES(jersey_number), status=VALUES(status);

-- ── 6. Demo Tournaments ──
INSERT INTO tournaments (id, created_by, name, sport, turf_id, entry_fee, prize_pool, max_teams, start_date, end_date, rules, status) VALUES
(1, 2, 'Inter-City Premier League',       'Football',  1, 3000, 25000, 8,  CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Standard FIFA 5-a-side rules. 20 min halves. Rolling substitutions.', 'open'),
(2, 3, 'Dhaka Cup Championship',          'Football',  2, 2500, 20000, 16, DATE_ADD(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 20 DAY), 'Knockout format. Yellow cards carry over.', 'open'),
(3, 8, 'Monsoon Box Cricket League',      'Cricket',   4, 2000, 15000, 8,  DATE_ADD(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 18 DAY), '6 overs per side. Tape-ball cricket.', 'open'),
(4, 6, 'Summer Smash Badminton Open',     'Badminton', 5, 1500, 10000, 8,  DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 21 DAY), 'BWF standard scoring (21 points, best of 3 sets).', 'open')
ON DUPLICATE KEY UPDATE name=VALUES(name), status=VALUES(status);

-- ── 7. Demo Tournament Registrations ──
INSERT INTO tournament_teams (id, tournament_id, team_id, status) VALUES
(1, 1, 1, 'approved'),
(2, 1, 2, 'approved'),
(3, 1, 3, 'approved'),
(4, 1, 4, 'approved'),
(5, 2, 1, 'approved'),
(6, 2, 3, 'approved'),
(7, 3, 6, 'approved'),
(8, 4, 7, 'approved')
ON DUPLICATE KEY UPDATE status=VALUES(status);

-- ── 8. Demo Fixtures ──
INSERT INTO fixtures (id, tournament_id, home_team_id, away_team_id, match_date, venue_id, round_name, home_score, away_score, scorers, status) VALUES
(1, 1, 1, 2, NOW(), 1, 'Group A - Match 1', 3, 1, '[{"name":"Tanvir Ahmed","minute":"14\'","team":"Dhaka Warriors FC"},{"name":"Sabbir Ahmed","minute":"28\'","team":"Dhaka Warriors FC"}]', 'live'),
(2, 1, 3, 4, DATE_ADD(NOW(), INTERVAL 1 DAY), 1, 'Group A - Match 2', 0, 0, '[]', 'upcoming'),
(3, 1, 1, 3, DATE_SUB(NOW(), INTERVAL 2 DAY), 1, 'Group A - Match 0', 4, 2, '[{"name":"Tanvir Ahmed","minute":"10\'","team":"Dhaka Warriors FC"},{"name":"Sabbir Ahmed","minute":"35\'","team":"Dhaka Warriors FC"},{"name":"Prottay Debnath","minute":"18\'","team":"Thunder FC"}]', 'completed'),
(4, 2, 1, 3, DATE_ADD(NOW(), INTERVAL 3 DAY), 2, 'Quarter Final 1', 0, 0, '[]', 'upcoming'),
(5, 3, 6, 6, DATE_ADD(NOW(), INTERVAL 4 DAY), 4, 'Match 1', 0, 0, '[]', 'upcoming'),
(6, 4, 7, 7, DATE_ADD(NOW(), INTERVAL 6 DAY), 5, 'Round 1', 0, 0, '[]', 'upcoming')
ON DUPLICATE KEY UPDATE home_score=VALUES(home_score), away_score=VALUES(away_score), status=VALUES(status);

-- ── 9. Demo Announcements ──
INSERT INTO announcements (id, title, content, priority, target_roles, created_by) VALUES
(1, '🎉 Welcome to TurfHub Platform!', 'TurfHub is now officially live across Dhaka! Book premier pitches, manage team rosters, and compete in tournaments with live referee scoring.', 'update', 'player,captain,owner,admin', 1),
(2, '⚠️ Scheduled Monsoon Turf Maintenance', 'Pitch 2 at The Green Arena is scheduled for turf re-leveling this coming Sunday 06:00 - 10:00 AM. Regular slots resume at 11:00 AM.', 'maintenance', 'player,captain,owner', 1),
(3, '🏆 Inter-City Premier League Entries Open', 'Registrations are now open for the Inter-City Premier League with a grand prize pool of ৳25,000. Secure your squad entry now!', 'critical', 'captain,player', 1),
(4, '⚡ Instant SSLCOMMERZ Payment Simulation', 'Players and Captains can now pay directly via bKash, Nagad, Rocket, and Cards with instant booking confirmations and tax receipts.', 'update', 'all', 1)
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- ── 10. Demo Messages (cross-role conversations for consistency) ──
INSERT INTO messages (id, sender_id, receiver_id, content, is_read, created_at) VALUES
-- Captain(3) ↔ Owner(2)
(1, 3, 2, 'Hi Rafiqul bhai, is Pitch 1 available tonight at 8 PM for Dhaka Warriors practice?', 1, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(2, 2, 3, 'Yes Tanvir, Pitch 1 is open! I reserved the 8 PM slot for you. See you on the pitch.', 1, DATE_SUB(NOW(), INTERVAL 3 HOUR)),
-- Player(4) ↔ Captain(3)
(3, 4, 3, 'Captain, confirmed for tonight match! Wearing jersey #7. Should I come 30 mins early for warm-up?', 1, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(4, 3, 4, 'Awesome Sabbir! Yes, reach by 5:30 PM. You are in our starting 11 lineup as striker 🔥', 1, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(5, 4, 3, 'Got it Captain! Bringing my boots and extra bibs. Let\'s win this!', 0, DATE_SUB(NOW(), INTERVAL 20 MINUTE)),
-- Player(4) ↔ Owner(2)
(6, 4, 2, 'Hello Rafiqul bhai, is parking free for players at The Green Arena?', 1, DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(7, 2, 4, 'Yes Sabbir, we have dedicated underground car & bike parking free for all TurfHub players.', 1, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
-- Admin(1) Broadcasts to Owners (one-way broadcast)
(8, 1, 2, '📢 TurfHub Official Notice: Your venue "The Green Arena" verification has been approved. All 3 pitches are now live on search catalog.', 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(9, 1, 5, '📢 Platform Maintenance Notice: Server optimization scheduled this Sunday 2:00 AM - 4:00 AM BST. Turfs will remain bookable.', 1, DATE_SUB(NOW(), INTERVAL 22 HOUR)),
-- Admin(1) Broadcasts to Captains (one-way broadcast)
(10, 1, 3, '📢 Platform Announcement: Tournament guidelines updated. All tournaments now feature automatic knockout fixture brackets.', 1, DATE_SUB(NOW(), INTERVAL 6 HOUR)),
(11, 1, 7, '📢 Platform Announcement: Tournament guidelines updated. All tournaments now feature automatic knockout fixture brackets.', 0, DATE_SUB(NOW(), INTERVAL 5 HOUR)),
-- Admin(1) Broadcasts to Players (one-way broadcast)
(12, 1, 4, '📢 Welcome to TurfHub! Explore local turfs, join registered teams, and track your match stats seamlessly.', 1, DATE_SUB(NOW(), INTERVAL 8 HOUR)),
(13, 1, 9, '📢 Welcome to TurfHub! Explore local turfs, join registered teams, and track your match stats seamlessly.', 0, DATE_SUB(NOW(), INTERVAL 7 HOUR)),
-- Captain(7) ↔ Owner(6)
(14, 7, 6, 'Hi Kamal bhai, can we book Smash Zone Arena for our badminton practice this weekend?', 1, DATE_SUB(NOW(), INTERVAL 10 HOUR)),
(15, 6, 7, 'Sure Prottay! I have slots open on Saturday 4-6 PM. Shall I reserve it for Thunder FC?', 0, DATE_SUB(NOW(), INTERVAL 9 HOUR)),
-- Player(9) ↔ Captain(3)
(16, 9, 3, 'Captain Tanvir, I would like to join Dhaka Warriors. I play as midfielder.', 1, DATE_SUB(NOW(), INTERVAL 12 HOUR)),
(17, 3, 9, 'Welcome aboard Arif! I have added you to the roster. Report to practice tomorrow at 5 PM.', 1, DATE_SUB(NOW(), INTERVAL 11 HOUR))
ON DUPLICATE KEY UPDATE content=VALUES(content);

SET FOREIGN_KEY_CHECKS = 1;
