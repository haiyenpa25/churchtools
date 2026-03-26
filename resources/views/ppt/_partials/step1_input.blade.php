{{-- ══ STEP 1: Template picker + logo + colors + text input ══ --}}

{{-- Template Gallery --}}
<div>
    <label class="block text-sm font-semibold text-gray-300 mb-3">
        🎨 Chọn Template Banner
        <span class="ml-2 text-xs text-gray-500 font-normal" x-text="templates.length + ' mẫu'"></span>
    </label>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <template x-for="tmpl in templates" :key="tmpl.id">
            <button type="button"
                    @click="selectTemplate(tmpl)"
                    class="relative group rounded-xl overflow-hidden border-2 transition-all duration-200 focus:outline-none"
                    :class="form.template_id == tmpl.id
                        ? 'border-white scale-105 shadow-xl shadow-white/10'
                        : 'border-gray-700 hover:border-gray-500 opacity-80 hover:opacity-100'">

                {{-- Mini Banner Preview --}}
                <div class="w-full relative overflow-hidden" style="aspect-ratio:16/9; background:#00FF00;">
                    <div class="absolute bottom-0 left-0 right-0" style="height:35%;"
                         :style="`background-color:#${tmpl._fc?.banner_color || '004D40'};`">

                        {{-- Logo circle --}}
                        <div class="absolute rounded-full border-2 flex items-center justify-center"
                             style="height:75%; aspect-ratio:1/1; left:4%; top:50%; transform:translateY(-50%);"
                             :style="`border-color:#${tmpl._fc?.logo_border_color || 'FFD700'}; background-color:#${tmpl._fc?.logo_bg_color || '004D40'};`">
                             <svg viewBox="0 0 24 24" fill="currentColor" style="width:50%;height:50%"
                                  :style="`color:#${tmpl._fc?.logo_border_color || 'FFD700'};`">
                                 <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                             </svg>
                        </div>

                        {{-- Text sample --}}
                        <div class="absolute inset-y-0 flex flex-col justify-center font-bold overflow-hidden"
                             style="left:32%; right:4%; font-size:8px; font-family:Georgia,serif; line-height:1.15;"
                             :style="`color:#${tmpl._fc?.color || 'FFD700'};`">
                             <div>Thánh Ca Ngài</div>
                             <div style="opacity:0.75">Con Dâng Lên</div>
                        </div>
                    </div>
                </div>

                {{-- Template Name --}}
                <div class="py-1.5 px-2 text-center text-[10px] font-semibold truncate"
                     :class="form.template_id == tmpl.id ? 'text-white bg-white/10' : 'text-gray-400 bg-gray-900'"
                     x-text="tmpl.name"></div>

                {{-- Check badge --}}
                <div x-show="form.template_id == tmpl.id"
                     class="absolute top-1 right-1 w-5 h-5 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </button>
        </template>
    </div>
    <p class="mt-2 text-xs text-gray-500">Màu sắc từ database — 10 chủ đề khác nhau.</p>
</div>

{{-- Logo + Quick color --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Logo Upload --}}
    <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/60">
        <label class="block text-sm font-semibold text-gray-300 mb-3">🎵 Logo Nhà Thờ</label>
        <div class="flex items-center gap-3">
            <div class="w-14 h-14 rounded-full border-2 flex items-center justify-center overflow-hidden flex-shrink-0"
                 :style="`border-color:#${activeTheme.logo_border_color||'FFD700'}; background:#${activeTheme.logo_bg_color||'004D40'};`">
                <template x-if="form.logo_file">
                    <img :src="URL.createObjectURL(form.logo_file)" class="w-full h-full object-cover rounded-full">
                </template>
                <template x-if="!form.logo_file">
                    <span class="text-2xl font-serif" :style="`color:#${activeTheme.logo_border_color||'FFD700'};`">♫</span>
                </template>
            </div>
            <div class="flex-1">
                <input type="file" accept="image/png,image/jpeg,image/webp" id="logo_upload_input"
                       @change="form.logo_file = $event.target.files[0]" class="hidden">
                <label for="logo_upload_input"
                       class="block w-full text-center text-sm font-semibold text-blue-300 py-2 px-3 bg-blue-500/15 hover:bg-blue-500/25 border border-blue-500/30 rounded-lg cursor-pointer transition">
                    📂 Chọn ảnh logo
                </label>
                <p class="text-[10px] text-gray-500 mt-1 text-center"
                   x-text="form.logo_file ? form.logo_file.name : 'PNG / JPG trong suốt'"></p>
            </div>
        </div>
    </div>

    {{-- Quick Color Pickers --}}
    <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/60">
        <label class="block text-sm font-semibold text-gray-300 mb-3">🎨 Màu sắc tuỳ chỉnh</label>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-400 block mb-1">Màu nền Banner</label>
                <div class="flex items-center gap-2">
                    <input type="color" class="w-9 h-9 rounded-lg border-0 cursor-pointer bg-transparent"
                           :value="`#${activeTheme.banner_color||'004D40'}`"
                           @input="form.custom_banner_color = $event.target.value.replace('#','')">
                    <span class="text-xs text-gray-400 font-mono uppercase"
                          x-text="'#' + (form.custom_banner_color || activeTheme.banner_color || '004D40')"></span>
                </div>
            </div>
            <div>
                <label class="text-xs text-gray-400 block mb-1">Màu Chữ</label>
                <div class="flex items-center gap-2">
                    <input type="color" class="w-9 h-9 rounded-lg border-0 cursor-pointer bg-transparent"
                           :value="`#${activeTheme.color||'FFD700'}`"
                           @input="form.custom_text_color = $event.target.value.replace('#','')">
                    <span class="text-xs text-gray-400 font-mono uppercase"
                          x-text="'#' + (form.custom_text_color || activeTheme.color || 'FFD700')"></span>
                </div>
            </div>
            <div>
                <label class="text-xs text-gray-400 block mb-1">Màu viền Logo</label>
                <div class="flex items-center gap-2">
                    <input type="color" class="w-9 h-9 rounded-lg border-0 cursor-pointer bg-transparent"
                           :value="`#${activeTheme.logo_border_color||'FFD700'}`"
                           @input="form.custom_logo_border = $event.target.value.replace('#','')">
                    <span class="text-xs text-gray-400 font-mono uppercase"
                          x-text="'#' + (form.custom_logo_border || activeTheme.logo_border_color || 'FFD700')"></span>
                </div>
            </div>
            <div>
                <label class="text-xs text-gray-400 block mb-1">Cỡ chữ (pt)</label>
                <input type="number" step="2" min="16" max="60"
                       x-model="form.overrides.font_size"
                       :placeholder="activeTheme.size || 36"
                       class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg py-1.5 px-2 text-sm focus:border-blue-500 outline-none">
            </div>
        </div>
    </div>
