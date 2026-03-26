<template>
  <div class="approval-center max-w-5xl mx-auto p-4 md:p-8 min-h-screen bg-gray-50 mt-4 rounded-xl shadow-inner">
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">AI Approval Center</h1>
        <p class="text-gray-500 mt-2 text-sm">Kiểm duyệt các trích xuất từ Gemini AI trước khi đưa vào dữ liệu chính thức.</p>
      </div>
      <div>
        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold shadow-sm">
          {{ pendingItems.length }} Pending
        </span>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="pendingItems.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center transition-all duration-500 hover:shadow-md">
      <div class="text-gray-300 mb-4 text-5xl">✨</div>
      <h3 class="text-lg font-medium text-gray-900 mb-1">Tất cả đã được duyệt!</h3>
      <p class="text-gray-500">AI hiện chưa có thêm dữ liệu trích xuất mới nào đang chờ bạn kiểm duyệt.</p>
    </div>

    <!-- List -->
    <div v-else class="space-y-4">
      <transition-group name="list" tag="div" class="space-y-4">
        <div v-for="item in pendingItems" :key="item.id" 
             class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-100 transition-all duration-300 relative overflow-hidden group">
          
          <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-indigo-400 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
             
          <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-600 bg-purple-50 px-2 py-0.5 rounded border border-purple-100">
                  {{ item.type || 'Entity' }}
                </span>
                <span class="text-xs text-gray-400 font-mono">#{{ item.id }}</span>
              </div>
              <h3 class="text-xl font-bold text-gray-800 mb-2">{{ item.title }}</h3>
              <p class="text-gray-600 leading-relaxed">{{ item.description }}</p>
              
              <div v-if="item.raw_data" class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                <p class="text-xs text-gray-400 mb-1 font-semibold uppercase">RAW JSON:</p>
                <pre class="text-xs text-slate-600 overflow-x-auto whitespace-pre-wrap">{{ JSON.stringify(item.raw_data, null, 2) }}</pre>
              </div>
            </div>
            
            <div class="flex flex-row md:flex-col gap-2 shrink-0">
              <button @click="approve(item.id)" :disabled="processing === item.id"
                      class="flex-1 md:flex-none flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-medium px-5 py-2.5 rounded-xl shadow-sm shadow-emerald-200 transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg v-if="processing !== item.id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <div v-else class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                Duyệt
              </button>
              
              <button @click="reject(item.id)" :disabled="processing === item.id"
                      class="flex-1 md:flex-none flex items-center justify-center gap-2 bg-white border border-gray-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-gray-600 font-medium px-5 py-2.5 rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Huỷ
              </button>
            </div>
          </div>
        </div>
      </transition-group>
    </div>

    <!-- Toast Notification -->
    <transition name="toast">
      <div v-if="toast.show" class="fixed bottom-5 right-5 z-50 rounded-xl shadow-lg border-l-4 p-4 min-w-[300px] flex items-center justify-between bg-white"
           :class="toast.type === 'success' ? 'border-emerald-500' : 'border-red-500'">
        <div class="flex items-center gap-3">
          <div :class="toast.type === 'success' ? 'text-emerald-500' : 'text-red-500'">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="toast.type === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div>
            <p class="font-bold text-gray-800">{{ toast.title }}</p>
            <p class="text-sm text-gray-500">{{ toast.message }}</p>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const pendingItems = ref([]);
const loading = ref(true);
const processing = ref(null);
const toast = ref({ show: false, title: '', message: '', type: 'success' });

// Hàm gọi Toast thông báo
const showToast = (title, message, type = 'success') => {
  toast.value = { show: true, title, message, type };
  setTimeout(() => toast.value.show = false, 3500);
};

// Gọi API lấy dữ liệu thực từ Backend (Repository 4-layer)
const fetchPending = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/bible-learning/approval/pending');
    pendingItems.value = response.data;
  } catch (error) {
    showToast('Lỗi Tải Dữ Liệu', error.response?.data?.message || 'Không thể kết nối đến Controller.', 'error');
  } finally {
    loading.value = false;
  }
};

const approve = async (id) => {
  try {
    processing.value = id;
    await axios.post(`/bible-learning/approval/${id}/approve`);
    
    // Animation loại bỏ Item khỏi Array
    pendingItems.value = pendingItems.value.filter(item => item.id !== id);
    showToast('Đã Duyệt', 'Trích xuất đã được lưu chính thức vào Hệ thống Kinh Thánh.');
  } catch (error) {
    showToast('Lỗi Dữ Liệu', error.response?.data?.message || 'Không vượt qua được lớp validate.', 'error');
  } finally {
    processing.value = null;
  }
};

const reject = async (id) => {
  try {
    processing.value = id;
    await axios.post(`/bible-learning/approval/${id}/reject`);
    
    pendingItems.value = pendingItems.value.filter(item => item.id !== id);
    showToast('Đã Xoá', 'Bản nháp AI đã bị xoá vĩnh viễn.', 'success');
  } catch (error) {
    showToast('Lỗi Xoá', error.response?.data?.message || 'Xóa thất bại.', 'error');
  } finally {
    processing.value = null;
  }
};

onMounted(() => {
  // Lấy dữ liệu ngay khi Vue component render xong
  fetchPending();
});
</script>

<style scoped>
/* Micro-animations chuẩn Premium cho Vue */
.list-enter-active, .list-leave-active { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.list-enter-from { opacity: 0; transform: translateY(20px) scale(0.95); }
.list-leave-to { opacity: 0; transform: translateX(80px) scale(0.9); }

.toast-enter-active, .toast-leave-active { transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
.toast-enter-from { opacity: 0; transform: translateY(30px) scale(0.9); }
.toast-leave-to { opacity: 0; transform: translateY(30px) scale(0.9); }
</style>
