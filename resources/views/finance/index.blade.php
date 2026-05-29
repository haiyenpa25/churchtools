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
/* ── RESET ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --g:#00c48c;--gl:#00e5a8;--gd:#009e71;--gg:rgba(0,196,140,.25);
  --r:#ff4d6d;--rl:#ff6b85;--rd:#e63356;--rg:rgba(255,77,109,.25);
  --b:#4c9aff;--p:#7b5cfa;--o:#ff9f43;--y:#ffd32a;--t:#1de9b6;
  --bg:#0d1117;--bg2:#161b22;--bg3:#21262d;--bg4:#2d333b;
  --tx:#e6edf3;--tx2:#8b949e;--tx3:#6e7681;
  --br:rgba(255,255,255,.08);--br2:rgba(255,255,255,.13);
  --safe-top:env(safe-area-inset-top,0px);
  --safe-bot:env(safe-area-inset-bottom,0px);
}
html{height:100%;height:-webkit-fill-available}
body{height:100%;height:-webkit-fill-available;overflow:hidden;background:var(--bg);font-family:'Inter',sans-serif;color:var(--tx);-webkit-tap-highlight-color:transparent;display:flex;justify-content:center}
input,select,button,textarea{font-family:'Inter',sans-serif;-webkit-tap-highlight-color:transparent}
::-webkit-scrollbar{width:0;height:0}

/* ── APP SHELL ── */
#app{
  position:fixed;top:0;left:0;right:0;bottom:0;
  width:100%;max-width:430px;margin:0 auto;
  display:flex;flex-direction:column;
  background:var(--bg);overflow:hidden;
}

/* ── TOPBAR ── */
.topbar{
  background:var(--bg2);border-bottom:1px solid var(--br);flex-shrink:0;
  padding:10px 16px 10px;padding-top:calc(10px + var(--safe-top));
  display:flex;align-items:center;justify-content:space-between;
}
.ta-left{display:flex;align-items:center;gap:10px}
.ta-ava{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--g),var(--b));display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;box-shadow:0 0 0 2px var(--gg);flex-shrink:0}
.ta-info span{display:block}.ta-greet{font-size:10px;color:var(--tx2)}.ta-name{font-size:13px;font-weight:700}
.ta-right{display:flex;align-items:center;gap:6px}
.ti-btn{width:32px;height:32px;border-radius:10px;background:var(--bg3);border:1px solid var(--br);color:var(--tx2);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;text-decoration:none;transition:all .18s}
.ti-btn:active{transform:scale(.88)}

/* ── CURRENCY BAR ── */
.currency-bar{background:var(--bg2);border-bottom:1px solid var(--br);padding:5px 16px;display:flex;gap:10px;overflow-x:auto;align-items:center;flex-shrink:0}
.currency-bar::-webkit-scrollbar{display:none}
.cur-item{display:flex;align-items:center;gap:4px;white-space:nowrap;flex-shrink:0}
.cur-flag{font-size:11px}.cur-code{font-size:10px;font-weight:700;color:var(--tx2)}.cur-val{font-size:11px;font-weight:700;color:var(--g)}
.cur-sep{width:1px;height:12px;background:var(--br);flex-shrink:0}.cur-time{font-size:9px;color:var(--tx3);flex-shrink:0}