</div>

{{-- Advanced overrides --}}
<div class="bg-gray-800/20 rounded-xl border border-gray-700/40">
    <div class="flex items-center justify-between cursor-pointer p-3" @click="showAdvanced = !showAdvanced">
        <label class="block text-xs font-medium text-gray-500 cursor-pointer">⚙️ Nâng cao: Toạ độ thủ công</label>
        <svg class="w-4 h-4 text-gray-600 transition-transform duration-200" :class="showAdvanced ? 'rotate-180' : ''"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
    <div x-show="showAdvanced" x-transition.opacity class="px-3 pb-3 grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div><label class="text-xs text-gray-500 block mb-1">X (inches)</label><input type="number" step="0.1" x-model="form.overrides.x" placeholder="Auto" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg py-1.5 px-2 text-sm focus:border-blue-500 outline-none"></div>
        <div><label class="text-xs text-gray-500 block mb-1">Y (inches)</label><input type="number" step="0.1" x-model="form.overrides.y" placeholder="Auto" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg py-1.5 px-2 text-sm focus:border-blue-500 outline-none"></div>
        <div><label class="text-xs text-gray-500 block mb-1">Width</label><input type="number" step="0.1" x-model="form.overrides.width" placeholder="Auto" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg py-1.5 px-2 text-sm focus:border-blue-500 outline-none"></div>
        <div><label class="text-xs text-gray-500 block mb-1">Height</label><input type="number" step="0.1" x-model="form.overrides.height" placeholder="Auto" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg py-1.5 px-2 text-sm focus:border-blue-500 outline-none"></div>
    </div>
</div>

