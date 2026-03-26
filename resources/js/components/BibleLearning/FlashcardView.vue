<template>
  <div class="flashcard-view p-6 flex flex-col items-center">
    <h2 class="text-xl font-bold mb-4">Spaced Repetition Flashcards</h2>
    
    <div 
      class="card w-96 h-64 border rounded-xl shadow-lg flex items-center justify-center cursor-pointer transition-transform duration-500"
      :class="{ 'rotate-y-180': isFlipped }"
      @click="isFlipped = !isFlipped"
    >
      <div v-if="!isFlipped" class="front text-center p-4">
        <h3 class="text-lg font-semibold">Ai là người lập Đền tạm tại Si-lô?</h3>
      </div>
      <div v-else class="back text-center p-4 bg-gray-50 rounded-xl w-full h-full flex flex-col justify-center">
        <p class="text-md text-gray-800">Giô-suê</p>
        <p class="text-sm text-gray-500 mt-2">Dẫn chứng: Giô-suê 18:1</p>
      </div>
    </div>

    <div v-if="isFlipped" class="mt-6 flex gap-4">
      <button class="bg-red-100 text-red-700 px-4 py-2 rounded">Quên (Lặp lại sớm)</button>
      <button class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded">Khó nhớ</button>
      <button class="bg-green-100 text-green-700 px-4 py-2 rounded">Nhớ rõ (Lặp lại xa)</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const isFlipped = ref(false);
</script>

<style scoped>
/* Add 3D flip effects here */
</style>
