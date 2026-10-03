/**
 * TurfHub Page Initializer
 * One-line init for any authenticated page.
 * 
 * Usage in any page:
 *   <script src="api-client.js"></script>
 *   <script src="auth.js"></script>
 *   <script src="layout.js"></script>
 *   <script src="page-init.js"></script>
 *   <script>
 *     initPage({ navId: 'dashboard', roles: ['owner'] }).then(user => { ... });
 *   </script>
 * 
 * This script:
 *  1. Removes any hardcoded <aside id="sidebar"> from the HTML
 *  2. Calls initAuth() with the specified roles
 *  3. Calls renderSidebar() with the session user
 *  4. Returns the user object
 */

async function initPage({ navId = '', roles = [] } = {}) {
  // Remove hardcoded sidebar if present (so layout.js can inject a dynamic one)
  const existingSidebar = document.getElementById('sidebar');
  if (existingSidebar && existingSidebar.tagName === 'ASIDE') {
    existingSidebar.remove();
  }
  const existingOverlay = document.getElementById('sidebar-overlay');
  if (existingOverlay) existingOverlay.remove();

  // Auth
  const user = await initAuth(...roles);
  if (!user) return null;

  // Sidebar
  renderSidebar(navId, user);

  return user;
}
