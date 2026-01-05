# CMS Deployment Guide

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+ or PostgreSQL 13+ (SQLite for development)
- Web server (Apache/Nginx)

---

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-repo/cms.git
cd cms
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cms_database
DB_USERNAME=cms_user
DB_PASSWORD=your_password
```

### 5. Run Migrations

```bash
php artisan migrate --seed
```

This will create all tables and seed:
- 4 default roles (Super Admin, Admin, Mentor, Student)
- 17 permissions
- Default theme
- Test user (test@example.com / password)

### 6. Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Development Server

```bash
# Using Laravel's built-in server
php artisan serve

# Or using the dev script (includes queue and Vite)
composer run dev
```

---

## Production Deployment

### 1. Server Requirements

- PHP 8.2+ with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- Composer
- Node.js & npm
- MySQL/PostgreSQL
- Redis (recommended for caching & queues)

### 2. Environment Setup

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# CMS Settings
CMS_DEFAULT_THEME=default
CMS_CACHE_ENABLED=true
CMS_AUDIT_ENABLED=true
```

### 3. Optimize Application

```bash
# Install production dependencies
composer install --optimize-autoloader --no-dev

# Build production assets
npm run build

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force
```

### 4. Set Permissions

```bash
# Storage and cache directories
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5. Configure Web Server

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/cms/public;

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

#### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/cms/public

    <Directory /var/www/cms/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/cms-error.log
    CustomLog ${APACHE_LOG_DIR}/cms-access.log combined
</VirtualHost>
```

### 6. Setup Queue Worker

```bash
# Using systemd
sudo nano /etc/systemd/system/cms-worker.service
```

```ini
[Unit]
Description=CMS Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/cms/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable cms-worker
sudo systemctl start cms-worker
```

### 7. Setup Scheduler

Add to crontab:
```bash
* * * * * cd /var/www/cms && php artisan schedule:run >> /dev/null 2>&1
```

---

## SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d your-domain.com

# Auto-renewal is configured automatically
```

---

## Backup Strategy

### Database Backup

```bash
# Create backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u cms_user -p cms_database > /backups/cms_$DATE.sql
```

### File Backup

```bash
# Backup uploads and themes/plugins
tar -czf /backups/cms_files_$DATE.tar.gz \
    /var/www/cms/storage/app/public \
    /var/www/cms/themes \
    /var/www/cms/plugins
```

---

## Monitoring

### Laravel Telescope (Development)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Laravel Horizon (Queue Monitoring)

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

---

## Troubleshooting

### Clear All Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Reset Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Check Logs

```bash
tail -f storage/logs/laravel.log
```

---

## Updating

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install --no-dev
npm install

# Run migrations
php artisan migrate --force

# Rebuild assets
npm run build

# Clear and rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
sudo systemctl restart cms-worker
```

---

## Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Use strong `APP_KEY`
- [ ] Configure HTTPS/SSL
- [ ] Set proper file permissions (775 for storage)
- [ ] Enable CSRF protection
- [ ] Configure rate limiting
- [ ] Use Redis for sessions in production
- [ ] Enable audit logging
- [ ] Regular backups
- [ ] Keep dependencies updated
- [ ] Use environment variables for secrets

---

## Performance Optimization

1. **Enable OPcache** in `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
```

2. **Use Redis for caching**:
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

3. **Enable CDN** for static assets

4. **Configure database connection pooling**

5. **Use queue workers** for heavy tasks

---

For support, visit [GitHub Issues](https://github.com/your-repo/cms/issues).
