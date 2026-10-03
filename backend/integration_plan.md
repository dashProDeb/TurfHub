# 🔌 TurfHub Phase 10 — Frontend ↔ Backend Integration Plan

## Overview
Replace all hardcoded UI data and localStorage-based auth with real PHP API calls via `api-client.js`.

---

## Step-by-Step Integration Tasks

### Step 1: Update `auth.js` — Backend Session Auth
- Replace `localStorage`-based role management with `apiGet('auth/session.php')` calls
- `signOut()` → call `apiPost('auth/logout.php')` then redirect
- `requireRole()` → validate via session endpoint
- Store user profile data from API response

### Step 2: Update `login.html` — Real Login/Register
- Wire Sign In form → `apiPost('auth/login.php', { email, password })`
- Wire Sign Up form → `apiPost('auth/register.php', { email, password, full_name, role })`
- Remove fake role-based routing; redirect based on API response `user.role`
- Add proper error messages from API

### Step 3: Update `layout.js` — Dynamic Sidebar from Session
- Sidebar user name/avatar/role → populated from session data
- Sign Out button → calls backend logout

### Step 4: Update `owner-dashboard.html` — Live Owner Data
- Stats → derive from `apiGet('bookings/owner-bookings.php')` and `apiGet('turfs/my-turfs.php')`
- Booking requests list → rendered from `owner-bookings` data
- Replace all hardcoded booking rows

### Step 5: Update `captain-dashboard.html` — Live Captain Data
- Team roster → `apiGet('teams/detail.php?team_id=X')`
- Upcoming matches → from fixtures
- Stats → computed from team data

### Step 6: Update `player-dashboard.html` — Live Player Data
- Upcoming fixtures → from API
- Stats → computed from user's team memberships

### Step 7: Update `admin-dashboard.html` — Live Admin KPIs
- All KPI cards → `apiGet('admin/dashboard-stats.php')`
- Recent bookings table → from same endpoint
- Replace hardcoded stats and table rows

### Step 8: Add `<script src="api-client.js">` to All HTML Pages
- Ensure every page loads the API client before other scripts

---

## Key Principles
- **Graceful fallback**: If API call fails, show a loading/error state
- **No localStorage for auth**: Session cookies handle authentication
- **Preserve existing design**: Only swap data sources, keep all CSS/layout intact
