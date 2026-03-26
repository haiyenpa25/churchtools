<template>
  <div class="min-h-screen bg-neutral-900 text-white font-sans flex flex-col pt-10 px-4 md:px-0">
    <div class="max-w-3xl w-full mx-auto flex-1 flex flex-col">
      
      <!-- Top Bar: Health & Score -->
      <div v-if="!gameOver && quizzes.length > 0" class="flex justify-between items-center bg-neutral-800 p-4 rounded-2xl border border-neutral-700 shadow-xl mb-8">
        <div class="flex gap-2">
          <span v-for="life in 3" :key="'hp-'+life" class="text-2xl transition-all duration-300"
                :class="life <= health ? 'text-red-500 scale-110 drop-shadow-[0_0_10px_rgba(239,68,68,0.8)]' : 'text-neutral-600 grayscale'">
            ❤️
          </span>
        </div>
        
        <div class="flex items-center gap-4">
          <div class="bg-indigo-900/50 border border-indigo-500/50 px-4 py-1.5 rounded-full text-indigo-300 font-bold font-mono">
            SCORE: {{ score }}
          </div>
          <div class="w-12 h-12 rounded-full flex items-center justify-center font-black text-xl shadow-inner border-4"
               :class="timer <= 5 ? 'bg-red-900/50 text-red-500 border-red-500 animate-pulse' : 'bg-neutral-900 text-green-400 border-green-500'">
            {{ timer }}
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex-1 flex justify-center items-center">
        <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-indigo-500"></div>
      </div>

      <!-- Game Over State -->
      <div v-else-if="gameOver" class="text-center bg-neutral-800 p-10 rounded-3xl border border-neutral-700 shadow-2xl mt-12 animate-fade-in-up">
        <div class="text-6xl mb-4">{{ health === 0 ? '💀' : '🏆' }}</div>
        <h2 class="text-4xl font-black mb-2" :class="health === 0 ? 'text-red-500' : 'text-yellow-400'">
          {{ health === 0 ? 'TRÒ CHƠI KẾT THÚC' : 'CHIẾN THẮNG TUYỆT ĐỐI' }}
        </h2>
        <p class="text-neutral-400 text-xl mb-8">Bạn đã trả lời đúng {{ score }} trên tổng số {{ quizzes.length }} câu hỏi.</p>
        
        <div class="flex gap-4 justify-center">
          <button @click="startGame" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl font-bold shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
            Chơi Lại
          </button>
          <a href="/bible-learning" class="px-6 py-3 bg-neutral-700 hover:bg-neutral-600 rounded-xl font-bold transition-all active:scale-95">
            Về Đại Sảnh
          </a>
        </div>
      </div>

      <!-- Quiz Card Area -->
      <div v-else-if="currentQuiz" class="flex-1 flex flex-col relative z-10 w-full animate-fade-in">
        
        <!-- Question Box -->
        <div class="bg-gradient-to-br from-indigo-900/40 to-neutral-800 border border-indigo-500/30 p-8 rounded-3xl shadow-2xl mb-8 relative overflow-hidden">
          <div class="absolute top-0 left-0 w-full h-1 bg-neutral-700">
            <div class="h-full bg-indigo-500 transition-all duration-1000 ease-linear" :style="`width: ${(timer / 15) * 100}%`"></div>
          </div>
          <span class="text-indigo-400 font-mono text-sm tracking-widest font-bold mb-4 block">CÂU HỎI {{ currentIndex + 1 }}/{{ quizzes.length }}</span>
          <h2 class="text-2xl md:text-3xl font-bold leading-relaxed text-white">{{ currentQuiz.question }}</h2>
          <div v-if="currentQuiz.reference" class="mt-4 inline-block bg-neutral-900/80 px-3 py-1 rounded text-xs text-neutral-400 font-mono">
            📖 {{ currentQuiz.reference }}
          </div>
        </div>

        <!-- Options Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <button v-for="(text, key) in currentQuiz.options" :key="key"
                  @click="selectAnswer(key)"
                  :disabled="isAnswered"
                  class="relative p-6 rounded-2xl border-2 text-left transition-all duration-200"
                  :class="getOptionClass(key)">
            <div class="flex items-start gap-4">
              <span class="w-8 h-8 shrink-0 flex items-center justify-center rounded-lg font-black bg-neutral-800 text-neutral-400 border border-neutral-700"
                    :class="getBadgeClass(key)">
                {{ key }}
              </span>
              <span class="text-lg font-medium">{{ text }}</span>
            </div>
          </button>
        </div>

        <!-- Explanation Drawer -->
        <div v-if="isAnswered" class="mt-8 bg-neutral-800 border border-neutral-700 p-6 rounded-2xl animate-fade-in">
          <h3 class="font-bold text-lg mb-2" :class="isCorrect ? 'text-green-400' : 'text-red-400'">
            {{ isCorrect ? 'Chính Xác!' : 'Sai Rồi!' }}
          </h3>
          <p class="text-neutral-300 leading-relaxed">{{ currentQuiz.explanation || 'Không có giải thích chi tiết.' }}</p>
          <div class="mt-6 flex justify-end">
            <button @click="nextQuestion" class="px-8 py-3 bg-white text-black font-bold rounded-xl hover:bg-neutral-200 transition-all active:scale-95 shadow-[0_0_15px_rgba(255,255,255,0.3)]">
              Câu Tiếp Theo →
            </button>
          </div>
        </div>

      </div>

      <!-- Empty State -->
      <div v-else class="text-center text-neutral-500 py-20">
        Hiện tại Đấu trường trống rỗng. Hãy nhờ Ban Kiểm Duyệt mở màn!
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import confetti from 'canvas-confetti';

