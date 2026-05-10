<template>
  <div class="review-form-wrapper">
    <h3 class="review-form-title">{{ t('review_leave') }}</h3>
    <p class="review-form-notice">{{ t('review_pending_notice') }}</p>

    <form @submit.prevent="submitReview">
      <!-- Star Rating -->
      <div class="form-group">
        <label>{{ t('review_rating') }} <span class="required">*</span></label>
        <div class="star-picker">
          <button
            v-for="star in 5"
            :key="star"
            type="button"
            @click="form.rating = star"
            @mouseover="hovered = star"
            @mouseleave="hovered = 0"
            class="star-btn"
            :class="{ active: star <= (hovered || form.rating) }"
          >★</button>
        </div>
        <span v-if="errors.rating" class="field-error">{{ errors.rating[0] }}</span>
      </div>

      <!-- Comment -->
      <div class="form-group">
        <label for="rv-comment">{{ t('review_comment') }} <span class="required">*</span></label>
        <textarea
          id="rv-comment"
          v-model="form.comment"
          rows="4"
          :placeholder="t('review_comment_placeholder')"
          :class="{ error: errors.comment }"
          maxlength="500"
          required
        ></textarea>
        <div class="char-count">{{ form.comment.length }}/500</div>
        <span v-if="errors.comment" class="field-error">{{ errors.comment[0] }}</span>
      </div>

      <!-- Name (optional) -->
      <div class="form-group">
        <label for="rv-name">{{ t('review_your_name') }}</label>
        <input
          id="rv-name"
          v-model="form.name"
          type="text"
          :placeholder="t('review_name_placeholder')"
        />
      </div>

      <!-- Email (optional) -->
      <div class="form-group">
        <label for="rv-email">{{ t('review_your_email') }}</label>
        <input
          id="rv-email"
          v-model="form.email"
          type="email"
          :placeholder="t('review_email_placeholder')"
          :class="{ error: errors.email }"
        />
        <span v-if="errors.email" class="field-error">{{ errors.email[0] }}</span>
      </div>

      <div v-if="globalError" class="form-alert error-alert">{{ globalError }}</div>
      <div v-if="successMessage" class="form-alert success-alert">{{ successMessage }}</div>

      <button type="submit" class="btn-submit" :disabled="loading || !form.rating">
        <span v-if="loading" class="spinner"></span>
        <span>{{ loading ? t('review_submitting') : t('review_submit') }}</span>
      </button>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useTranslations } from '../Composables/useTranslations';

const props = defineProps({
  artisanId: { type: Number, required: true },
});

const { t } = useTranslations();
const page = usePage();

const form = reactive({ rating: 0, comment: '', name: '', email: '' });
const hovered = ref(0);
const loading = ref(false);
const errors = ref({});
const globalError = ref('');
const successMessage = ref('');

async function getRecaptchaToken() {
  const siteKey = page.props.recaptchaSiteKey;
  if (!siteKey || typeof window.grecaptcha === 'undefined') return null;
  return new Promise((resolve) => {
    window.grecaptcha.ready(() => {
      window.grecaptcha.execute(siteKey, { action: 'review' }).then(resolve);
    });
  });
}

async function submitReview() {
  if (!form.rating) return;

  loading.value = true;
  errors.value = {};
  globalError.value = '';
  successMessage.value = '';

  try {
    const recaptchaToken = await getRecaptchaToken();
    const locale = page.props.locale || 'fr';

    const response = await window.axios.post(
      `/${locale}/api/artisans/${props.artisanId}/reviews/submit`,
      { ...form, recaptcha_token: recaptchaToken }
    );

    successMessage.value = response.data.message || t('review_submitted');
    form.rating = 0;
    form.comment = '';
    form.name = '';
    form.email = '';
  } catch (err) {
    if (err.response?.status === 422 && err.response.data?.errors) {
      errors.value = err.response.data.errors;
    } else if (err.response?.status === 429) {
      globalError.value = t('rate_limit_error');
    } else {
      globalError.value = err.response?.data?.message || t('review_error');
    }
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.review-form-wrapper {
  background: white;
  border-radius: 1.5rem;
  border: 2px solid #e2e8f0;
  padding: 2rem;
}
.review-form-title {
  font-size: 1.3rem;
  font-weight: 800;
  color: #113559;
  margin: 0 0 0.5rem;
}
.review-form-notice {
  font-size: 0.875rem;
  color: #94a3b8;
  margin: 0 0 1.5rem;
}
.form-group {
  margin-bottom: 1.25rem;
}
.form-group label {
  display: block;
  font-weight: 600;
  font-size: 0.9rem;
  color: #1e293b;
  margin-bottom: 0.4rem;
}
.required {
  color: #ef4444;
}
.star-picker {
  display: flex;
  gap: 0.25rem;
}
.star-btn {
  font-size: 2rem;
  color: #e2e8f0;
  background: none;
  border: none;
  cursor: pointer;
  transition: color 0.15s, transform 0.15s;
  line-height: 1;
  padding: 0;
}
.star-btn.active {
  color: #d78126;
}
.star-btn:hover {
  transform: scale(1.2);
}
.form-group input,
.form-group textarea {
  width: 100%;
  padding: 0.7rem 0.9rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.95rem;
  font-family: inherit;
  background: #f8fafc;
  transition: all 0.2s;
  box-sizing: border-box;
}
.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #d78126;
  background: white;
  box-shadow: 0 0 0 3px rgba(215, 129, 38, 0.1);
}
.form-group input.error,
.form-group textarea.error {
  border-color: #ef4444;
}
.char-count {
  text-align: right;
  font-size: 0.8rem;
  color: #94a3b8;
  margin-top: 0.25rem;
}
.field-error {
  display: block;
  color: #ef4444;
  font-size: 0.82rem;
  margin-top: 0.25rem;
}
.form-alert {
  padding: 0.8rem 1rem;
  border-radius: 10px;
  margin-bottom: 1rem;
  font-size: 0.9rem;
  font-weight: 500;
}
.error-alert { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.success-alert { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.btn-submit {
  width: 100%;
  padding: 0.85rem;
  background: linear-gradient(135deg, #d78126, #cba346);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}
.btn-submit:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(215, 129, 38, 0.3);
}
.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.spinner {
  width: 15px;
  height: 15px;
  border: 2px solid rgba(255,255,255,0.4);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
