import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useTranslations() {
  const page = usePage();

  const locale = computed(() => page.props.locale || 'en');
  const translations = computed(() => page.props.translations || {});
  const isRtl = computed(() => locale.value === 'ar');

  /**
   * Translate a key, with optional replacements.
   * Usage: t('artisans_count', { count: 5 }) => "5 Workers"
   */
  function t(key, replacements = {}) {
    let text = translations.value[key] || key;
    for (const [k, v] of Object.entries(replacements)) {
      text = text.replace(`:${k}`, v);
    }
    return text;
  }

  return { t, locale, isRtl };
}
