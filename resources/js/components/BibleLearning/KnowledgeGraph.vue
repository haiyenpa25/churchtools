<template>
  <div class="cosmos-root" @keydown.esc="closeAll">

    <!-- ══ STARS CANVAS BACKGROUND ══ -->
    <canvas ref="starCanvas" class="star-bg"></canvas>

    <!-- ══ HEADER ══ -->
    <header class="cosmos-header">
      <div class="header-left">
        <button @click="showSidebar = !showSidebar" class="mobile-menu-btn">☰</button>
        <a href="/bible-learning" class="back-btn">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="header-text-wrap">
          <h1 class="cosmos-title">✦ Lưới Tư Duy Thần Học</h1>
          <p class="cosmos-subtitle">Cosmos Theology Explorer · {{ allNodes.length }} Thực Thể · {{ allEdges.length }} Liên Kết</p>
        </div>
      </div>
      <div class="header-center">
        <div class="view-switcher">
          <button v-for="v in viewModes" :key="v.id" @click="switchView(v.id)"
            :class="['view-btn', { active: currentView === v.id }]">
            <span>{{ v.icon }}</span>
            <span class="view-label">{{ v.label }}</span>
          </button>
        </div>
      </div>
      <div class="header-right">
        <button @click="showSettings = true" class="settings-btn" title="Cài đặt Hệ Thống">⚙️</button>
        <button @click="showParser = true" class="ai-parse-btn">
          <span class="ai-icon">✨</span> <span class="ai-text">AI Phân Tích</span>
        </button>
      </div>
    </header>

    <!-- ══ LEFT SIDEBAR: FILTER ══ -->
    <aside :class="['filter-sidebar', { 'is-open': showSidebar }]">
      <h3 class="sidebar-title">🔍 Khám Phá</h3>
      <div class="search-wrap">
        <input v-model="searchQuery" @input="onSearch" placeholder="Tìm kiếm..." class="search-input"/>
        <span class="search-icon">⌕</span>
      </div>
      <div class="filter-section">
        <p class="filter-label">LỌC THEO LOẠI</p>
        <label v-for="g in groups" :key="g.id" class="filter-row" :style="`--gc: ${g.color}`">
          <input type="checkbox" v-model="activeGroups" :value="g.id" @change="applyFilter" />
          <span class="filter-dot"></span>
          <span>{{ g.label }}</span>
          <span class="filter-count">{{ nodeCountByGroup(g.id) }}</span>
        </label>
      </div>
      <div class="legend-section">
        <p class="filter-label">CHÚ GIẢI</p>
        <div v-for="g in groups" :key="'l'+g.id" class="legend-row">
          <span class="legend-dot" :style="`background:${g.color}; box-shadow: 0 0 8px ${g.color}`"></span>
          <span>{{ g.label }}</span>
        </div>
      </div>
    </aside>

    <!-- ══ GRAPH CONTAINER ══ -->
    <div ref="networkContainer" class="graph-area"></div>

    <!-- ══ RIGHT DETAIL PANEL (Enhanced + 3 Tabs) ══ -->
    <transition name="panel-slide">
      <aside v-if="selectedNode" class="detail-panel">
        <div class="panel-header">
          <span class="panel-group-badge" :style="`background:${groupColor(selectedNode.group)}`">
            {{ groupLabel(selectedNode.group) }}
          </span>
          <button @click="closePanel" class="close-btn">✕</button>
        </div>
        <h2 class="panel-title">{{ selectedNode.label }}</h2>
        <div class="panel-desc" v-html="selectedNode.title || 'Không có mô tả.'"></div>

        <!-- Tab Navigation -->
        <div class="panel-tabs">
          <button :class="['panel-tab', { active: panelTab === 'links' }]" @click="panelTab = 'links'">🔗 Liên Kết</button>
          <button :class="['panel-tab', { active: panelTab === 'bible' }]" @click="switchPanelTab('bible')">📖 Kinh Thánh</button>
          <button :class="['panel-tab', { active: panelTab === 'commentary' }]" @click="switchPanelTab('commentary')">📚 Giải Nghĩa</button>
        </div>

        <!-- TAB: LINKS -->
        <div v-if="panelTab === 'links'">
          <div class="panel-stats">
            <span class="stat-chip">🔗 {{ totalNeighbors }} liên kết</span>
            <span v-for="(list, type) in neighbors" :key="type" class="stat-chip"
              :style="`background:${groupColor(type)}22; color:${groupColor(type)}`">
              {{ groupIcon(type) }} {{ list.length }} {{ groupLabel(type) }}
            </span>
          </div>
          <div v-if="loadingNeighbors" class="nb-loading">Đang tải liên kết...</div>
          <div v-else-if="Object.keys(neighbors).length" class="neighbor-groups">
            <div v-for="(list, type) in neighbors" :key="'nb'+type" class="nb-group">
              <h4 class="nb-group-title" :style="`color:${groupColor(type)}`">
                {{ groupIcon(type) }} {{ groupLabel(type) }}
              </h4>
              <div v-for="nb in list" :key="nb.id" class="nb-card" @click="jumpToNode(nb.id)">
                <div class="nb-relation">{{ nb.relationship }}</div>
                <div class="nb-label">{{ nb.label }}</div>
              </div>
            </div>
          </div>
          <p v-else class="nb-empty">Chưa có liên kết nào.</p>
          <button @click="focusNode(selectedNode.id)" class="focus-btn">🔭 Focus node này</button>
        </div>

        <!-- TAB: KINH THÁNH -->
        <div v-if="panelTab === 'bible'" class="bible-tab">
          <div class="bible-nav">
            <input v-model="bibleBook" @keyup.enter="fetchBibleText" placeholder="Tên sách (VD: Ma-thi-ơ)" class="bible-input" />
            <div class="bible-chapter-nav">
              <button @click="navigateChapter(-1)" :disabled="bibleChapter <= 1" class="chap-btn">‹</button>
              <span class="chap-num">Chương {{ bibleChapter }}</span>
              <button @click="navigateChapter(1)" class="chap-btn">›</button>
            </div>
            <button @click="fetchBibleText" :disabled="loadingBible" class="bible-load-btn">
              {{ loadingBible ? '...' : '📖 Đọc' }}
            </button>
          </div>
          <div v-if="bibleError" class="bible-error">⚠️ {{ bibleError }}</div>
          <div v-if="bibleData" class="bible-content">
            <div class="bible-title">{{ bibleData.title }}</div>
            <div v-for="v in bibleData.verses" :key="v.verse" class="bible-verse">
              <span class="verse-num">{{ v.verse }}</span>
              <span class="verse-text">{{ v.text }}</span>
            </div>
          </div>
          <div v-if="!bibleData && !loadingBible && !bibleError" class="bible-hint">
            <p>💡 Nhập tên sách và bấm <b>Đọc</b> để xem câu Kinh Thánh từ dữ liệu local.</p>
            <p class="hint-sub">Ví dụ: <em>Ma-thi-ơ</em>, <em>Giăng</em>, <em>Sáng Thế Ký</em></p>
          </div>
        </div>

        <!-- TAB: GIẢI NGHĨA -->
        <div v-if="panelTab === 'commentary'" class="commentary-tab">
          <div class="commentary-nav">
            <input v-model="commentaryBook" @keyup.enter="fetchCommentary(1)" placeholder="Tên sách (VD: Sáng Thế Ký)" class="bible-input" />
            <button @click="fetchCommentary(1)" :disabled="loadingCommentary" class="bible-load-btn">
              {{ loadingCommentary ? '...' : '📚 Đọc' }}
            </button>
          </div>
          <div v-if="commentaryError" class="bible-error">⚠️ {{ commentaryError }}</div>
          <div v-if="commentaryData" class="commentary-content">
            <div class="commentary-source">{{ commentaryData.source }}</div>
            <div class="commentary-text">{{ commentaryData.page_content }}</div>
            <div class="commentary-pagination">
              <button @click="fetchCommentary(commentaryPage - 1)" :disabled="commentaryPage <= 1" class="page-btn">‹ Trước</button>
              <span class="page-info">Trang {{ commentaryPage }} / {{ commentaryData.total_pages }}</span>
              <button @click="fetchCommentary(commentaryPage + 1)" :disabled="!commentaryData.has_more" class="page-btn">Tiếp ›</button>
            </div>
          </div>
          <div v-if="!commentaryData && !loadingCommentary && !commentaryError" class="bible-hint">
            <p>💡 Giải nghĩa Kinh Thánh của Warren W. Wiersbe theo từng sách.</p>
            <p class="hint-sub">Nhập tên sách và bấm <b>Đọc</b> để xem giải nghĩa.</p>
          </div>
        </div>
      </aside>
    </transition>

    <!-- ══ AI TEXT PARSER MODAL ══ -->
    <transition name="modal-fade">
      <div v-if="showParser" class="modal-overlay" @click.self="showParser = false">
        <div class="parser-modal">
          <div class="modal-header">
            <div>
              <h2 class="modal-title">✨ AI Text-to-Graph Parser</h2>
              <p class="modal-sub">Dán văn bản Kinh Thánh / bài giảng → AI sinh ra nodes & edges mới</p>
            </div>
            <button @click="showParser = false" class="close-btn">✕</button>
          </div>

          <textarea v-model="parseText"
            class="parse-textarea"
            placeholder="Ví dụ: Môi-se là con của A-mơ-ram và Giô-kê-bết. Ông được sinh ra tại Ai Cập vào thời kỳ Pha-ra-ôn cai trị. Khi còn nhỏ, Môi-se được cứu khỏi bị giết bởi Bà Con Gái của Pha-ra-ôn..."
            rows="8"
          ></textarea>

          <div v-if="parseError" class="parse-error">⚠️ {{ parseError }}</div>

          <div class="modal-actions">
            <div class="parse-info">
              <span class="info-dot">💡</span>
              AI phân tích văn bản ngữ nghĩa, trích xuất nhân vật, địa danh, sự kiện và liên kết tự động.
            </div>
            <button @click="runParseText" :disabled="parsingAI || !parseText.trim()" class="parse-btn">
              <span v-if="parsingAI" class="btn-spinner"></span>
              <span>{{ parsingAI ? 'Đang phân tích...' : '🚀 Phân Tích & Nạp vào Graph' }}</span>
            </button>
          </div>

          <!-- AI Result Preview -->
          <div v-if="parseResult" class="parse-result">
            <div class="result-header">
              <span class="result-badge">✅ {{ parseResult.nodes.length }} Thực Thể — {{ parseResult.edges.length }} Liên Kết được tạo</span>
            </div>
            <p class="result-summary">{{ parseResult.summary }}</p>
            <div class="result-nodes">
              <span v-for="n in parseResult.nodes" :key="n.id"
                class="result-node-chip"
                :style="`border-color:${groupColor(n.group)};color:${groupColor(n.group)}`">
                {{ groupIcon(n.group) }} {{ n.label }}
              </span>
            </div>
            <button @click="injectIntoGraph" class="inject-btn">
              ⚡ Thêm vào Đồ Thị Ngay
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ══ ADMIN SETTINGS MODAL ══ -->
    <transition name="modal-fade">
      <div v-if="showSettings" class="modal-overlay" @click.self="showSettings = false">
        <div class="parser-modal settings-modal" style="max-width: 800px; height: 85vh;">
          <div class="modal-header">
            <div>
              <h2 class="modal-title">⚙️ Trung Tâm Quản Trị Hệ Sinh Thái Dữ Liệu</h2>
              <p class="modal-sub">Data Portability & Data Management Center</p>
            </div>
            <button @click="showSettings = false" class="close-btn">✕</button>
          </div>

          <div class="admin-tabs">
            <button :class="['admin-tab', { active: adminTab === 'files' }]" @click="adminTab = 'files'">📁 Quản Lý File Đầu Vào (Ingestion)</button>
            <button :class="['admin-tab', { active: adminTab === 'portability' }]" @click="adminTab = 'portability'">🔄 Di Chuyển Dữ Liệu (Dump & Restore)</button>
            <button :class="['admin-tab', { active: adminTab === 'settings' }]" @click="adminTab = 'settings'">🔑 Cấu Hình API Key</button>
          </div>


          <div v-if="adminMsg" :class="['admin-msg', { 'error': adminError }]">
            {{ adminMsg }}
          </div>

          <!-- TAB: DATA PORTABILITY -->
          <div v-if="adminTab === 'portability'" class="admin-scroll-area">
            <div class="admin-grid">
              <div class="admin-card">
                <h3>📥 Nạp JSON (Import)</h3>
                <p>Khôi phục Data tức thì từ <code>database/data/bible_dump/</code> không cần chạy qua Máy chủ AI.</p>
                <button @click="runAdminAction('import')" :disabled="adminLoading" class="admin-btn btn-blue">Nạp dữ liệu Git</button>
              </div>
              <div class="admin-card">
                <h3>📤 Đóng Gói (Export)</h3>
                <p>Đóng gói CSDL Graph thành JSON (5000 lines/file) đẩy vào Git Sync chuẩn bị chuyển Server.</p>
                <button @click="runAdminAction('export')" :disabled="adminLoading" class="admin-btn btn-green">Xuất File JSON</button>
              </div>
              <div class="admin-card" style="grid-column: 1 / -1;">
                <h3>🔄 Format Xóa Rỗng CSDL (Reset)</h3>
                <p>Xóa sạch Bảng <code>bl_nodes</code> và <code>bl_edges</code> làm lại từ đầu. Thận trọng khi dùng lệnh này!</p>
                <button @click="runAdminAction('reset')" :disabled="adminLoading" class="admin-btn btn-red">Xóa Trắng Database</button>
              </div>
            </div>
          </div>

          <!-- TAB: API KEY SETTINGS -->
          <div v-if="adminTab === 'settings'" class="admin-scroll-area">
            <div class="admin-card" style="grid-column: 1 / -1;">
              <h3 style="font-size: 16px; margin-bottom: 5px;">🔑 Thiết Lập Gemini API Key</h3>
              <p style="font-size: 13px;">Khóa (Key) ở cấp độ Server này sẽ Ghi Đè (Override) lên mọi cấu hình mặc định (Mỗi ngày Google cấp <span style="color:#6ee7b7">1500 Requests Miễn phí</span> cho một tài khoản cá nhân). Bạn có thể lấy Key miễn phí tại <a href="https://aistudio.google.com/app/apikey" target="_blank" style="color: #60a5fa; text-decoration: underline;">aistudio.google.com</a>.</p>
              
              <div style="display:flex; gap:10px; margin-top:20px; align-items:center; flex-wrap:wrap">
                <input v-model="geminiApiKeyInput" type="text" placeholder="Dán chuỗi khóa bí mật bắt đầu bằng AIzaSy... của bạn vào đây" class="fm-select" style="flex:1; min-width: 250px; font-family: monospace;" />
                <button @click="saveApiKey" :disabled="adminLoading" class="admin-btn btn-purple">💾 Lưu Khóa Lên Server</button>
              </div>

              <div style="margin-top:20px; font-size:13px; color:#cbd5e1;" v-if="currentMaskedKey">
                ✅ Máy chủ hiện đang vận hành bằng Key: <code style="font-size: 15px; background: rgba(168,85,247,0.2); padding: 4px 8px; border-radius: 6px; color: #d8b4fe; border: 1px solid rgba(168,85,247,0.3);">{{ currentMaskedKey }}</code>
              </div>
              <div style="margin-top:20px; font-size:13px; color:#f87171;" v-else>
                ⚠️ <strong>Máy chủ đang dùng Key nội bộ!</strong> Nếu nó hết hạn, hệ thống phân tích sẽ tê liệt. Hãy nhập Key riêng của bạn để làm chủ tốc độ.
              </div>
            </div>
          </div>

          <!-- TAB: FILE MANAGER (INGESTION) -->
          <div v-if="adminTab === 'files'" class="admin-scroll-area file-manager">
            <div class="fm-toolbar">
              <label class="fm-label">Thư Mục Dữ Liệu (Category):</label>
              <select v-model="selectedCategory" @change="fetchIngestionStatus" class="fm-select">
                <option value="kinh-thanh">Kinh Thánh (Chuẩn)</option>
                <option value="kinh-thanh-giai-nghia">Giải Nghĩa Kinh Thánh (Wiersbe)</option>
                <option value="duong-linh">Tài Liệu Dưỡng Linh</option>
              </select>
              <button @click="fetchIngestionStatus" class="fm-refresh-btn" :disabled="adminLoading">↻ Làm Mới</button>
              <button @click="ingestAllPending" class="fm-ingest-all-btn btn-purple" :disabled="adminLoading || pendingFilesCount === 0">🚀 Chạy AI {{ pendingFilesCount }} File Tồn Đọng</button>
              <button @click="runWorkQueue" class="fm-refresh-btn" style="background: rgba(239,68,68,0.2); color: #fca5a5; border-color: rgba(239,68,68,0.3); font-weight: 900;" :disabled="adminLoading" title="Bơm 2 File đang kẹt ĐANG XỬ LÝ (QUEUE) vào chạy ngay">⚡ Khởi Động AI Ngầm (Xử lý kẹt Queue)</button>
            </div>

            <div class="fm-table-wrap">
              <table class="fm-table">
                <thead>
                  <tr>
                    <th>Tên File Txt <span style="font-weight: normal; font-size: 9px; text-transform:none">(Xem bằng Tab Kinh Thánh cột phải)</span></th>
                    <th>Trạng Thái</th>
                    <th>Thực Thể AI (Nodes/Edges)</th>
                    <th>Tiến Độ Chunk</th>
                    <th>Hành Động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="ingestionFiles.length === 0">
                    <td colspan="5" style="text-align:center; padding: 20px;">Thư mục <code>tai-lieu/{{selectedCategory}}</code> trống. Hãy ném file văn bản txt vào đây.</td>
                  </tr>
                  <tr v-for="cf in ingestionFiles" :key="cf.file_name">
                    <td class="col-name">{{ cf.file_name }}.txt</td>
                    <td>
                      <span :class="['status-badge', cf.status]">{{ formatStatus(cf.status) }}</span>
                    </td>
                    <td class="col-stats">
                      <span v-if="cf.status === 'completed'" class="stat-node">N: {{ cf.nodes_added }}</span>
                      <span v-if="cf.status === 'completed'" class="stat-edge">E: {{ cf.edges_added }}</span>
                      <span v-if="cf.status !== 'completed'" style="color:#64748b">-</span>
                    </td>
                    <td class="col-progress">
                      <div class="progress-bar-bg" v-if="cf.total_chunks > 0">
                        <div class="progress-bar-fill" :style="`width: ${(cf.processed_chunks / cf.total_chunks) * 100}%`"></div>
                      </div>
                      <span class="progress-text" v-if="cf.total_chunks > 0">{{ cf.processed_chunks }}/{{ cf.total_chunks }}</span>
                      <span v-else style="color:#64748b">-</span>
                    </td>
                    <td>
                      <button v-if="cf.status === 'pending' || cf.status === 'changed' || cf.status === 'failed'"
                              @click="ingestSingleFile(cf.file_name)"
                              :disabled="adminLoading"
                              class="fm-action-btn btn-purple">
                        Bắt đầu Nạp
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p class="fm-note">💡 Ghi chú: Hãy mở Terminal chạy <code>php artisan queue:work</code> để Tiến trình AI hoạt động ngầm (Push Jobs xử lý từ từ chống bị Google chặn).</p>
          </div>

        </div>
      </div>
    </transition>

    <!-- Loading -->
    <transition name="fade">
      <div v-if="loading" class="cosmos-loading">
        <div class="loader-ring"></div>
        <p class="loader-text">Đang tải Vũ Trụ Kinh Thánh...</p>
      </div>
    </transition>

    <!-- Corner Badge -->
    <div class="corner-badge">
      <span :style="`color:${viewModes.find(v=>v.id===currentView)?.color}`">
        {{ viewModes.find(v=>v.id===currentView)?.icon }} {{ viewModes.find(v=>v.id===currentView)?.label }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const networkContainer = ref(null)
const starCanvas       = ref(null)
const loading          = ref(true)
const allNodes         = ref([])
const allEdges         = ref([])
const selectedNode     = ref(null)
const searchQuery      = ref('')
const currentView      = ref('galaxy')
const showSidebar      = ref(window.innerWidth > 768)
const showSettings     = ref(false)
const showParser       = ref(false)
const adminTab         = ref('files')
const adminLoading     = ref(false)
const adminMsg         = ref('')
const adminError       = ref(false)
const selectedCategory = ref('kinh-thanh')
const ingestionFiles   = ref([])
const pendingFilesCount= ref(0)
const parseText        = ref('')
const parseResult      = ref(null)
const parseError       = ref('')
const parsingAI        = ref(false)
const neighbors        = ref({})
const totalNeighbors   = ref(0)
const loadingNeighbors = ref(false)

const panelTab         = ref('links')
const bibleBook        = ref('')
const bibleChapter     = ref(1)
const bibleData        = ref(null)
const bibleError       = ref('')
const loadingBible     = ref(false)
const commentaryBook   = ref('')
const commentaryData   = ref(null)
const commentaryError  = ref('')
const commentaryPage   = ref(1)
const loadingCommentary = ref(false)

// API Key Setting
const geminiApiKeyInput = ref('')
const currentMaskedKey  = ref('')

let networkInstance = null
let nodesDataset    = null
let edgesDataset    = null

const viewModes = [
  { id: 'galaxy',      icon: '🌌', label: 'Vũ Trụ',     color: '#818cf8' },
  { id: 'timeline',    icon: '📜', label: 'Lịch Sử',    color: '#34d399' },
  { id: 'christology', icon: '✝️', label: 'Đấng Christ', color: '#f87171' },
]

const groups = [
  { id: 'testament', label: 'Giao Ước',    color: '#ef4444', icon: '⚔️' },
  { id: 'category',  label: 'Phân Loại',   color: '#f97316', icon: '📂' },
  { id: 'book_ot',   label: 'Cựu Ước',     color: '#14b8a6', icon: '📜' },
  { id: 'book_nt',   label: 'Tân Ước',     color: '#3b82f6', icon: '✝️' },
  { id: 'person',    label: 'Nhân Vật',    color: '#f59e0b', icon: '👤' },
  { id: 'place',     label: 'Địa Danh',    color: '#8b5cf6', icon: '📍' },
  { id: 'event',     label: 'Sự Kiện',     color: '#ec4899', icon: '⚡' },
  { id: 'concept',   label: 'Khái Niệm',   color: '#a78bfa', icon: '💡' },
]

const activeGroups = ref(groups.map(g => g.id))
const christologyIds = new Set([9031, 9055, 9034, 9035, 9041, 9042, 9053, 9054, 23, 40, 43, 66])

const groupColor = (g) => groups.find(x => x.id === g)?.color || '#9ca3af'
const groupLabel = (g) => groups.find(x => x.id === g)?.label || g
const groupIcon  = (g) => groups.find(x => x.id === g)?.icon  || '◉'
const nodeCountByGroup = (g) => allNodes.value.filter(n => n.group === g).length

const buildVisNode = (n, overrideColor = null) => {
  const c = overrideColor || groupColor(n.group)
  const size = sizeByGroup(n.group)
  return {
    id: n.id, label: n.label, title: n.title, group: n.group,
    color: { background: c, border: shadeColor(c, 40), highlight: { background: '#fff', border: c }, hover: { background: shadeColor(c, -20), border: '#fff' } },
    font: { color: '#fff', size, face: 'Inter, sans-serif' },
    shape: 'box',
    margin: { top: 10, bottom: 10, left: 14, right: 14 },
    borderWidth: 2,
    shadow: { enabled: true, color: c + '88', size: 16, x: 0, y: 0 },
    widthConstraint: { maximum: 200 }
  }
}

const sizeByGroup = (g) => ({ testament: 22, category: 18, book_ot: 15, book_nt: 15 }[g] ?? 13)
const shadeColor  = (hex, p) => {
  const n = parseInt(hex.slice(1), 16)
  const r = Math.min(255, Math.max(0, (n >> 16) + p))
  const g = Math.min(255, Math.max(0, ((n >> 8) & 255) + p))
  const b = Math.min(255, Math.max(0, (n & 255) + p))
  return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1)
}

