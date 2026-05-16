<template>
  <MainLayout>
    <SeoHead
      :title="t('about_seo_title')"
      :description="t('about_seo_desc')"
    />

    <!-- Header Section -->
    <section class="page-header">
      <div class="container header-inner">
        <h1>{{ t('about_hero_title') }} <span class="text-accent">{{ t('about_hero_title_accent') }}</span></h1>
        <p class="page-subtitle">{{ t('about_hero_subtitle') }}</p>
      </div>
    </section>

    <!-- Content Section -->
    <section class="container section">
      <div class="about-content">
        <div class="about-text">
          <h2>
            {{ t('about_mission_title') }}
            <span v-if="t('about_mission_title_accent')" class="text-accent"> {{ t('about_mission_title_accent') }}</span>
          </h2>
          <p>{{ t('about_mission_p1') }}</p>
          <p>{{ t('about_mission_p2') }}</p>

          <div class="stats-grid">
            <div class="stat-card">
              <h3>500+</h3>
              <p>{{ t('about_stat_artisans') }}</p>
            </div>
            <div class="stat-card">
              <h3>12</h3>
              <p>{{ t('about_stat_cities') }}</p>
            </div>
          </div>
        </div>
        <div class="about-image">
          <img src="https://images.unsplash.com/photo-1541123356219-284ebe98ae3b?q=80&w=2070&auto=format&fit=crop" :alt="t('about_image_alt')" />
        </div>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
import SeoHead from '../Components/SeoHead.vue';
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

.about-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
}

.about-text h2 {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 1.5rem;
  color: var(--text-main);
}

.about-text p {
  font-size: 1.1rem;
  line-height: 1.8;
  color: var(--text-muted);
  margin-bottom: 1.5rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-top: 3rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 16px;
  box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
  text-align: center;
  border: 1px solid #f1f5f9;
}

.stat-card h3 {
  font-size: 2rem;
  color: #d78126;
  margin: 0 0 0.5rem 0;
  font-weight: 800;
}

.stat-card p {
  margin: 0;
  font-size: 0.9rem;
  color: var(--text-muted);
  font-weight: 600;
}

.about-image img {
  width: 100%;
  height: 500px;
  object-fit: cover;
  border-radius: 24px;
  box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
}

@media (max-width: 900px) {
  .about-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  .about-image {
    order: -1;
  }
  .about-image img {
    height: 350px;
  }
}

@media (max-width: 600px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
