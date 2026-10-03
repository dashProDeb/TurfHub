# 🏟️ TurfHub — Backend Review & Multi-Role Implementation Plan
> **A Comprehensive Engineering Review of the Owner Backend & Master Implementation Roadmap for a 4-Member Engineering Team**  
> *Project: TurfHub Sports Management Platform*  
> *Target Architecture: Vanilla JS + PHP 8.x + MySQL (PDO) on Apache/XAMPP*  
> *Document Version: 1.0.0 — Author: Antigravity Team Lead / Architecture Review*

---

## 📌 Table of Contents
1. [Executive Summary](#1-executive-summary)
2. [Deep-Dive Review of the Owner Backend Implementation](#2-deep-dive-review-of-the-owner-backend-implementation)
   - [2.1 Architectural Conventions & Patterns Established](#21-architectural-conventions--patterns-established)
   - [2.2 Detailed Owner Endpoints Breakdown](#22-detailed-owner-endpoints-breakdown)
   - [2.3 Key Strengths & Production-Grade Highlights](#23-key-strengths--production-grade-highlights)
   - [2.4 Identified Edge Cases & Recommendations](#24-identified-edge-cases--recommendations)
3. [Master Sub-Feature Specifications for Other Roles](#3-master-sub-feature-specifications-for-other-roles)
   - [3.1 Platform Administrator Role (`admin`)](#31-platform-administrator-role-admin)
   - [3.2 Team Captain Role (`captain`)](#32-team-captain-role-captain)
   - [3.3 Registered Player / Free Agent Role (`player`)](#33-registered-player--free-agent-role-player)
   - [3.4 Cross-Cutting Shared Modules (Auth, Chat & Payment Simulation)](#34-cross-cutting-shared-modules-auth-chat--payment-simulation)
4. [4-Member Team Work Allocation & Responsibility Matrix](#4-4-member-team-work-allocation--responsibility-matrix)
   - [Member 1 (User / Lead Architect & Owner Module Lead)](#member-1-user--lead-architect--owner-module-lead)
   - [Member 2 (Platform Admin & Compliance Lead)](#member-2-platform-admin--compliance-lead)
   - [Member 3 (Team Captain & Tournament Operations Lead)](#member-3-team-captain--tournament-operations-lead)
   - [Member 4 (Player Experience & Match Center Lead)](#member-4-player-experience--match-center-lead)
5. [Step-by-Step Implementation Roadmap (4 Phases / Sprints)](#5-step-by-step-implementation-roadmap-4-phases--sprints)
6. [Team Engineering Standards & API Contract Guidelines](#6-team-engineering-standards--api-contract-guidelines)

---

## 1. Executive Summary

The TurfHub project is a multi-role sports turf booking, tournament organization, and team management platform serving four primary authenticated user types:
1. **Turf Owner / Ground Manager** (`owner`)
2. **Platform Administrator** (`admin`)
3. **Team Captain** (`captain`)
4. **Registered Player / Athlete** (`player`)
5. *Public Guest* (`guest` / unauthenticated)

The **Owner Module** has been developed as the platform's reference implementation, establishing architectural paradigms for database connectivity, role-based access control (RBAC), JSON-over-HTTP response structures, transactional slot reserving, and real-time database-driven reporting.

This document reviews the Owner section backend implementation, extracts its core design patterns, blueprints the sub-features required for all remaining roles, and establishes a balanced, non-overlapping work breakdown plan for a team of **4 software engineering members** (including the User).

---

## 2. Deep-Dive Review of the Owner Backend Implementation

The Owner section powers 7 core HTML views in the frontend:
- `owner-dashboard.html` (Operations command center & real-time booking table)
- `manage-turf.html` (Turf pitch catalog, edit modal, delete actions, amenity toggles)
- `slot-calendar.html` (7-day interactive slot matrix, walk-in hold, maintenance blocking)
- `owner-tournament.html` (Tournament overview, team entry approvals, pitch allocation)
- `score-entry.html` (Digital referee score controller & live goal-scorer recording)
- `owner-reports.html` (Database aggregations, revenue buckets, utilization rates)
- `owner-chat.html` (Customer inquiry inbox with real-time polling)

### 2.1 Architectural Conventions & Patterns Established

The Owner backend is built around a lightweight, clean, modular PHP architecture located under `backend/api/`:

```
backend/api/
├── config/
│   ├── database.php      # PDO MySQL singleton connection wrapper
│   └── helpers.php       # CORS headers, jsonResponse(), jsonError(), session initialization
├── auth/
│   ├── guard.php         # guardRole(...) enforcement gate
│   └── session.php       # Session verification & user profile payload
├── turfs/                # Turf ground CRUD & analytics
├── slots/                # Hourly time-slot generation & state transitions
├── bookings/             # Booking engine & status updates
├── tournaments/          # Tournament lifecycle & registration approvals
└── fixtures/             # Match scheduling & live scoreboard scoring
```

#### Key Patterns:
1. **Role Guarding**: Every owner endpoint begins with:
   ```php
   require_once __DIR__ . '/../config/helpers.php';
   require_once __DIR__ . '/../auth/guard.php';
   $user = guardRole('owner'); // Halts execution with 401/403 if unauthorized
   ```
2. **Ownership Isolation**: All mutating operations query against both the entity ID and the owner ID:
   ```sql
   UPDATE turf_grounds SET name = ?, ... WHERE id = ? AND owner_id = ?
   ```
   This prevents horizontal privilege escalation where an owner could modify another owner's ground.
3. **JSON Normalization**: Arrays like `amenities`, `photos`, and match `scorers` are stored as MySQL `JSON` columns and cleanly decoded (`json_decode($row['amenities'] ?? '[]', true)`) before returning to the frontend.
4. **Dynamic Aggregation over Hardcoding**: In `turfs/reports.php`, SQL `BETWEEN`, `COUNT(CASE WHEN...)`, and `GROUP BY` are used to compute:
   - Total & confirmed bookings
   - Gross revenue & net payout after 5% platform fee
   - Slot utilization percentage
   - Revenue buckets (`W1` through `W4`)
   - Peak hours breakdown (`Morning`, `Afternoon`, `Evening`, `Night`)

---

### 2.2 Detailed Owner Endpoints Breakdown

| Endpoint | Method | Input Parameters | Database Tables | Description |
| :--- | :--- | :--- | :--- | :--- |
| `turfs/my-turfs.php` | `GET` | *(None - Uses session)* | `turf_grounds` | Retrieves all grounds owned by the logged-in owner with decoded amenities & photos. |
| `turfs/create.php` | `POST` | `name`, `location`, `district`, `sport_type`, `price_per_hour`, `surface_type`, `field_size`, `amenities` | `turf_grounds` | Creates a new turf ground in `pending_review` status pending admin KYC. |
| `turfs/update.php` | `POST` | `id`, `name`, `location`, `district`, `sport_type`, `price_per_hour`, `surface_type`, `field_size`, `amenities`, `status` | `turf_grounds` | Updates existing turf details. Verifies `owner_id`. |
| `turfs/delete.php` | `POST` | `id` | `turf_grounds`, `bookings` | Deletes a turf if there are no active/pending bookings; otherwise disables it. |
| `turfs/upload-photo.php` | `POST` | `multipart/form-data`: `turf_id`, `photo` | `turf_grounds` | Stores images to `uploads/turf-photos/` and updates the photos JSON array. |
| `turfs/reports.php` | `GET` | `?period=week\|month\|quarter\|year` | `bookings`, `turf_grounds`, `turf_slots`, `users` | Generates full financial and operational aggregations for charts & KPI cards. |
| `slots/list.php` | `GET` | `?turf_id=X&start_date=Y&end_date=Z` | `turf_slots`, `users` | Returns hourly slot rows between dates with booking statuses and customer names. |
| `slots/generate.php` | `POST` | `turf_id`, `start_date`, `end_date`, `price` | `turf_slots` | Automatically generates 1-hour slots from 06:00 to 00:00 for the specified date range. |
| `slots/update-status.php` | `POST` | `slot_id`, `status` (`available`, `reserved`, `maintenance`) | `turf_slots`, `turf_grounds` | Toggles maintenance or walk-in hold. Validates ground ownership. |
| `bookings/owner-bookings.php` | `GET` | *(None - Uses session)* | `bookings`, `turf_grounds`, `turf_slots`, `users` | Returns all customer bookings for the owner's pitches with trx ID, price, and status. |
| `bookings/update-status.php` | `POST` | `booking_id`, `status` (`confirmed`, `cancelled`, `completed`) | `bookings`, `turf_grounds`, `turf_slots` | Updates booking state; releases or locks slot accordingly. |
| `tournaments/my-tournaments.php` | `GET` | *(None - Uses session)* | `tournaments`, `turf_grounds` | Fetches tournaments hosted by the owner or hosted on their grounds. |
| `tournaments/update.php` | `POST` | `id`, `name`, `sport`, `entry_fee`, `prize_pool`, `max_teams`, `start_date`, `end_date`, `rules`, `status` | `tournaments` | Updates tournament details, dates, and lifecycle states. |
| `tournaments/registrations.php` | `GET` | `?tournament_id=X` | `tournament_teams`, `teams`, `users` | Retrieves teams registered for a tournament with captain details. |
| `tournaments/update-registration.php` | `POST` | `registration_id`, `status` (`approved`, `rejected`) | `tournament_teams` | Approves or rejects team entry into the tournament bracket. |
| `fixtures/update-score.php` | `POST` | `fixture_id`, `home_score`, `away_score`, `scorers`, `status` | `fixtures` | Real-time score recording with goal scorers JSON and live status toggle. |

---

### 2.3 Key Strengths & Production-Grade Highlights

1. **Transactional Integrity on Bookings**: `bookings/create.php` implements `SELECT ... FOR UPDATE` inside a PDO transaction (`$db->beginTransaction()`). This guarantees that concurrent booking attempts on the exact same slot cannot result in a double booking.
2. **Responsive Degradation & Fallback**: The frontend integration uses clean `try/catch` wrappers around `apiGet` and `apiPost`, ensuring UI displays actionable feedback when the database server is offline.
3. **Reusable Layout Pipeline**: `page-init.js` and `layout.js` automatically strip hardcoded sidebars and dynamically inject role-appropriate sidebars with the logged-in user's name, role badge, and profile avatar.

---

### 2.4 Identified Edge Cases & Recommendations

- **Session Injection on Refresh**: Frontend pages must always verify the session via `auth/session.php` before rendering role-restricted DOM elements.
- **Cascading Slot Cleanup**: When a turf is deleted or deactivated, its unbooked future slots should be cleaned up or marked unavailable.
- **Uniform Paging**: As bookings and fixtures grow, adding `LIMIT` and `OFFSET` query parameters will maintain snappy page loads.

---

## 3. Master Sub-Feature Specifications for Other Roles

Using the Owner module as the architectural baseline, the remaining roles require end-to-end backend implementations connecting database tables to their respective frontend pages.

```
                              ┌─────────────────────────────┐
                              │       TURFHUB SYSTEM        │
                              └──────────────┬──────────────┘
               ┌─────────────────────────────┼────────────────────────────┐
               ▼                             ▼                            ▼
      ┌─────────────────┐          ┌───────────────────┐        ┌───────────────────┐
      │ PLATFORM ADMIN  │          │   TEAM CAPTAIN    │        │ REGISTERED PLAYER │
      │     (Member 2)  │          │     (Member 3)    │        │     (Member 4)    │
      └─────────────────┘          └───────────────────┘        └───────────────────┘
```

---

### 3.1 Platform Administrator Role (`admin`)
*Supervises entire platform operations, verifies owner KYC documents, monitors heatmaps, audits finances, and manages categories.*

| Feature ID | Sub-Feature Name | Frontend Page | Backend Endpoint(s) | Database Tables Involved | Specifications & Requirements |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **A-01** | **Admin Command Hub** | `admin-dashboard.html` | `GET admin/dashboard-stats.php` | `bookings`, `users`, `turf_grounds`, `tournaments` | Returns aggregate GMV, total active users, verified turfs, pending approvals count, active tournaments, and recent 10 transactions. |
| **A-02** | **KYC Approvals Queue** | `admin-approvals.html` | `GET admin/pending-approvals.php` | `users`, `turf_grounds` | Retrieves list of owners with `kyc_status = 'pending'`, including submitted NID image URLs, councilor certificates, and registered pitch info. |
| **A-03** | **KYC Inspector & Action** | `admin-approvals.html` | `POST admin/approve-kyc.php`<br>`POST admin/reject-kyc.php` | `users`, `turf_grounds` | `approve-kyc`: Updates `users.kyc_status = 'verified'` and sets `turf_grounds.is_verified = 1`, `status = 'active'`.<br>`reject-kyc`: Updates `users.kyc_status = 'rejected'` with optional reason text. |
| **A-04** | **Platform Utilization Heatmap** | `admin-analytics.html` | `GET admin/analytics.php` | `turf_slots`, `bookings`, `turf_grounds` | Aggregates hourly slot bookings across days of the week (0–4 intensity score), sport popularity distribution (percentages), and monthly revenue curves. |
| **A-05** | **Sports Categories CRUD** | `admin-categories.html` | `GET/POST/DELETE admin/categories.php` | `sport_categories` | Full management of sport types: name, icon, standard field dimensions, equipment guidelines, and active status toggle. |
| **A-06** | **Financial Ledger & Payouts** | `admin-reports.html` | `GET admin/financial-reports.php`<br>`POST admin/process-payout.php` | `bookings`, `turf_grounds`, `users` | Date-filtered financial ledger: gross booking price, 5% platform commission cut, 95% owner net payout, payout settlement trigger, and CSV download. |
| **A-07** | **System Broadcast Announcements** | `admin-announcements.html` | `GET/POST/DELETE admin/announcements.php` | `announcements`, `users` | Broadcast composer: target roles (`all`, `owner`, `captain`, `player`), priority (`critical`, `update`, `maintenance`), title, and content. |

---

### 3.2 Team Captain Role (`captain`)
*Squad leader who organizes team rosters, reserves turf pitches, hosts tournaments, and manages league entries.*

| Feature ID | Sub-Feature Name | Frontend Page | Backend Endpoint(s) | Database Tables Involved | Specifications & Requirements |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **C-01** | **Captain Command Center** | `captain-dashboard.html` | `GET teams/detail.php`<br>`GET bookings/user-bookings.php`<br>`GET tournaments/list.php` | `teams`, `team_members`, `bookings`, `tournaments` | Displays next scheduled match countdown, current squad size, upcoming tournament alerts, and quick-action shortcuts. |
| **C-02** | **Squad Roster & Player Status** | `manage-team.html` | `GET teams/detail.php`<br>`POST teams/add-member.php`<br>`POST teams/update-member.php`<br>`POST teams/remove-member.php` | `teams`, `team_members`, `users` | Dynamic roster grid: update jersey number, assign field position (`FWD`, `MID`, `DEF`, `GK`), toggle status (`active`, `bench`, `injured`), approve free-agent waitlist requests. |
| **C-03** | **4-Step Turf Booking Stepper** | `book-turf.html` | `GET turfs/list.php`<br>`GET slots/list.php`<br>`POST bookings/create.php` | `turf_grounds`, `turf_slots`, `bookings` | Step 1: Sport & date filter.<br>Step 2: Turf card selection.<br>Step 3: Hourly slot chip matrix with dynamic price calculation.<br>Step 4: Transactional SSLCOMMERZ checkout. |
| **C-04** | **Tournament Hosting Wizard** | `create-tournament.html` | `POST tournaments/create.php`<br>`GET turfs/list.php` | `tournaments`, `turf_grounds` | Form to publish cup/league: title, sport, venue ground, entry fee, prize pool, team limit (8/16/32), start/end dates, rules. |
| **C-05** | **Tournament Directory & Entry** | `tournament-registration.html` | `GET tournaments/list.php`<br>`POST tournaments/register-team.php` | `tournaments`, `tournament_teams`, `teams` | Lists open tournaments; allows captain to register their squad (validates team roster requirement and capacity). |
| **C-06** | **Live Match Center & Standings** | `fixtures.html` | `GET fixtures/list.php`<br>`GET fixtures/standings.php` | `fixtures`, `teams`, `tournaments` | Segmented tabs for Match Fixtures and League Standings (auto-calculates P, W, D, L, GF, GA, GD, PTS from completed fixtures). |
| **C-07** | **Invoices & Expense Splitter** | `booking-receipt.html` | `GET bookings/user-bookings.php` | `bookings`, `turf_grounds`, `turf_slots` | Detailed receipt card with Trx ID, payment method, dynamic per-player expense split calculator, and PDF export trigger. |
| **C-08** | **Captain Tactical Chat** | `chat.html` | `GET chat/conversations.php`<br>`GET chat/messages.php`<br>`POST chat/send.php`<br>`GET chat/poll.php` | `messages`, `users` | Dual-pane split chat with turf owners and team members, unread indicators, and 3-second polling routine. |

---

### 3.3 Registered Player / Free Agent Role (`player`)
*Athletes who track personal stats, RSVP to matches, find turfs via master-detail view, and join teams.*

| Feature ID | Sub-Feature Name | Frontend Page | Backend Endpoint(s) | Database Tables Involved | Specifications & Requirements |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **P-01** | **Player Hub & Stats** | `player-dashboard.html` | `GET players/stats.php` | `users`, `bookings`, `team_members` | Player profile header, career stats (matches played, goals, win rate, player rating), and team membership pill. |
| **P-02** | **Interactive Match RSVP** | `player-dashboard.html` | `POST players/rsvp.php` | `team_members`, `fixtures`, `bookings` | Hero match countdown banner with 1-tap `Attending` / `Unavailable` toggle, syncing with captain's squad sheet. |
| **P-03** | **Master-Detail Turf Explorer** | `turf-detail.html` | `GET turfs/list.php`<br>`GET turfs/detail.php` | `turf_grounds` | Split-view desktop explorer: left search filter list, right pane with full pitch gallery, specifications, and amenity tags. |
| **P-04** | **7-Day Slot Matrix & Checkout** | `turf-detail.html` | `GET slots/list.php`<br>`POST bookings/create.php` | `turf_slots`, `bookings` | Horizontal 7-day date strip, slot availability chips, and integrated SSLCOMMERZ payment modal for individual players. |
| **P-05** | **Free Agent Board & Join Squad** | `join-team.html` | `GET teams/recruiting.php`<br>`POST teams/join-request.php` | `teams`, `team_members` | Directory of recruiting teams filtered by sport; "Request to Join" application modal sent to captain's waitlist. |
| **P-06** | **Player Fixtures & Schedule** | `player-fixtures.html` | `GET fixtures/list.php` | `fixtures`, `teams`, `tournaments` | Filtered match schedule displaying only matches involving the player's registered squad, with live score indicators. |
| **P-07** | **Player Receipts & Invoices** | `player-receipts.html` | `GET bookings/user-bookings.php` | `bookings`, `turf_grounds`, `turf_slots` | Historical booking ledger for the individual player with downloadable transaction slips. |
| **P-08** | **Player Direct Messaging** | `player-chat.html` | `GET chat/conversations.php`<br>`GET chat/messages.php`<br>`POST chat/send.php` | `messages`, `users` | Direct messaging interface to communicate with squad captain and pitch owners. |

---

### 3.4 Cross-Cutting Shared Modules (Auth, Chat & Payment Simulation)

1. **Authentication & Session Core** (`backend/api/auth/`):
   - `register.php`: Bcrypt password hashing (`PASSWORD_BCRYPT`), unique email check, default KYC status assignment (`pending` for owner, `verified` for player/captain).
   - `login.php`: Validates credentials via `password_verify()`, writes user session (`$_SESSION['user_id']`, `role`, `full_name`).
   - `session.php`: Returns active session identity or 401.
   - `logout.php`: Destroys session cookie.
2. **Chat & Polling Engine** (`backend/api/chat/`):
   - `conversations.php`: Finds distinct message threads involving `user_id` with latest message snippet and unread counter.
   - `messages.php`: Returns message history between two users; marks incoming messages as `is_read = 1`.
   - `send.php`: Writes message row (`sender_id`, `receiver_id`, `content`).
   - `poll.php`: Lightweight timestamp query checking if newer messages exist since `last_timestamp`.
3. **Payment Simulation (SSLCOMMERZ EasyCheckout)**:
   - In `book-turf.html` and `turf-detail.html`, simulates the 3-step MFS payment animation (bKash/Nagad/Rocket), generates authentic transaction IDs (`TRX-XXXXXX`) and reference codes (`REF-XXXXXX`), and records them into `bookings` via `bookings/create.php`.

---

## 4. 4-Member Team Work Allocation & Responsibility Matrix

To achieve clean parallel development without merge conflicts, work is partitioned by functional role and directory boundaries:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                       TURFHUB 4-MEMBER TEAM RESPONSIBILITY MATRIX                      │
├──────────┬─────────────────────────────┬────────────────────────────────┬──────────────┤
│ MEMBER   │ PRIMARY ROLE FOCUS          │ KEY BACKEND MODULES (PHP)      │ FRONTEND UI  │
├──────────┼─────────────────────────────┼────────────────────────────────┼──────────────┤
│ MEMBER 1 │ Lead Architect / Owner Lead │ • turfs/*                      │ • Owner HTMLs│
│ (User)   │ Database & Shared Core      │ • slots/*                      │ • book-turf  │
│          │ Transactional Bookings      │ • bookings/*                   │ • api-client │
│          │                             │ • config/* & auth/guard        │              │
├──────────┼─────────────────────────────┼────────────────────────────────┼──────────────┤
│ MEMBER 2 │ Platform Administrator      │ • admin/dashboard-stats.php    │ • admin-*    │
│          │ KYC Compliance & Audits     │ • admin/pending-approvals.php  │   (all 6     │
│          │ Platform Analytics & Feeds  │ • admin/approve-kyc.php        │    admin     │
│          │                             │ • admin/categories.php         │    pages)    │
│          │                             │ • admin/financial-reports.php  │              │
│          │                             │ • admin/announcements.php      │              │
├──────────┼─────────────────────────────┼────────────────────────────────┼──────────────┤
│ MEMBER 3 │ Team Captain Lead           │ • teams/create.php             │ • captain-*  │
│          │ Squad Roster Management     │ • teams/detail.php             │ • manage-team│
│          │ Tournament Creation & Entry │ • teams/add/update/remove      │ • create-tour│
│          │                             │ • tournaments/create.php       │ • tour-reg   │
│          │                             │ • tournaments/register-team    │ • receipt    │
├──────────┼─────────────────────────────┼────────────────────────────────┼──────────────┤
│ MEMBER 4 │ Player Experience Lead      │ • players/stats.php            │ • player-*   │
│          │ Match Center & Standings    │ • players/rsvp.php             │ • turf-detail│
│          │ Free Agent Recruitment      │ • teams/recruiting.php         │ • join-team  │
│          │ Real-Time Chat Engine       │ • fixtures/list & standings    │ • fixtures   │
│          │                             │ • chat/* (all 4 files)         │ • chat.html  │
└──────────┴─────────────────────────────┴────────────────────────────────┴──────────────┘
```

---

### Member 1 (User / Lead Architect & Owner Module Lead)
> **Goal**: Maintain system stability, oversee core architecture, maintain the reference Owner module, and ensure database transactional integrity.

- **Primary Ownership**:
  - `backend/api/config/` (`database.php`, `helpers.php`)
  - `backend/api/auth/` (`guard.php`, `session.php`, `login.php`, `register.php`)
  - `backend/api/turfs/` & `backend/api/slots/`
  - `backend/api/bookings/create.php` (Double-booking lock engine)
  - `owner-dashboard.html`, `manage-turf.html`, `slot-calendar.html`, `owner-reports.html`
- **Key Deliverables**:
  1. Maintain MySQL migrations and seed scripts in `backend/api/sql/`.
  2. Ensure all owner views accurately render live data with zero hardcoded fallbacks.
  3. Support team members with API client integration and code reviews.

---

### Member 2 (Platform Admin & Compliance Lead)
> **Goal**: Deliver the entire Platform Administrator section, turning the admin UI into a live management console.

- **Primary Ownership**:
  - `backend/api/admin/*` (All 8 admin endpoints)
  - `admin-dashboard.html`, `admin-approvals.html`, `admin-analytics.html`, `admin-categories.html`, `admin-reports.html`, `admin-announcements.html`
- **Key Deliverables**:
  1. **KYC Workflow**: Connect `admin-approvals.html` to `admin/pending-approvals.php` and wire modal buttons to `admin/approve-kyc.php` and `reject-kyc.php`.
  2. **Analytics Heatmap**: Connect `admin-analytics.html` to `admin/analytics.php` to dynamically populate the 7x24 utilization grid and sport distribution percentages.
  3. **Categories CRUD**: Wire `admin-categories.html` to allow adding, editing, and disabling sports.
  4. **Announcements Engine**: Connect `admin-announcements.html` to create live broadcast announcements targeted by role.
  5. **Financial Audits**: Connect `admin-reports.html` with start/end date range filters and CSV export.

---

### Member 3 (Team Captain & Tournament Operations Lead)
> **Goal**: Complete the Captain dashboard, team roster management, tournament hosting, and booking integration.

- **Primary Ownership**:
  - `backend/api/teams/` (`create.php`, `detail.php`, `add-member.php`, `update-member.php`, `remove-member.php`)
  - `backend/api/tournaments/` (`create.php`, `list.php`, `register-team.php`)
  - `captain-dashboard.html`, `manage-team.html`, `create-tournament.html`, `tournament-registration.html`, `booking-receipt.html`
- **Key Deliverables**:
  1. **Team Roster System**: Replace hardcoded `PLAYERS` in `manage-team.html` with live data from `teams/detail.php`. Wire the drawer form to `teams/update-member.php` and "+ Add Player" modal to `teams/add-member.php`.
  2. **Waitlist Approvals**: Connect the recruitment waitlist in `manage-team.html` to accept/reject applicant players.
  3. **Tournament Creation**: Connect `create-tournament.html` form to `tournaments/create.php` so newly hosted cups appear platform-wide.
  4. **Tournament Registration**: Connect `tournament-registration.html` to allow captains to register their squad with capacity validation.
  5. **Captain Receipts**: Ensure `booking-receipt.html` renders confirmed bookings with the dynamic expense splitter.

---

### Member 4 (Player Experience & Match Center Lead)
> **Goal**: Deliver the Player dashboard, Free Agent board, Match Center / Fixtures, and real-time Chat system.

- **Primary Ownership**:
  - `backend/api/players/` (`stats.php`, `rsvp.php`)
  - `backend/api/fixtures/` (`list.php`, `standings.php`)
  - `backend/api/chat/` (`conversations.php`, `messages.php`, `send.php`, `poll.php`)
  - `backend/api/teams/` (`recruiting.php`, `join-request.php`)
  - `player-dashboard.html`, `turf-detail.html`, `join-team.html`, `fixtures.html`, `player-fixtures.html`, `player-receipts.html`, `chat.html`, `player-chat.html`
- **Key Deliverables**:
  1. **Player Hub & Match RSVP**: Connect `player-dashboard.html` to `players/stats.php` and make the hero match RSVP banner toggle attendance via `players/rsvp.php`.
  2. **Master-Detail Turf Ground Explorer**: Connect `turf-detail.html` to dynamic turf selection from `turfs/list.php` and slot availability from `slots/list.php`.
  3. **Free Agent Directory**: Connect `join-team.html` to `teams/recruiting.php` and make "Request to Join" submit to `teams/join-request.php`.
  4. **Match Center & Standings**: Connect `fixtures.html` and `player-fixtures.html` to `fixtures/list.php` and `fixtures/standings.php` (live results and computed league tables).
  5. **Real-Time Chat Engine**: Wire the chat views (`chat.html`, `player-chat.html`) to poll messages every 3 seconds and send instant replies.

---

## 5. Step-by-Step Implementation Roadmap (4 Phases / Sprints)

```
┌────────────────────────────────────────────────────────────────────────────────┐
│                        4-PHASE IMPLEMENTATION TIMELINE                         │
├────────────────────┬────────────────────┬───────────────────┬──────────────────┤
│ PHASE 1 (Days 1–2) │ PHASE 2 (Days 3–5) │ PHASE 3 (Days 6–7)│ PHASE 4 (Day 8)  │
│ Schema & Core      │ Endpoints & Logic  │ Frontend Wiring   │ Testing & Polish │
├────────────────────┼────────────────────┼───────────────────┼──────────────────┤
│ • DB verification  │ • Admin endpoints  │ • Admin HTMLs     │ • End-to-end UAT │
│ • Auth testing     │ • Team endpoints   │ • Captain HTMLs   │ • Cross-role sync│
│ • API client sync  │ • Player endpoints │ • Player HTMLs    │ • Error handling │
│ • Branch setup     │ • Chat polling     │ • Fixtures wiring │ • Final demo prep│
└────────────────────┴────────────────────┴───────────────────┴──────────────────┘
```

### Phase 1: Environment Sync & Architecture Alignment (Days 1–2)
- **All Members**:
  1. Start local XAMPP/WAMP environment (Apache on `80`, MySQL on `3306`).
  2. Execute `backend/api/sql/001_schema.sql` followed by `backend/api/sql/002_seed_data.sql`.
  3. Verify test logins for all 4 demo users:
     - Admin: `admin@turfhub.com` / `password123`
     - Owner: `rafiqul@turfhub.com` / `password123`
     - Captain: `tanvir@turfhub.com` / `password123`
     - Player: `sabbir@turfhub.com` / `password123`
  4. Review the reference Owner endpoints to understand input/output formatting.

### Phase 2: Endpoint Construction & Business Logic (Days 3–5)
- **Member 1**: Review transactions in `bookings/create.php`, optimize `turfs/reports.php` aggregations, refine slot generation logic.
- **Member 2**: Implement and unit test `admin/pending-approvals.php`, `approve-kyc.php`, `analytics.php`, `categories.php`, and `financial-reports.php`.
- **Member 3**: Implement and unit test `teams/detail.php`, `add-member.php`, `update-member.php`, `tournaments/create.php`, and `register-team.php`.
- **Member 4**: Implement and unit test `players/stats.php`, `players/rsvp.php`, `teams/recruiting.php`, `fixtures/standings.php`, and `chat/*`.

### Phase 3: Frontend Integration & Live Data Binding (Days 6–7)
- **Member 1**: Ensure Owner pages (`owner-dashboard.html`, `manage-turf.html`, `slot-calendar.html`) have seamless edit/delete modals and live slot state transitions.
- **Member 2**: Wire `admin-approvals.html` modal to live KYC records, wire `admin-analytics.html` heatmap, and bind `admin-categories.html` CRUD.
- **Member 3**: Remove static arrays in `manage-team.html` and bind live roster cards; bind `create-tournament.html` form and `booking-receipt.html`.
- **Member 4**: Bind `player-dashboard.html` KPIs and RSVP button, bind `join-team.html` recruitment cards, and connect `fixtures.html` to live match tables.

### Phase 4: Integration Testing, Concurrency & Security Audit (Day 8)
- **All Members**: Execute the 4 key cross-role workflow loops:
  1. **Booking Loop**: Player/Captain books a slot on `turf-detail.html` or `book-turf.html` → Slot immediately shows as booked on Owner's `slot-calendar.html` → Receipt appears in `booking-receipt.html`.
  2. **Recruitment & RSVP Loop**: Player requests to join on `join-team.html` → Captain approves on `manage-team.html` → Player appears on team roster → Match RSVP syncs between player and captain.
  3. **Tournament & Live Score Loop**: Captain creates tournament → Teams register → Owner approves entry on `owner-tournament.html` → Owner records match goals on `score-entry.html` → Live score and standings instantly update on `fixtures.html`.
  4. **KYC Verification Loop**: Owner registers → Admin inspects NID on `admin-approvals.html` → Admin verifies → Verified badge activates on all public turf cards.

---

## 6. Team Engineering Standards & API Contract Guidelines

To ensure code quality and consistency across all 4 team members, adhere to these non-negotiable conventions:

### 1. Consistent Guarding
Every protected endpoint must use `guardRole(...)` at the very top:
```php
<?php
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

// Allowed roles specified as arguments:
$user = guardRole('captain', 'admin'); 
```

### 2. Standardized JSON Responses
Never use raw `echo json_encode()`. Always use the helper functions:
```php
// Success (HTTP 200 by default, or custom code):
jsonResponse(['success' => true, 'data' => $data]);

// Error (with appropriate HTTP status code):
jsonError('Invalid request parameters', 400);
jsonError('Resource not found', 404);
jsonError('Unauthorized', 403);
```

### 3. SQL Injection Prevention
**Never** concatenate raw variables into SQL queries. Always use PDO prepared statements:
```php
// ❌ WRONG:
// $db->query("SELECT * FROM teams WHERE id = " . $_GET['id']);

// ✅ CORRECT:
$stmt = $db->prepare('SELECT * FROM teams WHERE id = ?');
$stmt->execute([(int) $_GET['id']]);
$team = $stmt->fetch();
```

### 4. Mutation Method Checks
All write operations (`INSERT`, `UPDATE`, `DELETE`) must explicitly check the request method:
```php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}
$input = json_decode(file_get_contents('php://input'), true);
```

### 5. Frontend Fetch Pattern
Always use the centralized `api-client.js` functions (`apiGet`, `apiPost`, `apiDelete`):
```javascript
try {
  const res = await apiGet('teams/detail.php');
  renderRoster(res.team.members);
} catch (err) {
  console.error('Failed to load roster:', err);
  showToast(err.message || 'Network error', 'error');
}
```

---

## 7. Deliverable Verification Checklist

Before submitting the complete project, verify that:
- [ ] No hardcoded arrays (`PLAYERS = [...]`, `WAITLIST = [...]`, `FIX_DATA = [...]`) remain in any HTML page.
- [ ] All forms (Tournament Creation, Team Join, Score Entry, Slot Update, Announcements) submit to live PHP endpoints.
- [ ] Database updates in one role immediately reflect when switching to another role without requiring manual SQL edits.
- [ ] All passwords use PHP `password_hash()` and `password_verify()`.
- [ ] Responsive navigation and sidebars work properly on both mobile (< 900px) and desktop screens.
