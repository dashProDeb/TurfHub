# TurfHub Codebase Structure & CSS Architecture Explainer

## 📌 Executive Summary
TurfHub uses a **hybrid architecture** combining a **Global Custom CSS Design System (`style.css` + page-level scoped `<style>`)** and **Tailwind CSS Utility Classes**. 

This document explains the division of responsibilities across the entire static codebase, outlining **where raw CSS is used**, **where Tailwind is used**, the **architectural rationale (why)** for each page, and the **state management / payment workflow integration** powering the platform.

---

## 🏗️ Global Architectural Strategy: Why Raw CSS vs. Tailwind CSS?

| Layer | Technology | Primary Purpose | Why This Choice? |
| :--- | :--- | :--- | :--- |
| **Global Design System & Shell** | **Raw CSS (`style.css`)** | Layout shell (`#app-wrapper`, `#main-content`, `#topbar`, `.sidebar`, `.page-body`), typography tokens, button variants, badges, and stat cards. | Provides guaranteed pixel-perfect layout stability, fixed sticky desktop sidebars, center-aligned body containers (`max-width: 1240px; margin: 0 auto`), and reusable role-based color variables (`--color-forest`, `--color-lime`) without framework lock-in or build-step dependencies. |
| **Bespoke Domain Components** | **Raw Scoped CSS (`<style>`)** | 7-day hourly booking matrices, SSLCOMMERZ dummy gateway modals, Smart NID cards, Ward Councilor certificates, interactive chat bubbles, match scoreboards, and league standings tables. | Complex custom styling (guilloché security patterns, certificate borders, SSLCOMMERZ checkout windows, dynamic slot matrix states, pulsing animations) requires precise multi-selector rules and custom animations difficult to express with pure utility classes. |
| **Layout & Micro-Utilities** | **Tailwind CSS (CDN)** | Flexbox alignment, spacing (`gap-*`, `p-*`, `m-*`), responsive grids (`grid-cols-*`), text sizing/weights, and rapid UI positioning. | Accelerates UI development, ensures fluid responsive behavior across viewports, and avoids boilerplate CSS for mundane spacing and typography rules. |

---

## 📄 Complete Page-by-Page Breakdown

### 1. Landing & Authentication Pages

#### 🏠 `index.html` (Public Landing Page)
- **Raw CSS Used:**
  - Hero stadium background overlay with dual linear/radial forest gradients and glassmorphism.
  - Multi-parameter search pill box with custom border focus glow (`rgba(126,211,33,0.18)`).
  - Floating keyframe badge animations (`@keyframes pulse-dot`).
- **Tailwind CSS Used:**
  - Responsive flex navbar (`flex items-center justify-between px-6 py-4`).
  - Feature & turf grid systems (`grid grid-cols-1 md:grid-cols-3 gap-6`).
  - Typography scale (`text-4xl md:text-6xl font-black`).
- **Why in short:** Tailwind structures the page layout and typography quickly, while raw CSS provides the hero visual depth and stadium lighting aesthetics.

#### 🔐 `login.html` (Role-Based Authentication Switcher)
- **Raw CSS Used:**
  - Split-screen hero panel background grid (`.hero-bg` with 50px SVG grid pattern).
  - Interactive role selection cards (`.role-card.selected` with custom lime outline and glow).
  - Animated toggle switch pills (`.tab-btn.active`).
- **Tailwind CSS Used:**
  - Form layout spacing and responsive column stacking.
- **Why in short:** The role cards and authentication tabs require instant visual feedback and tailored selection borders engineered in raw CSS.

#### 📞 `contact.html` (Customer Support & Inquiries)
- **Raw CSS Used:**
  - Root CSS variable tokens (`:root` colors).
- **Tailwind CSS Used:**
  - 2-column contact grid, form input wrappers, and FAQ accordion cards.
- **Why in short:** Standard responsive content page where Tailwind utilities maximize maintainability.

---

### 2. Platform Admin Pages

#### 📊 `admin-dashboard.html` (Platform Overview)
- **Raw CSS Used:**
  - Inlined standalone Admin sidebar navigation (`.sidebar`, `.sidebar-nav-item.active`).
  - Unified stat card grid (`.stat-grid`, `.stat-card`).
  - Content containers (`.content-panel`, `.panel-header`).