const getPhysicsOptions = (view) => {
  if (view === 'timeline') return { enabled: false }
  return {
    solver: 'forceAtlas2Based',
    forceAtlas2Based: { gravitationalConstant: -120, centralGravity: 0.005, springLength: 180, springConstant: 0.06, damping: 0.4 },
    maxVelocity: 60, stabilization: { iterations: 200 }
  }
}

const fetchAndDraw = async () => {
  const res = await axios.get('/api/graph')
  allNodes.value = res.data.nodes
  allEdges.value = res.data.edges
  drawGraph('galaxy')
}

const drawGraph = (view) => {
  if (typeof vis === 'undefined') return

  let nodesToShow = [...allNodes.value]
  let edgesToShow = [...allEdges.value]

  if (view === 'christology') {
    nodesToShow = nodesToShow.filter(n => christologyIds.has(n.id))
    const ids = new Set(nodesToShow.map(n => n.id))
    edgesToShow = edgesToShow.filter(e => ids.has(e.from) && ids.has(e.to))
  }

  const parsedNodes = nodesToShow
    .filter(n => activeGroups.value.includes(n.group))
    .map(n => {
      const vn = buildVisNode(n)
      if (view === 'timeline' && n.order) {
        vn.x = (n.order - 1) * 220 - 4000
        vn.y = typeToY(n.group)
        vn.fixed = { x: true, y: true }
      }
      return vn
    })

  const nodeIds = new Set(parsedNodes.map(n => n.id))
  const parsedEdges = edgesToShow
    .filter(e => nodeIds.has(e.from) && nodeIds.has(e.to))
    .map(e => ({
      id: e.id, from: e.from, to: e.to, label: e.label,
      arrows: { to: { enabled: true, scaleFactor: 0.7 } },
      font: { size: 10, color: '#94a3b8', face: 'Inter', align: 'middle', background: 'rgba(3,6,20,0.7)' },
      color: { color: 'rgba(148,163,184,0.25)', highlight: '#818cf8', hover: '#818cf8' },
      smooth: { type: 'curvedCW', roundness: 0.15 }
    }))

  const graphData = { nodes: new vis.DataSet(parsedNodes), edges: new vis.DataSet(parsedEdges) }
  nodesDataset = graphData.nodes
  edgesDataset = graphData.edges

  const options = {
    physics: getPhysicsOptions(view),
    interaction: { hover: true, tooltipDelay: 150, navigationButtons: false },
    layout: { improvedLayout: view !== 'timeline' }
  }

  if (networkInstance) {
    networkInstance.setData(graphData)
    networkInstance.setOptions(options)
  } else {
    networkInstance = new vis.Network(networkContainer.value, graphData, options)
    networkInstance.on('click', async (params) => {
      if (params.nodes.length > 0) {
        const id = params.nodes[0]
        selectedNode.value = allNodes.value.find(n => n.id === id) || null
        await fetchNeighbors(id)
      } else {
        closePanel()
      }
    })
  }
}

