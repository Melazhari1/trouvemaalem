<template>
  <MainLayout>
    <SeoHead
      :title="`${category.name} - trouvemaalem`"
      :description="t('category_seo_desc', { name: category.name })"
    />

    <section class="category-header" :style="{ backgroundImage: `linear-gradient(rgba(17, 53, 89, 0.8), rgba(17, 53, 89, 0.8)), url(${category.image})` }">
      <div class="container header-content">
        <Link href="/categories" class="back-link">&larr; {{ t('all_categories') }}</Link>
        <h1>{{ category.name }}</h1>
        <p>{{ category.description }}</p>
      </div>
    </section>

    <section class="container section">
      <div v-if="artisans.length === 0" class="empty-state">
        <div class="empty-icon">👷</div>
        <h3>{{ t('no_artisans_found') }}</h3>
        <p>{{ t('no_artisans_desc') }}</p>
      </div>

      <div class="artisan-grid" v-else>
        <div v-for="artisan in artisans" :key="artisan.id" class="artisan-card">
          <div class="card-image">
            <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(artisan.name)}&background=random&size=128`" :alt="artisan.name" />
            <div class="badge">{{ artisan.category.name }}</div>
          </div>
          <div class="card-info">
            <div class="rating-city">
              <span class="city"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg> {{ artisan.city }}</span>
              <span class="rating">⭐ {{ artisan.rating }}</span>
            </div>
            <h3>{{ artisan.name }}</h3>
            <p class="specialty">{{ artisan.specialty }}</p>
            <div class="card-footer">
              <Link :href="`/artisan/${artisan.slug}`" class="btn-profile">{{ t('view_profile') }}</Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import SeoHead from '../../Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '../../Composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
  category: Object,
  artisans: Array,
});
</script>

<style scoped>
.category-header {
  padding: 6rem 0;
  background-size: cover;
  background-position: center;
  color: white;
  text-align: center;
}

.header-content {
  max-width: 800px;
  margin: 0 auto;
}

.back-link {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  margin-bottom: 2rem;
  display: inline-block;
  transition: opacity 0.2s;
}

.back-link:hover {
  opacity: 0.8;
}

.category-header h1 {
  font-size: 3.5rem;
  margin-bottom: 1rem;
}

.category-header p {
  font-size: 1.25rem;
  opacity: 0.9;
}

.section {
  padding: 4rem 0;
}

.artisan-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
}

.artisan-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
  transition: transform 0.3s, box-shadow 0.3s;
  border: 1px solid #f1f5f9;
}

.artisan-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
}

.card-image {
  height: 200px;
  position: relative;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
}

.card-image img {
  width: 128px;
  height: 128px;
  border-radius: 50%;
  object-fit: cover;
  border: 4px solid white;
  box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

.badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: var(--primary-color);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.card-info {
  padding: 1.5rem;
}

.rating-city {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
  color: var(--text-muted);
}

.rating {
  font-weight: 700;
  color: #fbbf24;
}

.card-info h3 {
  margin: 0 0 0.5rem;
  font-size: 1.25rem;
}

.specialty {
  color: var(--text-muted);
  font-size: 0.95rem;
  margin-bottom: 1.5rem;
  min-height: 2.8rem;
}

.btn-profile {
  display: block;
  width: 100%;
  text-align: center;
  background: var(--text-main);
  color: white;
  text-decoration: none;
  padding: 0.75rem;
  border-radius: 12px;
  font-weight: 600;
  transition: background 0.2s;
}

.btn-profile:hover {
  background: var(--primary-color);
}

.empty-state {
  text-align: center;
  padding: 5rem 0;
  color: var(--text-muted);
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

@media (max-width: 768px) {
  .category-header h1 {
    font-size: 2.5rem;
  }
}
</style>
