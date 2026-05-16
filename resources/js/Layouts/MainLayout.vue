<template>
  <div class="app-wrapper font-display min-h-screen bg-slate-50 flex flex-col" :class="{ rtl: isRtl }">
    <!-- Premium Header -->
    <header class="sticky top-0 z-[1001] bg-white border-b border-slate-100 h-20 flex items-center shadow-sm">
      <div class="container mx-auto px-4 flex justify-between items-center">
        <!-- Logo -->
        <Link href="/" class="flex items-center gap-2 group">
          <img src="/images/logo.png" alt="trouvemaalem logo" class="h-10 md:h-12 w-auto transition-transform group-hover:scale-105" />
        </Link>

        <!-- Desktop Nav -->
        <nav class="hidden lg:flex items-center gap-8">
          <Link 
            v-for="item in navItems" 
            :key="item.href"
            :href="item.href"
            class="text-sm font-black uppercase tracking-widest transition-all hover:text-brand-orange"
            :class="isCurrent(item.href) ? 'text-brand-orange' : 'text-brand-blue'"
          >
            {{ item.label }}
          </Link>

          <!-- Divider -->
          <div class="w-px h-6 bg-slate-200 mx-2"></div>

          <!-- Language Switcher -->
          <div class="relative group">
            <button class="flex items-center gap-2 h-10 px-4 bg-slate-50 rounded-xl border border-slate-100 text-xs font-black uppercase tracking-widest text-brand-blue transition-all hover:border-brand-orange hover:text-brand-orange">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
              <span>{{ locale }}</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <div class="absolute top-full right-0 mt-2 w-48 bg-white rounded-2xl shadow-premium border border-slate-100 overflow-hidden opacity-0 invisible translate-y-2 transition-all group-hover:opacity-100 group-hover:visible group-hover:translate-y-0">
              <button
                v-for="lang in languages"
                :key="lang.code"
                class="w-full flex items-center justify-between px-4 py-3 text-sm font-bold transition-colors hover:bg-slate-50"
                :class="locale === lang.code ? 'text-brand-orange bg-brand-orange/5' : 'text-brand-blue'"
                @click="switchLocale(lang.code)"
              >
                <span>{{ lang.name }}</span>
                <span class="text-xl">{{ lang.flag }}</span>
              </button>
            </div>
          </div>

          <ActionButton variant="primary" size="sm" :href="`/${locale}/search`">
            {{ t('find_expert') }}
          </ActionButton>
        </nav>

        <!-- Mobile Toggle (Fallback for non-nav items) -->
        <button class="lg:hidden w-10 h-10 flex flex-col items-center justify-center gap-1.5" @click="mobileMenuOpen = !mobileMenuOpen">
          <div class="w-6 h-0.5 bg-brand-blue transition-all" :class="{ 'rotate-45 translate-y-2': mobileMenuOpen }"></div>
          <div class="w-6 h-0.5 bg-brand-blue transition-all" :class="{ 'opacity-0': mobileMenuOpen }"></div>
          <div class="w-6 h-0.5 bg-brand-blue transition-all" :class="{ '-rotate-45 -translate-y-2': mobileMenuOpen }"></div>
        </button>
      </div>
    </header>

    <!-- Mobile Fullscreen Menu -->
    <Transition 
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-4"
    >
      <div v-if="mobileMenuOpen" class="fixed inset-0 z-[90] bg-white pt-24 px-6 lg:hidden">
        <div class="flex flex-col gap-6">
          <Link 
            v-for="item in navItems" 
            :key="item.href"
            :href="item.href"
            class="text-xl font-black text-brand-blue uppercase tracking-tighter"
            @click="mobileMenuOpen = false"
          >
            {{ item.label }}
          </Link>
          
          <div class="h-px bg-slate-100 my-4"></div>
          
          <div class="grid grid-cols-3 gap-4">
            <button
              v-for="lang in languages"
              :key="lang.code"
              class="flex flex-col items-center gap-2 p-4 rounded-2xl border transition-all"
              :class="locale === lang.code ? 'border-brand-orange bg-brand-orange/5 text-brand-orange' : 'border-slate-100 text-slate-400'"
              @click="switchLocale(lang.code); mobileMenuOpen = false"
            >
              <span class="text-2xl">{{ lang.flag }}</span>
              <span class="text-[10px] font-black uppercase tracking-widest">{{ lang.code }}</span>
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Main Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Premium Mobile Nav -->
    <MobileNav />

    <!-- Premium Footer -->
    <footer class="bg-brand-blue mt-20 md:mt-32 pt-16 pb-20 lg:pb-16 text-white">
      <div class="container mx-auto px-4 text-center lg:text-left">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 mb-16">
          <div class="lg:col-span-2 space-y-6">            
            <p class="text-slate-400 max-w-md mx-auto lg:mx-0 leading-relaxed">
              Le premier marketplace premium au Maroc pour les services artisanaux. Qualité, confiance et expertise à portée de clic.
            </p>
          </div>
          <div class="space-y-4">
            <h4 class="text-xs font-black uppercase tracking-widest text-brand-orange">{{ t('nav_categories') }}</h4>
            <div class="flex flex-col gap-2">
              <Link v-for="item in navItems" :key="item.href" :href="item.href" class="text-sm font-bold text-slate-300 hover:text-white">{{ item.label }}</Link>
            </div>
          </div>
          <div class="space-y-4">
            <h4 class="text-xs font-black uppercase tracking-widest text-brand-orange">Legal</h4>
            <div class="flex flex-col gap-2 text-sm font-bold text-slate-300">
              <Link :href="`/${locale}/about`">À propos</Link>
              <Link :href="`/${locale}/contact`">Contact</Link>
              <Link :href="`/${locale}/faq`">FAQ</Link>
            </div>
          </div>
        </div>
        <div class="pt-8 border-t border-white/10 flex flex-col lg:flex-row justify-between items-center gap-4">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">© {{ new Date().getFullYear() }} TROUVEMAALEM. ALL RIGHTS RESERVED.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useTranslations } from '../Composables/useTranslations';
