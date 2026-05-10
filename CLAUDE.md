# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Laravel 13 + Inertia.js + Vue 3 directory site ("trouvemaalem") that lists local artisans/workers in Morocco. Users browse categories, search by service + city, and view artisan profiles on a Leaflet map. **MySQL** (MariaDB via WAMP64) is the database. Tailwind CSS v4 is wired through the Vite plugin — no `tailwind.config.js`, configuration lives in CSS.

## Commands

Composer scripts ([composer.json](composer.json)) drive most workflows:

- `composer setup` — first-time install: install deps, copy `.env`, generate key, migrate, build assets.
- `composer dev` — runs four processes concurrently (server, queue listener, `pail` log tail, Vite dev server). This is the canonical local-dev entrypoint.
- `composer test` — clears config cache then runs `php artisan test` (PHPUnit 12). Use `php artisan test --filter=TestName` for a single test.
- `npm run dev` / `npm run build` — Vite dev / production build (only needed if you don't run `composer dev`).
- `php artisan migrate` / `php artisan migrate:fresh --seed` — apply migrations / reset and reseed.
- `vendor/bin/pint` — Laravel Pint formatter (dev dependency, no preconfigured composer alias).

PHPUnit's [phpunit.xml](phpunit.xml) forces `DB_CONNECTION=sqlite` with `DB_DATABASE=:memory:` for tests, so the MySQL database is never touched by the suite.

## Database

- **Driver**: MySQL / MariaDB (WAMP64 local stack)
- **Database**: `trouvemaalem`, user `root`, no password, host `127.0.0.1:3306`
- **MariaDB key-length fix**: `AppServiceProvider::boot()` calls `Builder::defaultStringLength(191)` — required for utf8mb4 on MariaDB. Do not remove it.
- `.env` is committed in this working copy (not for real deployments) — treat as developer-local config.

## Architecture

### Localization is URL-driven and shapes routing

Every user-facing URL is prefixed with a locale segment: `/en/...`, `/fr/...`, `/ar/...`. The **default locale is French (`fr`)**. The contract is enforced in three places that must stay in sync:

1. [routes/web.php](routes/web.php) wraps all page routes in `Route::prefix('{locale}')->where(['locale' => 'en|fr|ar'])`. `/` redirects to `/fr`, and a `Route::fallback` redirects any non-prefixed path to `/fr/<path>`.
2. [app/Http/Middleware/SetLocale.php](app/Http/Middleware/SetLocale.php) reads `$request->segment(1)`, calls `App::setLocale()`, calls `URL::defaults(['locale' => $locale])` so all `route()` helpers auto-include the locale, then `forgetParameter('locale')` so controllers don't receive it as an argument. **Controller signatures intentionally omit `$locale`** — don't add it.
3. [app/Http/Middleware/HandleInertiaRequests.php](app/Http/Middleware/HandleInertiaRequests.php) shares `locale`, `translations` (from `lang/{locale}/app.php` via `__('app')`), and `recaptchaSiteKey` (from `AdminSetting`) as Inertia props on every response.

On the Vue side, [resources/js/Composables/useTranslations.js](resources/js/Composables/useTranslations.js) exposes `t(key, replacements)`, `locale`, and `isRtl` (true for `ar`). The root [resources/views/app.blade.php](resources/views/app.blade.php) sets `<html dir>` based on locale.

When adding a new translatable string: add the key to all three of `lang/en/app.php`, `lang/fr/app.php`, `lang/ar/app.php`, then call `t('key')` in Vue.

### Inertia + Vue 3 SPA shell

- Single Blade entry ([resources/views/app.blade.php](resources/views/app.blade.php)) renders `@inertia` and uses `@vite` with a per-page chunk hint: `resources/js/Pages/{$page['component']}.vue`.
- [resources/js/app.js](resources/js/app.js) wires `createInertiaApp` with glob-based page resolution from `./Pages/**/*.vue`. Document title template is ``${title} - trouvemaalem``.
- Pages live under [resources/js/Pages/](resources/js/Pages/) and are referenced by the controllers via `Inertia::render('Folder/Name', [...])` — keep folder casing aligned with these strings.
- Shared layout: [resources/js/Layouts/MainLayout.vue](resources/js/Layouts/MainLayout.vue). Reusable UI in [resources/js/Components/UI/](resources/js/Components/UI/) and map components ([LeafletMap.vue](resources/js/Components/LeafletMap.vue), [WorkerMap.vue](resources/js/Components/WorkerMap.vue)) using `@vue-leaflet/vue-leaflet` + `leaflet.markercluster`.

### Domain model

| Model | File | Notes |
|-------|------|-------|
| `Artisan` | [app/Models/Artisan.php](app/Models/Artisan.php) | Core entity. `average_rating` accessor counts **only approved reviews** |
| `Category` | [app/Models/Category.php](app/Models/Category.php) | Translatable |
| `Review` | [app/Models/Review.php](app/Models/Review.php) | Has `status` (`pending`/`approved`/`rejected`), `admin_notes`, `submitted_by_name`, `submitted_by_email`; scopes: `approved()`, `pending()` |
| `ContactSubmission` | [app/Models/ContactSubmission.php](app/Models/ContactSubmission.php) | Contact form submissions; status `new`/`read`/`replied`; scopes: `unread()`, `recent()` |
| `AdminSetting` | [app/Models/AdminSetting.php](app/Models/AdminSetting.php) | Key-value store for runtime configuration; `AdminSetting::get($key, $default)` / `AdminSetting::set($key, $value)` |
| `Post` | [app/Models/Post.php](app/Models/Post.php) | Blog |
| `Faq` | [app/Models/Faq.php](app/Models/Faq.php) | FAQ |
| `User` | [app/Models/User.php](app/Models/User.php) | Auth |

### Multilingual content (Spatie Translatable)

All content models use `spatie/laravel-translatable` v6 with **MySQL JSON columns** storing all three locales as `{"en":"...","fr":"...","ar":"..."}`.

| Model | Translatable fields |
|-------|-------------------|
| `Artisan` | `name`, `bio`, `location` |
| `Category` | `name`, `description` |
| `Post` | `title`, `excerpt`, `content` |
| `Faq` | `question`, `answer` |

**Critical pattern — all four translatable models implement the same three overrides:**

1. **Virtual getters** (`getNameEnAttribute()`, etc.) — allow Filament to read each locale individually via `$model->name_en`.
2. **`fill()` override** — intercepts keys like `name_en` and routes them to `setTranslation('name', 'en', ...)`. This is how Filament saves translated values.
3. **`attributesToArray()` override** — replaces each translatable field's JSON object with the single current-locale string. Essential for Inertia props — Spatie's implicit `'array'` cast would otherwise serialize JSON objects to the frontend.

Vue pages receive plain locale strings — `artisan.name`, `category.name`, etc. — never locale-keyed objects.

### Review system — moderation flow

All reviews go through admin moderation:

- **Guest submissions** via `POST /{locale}/api/artisans/{id}/reviews/submit` ([ReviewController](app/Http/Controllers/ReviewController.php)) store with `status = 'pending'`.
- **Admin panel** ([ReviewResource](app/Filament/Resources/Reviews/ReviewResource.php)) shows pending reviews first; bulk actions **Approve** / **Reject** / **Delete**; edit form has `admin_notes` field.
- **`ArtisanController::show`** loads only `approved` reviews via `->reviews' => fn($q) => $q->approved()`.
- **`Artisan::getAverageRatingAttribute()`** calls `$this->reviews()->approved()->avg('rating')` — unreviewed ratings never affect the displayed score.
- **`Artisans/Show.vue`** renders the `<ReviewFormSubmit>` component below the reviews list.

### Contact form system

- **Guest submissions** via `POST /{locale}/api/contact/submit` ([ContactFormController](app/Http/Controllers/ContactFormController.php)) store to `contact_submissions` table with `status = 'new'`.
- Rate-limited: `throttle:5,60` (5 per IP per hour) on the route.
- reCAPTCHA v3 validated using `AdminSetting::get('recaptcha_secret_key')` — validation is **silently skipped** if the key is not configured.
- **Admin panel** ([ContactSubmissionResource](app/Filament/Resources/ContactSubmissions/ContactSubmissionResource.php)) lists all submissions; bulk actions **Mark Read** / **Mark Replied** / **Delete**; edit view lets admin change status.
- **`Contact.vue`** renders `<ContactFormSubmit />` — no direct form logic in the page.

### Admin Settings (`AdminSetting` model)

Key-value configuration table seeded with 12 rows by the `2026_05_10_300000_create_admin_settings_table` migration. **Do not re-seed manually.**

| Key | Type | Purpose |
|-----|------|---------|
| `recaptcha_site_key` | string | reCAPTCHA v3 public key (sent to frontend via Inertia) |
| `recaptcha_secret_key` | string | reCAPTCHA v3 private key (used server-side) |
| `smtp_host` | string | SMTP server hostname |
| `smtp_port` | string | SMTP port (default `587`) |
| `smtp_username` | string | SMTP login |
| `smtp_password` | string | SMTP password |
| `mail_from_address` | string | Outgoing from address |
| `mail_from_name` | string | Outgoing from name (default `trouvemaalem`) |
| `google_tag_manager_id` | string | GTM container ID (e.g. `GTM-XXXXXX`) |
| `contact_notification_emails` | json | JSON array of emails to notify on new contact submissions |
| `site_title` | string | Optional site title override |
| `site_description` | string | Optional site description override |

The Filament Settings page is at `/admin/settings` — a custom `Page` class ([SettingsPage.php](app/Filament/Pages/SettingsPage.php)) using `form(Schema $schema)` + `content(Schema $schema)` + `$this->form->fill()` / `$this->form->getState()` (Filament v4 schema pattern). The Blade view ([resources/views/filament/pages/settings-page.blade.php](resources/views/filament/pages/settings-page.blade.php)) is just `{{ $this->content }}`.

### Search: SQL-driven Haversine + slug-pattern URLs

[SearchController.php](app/Http/Controllers/SearchController.php) is the centerpiece. Two entrypoints share one query builder:

- `index($request)` accepts `search`, `category_id`, `min_rating`, `lat`/`lng`/`distance`, `verified`. Text search uses `JSON_UNQUOTE(JSON_EXTRACT(name, '$."locale"')) LIKE ?` with current locale and EN fallback. Distance filtering uses inline Haversine `selectRaw`/`whereRaw` and orders by distance when geo params are present.
- `localSearch($request, $locale, $service, $city)` is bound to `/{locale}/{service}-in-{city}`. It looks up the category by slug, merges params, then delegates to `index`. **When changing search filters, update both paths together.**

`min_rating` filters via a correlated subquery against `reviews`. The listing hydrates `withAvg('reviews', 'rating')` (exposed as `reviews_avg_rating`).

### Map data + JSON-LD SEO

- `/{locale}/api/map-data` ([ArtisanController::mapData](app/Http/Controllers/ArtisanController.php)) returns a slimmed plain array. Uses model hydration + explicit `map()` — **do not replace with raw `select()`** (returns raw JSON blobs).
- `ArtisanController::show` loads only approved reviews, builds a `LocalBusiness` schema.org payload, passes it as `schema` prop to `Artisans/Show.vue`. Country hardcoded `MA`.
- `/sitemap.xml` is intentionally **not** locale-prefixed.

### Back Office (Filament v4)

The admin panel is at `/admin`. Powered by **FilamentPHP v4** (v3 is incompatible with Laravel 13).

- Panel config: [AdminPanelProvider.php](app/Providers/Filament/AdminPanelProvider.php) — color `Amber`, path `admin`, login route enabled.
- Resources auto-discovered from `app/Filament/Resources/`. Pages auto-discovered from `app/Filament/Pages/`.
- Each resource lives in its own subdirectory with `Schemas/` (form) and `Tables/` (table) classes using static `configure()` methods.

**All Filament resources:**

| Resource | Navigation Group | Notes |
|----------|-----------------|-------|
| `ArtisanResource` | Content | Full CRUD with translatable tabs |
| `CategoryResource` | Content | Translatable |
| `PostResource` | Content | Blog posts, translatable |
| `FaqResource` | Content | FAQs, translatable |
| `ReviewResource` | Moderation | Bulk approve/reject; status badge; admin notes |
| `ContactSubmissionResource` | Moderation | Read-only details; bulk mark-read/replied |

**Custom admin pages:**

| Page | Path | Purpose |
|------|------|---------|
| `SettingsPage` | `/admin/settings` | Runtime config: reCAPTCHA, SMTP, GTM, notification emails, branding |

**Filament v4 API rules:**
- Layout components: `Filament\Schemas\Components\Section`, `Tabs`, `Tab`, `Form`, `EmbeddedSchema`, `Actions`
- Input components: `Filament\Forms\Components\TextInput`, `Textarea`, `Select`, etc.
- Actions: `Filament\Actions\EditAction`, `DeleteBulkAction`, `BulkAction`, etc.
- `IconColumn::make()->boolean()` — `BooleanColumn` was removed in v4
- **Custom pages with forms** use `form(Schema $schema): Schema` + `content(Schema $schema): Schema` with `Form::make([EmbeddedSchema::make('form')])`. Call `$this->form->fill($data)` in `mount()` and `$this->form->getState()` in the save method.
- The `$view` property on custom pages is **instance** (`protected string $view`), not static.
- `$navigationGroup` type: `string|\UnitEnum|null`. `$navigationIcon` type: `string|BackedEnum|null`.
- **Category `->options()` in Select fields**: use `Category::all()->mapWithKeys(fn($c) => [$c->id => $c->name])` — `->pluck('name','id')` returns raw JSON strings.
- **No Spatie Filament plugin** (only supports v3). Translations handled manually via `Tabs` with one tab per locale. Arabic fields get `->extraAttributes(['dir' => 'rtl', 'class' => 'text-right'])`.

#### Admin locale switching

- [SetAdminLocale.php](app/Http/Middleware/SetAdminLocale.php) reads `session('admin_locale', 'fr')` and sets `App::setLocale()`.
- [Admin/LocaleController.php](app/Http/Controllers/Admin/LocaleController.php) handles `GET /admin/locale/{locale}`.
- Language switcher items (EN/FR/AR) in the Filament user menu via `AdminPanelProvider`.

### Vue components

**Form components (new):**

| Component | File | Purpose |
|-----------|------|---------|
| `ContactFormSubmit` | [resources/js/Components/ContactFormSubmit.vue](resources/js/Components/ContactFormSubmit.vue) | Contact form with validation, reCAPTCHA v3, loading state, error/success messages. Posts to `/{locale}/api/contact/submit` via axios. |
| `ReviewFormSubmit` | [resources/js/Components/ReviewFormSubmit.vue](resources/js/Components/ReviewFormSubmit.vue) | Review form with star picker, char counter, reCAPTCHA v3. Posts to `/{locale}/api/artisans/{id}/reviews/submit`. Shows "pending approval" notice. |

Both components read `page.props.recaptchaSiteKey` (shared by `HandleInertiaRequests`) and call `window.grecaptcha.execute()` before submitting. If the site key is empty, reCAPTCHA is skipped silently.

### Middleware registration

Middleware is registered in [bootstrap/app.php](bootstrap/app.php) (Laravel 11+ style — no `app/Http/Kernel.php`). `SetLocale` and `HandleInertiaRequests` are appended to the `web` group. `SetAdminLocale` is on the Filament panel middleware stack.

### Error pages

Custom error pages at [resources/views/errors/](resources/views/errors/):
- [minimal.blade.php](resources/views/errors/minimal.blade.php) — branded base layout (logo, gradient code, back-to-home button)
- [404.blade.php](resources/views/errors/404.blade.php) — extends minimal
- [500.blade.php](resources/views/errors/500.blade.php) — extends minimal

## Migrations

| Migration | What it does |
|-----------|-------------|
| `add_coordinates_to_artisans_table` | Adds `lat`/`lng` |
| `add_rating_city_to_artisans_table` | Adds `rating`, `city` |
| `add_is_verified_to_artisans_table` | Adds `is_verified boolean` |
| `make_*_translatable` (×4) | Drops varchar/text columns, re-adds as MySQL JSON |
| `2026_05_10_100000_create_contact_submissions_table` | `contact_submissions` table |
| `2026_05_10_200000_modify_reviews_table` | Adds `status`, `admin_notes`, `submitted_by_name`, `submitted_by_email` to `reviews` |
| `2026_05_10_300000_create_admin_settings_table` | `admin_settings` key-value table; seeds 12 default rows |

The alter migrations (`add_*`, `modify_*`) must not be squashed — they change existing tables that may already have data.

## Routes (public-facing)

All routes below are inside `Route::prefix('{locale}')->where(['locale' => 'en|fr|ar'])`:

| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/` | `HomeController@index` | `home` |
| GET | `/faq` | `FaqController@index` | `faq.index` |
| GET | `/about` | `PageController@about` | `about` |
| GET | `/contact` | `PageController@contact` | `contact` |
| GET | `/blog` | `PostController@index` | `blog.index` |
| GET | `/blog/{slug}` | `PostController@show` | `blog.show` |
| GET | `/categories` | `CategoryController@index` | `categories.index` |
| GET | `/categories/{slug}` | `CategoryController@show` | `categories.show` |
| GET | `/artisan/{slug}` | `ArtisanController@show` | `artisan.show` |
| GET | `/search` | `SearchController@index` | `search` |
| GET | `/{service}-in-{city}` | `SearchController@localSearch` | `search.local` |
| GET | `/api/map-data` | `ArtisanController@mapData` | `api.map-data` |
| POST | `/api/contact/submit` | `ContactFormController@submit` | `api.contact.submit` |
| POST | `/api/artisans/{id}/reviews/submit` | `ReviewController@submit` | `api.review.submit` |

The two POST routes are rate-limited: contact `throttle:5,60`, reviews `throttle:10,60`.

## Seed data

`php artisan migrate:fresh --seed` creates:
- Admin user: `admin@trouvemaalem.ma` / `admin123`
- 5 regular users, 6 categories, 12 artisans — all with EN/FR/AR content
- 15 FAQs, 3 blog posts — all trilingual
- ~22 reviews spread across artisans (all seeded with `status = 'pending'` after the 2026-05-10 migration)

After a fresh seed, go to `/admin/reviews` and bulk-approve the seeded reviews so they appear on artisan pages.

## Conventions worth knowing

- The brand name is `trouvemaalem`. Do not change it casually.
- `test_route.php` at the repo root is a scratch/debug file, not autoloaded.
- Default locale is **`fr`** — the root `/` redirect and `APP_LOCALE` both point to `fr`.
- `HomeController` must use `Category::withCount('artisans')->get()` (not `Category::all()`).
- `average_rating` on `Artisan` counts only `approved` reviews — do not change the scope filter.
- `AdminSetting` rows are created by the migration. Use `AdminSetting::set()` to update values; do not insert rows manually to avoid duplicates.
- reCAPTCHA v3 is entirely optional at development time. Leave `recaptcha_secret_key` empty in AdminSettings and all form submissions pass through without captcha validation.

## Production Deployment Checklist

### Environment Setup
1. Set `APP_ENV=production`, `APP_DEBUG=false`
2. Set `APP_LOCALE=fr` (default language)
3. Change `APP_URL` to your production domain (`https://`)
4. Set `DB_PASSWORD` to a strong password
5. Enable `SESSION_ENCRYPT=true`
6. Set `MAIL_MAILER=smtp`
7. Set `LOG_LEVEL=warning`

### Admin Settings to Configure Before Launch
Navigate to `/admin/settings` and configure:
1. **reCAPTCHA Keys** — Get from https://www.google.com/recaptcha/admin (v3). Both site key and secret key required.
2. **SMTP Configuration** — Email server credentials. Gmail: `smtp.gmail.com:587`, SendGrid: `smtp.sendgrid.net:587`, AWS SES: `email-smtp.{region}.amazonaws.com:587`.
3. **Google Tag Manager ID** — Optional. Format: `GTM-XXXXXX`.
4. **Contact Notification Emails** — Optional. One email per line. Stored as JSON array.

### After Deployment
- Run `php artisan migrate` (never `migrate:fresh` on production).
- Run `php artisan config:cache` and `php artisan route:cache`.
- Bulk-approve any seeded reviews via `/admin/reviews` if a fresh seed was run.

## SSL/HTTPS Configuration

**Essential for production:**
- Use Let's Encrypt (free, automatic renewal)
- Configure web server (Nginx/Apache) with SSL certificate
- Update `APP_URL` to `https://` in production `.env`
- Enable secure session flag: `SESSION_SECURE_COOKIES=true` in production
- Set HSTS header in web server config:
  ```
  Strict-Transport-Security: max-age=31536000; includeSubDomains
  ```

**Verify HTTPS is working:**
```bash
curl -I https://yourdomain.com
# Should return HTTP/2 200 or 301 redirect
```

## Database Backup Strategy

**Critical for business continuity:**

**Automated Backups:**
- Daily backups to cloud storage (AWS S3, Google Drive, Backblaze)
- Retention: Keep at least 7-day rolling backup
- Test restore procedure monthly

**Manual Backup Command:**
```bash
mysqldump -h 127.0.0.1 -u root -p trouvemaalem > backup-$(date +%Y%m%d).sql
```

**Recommended Tool:**
- Laravel Backup Package: `spatie/laravel-backup`
- Installation: `composer require spatie/laravel-backup`
- Scheduled: Daily at 2 AM
- Stores to S3 or other cloud storage

**Restore Backup:**
```bash
mysql -h 127.0.0.1 -u root -p trouvemaalem < backup-20260510.sql
```

## Email Configuration Testing

**Before going live, verify email delivery:**

1. Go to `/admin/settings`
2. Configure SMTP credentials:
   - SMTP Host: `smtp.gmail.com` (or your provider)
   - SMTP Port: `587` (TLS)
   - Username: Your email account
   - Password: App-specific password (Gmail requires this)
   - From Email: `no-reply@yourdomain.com`
   - From Name: `trouvemaalem`
3. Click "Test Email" button
4. Verify email reaches admin inbox (check spam folder)
5. Verify: From address, From name, Subject are correct
6. Test with different locales (EN/FR/AR subjects)

**Gmail Setup (Recommended for MVP):**
1. Enable 2-factor authentication
2. Go to https://myaccount.google.com/apppasswords
3. Select Mail → Windows Computer
4. Generate app-specific password
5. Use this password in SMTP_PASSWORD (not your account password)

**SendGrid Setup (Production Recommended):**
1. Create account at https://sendgrid.com
2. Create API key
3. SMTP Host: `smtp.sendgrid.net`
4. Username: `apikey`
5. Password: `SG.xxxxxxxxxxxxxxxx`

## reCAPTCHA v3 Configuration

**Spam protection for forms:**

1. Visit: https://www.google.com/recaptcha/admin
2. Create new site:
   - Name: `trouvemaalem`
   - Type: reCAPTCHA v3
   - Domains: `yourdomain.com` (add production domain)
3. Copy **Site Key** and **Secret Key**
4. Add to Admin Settings > reCAPTCHA Configuration
5. Test forms with submissions to verify bot protection

**How v3 works:**
- Invisible to users (no checkbox)
- Returns score 0.0-1.0 (0=bot, 1=human)
- Threshold: Accept if score > 0.5
- No user friction, better UX

## Production Performance Optimization

**Caching:**
```bash
# Cache configuration and routes (huge speed boost)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Change in production `.env`:
```
CACHE_STORE=redis  # or 'file' if Redis unavailable
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

**Database Optimization:**
```sql
-- Add indexes for common queries
CREATE INDEX idx_reviews_status ON reviews(status);
CREATE INDEX idx_reviews_created ON reviews(created_at DESC);
CREATE INDEX idx_contact_status ON contact_submissions(status);
CREATE INDEX idx_artisan_city ON artisans(city);
CREATE INDEX idx_artisan_category ON artisans(category_id);

-- Analyze table statistics
ANALYZE TABLE artisans, reviews, contact_submissions;
```

**Email Queue (Prevent Page Delays):**
```bash
# Set QUEUE_CONNECTION=database in .env
# Start queue worker (run continuously on production)
php artisan queue:work --daemon

# Or use supervisor to monitor process
```

**Assets:**
```bash
# Production build (minified, tree-shaken)
npm run build

# Enable Gzip on web server (nginx.conf or .htaccess)
# Reduces asset size by 70%
```

**Logging:**
```
LOG_LEVEL=warning    # Only errors, not debug
LOG_STACK=daily      # Rotate logs daily
```

## Security Headers Configuration

**Prevent common web attacks:**

**Nginx Configuration:**
```nginx
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;
```

**Apache Configuration (.htaccess):**
```apache
Header always set X-Content-Type-Options "nosniff"
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
```

**Or use Laravel CSP Package:**
```bash
composer require spatie/laravel-csp
php artisan vendor:publish --provider="Spatie\Csp\CspServiceProvider"
```

## Monitoring & Error Tracking

**Catch production errors in real-time:**

**Sentry (Recommended for MVP):**
- Website: https://sentry.io
- Free tier: 5,000 events/month
- Real-time alerts, error grouping, release tracking
- Installation:
  ```bash
  composer require sentry/sentry-laravel
  php artisan sentry:publish
  ```
- Set `SENTRY_LARAVEL_DSN` in `.env`

**Alternative Options:**
- **Rollbar** - Error tracking, free tier available
- **Bugsnag** - Real-time error monitoring
- **New Relic** - Full APM (paid, comprehensive)
- **DataDog** - Monitoring + analytics (paid)

**Manual Monitoring (Free):**
```bash
# Monitor logs in real-time
tail -f storage/logs/laravel.log

# Check error count
grep ERROR storage/logs/laravel.log | wc -l
```

## Deployment Procedure

### Initial Deployment (First Time)

**1. Prepare Server:**
```bash
# SSH into production server
ssh user@your-server-ip

# Navigate to web directory
cd /var/www

# Clone repository
git clone https://github.com/your-org/trouvemaalem.git
cd trouvemaalem
```

**2. Install Dependencies:**
```bash
# Composer dependencies (no dev packages in production)
composer install --no-dev --optimize-autoloader

# NPM dependencies and build
npm install
npm run build
```

**3. Configure Environment:**
```bash
# Copy production environment file
cp .env.production .env

# Generate encryption key
php artisan key:generate

# Verify .env is correct (database, mail, etc.)
nano .env
```

**4. Database Setup:**
```bash
# Run migrations (NOT migrate:fresh on production!)
php artisan migrate

# Seed admin user if first time
php artisan db:seed --class=AdminUserSeeder  # If seeder exists
```

**5. Permissions & Caching:**
```bash
# Set correct ownership
chown -R www-data:www-data /var/www/trouvemaalem
chmod -R 775 storage bootstrap/cache

# Clear and cache everything
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
```

**6. Verify Installation:**
```bash
# Test URL
curl https://yourdomain.com

# Check logs for errors
tail -f storage/logs/laravel.log

# Verify admin login works
# Navigate to https://yourdomain.com/admin
```

### Rolling Updates (Code Changes)

**With zero downtime:**

```bash
# Pull latest code
git pull origin main

# Update dependencies (if changed)
composer install --no-dev --optimize-autoloader

# Rebuild assets (if changed)
npm install && npm run build

# Run migrations if any
php artisan migrate

# Clear caches
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache

# Verify
curl https://yourdomain.com
tail -f storage/logs/laravel.log
```

### Deployment Rollback Plan

**If something goes wrong:**

```bash
# Revert to previous code version
git revert HEAD  # OR git checkout previous-commit-hash

# Restore database from backup
mysql -u root -p trouvemaalem < backup-20260510.sql

# Clear caches and restart
php artisan cache:clear
php artisan queue:restart

# Verify
curl https://yourdomain.com
```

## Quick Reference Guide

### Admin Panel Tasks

**Add New Artisan:**
1. Go to `/admin/artisans`
2. Click "New Artisan"
3. Fill in all three language tabs (EN, FR, AR)
4. Upload photo
5. Set coordinates (latitude, longitude)
6. Mark as verified if applicable
7. Save

**Moderate Reviews:**
1. Go to `/admin/reviews`
2. Filter by Status = "Pending"
3. Select reviews to approve
4. Click "Approve Selected" bulk action
5. Or click individual review to add admin notes before approving

**Respond to Contact Inquiries:**
1. Go to `/admin/contact-submissions`
2. Sort by "New" status
3. Click on submission to read full message
4. Send email reply directly to visitor
5. Mark as "Replied" when done
6. Archive (bulk action: Delete) if spam

**Configure Settings:**
1. Go to `/admin/settings`
2. Sections:
   - reCAPTCHA: Add keys from Google
   - SMTP Email: Configure mail server
   - GTM: Add Google Tag Manager ID (optional)
   - Contact Emails: Add notification recipients
   - Branding: Set site title/description
3. Click "Test Email" to verify SMTP works
4. Save

### Important Contacts
- Admin Email: `admin@trouvemaalem.ma`
- Support Email: Your email address
- Hosting: Your hosting provider
- Domain Registrar: Your registrar

### Server SSH Access
```bash
# SSH into server
ssh user@your-server-ip

# Navigate to project
cd /var/www/trouvemaalem

# View logs
tail -f storage/logs/laravel.log

# Restart queue
php artisan queue:restart

# Clear caches
php artisan cache:clear
```

## Conventions Worth Knowing (Updated)

- The brand name is `trouvemaalem`. Do not change it casually.
- `test_route.php` at the repo root is a scratch/debug file, not autoloaded.
- Default locale is **`fr`** — the root `/` redirect and `APP_LOCALE` both point to `fr`.
- `HomeController` must use `Category::withCount('artisans')->get()` (not `Category::all()`).
- `average_rating` on `Artisan` counts only `approved` reviews — do not change the scope filter.
- `AdminSetting` rows are created by the migration. Use `AdminSetting::set()` to update values; do not insert rows manually to avoid duplicates.
- reCAPTCHA v3 is entirely optional at development time. Leave `recaptcha_secret_key` empty in AdminSettings and all form submissions pass through without captcha validation.
- **NEVER commit `.env` or real credentials to git** — use `.env.example` as template.
- **NEVER run `migrate:fresh` on production** — only use `migrate` for new migrations.
- **ALWAYS test migrations on staging before production**.
- **ALWAYS keep database backups** — test restore procedure monthly.
- **CHANGE DEFAULT ADMIN PASSWORD** before deployment — `admin123` is not secure.
