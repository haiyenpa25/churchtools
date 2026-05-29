<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
<title>MoneyTracker</title>
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0d1117">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="MoneyTracker">
<link rel="apple-touch-icon" href="/icons/icon-192.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --green:#00c48c;--green-light:#00e5a8;--green-dark:#009e71;--green-glow:rgba(0,196,140,0.25);
  --red:#ff4d6d;--red-light:#ff6b85;--red-dark:#e63356;
  --blue:#4c9aff;--purple:#7b5cfa;--orange:#ff9f43;--yellow:#ffd32a;
  --bg:#0d1117;--bg2:#161b22;--bg3:#21262d;--bg4:#2d333b;
  --text:#e6edf3;--text2:#8b949e;--text3:#6e7681;
  --border:rgba(255,255,255,0.08);--border2:rgba(255,255,255,0.12);
  --radius:20px;--radius-sm:12px;--radius-xs:8px;
}
html,body{height:100%;overflow:hidden;background:var(--bg)}
body{font-family:'Inter',sans-serif;color:var(--text);-webkit-tap-highlight-color:transparent;display:flex;justify-content:center}
input,select,button{font-family:'Inter',sans-serif}
::-webkit-scrollbar{width:0;height:0}

/* ═══ APP SHELL ═══ */
#app{
  width:100%;max-width:430px;height:100dvh;
  display:flex;flex-direction:column;
  background:var(--bg);position:relative;overflow:hidden;
}

/* ═══ STATUS BAR SAFE AREA ═══ */
.safe-top{height:env(safe-area-inset-top,0px);background:var(--bg2);flex-shrink:0}

/* ═══ TOP BAR ═══ */
.topbar{
  background:var(--bg2);
  padding:12px 18px 10px;
  display:flex;align-items:center;justify-content:space-between;
  flex-shrink:0;
  border-bottom:1px solid var(--border);
}
.topbar-left{display:flex;align-items:center;gap:10px}
.topbar-avatar{
  width:34px;height:34px;border-radius:50%;
  background:linear-gradient(135deg,var(--green),var(--blue));
  display:flex;align-items:center;justify-content:center;
  font-size:14px;font-weight:700;color:#fff;
  box-shadow:0 0 0 2px rgba(0,196,140,0.3);
}
.topbar-info{display:flex;flex-direction:column}
.topbar-greeting{font-size:11px;color:var(--text2);font-weight:500}
.topbar-name{font-size:14px;font-weight:700;color:var(--text)}
.topbar-right{display:flex;align-items:center;gap:8px}
.topbar-btn{
  width:34px;height:34px;border-radius:10px;
  background:var(--bg3);border:1px solid var(--border);
  color:var(--text2);display:flex;align-items:center;justify-content:center;
  cursor:pointer;font-size:14px;text-decoration:none;
  transition:all .2s;
}
.topbar-btn:active{transform:scale(.9);background:var(--bg4)}
.online-indicator{
  display:flex;align-items:center;gap:5px;
  font-size:10px;font-weight:600;color:var(--green);
  background:rgba(0,196,140,.1);border:1px solid rgba(0,196,140,.2);
  border-radius:99px;padding:4px 8px;
}
.online-dot{width:6px;height:6px;border-radius:50%;background:var(--green);animation:pulse-g 2s infinite}
@keyframes pulse-g{0%,100%{opacity:1;box-shadow:0 0 0 0 var(--green-glow)}50%{opacity:.7;box-shadow:0 0 0 4px transparent}}

