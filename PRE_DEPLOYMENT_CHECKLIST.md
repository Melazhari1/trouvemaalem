# 📋 PRE-DEPLOYMENT CHECKLIST - trouvemaalem

**Project:** trouvemaalem (Laravel 13 Artisan Directory)  
**Deployment Date:** [DATE TO BE FILLED]  
**Deployed By:** [YOUR NAME]  
**Environment:** Production

---

## ⚡ CRITICAL (48 Hours Before Launch)

### Security & Credentials
- [ ] **Change default admin password** from `admin123` to strong password (min 16 chars, mixed case, numbers, symbols)
  - Command: Go to `/admin` → click user icon → Change Password
  - Save new credentials in secure location (password manager)
- [ ] **Verify `.env` is NOT committed to git**
  - Run: `git log --oneline -- .env` (should be empty)
  - Verify `.gitignore` contains `.env`
- [ ] **Generate new APP_KEY** on production server
  - Command: `php artisan key:generate`
- [ ] **Remove all debug code and console.logs**
  - Search for: `console.log`, `dd()`, `dump()`, `var_dump()`
  - Remove from all files
- [ ] **Set `APP_DEBUG=false`** in production `.env`
- [ ] **Verify database password is strong** (not default empty password)
- [ ] **Remove test/dummy admin accounts** (keep only production admin)
- [ ] **Check `.env` for hardcoded secrets**
  - Database password ✓
  - SMTP credentials ✓
  - reCAPTCHA secret key ✓
  - Any API keys ✓

### SSL/HTTPS Certificate
- [ ] **SSL certificate is valid and installed**
  - Test: `https://yourdomain.com`
  - Browser should show secure lock icon, no warnings
  - Check cert expiry: `openssl s_client -connect yourdomain.com:443 -showcerts`
- [ ] **HSTS header configured** (force HTTPS)
  - Test: `curl -I https://yourdomain.com | grep Strict-Transport`
- [ ] **Redirect HTTP to HTTPS works**
  - Test: `curl -I http://yourdomain.com` (should redirect to https)

### Configuration Files
- [ ] **Review `.env` production configuration:**
  ```
  APP_ENV=production          ✓
  APP_DEBUG=false             ✓
  APP_URL=https://yourdomain  ✓
  APP_LOCALE=fr               ✓
  DB_PASSWORD=<strong>        ✓
  MAIL_MAILER=smtp            ✓
  CACHE_STORE=redis or file   ✓
  SESSION_ENCRYPT=true        ✓
  LOG_LEVEL=warning           ✓
  ```
- [ ] **Verify `.env.example` does NOT contain real credentials**
- [ ] **Confirm database connection parameters**
  - Host, port, database name, username, password (strong)
- [ ] **Confirm mail SMTP configuration**
  - Provider: Gmail, SendGrid, AWS SES, etc.
  - Credentials correct
  - From address valid

---

## 🔧 CONFIGURATION (24 Hours Before)

### Admin Settings Panel
- [ ] **Navigate to `/admin/settings`**
- [ ] **reCAPTCHA Configuration:**
  - [ ] Site Key: `6L...` (from Google reCAPTCHA admin)
  - [ ] Secret Key: `6L...` (hidden, from Google)
  - [ ] Save → should show success message
- [ ] **SMTP Email Configuration:**
  - [ ] SMTP Host: `smtp.gmail.com` (or your provider)
  - [ ] SMTP Port: `587`
  - [ ] SMTP Username: `your-email@gmail.com`
  - [ ] SMTP Password: `app-specific-password` (not your account password)
  - [ ] From Email: `no-reply@yourdomain.com`
  - [ ] From Name: `trouvemaalem`
  - [ ] Click "Test Email" button
  - [ ] Verify test email received in inbox (check spam folder)
  - [ ] Save
- [ ] **Google Tag Manager (Optional):**
  - [ ] If using GTM, enter Container ID: `GTM-XXXXXX`
  - [ ] Save
- [ ] **Branding (Optional):**
  - [ ] Site Title: `trouvemaalem` or custom
  - [ ] Site Description: SEO description
  - [ ] Save

### Email Provider Setup (if Gmail)
- [ ] **Enable 2-Factor Authentication** on Gmail account
- [ ] **Generate App Password:**
  - Go to https://myaccount.google.com/apppasswords
  - Select Mail → Windows Computer
  - Generate password
  - Copy to SMTP_PASSWORD
- [ ] **Test email sending** from admin settings

### Email Provider Setup (if SendGrid)
- [ ] **Create SendGrid account:** https://sendgrid.com
- [ ] **Create API Key** in settings
- [ ] **Configure in Admin Settings:**
  - SMTP Host: `smtp.sendgrid.net`
  - Port: `587`
  - Username: `apikey`
  - Password: `SG.xxxxxxxxxxxx`

