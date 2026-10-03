USE turfhub;

-- ── 1. Demo Users (passwords: "password123" hashed with bcrypt) ──
-- Hash: $2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K

INSERT INTO users (id, email, password_hash, full_name, role, phone, avatar_url, nid_url, cert_url, kyc_status, kyc_data) VALUES
(1, 'admin@turfhub.com',       '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Admin User',          'admin',   '01700000000', 'turf_champions.jpg', NULL, NULL, 'verified', NULL),
(2, 'rafiqul@turfhub.com',      '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Rafiqul Islam',        'owner',   '01711111111', 'turf_champions.jpg', 'nid_front.jpg', 'councilor_cert.jpg', 'verified', '{"father_name":"Late Shamsul Islam","mother_name":"Laila Begum","nid_number":"19852691234567890","dob":"12 Aug 1985","address":"Road 7, Gulshan-1, Ward 19, Dhaka","blood_group":"O+","issue_date":"10 Jan 2018","mrz":"IDBGD19852691234567890<<<<<<<<8508125","payout_title":"BRAC Bank Ltd. (Gulshan Branch)","payout_details":"A/C No: 1501203498 · Routing: 060261325","trade_license":"TRAD/DNCC/019283/2023","cert_no":"CC-DNCC-W19-2024-0192","cert_date":"10 Jan 2024","councilor_header":"Dhaka North City Corporation · Ward 19","councilor_name":"M. H. Chowdhury"}'),
(3, 'tanvir@turfhub.com',       '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Tanvir Ahmed',         'captain', '01722222222', 'turf_football.jpg', NULL, NULL, 'verified', NULL),
(4, 'sabbir@turfhub.com',       '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Sabbir Ahmed',         'player',  '01733333333', 'turf_football.jpg', NULL, NULL, 'verified', NULL),
(5, 'zahid@turfhub.com',        '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Zahid Karim',         'owner',   '01744444444', 'turf_champions.jpg', 'nid_front.jpg', 'councilor_cert.jpg', 'pending', '{"father_name":"Late Karim Ullah","mother_name":"Fatema Karim","nid_number":"19892694455667788","dob":"05 May 1989","address":"Sector 4, Road 12, Uttara, Ward 1, Dhaka-1230","blood_group":"A+","issue_date":"15 Mar 2019","mrz":"IDBGD19892694455667788<<<<<<<<8905051","payout_title":"Dutch-Bangla Bank Ltd. (Uttara Branch)","payout_details":"A/C No: 1181204958 · Routing: 090261142","trade_license":"TRAD/DNCC/083921/2024","cert_no":"CC-DNCC-W01-2025-0551","cert_date":"20 Feb 2025","councilor_header":"Dhaka North City Corporation · Ward 01","councilor_name":"Afzal Hossain"}'),
(6, 'shahidul@dhakaturfs.com',  '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Shahidul Alam',       'owner',   '01711987654', 'turf_champions.jpg', 'nid_front.jpg', 'councilor_cert.jpg', 'pending', '{"father_name":"Late Shamsul Alam","mother_name":"Rokeya Begum","nid_number":"19882691234567890","dob":"14 Oct 1988","address":"House 42, Road 11, Block D, Banani, Ward 19, Dhaka-1213","blood_group":"B+","issue_date":"08 May 2018","mrz":"IDBGD19882691234567890<<<<<<<<8810145","payout_title":"The City Bank Ltd. (Gulshan-1 Branch)","payout_details":"A/C No: 1102938475 · Routing: 225271890","trade_license":"TRAD/DNCC/041920/2024","cert_no":"CC-DNCC-W19-2025-0842","cert_date":"02 July 2025","councilor_header":"Dhaka North City Corporation · Ward No. 19","councilor_name":"M. H. Chowdhury"}'),
(7, 'nusrat@arena360.bd',       '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Nusrat Jahan',        'owner',   '01812445566', 'turf_champions.jpg', 'nid_front.jpg', 'councilor_cert.jpg', 'pending', '{"father_name":"Md. Anisur Rahman","mother_name":"Nasreen Jahan","nid_number":"19922695566778899","dob":"28 Mar 1992","address":"Plot 18, Block D, Bashundhara R/A, Ward 40, Dhaka-1229","blood_group":"O+","issue_date":"12 Sep 2020","mrz":"IDBGD19922695566778899<<<<<<<<9203282","payout_title":"bKash Merchant Account (Arena 360 Ent.)","payout_details":"Wallet: 01812445566 · Merchant ID: BK-99482","trade_license":"TRAD/DNCC/099182/2024","cert_no":"CC-DNCC-W40-2025-1193","cert_date":"15 June 2025","councilor_header":"Dhaka North City Corporation · Ward No. 40","councilor_name":"Kazi Farhan Ahmed"}'),
(8, 'imran@turfhub.com',        '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Imran Hossain',       'player',  '01755555555', 'turf_football.jpg', NULL, NULL, 'verified', NULL),
(9, 'fahim@turfhub.com',        '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Fahim Chowdhury',      'captain', '01766666666', 'turf_football.jpg', NULL, NULL, 'verified', NULL),
(10, 'rashid@turfhub.com',      '$2y$10$B2nn9fL84zFBgDFX8ipnreU6eFZ8.Ri6cYx69mjFph4SxVR8eLK1K', 'Rashid Mahmud',       'player',  '01777777777', 'turf_football.jpg', NULL, NULL, 'verified', NULL)
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name), role=VALUES(role), kyc_status=VALUES(kyc_status), kyc_data=VALUES(kyc_data);

