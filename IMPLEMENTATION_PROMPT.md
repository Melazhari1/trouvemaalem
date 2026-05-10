# 🚀 IMPLEMENTATION PROMPT - Critical Features for Production Deployment

**PROJECT**: trouvemaalem (Laravel 13 + Inertia.js + Vue 3 + Filament v4)  
**OBJECTIVE**: Implement 5 critical features to make the site ready for public deployment

---

## ⚠️ KEY REQUIREMENTS

- ✅ No payment processing
- ✅ Admin-only communication (admin decides who gets contacted)
- ✅ All reviews pending until admin approves
- ✅ All contact form submissions stored for admin review
- ✅ Admin can configure: reCAPTCHA, Email (SMTP), Google Tag Manager, notification emails
- ✅ Bulk approve/reject actions in admin panel
- ✅ No automatic emails/notifications - admin checks BO manually
- ✅ No chat system between visitors and artisans

---

## FEATURE 1: CONTACT FORM SYSTEM

### 1.1 Database Migration
Create file: `database/migrations/2026_05_10_create_contact_submissions_table.php`

Table: `contact_submissions`
- `id` (primary key, unsigned big integer)
- `name` (string, required)
- `email` (string, required)
- `subject` (string, required)
- `message` (longText, required)
- `status` (enum: 'new', 'read', 'replied', default: 'new')
- `ip_address` (string, nullable)
- `timestamps` (created_at, updated_at)
- Indexes: email, status, created_at

### 1.2 Create Model
File: `app/Models/ContactSubmission.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    protected $fillable = ['name', 'email', 'subject', 'message', 'status', 'ip_address'];
    
    public function scopeUnread($query)
    {
        return $query->where('status', 'new');
    }
    
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
```

### 1.3 Create Controller
File: `app/Http/Controllers/ContactFormController.php`

- POST `/api/contact/submit` endpoint
- Validate: name (required), email (required, valid), subject (min 5), message (min 10)
- Validate reCAPTCHA v3 (call external API with secret key from admin settings)
- Rate limit: Max 5 submissions per IP per hour (use Laravel throttle)
- Store to DB with status='new', capture IP address
- Return JSON response: `{ success: true, message: "Your message was received" }`
- Handle errors gracefully

### 1.4 Create Frontend Component
File: `resources/js/Components/ContactFormSubmit.vue`

- Form fields: name (text), email (email), subject (text), message (textarea)
- reCAPTCHA v3 integration (call grecaptcha.execute() before submit)
- Submit button with loading state
- Translations from `lang/{locale}/app.php` for labels
- On success: Reset form + show toast "Thank you, we'll review your message"
- On error: Show error toast with message
- Prevent form submission if validation fails

### 1.5 Integrate into Contact Page
File: `resources/js/Pages/Contact.vue`

- Replace the existing contact form with the new `<ContactFormSubmit />` component
- Keep existing contact info section
- Ensure responsive design works

### 1.6 Add Route
File: `routes/web.php`

Inside the localized group `Route::prefix('{locale}')->group(function () {`:
```php
Route::post('/api/contact/submit', [ContactFormController::class, 'submit']);
```

### 1.7 Filament BO Resource
File: `app/Filament/Resources/ContactSubmissionResource.php`

**Table:**
- Columns: Name, Email, Subject (truncated), Date (formatted), Status (badge)
- Filters: By Status (New/Read/Replied), Date range
- Sort: Newest first (default)
- Bulk Actions:
  - Mark as Read
  - Mark as Replied
  - Delete selected
- Regular Actions: View/Edit (readonly view), Delete
- Page title: "Contact Form Submissions"

**Form (Detail View):**
- Show: Name, Email, Subject, Message (readonly), Status (select), Created date
- Allow editing Status field only
- Add Delete button

---

## FEATURE 2: GUEST REVIEW SYSTEM

### 2.1 Modify Reviews Migration
File: `database/migrations/2026_05_10_modify_reviews_table.php`

Add columns to `reviews` table:
- `status` (enum: 'pending', 'approved', 'rejected', default: 'pending')
- `admin_notes` (longText, nullable) - for rejection reasons
- `submitted_by_name` (string, nullable) - guest name
- `submitted_by_email` (string, nullable) - guest email

### 2.2 Modify Review Model
File: `app/Models/Review.php`

Add:
```php
protected $fillable = [..., 'status', 'admin_notes', 'submitted_by_name', 'submitted_by_email'];

public function scopeApproved($query)
{
    return $query->where('status', 'approved');
}

public function scopePending($query)
{
    return $query->where('status', 'pending');
}

public function canDisplay()
{
    return $this->status === 'approved';
}
```

