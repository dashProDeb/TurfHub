# 🏗️ TurfHub Backend Implementation Guide
## MySQL + PHP (REST API) — Step-by-Step Workflow

---

## 📌 Overview

This document provides a **complete, visual, step-by-step backend implementation plan** for the TurfHub platform. The frontend is already built as a static HTML/CSS/JS codebase with 27+ pages across 4 user roles (**Player**, **Captain**, **Owner**, **Admin**). The goal is to replace the current `localStorage`-based simulation layer with a real **MySQL + PHP** backend.

The architecture follows a **vanilla PHP REST API** pattern — no frameworks (no Laravel, no Symfony). Every backend operation is a PHP endpoint that the frontend calls via `fetch()`. Authentication uses **PHP sessions + secure cookies**. File uploads use the server filesystem. Real-time messaging uses **Server-Sent Events (SSE)** or **short polling** as a lightweight alternative to WebSockets.

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                     CURRENT STATE → TARGET STATE                            │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   BEFORE (Static Prototype)              AFTER (Live Backend)               │
│   ─────────────────────────              ────────────────────                │
│   • localStorage for auth               • PHP Sessions + MySQL users table  │
│   • Hardcoded HTML data                  • MySQL database (InnoDB)           │
│   • Simulated SSLCOMMERZ                 • Real transaction records in DB   │
│   • No real data persistence             • Full CRUD via PHP REST API       │
│   • Static sidebar role config           • DB-driven role + permissions     │
│   • No real-time chat                    • SSE / Short Polling via PHP      │
│   • No file uploads                      • PHP file uploads (move_uploaded) │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

### Technology Stack Summary

| Layer | Technology | Purpose |
|:---|:---|:---|
| **Database** | MySQL 8.0+ (InnoDB) | Relational data storage, transactions, foreign keys |
| **Server Language** | PHP 8.1+ | REST API endpoints, session management, file uploads |
| **Authentication** | PHP Sessions + `password_hash()` / `password_verify()` | Secure password hashing (bcrypt), session cookies |
| **File Storage** | Server Filesystem (`uploads/` directory) | Turf photos, KYC documents, avatars |
| **Real-Time** | Server-Sent Events (SSE) or Short Polling | Chat messages, live notifications |
| **Frontend ↔ Backend** | `fetch()` API (JSON request/response) | All AJAX communication |
| **Server** | Apache (XAMPP/WAMP) or Nginx + PHP-FPM | Local development & production |

---

## 🗺️ Master Workflow — The 10 Phases

```
  ┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐
  │ PHASE 1 │───▶│ PHASE 2 │───▶│ PHASE 3 │───▶│ PHASE 4 │───▶│ PHASE 5 │
  │ Env &   │    │ Database│    │ Auth    │    │ Turf &  │    │ Booking │
  │ Setup   │    │ Schema  │    │ System  │    │ Slots   │    │ Engine  │
  └─────────┘    └─────────┘    └─────────┘    └─────────┘    └─────────┘
                                                                    │
  ┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐
  │ PHASE 10│◀───│ PHASE 9 │◀───│ PHASE 8 │◀───│ PHASE 7 │◀───│ PHASE 6 │
  │ Deploy &│    │ Admin   │    │ Realtime│    │ Scores &│    │ Teams & │
  │ Polish  │    │ Panel   │    │ Chat    │    │ Fixtures│    │Tourneys │
  └─────────┘    └─────────┘    └─────────┘    └─────────┘    └─────────┘
```

---

## 🔷 PHASE 1 — Environment Setup & Project Structure

### Goal
Set up the local PHP + MySQL development environment and establish the backend directory structure within the existing TurfHub static codebase.

### Steps

```
Step 1.1  ──▶  Install XAMPP / WAMP / MAMP (or use existing installation)
               ├── PHP 8.1+ required
               ├── MySQL 8.0+ required
               ├── Apache with mod_rewrite enabled
               └── phpMyAdmin for visual DB management

Step 1.2  ──▶  Place the TurfHub project inside your web server root:
               ├── XAMPP:  C:\xampp\htdocs\TurfHub\
               ├── WAMP:   C:\wamp64\www\TurfHub\
               └── Access via: http://localhost/TurfHub/

Step 1.3  ──▶  Create the backend directory structure:
```

### Backend Directory Structure

```
TurfHub/
├── backend/
│   └── api/
│       ├── config/
│       │   └── database.php           ← MySQL PDO connection singleton
│       │
│       ├── auth/
│       │   ├── register.php           ← POST: Sign up (email, password, name, role)
│       │   ├── login.php              ← POST: Sign in (email, password)
│       │   ├── logout.php             ← POST: Destroy session
│       │   ├── session.php            ← GET:  Check active session + profile
│       │   └── guard.php              ← Included by protected endpoints (role check)
│       │
│       ├── turfs/
│       │   ├── list.php               ← GET:  All verified turfs (public browsing)
│       │   ├── my-turfs.php           ← GET:  Owner's turfs
│       │   ├── create.php             ← POST: Create new turf ground
│       │   ├── update.php             ← POST: Update turf details
│       │   └── upload-photo.php       ← POST: Upload turf images
│       │
│       ├── slots/
│       │   ├── list.php               ← GET:  Slots for turf on date range
│       │   ├── generate.php           ← POST: Generate hourly slots for a day
│       │   └── update-status.php      ← POST: Reserve / block / maintenance
│       │
│       ├── bookings/
│       │   ├── create.php             ← POST: Create booking + mark slot booked
│       │   ├── user-bookings.php      ← GET:  Current user's booking receipts
│       │   └── owner-bookings.php     ← GET:  Bookings on owner's turfs
│       │
│       ├── teams/
│       │   ├── create.php             ← POST: Create team
│       │   ├── detail.php             ← GET:  Team with roster members
│       │   ├── add-member.php         ← POST: Add player to squad
│       │   ├── update-member.php      ← POST: Update jersey / position / status
│       │   ├── remove-member.php      ← POST: Remove player from team
│       │   ├── recruiting.php         ← GET:  Teams with 'recruiting' status
│       │   └── join-request.php       ← POST: Player requests to join team
│       │
│       ├── tournaments/
│       │   ├── create.php             ← POST: Create tournament
│       │   ├── list.php               ← GET:  Open tournaments
│       │   ├── register-team.php      ← POST: Register team for tournament
│       │   ├── registrations.php      ← GET:  Tournament registrations
│       │   └── update-registration.php← POST: Approve/reject team entry
│       │
│       ├── fixtures/
│       │   ├── list.php               ← GET:  Match fixtures for tournament
│       │   ├── create.php             ← POST: Generate fixture matches
│       │   ├── update-score.php       ← POST: Update match score + scorers
│       │   └── standings.php          ← GET:  Computed league standings
│       │
│       ├── chat/
│       │   ├── conversations.php      ← GET:  List of chat threads
│       │   ├── messages.php           ← GET:  Messages between two users
│       │   ├── send.php               ← POST: Send a message
│       │   └── poll.php               ← GET:  SSE/polling for new messages
│       │
│       ├── admin/
│       │   ├── dashboard-stats.php    ← GET:  Platform KPI metrics
│       │   ├── pending-approvals.php  ← GET:  KYC approval queue
│       │   ├── approve-kyc.php        ← POST: Approve owner verification
│       │   ├── reject-kyc.php         ← POST: Reject owner with reason
│       │   ├── categories.php         ← GET/POST: Sport categories CRUD
│       │   ├── announcements.php      ← GET/POST: System announcements CRUD
│       │   ├── analytics.php          ← GET:  Heatmaps & revenue trends
│       │   └── financial-reports.php  ← GET:  Booking financial ledger
│       │
│       └── uploads/
│           ├── turf-photos/           ← Turf ground images
│           ├── kyc-documents/         ← NID scans, trade licenses, certificates
│           └── avatars/               ← User profile photos
```

### 📄 `backend/api/config/database.php` — MySQL PDO Connection

```php
<?php
// database.php — MySQL PDO connection singleton for TurfHub

class Database {
    private static ?PDO $instance = null;

    private const HOST = 'localhost';
    private const DB   = 'turfhub';
    private const USER = 'root';
    private const PASS = '';            // Set your MySQL password
    private const PORT = 3306;

    public static function connect(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . self::HOST
                 . ';port=' . self::PORT
                 . ';dbname=' . self::DB
                 . ';charset=utf8mb4';

            self::$instance = new PDO($dsn, self::USER, self::PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$instance;
    }
}
```

### 📄 CORS & JSON Response Helper (included at top of every API file)

```php
<?php
// Every API endpoint starts with:
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';
session_start();

function jsonResponse($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function jsonError(string $message, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}
```

### 📄 Frontend API Helper: `api-client.js`

```javascript
// api-client.js — Centralized fetch wrapper for all PHP API calls

const API_BASE = './backend/api';

export async function apiGet(endpoint) {
    const res = await fetch(`${API_BASE}/${endpoint}`, {
        credentials: 'include'    // send session cookie
    });
    if (!res.ok) {
        const err = await res.json();
        throw new Error(err.error || 'Request failed');
    }
    return res.json();
}

export async function apiPost(endpoint, body = {}) {
    const res = await fetch(`${API_BASE}/${endpoint}`, {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    });
    if (!res.ok) {
        const err = await res.json();
        throw new Error(err.error || 'Request failed');
    }
    return res.json();
}

export async function apiUpload(endpoint, formData) {
    const res = await fetch(`${API_BASE}/${endpoint}`, {
        method: 'POST',
        credentials: 'include',
        body: formData   // FormData (no Content-Type header — browser sets multipart boundary)
    });
    if (!res.ok) {
        const err = await res.json();
        throw new Error(err.error || 'Upload failed');
    }
    return res.json();
}
```

