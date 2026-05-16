<template>
  <MainLayout>
    <SeoHead
      :title="t('seo_home_title')"
      :description="t('seo_home_desc')"
      :schema="schema"
    />

    <!-- Hero Section -->
    <section class="hero-premium relative overflow-hidden bg-brand-blue pt-24 pb-32 md:pt-32 md:pb-24">
      <div class="absolute inset-0 z-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-brand-orange/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-brand-gold/10 rounded-full blur-3xl animate-pulse delay-700"></div>
      </div>

      <div class="container relative z-10 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-sm mb-6 animate-fade-in">
          <TrustBadge type="top" size="xs">{{ t('top_rated') }}</TrustBadge>
          <span class="text-white/80 text-xs font-bold uppercase tracking-widest">{{ t('hero_headline') }}</span>
        </div>

        <h1 class="text-white text-4xl md:text-6xl lg:text-7xl mb-6 max-w-4xl mx-auto leading-tight">
          {{ t('hero_title_1') }} <span class="text-brand-orange underline decoration-brand-orange/30">{{ t('hero_title_2') }}</span>
        </h1>
        
        <p class="text-slate-300 text-lg md:text-xl mb-10 max-w-2xl mx-auto leading-relaxed">
          {{ t('hero_subtitle') }}
        </p>

        <!-- Main Search Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-slide-up">
          <ActionButton size="lg" variant="primary" class="w-full sm:w-auto" :href="`/${locale}/search`">
            {{ t('view_artisans') }}
            <template #icon-right>
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </template>
          </ActionButton>
          
          <ActionButton 
            size="lg" 
            variant="white" 
            class="w-full sm:w-auto"
            @click="toggleGeolocation"
            :class="{ 'border-brand-orange text-brand-orange': userLocation }"
          >
            <template #icon-left>
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
            </template>
            {{ userLocation ? t('btn_location_active') : t('btn_use_location') }}
          </ActionButton>
        </div>
      </div>
    </section>

    <!-- Filters Bar (Stickyish) -->
    <section class="container my-12 md:my-16 relative z-20 ">
      <div class="glass-card p-6 md:p-8 rounded-3xl grid grid-cols-1 md:grid-cols-3 gap-6 shadow-premium mt-12 mb-12">
        <div class="flex flex-col gap-2">
          <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest pl-1">{{ t('filter_category') }}</label>
          <div class="relative">
            <select v-model="selectedCategory" class="w-full h-12 bg-slate-50 border-none rounded-xl px-4 text-brand-blue font-bold appearance-none focus:ring-2 focus:ring-brand-orange/20">
              <option value="">{{ t('all_categories') }}</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest pl-1">{{ t('filter_city') }}</label>
          <div class="relative">
            <select v-model="selectedCity" class="w-full h-12 bg-slate-50 border-none rounded-xl px-4 text-brand-blue font-bold appearance-none focus:ring-2 focus:ring-brand-orange/20">
              <option value="">{{ t('all_cities') }}</option>
              <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
            </select>
            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-center md:justify-end gap-4 pt-4 md:pt-0">
          <div class="text-right">
            <div class="text-2xl font-black text-brand-blue leading-none">{{ filteredArtisans.length }}</div>
            <div class="text-[10px] uppercase font-bold text-slate-400">{{ t('workers_found') }}</div>
          </div>
          <ActionButton variant="secondary" size="md" @click="clearFilters" v-if="selectedCategory || selectedCity">
            {{ t('clear_filters') }}
          </ActionButton>
        </div>
      </div>
    </section>

    <!-- Map Section -->
    <section class="container my-12 md:my-16 py-16 md:py-24">
      <div class="rounded-3xl overflow-hidden border-4 border-white shadow-premium h-[500px] md:h-[600px]">
        <ClientOnly>
          <WorkerMap
            :workers="filteredArtisans"
            :userLocation="userLocation"
            :initialCenter="mapCenter"
            :initialZoom="mapZoom"
            :translations="{ km_from_you: t('km_from_you'), view_details: t('view_details') }"
          />
        </ClientOnly>
      </div>
    </section>

    <!-- Popular Categories -->
    <section class="container my-12 md:my-16 py-16 md:py-24">
      <div class="text-center mb-20 md:mb-28 mt-12 mb-12">
        <h2 class="text-3xl md:text-5xl lg:text-6xl text-brand-blue mb-6">{{ t('nav_categories') }}</h2>
        <div class="w-24 h-2 bg-brand-orange mx-auto rounded-full"></div>
      </div>
      
      <div 
        ref="slider"
        class="flex overflow-x-auto gap-6 md:gap-8 pb-12 snap-x no-scrollbar cursor-grab active:cursor-grabbing select-none"
        @mousedown="startDragging"
        @mouseleave="stopDragging"
        @mouseup="stopDragging"
        @mousemove="move"
      >
        <Link 
          v-for="cat in categories" 
          :key="cat.id" 
          :href="`/${locale}/categories/${cat.slug}`"
          class="flex-none w-[280px] md:w-[350px] group relative h-96 rounded-3xl overflow-hidden shadow-soft transition-transform duration-500 hover:-translate-y-2 snap-start"
        >
          <img :src="cat.image" :alt="cat.name" class="absolute inset-0 w-full h-full object-fit-cover transition-transform duration-700 group-hover:scale-110" />
          <div class="absolute inset-0 bg-gradient-to-t from-brand-blue/90 via-brand-blue/20 to-transparent"></div>
          <div class="absolute bottom-8 left-8 right-8 text-white">
            <h3 class="text-2xl font-black mb-2">{{ cat.name }}</h3>
            <p class="text-xs text-white/70 font-bold uppercase tracking-widest">{{ t('artisans_count', { count: cat.artisans_count || 0 }) }}</p>
          </div>
        </Link>
      </div>
    </section>

    <!-- How it Works -->
    <section class="bg-slate-50 py-48 md:py-64">
      <div class="container">
        <div class="text-center mb-24 md:mb-32">
          <h2 class="text-3xl md:text-5xl lg:text-6xl text-brand-blue mb-6">{{ t('how_it_works_title') }}</h2>
          <p class="text-slate-500 max-w-xl mx-auto text-lg">{{ t('app_tagline') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-16 md:gap-24 relative">
          <!-- Step 1 -->
          <div class="text-center group">
            <div class="w-24 h-24 rounded-3xl bg-white shadow-soft flex items-center justify-center mx-auto mb-10 transition-transform group-hover:rotate-6">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#d78126" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </div>
            <h3 class="text-2xl text-brand-blue mb-4">{{ t('step_1_title') }}</h3>
            <p class="text-slate-500 text-base leading-relaxed">{{ t('step_1_desc') }}</p>
          </div>

          <!-- Step 2 -->
          <div class="text-center group">
            <div class="w-24 h-24 rounded-3xl bg-white shadow-soft flex items-center justify-center mx-auto mb-10 transition-transform group-hover:rotate-6">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#d78126" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h3 class="text-2xl text-brand-blue mb-4">{{ t('step_2_title') }}</h3>
            <p class="text-slate-500 text-base leading-relaxed">{{ t('step_2_desc') }}</p>
          </div>

          <!-- Step 3 -->
          <div class="text-center group">
            <div class="w-24 h-24 rounded-3xl bg-white shadow-soft flex items-center justify-center mx-auto mb-10 transition-transform group-hover:rotate-6">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#d78126" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </div>
            <h3 class="text-2xl text-brand-blue mb-4">{{ t('step_3_title') }}</h3>
            <p class="text-slate-500 text-base leading-relaxed">{{ t('step_3_desc') }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Top Rated Workers -->
    <section class="container py-48 md:py-64">
      <div class="flex flex-col md:flex-row items-center justify-between mb-12 gap-6">
        <div>
          <h2 class="text-3xl md:text-4xl text-brand-blue mb-2">{{ t('top_workers') }}</h2>
          <p class="text-slate-500">{{ t('seo_home_desc') }}</p>
        </div>
        <ActionButton variant="outline" :href="`/${locale}/search`">{{ t('view_artisans') }}</ActionButton>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <PremiumCard 
          v-for="worker in topArtisans.slice(0, 9)" 
          :key="worker.id"
          class="h-full flex flex-col"
          @click="goToWorker(worker)"
        >
          <template #header>
            <div class="h-64 relative overflow-hidden">
              <img :src="worker.image" :alt="worker.name" class="w-full h-full object-fit-cover transition-transform duration-500 group-hover:scale-110" />
              <div class="absolute top-4 left-4 flex flex-col gap-2">
                <TrustBadge type="top" size="sm">{{ t('top_rated') }}</TrustBadge>
                <TrustBadge type="verified" size="sm">{{ t('verified_badge') }}</TrustBadge>
              </div>
              <div v-if="worker.rating > 0" class="absolute bottom-4 right-4">
                <div class="bg-white/90 backdrop-blur-md px-3 py-1 rounded-full shadow-sm flex items-center gap-1">
                  <span class="text-brand-orange text-sm font-black">★</span>
                  <span class="text-brand-blue text-sm font-black">{{ Number(worker.rating).toFixed(1) }}</span>
                </div>
              </div>
            </div>
          </template>

          <div class="flex flex-col h-full">
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-xl text-brand-blue mb-1">{{ worker.name }}</h3>
                <div class="flex flex-wrap gap-2">
                  <Link
                    v-for="cat in worker.categories"
                    :key="cat.id"
                    :href="`/${locale}/categories/${cat.slug}`"
                    @click.stop
                    class="text-brand-orange text-xs font-bold uppercase tracking-widest hover:underline"
                  >{{ cat.name }}</Link>
                </div>
              </div>
            </div>

            <p class="text-slate-500 text-sm mb-6 line-clamp-2">
              {{ worker.bio }}
            </p>

            <div class="mt-auto pt-6 border-t border-slate-100 flex items-center justify-between">
              <div class="flex items-center gap-2 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                <span class="text-[11px] font-bold uppercase">{{ worker.city }}</span>
              </div>
              <div class="flex items-center gap-2">
                <button
                  @click.stop="quickViewArtisan = worker"
                  class="text-[10px] font-black uppercase tracking-wider text-brand-orange border border-brand-orange/30 px-2 py-1 rounded-full hover:bg-brand-orange hover:text-white transition-colors"
                >{{ t('quick_view') }}</button>
                <ActionButton size="sm" variant="ghost" class="p-0!">{{ t('view_details') }} →</ActionButton>
              </div>
            </div>
          </div>
        </PremiumCard>
      </div>
    </section>

    <ArtisanQuickView :artisan="quickViewArtisan" @close="quickViewArtisan = null" />
  </MainLayout>
</template>

<script setup>
import { ref, computed, defineAsyncComponent } from 'vue';
import MainLayout from '../Layouts/MainLayout.vue';
import SeoHead from '../Components/SeoHead.vue';
import ActionButton from '../Components/UI/ActionButton.vue';
import PremiumCard from '../Components/UI/PremiumCard.vue';
import TrustBadge from '../Components/UI/TrustBadge.vue';
import ArtisanQuickView from '../Components/ArtisanQuickView.vue';
const WorkerMap = defineAsyncComponent(() => import('../Components/WorkerMap.vue'));
import { Link, router } from '@inertiajs/vue3';
import { useTranslations } from '../Composables/useTranslations';

const { t, locale } = useTranslations();
const quickViewArtisan = ref(null);
const slider = ref(null);
let isDown = false;
let startX;
let scrollLeft;

const startDragging = (e) => {
  isDown = true;
  startX = e.pageX - slider.value.offsetLeft;
  scrollLeft = slider.value.scrollLeft;
};

const stopDragging = () => {
  isDown = false;
};

const move = (e) => {
  if (!isDown) return;
  e.preventDefault();
  const x = e.pageX - slider.value.offsetLeft;
  const walk = (x - startX) * 2; 
  slider.value.scrollLeft = scrollLeft - walk;
};

const props = defineProps({
  categories: Array,
  artisans: Array,
  topArtisans: Array,
  cities: Array,
  topFaqs: Array,
  schema: Object,
});

const selectedCategory = ref('');
const selectedCity = ref('');
const userLocation = ref(null);
const geoError = ref(null);

// Morocco center fallback
const MOROCCO_CENTER = [31.7917, -7.0926];
const MOROCCO_ZOOM = 6;

const mapCenter = computed(() => {
  if (userLocation.value) {
    return [userLocation.value.lat, userLocation.value.lng];
  }
  return MOROCCO_CENTER;
});

const mapZoom = computed(() => {
  return userLocation.value ? 10 : MOROCCO_ZOOM;
});

function haversineDistance(lat1, lng1, lat2, lng2) {
  const R = 6371;
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLng = ((lng2 - lng1) * Math.PI) / 180;
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((lat1 * Math.PI) / 180) *
      Math.cos((lat2 * Math.PI) / 180) *
      Math.sin(dLng / 2) *
      Math.sin(dLng / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return Math.round(R * c * 10) / 10;
}

const filteredArtisans = computed(() => {
  let results = [...props.artisans];

  if (selectedCategory.value) {
    results = results.filter(w => w.categories?.some(c => c.id == selectedCategory.value));
  }
  if (selectedCity.value) {
    results = results.filter(w => w.city === selectedCity.value);
  }

  if (userLocation.value) {
    results = results.map(w => ({
      ...w,
      distance: haversineDistance(userLocation.value.lat, userLocation.value.lng, w.lat, w.lng),
    }));
    results.sort((a, b) => a.distance - b.distance);
  }

  return results;
});

function toggleGeolocation() {
  if (userLocation.value) {
    userLocation.value = null;
    return;
  }

  if (!navigator.geolocation) {
    geoError.value = 'Geolocation not supported';
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (pos) => {
      userLocation.value = {
        lat: pos.coords.latitude,
        lng: pos.coords.longitude,
      };
    },
    (err) => {
      geoError.value = err.message;
      alert('Could not get your location. Please allow location access.');
    },
    { enableHighAccuracy: true, timeout: 10000 }
  );
}

function clearFilters() {
  selectedCategory.value = '';
  selectedCity.value = '';
}

function goToWorker(worker) {
  router.visit(`/${locale.value}/artisan/${worker.slug}`);
}
</script>

<script>
import { defineComponent } from 'vue';
export default defineComponent({
  components: {
    ClientOnly: defineComponent({
      data() { return { mounted: false } },
      mounted() { this.mounted = true },
      render() { return this.mounted ? this.$slots.default?.() : null }
    })
  }
});
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 1s ease-out;
}
.animate-slide-up {
  animation: slideUp 0.8s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.object-fit-cover {
  object-fit: cover;
}

.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