-- ── 2. Demo Turf Grounds ──
INSERT INTO turf_grounds (id, owner_id, name, location, district, sport_type, price_per_hour, surface_type, field_size, amenities, photos, is_verified, status, rating) VALUES
(1, 2, 'The Green Arena',                 'Gulshan, Dhaka',         'Dhaka',    'Football',   800,  'Artificial Grass (FIFA 2-Star)', '5-a-side', '["Floodlights","AC Lounge","Parking","Washroom","Changing Room"]', '["turf_football.jpg","turf_champions.jpg"]', 1, 'active', 4.8),
(2, 2, 'Champions Ground',                'Banani, Dhaka',          'Dhaka',    'Football',   1200, 'Natural Grass',                  '7-a-side', '["Floodlights","Gallery","CCTV","First Aid","Showers"]',         '["turf_champions.jpg","turf_football.jpg"]', 1, 'active', 4.5),
(3, 2, 'Thunder Court',                   'Dhanmondi, Dhaka',       'Dhaka',    'Basketball', 600,  'Hardwood Indoor',                'Full Court','["AC Indoor","Scoreboard","Parking","Locker Room"]',             '["turf_basketball.jpg"]', 1, 'active', 4.2),
(4, 5, 'Uttara Sports Arena',             'Sector 4, Uttara',       'Dhaka',    'Cricket',    1000, 'AstroTurf Pitch',                'Box Pitch', '["Floodlights","Net Cages","Equipment Rental"]',                '["turf_champions.jpg"]', 0, 'pending_review', 4.0),
(5, 6, 'Dhanmondi Arena Pro',             'Road 27, Dhanmondi',     'Dhaka',    'Football',   900,  'Artificial Turf',                '5-a-side (50x32m)', '["Floodlights","Parking","Shower Rooms","Locker Room"]', '["turf_football.jpg"]', 0, 'pending_review', 4.7),
(6, 7, 'Bashundhara Smash Complex',       'Block G, Bashundhara',   'Dhaka',    'Badminton',  500,  'Wooden Badminton Court',         '4 Courts',  '["Central AC","4 Shower Rooms","Pro Shop","Gallery"]',           '["turf_basketball.jpg"]', 0, 'pending_review', 4.9),
(7, 5, 'Mirpur Cricket Box Pro',          'Section 2, Mirpur',      'Dhaka',    'Cricket',    1100, 'AstroTurf Cricket Box',          '2 Astro Nets','["Floodlights","Bowling Machine","CCTV"]',                     '["turf_champions.jpg"]', 0, 'pending_review', 4.3),
(8, 2, 'Sylhet Green Park',               'Zindabazar, Sylhet',     'Sylhet',   'Football',   750,  'Natural Grass',                  '7-a-side', '["Floodlights","Parking","Gallery"]',                             '["turf_football.jpg"]', 1, 'active', 4.6),
(9, 2, 'Port City Futsal',                'GEC Circle, Chittagong', 'Chittagong','Football',  850,  'Artificial Turf',                '5-a-side', '["Floodlights","AC Lounge","Changing Room"]',                     '["turf_champions.jpg"]', 1, 'active', 4.4)
ON DUPLICATE KEY UPDATE name=VALUES(name), price_per_hour=VALUES(price_per_hour), is_verified=VALUES(is_verified), status=VALUES(status);

