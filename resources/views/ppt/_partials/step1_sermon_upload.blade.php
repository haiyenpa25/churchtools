{{-- step1_sermon_upload.blade.php — PDF Upload step for Bài Giảng Live --}}
<div x-show="step === 1">
    <div class="glass rounded-2xl p-8 mb-6">
        <h2 class="text-xl font-bold text-yellow-300 mb-5">📤 Bước 1 — Tải Tài Liệu Bài Giảng</h2>

        {{-- PDF drop zone --}}
        <label for="pdfInput"
               class="drop-zone block cursor-pointer rounded-2xl p-12 text-center mb-6 transition"
               :class="dragging ? 'drag-over' : ''"
               @dragover.prevent="dragging = true"
               @dragleave.prevent="dragging = false"
               @drop.prevent="onDrop($event)">
            <input type="file" id="pdfInput" accept=".pdf" class="hidden" @change="onFileChange">
            <div class="text-5xl mb-3">📄</div>
            <p class="font-bold text-yellow-300" x-show="!pdfFile">Kéo thả file PDF bài giảng vào đây</p>
            <p class="font-bold text-green-300" x-show="pdfFile" x-text="'✅ ' + pdfFile.name"></p>
            <p class="text-gray-500 text-sm mt-2">hoặc click để chọn file — Hỗ trợ PDF, tối đa 30MB</p>
        </label>

        {{-- Parse CTA --}}
        <button @click="parsePdf()"
                :disabled="!pdfFile || parsing"
                class="w-full py-4 bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-500 hover:to-orange-500 text-white font-bold rounded-2xl shadow-lg transition disabled:opacity-40 disabled:cursor-not-allowed">
            <span x-show="!parsing">🔍 Phân tích tài liệu → Tạo slides</span>
            <span x-show="parsing" class="animate-pulse">⏳ Đang phân tích PDF... (vài giây)</span>
        </button>

        <template x-if="parseError">
            <div class="mt-4 p-3 bg-red-900/30 border border-red-500/40 rounded-xl text-red-300 text-sm" x-text="parseError"></div>
        </template>
    </div>

    {{-- Design reference tip --}}
    <div class="glass rounded-xl p-4 text-sm text-gray-500 flex gap-3 items-start">
        <span class="text-2xl">💡</span>
        <div>
            <p class="text-gray-300 font-semibold mb-1">Định dạng PDF bài giảng được hỗ trợ:</p>
            <ul class="space-y-1">
                <li>🔵 <strong>Section Title</strong> — Tiêu đề phần (chữ hoa, ngắn gọn)</li>
                <li>🟢 <strong>Scripture</strong> — Kinh văn (SÁNG THẾ KÝ 2:18 …)</li>
                <li>🟡 <strong>Nguyên ngữ</strong> — Phân tích từ Hê-bơ-rơ / Hy-lạp + mũi tên →</li>
                <li>🟣 <strong>Danh sách</strong> — Bullet points</li>
                <li>🔴 <strong>Kết luận</strong> — Slide kết</li>
            </ul>
        </div>
    </div>
</div>
