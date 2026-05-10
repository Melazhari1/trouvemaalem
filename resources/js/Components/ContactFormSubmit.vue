<template>
  <form class="contact-form" @submit.prevent="submitForm">
    <div class="form-group">
      <label for="cf-name">{{ t('contact_name') }}</label>
      <input
        id="cf-name"
        v-model="form.name"
        type="text"
        :placeholder="t('contact_name_placeholder')"
        :class="{ error: errors.name }"
        required
      />
      <span v-if="errors.name" class="field-error">{{ errors.name[0] }}</span>
    </div>

    <div class="form-group">
      <label for="cf-email">{{ t('contact_email') }}</label>
      <input
        id="cf-email"
        v-model="form.email"
        type="email"
        :placeholder="t('contact_email_placeholder')"
        :class="{ error: errors.email }"
        required
      />
      <span v-if="errors.email" class="field-error">{{ errors.email[0] }}</span>
    </div>

    <div class="form-group">
      <label for="cf-subject">{{ t('contact_subject') }}</label>
      <input
        id="cf-subject"
        v-model="form.subject"
        type="text"
        :placeholder="t('contact_subject_placeholder')"
        :class="{ error: errors.subject }"
        required
      />
      <span v-if="errors.subject" class="field-error">{{ errors.subject[0] }}</span>
    </div>

    <div class="form-group">
      <label for="cf-message">{{ t('contact_message') }}</label>
      <textarea
        id="cf-message"
        v-model="form.message"
        rows="5"
        :placeholder="t('contact_message_placeholder')"
        :class="{ error: errors.message }"
        required
      ></textarea>
      <span v-if="errors.message" class="field-error">{{ errors.message[0] }}</span>
    </div>

    <div v-if="globalError" class="form-alert error-alert">{{ globalError }}</div>
    <div v-if="successMessage" class="form-alert success-alert">{{ successMessage }}</div>

    <button type="submit" class="btn-submit" :disabled="loading">
      <span v-if="loading" class="spinner"></span>
      <span>{{ loading ? t('contact_sending') : t('contact_send') }}</span>
    </button>
  </form>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useTranslations } from '../Composables/useTranslations';

const { t } = useTranslations();
const page = usePage();

const form = reactive({ name: '', email: '', subject: '', message: '' });
const loading = ref(false);
const errors = ref({});
const globalError = ref('');
const successMessage = ref('');

async function getRecaptchaToken() {
  const siteKey = page.props.recaptchaSiteKey;
  if (!siteKey || typeof window.grecaptcha === 'undefined') return null;
  return new Promise((resolve) => {
    window.grecaptcha.ready(() => {
      window.grecaptcha.execute(siteKey, { action: 'contact' }).then(resolve);
    });
  });
}

async function submitForm() {
  loading.value = true;
  errors.value = {};
  globalError.value = '';
  successMessage.value = '';

  try {
    const recaptchaToken = await getRecaptchaToken();

    const locale = page.props.locale || 'fr';
    const response = await window.axios.post(`/${locale}/api/contact/submit`, {
      ...form,
      recaptcha_token: recaptchaToken,
    });

    successMessage.value = response.data.message || t('contact_success');
    form.name = '';
    form.email = '';
    form.subject = '';
    form.message = '';
  } catch (err) {
    if (err.response?.status === 422 && err.response.data?.errors) {
      errors.value = err.response.data.errors;
    } else if (err.response?.status === 429) {
      globalError.value = t('rate_limit_error');
    } else {
      globalError.value = err.response?.data?.message || t('contact_error');
    }
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.form-group {
  margin-bottom: 1.5rem;
}
.form-group label {
  display: block;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: var(--text-main);
  font-size: 0.95rem;
}
.form-group input,
.form-group textarea {
  width: 100%;
  padding: 0.8rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 1rem;
  font-family: inherit;
  transition: all 0.2s;
  background: #f8fafc;
  box-sizing: border-box;
}
.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #d78126;
  background: white;
  box-shadow: 0 0 0 4px rgba(215, 129, 38, 0.1);
}
.form-group input.error,
.form-group textarea.error {
  border-color: #ef4444;
}
.field-error {
  display: block;
  color: #ef4444;
  font-size: 0.85rem;
  margin-top: 0.3rem;
}
.form-alert {
  padding: 0.9rem 1rem;
  border-radius: 10px;
  margin-bottom: 1rem;
  font-size: 0.95rem;
  font-weight: 500;
}
.error-alert {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}
.success-alert {
  background: #f0fdf4;
  color: #16a34a;
  border: 1px solid #bbf7d0;
}
.btn-submit {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #d78126, #cba346);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  margin-top: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}
.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(215, 129, 38, 0.3);
}
.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.4);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
