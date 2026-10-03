-- ══════════════════════════════════════════════════════════
-- TurfHub MySQL Schema — Full Migration
-- Run this in phpMyAdmin or MySQL CLI
-- ══════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS turfhub
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE turfhub;

-- ══════════════════════════════════════════════════════════
-- 1. USERS (authentication + profile in one table)
-- ══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name     VARCHAR(150) NOT NULL,
  role          ENUM('player', 'captain', 'owner', 'admin') NOT NULL DEFAULT 'player',
  phone         VARCHAR(20) DEFAULT NULL,
  avatar_url    VARCHAR(500) DEFAULT NULL,
  nid_url       VARCHAR(500) DEFAULT NULL,
  cert_url      VARCHAR(500) DEFAULT NULL,
  kyc_status    ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
  kyc_data      JSON DEFAULT NULL,
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- 2. SPORT CATEGORIES
-- ══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS sport_categories (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL UNIQUE,
  icon        VARCHAR(100) DEFAULT NULL,
  field_size  VARCHAR(100) DEFAULT NULL,
  equipment   JSON DEFAULT NULL,
  is_active   TINYINT(1) DEFAULT 1,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- 3. ANNOUNCEMENTS
-- ══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS announcements (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(255) NOT NULL,
  content       TEXT NOT NULL,
  priority      ENUM('critical', 'update', 'maintenance') DEFAULT 'update',
  target_roles  VARCHAR(255) DEFAULT '',
  created_by    INT DEFAULT NULL,
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- 4. TURF GROUNDS
-- ══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS turf_grounds (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  owner_id        INT NOT NULL,
  name            VARCHAR(200) NOT NULL,
  location        VARCHAR(300) NOT NULL,
  district        VARCHAR(100) DEFAULT NULL,
  sport_type      VARCHAR(100) NOT NULL,
  price_per_hour  INT NOT NULL,
  surface_type    VARCHAR(100) DEFAULT NULL,
  field_size      VARCHAR(100) DEFAULT NULL,
  amenities       JSON DEFAULT NULL,
  photos          JSON DEFAULT NULL,
  is_verified     TINYINT(1) DEFAULT 0,
  status          ENUM('active', 'inactive', 'pending_review') DEFAULT 'pending_review',
  rating          DECIMAL(2,1) DEFAULT 0.0,
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- 5. TEAMS
-- ══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS teams (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  captain_id  INT NOT NULL,
  name        VARCHAR(150) NOT NULL,
  sport       VARCHAR(100) NOT NULL,
  logo_url    VARCHAR(500) DEFAULT NULL,
  formation   VARCHAR(20) DEFAULT '4-3-3',
  status      ENUM('active', 'inactive', 'recruiting') DEFAULT 'active',
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (captain_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- 6. TEAM MEMBERS
-- ══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS team_members (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  team_id        INT NOT NULL,
  player_id      INT NOT NULL,
  jersey_number  INT DEFAULT NULL,
  position       VARCHAR(50) DEFAULT NULL,
  status         ENUM('active', 'bench', 'injured', 'pending') DEFAULT 'active',
  joined_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_team_player (team_id, player_id),
  FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
  FOREIGN KEY (player_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- 7. TURF SLOTS
-- ══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS turf_slots (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  turf_id     INT NOT NULL,
  slot_date   DATE NOT NULL,
  start_time  TIME NOT NULL,
  end_time    TIME NOT NULL,
  status      ENUM('available', 'booked', 'maintenance', 'reserved') DEFAULT 'available',
  booked_by   INT DEFAULT NULL,
  price       INT DEFAULT NULL,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_turf_date_time (turf_id, slot_date, start_time),
  FOREIGN KEY (turf_id) REFERENCES turf_grounds(id) ON DELETE CASCADE,
  FOREIGN KEY (booked_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- 8. BOOKINGS
-- ══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS bookings (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  user_id         INT NOT NULL,
  turf_id         INT NOT NULL,
  slot_id         INT NOT NULL,
  total_price     INT NOT NULL,
  payment_method  ENUM('bkash', 'nagad', 'rocket', 'upay', 'card', 'net_banking') DEFAULT NULL,
  trx_id          VARCHAR(50) UNIQUE DEFAULT NULL,
  ref_id          VARCHAR(50) UNIQUE DEFAULT NULL,
  status          ENUM('confirmed', 'pending', 'cancelled', 'completed') DEFAULT 'pending',
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (turf_id) REFERENCES turf_grounds(id) ON DELETE CASCADE,
  FOREIGN KEY (slot_id) REFERENCES turf_slots(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- 9. TOURNAMENTS
-- ══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS tournaments (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  created_by   INT NOT NULL,
  name         VARCHAR(200) NOT NULL,
  sport        VARCHAR(100) NOT NULL,
  turf_id      INT DEFAULT NULL,
  entry_fee    INT DEFAULT 0,
  prize_pool   INT DEFAULT 0,
  max_teams    INT DEFAULT 8,
  start_date   DATE DEFAULT NULL,
  end_date     DATE DEFAULT NULL,
  rules        TEXT DEFAULT NULL,
  status       ENUM('draft', 'open', 'in_progress', 'completed') DEFAULT 'draft',
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (turf_id) REFERENCES turf_grounds(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- 10. TOURNAMENT TEAMS (Registration)
-- ══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS tournament_teams (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  tournament_id  INT NOT NULL,
  team_id        INT NOT NULL,
  status         ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  registered_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_tourn_team (tournament_id, team_id),
  FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
  FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- 11. FIXTURES (Match Schedule & Results)
-- ══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS fixtures (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  tournament_id   INT NOT NULL,
  home_team_id    INT DEFAULT NULL,
  away_team_id    INT DEFAULT NULL,
  match_date      DATETIME DEFAULT NULL,
  venue_id        INT DEFAULT NULL,
  round_name      VARCHAR(50) DEFAULT NULL,
  home_score      INT DEFAULT 0,
  away_score      INT DEFAULT 0,
  scorers         JSON DEFAULT NULL,
  status          ENUM('upcoming', 'live', 'completed') DEFAULT 'upcoming',
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
  FOREIGN KEY (home_team_id) REFERENCES teams(id) ON DELETE SET NULL,
  FOREIGN KEY (away_team_id) REFERENCES teams(id) ON DELETE SET NULL,
  FOREIGN KEY (venue_id) REFERENCES turf_grounds(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- 12. MESSAGES (Chat System)
-- ══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS messages (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  sender_id    INT NOT NULL,
  receiver_id  INT NOT NULL,
  content      TEXT NOT NULL,
  is_read      TINYINT(1) DEFAULT 0,
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- PERFORMANCE INDEXES (Created during table definitions or safely)
-- ══════════════════════════════════════════════════════════

