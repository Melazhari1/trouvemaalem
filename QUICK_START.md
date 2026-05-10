# ⚡ QUICK START - trouvemaalem Development Setup

Get trouvemaalem running on your local machine in 5 minutes.

---

## Prerequisites

Before starting, ensure you have installed:

- **PHP 8.3+** (with mysql, curl extensions)
- **Node.js 18+** with npm
- **Composer** (latest version)
- **MySQL / MariaDB** (running)
- **Git** (for version control)

### Verify Installation

```bash
# PHP
php -v
# PHP 8.3.x (Zend Engine x.x)

# Node/npm
node -v && npm -v
# v18.x.x and 9.x.x

# Composer
composer -v
# Composer version 2.x.x

# Git
git -v
# git version 2.x.x

# MySQL (should be running)
mysql --version
# mysql  Ver 8.0.x or mariadb version x.x.x
```

---

## 1. Clone Repository

```bash
# Clone the repo
git clone https://github.com/your-org/trouvemaalem.git
cd trouvemaalem

# Or, if you already have a copy
cd /path/to/trouvemaalem
git pull origin main
```

---

## 2. Install Dependencies

```bash
# One-liner (recommended):
composer setup

# Or manually:
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
```

---

## 3. Configure Environment

Edit `.env` file:

```bash
nano .env
```

Key settings to check:

```
APP_DEBUG=true                    (Dev mode: true, Production: false)
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=trouvemaalem
DB_USERNAME=root
DB_PASSWORD=                      (leave empty if no password)

MAIL_MAILER=log                   (dev mode: emails logged, not sent)
```

---

## 4. Setup Database

```bash
# Create database (if not exists)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS trouvemaalem;"

# Run migrations and seed sample data
php artisan migrate --seed

# Verify
mysql -u root trouvemaalem -e "SELECT COUNT(*) as artisans FROM artisans;"
# Should output: artisans | X (number of seeded artisans)
```

---

## 5. Start Development Server

```bash
# Start all services at once (RECOMMENDED):
composer dev

# This runs 4 processes concurrently:
# ✓ Laravel dev server (http://localhost:8000)
# ✓ Queue listener (for jobs)
# ✓ Log tail (real-time logs)
# ✓ Vite dev server (asset bundling)

# Takes about 10 seconds to start up...
# Watch for: "Server running at http://127.0.0.1:8000"
```

**Or, start manually (one terminal per process):**

```bash
# Terminal 1: Laravel Server
php artisan serve

# Terminal 2: Queue Listener
php artisan queue:listen

# Terminal 3: Vite Dev Server
npm run dev

# Terminal 4: Log Tail (optional)
php artisan pail
```

---

## 6. Access the Application

Once running:

- **Homepage:** http://localhost:8000/fr (French by default)
- **Admin Panel:** http://localhost:8000/admin
  - Email: `admin@trouvemaalem.ma`
  - Password: `admin123` (local dev only!)

---

## Key Commands Cheat Sheet

```bash
# Development
composer dev                    # Start all 4 dev services
npm run dev                     # Start Vite dev server
php artisan serve               # Start Laravel server only

# Testing
composer test                   # Run full test suite
php artisan test --filter=Test  # Run specific test

# Database
php artisan migrate             # Run migrations
php artisan migrate:fresh --seed  # Reset + reseed (dev only!)
php artisan tinker              # Interactive shell

# Cache/Optimization (dev)
php artisan cache:clear         # Clear all caches
php artisan config:cache        # Cache config
php artisan view:cache          # Cache views

# Code Quality
vendor/bin/pint                 # Format code (Laravel Pint)
```

---

## Project Structure