### 2.3 Create Review Form Component
File: `resources/js/Components/ReviewFormSubmit.vue`

- Form fields:
  - Rating (1-5 stars, required)
  - Review text (textarea, min 10 chars, max 500 chars, required)
  - Your name (text, optional)
  - Your email (email, optional)
- reCAPTCHA v3 integration
- Message: "Your review will be published after admin approval"
- Translations for all labels
- Submit button with loading state
- On success: "Thank you! Your review is pending approval"
- On error: Show error message

### 2.4 Create/Modify Review Controller
File: `app/Http/Controllers/ReviewController.php`

Create or modify to add:
- POST `/api/artisans/{id}/reviews/submit` endpoint
- Validate: rating (1-5), comment (min 10, max 500), email format if provided
- Validate reCAPTCHA v3
- Rate limit: Max 1 review per email per artisan per day (or by IP if no email)
- Store to DB with:
  - `status = 'pending'`
  - `submitted_by_name` = submitted name or "Anonymous"
  - `submitted_by_email` = submitted email
  - `artisan_id`, `rating`, `comment` as before
- Return: `{ success: true, message: "Review submitted for approval" }`

### 2.5 Modify Artisan Show Page
File: `resources/js/Pages/Artisans/Show.vue`

- Add `<ReviewFormSubmit :artisanId="artisan.id" />` component
- Modify review display to show only reviews where `status === 'approved'`
- Modify average rating calculation to use only approved reviews:
  - Backend: In ArtisanController::show, use `withAvg('reviews' => function ($q) { $q->approved() }, 'rating')`
- Keep existing review display structure but filter by approved status

### 2.6 Filament BO Resource for Reviews
File: `app/Filament/Resources/ReviewResource.php` (modify if exists)

**Table:**
- Columns: Artisan (link), Rating (stars), Comment (truncated), Submitted by (email or name), Date, Status (badge)
- Filters: By Status (Pending/Approved/Rejected), By Artisan, By Rating, Date range
- Sort: Pending first (default), then newest
- Bulk Actions:
  - Approve selected (set status='approved')
  - Reject selected (set status='rejected', optional: prompt for rejection reason)
  - Delete selected
- Regular Actions: View/Edit, Delete

**Form (Detail View):**
- Show: Artisan, Rating, Comment (textarea, readonly), Submitted by (name & email), Date, Status (select)
- Add Admin Notes field (textarea) - for rejection reasons
- Approve/Reject buttons

---

## FEATURE 3: ADMIN SETTINGS PANEL

### 3.1 Database Migration
File: `database/migrations/2026_05_10_create_admin_settings_table.php`

Table: `admin_settings`
- `id` (primary key)
- `key` (string, unique) - e.g., 'recaptcha_site_key'
- `value` (longText, nullable)
- `type` (enum: 'string', 'boolean', 'json', default: 'string')
- `description` (text, nullable) - help text
- `timestamps`

Seed with default empty settings for:
- `recaptcha_site_key`
- `recaptcha_secret_key`
- `smtp_host`
- `smtp_port` (default: 587)
- `smtp_username`
- `smtp_password`
- `mail_from_address`
- `mail_from_name` (default: "trouvemaalem")
- `google_tag_manager_id`
- `contact_notification_emails` (type: json, for multiple emails)

### 3.2 Create Model
File: `app/Models/AdminSetting.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];
    
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;
        
        if ($setting->type === 'boolean') return (bool) $setting->value;
        if ($setting->type === 'json') return json_decode($setting->value, true);
        return $setting->value;
    }
    
    public static function set($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
```

### 3.3 Create Filament Resource
File: `app/Filament/Resources/SettingsResource.php`

**Form with Sections:**

**Section 1: reCAPTCHA Configuration**
- Site Key (TextInput)
- Secret Key (TextInput, password view)
- Help text: "Get keys from https://www.google.com/recaptcha/admin"

**Section 2: Email Configuration (SMTP)**
- SMTP Host (TextInput, e.g., smtp.gmail.com)
- SMTP Port (TextInput, default 587)
- SMTP Username (TextInput)
- SMTP Password (TextInput, password view)
- From Email Address (TextInput, required, must be valid email)
- From Name (TextInput, default "trouvemaalem")
- Help text with examples for: Gmail, SendGrid, AWS SES

**Section 3: Analytics & Tracking**
- Google Tag Manager ID (TextInput, optional, e.g., GTM-XXXXXX)
- Help text: "Leave blank to disable tracking"

