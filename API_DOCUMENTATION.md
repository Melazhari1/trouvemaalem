# 📡 API DOCUMENTATION - trouvemaalem

Complete API reference for trouvemaalem contact form and review system.

---

## Overview

The trouvemaalem API provides two main endpoints for guest submissions:
1. **Contact Form Submission** - For visitors to submit inquiries
2. **Review Submission** - For visitors to submit artisan reviews

Both endpoints use:
- ✅ reCAPTCHA v3 for spam protection
- ✅ Rate limiting to prevent abuse
- ✅ Validation before storage
- ✅ Admin moderation before publishing

---

## Base URL

```
https://yourdomain.com/en/api/
https://yourdomain.com/fr/api/
https://yourdomain.com/ar/api/
```

**Note:** Replace locale (`en`, `fr`, `ar`) with your desired language.

---

## Authentication

**Public APIs** - No authentication required.

All requests should:
- Include `Content-Type: application/json` header
- Include reCAPTCHA token from Google reCAPTCHA v3
- Respect rate limiting

---

## 1. Contact Form Submission

### Endpoint
```
POST /{locale}/api/contact/submit
```

### Method
`POST`

### Headers
```json
{
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

### Request Body
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "subject": "Inquiry about plumbing services",
  "message": "I need help with a leaky faucet in my kitchen...",
  "recaptcha_token": "03AGdBq27..."  // Token from grecaptcha.execute()
}
```

### Parameters

| Parameter | Type | Required | Description | Validation |
|-----------|------|----------|-------------|-----------|
| `name` | string | ✅ Yes | Full name of submitter | Min 2 chars, max 255 |
| `email` | string | ✅ Yes | Email address | Valid email format |
| `subject` | string | ✅ Yes | Message subject | Min 5 chars, max 255 |
| `message` | string | ✅ Yes | Message body | Min 10 chars, max 5000 |
| `recaptcha_token` | string | ✅ Yes | reCAPTCHA v3 token | From `grecaptcha.execute()` |

### Success Response

**HTTP 200 OK**
```json
{
  "success": true,
  "message": "Thank you for your message. We'll review it and respond soon."
}
```

### Error Responses

**HTTP 422 Unprocessable Entity** - Validation failed
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field must be a valid email."],
    "message": ["The message must be at least 10 characters."]
  }
}
```

**HTTP 429 Too Many Requests** - Rate limit exceeded
```json
{
  "message": "Too many requests. Maximum 5 submissions per hour per IP address."
}
```

**HTTP 500 Internal Server Error** - Server error
```json
{
  "message": "An error occurred while processing your request. Please try again later."
}
```

### Rate Limiting
- **Max:** 5 requests per IP address per hour
- **Time Window:** 60 minutes
- **Reset:** Automatic after 60 minutes

### Example Request (JavaScript)

```javascript
// 1. Get reCAPTCHA token
const token = await grecaptcha.execute('RECAPTCHA_SITE_KEY', {action: 'submit'});

// 2. Prepare form data
const formData = {
  name: "John Doe",
  email: "john@example.com",
  subject: "Plumbing inquiry",
  message: "I have a leaky faucet...",
  recaptcha_token: token
};

// 3. Send to API
const response = await fetch('/en/api/contact/submit', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify(formData)
});

const data = await response.json();

if (response.ok) {
  console.log("Success:", data.message);
} else {
  console.error("Error:", data.message);
  console.error("Validation errors:", data.errors);
}
```

### Example Request (cURL)

```bash
curl -X POST https://yourdomain.com/en/api/contact/submit \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "subject": "Plumbing inquiry",
    "message": "I have a leaky faucet in my kitchen...",
    "recaptcha_token": "03AGdBq27..."
  }'
```

### Admin Panel
Submissions appear in: `/admin/contact-submissions`

**Status Options:**
- `new` - Recently submitted (default)
- `read` - Admin has read it
- `replied` - Admin has responded

---

## 2. Review Submission

### Endpoint
```
POST /{locale}/api/artisans/{id}/reviews/submit
```

### Method
`POST`

### Headers
```json
{
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

### URL Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `locale` | string | ✅ Yes | Language code: `en`, `fr`, or `ar` |
| `id` | integer | ✅ Yes | Artisan ID (from database) |

### Request Body
```json
{
  "rating": 5,
  "comment": "Excellent electrician! Fixed my wiring issue quickly and professionally.",
  "submitted_by_name": "Jane Smith",
  "submitted_by_email": "jane@example.com",
  "recaptcha_token": "03AGdBq27..."
}
```

### Parameters

| Parameter | Type | Required | Description | Validation |
|-----------|------|----------|-------------|-----------|
| `rating` | integer | ✅ Yes | Star rating | 1-5 (inclusive) |
| `comment` | string | ✅ Yes | Review text | Min 10 chars, max 1000 |
| `submitted_by_name` | string | ❌ No | Reviewer name | Max 255 chars |
| `submitted_by_email` | string | ❌ No | Reviewer email | Valid email format if provided |
| `recaptcha_token` | string | ✅ Yes | reCAPTCHA v3 token | From `grecaptcha.execute()` |

### Success Response

**HTTP 200 OK**
```json
{
  "success": true,
  "message": "Thank you for your review! It will be published after admin approval."
}
```

### Error Responses

**HTTP 422 Unprocessable Entity** - Validation failed
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "rating": ["The rating must be between 1 and 5."],
    "comment": ["The comment must be at least 10 characters."]
  }
}
```

**HTTP 429 Too Many Requests** - Rate limit exceeded
```json
{
  "message": "You can only submit one review per artisan per day."
}
```

**HTTP 404 Not Found** - Artisan not found
```json
{
  "message": "Artisan not found."
}
```

**HTTP 500 Internal Server Error** - Server error
```json
{
  "message": "An error occurred while processing your review. Please try again later."
}
```

### Rate Limiting
- **Max:** 1 review per email/IP per artisan per day
- **Time Window:** 24 hours
- **Reset:** Automatic after 24 hours

### Review Moderation
- All reviews start with `status = 'pending'`
- Only appear on artisan page after admin approval
- Admin can approve, reject, or add notes
- Average rating calculated from **approved reviews only**

### Example Request (JavaScript)

```javascript
const artisanId = 5; // Example artisan ID

