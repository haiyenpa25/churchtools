<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sermon Generator — ChurchTools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;0,800;0,900;1,700;1,900&family=Noto+Serif:wght@600;700&display=swap" rel="stylesheet">
    <style>
        *,:before,:after{box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#07090F;color:#E2E8F0}
        .scrollbar-thin::-webkit-scrollbar{width:5px;height:5px}
        .scrollbar-thin::-webkit-scrollbar-track{background:transparent}
        .scrollbar-thin::-webkit-scrollbar-thumb{background:#2D3748;border-radius:4px}
        .scrollbar-thin::-webkit-scrollbar-thumb:hover{background:#4A5568}

        .field-label{display:block;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;margin-bottom:4px}
        .field-input{display:block;width:100%;background:rgba(0,0,0,.4);border:1px solid #2d3748;color:#e2e8f0;border-radius:8px;padding:7px 10px;font-size:.825rem;line-height:1.5;outline:none;transition:border-color .15s,background .15s;font-family:inherit}
        .field-input:focus{border-color:#3b82f6;background:rgba(30,41,59,.8)}
        .field-input::placeholder{color:#475569}
        textarea.field-input{font-size:.8rem}

        /* Grid BG */
        .preview-bg{background:#04060B;background-image:radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);background-size:28px 28px;}
        
        /* Slide canvas */
        .slide-canvas{width:100%;max-width:960px;aspect-ratio:16/9;position:relative;overflow:hidden;border-radius:12px;box-shadow:0 24px 64px rgba(0,0,0,.7),0 0 0 1px rgba(255,255,255,.08)}
        
        /* Tag colours mapped to text-*/
        .c-gold{color:hsl(43,80%,60%);background:hsl(43,70%,15%);border-color:hsl(43,60%,30%)}
        .c-emerald{color:hsl(152,60%,60%);background:hsl(152,40%,12%);border-color:hsl(152,40%,24%)}
        .c-indigo{color:hsl(238,80%,75%);background:hsl(238,40%,15%);border-color:hsl(238,40%,30%)}
        .c-rose{color:hsl(345,80%,65%);background:hsl(345,50%,13%);border-color:hsl(345,50%,26%)}
        .c-cyan{color:hsl(188,75%,65%);background:hsl(188,50%,12%);border-color:hsl(188,40%,22%)}
        .c-amber{color:hsl(35,80%,65%);background:hsl(35,60%,13%);border-color:hsl(35,55%,25%)}
        .c-purple{color:hsl(270,70%,75%);background:hsl(270,40%,14%);border-color:hsl(270,40%,28%)}
        .c-green{color:hsl(140,60%,60%);background:hsl(140,40%,10%);border-color:hsl(140,40%,20%)}
        .c-red{color:hsl(0,75%,65%);background:hsl(0,50%,13%);border-color:hsl(0,50%,28%)}
        .c-teal{color:hsl(175,65%,55%);background:hsl(175,45%,10%);border-color:hsl(175,40%,22%)}
        .c-orange{color:hsl(25,85%,65%);background:hsl(25,60%,13%);border-color:hsl(25,50%,26%)}
        .c-blue{color:hsl(210,75%,70%);background:hsl(210,50%,13%);border-color:hsl(210,45%,28%)}
        .c-gray{color:#94a3b8;background:#1a2030;border-color:#2d3748}
    </style>
    <script>
        /* Laravel base URLs — generated server-side to handle subdirectory installs */
        window.SERMON_API = {
            generate:   "{{ url('/api/ppt/sermon/generate') }}",
            parseFile:  "{{ url('/api/ppt/sermon/parse') }}",
            parseText:  "{{ url('/api/ppt/sermon/parse-text') }}",
            download:   "{{ url('/ppt/sermon/download') }}"
        };
    </script>
</head>
<body class="h-screen flex flex-col overflow-hidden" x-data="sermonEditor()">

<!-- ══════════ HEADER ══════════ -->
<header class="shrink-0 h-14 bg-[#0C1117]/95 border-b border-white/5 flex items-center justify-between px-5 z-30 backdrop-blur-md shadow-xl">
    <div class="flex items-center gap-3">
        <a href="{{ url('/') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="h-5 w-px bg-white/10"></div>
        <div>
            <h1 class="font-bold text-white text-[15px] flex items-center gap-2 leading-none">
                <span class="text-yellow-500">✝</span> Sermon PPT Generator
            </h1>
            <p class="text-[10px] text-gray-500 mt-0.5 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block shadow-[0_0_6px_rgba(52,211,153,.8)]"></span>
                <span x-text="slides.length + ' thẻ kịch bản  ·  Xem trước Live tức thì'"></span>
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <!-- Slide Navigator -->
        <div class="flex gap-1 bg-white/5 border border-white/10 rounded-lg p-1" x-show="slides.length > 0">
            <button @click="activeIndex = Math.max(0, activeIndex - 1)" :disabled="activeIndex === 0" class="w-7 h-7 rounded-md flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 disabled:opacity-30 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg></button>
            <span class="text-xs text-gray-400 px-2 flex items-center font-medium"><span x-text="activeIndex + 1"></span> / <span x-text="slides.length"></span></span>
            <button @click="activeIndex = Math.min(slides.length - 1, activeIndex + 1)" :disabled="activeIndex >= slides.length - 1" class="w-7 h-7 rounded-md flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 disabled:opacity-30 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></button>
        </div>

        <div class="h-6 w-px bg-white/10 mx-1"></div>

        <!-- Template Button -->
        <button @click="showTemplateModal = true" class="bg-indigo-600/20 text-indigo-300 border border-indigo-500/30 hover:bg-indigo-600/40 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2 transition mr-1">
            🎨 Template <span class="w-1.5 h-1.5 rounded-full" :class="uploadedTemplatePath ? 'bg-emerald-500 shadow-[0_0_6px_rgba(16,185,129,0.8)]' : 'bg-gray-600'"></span>
        </button>

        <!-- Export Button -->
        <button @click="generatePpt()" :disabled="isExporting"
            class="bg-gradient-to-br from-yellow-500 to-amber-600 hover:from-yellow-400 hover:to-amber-500 text-black font-bold text-sm px-5 py-2 rounded-lg transition-all hover:-translate-y-0.5 shadow-[0_4px_16px_rgba(246,173,85,0.25)] flex items-center gap-2 disabled:opacity-60 disabled:cursor-wait">
            <template x-if="!isExporting">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </template>
            <template x-if="isExporting">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            </template>
            <span x-text="isExporting ? exportStatus : 'Xuất Dual PPTX'"></span>
        </button>
    </div>
</header>

<!-- ══════════ WORKSPACE ══════════ -->
<div class="flex-1 flex overflow-hidden">

    <!-- ── Left: Slide Flow Editor ── -->
    <div class="w-[400px] shrink-0 bg-[#0C1117] border-r border-white/6 flex flex-col z-10 shadow-2xl">

        <!-- Title bar of left pane -->
        <div class="shrink-0 flex items-center justify-between px-4 py-3 border-b border-white/6 bg-black/20">
            <span class="text-sm font-bold text-gray-200">📋 Kịch Bản Slide</span>
            <button @click="slides = []; activeIndex = 0" class="text-[10px] text-gray-600 hover:text-red-400 transition font-medium px-2 py-1 rounded hover:bg-red-500/10">Xoá tất cả</button>
        </div>

        <!-- AI Parse Panel -->
        @include('ppt._partials.sermon_parse_panel')

        <!-- Grouped Type Add Toolbar -->
        <div class="shrink-0 bg-black/10 border-b border-white/5 max-h-40 overflow-y-auto scrollbar-thin p-3 space-y-2">
            <template x-for="grp in typeGroups" :key="grp.label">
                <div>
                    <p class="text-[9px] uppercase tracking-widest font-black text-gray-600 mb-1.5" x-text="grp.label"></p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="type in grp.types" :key="type.id">
                            <button @click="addSlide(type.id)"
                                class="text-[11px] font-semibold px-2.5 py-1 rounded-md border transition-all hover:brightness-125 hover:scale-105 active:scale-95 cursor-pointer"
                                :class="type.color">
                                + <span x-text="type.label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Slide List -->
        <div class="flex-1 overflow-y-auto scrollbar-thin py-3 px-3 space-y-2">
            <template x-if="slides.length === 0">
                <div class="flex flex-col items-center justify-center py-16 text-center select-none pointer-events-none">
                    <div class="text-5xl opacity-10 mb-4">📋</div>
                    <p class="text-sm font-semibold text-gray-600">Kịch bản trống</p>
                    <p class="text-xs text-gray-700 mt-1">Bấm loại thẻ phía trên để soạn bài giảng</p>
                </div>
            </template>

            <template x-for="(slide, index) in slides" :key="slide.id">
                <div @click="activeIndex = index"
                     class="rounded-xl border overflow-hidden transition-all duration-200 cursor-pointer group"
                     :class="activeIndex === index
                         ? 'border-blue-500/50 shadow-[0_0_20px_rgba(59,130,246,0.1)] ring-1 ring-blue-500/30 bg-[#111e38]'
                         : 'border-white/6 bg-[#0f1520] hover:bg-[#121b2c] hover:border-white/10'">

                    <!-- Row Header -->
                    <div class="flex items-center gap-2 px-3 py-2 border-b" :class="activeIndex === index ? 'border-blue-500/20 bg-blue-500/5' : 'border-white/5 bg-black/20'">
                        <span class="font-mono text-[10px] text-gray-600 bg-black/30 px-1.5 py-0.5 rounded shrink-0">#<span x-text="index+1"></span></span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full border leading-tight"
                              :class="getTypeById(slide.type)?.color || 'c-gray'"
                              x-text="getTypeById(slide.type)?.label || slide.type">
                        </span>
                        <div class="ml-auto flex gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                            <button @click.stop="moveUp(index)" :disabled="index===0" class="p-1 rounded text-gray-500 hover:text-white hover:bg-white/10 disabled:opacity-20 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg></button>
                            <button @click.stop="moveDown(index)" :disabled="index>=slides.length-1" class="p-1 rounded text-gray-500 hover:text-white hover:bg-white/10 disabled:opacity-20 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg></button>
                            <button @click.stop="duplicateSlide(index)" class="p-1 rounded text-gray-500 hover:text-blue-400 hover:bg-blue-500/10 transition"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></button>
                            <button @click.stop="removeSlide(index)" class="p-1 rounded text-gray-600 hover:text-red-400 hover:bg-red-500/10 transition ml-0.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </div>
                    </div>

                    <!-- Collapsed Preview text -->
                    <div class="px-3 py-2 text-xs text-gray-600 truncate" x-show="activeIndex !== index" x-text="getSlidePreview(slide)"></div>

                    <!-- Inline Form when active -->
                    <div class="px-3 pt-3 pb-4 space-y-2.5" x-show="activeIndex === index">
                        @include('ppt._partials.sermon_slide_form')
                    </div>
                </div>
            </template>
            <div class="h-12"></div>
        </div>
    </div>

    <!-- ── Right: WYSIWYG Preview ── -->
    <div class="flex-1 flex flex-col preview-bg relative overflow-hidden">
        
        <!-- View Mode Controls (floating pill) -->
        <div class="absolute top-5 inset-x-0 flex justify-center z-20 pointer-events-none">
            <div class="pointer-events-auto flex gap-1 bg-[#0d1117]/90 backdrop-blur-xl border border-white/10 shadow-2xl rounded-2xl p-1.5">
                <button @click="previewMode = 'full'"
                    class="px-5 py-2 rounded-xl text-sm font-semibold transition-all flex items-center gap-2"
                    :class="previewMode === 'full'
                        ? 'bg-indigo-600 text-white shadow-[0_0_20px_rgba(99,102,241,0.4)]'
                        : 'text-gray-500 hover:text-gray-200 hover:bg-white/5'">
                    🖥️ Toàn Trang
                </button>
                <button @click="previewMode = 'live'"
                    class="px-5 py-2 rounded-xl text-sm font-semibold transition-all flex items-center gap-2"
                    :class="previewMode === 'live'
                        ? 'bg-teal-600 text-white shadow-[0_0_20px_rgba(20,184,166,0.4)]'
                        : 'text-gray-500 hover:text-gray-200 hover:bg-white/5'">
                    📡 Livestream
                </button>
            </div>
        </div>

        <!-- Lower-Third Controls (only in live mode) -->
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 transition-all" x-show="previewMode === 'live'" x-transition>
            <div class="bg-[#0d1117]/90 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl px-5 py-3 flex items-center gap-5">
                <div class="w-52">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">Tỷ lệ Banner</span>
                        <span class="text-teal-400 text-xs font-black bg-teal-500/10 px-2 py-0.5 rounded" x-text="liveH + '%'"></span>
                    </div>
                    <input type="range" min="15" max="42" x-model="liveH" class="w-full h-1.5 bg-gray-800 rounded-full accent-teal-500">
                </div>
                <div class="h-8 w-px bg-white/10"></div>
                <div class="flex gap-2">
                    <button @click="liveH = 20" class="text-[11px] font-bold px-3 py-1.5 rounded-lg border border-gray-700 text-gray-400 hover:text-white hover:bg-white/5 transition">20%</button>
                    <button @click="liveH = 26" class="text-[11px] font-bold px-3 py-1.5 rounded-lg border border-teal-600/50 text-teal-400 bg-teal-600/10 hover:bg-teal-600/20 transition">26% ✓</button>
                    <button @click="liveH = 35" class="text-[11px] font-bold px-3 py-1.5 rounded-lg border border-gray-700 text-gray-400 hover:text-white hover:bg-white/5 transition">35%</button>
                </div>
            </div>
        </div>

        <!-- Canvas Container -->
        <div class="flex-1 flex items-center justify-center px-12 pt-24 pb-24 overflow-hidden">
            <template x-if="slides.length === 0">
                <div class="text-center select-none pointer-events-none">
                    <div class="text-7xl opacity-10 mb-6">🎬</div>
                    <p class="text-gray-600 font-semibold">Soạn kịch bản để xem preview</p>
                </div>
            </template>

            <template x-if="slides.length > 0 && activeSlide">
                <div class="slide-canvas shrink-0"
                     :class="previewMode === 'live' ? 'bg-[#00FF00]' : 'bg-slate-950'">
                     
                    <!-- Full-mode Background -->
                    <template x-if="previewMode === 'full'">
                        <div class="absolute inset-0 z-0">
                            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-[#0a0e1a] to-black"></div>
                            <div class="absolute inset-0 opacity-[0.04]" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22><rect width=%22100%22 height=%22100%22 fill=%22none%22 stroke=%22white%22 stroke-width=%220.5%22 opacity=%220.5%22/></svg>');background-size:60px 60px;"></div>
                            <div class="absolute bottom-0 inset-x-0 h-2/3 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                        </div>
                    </template>

                    <!-- The lower-third band (livem ode) OR full canvas (full mode) -->
                    <div class="absolute inset-x-0 bottom-0 z-10 transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]"
                         :style="previewMode === 'live'
                            ? `height:${liveH}%; background:rgba(8,10,16,0.96); border-top:3px solid #D4A017; backdrop-filter:blur(12px);`
                            : 'height:100%; background:transparent;'">
                        
                        <!-- Logo badge (live) -->
                        <div x-show="previewMode === 'live'" class="absolute right-4 bottom-3 flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full border-2 border-yellow-500 bg-black/60 flex items-center justify-center flex-col text-yellow-500 font-black text-[6px] leading-tight shadow-[0_0_12px_rgba(212,160,23,0.4)]">
                                <span>THÁNH</span><span>MỸ LỢI</span>
                            </div>
                        </div>

                        <!-- Content Renderer -->
                        <div class="h-full flex flex-col justify-center overflow-hidden"
                             :class="previewMode === 'full' ? 'px-16 py-12 text-center items-center pb-16' : 'px-7 py-4 pr-20'">
                            @include('ppt._partials.sermon_preview')
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<!-- ══════════ TEMPLATE MODAL ══════════ -->
<div x-show="showTemplateModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm" style="display: none;" x-transition>
    <div class="bg-[#0f1520] border border-white/10 rounded-2xl w-full max-w-6xl h-[85vh] flex flex-col shadow-2xl overflow-hidden" @click.away="showTemplateModal = false">
        <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center bg-black/40">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">🎨 Quản Lý Master Template</h3>
                <p class="text-[11px] text-gray-400 mt-1">Dùng giao diện này để "dạy" hệ thống tự đổ chữ vào đúng cái Layout cực đẹp mà bạn đã tải về từ Canva/PowerPoint</p>
            </div>
            <button @click="showTemplateModal = false" class="text-gray-500 hover:text-white p-2 rounded-lg hover:bg-white/5 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6 scrollbar-thin">
            <!-- Upload section -->
            <div class="mb-8 p-8 border-2 border-dashed border-gray-700 rounded-xl text-center bg-white/5 transition hover:bg-white/10 hover:border-indigo-500/50 group relative">
                <input type="file" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" accept=".pptx" @change="uploadTemplate">
                <div class="pointer-events-none">
                    <p class="text-sm font-bold text-gray-300 mb-2">📥 Kéo thả file .PPTX vào đây hoặc click để tải lên</p>
                    <p class="text-xs text-gray-500 mb-4">Hệ thống sẽ dùng Python đọc thẳng vào cấu trúc của file để trích xuất ra các Box (Placeholder).</p>
                    <button class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-semibold text-sm shadow-lg pointer-events-none" :class="isAnalyzingTmpl ? 'animate-pulse opacity-80' : ''">
                        <span x-text="isAnalyzingTmpl ? '⏳ Đang phân tích file vất vả lắm...' : 'Tải lên Template Của Bạn'"></span>
                    </button>
                </div>
            </div>
            
            <template x-if="extractedLayouts.length > 0">
                <div class="animate-[fade-in_0.3s_ease-out]">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-md font-bold text-white">Danh sách Layout Layouts & Mapping</h4>
                        <span class="text-xs font-medium text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded" x-text="`Đã tìm thấy ${extractedLayouts.length} layout`"></span>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                        <template x-for="l in extractedLayouts" :key="l.index">
                            <div class="bg-black/60 border rounded-xl p-4 flex flex-col gap-3 transition-all"
                                 :class="l.mapped_to ? 'border-emerald-500/50 shadow-[0_0_15px_rgba(16,185,129,0.1)]' : 'border-white/5'">
                                <div class="flex justify-between items-start gap-2">
                                    <span class="text-xs font-bold truncate flex-1" :class="l.mapped_to ? 'text-emerald-400' : 'text-gray-300'" x-text="l.name"></span>
                                    <span class="text-[9px] font-mono bg-white/10 px-1.5 py-0.5 rounded text-gray-500 shrink-0" x-text="'Idx:' + l.index"></span>
                                </div>
                                
                                <!-- Mini Wireframe renderer -->
                                <div class="w-full aspect-video bg-gray-900 rounded-lg relative border overflow-hidden shadow-inner shrink-0"
                                     :class="l.mapped_to ? 'border-emerald-500/30' : 'border-gray-800'">
                                    <template x-for="p in l.placeholders" :key="p.idx">
                                        <div class="absolute border flex items-center p-1 overflow-hidden"
                                             :class="l.mapped_to ? 'border-emerald-500/50 bg-emerald-500/10' : 'border-indigo-500/40 bg-indigo-500/10'"
                                             :style="`left:${p.x}%; top:${p.y}%; width:${p.w}%; height:${p.h}%`">
                                             <span class="text-[7px] font-black truncate w-full"
                                                   :class="l.mapped_to ? 'text-emerald-400/80' : 'text-indigo-300/60'"
                                                   x-text="p.type"></span>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Mapping Select -->
                                <div class="mt-auto">
                                    <label class="text-[10px] text-gray-500 font-bold uppercase mb-1 block">Tự động xài cho thẻ:</label>
                                    <select x-model="l.mapped_to" @change="updateMapping()" class="w-full bg-[#1e293b] text-sm text-gray-200 border border-slate-700 rounded-md py-1.5 px-2 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition cursor-pointer">
                                        <option value="">(Bỏ qua / Không dùng)</option>
                                        <optgroup label="Cấu trúc">
                                            <option value="title">Tựa Đề (Title)</option>
                                            <option value="section_break">Đầu Đề Phần</option>
                                            <option value="conclusion">Kết Luận</option>
                                        </optgroup>
                                        <optgroup label="Nội dung">
                                            <option value="verse">Câu Gốc (Verse)</option>
                                            <option value="main_point">Ý Chính</option>
                                            <option value="content">Văn Bản</option>
                                            <option value="list">Danh Sách</option>
                                            <option value="comparison">So Sánh</option>
                                            <option value="origin">Nguyên Ngữ</option>
                                        </optgroup>
                                        <optgroup label="Khác">
                                            <option value="prayer">Cầu Nguyện</option>
                                            <option value="illustration">Minh Hoạ</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
        <div class="px-6 py-4 border-t border-white/10 bg-[#0C1117] flex justify-end gap-3 items-center">
            <span class="text-xs text-gray-500 mr-auto" x-show="Object.keys(templateMapping).length > 0">
                Đã map <strong class="text-white" x-text="Object.keys(templateMapping).length"></strong> loại thẻ. Template đã sẵn sàng!
            </span>
            <button @click="showTemplateModal = false" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-8 rounded-xl shadow-lg shadow-emerald-900/50 transition">Lưu Cấu Hình</button>
        </div>
    </div>
</div>

<script>
function sermonEditor() {
    return {
        slides: [],
        activeIndex: 0,
        previewMode: 'live',
        liveH: 26,
        
        // Template Mapping States
        showTemplateModal: false,
        isAnalyzingTmpl: false,
        uploadedTemplatePath: null,
        extractedLayouts: [],
        templateMapping: {},
        analyzeEndpoint: "{{ url('/api/ppt/sermon/analyze-template') }}",

        typeGroups: [
            { label: '🏛️ Cấu Trúc Bài Giảng', types: [
                { id:'title',       label:'Tựa Đề',         color:'c-indigo' },
                { id:'section_break', label:'Đầu Đề Phần',  color:'c-amber'  },
                { id:'conclusion',  label:'Kết Luận',        color:'c-red'    },
            ]},
            { label: '📖 Kinh Thánh', types: [
                { id:'verse',       label:'Câu Gốc',         color:'c-emerald' },
                { id:'memory_verse',label:'Câu Ghi Nhớ',     color:'c-purple'  },
            ]},
            { label: '💡 Nội Dung Chính', types: [
                { id:'main_point',  label:'Ý Chính',         color:'c-gold'    },
                { id:'content',     label:'Văn Bản',         color:'c-gray'    },
                { id:'list',        label:'Danh Sách',       color:'c-amber'   },
                { id:'comparison',  label:'So Sánh',         color:'c-blue'    },
                { id:'timeline',    label:'Dòng Thời Gian',  color:'c-cyan'    },
            ]},
            { label: '🗣️ Nguyên Ngữ & Định Nghĩa', types: [
                { id:'origin',      label:'Nguyên Ngữ',      color:'c-rose'    },
                { id:'definition',  label:'Định Nghĩa',      color:'c-cyan'    },
                { id:'context',     label:'Bối Cảnh',        color:'c-amber'   },
            ]},
            { label: '🎙️ Minh Hoạ & Ứng Dụng', types: [
                { id:'illustration', label:'Minh Hoạ',       color:'c-orange'  },
                { id:'testimony',   label:'Nhân Chứng',      color:'c-orange'  },
                { id:'application', label:'Áp Dụng',         color:'c-green'   },
                { id:'question',    label:'Câu Hỏi',         color:'c-teal'    },
                { id:'quote',       label:'Trích Dẫn',       color:'c-purple'  },
                { id:'reflection',  label:'Suy Ngẫm',        color:'c-blue'    },
                { id:'image',       label:'Hình Ảnh',        color:'c-gray'    },
                { id:'map',         label:'Bản Đồ',          color:'c-teal'    },
            ]},
            { label: '🙏 Phụng Vụ & Thờ Phượng', types: [
                { id:'prayer',      label:'Cầu Nguyện',      color:'c-indigo'  },
                { id:'invitation',  label:'Mời Gọi',         color:'c-red'     },
                { id:'song',        label:'Bài Hát',         color:'c-teal'    },
                { id:'announcement',label:'Thông Báo',       color:'c-purple'  },
                { id:'blank',       label:'Slide Trắng',     color:'c-gray'    },
            ]},
        ],

        init() {
            // Demo slides for instant visual feedback
            this.slides = [
                { id:1, type:'title',      data:{ title:'Người Nữ Được Tạo Dựng', speaker:'MS. Quản Nhiệm', date:'21/03/2026', subtitle:'Sáng thế ký 2' } },
                { id:2, type:'verse',      data:{ ref:'Sáng thế ký 2:18', text:'Giê-hô-va Đức Chúa Trời phán: Loài người ở một mình thì không tốt; ta sẽ làm nên một kẻ giúp đỡ giống như nó.', translation:'Kinh Thánh 2010' } },
                { id:3, type:'main_point', data:{ number:'I.', text:'NGƯỜI NỮ ĐƯỢC TẠO DỰNG BÌNH ĐẲNG VỚI NGƯỜI NAM' } },
                { id:4, type:'origin',     data:{ word:'GIỐNG NHƯ', phonetic:'ke·neg·dô', lang:'Hebrew (עֵזֶר כְּנֶגְדּוֹ)', meaning:'Một người giúp đỡ tương xứng — đối lập—bổ sung.' } },
                { id:5, type:'list',       data:{ title:'3 Vai Trò Người Nữ', items:'Phản ánh hình ảnh Chúa như người Nam\nLà người bổ khuyết và giúp đỡ tương xứng\nĐồng lãnh đạo, cai trị thụ tạo với người Nam' } },
                { id:6, type:'application',data:{ text:'Hôm nay mỗi người chồng hãy nhìn vào vợ mình như một "ân điển" đến từ Chúa — không phải là sự yếu đuối mà là sức mạnh.', action:'Tuần này nói câu: "Cảm ơn em vì..." với người bạn đời.' } },
                { id:7, type:'prayer',     data:{ text:'Lạy Chúa, xin giúp chúng con nhìn nhau qua con mắt của Ngài — không phân biệt, không khinh thường, nhưng trong sự tôn trọng và yêu thương. Amen.' } },
            ];
            this.activeIndex = 2;
        },

        get activeSlide() {
            return this.slides[this.activeIndex] ?? null;
        },

        getTypeById(id) {
            for (const g of this.typeGroups)
                for (const t of g.types)
                    if (t.id === id) return t;
            return null;
        },

        getSlidePreview(slide) {
            const d = slide.data || {};
            switch (slide.type) {
                case 'title':       return d.title || '(Chưa có tiêu đề)';
                case 'verse':       return `${d.ref || ''} — ${d.text?.substring(0,60) || ''}...`;
                case 'main_point':  return `${d.number || ''} ${d.text || ''}`.trim();
                case 'origin':      return `"${d.word || ''}" — ${d.meaning?.substring(0,50) || ''}`;
                case 'list':        return d.title || d.items?.split('\n')[0] || '';
                case 'content':     return d.text?.substring(0,70) || '';
                case 'comparison':  return d.title || 'So sánh...';
                case 'quote':       return `"${d.text?.substring(0,60) || ''}..." — ${d.author || ''}`;
                case 'question':    return d.text?.substring(0,70) || '';
                case 'illustration':return d.title || d.text?.substring(0,60) || '';
                case 'prayer':      return d.text?.substring(0,70) || '';
                case 'invitation':  return d.text?.substring(0,70) || '';
                case 'application': return d.text?.substring(0,70) || '';
                case 'conclusion':  return d.text?.substring(0,70) || '';
                case 'definition':  return `${d.term || ''} — ${d.definition?.substring(0,50) || ''}`;
                case 'section_break': return `${d.label || ''} — ${d.subtitle || ''}`;
                case 'song':        return d.name || '(Bài hát)';
                default:            return JSON.stringify(slide.data).substring(0,60);
            }
        },

        addSlide(typeId) {
            this.slides.push({ id: Date.now(), type: typeId, data: {} });
            this.activeIndex = this.slides.length - 1;
        },

        removeSlide(index) {
            this.slides.splice(index, 1);
            this.activeIndex = Math.max(0, Math.min(this.activeIndex, this.slides.length - 1));
        },
        
        // ── Template Mapping Methods ──
        _originalSuggested: {},

        async uploadTemplate(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.isAnalyzingTmpl = true;
            
            const fd = new FormData();
            fd.append('file', file);
            
            try {
                const res = await fetch(this.analyzeEndpoint, { method: 'POST', body: fd, headers: { 'Accept': 'application/json' }});
                const data = await res.json();
                
                if (data.status === 'success') {
                    this.uploadedTemplatePath = data.template_file;
                    this._originalSuggested = data.suggested_mapping || {};
                    
                    this.extractedLayouts = data.layouts.map(l => {
                        let mappedType = '';
                        // Nhận diện Heuristic từ Python
                        for (const [key, mapping] of Object.entries(this._originalSuggested)) {
                            if (mapping.layout_index === l.index) {
                                mappedType = key;
                            }
                        }
                        return { ...l, mapped_to: mappedType };
                    });
                    this.updateMapping();
                } else {
                    alert('Lỗi phân tích Template:\n' + (data.message || '') + '\n' + (data.raw?.substring(0, 500) || ''));
                }
            } catch (err) {
                alert('Lỗi upload mạng: ' + err.message);
            } finally {
                this.isAnalyzingTmpl = false;
                e.target.value = null; // reset input
            }
        },
        
        updateMapping() {
            this.templateMapping = {};
            for (const l of this.extractedLayouts) {
                if (l.mapped_to) {
                    const original = this._originalSuggested[l.mapped_to];
                    // Chỉ dùng placeholder_map siêu chính xác nếu user không tự đổi Layout bằng tay
                    const phMap = (original && original.layout_index === l.index) ? original.placeholder_map : {};
                    
                    this.templateMapping[l.mapped_to] = { 
                        layout_index: l.index,
                        placeholder_map: phMap
                    };
                }
            }
        },

        duplicateSlide(index) {
            const clone = JSON.parse(JSON.stringify(this.slides[index]));
            clone.id = Date.now();
            this.slides.splice(index + 1, 0, clone);
            this.activeIndex = index + 1;
        },

        moveUp(index) {
            if (index > 0) {
                [this.slides[index-1], this.slides[index]] = [this.slides[index], this.slides[index-1]];
                if (this.activeIndex === index) this.activeIndex--;
                else if (this.activeIndex === index - 1) this.activeIndex++;
            }
        },

        moveDown(index) {
            if (index < this.slides.length - 1) {
                [this.slides[index], this.slides[index+1]] = [this.slides[index+1], this.slides[index]];
                if (this.activeIndex === index) this.activeIndex++;
                else if (this.activeIndex === index + 1) this.activeIndex--;
            }
        },

        isExporting: false,
        exportStatus: '',

        async generatePpt() {
            if (!this.slides.length) return alert('Chưa có kịch bản để xuất!');
            this.isExporting = true;
            this.exportStatus = 'Đang tạo file PPTX...';
            try {
                const payloadStr = JSON.stringify({
                    mode: 'both',
                    banner_ratio: this.liveH / 100,
                    slides: this.slides,
                    template_file: this.uploadedTemplatePath,
                    template_mapping: this.templateMapping
                });
                console.log("SENDING:", payloadStr);
                
                const resp = await fetch(window.SERMON_API.generate, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: payloadStr
                });
                const data = await resp.json();
                if (data.status === 'success') {
                    this.exportStatus = '✅ Hoàn thành!';
                    // Auto-trigger downloads for both files
                    if (data.urls?.live) {
                        const a = document.createElement('a');
                        a.href = data.urls.live; a.download = 'Sermon_Live.pptx';
                        document.body.appendChild(a); a.click(); a.remove();
                    }
                    if (data.urls?.full) {
                        await new Promise(r => setTimeout(r, 800));
                        const a = document.createElement('a');
                        a.href = data.urls.full; a.download = 'Sermon_Full.pptx';
                        document.body.appendChild(a); a.click(); a.remove();
                    }
                } else {
                    this.exportStatus = '❌ Lỗi!';
                    alert('Lỗi xuất file:\n' + (data.message || 'Không rõ') + '\n\n' + (data.raw || ''));
                }
            } catch(e) {
                this.exportStatus = '❌ ' + e.message;
                alert('Lỗi kết nối: ' + e.message);
            }
            setTimeout(() => { this.isExporting = false; this.exportStatus = ''; }, 4000);
        }
    };
}
</script>
</body>
</html>
