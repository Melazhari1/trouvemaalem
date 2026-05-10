<template>
  <MainLayout>
    <SeoHead
      v-if="artisan"
      :title="`${artisan.name} - ${artisan.category.name}`"
      :description="artisan.bio"
      :image="artisan.image"
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
                <Link :href="`/categories/${artisan.category.slug}`" class="text-brand-orange text-xs font-black uppercase tracking-widest hover:underline">{{ artisan.category.name }}</Link>
                <TrustBadge v-if="artisan.is_verified" type="verified" size="sm">{{ t('verified_badge') }}</TrustBadge>
                <TrustBadge v-if="artisan.rating >= 4.5" type="top" size="sm">{{ t('top_rated') }}</TrustBadge>
              </div>
              <h1 class="text-4xl md:text-6xl font-black mb-4">{{ artisan.name }}</h1>
              <div class="flex items-center justify-center md:justify-start gap-4 text-slate-300">
                <div class="flex items-center gap-1.5">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                  <span class="text-sm font-bold">{{ artisan.location }}, {{ artisan.city }}</span>
                </div>
                <div class="flex items-center gap-1.5">
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
                 <h2 class="text-2xl font-black text-brand-blue">{{ t('location_label') }}</h2>
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
                    variant="whatsapp" 
                    size="lg" 
                    fullWidth 
                    :href="`https://wa.me/${artisan.phone}`"
                    target="_blank"
                  >
                    <template #icon-left>
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.522.902 3.19 1.379 4.894 1.38h.005c5.454 0 9.893-4.438 9.896-9.891.002-2.646-1.03-5.127-2.903-7.004-1.873-1.877-4.355-2.909-7.001-2.91-5.451 0-9.891 4.438-9.894 9.892-.001 1.83.504 3.614 1.461 5.193l-.952 3.474 3.589-.944zm11.396-7.391c-.328-.164-1.94-.957-2.24-.1.066-.3-.329-.164-.328.164.1-.246.334-.415.547-.41.246.224.164.246-.104-.328-.164-.334-.415-.547-.41s-.437.058-.765.222c-.328.164-1.397.515-2.651 1.634-1.022.912-1.712 2.037-1.913 2.381s-.021.53.144.694c.148.148.328.383.492.574s.219.328.328.547c.11.219.055.41-.027.574-.082.164-.738 1.776-.984 2.377-.245.601-.492.519-.683.53-.175.011-.383.011-.59.011-.208 0-.547.078-.831.383s-1.082 1.057-1.082 2.578 1.115 3.007 1.262 3.205c.148.197 2.197 3.355 5.323 4.707.743.322 1.325.515 1.777.659.748.237 1.429.204 1.969.123.6-.09 1.94-.792 2.213-1.557.273-.765.273-1.421.191-1.557-.082-.136-.3-.218-.628-.382z"/></svg>
                    </template>
                    {{ t('btn_whatsapp') }}
                  </ActionButton>

                  <ActionButton 
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
    <div class="fixed bottom-0 left-0 right-0 z-50 lg:hidden bg-white/90 backdrop-blur-xl border-t border-slate-100 p-4 flex gap-3 shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.1)]">
      <ActionButton 
        variant="whatsapp" 
        size="md" 
        class="flex-1"
        :href="`https://wa.me/${artisan.phone}`"
        target="_blank"
      >
        <template #icon-left>
           <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.835c1.522.902 3.19 1.379 4.894 1.38h.005c5.454 0 9.893-4.438 9.896-9.891.002-2.646-1.03-5.127-2.903-7.004-1.873-1.877-4.355-2.909-7.001-2.91-5.451 0-9.891 4.438-9.894 9.892-.001 1.83.504 3.614 1.461 5.193l-.952 3.474 3.589-.944zm11.396-7.391c-.328-.164-1.94-.957-2.24-.1.066-.3-.329-.164-.328.164.1-.246.334-.415.547-.41.246.224.164.246-.104-.328-.164-.334-.415-.547-.41s-.437.058-.765.222c-.328.164-1.397.515-2.651 1.634-1.022.912-1.712 2.037-1.913 2.381s-.021.53.144.694c.148.148.328.383.492.574s.219.328.328.547c.11.219.055.41-.027.574-.082.164-.738 1.776-.984 2.377-.245.601-.492.519-.683.53-.175.011-.383.011-.59.011-.208 0-.547.078-.831.383s-1.082 1.057-1.082 2.578 1.115 3.007 1.262 3.205c.148.197 2.197 3.355 5.323 4.707.743.322 1.325.515 1.777.659.748.237 1.429.204 1.969.123.6-.09 1.94-.792 2.213-1.557.273-.765.273-1.421.191-1.557-.082-.136-.3-.218-.628-.382z"/></svg>
        </template>
        {{ t('btn_whatsapp') }}
      </ActionButton>
      <ActionButton 
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

const { t } = useTranslations();

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
