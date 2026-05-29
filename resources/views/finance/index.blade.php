<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>MoneyTracker — Quản Lý Tài Chính</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#00c48c">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MoneyTracker">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --green:  #00c48c;
        --green2: #00a876;
        --green3: #00e5a0;
        --red:    #ff5252;
        --red2:   #ff1744;
        --blue:   #448aff;
        --yellow: #ffd740;
        --bg:     #0d1117;
        --bg2:    #161b22;
        --bg3:    #1c2128;
        --card:   rgba(22,27,34,0.95);
        --border: rgba(255,255,255,0.08);
        --text:   #f0f6fc;
        --muted:  #8b949e;
        --muted2: #6e7681;
    }

    html, body { height: 100%; overflow: hidden; }
    body {
        font-family: 'Outfit', sans-serif;
        background: var(--bg);
        color: var(--text);
        -webkit-tap-highlight-color: transparent;
        display: flex;
        justify-content: center;
        align-items: stretch;
    }

    /* ===== APP SHELL ===== */
    #app {
        width: 100%;
        max-width: 430px;
        min-height: 100dvh;
        display: flex;
        flex-direction: column;
        background: var(--bg);
        position: relative;
        overflow: hidden;
        box-shadow: 0 0 80px rgba(0,196,140,0.06);
    }

    /* ===== SCROLLBAR ===== */
    ::-webkit-scrollbar { width: 3px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(0,196,140,0.2); border-radius: 4px; }

    /* ===== HEADER ===== */
    .app-header {
        background: var(--bg2);
        border-bottom: 1px solid var(--border);
        padding: 12px 16px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        position: relative;
        z-index: 20;
    }

    .header-month {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .month-nav-btn {
        width: 28px; height: 28px;
        border-radius: 8px;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border);
        color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.15s;
    }
    .month-nav-btn:active { background: rgba(0,196,140,0.15); color: var(--green); transform: scale(0.92); }

    .month-label {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .header-icon-btn {
        width: 32px; height: 32px;
        border-radius: 10px;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border);
        color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.15s;
    }
    .header-icon-btn:active { background: rgba(255,82,82,0.1); color: var(--red); transform: scale(0.92); }

    .online-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: var(--green);
        animation: pulse-dot 2s infinite;
        box-shadow: 0 0 6px var(--green);
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* ===== MAIN CONTENT ===== */
    .app-content {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }

    /* ===== WALLET SWIPER ===== */
    .wallet-section {
        padding: 16px 0 0;
    }

    .wallet-swiper {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding: 0 16px 12px;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .wallet-swiper::-webkit-scrollbar { display: none; }

    .wallet-card {
        flex-shrink: 0;
        width: 200px;
        border-radius: 20px;
        padding: 18px 16px;
        scroll-snap-align: start;
        cursor: pointer;
        transition: transform 0.2s;
        position: relative;
        overflow: hidden;
    }
    .wallet-card:active { transform: scale(0.96); }

    .wallet-card::before {
        content: '';
        position: absolute;
        top: -20px; right: -20px;
        width: 80px; height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
    }

    .wc-green  { background: linear-gradient(135deg, #00c48c, #00a876); }
    .wc-blue   { background: linear-gradient(135deg, #448aff, #2979ff); }
    .wc-purple { background: linear-gradient(135deg, #7c4dff, #651fff); }
    .wc-red    { background: linear-gradient(135deg, #ff5252, #d50000); }
    .wc-orange { background: linear-gradient(135deg, #ff9100, #ff6d00); }
    .wc-teal   { background: linear-gradient(135deg, #1de9b6, #00bfa5); }

    .wallet-card .wc-type {
        font-size: 10px;
        font-weight: 700;
        color: rgba(255,255,255,0.7);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .wallet-card .wc-name {
        font-size: 13px;
        font-weight: 600;
        color: rgba(255,255,255,0.9);
        margin-bottom: 16px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .wallet-card .wc-balance {
        font-size: 20px;
        font-weight: 800;
        color: #fff;
        line-height: 1;
    }

    .wallet-card .wc-currency {
        font-size: 10px;
        color: rgba(255,255,255,0.6);
        margin-top: 3px;
    }

    .wallet-add-card {
        flex-shrink: 0;
        width: 100px;
        border-radius: 20px;
        border: 2px dashed rgba(255,255,255,0.12);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        color: var(--muted);
        font-size: 11px;
        font-weight: 600;
        transition: all 0.2s;
        scroll-snap-align: start;
    }
    .wallet-add-card:active { border-color: var(--green); color: var(--green); transform: scale(0.96); }
    .wallet-add-card .add-icon { font-size: 22px; }

    /* ===== MONTH SUMMARY ===== */
    .month-summary {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding: 0 16px 16px;
    }

    .summary-card {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 12px 14px;
    }

    .summary-card .sc-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .summary-card .sc-value {
        font-size: 16px;
        font-weight: 800;
    }

    .sc-income .sc-label { color: var(--green); }
    .sc-income .sc-value { color: var(--green); }
    .sc-expense .sc-label { color: var(--red); }
    .sc-expense .sc-value { color: var(--red); }

    /* ===== SECTION TITLE ===== */
    .section-title {
        padding: 0 16px 10px;
        font-size: 13px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-more {
        font-size: 11px;
        color: var(--green);
        font-weight: 600;
        text-transform: none;
        letter-spacing: 0;
        cursor: pointer;
        text-decoration: none;
    }

    /* ===== CHART ===== */
    .chart-card {
        margin: 0 16px 16px;
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 16px;
    }

    .chart-card canvas { width: 100% !important; }

    .chart-legend {
        display: flex;
        gap: 16px;
        margin-bottom: 12px;
    }

    .chart-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        color: var(--muted);
    }

    .legend-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
    }

    /* ===== TRANSACTION GROUPS ===== */
    .tx-group {
        margin: 0 16px 4px;
    }

    .tx-group-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0 7px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 4px;
    }

    .tx-group-date {
        font-size: 12px;
        font-weight: 700;
        color: var(--muted);
    }

    .tx-group-date.today { color: var(--green); }
    .tx-group-date.yesterday { color: var(--yellow); }

    .tx-group-total {
        font-size: 12px;
        font-weight: 700;
        color: var(--muted);
    }

    .tx-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.03);
        cursor: pointer;
        transition: background 0.1s;
        border-radius: 8px;
        padding-left: 4px;
        padding-right: 4px;
        margin: 0 -4px;
    }
    .tx-item:last-child { border-bottom: none; }
    .tx-item:active { background: rgba(255,255,255,0.03); }

    .tx-cat-icon {
        width: 44px; height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .tx-info { flex: 1; min-width: 0; }

    .tx-cat-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tx-note {
        font-size: 11px;
        color: var(--muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 2px;
    }

    .tx-account-tag {
        font-size: 10px;
        color: var(--muted2);
        margin-top: 2px;
    }

    .tx-amount {
        font-size: 14px;
        font-weight: 700;
        text-align: right;
        flex-shrink: 0;
    }

    .tx-amount.income { color: var(--green); }
    .tx-amount.expense { color: var(--red); }
    .tx-amount.transfer { color: var(--blue); }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--muted);
    }

    .empty-state .es-icon { font-size: 48px; margin-bottom: 12px; opacity: 0.5; }
    .empty-state .es-title { font-size: 14px; font-weight: 600; margin-bottom: 6px; }
    .empty-state .es-desc { font-size: 12px; opacity: 0.7; }

    /* ===== BOTTOM NAVIGATION ===== */
    .bottom-nav {
        flex-shrink: 0;
        background: var(--bg2);
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-around;
        padding: 8px 0 max(8px, env(safe-area-inset-bottom));
        position: relative;
        z-index: 20;
    }

    .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 3px;
        padding: 4px 12px;
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 12px;
        background: none;
        border: none;
        color: var(--muted2);
        font-family: inherit;
    }

    .nav-item:active { transform: scale(0.9); }
    .nav-item.active { color: var(--green); }

    .nav-item .nav-icon { font-size: 20px; line-height: 1; }
    .nav-item .nav-label { font-size: 10px; font-weight: 600; }

    /* FAB — Center Button */
    .fab-container {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fab {
        width: 54px; height: 54px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--green3), var(--green));
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #fff;
        font-size: 24px;
        box-shadow: 0 6px 20px rgba(0,196,140,0.45);
        margin-top: -24px;
        transition: all 0.2s;
        font-family: inherit;
    }
    .fab:active { transform: scale(0.92); box-shadow: 0 3px 12px rgba(0,196,140,0.3); }

    /* ===== SCREENS (full-height panels) ===== */
    .screen {
        position: absolute;
        inset: 0;
        z-index: 50;
        display: flex;
        flex-direction: column;
        background: var(--bg);
        transform: translateY(100%);
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .screen.open { transform: translateY(0); }

    /* ===== ADD TRANSACTION SCREEN ===== */
    .add-tx-screen {}

    .add-tx-header {
        background: var(--bg2);
        border-bottom: 1px solid var(--border);
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .add-tx-close {
        width: 32px; height: 32px;
        border-radius: 10px;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border);
        color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: 16px;
    }
    .add-tx-close:active { background: rgba(255,82,82,0.15); color: var(--red); }

    .add-tx-type-tabs {
        display: flex;
        background: var(--bg3);
        border-radius: 12px;
        padding: 3px;
        gap: 2px;
    }

    .type-tab {
        flex: 1;
        text-align: center;
        padding: 7px 10px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: transparent;
        color: var(--muted);
        font-family: inherit;
    }

    .type-tab.active-expense { background: var(--red); color: #fff; }
    .type-tab.active-income  { background: var(--green); color: #fff; }
    .type-tab.active-transfer { background: var(--blue); color: #fff; }

    /* Amount Display */
    .amount-display {
        background: var(--bg2);
        padding: 20px 20px 16px;
        text-align: right;
        flex-shrink: 0;
    }

    .amount-display .amount-label {
        font-size: 11px;
        color: var(--muted);
        font-weight: 600;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .amount-display .amount-value {
        font-size: 38px;
        font-weight: 800;
        line-height: 1;
        color: var(--text);
        min-height: 46px;
        word-break: break-all;
    }

    .amount-display .amount-value.expense-color { color: var(--red); }
    .amount-display .amount-value.income-color { color: var(--green); }
    .amount-display .amount-value.transfer-color { color: var(--blue); }

    /* Category Picker */
    .cat-picker-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 12px 16px;
    }

    .cat-section-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 10px;
        margin-top: 4px;
    }

    .cat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 16px;
    }

    .cat-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        padding: 10px 4px;
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.15s;
        background: var(--bg2);
        border: 2px solid transparent;
    }
    .cat-btn:active { transform: scale(0.92); }
    .cat-btn.selected { border-color: var(--green); background: rgba(0,196,140,0.1); }
    .cat-btn.selected-expense { border-color: var(--red); background: rgba(255,82,82,0.1); }

    .cat-btn .cat-emoji { font-size: 22px; line-height: 1; }
    .cat-btn .cat-label { font-size: 10px; font-weight: 600; color: var(--muted); text-align: center; line-height: 1.2; }
    .cat-btn.selected .cat-label { color: var(--green); }
    .cat-btn.selected-expense .cat-label { color: var(--red); }

    /* Add TX Details */
    .add-tx-details {
        padding: 12px 16px;
        background: var(--bg2);
        border-top: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex-shrink: 0;
    }

    .detail-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .detail-icon {
        width: 32px; height: 32px;
        border-radius: 10px;
        background: var(--bg3);
        display: flex; align-items: center; justify-content: center;
        color: var(--muted);
        font-size: 14px;
        flex-shrink: 0;
    }

    .detail-input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        color: var(--text);
        font-size: 13px;
        font-weight: 500;
        font-family: inherit;
    }

    .detail-input::placeholder { color: var(--muted2); }

    .detail-select {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        color: var(--text);
        font-size: 13px;
        font-weight: 500;
        font-family: inherit;
        cursor: pointer;
        -webkit-appearance: none;
    }

    /* Numpad */
    .numpad {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1px;
        background: var(--border);
        border-top: 1px solid var(--border);
        flex-shrink: 0;
    }

    .numpad-key {
        background: var(--bg2);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px 0;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.1s;
        border: none;
        color: var(--text);
        font-family: inherit;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }
    .numpad-key:active { background: rgba(255,255,255,0.06); }
    .numpad-key.key-ok {
        background: var(--green);
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        grid-row: span 2;
    }
    .numpad-key.key-ok:active { background: var(--green2); }
    .numpad-key.key-del { color: var(--muted); font-size: 20px; }
    .numpad-key.key-00 { font-size: 15px; }

    /* ===== MODALS (bottom sheet) ===== */
    .bottom-sheet-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        z-index: 60;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    .bottom-sheet {
        width: 100%;
        max-width: 430px;
        background: var(--bg2);
        border-radius: 24px 24px 0 0;
        border-top: 1px solid var(--border);
        padding: 20px 20px max(20px, env(safe-area-inset-bottom));
        animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        max-height: 85vh;
        overflow-y: auto;
    }

    @keyframes slideUp {
        from { transform: translateY(100%); }
        to { transform: translateY(0); }
    }

    .sheet-handle {
        width: 36px; height: 4px;
        background: rgba(255,255,255,0.15);
        border-radius: 2px;
        margin: -8px auto 18px;
    }

    .sheet-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 18px;
    }

    .form-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 6px;
    }

    .form-input {
        width: 100%;
        background: var(--bg3);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 12px 14px;
        color: var(--text);
        font-size: 14px;
        font-family: inherit;
        outline: none;
        transition: border-color 0.2s;
        margin-bottom: 14px;
    }
    .form-input:focus { border-color: var(--green); }

    .form-select {
        width: 100%;
        background: var(--bg3);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 12px 14px;
        color: var(--text);
        font-size: 14px;
        font-family: inherit;
        outline: none;
        margin-bottom: 14px;
        -webkit-appearance: none;
        cursor: pointer;
    }

    .btn-submit {
        width: 100%;
        background: linear-gradient(135deg, var(--green), var(--green2));
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        padding: 14px;
        border-radius: 14px;
        border: none;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.2s;
        box-shadow: 0 4px 16px rgba(0,196,140,0.3);
    }
    .btn-submit:active { transform: scale(0.98); box-shadow: 0 2px 8px rgba(0,196,140,0.2); }

    /* ===== DEBTS TAB ===== */
    .debt-summary {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding: 16px 16px 0;
    }

    .debt-summary-card {
        border-radius: 16px;
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .dsc-lend { background: rgba(0,196,140,0.1); border: 1px solid rgba(0,196,140,0.2); }
    .dsc-borrow { background: rgba(255,82,82,0.1); border: 1px solid rgba(255,82,82,0.2); }

    .dsc-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
    .dsc-lend .dsc-label { color: var(--green); }
    .dsc-borrow .dsc-label { color: var(--red); }

    .dsc-value { font-size: 16px; font-weight: 800; color: var(--text); }
    .dsc-count { font-size: 10px; color: var(--muted); }

    .debt-item {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 14px;
        margin: 8px 16px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .debt-avatar {
        width: 42px; height: 42px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        font-weight: 800;
        flex-shrink: 0;
        color: #fff;
    }
    .debt-lend-av { background: linear-gradient(135deg, var(--green), var(--green2)); }
    .debt-borrow-av { background: linear-gradient(135deg, var(--red), var(--red2)); }

    .debt-info { flex: 1; min-width: 0; }
    .debt-name { font-size: 13px; font-weight: 700; color: var(--text); }
    .debt-meta { font-size: 11px; color: var(--muted); margin-top: 2px; }

    .debt-right { text-align: right; flex-shrink: 0; }
    .debt-amount { font-size: 14px; font-weight: 800; }
    .debt-amount.lend { color: var(--green); }
    .debt-amount.borrow { color: var(--red); }

    .debt-status {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 99px;
        margin-top: 4px;
        display: inline-block;
        cursor: pointer;
    }
    .ds-paid { background: rgba(255,255,255,0.08); color: var(--muted); }
    .ds-unpaid { background: rgba(0,196,140,0.15); color: var(--green); }

    /* ===== INVESTMENTS TAB ===== */
    .portfolio-header {
        margin: 16px 16px 0;
        background: linear-gradient(135deg, rgba(0,196,140,0.1), rgba(68,138,255,0.1));
        border: 1px solid rgba(0,196,140,0.2);
        border-radius: 20px;
        padding: 18px;
        text-align: center;
    }

    .ph-label { font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }
    .ph-value { font-size: 28px; font-weight: 800; color: var(--text); margin: 6px 0; }
    .ph-pnl { font-size: 14px; font-weight: 700; }
    .ph-pnl.positive { color: var(--green); }
    .ph-pnl.negative { color: var(--red); }

    .inv-item {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 14px;
        margin: 10px 16px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .inv-symbol-badge {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px;
        font-weight: 800;
        flex-shrink: 0;
        color: #fff;
        letter-spacing: -0.5px;
    }
    .inv-crypto { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .inv-stock  { background: linear-gradient(135deg, #448aff, #2979ff); }

    .inv-info { flex: 1; min-width: 0; }
    .inv-name { font-size: 13px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 6px; }
    .inv-type-tag { font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 6px; background: rgba(255,255,255,0.08); color: var(--muted); text-transform: uppercase; }
    .inv-qty { font-size: 11px; color: var(--muted); margin-top: 3px; }

    .inv-right { text-align: right; flex-shrink: 0; }
    .inv-value { font-size: 14px; font-weight: 800; color: var(--text); }
    .inv-pnl-pct { font-size: 12px; font-weight: 700; margin-top: 2px; }
    .inv-pnl-pct.pos { color: var(--green); }
    .inv-pnl-pct.neg { color: var(--red); }

    /* ===== TOAST ===== */
    .toast {
        position: fixed;
        top: 70px;
        left: 50%;
        transform: translateX(-50%) translateY(-20px);
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 600;
        z-index: 999;
        white-space: nowrap;
        box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
    .toast.success { border-color: rgba(0,196,140,0.3); color: var(--green); }
    .toast.error { border-color: rgba(255,82,82,0.3); color: var(--red); }

    /* ===== LOADING ===== */
    .loading-overlay {
        position: fixed;
        inset: 0;
        background: rgba(13,17,23,0.7);
        backdrop-filter: blur(4px);
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .spinner {
        width: 36px; height: 36px;
        border: 3px solid rgba(0,196,140,0.2);
        border-top-color: var(--green);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Account type colors for wallet cards */
    .wc-color-0 { background: linear-gradient(135deg, #00c48c, #00a876); }
    .wc-color-1 { background: linear-gradient(135deg, #448aff, #2979ff); }
    .wc-color-2 { background: linear-gradient(135deg, #7c4dff, #651fff); }
    .wc-color-3 { background: linear-gradient(135deg, #ff9100, #ff6d00); }
    .wc-color-4 { background: linear-gradient(135deg, #1de9b6, #00bfa5); }
    .wc-color-5 { background: linear-gradient(135deg, #f06292, #e91e63); }

    .bottom-spacer { height: 20px; }
    </style>
</head>
<body>

<div id="app" x-data="financeApp()" x-init="init()">

    <!-- LOADING -->
    <div class="loading-overlay" x-show="loading" x-transition style="display:none">
        <div class="spinner"></div>
    </div>

    <!-- TOAST -->
    <div class="toast" :class="[toast.show ? 'show' : '', toast.type]" x-text="toast.message"></div>

    <!-- ============ APP HEADER ============ -->
    <header class="app-header">
        <div class="header-month">
            <button class="month-nav-btn" @click="changeMonth(-1)">‹</button>
            <span class="month-label" x-text="monthLabel"></span>
            <button class="month-nav-btn" @click="changeMonth(1)">›</button>
        </div>
        <div class="header-right">
            <div class="online-dot" x-show="isOnline"></div>
            <form action="{{ route('logout') }}" method="POST" style="margin:0">
                @csrf
                <button type="submit" class="header-icon-btn" title="Đăng xuất">🚪</button>
            </form>
        </div>
    </header>

    <!-- ============ MAIN CONTENT ============ -->
    <div class="app-content">

        <!-- ====== TAB: HOME ====== -->
        <div x-show="activeTab === 'home'" x-transition:enter="transition ease-out duration-150">

            <!-- Wallet Swiper -->
            <div class="wallet-section">
                <div class="wallet-swiper" id="walletSwiper">
                    <template x-for="(acc, idx) in accounts" :key="acc.id">
                        <div class="wallet-card" :class="'wc-color-' + (idx % 6)">
                            <div class="wc-type">
                                <span x-text="acc.type === 'bank' ? '🏦' : (acc.type === 'e-wallet' ? '📱' : '👛')"></span>
                                <span x-text="acc.type === 'bank' ? 'Ngân hàng' : (acc.type === 'e-wallet' ? 'Ví điện tử' : 'Tiền mặt')"></span>
                            </div>
                            <div class="wc-name" x-text="acc.name"></div>
                            <div class="wc-balance" x-text="formatShort(acc.balance)"></div>
                            <div class="wc-currency">VNĐ</div>
                        </div>
                    </template>
                    <div class="wallet-add-card" @click="showAddAccountSheet = true">
                        <span class="add-icon">＋</span>
                        <span>Thêm ví</span>
                    </div>
                </div>
            </div>

            <!-- Month Summary -->
            <div class="month-summary">
                <div class="summary-card sc-income">
                    <div class="sc-label">↓ Thu nhập</div>
                    <div class="sc-value" x-text="formatShort(monthStats.income)"></div>
                </div>
                <div class="summary-card sc-expense">
                    <div class="sc-label">↑ Chi phí</div>
                    <div class="sc-value" x-text="formatShort(monthStats.expense)"></div>
                </div>
            </div>

            <!-- Weekly Bar Chart -->
            <div class="chart-card">
                <div class="chart-legend">
                    <div class="chart-legend-item">
                        <div class="legend-dot" style="background: var(--green)"></div>
                        Thu nhập
                    </div>
                    <div class="chart-legend-item">
                        <div class="legend-dot" style="background: var(--red)"></div>
                        Chi phí
                    </div>
                </div>
                <canvas id="weekChart" height="120"></canvas>
            </div>

            <!-- Recent Transactions -->
            <div class="section-title">
                Giao dịch gần đây
                <a class="section-more" @click.prevent="activeTab = 'transactions'">Xem tất cả</a>
            </div>

            <!-- Grouped Transactions -->
            <template x-for="group in groupedTransactions.slice(0,3)" :key="group.date">
                <div class="tx-group">
                    <div class="tx-group-header">
                        <span class="tx-group-date"
                              :class="group.label === 'Hôm nay' ? 'today' : (group.label === 'Hôm qua' ? 'yesterday' : '')"
                              x-text="group.label"></span>
                        <span class="tx-group-total" x-text="formatVnd(group.netAmount)"></span>
                    </div>
                    <template x-for="tx in group.items.slice(0,4)" :key="tx.id">
                        <div class="tx-item">
                            <div class="tx-cat-icon" :style="'background:' + getCatColor(tx.category) + '22'">
                                <span x-text="getCatEmoji(tx.category)"></span>
                            </div>
                            <div class="tx-info">
                                <div class="tx-cat-name" x-text="tx.category"></div>
                                <div class="tx-note" x-text="tx.note || 'Không có ghi chú'"></div>
                                <div class="tx-account-tag" x-text="tx.account ? tx.account.name : ''"></div>
                            </div>
                            <div>
                                <div class="tx-amount" :class="tx.type" x-text="(tx.type === 'income' ? '+' : (tx.type === 'expense' ? '-' : '⇄ ')) + formatShort(tx.amount)"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="transactions.length === 0">
                <div class="empty-state">
                    <div class="es-icon">💸</div>
                    <div class="es-title">Chưa có giao dịch</div>
                    <div class="es-desc">Nhấn nút + để ghi chép giao dịch đầu tiên</div>
                </div>
            </template>

            <div class="bottom-spacer"></div>
        </div>

        <!-- ====== TAB: TRANSACTIONS ====== -->
        <div x-show="activeTab === 'transactions'" x-transition:enter="transition ease-out duration-150">
            <div style="padding: 14px 16px 10px; display:flex; justify-content:flex-end;">
                <button @click="openAddTxScreen('expense')"
                    style="background: linear-gradient(135deg, var(--green), var(--green2)); color:#fff; border:none; border-radius:12px; padding: 8px 16px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; display:flex; align-items:center; gap:6px; box-shadow: 0 4px 14px rgba(0,196,140,0.3)">
                    ＋ Thêm giao dịch
                </button>
            </div>

            <!-- Month filter bar -->
            <div style="display:flex; align-items:center; justify-content:space-between; padding:0 16px 12px; gap:8px;">
                <div style="background:var(--bg2); border:1px solid var(--border); border-radius:10px; padding:6px 12px; font-size:12px; font-weight:700; color:var(--green)" x-text="'Thu: ' + formatShort(monthStats.income)"></div>
                <div style="background:var(--bg2); border:1px solid var(--border); border-radius:10px; padding:6px 12px; font-size:12px; font-weight:700; color:var(--red)" x-text="'Chi: ' + formatShort(monthStats.expense)"></div>
                <div style="background:var(--bg2); border:1px solid var(--border); border-radius:10px; padding:6px 12px; font-size:12px; font-weight:700; color:var(--text)" x-text="'Còn: ' + formatShort(monthStats.income - monthStats.expense)"></div>
            </div>

            <template x-if="groupedTransactions.length === 0">
                <div class="empty-state">
                    <div class="es-icon">📋</div>
                    <div class="es-title">Tháng này chưa có giao dịch</div>
                    <div class="es-desc">Nhấn nút ＋ để thêm</div>
                </div>
            </template>

            <template x-for="group in groupedTransactions" :key="group.date">
                <div class="tx-group">
                    <div class="tx-group-header">
                        <span class="tx-group-date"
                              :class="group.label === 'Hôm nay' ? 'today' : (group.label === 'Hôm qua' ? 'yesterday' : '')"
                              x-text="group.label"></span>
                        <span class="tx-group-total" x-text="formatVnd(group.netAmount)"></span>
                    </div>
                    <template x-for="tx in group.items" :key="tx.id">
                        <div class="tx-item" @click="confirmDeleteTx(tx)">
                            <div class="tx-cat-icon" :style="'background:' + getCatColor(tx.category) + '22'">
                                <span x-text="getCatEmoji(tx.category)"></span>
                            </div>
                            <div class="tx-info">
                                <div class="tx-cat-name" x-text="tx.category"></div>
                                <div class="tx-note" x-text="tx.note || 'Không có ghi chú'"></div>
                                <div class="tx-account-tag" x-text="tx.account ? tx.account.name : ''"></div>
                            </div>
                            <div>
                                <div class="tx-amount" :class="tx.type" x-text="(tx.type === 'income' ? '+' : (tx.type === 'expense' ? '-' : '⇄ ')) + formatVnd(tx.amount)"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
            <div class="bottom-spacer"></div>
        </div>

        <!-- ====== TAB: DEBTS ====== -->
        <div x-show="activeTab === 'debts'" x-transition:enter="transition ease-out duration-150">
            <div class="debt-summary">
                <div class="debt-summary-card dsc-lend">
                    <div class="dsc-label">📤 Cho vay</div>
                    <div class="dsc-value" x-text="formatShort(overview.total_lend)"></div>
                    <div class="dsc-count" x-text="debts.filter(d=>d.type==='lend').length + ' khoản'"></div>
                </div>
                <div class="debt-summary-card dsc-borrow">
                    <div class="dsc-label">📥 Đi vay</div>
                    <div class="dsc-value" x-text="formatShort(overview.total_borrow)"></div>
                    <div class="dsc-count" x-text="debts.filter(d=>d.type==='borrow').length + ' khoản'"></div>
                </div>
            </div>

            <div style="padding: 14px 16px 6px; display:flex; justify-content:space-between; align-items:center">
                <span style="font-size:12px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.06em">Danh sách nợ vay</span>
                <button @click="showAddDebtSheet = true"
                    style="background: linear-gradient(135deg, var(--green), var(--green2)); color:#fff; border:none; border-radius:10px; padding:7px 14px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit">
                    ＋ Thêm nợ
                </button>
            </div>

            <template x-if="debts.length === 0">
                <div class="empty-state">
                    <div class="es-icon">🤝</div>
                    <div class="es-title">Không có khoản nợ nào</div>
                    <div class="es-desc">Nhấn ＋ Thêm nợ để ghi nhận</div>
                </div>
            </template>

            <template x-for="d in debts" :key="d.id">
                <div class="debt-item">
                    <div class="debt-avatar" :class="d.type === 'lend' ? 'debt-lend-av' : 'debt-borrow-av'"
                         x-text="d.partner_name ? d.partner_name.charAt(0).toUpperCase() : '?'"></div>
                    <div class="debt-info">
                        <div class="debt-name" x-text="d.partner_name"></div>
                        <div class="debt-meta">
                            <span x-text="d.type === 'lend' ? '📤 Cho vay' : '📥 Đi vay'"></span>
                            <template x-if="d.due_date">
                                <span x-text="' · Hạn: ' + formatDate(d.due_date)"></span>
                            </template>
                        </div>
                        <div class="debt-meta" x-text="d.note" style="font-style:italic; margin-top:2px"></div>
                    </div>
                    <div class="debt-right">
                        <div class="debt-amount" :class="d.type" x-text="formatShort(d.amount)"></div>
                        <div>
                            <span class="debt-status" :class="d.status === 'paid' ? 'ds-paid' : 'ds-unpaid'"
                                  @click="toggleDebt(d.id)"
                                  x-text="d.status === 'paid' ? '✓ Đã trả' : '⏳ Chưa trả'"></span>
                        </div>
                        <button @click="deleteDebt(d.id)" style="background:none;border:none;color:var(--muted2);cursor:pointer;font-size:13px;margin-top:3px;padding:2px">🗑</button>
                    </div>
                </div>
            </template>
            <div class="bottom-spacer"></div>
        </div>

        <!-- ====== TAB: INVESTMENTS ====== -->
        <div x-show="activeTab === 'investments'" x-transition:enter="transition ease-out duration-150">
            <!-- Portfolio Header -->
            <div class="portfolio-header">
                <div class="ph-label">Tổng danh mục đầu tư</div>
                <div class="ph-value" x-text="formatShort(overview.total_investment)"></div>
                <div class="ph-pnl" :class="overview.investment_pnl >= 0 ? 'positive' : 'negative'"
                     x-text="(overview.investment_pnl >= 0 ? '▲ +' : '▼ ') + formatShort(overview.investment_pnl) + ' (' + overview.investment_pnl_percent.toFixed(2) + '%)'"></div>
            </div>

            <div style="padding: 14px 16px 6px; display:flex; justify-content:space-between; align-items:center">
                <span style="font-size:12px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.06em">Danh mục</span>
                <div style="display:flex; gap:8px">
                    <button @click="updateRates()"
                        style="background:var(--bg2); border:1px solid var(--border); color:var(--green); border-radius:10px; padding:7px 12px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit">
                        🔄 Cập nhật tỷ giá
                    </button>
                    <button @click="showAddInvestmentSheet = true"
                        style="background: linear-gradient(135deg, var(--green), var(--green2)); color:#fff; border:none; border-radius:10px; padding:7px 14px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit">
                        ＋ Thêm
                    </button>
                </div>
            </div>

            <template x-if="investments.length === 0">
                <div class="empty-state">
                    <div class="es-icon">📈</div>
                    <div class="es-title">Chưa có danh mục đầu tư</div>
                    <div class="es-desc">Nhấn ＋ để thêm Crypto hoặc Cổ phiếu</div>
                </div>
            </template>

            <template x-for="inv in investments" :key="inv.id">
                <div class="inv-item">
                    <div class="inv-symbol-badge" :class="inv.type === 'crypto' ? 'inv-crypto' : 'inv-stock'"
                         x-text="inv.symbol"></div>
                    <div class="inv-info">
                        <div class="inv-name">
                            <span x-text="inv.symbol"></span>
                            <span class="inv-type-tag" x-text="inv.type === 'crypto' ? 'Crypto' : 'Cổ phiếu'"></span>
                        </div>
                        <div class="inv-qty">
                            SL: <span x-text="inv.quantity"></span> ×
                            Mua: <span x-text="formatShort(inv.buy_price)"></span>
                        </div>
                        <div class="inv-qty" style="color:var(--muted2)">
                            Giá hiện tại: <span x-text="formatShort(inv.current_price)"></span>
                        </div>
                    </div>
                    <div class="inv-right">
                        <div class="inv-value" x-text="formatShort(inv.quantity * inv.current_price)"></div>
                        <div class="inv-pnl-pct"
                             :class="(inv.current_price - inv.buy_price) >= 0 ? 'pos' : 'neg'"
                             x-text="((inv.current_price - inv.buy_price) >= 0 ? '▲ +' : '▼ ') + (((inv.current_price - inv.buy_price) / inv.buy_price) * 100).toFixed(1) + '%'"></div>
                        <button @click="deleteInvestment(inv.id)" style="background:none;border:none;color:var(--muted2);cursor:pointer;font-size:13px;margin-top:4px;padding:2px">🗑</button>
                    </div>
                </div>
            </template>
            <div class="bottom-spacer"></div>
        </div>

    </div><!-- /app-content -->

    <!-- ============ BOTTOM NAVIGATION ============ -->
    <nav class="bottom-nav">
        <button class="nav-item" :class="activeTab === 'home' ? 'active' : ''" @click="activeTab = 'home'">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">Tổng quan</span>
        </button>
        <button class="nav-item" :class="activeTab === 'transactions' ? 'active' : ''" @click="activeTab = 'transactions'">
            <span class="nav-icon">📋</span>
            <span class="nav-label">Giao dịch</span>
        </button>
        <div class="fab-container">
            <button class="fab" @click="openAddTxScreen('expense')" title="Thêm giao dịch">＋</button>
        </div>
        <button class="nav-item" :class="activeTab === 'debts' ? 'active' : ''" @click="activeTab = 'debts'">
            <span class="nav-icon">🤝</span>
            <span class="nav-label">Nợ vay</span>
        </button>
        <button class="nav-item" :class="activeTab === 'investments' ? 'active' : ''" @click="activeTab = 'investments'">
            <span class="nav-icon">📈</span>
            <span class="nav-label">Đầu tư</span>
        </button>
    </nav>

    <!-- ============ ADD TRANSACTION FULL-SCREEN ============ -->
    <div class="screen add-tx-screen" :class="showAddTxScreen ? 'open' : ''">
        <!-- Header -->
        <div class="add-tx-header">
            <div class="add-tx-close" @click="showAddTxScreen = false">✕</div>
            <div class="add-tx-type-tabs">
                <button class="type-tab" :class="txForm.type === 'expense' ? 'active-expense' : ''"
                        @click="txForm.type = 'expense'">Chi phí</button>
                <button class="type-tab" :class="txForm.type === 'income' ? 'active-income' : ''"
                        @click="txForm.type = 'income'">Thu nhập</button>
                <button class="type-tab" :class="txForm.type === 'transfer' ? 'active-transfer' : ''"
                        @click="txForm.type = 'transfer'">Chuyển ví</button>
            </div>
            <div style="width:32px"></div>
        </div>

        <!-- Amount Display -->
        <div class="amount-display">
            <div class="amount-label">Số tiền (₫)</div>
            <div class="amount-value"
                 :class="txForm.type === 'expense' ? 'expense-color' : (txForm.type === 'income' ? 'income-color' : 'transfer-color')"
                 x-text="numpadDisplay || '0'"></div>
        </div>

        <!-- Category Picker -->
        <div class="cat-picker-scroll">
            <template x-if="txForm.type !== 'transfer'">
                <div>
                    <div class="cat-section-label" x-text="txForm.type === 'expense' ? 'Chọn danh mục chi phí' : 'Chọn danh mục thu nhập'"></div>
                    <div class="cat-grid">
                        <template x-for="cat in currentCategories" :key="cat.name">
                            <div class="cat-btn"
                                 :class="txForm.category === cat.name ? (txForm.type === 'expense' ? 'selected-expense' : 'selected') : ''"
                                 @click="txForm.category = cat.name">
                                <span class="cat-emoji" x-text="cat.emoji"></span>
                                <span class="cat-label" x-text="cat.name"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Transaction Details -->
        <div class="add-tx-details">
            <div class="detail-row">
                <div class="detail-icon">💳</div>
                <select class="detail-select" x-model="txForm.account_id">
                    <option value="">Chọn ví...</option>
                    <template x-for="acc in accounts" :key="acc.id">
                        <option :value="acc.id" x-text="acc.name + ' (' + formatShort(acc.balance) + ')'"></option>
                    </template>
                </select>
            </div>
            <template x-if="txForm.type === 'transfer'">
                <div class="detail-row">
                    <div class="detail-icon">➡️</div>
                    <select class="detail-select" x-model="txForm.to_account_id">
                        <option value="">Ví đích...</option>
                        <template x-for="acc in accounts" :key="acc.id">
                            <option :value="acc.id" x-text="acc.name"></option>
                        </template>
                    </select>
                </div>
            </template>
            <div class="detail-row">
                <div class="detail-icon">📅</div>
                <input type="date" class="detail-input" x-model="txForm.transaction_date">
            </div>
            <div class="detail-row">
                <div class="detail-icon">📝</div>
                <input type="text" class="detail-input" placeholder="Ghi chú..." x-model="txForm.note">
            </div>
        </div>

        <!-- Custom Numpad -->
        <div class="numpad">
            <template x-for="k in ['7','8','9','🔙','4','5','6','00','1','2','3','OK','.',  '0', 'C', '']" :key="k">
                <button class="numpad-key"
                        :class="k === 'OK' ? 'key-ok' : (k === '🔙' ? 'key-del' : (k === '00' ? 'key-00' : ''))"
                        :style="k === '' ? 'display:none' : ''"
                        @click="numpadPress(k)"
                        x-text="k === 'OK' ? 'LƯU' : k">
                </button>
            </template>
        </div>
    </div>

    <!-- ============ BOTTOM SHEETS (Modals) ============ -->

    <!-- Add Account Sheet -->
    <div class="bottom-sheet-overlay" x-show="showAddAccountSheet" @click.self="showAddAccountSheet = false" style="display:none" x-transition>
        <div class="bottom-sheet">
            <div class="sheet-handle"></div>
            <div class="sheet-title">➕ Thêm ví / Tài khoản</div>

            <div class="form-label">Tên ví</div>
            <input type="text" class="form-input" placeholder="Ví dụ: Techcombank, Tiền mặt..." x-model="accountForm.name">

            <div class="form-label">Loại tài khoản</div>
            <select class="form-select" x-model="accountForm.type">
                <option value="cash">👛 Tiền mặt</option>
                <option value="bank">🏦 Ngân hàng</option>
                <option value="e-wallet">📱 Ví điện tử</option>
                <option value="other">📦 Khác</option>
            </select>

            <div class="form-label">Số dư ban đầu (VNĐ)</div>
            <input type="number" class="form-input" placeholder="0" x-model="accountForm.balance" style="color:var(--green); font-weight:700; font-size:18px">

            <button class="btn-submit" @click="submitAccount()">Tạo ví ngay</button>
        </div>
    </div>

    <!-- Add Debt Sheet -->
    <div class="bottom-sheet-overlay" x-show="showAddDebtSheet" @click.self="showAddDebtSheet = false" style="display:none" x-transition>
        <div class="bottom-sheet">
            <div class="sheet-handle"></div>
            <div class="sheet-title">📝 Thêm khoản nợ</div>

            <div class="form-label">Tên người liên quan</div>
            <input type="text" class="form-input" placeholder="Tên người vay / cho vay..." x-model="debtForm.partner_name">

            <div class="form-label">Loại</div>
            <select class="form-select" x-model="debtForm.type">
                <option value="lend">📤 Tôi cho vay (Họ nợ tôi)</option>
                <option value="borrow">📥 Tôi đi vay (Tôi nợ họ)</option>
            </select>

            <div class="form-label">Số tiền (VNĐ)</div>
            <input type="number" class="form-input" placeholder="0" x-model="debtForm.amount" style="color:var(--green); font-weight:700; font-size:18px">

            <div class="form-label">Hạn thanh toán (không bắt buộc)</div>
            <input type="date" class="form-input" x-model="debtForm.due_date">

            <div class="form-label">Ghi chú</div>
            <input type="text" class="form-input" placeholder="Lý do vay mượn..." x-model="debtForm.note">

            <button class="btn-submit" @click="submitDebt()">Ghi nhận khoản nợ</button>
        </div>
    </div>

    <!-- Add Investment Sheet -->
    <div class="bottom-sheet-overlay" x-show="showAddInvestmentSheet" @click.self="showAddInvestmentSheet = false" style="display:none" x-transition>
        <div class="bottom-sheet">
            <div class="sheet-handle"></div>
            <div class="sheet-title">📈 Thêm tài sản đầu tư</div>

            <div class="form-label">Loại tài sản</div>
            <select class="form-select" x-model="investForm.type">
                <option value="crypto">🪙 Crypto (Bitcoin, ETH...)</option>
                <option value="stock">📊 Cổ phiếu</option>
            </select>

            <div class="form-label">Mã tài sản (Symbol)</div>
            <input type="text" class="form-input" placeholder="VD: BTC, ETH, VNM..." x-model="investForm.symbol" style="text-transform:uppercase">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px">
                <div>
                    <div class="form-label">Số lượng</div>
                    <input type="number" class="form-input" placeholder="0" x-model="investForm.quantity" step="any">
                </div>
                <div>
                    <div class="form-label">Giá mua (VNĐ)</div>
                    <input type="number" class="form-input" placeholder="0" x-model="investForm.buy_price">
                </div>
            </div>

            <button class="btn-submit" @click="submitInvestment()">Thêm vào danh mục</button>
        </div>
    </div>

</div><!-- /#app -->

<script>
const CSRF = document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]
    ? decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1])
    : '{{ csrf_token() }}';

// ===== CATEGORY DATA =====
const EXPENSE_CATS = [
    { name: 'Ăn uống',     emoji: '🍜', color: '#FF6B6B' },
    { name: 'Mua sắm',     emoji: '🛒', color: '#FF9F43' },
    { name: 'Di chuyển',   emoji: '🚗', color: '#54A0FF' },
    { name: 'Nhà ở',       emoji: '🏠', color: '#5F27CD' },
    { name: 'Sức khoẻ',    emoji: '💊', color: '#00D2D3' },
    { name: 'Giải trí',    emoji: '🎮', color: '#C8D6E5' },
    { name: 'Giáo dục',    emoji: '📚', color: '#786FA6' },
    { name: 'Làm đẹp',     emoji: '💄', color: '#FF9FF3' },
    { name: 'Cà phê',      emoji: '☕', color: '#A29BFE' },
    { name: 'Điện thoại',  emoji: '📱', color: '#6C5CE7' },
    { name: 'Du lịch',     emoji: '✈️', color: '#00CEC9' },
    { name: 'Quà tặng',    emoji: '🎁', color: '#FDCB6E' },
    { name: 'Tiện ích',    emoji: '💡', color: '#FFEAA7' },
    { name: 'Sửa chữa',    emoji: '🔧', color: '#B2BEC3' },
    { name: 'Thú cưng',    emoji: '🐾', color: '#E17055' },
    { name: 'Khác',        emoji: '📦', color: '#636E72' },
];

const INCOME_CATS = [
    { name: 'Lương',       emoji: '💰', color: '#00C48C' },
    { name: 'Kinh doanh',  emoji: '💼', color: '#54A0FF' },
    { name: 'Đầu tư',      emoji: '📈', color: '#00E5A0' },
    { name: 'Thưởng',      emoji: '🎯', color: '#FDCB6E' },
    { name: 'Quà',         emoji: '💝', color: '#FF9FF3' },
    { name: 'Hoàn tiền',   emoji: '🔄', color: '#A29BFE' },
    { name: 'Lãi suất',    emoji: '🏦', color: '#1DE9B6' },
    { name: 'Khác',        emoji: '📦', color: '#636E72' },
];

const ALL_CATS = [...EXPENSE_CATS, ...INCOME_CATS];

function getCatData(name) {
    return ALL_CATS.find(c => c.name === name) || { emoji: '💸', color: '#636E72' };
}

function financeApp() {
    return {
        // State
        loading: false,
        isOnline: navigator.onLine,
        activeTab: 'home',
        currentYear: new Date().getFullYear(),
        currentMonth: new Date().getMonth() + 1,

        // Data
        accounts: [],
        transactions: [],
        debts: [],
        investments: [],
        overview: { net_worth: 0, total_cash: 0, total_investment: 0, total_lend: 0, total_borrow: 0, investment_pnl: 0, investment_pnl_percent: 0 },

        // UI state
        showAddTxScreen: false,
        showAddAccountSheet: false,
        showAddDebtSheet: false,
        showAddInvestmentSheet: false,

        // Forms
        numpadDisplay: '',
        txForm: { type: 'expense', account_id: '', to_account_id: '', amount: '', category: '', transaction_date: new Date().toISOString().split('T')[0], note: '' },
        accountForm: { name: '', type: 'cash', balance: '' },
        debtForm: { partner_name: '', type: 'lend', amount: '', due_date: '', note: '' },
        investForm: { symbol: '', type: 'crypto', quantity: '', buy_price: '' },

        // Charts
        weekChartInstance: null,

        // Toast
        toast: { show: false, message: '', type: 'success' },

        // Computed
        get monthLabel() {
            return `Tháng ${this.currentMonth}/${this.currentYear}`;
        },

        get currentCategories() {
            return this.txForm.type === 'income' ? INCOME_CATS : EXPENSE_CATS;
        },

        get monthStats() {
            const filtered = this.transactions.filter(tx => {
                const d = new Date(tx.transaction_date);
                return d.getMonth() + 1 === this.currentMonth && d.getFullYear() === this.currentYear;
            });
            const income  = filtered.filter(t => t.type === 'income').reduce((s, t) => s + parseFloat(t.amount), 0);
            const expense = filtered.filter(t => t.type === 'expense').reduce((s, t) => s + parseFloat(t.amount), 0);
            return { income, expense };
        },

        get filteredTransactions() {
            return this.transactions.filter(tx => {
                const d = new Date(tx.transaction_date);
                return d.getMonth() + 1 === this.currentMonth && d.getFullYear() === this.currentYear;
            });
        },

        get groupedTransactions() {
            const groups = {};
            const today = new Date().toISOString().split('T')[0];
            const yesterday = new Date(Date.now() - 86400000).toISOString().split('T')[0];

            this.filteredTransactions.forEach(tx => {
                const date = tx.transaction_date.split('T')[0];
                if (!groups[date]) groups[date] = [];
                groups[date].push(tx);
            });

            return Object.entries(groups)
                .sort(([a], [b]) => b.localeCompare(a))
                .map(([date, items]) => {
                    const net = items.reduce((s, t) => {
                        if (t.type === 'income')  return s + parseFloat(t.amount);
                        if (t.type === 'expense') return s - parseFloat(t.amount);
                        return s;
                    }, 0);
                    const d = new Date(date + 'T00:00:00');
                    const label = date === today ? 'Hôm nay' : date === yesterday ? 'Hôm qua'
                        : d.toLocaleDateString('vi-VN', { weekday: 'short', day: 'numeric', month: 'numeric' });
                    return { date, label, items, netAmount: net };
                });
        },

        // ===== INIT =====
        async init() {
            window.addEventListener('online',  () => this.isOnline = true);
            window.addEventListener('offline', () => this.isOnline = false);
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            }
            await this.loadData();
        },

        // ===== DATA LOADING =====
        async loadData() {
            this.loading = true;
            try {
                const [accs, txs, dbs, invs, ov] = await Promise.all([
                    fetch('/api/finance/accounts').then(r => r.json()),
                    fetch('/api/finance/transactions').then(r => r.json()),
                    fetch('/api/finance/debts').then(r => r.json()),
                    fetch('/api/finance/investments').then(r => r.json()),
                    fetch('/api/finance/overview').then(r => r.json()),
                ]);
                this.accounts    = accs.accounts    || accs || [];
                this.transactions= txs.transactions || txs || [];
                this.debts       = dbs.debts        || dbs || [];
                this.investments = invs.investments  || invs || [];
                this.overview    = ov;

                this.$nextTick(() => this.renderWeekChart());
            } catch (e) {
                this.showToast('Lỗi tải dữ liệu: ' + e.message, 'error');
            } finally {
                this.loading = false;
            }
        },

        // ===== MONTH NAV =====
        changeMonth(dir) {
            this.currentMonth += dir;
            if (this.currentMonth > 12) { this.currentMonth = 1; this.currentYear++; }
            if (this.currentMonth < 1)  { this.currentMonth = 12; this.currentYear--; }
            this.$nextTick(() => this.renderWeekChart());
        },

        // ===== CHART =====
        renderWeekChart() {
            const canvas = document.getElementById('weekChart');
            if (!canvas) return;
            if (this.weekChartInstance) { this.weekChartInstance.destroy(); }

            // Build last 7 days data
            const days = [];
            const incomeData = [];
            const expenseData = [];
            for (let i = 6; i >= 0; i--) {
                const d = new Date(Date.now() - i * 86400000);
                const key = d.toISOString().split('T')[0];
                const dayTxs = this.transactions.filter(t => t.transaction_date && t.transaction_date.startsWith(key));
                days.push(d.toLocaleDateString('vi-VN', { weekday: 'short' }));
                incomeData.push(dayTxs.filter(t => t.type === 'income').reduce((s, t) => s + parseFloat(t.amount), 0) / 1e6);
                expenseData.push(dayTxs.filter(t => t.type === 'expense').reduce((s, t) => s + parseFloat(t.amount), 0) / 1e6);
            }

            this.weekChartInstance = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: days,
                    datasets: [
                        { label: 'Thu (M)', data: incomeData, backgroundColor: 'rgba(0,196,140,0.7)', borderRadius: 6, borderSkipped: false },
                        { label: 'Chi (M)', data: expenseData, backgroundColor: 'rgba(255,82,82,0.7)', borderRadius: 6, borderSkipped: false },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#6e7681', font: { size: 10, family: 'Outfit' } } },
                        y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#6e7681', font: { size: 10, family: 'Outfit' }, callback: v => v + 'M' } },
                    }
                }
            });
        },

        // ===== NUMPAD =====
        openAddTxScreen(type) {
            this.txForm = { type, account_id: this.accounts[0]?.id || '', to_account_id: '', amount: '', category: '', transaction_date: new Date().toISOString().split('T')[0], note: '' };
            this.numpadDisplay = '';
            this.showAddTxScreen = true;
        },

        numpadPress(key) {
            if (key === '🔙') {
                this.numpadDisplay = this.numpadDisplay.slice(0, -1);
            } else if (key === 'C') {
                this.numpadDisplay = '';
            } else if (key === 'OK') {
                this.submitTransaction();
            } else if (key === '.') {
                if (!this.numpadDisplay.includes('.')) this.numpadDisplay += '.';
            } else if (key === '00') {
                if (this.numpadDisplay) this.numpadDisplay += '00';
            } else {
                if (this.numpadDisplay.length < 13) this.numpadDisplay += key;
            }
            this.txForm.amount = this.numpadDisplay;
        },

        // ===== FORMATTERS =====
        formatVnd(v) {
            const n = parseFloat(v) || 0;
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumFractionDigits: 0 }).format(n);
        },

        formatShort(v) {
            const n = parseFloat(v) || 0;
            const abs = Math.abs(n);
            const sign = n < 0 ? '-' : '';
            if (abs >= 1e9) return sign + (abs / 1e9).toFixed(1).replace(/\.0$/, '') + ' tỷ';
            if (abs >= 1e6) return sign + (abs / 1e6).toFixed(1).replace(/\.0$/, '') + ' tr';
            if (abs >= 1e3) return sign + (abs / 1e3).toFixed(0) + 'K';
            return sign + abs.toFixed(0) + '₫';
        },

        formatDate(d) {
            if (!d) return '';
            return new Date(d + 'T00:00:00').toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
        },

        getCatEmoji(name) { return getCatData(name).emoji; },
        getCatColor(name) { return getCatData(name).color; },

        // ===== TOAST =====
        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => this.toast.show = false, 2800);
        },

        // ===== CRUD: TRANSACTIONS =====
        async submitTransaction() {
            const amount = parseFloat(this.txForm.amount);
            if (!amount || amount <= 0) { this.showToast('Vui lòng nhập số tiền', 'error'); return; }
            if (!this.txForm.account_id) { this.showToast('Vui lòng chọn ví', 'error'); return; }
            if (!this.txForm.category && this.txForm.type !== 'transfer') { this.showToast('Vui lòng chọn danh mục', 'error'); return; }

            this.loading = true;
            const payload = { ...this.txForm, amount };
            if (this.txForm.type === 'transfer') payload.category = 'Chuyển ví';

            try {
                const r = await fetch('/api/finance/transactions', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-XSRF-TOKEN': CSRF },
                    body: JSON.stringify(payload)
                });
                const data = await r.json();
                if (data.success) {
                    this.showAddTxScreen = false;
                    this.showToast('✅ Đã ghi chép thành công!');
                    await this.loadData();
                } else {
                    this.showToast(data.message || 'Lỗi!', 'error');
                }
            } catch (e) {
                this.showToast('Lỗi kết nối', 'error');
            } finally {
                this.loading = false;
            }
        },

        async confirmDeleteTx(tx) {
            if (!confirm(`Xoá giao dịch "${tx.category}" — ${this.formatVnd(tx.amount)}?`)) return;
            await this.deleteTransaction(tx.id);
        },

        async deleteTransaction(id) {
            this.loading = true;
            try {
                const r = await fetch('/api/finance/transactions/' + id, {
                    method: 'DELETE',
                    headers: { 'X-XSRF-TOKEN': CSRF }
                });
                const data = await r.json();
                if (data.success) { this.showToast('🗑 Đã xoá giao dịch'); await this.loadData(); }
                else this.showToast(data.message || 'Lỗi!', 'error');
            } catch { this.showToast('Lỗi kết nối', 'error'); }
            finally { this.loading = false; }
        },

        // ===== CRUD: ACCOUNTS =====
        async submitAccount() {
            if (!this.accountForm.name) { this.showToast('Vui lòng nhập tên ví', 'error'); return; }
            this.loading = true;
            try {
                const r = await fetch('/api/finance/accounts', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-XSRF-TOKEN': CSRF },
                    body: JSON.stringify({ ...this.accountForm, balance: parseFloat(this.accountForm.balance) || 0 })
                });
                const data = await r.json();
                if (data.success) {
                    this.showAddAccountSheet = false;
                    this.accountForm = { name: '', type: 'cash', balance: '' };
                    this.showToast('✅ Tạo ví thành công!');
                    await this.loadData();
                } else this.showToast(data.message || 'Lỗi!', 'error');
            } catch { this.showToast('Lỗi kết nối', 'error'); }
            finally { this.loading = false; }
        },

        // ===== CRUD: DEBTS =====
        async submitDebt() {
            if (!this.debtForm.partner_name || !this.debtForm.amount) { this.showToast('Điền đầy đủ thông tin', 'error'); return; }
            this.loading = true;
            try {
                const r = await fetch('/api/finance/debts', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-XSRF-TOKEN': CSRF },
                    body: JSON.stringify({ ...this.debtForm, amount: parseFloat(this.debtForm.amount) })
                });
                const data = await r.json();
                if (data.success) {
                    this.showAddDebtSheet = false;
                    this.debtForm = { partner_name: '', type: 'lend', amount: '', due_date: '', note: '' };
                    this.showToast('✅ Đã ghi nhận khoản nợ!');
                    await this.loadData();
                } else this.showToast(data.message || 'Lỗi!', 'error');
            } catch { this.showToast('Lỗi kết nối', 'error'); }
            finally { this.loading = false; }
        },

        async toggleDebt(id) {
            try {
                const r = await fetch('/api/finance/debts/' + id + '/toggle', {
                    method: 'PATCH',
                    headers: { 'X-XSRF-TOKEN': CSRF }
                });
                const data = await r.json();
                if (data.success) { this.showToast('✅ Cập nhật trạng thái'); await this.loadData(); }
            } catch { this.showToast('Lỗi kết nối', 'error'); }
        },

        async deleteDebt(id) {
            if (!confirm('Xoá khoản nợ này?')) return;
            this.loading = true;
            try {
                const r = await fetch('/api/finance/debts/' + id, { method: 'DELETE', headers: { 'X-XSRF-TOKEN': CSRF } });
                const data = await r.json();
                if (data.success) { this.showToast('🗑 Đã xoá khoản nợ'); await this.loadData(); }
            } catch { this.showToast('Lỗi kết nối', 'error'); }
            finally { this.loading = false; }
        },

        // ===== CRUD: INVESTMENTS =====
        async submitInvestment() {
            if (!this.investForm.symbol || !this.investForm.quantity || !this.investForm.buy_price) {
                this.showToast('Điền đầy đủ thông tin', 'error'); return;
            }
            this.loading = true;
            try {
                const r = await fetch('/api/finance/investments', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-XSRF-TOKEN': CSRF },
                    body: JSON.stringify({
                        ...this.investForm,
                        symbol: this.investForm.symbol.toUpperCase(),
                        quantity: parseFloat(this.investForm.quantity),
                        buy_price: parseFloat(this.investForm.buy_price)
                    })
                });
                const data = await r.json();
                if (data.success) {
                    this.showAddInvestmentSheet = false;
                    this.investForm = { symbol: '', type: 'crypto', quantity: '', buy_price: '' };
                    this.showToast('✅ Thêm tài sản thành công!');
                    await this.loadData();
                } else this.showToast(data.message || 'Lỗi!', 'error');
            } catch { this.showToast('Lỗi kết nối', 'error'); }
            finally { this.loading = false; }
        },

        async deleteInvestment(id) {
            if (!confirm('Xoá tài sản đầu tư này?')) return;
            this.loading = true;
            try {
                const r = await fetch('/api/finance/investments/' + id, { method: 'DELETE', headers: { 'X-XSRF-TOKEN': CSRF } });
                const data = await r.json();
                if (data.success) { this.showToast('🗑 Đã xoá'); await this.loadData(); }
            } catch { this.showToast('Lỗi kết nối', 'error'); }
            finally { this.loading = false; }
        },

        async updateRates() {
            this.loading = true;
            try {
                const r = await fetch('/api/finance/rates/update', { method: 'POST', headers: { 'X-XSRF-TOKEN': CSRF } });
                const data = await r.json();
                this.showToast(data.message || '✅ Đã cập nhật tỷ giá!');
                await this.loadData();
            } catch { this.showToast('Lỗi cập nhật tỷ giá', 'error'); }
            finally { this.loading = false; }
        },
    };
}

// Service Worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
}
</script>
</body>
</html>
