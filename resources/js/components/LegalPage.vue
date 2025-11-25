<template>
  <section class="px-4 md:px-8 py-8 flex-1">
    <div class="max-w-screen-lg mx-auto bg-white rounded-lg border border-[#ecebea] p-6 shadow-sm">
      <div v-if="loading" class="animate-pulse space-y-3">
        <div class="h-7 w-64 bg-gray-200 rounded"></div>
        <div class="h-4 w-full bg-gray-100 rounded"></div>
        <div class="h-4 w-11/12 bg-gray-100 rounded"></div>
        <div class="h-4 w-10/12 bg-gray-100 rounded"></div>
      </div>
      <article v-else class="prose max-w-none">
        <h1 class="!mt-0">{{ data.title }}</h1>
        <div v-html="data.body"></div>
      </article>
      <div v-if="error" class="mt-4 text-sm text-red-600">{{ error }}</div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
  endpoint: { type: String, required: true },
  fallback: { type: Object, default: () => ({ title: '', body: '' }) },
});

const loading = ref(true);
const error = ref('');
const data = ref({ title: props.fallback?.title || '', body: props.fallback?.body || '' });

onMounted(async () => {
  try {
    const res = await fetch(props.endpoint, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`Failed to load (${res.status})`);
    const json = await res.json();
    data.value = { title: json.title || data.value.title, body: json.body || data.value.body };
  } catch (e) {
    error.value = e?.message || 'Failed to load content';
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.prose :deep(h1){
  font-size: 1.75rem;
}
</style>
