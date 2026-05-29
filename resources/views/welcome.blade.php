<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ChurchTools — Bộ Công Cụ Hội Thánh</title>
    <meta name="description" content="Hệ sinh thái số toàn diện dành cho Hội Thánh: Slide trình chiếu, Học Kinh Thánh, Quản lý tài chính và nhiều hơn nữa.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:     #080c18;
            --bg2:    #0e1628;
            --gold:   #f59e0b;
            --gold2:  #fbbf24;
            --teal:   #10b981;
            --blue:   #3b82f6;
            --purple: #8b5cf6;
            --rose:   #f43f5e;
            --text:   #f1f5f9;
            --muted:  #64748b;
            --glass:  rgba(255,255,255,0.04);
            --glb:    rgba(255,255,255,0.07);
            --border: rgba(255,255,255,0.07);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 50% at 15% 0%, rgba(16,185,129,0.12) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 85% 100%, rgba(245,158,11,0.08) 0%, transparent 55%),
                radial-gradient(ellipse 40% 30% at 90% 10%, rgba(139,92,246,0.07) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        /* ===== NAV ===== */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            height: 60px;
            background: rgba(8,12,24,0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-weight: 700;
            font-size: 1.05rem;
            text-decoration: none;
            color: var(--text);
            letter-spacing: -0.01em;
        }

        .nav-logo-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--teal), #059669);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: white;
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        }

        .nav-logo span { color: var(--gold2); }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-user-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(245,158,11,0.1);
            border: 1px solid rgba(245,158,11,0.2);
            border-radius: 99px;
            padding: 0.3rem 0.85rem 0.3rem 0.5rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--gold2);
        }

        .nav-user-avatar {
            width: 22px; height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), #d97706);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: white;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.4rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s;
        }

        .btn-primary { background: var(--teal); color: white; }
        .btn-primary:hover { background: #059669; transform: translateY(-1px); }

        .btn-ghost {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
        }
        .btn-ghost:hover { color: var(--rose); border-color: rgba(244,63,94,0.4); background: rgba(244,63,94,0.08); }

        /* ===== HERO ===== */
        .hero {
            position: relative;
            z-index: 1;
            padding: 9rem 2rem 3.5rem;
            text-align: center;
            max-width: 720px;
            margin: 0 auto;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(245,158,11,0.1);
            border: 1px solid rgba(245,158,11,0.2);
            border-radius: 999px;
            padding: 0.3rem 1rem;
            font-size: 0.73rem;
            font-weight: 700;
            color: var(--gold2);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -0.03em;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #fff 0%, var(--gold2) 50%, var(--teal) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1rem;
            color: var(--muted);
            line-height: 1.7;
            max-width: 480px;
            margin: 0 auto 2rem;
        }

        .hero-cta-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn-hero {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.92rem;
            font-weight: 700;
            padding: 0.75rem 1.75rem;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, var(--teal), #059669);
            color: white;
            box-shadow: 0 4px 20px rgba(16,185,129,0.3);
        }
        .btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(16,185,129,0.45); }

        .btn-hero-ghost {
            background: var(--glass);
            color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-hero-ghost:hover { background: var(--glb); border-color: rgba(255,255,255,0.15); }

        /* ===== MAIN LAUNCHER ===== */
        .launcher {
            position: relative;
            z-index: 1;
            max-width: 1160px;
            margin: 0 auto;
            padding: 0 2rem 6rem;
        }

        .category-block {
            margin-bottom: 3.5rem;
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .category-icon {
            width: 34px; height: 34px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .category-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: 0.02em;
        }

        .category-count {
            margin-left: auto;
            font-size: 0.68rem;
            font-weight: 700;
            color: var(--muted);
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 99px;
            padding: 0.15rem 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        /* APP GRID */
        .app-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
        }

        .app-grid.featured-first .app-card:first-child {
            grid-column: span 2;
        }

        /* APP CARD */
        .app-card {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 1.4rem 1.25rem 1.1rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            text-decoration: none;
            color: var(--text);
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .app-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            opacity: 0;
            transition: opacity 0.22s;
        }

        .app-card:hover:not(.app-card--disabled) {
            border-color: rgba(255,255,255,0.14);
            background: var(--glb);
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.35);
        }
        .app-card:hover::before { opacity: 1; }

        .app-card--disabled {
            opacity: 0.38;
            pointer-events: none;
        }

        .app-card--featured {
            background: linear-gradient(135deg, rgba(245,158,11,0.06) 0%, rgba(16,185,129,0.06) 100%);
            border-color: rgba(245,158,11,0.18);
            animation: pulse-glow 3.5s ease-in-out infinite;
        }
        .app-card--featured:hover {
            border-color: rgba(245,158,11,0.38) !important;
            box-shadow: 0 16px 48px rgba(245,158,11,0.12) !important;
        }

        @keyframes pulse-glow {
            0%,100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.15); }
            50%      { box-shadow: 0 0 0 6px rgba(245,158,11,0.04); }
        }

        .app-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
            transition: transform 0.22s;
        }
        .app-card:hover .app-icon { transform: scale(1.08); }

        /* Icon color variants */
        .icon-teal   { background: rgba(16,185,129,0.15); box-shadow: 0 0 0 1px rgba(16,185,129,0.25), inset 0 1px 0 rgba(255,255,255,0.1); }
        .icon-gold   { background: rgba(245,158,11,0.15); box-shadow: 0 0 0 1px rgba(245,158,11,0.25), inset 0 1px 0 rgba(255,255,255,0.1); }
        .icon-blue   { background: rgba(59,130,246,0.15); box-shadow: 0 0 0 1px rgba(59,130,246,0.25), inset 0 1px 0 rgba(255,255,255,0.1); }
        .icon-purple { background: rgba(139,92,246,0.15); box-shadow: 0 0 0 1px rgba(139,92,246,0.25), inset 0 1px 0 rgba(255,255,255,0.1); }
        .icon-rose   { background: rgba(244,63,94,0.15);  box-shadow: 0 0 0 1px rgba(244,63,94,0.25),  inset 0 1px 0 rgba(255,255,255,0.1); }
        .icon-sky    { background: rgba(14,165,233,0.15); box-shadow: 0 0 0 1px rgba(14,165,233,0.25), inset 0 1px 0 rgba(255,255,255,0.1); }

        .app-name {
            font-size: 0.97rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.25;
        }

        .app-desc {
            font-size: 0.78rem;
            color: var(--muted);
            line-height: 1.6;
            flex: 1;
        }

        .app-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 0.7rem;
            border-top: 1px solid var(--border);
            font-size: 0.68rem;
            color: var(--muted);
            font-weight: 600;
        }

        .app-badge {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.55rem;
            border-radius: 99px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .badge-live   { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.25); }
        .badge-soon   { background: rgba(100,116,139,0.15); color: #94a3b8; border: 1px solid rgba(100,116,139,0.2); }
        .badge-new    { background: rgba(245,158,11,0.15); color: var(--gold2); border: 1px solid rgba(245,158,11,0.25); }
        .badge-beta   { background: rgba(139,92,246,0.15); color: #c4b5fd; border: 1px solid rgba(139,92,246,0.25); }

        .app-arrow {
            font-size: 0.75rem;
            color: var(--muted);
            transition: transform 0.2s, color 0.2s;
        }
        .app-card:hover .app-arrow { transform: translateX(4px); color: var(--gold2); }

        /* TAGS */
        .app-tags { display: flex; flex-wrap: wrap; gap: 0.3rem; margin-top: 0.4rem; }
        .app-tag {
            font-size: 0.62rem;
            font-weight: 600;
            padding: 0.1rem 0.45rem;
            border-radius: 99px;
            letter-spacing: 0.04em;
        }

        /* FEATURED CARD - horizontal on wide screens */
        @media (min-width: 640px) {
            .app-card--h {
                flex-direction: row;
                gap: 1.25rem;
            }
            .app-card--h .app-icon { width: 58px; height: 58px; font-size: 24px; }
            .app-card--h .app-body { flex: 1; display: flex; flex-direction: column; }
        }

        /* FOOTER */
        footer {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem;
            color: var(--muted);
            font-size: 0.75rem;
            border-top: 1px solid var(--border);
        }
        footer a { color: var(--gold2); text-decoration: none; }

        @media (max-width: 640px) {
            .app-grid.featured-first .app-card:first-child { grid-column: span 1; }
            nav { padding: 0 1rem; }
            .hero { padding: 7rem 1.25rem 3rem; }
            .launcher { padding: 0 1rem 5rem; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a href="/" class="nav-logo">
        <div class="nav-logo-icon"><i class="fa-solid fa-cross" style="font-size:12px"></i></div>
        Church<span>Tools</span>
    </a>

    <div class="nav-right">
        @auth
            <div class="nav-user-badge">
                <div class="nav-user-avatar"><i class="fa-solid fa-user" style="font-size:9px"></i></div>
                {{ Auth::user()->name }}
            </div>
            <a href="{{ url('/ppt') }}" class="btn-nav btn-primary">
                <i class="fa-solid fa-display"></i> Mở Ứng Dụng
            </a>
            <form action="{{ route('logout') }}" method="POST" style="margin:0">
                @csrf
                <button type="submit" class="btn-nav btn-ghost">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất
                </button>
            </form>
        @else
            <a href="{{ url('/login') }}" class="btn-nav btn-primary">
                <i class="fa-solid fa-lock"></i> Đăng nhập
            </a>
        @endauth
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-badge">
        <i class="fa-solid fa-cross" style="font-size:9px"></i>
        Hệ Sinh Thái Số · Hội Thánh · {{ date('Y') }}
    </div>
    <h1>Bộ Công Cụ<br>Toàn Diện Cho<br>Hội Thánh</h1>
    <p>Tất cả những gì bạn cần — từ trình chiếu slide, học Kinh Thánh đến quản lý tài chính — trong một hệ thống thống nhất, hiện đại và bảo mật.</p>

    @auth
        <div class="hero-cta-group">
            <a href="{{ url('/ppt') }}" class="btn-hero btn-hero-primary">
                <i class="fa-solid fa-rocket"></i> Vào Hệ Thống
            </a>
            <a href="{{ url('/finance') }}" class="btn-hero btn-hero-ghost">
                <i class="fa-solid fa-coins"></i> Tài Chính Cá Nhân
            </a>
        </div>
    @else
        <div class="hero-cta-group">
            <a href="{{ url('/login') }}" class="btn-hero btn-hero-primary">
                <i class="fa-solid fa-lock-open"></i> Đăng nhập để bắt đầu
            </a>
        </div>
    @endauth
</section>

<!-- APP LAUNCHER -->
<div class="launcher" id="apps">

    <!-- NHÓM 1: Trình Chiếu & Phụng Vụ -->
    <div class="category-block">
        <div class="category-header">
            <div class="category-icon icon-teal"><i class="fa-solid fa-display" style="color:#34d399"></i></div>
            <div>
                <div class="category-title">Trình Chiếu & Phụng Vụ</div>
                <div style="font-size:0.72rem; color:var(--muted); margin-top:1px">Công cụ hỗ trợ thờ phượng và livestream</div>
            </div>
            <span class="category-count">3 Ứng dụng</span>
        </div>

        <div class="app-grid featured-first">
            
            <!-- PPT Livestream — FEATURED -->
            <a href="{{ url('/ppt') }}" class="app-card app-card--featured app-card--h">
                <div class="app-icon icon-gold" style="font-size:26px;">🎵</div>
                <div class="app-body">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem">
                        <div class="app-name">PPT Livestream</div>
                        <span class="app-badge badge-live">Live</span>
                    </div>
                    <div class="app-desc">Tự động tạo slide PowerPoint từ lời bài hát. Hỗ trợ 10+ mẫu banner, logo nhà thờ, màu sắc tuỳ chỉnh và preview WYSIWYG trực quan.</div>
                    <div class="app-tags">
                        <span class="app-tag" style="background:rgba(245,158,11,0.12);color:#fbbf24;border:1px solid rgba(245,158,11,0.2)">Bulk Text</span>
                        <span class="app-tag" style="background:rgba(245,158,11,0.12);color:#fbbf24;border:1px solid rgba(245,158,11,0.2)">10 Templates</span>
                        <span class="app-tag" style="background:rgba(245,158,11,0.12);color:#fbbf24;border:1px solid rgba(245,158,11,0.2)">WYSIWYG Preview</span>
                        <span class="app-tag" style="background:rgba(245,158,11,0.12);color:#fbbf24;border:1px solid rgba(245,158,11,0.2)">Logo Upload</span>
                    </div>
                    <div class="app-footer" style="margin-top:0.6rem">
                        <span>PowerPoint · Python · Laravel</span>
                        <span class="app-arrow">Mở ngay →</span>
                    </div>
                </div>
            </a>

            <!-- Bài Giảng Live -->
            <a href="{{ url('/ppt/sermon') }}" class="app-card">
                <div class="app-icon icon-gold"><i class="fa-solid fa-book-open-reader" style="color:#fbbf24"></i></div>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem">
                        <div class="app-name">Bài Giảng Live</div>
                        <span class="app-badge badge-live">Live</span>
                    </div>
                    <div class="app-desc">Upload PDF bài giảng → AI phân tích và tạo slide lower-third cho livestream. Kinh văn, nguyên ngữ, phân đoạn tự động.</div>
                </div>
                <div class="app-footer">
                    <span>PDF · pdfplumber · AI</span>
                    <span class="app-arrow">Mở ngay →</span>
                </div>
            </a>

            <!-- Quản Lý Template PPT -->
            <a href="{{ url('/ppt/templates') }}" class="app-card">
                <div class="app-icon icon-purple"><i class="fa-solid fa-swatchbook" style="color:#c4b5fd"></i></div>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem">
                        <div class="app-name">Quản Lý Template</div>
                        <span class="app-badge badge-live">Live</span>
                    </div>
                    <div class="app-desc">Tạo, chỉnh sửa và quản lý các mẫu banner PPT. Lưu màu sắc, logo riêng cho từng template.</div>
                </div>
                <div class="app-footer">
                    <span>Laravel · MySQL · CRUD</span>
                    <span class="app-arrow">Mở ngay →</span>
                </div>
            </a>

        </div>
    </div>

    <!-- NHÓM 2: Kinh Thánh & Học Tập -->
    <div class="category-block">
        <div class="category-header">
            <div class="category-icon icon-blue"><i class="fa-solid fa-book-bible" style="color:#93c5fd"></i></div>
            <div>
                <div class="category-title">Kinh Thánh & Học Tập</div>
                <div style="font-size:0.72rem; color:var(--muted); margin-top:1px">Học, tra cứu và khám phá Lời Chúa</div>
            </div>
            <span class="category-count">4 Ứng dụng</span>
        </div>

        <div class="app-grid">

            <!-- Quản Lý Bài Hát -->
            <a href="{{ url('/songs') }}" class="app-card">
                <div class="app-icon icon-sky"><i class="fa-solid fa-music" style="color:#7dd3fc"></i></div>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem">
                        <div class="app-name">Thư Viện Bài Hát</div>
                        <span class="app-badge badge-live">Live</span>
                    </div>
                    <div class="app-desc">2000+ bài hát với tìm kiếm, phân loại và chỉnh sửa trực tiếp. Đồng bộ với PPT Livestream.</div>
                </div>
                <div class="app-footer">
                    <span>Alpine.js · MySQL</span>
                    <span class="app-arrow">Mở ngay →</span>
                </div>
            </a>

            <!-- Quản Lý Kinh Thánh -->
            <a href="{{ url('/bible-manager') }}" class="app-card">
                <div class="app-icon icon-teal"><i class="fa-solid fa-book" style="color:#34d399"></i></div>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem">
                        <div class="app-name">Quản Lý Kinh Thánh</div>
                        <span class="app-badge badge-live">Live</span>
                    </div>
                    <div class="app-desc">Trạm kiểm soát 66 sách, 1189 chương và 31,102 câu Kinh Thánh. Chỉnh sửa siêu nhanh với kiến trúc G-A-E-V.</div>
                </div>
                <div class="app-footer">
                    <span>Grapuco · 4-Layer REST</span>
                    <span class="app-arrow">Mở ngay →</span>
                </div>
            </a>

            <!-- BibleFlow AI (Karaoke) -->
            <a href="{{ url('/bibleflow') }}" class="app-card">
                <div class="app-icon icon-rose"><i class="fa-solid fa-microphone-lines" style="color:#fda4af"></i></div>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem">
                        <div class="app-name">BibleFlow AI</div>
                        <span class="app-badge badge-beta">Beta</span>
                    </div>
                    <div class="app-desc">Luyện đọc Kinh Thánh chuẩn xác với AI. Ghi âm trực tiếp, highlight chữ theo giọng và tự động dừng khi đọc sai.</div>
                </div>
                <div class="app-footer">
                    <span>Whisper · WebSocket · FastAPI</span>
                    <span class="app-arrow">Mở ngay →</span>
                </div>
            </a>

            <!-- Bible Learning Portal -->
            <a href="{{ route('biblelearning.portal') }}" class="app-card">
                <div class="app-icon icon-gold"><i class="fa-solid fa-graduation-cap" style="color:#fbbf24"></i></div>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem">
                        <div class="app-name">Bible Learning Hub</div>
                        <span class="app-badge badge-live">Live</span>
                    </div>
                    <div class="app-desc">Học Kinh Thánh chuyên sâu qua bản đồ, dòng thời gian và Flashcard Spaced Repetition kết hợp Gemini AI.</div>
                </div>
                <div class="app-footer">
                    <span>Gemini AI · Flashcards · Maps</span>
                    <span class="app-arrow">Mở ngay →</span>
                </div>
            </a>

        </div>
    </div>

    <!-- NHÓM 3: Cá Nhân & Quản Lý -->
    <div class="category-block">
        <div class="category-header">
            <div class="category-icon icon-gold"><i class="fa-solid fa-coins" style="color:#fbbf24"></i></div>
            <div>
                <div class="category-title">Cá Nhân & Quản Lý</div>
                <div style="font-size:0.72rem; color:var(--muted); margin-top:1px">Ứng dụng tiện ích cá nhân nội bộ</div>
            </div>
            <span class="category-count">1 Ứng dụng</span>
        </div>

        <div class="app-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr))">

            <!-- FinanceTracker PWA -->
            <a href="{{ url('/finance') }}" class="app-card app-card--featured app-card--h">
                <div class="app-icon icon-gold" style="font-size:26px">💰</div>
                <div class="app-body">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem">
                        <div class="app-name">FinanceTracker PWA</div>
                        <span class="app-badge badge-new">Mới</span>
                    </div>
                    <div class="app-desc">Quản lý tài chính cá nhân toàn diện: ví tiền mặt & ngân hàng, ghi nợ vay, tích sản đầu tư (Cổ phiếu + Crypto). Hỗ trợ cài đặt PWA trên điện thoại như ứng dụng native.</div>
                    <div class="app-tags" style="margin-top:0.5rem">
                        <span class="app-tag" style="background:rgba(245,158,11,0.12);color:#fbbf24;border:1px solid rgba(245,158,11,0.2)">PWA Mobile</span>
                        <span class="app-tag" style="background:rgba(245,158,11,0.12);color:#fbbf24;border:1px solid rgba(245,158,11,0.2)">Crypto Live</span>
                        <span class="app-tag" style="background:rgba(245,158,11,0.12);color:#fbbf24;border:1px solid rgba(245,158,11,0.2)">Offline First</span>
                    </div>
                    <div class="app-footer" style="margin-top:0.6rem">
                        <span>Alpine.js · Chart.js · CoinGecko</span>
                        <span class="app-arrow">Mở ngay →</span>
                    </div>
                </div>
            </a>

        </div>
    </div>

    <!-- NHÓM 4: Sắp Ra Mắt -->
    <div class="category-block">
        <div class="category-header">
            <div class="category-icon icon-purple"><i class="fa-solid fa-flask" style="color:#c4b5fd"></i></div>
            <div>
                <div class="category-title">Đang Phát Triển</div>
                <div style="font-size:0.72rem; color:var(--muted); margin-top:1px">Sắp ra mắt trong thời gian tới</div>
            </div>
            <span class="category-count">4 Ứng dụng</span>
        </div>

        <div class="app-grid">

            <div class="app-card app-card--disabled">
                <div class="app-icon icon-blue"><i class="fa-solid fa-calendar-days" style="color:#93c5fd"></i></div>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem">
                        <div class="app-name">Lịch Phụng Vụ</div>
                        <span class="app-badge badge-soon">Sắp có</span>
                    </div>
                    <div class="app-desc">Lên lịch chương trình thờ phượng, phân công người hầu việc và gửi nhắc nhở tự động.</div>
                </div>
                <div class="app-footer">
                    <span>Calendar · Zalo API</span>
                    <span class="app-arrow">Sắp có →</span>
                </div>
            </div>

            <div class="app-card app-card--disabled">
                <div class="app-icon icon-sky"><i class="fa-solid fa-tower-broadcast" style="color:#7dd3fc"></i></div>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem">
                        <div class="app-name">Livestream Manager</div>
                        <span class="app-badge badge-soon">Sắp có</span>
                    </div>
                    <div class="app-desc">Quản lý overlay, ticker chữ chạy và hiển thị lời bài hát trực tiếp trên stream OBS.</div>
                </div>
                <div class="app-footer">
                    <span>OBS · WebSocket</span>
                    <span class="app-arrow">Sắp có →</span>
                </div>
            </div>

            <div class="app-card app-card--disabled">
                <div class="app-icon icon-rose"><i class="fa-solid fa-bell" style="color:#fda4af"></i></div>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem">
                        <div class="app-name">Thông Báo Hội Chúng</div>
                        <span class="app-badge badge-soon">Sắp có</span>
                    </div>
                    <div class="app-desc">Gửi bản tin hàng tuần qua Zalo và Email cho toàn bộ hội chúng một cách tự động.</div>
                </div>
                <div class="app-footer">
                    <span>Email · Zalo API</span>
                    <span class="app-arrow">Sắp có →</span>
                </div>
            </div>

            <div class="app-card app-card--disabled">
                <div class="app-icon icon-purple"><i class="fa-solid fa-microphone" style="color:#c4b5fd"></i></div>
                <div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem">
                        <div class="app-name">Ghi Chép Bài Giảng</div>
                        <span class="app-badge badge-soon">Sắp có</span>
                    </div>
                    <div class="app-desc">Chuyển âm thanh bài giảng thành văn bản, tóm tắt và lưu trữ theo ngày tự động.</div>
                </div>
                <div class="app-footer">
                    <span>AI Transcription · Whisper</span>
                    <span class="app-arrow">Sắp có →</span>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- FOOTER -->
<footer>
    <p>✝ <strong>ChurchTools</strong> — Xây dựng với tình yêu dành cho Hội Thánh ·
    <a href="{{ url('/ppt') }}">PPT Livestream</a> ·
    <a href="{{ url('/finance') }}">FinanceTracker</a></p>
    <p style="margin-top:0.4rem; opacity:0.5">{{ date('Y') }} · Laravel {{ app()->version() }}</p>
</footer>

</body>
</html>