### Verification Checklist
- [ ] XAMPP/WAMP running, Apache + MySQL started
- [ ] `http://localhost/TurfHub/` serves the landing page
- [ ] `database.php` connects without error (test with a simple `phpinfo()` or connection test script)
- [ ] `api-client.js` is created and importable in the frontend

---

## 🔷 PHASE 2 — Database Schema Design (MySQL)

### Goal
Design the complete MySQL (InnoDB) schema matching all entities visible across the 27+ HTML pages.

### Entity Relationship Diagram

```
┌───────────────────────────────────────────────────────────────────────────────────────────┐
│                              TURFHUB DATABASE SCHEMA (ERD)                                │
├───────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                           │
│   ┌──────────────┐         ┌──────────────────┐         ┌────────────────┐                │
│   │    users      │────┐    │   turf_grounds   │────┐    │     teams      │                │
│   │──────────────│    │    │──────────────────│    │    │────────────────│                │
│   │ id (INT, PK) │    │    │ id (INT, PK)     │    │    │ id (INT, PK)   │                │
│   │ email        │    │    │ owner_id → users  │    │    │ captain_id →   │                │
│   │ password_hash│    │    │ name             │    │    │ name           │                │
│   │ full_name    │    │    │ location         │    │    │ sport          │                │
│   │ role (ENUM)  │    │    │ sport_type       │    │    │ logo_url       │                │
│   │ phone        │    │    │ price_per_hour   │    │    │ formation      │                │
│   │ avatar_url   │    │    │ surface_type     │    │    │ status (ENUM)  │                │
│   │ nid_url      │    │    │ amenities (JSON) │    │    └───────┬────────┘                │
│   │ cert_url     │    │    │ photos (JSON)    │    │            │                          │
│   │ kyc_status   │    │    │ is_verified      │    │    ┌───────▼────────┐                │
│   │ created_at   │    │    │ status (ENUM)    │    │    │  team_members  │                │
│   └──────┬───────┘    │    └───────┬──────────┘    │    │────────────────│                │
│          │            │            │               │    │ id (INT, PK)   │                │
│          │            │    ┌───────▼──────────┐    │    │ team_id → FK   │                │
│          │            │    │   turf_slots     │    │    │ player_id → FK │                │
│          │            │    │──────────────────│    │    │ jersey_number  │                │
│          │            │    │ id (INT, PK)     │    │    │ position       │                │
│          │            │    │ turf_id → FK     │    │    │ status (ENUM)  │                │
│          │            │    │ slot_date        │    │    └────────────────┘                │
│          │            │    │ start_time       │    │                                       │
│          │            │    │ end_time         │    │    ┌────────────────┐                │
│          │            │    │ status (ENUM)    │◀───┼───▶│   bookings     │                │
│          │            │    │ booked_by → FK   │    │    │────────────────│                │
│          │            │    │ price            │    │    │ id (INT, PK)   │                │
│          │            │    └──────────────────┘    │    │ user_id → FK   │                │
│          │            │                            │    │ turf_id → FK   │                │
│          │            │    ┌──────────────────┐    │    │ slot_id → FK   │                │
│          │            └───▶│  tournaments     │    │    │ total_price    │                │
│          │                 │──────────────────│    │    │ payment_method │                │
│          │                 │ id (INT, PK)     │    │    │ trx_id         │                │
│          │                 │ created_by → FK  │    │    │ ref_id         │                │
│          │                 │ name             │    │    │ status (ENUM)  │                │
│          │                 │ sport            │    │    │ created_at     │                │
│          │                 │ turf_id → FK     │    │    └────────────────┘                │
│          │                 │ entry_fee        │    │                                       │
│          │                 │ prize_pool       │    │    ┌────────────────┐                │
│          │                 │ max_teams        │    │    │    messages    │                │
│          │                 │ start_date       │    │    │────────────────│                │
│          │                 │ end_date         │    │    │ id (INT, PK)   │                │
│          │                 │ status (ENUM)    │    │    │ sender_id → FK │                │
│          │                 └───────┬──────────┘    │    │ receiver_id →FK│                │
│          │                         │               │    │ content        │                │
│          │                 ┌───────▼──────────┐    │    │ is_read        │                │
│          │                 │ tournament_teams │    │    │ created_at     │                │
│          │                 │──────────────────│    │    └────────────────┘                │
│          │                 │ id (INT, PK)     │    │                                       │
│          │                 │ tournament_id →FK│    │    ┌────────────────┐                │
│          │                 │ team_id → FK     │    │    │ announcements  │                │
│          │                 │ status (ENUM)    │    │    │────────────────│                │
│          │                 └──────────────────┘    │    │ id (INT, PK)   │                │
│          │                                         │    │ title          │                │
│          │                 ┌──────────────────┐    │    │ content        │                │
│          └────────────────▶│   fixtures       │    │    │ priority(ENUM) │                │
│                            │──────────────────│    │    │ target_roles   │                │
│                            │ id (INT, PK)     │    │    │ created_by →FK │                │
│                            │ tournament_id →FK│    │    │ created_at     │                │
│                            │ home_team_id →FK │    │    └────────────────┘                │
│                            │ away_team_id →FK │    │                                       │
│                            │ match_date       │    │    ┌────────────────┐                │
│                            │ venue_id → FK    │    │    │sport_categories│                │
│                            │ round_name       │    │    │────────────────│                │
│                            │ home_score       │    │    │ id (INT, PK)   │                │
│                            │ away_score       │    │    │ name           │                │
│                            │ status (ENUM)    │    │    │ field_size     │                │
│                            │ scorers (JSON)   │    │    │ equipment(JSON)│                │
│                            └──────────────────┘    │    │ icon           │                │
│                                                    │    │ is_active      │                │
│                                                    │    └────────────────┘                │
└───────────────────────────────────────────────────────────────────────────────────────────┘
```

### SQL Table Creation Order (Dependency-Sorted)

```
Execution Order:
  1. users               (no foreign keys — top-level entity)
  2. sport_categories     (standalone)
  3. announcements        (depends on: users)
  4. turf_grounds         (depends on: users)
  5. teams                (depends on: users)
  6. team_members         (depends on: teams, users)
  7. turf_slots           (depends on: turf_grounds, users)
  8. bookings             (depends on: users, turf_grounds, turf_slots)
  9. tournaments          (depends on: users, turf_grounds)
  10. tournament_teams    (depends on: tournaments, teams)
  11. fixtures            (depends on: tournaments, teams, turf_grounds)
  12. messages            (depends on: users)
```

### Complete SQL Migration (`sql/001_schema.sql`)

```sql
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
CREATE TABLE users (
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
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ══════════════════════════════════════════════════════════
-- 2. SPORT CATEGORIES
-- ══════════════════════════════════════════════════════════
CREATE TABLE sport_categories (
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
CREATE TABLE announcements (
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
CREATE TABLE turf_grounds (
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
CREATE TABLE teams (
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
CREATE TABLE team_members (
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
CREATE TABLE turf_slots (
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
CREATE TABLE bookings (
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
CREATE TABLE tournaments (
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
CREATE TABLE tournament_teams (
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
CREATE TABLE fixtures (
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
CREATE TABLE messages (
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
-- INDEXES for performance
-- ══════════════════════════════════════════════════════════
CREATE INDEX idx_turf_slots_turf_date ON turf_slots(turf_id, slot_date);
CREATE INDEX idx_bookings_user ON bookings(user_id);
CREATE INDEX idx_bookings_turf ON bookings(turf_id);
CREATE INDEX idx_messages_receiver ON messages(receiver_id, is_read);
CREATE INDEX idx_messages_sender_receiver ON messages(sender_id, receiver_id);
CREATE INDEX idx_fixtures_tournament ON fixtures(tournament_id);
CREATE INDEX idx_team_members_team ON team_members(team_id);
CREATE INDEX idx_team_members_player ON team_members(player_id);
CREATE INDEX idx_turf_grounds_owner ON turf_grounds(owner_id);
CREATE INDEX idx_turf_grounds_verified ON turf_grounds(is_verified, status);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_kyc ON users(kyc_status);
```

---

## 🔷 PHASE 3 — Authentication System (PHP Sessions)

### Goal
Replace the `localStorage`-based `auth.js` role simulation with real PHP session-based authentication using `password_hash()` and `password_verify()`.

### Current vs. Target Architecture

```
┌────────────────────────────────────────────────────────────────────────┐
│                         AUTH MIGRATION MAP                              │
├──────────────────────────────┬─────────────────────────────────────────┤
│   CURRENT (auth.js)          │   TARGET (PHP Sessions + MySQL)         │
├──────────────────────────────┼─────────────────────────────────────────┤
│ setRole(role)                │ POST /api/auth/register.php             │
│ → localStorage               │ → INSERT users + password_hash()       │
│                              │ → session_start() + $_SESSION           │
│                              │                                         │
│ getRole()                    │ GET /api/auth/session.php               │
│ → localStorage.getItem       │ → $_SESSION['user_id'] → SELECT users  │
│                              │                                         │
│ handleSubmit() → redirect    │ POST /api/auth/login.php               │
│                              │ → password_verify() → set session      │
│                              │ → return profile + role for redirect    │
│                              │                                         │
│ signOut() → clear storage    │ POST /api/auth/logout.php              │
│                              │ → session_destroy()                     │
│                              │                                         │
│ requireRole()                │ require guard.php in every endpoint     │
│ → check localStorage         │ → check $_SESSION + user role in DB     │
└──────────────────────────────┴─────────────────────────────────────────┘
```

### Steps

