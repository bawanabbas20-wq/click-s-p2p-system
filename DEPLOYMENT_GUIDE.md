# P2P System Deployment Guide

A complete guide for deploying the Click P2P Procurement System to production.

---

## 📋 Pre-Deployment Checklist

- [ ] Server meets requirements (PHP 8.2+, MySQL 8.0+)
- [ ] Domain and SSL certificate ready
- [ ] Database server provisioned
- [ ] SMTP settings for email notifications (optional)
- [ ] Backup strategy planned

---

## 🖥️ Server Requirements

| Component  | Minimum                 | Recommended |
| ---------- | ----------------------- | ----------- |
| PHP        | 8.2                     | 8.3+        |
| MySQL      | 8.0                     | 8.0+        |
| RAM        | 1 GB                    | 2 GB+       |
| Storage    | 10 GB                   | 20 GB+      |
| Web Server | Apache 2.4 / Nginx 1.18 | Nginx 1.24+ |

### Required PHP Extensions

```bash
php -m  # Check installed extensions
```

Required: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`

---

## 🚀 Deployment Steps

### Step 1: Clone Repository

```bash
cd /var/www
git clone https://github.com/your-repo/p2p-system.git
cd p2p-system
```

### Step 2: Install Dependencies

```bash
# PHP dependencies
composer install --optimize-autoloader --no-dev

# Node.js dependencies (for building assets)
npm install
npm run build
```

### Step 3: Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Environment

Edit `.env` file:

```env
# Application
APP_NAME="Click P2P"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=p2p_system
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=file

# Mail (Optional - for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="P2P System"
```

### Step 5: Set Permissions

```bash
# Storage and cache must be writable
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Step 6: Database Setup

```bash
# Run migrations
php artisan migrate --force

# Create admin account
php artisan db:seed --class=AdminSeeder
```

### Step 7: Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Step 8: Create Storage Link

```bash
php artisan storage:link
```

---

## 🗄️ Database Guide

### Database Schema Overview

```
┌─────────────────────────────────────────────────────────────┐
│                      DATABASE SCHEMA                         │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  users ─────────────┬─────────────── purchase_requests      │
│    │                │                      │                 │
│    │                │                      ├── offers        │
│    │                │                      │                 │
│    │                │                      └── request_logs  │
│    │                │                                        │
│    └── vendors      └── budgets           settings           │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Table Details

#### `users`

| Column     | Type         | Description                                    |
| ---------- | ------------ | ---------------------------------------------- |
| id         | bigint       | Primary key                                    |
| name       | varchar(255) | Full name                                      |
| email      | varchar(255) | Unique email                                   |
| password   | varchar(255) | Hashed password                                |
| role       | enum         | employee, procurement, finance, manager, admin |
| avatar     | varchar(255) | Profile image path                             |
| department | varchar(255) | Optional department                            |

#### `purchase_requests`

| Column             | Type         | Description       |
| ------------------ | ------------ | ----------------- |
| id                 | bigint       | Primary key       |
| user_id            | bigint       | FK to users       |
| item_name          | varchar(255) | What to purchase  |
| estimated_price    | decimal      | Estimated cost    |
| estimated_currency | enum         | IQD or USD        |
| date_wanted        | date         | When needed       |
| priority           | enum         | low, medium, high |
| justification      | text         | Why needed        |
| status             | varchar(100) | Current status    |

#### `offers`

| Column                     | Type         | Description          |
| -------------------------- | ------------ | -------------------- |
| id                         | bigint       | Primary key          |
| purchase_request_id        | bigint       | FK to requests       |
| vendor_name                | varchar(255) | Vendor/supplier name |
| price                      | decimal      | Quoted price         |
| currency                   | enum         | IQD or USD           |
| file                       | varchar(255) | Attachment path      |
| is_chosen                  | boolean      | Selected offer       |
| is_procurement_recommended | boolean      | Procurement pick     |
| is_finance_recommended     | boolean      | Finance pick         |

#### `request_logs`

| Column              | Type         | Description     |
| ------------------- | ------------ | --------------- |
| id                  | bigint       | Primary key     |
| purchase_request_id | bigint       | FK to requests  |
| user_id             | bigint       | Who made change |
| old_status          | varchar(100) | Previous status |
| new_status          | varchar(100) | New status      |
| comment             | text         | Optional note   |

#### `budgets`

| Column            | Type    | Description  |
| ----------------- | ------- | ------------ |
| id                | bigint  | Primary key  |
| year              | int     | Budget year  |
| month             | int     | Budget month |
| budget_amount_iqd | decimal | IQD budget   |
| budget_amount_usd | decimal | USD budget   |

#### `vendors`

| Column  | Type         | Description   |
| ------- | ------------ | ------------- |
| id      | bigint       | Primary key   |
| name    | varchar(255) | Company name  |
| email   | varchar(255) | Contact email |
| phone   | varchar(50)  | Phone number  |
| address | text         | Address       |

#### `settings`

| Column | Type         | Description   |
| ------ | ------------ | ------------- |
| id     | bigint       | Primary key   |
| key    | varchar(255) | Setting name  |
| value  | text         | Setting value |
| group  | varchar(100) | Category      |

### Database Backup

**MySQL Dump (Daily recommended):**

```bash
# Backup
mysqldump -u your_user -p p2p_system > backup_$(date +%Y%m%d).sql

