$ErrorActionPreference = "Stop"
$root = "g:\9th trimester\web-programming\turfhub\website\TurfHub"

# Map of file -> { navId, roles }
$pages = @{
    "manage-turf.html"            = @{ navId = "manage";       roles = "owner" }
    "slot-calendar.html"          = @{ navId = "calendar";     roles = "owner" }
    "owner-tournament.html"       = @{ navId = "tournaments";  roles = "owner" }
    "score-entry.html"            = @{ navId = "score";        roles = "owner" }
    "owner-chat.html"             = @{ navId = "messages";     roles = "owner" }
    "owner-reports.html"          = @{ navId = "reports";      roles = "owner" }
    "book-turf.html"              = @{ navId = "book";         roles = "captain','player" }
    "manage-team.html"            = @{ navId = "manage-team";  roles = "captain" }
    "fixtures.html"               = @{ navId = "tournaments";  roles = "captain','player" }
    "create-tournament.html"      = @{ navId = "host";         roles = "captain','owner" }
    "booking-receipt.html"        = @{ navId = "receipts";     roles = "captain','player" }
    "chat.html"                   = @{ navId = "messages";     roles = "captain','player" }
    "tournament-registration.html"= @{ navId = "tournaments";  roles = "captain','player" }
    "turf-detail.html"            = @{ navId = "turfs";        roles = "player','captain" }
    "join-team.html"              = @{ navId = "join-team";    roles = "player" }
    "player-fixtures.html"        = @{ navId = "fixtures";     roles = "player" }
    "player-receipts.html"        = @{ navId = "receipts";     roles = "player" }
    "player-chat.html"            = @{ navId = "messages";     roles = "player" }
    "admin-approvals.html"        = @{ navId = "approvals";    roles = "admin" }
    "admin-analytics.html"        = @{ navId = "analytics";    roles = "admin" }
    "admin-categories.html"       = @{ navId = "categories";   roles = "admin" }
    "admin-reports.html"          = @{ navId = "reports";      roles = "admin" }
    "admin-announcements.html"    = @{ navId = "announcements";roles = "admin" }
}

$scriptBlock = @"

  <script src="api-client.js"></script>
  <script src="auth.js"></script>
  <script src="layout.js"></script>
  <script src="page-init.js"></script>
"@

foreach ($file in $pages.Keys) {
    $path = Join-Path $root $file
    if (-not (Test-Path $path)) {
        Write-Host "SKIP: $file (not found)" -ForegroundColor Yellow
        continue
    }
    
    $content = Get-Content $path -Raw -Encoding UTF8
    $changed = $false
    
    # 1. Add script tags if missing (before </body>)
    if ($content -notmatch 'api-client\.js') {
        $navId = $pages[$file].navId
        $rolesStr = $pages[$file].roles
        
        $initScript = @"
$scriptBlock
  <script>
    initPage({ navId: '$navId', roles: ['$rolesStr'] });
  </script>
"@
        $content = $content -replace '(</body>)', "$initScript`n`$1"
        $changed = $true
    }
    
    if ($changed) {
        [System.IO.File]::WriteAllText($path, $content, [System.Text.Encoding]::UTF8)
        Write-Host "UPDATED: $file" -ForegroundColor Green
    } else {
        Write-Host "SKIP: $file (already has scripts)" -ForegroundColor Gray
    }
}

Write-Host "`nDone! All pages updated." -ForegroundColor Cyan
