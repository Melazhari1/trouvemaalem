<template>
  <MainLayout>
    <SeoHead
      :title="`${t('contact_page_title')} - trouvemaalem`"
      :description="t('contact_page_desc')"
    />

    <!-- Header Section -->
    <section class="page-header">
      <div class="container header-inner">
        <h1>{{ t('contact_heading_1') }} <span class="text-accent">{{ t('contact_heading_2') }}</span></h1>
        <p class="page-subtitle">{{ t('contact_subtitle') }}</p>
      </div>
    </section>

    <!-- Content Section -->
    <section class="container section">
      <div class="contact-grid">
        <!-- Contact Info -->
        <div class="contact-info">
          <h2>{{ t('contact_info_title_1') }} <span class="text-accent">{{ t('contact_info_title_2') }}</span></h2>
          <p class="info-desc">{{ t('contact_info_desc') }}</p>

          <div class="info-list">
            <div class="info-item">
              <div class="icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              </div>
              <div>
                <h4>{{ t('contact_phone_label') }}</h4>
                <p>+212 500 000 000</p>
              </div>
            </div>

            <div class="info-item">
              <div class="icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
              </div>
              <div>
                <h4>Email</h4>
                <p>contact@trouvemaalem.com</p>
              </div>
            </div>

            <div class="info-item">
              <div class="icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
              </div>
              <div>
                <h4>{{ t('contact_address_label') }}</h4>
                <p>123 Boulevard Mohammed V<br>Casablanca, Maroc</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-container">
          <ContactFormSubmit />
        </div>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
import SeoHead from '../Components/SeoHead.vue';
import ContactFormSubmit from '../Components/ContactFormSubmit.vue';
import { useTranslations } from '../Composables/useTranslations';

const { t } = useTranslations();
</script>

<style scoped>
/* =========== Header =========== */
.page-header {
  background: linear-gradient(135deg, #113559 0%, rgba(17, 53, 89, 0.9) 50%, rgba(17, 53, 89, 0.8) 100%);
  padding: 6rem 0 4rem;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.page-header::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at 50% 50%, rgba(203, 163, 70, 0.15) 0%, transparent 60%);
  pointer-events: none;
}
.header-inner { position: relative; z-index: 1; }
.page-header h1 {
  font-size: clamp(2rem, 5vw, 3.25rem);
  font-weight: 800;
  color: #f8fafc;
  margin: 0 0 0.75rem;
  line-height: 1.15;
  letter-spacing: -0.03em;
}
.text-accent { color: #d78126; }
.page-subtitle {
  font-size: 1.15rem;
  color: #94a3b8;
  margin: 0 0 2rem;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

/* =========== Content =========== */
.section {
  margin-top: 8rem;
  margin-bottom: 10rem;
}

.contact-grid {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  gap: 4rem;
}

.contact-info h2 {
  font-size: 2.2rem;
  font-weight: 800;
  margin-bottom: 1rem;
  color: var(--text-main);
}

.info-desc {
  font-size: 1.1rem;
  color: var(--text-muted);
  margin-bottom: 3rem;
  line-height: 1.6;
}

.info-list {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 1.5rem;
}

.icon-box {
  background: rgba(215, 129, 38, 0.1);
  color: #d78126;
  width: 54px;
  height: 54px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.info-item h4 {
  margin: 0 0 0.5rem 0;
  font-size: 1.1rem;
  color: var(--text-main);
  font-weight: 700;
}

.info-item p {
  margin: 0;
  color: var(--text-muted);
  line-height: 1.5;
}

/* Contact Form */
.contact-form-container {
  background: white;
  padding: 3rem;
  border-radius: 24px;
  box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1);
  border: 1px solid #f1f5f9;
}

@media (max-width: 900px) {
  .contact-grid {
    grid-template-columns: 1fr;
    gap: 3rem;
  }
}

@media (max-width: 600px) {
  .contact-form-container {
    padding: 2rem 1.5rem;
  }
}
</style>