```
Step 3.1  ──▶  Create PHP auth endpoints:
               ├── register.php  → hash password, INSERT user, start session
               ├── login.php     → verify password, start session
               ├── logout.php    → destroy session
               ├── session.php   → return current user profile (or 401)
               └── guard.php     → role-checking include file

Step 3.2  ──▶  Update login.html:
               ├── Sign Up form → fetch('/api/auth/register.php', { POST })
               ├── Sign In form → fetch('/api/auth/login.php', { POST })
               ├── Role selection → sent as field during registration
               └── Redirect → based on returned profile.role

Step 3.3  ──▶  Update all dashboard pages:
               ├── On page load → fetch('/api/auth/session.php')
               ├── If 401 → redirect to login.html
               └── If wrong role → redirect to login.html

Step 3.4  ──▶  Update layout.js to read profile from session API
               instead of localStorage
```

### 📄 `backend/api/auth/register.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$fullName = trim($input['full_name'] ?? '');
$role     = $input['role'] ?? 'player';

// Validation
if (!$email || !$password || !$fullName) {
    http_response_code(400);
    echo json_encode(['error' => 'Email, password, and name are required']);
    exit;
}

if (!in_array($role, ['player', 'captain', 'owner', 'admin'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid role']);
    exit;
}

$db = Database::connect();

// Check if email already exists
$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Email already registered']);
    exit;
}

// Hash password and insert
$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $db->prepare(
    'INSERT INTO users (email, password_hash, full_name, role) VALUES (?, ?, ?, ?)'
);
$stmt->execute([$email, $hash, $fullName, $role]);
$userId = (int) $db->lastInsertId();

// Start session
$_SESSION['user_id'] = $userId;
$_SESSION['role']    = $role;

echo json_encode([
    'success' => true,
    'user' => [
        'id'        => $userId,
        'email'     => $email,
        'full_name' => $fullName,
        'role'      => $role
    ]
]);
```

### 📄 `backend/api/auth/login.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

$db = Database::connect();
$stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid email or password']);
    exit;
}

// Start session
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['role']    = $user['role'];

// Return profile (exclude password hash)
unset($user['password_hash']);
echo json_encode(['success' => true, 'user' => $user]);
```

### 📄 `backend/api/auth/session.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT id, email, full_name, role, phone, avatar_url, kyc_status, created_at
     FROM users WHERE id = ?'
);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    http_response_code(401);
    echo json_encode(['error' => 'User not found']);
    exit;
}

echo json_encode(['user' => $user]);
```

### 📄 `backend/api/auth/logout.php`

```php
<?php
header('Content-Type: application/json');
session_start();
session_unset();
session_destroy();
echo json_encode(['success' => true]);
```

### 📄 `backend/api/auth/guard.php` (Role Gate — included by protected endpoints)

```php
<?php
// guard.php — Include at the top of any protected API endpoint
// Usage: require_once __DIR__ . '/../auth/guard.php';
//        guardRole('owner');           // single role
//        guardRole('captain', 'player'); // multiple allowed roles

session_start();

function guardRole(string ...$allowedRoles): array {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }

    $db = Database::connect();
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    if (count($allowedRoles) > 0 && !in_array($user['role'], $allowedRoles)) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden: insufficient role']);
        exit;
    }

    unset($user['password_hash']);
    return $user;
}
```

### Frontend Auth Integration: Updated `auth.js` Wrapper

```javascript
// auth.js — Updated to call PHP backend instead of localStorage
import { apiGet, apiPost } from './api-client.js';

const ROLE_DASHBOARDS = {
    owner:   'owner-dashboard.html',
    captain: 'captain-dashboard.html',
    player:  'player-dashboard.html',
    admin:   'admin-dashboard.html'
};

export async function signUp(email, password, fullName, role) {
    return apiPost('auth/register.php', { email, password, full_name: fullName, role });
}

export async function signIn(email, password) {
    return apiPost('auth/login.php', { email, password });
}

export async function getProfile() {
    try {
        const data = await apiGet('auth/session.php');
        return data.user;
    } catch {
        return null;
    }
}

export async function signOut() {
    await apiPost('auth/logout.php');
    window.location.href = 'login.html';
}

export function getDashboardUrl(role) {
    return ROLE_DASHBOARDS[role] || 'player-dashboard.html';
}

export async function requireRole(...allowedRoles) {
    const profile = await getProfile();
    if (!profile || (allowedRoles.length > 0 && !allowedRoles.includes(profile.role))) {
        window.location.href = 'login.html';
        return null;
    }
    return profile;
}
```

### Page-Level Auth Integration

```
┌─────────────────────────────────────────────────────────────────┐
│                  AUTH GUARD PER PAGE                             │
├──────────────────────────────┬──────────────────────────────────┤
│ Page                         │ Required Role(s)                 │
├──────────────────────────────┼──────────────────────────────────┤
│ index.html                   │ None (public)                    │
│ contact.html                 │ None (public)                    │
│ login.html                   │ None (public)                    │
│ admin-dashboard.html         │ admin                            │
│ admin-approvals.html         │ admin                            │
│ admin-analytics.html         │ admin                            │
│ admin-categories.html        │ admin                            │
│ admin-reports.html           │ admin                            │
│ admin-announcements.html     │ admin                            │
│ owner-dashboard.html         │ owner                            │
│ manage-turf.html             │ owner                            │
│ slot-calendar.html           │ owner                            │
│ owner-tournament.html        │ owner                            │
│ score-entry.html             │ owner                            │
│ owner-chat.html              │ owner                            │
│ owner-reports.html           │ owner                            │
│ captain-dashboard.html       │ captain                          │
│ book-turf.html               │ captain, player                  │
│ manage-team.html             │ captain                          │
│ create-tournament.html       │ captain, owner                   │
│ fixtures.html                │ captain, player                  │
│ booking-receipt.html         │ captain, player                  │
│ chat.html                    │ captain, owner                   │
│ tournament-registration.html │ captain                          │
│ player-dashboard.html        │ player                           │
│ turf-detail.html             │ player                           │
│ player-fixtures.html         │ player                           │
│ player-receipts.html         │ player                           │
│ player-chat.html             │ player                           │
│ join-team.html               │ player                           │
└──────────────────────────────┴──────────────────────────────────┘
```

---

## 🔷 PHASE 4 — Turf Grounds & Slot Management

### Goal
Replace hardcoded turf cards and slot matrices with live data from MySQL via PHP API endpoints.

### Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      TURF & SLOT DATA FLOW                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│   Owner: manage-turf.html                                                   │
│   ┌─────────────────────┐                                                   │
│   │ Add / Edit Turf Form│                                                   │
│   │ • Name, Location    │──POST /api/turfs/create.php──▶ turf_grounds table │
│   │ • Price, Amenities  │──POST /api/turfs/update.php──▶ turf_grounds table │
│   │ • Upload Photos     │──POST /api/turfs/upload-photo.php──▶ uploads/     │
│   └─────────────────────┘                                                   │
│                                                                             │
│   Owner: slot-calendar.html                                                 │
│   ┌─────────────────────┐                                                   │
│   │ 7-Day Slot Matrix   │                                                   │
│   │ • Generate weekly   │──POST /api/slots/generate.php──▶ turf_slots table │
│   │   hourly slots      │                                                   │
│   │ • Reserve / Block / │──POST /api/slots/update-status.php──▶ turf_slots  │
│   │   Mark maintenance  │                                                   │
│   └─────────────────────┘                                                   │
│                                                                             │
│   Player: turf-detail.html                                                  │
│   ┌─────────────────────┐                                                   │
│   │ Master-Detail View  │                                                   │
│   │ • Browse all turfs  │◀──GET /api/turfs/list.php       (is_verified=1)  │
│   │ • View slot matrix  │◀──GET /api/slots/list.php       (turf_id, date)  │
│   │ • Book selected     │──POST /api/bookings/create.php──▶ bookings table │
│   └─────────────────────┘                                                   │
│                                                                             │
│   Captain: book-turf.html                                                   │
│   ┌─────────────────────┐                                                   │
│   │ 4-Step Stepper      │                                                   │
│   │ 1. Select Sport     │◀──GET /api/admin/categories.php                   │
│   │ 2. Pick Turf        │◀──GET /api/turfs/list.php?sport=Football          │
│   │ 3. Choose Slots     │◀──GET /api/slots/list.php?status=available        │
│   │ 4. Pay (SSLCOMMERZ) │──POST /api/bookings/create.php──▶ bookings       │
│   └─────────────────────┘                                                   │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 📄 `backend/api/turfs/list.php` — Get Verified Turfs

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$db = Database::connect();

$sport = $_GET['sport'] ?? null;
$district = $_GET['district'] ?? null;

$sql = 'SELECT * FROM turf_grounds WHERE is_verified = 1 AND status = "active"';
$params = [];

if ($sport) {
    $sql .= ' AND sport_type = ?';
    $params[] = $sport;
}
if ($district) {
    $sql .= ' AND district = ?';
    $params[] = $district;
}

$sql .= ' ORDER BY rating DESC, created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$turfs = $stmt->fetchAll();

// Decode JSON fields
foreach ($turfs as &$turf) {
    $turf['amenities'] = json_decode($turf['amenities'] ?? '[]', true);
    $turf['photos']    = json_decode($turf['photos'] ?? '[]', true);
}

echo json_encode(['turfs' => $turfs]);
```

### 📄 `backend/api/turfs/my-turfs.php` — Get Owner's Turfs

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

