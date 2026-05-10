<template>
  <MainLayout>
    <SeoHead
      :title="`${post.title} - Blog trouvemaalem`"
      :description="post.excerpt"
    />

    <!-- Article Header -->
    <section class="article-header">
      <div class="container article-header-inner">
        <Link :href="`/${locale}/blog`" class="back-link">← Retour au blog</Link>
        <div class="article-meta">
          <span class="article-date">{{ new Date(post.created_at).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
        </div>
        <h1 class="article-title">{{ post.title }}</h1>
      </div>
    </section>

    <!-- Article Content -->
    <section class="container section">
      <div class="article-container">
        <!-- Featured Image -->
        <div class="article-featured-image" v-if="post.image">
          <img :src="post.image" :alt="post.title" />
        </div>
        
        <!-- Post Content -->
        <div class="article-content" v-html="formattedContent"></div>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import MainLayout from '../../Layouts/MainLayout.vue';
import SeoHead from '../../Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '../../Composables/useTranslations';

const { locale } = useTranslations();

const props = defineProps({
  post: {
    type: Object,
    required: true,
  }
});

// A simple formatter for Markdown-like bold text and newlines for dummy data
const formattedContent = computed(() => {
  if (!props.post.content) return '';
  
  let html = props.post.content;
  
  // Replace **text** with <strong>text</strong>
  html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
  
  // Replace newlines with <br> or paragraphs
  html = html.split('\n\n').map(p => `<p>${p}</p>`).join('');
  html = html.replace(/\n/g, '<br>');
  
  return html;
});
</script>

<style scoped>
/* =========== Header =========== */
.article-header {
  background: #f8fafc;
  padding: 4rem 0 2rem;
  border-bottom: 1px solid #e2e8f0;
}

.article-header-inner {
  max-width: 800px;
  margin: 0 auto;
}

.back-link {
  display: inline-block;
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  margin-bottom: 2rem;
  font-size: 0.95rem;
  transition: color 0.2s;
}

.back-link:hover {
  color: #113559;
}

.article-meta {
  margin-bottom: 1rem;
}

.article-date {
  font-size: 0.9rem;
  color: #64748b;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.article-title {
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 800;
  color: var(--text-main);
  margin: 0;
  line-height: 1.2;
}

/* =========== Content =========== */
.section {
  margin-top: 3rem;
  margin-bottom: 5rem;
}

.article-container {
  max-width: 800px;
  margin: 0 auto;
}

.article-featured-image {
  margin-bottom: 3rem;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
}

.article-featured-image img {
  width: 100%;
  height: auto;
  max-height: 500px;
  object-fit: cover;
  display: block;
}

.article-content {
  font-size: 1.15rem;
  line-height: 1.8;
  color: #334155;
}

.article-content :deep(p) {
  margin-bottom: 1.5rem;
}

.article-content :deep(strong) {
  color: var(--text-main);
  font-weight: 700;
}

.article-content :deep(h2), .article-content :deep(h3) {
  color: var(--text-main);
  margin-top: 2.5rem;
  margin-bottom: 1rem;
  font-weight: 800;
}

@media (max-width: 768px) {
  .article-header {
    padding: 2rem 0 1.5rem;
  }
  .article-featured-image {
    border-radius: 16px;
    margin-bottom: 2rem;
  }
}
</style>