- **Tailwind CSS Used:**
  - Metric row alignment and quick action link wrappers.
- **Why in short:** Uses the core design system for consistent admin sidebar layout and stat widgets.

#### ✅ `admin-approvals.html` (Approvals & KYC Verification Dialog)
- **Raw CSS Used:**
  - **KYC Verification Dialog Modal (`.kyc-modal-overlay`, `.kyc-modal-container`):** Backdrop glassmorphic blur and entrance animations.
  - **Smart NID Card (`.nid-card-frame`, `.nid-emblem`, `.nid-chip`, `.nid-photo-box`):** Authentic Government of Bangladesh smart card styling, holographic chip, and MRZ barcode styling.
  - **Character Certificate (`.cert-frame`, `.cert-seal`):** Formal municipal certificate parchment border with councilor rubber stamp.
- **Tailwind CSS Used:**
  - Document tab headers, approval queue list items, and checklist flex layouts.
- **Why in short:** Complex document visual fidelity (NID cards and sealed certificates) requires advanced CSS gradients, pseudo-elements, and SVG embellishments.

#### 📈 `admin-analytics.html` (Platform Analytics & Heatmaps)
- **Raw CSS Used:**
  - Hourly utilization heatmap matrix (`.heatmap-cell`, `.heat-0` through `.heat-4` color intensity levels).
  - CSS flex-column revenue bar charts (`.chart-bar`, `.chart-bar-fill`).
- **Tailwind CSS Used:**
  - Grid wrappers, metric cards, and filter controls.
- **Why in short:** Visual data charts and heatmaps require precise dynamic height calculations and color interpolation best managed in CSS.

#### 🏷️ `admin-categories.html` (Sport Categories Management)
- **Raw CSS Used:**
  - Sport catalog item cards, equipment tags, and "Add Sport Category" modal overlay.
- **Tailwind CSS Used:**
  - Action button rows, input grids, and modal forms.
- **Why in short:** Consistent modal dialog layout and category grid cards.

#### 📄 `admin-reports.html` (Platform Financial Reports)
- **Raw CSS Used:**
  - Financial summary table (`.admin-table`), status badges (`.badge`), and period selector pills.
- **Tailwind CSS Used:**
  - Revenue metrics, export button groups, and date filters.
- **Why in short:** Clean table layouts with custom table header styles and print/export buttons.

#### 📢 `admin-announcements.html` (System Broadcasts)
- **Raw CSS Used:**
  - Announcement history cards, priority pill badges (`Critical`, `Update`, `Maintenance`), and draft composer panel.
- **Tailwind CSS Used:**
  - Target audience selector checkboxes and compose form controls.
- **Why in short:** Sidebar shell integration and alert-card styling.

---

### 3. Turf Owner Pages

#### 🏟️ `owner-dashboard.html` (Turf Owner Overview)
- **Raw CSS Used:**
  - Inlined standalone Owner sidebar (`owner-dashboard.html` active).
  - Quick action rows (`.quick-action`) and activity feed dots (`.activity-dot`).
- **Tailwind CSS Used:**
  - Recent bookings list and KPI stat card arrangement.
- **Why in short:** Maintains 100% UI consistency with the global TurfHub owner dashboard prototype.

#### ⚙️ `manage-turf.html` (Turf Catalog & Facilities)
- **Raw CSS Used:**
  - Pitch cards with outdoor/indoor tags, facility chips, and Add Turf modal popup.
- **Tailwind CSS Used:**
  - Form grid layouts, upload photo dropzones, and toggle switches.
- **Why in short:** Custom image card banners and popup modal overlay.

#### 📅 `slot-calendar.html` (Slot Calendar & Booking Matrix)
- **Raw CSS Used:**
  - **7-Day Hourly Matrix (`.matrix-grid`, `.slot-cell`):** Fixed hour column with 7-day responsive grid.
  - **Slot State Rules:** `.slot-available` (lime hover), `.slot-booked` (soft red + team pill), `.slot-maintenance` (slate diagonal stripes), and `.slot-selected`.
  - Details popup modal (`#slot-modal`).
