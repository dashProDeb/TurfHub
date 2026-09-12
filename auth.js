/**
 * TurfHub Auth Utility
 * Role-based session management using localStorage
 * Roles: "owner" | "captain" | "player" | "admin"
 */

const ROLE_KEY = 'turfhub_role';
const USER_KEY = 'turfhub_user';

const ROLE_CONFIG = {
  owner: {
    label: 'Turf Owner',
    avatar: 'R',
    name: 'Rafiqul Islam',
    subtitle: 'Turf Owner',
    color: '#7ed321',
    dashboard: 'owner-dashboard.html',
    nav: [
      { id: 'dashboard',   icon: '📊', label: 'Dashboard',     href: 'owner-dashboard.html' },
      { id: 'manage',      icon: '🏟️', label: 'Manage Turfs',  href: 'manage-turf.html' },
      { id: 'calendar',    icon: '📅', label: 'Slot Calendar',  href: 'slot-calendar.html' },
      { id: 'tournaments', icon: '🏆', label: 'Tournaments',    href: 'create-tournament.html' },
      { id: 'score',       icon: '⚽', label: 'Score Entry',    href: 'score-entry.html' },
      { id: 'messages',    icon: '💬', label: 'Messages',       href: 'chat.html' },
      { id: 'reports',     icon: '📈', label: 'Reports',        href: 'owner-reports.html' },
    ]
  },
  captain: {
    label: 'Team Captain',
    avatar: 'F',
    name: 'Fatema Begum',
    subtitle: 'Team Captain',
    color: '#7ed321',
    dashboard: 'captain-dashboard.html',
    nav: [
      { id: 'dashboard',   icon: '📊', label: 'Dashboard',     href: 'captain-dashboard.html' },
      { id: 'book',        icon: '📅', label: 'Book Turf',     href: 'book-turf.html' },
      { id: 'tournaments', icon: '🏆', label: 'Tournaments',   href: 'create-tournament.html' },
      { id: 'fixtures',    icon: '📋', label: 'Fixtures',      href: 'fixtures.html' },
      { id: 'receipts',    icon: '🧾', label: 'Receipts',      href: 'booking-receipt.html' },
      { id: 'messages',    icon: '💬', label: 'Messages',      href: 'chat.html' },
    ]
  },
  player: {
    label: 'Player',
    avatar: 'A',
    name: 'Arif Hossain',
    subtitle: 'Player',
    color: '#7ed321',
    dashboard: 'player-dashboard.html',
    nav: [
      { id: 'dashboard',   icon: '📊', label: 'Dashboard',     href: 'player-dashboard.html' },
      { id: 'turfs',       icon: '🏟️', label: 'Find Turfs',   href: 'turf-detail.html' },
      { id: 'tournaments', icon: '🏆', label: 'Tournaments',   href: 'create-tournament.html' },
      { id: 'fixtures',    icon: '📋', label: 'Fixtures',      href: 'fixtures.html' },
      { id: 'receipts',    icon: '🧾', label: 'Receipts',      href: 'booking-receipt.html' },
      { id: 'messages',    icon: '💬', label: 'Messages',      href: 'chat.html' },
    ]
  },
  admin: {
    label: 'Admin',
    avatar: 'A',
    name: 'Admin User',
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
    ]
  }
};

function setRole(role) {
  localStorage.setItem(ROLE_KEY, role);
}

function getRole() {
  return localStorage.getItem(ROLE_KEY) || null;
}

function clearRole() {
  localStorage.removeItem(ROLE_KEY);
  localStorage.removeItem(USER_KEY);
}

function getRoleConfig(role) {
  return ROLE_CONFIG[role] || ROLE_CONFIG['player'];
}

function redirectByRole() {
  const role = getRole();
  if (!role) { window.location.href = 'login.html'; return; }
  window.location.href = getRoleConfig(role).dashboard;
}

function requireRole(...allowedRoles) {
  const role = getRole();
  if (!role || (allowedRoles.length > 0 && !allowedRoles.includes(role))) {
    window.location.href = 'login.html';
    return null;
  }
  return role;
}

function signOut() {
  clearRole();
  window.location.href = 'login.html';
}
