<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no,viewport-fit=cover">
<title>MoneyTracker — Quản Lý Tài Chính</title>
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0d1117">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="MoneyTracker">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --g:#00c48c;--gl:#00e5a8;--gd:#009e71;--gg:rgba(0,196,140,.25);
  --r:#ff4d6d;--rl:#ff6b85;--rd:#e63356;--rg:rgba(255,77,109,.25);
  --b:#4c9aff;--p:#7b5cfa;--o:#ff9f43;--y:#ffd32a;--t:#1de9b6;
  --bg:#0d1117;--bg2:#161b22;--bg3:#21262d;--bg4:#2d333b;
  --tx:#e6edf3;--tx2:#8b949e;--tx3:#6e7681;
  --br:rgba(255,255,255,.08);--br2:rgba(255,255,255,.13);
}
html,body{height:100%;overflow:hidden;background:var(--bg)}
body{font-family:'Inter',sans-serif;color:var(--tx);-webkit-tap-highlight-color:transparent;display:flex;justify-content:center}
input,select,button,textarea{font-family:'Inter',sans-serif}
::-webkit-scrollbar{width:0;height:0}

#app{width:100%;max-width:430px;height:100dvh;display:flex;flex-direction:column;background:var(--bg);overflow:hidden}

/* ── TOPBAR ── */
.topbar{background:var(--bg2);padding:12px 16px 10px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;border-bottom:1px solid var(--br)}
.ta-left{display:flex;align-items:center;gap:10px}
.ta-ava{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--g),var(--b));display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;box-shadow:0 0 0 2px rgba(0,196,140,.3);flex-shrink:0}
.ta-info span{display:block}.ta-greet{font-size:10px;color:var(--tx2)}.ta-name{font-size:13px;font-weight:700}
.ta-right{display:flex;align-items:center;gap:6px}
.ti-btn{width:32px;height:32px;border-radius:10px;background:var(--bg3);border:1px solid var(--br);color:var(--tx2);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;text-decoration:none;transition:all .18s}
.ti-btn:active{transform:scale(.88);background:var(--bg4)}

/* ── CURRENCY BAR ── */
.currency-bar{background:var(--bg2);border-bottom:1px solid var(--br);padding:6px 16px;display:flex;gap:12px;overflow-x:auto;align-items:center}
.currency-bar::-webkit-scrollbar{display:none}
.cur-item{display:flex;align-items:center;gap:5px;white-space:nowrap;flex-shrink:0}
.cur-flag{font-size:12px}
.cur-code{font-size:10px;font-weight:700;color:var(--tx2)}
.cur-val{font-size:11px;font-weight:700;color:var(--g)}
.cur-sep{width:1px;height:14px;background:var(--br);flex-shrink:0}
.cur-time{font-size:10px;color:var(--tx3);flex-shrink:0}