# Restore
mysql -u your_user -p p2p_system < backup_20250107.sql
```

**Automated Backup Script:**

```bash
#!/bin/bash
# /scripts/backup.sh
BACKUP_DIR=/backups/mysql
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u root -p'password' p2p_system | gzip > $BACKUP_DIR/p2p_backup_$DATE.sql.gz

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete
```

---

## 🔧 Web Server Configuration

### Nginx Configuration

```nginx
server {
    listen 80;
    listen 443 ssl http2;
    server_name your-domain.com;
    root /var/www/p2p-system/public;

    # SSL
    ssl_certificate /etc/ssl/certs/your-cert.crt;
    ssl_certificate_key /etc/ssl/private/your-key.key;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache Configuration (.htaccess)

Already included in Laravel's `public/.htaccess`

---

## 🔐 Security Considerations

### 1. Environment File

```bash
# NEVER commit .env to version control
chmod 640 .env
```

### 2. Debug Mode

```env
# ALWAYS set to false in production
APP_DEBUG=false
```

### 3. HTTPS Only

```env
# Force HTTPS
APP_URL=https://your-domain.com
```

### 4. Strong Passwords

- Change default admin password immediately
- Use strong database passwords
- Rotate passwords periodically

### 5. File Permissions

```bash
# Files: 644, Directories: 755
find /var/www/p2p-system -type f -exec chmod 644 {} \;
find /var/www/p2p-system -type d -exec chmod 755 {} \;

# Storage must be writable
chmod -R 775 storage bootstrap/cache
```

---

## 👤 Initial Admin Setup

After deployment, run:

```bash
php artisan db:seed --class=AdminSeeder
```

This creates:

- **Email:** admin@click.com
- **Password:ChangeMe123!**

⚠️ **IMPORTANT:** Change this password immediately after first login!

---

## 🔄 Updates & Maintenance

### Updating the Application

```bash
# Pull latest code
git pull origin main

# Install new dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Run new migrations
php artisan migrate --force

# Clear caches
php artisan optimize:clear
php artisan optimize
```

### Clearing Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🆘 Troubleshooting

| Issue             | Solution                                             |
| ----------------- | ---------------------------------------------------- |
| 500 Error         | Check `storage/logs/laravel.log`                   |
| Permission Denied | `chmod -R 775 storage bootstrap/cache`             |
| CSS Not Loading   | Run `npm run build` and `php artisan optimize`   |
| Database Error    | Verify `.env` database credentials                 |
| Session Issues    | `php artisan session:table && php artisan migrate` |

---

## 📞 Support Contacts

| Role             | Contact             |
| ---------------- | ------------------- |
| System Developer | [Your contact info] |
| Server Admin     | [IT admin contact]  |
| Database Admin   | [DBA contact]       |

---

## ✅ Post-Deployment Verification

- [ ] Can access login page
- [ ] Can log in as admin
- [ ] Can create new users
- [ ] Can create purchase request
- [ ] File uploads work
- [ ] Dark mode works
- [ ] Language switching works
- [ ] Charts display correctly

---

## ⚙️ Queue Worker Configuration (Crucial for Emails)

The system uses a background queue to send emails and process heavy tasks. Without this, **emails will not be sent.**

### Option A: Professional Server (Supervisor) - Recommended

If you are using a VPS (DigitalOcean, AWS, etc.), use Supervisor to ensure the queue worker runs permanently.

1. **Install Supervisor:**

   ```bash
   sudo apt-get install supervisor
   ```
2. **Create Config File:** `/etc/supervisor/conf.d/p2p-worker.conf`

   ```ini
   [program:p2p-worker]
   process_name=%(program_name)s_%(process_num)02d
   directory=/var/www/p2p-system
   command=php artisan queue:work --sleep=3 --tries=3 --max-time=3600
   autostart=true
   autorestart=true
   user=www-data
   numprocs=2
   redirect_stderr=true
   stdout_logfile=/var/www/p2p-system/storage/logs/worker.log
   stopwaitsecs=3600
   ```
3. **Start Supervisor:**

   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start p2p-worker:*
   ```

### Option B: Shared Hosting (Cron Job)

If you are on shared hosting (cPanel, HostGator) and cannot install Supervisor, use a Cron Job.

1. **Open Cron Jobs** in your hosting control panel.
2. **Add New Cron Job**:
   * **Frequency:** Once Per Minute (`* * * * *`)
   * **Command:**
     ```bash
     /usr/local/bin/php /home/your_user/public_html/artisan queue:work --stop-when-empty
     ```

   *(Note: Check your hosting provider's documentation for the correct path to the PHP executable.)*
