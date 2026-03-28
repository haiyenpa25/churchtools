<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Template – ChurchTools PPT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.08); }
        input[type=color] { -webkit-appearance: none; border: none; padding: 0; }
        input[type=color]::-webkit-color-swatch-wrapper { padding: 0; }
        input[type=color]::-webkit-color-swatch { border-radius: 6px; border: none; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen p-6"
      x-data="templateManager()"
      x-init="fetchTemplates()">

    {{-- Background blobs --}}
    <div class="fixed top-[-10%] left-[-5%] w-80 h-80 bg-teal-700 rounded-full mix-blend-multiply filter blur-3xl opacity-30 pointer-events-none"></div>
    <div class="fixed bottom-[-10%] right-[-5%] w-80 h-80 bg-purple-700 rounded-full mix-blend-multiply filter blur-3xl opacity-30 pointer-events-none"></div>

    <div class="max-w-5xl mx-auto relative z-10">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <a href="/ChurchTool/public/ppt" class="text-xs text-gray-500 hover:text-gray-300 transition">← PPT Tool</a>
                </div>
                <h1 class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-teal-400 to-blue-400">
                    🎨 Quản lý Template
                </h1>
                <p class="text-gray-500 text-sm mt-1">Tạo, sửa tên, đổi logo và xóa template PPT của bạn.</p>
            </div>
            {{-- New template button --}}
            <button @click="openCreate()"
                    class="flex items-center gap-2 bg-teal-600 hover:bg-teal-500 text-white font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-teal-500/20 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tạo Template Mới
            </button>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="text-center py-20 text-gray-500 animate-pulse">Đang tải...</div>

        {{-- Templates grid --}}
        <div x-show="!loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="tmpl in templates" :key="tmpl.id">
                <div class="glass rounded-2xl p-5 flex flex-col gap-4 hover:border-teal-500/40 transition group">

                    {{-- Template preview banner --}}
                    <div class="relative rounded-xl overflow-hidden" style="aspect-ratio:16/9; background:#00FF00;">
                        <div class="absolute bottom-0 inset-x-0" style="height:28%;"
                             :style="`background-color:#${tmpl._fc?.banner_color||'004D40'};`">

                            {{-- Logo: uploaded image or default icon --}}
                            <div class="absolute rounded-full border-2 overflow-hidden flex items-center justify-center"
                                 style="height:90%; aspect-ratio:1/1; left:3%; top:50%; transform:translateY(-50%);"
                                 :style="`border-color:#${tmpl._fc?.logo_border_color||'FFD700'}; background-color:#${tmpl._fc?.logo_bg_color||'004D40'};`">
                                <template x-if="tmpl.logo_url">
                                    <img :src="tmpl.logo_url" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!tmpl.logo_url">
                                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:55%;height:55%"
                                         :style="`color:#${tmpl._fc?.logo_border_color||'FFD700'};`">
                                        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                                    </svg>
                                </template>
                            </div>

                            {{-- Sample text --}}
                            <div class="absolute inset-y-0 flex flex-col justify-center font-bold"
                                 style="left:28%; right:3%; font-size:7px; font-family:Georgia,serif; line-height:1.2;"
                                 :style="`color:#${tmpl._fc?.color||'FFD700'};`">
                                <div>Thánh Ca Ngài</div>
                                <div style="opacity:.7">Con Dâng Lên</div>
                            </div>
                        </div>
                    </div>

                    {{-- Name + meta --}}
                    <div>
                        <h3 class="font-bold text-white text-base leading-tight" x-text="tmpl.name"></h3>
                        <p class="text-xs text-gray-500 mt-0.5" x-text="tmpl.logo_url ? '✅ Có logo' : '⬜ Chưa có logo'"></p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-2 mt-auto">
                        <button @click="openEdit(tmpl)"
                                class="flex-1 text-sm font-semibold text-teal-300 hover:text-white py-2 px-3 bg-teal-600/15 hover:bg-teal-600/30 border border-teal-600/30 rounded-lg transition">
                            ✏️ Sửa
                        </button>
                        <button @click="confirmDelete(tmpl)"
                                class="text-sm font-semibold text-red-400 hover:text-white py-2 px-3 bg-red-500/10 hover:bg-red-500/25 border border-red-500/20 rounded-lg transition">
                            🗑️
                        </button>
                    </div>
                </div>
            </template>

            {{-- Empty state --}}
            <div x-show="!loading && templates.length === 0"
                 class="col-span-3 text-center py-16 text-gray-600">
                <p class="text-5xl mb-3">🖼️</p>
                <p class="font-semibold">Chưa có template nào. Hãy tạo template đầu tiên!</p>
            </div>
        </div>
    </div>

    {{-- ══ Modal Create/Edit ══ --}}
    <div x-show="modal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @click.self="modal.open = false">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="relative glass rounded-2xl p-7 w-full max-w-md shadow-2xl">
            <h2 class="text-xl font-bold mb-5" x-text="modal.id ? '✏️ Sửa Template' : '🆕 Tạo Template Mới'"></h2>

            {{-- Name --}}
            <label class="block text-sm font-semibold text-gray-300 mb-1">Tên template</label>
            <input type="text" x-model="modal.name" placeholder="VD: Teal Gold Classic"
                   class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl py-2.5 px-4 mb-4 focus:outline-none focus:border-teal-500">

            {{-- Logo upload --}}
            <label class="block text-sm font-semibold text-gray-300 mb-2">Logo (tùy chọn)</label>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 rounded-full border-2 border-yellow-500 bg-gray-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <template x-if="modal.logoPreview">
                        <img :src="modal.logoPreview" class="w-full h-full object-cover rounded-full">
                    </template>
                    <template x-if="!modal.logoPreview && !modal.existingLogoUrl">
                        <svg class="w-7 h-7 text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                    </template>
                    <template x-if="!modal.logoPreview && modal.existingLogoUrl">
                        <img :src="modal.existingLogoUrl" class="w-full h-full object-cover rounded-full">
                    </template>
                </div>
                <div class="flex-1">
                    <input type="file" id="logoInput" accept="image/png,image/jpeg,image/webp"
                           @change="onLogoChange($event)" class="hidden">
                    <label for="logoInput"
                           class="block text-center text-sm font-semibold text-blue-300 py-2 px-3 bg-blue-500/15 hover:bg-blue-500/25 border border-blue-500/30 rounded-lg cursor-pointer transition">
                        📂 Chọn ảnh logo
                    </label>
                    <template x-if="modal.id && (modal.existingLogoUrl || modal.logoFile)">
                        <button @click="modal.removeLogo = true; modal.logoPreview = null; modal.existingLogoUrl = null; modal.logoFile = null"
                                class="mt-1 w-full text-[11px] text-red-400 hover:text-red-300 text-center">
                            ✕ Xóa logo hiện tại
                        </button>
                    </template>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex gap-3 mt-2">
                <button @click="modal.open = false"
                        class="flex-1 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 font-semibold transition">
                    Hủy
                </button>
                <button @click="saveTemplate()"
                        :disabled="saving || !modal.name.trim()"
                        class="flex-1 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold transition disabled:opacity-50">
                    <span x-show="!saving" x-text="modal.id ? 'Lưu Thay đổi' : 'Tạo Template'"></span>
                    <span x-show="saving" class="animate-pulse">Đang lưu...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══ Delete Confirm Modal ══ --}}
    <div x-show="deleteModal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @click.self="deleteModal.open = false">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="relative glass rounded-2xl p-7 w-full max-w-sm text-center shadow-2xl">
            <p class="text-4xl mb-3">🗑️</p>
            <h2 class="text-lg font-bold text-white mb-2">Xóa template này?</h2>
            <p class="text-gray-400 text-sm mb-5">
                "<span class="text-white font-semibold" x-text="deleteModal.name"></span>" sẽ bị xóa vĩnh viễn. Không thể hoàn tác.
            </p>
            <div class="flex gap-3">
                <button @click="deleteModal.open = false"
                        class="flex-1 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 font-semibold transition">
                    Hủy
                </button>
                <button @click="deleteTemplate()"
                        :disabled="saving"
                        class="flex-1 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold transition disabled:opacity-50">
                    <span x-show="!saving">Xác nhận Xóa</span>
                    <span x-show="saving" class="animate-pulse">Đang xóa...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div x-show="toast.show" x-cloak
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed bottom-8 inset-x-0 flex justify-center z-50 pointer-events-none">
        <div class="px-6 py-3 rounded-full shadow-2xl font-semibold text-sm pointer-events-auto"
             :class="toast.success ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
             x-text="toast.msg"></div>
    </div>

    <script>
    function templateManager() {
        return {
            templates: [],
            loading: true,
            saving: false,
            modal: { open: false, id: null, name: '', logoFile: null, logoPreview: null, existingLogoUrl: null, removeLogo: false },
            deleteModal: { open: false, id: null, name: '' },
            toast: { show: false, msg: '', success: true },

            async fetchTemplates() {
                this.loading = true;
                const res = await fetch('{{ url("/api/ppt/templates") }}');
                const data = await res.json();
                this.templates = data.map(t => {
                    let fc = {};
                    try {
                        let raw = t.presets?.[0]?.font_config ?? null;
                        let i = 0;
                        while (typeof raw === 'string' && i++ < 4) raw = JSON.parse(raw);
                        if (raw && typeof raw === 'object') fc = raw;
                    } catch(e) {}
                    t._fc = fc;
                    // Build logo URL if logo_path exists
                    t.logo_url = t.logo_path
                        ? '/ChurchTool/public/storage/' + t.logo_path
                        : null;
                    return t;
                });
                this.loading = false;
            },

            openCreate() {
                this.modal = { open: true, id: null, name: '', logoFile: null, logoPreview: null, existingLogoUrl: null, removeLogo: false };
                // Clear file input
                const el = document.getElementById('logoInput');
                if (el) el.value = '';
            },

            openEdit(tmpl) {
                this.modal = {
                    open: true,
                    id: tmpl.id,
                    name: tmpl.name,
                    logoFile: null,
                    logoPreview: null,
                    existingLogoUrl: tmpl.logo_url,
                    removeLogo: false
                };
                const el = document.getElementById('logoInput');
                if (el) el.value = '';
            },

            onLogoChange(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.modal.logoFile = file;
                this.modal.logoPreview = URL.createObjectURL(file);
                this.modal.removeLogo = false;
            },

            async saveTemplate() {
                if (!this.modal.name.trim()) return;
                this.saving = true;
                const fd = new FormData();
                fd.append('name', this.modal.name.trim());
                if (this.modal.logoFile) fd.append('logo_file', this.modal.logoFile);
                if (this.modal.removeLogo) fd.append('remove_logo', '1');

                const url = this.modal.id
                    ? `{{ url("/api/ppt/templates") }}/${this.modal.id}`
                    : '{{ url("/api/ppt/templates") }}';

                try {
                    const res = await fetch(url, { method: 'POST', body: fd });
                    const json = await res.json();
                    if (res.ok) {
                        this.modal.open = false;
                        await this.fetchTemplates();
                        this.showToast(this.modal.id ? 'Đã cập nhật template!' : 'Đã tạo template mới!', true);
                    } else {
                        this.showToast(json.message || 'Lỗi khi lưu', false);
                    }
                } catch(e) {
                    this.showToast('Lỗi kết nối', false);
                }
                this.saving = false;
            },

            confirmDelete(tmpl) {
                this.deleteModal = { open: true, id: tmpl.id, name: tmpl.name };
            },

            async deleteTemplate() {
                this.saving = true;
                try {
                    const res = await fetch(`{{ url("/api/ppt/templates") }}/${this.deleteModal.id}`, { method: 'DELETE' });
                    if (res.ok) {
                        this.deleteModal.open = false;
                        await this.fetchTemplates();
                        this.showToast('Đã xóa template!', true);
                    } else {
                        this.showToast('Không thể xóa', false);
                    }
                } catch(e) {
                    this.showToast('Lỗi kết nối', false);
                }
                this.saving = false;
            },

            showToast(msg, success = true) {
                this.toast = { show: true, msg, success };
                setTimeout(() => this.toast.show = false, 3000);
            }
        };
    }
    </script>
</body>
</html>