- **Tailwind CSS Used:**
  - Date navigator bar (`← Prev Week / Next Week →`), turf select dropdown, and request tabs.
- **Why in short:** The interactive multi-state slot grid requires exact CSS grid dimensioning and status background rules.

#### ⚽ `score-entry.html` (Match Scores & Scorer Tracker)
- **Raw CSS Used:**
  - Large digital match score counters (`.score-input`), team VS separator, and goal increment buttons (`.goal-btn`).
- **Tailwind CSS Used:**
  - 2-column home/away squad grid, tournament dropdowns, and match result cards.
- **Why in short:** Customized score inputs and goal counter buttons ensure prominent tournament score entry.

#### 📈 `owner-reports.html` (Revenue & Occupancy Analytics)
- **Raw CSS Used:**
  - Vertical CSS percentage bars (`.mini-bar`, `.mini-bar-fill`) and peak hour progress bars.
- **Tailwind CSS Used:**
  - Summary KPI stat cards and top customer breakdown lists.
- **Why in short:** Pure CSS bar visualizations for instant load without heavy third-party chart libraries.

#### 💬 `owner-chat.html` (Owner Messages & Customer Inquiries)
- **Raw CSS Used:**
  - Split conversation layout with unread indicators and tailored message bubble geometry.
- **Tailwind CSS Used:**
  - Search customer bar, message compose input, and timestamp alignment.
- **Why in short:** Dedicated messaging viewport for ground owners responding to slot and tournament queries.

---

### 4. Team Captain Pages

#### 🏆 `captain-dashboard.html` (Captain Team Hub)
- **Raw CSS Used:**
  - Standalone Captain sidebar (`captain-dashboard.html` active).
  - Player squad numbered avatars (`.player-num`) and match countdown cards.
- **Tailwind CSS Used:**
  - Roster table, stat cards, and upcoming league match cards.
- **Why in short:** Compact roster layout with custom numbered squad circles.

#### 👥 `manage-team.html` (Captain Squad & Team Management)
- **Raw CSS Used:**
  - Squad player roster cards with jersey number badges, position pills, and status tags (`Active`, `Bench`, `Injured`).
  - Invite player modal overlay and role delegation toggles.
- **Tailwind CSS Used:**
  - Search players input, squad stat cards, and action buttons.
- **Why in short:** Specialized squad roster management interface with custom player cards and jersey badges.

#### ⚽ `book-turf.html` (Interactive 4-Step Turf Booking Hub)
- **Raw CSS Used:**
  - **4-Step Stepper Component (`.stepper-wrap`, `.step.active`, `.step.done`, `.step.todo`, `.step-line.done`):** Horizontally centered step progress tracker.
  - **Centered Grid Layout (`.turf-booking-container`, `.turf-grid`):** Evenly spaced responsive turf cards (`repeat(auto-fit, minmax(310px, 1fr))`) with hover elevation and flex-aligned footers.
  - **Interactive Slot Matrix (`.slot-pill.available`, `.slot-pill.selected`, `.slot-pill.booked`):** Live calendar slot selection with dynamic price calculation.
  - **SSLCOMMERZ Checkout Modal (`.ssl-modal-overlay`, `.ssl-window`, `.ssl-btn-pay`):** Mobile banking (bKash/Nagad/Rocket/Upay), card checkout, spinner animation, and receipt auto-forwarding.
- **Tailwind CSS Used:**
  - Sport filter pills, amenity chips, and specification grid wrappers.
- **Why in short:** Provides an end-to-end booking flow from venue discovery to SSLCOMMERZ dummy payment execution for team captains.

#### ➕ `create-tournament.html` (Host Tournament Wizard)
- **Raw CSS Used:**
  - Tournament preview card with progress registration fill bar (`.progress-fill`).
  - Standardized form inputs with lime focus ring (`.form-input`).
- **Tailwind CSS Used:**
  - 2-column form grid (Organizer Form vs. Active Tournaments List).
- **Why in short:** Side-by-side management layout and tournament progress meters.

#### 📋 `fixtures.html` (Match Fixtures & League Standings)
- **Raw CSS Used:**
  - **Live Banner:** Glowing animated live indicator (`@keyframes pulse-dot`).
  - **Standings Table (`.standings-table`):** Dark forest header, rank medal badges (`.rank-1`, `.rank-2`), and form badges (`.form-w`, `.form-l`, `.form-d`).
  - Fixture match cards with score displays.
