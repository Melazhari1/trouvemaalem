<template>
  <Head>
    <!-- Standard Meta Tags -->
    <title>{{ computedTitle }}</title>
    <meta name="description" :content="computedDescription" />
    <link rel="canonical" :href="computedCanonical" />
    <meta name="robots" :content="robots" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" :content="ogType" />
    <meta property="og:url" :content="computedCanonical" />
    <meta property="og:title" :content="computedTitle" />
    <meta property="og:description" :content="computedDescription" />
    <meta property="og:image" :content="computedImage" />
    <meta property="og:site_name" content="trouvemaalem" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" :content="computedCanonical" />
    <meta name="twitter:title" :content="computedTitle" />
    <meta name="twitter:description" :content="computedDescription" />
    <meta name="twitter:image" :content="computedImage" />
    <meta name="twitter:site" content="@trouvemaalem" />

    <!-- JSON-LD Structured Data -->
    <component 
      v-if="schema" 
      is="script" 
      type="application/ld+json" 
      v-html="JSON.stringify(schema)"
    ></component>

    <!-- Slot for extra tags -->
    <slot />
  </Head>
</template>

<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  image: {
    type: String,
    default: ''
  },
  canonical: {
    type: String,
    default: ''
  },
  ogType: {
    type: String,
    default: 'website'
  },
  robots: {
    type: String,
    default: 'index, follow'
  },
  schema: {
    type: Object,
    default: null
  }
});

const page = usePage();
const defaultTitle = 'trouvemaalem - Artisans Experts & Services Premium au Maroc';
const defaultDescription = 'La plateforme N°1 pour trouver des artisans qualifiés au Maroc. Plombiers, électriciens, peintres et plus, sélectionnés pour leur excellence.';
const defaultImage = '/images/og-main.jpg';

const computedTitle = computed(() => props.title ? `${props.title} | trouvemaalem` : defaultTitle);
const computedDescription = computed(() => props.description || defaultDescription);
const computedImage = computed(() => props.image || defaultImage);
const computedCanonical = computed(() => props.canonical || (window.location.origin + page.url));
</script>
