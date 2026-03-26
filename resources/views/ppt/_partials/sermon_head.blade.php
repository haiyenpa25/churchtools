{{-- sermon_head.blade.php — CSS + dependencies for Bài Giảng Live --}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài Giảng Live – ChurchTools PPT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=EB+Garamond:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.08); }
        .drop-zone { border: 2px dashed rgba(212,160,23,0.35); transition: all .2s; }
        .drop-zone:hover, .drop-zone.drag-over { border-color: #D4A017; background: rgba(212,160,23,0.06); }

        /* Lower-third preview */
        .lt-slide { position: relative; aspect-ratio: 16/9; background: #00FF00; overflow: hidden; border-radius: 10px; }
        .lt-banner { position: absolute; bottom: 0; left: 0; right: 0; height: 27.5%; background: rgba(10,10,24,0.97); display: flex; align-items: stretch; }
        .lt-accent  { width: 1.2%; background: #D4A017; flex-shrink: 0; }
        .lt-content { flex: 1; padding: 2.5% 2% 2% 2%; display: flex; flex-direction: column; justify-content: center; overflow: hidden; }
        .lt-overlay-title { position: absolute; bottom: 0; left: 0; right: 0; height: 38%; background: #0E3A5C; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .lt-overlay-accent-top { position: absolute; top: 0; left: 0; right: 0; height: 6%; background: #D4A017; }
        .lt-ref   { color: #FFD700; font-weight: 700; font-size: clamp(5px, 1.5cqw, 14px); line-height: 1.3; font-family: 'Inter', sans-serif; }
        .lt-title { color: #fff; font-weight: 800; font-size: clamp(5px, 1.6cqw, 14px); line-height: 1.3; }
        .lt-body  { color: #e0e0e0; font-size: clamp(4px, 1.2cqw, 12px); line-height: 1.4; margin-top: 1%; }
        .lt-bullet { color: #e8e8e8; font-size: clamp(4px, 1.1cqw, 11px); line-height: 1.5; }
        .lt-deduction { color: #FFD700; font-weight: 700; font-size: clamp(4px, 1.1cqw, 11px); }
        .lt-section-title { color: #fff; font-weight: 900; font-size: clamp(5px, 2.4cqw, 22px); text-align: center; }
        .lt-section-sub   { color: #cbd5e1; font-size: clamp(4px, 1.4cqw, 14px); text-align: center; margin-top: 1%; }

        /* Type badge colors */
        .badge-section_title { background: #1e40af22; border-color: #3b82f6; color: #93c5fd; }
        .badge-scripture     { background: #16653422; border-color: #22c55e; color: #86efac; }
        .badge-origin_word   { background: #78350f22; border-color: #f59e0b; color: #fcd34d; }
        .badge-list          { background: #4c1d9522; border-color: #a855f7; color: #d8b4fe; }
        .badge-conclusion    { background: #7f1d1d22; border-color: #ef4444; color: #fca5a5; }
        .badge-body          { background: #1f293722; border-color: #64748b; color: #94a3b8; }
    </style>
</head>
