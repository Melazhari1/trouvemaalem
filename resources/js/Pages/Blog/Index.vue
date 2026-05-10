<template>
  <MainLayout>
    <SeoHead
      title="Blog - trouvemaalem"
      description="Stay updated with the latest news, tips, and stories from the trouvemaalem community."
    />

    <!-- Header Section -->
    <section class="page-header">
      <div class="container header-inner">
        <h1>Notre <span class="text-accent">Blog</span></h1>
        <p class="page-subtitle">Découvrez nos derniers articles, conseils et tendances sur l'artisanat marocain.</p>
      </div>
    </section>

    <!-- Blog Listing Section -->
    <section class="container section">
      <div v-if="posts.length === 0" class="empty-state">
        <div class="empty-icon">📝</div>
        <h3>Aucun article trouvé</h3>
        <p>Revenez plus tard pour lire nos nouveaux articles.</p>
      </div>

      <div class="blog-grid" v-else>
        <article v-for="post in posts" :key="post.id" class="blog-card">
          <Link :href="`/blog/${post.slug}`" class="blog-image-link">
            <div class="blog-card-img">
              <img v-if="post.image" :src="post.image" :alt="post.title" loading="lazy" />
              <div v-else class="placeholder-img"></div>
            </div>
          </Link>
          <div class="blog-card-content">
            <div class="blog-meta">
              <span class="blog-date">{{ new Date(post.created_at).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
            </div>
            <Link :href="`/blog/${post.slug}`" class="blog-title-link">
              <h2>{{ post.title }}</h2>
            </Link>
            <p class="blog-excerpt">{{ post.excerpt }}</p>
            <Link :href="`/blog/${post.slug}`" class="btn-read-more">
              Lire la suite →
            </Link>
          </div>
        </article>
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import MainLayout from '../../Layouts/MainLayout.vue';
import SeoHead from '../../Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  posts: {
    type: Array,
    required: true,
  }
});
</script>

<style scoped>
/* =========== Header =========== */
.page-header {
  background: linear-gradient(135deg, #113559 0%, rgba(17, 53, 89, 0.9) 50%, rgba(17, 53, 89, 0.8) 100%);
  padding: 4rem 0 2rem;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.page-header::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at 50% 50%, rgba(203, 163, 70, 0.15) 0%, transparent 60%);
  pointer-events: none;
}
.header-inner { position: relative; z-index: 1; }
.page-header h1 {
  font-size: clamp(2rem, 5vw, 3.25rem);
  font-weight: 800;
  color: #f8fafc;
  margin: 0 0 0.75rem;
  line-height: 1.15;
  letter-spacing: -0.03em;
}
.text-accent { color: #d78126; }
.page-subtitle {
  font-size: 1.15rem;
  color: #94a3b8;
  margin: 0 0 2rem;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

/* =========== Content =========== */
.section {
  margin-top: 4rem;
  margin-bottom: 5rem;
}

.blog-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.blog-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
  border: 1px solid #f1f5f9;
  transition: transform 0.25s, box-shadow 0.25s;
  display: flex;
  flex-direction: column;
}

.blog-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.12);
}

.blog-image-link {
  display: block;
}

.blog-card-img {
  position: relative;
  height: 220px;
  overflow: hidden;
}

.blog-card-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s;
}

.blog-card:hover .blog-card-img img {
  transform: scale(1.05);
}

.placeholder-img {
  width: 100%;
  height: 100%;
  background: #e2e8f0;
}

.blog-card-content {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.blog-meta {
  margin-bottom: 0.75rem;
}

.blog-date {
  font-size: 0.85rem;
  color: #94a3b8;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.blog-title-link {
  text-decoration: none;
  color: var(--text-main);
  transition: color 0.2s;
}

.blog-title-link:hover {
  color: var(--primary-color);
}

.blog-card-content h2 {
  font-size: 1.3rem;
  font-weight: 700;
  margin: 0 0 1rem;
  line-height: 1.4;
}

.blog-excerpt {
  color: var(--text-muted);
  font-size: 0.95rem;
  line-height: 1.6;
  margin: 0 0 1.5rem;
  flex: 1;
}

.btn-read-more {
  display: inline-block;
  font-weight: 700;
  color: #d78126;
  text-decoration: none;
  font-size: 0.95rem;
  transition: all 0.2s;
  align-self: flex-start;
}

.btn-read-more:hover {
  color: #113559;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 5rem 2rem;
  color: var(--text-muted);
}
.empty-icon { font-size: 3rem; margin-bottom: 1rem; }
.empty-state h3 { font-size: 1.5rem; margin: 0 0 0.5rem; color: var(--text-main); }

@media (max-width: 768px) {
  .blog-grid {
    grid-template-columns: 1fr;
  }
}
</style>