$db = Database::connect();
$stmt = $db->prepare('SELECT * FROM turf_grounds WHERE owner_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$turfs = $stmt->fetchAll();

foreach ($turfs as &$turf) {
    $turf['amenities'] = json_decode($turf['amenities'] ?? '[]', true);
    $turf['photos']    = json_decode($turf['photos'] ?? '[]', true);
}

echo json_encode(['turfs' => $turfs]);
```

### 📄 `backend/api/turfs/create.php` — Create New Turf

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$db = Database::connect();
$stmt = $db->prepare(
    'INSERT INTO turf_grounds (owner_id, name, location, district, sport_type, price_per_hour, surface_type, field_size, amenities)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([
    $user['id'],
    $input['name'],
    $input['location'],
    $input['district'] ?? null,
    $input['sport_type'],
    (int) $input['price_per_hour'],
    $input['surface_type'] ?? null,
    $input['field_size'] ?? null,
    json_encode($input['amenities'] ?? [])
]);

$turfId = (int) $db->lastInsertId();

echo json_encode(['success' => true, 'turf_id' => $turfId]);
```

### 📄 `backend/api/turfs/upload-photo.php` — Upload Turf Photo

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

$turfId = (int) ($_POST['turf_id'] ?? 0);

if (!$turfId || !isset($_FILES['photo'])) {
    http_response_code(400);
    echo json_encode(['error' => 'turf_id and photo file are required']);
    exit;
}

// Verify ownership
$db = Database::connect();
$stmt = $db->prepare('SELECT id, photos FROM turf_grounds WHERE id = ? AND owner_id = ?');
$stmt->execute([$turfId, $user['id']]);
$turf = $stmt->fetch();

if (!$turf) {
    http_response_code(403);
    echo json_encode(['error' => 'Turf not found or not owned by you']);
    exit;
}

// Handle upload
$uploadDir = __DIR__ . '/../uploads/turf-photos/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
$filename = 'turf_' . $turfId . '_' . time() . '.' . $ext;
$filepath = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['photo']['tmp_name'], $filepath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file']);
    exit;
}

$photoUrl = 'backend/api/uploads/turf-photos/' . $filename;

// Append to photos JSON array
$photos = json_decode($turf['photos'] ?? '[]', true);
$photos[] = $photoUrl;

$stmt = $db->prepare('UPDATE turf_grounds SET photos = ? WHERE id = ?');
$stmt->execute([json_encode($photos), $turfId]);

echo json_encode(['success' => true, 'photo_url' => $photoUrl]);
```

### 📄 `backend/api/slots/list.php` — Get Slots for Date Range

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$turfId   = (int) ($_GET['turf_id'] ?? 0);
$startDate = $_GET['start_date'] ?? date('Y-m-d');
$endDate   = $_GET['end_date'] ?? date('Y-m-d', strtotime('+6 days'));

if (!$turfId) {
    http_response_code(400);
    echo json_encode(['error' => 'turf_id is required']);
    exit;
}

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT ts.*, u.full_name AS booked_by_name
     FROM turf_slots ts
     LEFT JOIN users u ON u.id = ts.booked_by
     WHERE ts.turf_id = ? AND ts.slot_date BETWEEN ? AND ?
     ORDER BY ts.slot_date ASC, ts.start_time ASC'
);
$stmt->execute([$turfId, $startDate, $endDate]);
$slots = $stmt->fetchAll();

echo json_encode(['slots' => $slots]);
```

### 📄 `backend/api/slots/generate.php` — Generate Hourly Slots (Owner)

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

$input = json_decode(file_get_contents('php://input'), true);
$turfId = (int) ($input['turf_id'] ?? 0);
$date   = $input['date'] ?? '';
$price  = (int) ($input['price_per_hour'] ?? 0);

if (!$turfId || !$date || !$price) {
    http_response_code(400);
    echo json_encode(['error' => 'turf_id, date, and price_per_hour are required']);
    exit;
}

// Verify ownership
$db = Database::connect();
$stmt = $db->prepare('SELECT id FROM turf_grounds WHERE id = ? AND owner_id = ?');
$stmt->execute([$turfId, $user['id']]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'Turf not owned by you']);
    exit;
}

// Generate slots from 06:00 to 23:00 (INSERT IGNORE to skip duplicates)
$stmt = $db->prepare(
    'INSERT IGNORE INTO turf_slots (turf_id, slot_date, start_time, end_time, status, price)
     VALUES (?, ?, ?, ?, "available", ?)'
);

for ($h = 6; $h <= 23; $h++) {
    $start = sprintf('%02d:00:00', $h);
    $end   = sprintf('%02d:00:00', $h + 1);
    $stmt->execute([$turfId, $date, $start, $end, $price]);
}

echo json_encode(['success' => true, 'message' => 'Slots generated for ' . $date]);
```

### 📄 `backend/api/slots/update-status.php` — Update Slot Status (Owner)

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

$input  = json_decode(file_get_contents('php://input'), true);
$slotId = (int) ($input['slot_id'] ?? 0);
$status = $input['status'] ?? '';

if (!$slotId || !in_array($status, ['available', 'maintenance', 'reserved'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Valid slot_id and status are required']);
    exit;
}

// Verify the slot belongs to owner's turf
$db = Database::connect();
$stmt = $db->prepare(
    'SELECT ts.id FROM turf_slots ts
     JOIN turf_grounds tg ON tg.id = ts.turf_id
     WHERE ts.id = ? AND tg.owner_id = ?'
);
$stmt->execute([$slotId, $user['id']]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'Slot not found or not on your turf']);
    exit;
}

$stmt = $db->prepare('UPDATE turf_slots SET status = ? WHERE id = ?');
$stmt->execute([$status, $slotId]);

echo json_encode(['success' => true]);
```

---

## 🔷 PHASE 5 — Booking Engine & Payment Integration

### Goal
Create the booking flow connecting the SSLCOMMERZ dummy checkout UI to real database records.

### Booking Flow Diagram

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                      BOOKING & PAYMENT FLOW                                  │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ┌───────────────────────────────────────────┐                               │
│  │ Player: turf-detail.html                  │                               │
│  │ Captain: book-turf.html                   │                               │
│  │                                           │                               │
│  │ 1. User selects available slot            │                               │
│  │ 2. Clicks "Proceed to Pay"               │                               │
│  │ 3. SSLCOMMERZ modal opens                │                               │
│  │ 4. User selects bKash/Nagad/Card          │                               │
│  │ 5. Simulated OTP & spinner animation     │                               │
│  │ 6. "Payment Authorized!"                 │                               │
│  └───────────┬─────────────────────────────┘                                │
│              │                                                               │
│              ▼                                                               │
│  ┌─────────────────────────────────────────┐                                 │
│  │ JavaScript Payment Handler              │                                 │
│  │                                         │                                 │
│  │ 1. Generate unique trx_id & ref_id      │                                 │
│  │ 2. POST /api/bookings/create.php {      │                                 │
│  │      turf_id, slot_id,                  │                                 │
│  │      total_price, payment_method        │                                 │
│  │    }                                    │                                 │
│  │    → PHP inserts into bookings table    │                                 │
│  │    → PHP updates turf_slots to 'booked' │                                 │
│  │    → PHP returns trx_id, ref_id         │                                 │
│  │ 3. Show success animation               │                                 │
│  │ 4. Redirect to receipt page             │                                 │
│  └───────────┬─────────────────────────────┘                                 │
│              │                                                               │
│              ▼                                                               │
│  ┌─────────────────────────────────────────┐                                 │
│  │ booking-receipt.html / player-receipts  │                                 │
│  │ • GET /api/bookings/user-bookings.php   │                                 │
│  │ • Display confirmed receipt cards       │                                 │
│  │ • Show trx_id, ref_id, date, venue      │                                 │
│  └─────────────────────────────────────────┘                                 │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

### 📄 `backend/api/bookings/create.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain');

$input = json_decode(file_get_contents('php://input'), true);
$turfId        = (int) ($input['turf_id'] ?? 0);
$slotId        = (int) ($input['slot_id'] ?? 0);
$totalPrice    = (int) ($input['total_price'] ?? 0);
$paymentMethod = $input['payment_method'] ?? 'bkash';

if (!$turfId || !$slotId || !$totalPrice) {
    http_response_code(400);
    echo json_encode(['error' => 'turf_id, slot_id, and total_price are required']);
    exit;
}

$db = Database::connect();

// Verify slot is available
$stmt = $db->prepare('SELECT * FROM turf_slots WHERE id = ? AND status = "available" FOR UPDATE');
$db->beginTransaction();

try {
    $stmt->execute([$slotId]);
    $slot = $stmt->fetch();

    if (!$slot) {
        $db->rollBack();
        http_response_code(409);
        echo json_encode(['error' => 'Slot is no longer available']);
        exit;
    }

    // Generate unique IDs
    $trxId = 'SSL-TH-' . random_int(100000, 999999);
    $refId = 'TH-' . date('Y') . '-' . date('md') . '-' . random_int(100, 999);

    // Insert booking
    $stmt = $db->prepare(
        'INSERT INTO bookings (user_id, turf_id, slot_id, total_price, payment_method, trx_id, ref_id, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, "confirmed")'
    );
    $stmt->execute([$user['id'], $turfId, $slotId, $totalPrice, $paymentMethod, $trxId, $refId]);
    $bookingId = (int) $db->lastInsertId();

    // Mark slot as booked
    $stmt = $db->prepare('UPDATE turf_slots SET status = "booked", booked_by = ? WHERE id = ?');
    $stmt->execute([$user['id'], $slotId]);

    $db->commit();

    echo json_encode([
        'success' => true,
        'booking' => [
            'id'             => $bookingId,
            'trx_id'         => $trxId,
            'ref_id'         => $refId,
            'total_price'    => $totalPrice,
            'payment_method' => $paymentMethod,
            'status'         => 'confirmed'
        ]
    ]);
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Booking failed: ' . $e->getMessage()]);
}
```

> [!IMPORTANT]
> The booking uses a **MySQL transaction** with `FOR UPDATE` row lock to prevent double-booking the same slot when two users click "Pay" simultaneously.

### 📄 `backend/api/bookings/user-bookings.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain');

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT b.*, tg.name AS turf_name, tg.location AS turf_location, tg.sport_type,
            ts.slot_date, ts.start_time, ts.end_time
     FROM bookings b
     JOIN turf_grounds tg ON tg.id = b.turf_id
     JOIN turf_slots ts ON ts.id = b.slot_id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC'
);
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();

