# 🎨 TurfHub — Figma & Google Stitch UI Reconstruction Blueprint

> **A comprehensive scene-by-scene design specification to reconstruct the entire TurfHub web application into Figma or Google Stitch with pixel-perfect visual fidelity.**

---

## 📌 Table of Contents
1. [Global Design System & Tokens (Figma Styles / Variables)](#1-global-design-system--tokens)
2. [Master UI Components Library](#2-master-ui-components-library)
3. [Core Shell Structure (Sidebar & Topbar Architecture)](#3-core-shell-structure)
4. [Scene-by-Scene Reconstruction Guide (20 Scenes)](#4-scene-by-scene-reconstruction-guide)
   - [Scene 1: Public Landing Page (`index.html`)](#scene-1-public-landing-page)
   - [Scene 2: Multi-Role Authentication Screen (`login.html`)](#scene-2-multi-role-authentication-screen)
   - [Scene 3: Turf Owner Dashboard (`owner-dashboard.html`)](#scene-3-turf-owner-dashboard)
   - [Scene 4: Turf Owner 7-Day Slot Matrix & Reserve Modal (`slot-calendar.html`)](#scene-4-turf-owner-7-day-slot-matrix--reserve-modal)
   - [Scene 5: Turf Owner Venue Management (`manage-turf.html`)](#scene-5-turf-owner-venue-management)
   - [Scene 6: Turf Owner Tournaments Hub (`owner-tournament.html`)](#scene-6-turf-owner-tournaments-hub)
   - [Scene 7: Match Score Entry & Live Scoreboard (`score-entry.html`)](#scene-7-match-score-entry--live-scoreboard)
   - [Scene 8: Turf Owner Reports & Analytics (`owner-reports.html`)](#scene-8-turf-owner-reports--analytics)
   - [Scene 9: Team Captain Dashboard (`captain-dashboard.html`)](#scene-9-team-captain-dashboard)
   - [Scene 10: Captain Turf Booking Stepper (`book-turf.html`)](#scene-10-captain-turf-booking-stepper)
   - [Scene 11: Host Tournament Wizard (`create-tournament.html`)](#scene-11-host-tournament-wizard)
   - [Scene 12: Match Fixtures & League Standings (`fixtures.html`)](#scene-12-match-fixtures--league-standings)
   - [Scene 13: Booking Receipts & Invoices (`booking-receipt.html`)](#scene-13-booking-receipts--invoices)
   - [Scene 14: Player Dashboard & Match RSVP (`player-dashboard.html`)](#scene-14-player-dashboard--match-rsvp)
   - [Scene 15: Find Turfs Master-Detail Explorer (`turf-detail.html`)](#scene-15-find-turfs-master-detail-explorer)
   - [Scene 16: Tournament Registration Directory (`tournament-registration.html`)](#scene-16-tournament-registration-directory)
   - [Scene 17: Free Agent & Team Recruitment (`join-team.html`)](#scene-17-free-agent--team-recruitment)
   - [Scene 18: Admin Dashboard Overview (`admin-dashboard.html`)](#scene-18-admin-dashboard-overview)
   - [Scene 19: Admin Approvals & KYC Verification Dialog (`admin-approvals.html`)](#scene-19-admin-approvals--kyc-verification-dialog)
   - [Scene 20: Real-Time Messaging & Chat (`chat.html` / `owner-chat.html` / `player-chat.html`)](#scene-20-real-time-messaging--chat)
5. [Interactive Prototype Flow Connections](#5-interactive-prototype-flow-connections)

---

## 1. Global Design System & Tokens

### 🎨 Color Palette (Figma Color Variables)

| Variable Name | Hex Code | Purpose / Usage |
| :--- | :--- | :--- |
| `Brand/Forest-Dark` | `#071e14` | Footers, deep background accents |
| `Brand/Forest-Main` | `#0d2818` | Primary brand color, Sidebars, Hero section background |
| `Brand/Forest-Card` | `#133923` | Dark card containers, CTA banners |
| `Brand/Lime-Bright` | `#7ed321` | Primary CTA buttons, badges, active tab indicator, logos |
| `Brand/Lime-Dark` | `#6ab81c` | Button hover states, active borders |
| `Brand/Lime-Glow` | `rgba(126,211,33,0.15)` | Selected cards, badge background tints |
| `Neutral/App-BG` | `#f5f5f0` | Global page body background |
| `Neutral/Card-White` | `#ffffff` | Stat cards, table containers, modal bodies |
| `Neutral/Border` | `#e8ede8` | Card outlines, table borders, dividers |
| `Neutral/Text-Dark` | `#1a1a1a` | Primary headings, table text, card titles |
| `Neutral/Text-Muted` | `#6b7280` | Subtitles, labels, timestamps |
| `Status/Success-Green` | `#5a9e12` | Confirmed bookings, available slots (`#7ed321`) |
| `Status/Warning-Amber` | `#d97706` | Live indicators, pending status, owner-reserved slots (`#f59e0b`) |
| `Status/Danger-Red` | `#e53935` | Rejected status, booked slots (`#fee2e2` bg, `#dc2626` text) |

---

### 🔤 Typography Hierarchy (Inter Font Family)

| Text Style Name | Font Size | Weight | Line Height | Letter Spacing |
| :--- | :--- | :--- | :--- | :--- |
| `Display/Hero-Bold` | 64px / 72px | 900 (Black) | 1.1 | -0.03em |
| `Heading/H1-PageTitle` | 24px (1.5rem) | 800 (ExtraBold) | 1.2 | -0.02em |
| `Heading/H2-Section` | 20px (1.25rem) | 800 (ExtraBold) | 1.25 | -0.01em |
| `Heading/H3-CardTitle` | 16px (1.0rem) | 700 (Bold) | 1.3 | 0em |
| `Body/Regular` | 14px (0.875rem)| 400 (Regular) | 1.5 | 0em |
| `Body/Medium` | 14px (0.875rem)| 500 (Medium) | 1.5 | 0em |
| `Body/Bold` | 14px (0.875rem)| 700 (Bold) | 1.4 | 0em |
| `Caption/Small` | 12px (0.75rem) | 600 (SemiBold) | 1.4 | +0.01em |
| `Badge/Micro` | 11px (0.6875rem)| 700 (Bold) | 1.2 | +0.05em (Uppercase) |

---

### 🎛️ Shadows & Border Radii

- **Corner Radii:**
  - Badges & Pills: `100px` (Fully rounded)
  - Buttons & Inputs: `10px` to `12px`
  - Cards & Panels: `16px`
  - Large Banners & Hero Containers: `24px` to `32px`
- **Drop Shadows:**
  - `Elevation-1 (Soft Card)`: `0px 2px 12px rgba(0, 0, 0, 0.05)`
  - `Elevation-2 (Card Hover)`: `0px 8px 24px rgba(0, 0, 0, 0.09)`
  - `Elevation-3 (Modal / Search Glass)`: `0px 16px 40px rgba(0, 0, 0, 0.28), 0px 2px 8px rgba(0, 0, 0, 0.12)`

---

## 2. Master UI Components Library

### 1. Buttons
* **`Button / Primary-Lime`**: Height `40px`, Padding `10px 20px`, Fill `#7ed321`, Text `#0d2818` (800 Bold, 14px), Radius `10px`.
* **`Button / Outline-Dark`**: Height `40px`, Padding `10px 20px`, Border `1.5px #d1d5db`, Text `#1a1a1a` (600 SemiBold, 14px), Radius `10px`.
* **`Button / Outline-White`**: Height `40px`, Padding `10px 20px`, Border `1.5px rgba(255,255,255,0.4)`, Text `#ffffff` (700 Bold, 14px), Radius `10px`.
* **`Button / Book-Now`**: Height `38px`, Padding `9px 20px`, Fill `#7ed321`, Text `#0d2818` (800 Bold, 14px), Radius `10px`.

### 2. Status Badges & Pills
* **`Badge / Confirmed`**: Fill `rgba(126,211,33,0.15)`, Text `#5a9e12` (700 Bold, 11px), Radius `100px`.
* **`Badge / Pending`**: Fill `rgba(245,166,35,0.15)`, Text `#d97706` (700 Bold, 11px), Radius `100px`.
* **`Badge / Rejected`**: Fill `rgba(229,57,53,0.12)`, Text `#e53935` (700 Bold, 11px), Radius `100px`.
* **`Badge / Live`**: Fill `#fefce8`, Border `1px #fde68a`, Dot `#f59e0b` (Pulsing 8px), Text `#92400e`.
* **`Badge / Owner-Reserved`**: Fill `#fef3c7`, Border `1.5px #f59e0b`, Text `#92400e` (700 Bold, 11px).

### 3. Stat Card Component
* **Frame:** Width `Auto` (Fill Container), Fill `#ffffff`, Border `1px #e8ede8`, Radius `16px`, Padding `20px`.
* **Layout:** Auto Layout Horizontal (Gap `14px`, Align Center).
* **Left Icon Box:** `44x44px`, Radius `12px`, Colored background tint (`#f3e8ff` / `#fef3c7` / `rgba(126,211,33,0.15)`), Emoji/Icon centered.
* **Right Content:** Auto Layout Vertical (Gap `3px`).
  - Stat Value: `24px` Bold, `#1a1a1a`.
  - Stat Label: `12px` Medium, `#6b7280`.
  - Trend Indicator: `11px` Bold, `#7ed321` (e.g. `↑ 24% this month`).

---

## 3. Core Shell Structure

Every authenticated scene in TurfHub uses the standard **Desktop Shell Architecture** (`1440x900px` standard frame):

```
┌────────────────────────────────────────────────────────────────────────┐
│ FRAME: [Desktop Dashboard Shell] (1440 x 900)                          │
├──────────────────┬─────────────────────────────────────────────────────┤
│ SIDEBAR (220px)  │ MAIN CONTENT (1220px, Fill)                         │
│ Fill: #0d2818    ├─────────────────────────────────────────────────────┤
│                  │ TOPBAR (Height: 60px, Fill: #ffffff, Border-B)      │
│ - Brand Logo     │  - Page Title & Breadcrumb                          │
│ - User Card      │  - Contextual Badges / Action Buttons               │
│ - Nav Item List  ├─────────────────────────────────────────────────────┤
│ - Sign Out       │ PAGE BODY (Padding: 24px 28px, Fill: #f5f5f0)       │
│                  │  - Stat Grid (4-Column)                             │
│                  │  - Main Content Panels & Tables                     │
└──────────────────┴─────────────────────────────────────────────────────┘
```

---

## 4. Scene-by-Scene Reconstruction Guide

---

### Scene 1: Public Landing Page
* **File:** `index.html`
* **Frame Size:** `1440 x 2900px` (Full scroll landing page)
* **Background:** `#f5f5f0`

#### Layout & Visual Elements
1. **Header / Navbar (`1440 x 64px` Fixed)**:
   - Fill: `rgba(13, 40, 24, 0.95)` with backdrop blur `16px`, border bottom `rgba(126,211,33,0.15)`.
   - Left: `TH` green square icon (`32x32px`, `#7ed321`) + "TurfHub" title (`18px` White Bold).
   - Center: Nav Links ("Home" [Active `#ffffff`], "Turfs", "Fixtures", "Contact" [`rgba(255,255,255,0.8)`]).
   - Right: "Get Started" CTA Button (Lime fill `#7ed321`, text `#0d2818`, redirects to `login.html`).
2. **Hero Section (`1440 x 780px`)**:
   - Background: Dark Forest `#0d2818` with stadium turf photo background (`turf_football.jpg`) at 25% opacity with dark green gradient overlay.
   - Badge: Pill container with green pulse dot + text `"50+ premium turfs available now"`.
   - Title: `"Book Your Perfect"` (White 64px Black) + `"Sports Turf"` (Lime `#7ed321` 64px Black).
   - Subtitle: `"Find, book, and manage sports turf slots — or run full tournaments — all in one platform."` (85% White, 18px).
   - **Floating Search Widget (`768 x 80px`)**:
     - Fill: White `rgba(255,255,255,0.98)`, Radius `18px`, Shadow `Elevation-3`.
     - 4 Parameter Columns with vertical dividers: Location (`Dhaka, Bangladesh`), Sport (`All Sports`), Date (`2025-07-26`), Time (`Any`).
     - Right Button: `"Search Courts"` (Lime `#7ed321` with search icon).
3. **Stats Strip (`1440 x 140px`)**:
   - 4-Card Auto-Layout Grid: `12k+ Active Members`, `50+ Premium Turfs`, `200+ Tournaments Hosted`, `4.8/5 Average Rating`.
4. **Featured Turfs Section (`1440 x 620px`)**:
   - Section Header: `"Featured"` (Lime Uppercase) + `"Top Turfs Near You"` (30px Black).
   - 3-Card Grid (`340px` width each, radius `16px`):
     - Card 1: *The Green Arena* (Outdoor Football 5-a-side, `৳800/hr`, Book Now button).
     - Card 2: *Court Pro Indoor* (Indoor Basketball & Badminton, `৳600/hr`, Book Now button).
     - Card 3: *Champions Field* (Cricket pitch, `৳1,200/hr`, Red Overlay badge `"FULLY BOOKED"`).
5. **Why TurfHub Section & CTA Banner**:
   - 3 Feature Cards: Instant Booking (⚡), Tournament Engine (🏆), Conflict-Free Slots (🛡️).
   - CTA Container (`1140 x 160px`, Deep Forest `#0d2818`): `"Ready to play? Join 12,000+ players"` + Lime `"Book a Turf"` button.
6. **Footer (`1440 x 320px`, `#071e14`)**:
   - 4-Column Footer: Brand info, Platform links, Owner links, Support links, Copyright.

> **Google Stitch Prompt:**
> ```
> Modern sports turf booking landing page for "TurfHub". Dark forest green (#0d2818) and electric lime (#7ed321) sports color scheme. Top sticky glassmorphic navbar with TH lime logo and "Get Started" button. Hero section with dark stadium background, bold headline "Book Your Perfect Sports Turf", floating white multi-filter search widget (Location, Sport, Date, Time, Search Courts button). 4-column metric statistics strip. 3-card featured turf venues grid with pricing and tags. Dark green CTA banner "Ready to play?" with lime buttons. Clean, high-contrast, premium sports UI design.
> ```

---

### Scene 2: Multi-Role Authentication Screen
* **File:** `login.html`
* **Frame Size:** `1440 x 900px`
* **Background:** Split layout (`50%` Left Dark Forest `#0d2818`, `50%` Right Off-White `#f8fafc`)

#### Layout & Visual Elements
1. **Left Hero Branding Panel (`720 x 900px`)**:
   - Dark grid pattern background with stadium lighting gradient.
   - Large TH brand mark, Headline: `"Bangladesh's Premier Sports Turf Network"`.
   - 3 Feature Bullet Cards with lime checkmarks (Real-Time Availability, Instant Tournament Brackets, Verified Turf Owners).
2. **Right Authentication Form Panel (`720 x 900px`)**:
   - **Role Switcher Container**: Header `"Select Your Role to Continue"` with 4 clickable role cards:
     - 🏃 **Player** (Find & book slots, join tournaments)
     - 🏆 **Team Captain** (Manage squad, enter leagues, pay deposits)
     - 🏟️ **Turf Owner** (Manage slots, confirm bookings, analytics) — *Selected State with glowing lime border `#7ed321`*
     - 🛡️ **Platform Admin** (Approvals, KYC verification, revenue)
   - **Form Tabs**: Pill switcher with `"Sign In"` (Active) and `"Register"`.
   - **Input Fields**: Email or Phone number, Password with eye icon, "Remember me" checkbox, "Forgot password?" link.
   - **Action Button**: Wide Lime Button `"Sign In to TurfHub →"` (Height `48px`, Fill `#7ed321`).

> **Google Stitch Prompt:**
> ```
> Split-screen login interface for sports platform TurfHub. Left panel dark forest green with brand value points and stadium graphic. Right panel clean white form with 4 interactive role selection cards (Player, Team Captain, Turf Owner, Platform Admin) with the Turf Owner card highlighted in bright lime border. Tab switcher for Sign In / Register, modern text fields for email and password, large lime green submit button "Sign In to TurfHub".
> ```

---

### Scene 3: Turf Owner Dashboard
* **File:** `owner-dashboard.html`
* **Frame Size:** `1440 x 900px`
* **Role:** Rafiqul Islam (`Turf Owner`)

#### Layout & Visual Elements
1. **Sidebar (`220 x 900px`, `#0d2818`)**:
   - User profile: Avatar `'R'`, Name `"Rafiqul Islam"`, Role `"Turf Owner"`.
   - Navigation: Dashboard (Active), Manage Turfs, Slot Calendar, Tournaments, Score Entry, Messages, Reports.
2. **Topbar (`1220 x 60px`)**:
   - Title: `"Owner Dashboard"`, Subtitle: `"Welcome back, Rafiqul • Ground: The Green Arena"`.
   - Right Actions: Badge `"Live Operations"` + Lime Button `"+ Add Turf Slot"`.
3. **Stat Cards Row (4 Columns)**:
   - Card 1: `28` Today's Bookings (👥 `#f3e8ff`, `↑ 14% vs yesterday`).
   - Card 2: `৳48,500` Monthly Revenue (💰 `rgba(126,211,33,0.15)`, `↑ 22% this month`).
   - Card 3: `78%` Slot Occupancy Rate (📊 `#fef3c7`, `Peak: 8pm-11pm`).
   - Card 4: `3` Active Tournaments (🏆 `#fef9c3`, `Next match in 2h`).
4. **Main Content Grid (`2-Column: 1fr / 360px`)**:
   - **Left Panel (Upcoming Bookings Table)**:
     - Headers: Time Slot, Customer / Team, Sport, Status, Action.
     - 4 Rows with status badges (`Confirmed` in green, `Pending Payment` in amber).
   - **Right Panel (Quick Ground Actions & Peak Hours)**:
     - Peak hour mini progress bars (Friday: 95%, Saturday: 90%, Sunday: 85%).
     - Quick buttons: "Block Slot for Maintenance", "Download Daily Sheet".

---

### Scene 4: Turf Owner 7-Day Slot Matrix & Reserve Modal
* **File:** `slot-calendar.html`
* **Frame Size:** `1440 x 980px`
* **Highlight:** 7-Day Slot Grid + Owner Self-Reservation Floating Action Bar + Modal Popup

#### Layout & Visual Elements
1. **Header & Date Navigator**:
   - Left: Venue Dropdown Selector (`The Green Arena - Pitch 1`).
   - Center: `← Prev Week` | `July 28 – August 03, 2025` | `Next Week →`.
   - Right: Legend Pills (🟢 Available, 🔴 Booked, ⚪ Maintenance, 🟡 Reserved by Owner).
2. **7-Day Hourly Matrix (`1160 x 580px`)**:
   - 8 Columns: Column 1 = Time (06:00 to 23:00); Columns 2–8 = Mon, Tue, Wed, Thu, Fri, Sat, Sun.
   - Slot Cells:
     - Available: White background, soft border, lime hover glow.
     - Booked: Soft red `#fee2e2` with team label ("Dhaka Dynamos").
     - Selected: Bright lime fill `#7ed321`, text `#0d2818`.
     - Reserved by Owner: Amber fill `#fef3c7`, border `#f59e0b`, text `"Private Match"`.
3. **Floating Selection Action Bar (`Bottom Centered, 600 x 56px`)**:
   - Dark forest pill container `#0d2818` with drop shadow.
   - Text: `"3 Slots Selected (Mon 08:00, Mon 09:00, Mon 10:00)"`.
   - Right Action: Lime Button `"Reserve for Own Use"` (`#7ed321`).
4. **Owner Self-Reservation Dialog Modal (`480 x 420px Centered Modal`)**:
   - Glassmorphic backdrop blur overlay.
   - Modal Header: `"Reserve Slots for Private Use"`.
   - Form Fields:
     - Selected Slots Summary box (Green border).
     - Reservation Purpose Dropdown (`Private Friendly Match`, `Turf Maintenance / Renovation`, `Academy Practice Session`, `Corporate Event`).
     - Notes / Special Instructions Textarea.
   - Modal Footer: Secondary `"Cancel"` button + Primary `"Confirm & Block Slots"` button.

> **Google Stitch Prompt:**
> ```
> Interactive weekly sports turf slot calendar interface. Top week switcher and venue selector. 7-day hourly grid matrix showing time slots from 06:00 to 23:00. Color-coded slot blocks: green available, red booked with team names, gray maintenance, and amber owner-reserved. Bottom floating action pill "3 Slots Selected" with lime "Reserve for Own Use" button. Centered modal dialog for owner self-reservation with purpose dropdown and confirm button.
> ```

---

### Scene 5: Turf Owner Venue Management
* **File:** `manage-turf.html`
* **Frame Size:** `1440 x 900px`

#### Layout & Visual Elements
1. **Topbar**: Title `"Manage Turfs"`, Right button `"+ Add New Turf Pitch"`.
2. **Turf Cards Grid (2-Column Grid)**:
   - Pitch 1: *The Green Arena - Main Football 7-a-side* (Status: `Active` toggle, Rate `৳1,000/hr`, Dimensions `40x25m`, Floodlights, Changing room).
   - Pitch 2: *The Green Arena - Futsal Pitch 2* (Status: `Active` toggle, Rate `৳800/hr`, 5-a-side, Turf type: Synthetic 50mm).
   - Pitch 3: *The Green Arena - Cricket Practice Nets* (Status: `Maintenance` toggle, Rate `৳500/hr`).
3. **Slot Pricing & Rule Engine Card**:
   - Peak hour surge pricing toggles (6 PM - 11 PM: +20%), Weekend rates configuration.

---

### Scene 6: Turf Owner Tournaments Hub
* **File:** `owner-tournament.html`
* **Frame Size:** `1440 x 960px`

#### Layout & Visual Elements
1. **KPI Header Strip**: `৳120,000` Tournament Revenue, `4` Hosted Leagues, `32` Participating Teams.
2. **2-Column Workspace**:
   - **Left Panel (Host a New Tournament Form)**:
     - Inputs: Tournament Name ("Dhaka Monsoon Cup 2025"), Sport Dropdown, Max Teams (16), Entry Fee per Team (`৳5,000`), Total Prize Pool (`৳50,000`), Pitch Assignment, Match Duration (60 mins).
     - Submit: Lime button `"Create & Publish Tournament"`.
   - **Right Panel (Active & Upcoming Tournaments List)**:
     - Tournament Card 1: *Dhaka Corporate Super League* (12/16 Teams Registered, Progress Bar 75%, Status: `Open for Registration`).
     - Tournament Card 2: *Inter-University Futsal Clash* (Status: `Ongoing - Quarter Finals`, Fixtures button).

---

### Scene 7: Match Score Entry & Live Scoreboard
* **File:** `score-entry.html`
* **Frame Size:** `1440 x 900px`

#### Layout & Visual Elements
1. **Hero Match Scoreboard Header (`1160 x 220px`, Dark Forest `#0d2818`)**:
   - Match Status Badge: `● LIVE - 68'` (Pulsing Amber).
   - Team 1 (Left): "Gulshan Strikers" (Logo + Large Digital Score Counter: `3`).
   - Center: "VS" + Tournament Title ("Dhaka Premier League • Match #14").
   - Team 2 (Right): "Banani Titans" (Logo + Large Digital Score Counter: `2`).
2. **Score Increment Controls**:
   - Goal Stepper Buttons for each team (`+ Goal`, `- Goal`, `+ Yellow Card`, `+ Red Card`).
3. **Match Event Timeline**:
   - Vertical timeline list: `14' Goal - Tanvir (Gulshan)`, `38' Goal - Rafiq (Banani)`, `52' Goal - Arif (Gulshan)`.
4. **Action Bar**: Buttons `"Pause Match"`, `"End Match & Finalize Score"`, `"Export Match Sheet"`.

---

### Scene 8: Turf Owner Reports & Analytics
* **File:** `owner-reports.html`
* **Frame Size:** `1440 x 900px`

#### Layout & Visual Elements
1. **Filter Header**: Date Range Selector (`Last 30 Days`), Export CSV / PDF button.
2. **Revenue Breakdown Card**:
   - Revenue Bar Chart by Week: Week 1 (`৳28k`), Week 2 (`৳34k`), Week 3 (`৳41k`), Week 4 (`৳48.5k`).
3. **Occupancy by Sport**:
   - Football 5-a-side: `65%`, Cricket Practice: `20%`, Basketball: `15%`.
4. **Top 5 Team Customers Table**:
   - Rank, Team Name, Captain, Total Bookings, Total Spent (`৳`), Customer Rating.

---

### Scene 9: Team Captain Dashboard
* **File:** `captain-dashboard.html`
* **Frame Size:** `1440 x 900px`
* **Role:** Tanvir Ahmed / Fatema Begum (`Team Captain`)

#### Layout & Visual Elements
1. **Captain Sidebar (`220 x 900px`)**:
   - Profile: Avatar `'T'`, `"Tanvir Ahmed"`, Role `"Team Captain"`.
   - Nav: Dashboard, Book a Turf, Tournaments, Fixtures, My Receipts, Messages.
2. **Team Profile & Next Match Card (`1160 x 180px`)**:
   - Team Badge: "Dhaka Dynamos FC", Record: `12 Wins - 2 Draws - 1 Loss`.
   - Next Match Alert Box: `"Next Match vs Banani Titans in 2 Days, 4 Hours • Pitch 1, The Green Arena"`.
3. **Squad Roster Panel**:
   - 7-Player squad list with jersey numbers in circle badges (`#1`, `#7`, `#10`), positions (GK, DEF, MID, FWD), and match availability checkmarks.
4. **Recent Booking History & Pending Payments list**.

---

### Scene 10: Captain Turf Booking Stepper
* **File:** `book-turf.html`
* **Frame Size:** `1440 x 900px`

#### Layout & Visual Elements
1. **3-Step Progress Stepper Header**:
   - `Step 1: Select Venue (Completed ✔)` ── `Step 2: Pick Time Slot (Active 🟢)` ── `Step 3: Payment (Upcoming ⚪)`.
2. **Venue Card Selection**:
   - 3 Turf Ground Cards with price tags (`৳800/hr`, `৳1,000/hr`) and distance (`1.4 km away`).
3. **Date & Slot Selector Widget**:
   - Date picker row + Clickable time slot pills (Morning, Evening, Night).
4. **Order Summary Side Panel (`340px`)**:
   - Venue: The Green Arena, Slot: 8:00 PM - 9:00 PM (1 hr), Pitch Fee: `৳800`, Service Charge: `৳0`, Total: `৳800`.
   - Button: Lime `"Proceed to Payment (bKash / Card) →"`.

---

### Scene 11: Host Tournament Wizard
* **File:** `create-tournament.html`
* **Frame Size:** `1440 x 900px`

#### Layout & Visual Elements
1. **Organizer Form Panel (Left)**:
   - Form fields: League Title, Sport Type, Format (Knockout / Group + Knockout), Team Cap (8, 16, 32), Registration Deadline, Entry Fee, Cash Prize structure.
2. **Live Bracket & Card Preview (Right)**:
   - Live visual tournament banner card preview that updates as the captain/owner types.
   - Prize Pool Distribution preview: 1st Place (60%), 2nd Place (30%), 3rd Place (10%).

---

### Scene 12: Match Fixtures & League Standings
* **File:** `fixtures.html`
* **Frame Size:** `1440 x 960px`

#### Layout & Visual Elements
1. **Live Match Banner**:
   - Amber alert container `#fefce8` with pulsing dot: `"LIVE: Gulshan Strikers 3 - 2 Banani Titans (72') • Watch Live Center"`.
2. **View Switcher Pill Bar**:
   - Button Tabs: `[ 📋 Fixtures & Schedules ]` (Active) | `[ 🏆 Standings Table ]`.
3. **Fixtures List View**:
   - Cards showing Date Header (`Friday, 01 Aug 2025`), League tag, Home Team Logo + Name vs Away Team Logo + Name, Time `08:00 PM`, Pitch 1.
4. **Standings Table View**:
   - Table Header (Deep Forest `#0d2818`): `POS`, `TEAM`, `P`, `W`, `D`, `L`, `GF`, `GA`, `GD`, `PTS`, `FORM`.
   - Position Medals: 1st Gold `#fde68a`, 2nd Silver `#e57eb`, 3rd Bronze `#fed7aa`.
   - Highlighted Row: My Team ("Dhaka Dynamos") with soft green tint `rgba(126,211,33,0.05)`.
   - Form Badges: Green `W`, Red `L`, Gray `D`.

---

### Scene 13: Booking Receipts & Invoices
* **File:** `booking-receipt.html`
* **Frame Size:** `1440 x 900px`

#### Layout & Visual Elements
1. **Receipt Filters**: All Receipts, Confirmed, Completed, Refunded.
2. **Digital Receipt Cards (Perforated Ticket Aesthetic)**:
   - Receipt Card #TH-8942:
     - Header: "TurfHub Booking Invoice" + Paid Badge (`৳1,600` via bKash).
     - Booking Details: The Green Arena (Pitch 1), 2 Hours (8:00 PM - 10:00 PM), Booked by Tanvir Ahmed.
     - QR Code Stamp & Perforated line divider.
     - Actions: `"Download PDF Receipt"` (Lime Outline) + `"Add to Google Calendar"`.

---

### Scene 14: Player Dashboard & Match RSVP
* **File:** `player-dashboard.html`
* **Frame Size:** `1440 x 900px`
* **Role:** Arif Hossain (`Player`)

#### Layout & Visual Elements
1. **Player Profile Header Card (`1160 x 160px`)**:
   - Avatar `'A'`, Name `"Arif Hossain"`, Team `"Dhaka Dynamos (Forward #10)"`.
   - 3 Quick Metric Badges: `24 Matches Played`, `18 Goals Scored`, `8.9 Avg Player Rating`.
2. **Upcoming Fixture & Interactive RSVP Section**:
   - Card: Match against *Mirpur Warriors* this Saturday at 7:00 PM.
   - RSVP Action Pill Buttons:
     - Button 1: `✔ I'm Attending` (Lime Green `#7ed321`, Active)
     - Button 2: `✖ Cannot Play` (Soft Gray Outline)
     - Button 3: `❓ Tentative`
3. **Team Squad Status**:
   - Mini avatars of 6 teammates who confirmed attendance.

---

### Scene 15: Find Turfs Master-Detail Explorer
* **File:** `turf-detail.html`
* **Frame Size:** `1440 x 960px`

#### Layout & Visual Elements
1. **Top Search & Filter Strip**:
   - Location input (`Gulshan, Dhaka`), Sport filter, Price slider (`৳500 - ৳1500`), Facility checkboxes (AC, Floodlights, Parking).
2. **Master-Detail Split Interface**:
   - **Left Column (Master List, 420px)**:
     - Scrollable list of Turf venue cards with thumbnail, title, rating (`⭐ 4.8`), distance, and price/hr.
     - Selected card has glowing green left border.
   - **Right Column (Detail Pane, 740px)**:
     - **Photo Gallery**: Large Hero image + 4 interactive thumbnail previews below.
     - **Venue Information**: Overview, Amenities pills, Address with mini map view.
     - **Live Slot Picker**: Available time pills for selected date.
     - **Checkout Widget**: Total calculation + `"Book Selected Slot Now"` button.

---

### Scene 16: Tournament Registration Directory
* **File:** `tournament-registration.html`
* **Frame Size:** `1440 x 900px`

#### Layout & Visual Elements
1. **Sport Filter Pills**: `All Sports`, `Football (7-A-Side)`, `Futsal (5-A-Side)`, `Cricket (Tape-ball)`, `Basketball`.
2. **Tournament Cards Grid (3 Columns)**:
   - Card Top: Hero banner with trophy icon & dates.
   - Card Body:
     - Prize Pool Box: `৳50,000` (Gold highlight).
     - Entry Fee: `৳4,000 / team`.
     - Slots Filled: `12/16 Teams` (Progress bar at 75%).
   - Card Bottom: Lime `"Register My Team →"` button.

---

### Scene 17: Free Agent & Team Recruitment
* **File:** `join-team.html`
* **Frame Size:** `1440 x 900px`

#### Layout & Visual Elements
1. **Header Toggle**: `[ Find a Team to Join ]` | `[ Post Free Agent Listing ]`.
2. **Team Recruitment Cards Grid**:
   - Card 1: *Uttara Strikers FC* (Looking for: `1 Goalkeeper, 1 Defender`, Skill Level: `Intermediate`, Next Game: `Sunday`).
   - Card 2: *Gulshan Kings* (Looking for: `1 Striker`, Skill Level: `Competitive`).
   - Action: `"Send Join Request"` modal button.

---

### Scene 18: Admin Dashboard Overview
* **File:** `admin-dashboard.html`
* **Frame Size:** `1440 x 900px`
* **Role:** Platform Admin (`Admin User`)

#### Layout & Visual Elements
1. **Admin Sidebar (`220 x 900px`)**:
   - Nav: Overview (Active), Approvals (with red badge `3`), Analytics, Categories, Reports, Announcements.
2. **KPI Metrics Grid**:
   - `৳3.8M` Gross Platform Volume (`↑ 18%`).
   - `54` Approved Turf Venues (`4 Pending`).
   - `1,280` Monthly Match Bookings.
   - `0.2%` Dispute / Cancellation Rate.
3. **Platform Activity & Quick Approval Queue**:
   - Table of pending turf owner KYC submissions with "Review Documents" action.

---

### Scene 19: Admin Approvals & KYC Verification Dialog
* **File:** `admin-approvals.html`
* **Frame Size:** `1440 x 1050px`
* **Highlight:** Government of Bangladesh Smart NID Card & Municipal Character Certificate Modal

#### Layout & Visual Elements
1. **Approvals Queue Table**:
   - Turf Name: *Green Valley Sports Complex*, Owner: *Kabir Chowdhury*, Submitted: *2 hours ago*, Status: `Pending Review`.
   - Action: Lime button `"Review KYC Dossier"`.
2. **KYC Verification Dialog Modal (`780 x 680px Centered Modal`)**:
   - **Document Tabs**: `[ 🪪 Smart NID (Front) ]` | `[ 🪪 Smart NID (Back) ]` | `[ 📜 Ward Councilor Certificate ]`.
   - **Document Display Window**:
     - **Smart NID Card Front (Rendered Authentic Bangladesh Card)**:
       - Green/Emerald guilloché security background.
       - Emblem of Bangladesh, Bengali Government header.
       - Gold electronic holographic chip graphic (`44x36px`).
       - Owner photo portrait box with seal watermark.
       - Details: Name in Bangla & English, Father's name, Mother's name, Date of Birth, 10-digit NID Number.
     - **Smart NID Card Back**:
       - Address in Bangla, Blood Group `O+`, Issuing Authority signature, MRZ 3-line machine-readable barcode strip.
     - **Ward Certificate**:
       - Formal municipal parchment border, Dhaka North City Corporation header, Councilor signature and red rubber stamp seal.
   - **Admin Decision Footer**:
     - Danger Red Button: `"Reject Application (Provide Reason)"`.
     - Lime Green Button: `"✔ Approve & Activate Turf Ground"`.

> **Google Stitch Prompt:**
> ```
> Platform admin KYC verification modal for sports venue owner. Realistic Government of Bangladesh Smart NID Card preview with holographic chip, Bengali typography, portrait box, and MRZ barcode. Document tab switcher for NID Front, NID Back, and Ward Councilor Certificate. Verification checklist with Approve and Reject action buttons in dark forest and lime design system.
> ```

---

### Scene 20: Real-Time Messaging & Chat
* **File:** `chat.html` / `owner-chat.html` / `player-chat.html`
* **Frame Size:** `1440 x 900px`

#### Layout & Visual Elements
1. **2-Column Split Messaging Shell (`1160 x 740px`)**:
   - **Left Conversation List (`300px`)**:
     - Search conversations bar.
     - Active Thread 1: *Tanvir Ahmed (Captain - Dhaka Dynamos)* — Green unread dot `●`, latest message snippet, timestamp `2m ago`.
     - Thread 2: *Rafiqul Islam (Turf Owner - The Green Arena)*.
     - Thread 3: *Tournament Match Organizers*.
   - **Right Chat Stream (`860px`)**:
     - Chat Header: Contact Avatar, Contact Name, Role Badge, Online indicator (`🟢 Online`).
     - Message Bubble Stream:
       - Receiver Bubble (Left, White fill `#ffffff`, gray border, dark text).
       - Sender Bubble (Right, Dark Forest fill `#0d2818`, Lime text/accent `#7ed321` or White text).
       - Timestamps and read receipts (`✔✔`).
     - Chat Input Footer: Attachment button (`📎`), Text input field (`"Type a message..."`), Emoji picker (`😊`), Send Button (Lime green circle with paper plane icon).

---

## 5. Interactive Prototype Flow Connections

To configure interactions in Figma or Google Stitch:

```
[Scene 1: Landing Page]
   │
   ├── "Get Started" / "Turfs" / "Fixtures" / "Contact" Click ──► [Scene 2: Login Screen]
   
[Scene 2: Login Screen]
   │
   ├── Select Role "Turf Owner" + Sign In ──► [Scene 3: Owner Dashboard]
   ├── Select Role "Team Captain" + Sign In ──► [Scene 9: Captain Dashboard]
   ├── Select Role "Player" + Sign In ──────► [Scene 14: Player Dashboard]
   └── Select Role "Admin" + Sign In ───────► [Scene 18: Admin Dashboard]

[Owner Flow]
   [Scene 3: Owner Dashboard]
      ├── Click "Slot Calendar" ──► [Scene 4: Slot Matrix & Reserve Modal]
      ├── Click "Manage Turfs" ───► [Scene 5: Venue Management]
      ├── Click "Tournaments" ────► [Scene 6: Owner Tournaments]
      ├── Click "Score Entry" ────► [Scene 7: Match Score Entry]
      ├── Click "Reports" ────────► [Scene 8: Owner Reports]
      └── Click "Messages" ───────► [Scene 20: Chat]

[Captain Flow]
   [Scene 9: Captain Dashboard]
      ├── Click "Book Turf" ──────► [Scene 10: Booking Stepper]
      ├── Click "Host Tourney" ───► [Scene 11: Create Tournament]
      ├── Click "Fixtures" ───────► [Scene 12: Fixtures & Standings]
      └── Click "Receipts" ───────► [Scene 13: Booking Receipts]

[Player Flow]
   [Scene 14: Player Dashboard]
      ├── Click "Find Turfs" ─────► [Scene 15: Master-Detail Explorer]
      ├── Click "Tournaments" ────► [Scene 16: Tournament Registration]
      └── Click "Join Team" ──────► [Scene 17: Free Agent Recruitment]

[Admin Flow]
   [Scene 18: Admin Dashboard]
      └── Click "Review KYC" ─────► [Scene 19: KYC Modal Dialog]
```