const quizzes = ref([]);
const loading = ref(true);
const currentIndex = ref(0);
const score = ref(0);
const health = ref(3);
const gameOver = ref(false);

const timer = ref(15);
let timerInterval = null;

const selectedOption = ref(null);
const isAnswered = ref(false);
const isCorrect = ref(false);

const currentQuiz = computed(() => quizzes.value[currentIndex.value] || null);

const fetchQuizzes = async () => {
  try {
    const res = await axios.get('/api/quizzes/random');
    quizzes.value = res.data;
    if (quizzes.value.length > 0) {
      startTimer();
    }
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

const startGame = () => {
  currentIndex.value = 0;
  score.value = 0;
  health.value = 3;
  gameOver.value = false;
  fetchQuizzes(); // Refresh data for a new game
};

const startTimer = () => {
  clearInterval(timerInterval);
  timer.value = 15;
  timerInterval = setInterval(() => {
    if (timer.value > 0) {
      timer.value--;
    } else {
      // Time's up! Treat as wrong answer
      selectAnswer(null);
    }
  }, 1000);
};

const selectAnswer = (key) => {
  if (isAnswered.value) return;
  
  clearInterval(timerInterval);
  isAnswered.value = true;
  selectedOption.value = key;
  
  if (key === currentQuiz.value.correct_option) {
    isCorrect.value = true;
    score.value += 100 + (timer.value * 10); // Bonus boint for speed
    triggerConfetti();
  } else {
    isCorrect.value = false;
    health.value--;
    if (health.value <= 0) {
      setTimeout(() => {
        gameOver.value = true;
      }, 3000); // Allow them to read explanation before game over screen
    }
  }
};

const getOptionClass = (key) => {
  if (!isAnswered.value) {
    return 'border-neutral-700 bg-neutral-800 hover:border-indigo-500 hover:bg-neutral-800/80 cursor-pointer';
  }
  
  if (key === currentQuiz.value.correct_option) {
    return 'border-green-500 bg-green-900/20 text-green-100 shadow-[0_0_15px_rgba(34,197,94,0.2)]';
  }
  
  if (key === selectedOption.value) {
    return 'border-red-500 bg-red-900/20 text-red-100 opacity-50';
  }
  
  return 'border-neutral-800 bg-neutral-900 text-neutral-600 opacity-40 cursor-not-allowed';
};

const getBadgeClass = (key) => {
  if (!isAnswered.value) return '';
  if (key === currentQuiz.value.correct_option) return 'bg-green-500 text-white border-green-400';
  if (key === selectedOption.value) return 'bg-red-500 text-white border-red-400';
  return '';
};

const nextQuestion = () => {
  if (health.value <= 0) {
    gameOver.value = true;
    return;
  }
  
  if (currentIndex.value < quizzes.value.length - 1) {
    currentIndex.value++;
    isAnswered.value = false;
    selectedOption.value = null;
    startTimer();
  } else {
    gameOver.value = true;
    triggerBigConfetti();
  }
};

const triggerConfetti = () => {
  confetti({
    particleCount: 50,
    spread: 60,
    origin: { y: 0.8 },
    colors: ['#818cf8', '#34d399', '#fbbf24']
  });
};

const triggerBigConfetti = () => {
  const duration = 3000;
  const end = Date.now() + duration;

  (function frame() {
    confetti({
      particleCount: 5,
      angle: 60,
      spread: 55,
      origin: { x: 0 },
      colors: ['#818cf8', '#c084fc']
    });
    confetti({
      particleCount: 5,
      angle: 120,
      spread: 55,
      origin: { x: 1 },
      colors: ['#34d399', '#fbbf24']
    });

    if (Date.now() < end) requestAnimationFrame(frame);
  }());
};

onMounted(() => {
  startGame();
});

onUnmounted(() => {
  clearInterval(timerInterval);
});
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}
.animate-fade-in-up {
  animation: fadeInUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
