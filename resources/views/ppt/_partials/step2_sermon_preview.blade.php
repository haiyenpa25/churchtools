{{-- step2_sermon_preview.blade.php — Preview & Export step for Bài Giảng Live --}}
<div x-show="step === 2" x-cloak>

    {{-- Header bar --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-xl font-bold text-yellow-300">🎬 Bước 2 — Xem trước & Xuất PPTX</h2>
            <p class="text-gray-500 text-sm mt-0.5" x-text="`${blocks.length} slides • ${sermonTitle}`"></p>
        </div>
        <div class="flex gap-3">
            <button @click="step = 1; blocks = []; pdfFile = null"
                    class="text-sm bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-xl text-gray-400 hover:text-white transition">
                ← Upload lại
            </button>
            <button @click="generate()"
                    :disabled="generating || blocks.length === 0"
                    class="flex items-center gap-2 bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-500 hover:to-orange-500 text-white font-bold px-5 py-2.5 rounded-xl shadow-lg transition disabled:opacity-50">
                <span x-show="!generating">🚀 Xuất PPTX (@{{blocks.length}} slides)</span>
                <span x-show="generating" class="animate-pulse">⏳ Đang tạo...</span>
            </button>
        </div>
    </div>

    <template x-if="genError">
        <div class="mb-4 p-3 bg-red-900/30 border border-red-500/40 rounded-xl text-red-300 text-sm" x-text="genError"></div>
    </template>

    {{-- Slide grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-h-[720px] overflow-y-auto pr-2 pb-4">
        <template x-for="(block, idx) in blocks" :key="block.id">
            <div class="glass rounded-xl overflow-hidden">
                {{-- Slide number + type badge --}}
                <div class="flex items-center justify-between px-3 py-2">
                    <span class="text-xs text-gray-500 font-mono">#<span x-text="idx+1"></span></span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded border"
                          :class="'badge-' + block.type"
                          x-text="typeLabel(block.type)"></span>
                    <button @click="blocks.splice(idx, 1)"
                            class="text-xs text-gray-600 hover:text-red-400 transition">✕</button>
                </div>

                {{-- Lower-third preview --}}
                <div class="lt-slide @container mx-3 mb-3">

                    {{-- SECTION TITLE type --}}
                    <template x-if="block.type === 'section_title'">
                        <div class="lt-overlay-title">
                            <div class="lt-overlay-accent-top"></div>
                            <p class="lt-section-title px-5 pt-2" x-text="block.title"></p>
                            <p class="lt-section-sub px-5" x-show="block.subtitle" x-text="block.subtitle"></p>
                        </div>
                    </template>

                    {{-- SCRIPTURE type --}}
                    <template x-if="block.type === 'scripture'">
                        <div class="lt-banner">
                            <div class="lt-accent"></div>
                            <div class="lt-content">
                                <p class="lt-ref" x-text="block.reference"></p>
                                <p class="lt-body" x-text="block.body?.substring(0,120) + (block.body?.length > 120 ? '...' : '')"></p>
                            </div>
                        </div>
                    </template>

                    {{-- ORIGIN_WORD type --}}
                    <template x-if="block.type === 'origin_word'">
                        <div class="lt-banner">
                            <div class="lt-accent"></div>
                            <div class="lt-content">
                                <p class="lt-title" x-text="block.title"></p>
                                <div class="flex gap-4 mt-1">
                                    <div class="flex-1">
                                        <template x-for="b in (block.bullets || []).slice(0, Math.ceil((block.bullets||[]).length/2))" :key="b">
                                            <p class="lt-bullet">• <span x-text="b"></span></p>
                                        </template>
                                    </div>
                                    <div class="flex-1">
                                        <template x-for="b in (block.bullets || []).slice(Math.ceil((block.bullets||[]).length/2))" :key="b">
                                            <p class="lt-bullet">• <span x-text="b"></span></p>
                                        </template>
                                    </div>
                                </div>
                                <p x-show="block.deduction" class="lt-deduction mt-1">→ <span x-text="block.deduction"></span></p>
                            </div>
                        </div>
                    </template>

                    {{-- LIST type --}}
                    <template x-if="block.type === 'list'">
                        <div class="lt-banner">
                            <div class="lt-accent"></div>
                            <div class="lt-content">
                                <p x-show="block.title" class="lt-title" x-text="block.title"></p>
                                <template x-for="b in (block.bullets || []).slice(0,3)" :key="b">
                                    <p class="lt-bullet">• <span x-text="b"></span></p>
                                </template>
                                <p x-show="(block.bullets||[]).length > 3" class="lt-bullet text-gray-600">...</p>
                            </div>
                        </div>
                    </template>

                    {{-- CONCLUSION --}}
                    <template x-if="block.type === 'conclusion'">
                        <div class="lt-banner">
                            <div class="lt-accent" style="background:#ef4444;"></div>
                            <div class="lt-content">
                                <p class="lt-ref" x-text="block.title"></p>
                                <p x-show="block.subtitle" class="lt-title" x-text="block.subtitle"></p>
                                <template x-for="b in (block.bullets || []).slice(0,2)" :key="b">
                                    <p class="lt-bullet">• <span x-text="b"></span></p>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- BODY fallback --}}
                    <template x-if="block.type === 'body'">
                        <div class="lt-banner">
                            <div class="lt-accent" style="background:#64748b;"></div>
                            <div class="lt-content">
                                <p class="lt-body" x-text="(block.body || block.title || '').substring(0,100)"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>