- **Tailwind CSS Used:**
  - View switcher pills (Fixtures vs. Standings) and tournament tabs.
- **Why in short:** The sports standings table and match center require strict table typography and colored form pill badges.

#### 🧾 `booking-receipt.html` (Captain Booking Receipts & Invoices)
- **Raw CSS Used:**
  - Center-aligned layout container (`max-width: 980px; margin: 0 auto`).
  - Transaction receipt cards with PDF download action bar (`.receipt-card`, `.receipt-foot`).
  - Dynamic payment confirmation banner (`#payment-success-banner`) rendered from `localStorage`.
  - Status filter pills (`.filter-tab.active`).
- **Tailwind CSS Used:**
  - Price typography, date badge alignment, and filter bar flexbox.
  - Real-time client-side receipt injection engine (`loadRecentBookings()`).
- **Why in short:** Clean receipt invoice cards matching print/download design tokens with dynamic SSLCOMMERZ confirmation rendering.

#### 💬 `chat.html` (Messaging & Team Chat)
- **Raw CSS Used:**
  - Split chat shell (`.chat-shell` with 280px left list and fluid right chat).
  - Speech bubbles (`.msg-bubble.mine` with dark forest green and `.msg-bubble.them` in white).
  - Avatar badge circles and unread status dots (`.unread-dot`).
- **Tailwind CSS Used:**
  - Input bar container and message timestamp typography.
- **Why in short:** Fixed full-height messaging interface with custom scrollbars and chat bubble geometry.

#### 🏆 `tournament-registration.html` (Tournament Directory)
- **Raw CSS Used:**
  - Tournament card with dark forest hero header, entry fee vs prize pool boxes (`.prize-box`), and registration progress bar.
- **Tailwind CSS Used:**
  - Multi-sport filter pill row and card grid.
- **Why in short:** High-impact tournament prize cards with custom progress indicators.

---

### 5. Player Pages

#### 🏃 `player-dashboard.html` (Player Hub & RSVP)
- **Raw CSS Used:**
  - Calendar date block badges (`.fix-date`, `.fix-date-day`, `.fix-date-month`).
  - Availability toggle buttons (`.avail-btn.yes`, `.avail-btn.no`).
- **Tailwind CSS Used:**
  - Player performance metrics (Matches, Goals, Rating) and upcoming match cards.
- **Why in short:** Unique date-block typography and RSVP interactive toggle buttons.

#### 🔍 `turf-detail.html` (Find Turfs & Master-Detail Explorer)
- **Raw CSS Used:**
  - **Master-Detail Navigation (`.turf-nav-card.active`):** Left-hand turf cards with live selection indicators.
  - **Hero Gallery:** Thumbnail preview bar with active green border (`.gallery-thumb.active`).
  - **Interactive Slot Selector (`.slot-pill.available`, `.slot-pill.selected`, `.slot-pill.booked`):** Clickable time pills.
  - **SSLCOMMERZ Dummy Payment Gateway (`#ssl-modal`):** Full gateway modal with MFS (bKash/Nagad/Rocket/Upay with demo autofill), Cards, and Net Banking simulation.
- **Tailwind CSS Used:**
  - Top search bar, location dropdown, amenity chips, and customer review cards.
- **Why in short:** Complex split-view master-detail ground explorer with interactive slot booking, gallery switching, and integrated SSLCOMMERZ checkout flow.

#### 📅 `player-fixtures.html` (Player Match Schedule & Tournament Fixtures)
- **Raw CSS Used:**
  - Match cards with live stadium badges, VS score display, and attendance status.
  - Standings table summary with form pills (`W`, `D`, `L`).
- **Tailwind CSS Used:**
  - Filter pills by league/tournament, date headers, and match detail grids.
- **Why in short:** Dedicated fixture schedule tailored for individual players tracking upcoming games.

#### 🧾 `player-receipts.html` (Player Receipts & Payment History)
- **Raw CSS Used:**
  - Center-aligned layout container (`max-width: 980px; margin: 0 auto`).
  - Summary KPI strip (`.summary-strip`, `.summary-card`) displaying total bookings and total spent.
  - Dynamic receipt injection engine reading confirmed transactions from `localStorage`.
