# 👨‍💼 ADMIN USER GUIDE - trouvemaalem

Complete guide for using the trouvemaalem admin panel to manage content and moderation.

---

## Getting Started

### Admin Panel URL
```
https://yourdomain.com/admin
```

### Login Credentials
- **Email:** admin@trouvemaalem.ma (or your updated admin email)
- **Password:** (Your new strong password - NOT admin123)

### First Time Login
1. Go to https://yourdomain.com/admin
2. Enter email and password
3. Click "Login"
4. Dashboard loads with all your content

---

## Dashboard Overview

When you login, you see the Dashboard with:
- **Navigation Menu** (left sidebar)
- **Quick Stats** (artisans, reviews, contacts, etc.)
- **Recent Activity** (recent submissions, changes)
- **User Menu** (top right - your account)

### Navigation Groups

| Group | Pages | Purpose |
|-------|-------|---------|
| **Content** | Artisans, Categories, Posts, FAQs | Manage main site content |
| **Moderation** | Reviews, Contact Submissions | Moderate user submissions |
| **Settings** | Settings, Locale | Configure application |

---

## Content Management

### 1. ARTISANS (Service Providers)

**Navigate to:** Admin Panel > Content > Artisans

#### View All Artisans
- List shows: Name, Category, City, Rating, Verified Status
- **Search:** Use search bar to find artisans by name
- **Filter:** Filter by Category, City, Verified status
- **Sort:** Click column headers to sort

#### Add New Artisan
1. Click **"New"** button (top right)
2. Fill in tabs:
   - **EN** - English content (name, bio, location)
   - **FR** - French content (nom, biographie, localisation)
   - **AR** - Arabic content (الاسم, السيرة الذاتية, الموقع)
3. Upload artisan photo
4. Enter coordinates (latitude, longitude)
   - Get coordinates from: https://www.google.com/maps
   - Right-click location → coordinates appear
5. Set category (dropdown)
6. Mark as "Verified" if applicable
7. Click **"Save"**

**Important:** All three language tabs must be filled in.

#### Edit Existing Artisan
1. Click artisan name in list
2. Update information in tabs
3. Change photo if needed
4. Update coordinates/city
5. Click **"Save"**

#### Delete Artisan
1. Click artisan name
2. Click **"Delete"** button (bottom of form)
3. Confirm deletion
4. Artisan removed (reviews also deleted if configured)

#### Example Data
```
Name (EN): Ahmed's Plumbing
Name (FR): Plomberie d'Ahmed
Name (AR): السباكة الخاصة بأحمد

Bio (EN): Professional plumber with 15 years experience
City: Casablanca
Category: Plumbing
Coordinates: 33.5731, -7.5898
Verified: Yes
Photo: [upload professional photo]
```

---

### 2. CATEGORIES

**Navigate to:** Admin Panel > Content > Categories

#### View All Categories
- List shows all service categories
- Search by name
- Show count of artisans in each category

#### Add New Category
1. Click **"New"**
2. Fill in all three languages:
   - EN: "Plumbing", "Electrician", "Carpentry"
   - FR: "Plomberie", "Électricité", "Menuiserie"
   - AR: "السباكة", "الكهرباء", "النجارة"
3. Add description in each language (optional)
4. Click **"Save"**

#### Edit Category
1. Click category name
2. Update translations
3. Click **"Save"**

#### Delete Category
1. Click category
2. Click **"Delete"** (only if no artisans in it)
3. If artisans exist, must move them first

---

### 3. BLOG POSTS

**Navigate to:** Admin Panel > Content > Posts

#### View All Posts
- List shows: Title, Author, Status (Draft/Published), Date

#### Add New Blog Post
1. Click **"New"**
2. Fill in each language tab:
   - **Title** - Post heading
   - **Excerpt** - Short summary (displays in list)
   - **Content** - Full post body (supports HTML/Markdown)
3. Add featured image (optional)
4. Set status (Draft/Published)
5. Click **"Save"**

#### Edit Post
1. Click post title
2. Update content
3. Change publication status if needed
4. Click **"Save"**

