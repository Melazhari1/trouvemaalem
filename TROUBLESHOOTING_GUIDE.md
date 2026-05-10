# 🆘 TROUBLESHOOTING GUIDE - trouvemaalem

Common issues and solutions for troubleshooting trouvemaalem in production.

---

## Quick Diagnosis Checklist

```bash
# 1. Check if site is up
curl -I https://yourdomain.com
# Should return: HTTP/2 200 or 301 redirect

# 2. Check error logs
tail -100 storage/logs/laravel.log

# 3. Check server resources
top  # CPU, RAM usage
df -h  # Disk space

# 4. Check database connection
mysql -h 127.0.0.1 -u root -p trouvemaalem -e "SELECT 1;"

# 5. Check if services are running
ps aux | grep php
ps aux | grep nginx  # or apache
ps aux | grep mysql
```

---

## CRITICAL ISSUES

### ❌ Site Not Loading (White Page / 500 Error)

**Symptoms:**
- Homepage shows blank page
- Error 500 Internal Server Error
- No content loading

**Quick Fix:**
```bash
# 1. Check error logs
tail -50 storage/logs/laravel.log
# Look for PHP errors, database connection errors, permission issues

# 2. Clear caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# 3. Check database connection
mysql -u root -p trouvemaalem -e "SELECT COUNT(*) FROM artisans;"
# Should return a number, not an error

# 4. Check file permissions
ls -la storage/ | head
# Should show: drwxrwxrwx (or similar permissions)
# If not: chmod -R 775 storage bootstrap/cache

# 5. Restart PHP-FPM (if using)
sudo systemctl restart php8.3-fpm

# 6. Check if .env exists
ls -la .env
# Should exist and contain database credentials
```

**If still not working:**
```bash
# Check Laravel app key
grep APP_KEY .env
# Should have a value like: base64:xyz...

# Regenerate key if empty
php artisan key:generate

# Check database migrations
php artisan migrate:status
# All should show: "Migrated" or "Batch"
```

---

### ❌ Database Connection Error

**Symptoms:**
- "could not find driver"
- "connection refused"
- "Access denied for user"
- "Unknown database"

**Solutions:**

**For "could not find driver":**
```bash
# Check if MySQL extensions installed
php -m | grep -i mysql
# Should show: mysqli, PDO, pdo_mysql

# If missing, install:
# Ubuntu/Debian:
sudo apt-get install php8.3-mysql

# Then restart PHP:
sudo systemctl restart php8.3-fpm
```

**For "connection refused" (127.0.0.1:3306):**
```bash
# Check if MySQL is running
sudo systemctl status mysql

# Start MySQL if stopped
sudo systemctl start mysql

# Check MySQL is listening on 3306
sudo netstat -plnt | grep 3306
# Should show: LISTEN 127.0.0.1:3306
```

**For "Access denied for user":**
```bash
# Verify credentials in .env
cat .env | grep DB_

# Test connection manually
mysql -h 127.0.0.1 -u root -p
# If fails, reset MySQL password:
sudo mysql -u root
> ALTER USER 'root'@'localhost' IDENTIFIED BY 'newpassword';
> FLUSH PRIVILEGES;
> EXIT;

# Update .env with new password
nano .env
# DB_PASSWORD=newpassword
```

**For "Unknown database":**
```bash
# Check if database exists
mysql -u root -p -e "SHOW DATABASES;"

# Create database if missing
mysql -u root -p -e "CREATE DATABASE trouvemaalem;"

# Run migrations
php artisan migrate --force
```

---

### ❌ Out of Disk Space

**Symptoms:**
- "No space left on device"
- Cannot write to storage/logs
- Upload fails
- Application crashes

**Solutions:**
```bash
# Check disk usage
df -h

# Find large directories
du -sh /*
du -sh /var/www/trouvemaalem/*

# Common culprits:
# storage/logs - Clear old logs
ls -lh storage/logs/
# Delete files older than 30 days:
find storage/logs -name "*.log" -mtime +30 -delete

# storage/app - Old file uploads
du -sh storage/app/
# Review and delete unused uploads

# Compressed assets
npm run build  # Regenerates minified files
# Remove old build artifacts

# Database backups (if stored locally)
du -sh backups/
# Move to cloud storage or delete old backups

# Clear Laravel cache
php artisan cache:clear
php artisan view:clear

# Verify disk space recovered
df -h
```

---

## EMAIL ISSUES

### ❌ Contact Form Emails Not Being Sent

**Symptoms:**
- Forms submit successfully but no email received
- Admin settings show SMTP configured
- Contact submissions saved but admin not notified

