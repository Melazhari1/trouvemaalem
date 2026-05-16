<template>
  <Teleport to="body">
    <Transition name="qv">
      <div v-if="artisan" class="fixed inset-0 z-[9900] flex items-center justify-center p-4" @keydown.escape.window="$emit('close')">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="$emit('close')" />

        <!-- Modal card -->
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden">
          <!-- Photo -->
          <div class="relative h-64 bg-slate-100">
            <img
              v-if="artisan.image"
              :src="artisan.image"
              :alt="artisan.name"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
              <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </div>

            <!-- Close -->
            <button
              @click="$emit('close')"
              class="absolute top-3 right-3 w-8 h-8 bg-white/90 backdrop-blur-md rounded-full flex items-center justify-center text-slate-500 hover:text-brand-blue shadow transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>

            <!-- Verified -->
            <div v-if="artisan.is_verified" class="absolute top-3 left-3">
              <span class="bg-brand-orange text-white text-[10px] font-black px-2 py-1 rounded-full uppercase tracking-widest">✓ {{ t('verified_badge') }}</span>
            </div>

            <!-- Rating -->
            <div v-if="ratingValue > 0" class="absolute bottom-3 right-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full shadow flex items-center gap-1">
              <span class="text-brand-orange font-black text-sm">★</span>
              <span class="text-brand-blue font-black text-sm">{{ ratingValue.toFixed(1) }}</span>
            </div>
          </div>

          <!-- Info -->
          <div class="p-6">
            <h3 class="text-2xl font-black text-brand-blue mb-2">{{ artisan.name }}</h3>

            <div v-if="artisan.city" class="flex items-center gap-2 text-slate-400 mb-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
              <span class="text-sm font-bold">{{ artisan.city }}</span>
            </div>

            <div v-if="(artisan.categories ?? []).length" class="flex flex-wrap gap-2 mb-6">
              <span
                v-for="cat in artisan.categories"
                :key="cat.id"
                class="bg-brand-orange/10 text-brand-orange text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider"
              >{{ cat.name }}</span>
            </div>
            <div v-else class="mb-6"></div>

            <Link
              :href="`/${locale}/artisan/${artisan.slug}`"
              class="block text-center bg-brand-blue text-white font-black py-3 rounded-xl hover:bg-brand-orange transition-colors duration-200 text-sm uppercase tracking-widest"
            >
              {{ t('view_profile') }} →
            </Link>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '../Composables/useTranslations';

const { t, locale } = useTranslations();

const props = defineProps({
  artisan: { type: Object, default: null },
});

defineEmits(['close']);

const ratingValue = computed(() =>
  Number(props.artisan?.average_rating ?? props.artisan?.rating ?? 0)
);
</script>

<style scoped>
.qv-enter-active,
.qv-leave-active {
  transition: opacity 0.2s ease;
}
.qv-enter-from,
.qv-leave-to {
  opacity: 0;
}
.qv-enter-active .relative,
.qv-leave-active .relative {
  transition: transform 0.2s ease;
}
.qv-enter-from .relative,
.qv-leave-to .relative {
  transform: scale(0.95);
}
</style>
