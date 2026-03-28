<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản Lý Bài Hát — ChurchTools</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0B0E14; color: #E2E8F0; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="min-h-screen custom-scrollbar flex flex-col" x-data="songManager()">

    <!-- Header -->
    <header class="bg-[#111827] border-b border-gray-800 sticky top-0 z-10 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ url('/') }}" class="text-gray-400 hover:text-white transition w-8 h-8 flex items-center justify-center rounded-full bg-gray-800 hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-white flex items-center gap-2">
                        <span class="text-blue-400">📖</span> Thư Viện Bài Hát
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">Quản lý kho tàng Thánh Ca và Tôn Vinh (<span x-text="songs.length"></span> bài)</p>
                </div>
            </div>
            <button @click="openAdd()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Thêm bài mới
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Filters -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Tìm kiếm theo Tên, Số bài hát, hoặc Điệp khúc..." 
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-700 rounded-xl leading-5 bg-gray-800/50 text-gray-200 placeholder-gray-500 focus:outline-none focus:bg-gray-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition text-sm">
            </div>
            
            <select x-model="searchCategory" class="block w-full sm:w-64 pl-3 pr-10 py-2.5 bg-gray-800/50 border border-gray-700 text-white rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm transition">
                <option value="">Tất cả danh mục</option>
                <option value="thanh ca tin lanh">Thánh Ca Tin Lành</option>
                <option value="ton vinh chua hang huu">Tôn Vinh Chúa Hằng Hữu</option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-gray-800/50 border border-gray-700 rounded-2xl overflow-hidden shadow-2xl">
            <template x-if="isLoading">
                <div class="py-20 text-center text-gray-500">Đang tải dữ liệu bài hát...</div>
            </template>
            
            <div class="overflow-x-auto custom-scrollbar" x-show="!isLoading">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-900/80">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-24">Số</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tên Bài Hát</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-64">Danh mục</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider w-32">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        <template x-for="song in filteredSongs" :key="song.id">
                            <tr class="hover:bg-gray-700/30 transition group cursor-pointer" @click="openEdit(song)">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-400" x-text="song.number || '—'"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-200 group-hover:text-blue-400 transition" x-text="song.title"></div>
                                    <div class="text-xs text-gray-500 truncate w-96 mt-1" x-text="song.lyrics.replace(/\n/g, ' / ').substring(0, 80) + '...'"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-[10px] leading-4 font-bold rounded-full uppercase tracking-wide"
                                          :class="song.category.includes('thanh ca') ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'"
                                          x-text="song.category">
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium opacity-0 group-hover:opacity-100 transition">
                                    <button class="text-blue-400 hover:text-blue-300 mr-3">Sửa</button>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredSongs.length === 0 && !isLoading">
                            <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">Không tìm thấy bài hát nào khớp với tìm kiếm.</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Form -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <!-- Modal panel -->
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-[#1E293B] border border-gray-700 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                
                <div class="px-6 py-5 border-b border-gray-700 flex justify-between items-center bg-gray-800/50">
                    <h3 class="text-lg leading-6 font-bold text-white flex items-center gap-2" id="modal-title">
                        <span x-text="isEditing ? '✏️ Chỉnh Sửa Bài Hát' : '✨ Thêm Bài Hát Mới'"></span>
                    </h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-white transition focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">
                    <div class="grid grid-cols-4 gap-4">
                        <div class="col-span-1">
                            <label class="block text-xs font-semibold text-gray-400 mb-1 uppercase tracking-wider">Số bài</label>
                            <input type="text" x-model="currentSong.number" placeholder="Vd: 001" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg py-2 px-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                        </div>
                        <div class="col-span-3">
                            <label class="block text-xs font-semibold text-gray-400 mb-1 uppercase tracking-wider">Tựa đề bài hát <span class="text-red-400">*</span></label>
                            <input type="text" x-model="currentSong.title" placeholder="Cúi Xin Vua Thánh Ngự Lai..." class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg py-2 px-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1 uppercase tracking-wider">Danh mục <span class="text-red-400">*</span></label>
                        <select x-model="currentSong.category" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg py-2 px-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm appearance-none">
                            <option value="thanh ca tin lanh">Thánh Ca Tin Lành</option>
                            <option value="ton vinh chua hang huu">Tôn Vinh Chúa Hằng Hữu</option>
                            <option value="bai hat khac">Bài hát khác</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1 uppercase tracking-wider flex justify-between">
                            <span>Nội dung Lời bài hát <span class="text-red-400">*</span></span>
                            <span class="text-gray-600 font-normal lowercase normal-case">Dùng 2 dòng trống (Enter 2 lần) để chia slide</span>
                        </label>
                        <textarea x-model="currentSong.lyrics" rows="12" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg py-3 px-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm custom-scrollbar" placeholder="Đưa lời bài hát vào đây..."></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-800/50 border-t border-gray-700 flex justify-between items-center">
                    <div>
                        <button x-show="isEditing" @click="deleteSong()" type="button" class="text-sm text-red-400 hover:text-red-300 font-semibold px-3 py-2 rounded-lg hover:bg-red-500/10 transition">
                            Thùng rác
                        </button>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-800 text-gray-300 hover:text-white border border-gray-700 hover:bg-gray-700 rounded-lg text-sm font-semibold transition">
                            Hủy
                        </button>
                        <button type="button" @click="save()" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-bold shadow-lg shadow-blue-500/20 transition flex items-center gap-2" :disabled="isSaving" :class="{'opacity-50 cursor-not-allowed': isSaving}">
                            <span x-show="!isSaving">Lưu Bài Hát</span>
                            <span x-show="isSaving">Đang lưu...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script>
        function songManager() {
            return {
                songs: [],
                isLoading: false,
                isSaving: false,
                searchQuery: '',
                searchCategory: '',
                
                showModal: false,
                isEditing: false,
                currentSong: {},

                async init() {
                    this.isLoading = true;
                    try {
                        let res = await fetch('{{ url("/api/songs") }}');
                        if (res.ok) {
                            this.songs = await res.json();
                        }
                    } catch (e) {
                        console.error("Lỗi:", e);
                    }
                    this.isLoading = false;
                },

                get filteredSongs() {
                    let res = this.songs;
                    if (this.searchCategory) {
                        res = res.filter(s => s.category.toLowerCase().includes(this.searchCategory.toLowerCase()));
                    }
                    if (!this.searchQuery) return res;
                    
                    let q = this.searchQuery.toLowerCase();
                    return res.filter(s => 
                        s.title.toLowerCase().includes(q) || 
                        (s.number && s.number.toLowerCase() === q) ||
                        (s.number && s.number.toLowerCase().includes(q)) ||
                        (s.lyrics && s.lyrics.toLowerCase().includes(q))
                    );
                },

                openAdd() {
                    this.currentSong = { id: null, number: '', title: '', category: 'thanh ca tin lanh', lyrics: '' };
                    this.isEditing = false;
                    this.showModal = true;
                },

                openEdit(song) {
                    this.currentSong = JSON.parse(JSON.stringify(song)); // deep clone
                    this.isEditing = true;
                    this.showModal = true;
                },

                async save() {
                    if(!this.currentSong.title || !this.currentSong.category || !this.currentSong.lyrics) {
                        return alert('Vui lòng điền đủ Tên, Danh mục và Lời bài hát!');
                    }
                    
                    this.isSaving = true;
                    let url = '{{ url("/api/songs") }}';
                    let method = 'POST';
                    if (this.isEditing) {
                        url += '/' + this.currentSong.id;
                        method = 'PUT';
                    }

                    try {
                        let res = await fetch(url, {
                            method, 
                            headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
                            body: JSON.stringify(this.currentSong)
                        });
                        let data = await res.json();
                        if (res.ok && data.status === 'success') {
                            if (this.isEditing) {
                                let idx = this.songs.findIndex(s => s.id === this.currentSong.id);
                                if (idx !== -1) this.songs[idx] = data.song;
                            } else {
                                this.songs.push(data.song);
                            }
                            this.showModal = false;
                            
                            // Sort again
                            this.songs.sort((a,b) => {
                                let nA = parseInt(a.number) || 99999;
                                let nB = parseInt(b.number) || 99999;
                                if(nA !== nB) return nA - nB;
                                return a.title.localeCompare(b.title);
                            });
                        } else {
                            alert('Lỗi lưu dữ liệu: ' + (data.message || 'Server error'));
                        }
                    } catch (e) {
                        alert('Không thể kết nối máy chủ.');
                    }
                    this.isSaving = false;
                },

                async deleteSong() {
                    if(!confirm('Bạn có chắc xoá vĩnh viễn "' + this.currentSong.title + '" khỏi thư viện không? Hành động này không thể hoàn tác.')) return;
                    
                    this.isSaving = true;
                    try {
                        let res = await fetch('{{ url("/api/songs") }}/' + this.currentSong.id, { method: 'DELETE' });
                        if (res.ok) {
                            this.songs = this.songs.filter(s => s.id !== this.currentSong.id);
                            this.showModal = false;
                        }
                    } catch (e) {
                        alert('Lỗi kết nối.');
                    }
                    this.isSaving = false;
                }
            }
        }
    </script>
</body>
</html>