**Solutions:**

**Check SMTP Configuration:**
```bash
# Verify email settings in Admin Panel
# Go to: /admin/settings > SMTP Configuration

# Common issues:
1. SMTP Host wrong (should be: smtp.gmail.com, smtp.sendgrid.net, etc.)
2. SMTP Port wrong (usually 587 for TLS)
3. Password incorrect (Gmail: use app password, not account password)
4. From Email not verified in email provider
```

**Test Email Manually:**
```bash
# Test from command line
php artisan tinker

# Send test email
Mail::send('emails.test', [], function($message) {
    $message->to('admin@example.com')
            ->subject('Test Email')
            ->from('noreply@yourdomain.com');
});

# Or check logs
tail -f storage/logs/laravel.log | grep -i mail
```

**Check Email Logs:**
```bash
# If using log mailer (development)
cat storage/logs/laravel.log | grep -A5 "Message sent"

# Check for errors
grep -i "error" storage/logs/laravel.log | grep -i mail
```

**If using Gmail:**
```bash
# 1. Enable 2FA on Gmail account
# 2. Generate App Password (NOT account password)
# https://myaccount.google.com/apppasswords
# 3. Use in Admin Settings:
#    - SMTP Host: smtp.gmail.com
#    - SMTP Port: 587
#    - Username: your-email@gmail.com
#    - Password: [app-specific-password]
# 4. Click Test Email to verify
```

**If using SendGrid:**
```bash
# 1. Create SendGrid account
# 2. Generate API key
# 3. Configure in Admin Settings:
#    - SMTP Host: smtp.sendgrid.net
#    - SMTP Port: 587
#    - Username: apikey
#    - Password: SG.your_api_key_here
# 4. Click Test Email to verify
```

---

### ❌ Emails Going to Spam Folder

**Symptoms:**
- Emails arrive but in spam/junk folder
- SPF/DKIM issues
- Sender reputation low

**Solutions:**

**Set up Email Authentication:**

**SPF Record (in DNS):**
```
Type: TXT
Name: @
Value: v=spf1 include:sendgrid.net ~all
(adjust "sendgrid.net" based on your email provider)
```

**DKIM Record (from your email provider):**
```
Generated by your email provider (Gmail, SendGrid, etc.)
Add to DNS as TXT record
```

**DMARC Record (optional but recommended):**
```
Type: TXT
Name: _dmarc
Value: v=DMARC1; p=quarantine; rua=mailto:admin@yourdomain.com
```

**In Admin Settings:**
```
- Verify "From Email" matches verified domain
- Example: no-reply@yourdomain.com (not gmail.com)
- Make sure email provider allows sending from this address
```

**Warm up sending:**
- Start with low volume (10-20 emails/day)
- Gradually increase over 1-2 weeks
- Improves sender reputation

---

## FORM ISSUES

### ❌ Contact Form Not Submitting

**Symptoms:**
- Form appears to hang
- "Loading..." button never clears
- No success/error message
- Network error in browser console

**Solutions:**

**Check reCAPTCHA:**
```bash
# 1. Verify reCAPTCHA keys in Admin Settings
#    Should show: Site Key and Secret Key (filled in)

# 2. In browser, check page source for reCAPTCHA script:
grep -i recaptcha index.html
# Should load: https://www.google.com/recaptcha/api.js

# 3. Open browser console (F12 > Console tab)
# Should NOT show reCAPTCHA errors

# 4. If errors, verify:
#    - Domain registered in Google reCAPTCHA admin console
#    - Site key matches your domain
#    - reCAPTCHA v3 selected (not v2)
```

**Check Network:**
```bash
# In browser Developer Tools (F12 > Network tab):
# 1. Fill form and submit
# 2. Look for POST request to /api/contact/submit
# 3. Check response status:
#    - 200 = Success (form submitted)
#    - 422 = Validation failed (check error details)
#    - 429 = Rate limited (too many submissions)
#    - 500 = Server error (check logs)
```

**Check CORS:**
```bash
# If getting CORS error in browser console:
# Verify form submitting to correct domain
# Should be: https://yourdomain.com/api/contact/submit
# NOT: http://yourdomain.com (must be HTTPS)
```

**Check Form Validation:**
```bash
# Submit form with invalid data and check response
# In browser console:
# 1. Submit with blank email field
# 2. Should see validation error: "email must be valid"
# 3. Check that validation errors display on form

# If not displaying, check Vue component:
# resources/js/Components/ContactFormSubmit.vue
```

---

