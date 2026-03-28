<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ChurchTools — Bộ Công Cụ Hội Thánh</title>
    <meta name="description" content="Bộ công cụ số dành cho hội thánh: thiết kế slide, quản lý nội dung, livestream và nhiều hơn nữa.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>

<!-- NAV -->
<nav>
    <a href="/" class="logo">
        <div class="logo-icon">✝</div>
        Church<span>Tools</span>
    </a>
    <div class="nav-links">
        <a href="#tools">Công cụ</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-badge">✝ Phục Vụ Hội Thánh · {{ date('Y') }}</div>
    <h1>Bộ Công Cụ Số<br>Cho Hội Thánh</h1>
    <p>Tự động hoá thiết kế slide, quản lý nội dung phụng vụ, và phục vụ livestream — được xây dựng dành riêng cho người hầu việc Chúa.</p>
    <a href="{{ url('/ppt') }}" class="hero-btn">🎵 Dùng thử PPT Livestream ngay</a>
</section>

<!-- STATS -->
<div class="section">
    <div class="stats">
        <div>
            <div class="stat-num">10+</div>
            <div class="stat-lbl">Mẫu Banner Thiết Sẵn</div>
        </div>
        <div>
            <div class="stat-num">∞</div>
            <div class="stat-lbl">Slide Có Thể Tạo</div>
        </div>
        <div>
            <div class="stat-num">1 Click</div>
            <div class="stat-lbl">Xuất File PowerPoint</div>
        </div>
        <div>
            <div class="stat-num">LIVE</div>
            <div class="stat-lbl">Preview Trực Quan</div>
        </div>
    </div>
</div>

