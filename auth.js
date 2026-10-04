/**
 * TurfHub Auth Utility
 * Role-based session management via PHP backend API
 * Roles: "owner" | "captain" | "player" | "admin"
 */

const ROLE_CONFIG = {
  owner: {
    label: 'Turf Owner',
    subtitle: 'Turf Owner',
    color: '#7ed321',
    dashboard: 'owner-dashboard.html',
    nav: [
      { id: 'dashboard',   icon: '📊', label: 'Dashboard',     href: 'owner-dashboard.html' },
      { id: 'manage',      icon: '🏟️', label: 'Manage Turfs',  href: 'manage-turf.html' },
      { id: 'calendar',    icon: '📅', label: 'Slot Calendar',  href: 'slot-calendar.html' },
      { id: 'tournaments', icon: '🏆', label: 'Tournaments',    href: 'owner-tournament.html' },
      { id: 'score',       icon: '⚽', label: 'Score Entry',    href: 'score-entry.html' },
      { id: 'messages',    icon: '💬', label: 'Messages',       href: 'owner-chat.html' },
      { id: 'reports',     icon: '📈', label: 'Reports',        href: 'owner-reports.html' },
    ]
  },
  captain: {
    label: 'Team Captain',
    subtitle: 'Team Captain',
    color: '#7ed321',
    dashboard: 'captain-dashboard.html',
    nav: [
      { id: 'dashboard',   icon: '📊', label: 'Dashboard',         href: 'captain-dashboard.html' },
      { id: 'book',        icon: '📅', label: 'Book Turf',         href: 'book-turf.html' },
      { id: 'manage-team', icon: '👥', label: 'Manage Team',       href: 'manage-team.html' },
      { id: 'tournaments', icon: '🏆', label: 'Tournaments',       href: 'tournament-registration.html' },
      { id: 'fixtures',    icon: '📋', label: 'Fixtures & Tables', href: 'fixtures.html' },
      { id: 'host',        icon: '🏅', label: 'Host Tournament',   href: 'create-tournament.html' },
      { id: 'receipts',    icon: '🧾', label: 'My Receipts',       href: 'booking-receipt.html' },
      { id: 'messages',    icon: '💬', label: 'Messages',          href: 'chat.html' },
    ]
  },
  player: {
    label: 'Player',
    subtitle: 'Player',
    color: '#7ed321',
    dashboard: 'player-dashboard.html',
    nav: [
      { id: 'dashboard',   icon: '📊', label: 'Dashboard',     href: 'player-dashboard.html' },
      { id: 'turfs',       icon: '🏟️', label: 'Find Turfs',   href: 'turf-detail.html' },
      { id: 'join-team',   icon: '👥', label: 'Join Team',     href: 'join-team.html' },
      { id: 'fixtures',    icon: '📋', label: 'Fixtures',      href: 'player-fixtures.html' },
      { id: 'receipts',    icon: '🧾', label: 'My Receipts',   href: 'player-receipts.html' },
      { id: 'messages',    icon: '💬', label: 'Team Chat',     href: 'player-chat.html' },
    ]
  },
  admin: {
    label: 'Admin',
    subtitle: 'Platform Admin',
    color: '#7ed321',
    dashboard: 'admin-dashboard.html',
    nav: [
      { id: 'overview',      icon: '📊', label: 'Overview',      href: 'admin-dashboard.html' },
      { id: 'approvals',     icon: '✅', label: 'Approvals',     href: 'admin-approvals.html' },
      { id: 'analytics',     icon: '📈', label: 'Analytics',     href: 'admin-analytics.html' },
      { id: 'categories',    icon: '🏷️', label: 'Categories',    href: 'admin-categories.html' },
      { id: 'reports',       icon: '📄', label: 'Reports',       href: 'admin-reports.html' },
      { id: 'announcements', icon: '📢', label: 'Announce',      href: 'admin-announcements.html' },
      { id: 'messages',       icon: '📢', label: 'Broadcasts',    href: 'admin-chat.html' },
    ]
  }
};

/* ── In-memory session cache ────────────────────────────── */
let _currentUser = null;   // { id, email, full_name, role, phone, avatar_url, ... }

/**
 * Get the current user object (from cache).
 * Call checkSession() first to populate.
 */
function getCurrentUser() {
  return _currentUser;
}

function getRole() {
  return _currentUser ? _currentUser.role : null;
}

function getRoleConfig(role) {
  return ROLE_CONFIG[role] || ROLE_CONFIG['player'];
}

/**
 * Check session against the backend. Populates _currentUser.
 * @returns {Promise<Object|null>} user object or null
 */
async function checkSession() {
  try {
    const data = await apiGet('auth/session.php');
    _currentUser = data.user;
    return _currentUser;
  } catch (e) {
    _currentUser = null;
    return null;
  }
}

/**
 * Login via backend.
 * @returns {Promise<Object>} user object on success
 */
async function doLogin(email, password) {
  const data = await apiPost('auth/login.php', { email, password });
  _currentUser = data.user;
  return _currentUser;
}

/**
 * Register via backend.
 * @returns {Promise<Object>} user object on success
 */
async function doRegister(email, password, fullName, role, phone) {
  const data = await apiPost('auth/register.php', {
    email, password, full_name: fullName, role, phone
  });
  _currentUser = data.user;
  return _currentUser;
}

/**
 * Sign out — destroy backend session, then redirect.
 */
async function signOut() {
  try {
    await apiPost('auth/logout.php');
  } catch (e) { /* ignore */ }
  _currentUser = null;
  window.location.href = 'login.html';
}

/**
 * Redirect user to their role-appropriate dashboard.
 */
function redirectByRole() {
  const role = getRole();
  if (!role) { window.location.href = 'login.html'; return; }
  window.location.href = getRoleConfig(role).dashboard;
}

/**
 * Guard: ensure user is authenticated with one of the allowed roles.
 * Must be called after checkSession().
 * If not authenticated or wrong role, redirects to login.
 * @param  {...string} allowedRoles
 * @returns {string|null} current role, or null (after redirect)
 */
function requireRole(...allowedRoles) {
  const role = getRole();
  if (!role || (allowedRoles.length > 0 && !allowedRoles.includes(role))) {
    window.location.href = 'login.html';
    return null;
  }
  return role;
}

/**
 * Initialize auth on page load.
 * Checks session, guards role, and returns user.
 * Call this at the top of every authenticated page:
 *   const user = await initAuth('owner');
 *   if (!user) return; // redirected to login
 */
async function initAuth(...allowedRoles) {
  const user = await checkSession();
  if (!user) {
    window.location.href = 'login.html';
    return null;
  }
  if (allowedRoles.length > 0 && !allowedRoles.includes(user.role)) {
    window.location.href = 'login.html';
    return null;
  }
  return user;
}