const typeToY = (group) => ({ testament: 0, category: 160, book_ot: 340, book_nt: 340, concept: 120, person: 520, place: 700, event: 880 }[group] ?? 600)

const fetchNeighbors = async (nodeId) => {
  loadingNeighbors.value = true
  neighbors.value = {}
  totalNeighbors.value = 0
  try {
    const res = await axios.get(`/api/graph/neighbors/${nodeId}`)
    neighbors.value = res.data.neighbors || {}
    totalNeighbors.value = res.data.total || 0
  } catch (e) {
    console.error('fetchNeighbors error', e)
  } finally {
    loadingNeighbors.value = false
  }
}

// ── AI Text Parser ──
const runParseText = async () => {
  if (!parseText.value.trim() || parsingAI.value) return
  parsingAI.value = true
  parseError.value = ''
  parseResult.value = null

  try {
    const res = await axios.post('/api/graph/parse-text', { text: parseText.value })
    parseResult.value = res.data
  } catch (e) {
    parseError.value = e.response?.data?.error || 'Có lỗi xảy ra. Hãy thử lại.'
  } finally {
    parsingAI.value = false
  }
}

const injectIntoGraph = () => {
  if (!parseResult.value || !nodesDataset) return

  const newNodes = parseResult.value.nodes.map(n => buildVisNode({ ...n, title: n.description || '' }, null))
  const existingIds = new Set(nodesDataset.getIds())

  newNodes.forEach(n => {
    if (!existingIds.has(n.id)) {
      nodesDataset.add(n)
      allNodes.value.push({ id: n.id, label: n.label, group: n.group, title: n.title || '' })
    }
  })

  const existingEdgeIds = new Set(edgesDataset.getIds())
  parseResult.value.edges.forEach((e, i) => {
    const tempId = 'ai_' + Date.now() + '_' + i
    if (!existingEdgeIds.has(tempId)) {
      edgesDataset.add({
        id: tempId, from: e.from, to: e.to, label: e.label,
        arrows: { to: { enabled: true, scaleFactor: 0.7 } },
        color: { color: '#a78bfa44', highlight: '#a78bfa', hover: '#a78bfa' },
        font: { size: 10, color: '#a78bfa', background: 'rgba(3,6,20,0.7)' },
        smooth: { type: 'curvedCW', roundness: 0.2 }
      })
      allEdges.value.push(e)
    }
  })

  // Focus on newly injected nodes
  if (parseResult.value.nodes.length > 0) {
    networkInstance?.focus(parseResult.value.nodes[0].id, { scale: 1.5, animation: { duration: 800, easingFunction: 'easeInOutQuad' } })
  }

  showParser.value = false
  parseResult.value = null
  parseText.value = ''
}

