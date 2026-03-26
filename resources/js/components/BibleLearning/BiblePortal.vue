<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 font-sans p-6 md:p-12 overflow-x-hidden">
    
    <!-- Header -->
    <header class="max-w-6xl mx-auto mb-16 relative z-10">
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
          <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-700 to-purple-600 tracking-tight">
            Thư Viện Học Tập Kinh Thánh
          </h1>
          <p class="mt-3 text-slate-500 font-medium text-lg">Hệ sinh thái khám phá và ghi nhớ Lời Chúa 4-Layer RAG 🚀</p>
        </div>
        <div class="flex items-center gap-4 bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100">
          <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-inner">
            H
          </div>
          <div>
            <p class="text-sm font-bold text-slate-700">Học Giả</p>
            <p class="text-xs text-indigo-500 font-medium">Cấp độ 1 • Sơ cấp</p>
          </div>
        </div>
      </div>
    </header>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-4 border-slate-200 border-t-indigo-600"></div>
    </div>

    <!-- Main Grid Dashboard -->
    <main v-else class="max-w-6xl mx-auto relative z-10">
      
      <!-- Lời khuyên Hàng Ngày -->
      <div class="mb-10 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-8 relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
        <div class="relative z-10">
          <span class="inline-flex items-center gap-1.5 bg-white/20 text-white font-mono text-xs px-3 py-1.5 rounded-lg mb-4 backdrop-blur-sm">
            💡 TIP CHO HÔM NAY
          </span>
          <h2 class="text-2xl md:text-3xl font-bold text-white leading-tight max-w-2xl">
            "Lời Chúa là ngọn đèn cho chân tôi, ánh sáng cho đường lối tôi." <br>
            <span class="text-indigo-200 text-lg font-normal block mt-2">— Thi Thiên 119:105</span>
          </h2>
        </div>
      </div>

      <!-- Module Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- CARD 1: FLASHCARD STUDY -->
        <a href="/bible-learning/study" class="group relative bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 overflow-hidden flex flex-col">
          <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-indigo-100 transition-colors"></div>
          
          <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-6 ring-4 ring-indigo-50 group-hover:scale-110 transition-transform">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
          </div>
          
          <h3 class="text-2xl font-black text-slate-800 mb-2">Flashcard Study</h3>
          <p class="text-slate-500 font-medium mb-8 leading-relaxed">Ôn tập Lời Chúa qua các thẻ ghi nhớ 3D, sử dụng thuật toán Spaced Repetition (SM-2).</p>
          
          <div class="mt-auto flex justify-between items-center pt-4 border-t border-slate-50">
            <div>
              <p class="text-3xl font-black text-indigo-600">{{ stats.due_flashcards }}</p>
              <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Thẻ Cần Ôn</p>
            </div>
            <div class="text-indigo-600 bg-indigo-50 w-10 h-10 rounded-full flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </div>
          </div>
        </a>

        <!-- CARD 2: EVENT TIMELINE (TÍNH NĂNG MỚI) -->
        <a href="/bible-learning/timeline" class="group relative bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 overflow-hidden flex flex-col">
          <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-amber-100 transition-colors"></div>
          
          <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mb-6 ring-4 ring-amber-50 group-hover:scale-110 transition-transform">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          
          <h3 class="text-2xl font-black text-slate-800 mb-2 border-b-2 border-transparent w-max group-hover:border-amber-400 transition-colors">Event Timeline</h3>
          <p class="text-slate-500 font-medium mb-8 leading-relaxed mt-2"><span class="bg-amber-100 text-amber-700 text-[10px] px-2 py-0.5 rounded uppercase font-bold mr-1 tracking-wider">New</span> Dòng thời gian cuộn dọc 3D theo dấu các sự kiện Lịch sử Kinh Thánh.</p>
          
          <div class="mt-auto flex justify-between items-center pt-4 border-t border-slate-50">
            <div>
              <p class="text-3xl font-black text-amber-600">0</p>
              <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Sự Kiện Lịch Sử</p>
            </div>
            <div class="text-amber-600 bg-amber-50 w-10 h-10 rounded-full flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </div>
          </div>
        </a>

        <!-- CARD 4: ĐẤU TRƯỜNG QUIZ (NEW) -->
        <a href="/bible-learning/quiz" class="group relative bg-[#1c192b] rounded-3xl p-8 shadow-lg border border-purple-900/50 hover:shadow-2xl hover:shadow-purple-900/50 hover:-translate-y-2 transition-all duration-300 overflow-hidden flex flex-col md:col-span-3">
          <div class="absolute inset-0 bg-gradient-to-r from-purple-900/40 via-transparent to-pink-900/20 opacity-50"></div>
          
          <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-purple-500/30 group-hover:rotate-12 transition-transform">
              <span class="text-3xl">🎮</span>
            </div>
            
            <div class="flex-1 text-center md:text-left">
              <h3 class="text-2xl font-black text-white mb-2">Đấu Trường Trắc Nghiệm</h3>
              <p class="text-purple-200/70 font-medium">Thử thách tốc độ và trí tuệ với bộ câu hỏi Multiple Choice (Trắc Nghiệm 4 Đáp Án) trích xuất hoàn toàn tự động bằng AI RAG. Đếm ngược 15 giây kịch tính!</p>
            </div>
            
            <div class="text-white bg-white/10 w-12 h-12 rounded-full flex items-center justify-center group-hover:bg-purple-500 transition-colors backdrop-blur-md">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </div>
          </div>
        </a>

        <!-- CARD 5: KNOWLEDGE GRAPH (NEW) -->
        <a href="/bible-learning/graph" class="group relative bg-[#1c2e4a] rounded-3xl p-8 shadow-lg border border-blue-900/50 hover:shadow-2xl hover:shadow-blue-900/50 hover:-translate-y-2 transition-all duration-300 overflow-hidden flex flex-col md:col-span-3">
          <div class="absolute inset-0 bg-gradient-to-r from-blue-900/40 via-transparent to-teal-900/20 opacity-50"></div>
          
          <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-teal-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/30 group-hover:rotate-12 transition-transform">
              <span class="text-3xl">🕸️</span>
            </div>
            
            <div class="flex-1 text-center md:text-left">
              <h3 class="text-2xl font-black text-white mb-2">Lưới Tư Duy Thần Học 3D</h3>
              <p class="text-blue-200/70 font-medium">Khám phá các Mối Quan Hệ (Mạng Nhện) vô hình giữa Nhân Vật, Sự Kiện và Địa Danh trong Kinh Thánh bằng công nghệ đồ thị Vis-Network Physics 3D.</p>
            </div>
            
            <div class="text-white bg-white/10 w-12 h-12 rounded-full flex items-center justify-center group-hover:bg-blue-500 transition-colors backdrop-blur-md">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </div>
          </div>
        </a>

        <!-- CARD 3: APPROVAL CENTER -->
        <a href="/bible-learning/approval" class="group relative bg-[#0B0F19] rounded-3xl p-8 shadow-lg border border-slate-800 hover:shadow-2xl hover:shadow-cyan-900/50 hover:-translate-y-2 transition-all duration-300 overflow-hidden flex flex-col">
          <div class="absolute top-0 right-0 w-40 h-40 bg-cyan-600/20 rounded-full blur-3xl -mr-10 -mt-10 group-hover:bg-cyan-500/30 transition-colors"></div>
          
          <div class="w-14 h-14 rounded-2xl bg-cyan-900/50 text-cyan-400 border border-cyan-800 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
          </div>
          
          <h3 class="text-2xl font-black text-white mb-2">Approval Center</h3>
          <p class="text-slate-400 font-medium mb-8 leading-relaxed">Ban giám khảo kiểm duyệt. Nơi duy nhất cho phép dữ liệu từ AI RAG chui vào hệ thống lõi.</p>
          
          <div class="mt-auto flex justify-between items-center pt-4 border-t border-slate-800/80">
            <div>
              <p class="text-3xl font-black text-cyan-400">{{ stats.pending_approvals }}</p>
              <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Chờ Kiểm Duyệt</p>
            </div>
            <div class="text-cyan-400 bg-cyan-900/30 border border-cyan-800/50 w-10 h-10 rounded-full flex items-center justify-center group-hover:bg-cyan-500 group-hover:text-white transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </div>
          </div>
        </a>

      </div>

    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref({
  pending_approvals: 0,
  due_flashcards: 0,
  total_flashcards: 0,
  total_events: 0
});

const loading = ref(true);

onMounted(async () => {
  try {
    const response = await axios.get('/api/portal/stats');
    stats.value = response.data;
  } catch (error) {
    console.error("Lỗi Portal Stats", error);
  } finally {
    loading.value = false;
  }
});
</script>