### Database Backup
- [ ] **Initial backup created before going live:**
  ```bash
  mysqldump -h 127.0.0.1 -u root -p trouvemaalem > backup-pre-launch.sql
  ```
- [ ] **Backup uploaded to cloud storage** (AWS S3, Google Drive, etc.)
- [ ] **Backup location documented:**
  - Location: `________________________`
  - Frequency: Daily, 2 AM
  - Retention: 7-day rolling
- [ ] **Tested restore procedure** (simulate restoring from backup)

### Error Tracking Setup (Optional but Recommended)
- [ ] **Sentry account created:** https://sentry.io
- [ ] **SENTRY_LARAVEL_DSN** added to `.env`
- [ ] **Integration verified** (errors show up in Sentry dashboard)

---

## 🧪 TESTING (12 Hours Before)

### Forms Testing
- [ ] **Contact Form Submission:**
  - [ ] Navigate to `/en/contact`
  - [ ] Fill in all fields (name, email, subject, message)
  - [ ] Submit form
  - [ ] Verify: Success message appears
  - [ ] Verify: Submission appears in `/admin/contact-submissions`
  - [ ] Check submission details: name, email, message correct
  - [ ] Test all 3 locales (EN, FR, AR)

- [ ] **Review Form Submission:**
  - [ ] Navigate to any artisan page: `/en/artisan/[slug]`
  - [ ] Scroll to review section
  - [ ] Submit review (1 star, short text)
  - [ ] Verify: "Your review is pending approval" message
  - [ ] Verify: Review appears in `/admin/reviews` with status "pending"
  - [ ] Test all 3 locales

- [ ] **reCAPTCHA Validation:**
  - [ ] Submit contact form with fake/bot-like pattern
  - [ ] reCAPTCHA should block if score < 0.5 (optional, depends on config)
  - [ ] Real user submission should succeed

- [ ] **Rate Limiting:**
  - [ ] Try submitting contact form 6 times in 60 seconds
  - [ ] Should be blocked on 6th attempt: "429 Too Many Requests"
  - [ ] Same for reviews (max 1 per email per day)

- [ ] **Form Validation:**
  - [ ] Submit contact form with empty fields
  - [ ] Should show validation errors
  - [ ] Submit with invalid email
  - [ ] Should show email validation error
  - [ ] Submit with message < 10 chars
  - [ ] Should show length validation error

### Admin Panel Testing
- [ ] **Admin Login:**
  - [ ] Navigate to `https://yourdomain.com/admin`
  - [ ] Login with NEW admin password (not default)
  - [ ] Verify: Dashboard loads without errors

- [ ] **Contact Submissions Resource:**
  - [ ] Go to `/admin/contact-submissions`
  - [ ] Verify: All test submissions visible
  - [ ] Click on submission, view details
  - [ ] Bulk select submissions → Mark as Read
  - [ ] Verify: Status changed to "read"
  - [ ] Bulk select submissions → Delete
  - [ ] Verify: Submissions removed

- [ ] **Reviews Resource:**
  - [ ] Go to `/admin/reviews`
  - [ ] Filter by Status = "Pending"
  - [ ] Verify: Only pending reviews show
  - [ ] Select pending review → Click "Approve"
  - [ ] Verify: Status changed to "approved"
  - [ ] Go to artisan page, verify review is now visible
  - [ ] Go back to admin, select approved review → Click "Reject"
  - [ ] Verify: Status changed to "rejected"
  - [ ] Go to artisan page, verify review is hidden
  - [ ] Test bulk approve: Select multiple pending → "Approve Selected"

- [ ] **Settings Page:**
  - [ ] Go to `/admin/settings`
  - [ ] Verify: All sections load (reCAPTCHA, Email, GTM, etc.)
  - [ ] Click "Test Email" button
  - [ ] Verify: Email received within 30 seconds
  - [ ] Try changing a setting (e.g., From Name)
  - [ ] Click Save
  - [ ] Verify: Change saved successfully
  - [ ] Page should not have errors

- [ ] **Artisans Resource:**
  - [ ] Go to `/admin/artisans`
  - [ ] Verify: All artisans listed
  - [ ] Click on one artisan to view
  - [ ] Verify: All fields display correctly
  - [ ] Check translations (EN, FR, AR tabs)

### Frontend Testing
- [ ] **Homepage (`/en`):**
  - [ ] Page loads without errors
  - [ ] All sections visible
  - [ ] Search functionality works
  - [ ] Maps load correctly
  - [ ] No JavaScript errors (check console)

- [ ] **Search Page (`/en/search`):**
  - [ ] Search by keyword (plumber, electrician, etc.)
  - [ ] Search results appear
  - [ ] Filter by category works
  - [ ] Filter by rating works
  - [ ] Filter by distance works
  - [ ] No N+1 database queries (check logs)

