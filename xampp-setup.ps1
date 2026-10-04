<#
.SYNOPSIS
    TurfHub - XAMPP Local Hosting Setup Script
.DESCRIPTION
    Run this as Administrator in PowerShell.
    It creates a symlink in htdocs, imports the DB, and opens the browser.
#>

$ErrorActionPreference = "Stop"

$XAMPP_ROOT   = "C:\xampp"
$HTDOCS       = Join-Path $XAMPP_ROOT "htdocs"
$MYSQL_BIN    = Join-Path $XAMPP_ROOT "mysql\bin\mysql.exe"
$PROJECT_DIR  = Split-Path -Parent $MyInvocation.MyCommand.Path
$LINK_NAME    = "turfhub"
$LINK_PATH    = Join-Path $HTDOCS $LINK_NAME
$SQL_DIR      = Join-Path $PROJECT_DIR "backend\api\sql"

Write-Host ""
Write-Host "=== TurfHub - XAMPP Setup ===" -ForegroundColor Cyan
Write-Host ""

# Step 1: Check XAMPP exists
if (-not (Test-Path $XAMPP_ROOT)) {
    Write-Host "[ERROR] XAMPP not found at $XAMPP_ROOT" -ForegroundColor Red
    exit 1
}
Write-Host "[OK] XAMPP found at $XAMPP_ROOT" -ForegroundColor Green

# Step 2: Create symlink in htdocs
if (Test-Path $LINK_PATH) {
    $item = Get-Item $LINK_PATH -Force
    if ($item.Attributes -band [IO.FileAttributes]::ReparsePoint) {
        Write-Host "[OK] Symlink already exists: $LINK_PATH" -ForegroundColor Yellow
    } else {
        Write-Host "[WARN] $LINK_PATH exists but is not a symlink. Skipping." -ForegroundColor Yellow
    }
} else {
    Write-Host "[...] Creating junction: $LINK_PATH -> $PROJECT_DIR" -ForegroundColor Cyan
    try {
        cmd /c mklink /J "$LINK_PATH" "$PROJECT_DIR" | Out-Null
        if (Test-Path $LINK_PATH) {
            Write-Host "[OK] Junction created successfully" -ForegroundColor Green
        } else {
            throw "Junction was not created"
        }
    } catch {
        Write-Host "[ERROR] Failed to create junction." -ForegroundColor Red
        Write-Host "        Manually copy the project folder to: $LINK_PATH" -ForegroundColor Yellow
        exit 1
    }
}

# Step 3: Check MySQL is running
Write-Host ""
Write-Host "[...] Checking MySQL connection..." -ForegroundColor Cyan

$mysqlTest = & $MYSQL_BIN -u root --password= -e "SELECT 1" 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Cannot connect to MySQL. Make sure XAMPP MySQL is running!" -ForegroundColor Red
    Write-Host "        Open XAMPP Control Panel and click Start next to MySQL." -ForegroundColor Yellow
    exit 1
}
Write-Host "[OK] MySQL is running" -ForegroundColor Green

# Step 4: Import database schema
Write-Host ""
Write-Host "[...] Importing database schema..." -ForegroundColor Cyan

$schemaFile = Join-Path $SQL_DIR "001_schema.sql"
Get-Content $schemaFile -Raw -Encoding utf8 | & $MYSQL_BIN -u root --password= --default-character-set=utf8mb4 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Schema imported successfully" -ForegroundColor Green
} else {
    Write-Host "[WARN] Schema import had warnings (tables may already exist)" -ForegroundColor Yellow
}

# Step 5: Import seed data
Write-Host "[...] Importing seed data..." -ForegroundColor Cyan

$seedFile = Join-Path $SQL_DIR "002_seed_data.sql"
Get-Content $seedFile -Raw -Encoding utf8 | & $MYSQL_BIN -u root --password= --default-character-set=utf8mb4 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Seed data imported successfully" -ForegroundColor Green
} else {
    Write-Host "[WARN] Seed data import had warnings (rows may already exist)" -ForegroundColor Yellow
}

# Step 6: Create uploads directories if missing
$uploadDirs = @(
    (Join-Path $PROJECT_DIR "backend\api\uploads\avatars"),
    (Join-Path $PROJECT_DIR "backend\api\uploads\kyc-documents"),
    (Join-Path $PROJECT_DIR "backend\api\uploads\turf-photos")
)
foreach ($dir in $uploadDirs) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
}
Write-Host "[OK] Upload directories verified" -ForegroundColor Green

# Done
Write-Host ""
Write-Host "=== Setup Complete! ===" -ForegroundColor Green
Write-Host ""
Write-Host "  Your TurfHub is now available at:" -ForegroundColor White
Write-Host "  http://localhost/turfhub/" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Demo login credentials:" -ForegroundColor White
Write-Host "    Admin:   admin@turfhub.com   / password123" -ForegroundColor Gray
Write-Host "    Owner:   rafiqul@turfhub.com / password123" -ForegroundColor Gray
Write-Host "    Captain: tanvir@turfhub.com  / password123" -ForegroundColor Gray
Write-Host "    Player:  sabbir@turfhub.com  / password123" -ForegroundColor Gray
Write-Host ""
Write-Host "  phpMyAdmin: http://localhost/phpmyadmin/" -ForegroundColor Cyan
Write-Host ""

# Open in browser
Start-Process "http://localhost/turfhub/"