// 1. Get reCAPTCHA token
const token = await grecaptcha.execute('RECAPTCHA_SITE_KEY', {action: 'submit'});

// 2. Prepare review data
const reviewData = {
  rating: 5,
  comment: "Excellent service! Would recommend to anyone.",
  submitted_by_name: "Jane Smith",
  submitted_by_email: "jane@example.com",
  recaptcha_token: token
};

// 3. Send to API
const response = await fetch(`/en/api/artisans/${artisanId}/reviews/submit`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify(reviewData)
});

const data = await response.json();

if (response.ok) {
  console.log("Review submitted:", data.message);
} else {
  console.error("Error:", data.message);
  console.error("Validation errors:", data.errors);
}
```

### Example Request (cURL)

```bash
curl -X POST https://yourdomain.com/en/api/artisans/5/reviews/submit \
  -H "Content-Type: application/json" \
  -d '{
    "rating": 5,
    "comment": "Excellent service! Would recommend to anyone.",
    "submitted_by_name": "Jane Smith",
    "submitted_by_email": "jane@example.com",
    "recaptcha_token": "03AGdBq27..."
  }'
```

### Admin Panel
Reviews appear in: `/admin/reviews`

**Status Options:**
- `pending` - Awaiting approval (default)
- `approved` - Visible on artisan page
- `rejected` - Hidden from artisan page

---

## Map Data API

### Endpoint
```
GET /{locale}/api/map-data
```

### Method
`GET`

### Headers
```json
{
  "Accept": "application/json"
}
```

### Success Response

**HTTP 200 OK**
```json
{
  "artisans": [
    {
      "id": 1,
      "name": "Ahmed's Plumbing",
      "lat": 33.5731,
      "lng": -7.5898,
      "rating": 4.5,
      "reviews_count": 8,
      "slug": "ahmed-plumbing"
    },
    {
      "id": 2,
      "name": "Fatima's Electrical",
      "lat": 33.5741,
      "lng": -7.5888,
      "rating": 4.8,
      "reviews_count": 12,
      "slug": "fatima-electrical"
    }
  ]
}
```

### Parameters
None (returns all artisans with coordinates)

### Use Case
Used to populate Leaflet map with artisan markers and location pins.

---

## Error Handling

### Common HTTP Status Codes

| Status | Meaning | Action |
|--------|---------|--------|
| 200 | OK | Request successful |
| 400 | Bad Request | Malformed request, fix format |
| 422 | Unprocessable Entity | Validation failed, fix errors |
| 429 | Too Many Requests | Rate limited, wait and retry |
| 500 | Internal Server Error | Server error, try again later |
| 503 | Service Unavailable | Maintenance, try again later |

### Retry Strategy

```javascript
async function submitWithRetry(url, data, maxRetries = 3) {
  for (let i = 0; i < maxRetries; i++) {
    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      if (response.ok) {
        return await response.json();
      }

      if (response.status === 429) {
        // Rate limited, wait and retry
        await new Promise(r => setTimeout(r, 2000 * (i + 1)));
        continue;
      }

      if (response.status === 500 && i < maxRetries - 1) {
        // Server error, retry
        await new Promise(r => setTimeout(r, 1000 * (i + 1)));
        continue;
      }

      throw new Error(`HTTP ${response.status}`);
    } catch (error) {
      if (i === maxRetries - 1) throw error;
    }
  }
}
```

---

## CORS Policy

**CORS is enabled** for same-domain requests.

### Allowed Origins
- `https://yourdomain.com`
- `https://*.yourdomain.com`

### Allowed Methods
- `POST`
- `GET`
- `OPTIONS`

### Allowed Headers
- `Content-Type`
- `Accept`
- `Authorization` (if implemented)

---

## reCAPTCHA Integration

### Get reCAPTCHA Token