/* ── MONTH NAV ── */
.month-nav{background:var(--bg2);padding:7px 16px;display:flex;align-items:center;justify-content:center;gap:14px;flex-shrink:0}
.mn-btn{width:28px;height:28px;border-radius:8px;background:var(--bg3);border:1px solid var(--br);color:var(--tx2);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:13px;transition:all .18s}
.mn-btn:active{background:var(--g);color:#fff;transform:scale(.9)}
.mn-label{font-size:13px;font-weight:700;min-width:104px;text-align:center}

/* ── SEARCH BAR ── */
.search-bar{padding:8px 16px;background:var(--bg2);border-bottom:1px solid var(--br);display:flex;align-items:center;gap:8px;flex-shrink:0}
.sb-input{flex:1;background:var(--bg3);border:1.5px solid var(--br);border-radius:12px;padding:8px 12px 8px 36px;color:var(--tx);font-size:13px;outline:none;transition:border-color .2s}
.sb-input:focus{border-color:var(--g)}
.sb-icon{position:absolute;left:28px;font-size:14px;color:var(--tx3);pointer-events:none}
.sb-wrap{position:relative;flex:1;display:flex;align-items:center}
.filter-tabs{display:flex;gap:4px;flex-shrink:0}
.filter-tab{padding:6px 10px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;background:var(--bg3);border:1px solid var(--br);color:var(--tx3);transition:all .18s;white-space:nowrap}
.filter-tab.active{background:rgba(0,196,140,.15);border-color:rgba(0,196,140,.3);color:var(--g)}

/* ── SCROLL ── */
.scroll-area{flex:1;overflow-y:auto;overflow-x:hidden;-webkit-overflow-scrolling:touch}

/* ── HERO CARD ── */
.hero{margin:14px 16px 0;border-radius:22px;overflow:hidden;position:relative;background:linear-gradient(135deg,#0d2137,#0a1628 40%,#0d1f35 70%,#091829);padding:22px;border:1px solid rgba(0,196,140,.15)}
.hero::before{content:'';position:absolute;top:-40px;right:-30px;width:180px;height:180px;border-radius:50%;background:radial-gradient(circle,rgba(0,196,140,.15),transparent 70%);pointer-events:none}
.hero-lbl{font-size:10px;font-weight:700;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px;display:flex;align-items:center;gap:6px}
.hero-lbl::before{content:'';width:4px;height:4px;border-radius:50%;background:var(--g);display:inline-block}
.hero-bal{font-size:30px;font-weight:900;color:#fff;letter-spacing:-1px;line-height:1;margin-bottom:3px}
.hero-bal span{font-size:15px;font-weight:600;opacity:.65;margin-right:3px}
.hero-cur{font-size:11px;color:rgba(255,255,255,.38);margin-bottom:16px}
.hero-stats{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.hs{border-radius:14px;padding:10px 12px;display:flex;align-items:center;gap:10px}
.hs-i{background:rgba(0,196,140,.12);border:1px solid rgba(0,196,140,.2)}
.hs-e{background:rgba(255,77,109,.12);border:1px solid rgba(255,77,109,.2)}
.hs-ico{width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
.hs-i .hs-ico{background:rgba(0,196,140,.2)} .hs-e .hs-ico{background:rgba(255,77,109,.2)}
.hs-info{min-width:0}
.hs-label{font-size:9px;font-weight:700;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.06em}
.hs-val{font-size:14px;font-weight:800;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.hs-i .hs-val{color:var(--g)} .hs-e .hs-val{color:var(--r)}

/* ── BUDGET ALERT STRIP ── */
.budget-strip{margin:10px 16px 0}
.budget-alert{background:rgba(255,77,109,.08);border:1px solid rgba(255,77,109,.2);border-radius:14px;padding:10px 14px;display:flex;align-items:center;gap:10px}
.ba-icon{font-size:18px}
.ba-info{flex:1;min-width:0}
.ba-title{font-size:12px;font-weight:700;color:var(--r)}
.ba-sub{font-size:10px;color:var(--tx2);margin-top:1px}
.ba-action{font-size:11px;font-weight:700;color:var(--g);cursor:pointer;white-space:nowrap}

/* ── SECTION HEADER ── */
.sec-hdr{padding:14px 16px 8px;display:flex;align-items:center;justify-content:space-between}
.sec-lbl{font-size:11px;font-weight:700;color:var(--tx2);text-transform:uppercase;letter-spacing:.08em}
.sec-act{font-size:12px;font-weight:600;color:var(--g);cursor:pointer}

/* ── WALLET SWIPER ── */
.wallet-scroll{display:flex;gap:10px;overflow-x:auto;padding:0 16px 4px;scroll-snap-type:x mandatory}
.wallet-scroll::-webkit-scrollbar{display:none}
.wcard{flex-shrink:0;width:170px;height:96px;border-radius:18px;padding:13px 15px;scroll-snap-align:start;cursor:pointer;display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden;transition:transform .18s}
.wcard:active{transform:scale(.95)}
.wcard::after{content:'';position:absolute;top:-18px;right:-18px;width:70px;height:70px;border-radius:50%;background:rgba(255,255,255,.08);pointer-events:none}
.wc0{background:linear-gradient(135deg,#00c48c,#009e71)} .wc1{background:linear-gradient(135deg,#4c9aff,#2979ff)}
.wc2{background:linear-gradient(135deg,#7b5cfa,#5b3ff5)} .wc3{background:linear-gradient(135deg,#ff9f43,#e67e22)}
.wc4{background:linear-gradient(135deg,#ff4d6d,#c9184a)} .wc5{background:linear-gradient(135deg,#1de9b6,#00bfa5)}
.wcard-top{display:flex;align-items:center;justify-content:space-between}
.wcard-ico{font-size:15px;opacity:.85}
.wcard-dots{display:flex;gap:3px}.wcard-dot{width:5px;height:5px;border-radius:50%;background:rgba(255,255,255,.5)}
.wcard-name{font-size:11px;font-weight:600;color:rgba(255,255,255,.8);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.wcard-bal{font-size:15px;font-weight:800;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.wadd{flex-shrink:0;width:86px;height:96px;border-radius:18px;border:2px dashed rgba(255,255,255,.1);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px;cursor:pointer;color:var(--tx3);transition:all .18s;scroll-snap-align:start}
.wadd:active{border-color:var(--g);color:var(--g);transform:scale(.95)}

/* ── CHART ── */
.chart-wrap{margin:4px 16px 0;background:var(--bg2);border:1px solid var(--br);border-radius:20px;padding:14px 16px}
.chart-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.chart-title{font-size:11px;font-weight:700;color:var(--tx2);text-transform:uppercase;letter-spacing:.06em}
.chart-leg{display:flex;gap:12px}
.cl-item{display:flex;align-items:center;gap:4px;font-size:10px;font-weight:600;color:var(--tx3)}
.cl-dot{width:8px;height:8px;border-radius:3px}

/* ── BUDGET PROGRESS ── */
.budget-section{margin:10px 16px 0}
.budget-item{background:var(--bg2);border:1px solid var(--br);border-radius:16px;padding:12px 14px;margin-bottom:8px}
.bi-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.bi-cat{display:flex;align-items:center;gap:8px}
.bi-cat-ico{width:30px;height:30px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:15px}
.bi-cat-name{font-size:13px;font-weight:600;color:var(--tx)}
.bi-amounts{text-align:right}
.bi-used{font-size:12px;font-weight:700;color:var(--tx)}
.bi-limit{font-size:10px;color:var(--tx2)}
.bi-bar{height:6px;background:var(--bg4);border-radius:3px;overflow:hidden}
.bi-bar-fill{height:100%;border-radius:3px;transition:width .5s cubic-bezier(.16,1,.3,1)}
.bi-bar-ok{background:linear-gradient(90deg,var(--g),var(--gl))}
.bi-bar-warn{background:linear-gradient(90deg,var(--o),var(--y))}
.bi-bar-over{background:linear-gradient(90deg,var(--r),var(--rl))}

/* ── TRANSACTION LIST ── */
.tx-grp-wrap{margin:10px 16px 0}
.tx-dh{display:flex;align-items:center;justify-content:space-between;padding:5px 0 7px;border-bottom:1px solid var(--br);margin-bottom:2px}
.tx-dl{font-size:12px;font-weight:700;color:var(--tx2)}
.tx-dl.today{color:var(--g)} .tx-dl.yest{color:var(--y)}
.tx-dn{font-size:11px;font-weight:700;color:var(--tx3)}
.tx-row{display:flex;align-items:center;gap:11px;padding:10px 0;cursor:pointer;border-bottom:1px solid rgba(255,255,255,.03);transition:opacity .15s}
.tx-row:last-child{border-bottom:none}
.tx-row:active{opacity:.65}
.tx-ico{width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.tx-body{flex:1;min-width:0}
.tx-cat{font-size:13px;font-weight:600;color:var(--tx);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tx-meta{display:flex;align-items:center;gap:5px;margin-top:2px;flex-wrap:wrap}
.tx-acc-tag{font-size:10px;font-weight:600;color:var(--tx3);background:var(--bg3);border-radius:6px;padding:2px 6px}
.tx-note{font-size:11px;color:var(--tx3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tx-recur{font-size:10px;color:var(--b);background:rgba(76,154,255,.1);border-radius:6px;padding:2px 5px}
.tx-amount{font-size:14px;font-weight:800;flex-shrink:0;text-align:right}
.tx-amount.income{color:var(--g)} .tx-amount.expense{color:var(--r)} .tx-amount.transfer{color:var(--b)}

/* ── EMPTY STATE ── */
.empty{padding:44px 20px;text-align:center;color:var(--tx3)}
.empty-ico{font-size:50px;opacity:.3;margin-bottom:12px}
.empty-title{font-size:13px;font-weight:700;color:var(--tx2);margin-bottom:5px}
.empty-desc{font-size:11px}

/* ── STATS TAB ── */
.stats-summary{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;padding:12px 16px 0}
.ss-card{background:var(--bg2);border:1px solid var(--br);border-radius:14px;padding:10px 10px}
.ss-label{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px}
.ss-val{font-size:14px;font-weight:800}
.ss-income .ss-label{color:var(--g)} .ss-income .ss-val{color:var(--g)}
.ss-expense .ss-label{color:var(--r)} .ss-expense .ss-val{color:var(--r)}
.ss-rate .ss-label{color:var(--b)} .ss-rate .ss-val{color:var(--b)}

.cat-rank-item{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.04)}
.cat-rank-item:last-child{border-bottom:none}
.cr-rank{font-size:11px;font-weight:800;color:var(--tx3);width:16px;text-align:center;flex-shrink:0}
.cr-ico{width:36px;height:36px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.cr-info{flex:1;min-width:0}
.cr-name{font-size:13px;font-weight:600;color:var(--tx)}
.cr-bar-wrap{height:4px;background:var(--bg4);border-radius:2px;margin-top:4px;overflow:hidden}
.cr-bar{height:100%;border-radius:2px;background:var(--r);transition:width .6s cubic-bezier(.16,1,.3,1)}
.cr-amount{font-size:13px;font-weight:800;color:var(--r);flex-shrink:0}
.cr-pct{font-size:10px;color:var(--tx3);text-align:right;margin-top:2px}

/* ── SAVINGS GOALS ── */
.goal-item{background:var(--bg2);border:1px solid var(--br);border-radius:18px;padding:14px 16px;margin:8px 16px 0}
.gi-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.gi-name{font-size:14px;font-weight:700;color:var(--tx);display:flex;align-items:center;gap:8px}
.gi-pct{font-size:12px;font-weight:800;color:var(--g)}
.gi-amounts{display:flex;justify-content:space-between;margin-bottom:8px;font-size:11px;color:var(--tx2)}
.gi-bar{height:8px;background:var(--bg4);border-radius:4px;overflow:hidden}
.gi-fill{height:100%;border-radius:4px;background:linear-gradient(90deg,var(--g),var(--gl));transition:width .6s cubic-bezier(.16,1,.3,1)}
.gi-deadline{font-size:10px;color:var(--tx3);margin-top:5px}

/* ── DEBTS TAB ── */
.debt-banner{display:grid;grid-template-columns:1fr 1fr;gap:10px;padding:14px 16px 0}
.dbc{border-radius:18px;padding:16px}
.dbc-lend{background:linear-gradient(135deg,rgba(0,196,140,.15),rgba(0,196,140,.05));border:1px solid rgba(0,196,140,.2)}
.dbc-borrow{background:linear-gradient(135deg,rgba(255,77,109,.15),rgba(255,77,109,.05));border:1px solid rgba(255,77,109,.2)}
.dbc-ico{font-size:20px;margin-bottom:6px}
.dbc-lbl{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px}
.dbc-lend .dbc-lbl{color:var(--g)} .dbc-borrow .dbc-lbl{color:var(--r)}
.dbc-amt{font-size:17px;font-weight:800;color:var(--tx)}
.dbc-cnt{font-size:10px;color:var(--tx3);margin-top:2px}
.toolbar{display:flex;align-items:center;justify-content:space-between;padding:12px 16px 6px}
.tbar-lbl{font-size:11px;font-weight:700;color:var(--tx2);text-transform:uppercase;letter-spacing:.07em}
.btn-sm{background:linear-gradient(135deg,var(--gl),var(--g));color:#fff;border:none;border-radius:12px;padding:8px 14px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:0 2px 12px var(--gg);transition:all .18s;display:flex;align-items:center;gap:5px}
.btn-sm:active{transform:scale(.95)}
.debt-item{margin:0 16px 8px;background:var(--bg2);border:1px solid var(--br);border-radius:18px;padding:13px 15px;display:flex;align-items:center;gap:11px}
.d-ava{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#fff;flex-shrink:0}
.d-lend{background:linear-gradient(135deg,var(--g),var(--gd))} .d-borrow{background:linear-gradient(135deg,var(--r),var(--rd))}
.d-info{flex:1;min-width:0}
.d-name{font-size:13px;font-weight:700;color:var(--tx)}
.d-sub{font-size:11px;color:var(--tx3);margin-top:2px}
.d-right{text-align:right;flex-shrink:0}
.d-amount{font-size:14px;font-weight:800}
.d-lend-c{color:var(--g)} .d-borrow-c{color:var(--r)}
.d-badge{font-size:10px;font-weight:700;padding:3px 8px;border-radius:8px;display:inline-block;margin-top:4px;cursor:pointer;transition:all .18s}
.db-paid{background:var(--bg3);color:var(--tx3)} .db-unpaid{background:rgba(0,196,140,.15);color:var(--g)}
.d-del{background:none;border:none;color:var(--tx3);cursor:pointer;font-size:13px;margin-top:3px;display:block;padding:2px;transition:color .18s}
.d-del:hover{color:var(--r)}

/* ── INVESTMENTS TAB ── */
.port-card{margin:14px 16px 0;background:linear-gradient(135deg,rgba(76,154,255,.08),rgba(123,92,250,.08));border:1px solid rgba(76,154,255,.2);border-radius:20px;padding:18px;text-align:center}
.pc-lbl{font-size:10px;color:var(--tx2);font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:7px}
.pc-val{font-size:28px;font-weight:900;color:var(--tx);margin-bottom:6px}
.pc-pnl{font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:99px}
.pnl-p{background:rgba(0,196,140,.15);color:var(--g);border:1px solid rgba(0,196,140,.2)}
.pnl-n{background:rgba(255,77,109,.15);color:var(--r);border:1px solid rgba(255,77,109,.2)}
.inv-item{margin:8px 16px 0;background:var(--bg2);border:1px solid var(--br);border-radius:18px;padding:13px 15px;display:flex;align-items:center;gap:11px}
.inv-badge{width:44px;height:44px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;letter-spacing:-.5px;flex-shrink:0}
.ib-c{background:linear-gradient(135deg,#f59e0b,#d97706)} .ib-s{background:linear-gradient(135deg,var(--b),#2979ff)}
.inv-info{flex:1;min-width:0}
.inv-name{font-size:13px;font-weight:700;color:var(--tx);display:flex;align-items:center;gap:6px}
.inv-tag{font-size:9px;font-weight:700;background:var(--bg3);color:var(--tx3);padding:2px 6px;border-radius:5px;text-transform:uppercase}
.inv-sub{font-size:11px;color:var(--tx3);margin-top:2px}
.inv-right{text-align:right;flex-shrink:0}
.inv-val{font-size:14px;font-weight:800;color:var(--tx)}
.inv-pct{font-size:12px;font-weight:700;margin-top:2px}
.ip-p{color:var(--g)} .ip-n{color:var(--r)}
.inv-del{background:none;border:none;color:var(--tx3);cursor:pointer;font-size:13px;margin-top:3px;display:block;padding:2px}

/* ── BOTTOM NAV ── */
.bnav{flex-shrink:0;background:var(--bg2);border-top:1px solid var(--br);display:grid;grid-template-columns:1fr 1fr 64px 1fr 1fr;align-items:center;padding:6px 0 max(10px,env(safe-area-inset-bottom))}
.ni{display:flex;flex-direction:column;align-items:center;gap:3px;padding:5px 4px;cursor:pointer;background:none;border:none;color:var(--tx3);font-family:inherit;transition:all .2s;border-radius:12px}
.ni:active{transform:scale(.88)} .ni.on{color:var(--g)}
.ni-ico{font-size:20px;line-height:1} .ni-lbl{font-size:10px;font-weight:600}
.fab-wrap{display:flex;align-items:center;justify-content:center}
.fab{width:54px;height:54px;border-radius:50%;background:linear-gradient(135deg,var(--gl),var(--g));border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#fff;font-size:26px;font-weight:300;box-shadow:0 4px 20px var(--gg),0 2px 8px rgba(0,0,0,.4);margin-top:-18px;transition:all .2s;font-family:inherit}
.fab:active{transform:scale(.9);box-shadow:0 2px 10px var(--gg)}

/* ── SCREENS ── */
.screen{position:absolute;inset:0;z-index:50;display:flex;flex-direction:column;background:var(--bg);transform:translateY(100%);transition:transform .38s cubic-bezier(.16,1,.3,1);will-change:transform}
.screen.open{transform:translateY(0)}

/* ── ADD TX SCREEN ── */
.add-hdr{background:var(--bg2);border-bottom:1px solid var(--br);padding:13px 16px;flex-shrink:0;display:flex;align-items:center;justify-content:space-between;padding-top:max(13px,env(safe-area-inset-top))}
.close-btn{width:33px;height:33px;border-radius:10px;background:var(--bg3);border:1px solid var(--br);color:var(--tx2);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:17px;transition:all .18s}
.close-btn:active{background:rgba(255,77,109,.15);color:var(--r);transform:scale(.9)}
.type-tabs{display:flex;background:var(--bg3);border-radius:12px;padding:3px;gap:2px;flex:1;margin:0 10px}
.tt{flex:1;text-align:center;padding:8px 5px;border-radius:9px;font-size:11px;font-weight:700;cursor:pointer;transition:all .2s;border:none;background:transparent;color:var(--tx3);font-family:inherit}
.tt.te{background:var(--r);color:#fff;box-shadow:0 2px 8px var(--rg)}
.tt.ti{background:var(--g);color:#fff;box-shadow:0 2px 8px var(--gg)}
.tt.tt2{background:var(--b);color:#fff;box-shadow:0 2px 8px rgba(76,154,255,.35)}

/* Amount Hero */
.amt-hero{background:var(--bg2);padding:20px 20px 14px;text-align:center;flex-shrink:0;border-bottom:1px solid var(--br)}
.amt-cat-prev{display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:10px}
.acp-ico{width:34px;height:34px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:17px}
.acp-name{font-size:12px;font-weight:600;color:var(--tx2)}
.amt-val{font-size:40px;font-weight:900;letter-spacing:-1.5px;line-height:1;min-height:48px;display:flex;align-items:center;justify-content:center;transition:color .2s;word-break:break-all}
.av-e{color:var(--r)} .av-i{color:var(--g)} .av-t{color:var(--b)} .av-0{color:var(--tx3)}
.amt-cur{font-size:12px;font-weight:500;color:var(--tx3);margin-top:3px}

/* Recurring Row */
.recur-row{display:flex;align-items:center;gap:8px;padding:6px 0;flex-wrap:wrap}
.recur-toggle{display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;font-weight:600;color:var(--tx2)}
.recur-chk{width:16px;height:16px;border-radius:5px;border:1.5px solid var(--br2);background:var(--bg3);display:flex;align-items:center;justify-content:center;transition:all .2s;flex-shrink:0}
.recur-chk.checked{background:var(--g);border-color:var(--g)}
.recur-period-sel{background:var(--bg3);border:1px solid var(--br);border-radius:8px;padding:4px 8px;color:var(--g);font-size:11px;font-weight:700;font-family:inherit;cursor:pointer;outline:none}

/* Category Grid */
.cat-scroll{flex:1;overflow-y:auto;padding:10px 16px}
.cat-sec-title{font-size:10px;font-weight:700;color:var(--tx3);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px}
.cat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:14px}
.ci{display:flex;flex-direction:column;align-items:center;gap:4px;padding:9px 4px;border-radius:14px;cursor:pointer;border:2px solid transparent;background:var(--bg2);transition:all .16s}
.ci:active{transform:scale(.91)}
.ci.sel{border-color:var(--g);background:rgba(0,196,140,.1)}
.ci.sel-e{border-color:var(--r);background:rgba(255,77,109,.1)}
.ci .ci-ico{width:36px;height:36px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:19px}
.ci .ci-lbl{font-size:10px;font-weight:600;color:var(--tx2);text-align:center;line-height:1.2}
.ci.sel .ci-lbl{color:var(--g)} .ci.sel-e .ci-lbl{color:var(--r)}

/* TX Details */
.tx-details{background:var(--bg2);border-top:1px solid var(--br);flex-shrink:0}
.dr{display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid var(--br)}
.dr:last-child{border-bottom:none}
.dr-ico{width:30px;height:30px;border-radius:9px;background:var(--bg3);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
.dr-in{flex:1;background:none;border:none;outline:none;color:var(--tx);font-size:13px;font-weight:500;font-family:inherit}
.dr-in::placeholder{color:var(--tx3)}
.dr-sel{flex:1;background:none;border:none;outline:none;color:var(--tx);font-size:13px;font-weight:500;font-family:inherit;cursor:pointer;-webkit-appearance:none}
.dr-sel option{background:var(--bg2);color:var(--tx)}

/* Numpad */
.numpad{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--br);border-top:1px solid var(--br);flex-shrink:0}
.nk{background:var(--bg2);display:flex;align-items:center;justify-content:center;padding:16px 0;font-size:20px;font-weight:600;cursor:pointer;transition:background .1s;border:none;color:var(--tx);font-family:inherit;user-select:none;-webkit-tap-highlight-color:transparent;position:relative;overflow:hidden}
.nk::after{content:'';position:absolute;inset:0;background:rgba(255,255,255,0);transition:background .1s}
.nk:active::after{background:rgba(255,255,255,.05)}
.nk-del{color:var(--tx2);font-size:16px}
.nk-zero{font-size:16px}
.nk-ok{background:linear-gradient(135deg,var(--gl),var(--g));color:#fff;font-size:13px;font-weight:800;grid-row:span 2;box-shadow:inset 0 1px 0 rgba(255,255,255,.2)}
.nk-ok:active::after{background:rgba(0,0,0,.1)}

/* ── SHEETS ── */
.overlay{position:fixed;inset:0;z-index:60;background:rgba(0,0,0,.65);backdrop-filter:blur(6px);display:flex;align-items:flex-end;justify-content:center}
.sheet{width:100%;max-width:430px;background:var(--bg2);border-radius:24px 24px 0 0;border-top:1px solid var(--br2);padding:0 20px max(24px,env(safe-area-inset-bottom));animation:sheetUp .32s cubic-bezier(.16,1,.3,1);max-height:88vh;overflow-y:auto}
@keyframes sheetUp{from{transform:translateY(100%)}to{transform:translateY(0)}}
.sh-handle{width:36px;height:4px;background:rgba(255,255,255,.12);border-radius:2px;margin:13px auto 18px}
.sh-title{font-size:15px;font-weight:800;color:var(--tx);margin-bottom:18px}
.fl{font-size:10px;font-weight:700;color:var(--tx2);text-transform:uppercase;letter-spacing:.07em;margin-bottom:7px}
.fi{width:100%;background:var(--bg3);border:1.5px solid var(--br);border-radius:13px;padding:12px 13px;color:var(--tx);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;margin-bottom:12px}
.fi:focus{border-color:var(--g)}
.fs{width:100%;background:var(--bg3);border:1.5px solid var(--br);border-radius:13px;padding:12px 13px;color:var(--tx);font-size:14px;font-family:inherit;outline:none;margin-bottom:12px;-webkit-appearance:none;cursor:pointer}
.fs option{background:var(--bg2)}
.f-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.btn-p{width:100%;background:linear-gradient(135deg,var(--gl),var(--g));color:#fff;font-size:14px;font-weight:700;padding:14px;border-radius:15px;border:none;cursor:pointer;font-family:inherit;transition:all .2s;box-shadow:0 4px 20px var(--gg)}
.btn-p:active{transform:scale(.98);box-shadow:0 2px 10px var(--gg)}

/* ── TOAST ── */
.toast-wrap{position:fixed;top:0;left:50%;transform:translateX(-50%);z-index:999;pointer-events:none;padding-top:max(14px,env(safe-area-inset-top))}
.toast{background:var(--bg2);border:1px solid var(--br2);border-radius:14px;padding:10px 18px;font-size:13px;font-weight:600;white-space:nowrap;box-shadow:0 8px 32px rgba(0,0,0,.4);display:flex;align-items:center;gap:8px;transform:translateY(-60px);opacity:0;transition:all .35s cubic-bezier(.16,1,.3,1)}
.toast.show{transform:translateY(0);opacity:1}
.toast.success{border-color:rgba(0,196,140,.3);color:var(--g)}
.toast.error{border-color:rgba(255,77,109,.3);color:var(--r)}
.toast.info{border-color:rgba(76,154,255,.3);color:var(--b)}

/* ── LOADING ── */
.loading-wrap{position:fixed;inset:0;z-index:999;background:rgba(13,17,23,.75);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center}
.spinner{width:38px;height:38px;border:3px solid rgba(0,196,140,.15);border-top-color:var(--g);border-radius:50%;animation:spin .75s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

.pb{height:20px}
</style>
</head>
<body>
<div id="app" x-data="app()" x-init="init()">

<!-- LOADING -->
<div class="loading-wrap" x-show="loading" x-transition:enter.duration.200ms x-transition:leave.duration.150ms style="display:none">
  <div class="spinner"></div>
</div>

<!-- TOAST -->
<div class="toast-wrap">
  <div class="toast" :class="[toast.show?'show':'',toast.type]" x-text="toast.msg"></div>
</div>

<!-- ═══ TOPBAR ═══ -->
<div class="topbar">
  <div class="ta-left">
    <div class="ta-ava" x-text="uname.charAt(0).toUpperCase()"></div>
    <div class="ta-info">
      <span class="ta-greet">Xin chào 👋</span>
      <span class="ta-name" x-text="uname"></span>
    </div>
  </div>
  <div class="ta-right">
    <div style="display:flex;align-items:center;gap:4px;background:rgba(0,196,140,.1);border:1px solid rgba(0,196,140,.2);border-radius:99px;padding:4px 8px" x-show="isOnline">
      <div style="width:6px;height:6px;border-radius:50%;background:var(--g);animation:pulse-g 2s infinite"></div>
      <span style="font-size:10px;font-weight:600;color:var(--g)">Live</span>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="margin:0">
      @csrf
      <button type="submit" class="ti-btn" title="Đăng xuất">🚪</button>
    </form>
  </div>
</div>

<!-- ═══ CURRENCY BAR ═══ -->
<div class="currency-bar">
  <div class="cur-item">
    <span class="cur-flag">🇺🇸</span>
    <span class="cur-code">USD</span>
    <span class="cur-val" x-text="numFmt(cur.USD)+'₫'"></span>
  </div>
  <div class="cur-sep"></div>
  <div class="cur-item">
    <span class="cur-flag">🇪🇺</span>
    <span class="cur-code">EUR</span>
    <span class="cur-val" x-text="numFmt(cur.EUR)+'₫'"></span>
  </div>
  <div class="cur-sep"></div>
  <div class="cur-item">
    <span class="cur-flag">🇯🇵</span>
    <span class="cur-code">JPY</span>
    <span class="cur-val" x-text="numFmt(cur.JPY)+'₫'"></span>
  </div>
  <div class="cur-sep"></div>
  <div class="cur-item">
    <span class="cur-flag">🇬🇧</span>
    <span class="cur-code">GBP</span>
    <span class="cur-val" x-text="numFmt(cur.GBP)+'₫'"></span>
  </div>
  <div class="cur-sep"></div>
  <span class="cur-time" x-text="cur.updated ? '🕐 ' + cur.updated : ''"></span>
</div>

<!-- ═══ MONTH NAV (shown on most tabs) ═══ -->
<div class="month-nav" x-show="tab!=='stats'">
  <button class="mn-btn" @click="chMonth(-1)">‹</button>
  <span class="mn-label" x-text="mLabel"></span>
  <button class="mn-btn" @click="chMonth(1)">›</button>
</div>

<!-- ═══ SEARCH BAR (transactions tab only) ═══ -->
<div class="search-bar" x-show="tab==='transactions'" x-transition>
  <div class="sb-wrap">
    <span class="sb-icon">🔍</span>
    <input class="sb-input" type="text" placeholder="Tìm giao dịch, danh mục..." x-model="search" @input.debounce.300ms="filterTxs()">
  </div>
  <div class="filter-tabs">
    <button class="filter-tab" :class="txFilter==='all'?'active':''" @click="txFilter='all'">Tất cả</button>
    <button class="filter-tab" :class="txFilter==='expense'?'active':''" @click="txFilter='expense'">Chi</button>
    <button class="filter-tab" :class="txFilter==='income'?'active':''" @click="txFilter='income'">Thu</button>
  </div>
</div>

<!-- ═══ SCROLL AREA ═══ -->
<div class="scroll-area">

  <!-- ══ HOME ══ -->
  <div x-show="tab==='home'" x-transition:enter="transition ease-out duration-150">

    <!-- Hero Balance -->
    <div class="hero">
      <div class="hero-lbl">Tài sản ròng</div>
      <div class="hero-bal"><span>₫</span><span x-text="numFmt(ov.net_worth)"></span></div>
      <div class="hero-cur">VNĐ — Tổng tài sản trừ nợ</div>
      <div class="hero-stats">
        <div class="hs hs-i"><div class="hs-ico">↓</div><div class="hs-info"><div class="hs-label">Thu tháng này</div><div class="hs-val" x-text="fmtS(mStats.income)"></div></div></div>
        <div class="hs hs-e"><div class="hs-ico">↑</div><div class="hs-info"><div class="hs-label">Chi tháng này</div><div class="hs-val" x-text="fmtS(mStats.expense)"></div></div></div>
      </div>
    </div>

    <!-- Budget Alerts -->
    <template x-if="overBudgets.length>0">
      <div class="budget-strip">
        <template x-for="ub in overBudgets.slice(0,2)" :key="ub.cat">
          <div class="budget-alert">
            <span class="ba-icon">⚠️</span>
            <div class="ba-info">
              <div class="ba-title">Vượt ngân sách: <span x-text="ub.cat"></span></div>
              <div class="ba-sub">Đã chi <span x-text="fmtS(ub.spent)"></span> / Hạn mức <span x-text="fmtS(ub.limit)"></span></div>
            </div>
            <span class="ba-action" @click="tab='stats'">Xem →</span>
          </div>
        </template>
      </div>
    </template>

    <!-- Wallet Swiper -->
    <div class="sec-hdr"><span class="sec-lbl">Ví của tôi</span><span class="sec-act" @click="showAccSheet=true">＋ Thêm</span></div>
    <div class="wallet-scroll">
      <template x-for="(a,i) in accounts" :key="a.id">
        <div class="wcard" :class="'wc'+i%6">
          <div class="wcard-top">
            <span class="wcard-ico" x-text="a.type==='bank'?'🏦':(a.type==='e-wallet'?'📱':'👛')"></span>
            <div class="wcard-dots"><div class="wcard-dot"></div><div class="wcard-dot"></div><div class="wcard-dot"></div></div>
          </div>
          <div><div class="wcard-name" x-text="a.name"></div><div class="wcard-bal" x-text="fmtS(a.balance)"></div></div>
        </div>
      </template>
      <div class="wadd" @click="showAccSheet=true"><span style="font-size:22px;opacity:.5">＋</span><span style="font-size:10px;font-weight:700">Thêm ví</span></div>
    </div>

    <!-- Weekly Chart -->
    <div class="chart-wrap">
      <div class="chart-hdr">
        <span class="chart-title">Thu / Chi 7 ngày gần nhất</span>
        <div class="chart-leg">
          <div class="cl-item"><div class="cl-dot" style="background:var(--g)"></div>Thu</div>
          <div class="cl-item"><div class="cl-dot" style="background:var(--r)"></div>Chi</div>
        </div>
      </div>
      <div style="height:88px"><canvas id="weekChart"></canvas></div>
    </div>

    <!-- Budget Progress -->
    <template x-if="budgets.length > 0">
      <div>
        <div class="sec-hdr"><span class="sec-lbl">Ngân sách tháng</span><span class="sec-act" @click="showBudgetSheet=true">✏️ Sửa</span></div>
        <div class="budget-section">
          <template x-for="b in budgets" :key="b.cat">
            <div class="budget-item">
              <div class="bi-top">
                <div class="bi-cat">
                  <div class="bi-cat-ico" :style="'background:'+catColor(b.cat)+'22'" x-text="catEmoji(b.cat)"></div>
                  <span class="bi-cat-name" x-text="b.cat"></span>
                </div>
                <div class="bi-amounts">
                  <div class="bi-used" :style="b.spent>b.limit?'color:var(--r)':''" x-text="fmtS(b.spent)"></div>
                  <div class="bi-limit" x-text="'/ '+fmtS(b.limit)"></div>
                </div>
              </div>
              <div class="bi-bar">
                <div class="bi-bar-fill"
                     :class="b.pct>=100?'bi-bar-over':b.pct>=80?'bi-bar-warn':'bi-bar-ok'"
                     :style="'width:'+Math.min(b.pct,100)+'%'"></div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </template>
    <template x-if="budgets.length===0">
      <div style="margin:8px 16px 0"><button class="btn-p" style="background:var(--bg2);color:var(--g);box-shadow:none;border:1px solid rgba(0,196,140,.3);font-size:13px" @click="showBudgetSheet=true">💰 Thiết lập ngân sách tháng</button></div>
    </template>

    <!-- Recent Transactions -->
    <div class="sec-hdr" style="padding-top:14px"><span class="sec-lbl">Gần đây</span><span class="sec-act" @click="tab='transactions'">Xem tất cả</span></div>

    <template x-for="g in grouped.slice(0,2)" :key="g.date">
      <div class="tx-grp-wrap">
        <div class="tx-dh">
          <span class="tx-dl" :class="g.label==='Hôm nay'?'today':g.label==='Hôm qua'?'yest':''" x-text="g.label"></span>
          <span class="tx-dn" x-text="fmtS(g.net)"></span>
        </div>
        <template x-for="tx in g.items.slice(0,4)" :key="tx.id">
          <div class="tx-row">
            <div class="tx-ico" :style="'background:'+catColor(tx.category)+'22'"><span x-text="catEmoji(tx.category)"></span></div>
            <div class="tx-body">
              <div class="tx-cat" x-text="tx.category"></div>
              <div class="tx-meta">
                <span class="tx-acc-tag" x-text="tx.account?tx.account.name:''"></span>
                <span class="tx-note" x-text="tx.note||''"></span>
                <template x-if="tx.is_recurring"><span class="tx-recur">🔄 định kỳ</span></template>
              </div>
            </div>
            <div class="tx-amount" :class="tx.type" x-text="(tx.type==='income'?'+':tx.type==='expense'?'-':'⇄ ')+fmtS(tx.amount)"></div>
          </div>
        </template>
      </div>
    </template>

    <template x-if="transactions.length===0">
      <div class="empty"><div class="empty-ico">💸</div><div class="empty-title">Chưa có giao dịch nào</div><div class="empty-desc">Nhấn ＋ để ghi chép</div></div>
    </template>

    <!-- Savings Goals -->
    <template x-if="goals.length > 0">
      <div>
        <div class="sec-hdr"><span class="sec-lbl">Mục tiêu tiết kiệm</span><span class="sec-act" @click="showGoalSheet=true">＋ Thêm</span></div>
        <template x-for="g in goals" :key="g.id">
          <div class="goal-item">
            <div class="gi-top">
              <div class="gi-name"><span x-text="g.icon||'🎯'"></span><span x-text="g.name"></span></div>
              <span class="gi-pct" x-text="Math.min(Math.round((g.saved/g.target)*100),100)+'%'"></span>
            </div>
            <div class="gi-amounts">
              <span>Đã tiết kiệm: <b x-text="fmtS(g.saved)"></b></span>
              <span>Mục tiêu: <b x-text="fmtS(g.target)"></b></span>
            </div>
            <div class="gi-bar"><div class="gi-fill" :style="'width:'+Math.min((g.saved/g.target)*100,100)+'%'"></div></div>
            <div class="gi-deadline" x-text="g.deadline?'📅 Hạn: '+fmtDate(g.deadline):''"></div>
          </div>
        </template>
      </div>
    </template>
    <template x-if="goals.length===0">
      <div style="margin:8px 16px 0 16px;padding-bottom:4px">
        <button class="btn-p" style="background:var(--bg2);color:var(--b);box-shadow:none;border:1px solid rgba(76,154,255,.3);font-size:13px" @click="showGoalSheet=true">🎯 Thêm mục tiêu tiết kiệm</button>
      </div>
    </template>

    <div class="pb"></div>
  </div>

  <!-- ══ TRANSACTIONS ══ -->
  <div x-show="tab==='transactions'" x-transition:enter="transition ease-out duration-150">
    <div style="display:flex;gap:7px;padding:10px 16px 4px;overflow-x:auto">
      <div style="flex:1;background:rgba(0,196,140,.1);border:1px solid rgba(0,196,140,.2);border-radius:12px;padding:9px 11px;white-space:nowrap">
        <div style="font-size:9px;font-weight:700;color:var(--g);margin-bottom:2px;text-transform:uppercase;letter-spacing:.06em">↓ THU NHẬP</div>
        <div style="font-size:15px;font-weight:800;color:var(--g)" x-text="fmtS(mStats.income)"></div>
      </div>
      <div style="flex:1;background:rgba(255,77,109,.1);border:1px solid rgba(255,77,109,.2);border-radius:12px;padding:9px 11px;white-space:nowrap">
        <div style="font-size:9px;font-weight:700;color:var(--r);margin-bottom:2px;text-transform:uppercase;letter-spacing:.06em">↑ CHI PHÍ</div>
        <div style="font-size:15px;font-weight:800;color:var(--r)" x-text="fmtS(mStats.expense)"></div>
      </div>
      <div style="flex:1;background:var(--bg2);border:1px solid var(--br);border-radius:12px;padding:9px 11px;white-space:nowrap">
        <div style="font-size:9px;font-weight:700;color:var(--tx3);margin-bottom:2px;text-transform:uppercase;letter-spacing:.06em">⚖ CÒN LẠI</div>
        <div style="font-size:15px;font-weight:800;color:var(--tx)" x-text="fmtS(mStats.income-mStats.expense)"></div>
      </div>
    </div>

    <template x-if="displayedTxs.length===0">
      <div class="empty"><div class="empty-ico">📋</div><div class="empty-title">Không tìm thấy giao dịch</div><div class="empty-desc">Thử thay đổi bộ lọc hoặc thêm giao dịch mới</div></div>
    </template>

    <template x-for="g in displayedGroups" :key="g.date">
      <div class="tx-grp-wrap">
        <div class="tx-dh">
          <span class="tx-dl" :class="g.label==='Hôm nay'?'today':g.label==='Hôm qua'?'yest':''" x-text="g.label"></span>
          <span class="tx-dn" x-text="fmtS(g.net)"></span>
        </div>
        <template x-for="tx in g.items" :key="tx.id">
          <div class="tx-row" @click="delTx(tx)">
            <div class="tx-ico" :style="'background:'+catColor(tx.category)+'22'"><span x-text="catEmoji(tx.category)"></span></div>
            <div class="tx-body">
              <div class="tx-cat" x-text="tx.category"></div>
              <div class="tx-meta">
                <span class="tx-acc-tag" x-text="tx.account?tx.account.name:''"></span>
                <span class="tx-note" x-text="tx.note||'Không có ghi chú'"></span>
                <template x-if="tx.is_recurring"><span class="tx-recur">🔄 định kỳ</span></template>
              </div>
            </div>
            <div class="tx-amount" :class="tx.type" x-text="(tx.type==='income'?'+':tx.type==='expense'?'-':'⇄ ')+fmtS(tx.amount)"></div>
          </div>
        </template>
      </div>
    </template>
    <div class="pb"></div>
  </div>

  <!-- ══ STATS ══ -->
  <div x-show="tab==='stats'" x-transition:enter="transition ease-out duration-150">
    <!-- Month selector for stats -->
    <div class="month-nav" style="border-top:1px solid var(--br)">
      <button class="mn-btn" @click="chMonth(-1)">‹</button>
      <span class="mn-label" x-text="mLabel"></span>
      <button class="mn-btn" @click="chMonth(1)">›</button>
    </div>

    <!-- Summary cards -->
    <div class="stats-summary">
      <div class="ss-card ss-income"><div class="ss-label">Thu nhập</div><div class="ss-val" x-text="fmtS(stats.total_income||0)"></div></div>
      <div class="ss-card ss-expense"><div class="ss-label">Chi phí</div><div class="ss-val" x-text="fmtS(stats.total_expense||0)"></div></div>
      <div class="ss-card ss-rate"><div class="ss-label">Tiết kiệm</div><div class="ss-val" x-text="(stats.savings_rate||0)+'%'"></div></div>
    </div>

    <!-- 6-month trend chart -->
    <div class="chart-wrap" style="margin-top:12px">
      <div class="chart-hdr">
        <span class="chart-title">Xu hướng 6 tháng</span>
        <div class="chart-leg">
          <div class="cl-item"><div class="cl-dot" style="background:var(--g)"></div>Thu</div>
          <div class="cl-item"><div class="cl-dot" style="background:var(--r)"></div>Chi</div>
          <div class="cl-item"><div class="cl-dot" style="background:var(--b)"></div>Tiết kiệm</div>
        </div>
      </div>
      <div style="height:120px"><canvas id="trendChart"></canvas></div>
    </div>

    <!-- Donut Chart + Top Categories -->
    <div class="chart-wrap" style="margin-top:10px">
      <div class="chart-hdr"><span class="chart-title">Chi phí theo danh mục</span></div>
      <div style="display:flex;gap:12px;align-items:center">
        <div style="width:130px;height:130px;flex-shrink:0"><canvas id="donutChart"></canvas></div>
        <div style="flex:1;min-width:0">
          <template x-if="(stats.by_category||[]).length===0">
            <div style="color:var(--tx3);font-size:12px;text-align:center;padding:20px 0">Không có dữ liệu</div>
          </template>
          <template x-for="(cat,i) in (stats.by_category||[]).slice(0,5)" :key="cat.category">
            <div class="cat-rank-item">
              <span class="cr-rank" x-text="i+1"></span>
              <div class="cr-ico" :style="'background:'+catColor(cat.category)+'22'" x-text="catEmoji(cat.category)"></div>
              <div class="cr-info">
                <div class="cr-name" x-text="cat.category"></div>
                <div class="cr-bar-wrap"><div class="cr-bar" :style="'width:'+(stats.total_expense>0?(cat.total/stats.total_expense*100):0)+'%;background:'+catColor(cat.category)"></div></div>
              </div>
              <div>
                <div class="cr-amount" :style="'color:'+catColor(cat.category)" x-text="fmtS(cat.total)"></div>
                <div class="cr-pct" x-text="stats.total_expense>0?(cat.total/stats.total_expense*100).toFixed(1)+'%':'0%'"></div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- Income by Category -->
    <div class="chart-wrap" style="margin-top:10px">
      <div class="chart-hdr"><span class="chart-title">Thu nhập theo danh mục</span></div>
      <template x-if="(stats.income_by_category||[]).length===0">
        <div style="color:var(--tx3);font-size:12px;text-align:center;padding:20px 0">Không có dữ liệu</div>
      </template>
      <template x-for="(cat,i) in (stats.income_by_category||[]).slice(0,5)" :key="cat.category">
        <div class="cat-rank-item">
          <span class="cr-rank" x-text="i+1"></span>
          <div class="cr-ico" :style="'background:'+catColor(cat.category)+'22'" x-text="catEmoji(cat.category)"></div>
          <div class="cr-info">
            <div class="cr-name" x-text="cat.category"></div>
            <div class="cr-bar-wrap"><div class="cr-bar" :style="'width:'+(stats.total_income>0?(cat.total/stats.total_income*100):0)+'%;background:var(--g)'"></div></div>
          </div>
          <div>
            <div class="cr-amount" style="color:var(--g)" x-text="fmtS(cat.total)"></div>
            <div class="cr-pct" x-text="stats.total_income>0?(cat.total/stats.total_income*100).toFixed(1)+'%':'0%'"></div>
          </div>
        </div>
      </template>
    </div>
    <div class="pb"></div>
  </div>

  <!-- ══ DEBTS ══ -->
  <div x-show="tab==='debts'" x-transition:enter="transition ease-out duration-150">
    <div class="debt-banner">
      <div class="dbc dbc-lend"><div class="dbc-ico">📤</div><div class="dbc-lbl">Cho vay</div><div class="dbc-amt" x-text="fmtS(ov.total_lend)"></div><div class="dbc-cnt" x-text="debts.filter(d=>d.type==='lend').length+' khoản'"></div></div>
      <div class="dbc dbc-borrow"><div class="dbc-ico">📥</div><div class="dbc-lbl">Đi vay</div><div class="dbc-amt" x-text="fmtS(ov.total_borrow)"></div><div class="dbc-cnt" x-text="debts.filter(d=>d.type==='borrow').length+' khoản'"></div></div>
    </div>
    <div class="toolbar"><span class="tbar-lbl">Danh sách nợ vay</span><button class="btn-sm" @click="showDebtSheet=true">＋ Thêm nợ</button></div>
    <template x-if="debts.length===0"><div class="empty"><div class="empty-ico">🤝</div><div class="empty-title">Chưa có khoản nợ</div><div class="empty-desc">Nhấn ＋ Thêm nợ để ghi nhận</div></div></template>
    <template x-for="d in debts" :key="d.id">
      <div class="debt-item">
        <div class="d-ava" :class="d.type==='lend'?'d-lend':'d-borrow'" x-text="(d.partner_name||'?').charAt(0).toUpperCase()"></div>
        <div class="d-info">
          <div class="d-name" x-text="d.partner_name"></div>
          <div class="d-sub"><span x-text="d.type==='lend'?'📤 Cho vay':'📥 Đi vay'"></span><template x-if="d.due_date"><span x-text="' · Hạn: '+fmtDate(d.due_date)"></span></template></div>
          <div class="d-sub" style="font-style:italic;opacity:.7" x-text="d.note"></div>
        </div>
        <div class="d-right">
          <div class="d-amount" :class="d.type==='lend'?'d-lend-c':'d-borrow-c'" x-text="fmtS(d.amount)"></div>
          <span class="d-badge" :class="d.status==='paid'?'db-paid':'db-unpaid'" @click="toggleDebt(d.id)" x-text="d.status==='paid'?'✓ Đã trả':'⏳ Chưa trả'"></span>
          <button class="d-del" @click="deleteDebt(d.id)">🗑</button>
        </div>
      </div>
    </template>
    <div class="pb"></div>
  </div>

  <!-- ══ INVESTMENTS ══ -->
  <div x-show="tab==='investments'" x-transition:enter="transition ease-out duration-150">
    <div class="port-card">
      <div class="pc-lbl">Danh mục đầu tư</div>
      <div class="pc-val" x-text="'₫ '+numFmt(ov.total_investment)"></div>
      <span class="pc-pnl" :class="ov.investment_pnl>=0?'pnl-p':'pnl-n'"
            x-text="(ov.investment_pnl>=0?'▲ +':'▼ ')+fmtS(ov.investment_pnl)+' ('+ov.investment_pnl_percent.toFixed(2)+'%)'"></span>
    </div>
    <div class="toolbar">
      <span class="tbar-lbl">Danh mục</span>
      <div style="display:flex;gap:8px">
        <button class="btn-sm" style="background:var(--bg3);color:var(--g);box-shadow:none;border:1px solid rgba(0,196,140,.3)" @click="updateRates()">🔄 Tỷ giá</button>
        <button class="btn-sm" @click="showInvSheet=true">＋ Thêm</button>
      </div>
    </div>
    <template x-if="investments.length===0"><div class="empty"><div class="empty-ico">📈</div><div class="empty-title">Chưa có danh mục</div><div class="empty-desc">Thêm Crypto hoặc Cổ phiếu</div></div></template>
    <template x-for="inv in investments" :key="inv.id">
      <div class="inv-item">
        <div class="inv-badge" :class="inv.type==='crypto'?'ib-c':'ib-s'" x-text="inv.symbol"></div>
        <div class="inv-info">
          <div class="inv-name"><span x-text="inv.symbol"></span><span class="inv-tag" x-text="inv.type==='crypto'?'Crypto':'Cổ phiếu'"></span></div>
          <div class="inv-sub">SL: <b x-text="inv.quantity"></b> · Mua: <span x-text="fmtS(inv.buy_price)"></span></div>
          <div class="inv-sub" style="opacity:.6">Hiện tại: <span x-text="fmtS(inv.current_price)"></span></div>
        </div>
        <div class="inv-right">
          <div class="inv-val" x-text="fmtS(inv.quantity*inv.current_price)"></div>
          <div class="inv-pct" :class="(inv.current_price-inv.buy_price)>=0?'ip-p':'ip-n'"
               x-text="((inv.current_price-inv.buy_price)>=0?'▲ +':'▼ ')+(((inv.current_price-inv.buy_price)/inv.buy_price)*100).toFixed(1)+'%'"></div>
          <button class="inv-del" @click="deleteInv(inv.id)">🗑</button>
        </div>
      </div>
    </template>
    <div class="pb"></div>
  </div>

</div><!-- /scroll-area -->

<!-- ═══ BOTTOM NAV ═══ -->
<nav class="bnav">
  <button class="ni" :class="tab==='home'?'on':''" @click="tab='home'"><span class="ni-ico">🏠</span><span class="ni-lbl">Tổng quan</span></button>
  <button class="ni" :class="tab==='transactions'?'on':''" @click="tab='transactions'"><span class="ni-ico">📋</span><span class="ni-lbl">Giao dịch</span></button>
  <div class="fab-wrap"><button class="fab" @click="openAdd('expense')">＋</button></div>
  <button class="ni" :class="tab==='stats'?'on':''" @click="tab='stats';loadStats()"><span class="ni-ico">📊</span><span class="ni-lbl">Thống kê</span></button>
  <button class="ni" :class="tab==='debts'?'on':''" @click="tab='debts'"><span class="ni-ico">🤝</span><span class="ni-lbl">Nợ vay</span></button>
</nav>

<!-- ═══ ADD TRANSACTION SCREEN ═══ -->
<div class="screen" :class="showAddScreen?'open':''">
  <div class="add-hdr">
    <div class="close-btn" @click="showAddScreen=false">✕</div>
    <div class="type-tabs">
      <button class="tt" :class="form.type==='expense'?'te':''" @click="form.type='expense'">Chi phí</button>
      <button class="tt" :class="form.type==='income'?'ti':''" @click="form.type='income'">Thu nhập</button>
      <button class="tt" :class="form.type==='transfer'?'tt2':''" @click="form.type='transfer'">Chuyển ví</button>
    </div>
    <div style="width:33px"></div>
  </div>

  <!-- Amount -->
  <div class="amt-hero">
    <div class="amt-cat-prev">
      <div class="acp-ico" :style="'background:'+catColor(form.category)+'22'"><span x-text="catEmoji(form.category)"></span></div>
      <span class="acp-name" x-text="form.category||'Chọn danh mục'"></span>
    </div>
    <div class="amt-val"
         :class="numpad===''?'av-0':form.type==='expense'?'av-e':form.type==='income'?'av-i':'av-t'"
         x-text="numpad===''?'0':numpad.replace(/\B(?=(\d{3})+(?!\d))/g,'.')"></div>
    <div class="amt-cur">₫ VNĐ</div>
  </div>

  <!-- Categories -->
  <div class="cat-scroll">
    <template x-if="form.type!=='transfer'">
      <div>
        <div class="cat-sec-title" x-text="form.type==='expense'?'Chọn danh mục chi phí':'Chọn danh mục thu nhập'"></div>
        <div class="cat-grid">
          <template x-for="cat in curCats()" :key="cat.name">
            <div class="ci" :class="form.category===cat.name?(form.type==='expense'?'sel-e':'sel'):''" @click="form.category=cat.name">
              <div class="ci-ico" :style="'background:'+cat.color+'22'"><span x-text="cat.emoji"></span></div>
              <span class="ci-lbl" x-text="cat.name"></span>
            </div>
          </template>
        </div>
      </div>
    </template>
  </div>

  <!-- Details -->
  <div class="tx-details">
    <div class="dr">
      <div class="dr-ico">💳</div>
      <select class="dr-sel" x-model="form.acc">
        <option value="">Chọn ví...</option>
        <template x-for="a in accounts" :key="a.id"><option :value="a.id" x-text="a.name+' ('+fmtS(a.balance)+')'"></option></template>
      </select>
    </div>
    <template x-if="form.type==='transfer'">
      <div class="dr">
        <div class="dr-ico">➡️</div>
        <select class="dr-sel" x-model="form.toAcc">
          <option value="">Ví đích...</option>
          <template x-for="a in accounts" :key="a.id"><option :value="a.id" x-text="a.name"></option></template>
        </select>
      </div>
    </template>
    <div class="dr">
      <div class="dr-ico">📅</div>
      <input type="date" class="dr-in" x-model="form.date">
    </div>
    <div class="dr" style="border-bottom:none">
      <div class="dr-ico">📝</div>
      <input type="text" class="dr-in" placeholder="Ghi chú..." x-model="form.note">
      <!-- Recurring toggle -->
      <div class="recur-row" style="flex-shrink:0;margin-left:6px">
        <div class="recur-toggle" @click="form.recur=!form.recur">
          <div class="recur-chk" :class="form.recur?'checked':''" x-text="form.recur?'✓':''"></div>
          <span>Định kỳ</span>
        </div>
        <template x-if="form.recur">
          <select class="recur-period-sel" x-model="form.period">
            <option value="monthly">Hàng tháng</option>
            <option value="weekly">Hàng tuần</option>
            <option value="yearly">Hàng năm</option>
            <option value="daily">Hàng ngày</option>
          </select>
        </template>
      </div>
    </div>
  </div>

  <!-- Numpad -->
  <div class="numpad">
    <template x-for="k in ['7','8','9','⌫','4','5','6','000','1','2','3','OK','.','0','C','']">
      <button class="nk"
              :class="k==='OK'?'nk-ok':k==='⌫'?'nk-del':k==='000'?'nk-zero':''"
              :style="k===''?'visibility:hidden':''"
              @click="numPress(k)"
              x-text="k==='OK'?'LƯU':k"></button>
    </template>
  </div>
</div>

<!-- ═══ BOTTOM SHEETS ═══ -->

<!-- Add Account -->
<div class="overlay" x-show="showAccSheet" @click.self="showAccSheet=false" style="display:none" x-transition>
  <div class="sheet">
    <div class="sh-handle"></div>
    <div class="sh-title">➕ Thêm ví / Tài khoản</div>
    <div class="fl">Tên ví</div>
    <input class="fi" placeholder="Techcombank, Tiền mặt..." x-model="aForm.name">
    <div class="fl">Loại tài khoản</div>
    <select class="fs" x-model="aForm.type">
      <option value="cash">👛 Tiền mặt</option>
      <option value="bank">🏦 Ngân hàng</option>
      <option value="e-wallet">📱 Ví điện tử</option>
      <option value="other">📦 Khác</option>
    </select>
    <div class="fl">Số dư ban đầu (₫)</div>
    <input type="number" class="fi" placeholder="0" x-model="aForm.balance" style="font-size:22px;font-weight:800;color:var(--g)">
    <button class="btn-p" @click="saveAcc()">Tạo ví ngay 🎉</button>
  </div>
</div>

<!-- Add Debt -->
<div class="overlay" x-show="showDebtSheet" @click.self="showDebtSheet=false" style="display:none" x-transition>
  <div class="sheet">
    <div class="sh-handle"></div>
    <div class="sh-title">📝 Ghi nhận nợ vay</div>
    <div class="fl">Tên người liên quan</div>
    <input class="fi" placeholder="Họ tên..." x-model="dForm.name">
    <div class="fl">Loại</div>
    <select class="fs" x-model="dForm.type">
      <option value="lend">📤 Tôi cho vay (họ nợ tôi)</option>
      <option value="borrow">📥 Tôi đi vay (tôi nợ họ)</option>
    </select>
    <div class="f-row">
      <div><div class="fl">Số tiền (₫)</div><input type="number" class="fi" placeholder="0" x-model="dForm.amount" style="font-weight:700;color:var(--g)"></div>
      <div><div class="fl">Hạn trả</div><input type="date" class="fi" x-model="dForm.due"></div>
    </div>
    <div class="fl">Ghi chú</div>
    <input class="fi" placeholder="Lý do mượn..." x-model="dForm.note">
    <button class="btn-p" @click="saveDebt()">Ghi nhận 📋</button>
  </div>
</div>

<!-- Add Investment -->
<div class="overlay" x-show="showInvSheet" @click.self="showInvSheet=false" style="display:none" x-transition>
  <div class="sheet">
    <div class="sh-handle"></div>
    <div class="sh-title">📈 Thêm tài sản đầu tư</div>
    <div class="fl">Loại</div>
    <select class="fs" x-model="iForm.type">
      <option value="crypto">🪙 Crypto (BTC, ETH...)</option>
      <option value="stock">📊 Cổ phiếu Việt Nam</option>
    </select>
    <div class="fl">Mã tài sản</div>
    <input class="fi" placeholder="BTC, ETH, VNM, FPT..." x-model="iForm.sym" style="text-transform:uppercase;font-weight:700;font-size:18px;letter-spacing:1px">
    <div class="f-row">
      <div><div class="fl">Số lượng</div><input type="number" class="fi" placeholder="0" x-model="iForm.qty" step="any"></div>
      <div><div class="fl">Giá mua (₫)</div><input type="number" class="fi" placeholder="0" x-model="iForm.bp"></div>
    </div>
    <button class="btn-p" @click="saveInv()">Thêm vào danh mục 🚀</button>
  </div>
</div>

<!-- Budget Manager -->
<div class="overlay" x-show="showBudgetSheet" @click.self="showBudgetSheet=false" style="display:none" x-transition>
  <div class="sheet">
    <div class="sh-handle"></div>
    <div class="sh-title">💰 Ngân sách tháng</div>
    <div style="font-size:11px;color:var(--tx2);margin-bottom:14px">Thiết lập hạn mức chi tiêu cho từng danh mục. Hệ thống sẽ cảnh báo khi gần vượt.</div>
    <template x-for="cat in budgetCats" :key="cat">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
        <div style="width:34px;height:34px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0" :style="'background:'+catColor(cat)+'22'" x-text="catEmoji(cat)"></div>
        <span style="font-size:13px;font-weight:600;color:var(--tx);flex:1" x-text="cat"></span>
        <input type="number" placeholder="Hạn mức..." style="width:110px;background:var(--bg3);border:1.5px solid var(--br);border-radius:10px;padding:7px 10px;color:var(--g);font-size:13px;font-weight:700;font-family:inherit;outline:none;text-align:right"
               :value="getBudget(cat)"
               @change="setBudget(cat,$event.target.value)">
      </div>
    </template>
    <button class="btn-p" @click="showBudgetSheet=false">Lưu ngân sách ✅</button>
  </div>
</div>

<!-- Savings Goal Sheet -->
<div class="overlay" x-show="showGoalSheet" @click.self="showGoalSheet=false" style="display:none" x-transition>
  <div class="sheet">
    <div class="sh-handle"></div>
    <div class="sh-title">🎯 Mục tiêu tiết kiệm</div>
    <div class="fl">Tên mục tiêu</div>
    <input class="fi" placeholder="VD: Mua xe, Du lịch Nhật..." x-model="gForm.name">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
      <span style="font-size:11px;font-weight:700;color:var(--tx2)">Chọn icon:</span>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        <template x-for="ico in goalIcons" :key="ico">
          <div @click="gForm.icon=ico" style="width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;cursor:pointer;transition:all .18s"
               :style="gForm.icon===ico?'background:rgba(0,196,140,.2);transform:scale(1.2)':'background:var(--bg3)'">
            <span x-text="ico"></span>
          </div>
        </template>
      </div>
    </div>
    <div class="f-row">
      <div><div class="fl">Mục tiêu (₫)</div><input type="number" class="fi" placeholder="0" x-model="gForm.target" style="font-weight:700;color:var(--g)"></div>
      <div><div class="fl">Đã có (₫)</div><input type="number" class="fi" placeholder="0" x-model="gForm.saved" style="color:var(--b)"></div>
    </div>
    <div class="fl">Hạn chót (không bắt buộc)</div>
    <input type="date" class="fi" x-model="gForm.deadline">
    <button class="btn-p" @click="saveGoal()">Tạo mục tiêu 🎯</button>

    <!-- Existing goals -->
    <template x-if="goals.length>0">
      <div style="margin-top:14px;border-top:1px solid var(--br);padding-top:14px">
        <div class="fl">Mục tiêu hiện có</div>
        <template x-for="(g,i) in goals" :key="i">
          <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04)">
            <span><span x-text="g.icon"></span> <span style="font-size:13px;font-weight:600;color:var(--tx)" x-text="g.name"></span></span>
            <button @click="removeGoal(i)" style="background:none;border:none;color:var(--tx3);cursor:pointer;font-size:14px">🗑</button>
          </div>
        </template>
      </div>
    </template>
  </div>
</div>

</div><!-- /#app -->

<script>
const CSRF = '{{ csrf_token() }}';

const CATS_E = [
  {name:'Ăn uống',emoji:'🍜',color:'#ff6b6b'},{name:'Mua sắm',emoji:'🛒',color:'#ff9f43'},
  {name:'Di chuyển',emoji:'🚗',color:'#54a0ff'},{name:'Nhà ở',emoji:'🏠',color:'#5f27cd'},
  {name:'Sức khoẻ',emoji:'💊',color:'#00d2d3'},{name:'Giải trí',emoji:'🎮',color:'#c8d6e5'},
  {name:'Giáo dục',emoji:'📚',color:'#786fa6'},{name:'Làm đẹp',emoji:'💄',color:'#ff9ff3'},
  {name:'Cà phê',emoji:'☕',color:'#a29bfe'},{name:'Điện thoại',emoji:'📱',color:'#6c5ce7'},
  {name:'Du lịch',emoji:'✈️',color:'#00cec9'},{name:'Quà tặng',emoji:'🎁',color:'#fdcb6e'},
  {name:'Tiện ích',emoji:'💡',color:'#ffeaa7'},{name:'Sửa chữa',emoji:'🔧',color:'#b2bec3'},
  {name:'Thú cưng',emoji:'🐾',color:'#e17055'},{name:'Khác',emoji:'📦',color:'#636e72'},
];
const CATS_I = [
  {name:'Lương',emoji:'💰',color:'#00c48c'},{name:'Kinh doanh',emoji:'💼',color:'#54a0ff'},
  {name:'Đầu tư',emoji:'📈',color:'#00e5a0'},{name:'Thưởng',emoji:'🎯',color:'#fdcb6e'},
  {name:'Quà',emoji:'💝',color:'#ff9ff3'},{name:'Hoàn tiền',emoji:'🔄',color:'#a29bfe'},
  {name:'Lãi suất',emoji:'🏦',color:'#1de9b6'},{name:'Khác',emoji:'📦',color:'#636e72'},
];
const ALL_CATS = [...CATS_E,...CATS_I];
const catOf = n => ALL_CATS.find(c=>c.name===n)||{emoji:'💸',color:'#636e72'};

function app(){return{
  loading:false,isOnline:navigator.onLine,
  tab:'home',curY:new Date().getFullYear(),curM:new Date().getMonth()+1,
  uname:'{{ Auth::user()->name ?? "Bạn" }}',
  accounts:[],transactions:[],debts:[],investments:[],
  ov:{net_worth:0,total_cash:0,total_investment:0,total_lend:0,total_borrow:0,investment_pnl:0,investment_pnl_percent:0},
  stats:{},cur:{USD:25450,EUR:27200,JPY:170,GBP:32000,updated:''},
  search:'',txFilter:'all',

  showAddScreen:false,showAccSheet:false,showDebtSheet:false,showInvSheet:false,showBudgetSheet:false,showGoalSheet:false,

  numpad:'',
  form:{type:'expense',acc:'',toAcc:'',category:'',date:new Date().toISOString().split('T')[0],note:'',recur:false,period:'monthly'},
  aForm:{name:'',type:'cash',balance:''},
  dForm:{name:'',type:'lend',amount:'',due:'',note:''},
  iForm:{sym:'',type:'crypto',qty:'',bp:''},
  gForm:{name:'',icon:'🎯',target:'',saved:'',deadline:''},

  goalIcons:['🚗','🏠','✈️','💍','📱','💻','🎓','🏖️','🎯','💰','🏋️','🌏'],
  budgetCats:['Ăn uống','Mua sắm','Di chuyển','Giải trí','Cà phê','Nhà ở','Sức khoẻ'],

  charts:{week:null,trend:null,donut:null},
  toast:{show:false,msg:'',type:'success'},

  // ─ COMPUTED ─
  get mLabel(){return`Tháng ${this.curM}/${this.curY}`},

  get mStats(){
    const f=this.transactions.filter(t=>{const d=new Date(t.transaction_date);return d.getMonth()+1===this.curM&&d.getFullYear()===this.curY});
    return{income:f.filter(t=>t.type==='income').reduce((s,t)=>s+parseFloat(t.amount),0),
           expense:f.filter(t=>t.type==='expense').reduce((s,t)=>s+parseFloat(t.amount),0)};
  },

  get filtered(){
    return this.transactions.filter(t=>{const d=new Date(t.transaction_date);return d.getMonth()+1===this.curM&&d.getFullYear()===this.curY});
  },

  get displayedTxs(){
    let list=this.filtered;
    if(this.txFilter!=='all') list=list.filter(t=>t.type===this.txFilter);
    if(this.search.trim()){const s=this.search.toLowerCase();list=list.filter(t=>(t.category||'').toLowerCase().includes(s)||(t.note||'').toLowerCase().includes(s))}
    return list;
  },

  get displayedGroups(){return this.groupTxs(this.displayedTxs)},
  get grouped(){return this.groupTxs(this.filtered)},

  get budgets(){
    const raw=JSON.parse(localStorage.getItem('mt_budgets')||'{}');
    return Object.entries(raw).filter(([,v])=>v>0).map(([cat,limit])=>{
      const spent=this.filtered.filter(t=>t.type==='expense'&&t.category===cat).reduce((s,t)=>s+parseFloat(t.amount),0);
      return{cat,limit:parseFloat(limit),spent,pct:(spent/parseFloat(limit))*100};
    });
  },

  get overBudgets(){return this.budgets.filter(b=>b.pct>=90)},

  get goals(){return JSON.parse(localStorage.getItem('mt_goals')||'[]')},

  getBudget(cat){const b=JSON.parse(localStorage.getItem('mt_budgets')||'{}');return b[cat]||''},
  setBudget(cat,val){const b=JSON.parse(localStorage.getItem('mt_budgets')||'{}');if(val)b[cat]=parseFloat(val);else delete b[cat];localStorage.setItem('mt_budgets',JSON.stringify(b))},

  // ─ INIT ─
  async init(){
    window.addEventListener('online',()=>this.isOnline=true);
    window.addEventListener('offline',()=>this.isOnline=false);
    if('serviceWorker'in navigator)navigator.serviceWorker.register('/sw.js').catch(()=>{});
    await this.load();
    this.loadCurrency();
  },

  async load(){
    this.loading=true;
    try{
      const[a,t,d,i,o]=await Promise.all([
        fetch('/api/finance/accounts').then(r=>r.json()),
        fetch('/api/finance/transactions').then(r=>r.json()),
        fetch('/api/finance/debts').then(r=>r.json()),
        fetch('/api/finance/investments').then(r=>r.json()),
        fetch('/api/finance/overview').then(r=>r.json()),
      ]);
      this.accounts=a.accounts||[];
      this.transactions=t.transactions||[];
      this.debts=d.debts||[];
      this.investments=i.investments||[];
      this.ov={...this.ov,...o};
      this.$nextTick(()=>this.drawWeek());
    }catch(e){this.notify('Lỗi tải dữ liệu','error')}
    finally{this.loading=false}
  },

  async loadCurrency(){
    try{
      const d=await fetch('/api/finance/currency').then(r=>r.json());
      if(d.success) this.cur=d.rates;
    }catch{}
  },

  async loadStats(){
    try{
      const d=await fetch(`/api/finance/stats?year=${this.curY}&month=${this.curM}`).then(r=>r.json());
      if(d.success){this.stats=d;this.$nextTick(()=>{this.drawTrend();this.drawDonut();})}
    }catch{}
  },

  chMonth(dir){
    this.curM+=dir;
    if(this.curM>12){this.curM=1;this.curY++} if(this.curM<1){this.curM=12;this.curY--}
    this.$nextTick(()=>this.drawWeek());
    if(this.tab==='stats') this.loadStats();
  },

  filterTxs(){},

  // ─ CHARTS ─
  drawWeek(){
    const c=document.getElementById('weekChart');if(!c)return;
    if(this.charts.week)this.charts.week.destroy();
    const days=[],inc=[],exp=[];
    for(let i=6;i>=0;i--){
      const d=new Date(Date.now()-i*86400000),k=d.toISOString().split('T')[0];
      const txs=this.transactions.filter(t=>t.transaction_date&&t.transaction_date.startsWith(k));
      days.push(d.toLocaleDateString('vi-VN',{weekday:'short'}));
      inc.push(txs.filter(t=>t.type==='income').reduce((s,t)=>s+parseFloat(t.amount),0)/1e6);
      exp.push(txs.filter(t=>t.type==='expense').reduce((s,t)=>s+parseFloat(t.amount),0)/1e6);
    }
    this.charts.week=new Chart(c,{type:'bar',data:{labels:days,datasets:[
      {data:inc,backgroundColor:'rgba(0,196,140,.75)',borderRadius:5,borderSkipped:false,barPercentage:.5},
      {data:exp,backgroundColor:'rgba(255,77,109,.75)',borderRadius:5,borderSkipped:false,barPercentage:.5},
    ]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>(ctx.parsed.y).toFixed(1)+'M ₫'}}},
      scales:{x:{grid:{display:false},ticks:{color:'#6e7681',font:{size:9,family:'Inter'}}},y:{grid:{color:'rgba(255,255,255,.04)'},ticks:{color:'#6e7681',font:{size:9,family:'Inter'},callback:v=>v+'M'}}}}});
  },

  drawTrend(){
    const c=document.getElementById('trendChart');if(!c||!this.stats.trend)return;
    if(this.charts.trend)this.charts.trend.destroy();
    const tr=this.stats.trend||[];
    this.charts.trend=new Chart(c,{type:'line',data:{labels:tr.map(t=>t.label),datasets:[
      {data:tr.map(t=>t.income/1e6),borderColor:'#00c48c',backgroundColor:'rgba(0,196,140,.1)',tension:.4,fill:true,pointRadius:3,borderWidth:2},
      {data:tr.map(t=>t.expense/1e6),borderColor:'#ff4d6d',backgroundColor:'rgba(255,77,109,.08)',tension:.4,fill:true,pointRadius:3,borderWidth:2},
      {data:tr.map(t=>t.savings/1e6),borderColor:'#4c9aff',backgroundColor:'transparent',tension:.4,pointRadius:3,borderWidth:2,borderDash:[4,4]},
    ]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
      scales:{x:{grid:{display:false},ticks:{color:'#6e7681',font:{size:9,family:'Inter'}}},y:{grid:{color:'rgba(255,255,255,.04)'},ticks:{color:'#6e7681',font:{size:9,family:'Inter'},callback:v=>v+'M'}}}}});
  },

  drawDonut(){
    const c=document.getElementById('donutChart');if(!c)return;
    if(this.charts.donut)this.charts.donut.destroy();
    const cats=this.stats.by_category||[];
    if(!cats.length)return;
    const top5=cats.slice(0,5);
    this.charts.donut=new Chart(c,{type:'doughnut',data:{
      labels:top5.map(c=>c.category),
      datasets:[{data:top5.map(c=>c.total),backgroundColor:top5.map(c=>catOf(c.category).color),borderWidth:0,hoverOffset:8}]
    },options:{responsive:true,maintainAspectRatio:false,cutout:'72%',plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>ctx.label+': '+this.fmtS(ctx.parsed)}}}}});
  },

  // ─ HELPERS ─
  groupTxs(list){
    const G={},td=new Date().toISOString().split('T')[0],yd=new Date(Date.now()-86400000).toISOString().split('T')[0];
    list.forEach(t=>{const k=t.transaction_date.split('T')[0];(G[k]=G[k]||[]).push(t)});
    return Object.entries(G).sort(([a],[b])=>b.localeCompare(a)).map(([date,items])=>{
      const net=items.reduce((s,t)=>t.type==='income'?s+parseFloat(t.amount):t.type==='expense'?s-parseFloat(t.amount):s,0);
      const d=new Date(date+'T00:00:00');
      const label=date===td?'Hôm nay':date===yd?'Hôm qua':d.toLocaleDateString('vi-VN',{weekday:'short',day:'numeric',month:'numeric'});
      return{date,label,items,net};
    });
  },

  curCats(){return this.form.type==='income'?CATS_I:CATS_E},
  catEmoji(n){return catOf(n).emoji},
  catColor(n){return catOf(n).color},

  numFmt(v){return new Intl.NumberFormat('vi-VN',{maximumFractionDigits:0}).format(parseFloat(v)||0)},
  fmtS(v){
    const n=parseFloat(v)||0,a=Math.abs(n),s=n<0?'-':'';
    if(a>=1e9)return s+(a/1e9).toFixed(1).replace(/\.0$/,'')+'B';
    if(a>=1e6)return s+(a/1e6).toFixed(1).replace(/\.0$/,'')+'M';
    if(a>=1e3)return s+(a/1e3).toFixed(0)+'K';
    return s+a.toFixed(0)+'₫';
  },
  fmtDate(d){if(!d)return'';return new Date(d+'T00:00:00').toLocaleDateString('vi-VN',{day:'2-digit',month:'2-digit',year:'numeric'})},
  notify(msg,type='success'){this.toast={show:true,msg,type};setTimeout(()=>this.toast.show=false,2800)},

  async api(url,opts={}){
    const csrfCookie=document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1];
    const tok=csrfCookie?decodeURIComponent(csrfCookie):CSRF;
    const r=await fetch(url,{headers:{'Content-Type':'application/json','X-XSRF-TOKEN':tok},...opts});
    return r.json();
  },

  // ─ ADD SCREEN ─
  openAdd(type){
    this.form={type,acc:this.accounts[0]?.id||'',toAcc:'',category:'',date:new Date().toISOString().split('T')[0],note:'',recur:false,period:'monthly'};
    this.numpad='';this.showAddScreen=true;
  },

  numPress(k){
    if(k==='⌫') this.numpad=this.numpad.slice(0,-1);
    else if(k==='C') this.numpad='';
    else if(k==='OK') this.saveTx();
    else if(k==='.'){if(!this.numpad.includes('.'))this.numpad+='.'}
    else if(k==='000'){if(this.numpad)this.numpad+='000'}
    else{if(this.numpad.length<12)this.numpad+=k}
  },

  // ─ CRUD ─
  async saveTx(){
    const amt=parseFloat(this.numpad);
    if(!amt||amt<=0){this.notify('Nhập số tiền hợp lệ','error');return}
    if(!this.form.acc){this.notify('Vui lòng chọn ví','error');return}
    if(!this.form.category&&this.form.type!=='transfer'){this.notify('Vui lòng chọn danh mục','error');return}
    this.loading=true;
    const payload={type:this.form.type,account_id:this.form.acc,to_account_id:this.form.toAcc||null,
      amount:amt,category:this.form.type==='transfer'?'Chuyển ví':this.form.category,
      transaction_date:this.form.date,note:this.form.note,
      is_recurring:this.form.recur,recurring_period:this.form.recur?this.form.period:null};
    try{
      const d=await this.api('/api/finance/transactions',{method:'POST',body:JSON.stringify(payload)});
      if(d.success){this.showAddScreen=false;this.notify('✅ Ghi chép thành công!');await this.load()}
      else this.notify(d.message||'Lỗi!','error');
    }catch{this.notify('Lỗi kết nối','error')}
    finally{this.loading=false}
  },

  async delTx(tx){
    if(!confirm(`Xoá "${tx.category}" — ${this.fmtS(tx.amount)}?`))return;
    this.loading=true;
    try{
      const d=await this.api('/api/finance/transactions/'+tx.id,{method:'DELETE'});
      if(d.success){this.notify('🗑 Đã xoá');await this.load()}
      else this.notify(d.message,'error');
    }catch{this.notify('Lỗi kết nối','error')}
    finally{this.loading=false}
  },

  async saveAcc(){
    if(!this.aForm.name){this.notify('Nhập tên ví','error');return}
    this.loading=true;
    try{
      const d=await this.api('/api/finance/accounts',{method:'POST',body:JSON.stringify({...this.aForm,balance:parseFloat(this.aForm.balance)||0})});
      if(d.success){this.showAccSheet=false;this.aForm={name:'',type:'cash',balance:''};this.notify('✅ Tạo ví thành công!');await this.load()}
      else this.notify(d.message,'error');
    }catch{this.notify('Lỗi kết nối','error')}
    finally{this.loading=false}
  },

  async saveDebt(){
    if(!this.dForm.name||!this.dForm.amount){this.notify('Điền đầy đủ thông tin','error');return}
    this.loading=true;
    try{
      const d=await this.api('/api/finance/debts',{method:'POST',body:JSON.stringify({partner_name:this.dForm.name,type:this.dForm.type,amount:parseFloat(this.dForm.amount),due_date:this.dForm.due||null,note:this.dForm.note})});
      if(d.success){this.showDebtSheet=false;this.dForm={name:'',type:'lend',amount:'',due:'',note:''};this.notify('✅ Ghi nhận thành công!');await this.load()}
      else this.notify(d.message,'error');
    }catch{this.notify('Lỗi kết nối','error')}
    finally{this.loading=false}
  },

  async toggleDebt(id){
    try{const d=await this.api('/api/finance/debts/'+id+'/toggle',{method:'POST'});if(d.success){this.notify('✅ Cập nhật trạng thái');await this.load()}}
    catch{this.notify('Lỗi','error')}
  },

  async deleteDebt(id){
    if(!confirm('Xoá khoản nợ này?'))return;
    this.loading=true;
    try{const d=await this.api('/api/finance/debts/'+id,{method:'DELETE'});if(d.success){this.notify('🗑 Đã xoá');await this.load()}}
    catch{this.notify('Lỗi','error')}
    finally{this.loading=false}
  },

  async saveInv(){
    if(!this.iForm.sym||!this.iForm.qty||!this.iForm.bp){this.notify('Điền đầy đủ thông tin','error');return}
    this.loading=true;
    try{
      const d=await this.api('/api/finance/investments',{method:'POST',body:JSON.stringify({symbol:this.iForm.sym.toUpperCase(),type:this.iForm.type,quantity:parseFloat(this.iForm.qty),buy_price:parseFloat(this.iForm.bp)})});
      if(d.success){this.showInvSheet=false;this.iForm={sym:'',type:'crypto',qty:'',bp:''};this.notify('✅ Thêm thành công!');await this.load()}
      else this.notify(d.message,'error');
    }catch{this.notify('Lỗi kết nối','error')}
    finally{this.loading=false}
  },

  async deleteInv(id){
    if(!confirm('Xoá tài sản này?'))return;
    this.loading=true;
    try{const d=await this.api('/api/finance/investments/'+id,{method:'DELETE'});if(d.success){this.notify('🗑 Đã xoá');await this.load()}}
    catch{this.notify('Lỗi','error')}
    finally{this.loading=false}
  },

  async updateRates(){
    this.loading=true;
    try{const d=await this.api('/api/finance/rates/update',{method:'POST'});this.notify(d.message||'✅ Cập nhật tỷ giá!');await this.load()}
    catch{this.notify('Lỗi cập nhật','error')}
    finally{this.loading=false}
  },

  saveGoal(){
    if(!this.gForm.name||!this.gForm.target){this.notify('Điền tên và mục tiêu','error');return}
    const goals=JSON.parse(localStorage.getItem('mt_goals')||'[]');
    goals.push({name:this.gForm.name,icon:this.gForm.icon,target:parseFloat(this.gForm.target),saved:parseFloat(this.gForm.saved)||0,deadline:this.gForm.deadline,id:Date.now()});
    localStorage.setItem('mt_goals',JSON.stringify(goals));
    this.gForm={name:'',icon:'🎯',target:'',saved:'',deadline:''};
    this.notify('✅ Đã thêm mục tiêu tiết kiệm!');
  },

  removeGoal(i){
    const goals=JSON.parse(localStorage.getItem('mt_goals')||'[]');
    goals.splice(i,1);localStorage.setItem('mt_goals',JSON.stringify(goals));
  },
}}
</script>
</body>
</html>