- **Tailwind CSS Used:**
  - Status filter tabs (`All`, `Confirmed`, `Pending`, `Cancelled`) and receipt cards.
- **Why in short:** Gives players complete visibility over their booking receipts and invoices.

#### 👥 `join-team.html` (Free Agent & Team Recruitment)
- **Raw CSS Used:**
  - Team recruitment cards with squad size status pills (`.open-tag`, `.full-tag`) and team badge icons.
- **Tailwind CSS Used:**
  - Filter tabs, search bar, and join request button.
- **Why in short:** Standardized team cards with custom capacity tags.

#### 💬 `player-chat.html` (Player Direct & Team Messaging)
- **Raw CSS Used:**
  - Full-height messaging shell with distinct green/white chat bubble hierarchy.
- **Tailwind CSS Used:**
  - Message input form and participant list alignment.
- **Why in short:** Standalone chat interface for players communicating with team captains and ground hosts.

---

## 💳 Payment Gateway Architecture (SSLCOMMERZ Integration)

TurfHub incorporates a client-side **SSLCOMMERZ EasyCheckout Dummy Gateway** integrated across both **Player** (`turf-detail.html`) and **Captain** (`book-turf.html`) workflows.

### Gateway Components:
1. **Security & Header Strip**:
   - 256-Bit SSL Encryption badge and dynamic merchant identification (`Merchant: TurfHub Bangladesh Ltd.`).
2. **Multi-Channel Payment Tabs**:
   - **📱 Mobile Financial Services (MFS)**: bKash (`#d12053`), Nagad (`#f7941d`), Rocket (`#8c3494`), Upay (`#00a2e8`) with one-click **Demo Autofill**.
   - **💳 Credit / Debit Cards**: Visa, Mastercard, AMEX with card number, MM/YY expiry, and CVV validation formatting.
   - **🏦 Net Banking**: City Touch, BRAC Bank Astha, Islami Bank CellFin, DBBL NexusPay.
3. **Handshake & Verification Simulation**:
   - Multi-phase animation: `Connecting to Gateway...` &rarr; `Authenticating OTP & Wallet PIN...` &rarr; `Payment Authorized & Confirmed!` with animated green checkmark.
4. **Transaction Persistence & Redirection**:
   - Automatically writes transaction metadata to `localStorage.setItem('turfhub_recent_booking', ...)`:
     ```json
     {
       "turfName": "The Green Arena",
       "turfLocation": "Gulshan, Dhaka",
       "sport": "Football",
       "date": "Sat, 26 Jul 2025",
       "time": "6:00 PM – 7:00 PM",
       "price": 800,
       "paymentMethod": "bKash (SSLCOMMERZ)",
       "trxId": "SSL-TH-984210",
       "refId": "TH-2025-0726-881",
       "status": "confirmed"
     }
     ```
   - Redirects to `booking-receipt.html` (Captain) or `player-receipts.html` (Player) where dynamic confirmed cards are prepended to the invoice list.

---

## 📊 Summary Architecture Matrix

