<template>
  <MainLayout>
    <SeoHead
      v-if="artisan"
      :title="artisan ? `${artisan.name} - ${(artisan.categories ?? []).map(c => c?.name ?? '').filter(Boolean).join(', ')}` : ''"
      :description="artisan?.bio ?? ''"
      :image="artisan?.image ?? ''"
      ogType="profile"
      :schema="schema"
    />

    <div class="bg-slate-50 min-h-screen pb-24">
      <!-- Profile Hero Area -->
      <section class="bg-brand-blue pt-20 pb-32 md:pt-24 md:pb-48 text-white relative overflow-hidden">
        <div class="absolute inset-0 z-0">
          <div class="absolute top-0 right-0 w-96 h-96 bg-brand-orange/10 rounded-full blur-3xl"></div>
        </div>
        
        <div class="container relative z-10 px-4">
          <div class="flex flex-col md:flex-row items-center md:items-end gap-8">
            <div class="w-48 h-48 md:w-64 md:h-64 rounded-3xl overflow-hidden border-8 border-white/10 shadow-premium flex-shrink-0">
              <img :src="artisan.image" :alt="artisan.name" class="w-full h-full object-fit-cover" />
            </div>
            
            <div class="flex-1 text-center md:text-left pb-4">
              <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-4">
                <Link
                  v-for="cat in (artisan.categories ?? []).filter(Boolean)"
                  :key="cat.id"
                  :href="`/${locale}/categories/${cat.slug}`"
                  class="text-brand-orange text-xs font-black uppercase tracking-widest hover:underline"
                >{{ cat.name }}</Link>
                <TrustBadge v-if="artisan.is_verified" type="verified" size="sm">{{ t('verified_badge') }}</TrustBadge>
                <TrustBadge v-if="artisan.rating >= 4.5" type="top" size="sm">{{ t('top_rated') }}</TrustBadge>
              </div>
              <h1 class="text-4xl md:text-6xl font-black mb-4">{{ artisan.name }}</h1>
              <div class="flex items-center justify-center md:justify-start gap-4 text-slate-300">
                <div class="flex items-center gap-1.5">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                  <span class="text-sm font-bold">{{ [artisan.location, artisan.city].filter(Boolean).join(', ') }}</span>
                </div>
                <div v-if="artisan.rating > 0" class="flex items-center gap-1.5">
                  <span class="text-brand-orange font-black">★</span>
                  <span class="text-sm font-bold">{{ Number(artisan.rating).toFixed(1) }} ({{ artisan.reviews?.length || 0 }} {{ t('rating') }})</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Main Content Grid -->
      <main class="container mx-auto px-4 -mt-16 md:-mt-24 relative z-20 pb-32 md:pb-48">
        <div class="flex flex-col lg:flex-row gap-12 md:gap-16">
          
          <!-- Left Column: Bio, Map, Reviews -->
          <div class="lg:w-2/3 space-y-12 md:space-y-20">
            <PremiumCard :hoverable="false" bodyClass="p-8 md:p-12">
              <h2 class="text-2xl font-black text-brand-blue mb-8">{{ t('artisan_profile') }}</h2>
              <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed text-lg">
                {{ artisan.bio }}
              </div>
            </PremiumCard>

            <!-- Map Section -->
            <PremiumCard :hoverable="false" bodyClass="p-0 overflow-hidden">
               <div class="p-8 border-b border-slate-100">
                 <h2 class="text-2xl font-black text-brand-blue mb-3">{{ t('location_label') }}</h2>
                 <div v-if="artisan.locations?.length" class="space-y-1">
                   <p
                     v-for="(loc, i) in artisan.locations"
                     :key="i"
                     class="text-slate-500 text-sm font-medium"
                   >{{ loc[locale] || loc.en || loc.fr || '' }}</p>
                 </div>
               </div>
               <div class="h-[400px] md:h-[500px]">
                 <ClientOnly>
                    <LeafletMap
                      v-if="artisan.lat && artisan.lng"
                      :lat="Number(artisan.lat)"
                      :lng="Number(artisan.lng)"
                      :popupText="artisan.name"
                      height="100%"
                    />
                  </ClientOnly>
               </div>
            </PremiumCard>

            <!-- Reviews Section -->
            <section id="reviews" class="space-y-8 md:space-y-12">
              <div class="flex items-center justify-between">
                <h2 class="text-2xl font-black text-brand-blue">{{ t('rating') }} ({{ artisan.reviews?.length || 0 }})</h2>
              </div>

              <div v-if="artisan.reviews && artisan.reviews.length > 0" class="space-y-6 md:space-y-8">
                <PremiumCard v-for="review in artisan.reviews" :key="review.id" :hoverable="false" bodyClass="p-8">
                  <div class="flex gap-6">
                    <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-brand-blue font-black shrink-0 text-xl">
                      {{ (review.submitted_by_name || review.user?.name || 'A').charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1">
                      <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-brand-blue text-lg">{{ review.submitted_by_name || review.user?.name || t('review_anonymous') }}</span>
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">{{ new Date(review.created_at).toLocaleDateString() }}</span>
                      </div>
                      <div class="flex gap-1 mb-4">
                        <span v-for="s in 5" :key="s" class="text-sm" :class="s <= review.rating ? 'text-brand-orange' : 'text-slate-200'">★</span>
                      </div>
                      <p class="text-slate-600 text-base leading-relaxed">{{ review.comment }}</p>
                    </div>
                  </div>
                </PremiumCard>
              </div>
              <div v-else class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                <p class="text-slate-400 font-bold text-lg">{{ t('review_no_reviews') }}</p>
              </div>

              <!-- Submit a Review -->
              <ReviewFormSubmit :artisanId="artisan.id" />
            </section>
          </div>

          <!-- Right Column: Sticky Contact Sidebar -->
          <div class="lg:w-1/3">
            <div class="sticky top-24 space-y-6">
              <PremiumCard :hoverable="false" bodyClass="p-8">
                <div class="text-center mb-8">
                  <div class="text-sm font-black text-slate-400 uppercase tracking-widest mb-2">{{ t('contact_artisan') }}</div>
                  <h3 class="text-3xl font-black text-brand-blue">{{ artisan.name }}</h3>
                </div>

                <div class="space-y-4">
                  <ActionButton
                    v-if="artisan.whatsapp"
                    variant="whatsapp"
                    size="lg"
                    fullWidth
                    :href="`https://wa.me/${artisan.whatsapp.replace(/\D/g, '')}`"
                    target="_blank"
                  >
                    <template #icon-left>
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 448 512" fill="currentColor"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 0.9-6.9-0.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-0.2-6.9-0.2-10.6-0.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                    </template>
                    {{ t('btn_whatsapp') }}
                  </ActionButton>

                  <ActionButton
                    v-if="artisan.phone"
                    variant="secondary"
                    size="lg"
                    fullWidth
                    :href="`tel:${artisan.phone}`"
                  >
                    <template #icon-left>
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </template>
                    {{ t('btn_call') }}
                  </ActionButton>
                </div>

                <div class="mt-8 pt-8 border-t border-slate-100">
                   <div class="flex items-center gap-4 text-slate-500 mb-4">
                     <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                     <span class="text-sm font-bold uppercase tracking-wider">{{ t('verified_badge') }} Member</span>
                   </div>
                   <div class="flex items-center gap-4 text-slate-500">
                     <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                     <span class="text-sm font-bold uppercase tracking-wider">{{ t('fast_responder') }}</span>
                   </div>
                </div>
              </PremiumCard>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Sticky Mobile Contact Bar -->
    <div v-if="artisan.whatsapp || artisan.phone" class="fixed bottom-0 left-0 right-0 z-50 lg:hidden bg-white/90 backdrop-blur-xl border-t border-slate-100 p-4 flex gap-3 shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.1)]">
      <ActionButton
        v-if="artisan.whatsapp"
        variant="whatsapp"
        size="md"
        class="flex-1"
        :href="`https://wa.me/${artisan.whatsapp.replace(/\D/g, '')}`"
        target="_blank"
      >
        <template #icon-left>
           <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 448 512" fill="currentColor"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 0.9-6.9-0.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-0.2-6.9-0.2-10.6-0.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
        </template>
        {{ t('btn_whatsapp') }}
      </ActionButton>
      <ActionButton
        v-if="artisan.phone"
        variant="secondary"
        size="md"
        class="flex-1"
        :href="`tel:${artisan.phone}`"
      >
        <template #icon-left>
           <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </template>
        {{ t('btn_call') }}
      </ActionButton>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import MainLayout from '../../Layouts/MainLayout.vue';
import SeoHead from '../../Components/SeoHead.vue';
import ArtisanSkeleton from '../../Components/ArtisanSkeleton.vue';
import LeafletMap from '../../Components/LeafletMap.vue';
import ActionButton from '../../Components/UI/ActionButton.vue';
import PremiumCard from '../../Components/UI/PremiumCard.vue';
import TrustBadge from '../../Components/UI/TrustBadge.vue';
import ReviewFormSubmit from '../../Components/ReviewFormSubmit.vue';
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '../../Composables/useTranslations';

const { t, locale } = useTranslations();

const props = defineProps({
  artisan: Object,
  schema: Object,
});

const loading = ref(true);

onMounted(() => {
  setTimeout(() => {
    loading.value = false;
  }, 300);
});
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
.shadow-premium {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
</style>
