{{-- Sermon Editor - Slide List (Left Pane) --}}

<div class="p-3 border-b border-gray-800/80 bg-black/20 flex flex-wrap gap-1.5 max-h-36 overflow-y-auto scrollbar-thin pr-1">
    <template x-for="grp in typeGroups" :key="grp.label">
        <div class="flex gap-1 flex-wrap w-full">
            <span class="text-[9px] uppercase tracking-widest font-bold text-gray-600 w-full mt-1 px-1" x-text="grp.label"></span>
            <template x-for="type in grp.types" :key="type.id">
                <button @click="addSlide(type.id)"
                    class="text-[11px] font-semibold px-3 py-1 rounded-md border transition-all duration-150 hover:scale-105 active:scale-95 cursor-pointer"
                    :class="type.color">
                    + <span x-text="type.label"></span>
                </button>
            </template>
        </div>
    </template>
</div>

<div class="flex-1 overflow-y-auto scrollbar-thin py-3 px-3 space-y-2">
    <template x-if="slides.length === 0">
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-800 flex items-center justify-center text-3xl mb-4 opacity-40">📇</div>
            <p class="text-sm text-gray-500">Kịch bản chưa có thẻ nào.</p>
            <p class="text-xs text-gray-600 mt-1">Bấm vào loại slide bên trên để bắt đầu.</p>
        </div>
    </template>

    <template x-for="(slide, index) in slides" :key="slide.id">
        <div @click="activeIndex = index"
             class="rounded-xl border overflow-hidden transition-all duration-200 cursor-pointer group"
             :class="activeIndex === index
                 ? 'border-blue-500/60 shadow-[0_0_18px_rgba(59,130,246,0.12)] ring-1 ring-blue-500/40 bg-[#1a2540]'
                 : 'border-gray-700/40 bg-[#1a2133] hover:bg-[#1c2638] hover:border-gray-600/60'">

            {{-- Row Header --}}
            <div class="flex items-center gap-2 px-3 py-2 border-b" :class="activeIndex === index ? 'border-blue-500/20' : 'border-gray-700/30'">
                <span class="text-[10px] font-mono text-gray-600 bg-black/30 px-1.5 py-0.5 rounded shrink-0">
                    #<span x-text="index + 1"></span>
                </span>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full border leading-tight"
                      :class="getTypeById(slide.type)?.color || 'text-gray-400 bg-gray-800 border-gray-700'"
                      x-text="getTypeById(slide.type)?.label || slide.type">
                </span>
                <div class="ml-auto flex gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click.stop="moveUp(index)" :disabled="index === 0" class="p-1 rounded text-gray-500 hover:text-white hover:bg-white/10 disabled:opacity-20 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg></button>
                    <button @click.stop="moveDown(index)" :disabled="index === slides.length - 1" class="p-1 rounded text-gray-500 hover:text-white hover:bg-white/10 disabled:opacity-20 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg></button>
                    <button @click.stop="removeSlide(index)" class="p-1 rounded text-red-500/60 hover:text-red-400 hover:bg-red-500/10 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            </div>

            {{-- Inline Form (shown only when active) --}}
            <div class="px-3 pt-3 pb-3 space-y-2" x-show="activeIndex === index">
                @include('ppt._partials.sermon_slide_form')
            </div>

            {{-- Collapsed Preview --}}
            <div class="px-3 py-2 text-xs text-gray-500 truncate" x-show="activeIndex !== index">
                <span x-text="getSlidePreview(slide)"></span>
            </div>
        </div>
    </template>

    <div class="h-16"></div>
</div>
