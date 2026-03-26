<template>
  <div class="timeline-view p-6 border-l-4 border-blue-500 ml-4 space-y-6">
    <div v-for="event in events" :key="event.id" class="relative pl-6">
      <div class="absolute w-3 h-3 bg-blue-500 rounded-full -left-[10px] top-1"></div>
      <span class="text-xs text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded">{{ event.year }}</span>
      <h3 class="text-lg font-bold mt-1">{{ event.title }}</h3>
      <p class="text-gray-600 mt-1">{{ event.description }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const events = ref([
  { id: 1, year: '1370 TCN', title: 'Khởi đầu Thời Các Quan Xét', description: 'Sau cái chết của Giô-suê.' },
  { id: 2, year: '1360 TCN', title: 'Út-ni-ên giải cứu', description: 'Quan xét đầu tiên đánh bại vua Mê-sô-pô-ta-mi.' }
]);
</script>
