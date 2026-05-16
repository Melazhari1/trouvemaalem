<template>
  <MainLayout>
    <SeoHead
      :title="t('search_title')"
      :description="t('search_description')"
      :schema="schema"
    />

    <div class="search-page bg-slate-50 min-h-screen pb-20 lg:pb-0">
      <!-- Search Header & Filters -->
      <header class="bg-brand-blue pt-12 pb-24 text-white">
        <div class="container mx-auto px-4 text-center">
          <h1 class="text-3xl md:text-5xl font-black mb-4">{{ t('find_expert') }} <span class="text-brand-orange">{{ t('artisans') }}</span></h1>
          <p class="text-slate-300 max-w-xl mx-auto mb-8">{{ t('search_subtitle') }}</p>
        </div>
      </header>

      <div class="container mx-auto px-4 -mt-16 mb-24 md:mb-32">
        <div class="glass-card p-6 md:p-8 rounded-3xl shadow-premium">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Keyword -->
            <div class="flex flex-col gap-2">
              <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest pl-1">{{ t('search_term') }}</label>
              <div class="relative">
                <input 
                  type="text" 
                  v-model="form.search" 
                  class="w-full h-12 bg-slate-50 border-none rounded-xl px-10 text-brand-blue font-bold focus:ring-2 focus:ring-brand-orange/20"
                  :placeholder="t('search_placeholder')"
                  @keyup.enter="handleSearch"
                />
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              </div>
            </div>

            <!-- Category -->
            <div class="flex flex-col gap-2">
              <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest pl-1">{{ t('filter_category') }}</label>
              <select v-model="form.category_id" class="w-full h-12 bg-slate-50 border-none rounded-xl px-4 text-brand-blue font-bold focus:ring-2 focus:ring-brand-orange/20">
                <option value="">{{ t('all_categories') }}</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>

            <!-- Rating -->
            <div class="flex flex-col gap-2">
              <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest pl-1">{{ t('min_rating') }}</label>
              <select v-model="form.min_rating" class="w-full h-12 bg-slate-50 border-none rounded-xl px-4 text-brand-blue font-bold focus:ring-2 focus:ring-brand-orange/20">
                <option value="">{{ t('any_rating') }}</option>
                <option v-for="r in 5" :key="r" :value="r">{{ r }}+ ⭐</option>
              </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-3">
              <ActionButton variant="primary" size="md" class="flex-1 h-12" @click="handleSearch">
                {{ t('search_now') }}
              </ActionButton>
              <ActionButton variant="ghost" size="md" class="h-12 w-12 p-0!" @click="clearFilters">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
              </ActionButton>
            </div>
          </div>
          
          <!-- Secondary Filters Row -->
          <div class="mt-6 pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
             <div class="flex flex-col sm:flex-row sm:items-center gap-6 sm:gap-12">
               <label class="flex items-center gap-3 cursor-pointer group">
                 <div class="relative w-12 h-6 rounded-full transition-colors" :class="form.verified ? 'bg-brand-orange' : 'bg-slate-200'">
                   <input type="checkbox" v-model="form.verified" class="hidden" @change="handleSearch" />
                   <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform" :class="{ 'translate-x-6': form.verified }"></div>
                 </div>
                 <span class="text-[10px] font-black text-brand-blue uppercase tracking-widest group-hover:text-brand-orange transition-colors">{{ t('verified_badge') }}</span>
               </label>
               
               <div class="flex flex-col gap-2 w-full sm:w-64">
                 <div class="flex justify-between items-center">
                   <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ t('distance') }}</span>
                   <span class="text-[10px] font-black text-brand-orange uppercase tracking-widest">{{ form.distance }}km</span>
                 </div>
                 <input type="range" v-model="form.distance" min="1" max="200" step="5" class="w-full accent-brand-orange" @change="handleSearch" />
               </div>
             </div>

             <ActionButton size="sm" variant="outline" class="w-full sm:w-auto" @click="toggleLocation" :class="{ 'bg-brand-blue text-white': hasLocation }">
                <template #icon-left>
                   <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                </template>
                {{ hasLocation ? t('location_on') : t('get_location') }}
             </ActionButton>
          </div>
        </div>
      </div>

      <!-- Main Results Content -->
      <main class="container mx-auto px-4 pt-12 md:pt-20 pb-32 md:pb-48">
        <div class="flex flex-col lg:flex-row gap-8 relative">
          <!-- Left: Results Grid -->
          <div class="lg:w-3/5">
            <div class="flex items-center justify-between mb-8 mt-6">
              <h2 class="text-2xl font-black text-brand-blue">
                {{ artisans.total }} <span class="text-slate-400 font-bold uppercase text-sm tracking-widest ml-2">{{ t('artisans_found') }}</span>
              </h2>
              
              <!-- Mobile Map Toggle Button -->
              <button 
                @click="showMobileMap = !showMobileMap"
                class="lg:hidden flex items-center gap-2 px-4 py-2 bg-brand-orange text-white rounded-full text-xs font-black uppercase tracking-widest shadow-lg active:scale-95 transition-transform"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ showMobileMap ? 'Show List' : 'Show Map' }}
              </button>
            </div>

            <div v-if="artisans.data.length > 0 && !showMobileMap" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <PremiumCard 
                v-for="worker in artisans.data" 
                :key="worker.id"
                @click="goToWorker(worker)"
              >
                <template #header>
                  <div class="h-48 relative overflow-hidden">
                    <img :src="worker.image" :alt="worker.name" loading="lazy" decoding="async" class="w-full h-full object-fit-cover transition-transform duration-500 group-hover:scale-110" />
                    <div class="absolute top-3 left-3 flex flex-col gap-2">
                      <TrustBadge v-if="worker.is_verified" type="verified" size="xs">{{ t('verified_badge') }}</TrustBadge>
                      <TrustBadge v-if="worker.average_rating >= 4.5" type="top" size="xs">{{ t('top_rated') }}</TrustBadge>
                    </div>
                  </div>
                </template>

                <div>
                  <div class="flex justify-between items-start mb-2">
                    <h3 class="text-lg text-brand-blue">{{ worker.name }}</h3>
                    <div v-if="worker.average_rating > 0" class="flex items-center gap-1">
                      <span class="text-brand-orange text-sm">★</span>
                      <span class="text-brand-blue text-sm font-black">{{ Number(worker.average_rating).toFixed(1) }}</span>
                    </div>
                  </div>
                  <div class="flex flex-wrap gap-2 mb-3">
                    <Link
                      v-for="cat in worker.categories"
                      :key="cat.id"
                      :href="`/${locale}/categories/${cat.slug}`"
                      @click.stop
                      class="text-brand-orange text-[10px] font-black uppercase tracking-widest hover:underline"
                    >{{ cat.name }}</Link>
                  </div>
                  <p class="text-slate-500 text-sm mb-4 line-clamp-2 leading-relaxed">{{ worker.bio }}</p>
                  
                  <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <div class="flex items-center gap-2 text-slate-400">
                      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                      <span class="text-[10px] font-bold uppercase tracking-wider">{{ worker.location }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                      <span v-if="worker.distance" class="text-[10px] font-black text-green-600 bg-green-50 px-2 py-0.5 rounded-full">{{ Number(worker.distance).toFixed(1) }}km</span>
                      <button
                        @click.stop="quickViewArtisan = worker"
                        class="text-[10px] font-black uppercase tracking-wider text-brand-orange border border-brand-orange/30 px-2 py-0.5 rounded-full hover:bg-brand-orange hover:text-white transition-colors"
                      >{{ t('quick_view') }}</button>
                    </div>
                  </div>
                </div>
              </PremiumCard>
            </div>

            <!-- Mobile Map View -->
            <div v-if="showMobileMap" class="lg:hidden rounded-3xl overflow-hidden shadow-premium border-4 border-white h-[600px] mb-8">
               <ClientOnly>
                <WorkerMap 
                  :initialWorkers="artisans.data"
                  :userLocation="hasLocation ? { lat: form.lat, lng: form.lng } : null"
                  :translations="{ km_from_you: t('km_away'), view_details: t('view_details') }"
                />
              </ClientOnly>
            </div>

            <!-- Empty State -->
            <div v-if="artisans.data.length === 0" class="py-20 text-center">
              <div class="text-6xl mb-6">🔍</div>
              <h3 class="text-2xl text-brand-blue mb-2 font-black">{{ t('no_results_title') }}</h3>
              <p class="text-slate-500 mb-8">{{ t('no_results_desc') }}</p>
              <ActionButton variant="outline" @click="clearFilters">{{ t('clear_all_filters') }}</ActionButton>
            </div>

            <!-- Pagination -->
            <div v-if="!showMobileMap" class="mt-12">
              <SmartPagination
                :currentPage="artisans.current_page"
                :lastPage="artisans.last_page"
                @go="goToPage"
              />
            </div>
          </div>

          <!-- Right: Sticky Map -->
          <div class="lg:w-2/5 hidden lg:block mt-6">
            <div class="sticky top-24 rounded-3xl overflow-hidden shadow-premium border-4 border-white h-[calc(100vh-140px)] min-h-[500px]">
              <ClientOnly>
                <WorkerMap 
                  :initialWorkers="artisans.data"
                  :userLocation="hasLocation ? { lat: form.lat, lng: form.lng } : null"
                  :translations="{ km_from_you: t('km_away'), view_details: t('view_details') }"
                />
              </ClientOnly>
            </div>
          </div>
        </div>
      </main>
    </div>

    <ArtisanQuickView :artisan="quickViewArtisan" @close="quickViewArtisan = null" />
  </MainLayout>
</template>

<script setup>
import { ref, reactive, computed, defineAsyncComponent } from 'vue';
import MainLayout from '../../Layouts/MainLayout.vue';
import SeoHead from '../../Components/SeoHead.vue';
import ActionButton from '../../Components/UI/ActionButton.vue';
import PremiumCard from '../../Components/UI/PremiumCard.vue';
import TrustBadge from '../../Components/UI/TrustBadge.vue';
import ArtisanQuickView from '../../Components/ArtisanQuickView.vue';
import SmartPagination from '../../Components/SmartPagination.vue';
const WorkerMap = defineAsyncComponent(() => import('../../Components/WorkerMap.vue'));
import { Link, router } from '@inertiajs/vue3';
import { useTranslations } from '../../Composables/useTranslations';

const { t, locale } = useTranslations();

const props = defineProps({
  artisans: Object,
  categories: Array,
  filters: Object,
  schema: Object,
});

const showMobileMap    = ref(false);
const quickViewArtisan = ref(null);

const form = reactive({
  search: props.filters.search || '',
  category_id: props.filters.category_id || '',
  min_rating: props.filters.min_rating || '',
  distance: props.filters.distance || 50,
  lat: props.filters.lat || null,
  lng: props.filters.lng || null,
  verified: props.filters.verified || false,
});

const hasLocation = computed(() => !!form.lat && !!form.lng);

function handleSearch(page = 1) {
  router.get(`/${locale.value}/search`, { ...form, page }, {
    preserveState: true,
    replace: true,
  });
}

function goToPage(page) {
  handleSearch(page);
}

function clearFilters() {
  form.search     = '';
  form.category_id = '';
  form.min_rating = '';
  form.distance   = 50;
  form.lat        = null;
  form.lng        = null;
  form.verified   = false;
  handleSearch(1);
}

function toggleLocation() {
  if (hasLocation.value) {
    form.lat = null;
    form.lng = null;
    handleSearch();
    return;
  }

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition((pos) => {
      form.lat = pos.coords.latitude;
      form.lng = pos.coords.longitude;
      handleSearch();
    }, (err) => {
      alert(t('location_error'));
    });
  }
}

function goToWorker(worker) {
  router.visit(`/artisan/${worker.slug}`);
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
.object-fit-cover {
  object-fit: cover;
}
</style>
