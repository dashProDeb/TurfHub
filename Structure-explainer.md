# TurfHub Codebase Structure & CSS Architecture Explainer

## 📌 Executive Summary
TurfHub uses a **hybrid architecture** combining a **Global Custom CSS Design System (`style.css` + page-level scoped `<style>`)** and **Tailwind CSS Utility Classes**. 

This document explains the division of responsibilities across the entire static codebase, outlining **where raw CSS is used**, **where Tailwind is used**, and the **architectural rationale (why)** for each page.

---

## 🏗️ Global Architectural Strategy: Why Raw CSS vs. Tailwind CSS?

| Layer | Technology | Primary Purpose | Why This Choice? |
| :--- | :--- | :--- | :--- |
| **Global Design System & Shell** | **Raw CSS (`style.css`)** | Layout shell (`#app-wrapper`, `#main-content`, `#topbar`, `.sidebar`), typography tokens, button variants, badges, and stat cards. | Provides guaranteed pixel-perfect layout stability, fixed sticky desktop sidebars, and reusable role-based color variables (`--color-forest`, `--color-lime`) without framework lock-in or build-step dependencies. |
| **Bespoke Domain Components** | **Raw Scoped CSS (`<style>`)** | 7-day hourly booking matrices, Smart NID cards, Ward Councilor certificates, interactive chat bubbles, match scoreboards, and league standings tables. | Complex custom styling (guilloché security patterns, certificate borders, dynamic slot matrix states, pulsing animations) requires precise multi-selector rules and custom pseudo-elements difficult to express with pure utility classes. |
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

---

### 4. Team Captain Pages

#### 🏆 `captain-dashboard.html` (Captain Team Hub)
- **Raw CSS Used:**
  - Standalone Captain sidebar (`captain-dashboard.html` active).
  - Player squad numbered avatars (`.player-num`) and match countdown cards.
- **Tailwind CSS Used:**
  - Roster table, stat cards, and upcoming league match cards.
- **Why in short:** Compact roster layout with custom numbered squad circles.

#### ⚽ `book-turf.html` (Quick Turf Booking Stepper)
- **Raw CSS Used:**
  - Multi-step progress stepper (`.step.active`, `.step.done`, `.step-line`).
  - Interactive turf selection cards with green border highlight (`.turf-card.selected`).
- **Tailwind CSS Used:**
  - Sport filter dropdown, price tags, and continue CTA button.
- **Why in short:** Stepper progression and turf card selection states require custom CSS styling.

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

#### 🧾 `booking-receipt.html` (Booking Receipts & Invoices)
- **Raw CSS Used:**
  - Transaction receipt cards with PDF download action bar (`.receipt-card`, `.receipt-foot`).
  - Status filter pills (`.filter-tab.active`).
- **Tailwind CSS Used:**
  - Price typography, date badge alignment, and filter bar flexbox.
- **Why in short:** Clean receipt invoice cards matching print/download design tokens.

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
- **Tailwind CSS Used:**
  - Top search bar, location dropdown, amenity chips, and customer review cards.
- **Why in short:** Complex split-view master-detail ground explorer with interactive slot booking and photo switching.

#### 👥 `join-team.html` (Free Agent & Team Recruitment)
- **Raw CSS Used:**
  - Team recruitment cards with squad size status pills (`.open-tag`, `.full-tag`) and team badge icons.
- **Tailwind CSS Used:**
  - Filter tabs, search bar, and join request button.
- **Why in short:** Standardized team cards with custom capacity tags.

---

## 📊 Summary Architecture Matrix

```
┌──────────────────────────────────────────────────────────────────────────┐
│                             TURFHUB FRONTEND                             │
├──────────────────────────────────────────────────────────────────────────┤
│ 1. Core Shell & Design System: style.css (Raw CSS)                       │
│    ├── #app-wrapper, #main-content, #topbar, .page-body                  │
│    ├── .sidebar, .sidebar-nav-item, .sidebar-user, .sidebar-logo         │
│    └── Color Tokens: --color-forest (#0d2818), --color-lime (#7ed321)    │
├──────────────────────────────────────────────────────────────────────────┤
│ 2. Scoped Custom Components: <style> per Page (Raw CSS)                  │
│    ├── Slot Matrix Grid & Dynamic States (slot-calendar.html)            │
│    ├── NID Cards (Part 1/2) & Ward Certificates (admin-approvals.html)   │
│    ├── Standings Table & Form Badges (fixtures.html)                     │
│    ├── Interactive Master-Detail & Slot Pills (turf-detail.html)         │
│    └── Chat Shell & Message Bubbles (chat.html)                          │
├──────────────────────────────────────────────────────────────────────────┤
│ 3. Atomic Utilities: Tailwind CSS (Browser Runtime)                      │
│    ├── Responsive Grids (grid-cols-1, md:grid-cols-3, gap-6)             │
│    ├── Flexbox Utilities (flex, items-center, justify-between)           │
│    └── Fluid Spacing & Typography (text-sm, font-black, p-4, m-2)        │
└──────────────────────────────────────────────────────────────────────────┘
```
