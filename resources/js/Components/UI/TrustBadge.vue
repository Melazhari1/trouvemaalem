<template>
  <div 
    class="trust-badge"
    :class="[`type-${type}`, size]"
  >
    <div class="icon-wrapper">
      <slot name="icon">
        <!-- Default icons based on type -->
        <svg v-if="type === 'verified'" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <svg v-else-if="type === 'top'" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg v-else-if="type === 'fast'" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
      </slot>
    </div>
    <span class="label"><slot>{{ label }}</slot></span>
  </div>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'verified', // verified, top, fast, custom
  },
  label: String,
  size: {
    type: String,
    default: 'sm', // xs, sm, md
  }
});
</script>

<style scoped>
@reference "../../css/app.css";

.trust-badge {
  @apply inline-flex items-center gap-1.5 font-bold rounded-full tracking-tight;
}

/* Types */
.type-verified {
  @apply bg-blue-50 text-blue-600;
}

.type-top {
  @apply bg-brand-gold/10 text-brand-gold;
}

.type-fast {
  @apply bg-green-50 text-green-600;
}

/* Sizes */
.xs { @apply px-2 py-0.5 text-[10px]; }
.sm { @apply px-2.5 py-1 text-[11px]; }
.md { @apply px-3.5 py-1.5 text-xs; }

.icon-wrapper {
  @apply flex items-center justify-center;
}

.label {
  @apply uppercase;
}
</style>
