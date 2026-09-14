/**
 * TurfHub Layout Utility
 * Renders the shared sidebar for all authenticated pages.
 * Usage: call renderSidebar('dashboard') at page load.
 */

function renderSidebar(activeId) {
  const role = getRole();
  if (!role) { window.location.href = 'login.html'; return; }
  const cfg = getRoleConfig(role);

  const navHTML = cfg.nav.map(item => {
    const isActive = item.id === activeId;
    return `
      <a href="${item.href}" class="sidebar-nav-item ${isActive ? 'active' : ''}" title="${item.label}">
        <span class="sidebar-label">${item.label}</span>
      </a>`;
  }).join('');

  const sidebarHTML = `
    <aside id="sidebar" class="sidebar">
      <!-- Logo -->
      <a href="index.html" class="sidebar-logo">
        <div class="sidebar-logo-icon">TH</div>
        <span class="sidebar-logo-text">TurfHub</span>
      </a>

      <!-- User -->
      <div class="sidebar-user">
        <div class="sidebar-avatar">${cfg.avatar}</div>
        <div class="sidebar-user-info">
          <span class="sidebar-user-name">${cfg.name}</span>
          <span class="sidebar-user-role">${cfg.subtitle}</span>
        </div>
      </div>

      <!-- Nav -->
      <nav class="sidebar-nav">${navHTML}</nav>

      <!-- Sign Out -->
      <button class="sidebar-signout" onclick="signOut()">
        <span class="sidebar-label">Sign Out</span>
      </button>
    </aside>

    <!-- Mobile overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="closeSidebar()"></div>
  `;

  // Inject sidebar before main content
  const wrapper = document.getElementById('app-wrapper');
  if (wrapper) {
    wrapper.insertAdjacentHTML('afterbegin', sidebarHTML);
  }

  // Mobile toggle button (injected into topbar)
  const topbar = document.getElementById('topbar');
  if (topbar) {
    const btn = document.createElement('button');
    btn.id = 'sidebar-toggle';
    btn.className = 'sidebar-toggle';
    btn.innerHTML = '&#9776;';
    btn.onclick = toggleSidebar;
    topbar.prepend(btn);
  }
}

function toggleSidebar() {
  document.getElementById('sidebar')?.classList.toggle('open');
  document.getElementById('sidebar-overlay')?.classList.toggle('show');
}

function closeSidebar() {
  document.getElementById('sidebar')?.classList.remove('open');
  document.getElementById('sidebar-overlay')?.classList.remove('show');
}

