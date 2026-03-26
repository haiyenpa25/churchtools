{{-- WYSIWYG Preview Content — rendered inside the 16:9 canvas --}}
<template x-if="activeSlide">
<div class="w-full h-full flex flex-col justify-center" x-cloak>

    {{-- ── TITLE ── --}}
    <template x-if="activeSlide.type === 'title'">
        <div :class="previewMode==='full' ? 'flex flex-col items-center gap-3' : 'flex items-start gap-4'">
            <template x-if="previewMode==='full'">
                <div class="text-center">
                    <h1 class="font-['Playfair_Display'] font-black leading-[1.05] tracking-tight text-transparent bg-clip-text bg-gradient-to-b from-yellow-300 to-amber-600 drop-shadow-2xl text-[60px] mb-5" x-text="activeSlide.data.title || 'Tựa Đề Bài Giảng'"></h1>
                    <p class="text-xl uppercase tracking-[0.25em] text-gray-300 font-semibold" x-text="activeSlide.data.speaker || 'Diễn Giả'"></p>
                    <p class="text-sm text-gray-500 mt-2 font-medium" x-text="activeSlide.data.date || ''"></p>
                </div>
            </template>
            <template x-if="previewMode==='live'">
                <div>
                    <p class="text-[11px] text-yellow-500/70 uppercase tracking-widest font-bold mb-1" x-text="activeSlide.data.subtitle || 'Tựa Đề Bài Giảng'"></p>
                    <h1 class="font-['Playfair_Display'] font-black text-yellow-500 leading-tight text-[36px] line-clamp-2" x-text="activeSlide.data.title || 'Tựa Đề Bài Giảng'"></h1>
                    <p class="text-sm text-gray-300 mt-1 uppercase tracking-widest font-semibold" x-text="activeSlide.data.speaker || ''"></p>
                </div>
            </template>
        </div>
    </template>

    {{-- ── VERSE ── --}}
    <template x-if="activeSlide.type === 'verse'">
        <div>
            <template x-if="previewMode==='full'">
                <div class="flex flex-col items-center gap-5 max-w-3xl">
                    <div class="flex items-center gap-3">
                        <div class="h-px w-12 bg-emerald-500/50"></div>
                        <span class="text-2xl font-black text-emerald-400 uppercase tracking-widest" x-text="activeSlide.data.ref || 'KINH THÁNH'"></span>
                        <div class="h-px w-12 bg-emerald-500/50"></div>
                    </div>
                    <p class="font-['Playfair_Display'] text-[46px] text-white italic font-semibold text-center leading-[1.2]" x-text="`\u201c${activeSlide.data.text || 'Nội dung Lời Chúa'}\u201d`"></p>
                </div>
            </template>
            <template x-if="previewMode==='live'">
                <div>
                    <p class="text-emerald-400 font-black text-sm uppercase tracking-widest mb-1.5 border-l-4 border-emerald-500 pl-3" x-text="activeSlide.data.ref || 'KINH THÁNH'"></p>
                    <p class="font-['Playfair_Display'] text-white text-[24px] italic leading-snug line-clamp-3" x-text="`\u201c${activeSlide.data.text || 'Nội dung câu gốc'}\u201d`"></p>
                </div>
            </template>
        </div>
    </template>

    {{-- ── MAIN POINT ── --}}
    <template x-if="activeSlide.type === 'main_point'">
        <div>
            <template x-if="previewMode==='full'">
                <div>
                    <template x-if="activeSlide.data.number">
                        <div class="text-3xl font-black text-yellow-500/50 mb-2 uppercase tracking-widest" x-text="activeSlide.data.number"></div>
                    </template>
                    <h1 class="font-['Playfair_Display'] font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-amber-400 to-yellow-500 text-[80px] uppercase leading-[1.0] drop-shadow-2xl" x-text="activeSlide.data.text || 'Ý CHÍNH TRỌNG TÂM'"></h1>
                </div>
            </template>
            <template x-if="previewMode==='live'">
                <div class="flex items-baseline gap-3">
                    <span x-show="activeSlide.data.number" class="text-yellow-500/60 font-black text-xl shrink-0" x-text="activeSlide.data.number"></span>
                    <h1 class="font-black text-yellow-500 uppercase leading-[1.1] text-[46px] line-clamp-2" x-text="activeSlide.data.text || 'Ý CHÍNH TRỌNG TÂM'"></h1>
                </div>
            </template>
        </div>
    </template>

    {{-- ── CONTENT ── --}}
    <template x-if="activeSlide.type === 'content'">
        <div :class="previewMode==='full' ? 'max-w-4xl' : ''">
            <template x-if="activeSlide.data.title">
                <p class="font-black text-yellow-500/80 uppercase tracking-widest mb-3" :class="previewMode==='full' ? 'text-2xl' : 'text-sm mb-1.5'"><span x-text="activeSlide.data.title"></span></p>
            </template>
            <p class="text-white font-semibold leading-relaxed" :class="previewMode==='full' ? 'text-[42px]' : 'text-[22px] line-clamp-3'" x-text="activeSlide.data.text || 'Nội dung diễn giải chi tiết của bài giảng'"></p>
        </div>
    </template>

    {{-- ── ORIGIN ── --}}
    <template x-if="activeSlide.type === 'origin'">
        <div>
            <template x-if="previewMode==='full'">
                <div class="flex flex-col items-center gap-6 max-w-3xl">
                    <div class="flex flex-wrap items-baseline justify-center gap-4">
                        <span class="font-black text-yellow-500 text-[55px] uppercase leading-none" x-text="`NGUYÊN NGỮ: \u2018${activeSlide.data.word || 'TỪ GỐC'}\u2019`"></span>
                        <span class="font-mono text-gray-400 text-3xl italic" x-text="activeSlide.data.phonetic ? `• ${activeSlide.data.phonetic}` : ''"></span>
                    </div>
                    <div class="flex items-start gap-4 text-white font-['Playfair_Display'] font-semibold text-[46px] text-center leading-tight">
                        <span class="text-yellow-500 shrink-0">→</span>
                        <span x-text="activeSlide.data.meaning || 'Định nghĩa hoặc giải nghĩa'"></span>
                    </div>
                </div>
            </template>
            <template x-if="previewMode==='live'">
                <div>
                    <div class="flex items-baseline gap-3 mb-2">
                        <span class="font-black text-yellow-500 text-[26px] uppercase leading-none" x-text="`\u2018${activeSlide.data.word || 'TỪ GỐC'}\u2019`"></span>
                        <span class="font-mono text-gray-400 text-sm italic" x-text="activeSlide.data.phonetic ? `• ${activeSlide.data.phonetic}` : ''"></span>
                    </div>
                    <div class="flex items-start gap-2 text-white font-semibold text-[22px] leading-snug line-clamp-2">
                        <span class="text-yellow-500 shrink-0">→</span>
                        <span x-text="activeSlide.data.meaning || 'Định nghĩa'"></span>
                    </div>
                </div>
            </template>
        </div>
    </template>

    {{-- ── LIST ── --}}
    <template x-if="activeSlide.type === 'list'">
        <div :class="previewMode==='full' ? 'max-w-3xl w-full' : 'w-full'">
            <p class="font-black text-yellow-500 uppercase leading-none border-l-4 border-yellow-500 pl-4 mb-4"
               :class="previewMode==='full' ? 'text-[42px] mb-6' : 'text-[22px] mb-2'"
               x-text="activeSlide.data.title || 'DANH SÁCH'"></p>
            <div :class="previewMode==='full' ? 'space-y-4 pl-6' : 'space-y-1.5 pl-4'">
                <template x-for="(item, i) in (activeSlide.data.items || 'Mục 1\nMục 2').split('\n').filter(v=>v.trim()).slice(0, previewMode==='live' ? 3 : 999)">
                    <div class="flex items-center gap-3">
                        <span class="text-yellow-500 font-black shrink-0" :class="previewMode==='full' ? 'text-3xl' : 'text-lg'">•</span>
                        <span class="text-white font-semibold leading-tight" :class="previewMode==='full' ? 'text-[38px]' : 'text-[18px] line-clamp-1'" x-text="item.replace(/^[-•]\s*/,'')"></span>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- ── COMPARISON ── --}}
    <template x-if="activeSlide.type === 'comparison'">
        <div class="w-full">
            <template x-if="previewMode==='full'">
                <div class="flex flex-col gap-4 w-full max-w-4xl">
                    <p class="text-2xl font-black text-gray-400 uppercase tracking-widest text-center" x-text="activeSlide.data.title || 'SO SÁNH'"></p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-6">
                            <p class="text-sm font-black text-blue-400 uppercase tracking-widest mb-3">Vế A</p>
                            <p class="text-white font-semibold text-3xl leading-snug" x-text="activeSlide.data.left || 'Cột A'"></p>
                        </div>
                        <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-6">
                            <p class="text-sm font-black text-amber-400 uppercase tracking-widest mb-3">Vế B</p>
                            <p class="text-white font-semibold text-3xl leading-snug" x-text="activeSlide.data.right || 'Cột B'"></p>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="previewMode==='live'">
                <div>
                    <p class="text-yellow-500 font-black text-[18px] uppercase mb-1" x-text="activeSlide.data.title || 'SO SÁNH'"></p>
                    <div class="flex gap-4">
                        <p class="text-blue-200 text-[16px] flex-1 line-clamp-2" x-text="activeSlide.data.left || 'Cột A'"></p>
                        <span class="text-gray-500 text-xl font-bold">↔</span>
                        <p class="text-amber-200 text-[16px] flex-1 line-clamp-2" x-text="activeSlide.data.right || 'Cột B'"></p>
                    </div>
                </div>
            </template>
        </div>
    </template>

    {{-- ── QUOTE ── --}}
    <template x-if="activeSlide.type === 'quote'">
        <div :class="previewMode==='full' ? 'max-w-3xl' : ''">
            <div class="text-gray-400 font-['Playfair_Display'] font-black select-none" :class="previewMode==='full' ? 'text-[120px] leading-none mb-2 -ml-6 opacity-20' : 'text-[50px] leading-none -mb-2 opacity-20'">"</div>
            <p class="text-white font-['Playfair_Display'] italic leading-snug" :class="previewMode==='full' ? 'text-[50px]' : 'text-[22px] line-clamp-2'" x-text="activeSlide.data.text || 'Câu trích dẫn...'"></p>
            <p class="text-gray-500 uppercase tracking-widest font-bold mt-3" :class="previewMode==='full' ? 'text-xl' : 'text-xs'" x-text="activeSlide.data.author ? `— ${activeSlide.data.author}` : ''"></p>
        </div>
    </template>

    {{-- ── QUESTION ── --}}
    <template x-if="activeSlide.type === 'question'">
        <div :class="previewMode==='full' ? 'max-w-3xl flex flex-col items-center gap-4' : ''">
            <p class="text-teal-400 font-black text-sm uppercase tracking-widest mb-2" x-show="previewMode==='full'">❓ Suy Ngẫm</p>
            <p class="text-white font-['Playfair_Display'] font-bold leading-snug" :class="previewMode==='full' ? 'text-[52px] text-center' : 'text-[26px] line-clamp-3'" x-text="activeSlide.data.text || 'Câu hỏi suy ngẫm...'"></p>
        </div>
    </template>

    {{-- ── APPLICATION ── --}}
    <template x-if="activeSlide.type === 'application'">
        <div :class="previewMode==='full' ? 'max-w-4xl' : 'w-full'">
            <p class="text-green-400 font-black uppercase tracking-widest mb-2" :class="previewMode==='full' ? 'text-2xl mb-4' : 'text-sm mb-1'">✅ Áp Dụng Cá Nhân</p>
            <p class="text-white/90 font-semibold leading-relaxed" :class="previewMode==='full' ? 'text-[40px]' : 'text-[21px] line-clamp-2'" x-text="activeSlide.data.text || 'Ứng dụng thực tiễn...'"></p>
            <template x-if="activeSlide.data.action">
                <p class="text-green-300 font-bold italic" :class="previewMode==='full' ? 'text-2xl mt-6' : 'text-sm mt-1 line-clamp-1'" x-text="`→ ${activeSlide.data.action}`"></p>
            </template>
        </div>
    </template>

    {{-- ── CONCLUSION ── --}}
    <template x-if="activeSlide.type === 'conclusion'">
        <div :class="previewMode==='full' ? 'max-w-3xl flex flex-col items-center gap-5' : 'w-full'">
            <p x-show="previewMode==='full'" class="text-2xl font-black text-gray-500 uppercase tracking-widest">Kết Lại</p>
            <p class="text-white font-['Playfair_Display'] italic leading-snug" :class="previewMode==='full' ? 'text-[48px] text-center' : 'text-[23px] line-clamp-3'" x-text="activeSlide.data.text || 'Lời kết bài giảng...'"></p>
        </div>
    </template>

    {{-- ── PRAYER ── --}}
    <template x-if="activeSlide.type === 'prayer'">
        <div :class="previewMode==='full' ? 'max-w-3xl flex flex-col items-center gap-4' : 'w-full'">
            <p class="text-indigo-400 font-black text-sm uppercase tracking-widest" x-show="previewMode==='full'">🙏 Cầu Nguyện</p>
            <p class="text-indigo-100 font-['Playfair_Display'] italic leading-snug text-center" :class="previewMode==='full' ? 'text-[44px]' : 'text-[22px] line-clamp-3'" x-text="activeSlide.data.text || 'Lạy Chúa...'"></p>
        </div>
    </template>

    {{-- ── INVITATION ── --}}
    <template x-if="activeSlide.type === 'invitation'">
        <div :class="previewMode==='full' ? 'max-w-3xl flex flex-col items-center gap-5' : 'w-full'">
            <p x-show="previewMode==='full'" class="text-xl font-black text-red-500 uppercase tracking-widest">✝ Lời Mời Gọi</p>
            <p class="text-white font-black" :class="previewMode==='full' ? 'text-[54px] text-center leading-tight' : 'text-[26px] line-clamp-2'" x-text="activeSlide.data.text || 'Bạn có muốn tiếp nhận Chúa hôm nay?'"></p>
            <template x-if="activeSlide.data.guide">
                <p class="text-red-300/70 italic" :class="previewMode==='full' ? 'text-2xl' : 'text-sm line-clamp-1'" x-text="activeSlide.data.guide"></p>
            </template>
        </div>
    </template>

    {{-- ── SECTION BREAK ── --}}
    <template x-if="activeSlide.type === 'section_break'">
        <div :class="previewMode==='full' ? 'flex flex-col items-center gap-3 w-full' : 'w-full'">
            <div class="flex items-center gap-4 w-full" :class="previewMode==='full' ? 'justify-center mb-4' : 'mb-1'">
                <div class="flex-1 h-px bg-gradient-to-r from-transparent to-yellow-500/50"></div>
                <p class="font-black text-yellow-500 uppercase" :class="previewMode==='full' ? 'text-[52px]' : 'text-[28px]'" x-text="activeSlide.data.label || 'PHẦN'"></p>
                <div class="flex-1 h-px bg-gradient-to-l from-transparent to-yellow-500/50"></div>
            </div>
            <p class="text-gray-400 font-semibold" :class="previewMode==='full' ? 'text-2xl uppercase tracking-widest' : 'text-sm'" x-text="activeSlide.data.subtitle || ''"></p>
        </div>
    </template>

    {{-- ── DEFINITION ── --}}
    <template x-if="activeSlide.type === 'definition'">
        <div :class="previewMode==='full' ? 'max-w-4xl' : 'w-full'">
            <p class="font-black text-cyan-400 uppercase" :class="previewMode==='full' ? 'text-[60px] mb-4' : 'text-[28px] mb-1'" x-text="activeSlide.data.term || 'THUẬT NGỮ'"></p>
            <p class="text-white font-semibold leading-snug" :class="previewMode==='full' ? 'text-[40px]' : 'text-[20px] line-clamp-2'" x-text="activeSlide.data.definition || 'Định nghĩa hoặc giải thích thuật ngữ này'"></p>
        </div>
    </template>

    {{-- ── SONG ── --}}
    <template x-if="activeSlide.type === 'song'">
        <div :class="previewMode==='full' ? 'flex flex-col items-center gap-4' : 'w-full flex items-center gap-4'">
            <span :class="previewMode==='full' ? 'text-[60px]' : 'text-3xl'">🎵</span>
            <p class="font-bold text-teal-300" :class="previewMode==='full' ? 'text-[48px]' : 'text-[22px]'" x-text="activeSlide.data.name || 'Bài Hát'"></p>
        </div>
    </template>

    {{-- ── MEMORY VERSE ── --}}
    <template x-if="activeSlide.type === 'memory_verse'">
        <div :class="previewMode==='full' ? 'max-w-3xl flex flex-col items-center gap-5' : 'w-full'">
            <p x-show="previewMode==='full'" class="text-xl font-black text-purple-400 uppercase tracking-widest">📝 Câu Ghi Nhớ Trong Tuần</p>
            <p class="text-purple-100 font-['Playfair_Display'] font-bold italic leading-snug" :class="previewMode==='full' ? 'text-[50px] text-center' : 'text-[24px] line-clamp-2'" x-text="activeSlide.data.text || 'Câu Kinh Thánh cần ghi nhớ...'"></p>
            <p class="text-purple-400 font-black uppercase tracking-widest" :class="previewMode==='full' ? 'text-2xl' : 'text-sm'" x-text="activeSlide.data.ref || ''"></p>
        </div>
    </template>

    {{-- ── ILLUSTRATION ── --}}
    <template x-if="activeSlide.type === 'illustration'">
        <div :class="previewMode==='full' ? 'max-w-4xl' : 'w-full'">
            <p class="font-black text-amber-400 uppercase tracking-widest" :class="previewMode==='full' ? 'text-2xl mb-3' : 'text-xs mb-1'">📖 Câu Chuyện Minh Hoạ</p>
            <p class="font-bold text-gray-200" :class="previewMode==='full' ? 'text-[34px] mb-5' : 'text-[20px] mb-1'" x-text="activeSlide.data.title || ''"></p>
            <p class="text-gray-300 leading-relaxed" :class="previewMode==='full' ? 'text-[32px]' : 'text-[18px] line-clamp-2'" x-text="activeSlide.data.text || 'Nội dung câu chuyện minh hoạ...'"></p>
        </div>
    </template>

    {{-- ── BLANK ── --}}
    <template x-if="activeSlide.type === 'blank'">
        <div class="w-full flex items-center justify-center opacity-20">
            <span :class="previewMode==='full' ? 'text-8xl' : 'text-4xl'">[ BLANK ]</span>
        </div>
    </template>

    {{-- ── FALLBACK ── --}}
    <template x-if="!['title','verse','main_point','content','origin','list','comparison','quote','question','application','conclusion','prayer','invitation','section_break','definition','song','memory_verse','illustration','blank','context','timeline','image','reflection','testimony','map','announcement'].includes(activeSlide.type)">
        <div class="text-gray-600 italic" x-text="`Loại slide: ${activeSlide.type} — Sắp có preview`"></div>
    </template>

</div>
</template>
