// layout_editor.js — Alpine.js data for PPT Layout Editor
// Loaded by ppt_layout_editor.blade.php

function layoutEditor() {
    return {
        // ── state ──────────────────────────────────────────────────────────
        activeEl: 'banner',
        saveState: '', saveMsg: '  ',

        // Human-friendly schema state — ALWAYS derived from schema, no hardcoded defaults
        s: {
            banner: { h_ratio: 0.187, bottom_offset: 0.04, fill_color: '004D40' },
            logo:   { size_ratio_h: 0.24, dx_ratio_w: 0.06, overlap_ratio: 0.40, border_color: 'FFD700', bg_color: '004D40' },
            text:   {
                logo_gap_ratio: 0.01, pad_right: 0.015, pad_top: 0, pad_bottom: 0,
                inset_left: 7.2, inset_right: 7.2, inset_top: 0, inset_bottom: 0,
                text_align: 'center', vertical_align: 'middle',
                preferred_pt: 30, min_pt: 16, max_pt: 44, line_height: 1.1, color_hex: 'FFD700'
            },
            accent_color: '002800'
        },

        // ── canvas computed ─────────────────────────────────────────────────
        cW: 800, cH: 450,
        _drag: null,

        // ── computed helpers ────────────────────────────────────────────────
        get bannerH()  { return Math.round(this.s.banner.h_ratio * this.cH); },
        get bannerY()  { return this.cH - this.bannerH - this.accentH; },
        get accentH()  { return Math.round(this.s.banner.bottom_offset * this.cH); },

        get logoSize() { return Math.round(this.s.logo.size_ratio_h * this.cH); },
        get logoX()    { return Math.round(this.s.logo.dx_ratio_w * this.cW); },
        get logoY()    { return this.bannerY - Math.round(this.s.logo.overlap_ratio * this.logoSize); },

        get textX()    {
            const logoRight = this.logoX + this.logoSize;
            return logoRight + Math.round(this.s.text.logo_gap_ratio * this.cW);
        },
        get textY()    { return this.bannerY + Math.round(this.s.text.pad_top * this.cH); },
        get textW()    { return (this.cW - Math.round(this.s.text.pad_right * this.cW)) - this.textX; },
        get textH()    { return this.bannerH - Math.round((this.s.text.pad_top + this.s.text.pad_bottom) * this.cH); },

        // pt → canvas px approximation
        ptToPx(pt) {
            const scaleFactor = this.cW / (13.33 * 96);
            return Math.round(pt * (96 / 72) * scaleFactor * 8);
        },

        // ── init ────────────────────────────────────────────────────────────
        init() {
            this.loadFromSchema(window.INITIAL_SCHEMA || {});
            this.$nextTick(() => this.fitCanvas());
            window.addEventListener('resize', () => this.fitCanvas());
        },

        fitCanvas() {
            const area = this.$refs.canvasArea;
            if (!area) return;
            const aw = area.clientWidth - 64;
            const ah = area.clientHeight - 64;
            let w = aw, h = Math.round(w * 9 / 16);
            if (h > ah) { h = ah; w = Math.round(h * 16 / 9); }
            this.cW = w; this.cH = h;
        },

        // ── Schema load/save ────────────────────────────────────────────────
        loadFromSchema(schema) {
            const elements = schema.elements || [];
            const banner = elements.find(e => e.id === 'banner') || {};
            const logo   = elements.find(e => e.id === 'logo')   || {};
            const txt    = elements.find(e => e.id === 'banner_text') || {};
            const font   = txt.font || {};

            this.s.banner.h_ratio       = banner.h_ratio              ?? 0.187;
            this.s.banner.bottom_offset = banner.bottom_offset_ratio  ?? 0.04;
            this.s.banner.fill_color    = banner.fill_color            ?? '004D40';

            this.s.logo.size_ratio_h    = logo.size_ratio_h           ?? 0.24;
            this.s.logo.dx_ratio_w      = logo.dx_ratio_w             ?? 0.06;
            this.s.logo.overlap_ratio   = logo.overlap_ratio          ?? 0.40;
            this.s.logo.border_color    = logo.border_color           ?? 'FFD700';
            this.s.logo.bg_color        = logo.bg_color               ?? '004D40';

            this.s.text.logo_gap_ratio  = txt.logo_gap_ratio          ?? 0.01;
            this.s.text.pad_right       = txt.padding_right_ratio_w   ?? 0.015;
            this.s.text.pad_top         = txt.padding_top_ratio_h     ?? 0;
            this.s.text.pad_bottom      = txt.padding_bottom_ratio_h  ?? 0;
            this.s.text.inset_left      = txt.internal_margin_left_pt  ?? 7.2;
            this.s.text.inset_right     = txt.internal_margin_right_pt ?? 7.2;
            this.s.text.inset_top       = txt.internal_margin_top_pt   ?? 0;
            this.s.text.inset_bottom    = txt.internal_margin_bottom_pt ?? 0;
            this.s.text.text_align      = txt.text_align              ?? 'center';
            this.s.text.vertical_align  = txt.vertical_align          ?? 'middle';
            this.s.text.preferred_pt    = font.preferred_size_pt      ?? 30;
            this.s.text.min_pt          = font.min_size_pt            ?? 16;
            this.s.text.max_pt          = font.max_size_pt            ?? 44;
            this.s.text.line_height     = font.line_height            ?? 1.1;
            this.s.text.color_hex       = font.color_hex              ?? 'FFD700';
            this.s.accent_color         = schema._accent_color        ?? '002800';
        },

        buildSchema() {
            const base = JSON.parse(JSON.stringify(window.INITIAL_SCHEMA || {}));
            const elements = base.elements || [];
            const banner = elements.find(e => e.id === 'banner') || {};
            const logo   = elements.find(e => e.id === 'logo')   || {};
            const txt    = elements.find(e => e.id === 'banner_text') || {};
            const font   = txt.font || {};

            banner.h_ratio             = +this.s.banner.h_ratio.toFixed(4);
            banner.bottom_offset_ratio = +this.s.banner.bottom_offset.toFixed(4);
            banner.fill_color          = this.s.banner.fill_color;

            logo.size_ratio_h          = +this.s.logo.size_ratio_h.toFixed(4);
            logo.dx_ratio_w            = +this.s.logo.dx_ratio_w.toFixed(4);
            logo.overlap_ratio         = +this.s.logo.overlap_ratio.toFixed(3);
            logo.border_color          = this.s.logo.border_color;
            logo.bg_color              = this.s.logo.bg_color;

            txt.logo_gap_ratio         = +this.s.text.logo_gap_ratio.toFixed(4);
            txt.padding_right_ratio_w  = +this.s.text.pad_right.toFixed(4);
            txt.padding_top_ratio_h    = +this.s.text.pad_top.toFixed(4);
            txt.padding_bottom_ratio_h = +this.s.text.pad_bottom.toFixed(4);

            txt.internal_margin_left_pt   = +Number(this.s.text.inset_left).toFixed(2);
            txt.internal_margin_right_pt  = +Number(this.s.text.inset_right).toFixed(2);
            txt.internal_margin_top_pt    = +Number(this.s.text.inset_top).toFixed(2);
            txt.internal_margin_bottom_pt = +Number(this.s.text.inset_bottom).toFixed(2);
            txt.text_align     = this.s.text.text_align;
            txt.vertical_align = this.s.text.vertical_align;

            font.preferred_size_pt = this.s.text.preferred_pt;
            font.min_size_pt       = this.s.text.min_pt;
            font.max_size_pt       = this.s.text.max_pt;
            font.line_height       = +this.s.text.line_height.toFixed(2);
            font.color_hex         = this.s.text.color_hex;

            base._accent_color = this.s.accent_color;
            return base;
        },

        async saveLayout() {
            this.saveState = ''; this.saveMsg = '⏳ Đang lưu...';
            try {
                const schema = this.buildSchema();
                const res = await fetch('/ChurchTool/public/api/ppt/save-layout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ schema })
                });
                const data = await res.json();
                if (res.ok && data.status === 'saved') {
                    this.saveState = 'saved';
                    this.saveMsg = '✓ Đã lưu! Vào trang PPT và nhấn F5 để thấy layout mới.';
                } else { throw new Error(data.message || 'Server error'); }
            } catch (e) {
                this.saveState = 'error'; this.saveMsg = '✕ Lỗi: ' + e.message;
            }
            setTimeout(() => { this.saveState = ''; this.saveMsg = ''; }, 6000);
        },

        resetToDefault() {
            this.loadFromSchema(window.INITIAL_SCHEMA || {});
            this.onSchemaChange();
        },

        onSchemaChange() {
            this.s.banner.h_ratio = Math.min(0.45, Math.max(0.10, this.s.banner.h_ratio));
            this.s.logo.size_ratio_h = Math.min(0.40, Math.max(0.08, this.s.logo.size_ratio_h));
            ['inset_left','inset_right','inset_top','inset_bottom'].forEach(k => {
                this.s.text[k] = Math.max(0, Math.min(40, +this.s.text[k] || 0));
            });
        },

        // ── Nudge logo 1px ──────────────────────────────────────────────────
        nudgeLogo(dx, dy) {
            if (dx !== 0) {
                this.s.logo.dx_ratio_w = Math.min(0.25, Math.max(0.01,
                    this.s.logo.dx_ratio_w + (dx / this.cW)
                ));
            }
            if (dy !== 0) {
                this.s.logo.overlap_ratio = Math.min(0.80, Math.max(-0.10,
                    this.s.logo.overlap_ratio - (dy / this.logoSize)
                ));
            }
        },

        // ── Drag handlers ────────────────────────────────────────────────────
        startBannerDrag(e) {
            e.preventDefault(); this.activeEl = 'banner';
            const startY = e.clientY, startRatio = this.s.banner.h_ratio;
            this._drag = { type: 'banner', onMove: (ev) => {
                const dy = startY - ev.clientY;
                this.s.banner.h_ratio = Math.min(0.45, Math.max(0.10, startRatio + dy / this.cH));
            }};
        },

        startLogoDrag(e) {
            e.preventDefault(); this.activeEl = 'logo';
            const startX = e.clientX, startY = e.clientY;
            const startDx = this.s.logo.dx_ratio_w, startOverlap = this.s.logo.overlap_ratio;
            const logoSize = this.logoSize;
            this._drag = { type: 'logo-move', onMove: (ev) => {
                this.s.logo.dx_ratio_w = Math.min(0.25, Math.max(0.01, startDx + (ev.clientX - startX) / this.cW));
                this.s.logo.overlap_ratio = Math.min(0.80, Math.max(-0.10, startOverlap + (startY - ev.clientY) / logoSize));
            }};
        },

        startLogoResize(e) {
            e.preventDefault(); this.activeEl = 'logo';
            const startX = e.clientX, startSize = this.s.logo.size_ratio_h;
            this._drag = { type: 'logo-resize', onMove: (ev) => {
                this.s.logo.size_ratio_h = Math.min(0.40, Math.max(0.08, startSize + (ev.clientX - startX) / this.cH));
            }};
        },

        startTextResize(e) {
            e.preventDefault(); this.activeEl = 'text';
            const startX = e.clientX, startPad = this.s.text.pad_right;
            this._drag = { type: 'text-resize', onMove: (ev) => {
                this.s.text.pad_right = Math.min(0.12, Math.max(0.005, startPad + (startX - ev.clientX) / this.cW));
            }};
        },

        onMouseMove(e) { if (this._drag) this._drag.onMove(e); },
        onMouseUp(e)   { this._drag = null; },
    };
}