// Shared CSS injected once
(function injectLayoutCSS() {
  if (document.getElementById('layout-css')) return;
  const style = document.createElement('style');
  style.id = 'layout-css';
  style.textContent = `
    /* ─── Layout Shell ─── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; font-family: "Inter", ui-sans-serif, system-ui, sans-serif; }
    body { background: #f5f5f0; color: #1a1a1a; -webkit-font-smoothing: antialiased; }

    #app-wrapper {
      display: flex;
      min-height: 100vh;
    }

    /* ─── Sidebar ─── */
    .sidebar {
      width: 220px;
      min-width: 220px;
      background: #0d2818;
      display: flex;
      flex-direction: column;
      padding: 20px 12px;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
      z-index: 100;
      transition: transform 0.3s ease;
      flex-shrink: 0;
    }

    .sidebar-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      margin-bottom: 28px;
      padding: 4px 8px;
    }
    .sidebar-logo-icon {
      width: 34px;
      height: 34px;
      background: #7ed321;
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 900;
      font-size: 11px;
      color: #0d2818;
      flex-shrink: 0;
    }
    .sidebar-logo-text {
      font-weight: 800;
      font-size: 1.05rem;
      color: #fff;
      letter-spacing: -0.01em;
    }

    .sidebar-user {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 10px 18px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      margin-bottom: 16px;
    }
    .sidebar-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: #7ed321;
      color: #0d2818;
      font-weight: 800;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .sidebar-user-info { display: flex; flex-direction: column; min-width: 0; }
    .sidebar-user-name {
      font-weight: 700;
      font-size: 0.82rem;
      color: #fff;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .sidebar-user-role {
      font-size: 0.72rem;
      color: rgba(255,255,255,0.5);
      margin-top: 1px;
    }

    .sidebar-nav {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .sidebar-nav-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 12px;
      border-radius: 10px;
      text-decoration: none;
      color: rgba(255,255,255,0.6);
      font-size: 0.82rem;
      font-weight: 500;
      transition: background 0.15s ease, color 0.15s ease;
      white-space: nowrap;
    }
    .sidebar-nav-item:hover { background: rgba(255,255,255,0.07); color: #fff; }
    .sidebar-nav-item.active { background: #7ed321; color: #0d2818; font-weight: 700; }

    .sidebar-label { overflow: hidden; text-overflow: ellipsis; }

    .sidebar-signout {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 12px;
      border-radius: 10px;
      border: none;
      background: transparent;
      color: rgba(255,255,255,0.4);
      font-size: 0.82rem;
      font-weight: 500;
      cursor: pointer;
      width: 100%;
      text-align: left;
      margin-top: 8px;
      transition: color 0.15s ease, background 0.15s ease;
    }
    .sidebar-signout:hover { background: rgba(229,57,53,0.15); color: #e53935; }

    /* ─── Main Content ─── */
    #main-content {
      flex: 1;
      min-width: 0;
      display: flex;
      flex-direction: column;
    }

    /* ─── Topbar ─── */
    #topbar {
      background: #fff;
      border-bottom: 1px solid #e8ede8;
      padding: 0 28px;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 50;
      gap: 12px;
    }
    .topbar-title { display: flex; flex-direction: column; }
    .topbar-heading { font-weight: 800; font-size: 1.15rem; color: #1a1a1a; line-height: 1.2; }
    .topbar-sub { font-size: 0.75rem; color: #6b7280; margin-top: 1px; }
    .topbar-actions { display: flex; align-items: center; gap: 10px; }

    /* ─── Page Body ─── */
    .page-body {
      padding: 24px 28px;
      flex: 1;
      overflow-y: auto;
    }

    /* ─── Stat Cards ─── */
    .stat-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 24px;
    }
    .stat-card {
      background: #fff;
      border: 1px solid #e8ede8;
      border-radius: 16px;
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 14px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
    .stat-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      flex-shrink: 0;
    }
    .stat-body {}
    .stat-value { font-size: 1.6rem; font-weight: 900; color: #1a1a1a; line-height: 1; }
    .stat-label { font-size: 0.75rem; color: #6b7280; margin-top: 3px; }
    .stat-trend { font-size: 0.72rem; font-weight: 600; margin-top: 4px; }
    .stat-trend.up { color: #7ed321; }
    .stat-trend.down { color: #e53935; }

    /* ─── Content Grid ─── */
    .content-grid {
      display: grid;
      grid-template-columns: 1fr 340px;
      gap: 20px;
    }
    .content-panel {
      background: #fff;
      border: 1px solid #e8ede8;
      border-radius: 16px;
      padding: 22px;
    }
    .panel-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }
    .panel-title { font-weight: 800; font-size: 1rem; color: #1a1a1a; }
    .panel-action { font-size: 0.8rem; font-weight: 600; color: #7ed321; text-decoration: none; }
    .panel-action:hover { color: #6ab81c; }

    /* ─── Buttons ─── */
    .btn-lime {
      background: #7ed321;
      color: #0d2818;
      font-weight: 700;
      border: none;
      cursor: pointer;
      border-radius: 10px;
      padding: 10px 20px;
      font-size: 0.85rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: background 0.2s ease, transform 0.15s ease;
      white-space: nowrap;
    }
    .btn-lime:hover { background: #6ab81c; transform: scale(1.02); }

    .btn-outline {
      background: transparent;
      color: #1a1a1a;
      font-weight: 600;
      border: 1.5px solid #d1d5db;
      cursor: pointer;
      border-radius: 10px;
      padding: 10px 20px;
      font-size: 0.85rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: border-color 0.2s ease, background 0.2s ease;
    }
    .btn-outline:hover { border-color: #9ca3af; background: #f9fafb; }

    /* ─── Badges ─── */
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 10px;
      border-radius: 100px;
      font-size: 0.72rem;
      font-weight: 700;
    }
    .badge-live { background: rgba(245,166,35,0.15); color: #d97706; }
    .badge-pending { background: rgba(245,166,35,0.15); color: #d97706; }
    .badge-confirmed { background: rgba(126,211,33,0.15); color: #5a9e12; }
    .badge-rejected { background: rgba(229,57,53,0.12); color: #e53935; }
    .badge-draft { background: rgba(107,114,128,0.12); color: #4b5563; }
    .badge-available { background: rgba(126,211,33,0.15); color: #5a9e12; }
    .badge-uncertain { background: rgba(245,166,35,0.15); color: #d97706; }
    .badge-unavailable { background: rgba(229,57,53,0.12); color: #e53935; }
    .badge-dot::before { content: "•"; margin-right: 2px; }

    /* ─── Mobile ─── */
    .sidebar-toggle {
      display: none;
      background: transparent;
      border: none;
      font-size: 1.4rem;
      cursor: pointer;
      color: #1a1a1a;
      padding: 4px 8px;
    }
    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.4);
      z-index: 90;
    }
    .sidebar-overlay.show { display: block; }

    @media (max-width: 900px) {
      .sidebar {
        position: fixed;
        left: 0; top: 0; bottom: 0;
        transform: translateX(-100%);
        z-index: 100;
      }
      .sidebar.open { transform: translateX(0); }
      .sidebar-toggle { display: block; }
      .stat-grid { grid-template-columns: repeat(2, 1fr); }
      .content-grid { grid-template-columns: 1fr; }
      .page-body { padding: 16px; }
      #topbar { padding: 0 16px; }
    }
    @media (max-width: 520px) {
      .stat-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
    }
  `;
  document.head.appendChild(style);
})();