**Section 4: Contact Form Settings**
- Notification Emails (Textarea, one email per line, optional)
- Help text: "Emails that should be notified of contact form submissions (optional)"

**Section 5: Branding (Optional)**
- Site Title (TextInput, optional)
- Site Description (Textarea, optional)

**Actions:**
- Test Email Button
  - Sends test email to currently logged-in admin email
  - Uses configured SMTP settings
  - Shows toast: "Test email sent successfully" or error message

**Authorization:**
- Only super admin can access this resource (add isVisible/canView checks)

### 3.4 Update Mail Configuration
File: `config/mail.php`

Modify to read from admin settings:
```php
'from' => [
    'address' => env('MAIL_FROM_ADDRESS', AdminSetting::get('mail_from_address', 'hello@example.com')),
    'name' => env('MAIL_FROM_NAME', AdminSetting::get('mail_from_name', 'trouvemaalem')),
],
```

Create a service or helper that reads SMTP config from admin settings for dynamic mail configuration.

### 3.5 Integration with Controllers
File: `app/Http/Controllers/ContactFormController.php` & `app/Http/Controllers/ReviewController.php`

- Use `AdminSetting::get('recaptcha_secret_key')` for validation
- Use `AdminSetting::get('recaptcha_site_key')` to pass to frontend

File: `resources/js/Composables/useSettings.js` (create new)

```javascript
export function useSettings() {
  return {
    recaptchaSiteKey: import.meta.env.VITE_RECAPTCHA_SITE_KEY
  }
}
```

Update `.env.example` to include:
```
VITE_RECAPTCHA_SITE_KEY=
```

---

## FEATURE 4: ERROR PAGES

### 4.1 Create 404 Page
File: `resources/views/errors/404.blade.php`

```html
@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Page not found. Please check the URL and try again.'))

<!-- Link back to home -->
<a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-primary">
  {{ __('Back to Home') }}
</a>
```

### 4.2 Create 500 Page
File: `resources/views/errors/500.blade.php`

```html
@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Something went wrong on our end. Please try again later.'))

<!-- Link to contact -->
<a href="{{ route('contact', ['locale' => app()->getLocale()]) }}" class="btn btn-primary">
  {{ __('Contact Support') }}
</a>
```

### 4.3 Create Minimal Layout
File: `resources/views/errors/minimal.blade.php`

Use existing MainLayout or simple HTML with site branding.

---

## FEATURE 5: PRODUCTION CONFIGURATION

### 5.1 Update .env.example
File: `.env.example`

Change these values:
```
APP_ENV=local → APP_ENV=production
APP_DEBUG=true → APP_DEBUG=false
APP_LOCALE=en → APP_LOCALE=fr
APP_URL=http://localhost:8000 → APP_URL=https://yourdomain.com
DB_PASSWORD= → DB_PASSWORD=<change_me>
MAIL_MAILER=log → MAIL_MAILER=smtp
LOG_LEVEL=debug → LOG_LEVEL=warning
BCRYPT_ROUNDS=12 → BCRYPT_ROUNDS=12
SESSION_ENCRYPT=false → SESSION_ENCRYPT=true
```

Add new variables:
```
VITE_RECAPTCHA_SITE_KEY=
```

### 5.2 Update CLAUDE.md
File: `CLAUDE.md`

Add new section:

```markdown
## Production Deployment Checklist

### Environment Setup
1. Set `APP_ENV=production`, `APP_DEBUG=false`
2. Set `APP_LOCALE=fr` (default language)
3. Change `APP_URL` to your production domain (https://)
4. Set `DB_PASSWORD` to a strong password
5. Enable `SESSION_ENCRYPT=true`
6. Set `MAIL_MAILER=smtp` and configure SMTP via Admin Settings

### Admin Settings to Configure (Before Launch)
Navigate to `/admin/settings` and configure:
1. **reCAPTCHA Keys** - Get from https://www.google.com/recaptcha/admin
2. **SMTP Configuration** - Email server credentials (Gmail, SendGrid, AWS SES, etc.)
3. **Google Tag Manager ID** - (Optional) For analytics
4. **Contact Notification Emails** - (Optional) Admins to notify of form submissions

### Features Deployed
- ✅ Contact Form (admin reviews submissions in BO)
- ✅ Guest Review System (requires admin approval before publishing)
- ✅ Admin Settings Panel (configure reCAPTCHA, email, analytics)
- ✅ Bulk review approval/rejection
- ✅ Error pages (404, 500)

### Security
- reCAPTCHA v3 protects contact forms and reviews from spam
- Rate limiting prevents brute force attacks
- All user submissions reviewed by admin before publishing
```