/* ── MONTH NAV ── */
.month-nav{background:var(--bg2);padding:6px 16px;display:flex;align-items:center;justify-content:center;gap:14px;flex-shrink:0;border-bottom:1px solid var(--br)}
.mn-btn{width:28px;height:28px;border-radius:8px;background:var(--bg3);border:1px solid var(--br);color:var(--tx2);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:16px;transition:all .18s;line-height:1}
.mn-btn:active{background:var(--g);color:#fff;transform:scale(.9)}
.mn-label{font-size:13px;font-weight:700;min-width:100px;text-align:center}

/* ── SEARCH BAR ── */
.search-bar{padding:8px 16px;background:var(--bg2);border-bottom:1px solid var(--br);display:flex;align-items:center;gap:8px;flex-shrink:0}
.sb-wrap{position:relative;flex:1;display:flex;align-items:center}
.sb-icon{position:absolute;left:11px;font-size:13px;color:var(--tx3);pointer-events:none;z-index:1}
.sb-input{width:100%;background:var(--bg3);border:1.5px solid var(--br);border-radius:12px;padding:8px 12px 8px 32px;color:var(--tx);font-size:13px;outline:none;transition:border-color .2s}
.sb-input:focus{border-color:var(--g)}
.filter-tabs{display:flex;gap:4px;flex-shrink:0}
.filter-tab{padding:6px 9px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;background:var(--bg3);border:1px solid var(--br);color:var(--tx3);transition:all .18s;white-space:nowrap}
.filter-tab.active{background:rgba(0,196,140,.15);border-color:rgba(0,196,140,.3);color:var(--g)}

/* ── SCROLL AREA ── */
.scroll-area{flex:1;overflow-y:auto;overflow-x:hidden;-webkit-overflow-scrolling:touch;min-height:0}

/* ── HERO ── */
.hero{margin:14px 16px 0;border-radius:22px;overflow:hidden;position:relative;background:linear-gradient(135deg,#0d2137,#0a1628 40%,#0d1f35 70%,#091829);padding:20px;border:1px solid rgba(0,196,140,.15)}
.hero::before{content:'';position:absolute;top:-40px;right:-30px;width:180px;height:180px;border-radius:50%;background:radial-gradient(circle,rgba(0,196,140,.15),transparent 70%);pointer-events:none}
.hero-lbl{font-size:10px;font-weight:700;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;display:flex;align-items:center;gap:6px}
.hero-lbl::before{content:'';width:4px;height:4px;border-radius:50%;background:var(--g);display:inline-block}
.hero-bal{font-size:28px;font-weight:900;color:#fff;letter-spacing:-1px;line-height:1;margin-bottom:3px}
.hero-bal span{font-size:14px;font-weight:600;opacity:.65;margin-right:3px}
.hero-cur{font-size:10px;color:rgba(255,255,255,.35);margin-bottom:14px}
.hero-stats{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.hs{border-radius:13px;padding:10px 12px;display:flex;align-items:center;gap:9px}
.hs-i{background:rgba(0,196,140,.12);border:1px solid rgba(0,196,140,.2)}.hs-e{background:rgba(255,77,109,.12);border:1px solid rgba(255,77,109,.2)}
.hs-ico{width:30px;height:30px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0}
.hs-i .hs-ico{background:rgba(0,196,140,.2)}.hs-e .hs-ico{background:rgba(255,77,109,.2)}
.hs-info{min-width:0}
.hs-label{font-size:9px;font-weight:700;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.06em}
.hs-val{font-size:13px;font-weight:800;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.hs-i .hs-val{color:var(--g)}.hs-e .hs-val{color:var(--r)}

/* ── SECTION HEADER ── */
.sec-hdr{padding:12px 16px 7px;display:flex;align-items:center;justify-content:space-between}
.sec-lbl{font-size:11px;font-weight:700;color:var(--tx2);text-transform:uppercase;letter-spacing:.07em}
.sec-act{font-size:12px;font-weight:600;color:var(--g);cursor:pointer}

/* ── WALLETS ── */
.wallet-scroll{display:flex;gap:10px;overflow-x:auto;padding:0 16px 6px;scroll-snap-type:x mandatory}
.wallet-scroll::-webkit-scrollbar{display:none}
.wcard{flex-shrink:0;width:160px;height:90px;border-radius:18px;padding:12px 14px;scroll-snap-align:start;cursor:pointer;display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden;transition:transform .18s}
.wcard:active{transform:scale(.95)}
.wcard::after{content:'';position:absolute;top:-18px;right:-18px;width:70px;height:70px;border-radius:50%;background:rgba(255,255,255,.08);pointer-events:none}
.wc0{background:linear-gradient(135deg,#00c48c,#009e71)}.wc1{background:linear-gradient(135deg,#4c9aff,#2979ff)}.wc2{background:linear-gradient(135deg,#7b5cfa,#5b3ff5)}.wc3{background:linear-gradient(135deg,#ff9f43,#e67e22)}.wc4{background:linear-gradient(135deg,#ff4d6d,#c9184a)}.wc5{background:linear-gradient(135deg,#1de9b6,#00bfa5)}
.wcard-top{display:flex;align-items:center;justify-content:space-between;position:relative;z-index:1}
.wcard-ico{font-size:14px;opacity:.85}
.wcard-actions{display:flex;gap:4px}
.wcard-action-btn{background:rgba(0,0,0,.25);border:none;border-radius:6px;width:22px;height:22px;display:flex;align-items:center;justify-content:center;font-size:11px;cursor:pointer;color:rgba(255,255,255,.9);transition:all .18s;-webkit-tap-highlight-color:transparent}
.wcard-action-btn:active{transform:scale(.85);background:rgba(0,0,0,.5)}
.wcard-name{font-size:11px;font-weight:600;color:rgba(255,255,255,.85);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-top:2px}
.wcard-bal{font-size:15px;font-weight:800;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.wcard-type{font-size:9px;font-weight:600;color:rgba(255,255,255,.5);margin-top:1px}
.wadd{flex-shrink:0;width:80px;height:90px;border-radius:18px;border:2px dashed rgba(255,255,255,.1);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;cursor:pointer;color:var(--tx3);transition:all .18s;scroll-snap-align:start}
.wadd:active{border-color:var(--g);color:var(--g)}
/* Account Manager Screen */
.acc-item{display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--br)}
.acc-item:last-child{border-bottom:none}
.acc-ico{width:42px;height:42px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.acc-info{flex:1;min-width:0}
.acc-name{font-size:14px;font-weight:700;color:var(--tx)}
.acc-type{font-size:10px;color:var(--tx3);margin-top:2px}
.acc-bal{font-size:15px;font-weight:800;color:var(--g);flex-shrink:0;text-align:right}
.acc-actions{display:flex;gap:6px;flex-shrink:0}
.acc-act-btn{background:var(--bg3);border:1px solid var(--br);border-radius:9px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;font-size:13px;cursor:pointer;color:var(--tx3);transition:all .18s}
.acc-act-btn:active{transform:scale(.88)}
.acc-act-btn.del:active{background:rgba(255,77,109,.15);color:var(--r)}
.acc-act-btn.edit:active{background:rgba(76,154,255,.15);color:var(--b)}

/* ── CHART ── */
.chart-wrap{margin:6px 16px 0;background:var(--bg2);border:1px solid var(--br);border-radius:18px;padding:12px 14px}
.chart-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.chart-title{font-size:10px;font-weight:700;color:var(--tx2);text-transform:uppercase;letter-spacing:.06em}
.chart-leg{display:flex;gap:10px}
.cl-item{display:flex;align-items:center;gap:4px;font-size:10px;font-weight:600;color:var(--tx3)}
.cl-dot{width:7px;height:7px;border-radius:2px}

/* ── FIXED MONTHLY ── */
.fixed-card{margin:8px 16px 0;background:linear-gradient(135deg,rgba(123,92,250,.1),rgba(76,154,255,.06));border:1px solid rgba(123,92,250,.2);border-radius:18px;padding:14px}
.fc-summary{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:12px}
.fcs{border-radius:10px;padding:8px;background:rgba(0,0,0,.2)}
.fcs-label{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px}
.fcs-val{font-size:13px;font-weight:800}
.fcs-inc .fcs-label{color:var(--g)}.fcs-inc .fcs-val{color:var(--g)}
.fcs-exp .fcs-label{color:var(--r)}.fcs-exp .fcs-val{color:var(--r)}
.fcs-net .fcs-label{color:var(--b)}.fcs-net .fcs-val{color:var(--b)}
.fixed-item{display:flex;align-items:center;gap:9px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05)}
.fixed-item:last-child{border-bottom:none}
.fi-day{width:22px;height:22px;border-radius:6px;background:var(--bg3);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;color:var(--tx3);flex-shrink:0}
.fi-ico{width:30px;height:30px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0}
.fi-info{flex:1;min-width:0}
.fi-name{font-size:12px;font-weight:600;color:var(--tx);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.fi-sub{font-size:10px;color:var(--tx3)}
.fi-amt{font-size:13px;font-weight:800;flex-shrink:0}
.btn-apply{width:100%;background:linear-gradient(135deg,var(--p),var(--b));color:#fff;font-size:12px;font-weight:700;padding:11px;border-radius:12px;border:none;cursor:pointer;font-family:inherit;margin-top:10px;display:flex;align-items:center;justify-content:center;gap:6px;box-shadow:0 4px 16px rgba(123,92,250,.3);transition:all .2s}
.btn-apply:active{transform:scale(.98)}

/* ── BUDGET ── */
.budget-strip{margin:8px 16px 0}
.budget-alert{background:rgba(255,77,109,.08);border:1px solid rgba(255,77,109,.2);border-radius:13px;padding:10px 12px;display:flex;align-items:center;gap:9px;margin-bottom:6px}
.ba-icon{font-size:16px;flex-shrink:0}.ba-info{flex:1;min-width:0}
.ba-title{font-size:12px;font-weight:700;color:var(--r)}.ba-sub{font-size:10px;color:var(--tx2);margin-top:1px}
.ba-action{font-size:11px;font-weight:700;color:var(--g);cursor:pointer;white-space:nowrap;flex-shrink:0}
.budget-item{background:var(--bg2);border:1px solid var(--br);border-radius:14px;padding:11px 13px;margin-bottom:7px}
.bi-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:7px}
.bi-cat{display:flex;align-items:center;gap:7px}
.bi-cat-ico{width:28px;height:28px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:14px}
.bi-cat-name{font-size:12px;font-weight:600;color:var(--tx)}
.bi-amounts{text-align:right}
.bi-used{font-size:12px;font-weight:700;color:var(--tx)}.bi-limit{font-size:10px;color:var(--tx2)}
.bi-bar{height:5px;background:var(--bg4);border-radius:3px;overflow:hidden}
.bi-bar-fill{height:100%;border-radius:3px;transition:width .5s cubic-bezier(.16,1,.3,1)}
.bi-bar-ok{background:linear-gradient(90deg,var(--g),var(--gl))}.bi-bar-warn{background:linear-gradient(90deg,var(--o),var(--y))}.bi-bar-over{background:linear-gradient(90deg,var(--r),var(--rl))}

/* ── TX LIST ── */
.tx-grp-wrap{margin:0 16px;padding-top:8px}
.tx-dh{display:flex;align-items:center;justify-content:space-between;padding:4px 0 6px;border-bottom:1px solid var(--br);margin-bottom:2px}
.tx-dl{font-size:11px;font-weight:700;color:var(--tx2)}.tx-dl.today{color:var(--g)}.tx-dl.yest{color:var(--y)}
.tx-dn{font-size:11px;font-weight:700;color:var(--tx3)}
.tx-row{display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.03)}
.tx-row:last-child{border-bottom:none}
.tx-ico{width:40px;height:40px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0}
.tx-body{flex:1;min-width:0;cursor:pointer}
.tx-cat{font-size:13px;font-weight:600;color:var(--tx);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tx-meta{display:flex;align-items:center;gap:5px;margin-top:2px;flex-wrap:wrap}
.tx-acc-tag{font-size:10px;font-weight:600;color:var(--tx3);background:var(--bg3);border-radius:5px;padding:1px 5px}
.tx-note{font-size:10px;color:var(--tx3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:90px}
.tx-recur{font-size:10px;color:var(--b);background:rgba(76,154,255,.1);border-radius:5px;padding:1px 5px}
.tx-amount{font-size:13px;font-weight:800;flex-shrink:0;text-align:right}
.tx-amount.income{color:var(--g)}.tx-amount.expense{color:var(--r)}.tx-amount.transfer{color:var(--b)}
.tx-actions{display:flex;flex-direction:column;gap:2px;flex-shrink:0}
.tx-act-btn{background:none;border:none;cursor:pointer;font-size:13px;padding:2px;color:var(--tx3);line-height:1}

/* ── CALENDAR VIEW ── */
.cal-wrap{background:var(--bg2);border-radius:16px;margin:10px 16px 6px;overflow:hidden;border:1px solid var(--br)}
.cal-hdr{display:flex;align-items:center;justify-content:space-between;padding:11px 14px 8px}
.cal-title{font-size:14px;font-weight:800;color:var(--tx)}
.cal-nav{display:flex;align-items:center;gap:3px}
.cal-nav-btn{background:var(--bg3);border:none;border-radius:8px;width:30px;height:30px;cursor:pointer;color:var(--tx2);font-size:14px;display:flex;align-items:center;justify-content:center;transition:all .18s}
.cal-nav-btn:active{transform:scale(.88)}
.cal-dow{display:grid;grid-template-columns:repeat(7,1fr);padding:0 10px;margin-bottom:3px}
.cal-dow-lbl{text-align:center;font-size:9px;font-weight:800;color:var(--tx3);padding:3px 0;text-transform:uppercase}
.cal-dow-lbl.sun{color:#ff6b6b33}.cal-dow-lbl.sat{color:#a29bfe55}
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;padding:0 10px 10px}
.cal-day{
  aspect-ratio:1;border-radius:10px;display:flex;flex-direction:column;
  align-items:center;justify-content:center;cursor:pointer;
  transition:all .18s;position:relative;padding:2px;
}
.cal-day:active{transform:scale(.88)}
.cal-day.empty{cursor:default}
.cal-day.today .cdn{background:var(--g);color:#fff;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center}
.cal-day.selected{background:rgba(76,154,255,.15);border:1.5px solid rgba(76,154,255,.4)}
.cal-day.has-tx{background:var(--bg3)}
.cal-day.other-month .cdn{opacity:.25}
.cdn{font-size:11px;font-weight:700;color:var(--tx2);line-height:1}
.cal-dots{display:flex;gap:2px;margin-top:2px;align-items:center;justify-content:center;height:6px}
.cal-dot{width:4px;height:4px;border-radius:50%;flex-shrink:0}
.cal-dot.di{background:var(--g)}
.cal-dot.de{background:var(--r)}
/* Day summary popup */
.cal-day-popup{margin:0 16px 10px;background:var(--bg2);border:1px solid var(--br);border-radius:14px;padding:11px 14px;animation:fadeSlide .2s ease}
@keyframes fadeSlide{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.cdp-date{font-size:11px;font-weight:800;color:var(--tx3);margin-bottom:8px;text-transform:uppercase;letter-spacing:.06em}
.cdp-stats{display:flex;gap:8px;margin-bottom:8px}
.cdp-stat{flex:1;border-radius:10px;padding:7px 9px}
.cdp-stat.inc{background:rgba(0,196,140,.1)}.cdp-stat.exp{background:rgba(255,77,109,.1)}
.cdp-stat-lbl{font-size:8px;font-weight:800;color:var(--tx3);text-transform:uppercase}
.cdp-stat-val{font-size:13px;font-weight:800;margin-top:1px}
.cdp-stat.inc .cdp-stat-val{color:var(--g)}.cdp-stat.exp .cdp-stat-val{color:var(--r)}
/* View toggle */
.view-toggle{display:flex;background:var(--bg3);border-radius:10px;padding:3px;gap:2px}
.vt-btn{flex:1;text-align:center;padding:6px 4px;border-radius:7px;font-size:10px;font-weight:700;cursor:pointer;transition:all .18s;border:none;background:transparent;color:var(--tx3);font-family:inherit}
.vt-btn.active{background:var(--bg2);color:var(--tx);box-shadow:0 1px 4px rgba(0,0,0,.25)}

/* ── STATS ── */
.stats-summary{display:grid;grid-template-columns:1fr 1fr 1fr;gap:7px;padding:12px 16px 0}
.ss-card{background:var(--bg2);border:1px solid var(--br);border-radius:13px;padding:9px}
.ss-label{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px}
.ss-val{font-size:13px;font-weight:800}
.ss-income .ss-label{color:var(--g)}.ss-income .ss-val{color:var(--g)}
.ss-expense .ss-label{color:var(--r)}.ss-expense .ss-val{color:var(--r)}
.ss-rate .ss-label{color:var(--b)}.ss-rate .ss-val{color:var(--b)}
.cat-rank-item{display:flex;align-items:center;gap:9px;padding:9px 0;border-bottom:1px solid rgba(255,255,255,.04)}
.cat-rank-item:last-child{border-bottom:none}
.cr-rank{font-size:11px;font-weight:800;color:var(--tx3);width:14px;text-align:center;flex-shrink:0}
.cr-ico{width:34px;height:34px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.cr-info{flex:1;min-width:0}
.cr-name{font-size:12px;font-weight:600;color:var(--tx)}
.cr-bar-wrap{height:3px;background:var(--bg4);border-radius:2px;margin-top:4px;overflow:hidden}
.cr-bar{height:100%;border-radius:2px;transition:width .6s cubic-bezier(.16,1,.3,1)}
.cr-amount{font-size:12px;font-weight:800;flex-shrink:0}.cr-pct{font-size:10px;color:var(--tx3);text-align:right;margin-top:2px}

/* ── GOALS ── */
.goal-item{background:var(--bg2);border:1px solid var(--br);border-radius:16px;padding:13px 15px;margin:7px 16px 0}
.gi-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:9px}
.gi-name{font-size:13px;font-weight:700;color:var(--tx);display:flex;align-items:center;gap:7px}
.gi-pct{font-size:12px;font-weight:800;color:var(--g)}
.gi-amounts{display:flex;justify-content:space-between;margin-bottom:7px;font-size:11px;color:var(--tx2)}
.gi-bar{height:7px;background:var(--bg4);border-radius:4px;overflow:hidden}
.gi-fill{height:100%;border-radius:4px;background:linear-gradient(90deg,var(--g),var(--gl));transition:width .6s cubic-bezier(.16,1,.3,1)}
.gi-deadline{font-size:10px;color:var(--tx3);margin-top:4px}

/* ── DEBTS ── */
.debt-banner{display:grid;grid-template-columns:1fr 1fr;gap:9px;padding:12px 16px 0}
.dbc{border-radius:16px;padding:14px}
.dbc-lend{background:linear-gradient(135deg,rgba(0,196,140,.15),rgba(0,196,140,.05));border:1px solid rgba(0,196,140,.2)}
.dbc-borrow{background:linear-gradient(135deg,rgba(255,77,109,.15),rgba(255,77,109,.05));border:1px solid rgba(255,77,109,.2)}
.dbc-ico{font-size:18px;margin-bottom:5px}
.dbc-lbl{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:2px}
.dbc-lend .dbc-lbl{color:var(--g)}.dbc-borrow .dbc-lbl{color:var(--r)}
.dbc-amt{font-size:16px;font-weight:800;color:var(--tx)}.dbc-cnt{font-size:10px;color:var(--tx3);margin-top:1px}
.toolbar{display:flex;align-items:center;justify-content:space-between;padding:11px 16px 5px}
.tbar-lbl{font-size:11px;font-weight:700;color:var(--tx2);text-transform:uppercase;letter-spacing:.07em}
.btn-sm{background:linear-gradient(135deg,var(--gl),var(--g));color:#fff;border:none;border-radius:11px;padding:8px 13px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:0 2px 10px var(--gg);transition:all .18s;display:flex;align-items:center;gap:5px}
.btn-sm:active{transform:scale(.95)}
.debt-item{margin:0 16px 7px;background:var(--bg2);border:1px solid var(--br);border-radius:16px;padding:12px 14px;display:flex;align-items:center;gap:10px}
.d-ava{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0}
.d-lend{background:linear-gradient(135deg,var(--g),var(--gd))}.d-borrow{background:linear-gradient(135deg,var(--r),var(--rd))}
.d-info{flex:1;min-width:0}.d-name{font-size:13px;font-weight:700;color:var(--tx)}.d-sub{font-size:10px;color:var(--tx3);margin-top:2px}
.d-right{text-align:right;flex-shrink:0}.d-amount{font-size:13px;font-weight:800}
.d-lend-c{color:var(--g)}.d-borrow-c{color:var(--r)}
.d-badge{font-size:10px;font-weight:700;padding:3px 7px;border-radius:7px;display:inline-block;margin-top:3px;cursor:pointer;transition:all .18s}
.db-paid{background:var(--bg3);color:var(--tx3)}.db-unpaid{background:rgba(0,196,140,.15);color:var(--g)}
.d-del{background:none;border:none;color:var(--tx3);cursor:pointer;font-size:13px;margin-top:2px;display:block;padding:2px}

/* ── SETTINGS ── */
.set-section{margin:12px 16px 0;background:var(--bg2);border:1px solid var(--br);border-radius:16px;overflow:hidden}
.set-item{display:flex;align-items:center;gap:11px;padding:13px 15px;cursor:pointer;border-bottom:1px solid var(--br);transition:background .18s}
.set-item:last-child{border-bottom:none}
.set-item:active{background:var(--bg3)}
.si-ico{width:36px;height:36px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.si-info{flex:1;min-width:0}.si-title{font-size:13px;font-weight:600;color:var(--tx)}.si-sub{font-size:10px;color:var(--tx3);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.si-arrow{font-size:14px;color:var(--tx3)}
.set-version{text-align:center;padding:14px;font-size:11px;color:var(--tx3)}

/* ── BOTTOM NAV ── */
.bnav{
  flex-shrink:0;background:var(--bg2);border-top:1px solid var(--br);
  display:grid;grid-template-columns:1fr 1fr 58px 1fr 1fr;align-items:center;
  padding:4px 0;padding-bottom:calc(4px + var(--safe-bot));
}
.ni{display:flex;flex-direction:column;align-items:center;gap:2px;padding:5px 3px;cursor:pointer;background:none;border:none;color:var(--tx3);font-family:inherit;transition:all .2s;border-radius:10px}
.ni:active{transform:scale(.88)}.ni.on{color:var(--g)}
.ni-ico{font-size:19px;line-height:1}.ni-lbl{font-size:9px;font-weight:600}
.fab-wrap{display:flex;align-items:center;justify-content:center}
.fab{width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,var(--gl),var(--g));border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#fff;font-size:24px;font-weight:300;box-shadow:0 4px 18px var(--gg),0 2px 8px rgba(0,0,0,.4);margin-top:-14px;transition:all .2s;font-family:inherit;line-height:1}
.fab:active{transform:scale(.9)}

/* ── SCREENS (slide-up panels) ── */
.screen{
  position:fixed;top:0;left:0;right:0;bottom:0;
  z-index:50;max-width:430px;margin:0 auto;
  display:flex;flex-direction:column;
  background:var(--bg);
  transform:translateY(100%);
  transition:transform .36s cubic-bezier(.16,1,.3,1);
  will-change:transform;
}
.screen.open{transform:translateY(0)}

/* ── SCREEN HEADER ── */
.scr-hdr{
  background:var(--bg2);border-bottom:1px solid var(--br);flex-shrink:0;
  padding:10px 16px;padding-top:calc(10px + var(--safe-top));
  display:flex;align-items:center;gap:10px;
}
.close-btn{width:32px;height:32px;border-radius:10px;background:var(--bg3);border:1px solid var(--br);color:var(--tx2);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:16px;transition:all .18s;flex-shrink:0}
.close-btn:active{background:rgba(255,77,109,.15);color:var(--r);transform:scale(.9)}
.scr-title{font-size:15px;font-weight:800;flex:1}

/* ── ADD TX TYPE TABS ── */
.type-tabs{display:flex;background:var(--bg3);border-radius:11px;padding:3px;gap:2px;flex:1}
.tt{flex:1;text-align:center;padding:7px 3px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;transition:all .18s;border:none;background:transparent;color:var(--tx3);font-family:inherit;white-space:nowrap}
.tt.te{background:var(--r);color:#fff}.tt.ti{background:var(--g);color:#fff}.tt.tt2{background:var(--b);color:#fff}

/* ── AMOUNT HERO ── */
.amt-hero{
  background:var(--bg2);padding:12px 16px 10px;
  text-align:center;flex-shrink:0;border-bottom:1px solid var(--br);
  cursor:pointer;transition:background .18s;
}
.amt-hero:active{background:var(--bg3)}
.amt-cat-prev{display:flex;align-items:center;justify-content:center;gap:7px;margin-bottom:6px}
.acp-ico{width:30px;height:30px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.acp-name{font-size:11px;font-weight:600;color:var(--tx2)}.acp-sub{font-size:10px;color:var(--tx3)}
.amt-val{font-size:36px;font-weight:900;letter-spacing:-1.5px;line-height:1;height:44px;display:flex;align-items:center;justify-content:center;transition:color .2s;overflow:hidden}
.av-e{color:var(--r)}.av-i{color:var(--g)}.av-t{color:var(--b)}.av-0{color:var(--tx3)}
.amt-hint{font-size:10px;color:var(--tx3);margin-top:3px;display:flex;align-items:center;justify-content:center;gap:4px}
.amt-hint-pill{background:var(--bg3);border:1px solid var(--br);border-radius:99px;padding:2px 9px;font-size:10px;font-weight:700}

/* ── NUMPAD (collapsible) ── */
.numpad-wrap{
  flex-shrink:0;
  max-height:0;overflow:hidden;
  transition:max-height .32s cubic-bezier(.16,1,.3,1);
}
.numpad-wrap.open{max-height:260px}
.numpad{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--br);border-top:1px solid var(--br)}

/* ── SUBCAT BOTTOM SHEET ── */
.subcat-sheet{
  position:absolute;left:0;right:0;bottom:0;
  background:var(--bg2);border-top:2px solid var(--br);
  border-radius:20px 20px 0 0;
  z-index:10;padding:0 16px 20px;
  transform:translateY(100%);
  transition:transform .28s cubic-bezier(.16,1,.3,1);
  max-height:55vh;overflow-y:auto;
}
.subcat-sheet.open{transform:translateY(0)}
.subcat-sheet-handle{width:36px;height:4px;border-radius:2px;background:var(--br2);margin:10px auto 14px;cursor:pointer}
.subcat-sheet-title{font-size:11px;font-weight:800;color:var(--tx3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;display:flex;align-items:center;gap:6px}
.subcat-sheet-title::after{content:'';flex:1;height:1px;background:var(--br)}
.subcat-big-chip{
  display:inline-flex;align-items:center;gap:7px;
  padding:9px 16px;border-radius:12px;
  background:var(--bg3);border:2px solid var(--br);
  font-size:13px;font-weight:600;color:var(--tx);
  cursor:pointer;transition:all .15s;
  margin:0 6px 8px 0;
}
.subcat-big-chip:active{transform:scale(.94)}
.subcat-big-chip.sel-i{background:rgba(0,196,140,.15);border-color:var(--g);color:var(--g)}
.subcat-big-chip.sel-e{background:rgba(255,77,109,.15);border-color:var(--r);color:var(--r)}
.subcat-big-chip .sbc-emoji{font-size:18px}
.subcat-sheet-overlay{position:absolute;inset:0;z-index:9;background:rgba(0,0,0,.3);opacity:0;pointer-events:none;transition:opacity .28s}
.subcat-sheet-overlay.open{opacity:1;pointer-events:all}

.cat-scroll{flex:1;overflow-y:auto;-webkit-overflow-scrolling:touch;padding:10px 14px 0;min-height:0}
.cat-sec-title{font-size:9px;font-weight:700;color:var(--tx3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;display:flex;align-items:center;gap:6px}
.cat-sec-title::before{content:'';flex:1;height:1px;background:var(--br)}
.cat-sec-title::after{content:'';flex:1;height:1px;background:var(--br)}
.cat-group-label{font-size:9px;font-weight:800;color:var(--tx3);text-transform:uppercase;letter-spacing:.1em;padding:4px 2px 6px;display:flex;align-items:center;gap:5px}
.cat-group-label span{color:var(--tx2)}
/* 3-column grid for more categories visible at once */
.cat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:6px;margin-bottom:10px}
.ci{
  display:flex;flex-direction:column;align-items:center;gap:3px;
  padding:8px 2px 6px;border-radius:13px;
  cursor:pointer;border:2px solid transparent;
  background:var(--bg2);transition:all .15s;position:relative;
}
.ci:active{transform:scale(.9)}
.ci.sel{border-color:var(--g);background:rgba(0,196,140,.1)}.ci.sel-e{border-color:var(--r);background:rgba(255,77,109,.1)}
.ci-ico{width:36px;height:36px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:19px}
.ci-lbl{font-size:8.5px;font-weight:700;color:var(--tx2);text-align:center;line-height:1.2;max-width:64px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ci.sel .ci-lbl{color:var(--g)}.ci.sel-e .ci-lbl{color:var(--r)}
/* sub-count badge */
.ci-cnt{position:absolute;top:3px;right:3px;background:var(--bg4);color:var(--tx3);font-size:7px;font-weight:800;border-radius:99px;padding:1px 4px;line-height:1.4}
.ci.sel .ci-cnt{background:rgba(0,196,140,.2);color:var(--g)}
.ci.sel-e .ci-cnt{background:rgba(255,77,109,.2);color:var(--r)}
/* Sub-category chips */
.subcat-row{display:flex;gap:6px;flex-wrap:wrap;margin:2px 0 12px;padding:2px 0}
.subcat-chip{
  padding:7px 13px;border-radius:99px;
  background:var(--bg3);border:1.5px solid var(--br);
  font-size:12px;font-weight:600;color:var(--tx2);
  cursor:pointer;transition:all .15s;
  display:flex;align-items:center;gap:5px;white-space:nowrap;
}
.subcat-chip:active{transform:scale(.93)}
.subcat-chip.active{background:rgba(0,196,140,.15);border-color:var(--g);color:var(--g)}
.subcat-chip.active-e{background:rgba(255,77,109,.15);border-color:var(--r);color:var(--r)}
.subcat-chip .sc-emoji{font-size:14px}

/* ── TX DETAILS ROWS (inside scroll) ── */
.tx-details{background:var(--bg2);border:1px solid var(--br);border-radius:16px;margin:0 14px 10px}
.dr{display:flex;align-items:center;gap:9px;padding:10px 14px;border-bottom:1px solid var(--br)}
.dr:last-child{border-bottom:none}
.dr-ico{width:28px;height:28px;border-radius:8px;background:var(--bg3);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0}
.dr-in{flex:1;background:none;border:none;outline:none;color:var(--tx);font-size:13px;font-weight:500;font-family:inherit;min-width:0}
.dr-in::placeholder{color:var(--tx3)}
.dr-sel{flex:1;background:none;border:none;outline:none;color:var(--tx);font-size:13px;font-weight:500;font-family:inherit;cursor:pointer;-webkit-appearance:none;min-width:0}
.dr-sel option{background:var(--bg2);color:var(--tx)}
.recur-toggle{display:flex;align-items:center;gap:5px;cursor:pointer;font-size:11px;font-weight:600;color:var(--tx2);flex-shrink:0}
.recur-chk{width:16px;height:16px;border-radius:5px;border:1.5px solid var(--br2);background:var(--bg3);display:flex;align-items:center;justify-content:center;transition:all .2s;flex-shrink:0;font-size:10px}
.recur-chk.checked{background:var(--g);border-color:var(--g);color:#fff}
.period-sel{background:var(--bg3);border:1px solid var(--br);border-radius:8px;padding:4px 8px;color:var(--g);font-size:10px;font-weight:700;font-family:inherit;cursor:pointer;outline:none;flex-shrink:0}

/* ── NUMPAD ── */
.numpad{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--br);border-top:1px solid var(--br);flex-shrink:0}
.nk{
  background:var(--bg2);display:flex;align-items:center;justify-content:center;
  padding:0;height:52px;font-size:19px;font-weight:600;cursor:pointer;
  border:none;color:var(--tx);font-family:inherit;
  user-select:none;-webkit-tap-highlight-color:transparent;
  position:relative;overflow:hidden;transition:background .1s;
}
.nk::after{content:'';position:absolute;inset:0;background:rgba(255,255,255,0);transition:background .08s}
.nk:active::after{background:rgba(255,255,255,.06)}
.nk-spec{color:var(--tx2);font-size:15px}
.nk-ok{
  background:linear-gradient(135deg,var(--gl),var(--g));color:#fff;
  font-size:12px;font-weight:800;grid-row:span 2;
  box-shadow:inset 0 1px 0 rgba(255,255,255,.2);
}
.nk-ok:active::after{background:rgba(0,0,0,.1)}
.numpad-close-bar{
  display:flex;align-items:center;justify-content:center;
  background:var(--bg2);border-top:1px solid var(--br);padding:6px;
  cursor:pointer;
}
.numpad-close-btn{
  background:var(--bg3);border:1px solid var(--br);border-radius:99px;
  padding:4px 20px;font-size:10px;font-weight:800;color:var(--tx3);
  cursor:pointer;border:none;font-family:inherit;
}

/* ── CAT MANAGER SCREEN ── */
.cat-type-tab{display:flex;background:var(--bg3);border-radius:11px;padding:3px;margin:10px 16px;gap:3px;flex-shrink:0}
.ctt{flex:1;text-align:center;padding:8px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;border:none;background:transparent;color:var(--tx3);font-family:inherit;transition:all .18s}
.ctt.ae{background:var(--r);color:#fff}.ctt.ai{background:var(--g);color:#fff}
.cat-parent-group{margin:0 16px 12px;background:var(--bg2);border:1px solid var(--br);border-radius:16px;overflow:hidden}
.cpg-header{display:flex;align-items:center;gap:10px;padding:12px 14px;cursor:pointer}
.cpg-emoji{width:36px;height:36px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0}
.cpg-name{flex:1;font-size:13px;font-weight:700;color:var(--tx)}
.cpg-count{font-size:10px;color:var(--tx3)}
.cpg-toggle{font-size:14px;color:var(--tx3);transition:transform .25s;flex-shrink:0}
.cpg-toggle.open{transform:rotate(90deg)}
.cpg-children{border-top:1px solid var(--br)}
.cpg-child{display:flex;align-items:center;gap:9px;padding:9px 14px 9px 22px;border-bottom:1px solid rgba(255,255,255,.03)}
.cpg-child:last-child{border-bottom:none}
.cpc-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0}
.cpc-name{flex:1;font-size:12px;color:var(--tx2);font-weight:500}
.cpc-del{background:none;border:none;color:var(--tx3);cursor:pointer;font-size:12px;padding:3px}
.cpg-add-child{display:flex;align-items:center;gap:7px;padding:8px 14px 8px 22px;cursor:pointer;color:var(--g);font-size:11px;font-weight:600;border-top:1px solid var(--br)}
.cpg-del{background:none;border:none;color:var(--tx3);cursor:pointer;font-size:13px;padding:3px;flex-shrink:0}

/* ── FIXED SCREEN ── */
.fxd-summary{display:grid;grid-template-columns:1fr 1fr;gap:9px;margin:12px 16px 0}
.fxd-card{border-radius:16px;padding:13px}
.fxd-inc{background:linear-gradient(135deg,rgba(0,196,140,.15),rgba(0,196,140,.04));border:1px solid rgba(0,196,140,.2)}
.fxd-exp{background:linear-gradient(135deg,rgba(255,77,109,.15),rgba(255,77,109,.04));border:1px solid rgba(255,77,109,.2)}
.fxd-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px}
.fxd-inc .fxd-lbl{color:var(--g)}.fxd-exp .fxd-lbl{color:var(--r)}
.fxd-val{font-size:19px;font-weight:900;color:var(--tx)}.fxd-cnt{font-size:10px;color:var(--tx3);margin-top:2px}
.fxd-net-bar{margin:8px 16px;background:var(--bg2);border:1px solid var(--br);border-radius:13px;padding:11px 14px;display:flex;align-items:center;justify-content:space-between}
.fxd-net-label{font-size:11px;font-weight:700;color:var(--tx2)}.fxd-net-val{font-size:17px;font-weight:900}
.fxd-list{margin:0 16px;background:var(--bg2);border:1px solid var(--br);border-radius:18px;overflow:hidden}
.fxd-item{display:flex;align-items:center;gap:10px;padding:11px 14px;border-bottom:1px solid rgba(255,255,255,.04)}
.fxd-item:last-child{border-bottom:none}
.fxd-item-day{width:24px;height:24px;border-radius:7px;background:var(--bg3);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;color:var(--tx3);flex-shrink:0}
.fxd-item-ico{width:38px;height:38px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.fxd-item-info{flex:1;min-width:0}
.fxd-item-name{font-size:13px;font-weight:600;color:var(--tx);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.fxd-item-cat{font-size:10px;color:var(--tx3);margin-top:1px}
.fxd-item-amt{font-size:14px;font-weight:800;flex-shrink:0}
.fxd-item-del{background:none;border:none;color:var(--tx3);cursor:pointer;font-size:13px;padding:3px;flex-shrink:0}

/* ── OVERLAY + SHEET ── */
.overlay{
  position:fixed;inset:0;z-index:60;
  background:rgba(0,0,0,.6);backdrop-filter:blur(5px);
  display:flex;align-items:flex-end;justify-content:center;
}
.sheet{
  width:100%;max-width:430px;
  background:var(--bg2);border-radius:22px 22px 0 0;border-top:1px solid var(--br2);
  padding:0 18px;padding-bottom:calc(22px + var(--safe-bot));
  animation:sheetUp .3s cubic-bezier(.16,1,.3,1);
  max-height:88dvh;overflow-y:auto;-webkit-overflow-scrolling:touch;
}
@keyframes sheetUp{from{transform:translateY(100%)}to{transform:translateY(0)}}
.sh-handle{width:34px;height:4px;background:rgba(255,255,255,.1);border-radius:2px;margin:11px auto 16px}
.sh-title{font-size:14px;font-weight:800;color:var(--tx);margin-bottom:16px}
.fl{font-size:10px;font-weight:700;color:var(--tx2);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px}
.fi-input{width:100%;background:var(--bg3);border:1.5px solid var(--br);border-radius:12px;padding:11px 12px;color:var(--tx);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;margin-bottom:11px}
.fi-input:focus{border-color:var(--g)}
.fs{width:100%;background:var(--bg3);border:1.5px solid var(--br);border-radius:12px;padding:11px 12px;color:var(--tx);font-size:14px;font-family:inherit;outline:none;margin-bottom:11px;-webkit-appearance:none;cursor:pointer}
.fs option{background:var(--bg2)}
.f-row{display:grid;grid-template-columns:1fr 1fr;gap:9px}
.btn-p{width:100%;background:linear-gradient(135deg,var(--gl),var(--g));color:#fff;font-size:13px;font-weight:700;padding:13px;border-radius:13px;border:none;cursor:pointer;font-family:inherit;transition:all .2s;box-shadow:0 3px 16px var(--gg)}
.btn-p:active{transform:scale(.98)}

/* ── EMOJI + COLOR PICKERS ── */
.emoji-picker{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:11px}
.ep-item{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:18px;cursor:pointer;background:var(--bg3);border:2px solid transparent;transition:all .15s}
.ep-item:active{transform:scale(.88)}.ep-item.sel{border-color:var(--g);background:rgba(0,196,140,.1)}
.color-picker{display:flex;gap:7px;flex-wrap:wrap;margin-bottom:12px}
.cp-dot{width:28px;height:28px;border-radius:50%;cursor:pointer;border:3px solid transparent;transition:all .15s;box-shadow:0 2px 5px rgba(0,0,0,.3)}
.cp-dot:active{transform:scale(.88)}.cp-dot.sel{border-color:#fff;transform:scale(1.12)}

/* ── TOAST & LOADER ── */
.toast-wrap{position:fixed;top:0;left:50%;transform:translateX(-50%);z-index:999;pointer-events:none;padding-top:calc(12px + var(--safe-top))}
.toast{background:var(--bg2);border:1px solid var(--br2);border-radius:13px;padding:9px 16px;font-size:12px;font-weight:600;white-space:nowrap;box-shadow:0 6px 24px rgba(0,0,0,.4);display:flex;align-items:center;gap:7px;transform:translateY(-60px);opacity:0;transition:all .3s cubic-bezier(.16,1,.3,1)}
.toast.show{transform:translateY(0);opacity:1}.toast.success{border-color:rgba(0,196,140,.35);color:var(--g)}.toast.error{border-color:rgba(255,77,109,.35);color:var(--r)}.toast.info{border-color:rgba(76,154,255,.35);color:var(--b)}
.loading-wrap{position:fixed;inset:0;z-index:999;background:rgba(13,17,23,.7);backdrop-filter:blur(3px);display:flex;align-items:center;justify-content:center}
.spinner{width:36px;height:36px;border:3px solid rgba(0,196,140,.15);border-top-color:var(--g);border-radius:50%;animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes pulse-g{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.8)}}
.pb{height:20px}

/* ── INVEST ── */
.inv-item{margin:7px 16px 0;background:var(--bg2);border:1px solid var(--br);border-radius:16px;padding:12px 14px;display:flex;align-items:center;gap:10px}
.inv-badge{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#fff;letter-spacing:-.5px;flex-shrink:0}
.ib-c{background:linear-gradient(135deg,#f59e0b,#d97706)}.ib-s{background:linear-gradient(135deg,var(--b),#2979ff)}
.inv-info{flex:1;min-width:0}.inv-name{font-size:12px;font-weight:700;color:var(--tx);display:flex;align-items:center;gap:5px}
.inv-tag{font-size:9px;font-weight:700;background:var(--bg3);color:var(--tx3);padding:1px 5px;border-radius:4px;text-transform:uppercase}
.inv-sub{font-size:10px;color:var(--tx3);margin-top:1px}.inv-right{text-align:right;flex-shrink:0}
.inv-val{font-size:13px;font-weight:800;color:var(--tx)}.inv-pct{font-size:11px;font-weight:700;margin-top:1px}
.ip-p{color:var(--g)}.ip-n{color:var(--r)}.inv-del{background:none;border:none;color:var(--tx3);cursor:pointer;font-size:12px;margin-top:2px;display:block;padding:2px}

/* ── MONTH SELECTOR ── */
.month-sel-wrap{padding:9px 14px 6px;border-bottom:1px solid var(--br);background:var(--bg2)}
.month-sel-label{font-size:9px;font-weight:700;color:var(--tx3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:7px;display:flex;align-items:center;justify-content:space-between}
.month-chips{display:flex;gap:6px;overflow-x:auto;padding-bottom:2px}
.month-chips::-webkit-scrollbar{display:none}
.mchip{
  flex-shrink:0;padding:6px 12px;border-radius:99px;
  background:var(--bg3);border:1.5px solid var(--br);
  font-size:11px;font-weight:700;color:var(--tx2);
  cursor:pointer;transition:all .18s;white-space:nowrap;
  display:flex;flex-direction:column;align-items:center;gap:1px;
}
.mchip:active{transform:scale(.93)}
.mchip.current-m{border-color:rgba(76,154,255,.4);color:var(--b);background:rgba(76,154,255,.08)}
.mchip.sel-income{background:rgba(0,196,140,.15);border-color:var(--g);color:var(--g)}
.mchip.sel-expense{background:rgba(255,77,109,.15);border-color:var(--r);color:var(--r)}
.mchip-month{font-size:12px;font-weight:800}
.mchip-year{font-size:9px;opacity:.7}
.mchip-tag{font-size:8px;font-weight:700;padding:1px 5px;border-radius:99px;margin-top:1px}
.mchip.current-m .mchip-tag{background:rgba(76,154,255,.2);color:var(--b)}
.month-expand-btn{font-size:10px;font-weight:700;color:var(--g);cursor:pointer;white-space:nowrap;flex-shrink:0;padding:6px 4px}
.month-custom-row{display:flex;gap:7px;margin-top:7px;align-items:center}
.month-custom-sel{flex:1;background:var(--bg3);border:1.5px solid var(--br);border-radius:10px;padding:7px 9px;color:var(--tx);font-size:12px;font-family:inherit;outline:none;cursor:pointer;-webkit-appearance:none}
.month-custom-sel:focus{border-color:var(--g)}
</style>
</head>
<body>
<div id="app" x-data="app()" x-init="init()">

<!-- LOADING -->
<div class="loading-wrap" x-show="loading" style="display:none">
  <div class="spinner"></div>
</div>

<!-- TOAST -->
<div class="toast-wrap">
  <div class="toast" :class="[toast.show?'show':'',toast.type]" x-text="toast.msg"></div>
</div>

<!-- ══ TOPBAR ══ -->
<div class="topbar">
  <div class="ta-left">
    <div class="ta-ava" x-text="uname.charAt(0).toUpperCase()"></div>
    <div class="ta-info">
      <span class="ta-greet">Xin chào 👋</span>
      <span class="ta-name" x-text="uname"></span>
    </div>
  </div>
  <div class="ta-right">
    <div style="display:flex;align-items:center;gap:3px;background:rgba(0,196,140,.1);border:1px solid rgba(0,196,140,.2);border-radius:99px;padding:4px 8px" x-show="isOnline">
      <div style="width:5px;height:5px;border-radius:50%;background:var(--g);animation:pulse-g 2s infinite"></div>
      <span style="font-size:10px;font-weight:600;color:var(--g)">Live</span>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="margin:0">@csrf
      <button type="submit" class="ti-btn">🚪</button>
    </form>
  </div>
</div>

<!-- ══ CURRENCY BAR ══ -->
<div class="currency-bar">
  <div class="cur-item"><span class="cur-flag">🇺🇸</span><span class="cur-code">USD</span><span class="cur-val" x-text="numFmt(cur.USD)+'₫'"></span></div>
  <div class="cur-sep"></div>
  <div class="cur-item"><span class="cur-flag">🇪🇺</span><span class="cur-code">EUR</span><span class="cur-val" x-text="numFmt(cur.EUR)+'₫'"></span></div>
  <div class="cur-sep"></div>
  <div class="cur-item"><span class="cur-flag">🇯🇵</span><span class="cur-code">JPY</span><span class="cur-val" x-text="numFmt(cur.JPY)+'₫'"></span></div>
  <div class="cur-sep"></div>
  <div class="cur-item"><span class="cur-flag">🇬🇧</span><span class="cur-code">GBP</span><span class="cur-val" x-text="numFmt(cur.GBP)+'₫'"></span></div>
  <div class="cur-sep"></div>
  <span class="cur-time" x-text="cur.updated?'🕐 '+cur.updated:''"></span>
</div>

<!-- ══ MONTH NAV ══ -->
<div class="month-nav" x-show="['home','transactions','stats'].includes(tab)">
  <button class="mn-btn" @click="chMonth(-1)">‹</button>
  <span class="mn-label" x-text="mLabel"></span>
  <button class="mn-btn" @click="chMonth(1)">›</button>
</div>

<!-- ══ SEARCH BAR ══ -->
<div class="search-bar" x-show="tab==='transactions'" x-transition>
  <div class="sb-wrap">
    <span class="sb-icon">🔍</span>
    <input class="sb-input" type="text" placeholder="Tìm giao dịch, danh mục..." x-model="search">
  </div>
  <div class="filter-tabs">
    <button class="filter-tab" :class="txFilter==='all'?'active':''" @click="txFilter='all'">Tất cả</button>
    <button class="filter-tab" :class="txFilter==='expense'?'active':''" @click="txFilter='expense'">Chi</button>
    <button class="filter-tab" :class="txFilter==='income'?'active':''" @click="txFilter='income'">Thu</button>
  </div>
</div>

<!-- ══ SCROLL AREA ══ -->
<div class="scroll-area">

  <!-- HOME TAB -->
  <div x-show="tab==='home'">
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
            <div class="ba-info"><div class="ba-title">Vượt ngân sách: <span x-text="ub.cat"></span></div><div class="ba-sub">Đã chi <span x-text="fmtS(ub.spent)"></span> / <span x-text="fmtS(ub.limit)"></span></div></div>
            <span class="ba-action" @click="tab='stats'">Xem →</span>
          </div>
        </template>
      </div>
    </template>

    <!-- Fixed Monthly Preview -->
    <template x-if="fixedItems.length>0">
      <div>
        <div class="sec-hdr"><span class="sec-lbl">💼 Cố định hàng tháng</span><span class="sec-act" @click="showFixedScreen=true">Quản lý →</span></div>
        <div class="fixed-card">
          <div class="fc-summary">
            <div class="fcs fcs-inc"><div class="fcs-label">Thu CĐ</div><div class="fcs-val" x-text="fmtS(fixedIncome)"></div></div>
            <div class="fcs fcs-exp"><div class="fcs-label">Chi CĐ</div><div class="fcs-val" x-text="fmtS(fixedExpense)"></div></div>
            <div class="fcs fcs-net"><div class="fcs-label">Ròng</div><div class="fcs-val" x-text="fmtS(fixedIncome-fixedExpense)"></div></div>
          </div>
          <template x-for="fi in fixedItems.slice(0,3)" :key="fi.id">
            <div class="fixed-item">
              <div class="fi-day" x-text="fi.day"></div>
              <div class="fi-ico" :style="'background:'+catColor(fi.category)+'22'" x-text="catEmoji(fi.category)"></div>
              <div class="fi-info"><div class="fi-name" x-text="fi.name"></div><div class="fi-sub" x-text="fi.category"></div></div>
              <div class="fi-amt" :style="fi.type==='income'?'color:var(--g)':'color:var(--r)'" x-text="(fi.type==='income'?'+':'-')+fmtS(fi.amount)"></div>
            </div>
          </template>
          <button class="btn-apply" @click="applyFixedMonth()">⚡ Áp dụng tháng <span x-text="curM+'/'+curY"></span></button>
        </div>
      </div>
    </template>
    <template x-if="fixedItems.length===0">
      <div style="margin:8px 16px 0">
        <button class="btn-p" style="background:var(--bg2);color:var(--p);box-shadow:none;border:1px solid rgba(123,92,250,.3)" @click="showFixedScreen=true">💼 Thiết lập thu/chi cố định hàng tháng</button>
      </div>
    </template>

    <!-- Wallets -->
    <div class="sec-hdr"><span class="sec-lbl">Ví của tôi</span><span class="sec-act" @click="showAccMgrScreen=true">Quản lý →</span></div>
    <div class="wallet-scroll">
      <template x-for="(a,i) in accounts" :key="a.id">
        <div class="wcard" :class="'wc'+i%6">
          <div class="wcard-top">
            <span class="wcard-ico" x-text="a.type==='bank'?'🏦':(a.type==='e-wallet'?'📱':'👛')"></span>
            <div class="wcard-actions">
              <button class="wcard-action-btn" @click.stop="openEditAcc(a)" title="Sửa">✏️</button>
              <button class="wcard-action-btn" @click.stop="deleteAcc(a)" title="Xoá">🗑</button>
            </div>
          </div>
          <div>
            <div class="wcard-name" x-text="a.name"></div>
            <div class="wcard-bal" x-text="fmtS(a.balance)"></div>
            <div class="wcard-type" x-text="a.type==='bank'?'Ngân hàng':a.type==='e-wallet'?'Ví điện tử':'Tiền mặt'"></div>
          </div>
        </div>
      </template>
      <div class="wadd" @click="showAccSheet=true"><span style="font-size:20px;opacity:.5">＋</span><span style="font-size:9px;font-weight:700">Thêm ví</span></div>
    </div>

    <!-- Chart -->
    <div class="chart-wrap">
      <div class="chart-hdr"><span class="chart-title">7 ngày gần nhất</span><div class="chart-leg"><div class="cl-item"><div class="cl-dot" style="background:var(--g)"></div>Thu</div><div class="cl-item"><div class="cl-dot" style="background:var(--r)"></div>Chi</div></div></div>
      <div style="height:80px"><canvas id="weekChart"></canvas></div>
    </div>

    <!-- Budgets -->
    <template x-if="budgets.length>0">
      <div>
        <div class="sec-hdr"><span class="sec-lbl">Ngân sách tháng</span><span class="sec-act" @click="showBudgetSheet=true">✏️ Sửa</span></div>
        <div style="margin:0 16px">
          <template x-for="b in budgets" :key="b.cat">
            <div class="budget-item">
              <div class="bi-top"><div class="bi-cat"><div class="bi-cat-ico" :style="'background:'+catColor(b.cat)+'22'" x-text="catEmoji(b.cat)"></div><span class="bi-cat-name" x-text="b.cat"></span></div><div class="bi-amounts"><div class="bi-used" :style="b.spent>b.limit?'color:var(--r)':''" x-text="fmtS(b.spent)"></div><div class="bi-limit" x-text="'/ '+fmtS(b.limit)"></div></div></div>
              <div class="bi-bar"><div class="bi-bar-fill" :class="b.pct>=100?'bi-bar-over':b.pct>=80?'bi-bar-warn':'bi-bar-ok'" :style="'width:'+Math.min(b.pct,100)+'%'"></div></div>
            </div>
          </template>
        </div>
      </div>
    </template>

    <!-- Recent TX -->
    <div class="sec-hdr"><span class="sec-lbl">Gần đây</span><span class="sec-act" @click="tab='transactions'">Xem tất cả</span></div>
    <template x-if="grouped.length===0">
      <div style="padding:32px 20px;text-align:center;color:var(--tx3)"><div style="font-size:44px;opacity:.25;margin-bottom:10px">💸</div><div style="font-size:13px;font-weight:700;color:var(--tx2)">Chưa có giao dịch nào</div><div style="font-size:11px;margin-top:4px">Nhấn ＋ để ghi chép</div></div>
    </template>
    <template x-for="g in grouped.slice(0,2)" :key="g.date">
      <div class="tx-grp-wrap">
        <div class="tx-dh"><span class="tx-dl" :class="g.label==='Hôm nay'?'today':g.label==='Hôm qua'?'yest':''" x-text="g.label"></span><span class="tx-dn" x-text="fmtS(g.net)"></span></div>
        <template x-for="tx in g.items.slice(0,3)" :key="tx.id">
          <div class="tx-row">
            <div class="tx-ico" :style="'background:'+catColor(catParent(tx.category))+'22'" x-text="catEmoji(catParent(tx.category))"></div>
            <div class="tx-body"><div class="tx-cat" x-text="tx.category"></div><div class="tx-meta"><span class="tx-acc-tag" x-text="tx.account?tx.account.name:''"></span><span class="tx-note" x-text="tx.note"></span><template x-if="tx.is_recurring"><span class="tx-recur">🔄</span></template></div></div>
            <div class="tx-amount" :class="tx.type" x-text="(tx.type==='income'?'+':tx.type==='expense'?'-':'⇄')+fmtS(tx.amount)"></div>
          </div>
        </template>
      </div>
    </template>

    <!-- Goals -->
    <template x-if="goals.length>0">
      <div>
        <div class="sec-hdr"><span class="sec-lbl">Mục tiêu tiết kiệm</span><span class="sec-act" @click="showGoalSheet=true">＋ Thêm</span></div>
        <template x-for="g in goals" :key="g.id">
          <div class="goal-item">
            <div class="gi-top"><div class="gi-name"><span x-text="g.icon||'🎯'"></span><span x-text="g.name"></span></div><span class="gi-pct" x-text="Math.min(Math.round((g.saved/g.target)*100),100)+'%'"></span></div>
            <div class="gi-amounts"><span>Đã có: <b x-text="fmtS(g.saved)"></b></span><span>Mục tiêu: <b x-text="fmtS(g.target)"></b></span></div>
            <div class="gi-bar"><div class="gi-fill" :style="'width:'+Math.min((g.saved/g.target)*100,100)+'%'"></div></div>
            <div class="gi-deadline" x-text="g.deadline?'📅 Hạn: '+fmtDate(g.deadline):''"></div>
          </div>
        </template>
      </div>
    </template>
    <template x-if="goals.length===0">
      <div style="margin:7px 16px 0"><button class="btn-p" style="background:var(--bg2);color:var(--b);box-shadow:none;border:1px solid rgba(76,154,255,.3)" @click="showGoalSheet=true">🎯 Thêm mục tiêu tiết kiệm</button></div>
    </template>
    <div class="pb"></div>
  </div>

  <!-- TRANSACTIONS TAB -->
  <div x-show="tab==='transactions'">
    <!-- Month nav + view toggle -->
    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px 4px;gap:10px">
      <div style="display:flex;align-items:center;gap:6px">
        <button style="background:var(--bg3);border:none;border-radius:8px;width:28px;height:28px;cursor:pointer;color:var(--tx2);font-size:14px;display:flex;align-items:center;justify-content:center" @click="curM===1?(curM=12,curY--):(curM--)">‹</button>
        <span style="font-size:13px;font-weight:800;color:var(--tx);min-width:80px;text-align:center" x-text="'Tháng '+curM+'/'+curY"></span>
        <button style="background:var(--bg3);border:none;border-radius:8px;width:28px;height:28px;cursor:pointer;color:var(--tx2);font-size:14px;display:flex;align-items:center;justify-content:center" @click="curM===12?(curM=1,curY++):(curM++)">›</button>
      </div>
      <div class="view-toggle">
        <button class="vt-btn" :class="txView==='list'?'active':''" @click="txView='list'">&#9776; Danh sách</button>
        <button class="vt-btn" :class="txView==='calendar'?'active':''" @click="txView='calendar'">&#128197; Lịch</button>
      </div>
    </div>

    <!-- Stats bar -->
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:7px;padding:4px 16px 6px">
      <div style="background:rgba(0,196,140,.1);border:1px solid rgba(0,196,140,.2);border-radius:11px;padding:9px"><div style="font-size:9px;font-weight:700;color:var(--g);margin-bottom:2px;text-transform:uppercase;letter-spacing:.06em">↓ THU</div><div style="font-size:14px;font-weight:800;color:var(--g)" x-text="fmtS(mStats.income)"></div></div>
      <div style="background:rgba(255,77,109,.1);border:1px solid rgba(255,77,109,.2);border-radius:11px;padding:9px"><div style="font-size:9px;font-weight:700;color:var(--r);margin-bottom:2px;text-transform:uppercase;letter-spacing:.06em">↑ CHI</div><div style="font-size:14px;font-weight:800;color:var(--r)" x-text="fmtS(mStats.expense)"></div></div>
      <div style="background:var(--bg2);border:1px solid var(--br);border-radius:11px;padding:9px"><div style="font-size:9px;font-weight:700;color:var(--tx3);margin-bottom:2px;text-transform:uppercase;letter-spacing:.06em">⚖ CÒN</div><div style="font-size:14px;font-weight:800;color:var(--tx)" x-text="fmtS(mStats.income-mStats.expense)"></div></div>
    </div>

    <!-- ══ CALENDAR VIEW ══ -->
    <div x-show="txView==='calendar'">
      <div class="cal-wrap">
        <!-- Days of week header -->
        <div class="cal-dow">
          <div class="cal-dow-lbl sun">CN</div>
          <div class="cal-dow-lbl">T2</div>
          <div class="cal-dow-lbl">T3</div>
          <div class="cal-dow-lbl">T4</div>
          <div class="cal-dow-lbl">T5</div>
          <div class="cal-dow-lbl">T6</div>
          <div class="cal-dow-lbl sat">T7</div>
        </div>
        <!-- Calendar grid -->
        <div class="cal-grid">
          <template x-for="cell in calGrid" :key="cell.key">
            <div class="cal-day"
              :class="[
                cell.empty?'empty':'',
                cell.isToday?'today':'',
                cell.hasTx?'has-tx':'',
                !cell.empty&&calSelectedDay===cell.dateStr?'selected':'',
                cell.otherMonth?'other-month':''
              ]"
              @click="!cell.empty&&selectCalDay(cell.dateStr)">
              <span class="cdn" x-text="cell.day"></span>
              <div class="cal-dots" x-show="cell.hasTx">
                <div class="cal-dot di" x-show="cell.hasIncome"></div>
                <div class="cal-dot de" x-show="cell.hasExpense"></div>
              </div>
            </div>
          </template>
        </div>
      </div>
      <!-- Selected day popup -->
      <div class="cal-day-popup" x-show="calSelectedDay" x-cloak>
        <div class="cdp-date" x-text="calDayLabel"></div>
        <div class="cdp-stats">
          <div class="cdp-stat inc">
            <div class="cdp-stat-lbl">↓ Thu nhập</div>
            <div class="cdp-stat-val" x-text="fmtS(calDayStats.income)"></div>
          </div>
          <div class="cdp-stat exp">
            <div class="cdp-stat-lbl">↑ Chi phí</div>
            <div class="cdp-stat-val" x-text="fmtS(calDayStats.expense)"></div>
          </div>
        </div>
        <!-- Txs for selected day -->
        <template x-for="tx in calDayTxs" :key="tx.id">
          <div class="tx-row" style="padding:6px 0">
            <div class="tx-ico" style="width:32px;height:32px;font-size:16px" :style="'background:'+catColor(catParent(tx.category))+'22'" x-text="catEmoji(catParent(tx.category))"></div>
            <div class="tx-body" @click="editTx(tx)">
              <div class="tx-cat" x-text="tx.category"></div>
              <div class="tx-meta"><span class="tx-note" x-text="tx.note||''"></span></div>
            </div>
            <div class="tx-amount" :class="tx.type" x-text="(tx.type==='income'?'+':tx.type==='expense'?'-':'⇄')+fmtS(tx.amount)"></div>
          </div>
        </template>
        <div x-show="calDayTxs.length===0" style="text-align:center;color:var(--tx3);font-size:11px;padding:6px 0">Không có giao dịch</div>
      </div>
      <div class="pb"></div>
    </div>

    <!-- ══ LIST VIEW ══ -->
    <div x-show="txView==='list'">
      <!-- Search + filter -->
      <div style="display:flex;gap:7px;align-items:center;padding:2px 16px 8px">
        <div style="flex:1;background:var(--bg2);border:1px solid var(--br);border-radius:11px;display:flex;align-items:center;gap:7px;padding:7px 11px">
          <span style="font-size:13px;opacity:.4">&#128269;</span>
          <input type="text" style="flex:1;background:none;border:none;outline:none;color:var(--tx);font-size:12px;font-family:inherit" placeholder="Tìm giao dịch..." x-model="search">
        </div>
        <select style="background:var(--bg2);border:1px solid var(--br);border-radius:11px;padding:7px 9px;color:var(--tx);font-size:11px;font-weight:700;font-family:inherit;outline:none;cursor:pointer" x-model="txFilter">
          <option value="all">Tất cả</option>
          <option value="income">↓ Thu</option>
          <option value="expense">↑ Chi</option>
          <option value="transfer">⇄ Chuyển</option>
        </select>
      </div>
      <template x-if="displayedTxs.length===0"><div style="padding:36px 20px;text-align:center;color:var(--tx3)"><div style="font-size:44px;opacity:.25;margin-bottom:10px">&#128203;</div><div style="font-size:13px;font-weight:700;color:var(--tx2)">Không tìm thấy giao dịch</div></div></template>
      <template x-for="g in displayedGroups" :key="g.date">
        <div class="tx-grp-wrap">
          <div class="tx-dh"><span class="tx-dl" :class="g.label==='Hôm nay'?'today':g.label==='Hôm qua'?'yest':''" x-text="g.label"></span><span class="tx-dn" x-text="fmtS(g.net)"></span></div>
          <template x-for="tx in g.items" :key="tx.id">
            <div class="tx-row">
              <div class="tx-ico" :style="'background:'+catColor(catParent(tx.category))+'22'" x-text="catEmoji(catParent(tx.category))"></div>
              <div class="tx-body" @click="editTx(tx)">
                <div class="tx-cat" x-text="tx.category"></div>
                <div class="tx-meta"><span class="tx-acc-tag" x-text="tx.account?tx.account.name:''"></span><span class="tx-note" x-text="tx.note||''"></span><template x-if="tx.is_recurring"><span class="tx-recur">🔄</span></template></div>
              </div>
              <div class="tx-amount" :class="tx.type" x-text="(tx.type==='income'?'+':tx.type==='expense'?'-':'⇄')+fmtS(tx.amount)"></div>
              <div class="tx-actions">
                <button class="tx-act-btn" @click="editTx(tx)">✏️</button>
                <button class="tx-act-btn" @click="delTx(tx)">🗑</button>
              </div>
            </div>
          </template>
        </div>
      </template>
      <div class="pb"></div>
    </div>
  </div>

  <!-- STATS TAB -->
  <div x-show="tab==='stats'">
    <div class="stats-summary">
      <div class="ss-card ss-income"><div class="ss-label">Thu nhập</div><div class="ss-val" x-text="fmtS(stats.total_income||0)"></div></div>
      <div class="ss-card ss-expense"><div class="ss-label">Chi phí</div><div class="ss-val" x-text="fmtS(stats.total_expense||0)"></div></div>
      <div class="ss-card ss-rate"><div class="ss-label">Tiết kiệm</div><div class="ss-val" x-text="(stats.savings_rate||0)+'%'"></div></div>
    </div>
    <div class="chart-wrap" style="margin-top:10px">
      <div class="chart-hdr"><span class="chart-title">Xu hướng 6 tháng</span><div class="chart-leg"><div class="cl-item"><div class="cl-dot" style="background:var(--g)"></div>Thu</div><div class="cl-item"><div class="cl-dot" style="background:var(--r)"></div>Chi</div></div></div>
      <div style="height:100px"><canvas id="trendChart"></canvas></div>
    </div>
    <div class="chart-wrap" style="margin-top:9px">
      <div class="chart-hdr"><span class="chart-title">Chi phí theo danh mục</span></div>
      <div style="display:flex;gap:10px;align-items:center">
        <div style="width:110px;height:110px;flex-shrink:0"><canvas id="donutChart"></canvas></div>
        <div style="flex:1;min-width:0">
          <template x-if="(stats.by_category||[]).length===0"><div style="color:var(--tx3);font-size:11px;text-align:center;padding:16px 0">Không có dữ liệu</div></template>
          <template x-for="(cat,i) in (stats.by_category||[]).slice(0,5)" :key="cat.category">
            <div class="cat-rank-item">
              <span class="cr-rank" x-text="i+1"></span>
              <div class="cr-ico" :style="'background:'+catColor(catParent(cat.category))+'22'" x-text="catEmoji(catParent(cat.category))"></div>
              <div class="cr-info"><div class="cr-name" x-text="cat.category"></div><div class="cr-bar-wrap"><div class="cr-bar" :style="'width:'+(stats.total_expense>0?(cat.total/stats.total_expense*100):0)+'%;background:'+catColor(catParent(cat.category))"></div></div></div>
              <div><div class="cr-amount" :style="'color:'+catColor(catParent(cat.category))" x-text="fmtS(cat.total)"></div><div class="cr-pct" x-text="stats.total_expense>0?(cat.total/stats.total_expense*100).toFixed(1)+'%':'0%'"></div></div>
            </div>
          </template>
        </div>
      </div>
    </div>
    <div class="pb"></div>
  </div>

  <!-- DEBTS TAB -->
  <div x-show="tab==='debts'">
    <div class="debt-banner">
      <div class="dbc dbc-lend"><div class="dbc-ico">📤</div><div class="dbc-lbl">Cho vay</div><div class="dbc-amt" x-text="fmtS(ov.total_lend)"></div><div class="dbc-cnt" x-text="debts.filter(d=>d.type==='lend').length+' khoản'"></div></div>
      <div class="dbc dbc-borrow"><div class="dbc-ico">📥</div><div class="dbc-lbl">Đi vay</div><div class="dbc-amt" x-text="fmtS(ov.total_borrow)"></div><div class="dbc-cnt" x-text="debts.filter(d=>d.type==='borrow').length+' khoản'"></div></div>
    </div>
    <div class="toolbar"><span class="tbar-lbl">Danh sách nợ</span><button class="btn-sm" @click="showDebtSheet=true">＋ Thêm nợ</button></div>
    <template x-if="debts.length===0"><div style="padding:36px 20px;text-align:center;color:var(--tx3)"><div style="font-size:44px;opacity:.25;margin-bottom:10px">🤝</div><div style="font-size:13px;font-weight:700;color:var(--tx2)">Chưa có khoản nợ</div></div></template>
    <template x-for="d in debts" :key="d.id">
      <div class="debt-item">
        <div class="d-ava" :class="d.type==='lend'?'d-lend':'d-borrow'" x-text="(d.partner_name||'?').charAt(0).toUpperCase()"></div>
        <div class="d-info"><div class="d-name" x-text="d.partner_name"></div><div class="d-sub"><span x-text="d.type==='lend'?'📤 Cho vay':'📥 Đi vay'"></span><template x-if="d.due_date"><span x-text="' · '+fmtDate(d.due_date)"></span></template></div></div>
        <div class="d-right"><div class="d-amount" :class="d.type==='lend'?'d-lend-c':'d-borrow-c'" x-text="fmtS(d.amount)"></div><span class="d-badge" :class="d.status==='paid'?'db-paid':'db-unpaid'" @click="toggleDebt(d.id)" x-text="d.status==='paid'?'✓ Đã trả':'⏳ Chưa trả'"></span><button class="d-del" @click="deleteDebt(d.id)">🗑</button></div>
      </div>
    </template>
    <div class="pb"></div>
  </div>

  <!-- SETTINGS TAB -->
  <div x-show="tab==='settings'">
    <div style="padding:12px 16px 6px"><span style="font-size:10px;font-weight:700;color:var(--tx2);text-transform:uppercase;letter-spacing:.08em">Quản lý danh mục & giao dịch</span></div>
    <div class="set-section">
      <div class="set-item" @click="showAccMgrScreen=true">
        <div class="si-ico" style="background:rgba(0,196,140,.15)">💳</div>
        <div class="si-info"><div class="si-title">Quản lý ví & tài khoản</div><div class="si-sub" x-text="accounts.length+' ví · '+fmtS(ov.total_cash)"></div></div>
        <span class="si-arrow">›</span>
      </div>
      <div class="set-item" @click="showCatScreen=true">
        <div class="si-ico" style="background:rgba(255,159,67,.15)">📂</div>
        <div class="si-info"><div class="si-title">Quản lý danh mục</div><div class="si-sub">Thêm, sửa, xóa danh mục & danh mục con</div></div>
        <span class="si-arrow">›</span>
      </div>
      <div class="set-item" @click="showFixedScreen=true">
        <div class="si-ico" style="background:rgba(123,92,250,.15)">💼</div>
        <div class="si-info"><div class="si-title">Thu / Chi cố định hàng tháng</div><div class="si-sub" x-text="fixedItems.length+' khoản đã thiết lập'"></div></div>
        <span class="si-arrow">›</span>
      </div>
      <div class="set-item" @click="showBudgetSheet=true">
        <div class="si-ico" style="background:rgba(0,196,140,.15)">💰</div>
        <div class="si-info"><div class="si-title">Ngân sách tháng</div><div class="si-sub" x-text="budgets.length+' danh mục theo dõi'"></div></div>
        <span class="si-arrow">›</span>
      </div>
      <div class="set-item" @click="showGoalSheet=true">
        <div class="si-ico" style="background:rgba(76,154,255,.15)">🎯</div>
        <div class="si-info"><div class="si-title">Mục tiêu tiết kiệm</div><div class="si-sub" x-text="goals.length+' mục tiêu'"></div></div>
        <span class="si-arrow">›</span>
      </div>
    </div>
    <div style="padding:12px 16px 6px;margin-top:4px"><span style="font-size:10px;font-weight:700;color:var(--tx2);text-transform:uppercase;letter-spacing:.08em">Đầu tư & Nợ vay</span></div>
    <div class="set-section">
      <div class="set-item" @click="tab='debts'">
        <div class="si-ico" style="background:rgba(255,77,109,.15)">🤝</div>
        <div class="si-info"><div class="si-title">Quản lý nợ vay</div><div class="si-sub" x-text="debts.length+' khoản'"></div></div>
        <span class="si-arrow">›</span>
      </div>
      <div class="set-item" @click="showInvSheet=true">
        <div class="si-ico" style="background:rgba(76,154,255,.15)">📈</div>
        <div class="si-info"><div class="si-title">Danh mục đầu tư</div><div class="si-sub" x-text="investments.length+' tài sản · '+fmtS(ov.total_investment)"></div></div>
        <span class="si-arrow">›</span>
      </div>
    </div>
    <div style="padding:12px 16px 6px;margin-top:4px"><span style="font-size:10px;font-weight:700;color:var(--tx2);text-transform:uppercase;letter-spacing:.08em">Báo cáo</span></div>
    <div class="set-section">
      <div class="set-item" @click="exportReport()">
        <div class="si-ico" style="background:rgba(29,233,182,.15)">📤</div>
        <div class="si-info"><div class="si-title">Xuất báo cáo tháng</div><div class="si-sub" x-text="mLabel+' — Sao chép vào clipboard'"></div></div>
        <span class="si-arrow">›</span>
      </div>
    </div>
    <div class="set-version">MoneyTracker v5.1 · haiyenpa25 🚀</div>
    <div class="pb"></div>
  </div>

</div><!-- /scroll-area -->

<!-- ══ BOTTOM NAV ══ -->
<nav class="bnav">
  <button class="ni" :class="tab==='home'?'on':''" @click="tab='home'"><span class="ni-ico">🏠</span><span class="ni-lbl">Tổng quan</span></button>
  <button class="ni" :class="tab==='transactions'?'on':''" @click="tab='transactions'"><span class="ni-ico">📋</span><span class="ni-lbl">Giao dịch</span></button>
  <div class="fab-wrap"><button class="fab" @click="openAdd('expense')">＋</button></div>
  <button class="ni" :class="tab==='stats'?'on':''" @click="tab='stats';loadStats()"><span class="ni-ico">📊</span><span class="ni-lbl">Thống kê</span></button>
  <button class="ni" :class="tab==='settings'?'on':''" @click="tab='settings'"><span class="ni-ico">⚙️</span><span class="ni-lbl">Cài đặt</span></button>
</nav>

<!-- ══════════════════════════════════
     SCREEN: ADD / EDIT TRANSACTION
══════════════════════════════════ -->
<div class="screen" :class="showAddScreen?'open':''">
  <!-- Header -->
  <div class="scr-hdr">
    <div class="close-btn" @click="showAddScreen=false;editingTx=null">✕</div>
    <div class="type-tabs">
      <button class="tt" :class="form.type==='expense'?'te':''" @click="form.type='expense';form.category='';form.subcat=''" :disabled="!!editingTx">Chi phí</button>
      <button class="tt" :class="form.type==='income'?'ti':''" @click="form.type='income';form.category='';form.subcat=''" :disabled="!!editingTx">Thu nhập</button>
      <button class="tt" :class="form.type==='transfer'?'tt2':''" @click="form.type='transfer';form.category='Chuyển ví';form.subcat=''" :disabled="!!editingTx">Chuyển</button>
    </div>
    <div style="width:32px;font-size:10px;font-weight:700;color:var(--o);text-align:center;flex-shrink:0" x-text="editingTx?'SỬA':''"></div>
  </div>

  <!-- Amount Display (tap to open numpad) -->
  <div class="amt-hero" @click="showNumpad=true">
    <div class="amt-cat-prev">
      <div class="acp-ico" :style="'background:'+catColor(catParent(form.category))+'22'"><span x-text="catEmoji(catParent(form.category))||'💸'"></span></div>
      <div><div class="acp-name" x-text="form.category||'Chọn danh mục'"></div><div class="acp-sub" x-text="form.subcat?('↳ '+form.subcat):''"></div></div>
    </div>
    <div class="amt-val" :class="numpad===''?'av-0':form.type==='expense'?'av-e':form.type==='income'?'av-i':'av-t'">
      <span x-text="numpad===''?'0₫':numFmt(parseFloat(numpad.replace(',','.'))||0)+'₫'"></span>
    </div>
    <div class="amt-hint">
      <span class="amt-hint-pill" :style="showNumpad?'color:var(--r)':''" x-text="showNumpad?'▲ Ẩn bàn phím':'▼ Bấm để nhập số'"></span>
    </div>
  </div>

  <!-- Categories -->
  <div class="cat-scroll">
    <template x-if="form.type!=='transfer'">
      <div>
        <div class="cat-sec-title">Danh mục <span x-text="form.type==='expense'?'chi phí':'thu nhập'"></span></div>
        <div class="cat-grid">
          <template x-for="cat in curCats()" :key="cat.name">
            <div class="ci" :class="form.category===cat.name?(form.type==='expense'?'sel-e':'sel'):''" @click="selectCat(cat.name)">
              <div class="ci-ico" :style="'background:'+cat.color+'22'"><span x-text="cat.emoji"></span></div>
              <span class="ci-lbl" x-text="cat.name"></span>
              <template x-if="(cat.children||[]).length>0">
                <span class="ci-cnt" x-text="(cat.children||[]).length"></span>
              </template>
            </div>
          </template>
        </div>
        <!-- Subcat section: only shows as bottom sheet via JS -->
      </div>
    </template>
  </div><!-- /cat-scroll -->

  <!-- Details (inside scroll) -->
  <div class="tx-details">
    <div class="dr">
      <div class="dr-ico">💳</div>
      <select class="dr-sel" x-model="form.acc">
        <option value="">Chọn ví...</option>
        <template x-for="a in accounts" :key="a.id"><option :value="a.id" x-text="a.name+' (≈ '+fmtS(a.balance)+')'"></option></template>
      </select>
    </div>
    <template x-if="form.type==='transfer'">
      <div class="dr"><div class="dr-ico">➡️</div><select class="dr-sel" x-model="form.toAcc"><option value="">Ví đích...</option><template x-for="a in accounts" :key="a.id"><option :value="a.id" x-text="a.name"></option></template></select></div>
    </template>
    <!-- Month Quick Selector -->
    <div class="month-sel-wrap">
      <div class="month-sel-label">
        <span>📅 Ghi vào tháng</span>
        <span class="month-expand-btn" @click="showMonthExpand=!showMonthExpand" x-text="showMonthExpand?'Thu gọn':'Mở rộng ⌄'"></span>
      </div>
      <div class="month-chips">
        <template x-for="m in monthChips" :key="m.key">
          <div class="mchip"
            :class="[
              m.isCurrent?'current-m':'',
              form.month===m.key?(form.type==='income'?'sel-income':'sel-expense'):''
            ]"
            @click="selectMonth(m.key)">
            <span class="mchip-month" x-text="'T'+m.month"></span>
            <span class="mchip-year" x-text="m.year"></span>
            <template x-if="m.isCurrent"><span class="mchip-tag">Hiện tại</span></template>
            <template x-if="m.isPrev"><span class="mchip-tag" style="background:rgba(255,159,67,.15);color:var(--o)">Trước</span></template>
            <template x-if="m.isNext"><span class="mchip-tag" style="background:rgba(123,92,250,.15);color:var(--p)">Tiếp</span></template>
          </div>
        </template>
      </div>
      <div class="month-custom-row" x-show="showMonthExpand">
        <span style="font-size:11px;color:var(--tx3);flex-shrink:0">Ấn định:</span>
        <select class="month-custom-sel" x-model="customMonth" @change="applyCustomMonth()">
          <template x-for="m in allMonthOptions" :key="m.key">
            <option :value="m.key" x-text="'Tháng '+m.month+'/'+m.year"></option>
          </template>
        </select>
      </div>
      <div style="margin-top:5px;font-size:11px;color:var(--tx2)">
        ➤ Ghi vào: <strong :style="form.type==='income'?'color:var(--g)':'color:var(--r)'" x-text="'T'+form.month.split('-')[1]+'/'+form.month.split('-')[0]"></strong>
        &nbsp;&middot;&nbsp; ngày <span x-text="form.date.split('-')[2]"></span>
        &nbsp;&middot;&nbsp; <span style="color:var(--g);cursor:pointer;text-decoration:underline" @click="showDateDetail=!showDateDetail">Đổi ngày</span>
      </div>
      <div x-show="showDateDetail" style="padding-top:6px">
        <input type="date" style="width:100%;background:var(--bg3);border:1.5px solid var(--g);border-radius:10px;padding:7px 10px;color:var(--tx);font-family:inherit;font-size:13px;outline:none" x-model="form.date" @change="syncMonthFromDate()">
      </div>
    </div>
    <div class="dr">
      <div class="dr-ico">📝</div>
      <input type="text" class="dr-in" placeholder="Ghi chú (không bắt buộc)..." x-model="form.note">
      <div class="recur-toggle" @click="form.recur=!form.recur">
        <div class="recur-chk" :class="form.recur?'checked':''" x-text="form.recur?'✓':''"></div>
        <span>Định kỳ</span>
      </div>
      <template x-if="form.recur">
        <select class="period-sel" x-model="form.period">
          <option value="monthly">Tháng</option>
          <option value="weekly">Tuần</option>
          <option value="yearly">Năm</option>
        </select>
      </template>
    </div>
  </div>
  <div style="height:10px"></div>

  <!-- Numpad (collapsible) -->
  <div class="numpad-wrap" :class="showNumpad?'open':''">
    <div class="numpad-close-bar" @click="showNumpad=false">
      <button class="numpad-close-btn">▲ Ẩn bàn phím</button>
    </div>
    <div class="numpad">
    <button class="nk" @click="numPress('7')">7</button>
    <button class="nk" @click="numPress('8')">8</button>
    <button class="nk" @click="numPress('9')">9</button>
    <button class="nk nk-ok" @click="editingTx?updateTx():saveTx()">
      <span x-text="editingTx?'Cập\nnhật':'Lưu'" style="white-space:pre-line;line-height:1.3"></span>
    </button>
    <button class="nk" @click="numPress('4')">4</button>
    <button class="nk" @click="numPress('5')">5</button>
    <button class="nk" @click="numPress('6')">6</button>
    <button class="nk" @click="numPress('1')">1</button>
    <button class="nk" @click="numPress('2')">2</button>
    <button class="nk" @click="numPress('3')">3</button>
    <button class="nk nk-spec" @click="numPress('000')">000</button>
    <button class="nk nk-spec" @click="numPress('.')">.</button>
    <button class="nk nk-spec" @click="numPress('0')">0</button>
    <button class="nk nk-spec" @click="numPress('C')">C</button>
    <button class="nk nk-spec" @click="numPress('⌫')">⌫</button>
  </div><!-- /numpad grid -->
  </div><!-- /numpad-wrap -->

  <!-- ══ SUBCAT BOTTOM SHEET ══ -->
  <div class="subcat-sheet-overlay" :class="showSubcatSheet?'open':''" @click="showSubcatSheet=false"></div>
  <div class="subcat-sheet" :class="showSubcatSheet?'open':''">
    <div class="subcat-sheet-handle" @click="showSubcatSheet=false"></div>
    <div class="subcat-sheet-title">
      <span x-text="catEmoji(form.category)||'📌'"></span>
      <span x-text="form.category" style="color:var(--tx)"></span>
      <span style="color:var(--tx3)">→ Chọn mục con</span>
    </div>
    <div style="display:flex;flex-wrap:wrap">
      <div class="subcat-big-chip"
        :class="form.subcat===''?(form.type==='income'?'sel-i':'sel-e'):''"
        @click="form.subcat='';showSubcatSheet=false">
        <span class="sbc-emoji">✨</span>
        <span>Không chọn</span>
      </div>
      <template x-for="sc in subCats(form.category)" :key="sc.name">
        <div class="subcat-big-chip"
          :class="form.subcat===sc.name?(form.type==='income'?'sel-i':'sel-e'):''"
          @click="form.subcat=sc.name;showSubcatSheet=false">
          <span class="sbc-emoji" x-text="sc.emoji||'›'"></span>
          <span x-text="sc.name"></span>
        </div>
      </template>
    </div>
    <div x-show="subCats(form.category).length===0" style="color:var(--tx3);font-size:12px;text-align:center;padding:14px 0">
      Danh mục này không có mục con
    </div>
  </div>

</div><!-- /screen Add TX -->

<!-- ══════════════════════════════════
     SCREEN: CATEGORY MANAGER
══════════════════════════════════ -->
<div class="screen" :class="showCatScreen?'open':''">
  <div class="scr-hdr">
    <div class="close-btn" @click="showCatScreen=false">✕</div>
    <div class="scr-title">📂 Quản lý danh mục</div>
    <button class="btn-sm" style="font-size:11px;padding:7px 12px" @click="showAddCatSheet=true">＋ Thêm</button>
  </div>
  <div class="cat-type-tab">
    <button class="ctt" :class="catMgrType==='expense'?'ae':''" @click="catMgrType='expense'">💸 Chi phí</button>
    <button class="ctt" :class="catMgrType==='income'?'ai':''" @click="catMgrType='income'">💰 Thu nhập</button>
  </div>
  <div style="flex:1;overflow-y:auto;padding-bottom:24px;min-height:0">
    <template x-for="parent in catsByType(catMgrType)" :key="parent.name">
      <div class="cat-parent-group">
        <div class="cpg-header" @click="toggleCatGroup(parent.name)">
          <div class="cpg-emoji" :style="'background:'+parent.color+'22'" x-text="parent.emoji"></div>
          <div class="cpg-name" x-text="parent.name"></div>
          <span class="cpg-count" x-text="(parent.children||[]).length+' con'"></span>
          <span class="cpg-toggle" :class="openCatGroups.includes(parent.name)?'open':''" >›</span>
          <button class="cpg-del" @click.stop="deleteCat(parent.name,catMgrType)">🗑</button>
        </div>
        <div class="cpg-children" x-show="openCatGroups.includes(parent.name)">
          <template x-if="(parent.children||[]).length===0"><div style="padding:9px 14px;font-size:11px;color:var(--tx3)">Chưa có danh mục con</div></template>
          <template x-for="child in (parent.children||[])" :key="child.name">
            <div class="cpg-child">
              <div class="cpc-dot" :style="'background:'+parent.color"></div>
              <span class="cpc-name" x-text="(child.emoji||'›')+' '+child.name"></span>
              <button class="cpc-del" @click="deleteSubCat(parent.name,child.name,catMgrType)">🗑</button>
            </div>
          </template>
          <div class="cpg-add-child" @click="openAddSubCat(parent.name,catMgrType)">＋ Thêm danh mục con</div>
        </div>
      </div>
    </template>
    <template x-if="catsByType(catMgrType).length===0"><div style="padding:36px 20px;text-align:center;color:var(--tx3);font-size:13px">Chưa có danh mục nào</div></template>
  </div>
</div>

<!-- ══════════════════════════════════
     SCREEN: FIXED MONTHLY
══════════════════════════════════ -->
<div class="screen" :class="showFixedScreen?'open':''">
  <div class="scr-hdr">
    <div class="close-btn" @click="showFixedScreen=false">✕</div>
    <div class="scr-title">💼 Cố định hàng tháng</div>
    <button class="btn-sm" style="font-size:11px;padding:7px 12px" @click="showAddFixedSheet=true">＋ Thêm</button>
  </div>
  <div style="flex:1;overflow-y:auto;min-height:0">
    <div class="fxd-summary">
      <div class="fxd-card fxd-inc"><div class="fxd-lbl">📥 Thu cố định</div><div class="fxd-val" x-text="fmtS(fixedIncome)"></div><div class="fxd-cnt" x-text="fixedItems.filter(f=>f.type==='income').length+' khoản'"></div></div>
      <div class="fxd-card fxd-exp"><div class="fxd-lbl">📤 Chi cố định</div><div class="fxd-val" x-text="fmtS(fixedExpense)"></div><div class="fxd-cnt" x-text="fixedItems.filter(f=>f.type==='expense').length+' khoản'"></div></div>
    </div>
    <div class="fxd-net-bar"><span class="fxd-net-label">💵 Dòng tiền ròng / tháng</span><span class="fxd-net-val" :style="fixedIncome-fixedExpense>=0?'color:var(--g)':'color:var(--r)'" x-text="(fixedIncome-fixedExpense>=0?'+':'')+fmtS(fixedIncome-fixedExpense)"></span></div>
    <div style="padding:10px 16px">
      <button class="btn-apply" @click="applyFixedMonth()" style="background:linear-gradient(135deg,var(--p),var(--b))">⚡ Áp dụng cho Tháng <span x-text="curM+'/'+curY"></span></button>
    </div>
    <div class="fxd-list">
      <template x-if="fixedItems.length===0"><div style="padding:36px 20px;text-align:center;color:var(--tx3)"><div style="font-size:40px;opacity:.25;margin-bottom:10px">📅</div><div style="font-size:13px;font-weight:700;color:var(--tx2)">Chưa có khoản cố định nào</div><div style="font-size:11px;margin-top:4px">Nhấn ＋ Thêm để bắt đầu</div></div></template>
      <template x-for="fi in fixedItemsSorted" :key="fi.id">
        <div class="fxd-item">
          <div class="fxd-item-day" x-text="fi.day||'?'"></div>
          <div class="fxd-item-ico" :style="'background:'+catColor(fi.category)+'22'" x-text="catEmoji(fi.category)"></div>
          <div class="fxd-item-info"><div class="fxd-item-name" x-text="fi.name"></div><div class="fxd-item-cat" x-text="fi.category+(fi.acc_id?' · '+accName(fi.acc_id):'')"></div></div>
          <div class="fxd-item-amt" :style="fi.type==='income'?'color:var(--g)':'color:var(--r)'" x-text="(fi.type==='income'?'+':'-')+fmtS(fi.amount)"></div>
          <button class="fxd-item-del" @click="deleteFixed(fi.id)">🗑</button>
        </div>
      </template>
    </div>
    <div class="pb"></div>
  </div>
</div>

<!-- ══════════════════════════════════
     SCREEN: ACCOUNT MANAGER
══════════════════════════════════ -->
<div class="screen" :class="showAccMgrScreen?'open':''">
  <div class="scr-hdr">
    <div class="close-btn" @click="showAccMgrScreen=false">✕</div>
    <div class="scr-title">💳 Quản lý ví & Tài khoản</div>
    <button class="btn-sm" style="font-size:11px;padding:7px 12px" @click="editingAcc=null;aForm={name:'',type:'cash',balance:''};showAccSheet=true">＋ Thêm</button>
  </div>
  <!-- Tóm tắt -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;padding:12px 16px 0;flex-shrink:0">
    <div style="background:rgba(0,196,140,.1);border:1px solid rgba(0,196,140,.2);border-radius:14px;padding:12px">
      <div style="font-size:9px;font-weight:700;color:var(--g);margin-bottom:3px;text-transform:uppercase;letter-spacing:.06em">🏦 Tổng tài sản</div>
      <div style="font-size:18px;font-weight:900;color:var(--g)" x-text="fmtS(ov.total_cash)"></div>
    </div>
    <div style="background:rgba(76,154,255,.08);border:1px solid rgba(76,154,255,.2);border-radius:14px;padding:12px">
      <div style="font-size:9px;font-weight:700;color:var(--b);margin-bottom:3px;text-transform:uppercase;letter-spacing:.06em">💳 Số ví</div>
      <div style="font-size:18px;font-weight:900;color:var(--b)" x-text="accounts.length+' ví'"></div>
    </div>
  </div>
  <div style="flex:1;overflow-y:auto;min-height:0;padding:12px 16px 0">
    <template x-if="accounts.length===0">
      <div style="padding:40px 20px;text-align:center;color:var(--tx3)">
        <div style="font-size:48px;opacity:.2;margin-bottom:12px">👛</div>
        <div style="font-size:14px;font-weight:700;color:var(--tx2)">Chưa có ví nào</div>
        <div style="font-size:12px;margin-top:6px">Nhấn ＋ Thêm để tạo ví đầu tiên</div>
      </div>
    </template>
    <div style="background:var(--bg2);border:1px solid var(--br);border-radius:18px;overflow:hidden">
      <template x-for="(a,i) in accounts" :key="a.id">
        <div class="acc-item">
          <div class="acc-ico" :style="'background:'+['#00c48c','#4c9aff','#7b5cfa','#ff9f43','#ff4d6d','#1de9b6'][i%6]+'22'">
            <span x-text="a.type==='bank'?'🏦':(a.type==='e-wallet'?'📱':'👛')"></span>
          </div>
          <div class="acc-info">
            <div class="acc-name" x-text="a.name"></div>
            <div class="acc-type" x-text="a.type==='bank'?'🏦 Ngân hàng':a.type==='e-wallet'?'📱 Ví điện tử':a.type==='other'?'📦 Khác':'👛 Tiền mặt'"></div>
          </div>
          <div class="acc-bal" x-text="fmtS(a.balance)"></div>
          <div class="acc-actions">
            <button class="acc-act-btn edit" @click="openEditAcc(a)">✏️</button>
            <button class="acc-act-btn del" @click="deleteAcc(a)">🗑</button>
          </div>
        </div>
      </template>
    </div>
    <div style="margin-top:12px">
      <button class="btn-p" @click="editingAcc=null;aForm={name:'',type:'cash',balance:''};showAccSheet=true">＋ Thêm ví mới</button>
    </div>
    <div class="pb"></div>
  </div>
</div>

<!-- ══════════════════════════════════
     SHEETS (BOTTOM MODALS)
══════════════════════════════════ -->

<!-- Add / Edit Account Sheet -->
<div class="overlay" x-show="showAccSheet" @click.self="showAccSheet=false;editingAcc=null" style="display:none">
  <div class="sheet">
    <div class="sh-handle"></div>
    <div class="sh-title" x-text="editingAcc?'✏️ Sửa ví: '+editingAcc.name:'➕ Thêm ví / Tài khoản'"></div>
    <div class="fl">Tên ví</div>
    <input class="fi-input" placeholder="Techcombank, Tiền mặt..." x-model="aForm.name">
    <div class="fl">Loại</div>
    <select class="fs" x-model="aForm.type">
      <option value="cash">👛 Tiền mặt</option>
      <option value="bank">🏦 Ngân hàng</option>
      <option value="e-wallet">📱 Ví điện tử</option>
      <option value="other">📦 Khác</option>
    </select>
    <div class="fl" x-text="editingAcc?'Cập nhật số dư (₫)':'Số dư ban đầu (₫)'"></div>
    <input type="number" class="fi-input" placeholder="0" x-model="aForm.balance" style="font-size:20px;font-weight:800;color:var(--g)">
    <template x-if="editingAcc">
      <div style="background:rgba(255,159,67,.08);border:1px solid rgba(255,159,67,.25);border-radius:12px;padding:10px 12px;margin-bottom:12px;font-size:11px;color:var(--o);">⚠️ Số dư sẽ được cập nhật trực tiếp, không tạo giao dịch mới</div>
    </template>
    <button class="btn-p" @click="editingAcc?updateAcc():saveAcc()" x-text="editingAcc?'Cập nhật ví ✅':'Tạo ví ngay 🎉'"></button>
    <template x-if="editingAcc">
      <button @click="editingAcc=null;showAccSheet=false" style="width:100%;background:none;border:1px solid var(--br);border-radius:12px;padding:10px;color:var(--tx3);font-size:13px;cursor:pointer;font-family:inherit;margin-top:6px">Huỷ</button>
    </template>
  </div>
</div>

<!-- Add Debt Sheet -->
<div class="overlay" x-show="showDebtSheet" @click.self="showDebtSheet=false" style="display:none">
  <div class="sheet">
    <div class="sh-handle"></div><div class="sh-title">📝 Ghi nhận nợ vay</div>
    <div class="fl">Tên người</div><input class="fi-input" placeholder="Họ tên..." x-model="dForm.name">
    <div class="fl">Loại</div><select class="fs" x-model="dForm.type"><option value="lend">📤 Tôi cho vay</option><option value="borrow">📥 Tôi đi vay</option></select>
    <div class="f-row">
      <div><div class="fl">Số tiền (₫)</div><input type="number" class="fi-input" placeholder="0" x-model="dForm.amount" style="color:var(--g);font-weight:700"></div>
      <div><div class="fl">Hạn trả</div><input type="date" class="fi-input" x-model="dForm.due"></div>
    </div>
    <div class="fl">Ghi chú</div><input class="fi-input" placeholder="Lý do..." x-model="dForm.note">
    <button class="btn-p" @click="saveDebt()">Ghi nhận 📋</button>
  </div>
</div>

<!-- Budget Sheet -->
<div class="overlay" x-show="showBudgetSheet" @click.self="showBudgetSheet=false" style="display:none">
  <div class="sheet">
    <div class="sh-handle"></div><div class="sh-title">💰 Ngân sách tháng</div>
    <template x-for="cat in budgetCats" :key="cat">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:9px">
        <div style="width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0" :style="'background:'+catColor(cat)+'22'" x-text="catEmoji(cat)"></div>
        <span style="font-size:13px;font-weight:600;color:var(--tx);flex:1" x-text="cat"></span>
        <input type="number" placeholder="Hạn mức..." style="width:105px;background:var(--bg3);border:1.5px solid var(--br);border-radius:10px;padding:7px 9px;color:var(--g);font-size:13px;font-weight:700;font-family:inherit;outline:none;text-align:right" :value="getBudget(cat)" @change="setBudget(cat,$event.target.value)">
      </div>
    </template>
    <button class="btn-p" @click="showBudgetSheet=false">Lưu ngân sách ✅</button>
  </div>
</div>

<!-- Goals Sheet -->
<div class="overlay" x-show="showGoalSheet" @click.self="showGoalSheet=false" style="display:none">
  <div class="sheet">
    <div class="sh-handle"></div><div class="sh-title">🎯 Mục tiêu tiết kiệm</div>
    <div class="fl">Tên mục tiêu</div><input class="fi-input" placeholder="Mua xe, Du lịch Nhật..." x-model="gForm.name">
    <div style="display:flex;gap:5px;margin-bottom:11px;flex-wrap:wrap">
      <template x-for="ico in goalIcons" :key="ico"><div @click="gForm.icon=ico" style="width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:17px;cursor:pointer;transition:all .15s" :style="gForm.icon===ico?'background:rgba(0,196,140,.2);transform:scale(1.15)':'background:var(--bg3)'"><span x-text="ico"></span></div></template>
    </div>
    <div class="f-row">
      <div><div class="fl">Mục tiêu (₫)</div><input type="number" class="fi-input" placeholder="0" x-model="gForm.target" style="color:var(--g);font-weight:700"></div>
      <div><div class="fl">Đã có (₫)</div><input type="number" class="fi-input" placeholder="0" x-model="gForm.saved" style="color:var(--b)"></div>
    </div>
    <div class="fl">Hạn chót</div><input type="date" class="fi-input" x-model="gForm.deadline">
    <button class="btn-p" @click="saveGoal()">Tạo mục tiêu 🎯</button>
    <template x-if="goals.length>0">
      <div style="margin-top:12px;border-top:1px solid var(--br);padding-top:12px">
        <div class="fl">Hiện có</div>
        <template x-for="(g,i) in goals" :key="i">
          <div style="display:flex;align-items:center;justify-content:space-between;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.04)">
            <span><span x-text="g.icon"></span> <span style="font-size:13px;font-weight:600;color:var(--tx)" x-text="g.name"></span></span>
            <button @click="removeGoal(i)" style="background:none;border:none;color:var(--tx3);cursor:pointer;font-size:13px">🗑</button>
          </div>
        </template>
      </div>
    </template>
  </div>
</div>

<!-- Add Category Sheet -->
<div class="overlay" x-show="showAddCatSheet" @click.self="showAddCatSheet=false" style="display:none">
  <div class="sheet">
    <div class="sh-handle"></div><div class="sh-title">📂 Thêm danh mục</div>
    <div class="fl">Loại</div><select class="fs" x-model="newCat.type"><option value="expense">💸 Chi phí</option><option value="income">💰 Thu nhập</option></select>
    <div class="fl">Tên danh mục</div><input class="fi-input" placeholder="VD: Học tiếng Anh..." x-model="newCat.name">
    <div class="fl">Chọn icon</div>
    <div class="emoji-picker"><template x-for="e in emojiList" :key="e"><div class="ep-item" :class="newCat.emoji===e?'sel':''" @click="newCat.emoji=e" x-text="e"></div></template></div>
    <div class="fl">Chọn màu</div>
    <div class="color-picker"><template x-for="c in colorList" :key="c"><div class="cp-dot" :class="newCat.color===c?'sel':''" :style="'background:'+c" @click="newCat.color=c"></div></template></div>
    <button class="btn-p" @click="addCategory()">Thêm danh mục ✅</button>
  </div>
</div>

<!-- Add Subcategory Sheet -->
<div class="overlay" x-show="showAddSubCatSheet" @click.self="showAddSubCatSheet=false" style="display:none">
  <div class="sheet">
    <div class="sh-handle"></div>
    <div class="sh-title">➕ Danh mục con: "<span x-text="newSubCat.parent"></span>"</div>
    <div class="fl">Tên danh mục con</div><input class="fi-input" placeholder="VD: Cơm trưa, Học phí..." x-model="newSubCat.name">
    <div class="fl">Icon (tuỳ chọn)</div>
    <div class="emoji-picker"><template x-for="e in emojiList" :key="e"><div class="ep-item" :class="newSubCat.emoji===e?'sel':''" @click="newSubCat.emoji=e" x-text="e"></div></template></div>
    <button class="btn-p" @click="addSubCategory()">Thêm danh mục con ✅</button>
  </div>
</div>

<!-- Add Fixed Monthly Sheet -->
<div class="overlay" x-show="showAddFixedSheet" @click.self="showAddFixedSheet=false" style="display:none">
  <div class="sheet">
    <div class="sh-handle"></div><div class="sh-title">📅 Thêm khoản cố định</div>
    <div class="f-row" style="margin-bottom:11px">
      <div @click="fxdForm.type='income'" style="border-radius:12px;padding:11px;cursor:pointer;text-align:center;border:2px solid;transition:all .18s" :style="fxdForm.type==='income'?'border-color:var(--g);background:rgba(0,196,140,.1)':'border-color:var(--br);background:var(--bg3)'"><div style="font-size:18px;margin-bottom:3px">💰</div><div style="font-size:11px;font-weight:700" :style="fxdForm.type==='income'?'color:var(--g)':''">Thu nhập</div></div>
      <div @click="fxdForm.type='expense'" style="border-radius:12px;padding:11px;cursor:pointer;text-align:center;border:2px solid;transition:all .18s" :style="fxdForm.type==='expense'?'border-color:var(--r);background:rgba(255,77,109,.1)':'border-color:var(--br);background:var(--bg3)'"><div style="font-size:18px;margin-bottom:3px">💸</div><div style="font-size:11px;font-weight:700" :style="fxdForm.type==='expense'?'color:var(--r)':''">Chi phí</div></div>
    </div>
    <div class="fl">Tên khoản</div><input class="fi-input" placeholder="VD: Lương tháng, Tiền thuê nhà..." x-model="fxdForm.name">
    <div class="f-row">
      <div><div class="fl">Số tiền (₫)</div><input type="number" class="fi-input" placeholder="0" x-model="fxdForm.amount" :style="'color:'+(fxdForm.type==='income'?'var(--g)':'var(--r)')+';font-weight:700'"></div>
      <div><div class="fl">Ngày/tháng (1-31)</div><input type="number" class="fi-input" placeholder="1" x-model="fxdForm.day" min="1" max="31"></div>
    </div>
    <div class="fl">Danh mục</div>
    <select class="fs" x-model="fxdForm.category">
      <option value="">Chọn danh mục...</option>
      <template x-for="cat in allCats()" :key="cat.name"><option :value="cat.name" x-text="cat.emoji+' '+cat.name"></option></template>
    </select>
    <div class="fl">Ví (tuỳ chọn)</div>
    <select class="fs" x-model="fxdForm.acc_id"><option value="">Không chỉ định</option><template x-for="a in accounts" :key="a.id"><option :value="a.id" x-text="a.name"></option></template></select>
    <button class="btn-p" @click="saveFixed()" :style="'background:linear-gradient(135deg,'+(fxdForm.type==='income'?'var(--gl),var(--g)':'var(--rl),var(--r)')+')'">Thêm khoản cố định 📅</button>
  </div>
</div>

<!-- Invest Sheet -->
<div class="overlay" x-show="showInvSheet" @click.self="showInvSheet=false" style="display:none">
  <div class="sheet">
    <div class="sh-handle"></div><div class="sh-title">📈 Danh mục đầu tư</div>
    <div class="fl">Loại</div><select class="fs" x-model="iForm.type"><option value="crypto">🪙 Crypto</option><option value="stock">📊 Cổ phiếu</option></select>
    <div class="fl">Mã tài sản</div><input class="fi-input" placeholder="BTC, ETH, VNM..." x-model="iForm.sym" style="text-transform:uppercase;font-weight:700;font-size:17px">
    <div class="f-row">
      <div><div class="fl">Số lượng</div><input type="number" class="fi-input" placeholder="0" x-model="iForm.qty" step="any"></div>
      <div><div class="fl">Giá mua (₫)</div><input type="number" class="fi-input" placeholder="0" x-model="iForm.bp"></div>
    </div>
    <button class="btn-p" @click="saveInv()">Thêm tài sản 🚀</button>
    <div style="margin-top:12px;border-top:1px solid var(--br);padding-top:12px">
      <template x-for="inv in investments" :key="inv.id">
        <div class="inv-item" style="margin:0 0 7px">
          <div class="inv-badge" :class="inv.type==='crypto'?'ib-c':'ib-s'" x-text="inv.symbol"></div>
          <div class="inv-info"><div class="inv-name" x-text="inv.symbol"><span class="inv-tag" x-text="inv.type==='crypto'?'Crypto':'Stock'"></span></div><div class="inv-sub">SL: <b x-text="inv.quantity"></b> · Giá mua: <span x-text="fmtS(inv.buy_price)"></span></div></div>
          <div class="inv-right"><div class="inv-val" x-text="fmtS(inv.quantity*inv.current_price)"></div><div class="inv-pct" :class="(inv.current_price-inv.buy_price)>=0?'ip-p':'ip-n'" x-text="((inv.current_price-inv.buy_price)>=0?'▲ ':' ▼')+(((inv.current_price-inv.buy_price)/inv.buy_price)*100).toFixed(1)+'%'"></div><button class="inv-del" @click="deleteInv(inv.id)">🗑</button></div>
        </div>
      </template>
      <template x-if="investments.length>0">
        <button class="btn-p" style="background:var(--bg3);color:var(--g);box-shadow:none;border:1px solid rgba(0,196,140,.3);margin-top:8px" @click="updateRates()">🔄 Cập nhật tỷ giá</button>
      </template>
    </div>
  </div>
</div>

</div><!-- /#app -->

<script>
const CSRF='{{ csrf_token() }}';

const DEF_CATS_E=[
  /* ═══ NHÀ THỜ & TÂM LINH ═══ */
  {name:'Nhà Thờ',emoji:'⛪',color:'#a29bfe',children:[
    {name:'Phần Mười',emoji:'✝️'},{name:'Dâng Hiến',emoji:'🙏'},
    {name:'Đóng Góp HT',emoji:'💒'},{name:'Sự Kiện NT',emoji:'🎺'},
    {name:'Từ Thiện NT',emoji:'❤️‍🔥'},{name:'Tài Liệu Tâm Linh',emoji:'📖'},
    {name:'Trại / Retreat',emoji:'🕊️'},{name:'Âm Nhạc Thờ Phượng',emoji:'🎵'},
  ]},
  /* ═══ GIA ĐÌNH ═══ */
  {name:'Gia Đình',emoji:'👨‍👩‍👧‍👦',color:'#fd79a8',children:[
    {name:'Ba Mẹ Ruột',emoji:'👴'},{name:'Ba Mẹ Vợ / Chồng',emoji:'👵'},
    {name:'Con Cái',emoji:'👶'},{name:'Anh Chị Em',emoji:'🤝'},
    {name:'Ông Bà',emoji:'🧓'},{name:'Quà Tặng GĐ',emoji:'🎁'},
    {name:'Sinh Nhật',emoji:'🎂'},{name:'Du Lịch GĐ',emoji:'🏖️'},
    {name:'Thuốc / Sức Khoẻ GĐ',emoji:'💊'},{name:'Học Phí Con',emoji:'📚'},
    {name:'Tiệc Gia Đình',emoji:'🥳'},
  ]},
  /* ═══ XÃ HỘI / CỘNG ĐỒNG ═══ */
  {name:'Xã Hội',emoji:'🤲',color:'#00b894',children:[
    {name:'Giúp Đỡ Người Khó',emoji:'💝'},{name:'Thăm Người Bệnh',emoji:'🏥'},
    {name:'Ủng Hộ Thiên Tai',emoji:'🌊'},{name:'Quà Cộng Đồng',emoji:'🎀'},
    {name:'Tình Nguyện',emoji:'🙌'},{name:'Thăm Người Già',emoji:'👨‍🦳'},
    {name:'Hỗ Trợ Trẻ Em',emoji:'🧒'},{name:'Quyên Góp',emoji:'📦'},
  ]},
  /* ═══ ĐẦU TƯ BẢN THÂN ═══ */
  {name:'Bản Thân',emoji:'🚀',color:'#fdcb6e',children:[
    {name:'Khóa Học Online',emoji:'🎓'},{name:'Sách & Tài Liệu',emoji:'📚'},
    {name:'Hội Thảo / Workshop',emoji:'🎤'},{name:'Chứng Chỉ / Thi Cử',emoji:'📜'},
    {name:'Phần Mềm Học',emoji:'💻'},{name:'Coaching / Mentoring',emoji:'🧠'},
    {name:'Gym / Thể Dục',emoji:'🏋️'},{name:'Thiền / Yoga',emoji:'🧘'},
    {name:'Khám Sức Khoẻ',emoji:'🩺'},{name:'Spa / Thư Giãn',emoji:'💆'},
  ]},
  /* ═══ ĂN UỐNG ═══ */
  {name:'Ăn Uống',emoji:'🍜',color:'#ff6b6b',children:[
    {name:'Cơm Trưa',emoji:'🍱'},{name:'Cà Phê',emoji:'☕'},
    {name:'Ăn Tối',emoji:'🍽️'},{name:'Ăn Vặt',emoji:'🍕'},
    {name:'Nước Uống',emoji:'🥤'},{name:'Đặt Đồ Ăn Online',emoji:'📱'},
    {name:'Tiệc / Nhà Hàng',emoji:'🥂'},{name:'Bữa Sáng',emoji:'🥐'},
  ]},
  /* ═══ MUA SẮM ═══ */
  {name:'Mua Sắm',emoji:'🛒',color:'#ff9f43',children:[
    {name:'Quần Áo',emoji:'👕'},{name:'Đồ Dùng Nhà',emoji:'🏠'},
    {name:'Mỹ Phẩm',emoji:'💄'},{name:'Vệ Sinh Cá Nhân',emoji:'🧴'},
    {name:'Quà Bạn Bè',emoji:'🎁'},{name:'Sửa Chữa Đồ',emoji:'🔧'},
    {name:'Điện Tử',emoji:'📱'},{name:'Thực Phẩm',emoji:'🥦'},
  ]},
  /* ═══ DI CHUYỂN ═══ */
  {name:'Di Chuyển',emoji:'🚗',color:'#54a0ff',children:[
    {name:'Xăng Xe',emoji:'⛽'},{name:'Grab / Xe Ôm',emoji:'🛵'},
    {name:'Taxi',emoji:'🚕'},{name:'Xe Buýt',emoji:'🚌'},
    {name:'Máy Bay',emoji:'✈️'},{name:'Đỗ Xe',emoji:'🅿️'},
    {name:'Sửa Xe',emoji:'🔧'},{name:'Phí Cầu Đường',emoji:'🛣️'},
  ]},
  /* ═══ NHÀ Ở ═══ */
  {name:'Nhà Ở',emoji:'🏠',color:'#5f27cd',children:[
    {name:'Tiền Thuê',emoji:'🔑'},{name:'Điện Nước',emoji:'💡'},
    {name:'Internet / TV',emoji:'🌐'},{name:'Sửa Chữa Nhà',emoji:'🔨'},
    {name:'Nội Thất',emoji:'🛋️'},{name:'Vệ Sinh Nhà',emoji:'🧹'},
    {name:'Bảo Hiểm Nhà',emoji:'🏡'},{name:'Thuế Đất',emoji:'📋'},
  ]},
  /* ═══ SỨC KHOẺ ═══ */
  {name:'Sức Khoẻ',emoji:'💊',color:'#00d2d3',children:[
    {name:'Thuốc',emoji:'💊'},{name:'Khám Bệnh',emoji:'🏥'},
    {name:'Tiêm / Vaccine',emoji:'💉'},{name:'Nha Khoa',emoji:'🦷'},
    {name:'Mắt Kính',emoji:'👁️'},{name:'Bảo Hiểm Y Tế',emoji:'🛡️'},
  ]},
  /* ═══ GIẢI TRÍ ═══ */
  {name:'Giải Trí',emoji:'🎮',color:'#6c5ce7',children:[
    {name:'Phim Ảnh',emoji:'🎬'},{name:'Âm Nhạc',emoji:'🎵'},
    {name:'Game',emoji:'🎮'},{name:'Streaming',emoji:'📺'},
    {name:'Du Lịch',emoji:'✈️'},{name:'Bơi Lội',emoji:'🏊'},
    {name:'Thể Thao',emoji:'⚽'},{name:'Sở Thích',emoji:'🎨'},
  ]},
  /* ═══ TÀI CHÍNH ═══ */
  {name:'Tài Chính',emoji:'💳',color:'#00c48c',children:[
    {name:'Tiết Kiệm',emoji:'🐷'},{name:'Trả Nợ',emoji:'💸'},
    {name:'Phí Ngân Hàng',emoji:'🏦'},{name:'Bảo Hiểm',emoji:'🛡️'},
    {name:'Đầu Tư CK',emoji:'📈'},{name:'Thuế TNCN',emoji:'📋'},
  ]},
  /* ═══ TIỆN ÍCH ═══ */
  {name:'Tiện Ích',emoji:'💡',color:'#ffeaa7',children:[
    {name:'Điện Thoại',emoji:'📱'},{name:'Internet',emoji:'🌐'},
    {name:'Điện',emoji:'💡'},{name:'Nước',emoji:'💧'},
  ]},
  {name:'Khác',emoji:'📦',color:'#636e72',children:[
    {name:'Phí Dịch Vụ',emoji:'🔧'},{name:'Chi Không Rõ',emoji:'❓'},
  ]},
];
const DEF_CATS_I=[
  /* ═══ LƯƠNG ═══ */
  {name:'Lương',emoji:'💰',color:'#00c48c',children:[
    {name:'Lương Conasi',emoji:'🏢'},{name:'Lương Bách Vạn Thành',emoji:'🏗️'},
    {name:'Lương Bình An Thịnh',emoji:'🏠'},{name:'Lương Tú Hưng Điền',emoji:'🌿'},
    {name:'Lương GrandDaisy',emoji:'🌸'},{name:'Lương Thái Bảo Minh',emoji:'💎'},
    {name:'Lương Cơ Bản',emoji:'💵'},{name:'Thưởng',emoji:'🎁'},
    {name:'Làm Thêm Giờ',emoji:'⏰'},{name:'Hoa Hồng',emoji:'📊'},
    {name:'Phụ Cấp',emoji:'🏷️'},{name:'Lương Tháng 13',emoji:'🎊'},
  ]},
  /* ═══ KINH DOANH ═══ */
  {name:'Kinh Doanh',emoji:'💼',color:'#54a0ff',children:[
    {name:'Bán Hàng',emoji:'🛍️'},{name:'Dịch Vụ',emoji:'🔧'},
    {name:'Hợp Đồng',emoji:'🤝'},{name:'Tư Vấn',emoji:'💡'},
    {name:'Freelance',emoji:'💻'},{name:'Sản Phẩm',emoji:'📦'},
  ]},
  /* ═══ ĐẦU TƯ ═══ */
  {name:'Đầu Tư',emoji:'📈',color:'#00e5a0',children:[
    {name:'Cổ Tức',emoji:'📊'},{name:'Lãi Tiết Kiệm',emoji:'🏦'},
    {name:'Cho Thuê TS',emoji:'🏠'},{name:'Lãi Chứng Khoán',emoji:'📈'},
    {name:'Crypto',emoji:'₿'},{name:'Lãi Trái Phiếu',emoji:'📜'},
  ]},
  /* ═══ THU NHẬP KHÁC ═══ */
  {name:'Thu Nhập Khác',emoji:'💝',color:'#fd79a8',children:[
    {name:'Quà Tiền Mặt',emoji:'🎁'},{name:'Hoàn Tiền',emoji:'🔄'},
    {name:'Bán Đồ Cũ',emoji:'🛒'},{name:'Cashback',emoji:'💳'},
    {name:'Hỗ Trợ GĐ',emoji:'👨‍👩‍👧‍👦'},{name:'Trợ Cấp',emoji:'🏛️'},
  ]},
  /* ═══ NHÀ THỜ ═══ */
  {name:'Phúc Lợi NT',emoji:'⛪',color:'#a29bfe',children:[
    {name:'Quà Hội Thánh',emoji:'🎁'},{name:'Hỗ Trợ Mục Vụ',emoji:'🙏'},
    {name:'Học Bổng NT',emoji:'🎓'},
  ]},
];
const CAT_VERSION=7; // bump this to force-reset all users to new categories
function getCats(t){
  const ver=parseInt(localStorage.getItem('mt_cat_ver')||'0');
  // Auto-migrate: if version is old, clear old cats so new defaults load
  if(ver<CAT_VERSION){
    localStorage.removeItem('mt_cats_expense');
    localStorage.removeItem('mt_cats_income');
    localStorage.setItem('mt_cat_ver',CAT_VERSION);
  }
  return JSON.parse(localStorage.getItem('mt_cats_'+t)||'null')||(t==='expense'?DEF_CATS_E:DEF_CATS_I)
}
function saveCats(t,a){localStorage.setItem('mt_cats_'+t,JSON.stringify(a));localStorage.setItem('mt_cat_ver',CAT_VERSION)}
function catOf(n){const all=[...getCats('expense'),...getCats('income')];for(const p of all){if(p.name===n)return p;for(const c of(p.children||[])){if(c.name===n)return{...c,color:p.color}}}return{emoji:'💸',color:'#636e72'}}

function app(){return{
  loading:false,isOnline:navigator.onLine,
  tab:'home',curY:new Date().getFullYear(),curM:new Date().getMonth()+1,
  uname:'{{ Auth::user()->name ?? "Bạn" }}',
  accounts:[],transactions:[],debts:[],investments:[],
  ov:{net_worth:0,total_cash:0,total_investment:0,total_lend:0,total_borrow:0},
  stats:{},cur:{USD:25450,EUR:27200,JPY:170,GBP:32000,updated:''},
  search:'',txFilter:'all',editingTx:null,
  showAddScreen:false,showCatScreen:false,showFixedScreen:false,showAccMgrScreen:false,
  showAccSheet:false,showDebtSheet:false,showInvSheet:false,showBudgetSheet:false,showGoalSheet:false,
  showAddCatSheet:false,showAddSubCatSheet:false,showAddFixedSheet:false,
  editingAcc:null,showMonthExpand:false,showDateDetail:false,customMonth:'',
  txView:'list',calSelectedDay:'',showNumpad:false,showSubcatSheet:false,
  numpad:'',
  form:{type:'expense',acc:'',toAcc:'',category:'',subcat:'',date:new Date().toISOString().split('T')[0],note:'',recur:false,period:'monthly',month:new Date().toISOString().slice(0,7)},
  aForm:{name:'',type:'cash',balance:''},
  dForm:{name:'',type:'lend',amount:'',due:'',note:''},
  iForm:{sym:'',type:'crypto',qty:'',bp:''},
  gForm:{name:'',icon:'🎯',target:'',saved:'',deadline:''},
  fxdForm:{type:'expense',name:'',amount:'',day:1,category:'',acc_id:''},
  newCat:{type:'expense',name:'',emoji:'📦',color:'#00c48c'},
  newSubCat:{parent:'',type:'expense',name:'',emoji:''},
  catMgrType:'expense',openCatGroups:[],
  goalIcons:['🚗','🏠','✈️','💍','📱','💻','🎓','🏖️','🎯','💰','🏋️','🌏'],
  budgetCats:['Nhà Thờ','Gia Đình','Xã Hội','Bản Thân','Ăn Uống','Mua Sắm','Di Chuyển'],
  emojiList:['🍜','🛒','🚗','🏠','💊','🎮','📚','💡','🎁','🐾','📈','💰','💼','🎓','✈️','☕','🍕','🍱','🛵','⛽','🔑','💻','📱','👕','🏋️','🎬','🛍️','💝','🏦','🔄','💵','🌐','⚽','🌸','🎪','🍺','📦','⭐','🔥','💎','🚀','🌿','🔧'],
  colorList:['#00c48c','#ff4d6d','#4c9aff','#7b5cfa','#ff9f43','#ffd32a','#1de9b6','#00d2d3','#ff9ff3','#a29bfe','#fdcb6e','#e17055','#786fa6','#54a0ff','#636e72','#5f27cd'],
  charts:{week:null,trend:null,donut:null},
  toast:{show:false,msg:'',type:'success'},

  get mLabel(){return`Tháng ${this.curM}/${this.curY}`},

  // Month chips: -2, -1, current, +1, +2, +3
  get monthChips(){
    const now=new Date();const chips=[];
    for(let i=-1;i<=3;i++){
      const d=new Date(now.getFullYear(),now.getMonth()+i,1);
      const y=d.getFullYear(),m=d.getMonth()+1;
      const key=y+'-'+String(m).padStart(2,'0');
      chips.push({key,year:y,month:m,isCurrent:i===0,isPrev:i===-1,isNext:i===1});
    }
    return chips;
  },
  // All months: 12 months back to 6 months ahead
  get allMonthOptions(){
    const now=new Date();const opts=[];
    for(let i=-12;i<=6;i++){
      const d=new Date(now.getFullYear(),now.getMonth()+i,1);
      const y=d.getFullYear(),m=d.getMonth()+1;
      const key=y+'-'+String(m).padStart(2,'0');
      opts.push({key,year:y,month:m});
    }
    return opts;
  },

  get mStats(){const f=this.filtered;return{income:f.filter(t=>t.type==='income').reduce((s,t)=>s+parseFloat(t.amount),0),expense:f.filter(t=>t.type==='expense').reduce((s,t)=>s+parseFloat(t.amount),0)}},
  get filtered(){return this.transactions.filter(t=>{const d=new Date(t.transaction_date);return d.getMonth()+1===this.curM&&d.getFullYear()===this.curY})},
  get displayedTxs(){let l=this.filtered;if(this.txFilter!=='all')l=l.filter(t=>t.type===this.txFilter);if(this.search.trim()){const s=this.search.toLowerCase();l=l.filter(t=>(t.category||'').toLowerCase().includes(s)||(t.note||'').toLowerCase().includes(s))}return l},
  get displayedGroups(){return this.groupTxs(this.displayedTxs)},
  get grouped(){return this.groupTxs(this.filtered)},

  // ── CALENDAR COMPUTED ──
  get calGrid(){
    const y=this.curY,m=this.curM;
    const firstDay=new Date(y,m-1,1).getDay(); // 0=Sun
    const daysInMonth=new Date(y,m,0).getDate();
    const today=new Date().toISOString().split('T')[0];
    // Build a day→{hasIncome,hasExpense} map from filtered transactions
    const dayMap={};
    this.filtered.forEach(t=>{
      const d=(t.transaction_date||'').split('T')[0];
      if(!dayMap[d])dayMap[d]={income:false,expense:false};
      if(t.type==='income')dayMap[d].income=true;
      if(t.type==='expense')dayMap[d].expense=true;
    });
    const cells=[];
    // Empty cells before first day
    for(let i=0;i<firstDay;i++)cells.push({key:'e'+i,empty:true});
    // Day cells
    for(let d=1;d<=daysInMonth;d++){
      const dateStr=y+'-'+String(m).padStart(2,'0')+'-'+String(d).padStart(2,'0');
      const info=dayMap[dateStr]||{};
      cells.push({
        key:dateStr,day:d,dateStr,empty:false,
        isToday:dateStr===today,
        hasTx:!!dayMap[dateStr],
        hasIncome:!!info.income,hasExpense:!!info.expense,
        otherMonth:false
      });
    }
    return cells;
  },
  get calDayTxs(){
    if(!this.calSelectedDay)return[];
    return this.transactions.filter(t=>(t.transaction_date||'').split('T')[0]===this.calSelectedDay);
  },
  get calDayStats(){
    const txs=this.calDayTxs;
    return{
      income:txs.filter(t=>t.type==='income').reduce((s,t)=>s+parseFloat(t.amount),0),
      expense:txs.filter(t=>t.type==='expense').reduce((s,t)=>s+parseFloat(t.amount),0),
    };
  },
  get calDayLabel(){
    if(!this.calSelectedDay)return'';
    const d=new Date(this.calSelectedDay+'T00:00:00');
    return d.toLocaleDateString('vi-VN',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
  },
  selectCalDay(dateStr){
    this.calSelectedDay=this.calSelectedDay===dateStr?'':dateStr;
  },

  get budgets(){const r=JSON.parse(localStorage.getItem('mt_budgets')||'{}');return Object.entries(r).filter(([,v])=>v>0).map(([cat,limit])=>{const spent=this.filtered.filter(t=>t.type==='expense'&&t.category===cat).reduce((s,t)=>s+parseFloat(t.amount),0);return{cat,limit:parseFloat(limit),spent,pct:(spent/parseFloat(limit))*100}})},
  get overBudgets(){return this.budgets.filter(b=>b.pct>=90)},
  get goals(){return JSON.parse(localStorage.getItem('mt_goals')||'[]')},
  get fixedItems(){return JSON.parse(localStorage.getItem('mt_fixed')||'[]')},
  get fixedItemsSorted(){return[...this.fixedItems].sort((a,b)=>(a.day||0)-(b.day||0))},
  get fixedIncome(){return this.fixedItems.filter(f=>f.type==='income').reduce((s,f)=>s+parseFloat(f.amount),0)},
  get fixedExpense(){return this.fixedItems.filter(f=>f.type==='expense').reduce((s,f)=>s+parseFloat(f.amount),0)},

  getBudget(c){return(JSON.parse(localStorage.getItem('mt_budgets')||'{}'))[c]||''},
  setBudget(c,v){const b=JSON.parse(localStorage.getItem('mt_budgets')||'{}');v?b[c]=parseFloat(v):delete b[c];localStorage.setItem('mt_budgets',JSON.stringify(b))},

  catsByType(t){return getCats(t)},
  allCats(){return[...getCats('expense'),...getCats('income')]},
  catParent(n){const all=[...getCats('expense'),...getCats('income')];for(const p of all){if(p.name===n)return n;for(const c of(p.children||[])){if(c.name===n)return p.name}}return n},
  curCats(){return getCats(this.form.type==='income'?'income':'expense')},
  subCats(pn){const all=[...getCats('expense'),...getCats('income')];const p=all.find(c=>c.name===pn);return p?p.children||[]:[]},
  catEmoji(n){return catOf(n).emoji||'💸'},
  catColor(n){return catOf(n).color||'#636e72'},
  selectCat(name){
    this.form.category=name;
    this.form.subcat='';
    // Auto-open subcat sheet if this category has children
    const cats=getCats(this.form.type==='expense'?'expense':'income');
    const found=cats.find(c=>c.name===name);
    if(found&&(found.children||[]).length>0){
      this.showSubcatSheet=true;
    } else {
      this.showSubcatSheet=false;
    }
  },

  toggleCatGroup(n){const i=this.openCatGroups.indexOf(n);i>=0?this.openCatGroups.splice(i,1):this.openCatGroups.push(n)},
  addCategory(){
    if(!this.newCat.name){this.notify('Nhập tên danh mục','error');return}
    const cats=getCats(this.newCat.type);
    if(cats.find(c=>c.name===this.newCat.name)){this.notify('Danh mục đã tồn tại','error');return}
    cats.push({name:this.newCat.name,emoji:this.newCat.emoji||'📦',color:this.newCat.color||'#636e72',children:[]});
    saveCats(this.newCat.type,cats);this.showAddCatSheet=false;this.newCat={type:'expense',name:'',emoji:'📦',color:'#00c48c'};this.notify('✅ Đã thêm danh mục!');
  },
  deleteCat(n,t){if(!confirm(`Xóa danh mục "${n}"?`))return;saveCats(t,getCats(t).filter(c=>c.name!==n));this.notify('🗑 Đã xóa!')},
  openAddSubCat(p,t){this.newSubCat={parent:p,type:t,name:'',emoji:''};this.showAddSubCatSheet=true},
  addSubCategory(){
    if(!this.newSubCat.name){this.notify('Nhập tên danh mục con','error');return}
    const cats=getCats(this.newSubCat.type);const parent=cats.find(c=>c.name===this.newSubCat.parent);if(!parent)return;
    if(!parent.children)parent.children=[];parent.children.push({name:this.newSubCat.name,emoji:this.newSubCat.emoji});
    saveCats(this.newSubCat.type,cats);this.showAddSubCatSheet=false;this.notify('✅ Đã thêm danh mục con!');
  },
  deleteSubCat(p,c,t){const cats=getCats(t);const par=cats.find(x=>x.name===p);if(par)par.children=(par.children||[]).filter(x=>x.name!==c);saveCats(t,cats);this.notify('🗑 Đã xóa!')},

  saveFixed(){
    if(!this.fxdForm.name||!this.fxdForm.amount){this.notify('Điền đầy đủ thông tin','error');return}
    const f=JSON.parse(localStorage.getItem('mt_fixed')||'[]');
    f.push({...this.fxdForm,id:Date.now(),amount:parseFloat(this.fxdForm.amount),day:parseInt(this.fxdForm.day)||1});
    localStorage.setItem('mt_fixed',JSON.stringify(f));this.showAddFixedSheet=false;this.fxdForm={type:'expense',name:'',amount:'',day:1,category:'',acc_id:''};this.notify('✅ Đã thêm khoản cố định!');
  },
  deleteFixed(id){localStorage.setItem('mt_fixed',JSON.stringify(JSON.parse(localStorage.getItem('mt_fixed')||'[]').filter(f=>f.id!==id)));this.notify('🗑 Đã xóa!')},
  async applyFixedMonth(){
    if(!this.fixedItems.length){this.notify('Chưa có khoản cố định','error');return}
    if(!confirm(`Áp dụng ${this.fixedItems.length} khoản cố định cho Tháng ${this.curM}/${this.curY}?`))return;
    if(!this.accounts.length){this.notify('Vui lòng thêm ví trước','error');return}
    this.loading=true;let ok=0,fail=0;
    for(const fi of this.fixedItems){
      const d=`${this.curY}-${String(this.curM).padStart(2,'0')}-${String(fi.day||1).padStart(2,'0')}`;
      try{const r=await this.api('/api/finance/transactions',{method:'POST',body:JSON.stringify({type:fi.type,account_id:fi.acc_id||this.accounts[0].id,amount:fi.amount,category:fi.category||fi.name,transaction_date:d,note:fi.name+' (Cố định)',is_recurring:true,recurring_period:'monthly'})});r.success?ok++:fail++}catch{fail++}
    }
    await this.load();this.loading=false;this.notify(`✅ ${ok} khoản thành công${fail?` (${fail} lỗi)`:''}`)
  },
  accName(id){return(this.accounts.find(a=>String(a.id)===String(id))||{name:''}).name},

  async init(){
    window.addEventListener('online',()=>this.isOnline=true);
    window.addEventListener('offline',()=>this.isOnline=false);
    if('serviceWorker'in navigator)navigator.serviceWorker.register('/sw.js').catch(()=>{});
    await this.load();this.loadCurrency();
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
      this.accounts=a.accounts||[];this.transactions=t.transactions||[];this.debts=d.debts||[];this.investments=i.investments||[];this.ov={...this.ov,...o};
      this.$nextTick(()=>this.drawWeek());
    }catch(e){this.notify('Lỗi tải dữ liệu','error')}finally{this.loading=false}
  },
  async loadCurrency(){try{const d=await fetch('/api/finance/currency').then(r=>r.json());if(d.success)this.cur=d.rates}catch{}},
  async loadStats(){try{const d=await fetch(`/api/finance/stats?year=${this.curY}&month=${this.curM}`).then(r=>r.json());if(d.success){this.stats=d;this.$nextTick(()=>{this.drawTrend();this.drawDonut()})}}catch{}},
  chMonth(dir){this.curM+=dir;if(this.curM>12){this.curM=1;this.curY++}if(this.curM<1){this.curM=12;this.curY--}this.$nextTick(()=>this.drawWeek());if(this.tab==='stats')this.loadStats()},

  drawWeek(){
    const c=document.getElementById('weekChart');if(!c)return;if(this.charts.week)this.charts.week.destroy();
    const days=[],inc=[],exp=[];
    for(let i=6;i>=0;i--){const d=new Date(Date.now()-i*86400000),k=d.toISOString().split('T')[0];const txs=this.transactions.filter(t=>t.transaction_date&&t.transaction_date.startsWith(k));days.push(d.toLocaleDateString('vi-VN',{weekday:'short'}));inc.push(txs.filter(t=>t.type==='income').reduce((s,t)=>s+parseFloat(t.amount),0)/1e6);exp.push(txs.filter(t=>t.type==='expense').reduce((s,t)=>s+parseFloat(t.amount),0)/1e6)}
    this.charts.week=new Chart(c,{type:'bar',data:{labels:days,datasets:[{data:inc,backgroundColor:'rgba(0,196,140,.75)',borderRadius:4,barPercentage:.5},{data:exp,backgroundColor:'rgba(255,77,109,.75)',borderRadius:4,barPercentage:.5}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{color:'#6e7681',font:{size:8,family:'Inter'}}},y:{grid:{color:'rgba(255,255,255,.04)'},ticks:{color:'#6e7681',font:{size:8,family:'Inter'},callback:v=>v+'M'}}}}})
  },
  drawTrend(){
    const c=document.getElementById('trendChart');if(!c||!this.stats.trend)return;if(this.charts.trend)this.charts.trend.destroy();
    const tr=this.stats.trend||[];
    this.charts.trend=new Chart(c,{type:'line',data:{labels:tr.map(t=>t.label),datasets:[{data:tr.map(t=>t.income/1e6),borderColor:'#00c48c',backgroundColor:'rgba(0,196,140,.1)',tension:.4,fill:true,pointRadius:3,borderWidth:2},{data:tr.map(t=>t.expense/1e6),borderColor:'#ff4d6d',backgroundColor:'rgba(255,77,109,.07)',tension:.4,fill:true,pointRadius:3,borderWidth:2}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{color:'#6e7681',font:{size:8,family:'Inter'}}},y:{grid:{color:'rgba(255,255,255,.04)'},ticks:{color:'#6e7681',font:{size:8,family:'Inter'},callback:v=>v+'M'}}}}})
  },
  drawDonut(){
    const c=document.getElementById('donutChart');if(!c)return;if(this.charts.donut)this.charts.donut.destroy();
    const cats=(this.stats.by_category||[]).slice(0,5);if(!cats.length)return;
    this.charts.donut=new Chart(c,{type:'doughnut',data:{labels:cats.map(c=>c.category),datasets:[{data:cats.map(c=>c.total),backgroundColor:cats.map(c=>catOf(c.category).color||'#636e72'),borderWidth:0,hoverOffset:6}]},options:{responsive:true,maintainAspectRatio:false,cutout:'70%',plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>ctx.label+': '+this.fmtS(ctx.parsed)}}}}})
  },

  groupTxs(list){
    const G={};const td=new Date().toISOString().split('T')[0];const yd=new Date(Date.now()-86400000).toISOString().split('T')[0];
    list.forEach(t=>{const k=(t.transaction_date||'').split('T')[0];if(k)(G[k]=G[k]||[]).push(t)});
    return Object.entries(G).sort(([a],[b])=>b.localeCompare(a)).map(([date,items])=>{
      const net=items.reduce((s,t)=>t.type==='income'?s+parseFloat(t.amount):t.type==='expense'?s-parseFloat(t.amount):s,0);
      const d=new Date(date+'T00:00:00');
      const label=date===td?'Hôm nay':date===yd?'Hôm qua':d.toLocaleDateString('vi-VN',{weekday:'short',day:'numeric',month:'numeric'});
      return{date,label,items,net};
    });
  },
  numFmt(v){return new Intl.NumberFormat('vi-VN',{maximumFractionDigits:0}).format(parseFloat(v)||0)},
  fmtS(v){const n=parseFloat(v)||0,a=Math.abs(n),s=n<0?'-':'';if(a>=1e9)return s+(a/1e9).toFixed(1).replace(/\.0$/,'')+'B';if(a>=1e6)return s+(a/1e6).toFixed(1).replace(/\.0$/,'')+'M';if(a>=1e3)return s+(a/1e3).toFixed(0)+'K';return s+a.toFixed(0)+'₫'},
  fmtDate(d){if(!d)return'';return new Date(d+'T00:00:00').toLocaleDateString('vi-VN',{day:'2-digit',month:'2-digit',year:'numeric'})},
  notify(msg,type='success'){this.toast={show:true,msg,type};setTimeout(()=>this.toast.show=false,2800)},
  async api(url,opts={}){
    const tok=document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1];
    const r=await fetch(url,{headers:{'Content-Type':'application/json','X-XSRF-TOKEN':tok?decodeURIComponent(tok):CSRF},...opts});
    return r.json();
  },

  openAdd(type){
    this.editingTx=null;
    const today=new Date().toISOString().split('T')[0];
    const month=today.slice(0,7);
    this.form={type,acc:this.accounts[0]?.id||'',toAcc:'',category:'',subcat:'',date:today,note:'',recur:false,period:'monthly',month};
    this.numpad='';this.showMonthExpand=false;this.showDateDetail=false;
    this.showNumpad=false;this.showSubcatSheet=false;
    this.customMonth=month;this.showAddScreen=true;
  },
  editTx(tx){
    this.editingTx=tx;
    const date=(tx.transaction_date||'').split('T')[0];
    const month=date.slice(0,7);
    this.form={type:tx.type,acc:tx.account_id,toAcc:tx.to_account_id||'',category:tx.category,subcat:'',date,note:tx.note||'',recur:!!tx.is_recurring,period:tx.recurring_period||'monthly',month};
    this.numpad=String(Math.round(parseFloat(tx.amount)||0));
    this.showMonthExpand=false;this.showDateDetail=false;
    this.showNumpad=true;this.showSubcatSheet=false; // open numpad when editing
    this.customMonth=month;
    this.showAddScreen=true;
  },
  // Month selector functions
  selectMonth(key){
    this.form.month=key;
    // Keep same day-of-month, just change year-month
    const day=this.form.date.split('-')[2]||'01';
    const [y,m]=key.split('-');
    // Clamp day to valid range for that month
    const maxDay=new Date(parseInt(y),parseInt(m),0).getDate();
    const clampedDay=String(Math.min(parseInt(day),maxDay)).padStart(2,'0');
    this.form.date=`${key}-${clampedDay}`;
    this.customMonth=key;
  },
  syncMonthFromDate(){
    if(this.form.date){
      this.form.month=this.form.date.slice(0,7);
      this.customMonth=this.form.month;
    }
  },
  applyCustomMonth(){
    if(this.customMonth)this.selectMonth(this.customMonth);
  },
  numPress(k){
    if(k==='⌫')this.numpad=this.numpad.slice(0,-1);
    else if(k==='C')this.numpad='';
    else if(k==='.'){if(!this.numpad.includes('.'))this.numpad+='.'}
    else if(k==='000'){if(this.numpad)this.numpad+='000'}
    else{if(this.numpad.length<12)this.numpad+=k}
  },

  async saveTx(){
    const amt=parseFloat(this.numpad);
    if(!amt||amt<=0){this.notify('Nhập số tiền hợp lệ','error');return}
    if(!this.form.acc){this.notify('Vui lòng chọn ví','error');return}
    if(!this.form.category&&this.form.type!=='transfer'){this.notify('Vui lòng chọn danh mục','error');return}
    this.loading=true;
    const cat=this.form.subcat||this.form.category;
    const note=this.form.subcat&&this.form.category?`[${this.form.category}] ${this.form.note}`:this.form.note;
    try{
      const d=await this.api('/api/finance/transactions',{method:'POST',body:JSON.stringify({type:this.form.type,account_id:this.form.acc,to_account_id:this.form.toAcc||null,amount:amt,category:this.form.type==='transfer'?'Chuyển ví':cat,transaction_date:this.form.date,note,is_recurring:this.form.recur,recurring_period:this.form.recur?this.form.period:null})});
      if(d.success){this.showAddScreen=false;this.notify('✅ Ghi chép thành công!');await this.load()}else this.notify(d.message||'Lỗi!','error')
    }catch{this.notify('Lỗi kết nối','error')}finally{this.loading=false}
  },
  async updateTx(){
    const amt=parseFloat(this.numpad);
    if(!amt||amt<=0){this.notify('Nhập số tiền hợp lệ','error');return}
    this.loading=true;
    try{
      const d=await this.api('/api/finance/transactions/'+this.editingTx.id,{method:'PATCH',body:JSON.stringify({amount:amt,category:this.form.category,transaction_date:this.form.date,note:this.form.note,is_recurring:this.form.recur,recurring_period:this.form.recur?this.form.period:null})});
      if(d.success){this.showAddScreen=false;this.editingTx=null;this.notify('✅ Cập nhật thành công!');await this.load()}else this.notify(d.message||'Lỗi!','error')
    }catch{this.notify('Lỗi kết nối','error')}finally{this.loading=false}
  },
  async delTx(tx){
    if(!confirm(`Xoá "${tx.category}" — ${this.fmtS(tx.amount)}?`))return;
    this.loading=true;
    try{const d=await this.api('/api/finance/transactions/'+tx.id,{method:'DELETE'});if(d.success){this.notify('🗑 Đã xoá');await this.load()}else this.notify(d.message,'error')}catch{this.notify('Lỗi','error')}finally{this.loading=false}
  },
  async saveAcc(){
    if(!this.aForm.name){this.notify('Nhập tên ví','error');return}
    this.loading=true;
    try{
      const d=await this.api('/api/finance/accounts',{method:'POST',body:JSON.stringify({...this.aForm,balance:parseFloat(this.aForm.balance)||0})});
      if(d.success){this.showAccSheet=false;this.aForm={name:'',type:'cash',balance:''};this.notify('✅ Tạo ví thành công!');await this.load()}else this.notify(d.message,'error')
    }catch{this.notify('Lỗi kết nối','error')}finally{this.loading=false}
  },
  openEditAcc(a){
    this.editingAcc=a;
    this.aForm={name:a.name,type:a.type,balance:a.balance};
    this.showAccSheet=true;
  },
  async updateAcc(){
    if(!this.aForm.name){this.notify('Nhập tên ví','error');return}
    this.loading=true;
    try{
      const d=await this.api('/api/finance/accounts/'+this.editingAcc.id,{method:'PUT',body:JSON.stringify({name:this.aForm.name,type:this.aForm.type,balance:parseFloat(this.aForm.balance)||0})});
      if(d.success){
        this.showAccSheet=false;this.editingAcc=null;this.aForm={name:'',type:'cash',balance:''};
        this.notify('✅ Cập nhật ví thành công!');
        await this.load();
      }else this.notify(d.message,'error')
    }catch{this.notify('Lỗi kết nối','error')}finally{this.loading=false}
  },
  async deleteAcc(a){
    const txCount=this.transactions.filter(t=>String(t.account_id)===String(a.id)||String(t.to_account_id)===String(a.id)).length;
    let msg=`Xóa ví "${a.name}" (${this.fmtS(a.balance)})?`;
    if(txCount>0)msg+=`\n⚠️ Ví này có ${txCount} giao dịch. Số dư sẽ bị xóa!`;
    if(!confirm(msg))return;
    this.loading=true;
    try{
      const d=await this.api('/api/finance/accounts/'+a.id,{method:'DELETE'});
      if(d.success){
        this.notify('🗑 Đã xóa ví '+a.name);
        await this.load();
      }else this.notify(d.message||'Không thể xóa!','error')
    }catch{this.notify('Lỗi kết nối','error')}finally{this.loading=false}
  },
  async saveDebt(){
    if(!this.dForm.name||!this.dForm.amount){this.notify('Điền đầy đủ thông tin','error');return}
    this.loading=true;
    try{const d=await this.api('/api/finance/debts',{method:'POST',body:JSON.stringify({partner_name:this.dForm.name,type:this.dForm.type,amount:parseFloat(this.dForm.amount),due_date:this.dForm.due||null,note:this.dForm.note})});if(d.success){this.showDebtSheet=false;this.dForm={name:'',type:'lend',amount:'',due:'',note:''};this.notify('✅ Ghi nhận thành công!');await this.load()}else this.notify(d.message,'error')}catch{this.notify('Lỗi kết nối','error')}finally{this.loading=false}
  },
  async toggleDebt(id){try{const d=await this.api('/api/finance/debts/'+id+'/toggle',{method:'POST'});if(d.success){this.notify('✅ Cập nhật!');await this.load()}}catch{this.notify('Lỗi','error')}},
  async deleteDebt(id){if(!confirm('Xoá khoản nợ?'))return;this.loading=true;try{const d=await this.api('/api/finance/debts/'+id,{method:'DELETE'});if(d.success){this.notify('🗑 Đã xoá');await this.load()}}catch{this.notify('Lỗi','error')}finally{this.loading=false}},
  async saveInv(){
    if(!this.iForm.sym||!this.iForm.qty||!this.iForm.bp){this.notify('Điền đầy đủ thông tin','error');return}
    this.loading=true;
    try{const d=await this.api('/api/finance/investments',{method:'POST',body:JSON.stringify({symbol:this.iForm.sym.toUpperCase(),type:this.iForm.type,quantity:parseFloat(this.iForm.qty),buy_price:parseFloat(this.iForm.bp)})});if(d.success){this.iForm={sym:'',type:'crypto',qty:'',bp:''};this.notify('✅ Thêm thành công!');await this.load()}else this.notify(d.message,'error')}catch{this.notify('Lỗi kết nối','error')}finally{this.loading=false}
  },
  async deleteInv(id){if(!confirm('Xoá tài sản?'))return;this.loading=true;try{const d=await this.api('/api/finance/investments/'+id,{method:'DELETE'});if(d.success){this.notify('🗑 Đã xoá');await this.load()}}catch{this.notify('Lỗi','error')}finally{this.loading=false}},
  async updateRates(){this.loading=true;try{const d=await this.api('/api/finance/rates/update',{method:'POST'});this.notify(d.message||'✅ Cập nhật tỷ giá!');await this.load()}catch{this.notify('Lỗi','error')}finally{this.loading=false}},

  saveGoal(){
    if(!this.gForm.name||!this.gForm.target){this.notify('Điền tên và mục tiêu','error');return}
    const goals=JSON.parse(localStorage.getItem('mt_goals')||'[]');goals.push({...this.gForm,target:parseFloat(this.gForm.target),saved:parseFloat(this.gForm.saved)||0,id:Date.now()});localStorage.setItem('mt_goals',JSON.stringify(goals));this.gForm={name:'',icon:'🎯',target:'',saved:'',deadline:''};this.notify('✅ Đã thêm mục tiêu!')
  },
  removeGoal(i){const g=JSON.parse(localStorage.getItem('mt_goals')||'[]');g.splice(i,1);localStorage.setItem('mt_goals',JSON.stringify(g))},

  exportReport(){
    const l=[`📊 BÁO CÁO ${this.mLabel.toUpperCase()}`,`─────────────────────`,`💰 Thu: +${this.fmtS(this.mStats.income)}`,`💸 Chi: -${this.fmtS(this.mStats.expense)}`,`⚖️ Còn: ${this.fmtS(this.mStats.income-this.mStats.expense)}`,`🏦 Tài sản ròng: ${this.fmtS(this.ov.net_worth)}`,`─────────────────────`,`🕐 ${new Date().toLocaleString('vi-VN')}`];
    const txt=l.join('\n');
    if(navigator.clipboard)navigator.clipboard.writeText(txt).then(()=>this.notify('✅ Đã copy báo cáo!')).catch(()=>prompt('Copy:',txt));
    else prompt('Copy:',txt);
  },
}}
</script>
</body>
</html>