```javascript
// Initialize reCAPTCHA (usually in layout)
<script src="https://www.google.com/recaptcha/api.js"></script>

// Execute reCAPTCHA before form submission
const token = await grecaptcha.execute('RECAPTCHA_SITE_KEY', {
  action: 'submit'  // or 'contact', 'review', etc.
});

// Send token with form data
const data = {
  ...formFields,
  recaptcha_token: token
};
```

### Server-Side Validation

Server validates token with Google:
```
POST https://www.google.com/recaptcha/api/siteverify
Parameters:
  - secret: RECAPTCHA_SECRET_KEY
  - response: recaptcha_token

Response:
  {
    "success": true,
    "score": 0.9,  // 0.0-1.0 (higher = more human-like)
    "action": "submit",
    "challenge_ts": "2026-05-10T12:34:56Z",
    "hostname": "yourdomain.com"
  }
```

**If score < 0.5:** Request is likely spam, rejected.

---

## Localization

All API endpoints support 3 languages via URL prefix:

```
/en/api/...    # English
/fr/api/...    # French (Français)
/ar/api/...    # Arabic (العربية)
```

Error messages and response text are localized based on the locale in the URL.

---

## Examples & Code Snippets

### Vue.js Component Example

```vue
<template>
  <form @submit.prevent="submitForm">
    <input v-model="form.name" type="text" placeholder="Name" />
    <input v-model="form.email" type="email" placeholder="Email" />
    <textarea v-model="form.message" placeholder="Message"></textarea>
    <button type="submit" :disabled="loading">Submit</button>
    <p v-if="error" class="error">{{ error }}</p>
    <p v-if="success" class="success">{{ success }}</p>
  </form>
</template>

<script>
export default {
  data() {
    return {
      form: { name: '', email: '', message: '' },
      loading: false,
      error: null,
      success: null
    };
  },
  methods: {
    async submitForm() {
      this.loading = true;
      this.error = null;

      try {
        const token = await grecaptcha.execute(this.$page.props.recaptchaSiteKey, {
          action: 'submit'
        });

        const response = await fetch(`/${this.$page.props.locale}/api/contact/submit`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ ...this.form, recaptcha_token: token })
        });

        const data = await response.json();

        if (response.ok) {
          this.success = data.message;
          this.form = { name: '', email: '', message: '' };
        } else {
          this.error = data.message;
        }
      } catch (err) {
        this.error = 'An error occurred. Please try again.';
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
```

### React Component Example

```jsx
import { useState } from 'react';

export default function ContactForm({ locale, recaptchaSiteKey }) {
  const [form, setForm] = useState({ name: '', email: '', message: '' });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      const token = await window.grecaptcha.execute(recaptchaSiteKey, {
        action: 'submit'
      });

      const response = await fetch(`/${locale}/api/contact/submit`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...form, recaptcha_token: token })
      });

      const data = await response.json();

      if (response.ok) {
        setSuccess(data.message);
        setForm({ name: '', email: '', message: '' });
      } else {
        setError(data.message);
      }
    } catch (err) {
      setError('An error occurred. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <input
        value={form.name}
        onChange={(e) => setForm({ ...form, name: e.target.value })}
        placeholder="Name"
      />
      <input
        value={form.email}
        onChange={(e) => setForm({ ...form, email: e.target.value })}
        placeholder="Email"
        type="email"
      />
      <textarea
        value={form.message}
        onChange={(e) => setForm({ ...form, message: e.target.value })}
        placeholder="Message"
      />
      <button disabled={loading} type="submit">
        {loading ? 'Submitting...' : 'Submit'}
      </button>
      {error && <p className="error">{error}</p>}
      {success && <p className="success">{success}</p>}
    </form>
  );
}
```

---

## Testing

### Test Contact Form Endpoint

```bash
# Test with valid data
curl -X POST https://yourdomain.com/en/api/contact/submit \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "subject": "Test Subject",
    "message": "This is a test message with enough characters",
    "recaptcha_token": "test_token_here"
  }'

# Test with invalid email
curl -X POST https://yourdomain.com/en/api/contact/submit \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test",
    "email": "invalid-email",
    "subject": "Test",
    "message": "Message content here",
    "recaptcha_token": "test_token"
  }'

# Test rate limiting (submit 6 times rapidly)
for i in {1..6}; do
  curl -X POST https://yourdomain.com/en/api/contact/submit \
    -H "Content-Type: application/json" \
    -d '{...}'
done
# 6th should return 429 Too Many Requests
```

---

## Support & Issues

### If API returns 500 error:
1. Check error logs: `tail -f storage/logs/laravel.log`
2. Check database connection
3. Check reCAPTCHA keys are correct
4. Try again after 5 seconds

### If rate limiting is too strict:
- Contact form: 5 per hour per IP (configurable)
- Reviews: 1 per day per email per artisan (configurable)
- Contact admin to adjust limits

### If reCAPTCHA not working:
1. Verify site key is correct in `page.props.recaptchaSiteKey`
2. Verify domain is registered in Google reCAPTCHA admin console
3. Check that `grecaptcha.execute()` is called before submission
4. Look for reCAPTCHA errors in browser console

---

**Last Updated:** May 10, 2026  
**API Version:** 1.0  
**Framework:** Laravel 13 + Inertia.js
