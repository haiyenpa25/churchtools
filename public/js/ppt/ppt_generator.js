// ppt_generator.js — Alpine.js data for PptLivestream tool
// Loaded via <script src="/ChurchTool/public/js/ppt/ppt_generator.js">

function pptGenerator() {
    const SLIDE_W = 13.33, SLIDE_H = 7.5;

    // ── Default schema (fallback until AJAX loads the real one) ─────────────
    const DEFAULT_SCHEMA = {
        banner: { h_ratio: 0.1952 },
        logo:   { dx_ratio_w: 0.0593, size_ratio_h: 0.24, overlap_ratio: 0.18 },
        text:   {
            logo_gap_ratio: 0.01,
            padding_right_ratio_w: 0.015,
            padding_top_ratio_h: 0,
            padding_bottom_ratio_h: 0.028,
            font: { preferred_size_pt: 30, min_size_pt: 16, max_size_pt: 44, line_height: 1.1, color_hex: 'FFD700' }
        },
        safe_area: { left: 0.015, right: 0.015, top: 0.015, bottom: 0.015 }
    };

    return {
        L: { SLIDE_W, SLIDE_H, S: DEFAULT_SCHEMA },

        // ── State ──────────────────────────────────────────────────────────
        templates: [],
        debugMode: false,
        showAdvanced: false,
        isLoading: false,
        isExtracting: false,
        isSuccess: false,
        isError: false,
        errorMessage: '',
        downloadUrl: '',
        step: 1,
        blocks: [],

        form: {
            template_id: '',
            text: 'Câu 1:\nGod is good\nChúa thật tốt lành\nAll the time\nMọi lúc mọi nơi\n\nĐiệp khúc:\nHallelujah\nNgợi khen Chúa',
            logo_file: null,
            custom_banner_color: '',
            custom_text_color: '',
            custom_logo_border: '',
            overrides: { x: '', y: '', width: '', height: '', font_size: '', font_color: '' }
        },

        // ── Library & Playlist State ───────────────────────────────────────
        inputType: 'library', // 'library' | 'text'
        songs: [],
        songsLoading: false,
        searchSong: '',
        searchCategory: '',
        playlist: [],

        get filteredSongs() {
            let res = this.songs;
            if (this.searchCategory) {
                res = res.filter(s => s.category.toLowerCase().includes(this.searchCategory.toLowerCase()));
            }
            if (!this.searchSong) return res;
            
            const q = this.searchSong.toLowerCase();
            return res.filter(s => 
                s.title.toLowerCase().includes(q) || 
                (s.number && s.number.toLowerCase() === q) ||
                (s.number && s.number.toLowerCase().includes(q))
            );
        },

        // ── Init ───────────────────────────────────────────────────────────
        init() {
            this.fetchTemplates();
            this.fetchLayoutSchema();
            this.fetchSongs();
        },

        // ── Fetch real layout schema from saved JSON ──────────────────────
        async fetchLayoutSchema() {
            try {
                const res = await fetch('/ChurchTool/public/api/ppt/layout-schema');
                if (!res.ok) return;
                const schema = await res.json();
                const elems = schema.elements || [];
                const banner = elems.find(e => e.id === 'banner') || {};
                const logo   = elems.find(e => e.id === 'logo')   || {};
                const txt    = elems.find(e => e.id === 'banner_text') || {};

                // Reconstruct L.S from the real schema elements
                this.L = {
                    SLIDE_W: 13.33, SLIDE_H: 7.5,
                    S: {
                        banner: {
                            h_ratio: banner.h_ratio ?? 0.1952,
                        },
                        logo: {
                            dx_ratio_w:   logo.dx_ratio_w    ?? 0.0593,
                            size_ratio_h: logo.size_ratio_h  ?? 0.24,
                            overlap_ratio: logo.overlap_ratio ?? 0.18,
                        },
                        text: {
                            logo_gap_ratio:             txt.logo_gap_ratio              ?? 0.01,
                            padding_right_ratio_w:      txt.padding_right_ratio_w       ?? 0.015,
                            padding_top_ratio_h:        txt.padding_top_ratio_h         ?? 0,
                            padding_bottom_ratio_h:     txt.padding_bottom_ratio_h      ?? 0.028,
                            internal_margin_left_pt:    txt.internal_margin_left_pt      ?? 7.2,
                            internal_margin_right_pt:   txt.internal_margin_right_pt     ?? 7.2,
                            internal_margin_top_pt:     txt.internal_margin_top_pt       ?? 0,
                            internal_margin_bottom_pt:  txt.internal_margin_bottom_pt    ?? 0,
                            text_align:    txt.text_align    ?? 'center',
                            vertical_align: txt.vertical_align ?? 'middle',
                            font: txt.font ?? { preferred_size_pt: 30, min_size_pt: 16, max_size_pt: 44, line_height: 1.1, color_hex: 'FFD700' }
                        },
                        safe_area: schema.safe_area ?? { left: 0.015, right: 0.015, top: 0.015, bottom: 0.015 }
                    }
                };
                console.log('[PPT] Layout schema loaded from engine:', this.L.S.banner.h_ratio);
            } catch(e) {
                console.warn('[PPT] Could not load layout schema, using defaults:', e);
            }
        },

        // ── Fetch templates from DB ────────────────────────────────────────
        async fetchTemplates() {
            try {
                let response = await fetch('/ChurchTool/public/api/ppt/templates');
                let data = await response.json();

                const parsed = data.map(tmpl => {
                    let fc = {};
                    try {
                        let raw = tmpl.presets?.[0]?.font_config ?? null;
                        let i = 0;
                        while (typeof raw === 'string' && i++ < 4) raw = JSON.parse(raw);
                        if (raw && typeof raw === 'object') fc = raw;
                    } catch (e) {}
                    tmpl._fc = fc;
                    return tmpl;
                });

                this.templates = parsed.filter(t => !!t._fc?.banner_color);

                if (this.templates.length > 0) {
                    this.selectTemplate(this.templates[0]);
                }
            } catch (error) {
                console.error('Error fetching templates:', error);
            }
        },

        // ── Select template ────────────────────────────────────────────────
        selectTemplate(tmpl) {
            this.form.template_id = tmpl.id;
            if (tmpl.presets && tmpl.presets[0]) {
                const preset = tmpl.presets[0];
                if (+preset.x > 0)      this.form.overrides.x      = preset.x;
                if (+preset.y > 0)      this.form.overrides.y      = preset.y;
                if (+preset.width > 0)  this.form.overrides.width  = preset.width;
                if (+preset.height > 0) this.form.overrides.height = preset.height;
                if (tmpl._fc.size)  this.form.overrides.font_size  = tmpl._fc.size;
                if (tmpl._fc.color) this.form.overrides.font_color = tmpl._fc.color;
            }
        },

        // ── Library & Playlist Helpers ─────────────────────────────────────
        async fetchSongs() {
            this.songsLoading = true;
            try {
                let res = await fetch('/ChurchTool/public/api/songs');
                if (res.ok) {
                    this.songs = await res.json();
                }
            } catch (e) {
                console.error("Lỗi tải danh sách bài hát:", e);
            }
            this.songsLoading = false;
        },

        addSongToPlaylist(song) {
            // Clone into playlist so same song can be added twice if needed
            this.playlist.push({ ...song, _uuid: Date.now() + Math.random() });
        },

        removeSong(index) {
            this.playlist.splice(index, 1);
        },

        moveSongUp(index) {
            if (index > 0) {
                const temp = this.playlist[index];
                this.playlist[index] = this.playlist[index - 1];
                this.playlist[index - 1] = temp;
            }
        },

        moveSongDown(index) {
            if (index < this.playlist.length - 1) {
                const temp = this.playlist[index];
                this.playlist[index] = this.playlist[index + 1];
                this.playlist[index + 1] = temp;
            }
        },

        // ── Active theme (merged template + custom overrides) ──────────────
        get activeTheme() {
            const tmpl = this.templates.find(t => t.id == this.form.template_id);
            const base = tmpl?._fc || {};
            return {
                ...base,
                banner_color:      this.form.custom_banner_color || base.banner_color || '004D40',
                color:             this.form.custom_text_color   || base.color        || 'FFD700',
                logo_border_color: this.form.custom_logo_border  || base.logo_border_color || 'FFD700',
                logo_bg_color:     base.logo_bg_color || '004D40',
            };
        },

        // ── Parse text to blocks ───────────────────────────────────────────
        async parseTextToBlocks() {
            // Nối toàn bộ bài từ Playlist vào biến text trước khi gửi
            if (this.inputType === 'library') {
                if (this.playlist.length === 0) { alert('Vui lòng chọn ít nhất 1 bài hát!'); return; }
                
                // Nối lại bằng 2 Enter (chuẩn phân chia bài hát để SmartLyricsParser hiểu đó là đoạn chia slide)
                this.form.text = this.playlist.map(s => {
                    let titleText = s.number ? s.number + ". " + s.title : s.title;
                    let t = titleText.toUpperCase();
                    // Để giữ format sạch, thêm Tiêu đề làm Slide bìa cho mỗi bài!
                    return t + "\n" + s.lyrics;
                }).join('\n\n');
            }

            if (!this.form.text.trim()) { alert('Vui lòng nhập văn bản!'); return; }
            this.isLoading = true;
            this.isError = false;
            try {
                let res = await fetch('/ChurchTool/public/api/ppt/parse', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ text: this.form.text, is_dual_lang: this.form.is_dual_lang })
                });
                let data = await res.json();
                if (res.ok && data.status === 'success') {
                    this.blocks = data.blocks;
                    this.step = 2;
                } else {
                    this.isError = true;
                    this.errorMessage = data.message || 'Lỗi phân tích cú pháp.';
                }
            } catch (e) {
                this.isError = true;
                this.errorMessage = 'Không thể kết nối Backend.';
            }
            this.isLoading = false;
        },

        // ── Load .txt file ─────────────────────────────────────────────────
        loadTxtFile(event) {
            let file = event.target.files[0];
            if (!file) return;
            let reader = new FileReader();
            reader.onload = async (e) => {
                this.form.text = e.target.result;
                event.target.value = '';
                await this.parseTextToBlocks();
            };
            reader.readAsText(file, 'utf-8');
        },

        // ── Extract text from old .pptx ────────────────────────────────────
        async extractText(event) {
            let file = event.target.files[0];
            if (!file) return;
            this.isExtracting = true;
            let fd = new FormData();
            fd.append('file', file);
            try {
                let res = await fetch('/ChurchTool/public/api/ppt/extract', { method: 'POST', body: fd });
                let data = await res.json();
                if (data.status === 'success') {
                    this.form.text = data.text;
                } else {
                    alert('Lỗi trích xuất: ' + data.message);
                }
            } catch (e) {
                alert('Không thể trích xuất chữ: ' + e.message);
            } finally {
                this.isExtracting = false;
                event.target.value = '';
            }
        },

        // ── Submit → generate .pptx ────────────────────────────────────────
        async submit() {
            if (this.blocks.length === 0) return;
            this.isLoading = true;
            this.isSuccess = false;
            this.isError = false;

            try {
                let formData = new FormData();
                formData.append('template_id', this.form.template_id);
                formData.append('blocks', JSON.stringify(this.blocks));
                formData.append('overrides', JSON.stringify(this.form.overrides));

                // Pass active theme colors to Python engine
                const theme = this.activeTheme;
                formData.append('banner_color',      theme.banner_color      || '004D40');
                formData.append('logo_border_color', theme.logo_border_color || 'FFD700');
                formData.append('logo_bg_color',     theme.logo_bg_color     || '004D40');
                formData.append('text_color',        theme.color             || 'FFD700');

                if (this.form.logo_file) {
                    formData.append('logo_file', this.form.logo_file);
                }

                let response = await fetch('/ChurchTool/public/ppt/bulk-generate', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData
                });

                let result = await response.json();

                if (response.ok && result.status === 'queued_bulk') {
                    this.isSuccess = true;
                    if (result.download_url) {
                        window.location.href = result.download_url;
                    }
                    setTimeout(() => this.isSuccess = false, 5000);
                } else {
                    throw new Error(result.message || 'Lỗi xử lý server');
                }
            } catch (err) {
                this.isError = true;
                this.errorMessage = err.message;
                setTimeout(() => this.isError = false, 5000);
            } finally {
                this.isLoading = false;
            }
        }
    };
}