```
┌──────────────────────────────────────────────────────────────────────────┐
│                             TURFHUB FRONTEND                             │
├──────────────────────────────────────────────────────────────────────────┤
│ 1. Core Shell & Design System: style.css (Raw CSS)                       │
│    ├── #app-wrapper, #main-content, #topbar, .page-body (Centered)       │
│    ├── .sidebar, .sidebar-nav-item, .sidebar-user, .sidebar-logo         │
│    └── Color Tokens: --color-forest (#0d2818), --color-lime (#7ed321)    │
├──────────────────────────────────────────────────────────────────────────┤
│ 2. Scoped Custom Components: <style> per Page (Raw CSS)                  │
│    ├── SSLCOMMERZ EasyCheckout Window (turf-detail.html, book-turf.html) │
│    ├── Slot Matrix Grid & Dynamic States (slot-calendar.html)            │
│    ├── 4-Step Stepper & Centered Grid (book-turf.html)                   │
│    ├── NID Cards (Part 1/2) & Ward Certificates (admin-approvals.html)   │
│    ├── Standings Table & Form Badges (fixtures.html, player-fixtures)    │
│    ├── Dynamic Receipt Invoices (booking-receipt.html, player-receipts)  │
│    └── Chat Shell & Message Bubbles (chat.html, player/owner-chat)       │
├──────────────────────────────────────────────────────────────────────────┤
│ 3. Atomic Utilities: Tailwind CSS (Browser Runtime)                      │
│    ├── Responsive Grids (grid-cols-1, md:grid-cols-3, gap-6)             │
│    ├── Flexbox Utilities (flex, items-center, justify-between)           │
│    └── Fluid Spacing & Typography (text-sm, font-black, p-4, m-2)        │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## ⚡ JavaScript Architecture & UI Impact Explainer

TurfHub is designed as a **lightweight, zero-build-step client-side web application**. The JavaScript files (`.js`) and embedded page scripts do **not** run backend database operations; instead, they serve as the **dynamic UI presentation layer, DOM engine, and client-side state manager**.

---

### 1. 🔑 `auth.js` — Role-Based Session & UI Manifest Manager

#### 🎯 Primary Purpose
Simulates multi-role client-side authentication and controls role-driven UI visibility across the platform using the browser's `localStorage` (`"turfhub_role"`).

#### 🧩 Code Sections & Direct UI Impact

| Code Section / Function | What It Does in Code | Direct UI Impact |
| :--- | :--- | :--- |
| `ROLE_CONFIG` (Object, Lines 10–76) | Manifest defining UI metadata for each role: `owner`, `captain`, `player`, `admin`. | Controls the **Avatar Initial** (e.g. `'R'`, `'T'`, `'P'`, `'A'`), **User Full Name** (e.g. `Rafiqul Islam`, `Tanvir Ahmed`, `Sabbir Ahmed`), **Role Subtitle** (`Turf Owner`, `Team Captain`, `Player`), **Theme Color**, **Default Dashboard URL**, and the exact list of **Sidebar Navigation Items** rendered for each user role. |
| `setRole(role)` | Saves the active role string into `localStorage.setItem('turfhub_role', role)`. | Triggered when a user clicks a role card on the login screen, determining which identity is active across all subsequent screens. |
| `getRole()` | Reads `localStorage.getItem('turfhub_role')`. | Used on page load by all dashboards and layouts to know whether to render Captain, Owner, Player, or Admin interfaces. |
| `clearRole()` / `signOut()` | Clears `localStorage` keys and redirects to `login.html`. | Provides working **Sign Out** functionality from the sidebar and returns user to the login screen. |
| `getRoleConfig(role)` | Returns the configuration slice matching the active role (defaults to `player`). | Feeds the dynamic sidebar generator in `layout.js` with correct labels and links. |
| `redirectByRole()` | Reads current role and executes `window.location.href = cfg.dashboard`. | Auto-redirects users to their designated dashboard (`owner-dashboard.html`, `captain-dashboard.html`, `player-dashboard.html`, or `admin-dashboard.html`). |
| `requireRole(...allowedRoles)` | Role authorization gatekeeper. | If the active role does not match the page's permitted roles (or no user is logged in), it immediately kicks the user back to `login.html`, preventing unauthorized UI viewing. |

---

### 2. 🖥️ `layout.js` — Deep Architecture & Codebase Role Analysis

#### 🎯 Primary Purpose & Operational Role
`layout.js` is TurfHub’s **centralized client-side shell engine and layout generator**. It was engineered to solve two fundamental problems in zero-build static web applications:
1. **DRY (Don't Repeat Yourself) Navigation**: Eliminates the need to duplicate complex sidebar markup, logo headers, user profile cards, and navigation links across dozens of HTML pages.
2. **Self-Contained Style Delivery**: Ensures that all shell styling, typography tokens, button variants, stat cards, and mobile responsive rules are bundled and injected automatically at runtime.

---

#### 🧩 Comprehensive Code Structure & Functionality Breakdown

| Component / Function | Code Location in `layout.js` | Detailed Operational Mechanism | UI & Browser Impact |
| :--- | :--- | :--- | :--- |
| **`injectLayoutCSS()`** (IIFE) | Lines 79–404 | Immediately-Invoked Function Expression that creates a `<style id="layout-css">` element and injects 325+ lines of raw design-system CSS directly into `document.head`. | Injects the complete global styling rules: `#app-wrapper` flex container, fixed sticky sidebar (`width: 220px`), `#topbar` header bar, `.stat-grid` & `.stat-card` widgets, button variants (`.btn-lime`, `.btn-outline`), status badges (`.badge-live`, `.badge-confirmed`), and mobile off-canvas drawer media queries (`@media (max-width: 900px)`). |
| **`renderSidebar(activeId)`** | Lines 7–66 | Queries `getRole()` and `getRoleConfig(role)` from `auth.js`. Maps through the role’s `nav` array, comparing `item.id === activeId` to assign the `.active` CSS class. Assembles the HTML markup for `<aside id="sidebar">` and prepends it to `#app-wrapper`. Also creates `#sidebar-toggle` (hamburger icon) and prepends it to `#topbar`. | **Dynamically generates the complete visual sidebar**: <br>• **Brand Logo**: Displays `TH` icon and "TurfHub" title linking to `index.html`.<br>• **User Card**: Shows role avatar initial (`T`, `R`, `P`, `A`), name, and role subtitle.<br>• **Navigation Links**: Renders role-specific routes with active green pill highlight.<br>• **Sign Out Button**: Binds to `signOut()` in `auth.js`.<br>• **Mobile Drawer Controls**: Injects `#sidebar-overlay` backdrop and `#sidebar-toggle` hamburger button. |
| **`toggleSidebar()`** | Lines 68–71 | Toggles the `.open` class on `#sidebar` and `.show` class on `#sidebar-overlay`. | Toggles the sliding off-canvas drawer on mobile viewports (< 900px) when the hamburger button is clicked. |
| **`closeSidebar()`** | Lines 73–76 | Removes `.open` from `#sidebar` and `.show` from `#sidebar-overlay`. | Closes the mobile navigation drawer when the user taps outside the sidebar on the backdrop overlay. |