-- ── 3. Demo Sport Categories ──
INSERT INTO sport_categories (id, name, icon, field_size, equipment, is_active) VALUES
(1, 'Football',   '⚽', '5-a-side / 7-a-side / 11-a-side', '["Football","Boots","Shin Guards","Goalkeeper Gloves"]', 1),
(2, 'Cricket',    '🏏', '22 yards pitch / Box Cricket',   '["Cricket Bat","Ball","Stumps","Pads","Gloves","Helmet"]', 1),
(3, 'Basketball', '🏀', 'Full Court / Half Court',          '["Basketball","Jerseys","Court Shoes"]', 1),
(4, 'Badminton',  '🏸', 'Standard BWF Court',              '["Badminton Rackets","Shuttlecock","Court Shoes"]', 1),
(5, 'Volleyball', '🏐', 'Standard 18x9m Court',             '["Volleyball","Knee Pads","Net"]', 1),
(6, 'Tennis',     '🎾', 'Standard Hard / Clay Court',       '["Tennis Rackets","Tennis Balls"]', 1)
ON DUPLICATE KEY UPDATE name=VALUES(name), is_active=VALUES(is_active);

-- ── 4. Demo Teams ──
INSERT INTO teams (id, captain_id, name, sport, formation, status) VALUES
(1, 3, 'Dhaka Warriors FC',   'Football', '4-3-3',  'active'),
(2, 3, 'Banani United',        'Football', '3-5-2',  'recruiting'),
(3, 9, 'Thunder FC',           'Football', '4-2-3-1','active'),
(4, 9, 'Blaze United',         'Football', '4-4-2',  'active')
ON DUPLICATE KEY UPDATE name=VALUES(name), status=VALUES(status);

-- ── 5. Demo Team Members ──
INSERT INTO team_members (id, team_id, player_id, jersey_number, position, status) VALUES
(1, 1, 3, 10, 'Forward',    'active'),
(2, 1, 4, 7,  'Midfielder', 'active'),
(3, 1, 8, 1,  'Goalkeeper', 'active'),
(4, 3, 9, 9,  'Forward',    'active'),
(5, 3, 10, 4, 'Defender',   'active')
ON DUPLICATE KEY UPDATE jersey_number=VALUES(jersey_number), status=VALUES(status);

