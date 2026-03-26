<template>
  <div class="flashcard-study relative min-h-screen bg-[#0B0F19] text-gray-100 font-sans overflow-hidden py-10 px-4 md:p-12 flex flex-col items-center justify-center selection:bg-indigo-500/30">
    
    <!-- Background Glow Effects -->
    <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] rounded-full bg-indigo-600/20 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] rounded-full bg-purple-600/20 blur-[120px] pointer-events-none"></div>

    <div class="z-10 w-full max-w-4xl mb-12">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400 tracking-tight">Thư Viện Flashcard</h1>
          <p class="text-slate-400 mt-2 font-medium flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            SuperMemo-2 Active Session
          </p>
        </div>
        <div class="text-right">
          <div class="inline-flex flex-col items-end">
            <span class="text-3xl font-bold text-white">{{ dueCards.length }}</span>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Thẻ chờ duyệt</span>
          </div>
        </div>
      </div>
      
      <!-- Premium Progress Bar -->
      <div class="w-full bg-slate-800/50 backdrop-blur-sm rounded-full h-1.5 overflow-hidden border border-slate-700/50">
        <div class="bg-gradient-to-r from-cyan-400 via-indigo-500 to-purple-500 h-1.5 rounded-full transition-all duration-700 ease-out shadow-[0_0_10px_rgba(99,102,241,0.5)]" 
             :style="`width: ${progressPercentage}%`"></div>
      </div>
    </div>

    <!-- Tải dữ liệu -->
    <div v-if="loading" class="z-10 py-32 flex flex-col items-center">
      <div class="relative w-16 h-16">
        <div class="absolute inset-0 rounded-full border-t-2 border-indigo-500 animate-spin"></div>
        <div class="absolute inset-2 rounded-full border-r-2 border-cyan-400 animate-spin flex-reverse"></div>
      </div>
      <p class="text-slate-400 mt-6 animate-pulse font-medium tracking-wide">Syncing RAG Engine...</p>
    </div>

    <!-- Hoàn thành - Ăn mừng -->
    <div v-else-if="dueCards.length === 0" class="z-10 w-full max-w-2xl py-24 px-8 bg-slate-800/40 backdrop-blur-xl rounded-[2rem] border border-slate-700/50 text-center shadow-2xl transform transition-all duration-700 scale-100">
      <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-indigo-500/20 mb-8 ring-4 ring-indigo-500/30">
        <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
      </div>
      <h2 class="text-3xl font-black text-white mb-4">You're All Caught Up!</h2>
      <p class="text-slate-400 mb-10 text-lg leading-relaxed">Não bộ của bạn đã được tái cấu trúc thành công. <br>Thuật toán đã lên lịch cho các chu kỳ tiếp theo.</p>
      <a href="/bible-learning/approval" class="group relative inline-flex items-center justify-center px-8 py-4 font-bold text-white bg-indigo-600 rounded-2xl overflow-hidden transition-all hover:scale-105 hover:shadow-[0_0_40px_rgba(79,70,229,0.4)]">
        <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-full group-hover:h-56 opacity-10"></span>
        <span class="relative flex items-center gap-2">
          Hệ thống Approval Center
          <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
        </span>
      </a>
    </div>

    <!-- Card 3D Viewer PRO -->
    <div v-else class="z-10 w-full max-w-3xl perspective-1200">
      <!-- Thẻ Lật -->
      <div class="relative w-full aspect-[4/3] max-h-[450px] cursor-pointer group" @click="flipCard">
        <div class="w-full h-full transition-all duration-[600ms] transform-style-3d ease-[cubic-bezier(0.23,1,0.32,1)]" 
             :class="{ 'rotate-y-180': isFlipped }">
          
          <!-- [MẶT TRƯỚC]: Câu hỏi -->
          <div class="absolute inset-0 w-full h-full bg-slate-800/80 backdrop-blur-2xl rounded-[2rem] border border-slate-700 shadow-2xl backface-hidden flex flex-col p-10 group-hover:border-indigo-500/50 transition-colors">
            
            <div class="flex justify-between items-start mb-auto">
              <span class="inline-flex items-center gap-1.5 bg-slate-900/50 text-slate-400 font-mono text-xs px-3 py-1.5 rounded-lg border border-slate-700/50">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                QUESTION
              </span>
              <span class="text-slate-500 font-mono text-xs">ID: {{ currentCard.id }}</span>
              <!-- Nút phát lại âm thanh thủ công -->
            <button @click.stop="playAudio(currentCard.answer + '. ' + currentCard.reference)" class="absolute bottom-6 right-6 p-3 bg-indigo-600/20 text-indigo-400 hover:bg-indigo-600 hover:text-white rounded-full transition-colors z-20 border border-indigo-500/30">
               <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072V15H12l-4 4v-4H6V8h2l4-4v14z"></path></svg>
            </button>
          </div>
            
            <div class="flex-1 flex items-center justify-center">
              <h3 class="text-3xl md:text-4xl font-extrabold text-white leading-tight text-center drop-shadow-md">
                {{ currentCard.question }}
              </h3>
            </div>
            
            <div class="mt-auto flex justify-between items-end">
              <div class="text-slate-500 flex items-center gap-2 text-sm font-medium">
                <kbd class="px-2 py-1 bg-slate-900 border border-slate-700 rounded-md text-slate-400 font-mono text-xs shadow-inner shadow-black/50">SPACE</kbd>
                Để lật thẻ
              </div>
              <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                <svg class="w-6 h-6 text-indigo-400 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
              </div>
            </div>
          </div>

          <!-- [MẶT SAU]: Đáp án -->
          <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-indigo-900 via-slate-800 to-purple-900 rounded-[2rem] border border-indigo-500/30 shadow-[0_0_50px_rgba(79,70,229,0.15)] backface-hidden rotate-y-180 flex flex-col p-10">
            
            <div class="flex justify-between items-start mb-auto">
              <span class="inline-flex items-center gap-1.5 bg-indigo-500/20 text-indigo-300 font-mono text-xs px-3 py-1.5 rounded-lg border border-indigo-500/30">
                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                ANSWER
              </span>
              <span v-if="currentCard.reference" class="bg-slate-900/60 text-cyan-400 font-mono text-sm font-bold px-4 py-1.5 rounded-xl border border-cyan-500/20 backdrop-blur-md">
                📖 {{ currentCard.reference }}
              </span>
            </div>
            
            <div class="flex-1 flex flex-col items-center justify-center my-6 overflow-y-auto custom-scrollbar">
              <h3 class="text-2xl md:text-3xl font-bold text-white leading-relaxed text-center">
                {{ currentCard.answer }}
              </h3>
            </div>
            
            <div class="mt-auto p-4 bg-slate-900/50 rounded-xl border border-slate-700/50 text-center">
              <p class="text-sm text-slate-400 font-medium">Bạn đã nhớ chi tiết này ở mức độ nào?</p>
            </div>
          </div>

        </div>
      </div>

      <!-- Action Buttons PRO (Keyboard Bindings) -->
      <transition name="fly-up">
        <div v-if="isFlipped" class="mt-8 grid grid-cols-4 gap-3 md:gap-5">
          <!-- Phím 1: Lại -->
          <button @click="submitReview(0)" :disabled="submitting" class="relative group flex flex-col items-center justify-center py-4 rounded-2xl bg-slate-800 border border-slate-700 hover:bg-slate-700 hover:border-red-500/50 transition-all disabled:opacity-30 overflow-hidden shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-t from-red-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <kbd class="absolute top-3 left-3 px-1.5 py-0.5 bg-slate-900 border border-slate-700 rounded text-slate-500 font-mono text-[10px]">1</kbd>
            <span class="text-red-400 font-black text-xl md:text-2xl mt-2 group-hover:-translate-y-1 transition-transform">Lại</span>
            <span class="text-xs text-slate-500 mt-1 font-medium">&lt; 1 ngày</span>
          </button>
          
          <!-- Phím 2: Khó -->
          <button @click="submitReview(1)" :disabled="submitting" class="relative group flex flex-col items-center justify-center py-4 rounded-2xl bg-slate-800 border border-slate-700 hover:bg-slate-700 hover:border-orange-500/50 transition-all disabled:opacity-30 overflow-hidden shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-t from-orange-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <kbd class="absolute top-3 left-3 px-1.5 py-0.5 bg-slate-900 border border-slate-700 rounded text-slate-500 font-mono text-[10px]">2</kbd>
            <span class="text-orange-400 font-black text-xl md:text-2xl mt-2 group-hover:-translate-y-1 transition-transform">Khó</span>
            <span class="text-xs text-slate-500 mt-1 font-medium">Bình thường</span>
          </button>
          
          <!-- Phím 3: Tốt -->
          <button @click="submitReview(2)" :disabled="submitting" class="relative group flex flex-col items-center justify-center py-4 rounded-2xl bg-slate-800 border border-slate-700 hover:bg-slate-700 hover:border-emerald-500/50 transition-all disabled:opacity-30 overflow-hidden shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-t from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <kbd class="absolute top-3 left-3 px-1.5 py-0.5 bg-slate-900 border border-slate-700 rounded text-slate-500 font-mono text-[10px]">3</kbd>
            <span class="text-emerald-400 font-black text-xl md:text-2xl mt-2 group-hover:-translate-y-1 transition-transform">Tốt</span>
            <span class="text-xs text-slate-500 mt-1 font-medium">Chuẩn SM-2</span>
          </button>
          
          <!-- Phím 4: Dễ -->
          <button @click="submitReview(3)" :disabled="submitting" class="relative group flex flex-col items-center justify-center py-4 rounded-2xl bg-slate-800 border border-slate-700 hover:bg-slate-700 hover:border-cyan-500/50 transition-all disabled:opacity-30 overflow-hidden shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-t from-cyan-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <kbd class="absolute top-3 left-3 px-1.5 py-0.5 bg-slate-900 border border-slate-700 rounded text-slate-500 font-mono text-[10px]">4</kbd>
            <span class="text-cyan-400 font-black text-xl md:text-2xl mt-2 group-hover:-translate-y-1 transition-transform">Dễ</span>
            <span class="text-xs text-slate-500 mt-1 font-medium">Nhảy cóc</span>
          </button>
        </div>
      </transition>
    </div>

    <!-- Cảnh báo Keyboard -->
    <div v-show="!loading && dueCards.length > 0" class="fixed bottom-6 left-1/2 -translate-x-1/2 px-6 py-2 bg-slate-900/80 backdrop-blur-md rounded-full border border-slate-700 shadow-2xl z-50 pointer-events-none">
      <p class="text-slate-400 text-xs font-medium">Bật Unikey tiếng Anh và sử dụng <span class="text-white font-bold">Bàn phím (1, 2, 3, 4, Space)</span> để học siêu tốc.</p>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import confetti from 'canvas-confetti';
