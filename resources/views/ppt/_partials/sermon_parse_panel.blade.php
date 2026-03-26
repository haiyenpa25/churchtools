{{-- AI Parse Panel — Upload / Paste Text to Auto-Generate Slides --}}
<div x-data="sermonParser()" class="shrink-0 bg-black/20 border-b border-white/6">

    {{-- Toggle header --}}
    <button @click="open = !open"
        class="w-full flex items-center justify-between px-4 py-2.5 text-left hover:bg-white/5 transition">
        <div class="flex items-center gap-2">
            <span class="text-base">✨</span>
            <span class="text-sm font-bold text-white">AI Phân Tích Tự Động</span>
            <span class="text-[10px] font-bold px-2 py-0.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-full shadow-[0_0_10px_rgba(99,102,241,0.4)]">PRO</span>
        </div>
        <svg class="w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <div x-show="open" x-transition class="px-3 pb-4 space-y-3">
        {{-- Mode tabs --}}
        <div class="flex bg-black/30 rounded-xl p-1 gap-1 border border-white/5">
            <button @click="inputMode='text'"
                class="flex-1 text-[11px] font-bold py-1.5 rounded-lg transition text-center"
                :class="inputMode === 'text' ? 'bg-indigo-600 text-white shadow-[0_0_12px_rgba(99,102,241,0.4)]' : 'text-gray-500 hover:text-white'">
                📝 Paste Text
            </button>
            <button @click="inputMode='file'"
                class="flex-1 text-[11px] font-bold py-1.5 rounded-lg transition text-center"
                :class="inputMode === 'file' ? 'bg-indigo-600 text-white shadow-[0_0_12px_rgba(99,102,241,0.4)]' : 'text-gray-500 hover:text-white'">
                📄 Upload File
            </button>
        </div>

        {{-- Text input mode --}}
        <div x-show="inputMode === 'text'">
            <textarea x-model="rawText"
                class="w-full bg-black/40 border border-white/10 rounded-xl text-[11px] text-gray-300 p-3 resize-none h-28 focus:outline-none focus:border-indigo-500/60 placeholder-gray-700 font-mono leading-relaxed"
                placeholder="Dán nội dung bài giảng vào đây...&#10;&#10;Hệ thống sẽ tự nhận ra:&#10;• Câu Kinh Thánh (Giăng 3:16)&#10;• Ý chính (I. / II. / 1.)&#10;• Nguyên ngữ Hebrew/Greek&#10;• Danh sách, câu hỏi, kết luận..."></textarea>
        </div>

        {{-- File upload mode --}}
        <div x-show="inputMode === 'file'">
            <div @dragover.prevent @dragenter.prevent="dragging=true" @dragleave="dragging=false"
                 @drop.prevent="handleDrop($event)"
                 class="border-2 border-dashed rounded-xl p-5 text-center transition-all cursor-pointer"
                 :class="dragging ? 'border-indigo-500 bg-indigo-500/10' : 'border-white/10 hover:border-white/25'">
                <input type="file" id="sermonFileInput" class="hidden" accept=".pdf,.pptx,.txt" @change="handleFileSelect($event)">
                <label for="sermonFileInput" class="cursor-pointer">
                    <div class="text-3xl mb-2 opacity-50" x-text="file ? '📄' : '☁️'"></div>
                    <p x-show="!file" class="text-xs text-gray-500">Kéo thả hoặc bấm để chọn file</p>
                    <p x-show="!file" class="text-[10px] text-gray-700 mt-1">PDF · PPTX · TXT</p>
                    <p x-show="file" class="text-xs text-indigo-400 font-semibold" x-text="file?.name"></p>
                </label>
            </div>
        </div>

        {{-- Action button --}}
        <button @click="runParse()"
            :disabled="isParsing || (!rawText.trim() && !file)"
            class="w-full py-2.5 rounded-xl font-black text-sm transition-all flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed"
            :class="isParsing
                ? 'bg-indigo-600/50 text-indigo-300 cursor-wait'
                : 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white hover:from-purple-500 hover:to-indigo-500 shadow-[0_4px_20px_rgba(99,102,241,0.3)] hover:shadow-[0_4px_30px_rgba(99,102,241,0.5)] hover:-translate-y-0.5'">
            <template x-if="!isParsing">
                <span>🤖 Phân Tích Tự Động</span>
            </template>
            <template x-if="isParsing">
                <div class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-indigo-300" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="text-indigo-300 text-xs font-semibold" x-text="parseStatus"></span>
                </div>
            </template>
        </button>

        {{-- Parse result summary --}}
        <div x-show="lastResult" class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl px-4 py-3 flex items-center gap-3" x-transition>
            <span class="text-xl">✅</span>
            <div>
                <p class="text-sm font-bold text-emerald-400" x-text="`Tìm thấy ${lastResult?.total || 0} slide!`"></p>
                <p class="text-[10px] text-emerald-500/70" x-text="lastResult?.summary || ''"></p>
            </div>
        </div>

        {{-- Error display --}}
        <div x-show="parseError" class="bg-red-500/10 border border-red-500/30 rounded-xl px-3 py-2" x-transition>
            <p class="text-xs text-red-400" x-text="parseError"></p>
        </div>
    </div>