{{-- Text Input / Library Tabs --}}
<div x-show="step === 1" x-transition.opacity class="mt-8">
    
    {{-- Tabs Header --}}
    <div class="flex border-b border-gray-700 mb-5 gap-6">
        <button type="button" @click="inputType = 'library'" 
                class="pb-2 text-sm font-semibold transition"
                :class="inputType === 'library' ? 'border-b-2 border-blue-500 text-blue-400' : 'text-gray-500 hover:text-gray-300'">
            🎵 Thư viện Bài hát 
        </button>
        <button type="button" @click="inputType = 'text'" 
                class="pb-2 text-sm font-semibold transition"
                :class="inputType === 'text' ? 'border-b-2 border-blue-500 text-blue-400' : 'text-gray-500 hover:text-gray-300'">
            📝 Nhập chữ (Bulk Text)
        </button>
    </div>

    {{-- TAB 1: LIBRARY --}}
    <div x-show="inputType === 'library'" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        {{-- Left: Song List --}}
        <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-4 flex flex-col" style="height: 400px;">
            <div class="flex gap-2 mb-3">
                <input type="text" x-model="searchSong" placeholder="Tìm kiếm tên bài hoặc số..." class="flex-1 bg-gray-900 border border-gray-700 focus:border-blue-500 text-white rounded-lg px-3 py-2 text-sm outline-none">
                <select x-model="searchCategory" class="bg-gray-900 border border-gray-700 focus:border-blue-500 text-white rounded-lg px-2 py-2 text-sm outline-none">
                    <option value="">Tất cả danh mục</option>
                    <option value="thanh ca tin lanh">Thánh ca</option>
                    <option value="ton vinh chua hang huu">Tôn vinh Chúa Hằng Hữu</option>
                </select>
            </div>
            
            <div class="flex-1 overflow-y-auto space-y-1 pr-2 custom-scrollbar">
                <template x-if="songsLoading">
                    <div class="text-center text-gray-500 text-sm py-10">Đang tải thư viện...</div>
                </template>
                <template x-for="song in filteredSongs" :key="song.id">
                    <div class="flex justify-between items-center p-2.5 hover:bg-gray-700/60 rounded-lg group cursor-pointer transition border border-transparent hover:border-gray-600"
                         @click="addSongToPlaylist(song)">
                        <div>
                            <p class="text-sm font-semibold text-gray-200 group-hover:text-blue-300 transition" x-text="(song.number ? song.number + '. ' : '') + song.title"></p>
                            <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider" x-text="song.category"></p>
                        </div>
                        <button type="button" class="text-blue-500 opacity-0 group-hover:opacity-100 transition scale-125 font-bold">+</button>
                    </div>
                </template>
                <template x-if="filteredSongs.length === 0 && !songsLoading">
                    <div class="text-center text-gray-500 text-sm py-10">Không tìm thấy bài hát.</div>
                </template>
            </div>
        </div>

        {{-- Right: Playlist --}}
        <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-4 flex flex-col" style="height: 400px;">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-sm font-semibold text-gray-300">Danh sách chuẩn bị (<span class="text-blue-400" x-text="playlist.length"></span> bài)</h3>
                <button type="button" @click="playlist = []" x-show="playlist.length > 0" class="text-xs text-red-500 hover:text-red-400 transition">Xoá tất cả</button>
            </div>
            
            <div class="flex-1 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                <template x-for="(song, index) in playlist" :key="index + '-' + song.id">
                    <div class="flex justify-between items-center p-2.5 bg-gray-900 border border-gray-700 rounded-lg group">
                        <div class="flex-1 truncate">
                            <span class="text-xs font-bold text-gray-600 w-5 inline-block" x-text="(index + 1) + '.'"></span>
                            <span class="text-sm font-semibold text-gray-200" x-text="(song.number ? song.number + '. ' : '') + song.title"></span>
                        </div>
                        <div class="flex items-center gap-1.5 ml-2 opacity-60 group-hover:opacity-100 transition">
                            <button type="button" @click.stop="moveSongUp(index)" :disabled="index === 0" class="p-1 text-gray-400 hover:text-white disabled:opacity-20 transition">▲</button>
                            <button type="button" @click.stop="moveSongDown(index)" :disabled="index === playlist.length - 1" class="p-1 text-gray-400 hover:text-white disabled:opacity-20 transition">▼</button>
                            <button type="button" @click.stop="removeSong(index)" class="p-1 text-red-500 hover:text-red-400 ml-2 transition">✕</button>
                        </div>
                    </div>
                </template>
                <div x-show="playlist.length === 0" class="text-center text-gray-500 text-sm mt-20 flex flex-col items-center">
                    <span class="text-4xl mb-2 opacity-20">📂</span>
                    Danh sách trống.<br>Nhấn chuột vào bài hát bên trái để thêm vô đây.
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: TEXT INPUT --}}
    <div x-show="inputType === 'text'" class="mb-4">
        <div class="flex justify-end items-end mb-2">
            <div class="flex space-x-2">
                <button type="button" @click="$refs.txtFile.click()"
                        class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 py-1.5 px-3 bg-emerald-500/10 rounded-lg flex items-center transition">
                    <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Tải lên File .txt
                </button>
                <button type="button" @click="$refs.pptFile.click()" :disabled="isExtracting"
                        class="text-xs font-semibold text-blue-400 hover:text-blue-300 py-1.5 px-3 bg-blue-500/10 rounded-lg flex items-center transition">
                    <span x-show="!isExtracting" class="flex items-center">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Cào từ file PPT cũ
                    </span>
                    <span x-show="isExtracting" class="animate-pulse flex items-center">Đang bòn rút chữ...</span>
                </button>
            </div>
        </div>

        <input type="file" x-ref="txtFile" @change="loadTxtFile" class="hidden" accept=".txt">
        <input type="file" id="pptExtraFile" x-ref="pptFile" @change="extractText" class="hidden" accept=".ppt,.pptx">

        <textarea x-model="form.text" rows="12"
                  class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition placeholder-gray-600 custom-scrollbar"
                  placeholder="Dán bài hát tự do, Kinh Thánh hoặc bài giảng vào đây..."></textarea>
    </div>

    {{-- Universal Generate Button --}}
    <button type="button" @click="parseTextToBlocks" :disabled="isLoading || (inputType === 'library' && playlist.length === 0)"
            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-6 rounded-xl transition shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center mt-2 border border-blue-400/30">
        <span x-show="!isLoading" class="text-lg tracking-wide">Phân tích Cú pháp & Dựng Preview ✨</span>
        <span x-show="isLoading" class="animate-pulse flex items-center text-lg tracking-wide">
            <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            </svg>
            Đang xếp gạch...
        </span>
    </button>
</div>
