<template>
  <div 
    class="premium-card group"
    :class="[
      { 'hover-effect': hoverable, 'glass-morphism': glass }
    ]"
  >
    <!-- Card Header / Image Area -->
    <div v-if="$slots.header" class="card-header">
      <slot name="header"></slot>
    </div>

    <!-- Card Body -->
    <div class="card-body" :class="bodyClass">
      <slot></slot>
    </div>

    <!-- Card Footer -->
    <div v-if="$slots.footer" class="card-footer">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup>
defineProps({
  hoverable: {
    type: Boolean,
    default: true,
  },
  glass: Boolean,
  bodyClass: String,
});
</script>

<style scoped>
@reference "../../css/app.css";

.premium-card {
  @apply bg-surface rounded-2xl border border-slate-100 overflow-hidden shadow-soft transition-all duration-300;
}

.hover-effect:hover {
  @apply -translate-y-1.5 shadow-premium border-brand-orange/10;
}

.glass-morphism {
  @apply bg-white/70 backdrop-blur-lg border-white/20;
}

.card-header {
  @apply relative overflow-hidden;
}

.card-body {
  @apply p-5;
}

.card-footer {
  @apply p-5 pt-0 mt-auto;
}
</style>