/* ═══ MONTH NAV ═══ */
.month-nav{
  background:var(--bg2);
  padding:8px 18px;display:flex;align-items:center;justify-content:center;gap:16px;
  flex-shrink:0;
}
.month-btn{
  width:28px;height:28px;border-radius:8px;
  background:var(--bg3);border:1px solid var(--border);
  color:var(--text2);display:flex;align-items:center;justify-content:center;
  cursor:pointer;font-size:13px;transition:all .2s;
}
.month-btn:active{background:var(--green);color:#fff;transform:scale(.9)}
.month-text{font-size:13px;font-weight:700;color:var(--text);min-width:100px;text-align:center}

/* ═══ SCROLL CONTENT ═══ */
.scroll-area{flex:1;overflow-y:auto;overflow-x:hidden;-webkit-overflow-scrolling:touch}

/* ═══ HERO BALANCE CARD ═══ */
.hero-card{
  margin:14px 16px 0;
  border-radius:24px;overflow:hidden;position:relative;
  background:linear-gradient(135deg,#0d2137 0%,#0a1628 40%,#0d1f35 70%,#091829 100%);
  padding:22px 22px 20px;
  border:1px solid rgba(0,196,140,0.15);
}
.hero-card::before{
  content:'';position:absolute;top:-40px;right:-30px;
  width:180px;height:180px;border-radius:50%;
  background:radial-gradient(circle,rgba(0,196,140,.15) 0%,transparent 70%);
  pointer-events:none;
}
.hero-card::after{
  content:'';position:absolute;bottom:-30px;left:-20px;
  width:120px;height:120px;border-radius:50%;
  background:radial-gradient(circle,rgba(76,154,255,.1) 0%,transparent 70%);
  pointer-events:none;
}
.hero-label{
  font-size:11px;font-weight:600;color:rgba(255,255,255,.5);
  text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px;
  display:flex;align-items:center;gap:6px;
}
.hero-label::before{content:'';width:4px;height:4px;border-radius:50%;background:var(--green);display:inline-block}
.hero-balance{
  font-size:32px;font-weight:900;color:#fff;
  letter-spacing:-1px;line-height:1;margin-bottom:4px;
}
.hero-balance span{font-size:16px;font-weight:600;opacity:.7;margin-right:4px}
.hero-currency{font-size:12px;color:rgba(255,255,255,.4);font-weight:500;margin-bottom:18px}
.hero-stats{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.hero-stat{
  border-radius:14px;padding:10px 12px;
  display:flex;align-items:center;gap:10px;
}
.hero-stat-income{background:rgba(0,196,140,.12);border:1px solid rgba(0,196,140,.2)}
.hero-stat-expense{background:rgba(255,77,109,.12);border:1px solid rgba(255,77,109,.2)}
.hero-stat-icon{
  width:32px;height:32px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;
}
.hs-income-icon{background:rgba(0,196,140,.2)}
.hs-expense-icon{background:rgba(255,77,109,.2)}
.hero-stat-info{min-width:0}
.hero-stat-label{font-size:9px;font-weight:600;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.06em}
.hero-stat-value{font-size:14px;font-weight:800;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.hsv-income{color:var(--green)}
.hsv-expense{color:var(--red)}

/* ═══ WALLET SWIPER ═══ */
.section-header{
  padding:16px 18px 10px;
  display:flex;align-items:center;justify-content:space-between;
}
.section-label{font-size:12px;font-weight:700;color:var(--text2);text-transform:uppercase;letter-spacing:.08em}
.section-action{font-size:12px;font-weight:600;color:var(--green);cursor:pointer}

.wallet-scroll{
  display:flex;gap:12px;overflow-x:auto;padding:0 18px 4px;
  scroll-snap-type:x mandatory;scrollbar-width:none;
}
.wallet-scroll::-webkit-scrollbar{display:none}

.wallet-card{
  flex-shrink:0;width:180px;height:100px;
  border-radius:18px;padding:14px 16px;
  scroll-snap-align:start;cursor:pointer;
  display:flex;flex-direction:column;justify-content:space-between;
  position:relative;overflow:hidden;
  transition:transform .2s;
}
.wallet-card:active{transform:scale(.96)}
.wallet-card::after{
  content:'';position:absolute;top:-20px;right:-20px;
  width:80px;height:80px;border-radius:50%;
  background:rgba(255,255,255,.08);pointer-events:none;
}
.wallet-card::before{
  content:'';position:absolute;bottom:-25px;left:-10px;
  width:90px;height:90px;border-radius:50%;
  background:rgba(255,255,255,.05);pointer-events:none;
}
.wc-0{background:linear-gradient(135deg,#00c48c,#009e71)}
.wc-1{background:linear-gradient(135deg,#4c9aff,#2979ff)}
.wc-2{background:linear-gradient(135deg,#7b5cfa,#5b3ff5)}
.wc-3{background:linear-gradient(135deg,#ff9f43,#e67e22)}
.wc-4{background:linear-gradient(135deg,#ff4d6d,#c9184a)}
.wc-5{background:linear-gradient(135deg,#1de9b6,#00bfa5)}

.wc-top{display:flex;align-items:center;justify-content:space-between}
.wc-type-icon{font-size:16px;opacity:.9}
.wc-dots{display:flex;gap:3px}
.wc-dot{width:5px;height:5px;border-radius:50%;background:rgba(255,255,255,.5)}
.wc-name{font-size:12px;font-weight:600;color:rgba(255,255,255,.85);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.wc-balance{font-size:16px;font-weight:800;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

.wallet-add{
  flex-shrink:0;width:90px;height:100px;
  border-radius:18px;border:2px dashed rgba(255,255,255,.1);
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:6px;cursor:pointer;color:var(--text3);
  transition:all .2s;scroll-snap-align:start;
}
.wallet-add:active{border-color:var(--green);color:var(--green);transform:scale(.96)}
.wallet-add-icon{font-size:24px;opacity:.6}
.wallet-add-label{font-size:10px;font-weight:700}

/* ═══ CHART ═══ */
.chart-wrap{
  margin:4px 16px 0;
  background:var(--bg2);border:1px solid var(--border);
  border-radius:var(--radius);padding:14px 16px;
}
.chart-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.chart-title{font-size:12px;font-weight:700;color:var(--text2);text-transform:uppercase;letter-spacing:.06em}
.chart-legend{display:flex;gap:12px}
.cl-item{display:flex;align-items:center;gap:5px;font-size:10px;font-weight:600;color:var(--text2)}
.cl-dot{width:8px;height:8px;border-radius:3px}
.chart-canvas-wrap{height:90px}

/* ═══ TRANSACTIONS LIST ═══ */
.tx-group-wrap{margin:12px 16px 0}
.tx-date-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:6px 0 8px;margin-bottom:2px;
  border-bottom:1px solid var(--border);
}
.tx-date-label{font-size:12px;font-weight:700;color:var(--text2)}
.tx-date-label.is-today{color:var(--green)}
.tx-date-label.is-yesterday{color:var(--yellow)}
.tx-date-net{font-size:12px;font-weight:700;color:var(--text3)}

.tx-item{
  display:flex;align-items:center;gap:12px;
  padding:11px 0;cursor:pointer;
  border-bottom:1px solid rgba(255,255,255,.03);
  transition:opacity .15s;
}
.tx-item:last-child{border-bottom:none}
.tx-item:active{opacity:.7}

.tx-icon{
  width:46px;height:46px;border-radius:14px;
  display:flex;align-items:center;justify-content:center;
  font-size:22px;flex-shrink:0;
}
.tx-body{flex:1;min-width:0}
.tx-cat{font-size:14px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tx-meta{display:flex;align-items:center;gap:6px;margin-top:2px}
.tx-acc{font-size:10px;font-weight:600;color:var(--text3);background:var(--bg3);border-radius:6px;padding:2px 6px}
.tx-note{font-size:11px;color:var(--text3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tx-amount{font-size:15px;font-weight:800;flex-shrink:0;text-align:right}
.tx-amount.income{color:var(--green)}
.tx-amount.expense{color:var(--red)}
.tx-amount.transfer{color:var(--blue)}

/* ═══ EMPTY STATE ═══ */
.empty{padding:50px 20px;text-align:center;color:var(--text3)}
.empty-icon{font-size:52px;opacity:.3;margin-bottom:12px}
.empty-title{font-size:14px;font-weight:700;color:var(--text2);margin-bottom:6px}
.empty-desc{font-size:12px}

/* ═══ BOTTOM NAV ═══ */
.bottom-nav{
  flex-shrink:0;background:var(--bg2);
  border-top:1px solid var(--border);
  display:grid;grid-template-columns:1fr 1fr 70px 1fr 1fr;
  align-items:center;padding:6px 4px max(10px,env(safe-area-inset-bottom));
}
.nav-btn{
  display:flex;flex-direction:column;align-items:center;gap:3px;
  padding:6px 4px;cursor:pointer;background:none;border:none;
  color:var(--text3);font-family:inherit;transition:all .2s;border-radius:12px;
}
.nav-btn:active{transform:scale(.88)}
.nav-btn.active{color:var(--green)}
.nav-icon{font-size:21px;line-height:1}
.nav-label{font-size:10px;font-weight:600}

.fab-wrap{display:flex;align-items:center;justify-content:center}
.fab{
  width:56px;height:56px;border-radius:50%;
  background:linear-gradient(135deg,var(--green-light),var(--green));
  border:none;display:flex;align-items:center;justify-content:center;
  cursor:pointer;color:#fff;font-size:28px;font-weight:300;
  box-shadow:0 4px 20px var(--green-glow),0 2px 8px rgba(0,0,0,.4);
  margin-top:-20px;transition:all .2s;font-family:inherit;
}
.fab:active{transform:scale(.9);box-shadow:0 2px 10px var(--green-glow)}

/* ═══ SCREENS ═══ */
.screen{
  position:absolute;inset:0;z-index:50;
  display:flex;flex-direction:column;background:var(--bg);
  transform:translateY(100%);
  transition:transform .38s cubic-bezier(0.16,1,0.3,1);
  will-change:transform;
}
.screen.open{transform:translateY(0)}

/* ═══ ADD TRANSACTION SCREEN ═══ */
.add-header{
  background:var(--bg2);border-bottom:1px solid var(--border);
  padding:14px 16px;flex-shrink:0;
  display:flex;align-items:center;justify-content:space-between;
  padding-top:max(14px,env(safe-area-inset-top));
}
.close-btn{
  width:34px;height:34px;border-radius:10px;
  background:var(--bg3);border:1px solid var(--border);
  color:var(--text2);display:flex;align-items:center;justify-content:center;
  cursor:pointer;font-size:18px;transition:all .2s;
}
.close-btn:active{background:rgba(255,77,109,.15);color:var(--red);transform:scale(.9)}

.type-tabs{
  display:flex;background:var(--bg3);border-radius:12px;padding:3px;gap:2px;
  flex:1;margin:0 12px;
}
.type-tab{
  flex:1;text-align:center;padding:8px 6px;border-radius:9px;
  font-size:12px;font-weight:700;cursor:pointer;
  transition:all .2s;border:none;background:transparent;
  color:var(--text3);font-family:inherit;
}
.type-tab.t-expense{background:var(--red);color:#fff;box-shadow:0 2px 8px rgba(255,77,109,.35)}
.type-tab.t-income{background:var(--green);color:#fff;box-shadow:0 2px 8px var(--green-glow)}
.type-tab.t-transfer{background:var(--blue);color:#fff;box-shadow:0 2px 8px rgba(76,154,255,.35)}

/* Amount Hero */
.amount-hero{
  background:var(--bg2);padding:24px 20px 16px;
  text-align:center;flex-shrink:0;border-bottom:1px solid var(--border);
}
.amount-cat-preview{
  display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:12px;
}
.amount-cat-icon{
  width:36px;height:36px;border-radius:12px;
  display:flex;align-items:center;justify-content:center;font-size:18px;
}
.amount-cat-name{font-size:13px;font-weight:600;color:var(--text2)}
.amount-value{
  font-size:42px;font-weight:900;letter-spacing:-1.5px;line-height:1;
  transition:color .2s;
  min-height:50px;display:flex;align-items:center;justify-content:center;
}
.av-expense{color:var(--red)}
.av-income{color:var(--green)}
.av-transfer{color:var(--blue)}
.av-zero{color:var(--text3)}
.amount-currency{font-size:13px;font-weight:500;color:var(--text3);margin-top:4px}

/* Category Grid */
.cat-scroll{flex:1;overflow-y:auto;padding:12px 16px}
.cat-section{margin-bottom:16px}
.cat-section-title{font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px}
.cat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px}
.cat-item{
  display:flex;flex-direction:column;align-items:center;gap:5px;
  padding:10px 4px;border-radius:14px;cursor:pointer;
  border:2px solid transparent;background:var(--bg2);
  transition:all .18s;
}
.cat-item:active{transform:scale(.92)}
.cat-item.selected{border-color:var(--green);background:rgba(0,196,140,.1)}
.cat-item.selected-exp{border-color:var(--red);background:rgba(255,77,109,.1)}
.cat-item .ci-icon{
  width:38px;height:38px;border-radius:12px;
  display:flex;align-items:center;justify-content:center;font-size:20px;
}
.cat-item .ci-label{font-size:10px;font-weight:600;color:var(--text2);text-align:center;line-height:1.2}
.cat-item.selected .ci-label{color:var(--green)}
.cat-item.selected-exp .ci-label{color:var(--red)}

/* Transaction Details */
.tx-details{
  background:var(--bg2);border-top:1px solid var(--border);
  flex-shrink:0;
}
.detail-row{
  display:flex;align-items:center;gap:12px;
  padding:11px 16px;border-bottom:1px solid var(--border);
}
.detail-row:last-child{border-bottom:none}
.dr-icon{
  width:32px;height:32px;border-radius:10px;background:var(--bg3);
  display:flex;align-items:center;justify-content:center;
  font-size:15px;flex-shrink:0;
}
.dr-input{
  flex:1;background:none;border:none;outline:none;
  color:var(--text);font-size:14px;font-weight:500;font-family:inherit;
}
.dr-input::placeholder{color:var(--text3)}
.dr-select{
  flex:1;background:none;border:none;outline:none;
  color:var(--text);font-size:14px;font-weight:500;
  font-family:inherit;cursor:pointer;-webkit-appearance:none;
}
.dr-select option{background:var(--bg2);color:var(--text)}

/* Numpad */
.numpad{
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:1px;background:var(--border);border-top:1px solid var(--border);
  flex-shrink:0;
}
.nk{
  background:var(--bg2);display:flex;align-items:center;justify-content:center;
  padding:16px 0;font-size:20px;font-weight:600;cursor:pointer;
  transition:background .1s;border:none;color:var(--text);
  font-family:inherit;user-select:none;-webkit-tap-highlight-color:transparent;
  position:relative;overflow:hidden;
}
.nk::after{
  content:'';position:absolute;inset:0;background:rgba(255,255,255,0);
  transition:background .1s;
}
.nk:active::after{background:rgba(255,255,255,.05)}
.nk-del{color:var(--text2);font-size:18px}
.nk-zero{font-size:16px}
.nk-ok{
  background:linear-gradient(135deg,var(--green-light),var(--green));
  color:#fff;font-size:13px;font-weight:800;
  grid-row:span 2;
  box-shadow:inset 0 1px 0 rgba(255,255,255,.2);
}
.nk-ok:active::after{background:rgba(0,0,0,.1)}
.nk-dot{font-size:22px;font-weight:900}

/* ═══ BOTTOM SHEETS ═══ */
.overlay{
  position:fixed;inset:0;z-index:60;
  background:rgba(0,0,0,.65);backdrop-filter:blur(6px);
  display:flex;align-items:flex-end;justify-content:center;
}
.sheet{
  width:100%;max-width:430px;background:var(--bg2);
  border-radius:24px 24px 0 0;border-top:1px solid var(--border2);
  padding:0 20px max(24px,env(safe-area-inset-bottom));
  animation:sheetUp .3s cubic-bezier(0.16,1,0.3,1);
  max-height:88vh;overflow-y:auto;
}
@keyframes sheetUp{from{transform:translateY(100%)}to{transform:translateY(0)}}
.sheet-handle{width:36px;height:4px;background:rgba(255,255,255,.12);border-radius:2px;margin:14px auto 20px}
.sheet-title{font-size:16px;font-weight:800;color:var(--text);margin-bottom:20px}
.f-label{font-size:11px;font-weight:700;color:var(--text2);text-transform:uppercase;letter-spacing:.06em;margin-bottom:7px}
.f-input{
  width:100%;background:var(--bg3);border:1.5px solid var(--border);
  border-radius:14px;padding:13px 14px;color:var(--text);
  font-size:15px;font-family:inherit;outline:none;
  transition:border-color .2s;margin-bottom:14px;
}
.f-input:focus{border-color:var(--green)}
.f-select{
  width:100%;background:var(--bg3);border:1.5px solid var(--border);
  border-radius:14px;padding:13px 14px;color:var(--text);
  font-size:15px;font-family:inherit;outline:none;
  margin-bottom:14px;-webkit-appearance:none;cursor:pointer;
}
.f-select option{background:var(--bg2)}
.f-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.btn-primary{
  width:100%;background:linear-gradient(135deg,var(--green-light),var(--green));
  color:#fff;font-size:15px;font-weight:700;padding:15px;
  border-radius:16px;border:none;cursor:pointer;font-family:inherit;
  transition:all .2s;box-shadow:0 4px 20px var(--green-glow);
}
.btn-primary:active{transform:scale(.98);box-shadow:0 2px 10px var(--green-glow)}

/* ═══ DEBTS TAB ═══ */
.debt-banner{
  display:grid;grid-template-columns:1fr 1fr;gap:10px;padding:14px 16px 0;
}
.debt-card{border-radius:18px;padding:16px}
.dc-lend{background:linear-gradient(135deg,rgba(0,196,140,.15),rgba(0,196,140,.05));border:1px solid rgba(0,196,140,.2)}
.dc-borrow{background:linear-gradient(135deg,rgba(255,77,109,.15),rgba(255,77,109,.05));border:1px solid rgba(255,77,109,.2)}
.dc-icon{font-size:20px;margin-bottom:8px}
.dc-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px}
.dc-lend .dc-label{color:var(--green)}
.dc-borrow .dc-label{color:var(--red)}
.dc-amount{font-size:18px;font-weight:800;color:var(--text)}
.dc-count{font-size:10px;color:var(--text3);margin-top:2px}

.tab-toolbar{
  display:flex;align-items:center;justify-content:space-between;
  padding:14px 16px 8px;
}
.tab-toolbar-label{font-size:12px;font-weight:700;color:var(--text2);text-transform:uppercase;letter-spacing:.08em}
.btn-add-sm{
  background:linear-gradient(135deg,var(--green-light),var(--green));
  color:#fff;border:none;border-radius:12px;
  padding:8px 16px;font-size:12px;font-weight:700;
  cursor:pointer;font-family:inherit;
  box-shadow:0 2px 12px var(--green-glow);transition:all .2s;
  display:flex;align-items:center;gap:5px;
}
.btn-add-sm:active{transform:scale(.95)}

.debt-item{
  margin:0 16px 10px;background:var(--bg2);
  border:1px solid var(--border);border-radius:18px;padding:14px 16px;
  display:flex;align-items:center;gap:12px;
}
.debt-ava{
  width:44px;height:44px;border-radius:50%;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-size:16px;font-weight:800;color:#fff;
}
.da-lend{background:linear-gradient(135deg,var(--green),var(--green-dark))}
.da-borrow{background:linear-gradient(135deg,var(--red),var(--red-dark))}
.debt-info{flex:1;min-width:0}
.debt-name{font-size:14px;font-weight:700;color:var(--text)}
.debt-sub{font-size:11px;color:var(--text3);margin-top:2px}
.debt-right{text-align:right;flex-shrink:0}
.debt-amount{font-size:15px;font-weight:800}
.da-lend-text{color:var(--green)}
.da-borrow-text{color:var(--red)}
.debt-badge{
  font-size:10px;font-weight:700;padding:3px 8px;border-radius:8px;
  display:inline-block;margin-top:4px;cursor:pointer;transition:all .2s;
}
.db-paid{background:var(--bg3);color:var(--text3)}
.db-unpaid{background:rgba(0,196,140,.15);color:var(--green)}
.debt-del{background:none;border:none;color:var(--text3);cursor:pointer;font-size:14px;margin-top:4px;display:block;padding:2px}

/* ═══ INVESTMENTS TAB ═══ */
.portfolio-card{
  margin:14px 16px 0;
  background:linear-gradient(135deg,rgba(76,154,255,.1),rgba(123,92,250,.1));
  border:1px solid rgba(76,154,255,.2);border-radius:20px;padding:20px;text-align:center;
}
.pc-label{font-size:11px;color:var(--text2);font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px}
.pc-value{font-size:30px;font-weight:900;color:var(--text);margin-bottom:6px}
.pc-pnl{font-size:14px;font-weight:700;display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:99px}
.pnl-pos{background:rgba(0,196,140,.15);color:var(--green);border:1px solid rgba(0,196,140,.2)}
.pnl-neg{background:rgba(255,77,109,.15);color:var(--red);border:1px solid rgba(255,77,109,.2)}

.inv-item{
  margin:10px 16px 0;background:var(--bg2);
  border:1px solid var(--border);border-radius:18px;padding:14px 16px;
  display:flex;align-items:center;gap:12px;
}
.inv-badge{
  width:46px;height:46px;border-radius:14px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-size:11px;font-weight:800;color:#fff;letter-spacing:-.5px;
}
.ib-crypto{background:linear-gradient(135deg,#f59e0b,#d97706)}
.ib-stock{background:linear-gradient(135deg,var(--blue),#2979ff)}
.inv-info{flex:1;min-width:0}
.inv-name{font-size:14px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:6px}
.inv-tag{font-size:9px;font-weight:700;background:var(--bg3);color:var(--text3);padding:2px 6px;border-radius:6px;text-transform:uppercase}
.inv-sub{font-size:11px;color:var(--text3);margin-top:3px}
.inv-right{text-align:right;flex-shrink:0}
.inv-val{font-size:15px;font-weight:800;color:var(--text)}
.inv-pct{font-size:12px;font-weight:700;margin-top:3px}
.ip-pos{color:var(--green)}
.ip-neg{color:var(--red)}
.inv-del{background:none;border:none;color:var(--text3);cursor:pointer;font-size:13px;margin-top:4px;display:block;padding:2px}

/* ═══ TOAST ═══ */
.toast-wrap{
  position:fixed;top:0;left:50%;transform:translateX(-50%);
  z-index:999;pointer-events:none;padding-top:max(16px,env(safe-area-inset-top));
}
.toast{
  background:var(--bg2);border:1px solid var(--border2);
  border-radius:14px;padding:10px 18px;font-size:13px;font-weight:600;
  white-space:nowrap;box-shadow:0 8px 32px rgba(0,0,0,.4);
  display:flex;align-items:center;gap:8px;
  transform:translateY(-60px);opacity:0;
  transition:all .35s cubic-bezier(0.16,1,0.3,1);
}
.toast.show{transform:translateY(0);opacity:1}
.toast.success{border-color:rgba(0,196,140,.3);color:var(--green)}
.toast.error{border-color:rgba(255,77,109,.3);color:var(--red)}

/* ═══ LOADING SPINNER ═══ */
.loading-wrap{
  position:fixed;inset:0;z-index:999;background:rgba(13,17,23,.75);
  backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;
}
.spinner{
  width:40px;height:40px;border:3px solid rgba(0,196,140,.15);
  border-top-color:var(--green);border-radius:50%;
  animation:spin .75s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}

.pb{padding-bottom:20px}
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

<!-- TOP BAR -->
<div class="topbar">
  <div class="topbar-left">
    <div class="topbar-avatar" x-text="userName.charAt(0).toUpperCase()"></div>
    <div class="topbar-info">
      <span class="topbar-greeting">Xin chào 👋</span>
      <span class="topbar-name" x-text="userName"></span>
    </div>
  </div>
  <div class="topbar-right">
    <div class="online-indicator" x-show="isOnline">
      <div class="online-dot"></div>Online
    </div>
    <form action="{{ route('logout') }}" method="POST" style="margin:0">
      @csrf
      <button type="submit" class="topbar-btn" title="Đăng xuất">🚪</button>
    </form>
  </div>
</div>

<!-- MONTH NAV -->
<div class="month-nav">
  <button class="month-btn" @click="changeMonth(-1)">‹</button>
  <span class="month-text" x-text="monthLabel"></span>
  <button class="month-btn" @click="changeMonth(1)">›</button>
</div>

<!-- SCROLL AREA -->
<div class="scroll-area">

  <!-- ══ HOME TAB ══ -->
  <div x-show="tab==='home'" x-transition:enter="transition ease-out duration-150">

    <!-- Hero Balance -->
    <div class="hero-card">
      <div class="hero-label">Tài sản ròng</div>
      <div class="hero-balance">
        <span>₫</span><span x-text="numFmt(overview.net_worth)"></span>
      </div>
      <div class="hero-currency">VNĐ — Tổng tài sản trừ nợ</div>
      <div class="hero-stats">
        <div class="hero-stat hero-stat-income">
          <div class="hero-stat-icon hs-income-icon">↓</div>
          <div class="hero-stat-info">
            <div class="hero-stat-label">Thu tháng này</div>
            <div class="hero-stat-value hsv-income" x-text="fmtShort(monthStats.income)"></div>
          </div>
        </div>
        <div class="hero-stat hero-stat-expense">
          <div class="hero-stat-icon hs-expense-icon">↑</div>
          <div class="hero-stat-info">
            <div class="hero-stat-label">Chi tháng này</div>
            <div class="hero-stat-value hsv-expense" x-text="fmtShort(monthStats.expense)"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Wallet Swiper -->
    <div class="section-header">
      <span class="section-label">Ví của tôi</span>
      <span class="section-action" @click="showAccountSheet=true">＋ Thêm ví</span>
    </div>
    <div class="wallet-scroll">
      <template x-for="(acc,i) in accounts" :key="acc.id">
        <div class="wallet-card" :class="'wc-'+i%6">
          <div class="wc-top">
            <span class="wc-type-icon" x-text="acc.type==='bank'?'🏦':(acc.type==='e-wallet'?'📱':'👛')"></span>
            <div class="wc-dots"><div class="wc-dot"></div><div class="wc-dot"></div><div class="wc-dot"></div></div>
          </div>
          <div>
            <div class="wc-name" x-text="acc.name"></div>
            <div class="wc-balance" x-text="fmtShort(acc.balance)"></div>
          </div>
        </div>
      </template>
      <div class="wallet-add" @click="showAccountSheet=true">
        <div class="wallet-add-icon">＋</div>
        <div class="wallet-add-label">Thêm ví</div>
      </div>
    </div>

    <!-- Weekly Chart -->
    <div class="chart-wrap">
      <div class="chart-header">
        <span class="chart-title">Thu / Chi 7 ngày</span>
        <div class="chart-legend">
          <div class="cl-item"><div class="cl-dot" style="background:var(--green)"></div>Thu</div>
          <div class="cl-item"><div class="cl-dot" style="background:var(--red)"></div>Chi</div>
        </div>
      </div>
      <div class="chart-canvas-wrap"><canvas id="weekChart"></canvas></div>
    </div>

    <!-- Recent Transactions -->
    <div class="section-header" style="padding-top:14px">
      <span class="section-label">Gần đây</span>
      <span class="section-action" @click="tab='transactions'">Xem tất cả</span>
    </div>

    <template x-for="g in grouped.slice(0,2)" :key="g.date">
      <div class="tx-group-wrap">
        <div class="tx-date-header">
          <span class="tx-date-label" :class="g.label==='Hôm nay'?'is-today':(g.label==='Hôm qua'?'is-yesterday':'')" x-text="g.label"></span>
          <span class="tx-date-net" x-text="fmtShort(g.net)"></span>
        </div>
        <template x-for="tx in g.items.slice(0,3)" :key="tx.id">
          <div class="tx-item">
            <div class="tx-icon" :style="'background:'+catColor(tx.category)+'22'">
              <span x-text="catEmoji(tx.category)"></span>
            </div>
            <div class="tx-body">
              <div class="tx-cat" x-text="tx.category"></div>
              <div class="tx-meta">
                <span class="tx-acc" x-text="tx.account?tx.account.name:''"></span>
                <span class="tx-note" x-text="tx.note||''"></span>
              </div>
            </div>
            <div class="tx-amount" :class="tx.type" x-text="(tx.type==='income'?'+':'')+(tx.type==='expense'?'-':'')+''+fmtShort(tx.amount)"></div>
          </div>
        </template>
      </div>
    </template>

    <template x-if="transactions.length===0">
      <div class="empty">
        <div class="empty-icon">💸</div>
        <div class="empty-title">Chưa có giao dịch nào</div>
        <div class="empty-desc">Nhấn ＋ để ghi chép giao dịch đầu tiên</div>
      </div>
    </template>
    <div class="pb"></div>
  </div>

  <!-- ══ TRANSACTIONS TAB ══ -->
  <div x-show="tab==='transactions'" x-transition:enter="transition ease-out duration-150">
    <!-- Summary bar -->
    <div style="display:flex;gap:8px;padding:12px 16px 4px;overflow-x:auto">
      <div style="flex:1;background:rgba(0,196,140,.1);border:1px solid rgba(0,196,140,.2);border-radius:12px;padding:10px 12px;white-space:nowrap">
        <div style="font-size:10px;font-weight:700;color:var(--green);margin-bottom:3px">↓ THU NHẬP</div>
        <div style="font-size:15px;font-weight:800;color:var(--green)" x-text="fmtShort(monthStats.income)"></div>
      </div>
      <div style="flex:1;background:rgba(255,77,109,.1);border:1px solid rgba(255,77,109,.2);border-radius:12px;padding:10px 12px;white-space:nowrap">
        <div style="font-size:10px;font-weight:700;color:var(--red);margin-bottom:3px">↑ CHI PHÍ</div>
        <div style="font-size:15px;font-weight:800;color:var(--red)" x-text="fmtShort(monthStats.expense)"></div>
      </div>
      <div style="flex:1;background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:10px 12px;white-space:nowrap">
        <div style="font-size:10px;font-weight:700;color:var(--text3);margin-bottom:3px">⚖ CÒN LẠI</div>
        <div style="font-size:15px;font-weight:800;color:var(--text)" x-text="fmtShort(monthStats.income-monthStats.expense)"></div>
      </div>
    </div>

    <template x-if="grouped.length===0">
      <div class="empty">
        <div class="empty-icon">📋</div>
        <div class="empty-title">Tháng này chưa có giao dịch</div>
        <div class="empty-desc">Nhấn ＋ để thêm</div>
      </div>
    </template>

    <template x-for="g in grouped" :key="g.date">
      <div class="tx-group-wrap">
        <div class="tx-date-header">
          <span class="tx-date-label" :class="g.label==='Hôm nay'?'is-today':(g.label==='Hôm qua'?'is-yesterday':'')" x-text="g.label"></span>
          <span class="tx-date-net" x-text="fmtShort(g.net)"></span>
        </div>
        <template x-for="tx in g.items" :key="tx.id">
          <div class="tx-item" @click="delTx(tx)">
            <div class="tx-icon" :style="'background:'+catColor(tx.category)+'22'">
              <span x-text="catEmoji(tx.category)"></span>
            </div>
            <div class="tx-body">
              <div class="tx-cat" x-text="tx.category"></div>
              <div class="tx-meta">
                <span class="tx-acc" x-text="tx.account?tx.account.name:''"></span>
                <span class="tx-note" x-text="tx.note||'Không có ghi chú'"></span>
              </div>
            </div>
            <div class="tx-amount" :class="tx.type"
                 x-text="(tx.type==='income'?'+':tx.type==='expense'?'-':'⇄ ')+fmtShort(tx.amount)"></div>
          </div>
        </template>
      </div>
    </template>
    <div class="pb"></div>
  </div>

  <!-- ══ DEBTS TAB ══ -->
  <div x-show="tab==='debts'" x-transition:enter="transition ease-out duration-150">
    <div class="debt-banner">
      <div class="debt-card dc-lend">
        <div class="dc-icon">📤</div>
        <div class="dc-label">Cho vay</div>
        <div class="dc-amount" x-text="fmtShort(overview.total_lend)"></div>
        <div class="dc-count" x-text="debts.filter(d=>d.type==='lend').length+' khoản'"></div>
      </div>
      <div class="debt-card dc-borrow">
        <div class="dc-icon">📥</div>
        <div class="dc-label">Đi vay</div>
        <div class="dc-amount" x-text="fmtShort(overview.total_borrow)"></div>
        <div class="dc-count" x-text="debts.filter(d=>d.type==='borrow').length+' khoản'"></div>
      </div>
    </div>
    <div class="tab-toolbar">
      <span class="tab-toolbar-label">Danh sách</span>
      <button class="btn-add-sm" @click="showDebtSheet=true">＋ Thêm nợ</button>
    </div>
    <template x-if="debts.length===0">
      <div class="empty">
        <div class="empty-icon">🤝</div>
        <div class="empty-title">Chưa có khoản nợ nào</div>
        <div class="empty-desc">Nhấn ＋ Thêm nợ để ghi nhận</div>
      </div>
    </template>
    <template x-for="d in debts" :key="d.id">
      <div class="debt-item">
        <div class="debt-ava" :class="d.type==='lend'?'da-lend':'da-borrow'"
             x-text="(d.partner_name||'?').charAt(0).toUpperCase()"></div>
        <div class="debt-info">
          <div class="debt-name" x-text="d.partner_name"></div>
          <div class="debt-sub">
            <span x-text="d.type==='lend'?'📤 Cho vay':'📥 Đi vay'"></span>
            <template x-if="d.due_date"><span x-text="' · Hạn: '+fmtDate(d.due_date)"></span></template>
          </div>
          <div class="debt-sub" x-text="d.note" style="font-style:italic;opacity:.7"></div>
        </div>
        <div class="debt-right">
          <div class="debt-amount" :class="d.type==='lend'?'da-lend-text':'da-borrow-text'" x-text="fmtShort(d.amount)"></div>
          <span class="debt-badge" :class="d.status==='paid'?'db-paid':'db-unpaid'"
                @click="toggleDebt(d.id)" x-text="d.status==='paid'?'✓ Đã trả':'⏳ Chưa trả'"></span>
          <button class="debt-del" @click="deleteDebt(d.id)">🗑</button>
        </div>
      </div>
    </template>
    <div class="pb"></div>
  </div>

  <!-- ══ INVESTMENTS TAB ══ -->
  <div x-show="tab==='investments'" x-transition:enter="transition ease-out duration-150">
    <div class="portfolio-card">
      <div class="pc-label">Danh mục đầu tư</div>
      <div class="pc-value" x-text="'₫ '+numFmt(overview.total_investment)"></div>
      <span class="pc-pnl" :class="overview.investment_pnl>=0?'pnl-pos':'pnl-neg'"
            x-text="(overview.investment_pnl>=0?'▲ +':'▼ ')+fmtShort(overview.investment_pnl)+' ('+overview.investment_pnl_percent.toFixed(2)+'%)'"></span>
    </div>
    <div class="tab-toolbar">
      <span class="tab-toolbar-label">Danh mục</span>
      <div style="display:flex;gap:8px">
        <button class="btn-add-sm" style="background:var(--bg3);color:var(--green);box-shadow:none;border:1px solid rgba(0,196,140,.3)" @click="updateRates()">🔄 Tỷ giá</button>
        <button class="btn-add-sm" @click="showInvSheet=true">＋ Thêm</button>
      </div>
    </div>
    <template x-if="investments.length===0">
      <div class="empty">
        <div class="empty-icon">📈</div>
        <div class="empty-title">Chưa có danh mục</div>
        <div class="empty-desc">Thêm Crypto hoặc Cổ phiếu</div>
      </div>
    </template>
    <template x-for="inv in investments" :key="inv.id">
      <div class="inv-item">
        <div class="inv-badge" :class="inv.type==='crypto'?'ib-crypto':'ib-stock'" x-text="inv.symbol"></div>
        <div class="inv-info">
          <div class="inv-name">
            <span x-text="inv.symbol"></span>
            <span class="inv-tag" x-text="inv.type==='crypto'?'Crypto':'Cổ phiếu'"></span>
          </div>
          <div class="inv-sub">SL: <b x-text="inv.quantity"></b> · Mua: <span x-text="fmtShort(inv.buy_price)"></span></div>
          <div class="inv-sub" style="opacity:.6">Hiện tại: <span x-text="fmtShort(inv.current_price)"></span></div>
        </div>
        <div class="inv-right">
          <div class="inv-val" x-text="fmtShort(inv.quantity*inv.current_price)"></div>
          <div class="inv-pct" :class="(inv.current_price-inv.buy_price)>=0?'ip-pos':'ip-neg'"
               x-text="((inv.current_price-inv.buy_price)>=0?'▲ +':'▼ ')+(((inv.current_price-inv.buy_price)/inv.buy_price)*100).toFixed(1)+'%'"></div>
          <button class="inv-del" @click="deleteInv(inv.id)">🗑</button>
        </div>
      </div>
    </template>
    <div class="pb"></div>
  </div>

</div><!-- /scroll-area -->

<!-- BOTTOM NAV -->
<nav class="bottom-nav">
  <button class="nav-btn" :class="tab==='home'?'active':''" @click="tab='home'">
    <span class="nav-icon">🏠</span><span class="nav-label">Tổng quan</span>
  </button>
  <button class="nav-btn" :class="tab==='transactions'?'active':''" @click="tab='transactions'">
    <span class="nav-icon">📋</span><span class="nav-label">Giao dịch</span>
  </button>
  <div class="fab-wrap">
    <button class="fab" @click="openAddScreen('expense')">＋</button>
  </div>
  <button class="nav-btn" :class="tab==='debts'?'active':''" @click="tab='debts'">
    <span class="nav-icon">🤝</span><span class="nav-label">Nợ vay</span>
  </button>
  <button class="nav-btn" :class="tab==='investments'?'active':''" @click="tab='investments'">
    <span class="nav-icon">📈</span><span class="nav-label">Đầu tư</span>
  </button>
</nav>

<!-- ════ ADD TRANSACTION SCREEN ════ -->
<div class="screen" :class="showAddScreen?'open':''">
  <div class="add-header">
    <div class="close-btn" @click="showAddScreen=false">✕</div>
    <div class="type-tabs">
      <button class="type-tab" :class="form.type==='expense'?'t-expense':''" @click="form.type='expense'">Chi phí</button>
      <button class="type-tab" :class="form.type==='income'?'t-income':''" @click="form.type='income'">Thu nhập</button>
      <button class="type-tab" :class="form.type==='transfer'?'t-transfer':''" @click="form.type='transfer'">Chuyển ví</button>
    </div>
    <div style="width:34px"></div>
  </div>

  <!-- Amount Display -->
  <div class="amount-hero">
    <div class="amount-cat-preview">
      <div class="amount-cat-icon" :style="'background:'+catColor(form.category)+'22'" x-text="catEmoji(form.category)"></div>
      <span class="amount-cat-name" x-text="form.category||'Chọn danh mục'"></span>
    </div>
    <div class="amount-value"
         :class="numpad===''?'av-zero':form.type==='expense'?'av-expense':form.type==='income'?'av-income':'av-transfer'"
         x-text="numpad===''?'0':numpad.replace(/\B(?=(\d{3})+(?!\d))/g,'.')"></div>
    <div class="amount-currency">₫ VNĐ</div>
  </div>

  <!-- Category Grid -->
  <div class="cat-scroll">
    <template x-if="form.type!=='transfer'">
      <div>
        <div class="cat-section-title" x-text="form.type==='expense'?'Danh mục chi phí':'Danh mục thu nhập'"></div>
        <div class="cat-grid">
          <template x-for="cat in currentCats()" :key="cat.name">
            <div class="cat-item"
                 :class="form.category===cat.name?(form.type==='expense'?'selected-exp':'selected'):''"
                 @click="form.category=cat.name">
              <div class="ci-icon" :style="'background:'+cat.color+'22'">
                <span x-text="cat.emoji"></span>
              </div>
              <span class="ci-label" x-text="cat.name"></span>
            </div>
          </template>
        </div>
      </div>
    </template>
  </div>

  <!-- Details -->
  <div class="tx-details">
    <div class="detail-row">
      <div class="dr-icon">💳</div>
      <select class="dr-select" x-model="form.account_id">
        <option value="">Chọn ví...</option>
        <template x-for="a in accounts" :key="a.id">
          <option :value="a.id" x-text="a.name+' ('+fmtShort(a.balance)+')'"></option>
        </template>
      </select>
    </div>
    <template x-if="form.type==='transfer'">
      <div class="detail-row">
        <div class="dr-icon">➡️</div>
        <select class="dr-select" x-model="form.to_account_id">
          <option value="">Ví đích...</option>
          <template x-for="a in accounts" :key="a.id">
            <option :value="a.id" x-text="a.name"></option>
          </template>
        </select>
      </div>
    </template>
    <div class="detail-row">
      <div class="dr-icon">📅</div>
      <input type="date" class="dr-input" x-model="form.date">
    </div>
    <div class="detail-row" style="border-bottom:none">
      <div class="dr-icon">📝</div>
      <input type="text" class="dr-input" placeholder="Ghi chú..." x-model="form.note">
    </div>
  </div>

  <!-- Numpad -->
  <div class="numpad">
    <template x-for="k in ['7','8','9','⌫','4','5','6','000','1','2','3','OK','.','0','C','']">
      <button class="nk"
              :class="k==='OK'?'nk-ok':k==='⌫'?'nk-del':k==='000'?'nk-zero':k==='.'?'nk-dot':''"
              :style="k===''?'visibility:hidden':''"
              @click="numPress(k)"
              x-text="k==='OK'?'LƯU':k"></button>
    </template>
  </div>
</div>

<!-- ════ SHEETS ════ -->
<!-- Add Account -->
<div class="overlay" x-show="showAccountSheet" @click.self="showAccountSheet=false" style="display:none" x-transition>
  <div class="sheet">
    <div class="sheet-handle"></div>
    <div class="sheet-title">➕ Thêm ví / Tài khoản</div>
    <div class="f-label">Tên ví</div>
    <input class="f-input" placeholder="Techcombank, Tiền mặt..." x-model="aForm.name">
    <div class="f-label">Loại</div>
    <select class="f-select" x-model="aForm.type">
      <option value="cash">👛 Tiền mặt</option>
      <option value="bank">🏦 Ngân hàng</option>
      <option value="e-wallet">📱 Ví điện tử</option>
      <option value="other">📦 Khác</option>
    </select>
    <div class="f-label">Số dư ban đầu (₫)</div>
    <input type="number" class="f-input" placeholder="0" x-model="aForm.balance" style="font-size:22px;font-weight:800;color:var(--green)">
    <button class="btn-primary" @click="saveAccount()">Tạo ví ngay 🎉</button>
  </div>
</div>

<!-- Add Debt -->
<div class="overlay" x-show="showDebtSheet" @click.self="showDebtSheet=false" style="display:none" x-transition>
  <div class="sheet">
    <div class="sheet-handle"></div>
    <div class="sheet-title">📝 Ghi nhận nợ vay</div>
    <div class="f-label">Tên người liên quan</div>
    <input class="f-input" placeholder="Họ tên đầy đủ..." x-model="dForm.partner_name">
    <div class="f-label">Loại</div>
    <select class="f-select" x-model="dForm.type">
      <option value="lend">📤 Tôi cho vay (họ nợ tôi)</option>
      <option value="borrow">📥 Tôi đi vay (tôi nợ họ)</option>
    </select>
    <div class="f-row">
      <div>
        <div class="f-label">Số tiền (₫)</div>
        <input type="number" class="f-input" placeholder="0" x-model="dForm.amount" style="font-weight:700;color:var(--green)">
      </div>
      <div>
        <div class="f-label">Hạn trả</div>
        <input type="date" class="f-input" x-model="dForm.due_date">
      </div>
    </div>
    <div class="f-label">Ghi chú</div>
    <input class="f-input" placeholder="Lý do mượn tiền..." x-model="dForm.note">
    <button class="btn-primary" @click="saveDebt()">Ghi nhận khoản nợ</button>
  </div>
</div>

<!-- Add Investment -->
<div class="overlay" x-show="showInvSheet" @click.self="showInvSheet=false" style="display:none" x-transition>
  <div class="sheet">
    <div class="sheet-handle"></div>
    <div class="sheet-title">📈 Thêm tài sản đầu tư</div>
    <div class="f-label">Loại tài sản</div>
    <select class="f-select" x-model="iForm.type">
      <option value="crypto">🪙 Crypto (BTC, ETH...)</option>
      <option value="stock">📊 Cổ phiếu</option>
    </select>
    <div class="f-label">Mã tài sản</div>
    <input class="f-input" placeholder="BTC, ETH, VNM..." x-model="iForm.symbol" style="text-transform:uppercase;font-weight:700;font-size:18px">
    <div class="f-row">
      <div>
        <div class="f-label">Số lượng</div>
        <input type="number" class="f-input" placeholder="0" x-model="iForm.quantity" step="any">
      </div>
      <div>
        <div class="f-label">Giá mua (₫)</div>
        <input type="number" class="f-input" placeholder="0" x-model="iForm.buy_price">
      </div>
    </div>
    <button class="btn-primary" @click="saveInv()">Thêm vào danh mục 🚀</button>
  </div>
</div>

</div><!-- /#app -->

<script>
const CSRF = '{{ csrf_token() }}';

const CATS_EXPENSE = [
  {name:'Ăn uống',emoji:'🍜',color:'#ff6b6b'},
  {name:'Mua sắm',emoji:'🛒',color:'#ff9f43'},
  {name:'Di chuyển',emoji:'🚗',color:'#54a0ff'},
  {name:'Nhà ở',emoji:'🏠',color:'#5f27cd'},
  {name:'Sức khoẻ',emoji:'💊',color:'#00d2d3'},
  {name:'Giải trí',emoji:'🎮',color:'#c8d6e5'},
  {name:'Giáo dục',emoji:'📚',color:'#786fa6'},
  {name:'Làm đẹp',emoji:'💄',color:'#ff9ff3'},
  {name:'Cà phê',emoji:'☕',color:'#a29bfe'},
  {name:'Điện thoại',emoji:'📱',color:'#6c5ce7'},
  {name:'Du lịch',emoji:'✈️',color:'#00cec9'},
  {name:'Quà tặng',emoji:'🎁',color:'#fdcb6e'},
  {name:'Tiện ích',emoji:'💡',color:'#ffeaa7'},
  {name:'Sửa chữa',emoji:'🔧',color:'#b2bec3'},
  {name:'Thú cưng',emoji:'🐾',color:'#e17055'},
  {name:'Khác',emoji:'📦',color:'#636e72'},
];
const CATS_INCOME = [
  {name:'Lương',emoji:'💰',color:'#00c48c'},
  {name:'Kinh doanh',emoji:'💼',color:'#54a0ff'},
  {name:'Đầu tư',emoji:'📈',color:'#00e5a0'},
  {name:'Thưởng',emoji:'🎯',color:'#fdcb6e'},
  {name:'Quà',emoji:'💝',color:'#ff9ff3'},
  {name:'Hoàn tiền',emoji:'🔄',color:'#a29bfe'},
  {name:'Lãi suất',emoji:'🏦',color:'#1de9b6'},
  {name:'Khác',emoji:'📦',color:'#636e72'},
];
const ALL_CATS = [...CATS_EXPENSE,...CATS_INCOME];
const catOf = n => ALL_CATS.find(c=>c.name===n)||{emoji:'💸',color:'#636e72'};

function app(){return{
  loading:false,isOnline:navigator.onLine,
  tab:'home',
  curYear:new Date().getFullYear(),curMonth:new Date().getMonth()+1,
  userName:'{{ Auth::user()->name ?? "Bạn" }}',
  accounts:[],transactions:[],debts:[],investments:[],
  overview:{net_worth:0,total_cash:0,total_investment:0,total_lend:0,total_borrow:0,investment_pnl:0,investment_pnl_percent:0},
  showAddScreen:false,showAccountSheet:false,showDebtSheet:false,showInvSheet:false,
  numpad:'',
  form:{type:'expense',account_id:'',to_account_id:'',category:'',date:new Date().toISOString().split('T')[0],note:''},
  aForm:{name:'',type:'cash',balance:''},
  dForm:{partner_name:'',type:'lend',amount:'',due_date:'',note:''},
  iForm:{symbol:'',type:'crypto',quantity:'',buy_price:''},
  chart:null,
  toast:{show:false,msg:'',type:'success'},

  get monthLabel(){return`Tháng ${this.curMonth}/${this.curYear}`},

  get monthStats(){
    const f=this.transactions.filter(t=>{const d=new Date(t.transaction_date);return d.getMonth()+1===this.curMonth&&d.getFullYear()===this.curYear});
    return{income:f.filter(t=>t.type==='income').reduce((s,t)=>s+parseFloat(t.amount),0),
           expense:f.filter(t=>t.type==='expense').reduce((s,t)=>s+parseFloat(t.amount),0)};
  },

  get filtered(){
    return this.transactions.filter(t=>{const d=new Date(t.transaction_date);return d.getMonth()+1===this.curMonth&&d.getFullYear()===this.curYear});
  },

  get grouped(){
    const G={};const td=new Date().toISOString().split('T')[0];const yd=new Date(Date.now()-86400000).toISOString().split('T')[0];
    this.filtered.forEach(t=>{const k=t.transaction_date.split('T')[0];(G[k]=G[k]||[]).push(t)});
    return Object.entries(G).sort(([a],[b])=>b.localeCompare(a)).map(([date,items])=>{
      const net=items.reduce((s,t)=>t.type==='income'?s+parseFloat(t.amount):t.type==='expense'?s-parseFloat(t.amount):s,0);
      const d=new Date(date+'T00:00:00');
      const label=date===td?'Hôm nay':date===yd?'Hôm qua':d.toLocaleDateString('vi-VN',{weekday:'short',day:'numeric',month:'numeric'});
      return{date,label,items,net};
    });
  },

  currentCats(){return this.form.type==='income'?CATS_INCOME:CATS_EXPENSE},
  catEmoji(n){return catOf(n).emoji},
  catColor(n){return catOf(n).color},

  async init(){
    window.addEventListener('online',()=>this.isOnline=true);
    window.addEventListener('offline',()=>this.isOnline=false);
    if('serviceWorker'in navigator)navigator.serviceWorker.register('/sw.js').catch(()=>{});
    await this.load();
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
      this.accounts=a.accounts||a||[];
      this.transactions=t.transactions||t||[];
      this.debts=d.debts||d||[];
      this.investments=i.investments||i||[];
      this.overview=o;
      this.$nextTick(()=>this.drawChart());
    }catch(e){this.notify('Lỗi tải dữ liệu','error');}
    finally{this.loading=false}
  },

  changeMonth(d){
    this.curMonth+=d;
    if(this.curMonth>12){this.curMonth=1;this.curYear++}
    if(this.curMonth<1){this.curMonth=12;this.curYear--}
    this.$nextTick(()=>this.drawChart());
  },

  drawChart(){
    const c=document.getElementById('weekChart');if(!c)return;
    if(this.chart)this.chart.destroy();
    const days=[],inc=[],exp=[];
    for(let i=6;i>=0;i--){
      const d=new Date(Date.now()-i*86400000);
      const k=d.toISOString().split('T')[0];
      const txs=this.transactions.filter(t=>t.transaction_date&&t.transaction_date.startsWith(k));
      days.push(d.toLocaleDateString('vi-VN',{weekday:'short'}));
      inc.push(txs.filter(t=>t.type==='income').reduce((s,t)=>s+parseFloat(t.amount),0)/1e6);
      exp.push(txs.filter(t=>t.type==='expense').reduce((s,t)=>s+parseFloat(t.amount),0)/1e6);
    }
    this.chart=new Chart(c,{type:'bar',data:{labels:days,datasets:[
      {label:'Thu',data:inc,backgroundColor:'rgba(0,196,140,.75)',borderRadius:6,borderSkipped:false,barPercentage:.55},
      {label:'Chi',data:exp,backgroundColor:'rgba(255,77,109,.75)',borderRadius:6,borderSkipped:false,barPercentage:.55},
    ]},options:{responsive:true,maintainAspectRatio:false,
      plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>(ctx.dataset.label+': '+(ctx.parsed.y).toFixed(1)+'M ₫')}}},
      scales:{x:{grid:{display:false},ticks:{color:'#6e7681',font:{size:10,family:'Inter'}}},
              y:{grid:{color:'rgba(255,255,255,.04)'},ticks:{color:'#6e7681',font:{size:10,family:'Inter'},callback:v=>v+'M'}}}}});
  },

  openAddScreen(type){
    this.form={type,account_id:this.accounts[0]?.id||'',to_account_id:'',category:'',date:new Date().toISOString().split('T')[0],note:''};
    this.numpad='';this.showAddScreen=true;
  },

  numPress(k){
    if(k==='⌫'){this.numpad=this.numpad.slice(0,-1)}
    else if(k==='C'){this.numpad=''}
    else if(k==='OK'){this.saveTx()}
    else if(k==='.'){if(!this.numpad.includes('.'))this.numpad+='.'}
    else if(k==='000'){if(this.numpad)this.numpad+='000'}
    else{if(this.numpad.length<12)this.numpad+=k}
  },

  numFmt(v){return new Intl.NumberFormat('vi-VN',{maximumFractionDigits:0}).format(parseFloat(v)||0)},
  fmtShort(v){
    const n=parseFloat(v)||0,a=Math.abs(n),s=n<0?'-':'';
    if(a>=1e9)return s+(a/1e9).toFixed(1).replace(/\.0$/,'')+'B';
    if(a>=1e6)return s+(a/1e6).toFixed(1).replace(/\.0$/,'')+'M';
    if(a>=1e3)return s+(a/1e3).toFixed(0)+'K';
    return s+a.toFixed(0)+'₫';
  },
  fmtDate(d){if(!d)return'';return new Date(d+'T00:00:00').toLocaleDateString('vi-VN',{day:'2-digit',month:'2-digit',year:'numeric'})},

  notify(msg,type='success'){
    this.toast={show:true,msg,type};
    setTimeout(()=>this.toast.show=false,2800);
  },

  async api(url,opts={}){
    const r=await fetch(url,{headers:{'Content-Type':'application/json','X-XSRF-TOKEN':decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]||CSRF)},...opts});
    return r.json();
  },

  async saveTx(){
    const amt=parseFloat(this.numpad);
    if(!amt||amt<=0){this.notify('Nhập số tiền hợp lệ','error');return}
    if(!this.form.account_id){this.notify('Vui lòng chọn ví','error');return}
    if(!this.form.category&&this.form.type!=='transfer'){this.notify('Vui lòng chọn danh mục','error');return}
    this.loading=true;
    const payload={...this.form,amount:amt};
    if(this.form.type==='transfer')payload.category='Chuyển ví';
    try{
      const d=await this.api('/api/finance/transactions',{method:'POST',body:JSON.stringify(payload)});
      if(d.success){this.showAddScreen=false;this.notify('✅ Ghi chép thành công!');await this.load()}
      else this.notify(d.message||'Lỗi!','error');
    }catch{this.notify('Lỗi kết nối','error')}
    finally{this.loading=false}
  },

  async delTx(tx){
    if(!confirm(`Xoá "${tx.category}" — ${this.fmtShort(tx.amount)}?`))return;
    this.loading=true;
    try{
      const d=await this.api('/api/finance/transactions/'+tx.id,{method:'DELETE'});
      if(d.success){this.notify('🗑 Đã xoá giao dịch');await this.load()}
      else this.notify(d.message,'error');
    }catch{this.notify('Lỗi kết nối','error')}
    finally{this.loading=false}
  },

  async saveAccount(){
    if(!this.aForm.name){this.notify('Nhập tên ví','error');return}
    this.loading=true;
    try{
      const d=await this.api('/api/finance/accounts',{method:'POST',body:JSON.stringify({...this.aForm,balance:parseFloat(this.aForm.balance)||0})});
      if(d.success){this.showAccountSheet=false;this.aForm={name:'',type:'cash',balance:''};this.notify('✅ Tạo ví thành công!');await this.load()}
      else this.notify(d.message,'error');
    }catch{this.notify('Lỗi kết nối','error')}
    finally{this.loading=false}
  },

  async saveDebt(){
    if(!this.dForm.partner_name||!this.dForm.amount){this.notify('Điền đầy đủ thông tin','error');return}
    this.loading=true;
    try{
      const d=await this.api('/api/finance/debts',{method:'POST',body:JSON.stringify({...this.dForm,amount:parseFloat(this.dForm.amount)})});
      if(d.success){this.showDebtSheet=false;this.dForm={partner_name:'',type:'lend',amount:'',due_date:'',note:''};this.notify('✅ Ghi nhận thành công!');await this.load()}
      else this.notify(d.message,'error');
    }catch{this.notify('Lỗi kết nối','error')}
    finally{this.loading=false}
  },

  async toggleDebt(id){
    try{const d=await this.api('/api/finance/debts/'+id+'/toggle',{method:'PATCH'});if(d.success){this.notify('✅ Đã cập nhật');await this.load()}}
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
    if(!this.iForm.symbol||!this.iForm.quantity||!this.iForm.buy_price){this.notify('Điền đầy đủ thông tin','error');return}
    this.loading=true;
    try{
      const d=await this.api('/api/finance/investments',{method:'POST',body:JSON.stringify({...this.iForm,symbol:this.iForm.symbol.toUpperCase(),quantity:parseFloat(this.iForm.quantity),buy_price:parseFloat(this.iForm.buy_price)})});
      if(d.success){this.showInvSheet=false;this.iForm={symbol:'',type:'crypto',quantity:'',buy_price:''};this.notify('✅ Thêm thành công!');await this.load()}
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
    try{
      const d=await this.api('/api/finance/rates/update',{method:'POST'});
      this.notify(d.message||'✅ Cập nhật tỷ giá!');await this.load();
    }catch{this.notify('Lỗi cập nhật','error')}
    finally{this.loading=false}
  },
}}
</script>
</body>
</html>