<!-- TOOLS -->
<div class="section" id="tools">

    <div class="section-header">
        <h2>🛠 Công Cụ Đang Hoạt Động</h2>
        <span class="pill pill-green">Sẵn sàng</span>
    </div>

    <div class="grid" style="margin-bottom:2.5rem">

        <!-- PPT Featured -->
        <a href="{{ url('/ppt') }}" class="card featured">
            <div class="card-body">
                <!-- Mini slide preview -->
                <div class="ppt-mock">
                    <div style="display:flex; flex-direction:column; width:100%; height:100%">
                        <div style="flex:1; background:#00FF00"></div>
                        <div class="mock-banner">
                            <div class="mock-logo"></div>
                            <div class="mock-text">Thánh Ca Ngài<br>Con Dâng Lên</div>
                        </div>
                    </div>
                </div>

                <div style="flex:1">
                    <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:0.6rem">
                        <div class="icon icon-teal">🎵</div>
                        <div>
                            <div class="card-title" style="font-size:1.05rem">PPT Livestream</div>
                            <div style="font-size:0.72rem; color:#4ecba6; font-weight:700">✓ Sẵn sàng sử dụng</div>
                        </div>
                    </div>
                    <p class="card-desc" style="font-size:0.83rem">
                        Tự động sinh slide PowerPoint từ lời bài hát. Hỗ trợ 10 mẫu banner đa màu sắc, chọn logo nhà thờ, tuỳ chỉnh màu nền &amp; chữ, preview trực quan WYSIWYG trước khi xuất file .pptx.
                    </p>
                    <div class="tags">
                        <span class="tag">Bulk Text</span>
                        <span class="tag">10 Templates</span>
                        <span class="tag">WYSIWYG Preview</span>
                        <span class="tag">Logo Upload</span>
                        <span class="tag">Color Picker</span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span>PowerPoint Engine · Python · Laravel</span>
                <span class="arrow">Mở ngay →</span>
            </div>
        </a>

        <!-- Sermon / Bài Giảng Live -->
        <a href="{{ url('/ppt/sermon') }}" class="card" style="border-color:rgba(212,160,23,0.2); background:linear-gradient(135deg,rgba(212,160,23,0.05) 0%,rgba(10,10,24,0.6) 100%)">
            
            <!-- Lower-third banner preview -->
            <div class="ppt-mock" style="width:100%; height:auto; aspect-ratio:21/9; background:#002a00; border-radius:10px; margin-bottom:0.2rem">
                <div style="display:flex; flex-direction:column; width:100%; height:100%">
                    <div style="flex:1; background:#00FF00;"></div>
                    <div style="height:35%; background:#0A0A18; display:flex; align-items:stretch;">
                        <div style="width:3%; background:#D4A017; flex-shrink:0;"></div>
                        <div style="flex:1; padding:4px 8px; display:flex; flex-direction:column; justify-content:center;">
                            <div style="font-size:8px; color:#FFD700; font-weight:800; font-family:Georgia;">NGUYÊN NGỮ: 'GIỐNG NHƯ'</div>
                            <div style="font-size:6px; color:#e8e8e8; margin:2px 0;">• ke·neg·dô  • ke = như</div>
                            <div style="font-size:6px; color:#FFD700;">→ Một người giúp đỡ tương xứng</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body text-sm" style="flex:1">
                <div class="icon icon-gold">📖</div>
                <div style="flex:1">
                    <div class="card-title">Bài Giảng Live</div>
                    <div style="font-size:0.72rem; color:#e8b85a; font-weight:700; margin-bottom:0.6rem">✓ Sẵn sàng sử dụng</div>
                    <p class="card-desc">Upload PDF bài giảng → tự động phân tích và tạo slides lower-third cho livestream. Hỗ trợ kinh văn, nguyên ngữ, phân đoạn và kết luận.</p>
                    <div class="tags">
                        <span class="tag" style="background:rgba(212,160,23,0.15);color:#e8b85a;border-color:rgba(212,160,23,0.25)">PDF</span>
                        <span class="tag" style="background:rgba(212,160,23,0.15);color:#e8b85a;border-color:rgba(212,160,23,0.25)">Lower-Third</span>
                        <span class="tag" style="background:rgba(212,160,23,0.15);color:#e8b85a;border-color:rgba(212,160,23,0.25)">AI Detect</span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span>Python · pdfplumber · Laravel</span>
                <span class="arrow">Mở ngay →</span>
            </div>
        </a>

        <!-- Template Manager -->
        <a href="{{ url('/ppt/templates') }}" class="card" style="border-color:rgba(120,80,200,0.2)">
            <div class="card-body">
                <div class="icon icon-purple">🗂️</div>
                <div>
                    <div class="card-title">Quản Lý Template PPT</div>
                    <p class="card-desc">Tạo, đổi tên, sửa logo và xóa các template banner. Mỗi template lưu riêng màu sắc và logo.</p>
                    <div class="tags">
                        <span class="tag" style="background:rgba(120,80,200,0.15);color:#d8b4fe;border-color:rgba(120,80,200,0.25)">CRUD</span>
                        <span class="tag" style="background:rgba(120,80,200,0.15);color:#d8b4fe;border-color:rgba(120,80,200,0.25)">Logo Upload</span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span>Laravel · MySQL</span>
                <span class="arrow">Mở ngay →</span>
            </div>
        </a>

        <!-- Song Library Manager -->
        <a href="{{ url('/songs') }}" class="card" style="border-color:rgba(56,189,248,0.2); background:linear-gradient(135deg,rgba(56,189,248,0.05) 0%,rgba(10,10,24,0.6) 100%)">
            <div class="card-body">
                <div class="icon icon-blue">🎵</div>
                <div>
                    <div class="card-title">Quản Lý Bài Hát</div>
                    <p class="card-desc">Thư viện bài hát số với phân loại, tìm kiếm, và chỉnh sửa trực tiếp. Dữ liệu đồng bộ với PPT Livestream.</p>
                    <div class="tags">
                        <span class="tag" style="background:rgba(56,189,248,0.15);color:#7dd3fc;border-color:rgba(56,189,248,0.25)">2000+ Bài Hát</span>
                        <span class="tag" style="background:rgba(56,189,248,0.15);color:#7dd3fc;border-color:rgba(56,189,248,0.25)">MySQL</span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span>Database · Laravel · Alpine</span>
                <span class="arrow">Mở ngay →</span>
            </div>
        </a>

        <!-- Bible Manager (G-A-E-V) -->
        <a href="{{ url('/bible-manager') }}" class="card" style="border-color:rgba(16,185,129,0.2); background:linear-gradient(135deg,rgba(16,185,129,0.05) 0%,rgba(10,10,24,0.6) 100%)">
            <div class="card-body">
                <div class="icon icon-green">📖</div>
                <div>
                    <div class="card-title">Quản Lý Kinh Thánh</div>
                    <div style="font-size:0.72rem; color:#10b981; font-weight:700; margin-bottom:0.6rem">✓ G-A-E-V Engine</div>
                    <p class="card-desc">Trạm kiểm soát trung tâm toàn bộ 66 Sách, 1189 Chương và 31,102 Câu Kinh Thánh. Chỉnh sửa cực nhanh với kiến trúc 4-Layer siêu việt.</p>
                    <div class="tags">
                        <span class="tag" style="background:rgba(16,185,129,0.15);color:#34d399;border-color:rgba(16,185,129,0.25)">Client-Side Alpine</span>
                        <span class="tag" style="background:rgba(16,185,129,0.15);color:#34d399;border-color:rgba(16,185,129,0.25)">4-Layer REST</span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span>Grapuco · Laravel · Repository</span>
                <span class="arrow">Mở ngay →</span>
            </div>
        </a>

        <!-- BibleFlow AI -->
        <a href="{{ url('/bibleflow') }}" class="card" style="border-color:rgba(16,185,129,0.2); background:linear-gradient(135deg,rgba(16,185,129,0.05) 0%,rgba(10,10,24,0.6) 100%)">
            <div class="card-body">
                <div class="icon icon-green">🎤</div>
                <div>
                    <div class="card-title">BibleFlow AI (Karaoke)</div>
                    <p class="card-desc">Luyện đọc Kinh Thánh chuẩn xác với AI. Ghi âm trực tiếp, highlight chữ theo giọng nói và tự động dừng khi đọc sai.</p>
                    <div class="tags">
                        <span class="tag" style="background:rgba(16,185,129,0.15);color:#34d399;border-color:rgba(16,185,129,0.25)">Faster Whisper</span>
                        <span class="tag" style="background:rgba(16,185,129,0.15);color:#34d399;border-color:rgba(16,185,129,0.25)">WebSockets</span>
                        <span class="tag" style="background:rgba(16,185,129,0.15);color:#34d399;border-color:rgba(16,185,129,0.25)">Stop-on-Error</span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span>FastAPI · Laravel</span>
                <span class="arrow">Mở ngay →</span>
            </div>
        </a>

        <!-- Bible Learning -->
        <a href="{{ route('biblelearning.portal') }}" class="card" style="border-color:rgba(245,158,11,0.2); background:linear-gradient(135deg,rgba(245,158,11,0.05) 0%,rgba(10,10,24,0.6) 100%)">
            <div class="card-body">
                <div class="icon icon-gold">📜</div>
                <div>
                    <div class="card-title">Bible Learning Portal Hub</div>
                    <p class="card-desc">Hệ sinh thái học Kinh Thánh chuyên sâu bằng bản đồ, dòng thời gian và Flashcard Spaced Repetition kết hợp Gemini AI.</p>
                    <div class="tags">
                        <span class="tag" style="background:rgba(245,158,11,0.15);color:#fbbf24;border-color:rgba(245,158,11,0.25)">Gemini AI</span>
                        <span class="tag" style="background:rgba(245,158,11,0.15);color:#fbbf24;border-color:rgba(245,158,11,0.25)">Maps</span>
                        <span class="tag" style="background:rgba(245,158,11,0.15);color:#fbbf24;border-color:rgba(245,158,11,0.25)">Flashcards</span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span>Vue 3 · MySQL 8</span>
                <span class="arrow">Mở ngay →</span>
            </div>
        </a>

    </div>

    <!-- Coming Soon -->
    <div class="section-header" style="margin-top:1rem">
        <h2 style="color:#4a5168; font-size:1.05rem">🔮 Sắp Ra Mắt</h2>
        <span class="pill pill-gray">Đang phát triển</span>
    </div>

    <div class="grid">

        <div class="card disabled">
            <div class="card-body">
                <div class="icon icon-purple">📅</div>
                <div>
                    <div class="card-title">Lịch Phụng Vụ</div>
                    <p class="card-desc">Lên lịch chương trình thờ phượng, phân công người hầu việc, gửi nhắc nhở tự động.</p>
                </div>
            </div>
            <div class="card-footer">
                <span>Calendar · Zalo API</span>
                <span class="arrow">Sắp có →</span>
            </div>
        </div>

        <div class="card disabled">
            <div class="card-body">
                <div class="icon icon-blue">📡</div>
                <div>
                    <div class="card-title">Livestream Manager</div>
                    <p class="card-desc">Quản lý overlay, ticker chữ chạy, và hiển thị lời bài hát trực tiếp trên stream.</p>
                </div>
            </div>
            <div class="card-footer">
                <span>OBS · WebSocket</span>
                <span class="arrow">Sắp có →</span>
            </div>
        </div>

        <div class="card disabled">
            <div class="card-body">
                <div class="icon icon-rose">💌</div>
                <div>
                    <div class="card-title">Thông Báo Hội Chúng</div>
                    <p class="card-desc">Gửi bản tin hàng tuần qua Zalo / email cho toàn bộ hội chúng tự động.</p>
                </div>
            </div>
            <div class="card-footer">
                <span>Email · Zalo API</span>
                <span class="arrow">Sắp có →</span>
            </div>
        </div>

        <div class="card disabled">
            <div class="card-body">
                <div class="icon icon-teal">🎙</div>
                <div>
                    <div class="card-title">Ghi Chép Bài Giảng</div>
                    <p class="card-desc">Chuyển âm thanh bài giảng thành văn bản, tóm tắt và lưu trữ theo ngày tự động.</p>
                </div>
            </div>
            <div class="card-footer">
                <span>AI Transcription · Whisper</span>
                <span class="arrow">Sắp có →</span>
            </div>
        </div>

    </div>
</div>

<!-- FOOTER -->
<footer>
    <p>✝ <strong>ChurchTools</strong> — Xây dựng với tình yêu thương dành cho Hội Thánh ·
    <a href="{{ url('/ppt') }}">PPT Livestream</a></p>
    <p style="margin-top:0.4rem; opacity:0.6">{{ date('Y') }} · XAMPP Local Dev · Laravel {{ app()->version() }}</p>
</footer>

</body>
</html>
