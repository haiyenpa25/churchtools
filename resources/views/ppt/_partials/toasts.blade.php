{{-- ══ Toast notifications ══ --}}

{{-- Success --}}
<div x-show="isSuccess"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-4"
     class="fixed bottom-10 inset-x-0 flex justify-center z-50 pointer-events-none"
     style="display:none;">
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-4 pointer-events-auto ring-1 ring-white/20">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex flex-col">
            <span class="font-bold text-sm">Thành công!</span>
            <span class="text-xs text-green-100">File PPTX đã được sinh ra và gửi thẳng xuống thiết bị.</span>
        </div>
        <button @click="isSuccess = false" class="ml-4 text-green-200 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

{{-- Error --}}
<div x-show="isError"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-4"
     class="fixed bottom-10 inset-x-0 flex justify-center z-50 pointer-events-none"
     style="display:none;">
    <div class="bg-red-500 text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-4 pointer-events-auto">
        <span class="font-bold text-sm" x-text="errorMessage">Có lỗi xảy ra!</span>
        <button @click="isError = false" class="ml-4 opacity-70 hover:opacity-100 transition">&times;</button>
    </div>
</div>