#### Delete Post
1. Click post
2. Click **"Delete"**
3. Confirm

---

### 4. FAQ (Frequently Asked Questions)

**Navigate to:** Admin Panel > Content > FAQs

#### View All FAQs
- List shows questions in current language

#### Add New FAQ
1. Click **"New"**
2. Fill in each language:
   - **Question** - The question text
   - **Answer** - The answer content
3. Reorder using drag handles (if available)
4. Click **"Save"**

#### Edit FAQ
1. Click question
2. Update content
3. Reorder if needed
4. Click **"Save"**

#### Delete FAQ
1. Click FAQ
2. Click **"Delete"**

---

## Moderation Tasks

### 1. REVIEWS (Review Approval)

**Navigate to:** Admin Panel > Moderation > Reviews

#### Understanding Review Status
- **Pending** 🟡 - Waiting for approval (NEW - check these first!)
- **Approved** 🟢 - Visible on artisan page
- **Rejected** 🔴 - Hidden from artisan page

#### View Pending Reviews
1. Click **"Filters"** button
2. Select Status: "Pending"
3. Click **"Apply"**
4. List shows only pending reviews

#### Approve a Review
1. Find pending review in list
2. **Option A (Quick):**
   - Select checkbox next to review
   - Click **"Approve Selected"** (bulk action)
3. **Option B (Detailed):**
   - Click review to open details
   - Read review content
   - Click **"Approve"** button
4. Review now visible on artisan page

#### Reject a Review
1. Open review details
2. Click **"Reject"** button
3. **Optional:** Add "Admin Notes" explaining rejection
   - Example: "Contains offensive language"
   - Example: "Competitor review"
4. Review hidden from public (only visible to admin)

#### Edit Review
1. Open review details
2. Update rating or comment if needed (rare)
3. Add admin notes for internal reference
4. Click **"Save"**

#### Delete Review
1. Open review
2. Click **"Delete"**
3. Review permanently removed

#### Bulk Actions
Select multiple reviews using checkboxes, then:
- **Approve Selected** - Approve all at once
- **Reject Selected** - Reject all at once
- **Delete Selected** - Delete all at once

#### Example Workflow
```
Morning Admin Check:
1. Go to /admin/reviews
2. Filter by: Status = Pending
3. See 15 new pending reviews
4. Review each one:
   - Legitimate? → Click Approve
   - Spam/Fake? → Click Reject + add note
   - Offensive? → Click Delete
5. All processed, pending count returns to 0 ✓
```

---

### 2. CONTACT SUBMISSIONS

**Navigate to:** Admin Panel > Moderation > Contact Submissions

#### Understanding Submission Status
- **New** 🆕 - Just arrived (check these first!)
- **Read** ✓ - Admin has read
- **Replied** 📧 - Admin has responded

#### View All Submissions
- List shows: Name, Email, Subject, Date, Status
- Newest first (default)
- Search by name or email

#### Read a Submission
1. Click the submission row
2. Details appear: Full message, email, phone (if provided)
3. Email address shown for contacting submitter

#### Mark as Read
1. Click submission
2. Click **"Mark as Read"** or
3. Select in list → **"Mark as Read"** (bulk action)

#### Mark as Replied
1. After you've responded to the visitor via email:
2. Click submission
3. Click **"Mark as Replied"**

#### Delete Submission
1. Click submission
2. Click **"Delete"** (for spam/junk)

#### Respond to a Submission
1. Open submission
2. Copy their email address: `jane@example.com`
3. Send email directly from your email client
4. Once replied, mark status as "Replied"

#### Example Process
```
Typical workflow:
1. Check /admin/contact-submissions
2. Filter: Status = New
3. See 3 new inquiries:
   - "Plumbing repair" from John (john@example.com)
     → Email: "Hi John, I'll connect you with Ahmed's Plumbing"
     → Mark as "Replied"
   - "Electrical work" from Mary
     → Email: "Hi Mary, Our electrician Fatima can help..."
     → Mark as "Replied"
   - Spam: "Buy cheap products!!!"
     → Click Delete (spam)
4. Check done ✓
```