echo json_encode(['bookings' => $bookings]);
```

### 📄 `backend/api/bookings/owner-bookings.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT b.*, u.full_name AS customer_name, u.phone AS customer_phone,
            tg.name AS turf_name, ts.slot_date, ts.start_time, ts.end_time
     FROM bookings b
     JOIN users u ON u.id = b.user_id
     JOIN turf_grounds tg ON tg.id = b.turf_id
     JOIN turf_slots ts ON ts.id = b.slot_id
     WHERE tg.owner_id = ?
     ORDER BY b.created_at DESC'
);
$stmt->execute([$user['id']]);
$bookings = $stmt->fetchAll();

echo json_encode(['bookings' => $bookings]);
```

---

## 🔷 PHASE 6 — Teams & Tournament System

### Goal
Connect team management, player recruitment, tournament creation, and tournament registration.

### System Flow

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                      TEAMS & TOURNAMENTS FLOW                                │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  Captain: manage-team.html                                                   │
│  ┌────────────────────────┐          ┌───────────────────────┐               │
│  │ Create Team            │──POST──▶ │ /api/teams/create.php │               │
│  │ Add/Remove Players     │──POST──▶ │ /api/teams/add-member │               │
│  │ Set Formation          │──POST──▶ │ /api/teams/update     │               │
│  │ Change Jersey Numbers  │──POST──▶ │ /api/teams/update-mem │               │
│  └────────────────────────┘          └───────────────────────┘               │
│                                                                              │
│  Player: join-team.html                                                      │
│  ┌────────────────────────┐          ┌───────────────────────┐               │
│  │ Browse recruiting teams│◀──GET──  │ /api/teams/recruiting │               │
│  │ Send join request      │──POST──▶ │ /api/teams/join-req.  │               │
│  └────────────────────────┘          └───────────────────────┘               │
│                                                                              │
│  Captain/Owner: create-tournament.html                                       │
│  ┌────────────────────────┐          ┌───────────────────────┐               │
│  │ Create Tournament Form │──POST──▶ │ /api/tournaments/     │               │
│  │ • Name, Sport, Fees    │          │   create.php           │               │
│  │ • Prize Pool, Max Teams│          │                       │               │
│  │ • Select Venue Turf    │          │                       │               │
│  └────────────────────────┘          └───────────────────────┘               │
│                                                                              │
│  Captain: tournament-registration.html                                       │
│  ┌────────────────────────┐          ┌───────────────────────┐               │
│  │ Browse open tournaments│◀──GET──  │ /api/tournaments/list │               │
│  │ Register team + pay fee│──POST──▶ │ /api/tournaments/     │               │
│  │                        │          │   register-team.php    │               │
│  └────────────────────────┘          └───────────────────────┘               │
│                                                                              │
│  Owner: owner-tournament.html                                                │
│  ┌────────────────────────┐          ┌───────────────────────┐               │
│  │ View registrations     │◀──GET──  │ /api/tournaments/     │               │
│  │ Approve/reject teams   │──POST──▶ │   registrations.php   │               │
│  │ Generate fixtures      │──POST──▶ │ /api/fixtures/create  │               │
│  └────────────────────────┘          └───────────────────────┘               │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

### 📄 `backend/api/teams/create.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('captain');

$input = json_decode(file_get_contents('php://input'), true);

$db = Database::connect();
$stmt = $db->prepare(
    'INSERT INTO teams (captain_id, name, sport, logo_url, formation, status)
     VALUES (?, ?, ?, ?, ?, ?)'
);
$stmt->execute([
    $user['id'],
    $input['name'],
    $input['sport'],
    $input['logo_url'] ?? null,
    $input['formation'] ?? '4-3-3',
    $input['status'] ?? 'active'
]);

$teamId = (int) $db->lastInsertId();

// Auto-add captain as a member
$stmt = $db->prepare(
    'INSERT INTO team_members (team_id, player_id, jersey_number, position, status)
     VALUES (?, ?, 10, "Captain", "active")'
);
$stmt->execute([$teamId, $user['id']]);

echo json_encode(['success' => true, 'team_id' => $teamId]);
```

### 📄 `backend/api/teams/detail.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$teamId = (int) ($_GET['team_id'] ?? 0);
if (!$teamId) {
    http_response_code(400);
    echo json_encode(['error' => 'team_id is required']);
    exit;
}

$db = Database::connect();

// Get team
$stmt = $db->prepare('SELECT t.*, u.full_name AS captain_name FROM teams t JOIN users u ON u.id = t.captain_id WHERE t.id = ?');
$stmt->execute([$teamId]);
$team = $stmt->fetch();

if (!$team) {
    http_response_code(404);
    echo json_encode(['error' => 'Team not found']);
    exit;
}

// Get members
$stmt = $db->prepare(
    'SELECT tm.*, u.full_name, u.avatar_url, u.email
     FROM team_members tm
     JOIN users u ON u.id = tm.player_id
     WHERE tm.team_id = ?
     ORDER BY tm.jersey_number ASC'
);
$stmt->execute([$teamId]);
$team['members'] = $stmt->fetchAll();

echo json_encode(['team' => $team]);
```

### 📄 `backend/api/teams/join-request.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player');

$input  = json_decode(file_get_contents('php://input'), true);
$teamId = (int) ($input['team_id'] ?? 0);

if (!$teamId) {
    http_response_code(400);
    echo json_encode(['error' => 'team_id is required']);
    exit;
}

$db = Database::connect();

// Check if already a member
$stmt = $db->prepare('SELECT id FROM team_members WHERE team_id = ? AND player_id = ?');
$stmt->execute([$teamId, $user['id']]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Already a member or pending request']);
    exit;
}

$stmt = $db->prepare(
    'INSERT INTO team_members (team_id, player_id, status) VALUES (?, ?, "pending")'
);
$stmt->execute([$teamId, $user['id']]);

echo json_encode(['success' => true, 'message' => 'Join request sent']);
```

### 📄 `backend/api/tournaments/create.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('captain', 'owner');

$input = json_decode(file_get_contents('php://input'), true);

$db = Database::connect();
$stmt = $db->prepare(
    'INSERT INTO tournaments (created_by, name, sport, turf_id, entry_fee, prize_pool, max_teams, start_date, end_date, rules, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([
    $user['id'],
    $input['name'],
    $input['sport'],
    $input['turf_id'] ?? null,
    (int) ($input['entry_fee'] ?? 0),
    (int) ($input['prize_pool'] ?? 0),
    (int) ($input['max_teams'] ?? 8),
    $input['start_date'] ?? null,
    $input['end_date'] ?? null,
    $input['rules'] ?? null,
    $input['status'] ?? 'draft'
]);

echo json_encode(['success' => true, 'tournament_id' => (int) $db->lastInsertId()]);
```

### 📄 `backend/api/tournaments/list.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT t.*, tg.name AS venue_name, tg.location AS venue_location,
            (SELECT COUNT(*) FROM tournament_teams tt WHERE tt.tournament_id = t.id) AS registered_teams
     FROM tournaments t
     LEFT JOIN turf_grounds tg ON tg.id = t.turf_id
     WHERE t.status = "open"
     ORDER BY t.start_date ASC'
);
$stmt->execute();
$tournaments = $stmt->fetchAll();

echo json_encode(['tournaments' => $tournaments]);
```

### 📄 `backend/api/tournaments/register-team.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('captain');

$input = json_decode(file_get_contents('php://input'), true);
$tournamentId = (int) ($input['tournament_id'] ?? 0);
$teamId       = (int) ($input['team_id'] ?? 0);

if (!$tournamentId || !$teamId) {
    http_response_code(400);
    echo json_encode(['error' => 'tournament_id and team_id are required']);
    exit;
}

$db = Database::connect();

// Verify captain owns the team
$stmt = $db->prepare('SELECT id FROM teams WHERE id = ? AND captain_id = ?');
$stmt->execute([$teamId, $user['id']]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'You are not the captain of this team']);
    exit;
}

// Check if already registered
$stmt = $db->prepare('SELECT id FROM tournament_teams WHERE tournament_id = ? AND team_id = ?');
$stmt->execute([$tournamentId, $teamId]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Team already registered']);
    exit;
}

$stmt = $db->prepare(
    'INSERT INTO tournament_teams (tournament_id, team_id, status) VALUES (?, ?, "pending")'
);
$stmt->execute([$tournamentId, $teamId]);

echo json_encode(['success' => true, 'message' => 'Registration submitted']);
```

---

## 🔷 PHASE 7 — Scores, Fixtures & Standings

### Goal
Replace static fixture cards and standings tables with live DB-powered data. Enable owners to enter live scores.

### Page-to-API Mapping

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                     FIXTURES & SCORES INTEGRATION                            │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   score-entry.html (Owner)                                                   │
│   ┌──────────────────────────┐                                               │
│   │ Match Scoreboard UI      │                                               │
│   │ • Select tournament      │◀── GET /api/tournaments/list.php (owner's)    │
│   │ • Select fixture/match   │◀── GET /api/fixtures/list.php?tournament_id=  │
│   │ • Tap +/- goals          │──  POST /api/fixtures/update-score.php        │
│   │ • Add scorer names       │──  (scorers JSON appended)                    │
│   │ • Set status (Live/FT)   │──  POST /api/fixtures/update-score.php        │
│   └──────────────────────────┘                                               │
│                                                                              │
│   fixtures.html / player-fixtures.html                                       │
│   ┌──────────────────────────┐                                               │
│   │ Match Fixtures Tab       │◀── GET /api/fixtures/list.php?tournament_id=  │
│   │ • Upcoming / Live / FT   │                                               │
│   │                          │                                               │
│   │ League Standings Tab     │◀── GET /api/fixtures/standings.php            │
│   │ • W/D/L, GF, GA, Pts    │    (Computed by PHP from fixtures data)        │
│   └──────────────────────────┘                                               │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

### 📄 `backend/api/fixtures/update-score.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('owner');

