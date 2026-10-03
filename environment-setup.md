# 🛠️ TurfHub — Local Environment Setup Guide
> **Comprehensive guide to set up and run the TurfHub platform on any local PC (Windows, macOS, Linux).**  
> *Stack: Vanilla HTML5/CSS3/JS + PHP 8.x (PDO) + MySQL / MariaDB (Apache or PHP Built-in Server)*

---

## 📌 Table of Contents
1. [Prerequisites & System Requirements](#1-prerequisites--system-requirements)
2. [Method 1: XAMPP on Windows (Recommended for Beginners)](#2-method-1-xampp-on-windows-recommended-for-beginners)
3. [Method 2: Lightweight Setup using PHP Built-in Server (Any OS)](#3-method-2-lightweight-setup-using-php-built-in-server-any-os)
4. [Method 3: Setup on macOS (Homebrew / MAMP)](#4-method-3-setup-on-macos-homebrew--mamp)
5. [Method 4: Setup on Linux (Ubuntu / Debian)](#5-method-4-setup-on-linux-ubuntu--debian)
6. [Database Configuration & Migration](#6-database-configuration--migration)
7. [Directory Permissions & Uploads](#7-directory-permissions--uploads)
8. [Demo Login Credentials](#8-demo-login-credentials)
9. [Troubleshooting Common Issues](#9-troubleshooting-common-issues)

---

## 1. Prerequisites & System Requirements

Before beginning, ensure your machine has:
- **PHP**: `8.0` or higher (`8.1`, `8.2`, or `8.3` recommended)
  - Required PHP Extensions enabled: `pdo_mysql`, `json`, `mbstring`, `session`, `fileinfo`
- **Database Server**: **MySQL 5.7+** or **MariaDB 10.4+**
- **Web Server**: Apache, Nginx, or the native PHP development server
- **Git** (for cloning and pulling branches)
- Modern web browser (Chrome, Edge, Firefox, Brave, Safari)

---

## 2. Method 1: XAMPP on Windows (Recommended for Beginners)

XAMPP bundles Apache, PHP, and MariaDB/MySQL in an easy graphical installer.

### Step 1: Install XAMPP
1. Download **XAMPP for Windows (with PHP 8.x)** from [apachefriends.org](https://www.apachefriends.org/).
2. Run the installer and install it to the default path: `C:\xampp`.

### Step 2: Place TurfHub in `htdocs`
1. Open terminal (Command Prompt or PowerShell) and clone or copy the project into your `htdocs` directory:
   ```powershell
   cd C:\xampp\htdocs
   git clone -b feature/php-backend https://github.com/dashProDeb/TurfHub.git TurfHub
   ```
2. Verify that your directory looks like:
   ```
   C:\xampp\htdocs\TurfHub\
   ├── index.html
   ├── login.html
   ├── api-client.js
   ├── backend\
   └── ...
   ```

### Step 3: Configure Database Credentials
Open `C:\xampp\htdocs\TurfHub\backend\api\config\database.php` in your code editor:
```php
private const HOST = 'localhost';
private const DB   = 'turfhub';
private const USER = 'root';
private const PASS = ''; // Default XAMPP has no password (empty string)
private const PORT = 3306;
```
> [!NOTE]
> If your MySQL root account has a password, enter it in `PASS`. By default, standard XAMPP installs have no root password (`''`).

### Step 4: Import Database Schema & Seed Data
1. Launch the **XAMPP Control Panel** and click **Start** next to **Apache** and **MySQL**.
2. Open your browser and go to **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
3. Click the **SQL** tab at the top.
4. Open and copy the entire contents of:
   - `backend/api/sql/001_schema.sql`
   - Paste into phpMyAdmin and click **Go** (This creates the `turfhub` database and 12 tables).
5. Open and copy the entire contents of:
   - `backend/api/sql/002_seed_data.sql`
   - Paste into phpMyAdmin and click **Go** (This inserts demo accounts, turfs, categories, and teams).

### Step 5: Run the Project
Open your browser and navigate to:
```
http://localhost/TurfHub/
```
Or go directly to the sign-in page:
```
http://localhost/TurfHub/login.html
```

---

## 3. Method 2: Lightweight Setup using PHP Built-in Server (Any OS)

If you already have PHP and MySQL installed, or prefer not to install XAMPP, you can use PHP's built-in development server.

### Step 1: Verify PHP & MySQL
Open your terminal and check:
```bash
php -v
mysql -V
```

Ensure `pdo_mysql` is enabled. You can check with:
```bash
php -m | grep pdo_mysql
```
*(On Windows PowerShell: `php -m | Select-String "pdo_mysql"`)*

If missing, open your `php.ini` file and uncomment:
```ini
extension=pdo_mysql
extension=fileinfo
extension=mbstring
```

### Step 2: Import Database via MySQL CLI
From the root of the project directory:
```bash
mysql -u root -p < backend/api/sql/001_schema.sql
mysql -u root -p turfhub < backend/api/sql/002_seed_data.sql
```
*(Press Enter if you do not have a password set).*

### Step 3: Configure Database Connection
Edit `backend/api/config/database.php` to match your local MySQL credentials:
```php
private const HOST = '127.0.0.1'; // or 'localhost'
private const DB   = 'turfhub';
private const USER = 'root';
private const PASS = 'YOUR_MYSQL_PASSWORD';
private const PORT = 3306;
```

### Step 4: Start the PHP Server
From the root `TurfHub` folder, run:
```bash
php -S localhost:8000
```

### Step 5: Open in Browser
Visit:
```
http://localhost:8000/login.html
```

---

## 4. Method 3: Setup on macOS (Homebrew / MAMP)

### Using Homebrew:
1. Install PHP and MySQL:
   ```bash
   brew install php mysql
   brew services start mysql
   ```
2. Create and seed the database:
   ```bash
   mysql -u root < backend/api/sql/001_schema.sql
   mysql -u root turfhub < backend/api/sql/002_seed_data.sql
   ```
3. Update `backend/api/config/database.php` (`USER = 'root'`, `PASS = ''`).
4. Start the server from inside the project directory:
   ```bash
   php -S 127.0.0.1:8000
   ```
5. Open [http://127.0.0.1:8000/login.html](http://127.0.0.1:8000/login.html).

*(Alternatively, you can place the folder in `/Applications/MAMP/htdocs/TurfHub` and start MAMP).*

---

## 5. Method 4: Setup on Linux (Ubuntu / Debian)

1. Install Apache, PHP, and MySQL:
   ```bash
   sudo apt update
   sudo apt install apache2 php libapache2-mod-php php-mysql mysql-server git -y
   ```
2. Clone repository into Apache root:
   ```bash
   cd /var/www/html
   sudo git clone -b feature/php-backend https://github.com/dashProDeb/TurfHub.git
   sudo chown -R www-data:www-data /var/www/html/TurfHub
   sudo chmod -R 775 /var/www/html/TurfHub/backend/api/uploads
   ```
3. Import Database:
   ```bash
   sudo mysql < /var/www/html/TurfHub/backend/api/sql/001_schema.sql
   sudo mysql turfhub < /var/www/html/TurfHub/backend/api/sql/002_seed_data.sql
   ```
4. Create a dedicated MySQL user or configure `backend/api/config/database.php`.
5. Access via: `http://localhost/TurfHub/login.html`

---

## 6. Database Configuration & Migration

The connection file is located at:
📁 [`backend/api/config/database.php`](file:///backend/api/config/database.php)

```php
<?php
class Database {
    private static ?PDO $instance = null;

    private const HOST = 'localhost';
    private const DB   = 'turfhub';
    private const USER = 'root';
    private const PASS = ''; // <-- Put your MySQL password here
    private const PORT = 3306;

    public static function connect(): PDO {
        // ... PDO initialization with UTF-8 and Exception Mode
    }
}
```

### Migration Order:
Always run the scripts in numerical order:
1. `backend/api/sql/001_schema.sql` (Creates DB, 12 tables, indexes, constraints)
2. `backend/api/sql/002_seed_data.sql` (Seeds demo users with bcrypt passwords, categories, turfs, teams, slots, and bookings)

---

## 7. Directory Permissions & Uploads

TurfHub stores uploaded files locally inside `backend/api/uploads/`:
- `uploads/turf-photos/` — Ground banners and gallery images
- `uploads/kyc-documents/` — Owner National ID and Councilor Character Certificates
- `uploads/avatars/` — Profile images

Make sure these folders exist and have write permissions:
- **Windows**: Right-click folder → Properties → Ensure not "Read-Only".
- **Linux / macOS**:
  ```bash
  chmod -R 775 backend/api/uploads
  ```

---

## 8. Demo Login Credentials

The seed script creates 4 pre-configured demo users representing each role.

> [!IMPORTANT]  
> All demo accounts use the same password: **`password123`**

| Role | Name | Email | Password | Landing Page |
| :--- | :--- | :--- | :--- | :--- |
| 🛡️ **Platform Admin** | Admin User | `admin@turfhub.com` | `password123` | `admin-dashboard.html` |
| 🏟️ **Turf Owner** | Rafiqul Islam | `rafiqul@turfhub.com` | `password123` | `owner-dashboard.html` |
| 🏆 **Team Captain** | Tanvir Ahmed | `tanvir@turfhub.com` | `password123` | `captain-dashboard.html` |
| 🏃 **Player / Free Agent** | Sabbir Ahmed | `sabbir@turfhub.com` | `password123` | `player-dashboard.html` |

You can also use the **1-Click Quick Login** pill buttons on the [login.html](file:///login.html) page to automatically test any role.

---

## 9. Troubleshooting Common Issues

### Issue 1: "Failed to load data. Is the backend running?"
- **Cause**: PHP is not serving the API endpoints, or MySQL is stopped.
- **Solution**:
  1. Verify Apache and MySQL are running green in XAMPP.
  2. Test if the session API works directly in your browser:  
     `http://localhost/TurfHub/backend/api/auth/session.php`  
     *(Should return `{"authenticated":false}` as clean JSON)*.

---

### Issue 2: `SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'`
- **Cause**: The password in `backend/api/config/database.php` does not match your MySQL root password.
- **Solution**: Open `database.php` and set `PASS` to your actual MySQL password (or leave it `''` if empty).

---

### Issue 3: `could not find driver`
- **Cause**: The PHP `pdo_mysql` extension is disabled.
- **Solution**:
  1. Open your `php.ini` file (In XAMPP: Click **Config** next to Apache → `PHP (php.ini)`).
  2. Search for `;extension=pdo_mysql` and remove the leading semicolon:
     ```ini
     extension=pdo_mysql
     ```
  3. Save and restart Apache.

---

### Issue 4: Port 80 or 3306 Conflict in XAMPP
- **Cause**: Another program (Skype, VMware, IIS, or an existing MySQL service) is using port 80 or 3306.
- **Solution**:
  - For MySQL: If another MySQL service is running on 3306, stop it in Windows Services (`services.msc`), or change port in `my.ini` and update `PORT = 3307` in `database.php`.
  - For Apache: In XAMPP, click **Config** → `httpd.conf` and change `Listen 80` to `Listen 8080`. Then access via `http://localhost:8080/TurfHub/`.

---

### Issue 5: Session Login Does Not Persist
- **Cause**: Browser blocked third-party cookies or session cookies.
- **Solution**: Always access via the same hostname (e.g., both frontend and API using `localhost`, not mixing `127.0.0.1` with `localhost`).

---

🎉 **You are all set! Happy coding and pair programming on TurfHub!**
