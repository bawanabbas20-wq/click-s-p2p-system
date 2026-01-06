# P2P System - Deployment & Setup Guide

## 1. Local Development Setup (Email)

To test emails locally using a Gmail account:

1.  **Get an App Password**:
    *   Go to [Google Account Security](https://myaccount.google.com/security).
    *   Enable 2-Step Verification.
    *   Search for "App passwords" and create one.
2.  **Configure `.env`**:
    ```env
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.gmail.com
    MAIL_PORT=587
    MAIL_USERNAME=your-email@gmail.com
    MAIL_PASSWORD=your-app-password-here
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS="your-email@gmail.com"
    MAIL_FROM_NAME="Click P2P System"
    ```
3.  **Run the Queue Worker**:
    Since `QUEUE_CONNECTION=database`, you must run this command in a separate terminal to send emails:
    ```bash
    php artisan queue:work
    ```

---

## 2. Production Deployment (Going Live)

When deploying to a live server (e.g., DigitalOcean, AWS, Linode), follow these steps.

### A. Environment Configuration
Update your production `.env` file:
*   `APP_ENV=production`
*   `APP_DEBUG=false` (Critical for security)
*   `QUEUE_CONNECTION=database` (or `redis` for speed)

### B. Email Service
**Do not use Gmail for production.** It has strict rate limits. Use a transactional email provider:
*   **Mailgun**
*   **Postmark**
*   **Amazon SES**

### C. Setting up the Queue Worker (Supervisor)
You cannot run `php artisan queue:work` manually in a terminal on a server. Use **Supervisor** to keep it running in the background.

1.  **Install Supervisor**:
    ```bash
    sudo apt-get install supervisor
    ```

2.  **Create Config** (`/etc/supervisor/conf.d/p2p-worker.conf`):
    ```ini
    [program:p2p-worker]
    process_name=%(program_name)s_%(process_num)02d
    directory=/path/to/your/project
    command=php artisan queue:work --sleep=3 --tries=3 --max-time=3600
    autostart=true
    autorestart=true
    user=www-data
    numprocs=2
    redirect_stderr=true
    stdout_logfile=/path/to/your/project/storage/logs/worker.log
    ```

3.  **Start Supervisor**:
    ```bash
    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start p2p-worker:*
    ```

### D. Optimization
Run these commands during deployment to cache configuration and routes for speed:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
```
