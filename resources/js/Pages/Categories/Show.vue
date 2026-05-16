<template>
  <MainLayout>
    <SeoHead
      :title="`${category.name} - trouvemaalem`"
      :description="t('category_seo_desc', { name: category.name })"
      :schema="schema"
    />

    <!-- Header -->
    <section
      class="category-header"
      :style="{ backgroundImage: `linear-gradient(rgba(17,53,89,0.82), rgba(17,53,89,0.82)), url(${category.image})` }"
    >
      <div class="container header-content">
        <Link :href="`/${locale}/categories`" class="back-link">&larr; {{ t('all_categories') }}</Link>
        <h1>{{ category.name }}</h1>
        <p>{{ category.description }}</p>
      </div>
    </section>

    <!-- Grid -->
    <section class="container section">
      <div v-if="artisans.data.length === 0" class="empty-state">
        <div class="empty-icon">👷</div>
        <h3>{{ t('no_artisans_found') }}</h3>
        <p>{{ t('no_artisans_desc') }}</p>
      </div>

      <div v-else class="artisan-grid">
        <div v-for="artisan in artisans.data" :key="artisan.id" class="artisan-card">
          <!-- Image area -->
          <div class="card-image">
            <img
              :src="artisan.image || `https://ui-avatars.com/api/?name=${encodeURIComponent(artisan.name)}&background=random&size=128`"
              :alt="artisan.name"
            />
            <div v-if="artisan.categories?.[0]?.name" class="badge">{{ artisan.categories[0].name }}</div>
          </div>

          <!-- Info -->
          <div class="card-info">
            <div class="rating-city">
              <span class="city">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ artisan.city }}
              </span>
              <span v-if="artisan.rating > 0" class="rating">⭐ {{ Number(artisan.rating).toFixed(1) }}</span>
            </div>
            <h3>{{ artisan.name }}</h3>

            <div class="card-footer">
              <button
                @click="quickViewArtisan = artisan"
                class="btn-quick"
              >{{ t('quick_view') }}</button>
              <Link :href="`/${locale}/artisan/${artisan.slug}`" class="btn-profile">
                {{ t('view_profile') }} →
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pagination -->
    <div class="pagination-wrap">
      <SmartPagination
        :currentPage="artisans.current_page"
        :lastPage="artisans.last_page"
        @go="goToPage"
      />
    </div>

    <ArtisanQuickView :artisan="quickViewArtisan" @close="quickViewArtisan = null" />
  </MainLayout>
</template>

<script setup>
import { ref } from 'vue';
import MainLayout from '../../Layouts/MainLayout.vue';
import SeoHead from '../../Components/SeoHead.vue';
import ArtisanQuickView from '../../Components/ArtisanQuickView.vue';
import SmartPagination from '../../Components/SmartPagination.vue';
import { Link, router } from '@inertiajs/vue3';
import { useTranslations } from '../../Composables/useTranslations';

const { t, locale } = useTranslations();
const quickViewArtisan = ref(null);

const props = defineProps({
  category: Object,
  artisans: Object,
  schema: Object,
});

function goToPage(page) {
  router.get(`/${locale.value}/categories/${props.category.slug}`, { page }, {
    preserveState: true,
    replace: true,
  });
}
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
  color: #f97316;
  text-decoration: none;
  font-weight: 700;
  margin-bottom: 2rem;
  display: inline-block;
  transition: opacity 0.2s;
}
.back-link:hover { opacity: 0.8; }
.category-header h1 { font-size: 3.5rem; margin-bottom: 1rem; }
.category-header p  { font-size: 1.25rem; opacity: 0.9; }

.section { padding: 4rem 0; }

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
  border: 1px solid #f1f5f9;
  transition: transform 0.3s, box-shadow 0.3s;
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
  background: #113559;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
}

.card-info { padding: 1.5rem; }
.rating-city {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
  color: #64748b;
  align-items: center;
  gap: 0.5rem;
}
.city { display: flex; align-items: center; gap: 0.35rem; }
.rating { font-weight: 700; color: #fbbf24; }
.card-info h3 { margin: 0 0 1rem; font-size: 1.2rem; font-weight: 800; color: #113559; }

.card-footer {
  display: flex;
  gap: 0.5rem;
}
.btn-quick {
  flex: 1;
  text-align: center;
  border: 1.5px solid #f97316;
  color: #f97316;
  background: transparent;
  padding: 0.65rem 0.5rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.btn-quick:hover { background: #f97316; color: white; }

.btn-profile {
  flex: 1;
  display: block;
  text-align: center;
  background: #113559;
  color: white;
  text-decoration: none;
  padding: 0.65rem 0.5rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 700;
  transition: background 0.2s;
}
.btn-profile:hover { background: #f97316; }

.pagination-wrap { padding: 2.5rem 0 3rem; }

.empty-state { text-align: center; padding: 5rem 0; color: #64748b; }
.empty-icon { font-size: 4rem; margin-bottom: 1rem; }

@media (max-width: 768px) {
  .category-header h1 { font-size: 2.5rem; }
}
</style>
