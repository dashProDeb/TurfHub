# 📱 TurfHub — Mobile & Web Role-to-Feature Specification & Design Blueprint

> **A comprehensive, role-by-role feature architecture and UI/UX blueprint (`Role <-> Feature` & `Role <-> Role`) specifically engineered on the basis of the whole UI of `http://localhost/TurfHub/` (Turf Booking, Tournament Management, Team Operations, Live Match Center, and Ground Administration), strictly scoped without Coach and Marketplace modules.**

---

## 📌 Table of Contents
1. [UI/UX Foundations & Design System Standards](#1-uiux-foundations--design-system-standards)
2. [Master Role-to-Feature Architecture (`Role <-> Feature`)](#2-master-role-to-feature-architecture-role---feature)
   - [Role 0: Public Guest / Unauthenticated Visitor](#role-0-public-guest--unauthenticated-visitor)
   - [Role 1: Registered Player (Athlete / Free Agent)](#role-1-registered-player-athlete--free-agent)
   - [Role 2: Team Captain (Squad Leader & Organizer)](#role-2-team-captain-squad-leader--organizer)
   - [Role 3: Turf Owner / Ground Manager](#role-3-turf-owner--ground-manager)
   - [Role 4: Platform Administrator](#role-4-platform-administrator)
3. [Inter-Role Collaboration Matrix (`Role <-> Role`)](#3-inter-role-collaboration-matrix-role---role)
4. [Navigation Architecture & Layout Engine (Sidebars & Mobile Views)](#4-navigation-architecture--layout-engine-sidebars--mobile-views)
5. [Payment Gateway Architecture (SSLCOMMERZ EasyCheckout Flow)](#5-payment-gateway-architecture-sslcommerz-easycheckout-flow)
6. [Master UI Screen & Component Inventory (Frame Catalog)](#6-master-ui-screen--component-inventory-frame-catalog)
7. [Interactive Prototype Flows & User Gesture Map](#7-interactive-prototype-flows--user-gesture-map)
8. [Complete Codebase UI Page Mapping & Specification Matrix](#8-complete-codebase-ui-page-mapping--specification-matrix)

---

## 1. UI/UX Foundations & Design System Standards

### 📐 Screen & Viewport Standards
- **Desktop Primary Canvas**: `1240px` max-width centered container (`margin: 0 auto`), `220px` sticky fixed left sidebar, fluid main viewport.
- **Mobile Responsive Frame**: `393 x 852 px` baseline (iPhone 15/16 & Pixel standard) with off-canvas hamburger drawer overlay.
- **Safe Area Insets**: Top status bar `54px`, bottom home indicator `34px`, content viewport `393 x 764 px`.
- **Spacing Scale (8pt System)**: `8px`, `12px`, `16px`, `20px`, `24px`, `32px`.
- **Interactive Touch Targets**: Minimum `44 x 44 px` for buttons, slot chips, and navigation triggers.
- **Corner Radii Tokens**: `6px`–`8px` (Pills, Badges), `12px` (Inputs, Buttons), `14px`–`16px` (Cards), `20px`–`24px` (Dialogs, Modals).

### 🎨 Design System Variables & Color Tokens
- **Primary Brand (Forest)**:
  - `Brand/Forest-Main`: `#0d2818` (Primary dark header, sidebars, hero containers, brand text)
  - `Brand/Forest-Dark`: `#071e14` (Deep app background, dark footer surfaces, modal backdrops)
  - `Brand/Forest-Card`: `#133923` (Elevated dark containers, header highlight cards)
- **Accent Brand (Electric Lime)**:
  - `Brand/Lime-Bright`: `#7ed321` (Primary CTAs, active states, available slots, success badges)
  - `Brand/Lime-Dark`: `#6ab81c` (Hover and pressed states for primary buttons)
  - `Brand/Lime-Glow`: `rgba(126, 211, 33, 0.18)` (Active pill glow, card outlines, focus rings)
- **Neutrals & Surfaces**:
  - `Surface/App-BG`: `#f5f5f0` (Main light body canvas)
  - `Surface/Card-White`: `#ffffff` (Elevated white cards, dialog boxes, receipt bills)
  - `Surface/Border`: `#e8ede8` (Hairline dividers and subtle container outlines)
  - `Text/Primary`: `#1a1a1a` (High-contrast titles, numbers, player names)
  - `Text/Muted`: `#6b7280` (Subtitles, metadata timestamps, descriptions)
- **State & Status Badges**:
  - `State/Available`: Background `#eefbee`, Text `#2e7d32`, Border `#bbf7d0` (Slots ready to book)
  - `State/Booked`: Background `#fee2e2`, Text `#b91c1c`, Border `#fca5a5` (Occupied slots)
  - `State/Reserved`: Background `#fef3c7`, Text `#b45309`, Border `#fde68a` (Walk-in / Owner hold)
  - `State/Live`: Background `#e53935`, Text `#ffffff` with pulsing dot animation (`@keyframes pulse-dot`)
  - `State/Maintenance`: Background `#f1f5f9`, Text `#64748b` with subtle diagonal striping

---

## 2. Master Role-to-Feature Architecture (`Role <-> Feature`)

```
                                          ┌────────────────────────────────┐
                                          │        TURFHUB PLATFORM        │
                                          └───────────────┬────────────────┘
         ┌──────────────────┬─────────────────────┼────────────────────┬─────────────────┐
         ▼                  ▼                     ▼                    ▼                 ▼
┌─────────────────┐ ┌───────────────┐ ┌───────────────────────┐ ┌──────────────┐ ┌────────────────┐
│  PUBLIC GUEST   │ │ PLAYER (USER) │ │  TEAM CAPTAIN (LEADER)│ │  TURF OWNER  │ │ PLATFORM ADMIN │
└─────────────────┘ └───────────────┘ └───────────────────────┘ └──────────────┘ └────────────────┘
```

---

### Role 0: Public Guest / Unauthenticated Visitor
> **User Goal**: Discover verified sports turfs, search slot availability, view live tournament fixtures, read FAQs, and sign in or register with a dedicated role.

| # | Feature Name | Feature Description | Core UI Page / Component | Interaction & System Behavior |
| :--- | :--- | :--- | :--- | :--- |
| **G-01** | **Hero Stadium & Value Proposition** | Dark forest stadium header with live availability counter (`50+ Verified Turfs`, `12k+ Athletes`, `200+ Tournaments`), search trigger, and instant CTA buttons. | `index.html` (Hero Section) | Smooth scrolling; "Book a Turf" jumps to search; "Get Started" opens authentication switcher. |
| **G-02** | **Multi-Parameter Quick-Search Bar** | Floating search pill with live filters for Location (Dhaka, Sylhet, Chittagong), Sport (Football, Cricket, Basketball), Date picker, and Time of day. | `index.html` (Quick Search Widget) | Interactive filter inputs; Submitting redirects to `turf-detail.html` with pre-filled query parameters. |
| **G-03** | **Featured Turfs Showcase** | Responsive card grid showcasing premier turf grounds with sport badges, location tags, hourly price (`৳800/hr`), and instant booking buttons. | `index.html` (Featured Turfs Grid) | Card hover zoom effect; Clicking card navigates directly to `turf-detail.html`. |
| **G-04** | **Platform Metric Highlights** | High-impact stat counters highlighting platform reach: 12k+ active players, 50+ partner grounds, 200+ hosted tournaments, and 4.8/5 satisfaction rating. | `index.html` (Stats Strip) | Animated counter presentation and responsive 4-column layout. |
| **G-05** | **Multi-Role Authentication Switcher** | Role selector cards (Turf Owner, Team Captain, Player, Platform Admin) with instant visual glow highlight, and Tab toggle between `Sign In` and `Sign Up`. | `login.html` (Auth Card & Tabs) | Clicking role card updates visual highlight (`.role-card.selected`), updates form context, and signs in with role profile. |
| **G-06** | **Guest Fixtures & Standings Center** | Public match schedule viewer displaying ongoing tournament brackets, live score banners, and group points tables. | `fixtures.html` (Public View) | Segmented view switcher between "Fixtures" and "Standings" with W/D/L form badges. |
| **G-07** | **Help, Support Hotline & FAQ Accordion** | Customer support inquiry form, direct phone hotline (`+880 1700-000000`), WhatsApp direct chat trigger, office address map, and accordion FAQs. | `contact.html` (Support Center) | Expandable FAQ cards; Form submission validation with success confirmation message. |

---

### Role 1: Registered Player (Athlete / Free Agent)
> **User Goal**: Track personal performance statistics, RSVP to upcoming captain matches, discover and book turf slots via master-detail explorer, join squads as a free agent, inspect receipts, and chat with captains and turf owners.

| # | Feature Name | Feature Description | Core UI Page / Component | Interaction & System Behavior |
| :--- | :--- | :--- | :--- | :--- |
| **P-01** | **Player Hub & Performance KPIs** | Personalized greeting with avatar initial, performance KPI cards: Matches Played (`18`), Goals Scored (`12`), Win Rate (`72%`), and Player Rating (`4.8 ★`). | `player-dashboard.html` (Top Stat Grid) | Instant dashboard load; Dynamic user data populated from authentication session. |
| **P-02** | **Interactive Match RSVP Banner** | Prominent hero match card with countdown clock, opponent team name, venue pitch, kick-off time, and 1-tap RSVP action buttons (`Attending` ✅ / `Unavailable` ❌). | `player-dashboard.html` (Match RSVP Hero) | Clicking "Attending" changes button to solid lime, updates status badge to green, and syncs response with Captain's squad roster. |
| **P-03** | **Master-Detail Turf Ground Explorer** | Split-view master-detail ground finder. Left sidebar lists all turfs with search/district filters; right pane displays rich pitch details, pricing, and amenities. | `turf-detail.html` (Master-Detail Shell) | Clicking turf card on left switches right pane instantly with active green highlight. |
| **P-04** | **Venue Photo Gallery & Amenity Tags** | Multi-photo hero viewer with clickable thumbnail carousel, verified turf badge, address pin, and facility amenity chips (Lighting, AC Lounge, Parking, Showers). | `turf-detail.html` (Gallery & Specs) | Clicking thumbnail updates main hero image; Amenity chips highlight included venue facilities. |
| **P-05** | **Interactive 7-Day Date & Slot Picker** | 7-day horizontal calendar date strip combined with interactive 2-column slot pills (🟢 Available, 🔴 Booked, 🟩 Selected) with dynamic total price calculation. | `turf-detail.html` (Slot Matrix Section) | Clicking date strip refreshes slot availability; Clicking available slots toggles selection and updates checkout total. |
| **P-06** | **SSLCOMMERZ EasyCheckout Gateway** | Integrated checkout modal supporting Mobile Banking (bKash, Nagad, Rocket, Upay), Credit/Debit Cards, and Net Banking with 1-click demo autofill and payment simulator. | `turf-detail.html` (SSLCOMMERZ Modal) | Clicking "Pay Now" runs 3-phase payment animation (Connecting → Authenticating OTP → Confirmed) and redirects to receipt. |
| **P-07** | **Free Agent Board & Join Squad** | Athlete team recruitment directory: explore teams with open roster spots, filter by sport, view squad size tags (`Open` / `Full`), and submit "Request to Join". | `join-team.html` (Recruitment Cards) | Clicking "Request to Join" opens application modal; Submitting sends request to Captain's inbox. |
| **P-08** | **Personal Fixtures & Tournament Tracker** | Dedicated schedule of upcoming matches, match attendance indicators, pitch locations, opponent details, and tournament group standings table. | `player-fixtures.html` (Fixtures View) | Filter by tournament/league; Displays live match score alerts with pulsing red dot indicator. |
| **P-09** | **Digital Receipts & Payment History** | Complete payment ledger showing all paid bookings with unique TrxID, Reference ID, amount, payment method, date/time, and PDF invoice download trigger. | `player-receipts.html` (Receipts Grid) | Prepend recently completed SSLCOMMERZ transactions from `localStorage`; Filter by status (`All`, `Confirmed`, `Pending`). |
| **P-10** | **Player Direct & Team Messaging** | Real-time chat interface to communicate with Team Captains and Turf Ground Owners, featuring unread message indicators and conversation threads. | `player-chat.html` (Chat Shell) | Select conversation on left list; Send instant text messages; Green bubbles for sent, white for received. |

---

### Role 2: Team Captain (Squad Leader & Organizer)
> **User Goal**: Manage team squad rosters, track player match RSVPs, execute 4-step turf bookings, host custom tournaments, register for leagues, split expenses, and coordinate team tactical chat.

| # | Feature Name | Feature Description | Core UI Page / Component | Interaction & System Behavior |
| :--- | :--- | :--- | :--- | :--- |
| **C-01** | **Captain Command Center** | Team identity header, Next Match countdown hero card (VS opponent, pitch name, kick-off), Squad Roster overview with jersey badges, and quick action buttons. | `captain-dashboard.html` (Main Hub) | Quick navigation to Book Turf, Manage Team, Host Tournament, and View Receipts. |
| **C-02** | **Squad Roster & Player Status Manager** | Player roster cards displaying Jersey Number badge, position pill (FWD, MID, DEF, GK), status tag (`Active`, `Bench`, `Injured`), and RSVP attendance summary (`8/11 Confirmed`). | `manage-team.html` (Roster Grid) | Open "+ Add Player" modal to invite new teammate; Tap player to edit jersey number/position or change active/bench status. |
| **C-03** | **4-Step Interactive Turf Booking Stepper** | Streamlined booking wizard: (1) Select Sport & Date → (2) Pick Verified Turf Ground → (3) Choose Hourly Slots with live price calculator → (4) SSLCOMMERZ Deposit Checkout. | `book-turf.html` (4-Step Stepper) | Progress bar tracker (`.step.active`, `.step.done`); Responsive turf card selection; Live slot chip toggles; Modal payment launch. |
| **C-04** | **Tournament Hosting Wizard** | Comprehensive tournament creator form: Tournament Name, Sport Category, Venue Ground, Entry Fee (`৳3,000`), Prize Pool (`৳25,000`), Max Teams (8/16/32), and Rules. | `create-tournament.html` (Host Wizard) | Side-by-side layout with live interactive Tournament Preview Card and registration progress fill bar. |
| **C-05** | **Tournament Directory & Team Registration** | Explore active cups and leagues, view entry fee vs. prize pool boxes, inspect registration deadlines, and submit team registration. | `tournament-registration.html` & `fixtures.html` | Clicking "Register Team" opens registration modal to select squad roster and confirm entry. |
| **C-06** | **Live Match Center & League Standings** | Real-time scoreboard with pulsating live indicator (`@keyframes pulse-dot`), match results, and official league standings table (Rank, Played, Won, Drawn, Lost, GD, Points). | `fixtures.html` (Standings Table) | Segmented tab switcher; Gold/Silver/Bronze rank medal badges; Color-coded form pills (`W`, `D`, `L`). |
| **C-07** | **Captain Invoices & Expense Splitter** | Detailed transaction invoices with PDF download, dynamic SSLCOMMERZ success confirmation banner, and expense split calculator per attending player. | `booking-receipt.html` (Invoice Hub) | Dynamically renders confirmed booking cards from `localStorage`; Share invoice summary to team WhatsApp. |
| **C-08** | **Captain Team & Direct Chat Channel** | Team communication hub featuring pinned announcements, match kick-off alerts, and direct messaging channels with Turf Owners. | `chat.html` (Split Chat Interface) | Split conversation layout (280px left thread list, fluid right chat viewport) with instant message composer. |

---

### Role 3: Turf Owner / Ground Manager
> **User Goal**: Maximize venue slot utilization, manage pitch catalog and amenities, maintain 7-day booking matrix, enter live match scores, approve tournament entries, and track revenue reports.

| # | Feature Name | Feature Description | Core UI Page / Component | Interaction & System Behavior |
| :--- | :--- | :--- | :--- | :--- |
| **O-01** | **Owner Operations Dashboard** | Operational pulse: Today's Bookings count, Today's Slot Revenue (`৳12,400`), Monthly Total Revenue (`৳60,900`), Occupancy Rate (`82%`), and recent customer bookings table. | `owner-dashboard.html` (Overview Hub) | Real-time KPI stat cards with trend badges; Quick-action shortcuts to slot calendar, pitch manager, and score entry. |
| **O-02** | **Multi-Pitch & Facilities Catalog** | Pitch management cards (e.g. Pitch 1 5-a-side Outdoor, Pitch 2 7-a-side Indoor), surface specification, floodlight status, hourly price editor, and "+ Add New Pitch" modal. | `manage-turf.html` (Pitch Catalog) | Edit pricing and facility chips (Parking, Changing Room, Turf Shoes); Upload photo banners; Toggle pitch active state. |
| **O-03** | **7-Day Hourly Interactive Slot Matrix** | 7-day hourly visual matrix grid. Color-coded states: 🟢 Available (Lime hover), 🔴 Customer Booked (with booker pill), 🟡 Walk-in Reserved, ⚪ Maintenance (striped). | `slot-calendar.html` (Matrix Grid) | Week navigation (`← Prev Week / Next Week →`); Turf ground switcher; Clicking any slot opens slot detail modal. |
| **O-04** | **Owner Slot Action & Management Modal** | Contextual modal for any slot: (1) View booker details, (2) Reserve for Walk-in Customer, (3) Block for Maintenance, (4) Cancel / Release slot back to available. | `slot-calendar.html` (Slot Modal) | Instant client-side state toggle with badge color updates and confirmation toast. |
| **O-05** | **Owner Tournament Operations Hub** | Venue tournament manager: view hosted leagues and cups, review and approve registered team entries, assign pitches, and publish tournament schedule. | `owner-tournament.html` (Tournament Hub) | Tab switcher (Active, Upcoming, Completed); Approve team entry; Assign pitch slots to match fixtures. |
| **O-06** | **Digital Referee & Live Score Entry** | Digital match controller: large digital score counters (`.score-input`), goal increment buttons (`+1` / `-1`), goal scorer recorder, card markers (🟨 Yellow / 🟥 Red), and `Publish Score`. | `score-entry.html` (Scoreboard Tool) | Select Home/Away team; Increment goals; Record goal scorer; Publish updates to platform-wide live fixtures. |
| **O-07** | **Revenue & Occupancy Analytics** | Visual CSS bar charts for monthly revenue trends, peak utilization hours (8 PM – 11 PM), occupancy percentages, and financial summary cards. | `owner-reports.html` (Analytics View) | Filter by Week / Month / Year; Export financial ledger as CSV or PDF report. |
| **O-08** | **Customer Inquiries & Slot Messaging** | Inbox of customer inquiries regarding slot bookings, ground rules, and tournament hosting, with quick-reply templates. | `owner-chat.html` (Owner Messages) | Filter conversations by booker; Send instant replies to slot availability queries. |

---

### Role 4: Platform Administrator
> **User Goal**: Supervise platform operations, inspect and verify Turf Owner KYC documents and Trade Licenses, analyze platform utilization heatmaps, manage sport categories, audit financial reports, and broadcast system announcements.

| # | Feature Name | Feature Description | Core UI Page / Component | Interaction & System Behavior |
| :--- | :--- | :--- | :--- | :--- |
| **A-01** | **Admin Platform Command Hub** | Master executive metrics: Total Platform GMV (`৳1,850,000`), Verified Turfs (`54`), Registered Teams (`120`), Pending Approvals (`9`), and System Health monitors. | `admin-dashboard.html` (Master Overview) | Unified stat card grid; Pending approval alert counter; Recent system activity log. |
| **A-02** | **KYC & Ground Approvals Queue** | Verification queue of pending Turf Owner registrations and newly submitted turf grounds awaiting platform listing approval. | `admin-approvals.html` (Approvals List) | Tab switcher between Pending, Approved, and Rejected; Tap "Inspect Documents" to launch modal. |
| **A-03** | **Interactive KYC Document Inspector Modal** | High-fidelity zoomable document viewer featuring authentic Smart NID Card (Front/Back with hologram, chip, and MRZ barcode) and Municipal Councilor Character Certificate with rubber stamp. | `admin-approvals.html` (KYC Inspector Modal) | Switch between NID Part 1 / Part 2 and Ward Certificate; 1-tap "Approve & Verify" (Lime) or "Reject with Reason" (Red). |
| **A-04** | **Platform Utilization Heatmaps & Analytics** | Hourly platform utilization matrix with 0–4 heat intensity levels, sport popularity distribution (Football 65%, Cricket 25%, Basketball 10%), and revenue growth curves. | `admin-analytics.html` (Heatmap & Charts) | Interactive heatmap grid; District and Sport filter controls; Visual CSS-based revenue bar charts. |
| **A-05** | **Sports Categories & Specifications Manager** | Master catalog of supported sports (Football, Cricket, Basketball, Badminton), field dimensions, equipment guidelines, and "+ Add Sport Category" modal. | `admin-categories.html` (Categories Grid) | Toggle sport active/inactive status platform-wide; Add new sport categories with custom rules and specs. |
| **A-06** | **Platform Financial Ledger & Payout Audits** | Comprehensive booking transaction audit ledger, platform commission breakdown, Turf Owner payout disbursements, and CSV/PDF export. | `admin-reports.html` (Financial Reports) | Filter transactions by date range; Trigger "Process Payout" for turf owners; Export financial summary. |
| **A-07** | **System Broadcast & Push Announcements** | Platform broadcast composer: target audience selector (All Users, Turf Owners, Team Captains, Players), priority tagging (`Critical`, `Update`, `Maintenance`), and history list. | `admin-announcements.html` (Broadcast Composer) | Select recipient checkboxes; Enter title and announcement body; Send live notification banner. |

---

## 3. Inter-Role Collaboration Matrix (`Role <-> Role`)

This matrix details the bidirectional operational and data workflows connecting all 4 authenticated roles and public guests across the TurfHub platform:

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                 INTER-ROLE INTERACTION WORKFLOW MATRIX                                 │
├───────────────────┬──────────────────────┬──────────────────────┬──────────────────────┬───────────────┤
│ FROM \ TO         │ 🏃 PLAYER            │ 🏆 TEAM CAPTAIN      │ 🏟️ TURF OWNER        │ 🛡️ ADMIN      │
├───────────────────┼──────────────────────┼──────────────────────┼──────────────────────┼───────────────┤
│ 🏃 PLAYER         │ • Peer match chat    │ • RSVP to match      │ • Book slot (SSL)    │ • Submit FAQ/ │
│                   │ • View free agents   │ • Join squad request │ • Inquire on rules   │   help ticket │
├───────────────────┼──────────────────────┼──────────────────────┼──────────────────────┼───────────────┤
│ 🏆 TEAM CAPTAIN   │ • Send squad invites │ • Challenge VS match │ • 4-Step slot booking│ • Host tourney│
│                   │ • Assign jersey & pos│ • Compare standings  │ • Register for cups  │ • Report score│
├───────────────────┼──────────────────────┼──────────────────────┼──────────────────────┼───────────────┤
│ 🏟️ TURF OWNER     │ • Confirm bookings   │ • Approve cup entry  │ • Coordinate venues  │ • Submit KYC  │
│                   │ • Reply to inquiries │ • Allocate match slot│ • Share pitch specs  │ • Request pay │
├───────────────────┼──────────────────────┼──────────────────────┼──────────────────────┼───────────────┤
│ 🛡️ PLATFORM ADMIN │ • Push announcements │ • Approve tournament │ • Verify NID & Cert  │ • Platform    │
│                   │ • Broadcast alerts   │ • Sanction league cup│ • Disburse payouts   │   Auditing    │
└───────────────────┴──────────────────────┴──────────────────────┴──────────────────────┴───────────────┘
```

### Key Workflow Loops:
1. **Turf Booking Loop**: Player/Captain selects turf (`turf-detail.html` or `book-turf.html`) → Selects slot → Pays via SSLCOMMERZ → Slot turns 🔴 Booked on Owner's Calendar (`slot-calendar.html`) → Confirmed receipt rendered (`booking-receipt.html` / `player-receipts.html`).
2. **Team & RSVP Loop**: Captain creates squad roster (`manage-team.html`) → Player applies via Free Agent board (`join-team.html`) → Captain approves → Captain schedules match → Player clicks "Attending" on RSVP banner (`player-dashboard.html`) → Attendance syncs to Captain's roster (`8/11 Confirmed`).
3. **Tournament & Score Entry Loop**: Captain creates tournament (`create-tournament.html`) → Other teams register (`tournament-registration.html`) → Turf Owner assigns pitches (`owner-tournament.html`) → Matches played → Owner enters live scores & goal scorers (`score-entry.html`) → Standings & fixtures auto-update live platform-wide (`fixtures.html` & `player-fixtures.html`).
4. **KYC Verification Loop**: Turf Owner registers and submits documents → Admin reviews NID & Ward Councilor Certificate in interactive inspector (`admin-approvals.html`) → Admin approves → Owner pitch verified badge activates platform-wide.

---

## 4. Navigation Architecture & Layout Engine (Sidebars & Mobile Views)

TurfHub implements a dual-mode layout architecture: **Static Inlined Shells** for instant zero-CLS static preview and a dynamic JavaScript engine (`auth.js` + `layout.js`) for runtime role switching and responsive off-canvas drawers.

### Role-Based Navigation Specifications

| Role | Dashboard URL | Sidebar & Nav Items | Primary Theme Accent |
| :--- | :--- | :--- | :--- |
| **Turf Owner** | `owner-dashboard.html` | `Dashboard` (📊), `Manage Turfs` (🏟️), `Slot Calendar` (📅), `Tournaments` (🏆), `Score Entry` (⚽), `Messages` (💬), `Reports` (📈) | Electric Lime (`#7ed321`) |
| **Team Captain** | `captain-dashboard.html` | `Dashboard` (📊), `Book Turf` (📅), `Manage Team` (👥), `Tournaments` (🏆), `Host Tournament` (🏅), `My Receipts` (🧾), `Messages` (💬) | Electric Lime (`#7ed321`) |
| **Player** | `player-dashboard.html` | `Dashboard` (📊), `Find Turfs` (🏟️), `Join Team` (👥), `Fixtures` (📋), `My Receipts` (🧾), `Team Chat` (💬) | Electric Lime (`#7ed321`) |
| **Platform Admin** | `admin-dashboard.html` | `Overview` (📊), `Approvals` (✅), `Analytics` (📈), `Categories` (🏷️), `Reports` (📄), `Announce` (📢) | Electric Lime (`#7ed321`) |

### Responsive Layout Behavior:
- **Desktop (>= 900px)**: Fixed sticky left sidebar (`width: 220px`), topbar with brand indicator, page header, and centered main container (`max-width: 1240px`).
- **Mobile (< 900px)**: Off-canvas drawer navigation triggered by `#sidebar-toggle` (hamburger button), dark backdrop overlay (`#sidebar-overlay`), and bottom action buttons.

---

## 5. Payment Gateway Architecture (SSLCOMMERZ EasyCheckout Flow)

Integrated across both Player (`turf-detail.html`) and Captain (`book-turf.html`) booking journeys, the **SSLCOMMERZ EasyCheckout Simulator** replicates an end-to-end Bangladeshi digital payments experience:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        SSLCOMMERZ EASYCHECKOUT PAYMENT FLOW                            │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 1. TRIGGER: User selects pitch slot & clicks "Proceed to Payment" / "Book Now"         │
│    ├── Calculates dynamic amount (e.g. ৳800 or ৳1,600)                                 │
│    └── Opens SSLCOMMERZ Modal Overlay with 256-Bit SSL Encryption Header               │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 2. PAYMENT CHANNELS:                                                                   │
│    ├── 📱 Mobile Financial Services (MFS): bKash, Nagad, Rocket, Upay (Demo Autofill)  │
│    ├── 💳 Credit / Debit Cards: Visa, Mastercard, AMEX (Formatted input simulation)    │
│    └── 🏦 Net Banking: City Touch, BRAC Bank Astha, Islami Bank CellFin, DBBL NexusPay │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 3. HANDSHAKE SIMULATION:                                                               │
│    ├── Phase 1: "Connecting to SSLCOMMERZ Secure Gateway..."                           │
│    ├── Phase 2: "Authenticating OTP & Wallet PIN..."                                   │
│    └── Phase 3: "Payment Authorized & Confirmed!" with green checkmark animation       │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 4. PERSISTENCE & INVOICING:                                                            │
│    ├── Saves transaction object to localStorage ('turfhub_recent_booking')             │
│    │   { turfName, location, sport, date, time, price, paymentMethod, trxId, refId }   │
│    └── Auto-redirects to booking-receipt.html / player-receipts.html with banner       │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 6. Master UI Screen & Component Inventory (Frame Catalog)

Use this inventory to structure design files, Figma frames, component sets, and frontend routes:

### 🏠 1. Public & Authentication Screens
- `UI-GUEST-01` (`index.html`): Public Landing Page (Hero, Quick-Search, Features, Top Turfs, Stats, Footer).
- `UI-GUEST-02` (`login.html`): Multi-Role Authentication Portal (Role Cards, Sign In / Sign Up Forms, Demo Switcher).
- `UI-GUEST-03` (`contact.html`): Support & Inquiry Center (Contact Form, Phone/WhatsApp, Map, FAQ Accordion).

### 🏃 2. Player Experience Screens
- `UI-PLY-01` (`player-dashboard.html`): Player Hub & RSVP (Performance Stats, Upcoming Match Hero Card, Attending Toggle).
- `UI-PLY-02` (`turf-detail.html`): Master-Detail Turf Ground Explorer (Search, Filter, Master List, Detail Pane).
- `UI-PLY-03` (`turf-detail.html`): Venue Photo Gallery & Amenities (Thumbnail Carousel, Spec Badges, Amenities).
- `UI-PLY-04` (`turf-detail.html`): 7-Day Slot Matrix & Checkout (Date Strip, Slot Pills, SSLCOMMERZ Modal).
- `UI-PLY-05` (`join-team.html`): Free Agent & Team Recruitment (Team Cards, Squad Capacity, Join Application Modal).
- `UI-PLY-06` (`player-fixtures.html`): Player Fixtures & Standings (Upcoming Schedule, Attendance State, League Table).
- `UI-PLY-07` (`player-receipts.html`): Player Receipts & Payment History (Spend KPI Strip, Invoice Cards, PDF Export).
- `UI-PLY-08` (`player-chat.html`): Player Direct & Team Chat (Captain & Owner Threads, Message Composer).

### 🏆 3. Captain Experience Screens
- `UI-CAP-01` (`captain-dashboard.html`): Captain Command Center (Team Header, Match Countdown, Roster Summary, Quick Actions).
- `UI-CAP-02` (`manage-team.html`): Squad & Player Roster Manager (Jersey Badges, Position Pills, Active/Bench Tags, Add Player Modal).
- `UI-CAP-03` (`book-turf.html`): 4-Step Turf Booking Stepper (Step 1: Sport/Date → Step 2: Pitch → Step 3: Slots → Step 4: Pay).
- `UI-CAP-04` (`create-tournament.html`): Host Tournament Wizard (Organizer Form, Prize Pool Setup, Live Preview Card).
- `UI-CAP-05` (`tournament-registration.html`): Tournament Directory & Registration (Browse Tournaments, Entry Fees, Prize Boxes).
- `UI-CAP-06` (`fixtures.html`): Match Fixtures & Official League Standings (Live Score Pulse, Fixture Cards, Form Pills `W/D/L`).
- `UI-CAP-07` (`booking-receipt.html`): Captain Receipts & Cost Splitter (Invoice Cards, WhatsApp Split Share, Download PDF).
- `UI-CAP-08` (`chat.html`): Captain Team & Direct Chat Hub (Team Announcements Channel, Owner Direct Message Thread).

### 🏟️ 4. Turf Owner Experience Screens
- `UI-OWN-01` (`owner-dashboard.html`): Owner Operations Dashboard (Today's Bookings, Revenue KPIs, Occupancy Rate, Recent Bookings).
- `UI-OWN-02` (`manage-turf.html`): Multi-Pitch & Facilities Catalog (Pitch Cards, Specs, Pricing Editor, Add Pitch Modal).
- `UI-OWN-03` (`slot-calendar.html`): 7-Day Interactive Slot Matrix (Hourly Slot Grid, Available/Booked/Reserved/Maintenance States).
- `UI-OWN-04` (`slot-calendar.html`): Owner Slot Action Modal (Walk-in Hold, Maintenance Block, Booker Inspection).
- `UI-OWN-05` (`owner-tournament.html`): Owner Tournament Operations (Tournament Manager, Team Approvals, Pitch Scheduling).
- `UI-OWN-06` (`score-entry.html`): Digital Referee & Score Entry (Score Inputs, Scorer Selector, Card Markers, Publish Score).
- `UI-OWN-07` (`owner-reports.html`): Revenue & Occupancy Reports (Monthly Bar Charts, Peak Hour Analysis, CSV/PDF Export).
- `UI-OWN-08` (`owner-chat.html`): Owner Customer Inquiries & Chat (Customer Conversation List, Quick-Reply Messages).

### 🛡️ 5. Platform Admin Experience Screens
- `UI-ADM-01` (`admin-dashboard.html`): Admin Platform Command Hub (Total GMV, Verified Grounds, Pending Queue, Activity Stream).
- `UI-ADM-02` (`admin-approvals.html`): KYC & Ground Verification Queue (Pending Owner & Ground Submissions List).
- `UI-ADM-03` (`admin-approvals.html`): Interactive KYC Inspector Modal (Zoomable Smart NID Part 1/2, Municipal Councilor Certificate).
- `UI-ADM-04` (`admin-analytics.html`): Platform Heatmaps & Sport Popularity (Hourly Matrix Heatmap, Sport Distribution Charts).
- `UI-ADM-05` (`admin-categories.html`): Sports Categories & Specifications (Sport Cards, Dimensions, Equipment, Add Category Modal).
- `UI-ADM-06` (`admin-reports.html`): Platform Financial Audit & Payouts (Transaction Ledger, Commission Breakdown, Payout Disbursement).
- `UI-ADM-07` (`admin-announcements.html`): System Broadcast & Announcements (Broadcast Composer, Target Checkboxes, Priority Tags).

### 🧩 6. Reusable Global Modals & Dialogs
- `UI-MODAL-01`: SSLCOMMERZ EasyCheckout Modal (`#ssl-modal` in `turf-detail.html` and `book-turf.html`).
- `UI-MODAL-02`: Add / Invite Teammate Modal (`manage-team.html`).
- `UI-MODAL-03`: Add Pitch / Ground Facility Modal (`manage-turf.html`).
- `UI-MODAL-04`: Add Sport Category Modal (`admin-categories.html`).
- `UI-MODAL-05`: KYC Smart Document Inspector Modal (`admin-approvals.html`).
- `UI-MODAL-06`: Slot Action & Walk-In Reservation Modal (`slot-calendar.html`).

---

## 7. Interactive Prototype Flows & User Gesture Map

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                FIGMA & FRONTEND INTERACTION FLOW MAP                                   │
├──────────────────────────┬─────────────────────────────┬──────────────────┬────────────────────────────┤
│ Trigger Action           │ Source Screen / Frame       │ Transition / UX  │ Destination Screen / State │
├──────────────────────────┼─────────────────────────────┼──────────────────┼────────────────────────────┤
│ Tap "Sign In"            │ `login.html` (Role Select)  │ Smart Animate    │ Role Dashboard (`*-dash`)  │
│ Select Quick Search Turf │ `index.html` (Search Pill)  │ Push / Navigate  │ `turf-detail.html`         │
│ Tap Master Turf Card     │ `turf-detail.html` (Master) │ Instant DOM Swap │ Updates Right Detail Pane  │
│ Tap Calendar Date Chip   │ `turf-detail.html` / Book   │ State Reload     │ Refreshes Hourly Slots     │
│ Tap Available Slot Chip  │ `turf-detail.html` / Book   │ CSS Class Toggle │ Selected State + Recalc Sum│
│ Tap "Proceed to Pay"     │ `turf-detail.html` / Book   │ Fade / Backdrop  │ SSLCOMMERZ Modal Opens     │
│ Click bKash Demo Pay     │ SSLCOMMERZ Modal            │ Keyframe Spinner │ Payment Confirmed Checkmark│
│ Redirect After Payment   │ SSLCOMMERZ Success State    │ Auto-Redirect    │ `booking-receipt.html`     │
│ Tap "Attending" on RSVP  │ `player-dashboard.html`     │ Instant State    │ Badge turns Green (Active) │
│ Tap "+ Add Player"       │ `manage-team.html`          │ Modal Slide In   │ Add Player Dialog Opens    │
│ Tap Slot in Matrix       │ `slot-calendar.html`        │ Modal Fade In    │ Owner Slot Action Sheet    │
│ Tap Goal `+1` Button     │ `score-entry.html`          │ Spring Animate   │ Score increments + Scorer  │
│ Tap "Inspect KYC"        │ `admin-approvals.html`      │ Backdrop Zoom    │ Smart NID & Seal Modal     │
│ Click "Process Payout"   │ `admin-reports.html`        │ Toast / Confirm  │ Payout Status Disbursed    │
│ Toggle Sidebar Drawer    │ Mobile Hamburger (`<900px`) │ Slide Right 0.2s │ Off-Canvas Menu Drawer     │
└──────────────────────────┴─────────────────────────────┴──────────────────┴────────────────────────────┘
```

---

## 8. Complete Codebase UI Page Mapping & Specification Matrix

This matrix maps every single HTML page in the TurfHub repository (`http://localhost/TurfHub/`) to its functional domain, authorized roles, and design specifications:

| # | File Name | Primary Role Access | Primary Purpose & Key Features | CSS Architecture Used |
| :--- | :--- | :--- | :--- | :--- |
| **01** | `index.html` | Public Guest | Landing page with Hero stadium lighting, quick-search pill bar, featured turfs carousel, and platform stats. | Raw CSS Hero + Tailwind CDN Grid |
| **02** | `login.html` | Public Guest | Multi-role authentication selector (Owner, Captain, Player, Admin) with instant glow selection and tab switcher. | Raw CSS Role Cards + Tailwind Form |
| **03** | `contact.html` | Public Guest | Contact support center, inquiry form, hotline, WhatsApp trigger, and FAQ accordion. | Scoped CSS + Tailwind Grids |
| **04** | `player-dashboard.html` | Player | Player performance statistics (Matches, Goals, Rating) and 1-tap Match RSVP banner (`Attending` / `Unavailable`). | Raw CSS Badges + Tailwind Metrics |
| **05** | `turf-detail.html` | Player / Captain | Master-detail turf ground explorer, photo gallery switcher, 7-day slot matrix, and SSLCOMMERZ dummy payment gateway. | Scoped Master-Detail CSS + Gateway Modal |
| **06** | `join-team.html` | Player | Free agent team recruitment board with squad capacity tags and team join request modal. | Raw CSS Capacity Tags + Tailwind Cards |
| **07** | `player-fixtures.html` | Player | Player fixture schedule with live stadium markers, match attendance badges, and league standings. | Standings Table CSS + Tailwind Tabs |
| **08** | `player-receipts.html` | Player | Booking receipts history with spend summary strip, dynamic invoice cards from `localStorage`, and PDF export. | Scoped Invoice CSS + Dynamic Injection |
| **09** | `player-chat.html` | Player | Direct messaging interface between Player, Team Captain, and Turf Ground Hosts. | Raw CSS Chat Bubbles + Split Viewport |
| **10** | `captain-dashboard.html` | Team Captain | Captain command hub with match countdown card, squad roster preview, and quick-action shortcuts. | Raw CSS Player Avatars + Tailwind Grid |
| **11** | `manage-team.html` | Team Captain | Squad management with jersey number badges, position pills (FWD, MID, DEF, GK), status tags, and Add Player modal. | Scoped Roster CSS + Modal Overlay |
| **12** | `book-turf.html` | Team Captain | 4-step turf booking stepper (Sport/Date → Turf → Slots → SSLCOMMERZ payment) with live price calculation. | Raw CSS Stepper + Centered Turf Grid |
| **13** | `create-tournament.html` | Team Captain | Tournament hosting wizard with entry fee/prize pool setup and live interactive tournament preview card. | Progress Bar CSS + Tailwind 2-Col Form |
| **14** | `tournament-registration.html` | Team Captain / Player | Tournament directory with prize pool boxes, entry fee cards, and squad registration flow. | Scoped Prize Cards + Tailwind Grid |
| **15** | `fixtures.html` | Captain / Player / Guest | Live match center with pulsing red dot, fixture results, and official league standings table with form guide. | Pulse Dot CSS + Standings Table CSS |
| **16** | `booking-receipt.html` | Team Captain | Captain transaction receipts with dynamic SSLCOMMERZ confirmation banner and WhatsApp expense splitter. | Scoped Receipt CSS + Dynamic Injection |
| **17** | `chat.html` | Team Captain | Team announcement channel and owner direct messaging with custom speech bubbles and unread counters. | Full-Height Chat Shell CSS |
| **18** | `owner-dashboard.html` | Turf Owner | Operations overview: Today's Bookings, Today's Slot Revenue, Monthly Total, Occupancy Rate, and Recent Bookings. | Unified Stat Card Grid CSS + Inlined Sidebar |
| **19** | `manage-turf.html` | Turf Owner | Pitch catalog management (5-a-side / 7-a-side), pricing editor, facility amenity chips, and Add Pitch modal. | Pitch Card CSS + Modal Dialog |
| **20** | `slot-calendar.html` | Turf Owner | 7-day hourly slot matrix calendar with state color rules (Available, Booked, Reserved, Maintenance) and Slot Action modal. | 7-Day Matrix CSS + Slot State Engine |
| **21** | `owner-tournament.html` | Turf Owner | Venue tournament management: team entry approvals, pitch slot allocation, and schedule publishing. | Scoped Tournament CSS + Tailwind Tabs |
| **22** | `score-entry.html` | Turf Owner | Digital scoreboard referee tool: digital score inputs, goal counters (`+1`), scorer recorder, and card markers. | Scored Referee Input CSS |
| **23** | `owner-reports.html` | Turf Owner | Financial and occupancy analytics with vertical CSS revenue bars, peak hour breakdown, and PDF export. | Scoped CSS Chart Bars + Tailwind Stat Rows |
| **24** | `owner-chat.html` | Turf Owner | Customer inquiry inbox for slot bookings and ground queries with quick-reply messaging. | Split Chat CSS Viewport |
| **25** | `admin-dashboard.html` | Platform Admin | Admin command center: Total GMV, Active Turfs, Pending Approvals alert, and system activity log. | Master Admin Sidebar CSS + KPI Stat Grid |
| **26** | `admin-approvals.html` | Platform Admin | KYC & ground verification queue with high-fidelity zoomable Smart NID card and Municipal Councilor Certificate inspector. | Authentic NID & Seal CSS + Modal Container |
| **27** | `admin-analytics.html` | Platform Admin | Platform utilization hourly heatmap matrix (0–4 heat levels), sport popularity breakdown, and growth charts. | Heatmap Grid CSS + CSS Flex Bar Charts |
| **28** | `admin-categories.html` | Platform Admin | Sports categories manager with pitch dimensions, equipment tags, and Add Sport Category modal. | Category Card CSS + Modal Form |
| **29** | `admin-reports.html` | Platform Admin | Platform financial audit ledger, Turf Owner payout disbursements, commission tracking, and CSV/PDF export. | Financial Table CSS + Badge System |
| **30** | `admin-announcements.html` | Platform Admin | System push broadcast composer with audience targeting (All, Owners, Captains, Players) and priority badges. | Broadcast Composer CSS + Alert Badges |

---

## 🎯 Summary Checklist for UI/UX Designers & Frontend Developers
- [x] **Color Tokens**: Standardized Brand Forest (`#0d2818`), Brand Forest Card (`#133923`), Electric Lime (`#7ed321`), App BG (`#f5f5f0`), White (`#ffffff`), and status badge colors.
- [x] **Role Hierarchy**: Clean 4-role authenticated structure (Player, Captain, Turf Owner, Admin) + Public Guest.
- [x] **No Coach / Marketplace Elements**: Completely excised all references to Coach licensing, Trainee radars, B2C Pro-Shops, and C2C peer-to-peer gear commerce.
- [x] **Booking & Payment Fidelity**: Accurately blueprints the 4-step stepper (`book-turf.html`), master-detail explorer (`turf-detail.html`), and SSLCOMMERZ EasyCheckout modal.
- [x] **Sports Governance Fidelity**: Fully details the tournament wizard, live fixtures with pulsing indicator, league standings table, digital scoreboard referee entry (`score-entry.html`), and Smart NID KYC verification inspector (`admin-approvals.html`).