- [ ] **Category Pages (`/en/categories`):**
  - [ ] Browse categories
  - [ ] Click category to view artisans
  - [ ] Artisans load correctly
  - [ ] Ratings display properly

- [ ] **Artisan Detail Pages (`/en/artisan/[slug]`):**
  - [ ] Load artisan profile
  - [ ] Map shows correct location
  - [ ] Only APPROVED reviews display
  - [ ] Average rating calculated from approved reviews only
  - [ ] Review form visible at bottom
  - [ ] Contact form accessible

- [ ] **Blog Pages (`/en/blog`):**
  - [ ] Blog index loads
  - [ ] Blog posts list
  - [ ] Click post to view
  - [ ] Post content displays

- [ ] **FAQ Pages (`/en/faq`):**
  - [ ] FAQ page loads
  - [ ] All questions visible
  - [ ] Expandable answers work

- [ ] **Multilingual Testing:**
  - [ ] Test each locale: `/en`, `/fr`, `/ar`
  - [ ] Language switcher works
  - [ ] All content translates correctly
  - [ ] RTL layout works for Arabic
  - [ ] Maps load in all locales

- [ ] **Mobile Responsiveness:**
  - [ ] Test on mobile devices (iOS, Android)
  - [ ] Test on tablets
  - [ ] Test on desktop
  - [ ] All pages responsive
  - [ ] Forms easy to use on mobile
  - [ ] Navigation accessible

- [ ] **Error Pages:**
  - [ ] Trigger 404 (visit `/en/nonexistent`)
  - [ ] Verify: Branded 404 page appears
  - [ ] Back to home link works
  - [ ] Trigger 500 (optional, if test route available)
  - [ ] Verify: Branded 500 page appears

### Database Testing
- [ ] **Database Migrations Ran Successfully:**
  - [ ] Run: `php artisan migrate --force` (if not already done)
  - [ ] No errors in logs
  - [ ] All tables created
  - [ ] Foreign keys intact

- [ ] **Data Integrity:**
  - [ ] All artisans visible in admin
  - [ ] All categories visible
  - [ ] All seeded reviews have status='pending'
  - [ ] Average ratings calculated correctly
  - [ ] No orphaned records (artisans without categories, etc.)

- [ ] **Backup/Restore Test:**
  - [ ] Create backup: `mysqldump -u root -p trouvemaalem > test-restore.sql`
  - [ ] Drop database: `DROP DATABASE trouvemaalem;`
  - [ ] Restore from backup: `mysql -u root -p trouvemaalem < test-restore.sql`
  - [ ] Verify: All data restored correctly
  - [ ] Verify: Application still works after restore

### Performance Testing
- [ ] **Page Load Speed:**
  - [ ] Homepage loads in < 2 seconds
  - [ ] Search page loads in < 1 second
  - [ ] Artisan detail loads in < 2 seconds
  - [ ] Admin pages load in < 3 seconds

- [ ] **Concurrent Users:**
  - [ ] Use Apache Bench or similar: `ab -n 100 -c 10 https://yourdomain.com`
  - [ ] Should handle 100 concurrent requests without errors
  - [ ] No timeout errors
  - [ ] Server stays responsive

- [ ] **Database Query Performance:**
  - [ ] Enable query logging: `DB_LOG=true` in `.env` (temporary)
  - [ ] Run tests above
  - [ ] Check `storage/logs/` for slow queries (> 1 second)
  - [ ] Optimize slow queries if found

### Security Testing
- [ ] **HTTPS Enforcement:**
  - [ ] Visit `http://yourdomain.com` (without S)
  - [ ] Should redirect to `https://yourdomain.com`
  - [ ] Verify: SSL certificate valid, no warnings

- [ ] **Security Headers Present:**
  - [ ] Run: `curl -I https://yourdomain.com`
  - [ ] Should show headers: X-Content-Type-Options, X-Frame-Options, etc.

- [ ] **reCAPTCHA Working:**
  - [ ] Visit contact form
  - [ ] Check page source for reCAPTCHA script
  - [ ] Submit form with valid captcha
  - [ ] Should succeed

- [ ] **Password Hashing:**
  - [ ] Check admin password in database: `SELECT password FROM users WHERE email='admin@...'`
  - [ ] Should be bcrypt hash (starts with `$2y$`)
  - [ ] NOT plain text

- [ ] **Session Security:**
  - [ ] LOGIN to admin
  - [ ] Check `SESSION_ENCRYPT=true` in `.env`
  - [ ] Session cookies should be HttpOnly
  - [ ] Verify: `Set-Cookie: LARAVEL_SESSION=...; HttpOnly; Secure`

---

## 🚀 DEPLOYMENT DAY (Actual Deployment)