$input     = json_decode(file_get_contents('php://input'), true);
$fixtureId = (int) ($input['fixture_id'] ?? 0);

if (!$fixtureId) {
    http_response_code(400);
    echo json_encode(['error' => 'fixture_id is required']);
    exit;
}

$db = Database::connect();

// Verify owner has access to this fixture's tournament venue
$stmt = $db->prepare(
    'SELECT f.id FROM fixtures f
     JOIN tournaments t ON t.id = f.tournament_id
     JOIN turf_grounds tg ON tg.id = t.turf_id
     WHERE f.id = ? AND tg.owner_id = ?'
);
$stmt->execute([$fixtureId, $user['id']]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'No access to this fixture']);
    exit;
}

// Build dynamic UPDATE
$updates = [];
$params  = [];

if (isset($input['home_score'])) {
    $updates[] = 'home_score = ?';
    $params[]  = (int) $input['home_score'];
}
if (isset($input['away_score'])) {
    $updates[] = 'away_score = ?';
    $params[]  = (int) $input['away_score'];
}
if (isset($input['status'])) {
    $updates[] = 'status = ?';
    $params[]  = $input['status'];
}
if (isset($input['scorers'])) {
    $updates[] = 'scorers = ?';
    $params[]  = json_encode($input['scorers']);
}

if (empty($updates)) {
    http_response_code(400);
    echo json_encode(['error' => 'No fields to update']);
    exit;
}

$params[] = $fixtureId;
$sql = 'UPDATE fixtures SET ' . implode(', ', $updates) . ' WHERE id = ?';
$stmt = $db->prepare($sql);
$stmt->execute($params);

echo json_encode(['success' => true]);
```

### 📄 `backend/api/fixtures/standings.php` — Computed League Standings

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$tournamentId = (int) ($_GET['tournament_id'] ?? 0);
if (!$tournamentId) {
    http_response_code(400);
    echo json_encode(['error' => 'tournament_id is required']);
    exit;
}

$db = Database::connect();

// Get all completed fixtures for this tournament
$stmt = $db->prepare(
    'SELECT home_team_id, away_team_id, home_score, away_score
     FROM fixtures
     WHERE tournament_id = ? AND status = "completed"'
);
$stmt->execute([$tournamentId]);
$matches = $stmt->fetchAll();

// Get all team names in this tournament
$stmt = $db->prepare(
    'SELECT t.id, t.name FROM teams t
     JOIN tournament_teams tt ON tt.team_id = t.id
     WHERE tt.tournament_id = ? AND tt.status = "approved"'
);
$stmt->execute([$tournamentId]);
$teamsMap = [];
foreach ($stmt->fetchAll() as $team) {
    $teamsMap[$team['id']] = [
        'team_id'       => $team['id'],
        'team_name'     => $team['name'],
        'played'        => 0,
        'won'           => 0,
        'drawn'         => 0,
        'lost'          => 0,
        'goals_for'     => 0,
        'goals_against' => 0,
        'goal_diff'     => 0,
        'points'        => 0
    ];
}

// Compute standings from match results
foreach ($matches as $m) {
    $homeId = $m['home_team_id'];
    $awayId = $m['away_team_id'];
    $hg = (int) $m['home_score'];
    $ag = (int) $m['away_score'];

    if (!isset($teamsMap[$homeId]) || !isset($teamsMap[$awayId])) continue;

    // Home team stats
    $teamsMap[$homeId]['played']++;
    $teamsMap[$homeId]['goals_for']     += $hg;
    $teamsMap[$homeId]['goals_against'] += $ag;

    // Away team stats
    $teamsMap[$awayId]['played']++;
    $teamsMap[$awayId]['goals_for']     += $ag;
    $teamsMap[$awayId]['goals_against'] += $hg;

    if ($hg > $ag) {
        $teamsMap[$homeId]['won']++;
        $teamsMap[$homeId]['points'] += 3;
        $teamsMap[$awayId]['lost']++;
    } elseif ($hg < $ag) {
        $teamsMap[$awayId]['won']++;
        $teamsMap[$awayId]['points'] += 3;
        $teamsMap[$homeId]['lost']++;
    } else {
        $teamsMap[$homeId]['drawn']++;
        $teamsMap[$homeId]['points'] += 1;
        $teamsMap[$awayId]['drawn']++;
        $teamsMap[$awayId]['points'] += 1;
    }
}

// Calculate goal difference & sort
$standings = array_values($teamsMap);
foreach ($standings as &$s) {
    $s['goal_diff'] = $s['goals_for'] - $s['goals_against'];
}
unset($s);

usort($standings, function($a, $b) {
    if ($b['points'] !== $a['points']) return $b['points'] - $a['points'];
    if ($b['goal_diff'] !== $a['goal_diff']) return $b['goal_diff'] - $a['goal_diff'];
    return $b['goals_for'] - $a['goals_for'];
});

echo json_encode(['standings' => $standings]);
```

---

## 🔷 PHASE 8 — Real-Time Chat System (SSE / Polling)

### Goal
Replace the static chat bubble UI with live messaging using PHP + MySQL.

### Architecture

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                     CHAT ARCHITECTURE (PHP + SSE/Polling)                     │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   User A (chat.html)                        User B (player-chat.html)       │
│   ┌──────────────┐                          ┌──────────────┐                │
│   │ Send message  │──POST /api/chat/send──▶ │              │                │
│   │               │       messages table     │ Send message  │                │
│   │ Poll for new  │◀─ GET /api/chat/poll ──▶ │ Poll for new  │                │
│   │ messages      │   (every 3 seconds)      │ messages      │                │
│   │ (OR SSE)      │   OR Server-Sent Events  │ (OR SSE)      │                │
│   └──────────────┘                          └──────────────┘                │
│                                                                              │
│   3 Chat Pages (same logic, different role context):                         │
│   ├── chat.html          (Captain ↔ Owner)                                  │
│   ├── player-chat.html   (Player ↔ Captain/Owner)                           │
│   └── owner-chat.html    (Owner ↔ Captain/Player)                           │
│                                                                              │
│   Approach:                                                                  │
│   Option A (Simple): Short polling with setInterval(fetch, 3000)            │
│   Option B (Better): Server-Sent Events (SSE) for push-like behavior       │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

### 📄 `backend/api/chat/send.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain', 'owner', 'admin');

$input      = json_decode(file_get_contents('php://input'), true);
$receiverId = (int) ($input['receiver_id'] ?? 0);
$content    = trim($input['content'] ?? '');

if (!$receiverId || !$content) {
    http_response_code(400);
    echo json_encode(['error' => 'receiver_id and content are required']);
    exit;
}

$db = Database::connect();
$stmt = $db->prepare(
    'INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)'
);
$stmt->execute([$user['id'], $receiverId, $content]);

$msgId = (int) $db->lastInsertId();

echo json_encode([
    'success' => true,
    'message' => [
        'id'          => $msgId,
        'sender_id'   => $user['id'],
        'receiver_id' => $receiverId,
        'content'     => $content,
        'is_read'     => 0,
        'created_at'  => date('Y-m-d H:i:s')
    ]
]);
```

### 📄 `backend/api/chat/messages.php` — Load Conversation

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user    = guardRole('player', 'captain', 'owner', 'admin');
$otherId = (int) ($_GET['other_user_id'] ?? 0);

if (!$otherId) {
    http_response_code(400);
    echo json_encode(['error' => 'other_user_id is required']);
    exit;
}

$db = Database::connect();

// Fetch messages between the two users
$stmt = $db->prepare(
    'SELECT m.*, u.full_name AS sender_name, u.avatar_url AS sender_avatar
     FROM messages m
     JOIN users u ON u.id = m.sender_id
     WHERE (m.sender_id = ? AND m.receiver_id = ?)
        OR (m.sender_id = ? AND m.receiver_id = ?)
     ORDER BY m.created_at ASC'
);
$stmt->execute([$user['id'], $otherId, $otherId, $user['id']]);
$messages = $stmt->fetchAll();

// Mark received messages as read
$stmt = $db->prepare(
    'UPDATE messages SET is_read = 1
     WHERE sender_id = ? AND receiver_id = ? AND is_read = 0'
);
$stmt->execute([$otherId, $user['id']]);

echo json_encode(['messages' => $messages]);
```

### 📄 `backend/api/chat/conversations.php` — List Chat Threads

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('player', 'captain', 'owner', 'admin');

$db = Database::connect();

// Get distinct conversation partners with last message
$stmt = $db->prepare(
    'SELECT
        other_id,
        u.full_name AS other_name,
        u.avatar_url AS other_avatar,
        u.role AS other_role,
        sub.last_content,
        sub.last_time,
        COALESCE(unread.cnt, 0) AS unread_count
     FROM (
        SELECT
            CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END AS other_id,
            content AS last_content,
            created_at AS last_time,
            ROW_NUMBER() OVER (
                PARTITION BY CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END
                ORDER BY created_at DESC
            ) AS rn
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
     ) sub
     JOIN users u ON u.id = sub.other_id
     LEFT JOIN (
        SELECT sender_id, COUNT(*) AS cnt
        FROM messages
        WHERE receiver_id = ? AND is_read = 0
        GROUP BY sender_id
     ) unread ON unread.sender_id = sub.other_id
     WHERE sub.rn = 1
     ORDER BY sub.last_time DESC'
);
$uid = $user['id'];
$stmt->execute([$uid, $uid, $uid, $uid, $uid]);
$conversations = $stmt->fetchAll();