---

## IMPLEMENTATION CHECKLIST

### Database & Models
- [ ] Create ContactSubmission migration and model
- [ ] Create AdminSetting migration and model
- [ ] Modify reviews table migration (add status, admin_notes, etc.)
- [ ] Modify Review model (add scopes, canDisplay())
- [ ] Run migrations: `php artisan migrate`

### Controllers
- [ ] Create ContactFormController with submit endpoint
- [ ] Modify ReviewController with submit endpoint
- [ ] Add reCAPTCHA validation logic in both controllers
- [ ] Add rate limiting

### Frontend Components
- [ ] Create ContactFormSubmit.vue component
- [ ] Create ReviewFormSubmit.vue component
- [ ] Integrate ContactFormSubmit into Contact.vue page
- [ ] Integrate ReviewFormSubmit into Artisans/Show.vue
- [ ] Update Artisans/Show.vue to filter reviews by status='approved'

### Admin Panel (Filament)
- [ ] Create ContactSubmissionResource
- [ ] Create/Modify ReviewResource with bulk actions
- [ ] Create SettingsResource with all sections
- [ ] Add test email functionality

### Error Pages
- [ ] Create 404.blade.php
- [ ] Create 500.blade.php
- [ ] Create minimal.blade.php layout

### Configuration
- [ ] Update .env.example
- [ ] Update CLAUDE.md with deployment info
- [ ] Test all forms with reCAPTCHA
- [ ] Test bulk actions in admin panel
- [ ] Test email configuration
- [ ] Test error pages (trigger 404, 500)

### Testing
- [ ] Contact form submits and appears in BO
- [ ] Review form submits with status='pending'
- [ ] Admin can approve/reject reviews
- [ ] Only approved reviews show on artisan page
- [ ] reCAPTCHA validation works
- [ ] Rate limiting prevents spam
- [ ] All translations work (EN/FR/AR)
- [ ] Mobile responsive design
- [ ] Admin can configure all settings

---

## ADDITIONAL NOTES

**reCAPTCHA v3:**
- No user interaction required (invisible)
- Returns score 0.0-1.0 (0=bot, 1=human)
- Threshold: Accept if score > 0.5
- Documentation: https://developers.google.com/recaptcha/docs/v3

**Email Configuration Examples:**
- Gmail SMTP: host=smtp.gmail.com, port=587, username=your-email@gmail.com, password=app-password
- SendGrid: host=smtp.sendgrid.net, port=587, username=apikey, password=SG.xxxx
- AWS SES: host=email-smtp.{region}.amazonaws.com, port=587, username/password from AWS console

**Rate Limiting:**
- Use Laravel's `throttle()` middleware or RateLimiter facade
- Config in `config/rate-limiting.php` or `app/Http/Middleware/ThrottleRequests.php`

**Translations:**
- Add keys to: `lang/en/app.php`, `lang/fr/app.php`, `lang/ar/app.php`
- Use `t('key')` in Vue, `__('key')` in Blade

---

## FILES TO CREATE/MODIFY SUMMARY

**NEW FILES:**
1. `database/migrations/2026_05_10_create_contact_submissions_table.php`
2. `database/migrations/2026_05_10_modify_reviews_table.php`
3. `database/migrations/2026_05_10_create_admin_settings_table.php`
4. `app/Models/ContactSubmission.php`
5. `app/Models/AdminSetting.php`
6. `app/Http/Controllers/ContactFormController.php`
7. `app/Filament/Resources/ContactSubmissionResource.php`
8. `app/Filament/Resources/SettingsResource.php` (or modify existing ReviewResource)
9. `resources/js/Components/ContactFormSubmit.vue`
10. `resources/js/Components/ReviewFormSubmit.vue`
11. `resources/views/errors/404.blade.php`
12. `resources/views/errors/500.blade.php`
13. `resources/views/errors/minimal.blade.php`

**MODIFIED FILES:**
1. `app/Models/Review.php`
2. `app/Http/Controllers/ReviewController.php`
3. `resources/js/Pages/Contact.vue`
4. `resources/js/Pages/Artisans/Show.vue`
5. `routes/web.php`
6. `config/mail.php`
7. `.env.example`
8. `CLAUDE.md`
9. `lang/en/app.php` (add translation keys)
10. `lang/fr/app.php` (add translation keys)
11. `lang/ar/app.php` (add translation keys)

---

**END OF PROMPT**

Execute this step-by-step and test each feature before moving to the next. Use `composer dev` to run dev server while implementing.