### ❌ Review Form Not Working

**Symptoms:**
- Review form doesn't appear on artisan page
- Submit button doesn't work
- Reviews not appearing in admin panel

**Solutions:**

**Check if ReviewFormSubmit component loads:**
```bash
# In browser console (F12):
# Should see the review form on artisan detail page
# If not, check:
# 1. Page loaded correctly (no 404)
# 2. Artisan exists in database
# 3. Resources/js/Components/ReviewFormSubmit.vue exists
```

**Check review submission:**
```bash
# 1. Submit test review
# 2. Check browser console for errors
# 3. Check Network tab for POST to /api/artisans/{id}/reviews/submit
# 4. Should get 200 response with "pending approval" message
# 5. Go to /admin/reviews to see pending review
```

**Check artisan ID:**
```bash
# Review endpoint needs correct artisan ID
# Should be: /api/artisans/{id}/reviews/submit
# Not: /api/artisans/{slug}/reviews/submit

# In database:
mysql -u root -p trouvemaalem
> SELECT id, name FROM artisans LIMIT 5;
# Use the ID, not the name/slug
```

---

## ADMIN PANEL ISSUES

### ❌ Cannot Login to Admin Panel

**Symptoms:**
- Admin login page won't authenticate
- "Invalid credentials" message
- Session not being created

**Solutions:**

**Verify Admin Account Exists:**
```bash
# In database:
mysql -u root -p trouvemaalem
> SELECT id, email, password FROM users;

# Should show admin user:
# Email: admin@trouvemaalem.ma
# Password: bcrypt hash (starts with $2y$)
```

**Reset Admin Password:**
```bash
# Via command line (Tinker):
php artisan tinker
> $user = App\Models\User::where('email', 'admin@trouvemaalem.ma')->first();
> $user->password = Hash::make('NewPassword123!');
> $user->save();
> exit;

# Then login with: admin@trouvemaalem.ma / NewPassword123!
```

**Check Session Configuration:**
```bash
# Verify in .env:
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Check sessions table exists:
mysql -u root -p trouvemaalem
> SELECT COUNT(*) FROM sessions;

# If table missing, run:
php artisan session:table
php artisan migrate
```

**Clear Authentication Cache:**
```bash
# If login still fails:
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Restart PHP-FPM:
sudo systemctl restart php8.3-fpm

# Try login again
```

---

### ❌ Admin Panel Very Slow / Timing Out

**Symptoms:**
- Admin pages take 10+ seconds to load
- "504 Gateway Timeout" error
- Bulk actions not completing

**Solutions:**

**Check Database Query Performance:**
```bash
# Enable query logging temporarily:
# In .env:
DB_LOG=true

# Check logs for slow queries:
grep "slow query" storage/logs/laravel.log

# Queries taking > 1 second are slow
# Optimize with database indexes:
mysql -u root -p trouvemaalem
> CREATE INDEX idx_reviews_status ON reviews(status);
> CREATE INDEX idx_contact_status ON contact_submissions(status);
> ANALYZE TABLE artisans, reviews, contact_submissions;
```

**Check Server Resources:**
```bash
# CPU Usage
top

# RAM Usage
free -h

# Disk I/O
iostat 1 5

# If any are maxed out (100%), that's the bottleneck
```

**Increase Timeouts (Temporary Fix):**
```bash
# In .env:
REQUEST_TIMEOUT=300  # 5 minutes

# Or in web server config (Nginx):
fastcgi_read_timeout 300;
fastcgi_connect_timeout 300;
```

**Optimize Admin Panel:**
```bash
# Clear caches
php artisan cache:clear

# Rebuild routes
php artisan route:cache

# Rebuild views
php artisan view:cache

# Optimize autoloader
composer dumpautoload -o
```

---

## SEARCH & MAP ISSUES

### ❌ Search Returns No Results

**Symptoms:**
- Homepage search returns empty
- Filter search not working
- Location-based search not working

**Solutions:**

**Verify Data Exists:**
```bash
# Check if artisans exist
mysql -u root -p trouvemaalem
> SELECT COUNT(*) FROM artisans;

# Check if they have required fields
> SELECT id, name, city, category_id FROM artisans WHERE city IS NOT NULL;

# If empty, seed data:
php artisan db:seed
```

**Check Search Query:**
```bash
# In SearchController, check:
# 1. Text search uses correct fields (name, bio, location)
# 2. Category filter uses correct joins
# 3. Rating filter uses correct aggregation

# Test search manually:
# https://yourdomain.com/en/search?search=plumber

# Should return artisans with "plumber" in name/bio/location
```

