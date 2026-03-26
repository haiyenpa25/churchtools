<template>
  <div class="timeline-container min-h-screen bg-[#0B0F19] text-gray-100 font-sans py-16 px-4 md:px-0 overflow-x-hidden relative">
    
    <!-- Background Decorators -->
    <div class="fixed inset-0 z-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(#4f46e5 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="fixed top-[-20%] left-[-10%] w-[50%] h-[50%] rounded-full bg-indigo-900/40 blur-[150px] pointer-events-none z-0"></div>

    <div class="max-w-5xl mx-auto relative z-10">
      <header class="text-center mb-24 relative">
        <h1 class="text-4xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-400 to-amber-600 tracking-tight mb-4 drop-shadow-lg">
          Dòng Thời Gian Lịch Sử
        </h1>
        <p class="text-slate-400 text-lg md:text-xl font-medium max-w-2xl mx-auto">
          Truy vết dòng chảy của Cựu Ước và Tân Ước. Từ khi thế gian được tạo dựng cho đến ngày Đấng Christ trở lại.
        </p>
        
        <a href="/bible-learning" class="inline-flex mt-8 items-center gap-2 text-indigo-400 hover:text-indigo-300 bg-indigo-900/30 px-6 py-2 rounded-full border border-indigo-500/30 transition-all hover:bg-indigo-800/50">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Quay lại Đại Sảnh
        </a>
      </header>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center py-20">
        <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-amber-500"></div>
      </div>

      <!-- Timeline Component -->
      <div v-else class="relative wrap overflow-hidden p-2 md:p-10 h-full">
        <!-- Central Line -->
        <div class="absolute border-opacity-20 border-slate-500 h-full border" style="left: 50%"></div>
        
        <!-- Empty State -->
        <div v-if="events.length === 0" class="text-center py-20 bg-slate-800/50 backdrop-blur-md rounded-3xl border border-slate-700">
          <div class="text-6xl mb-6">🏜️</div>
          <h2 class="text-2xl font-bold text-white mb-2">Chưa có Sự kiện Lịch sử nào</h2>
          <p class="text-slate-400">Có vẻ trí tuệ nhân tạo (AI RAG) chưa duyệt sự kiện lịch sử nào cả.</p>
        </div>

        <!-- Event Nodes -->
        <div v-for="(event, index) in events" :key="event.id" 
             class="mb-12 flex justify-between items-center w-full group timeline-item reveal" 
             :class="{ 'flex-row-reverse left-timeline': index % 2 !== 0, 'right-timeline': index % 2 === 0 }">
             
          <div class="order-1 w-5/12 hidden md:block"></div>
          
          <!-- Node Center Dot -->
          <div class="z-20 flex items-center order-1 bg-[#0B0F19] shadow-xl w-12 h-12 rounded-full border-4 relative"
               :class="index % 2 === 0 ? 'border-amber-500' : 'border-indigo-500'">
               <h1 class="mx-auto font-black text-xl text-white">{{ index + 1 }}</h1>
               <div class="absolute inset-0 bg-white/20 rounded-full animate-ping opacity-75"></div>
          </div>
          
          <!-- Card Content -->
          <div class="order-1 bg-slate-800/80 backdrop-blur-xl rounded-3xl border border-slate-700 shadow-2xl w-full md:w-5/12 px-6 py-8 transition-transform duration-500 hover:-translate-y-2 relative overflow-hidden group">
            
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-3xl -mr-16 -mt-16 opacity-20 pointer-events-none transition-colors"
                 :class="index % 2 === 0 ? 'bg-amber-500' : 'bg-indigo-500'"></div>

            <div class="relative z-10 flex flex-col gap-3">
              <span class="inline-block w-max font-mono text-xs font-bold tracking-widest px-3 py-1 bg-slate-900 rounded-lg shadow-inner"
                    :class="index % 2 === 0 ? 'text-amber-400' : 'text-indigo-400'">
                {{ event.era || 'Không xác định' }}
              </span>
              
              <!-- Khối tiêu đề + Nút Đọc -->
              <div class="flex items-center justify-between gap-4">
                <h3 class="font-black text-white text-2xl leading-tight">{{ event.title }}</h3>
                <button @click.stop="playAudio(event.title + '. ' + event.description)" class="shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-slate-700/50 hover:bg-amber-600 text-slate-300 hover:text-white transition-all transform hover:scale-110 active:scale-95 shadow-md">
                   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                </button>
              </div>
              
              <p class="text-slate-400 leading-relaxed max-w-sm" v-html="event.description.replace(/\n/g, '<br>')"></p>
            </div>
            
            <div v-if="event.image_url" class="mt-6 rounded-2xl overflow-hidden shadow-inner border border-slate-700 relative z-10 group/img">
              <img :src="event.image_url" class="w-full object-cover h-48 group-hover/img:scale-110 transition-transform duration-700 ease-out" alt="Event visual"/>
              <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent pointer-events-none"></div>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { AudioSpeechService } from '../../services/AudioSpeechService';

const events = ref([]);
const loading = ref(true);

const fetchEvents = async () => {
  try {
    const res = await axios.get('/api/events');
    events.value = res.data;
    
    // Auto-reveal animation initialization via intersection observer (Mocked for immediate load)
    setTimeout(() => {
      document.querySelectorAll('.timeline-item').forEach((el, index) => {
        setTimeout(() => {
          el.classList.add('visible');
        }, index * 200);
      });
    }, 100);

  } catch (error) {
    console.error("Lỗi lấy Timeline:", error);
  } finally {
    loading.value = false;
  }
};

const playAudio = (text) => {
  AudioSpeechService.speak(text);
};

onMounted(() => {
  fetchEvents();
});

onUnmounted(() => {
  AudioSpeechService.stop();
});
</script>

<style scoped>
.timeline-item {
  opacity: 0;
  transform: translateY(40px);
  transition: all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.timeline-item.visible {
  opacity: 1;
  transform: translateY(0);
}

@media (max-width: 768px) {
  .wrap {
    padding-left: 20px !important;
  }
  .wrap > div.absolute {
    left: 40px !important;
  }
  .timeline-item {
    flex-direction: row-reverse !important;
    justify-content: flex-end !important;
    gap: 20px;
  }
  .timeline-item .order-1.w-5\/12 {
    display: none;
  }
  .timeline-item .order-1.bg-slate-800\/80 {
    width: calc(100% - 70px) !important;
  }
}
</style>
