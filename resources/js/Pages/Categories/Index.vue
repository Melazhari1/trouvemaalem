<template>
  <MainLayout>
    <SeoHead
      :title="t('seo_categories_title')"
      :description="t('seo_categories_desc')"
      :schema="schema"
    />

    <section class="container section page-header">
      <h1>{{ t('categories_title') }}</h1>
      <p class="subtitle">{{ t('categories_subtitle') }}</p>
    </section>

    <section class="container section">
      <div class="grid category-grid">
        <article v-for="category in categories" :key="category.id" class="category-list-card">
          <div class="card-img-wrapper">
            <img :src="category.image" :alt="category.name" loading="lazy" />
          </div>
          <div class="card-content">
            <h2>{{ category.name }}</h2>
            <p>{{ category.description }}</p>
            <div class="meta">
              <span class="count">{{ t('artisans_count', { count: category.artisans_count || 0 }) }}</span>
              <Link :href="`/${locale}/categories/${category.slug}`" class="view-link">{{ t('view_artisans') }} &rarr;</Link>
            </div>
          </div>
        </article>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import SeoHead from '../../Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '../../Composables/useTranslations';

const { t, locale } = useTranslations();

const props = defineProps({
  categories: Array,
  schema: Object,
});
</script>

<style scoped>
.page-header {
  text-align: center;
  padding: 4rem 0;
}

.page-header h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: var(--text-muted);
  font-size: 1.2rem;
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.category-list-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
  display: flex;
  flex-direction: column;
}

.card-img-wrapper {
  height: 250px;
}

.card-img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.card-content {
  padding: 2rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.card-content h2 {
  margin: 0 0 1rem;
  font-size: 1.5rem;
}

.card-content p {
  color: var(--text-muted);
  line-height: 1.6;
  margin-bottom: 2rem;
  flex: 1;
}

.meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid #f1f5f9;
}

.count {
  font-weight: 600;
  color: var(--primary-color);
  background: rgba(215, 129, 38, 0.15);
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.875rem;
}

.view-link {
  color: var(--text-main);
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s;
}

.view-link:hover {
  color: var(--primary-hover);
}
</style>