**Check Translations:**
```bash
# If searching in non-English locale:
# https://yourdomain.com/fr/search?search=plombier

# Verify data is translatable:
mysql -u root -p trouvemaalem
> SELECT name FROM artisans LIMIT 1\G

# Should show JSON: {"en":"...","fr":"...","ar":"..."}
```

---

### ❌ Map Not Loading or Shows Wrong Locations

**Symptoms:**
- Map doesn't appear on page
- Markers don't show
- Wrong coordinates shown
- Console errors about Leaflet

**Solutions:**

**Check Map Data API:**
```bash
# Verify endpoint returns data:
curl https://yourdomain.com/en/api/map-data | jq .

# Should return JSON array of artisans with lat/lng
# If empty, check:
# 1. Artisans exist in database
# 2. Have lat/lng coordinates set
# 3. Are not soft-deleted
```

**Check Leaflet Library:**
```bash
# In browser console (F12):
# Type: L.map
# Should return a function (Leaflet is loaded)

# If error "L is not defined":
# Check that Leaflet script is loaded from CDN
# Search for: https://unpkg.com/leaflet
# Should be in page <head> section
```

**Verify Coordinates:**
```bash
# In database, check artisan coordinates:
mysql -u root -p trouvemaalem
> SELECT id, name, lat, lng FROM artisans WHERE lat IS NOT NULL;

# Should show valid coordinates (Casablanca area):
# lat: ~33.5 (around 33)
# lng: ~-7.6 (around -8)

# If coordinates wrong (0, 0 or null):
# Update via admin panel or manually:
> UPDATE artisans SET lat=33.5731, lng=-7.5898 WHERE id=1;
```

---

## PERFORMANCE ISSUES

### ❌ Site Slow / High Load Time

**Symptoms:**
- Pages take > 3 seconds to load
- CPU usage high (> 80%)
- Memory usage high (> 80%)

**Solutions:**

**Identify Slow Pages:**
```bash
# Check Laravel logs for slow requests:
grep "Processed in" storage/logs/laravel.log | tail -20

# Requests > 1000ms are slow
# Example: [2026-05-10 12:34:56] ... Processed in 2500ms

# Focus optimization efforts on slowest pages
```

**Enable Caching:**
```bash
# In .env:
CACHE_STORE=redis  # Much faster than database/file

# If Redis not available:
CACHE_STORE=file

# Cache everything:
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Optimize Database:**
```bash
# Add indexes:
mysql -u root -p trouvemaalem
> CREATE INDEX idx_artisan_city ON artisans(city);
> CREATE INDEX idx_artisan_category ON artisans(category_id);
> CREATE INDEX idx_reviews_artisan ON reviews(artisan_id);
> CREATE INDEX idx_reviews_status ON reviews(status);
> ANALYZE TABLE artisans, reviews, contact_submissions;
```

**Optimize Assets:**
```bash
# Production build (minified, optimized)
npm run build

# Check asset sizes:
ls -lh public/build/assets/

# Should be < 500KB total for CSS + JS
```

**Enable Asset Compression:**
```nginx
# In Nginx config:
gzip on;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss;
gzip_min_length 1000;
```

---

## SECURITY ISSUES

### ❌ Too Many Failed Login Attempts

**Symptoms:**
- Admin account locked
- Cannot login after multiple failed attempts
- "Too many login attempts" message

**Solutions:**

**Check Admin Account Status:**
```bash
# In database:
mysql -u root -p trouvemaalem
> SELECT email, locked, locked_at FROM users;

# If locked:
> UPDATE users SET locked=NULL, locked_at=NULL WHERE email='admin@trouvemaalem.ma';
```

**Reset Admin Password:**
```bash
# Use Tinker:
php artisan tinker
> $user = User::where('email', 'admin@trouvemaalem.ma')->first();
> $user->password = Hash::make('NewSecurePassword123!');
> $user->update();

# Then try logging in again
```

**Check for Brute Force Attacks:**
```bash
# Monitor login attempts:
grep "invalid login\|failed login" storage/logs/laravel.log

# If many failed attempts from same IP:
# 1. Block IP at firewall level
# 2. Consider implementing 2FA
# 3. Change admin email/password
```

---

### ❌ Spam Submissions (Contact Form / Reviews)

**Symptoms:**
- Hundreds of spam contact submissions
- Fake reviews appearing
- reCAPTCHA not blocking bots

**Solutions:**

**Check reCAPTCHA Score:**
```bash
# Legitimate users: score > 0.5
# Bots: score < 0.5 (should be rejected)