const switchView  = (view) => { currentView.value = view; selectedNode.value = null; drawGraph(view) }
const applyFilter = () => drawGraph(currentView.value)
const focusNode   = (id) => { networkInstance?.focus(id, { scale: 2, animation: { duration: 800, easingFunction: 'easeInOutQuad' } }); networkInstance?.selectNodes([id]) }
const jumpToNode  = (id) => { selectedNode.value = allNodes.value.find(n => n.id === id) || null; focusNode(id); fetchNeighbors(id) }
const closePanel  = () => { selectedNode.value = null; neighbors.value = {}; panelTab.value = 'links'; bibleData.value = null; commentaryData.value = null }
const closeAll    = () => { closePanel(); showParser.value = false; showSettings.value = false }

// ── Settings Modal System ──
const runAdminAction = async (action) => {
  adminLoading.value = true
  adminMsg.value = 'Đang xử lý xin vui lòng chờ...'
  adminError.value = false
  try {
    const res = await axios.post(`/api/graph/admin/${action}`)
    adminMsg.value = '✅ ' + (res.data.message || 'Thành công!')
    if (action === 'reset' || action === 'import') {
      setTimeout(() => fetchAndDraw(), 1500)
    }
  } catch (e) {
    adminError.value = true
    adminMsg.value = '⚠️ Lỗi: ' + (e.response?.data?.message || e.message)
  } finally {
    adminLoading.value = false
  }
}