---

#### ⚖️ The Relationship Between `layout.js` and `style.css` (Dual-Mode Architecture)

Across the TurfHub repository, you will observe two complementary layout strategies:

1. **The Static Detached Strategy (`style.css` + Inlined HTML Sidebars)**:
   - To provide **instant rendering without Cumulative Layout Shift (CLS)** and allow previewing individual `.html` files without running scripts, all 27+ HTML pages include statically inlined `<aside class="sidebar">` markup.
   - The CSS rules defined in `style.css` are the **static extraction** of the styles originally generated by `injectLayoutCSS()` inside `layout.js`.

2. **The Dynamic Programmatic Strategy (`layout.js` + `auth.js`)**:
   - `layout.js` serves as the **canonical architectural blueprint and runtime engine**.
   - If a new page is created with only `<div id="app-wrapper"><div id="main-content">...</div></div>`, simply including `<script src="auth.js"></script><script src="layout.js"></script><script>renderSidebar('pageId');</script>` will **automatically build, style, and bind the entire sidebar, topbar, mobile drawer, and authorization check** with zero boilerplate HTML.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        LAYOUT.JS SYSTEM DIAGRAM                        │
├────────────────────────────────────────────────────────────────────────┤
│                                                                        │
│   auth.js (Role Manifest)                                              │
│       │                                                                │
│       ▼                                                                │
│   layout.js ──► 1. injectLayoutCSS() ──► Injects CSS into <head>       │
│       │                                                                │
│       ├──► 2. renderSidebar()   ──► Injects <aside id="sidebar"> into  │
│       │                             #app-wrapper & toggle in #topbar   │
│       │                                                                │
│       └──► 3. toggleSidebar()   ──► Controls Mobile Drawer (<900px)    │
│                                                                        │
└────────────────────────────────────────────────────────────────────────┘
```

---

### 3. 🖱️ Page-Level Frontend JavaScript (`<script>` in HTML)

| Page (`.html`) | Script Code Part | Direct UI Impact |
| :--- | :--- | :--- |
| **`login.html`** | Role Card Click Listeners & Tab Switcher (`tab-btn`) | Highlights selected role card (`.role-card.selected`) with green border glow, switches between Login and Register form tabs, and updates `turfhub_role` in `localStorage`. |
| **`index.html`** | Mobile Menu Toggle & Scroll Listener | Expands/collapses mobile navigation dropdown and applies drop shadow to sticky navbar on scroll (`window.scrollY > 20`). |
| **`slot-calendar.html`** | Slot Matrix Selector & Owner Reserve Modal Engine | Allows clicking available slots to toggle `.slot-selected`, opens the **Owner Slot Reservation Modal**, handles date navigation, and dynamically updates slot states (`Available` &rarr; `Reserved by Owner` / `Booked`). |
| **`turf-detail.html`** | Master-Detail Explorer, Photo Gallery & SSLCOMMERZ Engine | Handles turf switching, photo gallery thumbnail updates, interactive slot matrix selection, opens the **SSLCOMMERZ EasyCheckout Modal**, simulates payment processing, writes confirmed booking to `localStorage`, and auto-redirects to `booking-receipt.html`. |
| **`book-turf.html`** | 4-Step Booking Stepper, Slot Generator & SSLCOMMERZ Modal | Controls 4-step wizard progression (`Choose Turf` &rarr; `Pick Slot` &rarr; `Payment` &rarr; `Confirmed`), sports category filtering, time slot selection, executes dummy payment with animated spinner/checkmark, and transfers transaction details to receipt page. |
| **`booking-receipt.html` & `player-receipts.html`** | Dynamic Receipt Engine (`loadRecentBookings`) | Reads `turfhub_recent_booking` from `localStorage`, displays celebration alert banner with Transaction ID, and dynamically prepends verified confirmed invoice cards to the receipt list. |
| **`admin-approvals.html`** | KYC Modal Controller & Document Tab Switcher | Clicking "Review KYC" opens the glassmorphic modal, allows switching between **Smart NID Card (Front/Back)** and **Ward Certificate**, and dynamically updates verification checklist items and status badges. |
| **`fixtures.html` & `player-fixtures.html`** | Tournament & View Switcher Tabs | Toggles between Match Fixtures list and League Standings table view, updating active tab styling (`.tournament-tab.active` and `.view-tab.active`). |
| **`score-entry.html`** | Live Scoreboard Increment/Decrement Counters | Goal buttons (`+` / `-`) update the large digital match score numbers in real time and switch match status between Upcoming, Live, and Full Time. |
| **`player-dashboard.html`** | Match RSVP Buttons | Toggles player attendance status pills (`Yes` / `No` / `Tentative`) for upcoming league fixtures with instant color feedback. |
| **`chat.html` / `player-chat.html` / `owner-chat.html`** | Chat Input & Conversation Switcher | Appends new message bubbles (`.msg-bubble.mine`) to the scrollable message window upon pressing Enter or clicking Send, and switches active chat threads. |
| **`admin-categories.html`** | Category Modal & Tag Creator | Opens the "Add Sport Category" modal dialog and dynamically toggles equipment and format tags. |
| **`admin-announcements.html`** | Broadcast Composer & Priority Selector | Updates priority pill tags (`Critical`, `Update`, `Maintenance`) and appends published announcements to the live broadcast feed. |

---

### 🔄 Summary Interaction Flow

```
┌─────────────────────────┐
│     User on Page        │
└────────────┬────────────┘
             │ 1. Checks Active Role
             ▼
┌─────────────────────────┐
│        auth.js          │ ──► Reads 'turfhub_role' from localStorage
└────────────┬────────────┘
             │ 2. Provides Role Manifest (Avatar, Name, Links)
             ▼
┌─────────────────────────┐
│        layout.js        │ ──► Dynamically injects #sidebar, #topbar, #sidebar-overlay
└────────────┬────────────┘
             │ 3. Injects Base Layout CSS (head style tag)
             ▼
┌─────────────────────────┐
│ Page Interactive Script │ ──► Handles dynamic clicks (modals, tabs, slot selection, score counters)
└────────────┬────────────┘
             │ 4. Executes Checkout & Stores State
             ▼
┌─────────────────────────┐
│  SSLCOMMERZ Gateway     │ ──► Simulates Handshake/OTP ──► Writes to localStorage ──► booking-receipt.html
└─────────────────────────┘
```
