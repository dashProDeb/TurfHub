# 🛠️ TurfHub Complete Backend Architecture & Workflow Explainer

This document provides a comprehensive, end-to-end architectural and operational guide to the **TurfHub** backend API, database schema, multi-role security system, and feature workflows.

---

## 📑 Table of Contents
1. [System Architecture Overview](#1-system-architecture-overview)
2. [Database Schema Reference](#2-database-schema-reference)
3. [Authentication & Security Layer](#3-authentication--security-layer)
4. [Role 1: Platform Administrator](#4-role-1-platform-administrator)
5. [Role 2: Turf Ground Owner](#5-role-2-turf-ground-owner)
6. [Role 3: Team Captain](#6-role-3-team-captain)
7. [Role 4: Registered Player](#7-role-4-registered-player)
8. [Chat & Official Broadcast Engine](#8-chat--official-broadcast-engine)
9. [Complete Backend Files Index (`which file does what`)](#9-complete-backend-files-index)
10. [End-to-End Verification Test Results](#10-end-to-end-verification-test-results)

---

## 1. System Architecture Overview

TurfHub is powered by a high-performance, stateless PHP REST API coupled with a relational MySQL/MariaDB database. The frontend is built on semantic HTML5, Vanilla CSS, and JavaScript with modular client adapters.

```
                          ┌─────────────────────────────┐
                          │    Browser UI Frontends     │
                          │   Admin / Owner / Captain   │
                          │          Player             │
                          └──────────────┬──────────────┘
                                         │ JSON over HTTP
                                         ▼
                          ┌─────────────────────────────┐
                          │   api-client.js / auth.js   │
                          │  (Session & Request Wrapper) │
                          └──────────────┬──────────────┘
                                         │
                   ┌─────────────────────┴─────────────────────┐
                   ▼                                           ▼
       ┌───────────────────────┐                   ┌───────────────────────┐
       │   auth/guard.php      │                   │   config/helpers.php  │
       │ Role Authorization &  │                   │ JSON Responses, CORS, │
       │ Session Verification  │                   │ Session Init, Errors  │
       └───────────┬───────────┘                   └───────────┬───────────┘
                   │                                           │
                   └─────────────────────┬─────────────────────┘
                                         ▼
                          ┌─────────────────────────────┐
                          │  config/database.php (PDO)  │
                          │    Database Singleton       │
                          └──────────────┬──────────────┘
                                         │ UTF-8mb4 / Transactions
                                         ▼
                          ┌─────────────────────────────┐
                          │    MySQL Database: turfhub  │
                          │      12 Core Tables         │
                          └─────────────────────────────┘
```

### Directory Layout
```
backend/api/
├── admin/          ← System oversight, analytics, KYC approvals, categories, announcements
├── auth/           ← Session authentication, login, register, logout, guard middleware
├── bookings/       ← Turf reservation engine, status transitions, user/owner booking feeds
├── chat/           ← Real-time messaging, short-polling, contacts, admin broadcasts
├── config/         ← PDO database singleton, global helpers, CORS & JSON formatting
├── contact/        ← Contact form submissions
├── fixtures/       ← Tournament match fixtures, scores, automated standings generator
├── players/        ← Career performance metrics, stats, match RSVP
├── slots/          ← Turf hourly time slot generator, blackout and availability manager
├── sql/            ← Migration scripts (001_schema.sql) & seed data (002_seed_data.sql)
├── teams/          ← Squad management, rosters, formations, join requests, recruiting
├── tournaments/    ← Tournament catalog, registrations, status lifecycle
├── turfs/          ← Venue profiles, pitches, owner management, analytics reports
└── uploads/        ← Static uploaded media (turf photos, NID, certificates)
```

---

## 2. Database Schema Reference

The database consists of **12 interconnected relational tables**:

| Table | Purpose | Key Columns | Relationships |
|---|---|---|---|
| `users` | User accounts & authentication | `id`, `email`, `password_hash`, `full_name`, `role`, `kyc_status` | Referenced by all role-specific tables |
| `sport_categories` | Sports catalog & equipment metadata | `id`, `name`, `icon`, `field_size`, `equipment`, `is_active` | Classifies turfs and tournaments |
| `announcements` | System announcements & broadcast notices | `id`, `title`, `content`, `priority`, `target_roles`, `created_by` | `created_by` → `users(id)` |
| `turf_grounds` | Venues and pitch listings | `id`, `owner_id`, `name`, `location`, `price_per_hour`, `status`, `rating` | `owner_id` → `users(id)` |
| `turf_slots` | Hourly reservation availability slots | `id`, `turf_id`, `slot_date`, `start_time`, `end_time`, `status`, `price` | `turf_id` → `turf_grounds(id)` |
| `bookings` | Customer reservations & payments | `id`, `user_id`, `turf_id`, `slot_id`, `total_price`, `trx_id`, `status` | `user_id` → `users(id)`, `turf_id` → `turf_grounds(id)` |
| `teams` | Registered sports teams | `id`, `captain_id`, `name`, `sport`, `formation`, `status` | `captain_id` → `users(id)` |
| `team_members` | Team squad roster | `id`, `team_id`, `player_id`, `jersey_number`, `position`, `status` | `team_id` → `teams(id)`, `player_id` → `users(id)` |
| `tournaments` | Tournaments & championships | `id`, `created_by`, `name`, `sport`, `entry_fee`, `prize_pool`, `status` | `created_by` → `users(id)`, `turf_id` → `turf_grounds(id)` |
| `tournament_teams` | Tournament registrations | `id`, `tournament_id`, `team_id`, `status`, `registered_at` | `tournament_id` → `tournaments(id)`, `team_id` → `teams(id)` |
| `fixtures` | Tournament match schedule & results | `id`, `tournament_id`, `home_team_id`, `away_team_id`, `home_score`, `away_score`, `status` | `home_team_id`, `away_team_id` → `teams(id)` |
| `messages` | Chat & broadcast messages | `id`, `sender_id`, `receiver_id`, `content`, `is_read`, `created_at` | `sender_id`, `receiver_id` → `users(id)` |

---

## 3. Authentication & Security Layer

### `backend/api/auth/guard.php`
Centralized Access Control. Restricts endpoint execution to specified roles:
```php
$user = guardRole('admin');                           // Admin only
$user = guardRole('owner');                           // Turf Owner only
$user = guardRole('player', 'captain');               // Squad roles
$user = guardRole('player', 'captain', 'owner', 'admin'); // Authenticated users
```
- Checks PHP session (`$_SESSION['user_id']`).
- If unauthenticated, immediately aborts with `HTTP 401 Unauthorized`.
- If role does not match permitted parameters, immediately aborts with `HTTP 403 Forbidden`.

### `backend/api/auth/login.php`
- Accepts `POST` with `email` and `password`.
- Verifies password using `password_verify($password, $user['password_hash'])`.
- Generates a secure session: sets `$_SESSION['user_id'] = $user['id']` and `$_SESSION['user_role'] = $user['role']`.
- Returns user object with role and redirects frontend to the corresponding dashboard.

### `backend/api/auth/register.php`
- Accepts `POST` with `email`, `password`, `full_name`, `role`, and optional `phone`.
- Validates role (`player`, `captain`, `owner`).
- Hashes password using `PASSWORD_BCRYPT`.
- Automatically logs user in and returns session credentials.

### `backend/api/auth/session.php` & `logout.php`
- `session.php`: Returns profile for active session or 401 if expired.
- `logout.php`: Clears `$_SESSION` and destroys active session cookie.

---

## 4. Role 1: Platform Administrator

The Administrator supervises platform health, verifies turf hosts, oversees platform finances, manages categories, and broadcasts announcements.

```
  [admin-dashboard.html]  ──► GET admin/dashboard-stats.php (KPI Cards & Queues)
  [admin-approvals.html]  ──► GET admin/pending-approvals.php (Owner KYC Queue)
                          ──► POST admin/approve-kyc.php (Verify Host NID/Trade License)
                          ──► POST admin/reject-kyc.php (Reject Host with Reason)
  [admin-analytics.html]  ──► GET admin/analytics.php (Heatmaps, User Growth, Utilization)
  [admin-categories.html] ──► GET/POST/DELETE admin/categories.php (Sport Categories)
  [admin-reports.html]    ──► GET admin/financial-reports.php (Platform Revenue & Commissions)
                          ──► POST admin/process-payout.php (Disburse Host Payouts)
  [admin-announcements]   ──► GET/POST/DELETE admin/announcements.php (Audience Segmentation)
  [admin-chat.html]       ──► POST chat/broadcast.php (Direct Inbox Dispatch to All Roles)
```

### Detailed Endpoint Workflows

#### 1. `backend/api/admin/dashboard-stats.php`
- **Method**: `GET`
- **Guarded Role**: `admin`
- **Workflow**:
  - Computes `total_users` grouped by role.
  - Computes total confirmed bookings and cumulative gross transaction revenue.
  - Counts pending KYC verifications awaiting manual inspection.
  - Counts active turf grounds cataloged.
- **Output**:
  ```json
  {
    "stats": {
      "total_revenue": 11700,
      "monthly_revenue": 11700,
      "total_bookings": 9,
      "active_turfs": 6,
      "total_users": 14,
      "pending_kyc": 0
    }
  }
  ```

#### 2. `backend/api/admin/pending-approvals.php`, `approve-kyc.php`, `reject-kyc.php`
- **Methods**: `GET` (queue), `POST` (decision)
- **Guarded Role**: `admin`
- **Workflow**:
  - `pending-approvals.php`: Queries `SELECT * FROM users WHERE role = 'owner' AND kyc_status = 'pending'`.
  - `approve-kyc.php`: Sets `UPDATE users SET kyc_status = 'verified' WHERE id = ?`. Automatically publishes their turfs from `pending_review` to `active`.
  - `reject-kyc.php`: Sets `UPDATE users SET kyc_status = 'rejected' WHERE id = ?` and records audit notes.

#### 3. `backend/api/admin/announcements.php`
- **Methods**: `GET`, `POST`, `DELETE`
- **Guarded Role**: `admin`
- **Target Audience Segmentation**:
  - `all`: Reaches all registered users across the entire platform.
  - `owner`: Reaches verified Turf Owners only.
  - `captain`: Reaches Team Captains only.
  - `player`: Reaches registered Players only.
- **Dual Delivery**:
  - Inserts record into `announcements` table with `target_roles` and priority (`update`, `critical`, `maintenance`).
  - **Simultaneously inserts inbox messages** into `messages` table for every user matching the target segment, ensuring instant notification on their Messages tab.

---

## 5. Role 2: Turf Ground Owner

Turf Owners manage pitches, monitor reservations, configure time slots, host tournaments, manage match fixtures, review revenues, and chat with team captains.

```
  [owner-dashboard.html]  ──► GET turfs/my-turfs.php + GET bookings/owner-bookings.php
  [my-turfs.html]         ──► GET turfs/my-turfs.php
                          ──► POST turfs/create.php (Add Pitch with Photos & Pricing)
                          ──► POST turfs/delete.php (Archive Venue)
  [turf-detail.html]      ──► GET turfs/detail.php (Inspect Pitch)
                          ──► POST turfs/update.php (Edit Price, Surface, Amenities)
                          ──► POST slots/generate.php (Auto-Generate 6 AM - 11 PM Slots)
  [owner-bookings.html]   ──► GET bookings/owner-bookings.php (Incoming Reservations)
                          ──► POST bookings/update-status.php (Confirm, Check-In, Complete)
  [owner-tournament.html] ──► GET/POST tournaments/my-tournaments.php (Host Tournaments)
                          ──► GET/POST tournaments/registrations.php (Approve Teams)
  [owner-fixtures.html]   ──► GET fixtures/list.php + POST fixtures/create.php (Manage Brackets)
                          ──► POST fixtures/update-score.php (Submit Final Match Scores)
  [owner-reports.html]    ──► GET turfs/reports.php (Revenue Breakdown & Peak Hours)
  [owner-chat.html]       ──► GET chat/conversations.php + POST chat/send.php (Direct Chat)
```

### Detailed Endpoint Workflows

#### 1. `backend/api/turfs/my-turfs.php`, `create.php`, `update.php`
- **Guarded Role**: `owner`
- **Workflow**:
  - `my-turfs.php`: Retrieves all turf pitches owned by `$_SESSION['user_id']`.
  - `create.php`: Inserts a new pitch into `turf_grounds`. Status defaults to `pending_review` until KYC verification.
  - `update.php`: Updates venue metadata: `name`, `price_per_hour`, `surface_type`, `field_size`, `amenities`.

#### 2. `backend/api/bookings/owner-bookings.php` & `update-status.php`
- **Guarded Role**: `owner`
- **Workflow**:
  - Joins `bookings` with `users` (customer info), `turf_grounds`, and `turf_slots` (time slot date and hours).
  - Owners can update booking lifecycle: `confirmed` ➔ `completed` or `cancelled`.
  - On cancellation, the slot status in `turf_slots` is automatically released back to `available`.

#### 3. `backend/api/tournaments/my-tournaments.php` & `owner-fixtures.html`
- **Guarded Role**: `owner`
- **Workflow**:
  - `my-tournaments.php`: Allows turf hosts to organize community tournaments on their pitches, set entry fees, and define prize pools.
  - `fixtures/create.php`: Hosts configure tournament match matchups (Home Team vs Away Team), match date/time, and round name (`Quarter Final`, `Semi Final`, `Final`).
  - `fixtures/update-score.php`: Records final scores. Updates match status to `completed` and auto-recalculates tournament standings.

---

## 6. Role 3: Team Captain

Team Captains manage their squad roster, register teams into tournaments, book practice slots, submit match scores, and coordinate team discussions.

```
  [captain-dashboard.html] ──► GET teams/detail.php + GET tournaments/list.php
  [team-management.html]   ──► GET teams/detail.php (Squad Roster, Stats, Formation)
                           ──► POST teams/add-member.php (Recruit Player with Jersey & Pos)
                           ──► POST teams/remove-member.php (Drop Player from Roster)
                           ──► POST teams/update-member.php (Change Squad Role or Number)
  [captain-tournaments]    ──► GET tournaments/list.php (Discover Tournaments)
                           ──► POST tournaments/register-team.php (Enroll Team)
                           ──► GET fixtures/list.php + GET fixtures/standings.php
  [score-entry.html]       ──► POST fixtures/update-score.php (Submit Score Report)
  [find-turfs.html]        ──► POST bookings/create.php (Book Pitch with Team Payment)
  [chat.html]              ──► GET chat/conversations.php + POST chat/send.php
```

### Detailed Endpoint Workflows

#### 1. `backend/api/teams/detail.php`, `add-member.php`, `remove-member.php`
- **Guarded Role**: `captain`, `player`
- **Workflow**:
  - `detail.php`: Returns team profile, captain information, and joins `team_members` with `users` for full squad roster.
  - `add-member.php`: Enforces captain permissions. Validates that the player exists and is not already enrolled. Inserts record into `team_members` with `jersey_number` and `position`.
  - `remove-member.php`: Captain removes player from squad.

#### 2. `backend/api/tournaments/register-team.php`
- **Guarded Role**: `captain`
- **Workflow**:
  - Verifies that the tournament has not exceeded `max_teams`.
  - Inserts entry into `tournament_teams` with status `pending` or `approved`.

#### 3. `backend/api/fixtures/standings.php`
- **Guarded Role**: Public / Authenticated
- **Workflow**:
  - Dynamically calculates the points table from completed fixtures:
  - `Points = (Wins * 3) + (Draws * 1)`
  - Computes `Matches Played (P)`, `Won (W)`, `Drawn (D)`, `Lost (L)`, `Goals For (GF)`, `Goals Against (GA)`, `Goal Difference (GD)`.

---

## 7. Role 4: Registered Player

Players search and discover local turfs, make instant pitch reservations, track career statistics, find teams recruiting, and review payment receipts.

```
  [player-dashboard.html]  ──► GET players/stats.php (Career Stats, Team, Match RSVP)
  [find-turfs.html]        ──► GET turfs/list.php (Search by Sport, District, Price)
  [turf-detail.html]       ──► GET turfs/detail.php (Photos, Amenities, Reviews)
                           ──► GET slots/list.php (Available Hourly Slots)
                           ──► POST bookings/create.php (SSLCOMMERZ Checkout Simulation)
  [player-receipts.html]   ──► GET bookings/user-bookings.php (Payment Receipts & TrxID)
                           ──► POST bookings/cancel.php (Refund & Cancellation)
  [player-chat.html]       ──► GET chat/conversations.php (Messages & Admin Broadcasts)
```

### Detailed Endpoint Workflows

#### 1. `backend/api/players/stats.php` & `rsvp.php`
- **Guarded Role**: `player`, `captain`
- **Workflow**:
  - `stats.php`: Joins `team_members` to retrieve team affiliation, jersey number, and position. Aggregates completed bookings and expenditures. Calculates matches played, goals, win rate, and MVP ratings.
  - `rsvp.php`: Updates upcoming match attendance status (`going`, `declined`, `tentative`).

#### 2. `backend/api/turfs/list.php` & `detail.php`
- **Public / Authenticated**
- **Workflow**:
  - `list.php`: Filters active, verified turfs by `sport_type`, `district`, min/max price range, and minimum rating.
  - `detail.php`: Retrieves venue details, owner contact information, and pitch photo galleries.

#### 3. `backend/api/bookings/create.php` & `cancel.php`
- **Guarded Role**: `player`, `captain`
- **Workflow**:
  - Checks if the requested `turf_slots` row is `status = 'available'`.
  - Begins database transaction (`$db->beginTransaction()`).
  - Marks `turf_slots` as `booked` with `booked_by = $user['id']`.
  - Inserts record into `bookings` with generated unique `trx_id` (e.g. `TRX-TH-6701ABCD`) and `ref_id`.
  - Simulates instant SSLCOMMERZ checkout integration (bKash, Nagad, Cards).
  - Commits transaction (`$db->commit()`).

---

## 8. Chat & Official Broadcast Engine

TurfHub features a role-aware communication network with strict administrative isolation.

```
                       ┌─────────────────────────┐
                       │   Platform Admin (1)    │
                       └────────────┬────────────┘
                                    │ Broadcast Only (One-Way)
                                    ▼
       ┌────────────────────────────┬────────────────────────────┐
       ▼                            ▼                            ▼
┌──────────────┐             ┌──────────────┐             ┌──────────────┐
│  Turf Owner  │◄───────────►│ Team Captain │◄───────────►│    Player    │
└──────────────┘  2-Way Chat └──────────────┘  2-Way Chat └──────────────┘
```

### Communication Rules Enforced by Backend:
1. **Administrators communicate strictly via Broadcasts**:
   - Admin sends official announcements to `All Users`, `Turf Owners`, `Team Captains`, or `Players` via `backend/api/chat/broadcast.php` or `backend/api/admin/announcements.php`.
2. **Non-Admin users cannot direct message Administrators**:
   - `contacts.php`: Automatically excludes all admin users from contact pickers for non-admin roles.
   - `send.php`: Rejects any attempt to message an admin user with `HTTP 403 Forbidden`.
   - UI: When viewing messages from Admin, the text input and send button are replaced with:
     > 📢 **Official Announcement Channel** — Direct replies to administrators are disabled.
3. **Peer-to-Peer 2-Way Messaging**:
   - Owners, Captains, and Players can seamlessly message each other for pitch booking inquiries, match coordination, and team logistics.
   - Live short-polling (`poll.php`) delivers new messages in real-time every 3 seconds.

---

## 9. Complete Backend Files Index

Below is the complete reference table mapping **every backend file** to its role, HTTP methods, database tables, and exact function:

| Directory | File Name | Methods | Guarded Roles | Tables Used | Primary Function |
|---|---|---|---|---|---|
| **`config/`** | `database.php` | - | - | - | PDO database connection singleton. Configures UTF-8mb4, transactions, error modes. |
| | `helpers.php` | - | - | - | JSON helpers (`jsonResponse`, `jsonError`), CORS headers, session bootstrap. |
| **`auth/`** | `guard.php` | - | - | `users` | Role-based authentication guard (`guardRole()`). Validates active session and permissions. |
| | `login.php` | `POST` | Public | `users` | Authenticates email & password using `password_verify`. Creates session. |
| | `register.php` | `POST` | Public | `users` | Registers new account with `password_hash(PASSWORD_BCRYPT)`. |
| | `session.php` | `GET` | Authenticated | `users` | Returns current logged-in user profile, KYC status, and role. |
| | `logout.php` | `POST`, `GET` | Authenticated | - | Destroys PHP session and clears session cookies. |
| **`admin/`** | `dashboard-stats.php` | `GET` | `admin` | `users`, `bookings`, `turf_grounds` | Computes platform KPIs: total revenue, bookings, user counts, pending KYC. |
| | `pending-approvals.php` | `GET` | `admin` | `users` | Lists turf owners awaiting KYC / NID / Trade license approval. |
| | `approve-kyc.php` | `POST` | `admin` | `users`, `turf_grounds` | Approves turf owner KYC and activates their venues in search. |
| | `reject-kyc.php` | `POST` | `admin` | `users` | Rejects turf owner KYC application with review notes. |
| | `analytics.php` | `GET` | `admin` | `bookings`, `users`, `turf_grounds` | Aggregates revenue trends, turf utilization, and user growth heatmaps. |
| | `categories.php` | `GET`, `POST`, `DELETE` | `admin` | `sport_categories` | Sport categories management (cricket, football, badminton, etc.). |
| | `financial-reports.php` | `GET` | `admin` | `bookings`, `turf_grounds` | Financial reports, gross volume, platform commission, host balances. |
| | `process-payout.php` | `POST` | `admin` | `bookings` | Marks turf host revenue payout as disbursed. |
| | `announcements.php` | `GET`, `POST`, `DELETE` | `admin` | `announcements`, `users`, `messages` | System announcements CRUD with Audience Segmentation and inbox dispatch. |
| **`turfs/`** | `list.php` | `GET` | Public | `turf_grounds` | Public venue search with sport, district, price, and rating filters. |
| | `detail.php` | `GET` | Public | `turf_grounds`, `users` | Returns full turf profile, photos, amenities, and owner details. |
| | `my-turfs.php` | `GET` | `owner` | `turf_grounds` | Lists all turf venues owned by current turf owner. |
| | `create.php` | `POST` | `owner` | `turf_grounds` | Registers a new turf venue with location, pricing, and amenities. |
| | `update.php` | `POST` | `owner` | `turf_grounds` | Updates turf details, price per hour, surface type, field size. |
| | `delete.php` | `POST`, `DELETE` | `owner` | `turf_grounds` | Deletes or archives a turf venue. |
| | `upload-photo.php` | `POST` | `owner` | `turf_grounds` | Uploads and associates venue photography. |
| | `reports.php` | `GET` | `owner` | `bookings`, `turf_grounds` | Turf owner analytics: monthly revenue, occupancy rate, peak hours. |
| **`slots/`** | `list.php` | `GET` | Public | `turf_slots` | Returns available hourly time slots for a turf on a specific date. |
| | `generate.php` | `POST` | `owner` | `turf_slots` | Auto-generates standard hourly slots for a date range (6 AM - 11 PM). |
| | `update-status.php` | `POST` | `owner` | `turf_slots` | Locks, unlocks, or schedules maintenance on specific slots. |
| **`bookings/`** | `create.php` | `POST` | `player`, `captain` | `bookings`, `turf_slots` | Reserves slot with transaction, generates TrxID, simulates SSLCOMMERZ. |
| | `user-bookings.php` | `GET` | `player`, `captain` | `bookings`, `turf_grounds`, `turf_slots` | Returns customer booking history with payment breakdown & tax receipt data. |
| | `owner-bookings.php` | `GET` | `owner` | `bookings`, `turf_grounds`, `turf_slots`, `users` | Returns all customer bookings across the owner's pitches. |
| | `update-status.php` | `POST` | `owner`, `admin` | `bookings`, `turf_slots` | Updates booking status (`confirmed`, `completed`, `cancelled`). |
| | `cancel.php` | `POST` | `player`, `captain`, `owner` | `bookings`, `turf_slots` | Cancels reservation and releases slot back to available. |
| **`teams/`** | `create.php` | `POST` | `captain` | `teams`, `team_members` | Forms a new sports squad and designates captain. |
| | `detail.php` | `GET` | Authenticated | `teams`, `team_members`, `users` | Returns team formation, captain, and full squad roster. |
| | `add-member.php` | `POST` | `captain` | `team_members` | Adds player to roster with jersey number and position. |
| | `remove-member.php` | `POST` | `captain` | `team_members` | Removes player from team roster. |
| | `update-member.php` | `POST` | `captain` | `team_members` | Updates player's jersey number, squad position, or bench status. |
| | `recruiting.php` | `GET` | Authenticated | `teams` | Lists teams actively looking for squad members. |
| | `join-request.php` | `POST` | `player` | `team_members` | Player requests to join a recruiting team. |
| **`tournaments/`** | `list.php` | `GET` | Public | `tournaments`, `turf_grounds` | Catalog of open, ongoing, and completed tournaments. |
| | `my-tournaments.php` | `GET` | `owner` | `tournaments`, `turf_grounds` | Tournaments organized/hosted by the turf owner. |
| | `create.php` | `POST` | `owner`, `admin` | `tournaments` | Creates a tournament with entry fee, prize pool, and team limits. |
| | `update.php` | `POST` | `owner`, `admin` | `tournaments` | Edits tournament rules, dates, or status (`open`, `in_progress`, `completed`). |
| | `register-team.php` | `POST` | `captain` | `tournament_teams` | Registers captain's squad into tournament. |
| | `registrations.php` | `GET` | `owner`, `admin` | `tournament_teams`, `teams` | Lists registered teams for tournament approval. |
| | `update-registration.php` | `POST` | `owner`, `admin` | `tournament_teams` | Approves or rejects team tournament participation. |
| **`fixtures/`** | `list.php` | `GET` | Public | `fixtures`, `teams`, `turf_grounds` | Lists tournament match schedule, rounds, and scores. |
| | `create.php` | `POST` | `owner`, `admin` | `fixtures` | Creates fixture match (Home Team vs Away Team) with date/time. |
| | `delete.php` | `POST`, `DELETE` | `owner`, `admin` | `fixtures` | Deletes a scheduled match fixture. |
| | `update-score.php` | `POST` | `owner`, `captain`, `admin` | `fixtures` | Records final match scores, sets status to `completed`. |
| | `standings.php` | `GET` | Public | `fixtures`, `teams` | Dynamically generates tournament points table (P, W, D, L, GF, GA, GD, Pts). |
| **`players/`** | `stats.php` | `GET` | `player`, `captain` | `team_members`, `bookings`, `turf_grounds` | Player career KPIs: goals, matches, win rate, bookings, MVP rating. |
| | `rsvp.php` | `POST` | `player`, `captain` | `bookings` | RSVP confirmation for upcoming matches (`going`, `declined`). |
| **`chat/`** | `conversations.php` | `GET` | Authenticated | `messages`, `users` | Returns conversation threads with last message snippet and unread badges. |
| | `messages.php` | `GET` | Authenticated | `messages`, `users` | Message history between two users; marks received messages as read. |
| | `send.php` | `POST` | Authenticated | `messages`, `users` | Sends direct message. Enforces anti-self and anti-admin reply protections. |
| | `poll.php` | `GET` | Authenticated | `messages`, `users` | Real-time short-polling for incoming messages. |
| | `contacts.php` | `GET` | Authenticated | `users`, `bookings` | Returns eligible messaging contacts (excludes admin for non-admin roles). |
| | `broadcast.php` | `GET`, `POST` | `admin` | `announcements`, `users`, `messages` | Admin broadcast center. Dispatches message to all users or target segments. |
| **`contact/`** | `submit.php` | `POST` | Public | - | Processes public landing page inquiry form submissions. |

---

## 10. End-to-End Verification Test Results

A full automated verification test suite was executed across all 4 roles against the local database:

```
====================================================================
 TURFHUB MULTI-ROLE BACKEND COMPREHENSIVE TEST SUITE
====================================================================

--- TESTING ROLE: ADMIN (Super Admin, ID: 1) ---
  [PASS] Admin Dashboard Stats Retrieval (Users: 14, Turfs: 6, Revenue: ৳11700)
  [PASS] Admin Pending KYC Queue Retrieval (Pending KYC items: 0)
  [PASS] Admin KYC Verification Update (User 5 verified)
  [PASS] Admin Sport Categories Retrieval (Active categories: 6)
  [PASS] Admin Announcement Creation (Segment: Captain) (Announcement ID: 11)
  [PASS] Admin Announcement Deletion
  [PASS] Admin Inbox Broadcast Dispatch (Dispatched to 3 captains)

--- TESTING ROLE: OWNER (Rafiqul Islam, ID: 2) ---
  [PASS] Owner Venues Listing (Found 3 turfs)
  [PASS] Owner Venue Price Update (Price updated from 800 to 900)
  [PASS] Owner Bookings Feed Retrieval (Found 9 bookings)
  [PASS] Owner Time Slots Availability Check (Available slots: 48)
  [PASS] Owner Hosted Tournaments Retrieval (Tournaments: 1)
  [PASS] Owner Fixtures Management Feed (Fixtures: 3)
  [PASS] Owner Messaging to Admin Blocked (Broadcast Read-Only) (Enforced at API layer)

--- TESTING ROLE: CAPTAIN (Tanvir Ahmed, ID: 3) ---
  [PASS] Captain Team Retrieval (Team: Dhaka Warriors FC (4-3-3))
  [PASS] Captain Squad Roster Retrieval (Roster size: 6)
  [PASS] Captain Tactical Formation Update (Formation: 4-2-3-1)
  [PASS] Captain Tournament Registrations (Registered tournaments: 2)
  [PASS] Captain Team Match Schedule & Fixtures (Scheduled matches: 3)
  [PASS] Captain Messages Thread Retrieval (Total messages: 12)

--- TESTING ROLE: PLAYER (Sabbir Rahman, ID: 4) ---
  [PASS] Player Profile & Team Membership Retrieval (Team: Dhaka Warriors FC, Jersey: #7, Pos: Midfielder)
  [PASS] Player Performance & Bookings Metrics (Bookings: 5, Spent: ৳4400)
  [PASS] Player Turf Catalog Discovery (Discovered active turfs: 5)
  [PASS] Player Booking History & Receipts (Bookings count: 6)
  [PASS] Player Team Recruiting Discovery (Recruiting teams found: 5)
  [PASS] Player Receives Admin Broadcast in Inbox (Broadcasts in inbox: 1)

====================================================================
 TEST EXECUTION SUMMARY
====================================================================
ADMIN ROLE:   7 Passed, 0 Failed (100%)
OWNER ROLE:   7 Passed, 0 Failed (100%)
CAPTAIN ROLE: 6 Passed, 0 Failed (100%)
PLAYER ROLE:  6 Passed, 0 Failed (100%)
TOTAL:        26 Passed, 0 Failed (100% Success)
====================================================================
```

---
*Generated for TurfHub Platform Core Team. All rights reserved.*