echo json_encode(['conversations' => $conversations]);
```

### 📄 `backend/api/chat/poll.php` — Short Polling for New Messages

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user  = guardRole('player', 'captain', 'owner', 'admin');
$since = $_GET['since'] ?? date('Y-m-d H:i:s', strtotime('-10 seconds'));

$db = Database::connect();
$stmt = $db->prepare(
    'SELECT m.*, u.full_name AS sender_name
     FROM messages m
     JOIN users u ON u.id = m.sender_id
     WHERE m.receiver_id = ? AND m.created_at > ?
     ORDER BY m.created_at ASC'
);
$stmt->execute([$user['id'], $since]);
$newMessages = $stmt->fetchAll();

echo json_encode(['messages' => $newMessages]);
```

### Frontend Chat Polling Integration

```javascript
// chat-client.js — Frontend polling for new messages
import { apiGet, apiPost } from './api-client.js';

let pollTimer = null;
let lastPollTime = new Date().toISOString();

export async function loadConversations() {
    const data = await apiGet('chat/conversations.php');
    return data.conversations;
}

export async function loadMessages(otherUserId) {
    const data = await apiGet(`chat/messages.php?other_user_id=${otherUserId}`);
    return data.messages;
}

export async function sendMessage(receiverId, content) {
    const data = await apiPost('chat/send.php', { receiver_id: receiverId, content });
    return data.message;
}

export function startPolling(onNewMessages) {
    pollTimer = setInterval(async () => {
        try {
            const data = await apiGet(`chat/poll.php?since=${encodeURIComponent(lastPollTime)}`);
            if (data.messages.length > 0) {
                lastPollTime = data.messages[data.messages.length - 1].created_at;
                onNewMessages(data.messages);
            }
        } catch (err) {
            console.warn('Poll error:', err);
        }
    }, 3000); // Poll every 3 seconds
}

export function stopPolling() {
    if (pollTimer) clearInterval(pollTimer);
}
```

---

## 🔷 PHASE 9 — Admin Panel Backend

### Goal
Power the admin dashboard, KYC approvals, analytics, categories, reports, and announcements with real data.

### Admin Functions Map

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                         ADMIN BACKEND FUNCTIONS                              │
├──────────────────────────┬───────────────────────────────────────────────────┤
│ Admin Page               │ PHP API Endpoint + DB Operations                 │
├──────────────────────────┼───────────────────────────────────────────────────┤
│ admin-dashboard.html     │ GET /api/admin/dashboard-stats.php               │
│                          │ • SELECT COUNT(*) FROM bookings (this month)     │
│                          │ • SELECT SUM(total_price) FROM bookings          │
│                          │ • SELECT COUNT(*) FROM users                     │
│                          │ • SELECT COUNT(*) FROM turf_grounds              │
│                          │ • SELECT recent bookings with JOINs              │
├──────────────────────────┼───────────────────────────────────────────────────┤
│ admin-approvals.html     │ GET  /api/admin/pending-approvals.php            │
│                          │ POST /api/admin/approve-kyc.php                  │
│                          │ POST /api/admin/reject-kyc.php                   │
│                          │ • SELECT users WHERE kyc_status='pending'        │
│                          │ • UPDATE users SET kyc_status='verified'         │
│                          │ • UPDATE turf_grounds SET is_verified=1          │
├──────────────────────────┼───────────────────────────────────────────────────┤
│ admin-analytics.html     │ GET /api/admin/analytics.php                     │
│                          │ • Hourly booking heatmap (GROUP BY HOUR)         │
│                          │ • Sport popularity (GROUP BY sport_type)         │
│                          │ • Revenue trend (GROUP BY DATE/MONTH)            │
├──────────────────────────┼───────────────────────────────────────────────────┤
│ admin-categories.html    │ GET/POST /api/admin/categories.php               │
│                          │ • SELECT * FROM sport_categories                 │
│                          │ • INSERT/UPDATE/DELETE sport_categories           │
├──────────────────────────┼───────────────────────────────────────────────────┤
│ admin-reports.html       │ GET /api/admin/financial-reports.php              │
│                          │ • SELECT bookings with turf+user JOINs           │
│                          │ • Date range filtering                           │
│                          │ • Revenue summaries                              │
├──────────────────────────┼───────────────────────────────────────────────────┤
│ admin-announcements.html │ GET/POST /api/admin/announcements.php            │
│                          │ • SELECT * FROM announcements ORDER BY date      │
│                          │ • INSERT announcements (title, priority, roles)  │
│                          │ • DELETE old announcements                       │
└──────────────────────────┴───────────────────────────────────────────────────┘
```

### 📄 `backend/api/admin/dashboard-stats.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
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
$pendingApprovals = (int) $stmt->fetch()['cnt'];

// This month's revenue
$stmt = $db->prepare(
    'SELECT COALESCE(SUM(total_price), 0) AS total FROM bookings
     WHERE status = "confirmed" AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())'
);
$stmt->execute();
$monthlyRevenue = (int) $stmt->fetch()['total'];

echo json_encode([
    'total_revenue'     => $totalRevenue,
    'monthly_revenue'   => $monthlyRevenue,
    'total_bookings'    => $totalBookings,
    'total_users'       => $totalUsers,
    'total_turfs'       => $totalTurfs,
    'pending_approvals' => $pendingApprovals
]);
```

### 📄 `backend/api/admin/approve-kyc.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$admin = guardRole('admin');

$input  = json_decode(file_get_contents('php://input'), true);
$userId = (int) ($input['user_id'] ?? 0);

if (!$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'user_id is required']);
    exit;
}

$db = Database::connect();
$db->beginTransaction();

try {
    // Verify user's KYC
    $stmt = $db->prepare('UPDATE users SET kyc_status = "verified" WHERE id = ?');
    $stmt->execute([$userId]);

    // Also verify their turfs
    $stmt = $db->prepare('UPDATE turf_grounds SET is_verified = 1, status = "active" WHERE owner_id = ?');
    $stmt->execute([$userId]);

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'KYC approved and turfs verified']);
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Approval failed: ' . $e->getMessage()]);
}
```

### 📄 `backend/api/admin/categories.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('admin');
$db = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // List all categories
    $stmt = $db->query('SELECT * FROM sport_categories ORDER BY name');
    $categories = $stmt->fetchAll();
    foreach ($categories as &$cat) {
        $cat['equipment'] = json_decode($cat['equipment'] ?? '[]', true);
    }
    echo json_encode(['categories' => $categories]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create or update category
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['id']) && $input['id']) {
        // Update
        $stmt = $db->prepare(
            'UPDATE sport_categories SET name = ?, icon = ?, field_size = ?, equipment = ?, is_active = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $input['name'],
            $input['icon'] ?? null,
            $input['field_size'] ?? null,
            json_encode($input['equipment'] ?? []),
            $input['is_active'] ?? 1,
            (int) $input['id']
        ]);
        echo json_encode(['success' => true, 'message' => 'Category updated']);
    } else {
        // Insert
        $stmt = $db->prepare(
            'INSERT INTO sport_categories (name, icon, field_size, equipment) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $input['name'],
            $input['icon'] ?? null,
            $input['field_size'] ?? null,
            json_encode($input['equipment'] ?? [])
        ]);
        echo json_encode(['success' => true, 'category_id' => (int) $db->lastInsertId()]);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $catId = (int) ($input['id'] ?? 0);
    if ($catId) {
        $stmt = $db->prepare('DELETE FROM sport_categories WHERE id = ?');
        $stmt->execute([$catId]);
    }
    echo json_encode(['success' => true]);
}
```

### 📄 `backend/api/admin/announcements.php`

```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('admin');
$db = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->prepare(
        'SELECT a.*, u.full_name AS author_name
         FROM announcements a
         LEFT JOIN users u ON u.id = a.created_by
         ORDER BY a.created_at DESC'
    );
    $stmt->execute();
    echo json_encode(['announcements' => $stmt->fetchAll()]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $stmt = $db->prepare(
        'INSERT INTO announcements (title, content, priority, target_roles, created_by)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $input['title'],
        $input['content'],
        $input['priority'] ?? 'update',
        $input['target_roles'] ?? '',
        $user['id']
    ]);

    echo json_encode(['success' => true, 'announcement_id' => (int) $db->lastInsertId()]);
}
```

---

## 🔷 PHASE 10 — Integration, Testing & Deployment

### Goal
Final integration, error handling, seeding, and deployment.

### Integration Checklist

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                     FINAL INTEGRATION CHECKLIST                              │
├──────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  Step 10.1  ──▶  Replace all localStorage references                        │
│                  ├── Search: localStorage.setItem('turfhub_                  │
│                  ├── Search: localStorage.getItem('turfhub_                  │
│                  └── Replace with fetch() calls to PHP API endpoints        │
│                                                                              │
│  Step 10.2  ──▶  Add loading states & error handling                        │
│                  ├── Show spinner while fetching data                        │
│                  ├── Show error toasts on API failures                       │
│                  └── Add empty state messages ("No bookings yet")           │
│                                                                              │
│  Step 10.3  ──▶  Seed the database with demo data                           │
│                  ├── 4 demo users (1 per role)                               │
│                  ├── 3 turf grounds with photos                              │
│                  ├── 2 teams with members                                    │
│                  ├── 1 tournament with fixtures                              │
│                  └── Sample bookings and messages                            │
│                                                                              │
│  Step 10.4  ──▶  Test every page manually                                   │
│                  ├── Sign up → Sign in → Dashboard loads                     │
│                  ├── Browse turfs → Book slot → Receipt appears              │
│                  ├── Create team → Add players → Join tournament             │
│                  ├── Enter scores → Standings update                         │
│                  ├── Send chat → Polled messages appear                      │
│                  ├── Admin approve KYC → Turf becomes verified               │
│                  └── Admin create announcement → Users see it                │
│                                                                              │
│  Step 10.5  ──▶  Deploy                                                     │
│                  ├── Option A: Shared Hosting (cPanel + MySQL)               │
│                  │   └── Upload via FTP, import SQL via phpMyAdmin           │
│                  ├── Option B: VPS (DigitalOcean / AWS EC2)                  │
│                  │   └── Install LAMP stack, clone repo, import SQL          │
│                  └── Option C: Local Demo (XAMPP)                            │
│                      └── Present from localhost                               │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

### 📄 `sql/004_seed_data.sql` — Demo Data

```sql
USE turfhub;