```
trouvemaalem/
├── app/
│   ├── Models/                 ← Database models
│   │   ├── Artisan.php
│   │   ├── Review.php
│   │   ├── ContactSubmission.php
│   │   └── AdminSetting.php
│   ├── Http/Controllers/       ← Request handlers
│   │   ├── ArtisanController.php
│   │   ├── ReviewController.php
│   │   ├── ContactFormController.php
│   │   └── SearchController.php
│   ├── Filament/               ← Admin panel resources
│   │   └── Resources/
│   └── Providers/              ← Service providers
├── routes/
│   └── web.php                 ← Route definitions
├── resources/
│   ├── js/                     ← Vue 3 components
│   │   ├── Pages/              ← Page components
│   │   ├── Components/         ← Reusable components
│   │   └── Composables/        ← Vue composables
│   └── views/                  ← Blade templates
├── database/
│   ├── migrations/             ← Database schemas
│   ├── seeders/                ← Seed data
│   └── factories/              ← Fake data generators
├── lang/
│   ├── en/app.php              ← English translations
│   ├── fr/app.php              ← French translations
│   └── ar/app.php              ← Arabic translations
├── storage/
│   └── logs/                   ← Application logs (check for errors!)
├── tests/
│   ├── Feature/                ← Feature tests
│   └── Unit/                   ← Unit tests
├── CLAUDE.md                   ← Project documentation
├── .env.example                ← Environment template
├── .gitignore                  ← Files to ignore in git
├── composer.json               ← PHP dependencies
└── package.json                ← Node dependencies
```

---

## Common Development Tasks

### Add a New Page

1. Create Vue component: `resources/js/Pages/MyPage.vue`
2. Create controller method: `app/Http/Controllers/MyController.php`
3. Add route: `routes/web.php`
4. Test: Visit URL in browser

### Add a Database Field

1. Create migration: `php artisan make:migration add_field_to_table`
2. Edit migration file: `database/migrations/xxxx_add_field_to_table.php`
3. Run migration: `php artisan migrate`
4. Update model: `app/Models/MyModel.php`

### Add a Translation Key

1. Add to all language files:
   - `lang/en/app.php` → `'my_key' => 'English text'`
   - `lang/fr/app.php` → `'my_key' => 'Texte français'`
   - `lang/ar/app.php` → `'my_key' => 'النص العربي'`
2. Use in Vue: `{{ t('my_key') }}`
3. Use in Blade: `{{ __('app.my_key') }}`

### Debug an Issue

```bash
# 1. Check Laravel logs
tail -f storage/logs/laravel.log

# 2. Check browser console (F12)
# Look for JavaScript errors, network errors

# 3. Use Tinker (interactive shell)
php artisan tinker
> User::all()
> exit

# 4. Use Laravel Debugbar (if installed)
# Shows: SQL queries, timing, headers

# 5. Check database
mysql -u root trouvemaalem
> SELECT * FROM reviews LIMIT 1\G
```

---

## Testing

### Run All Tests

```bash
composer test
```

### Run Specific Test

```bash
php artisan test --filter=ReviewTest
```

### Create New Test

```bash
php artisan make:test MyFeatureTest
```

---

## Useful Development Extensions

### VS Code Extensions (Recommended)

- **Laravel Extra Intellisense** - Laravel code intelligence
- **Laravel Blade Snippets** - Blade template snippets
- **Vetur** - Vue.js support
- **PHP Intelephense** - PHP intellisense
- **MySQL** - MySQL client

### VS Code Settings for trouvemaalem

```json
{
  "[php]": {
    "editor.defaultFormatter": "DEVSENSE.phptools-vscode",
    "editor.formatOnSave": true
  },
  "[vue]": {
    "editor.defaultFormatter": "octref.vetur"
  },
  "files.exclude": {
    "node_modules": true,
    "vendor": true
  }
}
```

---

## Frontend Development Tips

### Tailwind CSS

Project uses **Tailwind v4** with Vite:

```bash
# Already configured, just use utility classes in Vue/Blade:
<div class="text-2xl font-bold text-blue-600 p-4">Hello</div>

# No separate CSS files needed (most cases)
# All styles in CSS files are processed by Tailwind
```

### Vue 3 Composition API

