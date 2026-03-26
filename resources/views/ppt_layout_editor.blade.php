<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PPT Layout Editor — ChurchTools</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg: #0d1117; --panel: #161b22; --panel2: #1c222b;
    --border: #30363d; --text: #e6edf3; --muted: #8b949e;
    --gold: #e8b85a; --teal: #2ea399; --red: #f85149; --green: #3fb950;
    --blue: #58a6ff; --purple: #bc8cff;
}
html, body { height: 100%; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); overflow: hidden; }
body { display: flex; flex-direction: column; }

/* ── Top Bar ── */
.topbar {
    height: 48px; background: var(--panel); border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 1rem; padding: 0 1.25rem;
    flex-shrink: 0; z-index: 100;
}
.topbar-logo { font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; }
.topbar-logo .x { background: var(--teal); border-radius: 5px; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 11px; }
.topbar-title { font-size: 0.8rem; color: var(--muted); border-left: 1px solid var(--border); padding-left: 1rem; margin-left: 0.25rem; }
.topbar-actions { margin-left: auto; display: flex; gap: 0.5rem; align-items: center; }
.btn { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; font-weight: 600; padding: 0.35rem 0.85rem; border-radius: 7px; border: 1px solid transparent; cursor: pointer; transition: all 0.15s; text-decoration: none; }
.btn-primary   { background: var(--teal); color: #fff; }
.btn-primary:hover { background: #25877e; }
.btn-secondary { background: transparent; color: var(--text); border-color: var(--border); }
.btn-secondary:hover { background: var(--panel2); }
.btn-danger    { background: transparent; color: var(--red); border-color: var(--red); }
.btn-danger:hover { background: rgba(248,81,73,0.1); }
.save-indicator { font-size: 0.72rem; color: var(--muted); }
.save-indicator.saved { color: var(--green); }
.save-indicator.error { color: var(--red); }

/* ── Main Layout ── */
.main { display: flex; flex: 1; overflow: hidden; }

/* ── Sidebar ── */
.sidebar {
    width: 290px; flex-shrink: 0;
    background: var(--panel); border-right: 1px solid var(--border);
    display: flex; flex-direction: column; overflow-y: auto;
}
.sidebar-section { border-bottom: 1px solid var(--border); }
.sidebar-section-title {
    font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.08em; color: var(--muted);
    padding: 0.65rem 1rem 0.4rem;
}
.field-group { padding: 0 1rem 0.75rem; display: flex; flex-direction: column; gap: 0.45rem; }
.field-row { display: flex; align-items: center; gap: 0.5rem; }
.field-label { font-size: 0.7rem; color: var(--muted); width: 76px; flex-shrink: 0; }
.field-val { font-size: 0.76rem; font-weight: 600; color: var(--text); min-width: 40px; text-align: right; }
.field-unit { font-size: 0.65rem; color: var(--muted); width: 24px; text-align: right; }
input[type=range] {
    flex: 1; -webkit-appearance: none; height: 4px;
    background: var(--border); border-radius: 2px; outline: none; cursor: pointer;
}
input[type=range]::-webkit-slider-thumb {
    -webkit-appearance: none; width: 13px; height: 13px;
    background: var(--teal); border-radius: 50%; cursor: grab;
}
input[type=range]::-webkit-slider-thumb:active { cursor: grabbing; }
.color-input-row { display: flex; align-items: center; gap: 0.5rem; }
input[type=color] { width: 30px; height: 22px; padding: 1px; border: 1px solid var(--border); border-radius: 5px; background: var(--panel2); cursor: pointer; }
select.field-select {
    flex: 1; background: var(--panel2); border: 1px solid var(--border);
    color: var(--text); font-size: 0.72rem; padding: 0.25rem 0.4rem;
    border-radius: 5px; outline: none; cursor: pointer;
}
select.field-select:focus { border-color: var(--teal); }
.hint { font-size: 0.65rem; color: var(--muted); padding: 0 1rem 0.6rem; line-height: 1.5; }
.element-selector { padding: 0.5rem 1rem; display: flex; gap: 0.4rem; flex-wrap: wrap; }
.el-chip {
    font-size: 0.71rem; padding: 0.22rem 0.6rem; border-radius: 99px;
    border: 1px solid var(--border); cursor: pointer; transition: all 0.15s;
    background: var(--panel2); color: var(--muted);
}
.el-chip.active { border-color: var(--teal); color: var(--teal); background: rgba(46,163,153,0.1); }

/* Nudge arrows */
.nudge-grid {
    display: grid; grid-template-columns: repeat(3, 30px); grid-template-rows: repeat(3, 28px);
    gap: 2px; justify-content: center; margin: 0.25rem 0;
}
.nudge-btn {
    display: flex; align-items: center; justify-content: center;
    background: var(--panel2); border: 1px solid var(--border);
    border-radius: 5px; cursor: pointer; font-size: 14px;
    color: var(--muted); transition: all 0.12s; user-select: none;
}
.nudge-btn:hover { background: rgba(46,163,153,0.15); border-color: var(--teal); color: var(--teal); }
.nudge-btn:active { transform: scale(0.9); }

/* 4-corner inset grid */
.inset-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 0.4rem;
}
.inset-cell { display: flex; flex-direction: column; gap: 0.15rem; }
.inset-cell label { font-size: 0.62rem; color: var(--muted); }
input.inset-num {
    width: 100%; background: var(--panel2); border: 1px solid var(--border);
    color: var(--text); font-size: 0.75rem; padding: 0.25rem 0.4rem;
    border-radius: 5px; outline: none; text-align: center;
}
input.inset-num:focus { border-color: var(--teal); }

/* ── Canvas area ── */
.canvas-area {
    flex: 1; display: flex; align-items: center; justify-content: center;
    background: var(--bg); overflow: hidden; position: relative;
    padding: 2rem;
}
.slide-canvas {
    position: relative; background: #00FF00;
    box-shadow: 0 0 0 1px rgba(255,255,255,0.12), 0 8px 40px rgba(0,0,0,0.6);
    border-radius: 4px; overflow: hidden; user-select: none;
}

/* ── Elements on canvas ── */
.el-banner {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: #004D40; cursor: ns-resize;
    border-top: 2px solid transparent; transition: border-color 0.15s;
}
.el-banner:hover, .el-banner.active { border-top-color: var(--gold); }
.el-banner-handle {
    position: absolute; top: -1px; left: 0; right: 0; height: 8px;
    cursor: ns-resize; background: transparent;
}
.el-banner-handle::after {
    content: '⠿';
    position: absolute; top: -8px; left: 50%; transform: translateX(-50%);
    font-size: 12px; color: var(--gold); opacity: 0.7; pointer-events: none;
}

.el-logo {
    position: absolute; border-radius: 50%;
    border: 3px solid #FFD700; background: #004D40;
    cursor: move; display: flex; align-items: center; justify-content: center;
    font-size: 22px; color: #FFD700;
    box-shadow: 0 0 0 2px rgba(255,215,0,0.2);
    transition: box-shadow 0.15s;
}
.el-logo:hover, .el-logo.active { box-shadow: 0 0 0 3px rgba(255,215,0,0.5); }

/* Resize handle */
.resize-handle {
    position: absolute; width: 10px; height: 10px;
    background: var(--gold); border-radius: 50%;
    border: 2px solid var(--bg);
    cursor: nwse-resize;
    bottom: 0; right: 0;
    transform: translate(40%, 40%);
}

.el-textbox {
    position: absolute; border: 2px dashed rgba(255,215,0,0.6);
    cursor: move; display: flex; align-items: center; justify-content: center;
    font-weight: 600; font-family: Georgia, serif;
    transition: border-color 0.15s;
    overflow: hidden;
}
.el-textbox:hover, .el-textbox.active { border-color: #FFD700; }
.el-textbox-resize { cursor: se-resize; bottom: 0; right: 0; border-radius: 0 0 2px 0; }

.el-accent {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: #002800; pointer-events: none;
}

/* Inset visual overlay */
.inset-overlay {
    position: absolute; border: 1px dashed rgba(100,200,255,0.4);
    pointer-events: none;
}

.ruler-label {
    position: absolute; font-size: 9px; color: rgba(255,255,255,0.35);
    pointer-events: none;
}

/* ── Status bar ── */
.statusbar {
    height: 24px; background: var(--panel); border-top: 1px solid var(--border);
    display: flex; align-items: center; gap: 1.5rem; padding: 0 1rem;
    font-size: 0.68rem; color: var(--muted); flex-shrink: 0;
}
.statusbar span span { color: var(--text); font-weight: 600; }

/* ── Scrollbar ── */
::-webkit-scrollbar { width: 5px; } ::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
</style>
</head>
<body x-data="layoutEditor()" x-init="init()" @mousemove.window="onMouseMove($event)" @mouseup.window="onMouseUp($event)">

<!-- TOP BAR -->
<div class="topbar">
    <div class="topbar-logo">
        <div class="x">✝</div> ChurchTools
    </div>
    <div class="topbar-title">🎨 PPT Layout Editor <span style="color:var(--muted); font-size:0.7rem; margin-left:0.5rem">— chỉnh xong nhấn 💾 để áp dụng</span></div>
    <div class="topbar-actions">
        <a href="/ChurchTool/public/ppt" class="btn btn-secondary">← Quay lại PPT</a>
        <span class="save-indicator" :class="saveState" x-text="saveMsg"></span>
        <button class="btn btn-secondary" @click="resetToDefault">↺ Reset</button>
        <button class="btn btn-primary" @click="saveLayout">💾 Lưu Layout</button>
    </div>
</div>

<!-- MAIN -->
<div class="main">

    @include('ppt._partials.layout_editor_sidebar')

    <!-- CANVAS AREA -->
    <div class="canvas-area" x-ref="canvasArea">
        <div class="slide-canvas" x-ref="slide"
             :style="`width:${cW}px; height:${cH}px`">

            <!-- Accent stripe (bottom) -->
            <div class="el-accent" :style="`height:${accentH}px`"></div>

            <!-- BANNER -->
            <div class="el-banner"
                 :class="{active: activeEl==='banner'}"
                 :style="`height:${bannerH}px; background:#${s.banner.fill_color}`"
                 @mousedown="activeEl='banner'">
                <!-- Drag handle on top edge -->
                <div class="el-banner-handle"
                     @mousedown.stop="startBannerDrag($event)"></div>
                <span class="ruler-label" style="top:4px; left:8px" x-text="`Banner: ${Math.round(s.banner.h_ratio*100)}% H`"></span>
            </div>

            <!-- TEXT INSET OVERLAY (shown when text active) -->
            <template x-if="activeEl==='text'">
                <div class="inset-overlay"
                     :style="`
                       left:${textX + ptToPx(s.text.inset_left)}px;
                       top:${textY + ptToPx(s.text.inset_top)}px;
                       width:${textW - ptToPx(s.text.inset_left) - ptToPx(s.text.inset_right)}px;
                       height:${textH - ptToPx(s.text.inset_top) - ptToPx(s.text.inset_bottom)}px;
                     `"></div>
            </template>

            <!-- LOGO CIRCLE -->
            <div class="el-logo"
                 :class="{active: activeEl==='logo'}"
                 :style="`
                   left:${logoX}px; top:${logoY}px;
                   width:${logoSize}px; height:${logoSize}px;
                   border-color:#${s.logo.border_color};
                   background:#${s.logo.bg_color};
                   font-size:${Math.round(logoSize*0.4)}px;
                 `"
                 @mousedown.stop="startLogoDrag($event)">
                ♫
                <!-- Resize handle bottom-right -->
                <div class="resize-handle" @mousedown.stop="startLogoResize($event)"></div>
            </div>

            <!-- TEXT BOX -->
            <div class="el-textbox"
                 :class="{active: activeEl==='text'}"
                 :style="`
                   left:${textX}px; top:${textY}px;
                   width:${textW}px; height:${textH}px;
                   font-size:${Math.min(14, Math.max(8, textH/4.5))}px;
                   color:#${s.text.color_hex};
                   text-align:${s.text.text_align};
                   justify-content:${s.text.vertical_align === 'top' ? 'flex-start' : s.text.vertical_align === 'bottom' ? 'flex-end' : 'center'};
                   padding: ${ptToPx(s.text.inset_top)}px ${ptToPx(s.text.inset_right)}px ${ptToPx(s.text.inset_bottom)}px ${ptToPx(s.text.inset_left)}px;
                 `"
                 @mousedown.stop="activeEl='text'">
                <div>
                    <div style="opacity:0.5; font-size:0.7em; margin-bottom:2px">TEXT ZONE</div>
                    <div>Hiệp lại trong ân điển Ngài</div>
                    <div>Dự phần trong công tác Ngài</div>
                </div>
                <!-- Resize indicator -->
                <div class="resize-handle el-textbox-resize" style="border-radius:0 0 2px 0" @mousedown.stop="startTextResize($event)"></div>
            </div>

        </div>
    </div>

</div>

<!-- STATUS BAR -->
<div class="statusbar">
    <span>📐 Canvas: <span x-text="`${cW}×${cH}px`"></span></span>
    <span>🟦 Banner: <span x-text="`${Math.round(s.banner.h_ratio*100)}%`"></span></span>
    <span>⭕ Logo: <span x-text="`${Math.round(s.logo.size_ratio_h*100)}%H  x:${Math.round(s.logo.dx_ratio_w*100)}%`"></span></span>
    <span>📝 Align: <span x-text="`${s.text.text_align} / ${s.text.vertical_align}`"></span></span>
    <span>🔲 Inset: <span x-text="`L${s.text.inset_left} R${s.text.inset_right} T${s.text.inset_top} B${s.text.inset_bottom}pt`"></span></span>
    <span style="margin-left:auto">💾 Lưu layout để áp dụng vào PPT</span>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    window.INITIAL_SCHEMA = @json($schema);
</script>
<script src="/ChurchTool/public/js/ppt/layout_editor.js"></script>
</body>
</html>
