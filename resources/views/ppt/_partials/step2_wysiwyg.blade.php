{{-- ══ STEP 2: WYSIWYG Canvas Preview ══ --}}
<div x-show="step === 2" x-cloak x-transition.opacity class="mt-6" x-data="{ previewCols: 2 }">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-white flex items-center">
            <span class="text-blue-500 mr-2">●</span> Trình chỉnh sửa Trực quan (WYSIWYG)
        </h3>
        <div class="flex items-center gap-3">
            {{-- Preview size slider --}}
            <div class="flex items-center gap-2 bg-gray-800/60 border border-gray-700 rounded-lg px-3 py-1.5">
                <span class="text-[10px] text-gray-500 select-none">Nhỏ</span>
                <input type="range" min="1" max="2" step="1" x-model.number="previewCols"
                       class="w-14 h-1 accent-blue-500 cursor-pointer">
                <span class="text-[10px] text-gray-500 select-none">To</span>
                <span class="text-[10px] text-blue-400 font-mono ml-1"
                      x-text="previewCols === 1 ? '1 cột' : '2 cột'"></span>
            </div>
            <button type="button" @click="debugMode = !debugMode"
                    :class="debugMode ? 'bg-yellow-500/20 text-yellow-300 border-yellow-500/50' : 'bg-gray-800 text-gray-400 border-gray-700'"
                    class="text-xs font-semibold px-3 py-1.5 border rounded-lg transition">
                🐞 Debug
            </button>
            <button type="button" @click="step = 1"
                    class="text-sm font-semibold text-gray-400 hover:text-white px-3 py-1.5 bg-gray-800 rounded-lg">
                ← Quay Lại
            </button>
        </div>
    </div>


    {{-- Slide Grid — columns driven by previewCols slider --}}
    <div class="gap-4 max-h-[600px] overflow-y-auto pr-2 pb-4 pt-2"
         :class="previewCols === 1 ? 'grid grid-cols-1' : 'grid grid-cols-1 md:grid-cols-2'">
        <template x-for="(block, index) in blocks" :key="block.id">
            <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700 relative group aspect-video w-full flex items-center justify-center @container shadow-lg">

                {{-- Slide label --}}
                <div class="absolute top-1.5 left-1.5 bg-blue-600/90 text-white text-[9px] font-bold px-2 py-0.5 rounded shadow z-30"
                     x-text="block.label || 'Slide ' + (index+1)"></div>

                {{-- Green screen background --}}
                <div class="absolute inset-0 bg-[#00FF00]"></div>

                {{-- Banner --}}
                <div class="absolute bottom-0 inset-x-0"
                     :class="debugMode ? 'ring-2 ring-blue-400' : ''"
                     :style="`
                       height: ${L.S.banner.h_ratio * 100}%;
                       background-color: #${activeTheme.banner_color || '004D40'};
                     `">

                    {{-- Logo circle --}}
                    <div class="absolute rounded-full border-2 overflow-hidden flex justify-center items-center shadow-lg"
                         :class="debugMode ? 'ring-2 ring-yellow-300' : ''"
                         :style="`
                           width:  ${L.S.logo.size_ratio_h * L.SLIDE_H / L.SLIDE_W * 100}%;
                           height: ${L.S.logo.size_ratio_h * 100 / L.S.banner.h_ratio}%;
                           left:   ${L.S.logo.dx_ratio_w * 100}%;
                           bottom: ${-L.S.logo.overlap_ratio * L.S.logo.size_ratio_h * L.SLIDE_H / L.S.banner.h_ratio / L.SLIDE_H * 100}%;
                           border-color: #${activeTheme.logo_border_color || 'FFD700'};
                           background-color: #${activeTheme.logo_bg_color || '004D40'};
                           box-shadow: 0 0 12px #${activeTheme.logo_border_color || 'FFD700'}40;
                         `">
                        <template x-if="form.logo_file">
                            <img :src="URL.createObjectURL(form.logo_file)" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!form.logo_file">
                            <span class="font-serif font-bold text-[4cqw]"
                                  :style="`color: #${activeTheme.logo_border_color || 'FFD700'};`">♫</span>
                        </template>
                    </div>

                    {{-- Text box —  use schema padding values for accurate preview --}}
                    <div class="absolute overflow-hidden flex flex-col justify-center items-center text-center"
                         :class="debugMode ? 'ring ring-red-400' : ''"
                         :style="`
                           left:   ${(L.S.logo.dx_ratio_w + L.S.logo.size_ratio_h * L.SLIDE_H / L.SLIDE_W + (L.S.text.logo_gap_ratio||0.01)) * 100}%;
                           top:    ${(L.S.text.padding_top_ratio_h || 0) * 100}%;
                           right:  ${L.S.text.padding_right_ratio_w * 100}%;
                           bottom: ${(L.S.text.padding_bottom_ratio_h || 0) * 100}%;
                           line-height: ${L.S.text.font.line_height};
                         `">
                        <template x-for="(line, lidx) in block.lines" :key="lidx">
                            <div class="w-full font-serif font-bold"
                                 style="-webkit-text-stroke: 0.3px rgba(0,0,0,0.9); margin: 0; padding: 0;"
                                 :style="`
                                    font-size: ${Math.min((+form.overrides.font_size || L.S.text.font.preferred_size_pt) / 36 * 7, 8.5)}cqh;
                                    color:     #${activeTheme.color || 'FFD700'};
                                    line-height: ${L.S.text.font.line_height};
                                    text-shadow: 1px 2px 4px rgba(0,0,0,0.9);
                                 `"
                                 x-text="line.primary">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Debug: safe area overlay --}}
                <template x-if="debugMode">
                    <div class="absolute inset-0 pointer-events-none"
                         :style="`border: ${L.S.safe_area.left * 100}% solid rgba(255,255,0,0.3);`"></div>
                </template>
            </div>
        </template>
    </div>

    {{-- Export button --}}
    <button type="button" @click="submit" :disabled="isLoading"
            class="mt-6 w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white font-bold py-4 px-6 rounded-xl transition shadow-lg shadow-purple-500/20 disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center group">
        <span x-show="!isLoading" class="flex items-center gap-2">
            <svg class="w-6 h-6 group-hover:-translate-y-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
            Xuất Bản File PowerPoint 🚀
        </span>
        <span x-show="isLoading" class="animate-pulse flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            </svg>
            Đang kết nối Python Engine (Mất ~3s)...
        </span>
    </button>
</div>
