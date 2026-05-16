<template>
  <MainLayout>
    <SeoHead
      title="Frequently Asked Questions - trouvemaalem"
      description="Find answers to all your questions about trouvemaalem, finding workers, pricing, and more."
      :schema="schema"
    />

    <!-- Header Section -->
    <section class="faq-header">
      <div class="container faq-header-inner">
        <h1>Frequently Asked <span class="text-accent">Questions</span></h1>
        <p class="faq-subtitle">Everything you need to know about our platform and how it works.</p>
      </div>
    </section>

    <!-- FAQ Accordion Section -->
    <section class="container section faq-section">
      <div class="faq-container">
        <FaqAccordion :faqs="faqs" />
      </div>
    </section>
  </MainLayout>
</template>

<script setup>
import MainLayout from '../Layouts/MainLayout.vue';
import SeoHead from '../Components/SeoHead.vue';
import FaqAccordion from '../Components/FaqAccordion.vue';

const props = defineProps({
  faqs: {
    type: Array,
    required: true,
    default: () => []
  },
  schema: Object,
});
</script>

<style scoped>
/* =========== Header =========== */
.faq-header {
  background: linear-gradient(135deg, #113559 0%, rgba(17, 53, 89, 0.9) 50%, rgba(17, 53, 89, 0.8) 100%);
  padding: 4rem 0 2rem;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.faq-header::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at 50% 50%, rgba(203, 163, 70, 0.15) 0%, transparent 60%);
  pointer-events: none;
}
.faq-header-inner { position: relative; z-index: 1; }
.faq-header h1 {
  font-size: clamp(2rem, 5vw, 3.25rem);
  font-weight: 800;
  color: #f8fafc;
  margin: 0 0 0.75rem;
  line-height: 1.15;
  letter-spacing: -0.03em;
}
.text-accent { color: #d78126; }
.faq-subtitle {
  font-size: 1.15rem;
  color: #94a3b8;
  margin: 0 0 2rem;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

/* =========== FAQ Section =========== */
.section {
  margin-top: 3rem;
  margin-bottom: 5rem;
}

.faq-container {
  max-width: 800px;
  margin: 0 auto;
}
</style>
