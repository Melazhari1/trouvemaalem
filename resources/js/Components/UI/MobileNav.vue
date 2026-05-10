<template>
  <nav class="fixed bottom-0 left-0 right-0 z-50 lg:hidden bg-white/80 backdrop-blur-xl border-t border-slate-100 px-6 py-3 flex justify-between items-center shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.1)]">
    <Link 
      v-for="item in navItems" 
      :key="item.href"
      :href="item.href"
      class="flex flex-col items-center gap-1 group transition-all"
      :class="[isCurrent(item.href) ? 'text-brand-orange' : 'text-slate-400']"
    >
      <div 
        class="p-2 rounded-xl transition-all"
        :class="[isCurrent(item.href) ? 'bg-brand-orange/10 scale-110' : 'group-hover:bg-slate-50']"
      >
        <component :is="item.icon" class="w-6 h-6" stroke-width="2.5" />
      </div>
      <span class="text-[10px] font-black uppercase tracking-widest">{{ item.label }}</span>
    </Link>
  </nav>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useTranslations } from '../../Composables/useTranslations';

const { t, locale } = useTranslations();
const page = usePage();

const isCurrent = (href) => {
  const currentPath = page.url;
  if (href === `/${locale.value}`) return currentPath === `/${locale.value}` || currentPath === `/${locale.value}/`;
  return currentPath.startsWith(href);
};

const navItems = [
  { 
    label: t('nav_home'), 
    href: `/${locale.value}`, 
    icon: {
      template: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`
    }
  },
  { 
    label: t('nav_categories'), 
    href: `/${locale.value}/categories`, 
    icon: {
      template: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>`
    }
  },
  { 
    label: t('artisan_profile'), 
    href: `/${locale.value}/about`, 
    icon: {
      template: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>`
    }
  }
];
</script>