---

## Settings & Configuration

### Navigate to: Admin Panel > Settings

#### reCAPTCHA Configuration
**Purpose:** Spam protection for forms

1. **Site Key** field
   - From: https://www.google.com/recaptcha/admin
   - Look for your domain
   - Copy "Site Key"
   - Paste into this field

2. **Secret Key** field
   - Copy "Secret Key" from Google
   - Paste into this field
   - This is PRIVATE, don't share

3. **Test:** Click "Test reCAPTCHA"
   - Should show "Connection successful"

**Note:** If both are empty, forms will NOT have spam protection.

#### SMTP Email Configuration
**Purpose:** Send emails for contact form notifications

**Gmail Setup:**
1. **SMTP Host:** `smtp.gmail.com`
2. **SMTP Port:** `587`
3. **SMTP Username:** `your-email@gmail.com`
4. **SMTP Password:** (Your Gmail app password)
   - NOT your Gmail account password!
   - Get from: https://myaccount.google.com/apppasswords
5. **From Email:** `no-reply@yourdomain.com`
6. **From Name:** `trouvemaalem`
7. Click **"Test Email"**
   - Check your inbox (and spam folder)
   - Should receive test email within 30 seconds

**SendGrid Setup:**
1. **SMTP Host:** `smtp.sendgrid.net`
2. **SMTP Port:** `587`
3. **SMTP Username:** `apikey`
4. **SMTP Password:** `SG.your_api_key_here`
5. **From Email:** `noreply@yourdomain.com`
6. **From Name:** `trouvemaalem`
7. Click **"Test Email"**

#### Google Tag Manager
**Purpose:** Analytics tracking (optional)

1. **Google Tag Manager ID:** `GTM-XXXXXX`
   - From: https://tagmanager.google.com
   - Create container for your domain
   - Copy container ID
   - Paste into this field
2. Leave blank if not using GTM

#### Contact Notification Emails
**Purpose:** Alert admins of new contact submissions (optional)

1. **Email Addresses:** (One per line)
   ```
   admin@yourdomain.com
   manager@yourdomain.com
   ```
2. Admins will receive email when new contact submitted

#### Branding (Optional)
**Site Title:** `trouvemaalem` (or your custom title)
**Site Description:** "Find local artisans in Morocco" (or custom description)

---

## Language Switching

### Switch Admin Panel Language
1. Click user menu (top right)
2. Select language: EN / FR / AR
3. Admin interface updates to selected language

**Important:** Changing language shows content in that language, but doesn't change the site language. Visitors choose their own language using the site's language switcher.

---

## User Account Management

### Change Your Password
1. Click user menu (top right)
2. Click **"Account Settings"** or **"Change Password"**
3. Enter current password
4. Enter new password (strong password required)
5. Confirm new password
6. Click **"Save"**

### Logout
1. Click user menu (top right)
2. Click **"Logout"**
3. Redirected to login page

---

## Daily Admin Checklist

### Morning (Start of Day)
```
☐ Login to /admin
☐ Check Moderation > Reviews (filter: Pending)
  - Approve legitimate reviews (green checkmark)
  - Reject spam/fake reviews (red X)
  - Add admin notes if rejecting
☐ Check Moderation > Contact Submissions (filter: New)
  - Read each inquiry
  - Respond via email
  - Mark status as "Replied"
  - Delete spam
☐ Check Settings > Email Test
  - Send test email if configuration changed
```

### Weekly
```
☐ Review pending reviews (ensure < 5 pending)
☐ Respond to all new contacts (within 24 hours)
☐ Review and approve legitimate reviews
☐ Delete spam submissions
☐ Check Blog > Posts (publish scheduled content)
☐ Monitor Analytics (if using Google Tag Manager)
```

### Monthly
```
☐ Update artisan information (availability, pricing)
☐ Add new categories if needed
☐ Create blog post about local artisans
☐ Review FAQ and update as needed
☐ Check Settings for any needed updates
☐ Verify email configuration is working
☐ Back up database
```

