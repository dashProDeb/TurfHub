USE turfhub;

-- ── Demo Users (passwords: "password123" hashed with bcrypt) ──
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO users (email, password_hash, full_name, role, phone, kyc_status) VALUES
('admin@turfhub.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User',      'admin',   '01700000000', 'verified'),
('rafiqul@turfhub.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rafiqul Islam',    'owner',   '01711111111', 'verified'),
('tanvir@turfhub.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tanvir Ahmed',     'captain', '01722222222', 'verified'),
('sabbir@turfhub.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sabbir Ahmed',     'player',  '01733333333', 'verified');

-- ── Demo Turf Grounds ──
INSERT INTO turf_grounds (owner_id, name, location, district, sport_type, price_per_hour, surface_type, field_size, amenities, is_verified, status, rating) VALUES
(2, 'The Green Arena',     'Gulshan, Dhaka',     'Dhaka',    'Football', 800,  'Artificial Grass (FIFA 2-Star)', '5-a-side', '["Floodlights","AC Lounge","Parking","Washroom"]', 1, 'active', 4.8),
(2, 'Champions Ground',    'Banani, Dhaka',      'Dhaka',    'Football', 1200, 'Natural Grass',                  '7-a-side', '["Floodlights","Gallery","CCTV","First Aid"]',      1, 'active', 4.5),
(2, 'Thunder Court',       'Dhanmondi, Dhaka',   'Dhaka',    'Basketball', 600, 'Hardwood Indoor',               'Full Court', '["AC Indoor","Scoreboard","Parking"]',            1, 'active', 4.2);

-- ── Demo Sport Categories ──
INSERT INTO sport_categories (name, icon, field_size, equipment) VALUES
('Football',   '⚽', '5-a-side / 7-a-side / 11-a-side', '["Football","Boots","Shin Guards","Goalkeeper Gloves"]'),
('Cricket',    '🏏', '22 yards pitch',                   '["Cricket Bat","Ball","Stumps","Pads","Gloves","Helmet"]'),
('Basketball', '🏀', 'Full Court / Half Court',          '["Basketball","Jerseys","Court Shoes"]');

-- ── Demo Teams ──
INSERT INTO teams (captain_id, name, sport, formation, status) VALUES
(3, 'Dhaka Warriors FC',   'Football', '4-3-3',  'active'),
(3, 'Banani United',        'Football', '3-5-2',  'recruiting');

-- ── Demo Team Members ──
INSERT INTO team_members (team_id, player_id, jersey_number, position, status) VALUES
(1, 3, 10, 'Forward',    'active'),
(1, 4, 7,  'Midfielder', 'active');

-- ── Demo Announcements ──
INSERT INTO announcements (title, content, priority, target_roles, created_by) VALUES
('Welcome to TurfHub!', 'TurfHub is now live. Book turfs, manage teams, and compete in tournaments!', 'update', 'player,captain,owner', 1),
('Maintenance Notice', 'Scheduled maintenance on Sunday 2am-4am. Services may be briefly unavailable.', 'maintenance', 'player,captain,owner,admin', 1);
