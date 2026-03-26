<!-- SIDEBAR -->
<div class="sidebar">

    <div class="sidebar-section">
        <div class="sidebar-section-title">Phần tử đang chỉnh</div>
        <div class="element-selector">
            <div class="el-chip" :class="{active: activeEl==='banner'}" @click="activeEl='banner'">🟦 Banner</div>
            <div class="el-chip" :class="{active: activeEl==='logo'}"   @click="activeEl='logo'">⭕ Logo</div>
            <div class="el-chip" :class="{active: activeEl==='text'}"   @click="activeEl='text'">📝 Text Box</div>
        </div>
    </div>

    <!-- BANNER CONTROLS -->
    <template x-if="activeEl==='banner'">
        <div>
            <div class="sidebar-section">
                <div class="sidebar-section-title">📐 Kích thước Banner</div>
                <div class="field-group">
                    <div class="field-row">
                        <span class="field-label">Chiều cao</span>
                        <input type="range" x-model.number="s.banner.h_ratio" min="0.10" max="0.45" step="0.005" @input="onSchemaChange">
                        <span class="field-val" x-text="Math.round(s.banner.h_ratio*100)+'%'"></span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Khoảng dưới</span>
                        <input type="range" x-model.number="s.banner.bottom_offset" min="0" max="0.08" step="0.002" @input="onSchemaChange">
                        <span class="field-val" x-text="Math.round(s.banner.bottom_offset*100)+'%'"></span>
                    </div>
                </div>
            </div>
            <div class="sidebar-section">
                <div class="sidebar-section-title">🎨 Màu sắc Banner</div>
                <div class="field-group">
                    <div class="color-input-row">
                        <span class="field-label">Nền banner</span>
                        <input type="color" :value="'#'+s.banner.fill_color" @input="e => { s.banner.fill_color = e.target.value.replace('#',''); onSchemaChange(); }">
                        <span class="field-val" x-text="'#'+s.banner.fill_color"></span>
                    </div>
                    <div class="color-input-row">
                        <span class="field-label">Accent stripe</span>
                        <input type="color" :value="'#'+s.accent_color" @input="e => { s.accent_color = e.target.value.replace('#',''); onSchemaChange(); }">
                        <span class="field-val" x-text="'#'+s.accent_color"></span>
                    </div>
                </div>
            </div>
            <p class="hint">💡 Kéo mép trên banner để đổi chiều cao. Accent stripe là dải màu nhỏ dưới đáy.</p>
        </div>
    </template>

    <!-- LOGO CONTROLS -->
    <template x-if="activeEl==='logo'">
        <div>
            <div class="sidebar-section">
                <div class="sidebar-section-title">📐 Kích thước Logo</div>
                <div class="field-group">
                    <div class="field-row">
                        <span class="field-label">Kích thước</span>
                        <input type="range" x-model.number="s.logo.size_ratio_h" min="0.08" max="0.40" step="0.005" @input="onSchemaChange">
                        <span class="field-val" x-text="Math.round(s.logo.size_ratio_h*100)+'%H'"></span>
                    </div>
                </div>
            </div>

            <!-- Position nudge -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">🧭 Vị trí Logo — Kéo hoặc nhấn mũi tên</div>
                <div class="field-group">
                    <div class="field-row">
                        <span class="field-label">Ngang (X)</span>
                        <input type="range" x-model.number="s.logo.dx_ratio_w" min="0.01" max="0.25" step="0.002" @input="onSchemaChange">
                        <span class="field-val" x-text="Math.round(s.logo.dx_ratio_w*100)+'%W'"></span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Nhô lên</span>
                        <input type="range" x-model.number="s.logo.overlap_ratio" min="-0.10" max="0.80" step="0.01" @input="onSchemaChange">
                        <span class="field-val" x-text="Math.round(s.logo.overlap_ratio*100)+'%'"></span>
                    </div>
                </div>
                <!-- Nudge buttons -->
                <div style="padding: 0 1rem 0.75rem;">
                    <div class="nudge-grid">
                        <div></div>
                        <div class="nudge-btn" @mousedown.prevent @click="nudgeLogo(0,-1)">↑</div>
                        <div></div>
                        <div class="nudge-btn" @mousedown.prevent @click="nudgeLogo(-1,0)">←</div>
                        <div class="nudge-btn" style="background:var(--panel2); cursor:default; font-size:10px; color:var(--muted)">px</div>
                        <div class="nudge-btn" @mousedown.prevent @click="nudgeLogo(1,0)">→</div>
                        <div></div>
                        <div class="nudge-btn" @mousedown.prevent @click="nudgeLogo(0,1)">↓</div>
                        <div></div>
                    </div>
                    <p style="font-size:0.62rem; color:var(--muted); text-align:center; margin-top:0.3rem">Nudge ±1px</p>
                </div>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-section-title">🎨 Màu Logo</div>
                <div class="field-group">
                    <div class="color-input-row">
                        <span class="field-label">Viền logo</span>
                        <input type="color" :value="'#'+s.logo.border_color" @input="e => { s.logo.border_color = e.target.value.replace('#',''); onSchemaChange(); }">
                        <span class="field-val" x-text="'#'+s.logo.border_color"></span>
                    </div>
                    <div class="color-input-row">
                        <span class="field-label">Nền logo</span>
                        <input type="color" :value="'#'+s.logo.bg_color" @input="e => { s.logo.bg_color = e.target.value.replace('#',''); onSchemaChange(); }">
                        <span class="field-val" x-text="'#'+s.logo.bg_color"></span>
                    </div>
                </div>
            </div>
            <p class="hint">💡 Kéo logo trên canvas để di chuyển. Mũi tên điều chỉnh ±1px chính xác.</p>
        </div>
    </template>

    <!-- TEXT CONTROLS -->
    <template x-if="activeEl==='text'">
        <div>
            <!-- Padding ngoài -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">📦 Lề ngoài Text Box (% slide)</div>
                <div class="field-group">
                    <div class="field-row">
                        <span class="field-label">Gap trái</span>
                        <input type="range" x-model.number="s.text.logo_gap_ratio" min="0.0" max="0.06" step="0.002" @input="onSchemaChange">
                        <span class="field-val" x-text="Math.round(s.text.logo_gap_ratio*1000)/10+'%'"></span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Lề phải</span>
                        <input type="range" x-model.number="s.text.pad_right" min="0.005" max="0.08" step="0.002" @input="onSchemaChange">
                        <span class="field-val" x-text="Math.round(s.text.pad_right*100)+'%'"></span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Lề trên</span>
                        <input type="range" x-model.number="s.text.pad_top" min="0.0" max="0.06" step="0.002" @input="onSchemaChange">
                        <span class="field-val" x-text="Math.round(s.text.pad_top*100)+'%'"></span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Lề dưới</span>
                        <input type="range" x-model.number="s.text.pad_bottom" min="0.0" max="0.06" step="0.002" @input="onSchemaChange">
                        <span class="field-val" x-text="Math.round(s.text.pad_bottom*100)+'%'"></span>
                    </div>
                </div>
            </div>

            <!-- Internal margins -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">🔲 Lề trong Text Box (pt)</div>
                <div class="field-group">
                    <div class="inset-grid">
                        <div class="inset-cell">
                            <label>Trái (pt)</label>
                            <input class="inset-num" type="number" x-model.number="s.text.inset_left" min="0" max="40" step="0.5" @input="onSchemaChange">
                        </div>
                        <div class="inset-cell">
                            <label>Phải (pt)</label>
                            <input class="inset-num" type="number" x-model.number="s.text.inset_right" min="0" max="40" step="0.5" @input="onSchemaChange">
                        </div>
                        <div class="inset-cell">
                            <label>Trên (pt)</label>
                            <input class="inset-num" type="number" x-model.number="s.text.inset_top" min="0" max="40" step="0.5" @input="onSchemaChange">
                        </div>
                        <div class="inset-cell">
                            <label>Dưới (pt)</label>
                            <input class="inset-num" type="number" x-model.number="s.text.inset_bottom" min="0" max="40" step="0.5" @input="onSchemaChange">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alignment -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">↔ Căn chỉnh Text</div>
                <div class="field-group">
                    <div class="field-row">
                        <span class="field-label">Căn ngang</span>
                        <select class="field-select" x-model="s.text.text_align" @change="onSchemaChange">
                            <option value="left">Trái (Left)</option>
                            <option value="center">Giữa (Center)</option>
                            <option value="right">Phải (Right)</option>
                        </select>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Căn dọc</span>
                        <select class="field-select" x-model="s.text.vertical_align" @change="onSchemaChange">
                            <option value="top">Trên (Top)</option>
                            <option value="middle">Giữa (Middle)</option>
                            <option value="bottom">Dưới (Bottom)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Font -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">✍️ Font</div>
                <div class="field-group">
                    <div class="field-row">
                        <span class="field-label">Cỡ ưa thích</span>
                        <input type="range" x-model.number="s.text.preferred_pt" min="14" max="60" step="1" @input="onSchemaChange">
                        <span class="field-val" x-text="s.text.preferred_pt+'pt'"></span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Cỡ tối thiểu</span>
                        <input type="range" x-model.number="s.text.min_pt" min="10" max="30" step="1" @input="onSchemaChange">
                        <span class="field-val" x-text="s.text.min_pt+'pt'"></span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Cỡ tối đa</span>
                        <input type="range" x-model.number="s.text.max_pt" min="20" max="72" step="1" @input="onSchemaChange">
                        <span class="field-val" x-text="s.text.max_pt+'pt'"></span>
                    </div>
                    <div class="field-row">
                        <span class="field-label">Line height</span>
                        <input type="range" x-model.number="s.text.line_height" min="0.9" max="2.0" step="0.05" @input="onSchemaChange">
                        <span class="field-val" x-text="s.text.line_height.toFixed(2)"></span>
                    </div>
                    <div class="color-input-row">
                        <span class="field-label">Màu chữ</span>
                        <input type="color" :value="'#'+s.text.color_hex" @input="e => { s.text.color_hex = e.target.value.replace('#',''); onSchemaChange(); }">
                        <span class="field-val" x-text="'#'+s.text.color_hex"></span>
                    </div>
                </div>
            </div>
            <p class="hint">💡 Lề trong (pt) = khoảng cách từ mép hộp đến chữ bên trong (inset). Lề ngoài (%) = vị trí hộp text trong banner.</p>
        </div>
    </template>

</div>