const fetchIngestionStatus = async () => {
  adminLoading.value = true
  adminMsg.value = ''
  adminError.value = false
  try {
    const res = await axios.get('/api/graph/admin/ingestion-status', { params: { category: selectedCategory.value } })
    ingestionFiles.value = res.data.files || []
    pendingFilesCount.value = ingestionFiles.value.filter(f => f.status === 'pending' || f.status === 'changed' || f.status === 'failed').length
  } catch (e) {
    adminError.value = true
    adminMsg.value = '⚠️ Không lấy được thông tin thư mục: ' + e.message
  } finally {
    adminLoading.value = false
  }
}

const ingestSingleFile = async (filename) => {
  adminLoading.value = true
  adminMsg.value = 'Đang đẩy file ' + filename + ' vào hàng đợi...'
  adminError.value = false
  try {
    const res = await axios.post('/api/graph/admin/ingest-single', {
      category: selectedCategory.value,
      filename: filename
    })
    adminMsg.value = '✅ ' + (res.data.message || 'Đã thêm file vào Queue!')
    setTimeout(() => fetchIngestionStatus(), 1000)
  } catch (e) {
    adminError.value = true
    adminMsg.value = '⚠️ Lỗi Nạp Dữ Liệu: ' + (e.response?.data?.message || e.message)
  } finally {
    adminLoading.value = false
  }
}

const runWorkQueue = async () => {
  adminLoading.value = true
  adminMsg.value = 'Đang kích hoạt Worker đẩy luồng chạy ngầm cho 3 Jobs bị kẹt...'
  adminError.value = false
  try {
    const res = await axios.get('/api/graph/admin/work-queue') // Sử dụng GET Method do đã đổi thành any
    adminMsg.value = '⚡ ' + (res.data.message || 'Worker đã chạy xong 3 File!')
    setTimeout(() => fetchIngestionStatus(), 1000)
  } catch (e) {
    adminError.value = true
    adminMsg.value = '⚠️ Lỗi Kích Hoạt Worker: ' + (e.response?.data?.message || e.message)
  } finally {
    adminLoading.value = false
  }
}

const ingestAllPending = async () => {
  adminLoading.value = true
  adminMsg.value = 'Đang rải tất cả tài liệu vào hàng đợi (10-15 câu / 1 chunk)...'
  adminError.value = false
  try {
    const res = await axios.post('/api/graph/admin/ingest', { category: selectedCategory.value })
    adminMsg.value = '✅ ' + (res.data.message || 'Thành công!')
    setTimeout(() => fetchIngestionStatus(), 1500)
  } catch (e) {
    adminError.value = true
    adminMsg.value = '⚠️ Lỗi: ' + (e.response?.data?.message || e.message)
  } finally {
    adminLoading.value = false
  }
}

const formatStatus = (s) => {
  if (s === 'completed') return 'ĐÃ NẠP (OK)'
  if (s === 'pending') return 'CHƯA NẠP'
  if (s === 'processing') return 'ĐANG XỬ LÝ (QUEUE)'
  if (s === 'changed') return 'ĐÃ BỊ SỬA (LỆCH HASH)'
  if (s === 'failed') return 'LỖI AI (THẤT BẠI)'
  return s
}

import { watch } from 'vue'
watch(showSettings, (val) => {
  if (val && adminTab.value === 'files') {
    fetchIngestionStatus()
  }
  if (val && adminTab.value === 'settings') {
    fetchApiKey()
  }
})
watch(adminTab, (val) => {
  if (val === 'files') fetchIngestionStatus()
  if (val === 'settings') fetchApiKey()
})

// Switch panel tab and auto-populate book name from node
const switchPanelTab = (tab) => {
  panelTab.value = tab
  if (selectedNode.value && tab === 'bible' && !bibleBook.value) {
    // auto-fill from node label
    bibleBook.value = selectedNode.value.label
  }
  if (selectedNode.value && tab === 'commentary' && !commentaryBook.value) {
    commentaryBook.value = selectedNode.value.label
  }
}

// ── Bible Text API ──
const fetchBibleText = async () => {
  if (!bibleBook.value.trim() || loadingBible.value) return
  loadingBible.value = true
  bibleError.value = ''
  bibleData.value = null
  try {
    const res = await axios.get('/api/bible/text', {
      params: { book: bibleBook.value.trim(), chapter: bibleChapter.value }
    })
    bibleData.value = res.data
  } catch (e) {
    bibleError.value = e.response?.data?.error || 'Không tìm thấy. Kiểm tra tên sách và số chương.'
  } finally {
    loadingBible.value = false
  }
}

const navigateChapter = (delta) => {
  const next = bibleChapter.value + delta
  if (next >= 1) { bibleChapter.value = next; fetchBibleText() }
}

// ── Commentary API ──
const fetchCommentary = async (page = 1) => {
  if (!commentaryBook.value.trim() || loadingCommentary.value) return
  loadingCommentary.value = true
  commentaryError.value = ''
  commentaryPage.value = page
  try {
    const res = await axios.get('/api/bible/commentary', {
      params: { book: commentaryBook.value.trim(), page }
    })
    commentaryData.value = res.data
  } catch (e) {
    commentaryError.value = e.response?.data?.error || 'Không tìm thấy giải nghĩa cho sách này.'
    commentaryData.value = null
  } finally {
    loadingCommentary.value = false
  }
}

let searchTimeout = null
const onSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    if (!nodesDataset || !networkInstance) return
    const q = searchQuery.value.toLowerCase()
    nodesDataset.forEach(n => {
      const match = !q || n.label.toLowerCase().includes(q)
      nodesDataset.update({ id: n.id, opacity: match ? 1 : 0.1 })
      if (match && q) networkInstance.focus(n.id, { scale: 1.5, animation: { duration: 500, easingFunction: 'easeInOutQuad' } })
    })
  }, 300)
}