# If spam still getting through:
# 1. Verify reCAPTCHA secret key is correct
# 2. Check that secret key matches site key domain
# 3. Verify v3 is selected (not v2)
```

**Bulk Delete Spam:**
```bash
# In admin panel:
# 1. Go to /admin/contact-submissions or /admin/reviews
# 2. Sort by recent first
# 3. Identify spam pattern (keywords, email domains)
# 4. Bulk select spam submissions
# 5. Delete selected

# Or manually in database:
mysql -u root -p trouvemaalem
> DELETE FROM contact_submissions WHERE email LIKE '%spam-domain.com%';
> DELETE FROM reviews WHERE comment LIKE '%viagra%' OR comment LIKE '%casino%';
```

**Strengthen Rate Limiting:**
```bash
# In .env (if configurable):
RATE_LIMIT_CONTACT=5,60        # 5 per hour
RATE_LIMIT_REVIEW=1,1440       # 1 per day per email

# For more aggressive limiting, modify in Controller:
# ReviewController: add email-based rate limiting
# ContactFormController: add honeypot field (hidden field)
```

---

## BACKUP & RECOVERY

### ❌ Backup Not Working

**Symptoms:**
- Backup script fails
- No backup files being created
- Old backup files not being deleted

**Solutions:**

**Manual Backup:**
```bash
# Create database backup:
mysqldump -h 127.0.0.1 -u root -p trouvemaalem > /var/backups/trouvemaalem_$(date +%Y%m%d_%H%M%S).sql

# Verify backup created:
ls -lh /var/backups/trouvemaalem_*.sql

# Upload to cloud storage (AWS S3, Google Drive, etc.)
```

**Automated Backup (using Laravel):**
```bash
# Install backup package:
composer require spatie/laravel-backup

# Publish config:
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"

# Schedule in app/Console/Kernel.php:
$schedule->command('backup:run')->daily()->at('02:00');

# Test backup:
php artisan backup:run

# Verify backup created:
ls -lh storage/app/backups/
```

**Test Restore:**
```bash
# 1. Create test database:
mysql -u root -p -e "CREATE DATABASE trouvemaalem_restore_test;"

# 2. Restore from backup:
mysql -u root -p trouvemaalem_restore_test < /var/backups/trouvemaalem_20260510.sql

# 3. Verify data restored:
mysql -u root -p trouvemaalem_restore_test -e "SELECT COUNT(*) FROM artisans;"

# 4. Drop test database:
mysql -u root -p -e "DROP DATABASE trouvemaalem_restore_test;"
```

---

## SSL / HTTPS ISSUES

### ❌ SSL Certificate Error

**Symptoms:**
- Browser shows "Not Secure"
- "Invalid Certificate" error
- Mixed content warning (HTTP + HTTPS)

**Solutions:**

**Verify Certificate:**
```bash
# Check certificate validity:
openssl s_client -connect yourdomain.com:443 -showcerts

# Check expiration date:
openssl s_client -connect yourdomain.com:443 | openssl x509 -noout -dates

# Should show:
# notBefore=...
# notAfter=... (should be future date)
```

**Renew Certificate (Let's Encrypt):**
```bash
# If using Certbot:
sudo certbot renew

# Force renewal if needed:
sudo certbot renew --force-renewal

# Auto-renew:
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

**Fix Mixed Content:**
```bash
# If seeing "mixed content" warning:
# Some assets are loading over HTTP instead of HTTPS

# In .env:
APP_URL=https://yourdomain.com  (must be HTTPS)

# Clear cache:
php artisan config:cache

# Check assets in HTML:
# All CSS/JS/images should load from https:// URLs
```

---

## Getting Help

### When You're Stuck:

1. **Check error logs first:**
   ```bash
   tail -100 storage/logs/laravel.log
   ```

2. **Check browser console (F12):**
   - Look for JavaScript errors
   - Look for network errors
   - Check for CORS errors

3. **Check server resources:**
   ```bash
   top
   df -h
   free -h
   ```

4. **Search this guide:**
   - Symptom + "issue" (e.g., "form not submitting")
   - Error message (e.g., "connection refused")

5. **Search Laravel docs:**
   - https://laravel.com/docs
   - https://laravel.com/docs/troubleshooting

6. **Search error message online:**
   - Google the exact error message
   - Check Stack Overflow

---

**Last Updated:** May 10, 2026  
**Framework:** Laravel 13  
**Version:** 1.0

Still stuck? Check CLAUDE.md or contact your hosting provider.