import ActionButton from '../Components/UI/ActionButton.vue';
import MobileNav from '../Components/UI/MobileNav.vue';

const { t, locale, isRtl } = useTranslations();
const page = usePage();

const mobileMenuOpen = ref(false);

const languages = [
  { code: 'en', name: 'English', flag: '🇬🇧' },
  { code: 'fr', name: 'Français', flag: '🇫🇷' },
  { code: 'ar', name: 'العربية', flag: '🇲🇦' },
];

const navItems = computed(() => [
  { label: t('nav_home'), href: `/${locale.value}` },
  { label: t('nav_categories'), href: `/${locale.value}/categories` },
  { label: t('nav_search'), href: `/${locale.value}/search` },
  { label: t('nav_blog'), href: `/${locale.value}/blog` },
]);

const isCurrent = (href) => {
  const currentPath = page.url;
  if (href === `/${locale.value}`) return currentPath === `/${locale.value}` || currentPath === `/${locale.value}/`;
  return currentPath.startsWith(href);
};

function switchLocale(code) {
  const segments = window.location.pathname.split('/').filter(s => s !== '');
  const availableLocales = languages.map(l => l.code);
  if (segments.length > 0 && availableLocales.includes(segments[0])) {
    segments[0] = code;
  } else {
    segments.unshift(code);
  }
  const newPath = '/' + segments.join('/');
  router.visit(newPath, { preserveScroll: true });
}
</script>

<style>
/* Global CSS Base */
:root {
  --primary-color: #d78126;
  --primary-hover: #cba346;
  --bg-color: #f8fafc;
  --text-main: #113559;
  --text-muted: #64748b;
  --nav-bg: #ffffff;
}