// ── Star Canvas ──
const drawStars = () => {
  const canvas = starCanvas.value
  if (!canvas) return
  const ctx = canvas.getContext('2d')
  canvas.width = window.innerWidth; canvas.height = window.innerHeight
  const stars = Array.from({ length: 280 }, () => ({ x: Math.random() * canvas.width, y: Math.random() * canvas.height, r: Math.random() * 1.6 + 0.2, o: Math.random() * 0.7 + 0.2, d: Math.random() * 0.004 + 0.001, t: Math.random() * Math.PI * 2 }))
  const animate = () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height)
    stars.forEach(s => { s.t += s.d; const a = s.o * (0.6 + 0.4 * Math.sin(s.t)); ctx.beginPath(); ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2); ctx.fillStyle = `rgba(200,215,255,${a})`; ctx.fill() })
    requestAnimationFrame(animate)
  }
  animate()
  window.addEventListener('resize', () => { canvas.width = window.innerWidth; canvas.height = window.innerHeight })
}

onMounted(async () => {
  drawStars()
  try { await fetchAndDraw() } catch (e) { console.error(e) } finally { loading.value = false }
})
</script>

<style scoped>
/* Base */
.cosmos-root { position: relative; width: 100%; height: 100vh; background: #030614; overflow: hidden; font-family: 'Inter', sans-serif; color: #e2e8f0; }
.star-bg { position: absolute; inset: 0; z-index: 0; pointer-events: none; }

/* Header */
.cosmos-header { position: absolute; top: 0; left: 0; right: 0; z-index: 30; height: 64px; display: flex; align-items: center; justify-content: space-between; padding: 0 20px 0 16px; background: linear-gradient(to bottom, rgba(3,6,20,0.96), transparent); border-bottom: 1px solid rgba(255,255,255,0.06); backdrop-filter: blur(12px); gap: 12px; }
.header-left { display: flex; align-items: center; gap: 14px; flex-shrink: 0; }
.header-center { flex: 1; display: flex; justify-content: center; }
.header-right { flex-shrink: 0; }
.back-btn { display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: #94a3b8; transition: all .2s; text-decoration: none; }
.back-btn:hover { background: rgba(255,255,255,0.14); color: #fff; }
.cosmos-title { font-size: 1.15rem; font-weight: 900; background: linear-gradient(135deg, #a5b4fc 0%, #f0abfc 60%, #facc15 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0; }
.cosmos-subtitle { font-size: 11px; color: #475569; margin: 2px 0 0; }
.mobile-menu-btn { display: none; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; width: 36px; height: 36px; font-size: 18px; cursor: pointer; transition: 0.2s; align-items: center; justify-content: center; }
.settings-btn { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #cbd5e1; width: 38px; height: 38px; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.settings-btn:hover { background: rgba(255,255,255,0.15); transform: rotate(45deg); }
.header-right { flex-shrink: 0; display: flex; gap: 8px; }

/* View Switcher */
.view-switcher { display: flex; gap: 6px; }
.view-btn { display: flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.04); color: #64748b; font-size: 12px; font-weight: 700; cursor: pointer; transition: all .2s; }
.view-btn:hover { background: rgba(255,255,255,0.1); color: #e2e8f0; }
.view-btn.active { background: rgba(129,140,248,0.2); color: #a5b4fc; border-color: rgba(129,140,248,0.4); }
.view-label { display: none; }
@media (min-width: 768px) { .view-label { display: inline; } }

/* AI Parse Button */
.ai-parse-btn { display: flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 12px; background: linear-gradient(135deg, rgba(167,139,250,0.25), rgba(245,158,11,0.2)); border: 1px solid rgba(167,139,250,0.4); color: #c4b5fd; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .25s; }
.ai-parse-btn:hover { background: linear-gradient(135deg, rgba(167,139,250,0.4), rgba(245,158,11,0.35)); box-shadow: 0 0 20px rgba(167,139,250,0.2); }

/* Left Sidebar */
.filter-sidebar { position: absolute; top: 64px; left: 0; bottom: 0; width: 210px; z-index: 20; background: rgba(3,6,20,0.85); border-right: 1px solid rgba(255,255,255,0.07); backdrop-filter: blur(16px); padding: 16px 12px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px; transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); transform: translateX(-100%); }
.filter-sidebar.is-open { transform: translateX(0); }
.sidebar-title { font-size: 13px; font-weight: 800; letter-spacing: 0.05em; color: #94a3b8; margin: 0; }
.search-wrap { position: relative; }
.search-input { width: 100%; padding: 8px 12px 8px 32px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #e2e8f0; font-size: 13px; outline: none; transition: border-color .2s; box-sizing: border-box; }
.search-input::placeholder { color: #475569; }
.search-input:focus { border-color: #818cf8; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #475569; font-size: 15px; }
.filter-label { font-size: 9px; font-weight: 800; letter-spacing: 0.1em; color: #334155; text-transform: uppercase; margin: 0 0 6px; }
.filter-row { display: flex; align-items: center; gap: 8px; color: #94a3b8; font-size: 12px; cursor: pointer; padding: 3px 0; transition: color .15s; }
.filter-row:hover { color: #e2e8f0; }
.filter-row input { display: none; }
.filter-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; background: var(--gc); box-shadow: 0 0 6px var(--gc); transition: transform .2s; }
.filter-row input:not(:checked) + .filter-dot { opacity: 0.3; }
.filter-count { margin-left: auto; font-size: 10px; color: #334155; background: rgba(255,255,255,0.05); border-radius: 8px; padding: 1px 6px; }
.legend-row { display: flex; align-items: center; gap: 8px; font-size: 11px; color: #64748b; padding: 2px 0; }
.legend-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* Graph Area */
.graph-area { position: absolute; top: 64px; left: 210px; right: 0; bottom: 0; z-index: 10; cursor: grab; }
.graph-area:active { cursor: grabbing; }

/* Right Detail Panel */
.detail-panel { position: absolute; top: 64px; right: 0; width: 320px; bottom: 0; z-index: 25; background: rgba(3,6,20,0.95); border-left: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(20px); padding: 20px 18px; display: flex; flex-direction: column; gap: 12px; overflow-y: auto; }
.panel-slide-enter-active, .panel-slide-leave-active { transition: transform .3s cubic-bezier(0.4,0,0.2,1), opacity .3s; }
.panel-slide-enter-from, .panel-slide-leave-to { transform: translateX(100%); opacity: 0; }
.panel-header { display: flex; align-items: center; justify-content: space-between; }
.panel-group-badge { padding: 3px 10px; border-radius: 999px; font-size: 10px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #fff; }
.close-btn { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #64748b; width: 28px; height: 28px; cursor: pointer; font-size: 12px; transition: all .2s; }
.close-btn:hover { background: rgba(239,68,68,0.15); color: #f87171; }
.panel-title { font-size: 1.25rem; font-weight: 900; color: #f1f5f9; margin: 0; line-height: 1.3; }
.panel-desc { font-size: 13px; line-height: 1.65; color: #94a3b8; }
:deep(.panel-desc a) { color: #818cf8; text-decoration: underline; }
.panel-stats { display: flex; flex-wrap: wrap; gap: 6px; }
.stat-chip { font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 8px; background: rgba(255,255,255,0.06); color: #64748b; }

/* Neighbor Groups */
.nb-loading { font-size: 12px; color: #475569; text-align: center; padding: 16px; }
.neighbor-groups { display: flex; flex-direction: column; gap: 12px; }
.nb-group-title { font-size: 10px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; margin: 0 0 6px; }
.nb-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); border-radius: 10px; padding: 8px 10px; cursor: pointer; transition: all .2s; }
.nb-card:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.15); transform: translateX(2px); }
.nb-relation { font-size: 9px; color: #475569; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 2px; }
.nb-label { font-size: 13px; color: #e2e8f0; font-weight: 700; }

.focus-btn { margin-top: auto; padding: 10px; border-radius: 12px; font-size: 13px; font-weight: 700; background: linear-gradient(135deg, rgba(129,140,248,0.2), rgba(168,85,247,0.2)); border: 1px solid rgba(129,140,248,0.3); color: #a5b4fc; cursor: pointer; transition: all .2s; }
.focus-btn:hover { background: linear-gradient(135deg, rgba(129,140,248,0.35), rgba(168,85,247,0.35)); }

/* AI Parser Modal */
.modal-overlay { position: fixed; inset: 0; z-index: 100; background: rgba(3,6,20,0.8); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; padding: 20px; }
.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity .3s; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
.parser-modal { background: #0d1225; border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; width: 100%; max-width: 640px; padding: 28px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 25px 60px rgba(0,0,0,0.6); }
.modal-header { display: flex; justify-content: space-between; align-items: flex-start; }
.modal-title { font-size: 1.4rem; font-weight: 900; background: linear-gradient(135deg, #a5b4fc, #f0abfc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0; }
.modal-sub { font-size: 12px; color: #64748b; margin: 4px 0 0; }
.parse-textarea { width: 100%; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #e2e8f0; padding: 14px; font-size: 13px; line-height: 1.6; outline: none; resize: vertical; font-family: inherit; transition: border-color .2s; box-sizing: border-box; }
.parse-textarea::placeholder { color: #334155; }
.parse-textarea:focus { border-color: #818cf8; }
.parse-error { color: #f87171; font-size: 12px; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); border-radius: 8px; padding: 8px 12px; }
.modal-actions { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.parse-info { font-size: 12px; color: #475569; display: flex; gap: 6px; flex: 1; align-items: flex-start; }
.info-dot { flex-shrink: 0; }
.parse-btn { display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px; background: linear-gradient(135deg, #4f46e5, #7c3aed); border: none; color: #fff; font-size: 14px; font-weight: 800; cursor: pointer; transition: all .25s; white-space: nowrap; }
.parse-btn:hover:not(:disabled) { background: linear-gradient(135deg, #4338ca, #6d28d9); box-shadow: 0 0 25px rgba(79,70,229,0.4); }
.parse-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-spinner { width: 14px; height: 14px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Parse Result */
.parse-result { background: rgba(167,139,250,0.06); border: 1px solid rgba(167,139,250,0.2); border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 10px; }
.result-header { display: flex; align-items: center; gap: 10px; }
.result-badge { font-size: 12px; font-weight: 800; color: #a78bfa; background: rgba(167,139,250,0.15); padding: 4px 10px; border-radius: 8px; }
.result-summary { font-size: 13px; color: #94a3b8; margin: 0; }
.result-nodes { display: flex; flex-wrap: wrap; gap: 6px; }
.result-node-chip { font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 8px; border: 1px solid; background: transparent; }
.inject-btn { padding: 10px; border-radius: 12px; font-size: 13px; font-weight: 800; background: linear-gradient(135deg, rgba(167,139,250,0.3), rgba(245,158,11,0.25)); border: 1px solid rgba(167,139,250,0.4); color: #c4b5fd; cursor: pointer; transition: all .2s; }
.inject-btn:hover { background: linear-gradient(135deg, rgba(167,139,250,0.5), rgba(245,158,11,0.4)); box-shadow: 0 0 20px rgba(167,139,250,0.3); }

/* Loading */
.cosmos-loading { position: absolute; inset: 0; z-index: 50; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; background: rgba(3,6,20,0.9); backdrop-filter: blur(12px); }
.loader-ring { width: 56px; height: 56px; border-radius: 50%; border: 3px solid rgba(165,180,252,0.15); border-top-color: #a5b4fc; animation: spin 1s linear infinite; }
.loader-text { color: #64748b; font-size: 13px; font-weight: 600; }
.fade-enter-active, .fade-leave-active { transition: opacity .5s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Corner Badge */
.corner-badge { position: absolute; bottom: 16px; right: 16px; z-index: 20; background: rgba(3,6,20,0.8); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 6px 12px; font-size: 12px; font-weight: 700; backdrop-filter: blur(8px); }

/* ── Panel Tabs ── */
.panel-tabs { display: flex; gap: 4px; border-bottom: 1px solid rgba(255,255,255,0.07); padding-bottom: 10px; margin-bottom: 4px; }
.panel-tab { flex: 1; padding: 7px 6px; border-radius: 10px; border: 1px solid transparent; background: rgba(255,255,255,0.04); color: #475569; font-size: 11px; font-weight: 700; cursor: pointer; transition: all .2s; white-space: nowrap; }
.panel-tab:hover { background: rgba(255,255,255,0.08); color: #94a3b8; }
.panel-tab.active { background: rgba(129,140,248,0.18); border-color: rgba(129,140,248,0.35); color: #a5b4fc; }

/* ── Bible Tab ── */
.bible-tab, .commentary-tab { display: flex; flex-direction: column; gap: 10px; }
.bible-nav, .commentary-nav { display: flex; flex-direction: column; gap: 6px; }
.bible-input { width: 100%; padding: 8px 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #e2e8f0; font-size: 13px; outline: none; transition: border-color .2s; box-sizing: border-box; font-family: inherit; }
.bible-input::placeholder { color: #334155; }
.bible-input:focus { border-color: #818cf8; }
.bible-chapter-nav { display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 4px 6px; }
.chap-btn { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 7px; color: #94a3b8; width: 26px; height: 26px; cursor: pointer; font-size: 16px; font-weight: 700; transition: all .2s; line-height: 1; }
.chap-btn:hover:not(:disabled) { background: rgba(129,140,248,0.2); color: #a5b4fc; }
.chap-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.chap-num { font-size: 12px; font-weight: 800; color: #a5b4fc; }
.bible-load-btn { padding: 8px 14px; border-radius: 10px; background: linear-gradient(135deg, rgba(129,140,248,0.25), rgba(168,85,247,0.2)); border: 1px solid rgba(129,140,248,0.3); color: #a5b4fc; font-size: 12px; font-weight: 800; cursor: pointer; transition: all .2s; }
.bible-load-btn:hover:not(:disabled) { background: linear-gradient(135deg, rgba(129,140,248,0.4), rgba(168,85,247,0.35)); }
.bible-load-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.bible-error { color: #f87171; font-size: 12px; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); border-radius: 8px; padding: 8px 12px; }
.bible-content { max-height: 420px; overflow-y: auto; display: flex; flex-direction: column; gap: 0; scrollbar-width: thin; scrollbar-color: #1e293b transparent; }
.bible-title { font-size: 14px; font-weight: 900; color: #a5b4fc; padding: 8px 0 10px; border-bottom: 1px solid rgba(255,255,255,0.07); margin-bottom: 6px; }
.bible-verse { display: flex; gap: 8px; padding: 5px 0; border-bottom: 1px solid rgba(255,255,255,0.04); line-height: 1.65; }
.bible-verse:hover { background: rgba(255,255,255,0.03); border-radius: 6px; }
.verse-num { font-size: 10px; font-weight: 900; color: #4f46e5; min-width: 20px; padding-top: 2px; flex-shrink: 0; }
.verse-text { font-size: 13px; color: #cbd5e1; line-height: 1.7; }
.bible-hint { text-align: center; padding: 16px; color: #475569; font-size: 13px; line-height: 1.7; }
.hint-sub { font-size: 12px; color: #334155; margin-top: 4px; }
.nb-empty { font-size: 12px; color: #334155; text-align: center; padding: 12px; }

/* ── Commentary Tab ── */
.commentary-content { display: flex; flex-direction: column; gap: 10px; }
.commentary-source { font-size: 10px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #f59e0b; background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2); border-radius: 8px; padding: 4px 10px; display: inline-block; }
.commentary-text { font-size: 13px; color: #94a3b8; line-height: 1.75; max-height: 380px; overflow-y: auto; white-space: pre-line; scrollbar-width: thin; scrollbar-color: #1e293b transparent; }
.commentary-pagination { display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.07); padding-top: 8px; }
.page-btn { padding: 6px 12px; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #64748b; font-size: 12px; font-weight: 700; cursor: pointer; transition: all .2s; }
.page-btn:hover:not(:disabled) { background: rgba(129,140,248,0.15); color: #a5b4fc; }
.page-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.page-info { font-size: 11px; color: #475569; font-weight: 700; }

/* Admin Framework */
.admin-scroll-area { flex: 1; overflow-y: auto; padding-right: 4px; display: flex; flex-direction: column; gap: 16px; scrollbar-width: thin; scrollbar-color: #334155 transparent; }
.admin-tabs { display: flex; gap: 8px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 12px; margin-bottom: 4px; flex-wrap: wrap; }
.admin-tab { padding: 10px 16px; border-radius: 12px; border: 1px solid transparent; background: rgba(255,255,255,0.04); color: #64748b; font-size: 13px; font-weight: 800; cursor: pointer; transition: all .2s; }
.admin-tab:hover { background: rgba(255,255,255,0.08); color: #94a3b8; }
.admin-tab.active { background: rgba(59,130,246,0.15); border-color: rgba(59,130,246,0.3); color: #93c5fd; }

/* FileManager Dashboard */
.file-manager { display: flex; flex-direction: column; gap: 16px; }
.fm-toolbar { display: flex; align-items: center; gap: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); padding: 12px; border-radius: 12px; flex-wrap: wrap; }
.fm-label { font-size: 13px; font-weight: 700; color: #94a3b8; }
.fm-select { background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: #e2e8f0; border-radius: 8px; padding: 8px 12px; font-size: 13px; outline: none; transition: 0.2s; font-family: inherit; }
.fm-select:focus { border-color: #818cf8; }
.fm-refresh-btn { padding: 8px 14px; border-radius: 8px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #e2e8f0; font-size: 12px; font-weight: 800; cursor: pointer; transition: 0.2s; }
.fm-refresh-btn:hover { background: rgba(255,255,255,0.15); }
.fm-ingest-all-btn { margin-left: auto; padding: 8px 16px; border-radius: 8px; font-size: 13px; }

.fm-table-wrap { background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.06); border-radius: 14px; overflow-y: auto; max-height: 50vh; scrollbar-width: thin; scrollbar-color: #334155 transparent; }
.fm-table { width: 100%; border-collapse: collapse; text-align: left; }
.fm-table th { position: sticky; top: 0; background: #080d1e; font-size: 11px; font-weight: 800; text-transform: uppercase; color: #94a3b8; padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,0.08); letter-spacing: 0.05em; z-index: 5; box-shadow: 0 4px 10px rgba(0,0,0,0.5); }
.fm-table td { padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,0.03); font-size: 13px; color: #cbd5e1; }
.fm-table tr:hover td { background: rgba(255,255,255,0.02); }
.fm-table tr:last-child td { border-bottom: none; }

.col-name { font-weight: 700; color: #e2e8f0; }
.status-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 999px; font-size: 10px; font-weight: 900; letter-spacing: 0.05em; }
.status-badge.completed { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
.status-badge.pending { background: rgba(100,116,139,0.15); color: #94a3b8; border: 1px solid rgba(100,116,139,0.3); }
.status-badge.processing { background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); }
.status-badge.changed { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); }
.status-badge.failed { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }

.stat-node, .stat-edge { display: inline-block; font-size: 11px; background: rgba(255,255,255,0.06); padding: 3px 8px; border-radius: 6px; margin-right: 4px; font-weight: 700; color: #a78bfa; }
.stat-edge { color: #f472b6; }

.progress-bar-bg { width: 100px; height: 6px; background: rgba(255,255,255,0.1); border-radius: 999px; overflow: hidden; display: inline-block; vertical-align: middle; margin-right: 6px; }
.progress-bar-fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #8b5cf6); transition: width 0.3s; }
.progress-text { font-size: 11px; font-weight: 800; color: #94a3b8; }

.fm-action-btn { padding: 6px 12px; border-radius: 6px; font-size: 11px; }
.fm-note { font-size: 12px; color: #64748b; line-height: 1.6; padding: 0 10px; margin: 0; }

/* Admin Modal Grid */
.admin-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.admin-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 8px; }
.admin-card h3 { margin: 0; font-size: 14px; color: #e2e8f0; font-weight: 800; }
.admin-card p { font-size: 12px; color: #94a3b8; line-height: 1.5; flex: 1; margin: 0; }
.admin-card code { background: rgba(0,0,0,0.4); padding: 2px 6px; border-radius: 4px; color: #a5b4fc; font-size: 11px; }
.admin-btn { padding: 10px; border-radius: 8px; border: none; font-size: 13px; font-weight: 800; color: #fff; cursor: pointer; transition: 0.2s; }
.admin-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-blue { background: rgba(59,130,246,0.2); color: #93c5fd; border: 1px solid rgba(59,130,246,0.3); } .btn-blue:hover { background: rgba(59,130,246,0.4); }
.btn-green { background: rgba(16,185,129,0.2); color: #6ee7b7; border: 1px solid rgba(16,185,129,0.3); } .btn-green:hover { background: rgba(16,185,129,0.4); }
.btn-red { background: rgba(239,68,68,0.2); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); } .btn-red:hover { background: rgba(239,68,68,0.4); }
.btn-purple { background: rgba(168,85,247,0.2); color: #d8b4fe; border: 1px solid rgba(168,85,247,0.3); } .btn-purple:hover { background: rgba(168,85,247,0.4); }
.admin-msg { margin-top: 10px; padding: 12px; border-radius: 8px; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #10b981; font-size: 13px; text-align: center; }
.admin-msg.error { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.2); color: #ef4444; }

/* Responsive Mobile */
@media (max-width: 768px) {
  .mobile-menu-btn { display: flex; }
  .header-text-wrap { display: none; }
  .view-label { display: none; }
  .ai-text { display: none; }
  .cosmos-header { padding: 0 10px; }
  .graph-area { left: 0; }
  .detail-panel { width: 100%; border-left: none; }
  .admin-grid { grid-template-columns: 1fr; }
}
@media (min-width: 769px) {
  .filter-sidebar { transform: translateX(0) !important; }
}
</style>