---

## Common Tasks

### Task: Connect Visitor with Artisan
**Scenario:** Contact form inquiry for "plumbing work"

1. Go to /admin/contact-submissions
2. Open inquiry from visitor
3. Find appropriate artisan:
   - Go to Content > Artisans
   - Search for "plumber"
   - Click artisan > see details + contact info
4. Email visitor with artisan's details:
   ```
   Hi John,
   
   Thank you for your inquiry. I'd like to connect you with
   Ahmed's Plumbing - a professional plumber in Casablanca.
   
   Contact: [artisan email/phone]
   
   Best regards,
   trouvemaalem Team
   ```
5. Back in admin, mark submission as "Replied"

---

### Task: Handle Fake/Spam Review
**Scenario:** Review with 1 star "Your service is terrible!!" no details

1. Go to /admin/reviews
2. Find suspicious review
3. Click to open details
4. Click **"Reject"** button
5. Add Admin Notes: "Spam - no legitimate feedback"
6. Review now hidden from artisan page
7. Artisan's rating unaffected

---

### Task: Approve Bulk Reviews
**Scenario:** 10 new legitimate reviews came in

1. Go to /admin/reviews
2. Filter: Status = Pending
3. Select all checkboxes (or select individually)
4. Click **"Approve Selected"** button
5. All reviews approved at once
6. Now visible on artisan pages ✓

---

## Tips & Best Practices

### Do's ✅
- ✅ Review submissions daily
- ✅ Respond to contacts within 24 hours
- ✅ Approve legitimate reviews quickly (users appreciate this)
- ✅ Add admin notes when rejecting (for future reference)
- ✅ Keep password strong and confidential
- ✅ Test email configuration monthly
- ✅ Update artisan information regularly

### Don'ts ❌
- ❌ Don't leave submissions pending for weeks
- ❌ Don't approve reviews without reading them
- ❌ Don't delete legitimate reviews
- ❌ Don't share admin credentials
- ❌ Don't skip updating translations (incomplete content looks unprofessional)
- ❌ Don't change settings without testing
- ❌ Don't leave debug mode on (APP_DEBUG=true in production)

---

## Frequently Asked Questions

### Q: Can visitors edit their own reviews?
**A:** No. Visitors submit once, then admin approves. If they want to change it, they submit a new review and admin can reject the old one.

### Q: What if someone submits a contact form multiple times?
**A:** Rate limiting prevents abuse (5 per IP per hour). Multiple submissions from same person are saved separately and all appear in your admin panel.

### Q: Can I delete an artisan?
**A:** Yes, but all their reviews will be deleted too. Instead, consider unpublishing them temporarily.

### Q: How do I add artisans in multiple languages?
**A:** Use the three tabs (EN, FR, AR) in the artisan form. Fill in name, bio, and location in each language. All three must be completed.

### Q: What if email test fails?
**A:** 
1. Check SMTP credentials are correct
2. Verify password is app-specific password (for Gmail)
3. Check firewall/server allows SMTP on port 587
4. Check email provider account has SMTP enabled
5. See TROUBLESHOOTING_GUIDE.md section on "Emails Not Sending"

### Q: Can I change the admin password?
**A:** Yes! Click user menu > Account Settings > Change Password. Use strong password (min 16 chars).

### Q: What if I forget the admin password?
**A:** You'll need database access or contact hosting provider to reset via command line:
```bash
php artisan tinker
$user = User::where('email', 'admin@..')->first();
$user->password = Hash::make('NewPassword123!');
$user->save();
```

---

## Support Resources

### If You Need Help:
1. **Refer to:** TROUBLESHOOTING_GUIDE.md for common issues
2. **Check:** ADMIN_USER_GUIDE.md (this file) for admin tasks
3. **Read:** SECURITY_REVIEW.md for security best practices
4. **Review:** API_DOCUMENTATION.md for technical details

---

**Last Updated:** May 10, 2026  
**Framework:** Laravel 13 + Filament v4  
**Admin Panel Version:** 1.0

Good luck managing trouvemaalem! 🚀