import { AudioSpeechService } from '../../services/AudioSpeechService';

const dueCards = ref([]);
const loading = ref(true);
const submitting = ref(false);
const currentIndex = ref(0);
const isFlipped = ref(false);
const autoRead = ref(true);
const totalCardsToday = ref(0);

// Tính toán
const currentCard = computed(() => dueCards.value.length > 0 ? dueCards.value[0] : null);
const progressPercentage = computed(() => {
  if (totalCardsToday.value === 0) return 100;
  return ((totalCardsToday.value - dueCards.value.length) / totalCardsToday.value) * 100;
});

// Sound Effect Base64 (Optional - Soft click)
const playClickSound = () => {
    // Để cho Pro thì có thể thêm âm thanh lật bài
};

const triggerConfetti = () => {
  const duration = 3 * 1000;
  const animationEnd = Date.now() + duration;
  const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 100 };

  const randomInRange = (min, max) => Math.random() * (max - min) + min;
  
  const interval = setInterval(function() {
    const timeLeft = animationEnd - Date.now();
    if (timeLeft <= 0) return clearInterval(interval);
    const particleCount = 50 * (timeLeft / duration);
    
    confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
    confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
  }, 250);
};

const fetchDueCards = async () => {
  try {
    loading.value = true;
    // Tắt âm thanh nếu chuyển thẻ quá nhanh
    AudioSpeechService.stop();

    const response = await axios.get('/api/flashcards/due');
    dueCards.value = response.data;
    totalCardsToday.value = dueCards.value.length;
  } catch (error) {
    console.error("Lỗi lấy thẻ:", error);
  } finally {
    loading.value = false;
  }
};