</div>

<script>
const _SERMON_PARSE_URL      = "{{ url('/api/ppt/sermon/parse') }}";
const _SERMON_PARSE_TEXT_URL = "{{ url('/api/ppt/sermon/parse-text') }}";

function sermonParser() {
    return {
        open: true,
        inputMode: 'text',
        rawText: '',
        file: null,
        dragging: false,
        isParsing: false,
        parseStatus: 'Đang phân tích...',
        lastResult: null,
        parseError: '',

        handleDrop(event) {
            this.dragging = false;
            const f = event.dataTransfer.files[0];
            if (f) { this.file = f; this.inputMode = 'file'; }
        },

        handleFileSelect(event) {
            this.file = event.target.files[0] || null;
        },

        async runParse() {
            this.isParsing = true;
            this.parseError = '';
            this.lastResult = null;

            const statuses = [
                'Trích xuất nội dung...', 'Phân đoạn văn bản...',
                'Nhận diện câu Kinh Thánh...', 'Phân tích cấu trúc...',
                'Phân loại slide thông minh...', 'Hoàn thiện kịch bản...'
            ];
            let si = 0;
            this.parseStatus = statuses[si];
            const timer = setInterval(() => {
                si = Math.min(si + 1, statuses.length - 1);
                this.parseStatus = statuses[si];
            }, 700);

            try {
                let resp;
                if (this.inputMode === 'text' && this.rawText.trim()) {
                    resp = await fetch(_SERMON_PARSE_TEXT_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ text: this.rawText })
                    });
                } else if (this.file) {
                    const form = new FormData();
                    form.append('file', this.file);
                    resp = await fetch(_SERMON_PARSE_URL, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: form
                    });
                } else {
                    throw new Error('Chưa nhập nội dung hoặc chọn file!');
                }

                clearInterval(timer);
                const data = await resp.json();
                if (data.status === 'success' && data.slides) {
                    // Push slides into the main sermonEditor Alpine component
                    const editorEl = document.querySelector('[x-data^="sermonEditor"]');
                    if (editorEl) {
                        const editor = Alpine.$data(editorEl);
                        if (editor) {
                            editor.slides = data.slides;
                            editor.activeIndex = 0;
                        }
                    }
                    document.dispatchEvent(new CustomEvent('sermon:slides-loaded', { detail: data.slides }));

                    const confidence = data.slides.map(s => s.confidence || 0);
                    const avgConf = confidence.length ? Math.round(confidence.reduce((a,b)=>a+b,0)/confidence.length*100) : 0;
                    const highConf = confidence.filter(c => c >= 0.85).length;
                    this.lastResult = {
                        total: data.slides.length,
                        summary: `Độ tin cậy TB: ${avgConf}% · ${highConf}/${data.slides.length} slide chắc chắn cao`
                    };
                } else {
                    this.parseError = data.message || 'Lỗi không xác định từ engine';
                }
            } catch(e) {
                clearInterval(timer);
                this.parseError = e.message;
            }
            this.isParsing = false;
        }
    };
}
</script>

