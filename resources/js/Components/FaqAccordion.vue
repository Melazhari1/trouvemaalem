<template>
  <div class="faq-accordion">
    <div
      v-for="(faq, index) in faqs"
      :key="faq.id || index"
      class="faq-item"
      :class="{ active: activeIndex === index }"
    >
      <button class="faq-question" @click="toggle(index)">
        <h3>{{ faq.question }}</h3>
        <span class="faq-icon">
          <svg v-if="activeIndex === index" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        </span>
      </button>
      <div
        class="faq-answer"
        :style="{ maxHeight: activeIndex === index ? '1000px' : '0', opacity: activeIndex === index ? '1' : '0' }"
      >
        <div class="faq-answer-inner">
          <p>{{ faq.answer }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  faqs: {
    type: Array,
    required: true,
    default: () => []
  }
});

const activeIndex = ref(null);

function toggle(index) {
  activeIndex.value = activeIndex.value === index ? null : index;
}
</script>

<style scoped>
.faq-accordion {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  width: 100%;
}

.faq-item {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.faq-item:hover {
  border-color: #cbd5e1;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.faq-item.active {
  border-color: #d78126;
  box-shadow: 0 4px 12px rgba(215, 129, 38, 0.1);
}

.faq-question {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  padding: 1.25rem 1.5rem;
  background: transparent;
  border: none;
  cursor: pointer;
  text-align: left;
  color: var(--text-main, #1e293b);
  transition: all 0.2s;
}

.faq-question h3 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 600;
  padding-right: 1rem;
}

.faq-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #d78126;
  flex-shrink: 0;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: rgba(215, 129, 38, 0.1);
  transition: all 0.3s;
}

.faq-item.active .faq-icon {
  background: #d78126;
  color: white;
  transform: rotate(180deg);
}

.faq-answer {
  max-height: 0;
  opacity: 0;
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.faq-answer-inner {
  padding: 0 1.5rem 1.25rem;
  color: var(--text-muted, #64748b);
  font-size: 0.95rem;
  line-height: 1.6;
}

.faq-answer-inner p {
  margin: 0;
}
</style>
