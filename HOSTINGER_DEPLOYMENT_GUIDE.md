# 🌐 HOSTINGER DEPLOYMENT GUIDE - trouvemaalem

Complete guide to deploy trouvemaalem to Hostinger hosting.

---

## 📋 What You'll Need

Before starting, gather:
- ✅ Hostinger account (Business plan minimum recommended)
- ✅ FTP credentials (or SSH access)
- ✅ MySQL database credentials
- ✅ Your domain name
- ✅ SSL certificate (Hostinger provides free Let's Encrypt)
- ✅ Email credentials (for SMTP configuration)

---

## 🎯 Hostinger Hosting Options

### Recommended Plan: **Business Shared Hosting**

**Why Business Plan?**
- ✅ Unlimited subdomains
- ✅ Unlimited MySQL databases
- ✅ Free SSL certificate (Let's Encrypt)
- ✅ 100+ GB storage
- ✅ SSH access (allows git deployment)
- ✅ PHP 8.3+ support
- ✅ Cronjobs support (for queue)
- ✅ Email hosting

**Minimum Requirements:**
- PHP 8.3+
- MySQL 8.0+ or MariaDB
- Composer support (check with Hostinger)
- SSH terminal access

---

## ⚙️ Step 1: Hostinger Control Panel Setup

### 1.1 Login to Hostinger

1. Go to https://www.hostinger.com/
2. Click "Log In" (top right)
3. Enter your credentials
4. Dashboard appears

### 1.2 Create MySQL Database

1. In Hostinger Dashboard, click **"MySQL Databases"**
2. Click **"Create Database"**
3. Fill in:
   - **Database Name:** `trouvemaalem`
   - **Database User:** `trouvemaalem_user`
   - **Password:** Strong password (min 16 chars)
4. Click **"Create Database"**
5. **Save credentials:**
   - DB Host: Usually `localhost` or IP address shown
   - DB Name: `trouvemaalem`
   - DB User: `trouvemaalem_user`
   - DB Password: Your strong password

### 1.3 Setup File Manager / FTP Access

**Option A: Using File Manager (Web-based)**
1. Dashboard → **"File Manager"**
2. Navigate to `public_html` folder
3. Can upload files directly (easier for beginners)

**Option B: Using FTP (Recommended)**
1. Dashboard → **"FTP Accounts"**
2. Click **"New FTP Account"**
3. Create account:
   - **Username:** `trouvemaalem` (or your preference)
   - **Password:** Strong password
   - **Home Directory:** `/public_html` (or `/trouvemaalem`)
4. Click **"Create"**
5. **Save FTP credentials:**
   - Host: `ftp.yourdomain.com` or IP shown
   - Username: Your FTP username
   - Password: Your FTP password
   - Port: 21 (standard FTP) or 22 (SFTP preferred)

**Option C: Using SSH (Most Powerful)**
1. Dashboard → **"SSH/Terminal"**
2. Check if SSH is available (Business plan+)
3. Generate or use existing SSH keys
4. Note the SSH command: `ssh user@yourdomain.com`

### 1.4 Configure PHP Version

1. Dashboard → **"PHP Version"**
2. Select **PHP 8.3** (or latest available)
3. Click **"Save"**
4. Wait for 5-10 minutes for changes to apply

### 1.5 Setup Free SSL Certificate

1. Dashboard → **"SSL Certificates"**
2. Should show **"Let's Encrypt"** option (free)
3. Click **"Install"** next to Let's Encrypt
4. Select your domain
5. Check **"Auto-renewal"**
6. Click **"Activate"**
7. Wait 5-15 minutes for activation
8. Verify: Visit `https://yourdomain.com` (lock icon appears)

### 1.6 Setup Email (for SMTP)

1. Dashboard → **"Email Accounts"**
2. Click **"Create Email"**
3. Create:
   - **Name:** `noreply@yourdomain.com`
   - **Password:** Strong password
4. Click **"Create"**
5. **Save credentials for SMTP:**
   - SMTP Host: `smtp.hostinger.com`
   - SMTP Port: `465` (SSL) or `587` (TLS)
   - Username: `noreply@yourdomain.com`
   - Password: Your email password

---

## 📂 Step 2: Deploy Code via SSH (Recommended)

### 2.1 Connect via SSH

```bash
# From your local machine:
ssh user@yourdomain.com
# Replace 'user' with your Hostinger SSH username

# If asked, confirm the host key: type 'yes'
# Enter your SSH password (or use key-based auth)

# You should now be connected to your server
# Prompt shows: user@hostinger-server:~$
```

### 2.2 Navigate to Web Root

```bash
# See current directory
pwd
# Should show: /home/user

# Change to web root
cd public_html
# Or if you created a subdirectory:
cd trouvemaalem

# Verify you're in the right place:
ls -la
# Should be empty or have Hostinger files
```

### 2.3 Clone Your Repository

```bash
# Clone from GitHub
git clone https://github.com/your-org/trouvemaalem.git .

# The dot (.) clones into current directory
# If that fails, do:
git clone https://github.com/your-org/trouvemaalem.git
cd trouvemaalem
```

### 2.4 Copy Environment File

```bash
# Copy example environment
cp .env.example .env

# Edit .env with production values:
nano .env

# Update these values:
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://yourdomain.com
# APP_KEY=base64:xyz...  (generate with: php artisan key:generate)
# DB_HOST=localhost
# DB_DATABASE=trouvemaalem
# DB_USERNAME=trouvemaalem_user
# DB_PASSWORD=your_strong_password
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.hostinger.com
# MAIL_PORT=587
# MAIL_USERNAME=noreply@yourdomain.com
# MAIL_PASSWORD=your_email_password

# Save: Press Ctrl+X, then Y, then Enter
```

### 2.5 Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# This takes 2-3 minutes
# Watch for: "Loading composer repositories..."
# Wait for: "✓ Successfully installed"

# Install Node dependencies
npm install

# Build assets
npm run build

# Both take a few minutes
```

### 2.6 Generate Application Key

```bash
# Generate new secure key
php artisan key:generate

# Should show: Application key set successfully
# Verify in .env: APP_KEY should be populated
```

### 2.7 Setup Database

```bash
# Run migrations (create tables)
php artisan migrate --force

# Seed sample data (optional, for testing)
php artisan db:seed --class=DatabaseSeeder

# Verify:
mysql -u trouvemaalem_user -p trouvemaalem -e "SELECT COUNT(*) FROM artisans;"
# Should show a number (count of artisans)
```

### 2.8 Set File Permissions

```bash
# Storage directory (for logs, cache)
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# .env file (make secure)
chmod 600 .env

# Verify:
ls -la | grep storage
# Should show: drwxr-xr-x
```

### 2.9 Configure Caching

```bash
# Create cache files
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Should show: [✓] Application config cached
# [✓] Routes cached
# [✓] Views cached
```

### 2.10 Clear Cache (Important!)

```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

---

## 📂 Step 3: Deploy Code via File Manager (Alternative)

**If SSH not available or uncomfortable with terminal:**

### 3.1 Prepare Local Files

On your computer:

```bash
# Navigate to project
cd trouvemaalem

# Create deployment package
# This creates a ZIP file ready to upload
mkdir -p deploy_package
cp -r app bootstrap config database resources routes storage tests vendor .env.production .gitignore deploy_package/
cd deploy_package
zip -r ../trouvemaalem.zip .
```

### 3.2 Upload via File Manager

1. In Hostinger Dashboard → **"File Manager"**
2. Navigate to `public_html`
3. Click **"Upload Files"**
4. Select `trouvemaalem.zip` (from your computer)
5. Wait for upload to complete
6. Right-click ZIP → **"Extract"**
7. Delete ZIP file

### 3.3 Install Dependencies (SSH Required)

Even with File Manager, you need SSH to run:

```bash
cd public_html/trouvemaalem
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
```

---

## 🔧 Step 4: Configure Public Directory

Hostinger serves files from `public_html`. Laravel's public folder needs to be the web root.

### Option A: Move Laravel Public Folder (Recommended)

```bash
# Copy Laravel public folder to public_html root
cp -r public/* ~/public_html/
cp public/.htaccess ~/public_html/  (if using Apache)

# Verify:
ls ~/public_html | grep index.php
# Should show: index.php
```

### Option B: Create Symlink

```bash
# Create symlink from public_html to public folder
cd ~/public_html
rm -rf index.php  (remove default Hostinger file)
ln -s ~/public_html/trouvemaalem/public/* .

# Verify:
ls -la | grep index.php
# Should show: index.php -> /path/to/public/index.php
```

### Option C: Use .htaccess Redirect

If you can't move files, create `.htaccess` in `public_html`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.html$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /trouvemaalem/public/index.php [L]
</IfModule>
```

---

## 🌐 Step 5: Point Domain to Your Site

### 5.1 In Hostinger Dashboard

1. Dashboard → **"Websites"**
2. Should show your domain already connected
3. If not, add domain:
   - Click **"Add Domain"**
   - Select your domain (or add new)
   - Connect to `public_html` folder

### 5.2 DNS Configuration (If Using External Domain Registrar)

If your domain is not at Hostinger:

1. Get Hostinger's **Nameservers:**
   - Dashboard → **"Domain"** → **"Nameservers"**
   - Copy nameservers (usually ns1.hostinger.com, ns2.hostinger.com)

2. Update at your domain registrar:
   - Login to where you bought the domain
   - Find "Nameservers" or "DNS"
   - Replace with Hostinger's nameservers
   - Wait 24-48 hours for propagation

### 5.3 Test Domain

```bash
# From terminal:
nslookup yourdomain.com
# Should show Hostinger's IP

# Or visit:
https://yourdomain.com
# Should show your Laravel home page
```

---

## ⚙️ Step 6: Configure Admin Settings

### 6.1 Configure reCAPTCHA

1. Go to your site: `https://yourdomain.com/admin`
2. Login: `admin@trouvemaalem.ma` / your new strong password
3. Go to: **Admin > Settings**
4. **reCAPTCHA Configuration:**
   - Get keys from: https://www.google.com/recaptcha/admin
   - Enter Site Key and Secret Key
   - Click "Test reCAPTCHA"
   - Should show: "Connection successful"

### 6.2 Configure Email (SMTP)

1. In Admin > Settings
2. **SMTP Configuration:**
   - SMTP Host: `smtp.hostinger.com`
   - SMTP Port: `587`
   - SMTP Username: `noreply@yourdomain.com`
   - SMTP Password: Your email password (from Step 1.6)
   - From Email: `noreply@yourdomain.com`
   - From Name: `trouvemaalem`
3. Click **"Test Email"**
4. Check your email inbox (or spam folder)
5. Should receive test email within 30 seconds

### 6.3 Configure Google Tag Manager (Optional)

1. Create GTM account: https://tagmanager.google.com
2. Create container for your domain
3. Copy Container ID (GTM-XXXXXX)
4. In Admin > Settings, paste into "Google Tag Manager ID"

---

## 🚀 Step 7: Verify Installation

### 7.1 Test Public Site

```bash
# Test from terminal:
curl -I https://yourdomain.com
# Should return: HTTP/2 200 or 301 redirect

# Or visit in browser:
https://yourdomain.com
# Should see: Home page with artisans list

# Test all 3 locales:
https://yourdomain.com/en/
https://yourdomain.com/fr/
https://yourdomain.com/ar/
# All should work without errors
```

### 7.2 Test Admin Panel

```bash
# Visit:
https://yourdomain.com/admin

# Login with:
Email: admin@trouvemaalem.ma
Password: Your new strong password

# Should see: Admin dashboard
# Verify all resources visible:
- Content > Artisans
- Content > Categories
- Moderation > Reviews
- Moderation > Contact Submissions
- Settings
```

### 7.3 Test Forms

```bash
# Test Contact Form:
https://yourdomain.com/en/contact
- Fill form
- Submit
- Should see: "Thank you, your message was received"
- Check admin panel: Form submission should appear

# Test Review Form:
https://yourdomain.com/en/artisan/[artisan-slug]
- Submit test review
- Should see: "Thank you, pending approval"
- Check admin panel: Review should be pending
```

### 7.4 Check Logs

```bash
# SSH into server:
ssh user@yourdomain.com
cd public_html/trouvemaalem

# View error logs:
tail -50 storage/logs/laravel.log

# Should be mostly empty (no errors)
# If errors exist, investigate and fix
```

---

## 🔧 Step 8: Setup Queue (For Email Sending)

Hostinger queues run via cronjobs.

### 8.1 Setup Cronjob in Hostinger

1. Dashboard → **"Cron Jobs"**
2. Click **"New Cron Job"**
3. Configure:
   - **Execute:** `/usr/bin/php /home/user/public_html/trouvemaalem/artisan queue:work --max-jobs=1000`
   - **Interval:** Every minute
   - **Active:** Yes
4. Click **"Create"**

### 8.2 Verify Queue Working

```bash
# SSH into server:
ssh user@yourdomain.com
cd public_html/trouvemaalem

# Check if queue is running:
ps aux | grep queue:work

# Or test manually:
php artisan queue:work --stop-when-empty
```

---

## 📧 Step 9: Test Email Delivery

### 9.1 Send Test Email

1. In Admin Panel > Settings
2. Click **"Test Email"**
3. Check your inbox (and spam folder)

### 9.2 If Email Not Arriving

**Check reCAPTCHA score first** (contact forms won't send emails if captcha fails)

```bash
# SSH into server:
ssh user@yourdomain.com
cd public_html/trouvemaalem

# Check mail logs:
tail -50 storage/logs/laravel.log | grep -i mail

# Verify SMTP credentials in .env:
cat .env | grep MAIL_

# Test manually:
php artisan tinker
> Mail::send('emails.test', [], function($m) { $m->to('your-email@example.com')->subject('Test'); });
```

**Common Issues:**
1. SMTP credentials wrong → Verify in admin settings
2. Email not whitelisted → Check Hostinger email settings
3. SPF/DKIM not configured → Add DNS records
4. reCAPTCHA failing → Contact form won't trigger email

---

## 🔒 Step 10: Security Hardening

### 10.1 Change Admin Password

**CRITICAL:** Change default password immediately!

1. Login to admin panel
2. Click user menu (top right)
3. **"Account Settings"** or **"Change Password"**
4. Enter old password: `admin123`
5. Enter new strong password (min 16 chars, mixed case, numbers, symbols)
6. Save
7. Logout and login with new password

### 10.2 Disable Directory Listing

SSH into server:

```bash
cd public_html

# Create .htaccess (if not exists)
cat > .htaccess << 'EOF'
<IfModule mod_autoindex.c>
    Options -Indexes
</IfModule>

# Hide sensitive files
<Files .env>
    Order allow,deny
    Deny from all
</Files>

<Files .gitignore>
    Order allow,deny
    Deny from all
</Files>
EOF
```

### 10.3 Set Correct Permissions

```bash
cd public_html/trouvemaalem

# Make directories writable
chmod 755 storage
chmod 755 bootstrap/cache

# Make .env secure
chmod 600 .env

# Make storage/logs writable (for error logs)
chmod 755 storage/logs
```

### 10.4 Enable HTTPS Redirect

Verify SSL is installed, then in Hostinger:

1. Dashboard → **"SSL/TLS"**
2. Should show: **"Active"** for Let's Encrypt
3. Check **"Auto-redirect HTTPS"**
4. This forces all HTTP → HTTPS

Verify:
```bash
curl -I http://yourdomain.com
# Should return: 301 Moved Permanently
# Location: https://yourdomain.com
```

---

## 📊 Step 11: Monitor & Maintain

### 11.1 Daily Tasks

```bash
# Check error logs:
ssh user@yourdomain.com
tail -50 public_html/trouvemaalem/storage/logs/laravel.log

# Monitor admin submissions:
# Login to /admin > Contact Submissions & Reviews
# Check for new submissions
# Respond to inquiries within 24 hours
```

### 11.2 Weekly Tasks

```bash
# Backup database
mysqldump -u trouvemaalem_user -p trouvemaalem > backup_$(date +%Y%m%d).sql

# Check error counts
grep ERROR storage/logs/laravel.log | wc -l
```

### 11.3 Monthly Tasks

```bash
# Test backup restoration
# Check Hostinger resource usage
# Review error logs for patterns
# Update dependencies (if needed)
# Test disaster recovery procedure
```

---

## 🆘 Troubleshooting for Hostinger

### Problem: "500 Internal Server Error"

```bash
# 1. Check error logs:
ssh user@yourdomain.com
tail -100 public_html/trouvemaalem/storage/logs/laravel.log

# 2. Check permissions:
ls -la public_html | grep trouvemaalem
# storage and bootstrap folders should have write permission

# 3. Clear caches:
cd public_html/trouvemaalem
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# 4. Check .env exists and is readable:
cat .env | grep APP_KEY
# Should show: APP_KEY=base64:xyz...
```

### Problem: "Database Connection Refused"

```bash
# 1. Verify credentials in .env:
cat .env | grep DB_

# 2. Test connection:
mysql -h localhost -u trouvemaalem_user -p trouvemaalem -e "SELECT 1;"
# If fails, reset password in Hostinger dashboard

# 3. Check MySQL is running:
ps aux | grep mysql

# 4. Verify database exists:
mysql -u root -e "SHOW DATABASES;" | grep trouvemaalem
```

### Problem: "Email Not Sending"

```bash
# 1. Test SMTP connection:
php artisan tinker
> Mail::raw('Test email', function($m) { 
    $m->to('your-email@example.com')->subject('Test'); 
  });

# 2. Check logs:
grep -i "mail\|swift" storage/logs/laravel.log

# 3. Verify credentials:
cat .env | grep MAIL_

# 4. Common issues:
- Wrong SMTP password
- Email account not verified in Hostinger
- reCAPTCHA failing (blocks form submission)
- Port 587 blocked by firewall (try 465)
```

### Problem: "Site Not Loading"

```bash
# 1. Test if server responding:
curl -I https://yourdomain.com

# 2. Check public_html/index.php exists:
ls -la public_html/index.php

# 3. Check PHP version:
php -v
# Should show: PHP 8.3+

# 4. Check if domain pointing correctly:
nslookup yourdomain.com
# Should show Hostinger IP
```

### Problem: "Admin Panel Login Not Working"

```bash
# 1. Reset admin password:
cd public_html/trouvemaalem
php artisan tinker
> User::first()->update(['password' => Hash::make('NewPassword123!')]);

# 2. Clear sessions:
mysql -u trouvemaalem_user -p trouvemaalem
> DELETE FROM sessions;

# 3. Check sessions table exists:
mysql -u trouvemaalem_user -p trouvemaalem -e "DESCRIBE sessions;"
```

---

## ✅ Post-Deployment Checklist

After successful deployment:

- [ ] Site loads at https://yourdomain.com
- [ ] All 3 locales work (en, fr, ar)
- [ ] Admin panel accessible at /admin
- [ ] Admin password changed from default
- [ ] Database credentials updated in .env
- [ ] SMTP email configured and tested
- [ ] reCAPTCHA configured and tested
- [ ] SSL certificate active (lock icon visible)
- [ ] Contact form submitting successfully
- [ ] Review form submitting successfully
- [ ] Backups created and tested
- [ ] Error logs monitored (no critical errors)
- [ ] File permissions set correctly (755, 600)

---

## 🎯 Final Verification

```bash
# SSH into server and run final checks:
ssh user@yourdomain.com
cd public_html/trouvemaalem

# 1. Check site health:
php artisan tinker
> app()->version()
# Should return: Laravel framework version

# 2. Check database:
> DB::connection()->getPdo()
# Should return: PDO object (connection OK)

# 3. Check migrations:
> Schema::hasTable('artisans')
# Should return: true

# 4. Count data:
> Artisan::count()
# Should return: number of seeded artisans
```

---

## 📞 Hostinger Support

If you encounter issues:

1. **Hostinger Support:** https://www.hostinger.com/help
2. **Live Chat:** Available 24/7
3. **Common Issues:** Check Hostinger's knowledge base
4. **SSH/Terminal:** Contact support if not available

---

## 🚀 You're Live!

Congratulations! Your trouvemaalem site is now running on Hostinger!

**Next Steps:**
1. Monitor error logs daily for first week
2. Test all admin functions thoroughly
3. Setup regular backups
4. Monitor user submissions (reviews, contacts)
5. Respond to contact inquiries promptly

---

**Last Updated:** May 10, 2026  
**Hosting:** Hostinger Business Plan  
**Framework:** Laravel 13 + Inertia.js  
**Status:** Production Deployment Ready ✅

For detailed troubleshooting, see TROUBLESHOOTING_GUIDE.md  
For admin operations, see ADMIN_USER_GUIDE.md