const toggleAutoRead = () => {
  autoRead.value = !autoRead.value;
  if (!autoRead.value) AudioSpeechService.stop();
};

const playAudio = (text) => {
  AudioSpeechService.speak(text);
};

// Lật thẻ
const flipCard = () => {
  if (loading.value || dueCards.value.length === 0) return;
  isFlipped.value = !isFlipped.value;
  
  if (isFlipped.value && autoRead.value && currentCard.value) {
     playAudio(currentCard.value.answer + ". " + (currentCard.value.reference || ''));
  }
};

const submitReview = async (rating) => {
  if (!currentCard.value || submitting.value || !isFlipped.value) return;
  
  try {
    submitting.value = true;
    await axios.post(`/api/flashcards/${currentCard.value.id}/review`, { rating });
    
    // Đóng thẻ lại
    isFlipped.value = false;
    
    // Đợi thẻ đóng hoàn tất mới chuyển thẻ tiếp theo (600ms matching transition)
    setTimeout(() => {
      dueCards.value.shift();
      submitting.value = false;
      
      // Check hoàn thành
      if (dueCards.value.length === 0) {
        triggerConfetti();
      }
    }, 600); 
    
  } catch (error) {
    console.error("Lỗi gửi bài:", error);
    submitting.value = false;
  }
};

