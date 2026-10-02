# 📱 TurfHub — Mobile Figma Design & Role-to-Feature Specification

> **A comprehensive, role-by-role feature architecture and mobile UI/UX blueprint (`Role <-> Feature` & `Role <-> Role`) specifically engineered for designing Figma mobile screen frames, prototypes, and design systems.**

---

## 📌 Table of Contents
1. [Mobile UI/UX Foundations & Figma Frame Standards](#1-mobile-uiux-foundations--figma-frame-standards)
2. [Master Role-to-Feature Architecture (`Role <-> Feature`)](#2-master-role-to-feature-architecture-role---feature)
   - [Role 0: Public Guest / Unauthenticated Visitor](#role-0-public-guest--unauthenticated-visitor)
   - [Role 1: Registered Player (Athlete / Free Agent)](#role-1-registered-player-athlete--free-agent)
   - [Role 2: Team Captain (Squad Leader & Organizer)](#role-2-team-captain-squad-leader--organizer)
   - [Role 3: Certified Coach / Trainer (Athletic Mentor & Academy Specialist)](#role-3-certified-coach--trainer-athletic-mentor--academy-specialist)
   - [Role 4: Turf Owner / Ground Manager](#role-4-turf-owner--ground-manager)
   - [Role 5: Platform Administrator](#role-5-platform-administrator)
3. [TurfHub Sports Marketplace Architecture (C2C & B2C)](#3-turfhub-sports-marketplace-architecture-c2c--b2c)
   - [B2C Pro-Shop: Turf Owner to Players & Captains](#b2c-pro-shop-turf-owner-to-players--captains)
   - [C2C Peer-to-Peer: Player-to-Player / Captain-to-Captain Gear Exchange](#c2c-peer-to-peer-player-to-player--captain-to-captain-gear-exchange)
   - [Marketplace Core Mobile Feature Catalog](#marketplace-core-mobile-feature-catalog)
4. [Inter-Role Collaboration Matrix (`Role <-> Role`)](#4-inter-role-collaboration-matrix-role---role)
5. [Mobile Navigation Architecture & Bottom Tab Bars](#5-mobile-navigation-architecture--bottom-tab-bars)
6. [Master Figma Mobile Screen & Component Inventory (Frame Catalog)](#6-master-figma-mobile-screen--component-inventory-frame-catalog)
7. [Interactive Mobile Prototype Flows & Gestures](#7-interactive-mobile-prototype-flows--gestures)

---

## 1. Mobile UI/UX Foundations & Figma Frame Standards

### 📐 Mobile Artboard & Screen Standards
- **Primary Canvas Frame**: `393 x 852 px` (iPhone 15 / 16 Standard & Pixel 8 baseline).
- **Safe Area Insets**:
  - Top Notch / Dynamic Island: `54px` Status Bar.
  - Bottom Home Indicator: `34px` Home Indicator Zone.
  - Usable Content Viewport: `393 x 764 px`.
- **Grid & Spacing Scale (8pt System)**:
  - Screen Padding (Horizontal Gutter): `16px` or `20px`.
  - Component Gap Spacing: `8px`, `12px`, `16px`, `24px`, `32px`.
  - Minimum Touch Target: `44 x 44 px` (for all buttons, icon triggers, and slot chips).
  - Corner Radii: `8px` (Tags), `12px` (Inputs/Buttons), `16px`–`20px` (Cards), `24px`–`28px` (Bottom Sheets).

### 🎨 Mobile Design System Variables (Figma Tokens)
- **Primary Brand (Forest)**:
  - `Brand/Forest-Main`: `#0d2818` (Primary Dark Shell, Top Navs, Hero Cards)
  - `Brand/Forest-Dark`: `#071e14` (Deep Backgrounds, Dark Footers)
  - `Brand/Forest-Card`: `#133923` (Elevated dark containers)
- **Accent Brand (Electric Lime)**:
  - `Brand/Lime-Bright`: `#7ed321` (Primary CTAs, Active States, Live Indicators)
  - `Brand/Lime-Dark`: `#6ab81c` (Pressed/Hover Button States)
  - `Brand/Lime-Glow`: `rgba(126, 211, 33, 0.15)` (Active Chip Fills, Selected Card Outlines)
- **Marketplace & Accent Tones**:
  - `Accent/Gold`: `#f59e0b` (Coach Badges, Premium Gear, Verified Seller)
  - `Accent/Purple`: `#8b5cf6` (C2C Community Marketplace Tag)
  - `Accent/Teal`: `#0d9488` (B2C Pro-Shop Official Turf Tag)
- **Neutrals & Surfaces**:
  - `Surface/App-BG`: `#f5f5f0` (Mobile App Canvas BG)
  - `Surface/Card-White`: `#ffffff` (Elevated White Cards & Bottom Sheets)
  - `Surface/Border`: `#e8ede8` (1px Hairline dividers and card outlines)
  - `Text/Primary`: `#1a1a1a` (Titles, Headings, Primary values)
  - `Text/Muted`: `#6b7280` (Subtitles, Meta timestamps, labels)
- **State Badges**:
  - `State/Success`: `#5a9e12` (Confirmed, Available `#7ed321`, Paid, In-Stock)
  - `State/Warning`: `#d97706` (Pending, Reserved, Live pulsing `#f59e0b`, Low Stock)
  - `State/Danger`: `#e53935` (Booked `#fee2e2`, Rejected, Injured, Out of Stock)

---

## 2. Master Role-to-Feature Architecture (`Role <-> Feature`)

```
                                          ┌────────────────────────────────┐
                                          │      TURFHUB PLATFORM          │
                                          └───────────────┬────────────────┘
         ┌──────────────────┬─────────────────────┼────────────────────┬──────────────────┬─────────────────┐
         ▼                  ▼                     ▼                    ▼                  ▼                 ▼
┌─────────────────┐ ┌───────────────┐ ┌───────────────────────┐ ┌──────────────┐ ┌──────────────┐ ┌────────────────┐
│  PUBLIC GUEST   │ │ PLAYER (USER) │ │  TEAM CAPTAIN (LEADER)│ │ COACH/TRAINER│ │  TURF OWNER  │ │ PLATFORM ADMIN │
└─────────────────┘ └───────────────┘ └───────────────────────┘ └──────────────┘ └──────────────┘ └────────────────┘
```

---

### Role 0: Public Guest / Unauthenticated Visitor
> **User Goal**: Discover available turf grounds, explore sports tournaments, browse marketplace items, find coaches, and easily sign up or switch roles.

| # | Feature Name | Feature Description | Figma Mobile Screen / Component | User Interaction / Gesture |
| :--- | :--- | :--- | :--- | :--- |
| **G-01** | **Mobile Hero & Stadium Showcase** | High-contrast dark forest stadium header with live availability counter (`50+ premium turfs`) and value proposition. | `M-GUEST-01-Landing` (Top 40% Hero viewport) | Vertical scroll; Tap on "Get Started" to open Auth Switcher. |
| **G-02** | **Sticky Quick-Search Pill Bar** | Compact mobile search bar with expandable bottom sheet filter for Location (Dhaka, Sylhet, Chittagong), Sport, Date, and Time. | `M-GUEST-01-Landing` + `M-BS-SearchFilter` (Bottom Sheet) | Tap search pill -> triggers 80% height bottom sheet with date picker and sport chips. |
| **G-03** | **Featured Turfs Carousel** | Horizontal snap-scroll cards showcasing top-rated venues, pricing per hour (`৳800/hr`), sport badges, and real-time status. | `M-GUEST-01-Landing` (Carousel Component) | Horizontal swipe gestures; Tap card -> navigates to Venue Details. |
| **G-04** | **Platform Metric Counters** | Animated stat chips displaying 12k+ players, 200+ tournaments, 50+ venues, 80+ coaches, 4.8/5 ratings. | `M-GUEST-01-Landing` (2x2 Grid Widget) | Passive display / Scroll into view. |
| **G-05** | **Multi-Role Authentication Switcher** | Role selector cards (Player, Captain, Coach, Turf Owner, Admin) + Tab toggle for `Sign In` and `Sign Up`. | `M-AUTH-01-RoleSelect` & `M-AUTH-02-SignInUp` | Tap role card (glowing lime ring) -> updates form context and permissions. |
| **G-06** | **Guest Public Fixtures & Standings** | View-only match center showcasing ongoing tournaments, live scores, and group standings. | `M-GUEST-02-PublicFixtures` (Tabbed View) | Segmented control toggle between "Fixtures" and "Standings". |
| **G-07** | **Public Marketplace Showcase** | Browse trending sports gear (boots, kits, balls) with guest view price tags and sign-in prompts to buy/sell. | `M-GUEST-04-PublicMarketplace` | Horizontal scroll feed; Tap "Buy" prompts login modal. |
| **G-08** | **Help, FAQs & Contact Support** | Accordion FAQ cards, direct hotline tap-to-call, WhatsApp trigger, and support ticket submission form. | `M-GUEST-03-ContactHelp` | Tap accordion to expand; Tap floating call button. |

---

### Role 1: Registered Player (Athlete / Free Agent)
> **User Goal**: Check upcoming match RSVPs, book individual slots, hire a coach, buy/sell pre-owned gear (C2C), purchase turf refreshments (B2C), join squads as a free agent, and track payments.

| # | Feature Name | Feature Description | Figma Mobile Screen / Component | User Interaction / Gesture |
| :--- | :--- | :--- | :--- | :--- |
| **P-01** | **Player Hub & Performance KPIs** | Personalized greeting with circular avatar, Player Rating (`4.8 ★`), Matches Played (`18`), Goals Scored (`12`), Win Rate (`72%`). | `M-PLY-01-Dashboard` (Header & Stat Row) | Pull-to-refresh; Tap avatar to edit profile. |
| **P-02** | **Interactive Match RSVP Card** | Upcoming match banner with venue name, countdown clock, and one-tap RSVP buttons (`Attending` ✅ / `Unavailable` ❌). | `M-PLY-01-Dashboard` (Sticky Hero Card) | Tap "Attending" -> updates state badge to green & syncs with Captain's roster. |
| **P-03** | **Master-Detail Turf Ground Explorer** | Search & filter turfs by sport, district, pricing, lighting, AC lounge, and parking. Master card list with thumbnail badges. | `M-PLY-02-TurfExplore` | Vertical list scroll; Filter pill chips; Tap card to push detail screen. |
| **P-04** | **Mobile Venue Gallery & Amenity Tags** | Full-width photo carousel with thumbnail pagination, location map pin, surface type (FIFA 2-star artificial grass), and amenity icons. | `M-PLY-03-TurfDetail` | Swipe photos horizontally; Tap map to open Google Maps navigation. |
| **P-05** | **Mobile Date & Hourly Slot Picker** | 7-day horizontal calendar date strip + 2-column touch-friendly time slot chips (`Available` [Lime Outline], `Booked` [Red Disabled], `Selected` [Solid Lime]). | `M-PLY-03-TurfDetail` (Slot Matrix Section) | Tap date chip -> updates slot matrix; Tap slot chip -> recalculates total price. |
| **P-06** | **Mobile SSLCOMMERZ Checkout Sheet** | Native mobile payment sheet supporting bKash, Nagad, Rocket, Upay, Visa/Mastercard with simulated 1-tap OTP verification. | `M-BS-PaymentCheckout` (Full-Screen Bottom Sheet) | Tap "Proceed to Pay" -> displays MFS logos -> 1-tap instant payment success. |
| **P-07** | **Digital Booking Receipts & Invoice Bar** | List of all paid bookings with unique Transaction ID, QR code badge, payment status, and 1-tap `Download PDF` / `Share Receipt`. | `M-PLY-04-Receipts` | Tap receipt card -> opens detailed invoice modal; Tap share icon. |
| **P-08** | **Free Agent Board & Join Squad** | Athlete recruitment hub: browse open captain recruitment posts, filter by preferred sport/position, and submit "Request to Join". | `M-PLY-05-FreeAgentBoard` | Tap "Apply to Squad" -> opens message bottom sheet to captain. |
| **P-09** | **Book Certified Coach for Training** | Browse certified coaches directory, view coaching rates (`৳500/session`), book private 1-on-1 coaching at verified turfs, and track personal fitness logs. | `M-PLY-09-CoachFinder` & `M-BS-CoachBooking` | Tap "Book Training Session" -> selects coach & slot matrix -> MFS payment. |
| **P-10** | **C2C Player Gear Marketplace (Sell/Buy)** | Buy & sell pre-owned boots, bats, kits; snap camera photos, set price (`৳1,800`), choose handover venue, chat with buyer/seller. | `M-MKT-01-MarketHome` & `M-MKT-03-CreateListing` | Tap `+ Sell Gear` FAB -> upload 3 photos, select condition pill, post. |
| **P-11** | **B2C Turf Pro-Shop Pre-Order** | Pre-order refreshments (energy drinks, hydration packs), grip socks, or rental bibs to be ready at turf reception upon arrival. | `M-MKT-02-ProductDetail` + `M-BS-TurfAddons` | Toggle "Add to Turf Booking" -> adds item cost to booking invoice. |
| **P-12** | **1-on-1 & Team Squad Chat** | Real-time chat with Captain, Coach, teammates, or Ground Owners with unread badges, offer negotiation buttons, and location pins. | `M-PLY-07-ChatList` & `M-PLY-08-ChatThread` | Tap thread -> opens mobile chat screen; Send instant text & match location. |

---

### Role 2: Team Captain (Squad Leader & Organizer)
> **User Goal**: Lead squad operations, recruit players & certified coaches, enter leagues, book turfs via 4-step stepper, order team uniforms (B2C), and manage split finances.

| # | Feature Name | Feature Description | Figma Mobile Screen / Component | User Interaction / Gesture |
| :--- | :--- | :--- | :--- | :--- |
| **C-01** | **Captain Command Center** | Team badge, Next Match Countdown card (VS opponent, pitch name), Quick Action Floating Hub (Book Pitch, Hire Coach, Enter Cup, Order Kits). | `M-CAP-01-Dashboard` | Pull-to-refresh; Quick action shortcuts. |
| **C-02** | **Squad Roster & Player Status Manager** | Player roster cards displaying Jersey Number badge, position pill (FWD, MID, DEF, GK), status tag (`Active`, `Bench`, `Injured`), and RSVP summary (`8/11 Confirmed`). | `M-CAP-02-ManageTeam` | Swipe left on player to Bench/Remove; Tap to edit jersey/position. |
| **C-03** | **Player & Coach Recruitment Hub** | Post open positions for squad vacancies (e.g. Need Goalkeeper) or post Team Coach Vacancies with target tournament goals and budget. | `M-BS-InvitePlayer` & `M-CAP-09-HireCoach` | Tap "Hire Coach" -> opens coach candidate list with AFC/FIFA badges. |
| **C-04** | **4-Step Mobile Turf Booking Stepper** | Streamlined booking workflow: (1) Select Sport & Date → (2) Pick Verified Turf → (3) Choose Hourly Slots & Add Pro-Shop Addons → (4) SSLCOMMERZ Deposit Checkout. | `M-CAP-03-BookStepper` (Steps 1–4) | Multi-step progress bar; Sticky bottom bar with "Next Step: Select Slots" button. |
| **C-05** | **Tournament Hosting Wizard** | Mobile form wizard to create a custom tournament: Tournament Name, Sport, Pitch location, Entry Fee (`৳3,000`), Prize Pool (`৳25,000`), Max Teams (8/16/32), Rules. | `M-CAP-04-CreateTournament` | Form fields with step validation; Instant preview card rendering. |
| **C-06** | **Tournament Directory & One-Tap Registration** | Explore active cups/leagues, view tournament prize pool cards, check registration deadlines, and pay team registration fees via MFS. | `M-CAP-05-Tournaments` | Tap "Register Team" -> opens team roster selector + MFS checkout sheet. |
| **C-07** | **B2C Bulk Uniform & Gear Orders** | Order customized team kits, bulk practice balls, and hydration cases directly from Turf Pro-Shops or official suppliers at team discount. | `M-MKT-07-TeamUniformShop` | Select kit colors, input 15 player names/numbers, checkout via MFS. |
| **C-08** | **Captain Invoices & Expense Splitter** | View detailed transaction receipts, split turf booking & coaching cost per attending player (`(৳800 + ৳500) ÷ 10 = ৳130/player`), and copy payment request links. | `M-CAP-07-BookingReceipts` | Tap "Split Cost" -> calculates per-player amount -> generates WhatsApp text. |
| **C-09** | **Captain Team & Tactical Chat Channel** | Announcement channel with pinned tactical lineups, training schedules set by Team Coach, and match kick-off push alerts. | `M-CAP-08-TeamChat` | Send pinned announcement; View member read receipts. |

---

### Role 3: Certified Coach / Trainer (Athletic Mentor & Academy Specialist)
> **User Goal**: Build coaching profile, apply for team coaching jobs, apply for vacancies at Turf Owner venues / academies, run training sessions, track trainee development, and earn session fees.

| # | Feature Name | Feature Description | Figma Mobile Screen / Component | User Interaction / Gesture |
| :--- | :--- | :--- | :--- | :--- |
| **CO-01** | **Coach Command Center & Schedule** | Daily coaching schedule, active trainees counter (`24`), booked 1-on-1 sessions, team contracts (`2 Active Teams`), monthly earnings (`৳34,000`). | `M-COA-01-Dashboard` | Stat cards with session timeline; Tap session to view trainee notes. |
| **CO-02** | **Coach Profile & Credentials Inspector** | Certification showcase (AFC 'C'/'B' License, FIFA Grassroots, BPED), specialty pills (Tactics, Striker Finishing, Goalkeeping), hourly rate (`৳600/hr`), intro reel. | `M-COA-02-CoachProfile` | Tap "Edit Credentials" -> upload certification scan for Admin badge. |
| **CO-03** | **Join a Team / Squad Job Board** | Browse team captain job posts seeking tournament coaches, view offered stipends, submit coaching proposals with tactical philosophies. | `M-COA-03-TeamJobBoard` | Tap "Apply to Squad" -> attach tactical portfolio & proposed session fee. |
| **CO-04** | **Turf Owner Vacancy & Academy Residency** | Browse turf owner job listings seeking Resident Turf Coaches / Academy Directors; apply to run weekend football/cricket youth clinics at specific turf venues. | `M-COA-04-TurfVacancies` | Filter by Turf Venue; Tap "Apply for Turf Vacancy" -> submit pitch residency request. |
| **CO-05** | **Training Session Manager & Turf Slot Booking** | Create customized training sessions (Solo Drill, Group 5v5 Tactical, Weekend Boot Camp), sync slot booking with venue calendar, send RSVP to trainees. | `M-COA-05-SessionManager` | Tap `+ New Session` -> select turf ground, date, maximum trainees, price. |
| **CO-06** | **Trainee Roster & Skill Evaluation** | Performance scorecard for individual athletes (Pace, Stamina, Passing, Shooting, Discipline), progress radar chart, private feedback notes. | `M-COA-06-TraineeTracker` | Tap athlete card -> adjust skill sliders -> send feedback notification. |
| **CO-07** | **Coach Earnings & Payout Ledger** | Track incoming payments from 1-on-1 bookings, team coaching stipends, turf academy commission cuts, 1-tap payout withdrawal request to bKash/Nagad. | `M-COA-07-EarningsLedger` | Tap "Withdraw Earnings" -> enter amount -> instant payout confirmation. |
| **CO-08** | **Coach Direct Messaging & Tactical Board** | In-app chat with Captains (tactical planning), Players (personal fitness homework), and Turf Owners (ground reservation & equipment access). | `M-COA-08-CoachChat` | Send tactical whiteboard diagrams & training video clips. |

---

### Role 4: Turf Owner / Ground Manager
> **User Goal**: Maximize pitch bookings, post coaching vacancies for venue academies, operate B2C Pro-Shop & concession sales, enter match scores, and track revenue.

| # | Feature Name | Feature Description | Figma Mobile Screen / Component | User Interaction / Gesture |
| :--- | :--- | :--- | :--- | :--- |
| **O-01** | **Owner Operations Dashboard** | Operational pulse: Today's Bookings (`28`), Pro-Shop Revenue (`৳12,400`), Monthly Total (`৳60,900`), Occupancy Rate (`82%`), Active Academy Camps (`2`). | `M-OWN-01-Dashboard` | Stat cards with trend indicators; Tap card to view filtered report. |
| **O-02** | **Multi-Pitch & Facilities Catalog** | Pitch management cards (Pitch 1 5-a-side Outdoor, Pitch 2 7-a-side Indoor), lighting status, hourly pricing editor, and photo gallery manager. | `M-OWN-02-ManageTurfs` | Tap "Edit Pricing/Amenities"; Tap `+ Add New Pitch` Floating Action Button (FAB). |
| **O-03** | **Mobile 7-Day & Daily Slot Matrix** | Mobile day-view & week-view slot calendar. Visual color codes: 🟢 Available, 🔴 Customer Booked, 🟡 Owner Reserved, ⚪ Maintenance, 🔵 Coaching Camp. | `M-OWN-03-SlotCalendar` | Swipe between days; Tap any time slot to open Owner Action Bottom Sheet. |
| **O-04** | **Owner Slot Action Bottom Sheet** | Quick-action sheet for any selected slot: (1) Reserve for Walk-in Customer, (2) Block for Pitch Maintenance, (3) Allocate to Academy Coach, (4) Contact Booker. | `M-BS-SlotAction` (Bottom Sheet) | Single-tap actions with instant status badge update and confirmation toast. |
| **O-05** | **Turf Coach Vacancy & Academy Manager** | Post coaching vacancies for resident academy coaches (e.g. "Seeking Weekend Youth Football Coach, ৳15,000/mo + 20% clinic split"), review applicant certifications, hire coach. | `M-OWN-08-CoachRecruitment` | Tap `+ Post Coaching Vacancy` -> set requirements -> review applicant coach profiles. |
| **O-06** | **B2C Turf Pro-Shop & Concession Manager** | Manage on-site retail inventory: drinks, grip socks, rental bibs, sports tape, match balls; set prices, track stock levels, and view online pre-orders. | `M-OWN-09-ProShopManager` | Tap "Add Product", update stock count, toggle "Available for Addon Booking". |
| **O-07** | **Tournament Management Hub** | Tournament bracket overview, approving registered teams, assigning pitch slots to matches, and publishing tournament schedules. | `M-OWN-04-OwnerTournaments` | Tab switcher (Active / Pending / Draft); Tap team to approve entry. |
| **O-08** | **Live Score Entry & Scorer Tracker** | Mobile digital referee tool: digital score counters (`+1 Goal`), team VS badge, goal scorer selection sheet, yellow/red card assignment, and `Publish Final Score`. | `M-OWN-05-ScoreEntry` | Tap large `+` button to increment goal; Opens player bottom sheet to pick scorer. |
| **O-09** | **Revenue & Occupancy Analytics** | Visual mobile bar charts for slot revenue vs. pro-shop retail revenue vs. academy commission, peak hours (8 PM–11 PM), and daily cash ledger. | `M-OWN-06-OwnerReports` | Filter by Day / Week / Month; Export daily ledger as CSV/PDF. |
| **O-10** | **Owner Customer Inquiries & Chat** | Inbox of customer inquiries regarding slot bookings, coach bookings, pro-shop gear availability, and tournament queries with fast-reply message chips. | `M-OWN-07-OwnerChat` | Tap inquiry -> reply with pre-saved quick response ("Pitch 1 is open at 9 PM"). |

---

### Role 5: Platform Administrator
> **User Goal**: Supervise platform operations, verify Owner KYC and Coach certifications, moderate C2C/B2C marketplace listings, manage sports categories, and audit financial transactions.

| # | Feature Name | Feature Description | Figma Mobile Screen / Component | User Interaction / Gesture |
| :--- | :--- | :--- | :--- | :--- |
| **A-01** | **Admin Platform Command Hub** | Master platform metrics: Total GMV (`৳1,850,000`), Verified Turfs (`54`), Certified Coaches (`42`), Pending Approvals (`9`), Marketplace Items (`310`). | `M-ADM-01-Dashboard` | Metric KPI grid; Alert banner for pending KYC & Coach license submissions. |
| **A-02** | **KYC & Coach Certification Queue** | Verification queue with 2 tabs: (1) Turf Owner KYC (NID + Trade License) and (2) Coach Accreditation (AFC/FIFA Coaching Badges + Govt ID). | `M-ADM-02-ApprovalsQueue` | Swipe right to Approve; Tap card to inspect full security document viewer. |
| **A-03** | **Interactive Document Verification Modal** | High-fidelity zoomable Smart NID card preview (front/back with holographic chip), Municipal Councilor Certificate, and Coach AFC License verification. | `M-BS-KYCInspector` (Full-Screen Modal) | Pinch-to-zoom on documents; Tap "Approve & Verify" (Lime) or "Reject with Reason" (Red). |
| **A-04** | **Marketplace Moderation & Dispute Audit** | Moderate C2C peer-to-peer listings (flag counter, counterfeit checks, scam prevention), resolve buyer-seller disputes, hold/release escrow funds. | `M-ADM-07-MarketModeration` | Tap flagged listing -> review photos & chat history -> "Remove Listing" or "Dismiss". |
| **A-05** | **Platform Utilization Heatmaps & Analytics** | Hourly platform utilization heatmaps (intensity scale 0–4), sport popularity charts (Football 65%, Cricket 25%, Basketball 10%), revenue trends. | `M-ADM-03-Analytics` | Horizontal scrollable heatmap matrix; Filter by City/Region. |
| **A-06** | **Sports Categories & Equipment Manager** | Catalog of supported sports, field dimension specifications, recommended equipment checklists, and `+ Add Sport Category` modal. | `M-ADM-04-Categories` | Tap toggle to enable/disable sport platform-wide; Tap `+ Add Sport`. |
| **A-07** | **Financial Audit & Payout Approvals** | Comprehensive transaction ledger (Turf slots, Coach sessions, Marketplace C2C/B2C sales), SSLCOMMERZ gateway fees, owner/coach payout disbursements. | `M-ADM-05-FinancialReports` | Tap "Process Payout" -> confirmation modal; Filter by date range. |
| **A-08** | **System Broadcast & Push Notifications** | Push notification composer with audience targeting (All Users, Turf Owners, Coaches, Captains Only) and priority tagging (`Critical`, `Update`, `Maintenance`). | `M-ADM-06-Announcements` | Select target audience checkboxes; Tap "Send Broadcast" to fire instant push alert. |

---

## 3. TurfHub Sports Marketplace Architecture (C2C & B2C)

TurfHub features a hybrid **B2C & C2C Sports Marketplace** embedded directly into the turf ecosystem.

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                       TURFHUB SPORTS COMMERCE ECOSYSTEM                                         │
├────────────────────────────────────────────────────────┬─────────────────────────────────────────────────────────┤
│ 🏬 B2C PRO-SHOP (Turf Owner -> Players & Teams)       │ 🔄 C2C PEER-TO-PEER (Player <-> Player / Captain)       │
├────────────────────────────────────────────────────────┼─────────────────────────────────────────────────────────┤
│ • Official turf merchandise & pro-shop inventory       │ • Community buy & sell for pre-owned / surplus gear     │
│ • "Add-on to Turf Booking" 1-tap checkout              │ • Football boots, cricket bats, rackets, gloves, kits   │
│ • Energy drinks, hydration packs, grip socks, ice packs│ • "Meet at Verified Turf" secure in-person pickup       │
│ • Bulk team jerseys & tournament kit printing          │ • In-app offer bargaining & counter-offer chat          │
│ • Immediate counter pickup upon ground arrival         │ • Escrow digital payment or cash on meetup              │
└────────────────────────────────────────────────────────┴─────────────────────────────────────────────────────────┘
```

### Marketplace Core Mobile Feature Catalog

| # | Feature Name | Description | Figma Screen / Component | Mobile Interaction / Flow |
| :--- | :--- | :--- | :--- | :--- |
| **MKT-01** | **Marketplace Hub & Segmented Switcher** | Main mobile shopping feed with segmented control: `All Items`, `Turf Pro-Shops (B2C)`, `Community Deals (C2C)`. Search bar with category chips (Footwear, Apparel, Balls, Protection). | `M-MKT-01-MarketHome` | Tap tab switcher; Horizontal swipe category pills; Pull-to-refresh deals. |
| **MKT-02** | **Product Detail Card & Seller Verification** | High-res photo gallery, price tag in BDT (`৳2,400`), Condition badge (`Brand New`, `Like New`, `Good`), Seller card with "Verified Turf Owner 🏟️" or "Verified Athlete 🏃" badge, Pickup location map. | `M-MKT-02-ProductDetail` | Swipe product images; Tap "Make Offer" or "Buy Now" bottom bar. |
| **MKT-03** | **1-Tap C2C Listing Creator** | Camera snap / gallery upload (up to 5 photos), auto-category detection, condition selector, asking price, negotiable toggle, and preferred handover turf dropdown. | `M-MKT-03-CreateListing` (Multi-Step Sheet) | Snap photos -> fill title/price -> select local turf ground -> publish in 30 seconds. |
| **MKT-04** | **In-App Price Negotiation & Offer Engine** | Interactive offer sheet where buyers can propose a price (`৳1,500` instead of `৳1,800`). Seller receives instant push alert with 1-tap `Accept`, `Decline`, or `Counter-Offer`. | `M-BS-MakeOffer` & `M-PLY-08-ChatThread` | Tap "Make an Offer" -> slide amount -> triggers in-chat interactive offer card. |
| **MKT-05** | **"Add-on to Booking" Seamless Checkout** | When booking a turf slot, an interactive drawer presents turf refreshments and gear (e.g. 10x Gatorade + 2x Grip Socks) bundled directly into SSLCOMMERZ checkout. | `M-BS-TurfAddons` (Drawer in Booking Stepper) | Checkbox toggle addons -> dynamically updates total checkout summary. |
| **MKT-06** | **Turf Owner B2C Store Manager** | Mobile inventory manager for turf owners: add drinks, balls, jerseys; edit retail pricing; toggle active stock; view pending customer pickup orders. | `M-OWN-09-ProShopManager` | Tap product row to edit stock; Tap order to mark "Ready for Pickup". |
| **MKT-07** | **Marketplace Order Tracking & QR Pickup** | Order receipt with unique QR verification code. For B2C turf items or C2C turf meetups, turf counter staff or seller scans QR code to confirm safe handover. | `M-MKT-08-OrderReceipt` | Display brightness auto-boosts QR code for seamless scanning at turf counter. |

---

## 4. Inter-Role Collaboration Matrix (`Role <-> Role`)

This matrix maps how all 5 authenticated roles and guests interact and exchange state across TurfHub mobile workflows:

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                             INTER-ROLE INTERACTION WORKFLOW MATRIX                                                    │
├───────────────────┬──────────────────────────┬──────────────────────────┬──────────────────────────┬───────────────────────────────────┤
│ FROM \ TO         │ 🏃 PLAYER                │ 🏆 TEAM CAPTAIN          │ 🏅 CERTIFIED COACH       │ 🏟️ TURF OWNER / ADMIN             │
├───────────────────┼──────────────────────────┼──────────────────────────┼──────────────────────────┼───────────────────────────────────┤
│ 🏃 PLAYER         │ • C2C Gear trade/bargain │ • RSVP to match invites  │ • Book 1-on-1 coaching   │ • Owner: Book slot / buy drinks   │
│                   │ • Free agent chats       │ • Request to join squad  │ • Receive drill feedback │ • Admin: Submit support dispute   │
├───────────────────┼──────────────────────────┼──────────────────────────┼──────────────────────────┼───────────────────────────────────┤
│ 🏆 TEAM CAPTAIN   │ • Send squad invites     │ • Challenge captain (VS) │ • Hire team coach        │ • Owner: Book practice / cup entry│
│                   │ • Split invoices         │ • View league standings  │ • Assign match tactics   │ • Admin: Report score disputes    │
├───────────────────┼──────────────────────────┼──────────────────────────┼──────────────────────────┼──────────────────────────┼────────┤
│ 🏅 COACH/TRAINER  │ • Send training drills   │ • Submit tactical plan   │ • Peer coaching network  │ • Owner: Apply for turf vacancy   │
│                   │ • Log performance scores │ • Manage squad lineup    │ • Share training venues  │ • Admin: Submit license for badge │
├───────────────────┼──────────────────────────┼──────────────────────────┼──────────────────────────┼───────────────────────────────────┤
│ 🏟️ TURF OWNER     │ • Confirm slot & orders  │ • Approve cup entry      │ • Hire academy coach     │ • Admin: Submit KYC & Trade lic.  │
│                   │ • Handover B2C gear (QR) │ • Allocate match pitches │ • Host weekend clinics   │ • Admin: Request payout cash-out  │
├───────────────────┼──────────────────────────┼──────────────────────────┼──────────────────────────┼───────────────────────────────────┤
│ 🛡️ PLATFORM ADMIN │ • Moderate C2C listings  │ • Approve custom league  │ • Verify coaching license│ • Owner: Verify NID & Turf photos │
│                   │ • Push broadcast alert   │ • Enforce cup rules      │ • Disburse training funds│ • Owner: Disburse platform payouts│
└───────────────────┴──────────────────────────┴──────────────────────────┴──────────────────────────┴───────────────────────────────────┘
```

---

## 5. Mobile Navigation Architecture & Bottom Tab Bars

In Figma mobile design, each authenticated role has a dedicated **Bottom Navigation Bar (Height: 64px + 34px Safe Area)** with persistent primary access:

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                       ROLE BOTTOM NAVIGATION BARS                                        │
├──────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ 🏃 PLAYER:        [ 🏠 Home ]       [ 🔍 Explore ]     [ 🛍️ Market ]    [ ⚽ Fixtures ]   [ 👤 Profile ]    │
├──────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ 🏆 TEAM CAPTAIN:  [ 🏠 Captain ]    [ 👥 Squad ]       [ 📅 Book Turf ]  [ 🛍️ Shop/Kits ]  [ 💬 Messages ]   │
├──────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ 🏅 COACH/TRAINER: [ 🏠 Coach Hub ]  [ 📅 Schedule ]    [ 💼 Teams/Jobs ] [ 🏃 Trainees ]   [ 💬 Chat ]       │
├──────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ 🏟️ TURF OWNER:    [ 📊 Overview ]   [ 📅 Calendar ]    [ 🛍️ Pro-Shop ]   [ ⚽ Scores ]     [ 💬 Inquiries ]  │
├──────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ 🛡️ PLATFORM ADMIN:[ 📊 Dashboard ]  [ ✅ Approvals ]   [ 🛍️ Market Mod ] [ 📈 Analytics ]  [ 📢 Broadcast ]  │
└──────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### Top App Bar (Header Architecture)
- **Left**: Role Profile Avatar with Status Badge (`Online` Dot) or Contextual Back Button (`←`).
- **Center**: Screen Title (`Turf Detail`, `Marketplace`, `Coach Schedule`, `KYC Verification`).
- **Right**: Notification Bell with badge counter (`🔴 3`) + Role Switcher Icon / Cart Icon (`🛒 2`).

---

## 6. Master Figma Mobile Screen & Component Inventory (Frame Catalog)

Use this systematic naming convention when generating frames and components in Figma:

### 📱 1. Authentication & Onboarding Flow (`M-AUTH`)
- `M-AUTH-01`: Splash Screen with Stadium Background & TH Lime Logo.
- `M-AUTH-02`: Role Selection Carousel (Player, Captain, Coach, Turf Owner, Platform Admin).
- `M-AUTH-03`: Mobile Sign In Screen (Phone/Email + Password + OTP trigger).
- `M-AUTH-04`: Mobile Sign Up Screen (Role-specific onboarding fields).
- `M-AUTH-05`: Coach License & Certificate Upload Onboarding Screen.
- `M-AUTH-06`: OTP Verification Bottom Sheet (6-digit keypad input).

### 🏃 2. Player Experience Screens (`M-PLY`)
- `M-PLY-01`: Player Dashboard (Greeting, Match RSVP Banner, Performance Stats, Quick Actions).
- `M-PLY-02`: Master Turf Ground Explorer (Search Pill, Filter Chips, Master Venue Cards).
- `M-PLY-03`: Turf Venue Detail & Gallery (Image Slider, Amenities, Location Map Pin).
- `M-PLY-04`: Date & Time Slot Matrix Picker (Horizontal Date Strip + 2-Column Slot Chips).
- `M-PLY-05`: Match Fixtures & Live Center (Upcoming matches, Live score badges, Group tables).
- `M-PLY-06`: Free Agent & Team Finder (Browse open squad ads, Apply to team).
- `M-PLY-07`: Digital Receipts & Invoices (Payment ledger, QR code passes, PDF export).
- `M-PLY-08`: Player Chat Thread (1-on-1, Coach tactical chat, Offer negotiation card).
- `M-PLY-09`: Coach Finder & Booking Hub (Coach cards, Hourly rates, Session slot booking).

### 🏆 3. Captain Experience Screens (`M-CAP`)
- `M-CAP-01`: Captain Command Center (Countdown clock, Match status, Next opponent card).
- `M-CAP-02`: Squad Roster Management (Jersey number badges, Position chips, Bench/Active status).
- `M-CAP-03`: 4-Step Turf Booking Stepper (Step 1: Sport/Date → Step 2: Turf → Step 3: Slots & Addons → Step 4: Pay).
- `M-CAP-04`: Host Tournament Wizard (Multi-step form for creating leagues and prize pools).
- `M-CAP-05`: Tournament Directory & Team Entry (Browse cups, Submit squad lineup, Pay fee).
- `M-CAP-06`: League Standings & Match Scores (Form pills `W/D/L`, Goal Difference, Points).
- `M-CAP-07`: Captain Invoices & Cost Splitter (Per-player calculation & WhatsApp share trigger).
- `M-CAP-08`: Team Announcement Channel (Broadcast match details to all players).
- `M-CAP-09`: Hire Team Coach Wizard (Post coaching requirements & review coach applicants).

### 🏅 4. Coach Experience Screens (`M-COA`)
- `M-COA-01`: Coach Command Center (Daily schedule timeline, Active trainees, Monthly earnings).
- `M-COA-02`: Coach Profile & Certification Inspector (AFC licenses, Rates, Bio reel).
- `M-COA-03`: Team Job Board (Browse captain posts seeking tournament coaches, Submit proposals).
- `M-COA-04`: Turf Owner Vacancies & Pitch Residency (Apply for resident academy roles at turfs).
- `M-COA-05`: Training Session Creator & Calendar (Schedule group drills, Book turf ground slots).
- `M-COA-06`: Trainee Roster & Skill Scorecard (Player fitness radars, Tactical feedback notes).
- `M-COA-07`: Coach Earnings & Payout Ledger (Session fee breakdown, Payout withdrawal to bKash).
- `M-COA-08`: Coach Tactical Chat (Whiteboard diagrams, Video drill assignments).

### 🏟️ 5. Turf Owner Experience Screens (`M-OWN`)
- `M-OWN-01`: Turf Owner Operations Dashboard (Today's Bookings, Pro-Shop Revenue KPI, Occupancy).
- `M-OWN-02`: Pitch Catalog & Facilities Editor (Pitch cards, Pricing per hour, Amenity toggles).
- `M-OWN-03`: 7-Day / Daily Interactive Slot Matrix (Hourly slot grid with status colors & academy blocks).
- `M-OWN-04`: Tournament Operations Hub (Approve registered teams, Schedule bracket matches).
- `M-OWN-05`: Match Score Entry & Live Referee (Goal counters `+1`, Scorer picker, Card markers).
- `M-OWN-06`: Revenue & Peak Hour Analytics (Slot fees vs. Pro-Shop vs. Academy cuts).
- `M-OWN-07`: Customer Inquiries & Chat Inbox (Customer slot requests, Quick-reply pills).
- `M-OWN-08`: Coach Vacancy & Academy Manager (Post resident coach job, Review coach applicants).
- `M-OWN-09`: B2C Pro-Shop & Concession Manager (Add drinks/balls/gear, Edit prices, Track stock).

### 🛍️ 6. Marketplace Screens (`M-MKT`)
- `M-MKT-01`: Marketplace Main Feed (B2C & C2C toggle tabs, Category pills, Search, Trending gear).
- `M-MKT-02`: Product Detail Screen (Image carousel, Price, Verified seller badge, Location map).
- `M-MKT-03`: Create C2C Gear Listing Sheet (Photo snap, Condition tag, Price, Handover turf).
- `M-MKT-04`: My Marketplace Listings & Sales (Active listings, Pending offers, Sold items).
- `M-MKT-05`: Cart & Multi-Item Checkout Sheet (B2C + C2C items, MFS payment integration).
- `M-MKT-06`: In-App Bargain & Negotiation Thread (Interactive offer status card in chat).
- `M-MKT-07`: Team Bulk Uniform Shop (Custom jersey numbers & team kit bundle orders).
- `M-MKT-08`: Digital Order Receipt & QR Handover Pass (Scan-to-verify item exchange).

### 🛡️ 7. Platform Admin Screens (`M-ADM`)
- `M-ADM-01`: Admin Platform Command Hub (Total GMV, Active Turfs, System Health stat cards).
- `M-ADM-02`: KYC & Coach Verification Queue (List of pending ground owners and coaches).
- `M-ADM-03`: KYC Document Inspector Modal (Zoomable Smart NID card, Sealed Certificate, Coach Badges).
- `M-ADM-04`: Platform Utilization Heatmap (Hourly density matrix across sports and districts).
- `M-ADM-05`: Sport Categories & Equipment Manager (Category cards, Dimensions, Gear lists).
- `M-ADM-06`: Financial Audit & Payout Approvals (Platform fees, Owner/Coach withdrawal requests).
- `M-ADM-07`: Marketplace Moderation & Dispute Hub (Flagged listings, Escrow hold/release).
- `M-ADM-08`: System Push Notification Broadcast (Composer, Audience filters, Priority badges).

### 🧩 8. Reusable Mobile Bottom Sheets & Modals (`M-BS`)
- `M-BS-SearchFilter`: Filter by Sport, Price Range Slider, Location District, Amenities.
- `M-BS-PaymentCheckout`: SSLCOMMERZ Mobile Banking (bKash/Nagad/Cards) with 1-tap OTP.
- `M-BS-TurfAddons`: Pre-order drinks & gear drawer inside turf booking flow.
- `M-BS-SlotAction`: Owner slot modal (Reserve for Walk-in, Block for Maintenance, Academy block).
- `M-BS-InvitePlayer`: Captain share sheet (Copy invite link, WhatsApp trigger, QR code).
- `M-BS-CoachBooking`: 1-on-1 coaching session time picker & fee summary.
- `M-BS-MakeOffer`: In-app price bargaining input sheet with counter-offer preview.
- `M-BS-ScorerSelect`: Referee sheet to select goal scorer and assist provider from team roster.
- `M-BS-SuccessReceipt`: Booking confirmation overlay with green tick animation and Booking ID.

---

## 7. Interactive Mobile Prototype Flows & Gestures

When setting up Figma Interactive Prototype Connections, configure the following transitions:

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                    FIGMA PROTOTYPE INTERACTION FLOWS                                   │
├──────────────────────────┬─────────────────────────────┬──────────────────┬────────────────────────────┤
│ Trigger Action           │ Source Frame                │ Animation Type   │ Destination Frame          │
├──────────────────────────┼─────────────────────────────┼──────────────────┼────────────────────────────┤
│ Tap "Sign In"            │ `M-AUTH-01-RoleSelect`      │ Smart Animate    │ `M-AUTH-03-SignIn`         │
│ Submit Sign In           │ `M-AUTH-03-SignIn`          │ Push (Left)      │ Role Dashboard (`M-*-01`)  │
│ Tap Search Bar           │ `M-PLY-02-TurfExplore`      │ Slide Up (300ms) │ `M-BS-SearchFilter`        │
│ Tap Turf Venue Card      │ `M-PLY-02-TurfExplore`      │ Push (Left)      │ `M-PLY-03-TurfDetail`      │
│ Tap Time Slot Chip       │ `M-PLY-04-SlotPicker`       │ Instant State    │ Toggle Active Lime State   │
│ Tap "Add Drinks/Gear"    │ `M-CAP-03-BookStepper`      │ Slide Up (300ms) │ `M-BS-TurfAddons`          │
│ Tap "Book Now"           │ `M-PLY-03-TurfDetail`       │ Slide Up (350ms) │ `M-BS-PaymentCheckout`     │
│ Tap "Pay with bKash"     │ `M-BS-PaymentCheckout`      │ Smart Animate    │ `M-BS-SuccessReceipt`      │
│ Tap `+ Sell Gear` FAB    │ `M-MKT-01-MarketHome`       │ Slide Up (350ms) │ `M-MKT-03-CreateListing`   │
│ Tap "Make an Offer"      │ `M-MKT-02-ProductDetail`    │ Slide Up (250ms) │ `M-BS-MakeOffer`           │
│ Tap "Book Coach"         │ `M-PLY-09-CoachFinder`      │ Slide Up (300ms) │ `M-BS-CoachBooking`        │
│ Tap "Apply for Vacancy"  │ `M-COA-04-TurfVacancies`    │ Slide Up (300ms) │ Coach Proposal Sheet       │
│ Tap Slot in Matrix       │ `M-OWN-03-SlotCalendar`     │ Slide Up (250ms) │ `M-BS-SlotAction`          │
│ Tap "Inspect KYC/Badges" │ `M-ADM-02-ApprovalsQueue`   │ Slide Up (300ms) │ `M-BS-KYCInspector`        │
│ Tap Goal `+` Counter     │ `M-OWN-05-ScoreEntry`       │ Spring Animate   │ Number increment + Modal   │
│ Swipe Left on Player     │ `M-CAP-02-ManageTeam`       │ Interactive Drag │ Reveals "Bench / Remove"   │
└──────────────────────────┴─────────────────────────────┴──────────────────┴────────────────────────────┘
```

---

## 🎯 Summary Checklist for Figma Designers
- [ ] **Styles**: Import color tokens (`#0d2818` Forest, `#7ed321` Lime, `#f59e0b` Gold, `#8b5cf6` Purple, `#0d9488` Teal) into Figma Variables.
- [ ] **Components**: Build Master Component Sets for 5 Role Bottom Navigation Bars, Stat Cards, Slot Chips, Product Cards, Offer Status Bubbles, and Document Inspector Modals.
- [ ] **Frames**: Set up Mobile Frames at `393 x 852 px` using Auto-Layout with `16px` horizontal padding.
- [ ] **Coach & Market Flows**: Ensure dedicated frames for Coach Schedules (`M-COA-*`), Vacancy Application flows, and Marketplace feeds (`M-MKT-*`).
- [ ] **Touch Usability**: Maintain all buttons, offer chips, and interactive slot matrix cells at minimum `44px` height with `8px` spacing.
- [ ] **Prototype Links**: Wire up Bottom Navigation items, bottom sheets (`Open Overlay` -> `Bottom Center`), and interactive role switching.