### Pre-Deployment (30 mins before)
- [ ] **Final backup of production database**
  ```bash
  mysqldump -u root -p trouvemaalem > backup-pre-deployment-$(date +%Y%m%d_%H%M%S).sql
  ```
- [ ] **Notify stakeholders** that deployment is starting
- [ ] **Have rollback plan ready** (documented)
- [ ] **Team on standby** if issues arise

### Deployment Steps
- [ ] **Pull latest code:**
  ```bash
  cd /var/www/trouvemaalem
  git pull origin main
  ```
- [ ] **Install dependencies:**
  ```bash
  composer install --no-dev --optimize-autoloader
  npm install && npm run build
  ```
- [ ] **Run migrations:**
  ```bash
  php artisan migrate --force
  ```
- [ ] **Cache everything:**
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```
- [ ] **Verify application:**
  ```bash
  curl https://yourdomain.com
  tail -f storage/logs/laravel.log
  ```
- [ ] **Restart queue worker (if applicable):**
  ```bash
  php artisan queue:restart
  ```

### Post-Deployment (First 30 mins)
- [ ] **Monitor error logs:**
  - [ ] `tail -f storage/logs/laravel.log`
  - [ ] Should show minimal/no errors
- [ ] **Test critical paths:**
  - [ ] Homepage loads
  - [ ] Search works
  - [ ] Contact form submits
  - [ ] Admin panel accessible
  - [ ] Artisan pages load
- [ ] **Check server resources:**
  - [ ] CPU usage normal
  - [ ] Memory usage normal
  - [ ] Disk space available
  - [ ] Database connections healthy
- [ ] **Notify stakeholders** that deployment is complete
- [ ] **Monitor for 24 hours** continuously

---

## ✅ POST-LAUNCH (First Week)

### Daily Monitoring
- [ ] **Check error logs every morning:**
  ```bash
  tail -100 storage/logs/laravel.log
  ```
- [ ] **Monitor server resources:**
  - [ ] CPU usage < 80%
  - [ ] Memory usage < 80%
  - [ ] Disk space > 10% free
- [ ] **Email delivery verification:**
  - [ ] Test contact form email
  - [ ] Verify delivery time < 1 minute
  - [ ] Check spam folder
- [ ] **User feedback:**
  - [ ] Any reported issues?
  - [ ] Slow pages?
  - [ ] Broken links?

### Weekly Tasks
- [ ] **Review analytics:**
  - [ ] Unique visitors
  - [ ] Page views
  - [ ] Popular artisans
  - [ ] Search terms used
- [ ] **Review contact submissions:**
  - [ ] Any spam?
  - [ ] Legitimate inquiries being received?
- [ ] **Review moderated content:**
  - [ ] Pending reviews processed?
  - [ ] Any abuse patterns?
- [ ] **Database maintenance:**
  - [ ] Backup completed successfully
  - [ ] Database size normal
  - [ ] No replication lag (if applicable)

### Monthly Tasks
- [ ] **Test disaster recovery:**
  - [ ] Restore from backup to test server
  - [ ] Verify full restoration
  - [ ] Document any issues
- [ ] **Review security:**
  - [ ] Check for failed login attempts
  - [ ] Review admin access logs
  - [ ] Verify SSL certificate renewal (if auto-renewal enabled)
- [ ] **Performance review:**
  - [ ] Slowest pages?
  - [ ] Database optimization opportunities?
  - [ ] Update dependencies if needed

---

## 🆘 Rollback Procedure (If Critical Issues)

**If critical issues prevent site from functioning:**

```bash
# 1. Revert code to previous version
cd /var/www/trouvemaalem
git log --oneline  # Find previous commit hash
git checkout <previous-commit-hash>

# 2. Restore database backup
mysqldump -u root -p trouvemaalem > current-broken.sql
mysql -u root -p trouvemaalem < backup-pre-deployment.sql

# 3. Clear caches
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache

# 4. Restart queue
php artisan queue:restart

# 5. Verify
curl https://yourdomain.com
tail -f storage/logs/laravel.log

# 6. Notify stakeholders
# "Rollback complete, investigating issue"
```

---

## 📞 Support Contacts

| Role | Contact | Availability |
|------|---------|--------------|
| Admin | admin@trouvemaalem.ma | During business hours |
| Tech Support | [YOUR EMAIL] | 24/7 critical issues |
| Hosting | [HOSTING PROVIDER] | Support portal |
| Domain | [DOMAIN REGISTRAR] | Support portal |

---

## 📝 Sign-Off

- Deploying by: _________________ Date: _________
- Verified by: _________________ Date: _________
- Approved by: _________________ Date: _________

**Notes/Issues Found:**
```
[Space for notes about any issues encountered]




```

---

**Keep this checklist for future deployments. Update as needed based on lessons learned.**
