<template>
  <div v-if="lastPage > 1" class="flex items-center justify-center gap-1 flex-wrap relative">
    <!-- Previous -->
    <button
      @click="go(currentPage - 1)"
      :disabled="currentPage <= 1"
      class="w-10 h-10 flex items-center justify-center rounded-xl font-black text-sm transition-all"
      :class="currentPage <= 1 ? 'text-slate-300 cursor-not-allowed' : 'bg-white text-brand-blue hover:bg-brand-orange hover:text-white shadow-sm'"
    >«</button>

    <!-- Page items -->
    <template v-for="item in paginationItems" :key="item.key">
      <!-- Page number -->
      <button
        v-if="item.type === 'page'"
        @click="go(item.page)"
        class="w-10 h-10 flex items-center justify-center rounded-xl font-black text-sm transition-all shadow-sm"
        :class="item.active
          ? 'bg-brand-orange text-white'
          : 'bg-white text-brand-blue hover:bg-brand-orange hover:text-white'"
      >{{ item.page }}</button>

      <!-- Dots -->
      <div v-else class="relative">
        <button
          @click="toggleJump(item.key)"
          class="w-10 h-10 flex items-center justify-center rounded-xl font-black text-slate-400 bg-white hover:bg-slate-50 shadow-sm transition-all text-lg tracking-tighter"
          title="Aller à la page…"
        >···</button>

        <!-- Jump input popup -->
        <div
          v-if="activeJump === item.key"
          class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-white rounded-2xl shadow-xl border border-slate-100 p-3 flex items-center gap-2 z-10 min-w-[140px]"
        >
          <input
            ref="jumpInputRef"
            v-model.number="jumpPage"
            type="number"
            :min="1"
            :max="lastPage"
            :placeholder="`1–${lastPage}`"
            class="w-16 h-8 border border-slate-200 rounded-lg px-2 text-sm font-bold text-brand-blue text-center focus:outline-none focus:ring-2 focus:ring-brand-orange/30"
            @keyup.enter="doJump"
            @keyup.escape="activeJump = null"
          />
          <button
            @click="doJump"
            class="h-8 px-3 bg-brand-orange text-white text-xs font-black rounded-lg hover:bg-brand-blue transition-colors"
          >OK</button>
        </div>
      </div>
    </template>

    <!-- Next -->
    <button
      @click="go(currentPage + 1)"
      :disabled="currentPage >= lastPage"
      class="w-10 h-10 flex items-center justify-center rounded-xl font-black text-sm transition-all"
      :class="currentPage >= lastPage ? 'text-slate-300 cursor-not-allowed' : 'bg-white text-brand-blue hover:bg-brand-orange hover:text-white shadow-sm'"
    >»</button>
  </div>
</template>

<script setup>
import { computed, ref, nextTick } from 'vue';

const props = defineProps({
  currentPage: { type: Number, required: true },
  lastPage:    { type: Number, required: true },
});

const emit = defineEmits(['go']);

const activeJump   = ref(null);
const jumpPage     = ref('');
const jumpInputRef = ref(null);

function go(page) {
  if (page < 1 || page > props.lastPage) return;
  activeJump.value = null;
  emit('go', page);
}

async function toggleJump(key) {
  if (activeJump.value === key) {
    activeJump.value = null;
    return;
  }
  activeJump.value = key;
  jumpPage.value   = '';
  await nextTick();
  jumpInputRef.value?.focus();
}

function doJump() {
  const p = Number(jumpPage.value);
  if (p >= 1 && p <= props.lastPage) go(p);
}

const paginationItems = computed(() => {
  const cur  = props.currentPage;
  const last = props.lastPage;

  if (last <= 7) {
    return Array.from({ length: last }, (_, i) => ({
      type: 'page', page: i + 1, active: i + 1 === cur, key: `p${i + 1}`,
    }));
  }

  // Pages to always show
  const show = new Set([
    1, 2,
    Math.max(1, cur - 1), cur, Math.min(last, cur + 1),
    last - 1, last,
  ]);

  const sorted = Array.from(show).filter(p => p >= 1 && p <= last).sort((a, b) => a - b);
  const items  = [];
  let prev     = 0;

  for (const page of sorted) {
    if (page - prev > 1) {
      items.push({ type: 'dots', key: `dots${prev}-${page}` });
    }
    items.push({ type: 'page', page, active: page === cur, key: `p${page}` });
    prev = page;
  }

  return items;
});
</script>
