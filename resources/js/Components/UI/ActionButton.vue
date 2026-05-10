<template>
  <component
    :is="isLink ? 'Link' : 'button'"
    :href="href"
    class="action-button"
    :class="[
      `variant-${variant}`,
      `size-${size}`,
      { 'w-full': fullWidth, 'opacity-50 cursor-not-allowed': disabled }
    ]"
    :disabled="disabled"
    @click="$emit('click', $event)"
  >
    <span v-if="loading" class="loader"></span>
    <span v-else class="flex items-center justify-center gap-2">
      <slot name="icon-left"></slot>
      <slot></slot>
      <slot name="icon-right"></slot>
    </span>
  </component>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  variant: {
    type: String,
    default: 'primary', // primary, secondary, outline, ghost, white, whatsapp
  },
  size: {
    type: String,
    default: 'md', // sm, md, lg, xl
  },
  href: String,
  disabled: Boolean,
  loading: Boolean,
  fullWidth: Boolean,
});

const isLink = computed(() => !!props.href);

defineEmits(['click']);
</script>

<style scoped>
@reference "../../css/app.css";

.action-button {
  @apply relative inline-flex items-center justify-center font-display font-bold transition-all duration-300 rounded-xl cursor-pointer active:scale-95;
}

/* Variants */
.variant-primary {
  @apply bg-brand-orange text-white shadow-lg shadow-brand-orange/20 hover:bg-brand-orange-light hover:shadow-brand-orange/30 hover:-translate-y-0.5;
}

.variant-secondary {
  @apply bg-brand-blue text-white shadow-lg shadow-brand-blue/20 hover:bg-brand-blue-light hover:shadow-brand-blue/30 hover:-translate-y-0.5;
}

.variant-outline {
  @apply bg-transparent border-2 border-brand-blue text-brand-blue hover:bg-brand-blue hover:text-white;
}

.variant-ghost {
  @apply bg-transparent text-brand-blue hover:bg-brand-blue/5;
}

.variant-white {
  @apply bg-white text-brand-blue shadow-soft hover:shadow-premium hover:-translate-y-0.5;
}

.variant-whatsapp {
  @apply bg-[#25D366] text-white shadow-lg shadow-[#25D366]/20 hover:bg-[#22c35e] hover:shadow-[#25D366]/30 hover:-translate-y-0.5;
}

/* Sizes */
.size-sm { @apply px-4 py-2 text-sm; }
.size-md { @apply px-6 py-3 text-base; }
.size-lg { @apply px-8 py-4 text-lg; }
.size-xl { @apply px-10 py-5 text-xl; }

.loader {
  @apply w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin;
}
</style>