// ==========================================
// KEYBOARD BINDINGS (SỨC MẠNH CỦA PRO YÊU CẦU)
// ==========================================
const handleKeydown = (e) => {
  // Bỏ qua nếu đang gõ input ở đâu đó
  if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;
  
  if (e.code === 'Space') {
    e.preventDefault(); // Tránh cuộn trang
    flipCard();
  }
  
  if (isFlipped.value && !submitting.value) {
    if (e.key === '1') submitReview(0);
    if (e.key === '2') submitReview(1);
    if (e.key === '3') submitReview(2);
    if (e.key === '4') submitReview(3);
  }
};

onMounted(() => {
  fetchDueCards();
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
});
</script>

<style scoped>
/* Core 3D effects PRO */
.perspective-1200 {
  perspective: 1200px;
}
.transform-style-3d {
  transform-style: preserve-3d;
}
.backface-hidden {
  backface-visibility: hidden;
  -webkit-backface-visibility: hidden;
}
.rotate-y-180 {
  transform: rotateY(180deg);
}

/* Animations */
.fly-up-enter-active {
  transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.fly-up-leave-active {
  transition: all 0.3s cubic-bezier(0.55, 0.085, 0.68, 0.53);
}
.fly-up-enter-from {
  opacity: 0;
  transform: translateY(40px) scale(0.9);
}
.fly-up-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}

/* Custom Scrollbar cho phần nội dung thẻ dài */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(15, 23, 42, 0.3);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(99, 102, 241, 0.5);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(99, 102, 241, 0.8);
}
</style>