body {
  margin: 0;
  font-family: 'Inter', sans-serif;
  background-color: var(--bg-color);
  color: var(--text-main);
  -webkit-font-smoothing: antialiased;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.app-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* RTL Support */
.app-wrapper.rtl {
  direction: rtl;
  text-align: right;
}

.app-wrapper.rtl .main-nav a {
  margin-left: 0;
  margin-right: 2rem;
}

.header {
  position: sticky;
  top: 0;
  z-index: 1001;
  background-color: var(--nav-bg);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid #e2e8f0;
}

.nav-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 4rem;
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo {
  display: flex;
  align-items: center;
  text-decoration: none;
}

.logo-img {
  height: 56px; /* Adjust height based on actual logo proportion */
  width: auto;
  object-fit: contain;
}

.main-nav a {
  margin-left: 2rem;
  color: var(--text-main);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.main-nav a:hover {
  color: var(--primary-color);
}

.main-content {
  flex: 1;
  padding: 2rem 0;
}

.footer {
  background-color: #ffffff;
  border-top: 1px solid #e2e8f0;
  padding: 3rem 0;
  color: var(--text-muted);
}

.footer-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
}

.footer-links {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 2rem;
}

.footer-links a {
  color: var(--text-main);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: var(--primary-color);
}

.copyright {
  margin: 0;
  font-size: 0.9rem;
}

/* =========== Responsive Visibility =========== */
.mobile-only { display: none !important; }

@media (max-width: 1024px) {
  .desktop-only { display: none !important; }
  .mobile-only { display: flex !important; }
}

/* =========== Burger Button =========== */
.burger-btn {
  width: 40px;
  height: 40px;
  border: none;
  background: none;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 5px;
  padding: 8px;
  border-radius: 10px;
  transition: background 0.2s;
  z-index: 60;
}

.burger-btn:hover {
  background: #f1f5f9;
}

.burger-line {
  display: block;
  width: 22px;
  height: 2.5px;
  background: var(--text-main);
  border-radius: 2px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  transform-origin: center;
}

/* Animated X when open */
.burger-btn.active .burger-line:nth-child(1) {
  transform: translateY(7.5px) rotate(45deg);
}
.burger-btn.active .burger-line:nth-child(2) {
  opacity: 0;
  transform: scaleX(0);
}
.burger-btn.active .burger-line:nth-child(3) {
  transform: translateY(-7.5px) rotate(-45deg);
}

/* =========== Mobile Menu =========== */
.mobile-menu-overlay {
  position: fixed;
  inset: 0;
  top: 4rem;
  background: rgba(15, 23, 42, 0.4);
  backdrop-filter: blur(4px);
  z-index: 40;
}

.mobile-menu {
  background: white;
  border-bottom: 1px solid #e2e8f0;
  padding: 1rem 0;
  box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
}

.mobile-nav {
  display: flex;
  flex-direction: column;
  padding: 0.5rem 1.5rem;
}

.mobile-nav-link {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 1rem 1rem;
  border-radius: 12px;
  color: var(--text-main);
  text-decoration: none;
  font-weight: 600;
  font-size: 1.05rem;
  transition: all 0.15s;
}

.mobile-nav-link:hover,
.mobile-nav-link:active {
  background: #f8fafc;
  color: var(--primary-color);
}

.mobile-nav-link svg {
  flex-shrink: 0;
  color: var(--text-muted);
}

.mobile-nav-link:hover svg {
  color: var(--primary-color);
}

.mobile-divider {
  height: 1px;
  background: #e2e8f0;
  margin: 0.5rem 1.5rem;
}

.mobile-lang-section {
  display: flex;
  flex-direction: column;
  padding: 0.5rem 1.5rem;
}

.mobile-lang-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.85rem 1rem;
  border: none;
  background: none;
  font-size: 1rem;
  color: var(--text-main);
  cursor: pointer;
  border-radius: 12px;
  transition: background 0.15s;
  font-family: inherit;
  text-align: left;
}

.app-wrapper.rtl .mobile-lang-btn {
  text-align: right;
}

.mobile-lang-btn:hover {
  background: #f8fafc;
}

.mobile-lang-btn.active {
  background: rgba(215, 129, 38, 0.1);
  color: var(--primary-color);
  font-weight: 600;
}

.mobile-lang-btn .lang-flag {
  font-size: 1.3rem;
}

.mobile-lang-btn .lang-label {
  flex: 1;
}

/* =========== Mobile Menu Transition =========== */
.mobile-menu-enter-active {
  transition: opacity 0.2s ease;
}
.mobile-menu-enter-active .mobile-menu {
  transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.mobile-menu-leave-active {
  transition: opacity 0.2s ease;
}
.mobile-menu-leave-active .mobile-menu {
  transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.mobile-menu-enter-from {
  opacity: 0;
}
.mobile-menu-enter-from .mobile-menu {
  transform: translateY(-10px);
}
.mobile-menu-leave-to {
  opacity: 0;
}
.mobile-menu-leave-to .mobile-menu {
  transform: translateY(-10px);
}

/* =========== Language Switcher (Desktop) =========== */
.lang-switcher {
  position: relative;
}

.lang-toggle {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.45rem 0.85rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  background: white;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--text-main);
  cursor: pointer;
  transition: all 0.2s;
  font-family: inherit;
}

.lang-toggle:hover {
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.lang-current {
  font-size: 0.8rem;
  letter-spacing: 0.05em;
}

.lang-dropdown {
  position: absolute;
  top: calc(100% + 6px);
  right: 0;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 40px -8px rgba(0, 0, 0, 0.15);
  border: 1px solid #e2e8f0;
  overflow: hidden;
  min-width: 180px;
  z-index: 100;
  animation: dropdownIn 0.15s ease-out;
}

.app-wrapper.rtl .lang-dropdown {
  right: auto;
  left: 0;
}

@keyframes dropdownIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}

.lang-option {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  width: 100%;
  padding: 0.7rem 1rem;
  border: none;
  background: none;
  font-size: 0.9rem;
  color: var(--text-main);
  cursor: pointer;
  transition: background 0.15s;
  font-family: inherit;
  text-align: left;
}

.app-wrapper.rtl .lang-option {
  text-align: right;
}

.lang-option:hover {
  background: #f8fafc;
}

.lang-option.active {
  background: rgba(215, 129, 38, 0.1);
  color: var(--primary-color);
  font-weight: 600;
}

.lang-flag {
  font-size: 1.2rem;
  line-height: 1;
}

.lang-label {
  flex: 1;
}
</style>