-- ── 6. Demo Tournaments ──
INSERT INTO tournaments (id, created_by, name, sport, turf_id, entry_fee, prize_pool, max_teams, start_date, end_date, rules, status) VALUES
(1, 2, 'Inter-City Premier League',               'Football', 1, 3000, 25000, 8,  CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Standard FIFA 5-a-side rules. 20 min halves. Rolling substitutions.', 'open'),
(2, 3, 'Dhaka Cup Championship',                  'Football', 2, 2500, 20000, 16, DATE_ADD(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 20 DAY), 'Knockout format. Yellow cards carry over.', 'open'),
(3, 2, 'Dhaka Corporate Futsal Championship 2025', 'Football', 1, 4000, 50000, 16, DATE_ADD(CURDATE(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 25 DAY), 'Corporate identity required. Prize pool guaranteed.', 'draft')
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

-- ── 9. Demo Turf Slots ──
INSERT INTO turf_slots (id, turf_id, slot_date, start_time, end_time, status, booked_by, price) VALUES
(1,  1, CURDATE(),                           '07:00:00', '08:00:00', 'booked', 3, 800),
(2,  1, CURDATE(),                           '08:00:00', '09:00:00', 'booked', 4, 800),
(3,  1, CURDATE(),                           '18:00:00', '19:00:00', 'booked', 3, 800),
(4,  1, CURDATE(),                           '19:00:00', '20:00:00', 'booked', 4, 800),
(5,  1, CURDATE(),                           '20:00:00', '21:00:00', 'available', NULL, 800),
(6,  2, CURDATE(),                           '18:00:00', '19:00:00', 'booked', 3, 1200),
(7,  2, CURDATE(),                           '19:00:00', '20:00:00', 'booked', 4, 1200),
(8,  3, CURDATE(),                           '17:00:00', '18:00:00', 'booked', 8, 600),
(9,  1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '19:00:00', '20:00:00', 'booked', 3, 800),
(10, 2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '20:00:00', '21:00:00', 'booked', 4, 1200),
(11, 3, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '18:00:00', '19:00:00', 'booked', 3, 600),
(12, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '21:00:00', '22:00:00', 'booked', 8, 800),
(13, 2, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '19:00:00', '20:00:00', 'booked', 9, 1200),
(14, 1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), '18:00:00', '19:00:00', 'booked', 4, 800),
(15, 3, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '20:00:00', '21:00:00', 'booked', 10, 600),
(16, 8, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '17:00:00', '18:00:00', 'booked', 4, 750),
(17, 9, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '19:00:00', '20:00:00', 'booked', 8, 850),
(18, 1, DATE_SUB(CURDATE(), INTERVAL 7 DAY), '18:00:00', '19:00:00', 'booked', 3, 800),
(19, 2, DATE_SUB(CURDATE(), INTERVAL 8 DAY), '20:00:00', '21:00:00', 'booked', 9, 1200),
(20, 3, DATE_SUB(CURDATE(), INTERVAL 9 DAY), '16:00:00', '17:00:00', 'booked', 4, 600)
ON DUPLICATE KEY UPDATE status=VALUES(status);

-- ── 10. Demo Bookings ──
INSERT INTO bookings (id, user_id, turf_id, slot_id, total_price, payment_method, trx_id, ref_id, status, created_at) VALUES
(1,  3,  1, 1,  800,  'bkash',  'TRX-998241', 'REF-001', 'confirmed', NOW()),
(2,  4,  1, 2,  800,  'nagad',  'TRX-883912', 'REF-002', 'confirmed', NOW()),
(3,  3,  1, 3,  800,  'bkash',  'TRX-774619', 'REF-003', 'confirmed', NOW()),
(4,  4,  1, 4,  800,  'rocket', 'TRX-661928', 'REF-004', 'confirmed', NOW()),
(5,  3,  2, 6,  1200, 'bkash',  'TRX-559281', 'REF-005', 'confirmed', NOW()),
(6,  4,  2, 7,  1200, 'card',   'TRX-448192', 'REF-006', 'confirmed', NOW()),
(7,  8,  3, 8,  600,  'upay',   'TRX-339182', 'REF-007', 'confirmed', NOW()),
(8,  3,  1, 9,  800,  'bkash',  'TRX-228193', 'REF-008', 'confirmed', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(9,  4,  2, 10, 1200, 'nagad',  'TRX-119284', 'REF-009', 'confirmed', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(10, 3,  3, 11, 600,  'bkash',  'TRX-009285', 'REF-010', 'confirmed', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(11, 8,  1, 12, 800,  'rocket', 'TRX-994821', 'REF-011', 'confirmed', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(12, 9,  2, 13, 1200, 'bkash',  'TRX-883719', 'REF-012', 'confirmed', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(13, 4,  1, 14, 800,  'card',   'TRX-772810', 'REF-013', 'confirmed', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(14, 10, 3, 15, 600,  'nagad',  'TRX-661947', 'REF-014', 'cancelled', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(15, 4,  8, 16, 750,  'bkash',  'TRX-551982', 'REF-015', 'confirmed', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(16, 8,  9, 17, 850,  'rocket', 'TRX-441298', 'REF-016', 'confirmed', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(17, 3,  1, 18, 800,  'bkash',  'TRX-339481', 'REF-017', 'confirmed', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(18, 9,  2, 19, 1200, 'card',   'TRX-228394', 'REF-018', 'confirmed', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(19, 4,  3, 20, 600,  'nagad',  'TRX-117283', 'REF-019', 'confirmed', DATE_SUB(NOW(), INTERVAL 9 DAY))
ON DUPLICATE KEY UPDATE status=VALUES(status), total_price=VALUES(total_price);

-- ── 11. Demo Announcements ──
INSERT INTO announcements (id, title, content, priority, target_roles, created_by) VALUES
(1, 'Welcome to TurfHub!', 'TurfHub is now live. Book turfs, manage teams, and compete in tournaments!', 'update', 'player,captain,owner,admin', 1),
(2, 'Monsoon Maintenance Notice', 'Pitch 2 at The Green Arena is under scheduled maintenance on Sunday.', 'maintenance', 'player,captain,owner', 1),
(3, 'Championship Registration Open', 'Registrations are now open for the Inter-City Premier League. Prize pool ৳25,000!', 'critical', 'captain,player', 1),
(4, 'Inter-City Summer Cup 2025 Fixtures Released', 'Quarter-final match fixtures and referee allocations have been scheduled across venues.', 'update', 'all', 1),
(5, 'Scheduled Gateway Maintenance', 'bKash merchant auto-settlement routine will run Saturday 2:00 AM – 3:30 AM.', 'maintenance', 'owner', 1)
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- ── 12. Demo Messages ──
INSERT INTO messages (id, sender_id, receiver_id, content, is_read, created_at) VALUES
(1, 3, 2, 'Hi Rafiqul bhai, is Pitch 1 available tonight at 8 PM for team training?', 1, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(2, 2, 3, 'Yes Tanvir, Pitch 1 is open! You can book via the app or I can reserve it for you.', 1, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(3, 4, 3, 'Captain, confirmed for tonight match! Wearing jersey #7.', 0, DATE_SUB(NOW(), INTERVAL 30 MINUTE))
ON DUPLICATE KEY UPDATE content=VALUES(content);