-- ── Demo Users (passwords: "password123" hashed with bcrypt) ──
-- Note: Generate these hashes in PHP: echo password_hash('password123', PASSWORD_BCRYPT);
-- Example hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

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
```

---

## 📁 Final File Structure

```
TurfHub/
├── index.html                          ← Public landing (no backend changes)
├── login.html                          ← Updated: fetch() to PHP auth API
├── contact.html                        ← Minimal backend (contact form)
│
├── 🆕 api-client.js                   ← Centralized fetch() wrapper
├── auth.js                             ← Updated: calls PHP session API
├── layout.js                           ← Updated: reads profile from session API
├── style.css                           ← No changes
│
├── admin-dashboard.html                ← Updated: fetch KPIs from PHP
├── admin-approvals.html                ← Updated: KYC queue from PHP
├── admin-analytics.html                ← Updated: heatmaps from PHP
├── admin-categories.html               ← Updated: categories CRUD via PHP
├── admin-reports.html                  ← Updated: financial data from PHP
├── admin-announcements.html            ← Updated: announcements CRUD via PHP
│
├── owner-dashboard.html                ← Updated: owner stats from PHP
├── manage-turf.html                    ← Updated: turf CRUD via PHP
├── slot-calendar.html                  ← Updated: slots from PHP
├── owner-tournament.html               ← Updated: tournament management
├── score-entry.html                    ← Updated: live score posting via PHP
├── owner-chat.html                     ← Updated: polling chat
├── owner-reports.html                  ← Updated: revenue from PHP
│
├── captain-dashboard.html              ← Updated: team stats from PHP
├── book-turf.html                      ← Updated: live slot booking via PHP
├── manage-team.html                    ← Updated: team CRUD via PHP
├── create-tournament.html              ← Updated: tournament creation via PHP
├── fixtures.html                       ← Updated: live fixtures & standings
├── booking-receipt.html                ← Updated: receipts from PHP
├── chat.html                           ← Updated: polling chat
├── tournament-registration.html        ← Updated: registration via PHP
│
├── player-dashboard.html               ← Updated: player stats from PHP
├── turf-detail.html                    ← Updated: live turf browsing
├── player-fixtures.html                ← Updated: player match schedule
├── player-receipts.html                ← Updated: receipts from PHP
├── player-chat.html                    ← Updated: polling chat
├── join-team.html                      ← Updated: team discovery via PHP
│
└── 📁 backend/
    └── 📁 api/
        ├── 📁 config/
        │   └── 🆕 database.php         ← MySQL PDO connection
        │
        ├── 📁 auth/
        │   ├── 🆕 register.php          ← Sign up
        │   ├── 🆕 login.php             ← Sign in
        │   ├── 🆕 logout.php            ← Sign out
        │   ├── 🆕 session.php           ← Check session
        │   └── 🆕 guard.php             ← Role gate include
        │
        ├── 📁 turfs/
        │   ├── 🆕 list.php              ← Browse verified turfs
        │   ├── 🆕 my-turfs.php          ← Owner's turfs
        │   ├── 🆕 create.php            ← Create turf
        │   ├── 🆕 update.php            ← Update turf
        │   └── 🆕 upload-photo.php      ← Upload turf image
        │
        ├── 📁 slots/
        │   ├── 🆕 list.php              ← Get slots for date range
        │   ├── 🆕 generate.php          ← Generate hourly slots
        │   └── 🆕 update-status.php     ← Reserve/block/maintenance
        │
        ├── 📁 bookings/
        │   ├── 🆕 create.php            ← Create booking (transactional)
        │   ├── 🆕 user-bookings.php     ← User's receipts
        │   └── 🆕 owner-bookings.php    ← Owner's booking list
        │
        ├── 📁 teams/
        │   ├── 🆕 create.php            ← Create team
        │   ├── 🆕 detail.php            ← Team + roster
        │   ├── 🆕 add-member.php        ← Add player
        │   ├── 🆕 update-member.php     ← Update member
        │   ├── 🆕 remove-member.php     ← Remove player
        │   ├── 🆕 recruiting.php        ← Open teams
        │   └── 🆕 join-request.php      ← Player join request
        │
        ├── 📁 tournaments/
        │   ├── 🆕 create.php            ← Create tournament
        │   ├── 🆕 list.php              ← Open tournaments
        │   ├── 🆕 register-team.php     ← Register team
        │   ├── 🆕 registrations.php     ← View registrations
        │   └── 🆕 update-registration.php ← Approve/reject
        │
        ├── 📁 fixtures/
        │   ├── 🆕 list.php              ← Match fixtures
        │   ├── 🆕 create.php            ← Generate fixtures
        │   ├── 🆕 update-score.php      ← Update score
        │   └── 🆕 standings.php         ← Computed standings
        │
        ├── 📁 chat/
        │   ├── 🆕 conversations.php     ← Chat threads
        │   ├── 🆕 messages.php          ← Conversation messages
        │   ├── 🆕 send.php              ← Send message
        │   └── 🆕 poll.php              ← Poll for new messages
        │
        ├── 📁 admin/
        │   ├── 🆕 dashboard-stats.php   ← Platform KPIs
        │   ├── 🆕 pending-approvals.php ← KYC queue
        │   ├── 🆕 approve-kyc.php       ← Approve owner
        │   ├── 🆕 reject-kyc.php        ← Reject owner
        │   ├── 🆕 categories.php        ← Sport categories CRUD
        │   ├── 🆕 announcements.php     ← System announcements
        │   ├── 🆕 analytics.php         ← Heatmaps & revenue
        │   └── 🆕 financial-reports.php ← Financial ledger
        │
        ├── 📁 uploads/
        │   ├── 📁 turf-photos/
        │   ├── 📁 kyc-documents/
        │   └── 📁 avatars/
        │
        └── 📁 sql/
            ├── 🆕 001_schema.sql         ← All CREATE TABLE statements
            ├── 🆕 002_indexes.sql         ← Performance indexes
            ├── 🆕 003_seed_data.sql       ← Demo data for testing
            └── 🆕 004_stored_procedures.sql ← Standings computation (optional)
```

---

## ⚡ Quick Reference — PHP Endpoint → Page Mapping

| PHP API Directory | Pages That Use It |
|:---|:---|
| `api/auth/*` | **ALL pages** (session check + role guard) |
| `api/turfs/*` | `manage-turf.html`, `turf-detail.html`, `book-turf.html`, `slot-calendar.html` |
| `api/slots/*` | `slot-calendar.html`, `turf-detail.html`, `book-turf.html` |
| `api/bookings/*` | `book-turf.html`, `turf-detail.html`, `booking-receipt.html`, `player-receipts.html`, `owner-dashboard.html` |
| `api/teams/*` | `manage-team.html`, `join-team.html`, `captain-dashboard.html`, `player-dashboard.html` |
| `api/tournaments/*` | `create-tournament.html`, `tournament-registration.html`, `owner-tournament.html` |
| `api/fixtures/*` | `fixtures.html`, `player-fixtures.html`, `score-entry.html` |
| `api/chat/*` | `chat.html`, `player-chat.html`, `owner-chat.html` |
| `api/admin/*` | `admin-dashboard.html`, `admin-approvals.html`, `admin-analytics.html`, `admin-categories.html`, `admin-reports.html`, `admin-announcements.html` |

---

## 🎯 Priority Order for Implementation

> [!IMPORTANT]
> Follow this exact order. Each phase depends on the previous one.

```
Priority 1 (Foundation)     ──▶  Phase 1 (Setup) → Phase 2 (Schema) → Phase 3 (Auth)
Priority 2 (Core Business)  ──▶  Phase 4 (Turfs) → Phase 5 (Bookings)
Priority 3 (Community)      ──▶  Phase 6 (Teams & Tournaments)
Priority 4 (Live Features)  ──▶  Phase 7 (Scores) → Phase 8 (Chat)
Priority 5 (Admin & Polish) ──▶  Phase 9 (Admin) → Phase 10 (Deploy)
```

> [!TIP]
> **Estimated Timeline**: With 1 developer working full-time, this can be completed in approximately **3–4 weeks**. Phase 1–3 takes ~3 days, Phase 4–5 takes ~5 days, Phase 6–7 takes ~5 days, Phase 8 takes ~3 days, Phase 9–10 takes ~4 days.

---

## 🔒 Security Best Practices

| Concern | Implementation |
|:---|:---|
| **SQL Injection** | All queries use PDO **prepared statements** with parameterized `?` placeholders. Never concatenate user input into SQL. |
| **Password Storage** | `password_hash($pw, PASSWORD_BCRYPT)` for hashing, `password_verify()` for checking. Raw passwords are never stored. |
| **Session Hijacking** | Use `session_regenerate_id(true)` after login. Set `session.cookie_httponly = 1` and `session.cookie_secure = 1` in `php.ini`. |
| **CORS** | In production, replace `Access-Control-Allow-Origin: *` with your actual domain. |
| **File Uploads** | Validate file extension and MIME type. Limit file size. Store outside web root or with randomized filenames. |
| **Role Authorization** | Every protected endpoint includes `guard.php` which checks `$_SESSION` and user role before any DB operation. |
| **Double Booking** | Booking creation uses MySQL **transactions** with `SELECT ... FOR UPDATE` row locks to prevent race conditions. |
