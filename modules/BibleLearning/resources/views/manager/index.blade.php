<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản Lý Kinh Thánh — ChurchTools (G-A-E-V)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0B0E14; color: #E2E8F0; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
        
        .active-book { background-color: rgba(59, 130, 246, 0.15); border-left: 2px solid #3b82f6; }
        .active-chapter { background-color: rgba(16, 185, 129, 0.15); border-left: 2px solid #10b981; }
    </style>
</head>
<body class="min-h-screen custom-scrollbar flex flex-col" x-data="bibleManager()">

    <!-- Header -->
    <header class="bg-[#111827] border-b border-gray-800 sticky top-0 z-10 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ url('/') }}" class="text-gray-400 hover:text-white transition w-8 h-8 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="text-blue-400">📖</span> Trung Tâm Quản Lý Kinh Thánh
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">Hệ thống tra cứu và chỉnh sửa cấu trúc Data (Sách - Chương - Câu)</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- UI 3 Cột Liên Hoàn -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 h-[80vh]">
            
            <!-- CỘT 1: SÁCH KINH THÁNH -->
            <div class="col-span-1 bg-gray-800/50 border border-gray-700 rounded-2xl overflow-hidden shadow-2xl flex flex-col">
                <div class="p-4 border-b border-gray-700 bg-gray-900/80">
                    <h2 class="font-bold text-gray-300">📚 66 Sách</h2>
                    <input type="text" x-model="searchBook" placeholder="Tìm sách..." class="mt-2 w-full bg-gray-900 border border-gray-700 text-white rounded-lg py-1 px-3 focus:outline-none focus:border-blue-500 text-sm">
                </div>
                <div class="flex-1 overflow-y-auto custom-scrollbar p-2">
                    <template x-if="isLoadingBooks">
                        <div class="text-center py-4 text-xs text-gray-500">Đang tải sách...</div>
                    </template>
                    <template x-for="book in filteredBooks" :key="book.id">
                        <div @click="selectBook(book)" 
                             class="px-3 py-2 cursor-pointer rounded-lg text-sm hover:bg-gray-700/50 transition mb-1 text-gray-400 font-medium"
                             :class="selectedBook?.id === book.id ? 'active-book text-blue-400' : ''">
                            <span x-text="book.book_number < 40 ? 'Cựu' : 'Tân'" class="text-[9px] uppercase tracking-wider px-1 inline-block w-8 text-center text-gray-600"></span>
                            <span x-text="book.name"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- CỘT 2: CHƯƠNG -->
            <div class="col-span-1 bg-gray-800/50 border border-gray-700 rounded-2xl overflow-hidden shadow-2xl flex flex-col">
                <div class="p-4 border-b border-gray-700 bg-gray-900/80">
                    <h2 class="font-bold text-gray-300">🔢 Chương <span x-show="selectedBook" class="text-xs font-normal text-blue-400 block truncate" x-text="selectedBook.name"></span></h2>
                </div>
                <div class="flex-1 overflow-y-auto custom-scrollbar p-2">
                    <template x-if="!selectedBook">
                        <div class="text-center py-10 text-xs text-gray-500">Hãy chọn một Sách ở cột bên trái</div>
                    </template>
                    <template x-if="isLoadingChapters">
                        <div class="text-center py-4 text-xs text-gray-500">Đang tải chương...</div>
                    </template>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        <template x-for="chapter in chapters" :key="chapter.id">
                            <button @click="selectChapter(chapter)" 
                                    class="py-2.5 text-center rounded-lg text-sm font-bold border border-gray-700 hover:border-emerald-500/50 focus:outline-none transition shadow-sm"
                                    :class="selectedChapter?.id === chapter.id ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-gray-200'">
                                <span x-text="chapter.chapter_number"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- CỘT 3: CÂU KINH THÁNH TRỰC DIỆN -->
            <div class="col-span-1 md:col-span-2 bg-gray-800/50 border border-gray-700 rounded-2xl overflow-hidden shadow-2xl flex flex-col relative">
                <div class="p-4 border-b border-gray-700 bg-gray-900/80 flex justify-between items-center z-10">
                    <div>
                        <h2 class="font-bold text-gray-300 text-lg flex items-center gap-2">
                            ✨ Nội dung Chương
                        </h2>
                        <p class="text-[11px] text-emerald-400 mt-1 uppercase tracking-wider font-semibold" x-show="selectedBook && selectedChapter" x-text="selectedBook.name + ' - Chương ' + selectedChapter.chapter_number"></p>
                    </div>
                    <div class="text-xs text-gray-500" x-show="verses.length > 0">
                        Tổng cộng: <span x-text="verses.length" class="text-gray-300 font-bold"></span> CÂU
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar p-4 lg:p-6" style="scroll-behavior: smooth;">
                    <template x-if="!selectedChapter">
                        <div class="flex flex-col items-center justify-center h-full text-center opacity-50">
                            <svg class="w-16 h-16 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <p class="text-sm text-gray-400">Dữ liệu Câu Kinh Thánh sẽ hiển thị tại đây.</p>
                            <p class="text-xs text-gray-500 mt-2">Vui lòng chọn Sách và Chương tương ứng.</p>
                        </div>
                    </template>
                    
                    <template x-if="isLoadingVerses">
                        <div class="text-center py-20">
                            <div class="inline-block w-6 h-6 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                            <p class="text-xs text-emerald-500 mt-2 font-medium">Đang bung kho dữ liệu...</p>
                        </div>
                    </template>

                    <div class="space-y-3" x-show="verses.length > 0 && !isLoadingVerses">
                        <template x-for="verse in verses" :key="verse.id">
                            <div class="group relative flex gap-3 p-3 rounded-xl hover:bg-gray-700/30 transition border border-transparent hover:border-gray-700">
                                <div class="font-bold text-gray-500 text-sm pt-0.5 select-none w-6 text-right" x-text="verse.verse_number"></div>
                                <div class="flex-1 text-gray-300 text-[15px] leading-relaxed font-serif" x-text="verse.content"></div>
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                                    <button @click="openEditModal(verse)" class="bg-blue-600/20 text-blue-400 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-md text-xs font-semibold backdrop-blur transition border border-blue-500/30">
                                        Sửa Câu Đọc
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Form Update Verse -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <!-- Modal panel -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-[#1E293B] border border-gray-700 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
                
                <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center bg-gray-800/80">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>✏️ Chỉnh Sửa Câu</span>
                        <span class="text-blue-400 bg-blue-900/30 px-2 py-0.5 rounded text-sm" x-text="selectedBook?.name + ' ' + selectedChapter?.chapter_number + ':' + currentVerse.verse_number"></span>
                    </h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-white transition focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6">
                    <div x-show="errorMsg" class="mb-4 bg-red-900/20 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg text-sm" x-text="errorMsg"></div>
                    
                    <label class="block text-xs font-semibold text-gray-400 mb-2 uppercase tracking-wider">Nội dung Câu (Giữ nguyên cấu trúc nguyên bản nếu có thể)</label>
                    <textarea x-model="currentVerse.content" rows="6" class="w-full bg-gray-900 border border-gray-700 text-gray-200 font-serif text-[15px] leading-relaxed rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 custom-scrollbar"></textarea>
                </div>

                <div class="px-6 py-4 bg-gray-800/50 border-t border-gray-700 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-gray-300 hover:text-white text-sm font-semibold transition">
                        Đóng lại
                    </button>
                    <button type="button" @click="saveVerse()" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-bold shadow-lg shadow-blue-500/20 transition flex items-center gap-2" :disabled="isSaving" :class="{'opacity-50 cursor-not-allowed': isSaving}">
                        <span x-show="!isSaving">💾 Lưu Thay Đổi</span>
                        <span x-show="isSaving">Đang ghi...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script G-A-E-V App -->
    <script>
        function bibleManager() {
            return {
                allBooks: [],
                chapters: [],
                verses: [],
                
                selectedBook: null,
                selectedChapter: null,
                
                searchBook: '',
                
                isLoadingBooks: false,
                isLoadingChapters: false,
                isLoadingVerses: false,
                
                // Edit Modal Area
                showModal: false,
                isSaving: false,
                currentVerse: { id: null, verse_number: '', content: '' },
                errorMsg: '',

                async init() {
                    this.isLoadingBooks = true;
                    try {
                        let res = await fetch('{{ url("/bible-manager/api/books") }}');
                        if (res.ok) {
                            this.allBooks = await res.json();
                        }
                    } catch (e) {
                        console.error('Lỗi tải sách:', e);
                    }
                    this.isLoadingBooks = false;
                },

                get filteredBooks() {
                    if (!this.searchBook.trim()) return this.allBooks;
                    const q = this.searchBook.toLowerCase();
                    return this.allBooks.filter(b => b.name.toLowerCase().includes(q));
                },

                async selectBook(book) {
                    this.selectedBook = book;
                    this.selectedChapter = null;
                    this.verses = [];
                    this.chapters = [];
                    
                    this.isLoadingChapters = true;
                    try {
                        let res = await fetch('{{ url("/bible-manager/api/chapters") }}?book_id=' + book.id);
                        if (res.ok) {
                            this.chapters = await res.json();
                        }
                    } catch (e) { console.error('Lỗi tải chương:', e); }
                    this.isLoadingChapters = false;
                },

                async selectChapter(chapter) {
                    this.selectedChapter = chapter;
                    this.verses = [];
                    
                    this.isLoadingVerses = true;
                    try {
                        let res = await fetch('{{ url("/bible-manager/api/verses") }}?chapter_id=' + chapter.id);
                        if (res.ok) {
                            this.verses = await res.json();
                        }
                    } catch (e) { console.error('Lỗi tải câu:', e); }
                    this.isLoadingVerses = false;
                },

                openEditModal(verse) {
                    this.currentVerse = JSON.parse(JSON.stringify(verse));
                    this.errorMsg = '';
                    this.showModal = true;
                },

                async saveVerse() {
                    this.errorMsg = '';
                    if (!this.currentVerse.content.trim()) {
                        this.errorMsg = 'Nội dung không thể để trống.';
                        return;
                    }
                    
                    this.isSaving = true;
                    try {
                        let res = await fetch('{{ url("/bible-manager/api/verses") }}/' + this.currentVerse.id, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({ content: this.currentVerse.content })
                        });
                        
                        let data = await res.json();
                        if (res.ok && data.success) {
                            // Update local variable directly
                            let idx = this.verses.findIndex(v => v.id === this.currentVerse.id);
                            if (idx !== -1) {
                                this.verses[idx].content = data.verse.content;
                            }
                            this.showModal = false;
                        } else {
                            this.errorMsg = data.error || 'Lỗi hệ thống khi cập nhật.';
                        }
                    } catch (e) {
                        this.errorMsg = 'Lỗi kết nối Máy chủ.';
                    }
                    this.isSaving = false;
                }
            }
        }
    </script>
</body>
</html>