```vue
<script setup>
import { ref, computed } from 'vue'
import { useTranslations } from '@/Composables/useTranslations'

const { t, locale } = useTranslations()
const count = ref(0)
const doubled = computed(() => count.value * 2)
</script>

<template>
  <div>
    <p>{{ t('hello') }}</p>
    <p>Count: {{ count }}</p>
    <button @click="count++">Increment</button>
  </div>
</template>
```

### Inertia.js Props

Props passed from controller are automatically available:

```php
// Controller:
return Inertia::render('Page', [
    'artisan' => $artisan,
    'reviews' => $reviews
]);
```

```vue
<!-- Page.vue: -->
<script setup>
defineProps({
    artisan: Object,
    reviews: Array
})
</script>

<template>
  <div>{{ artisan.name }}</div>
</template>
```

---

## Database Tips

### Common Queries

```bash
php artisan tinker

# Get all artisans
App\Models\Artisan::all()

# Get artisans with reviews
App\Models\Artisan::with('reviews')->get()

# Get only approved reviews
App\Models\Review::approved()->get()

# Count pending reviews
App\Models\Review::pending()->count()

# Delete all pending reviews (dangerous!)
App\Models\Review::pending()->delete()

# Clear sessions
DB::table('sessions')->delete()
```

### Inspect Database

```bash
mysql -u root trouvemaalem

# List tables
SHOW TABLES;

# View table structure
DESCRIBE artisans;

# Sample data
SELECT * FROM artisans LIMIT 5;

# Count records
SELECT COUNT(*) FROM reviews;

# Export data
mysqldump trouvemaalem > backup.sql
```

---

## Troubleshooting

### "Port 8000 already in use"
```bash
# Find process using port 8000
lsof -i :8000

# Kill process
kill -9 <PID>

# Or use different port
php artisan serve --port=8001
```

### "Column doesn't exist" error
```bash
# A migration likely didn't run
php artisan migrate

# Check migration status
php artisan migrate:status
```

### "Cache driver not found"
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Restart Vite dev server
npm run dev
```

### "npm ERR! node-gyp rebuild"
```bash
# Clean install
rm -rf node_modules package-lock.json
npm install
```

### "Composer memory limit"
```bash
# Increase PHP memory limit
php -d memory_limit=-1 /usr/local/bin/composer install
```

---

## Next Steps

After getting it running locally:

1. **Read CLAUDE.md** - Understand architecture
2. **Explore Pages** - Look at `resources/js/Pages/`
3. **Check Models** - Understand data models in `app/Models/`
4. **Review Tests** - Check `tests/` for examples
5. **Try Adding Feature** - Create a simple new page
6. **Check Logs** - Always watch `storage/logs/laravel.log`

---

## Additional Resources

- **Laravel Docs:** https://laravel.com/docs
- **Vue.js Docs:** https://vuejs.org
- **Inertia.js Docs:** https://inertiajs.com
- **Tailwind CSS:** https://tailwindcss.com
- **Filament PHP:** https://filamentphp.com
- **Leaflet Maps:** https://leafletjs.com

---

## Quick Help

### "How do I...?"

**...change the default language?**
- Edit `.env`: `APP_LOCALE=fr` (default is French)

**...modify the navigation menu?**
- Edit `resources/js/Layouts/MainLayout.vue`

**...add a new model/migration?**
```bash
php artisan make:model MyModel -m  # Creates model + migration
```

**...run code on every request?**
- Add middleware in `bootstrap/app.php`

**...debug a slow query?**
```bash
# Enable query logging
# Check: storage/logs/laravel.log
# Look for queries taking > 1000ms
```

**...reset everything to clean slate?**
```bash
php artisan migrate:fresh --seed  # ⚠️ DELETES ALL DATA
```

---

## Git Workflow

```bash
# Create feature branch
git checkout -b feature/my-feature

# Make changes...
git status              # See what changed
git add .               # Stage changes
git commit -m "Add my feature"

# Push to GitHub
git push origin feature/my-feature

# Create Pull Request
# Get reviewed, merge to main
```

---

**You're all set! Happy coding! 🚀**

If stuck, check TROUBLESHOOTING_GUIDE.md or ask your team.
