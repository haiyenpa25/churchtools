<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>FinanceTracker - Quản lý tài chính cá nhân</title>
    
    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f59e0b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Finance">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        darkBg: '#0b0f19',
                        darkCard: 'rgba(17, 24, 39, 0.7)',
                        goldAccent: '#f59e0b',
                        emeraldAccent: '#10b981',
                        roseAccent: '#ef4444',
                    }
                }
            }
        }
    </script>

    <!-- Chart.js & Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background-color: #0b0f19;
            color: #f3f4f6;
            -webkit-tap-highlight-color: transparent;
        }
        /* Custom Glassmorphism scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.2);
            border-radius: 4px;
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .bottom-nav-active {
            color: #f59e0b;
            text-shadow: 0 0 12px rgba(245, 158, 11, 0.4);
        }
        .animate-slide-up {
            animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
    </style>
</head>
<body class="flex justify-center min-h-screen">

    <!-- Mobile Screen Wrapper (Centered with max-width to look like a real mobile app on desktop) -->
    <div x-data="financeApp()" 
         x-init="init()"
         class="w-full max-w-md bg-darkBg min-h-screen flex flex-col relative shadow-2xl border-x border-gray-800/40 pb-20 select-none">
        
        <!-- Header -->
        <header class="glass-card sticky top-0 z-30 px-5 py-4 flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-yellow-300 flex items-center justify-center text-darkBg font-bold text-lg shadow-lg shadow-amber-500/20">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-wide bg-gradient-to-r from-amber-400 to-yellow-200 bg-clip-text text-transparent">FinanceTracker</h1>
                    <span class="text-xs text-gray-400 font-medium">Hải Yến • PWA Mobile</span>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Status Offline / Online -->
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold"
                     :class="isOnline ? 'bg-emeraldAccent/10 text-emeraldAccent border border-emeraldAccent/20' : 'bg-roseAccent/10 text-roseAccent border border-roseAccent/20'">
                    <span class="w-1.5 h-1.5 rounded-full" :class="isOnline ? 'bg-emeraldAccent animate-pulse' : 'bg-roseAccent'"></span>
                    <span x-text="isOnline ? 'ONLINE' : 'OFFLINE'"></span>
                </div>
                
                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-8 h-8 rounded-lg bg-gray-800/60 hover:bg-gray-700/60 border border-gray-700/40 flex items-center justify-center text-gray-300 hover:text-roseAccent transition">
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 px-4 py-5 overflow-y-auto">

            <!-- Dynamic Loader -->
            <div x-show="loading" class="fixed inset-0 bg-darkBg/60 backdrop-blur-sm z-50 flex items-center justify-center">
                <div class="flex flex-col items-center gap-3 glass-card p-6 rounded-2xl">
                    <svg class="animate-spin h-8 w-8 text-amber-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-xs text-amber-500 font-bold tracking-wider">ĐANG XỬ LÝ...</span>
                </div>
            </div>

            <!-- Tab 1: Tổng quan -->
            <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200" class="space-y-5">
                
                <!-- Net Worth Card -->
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-gray-900 via-slate-900 to-zinc-900 border border-gray-800 shadow-2xl p-6">
                    <div class="absolute -right-16 -top-16 w-36 h-36 bg-amber-500/10 rounded-full blur-3xl"></div>
                    <div class="absolute -left-16 -bottom-16 w-36 h-36 bg-indigo-500/10 rounded-full blur-3xl"></div>
                    
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs text-gray-400 font-semibold tracking-wider uppercase">TỔNG TÀI SẢN RÒNG</span>
                        <i class="fa-solid fa-shield-halved text-amber-500/50 text-sm"></i>
                    </div>
                    
                    <div class="text-3xl font-extrabold text-amber-400 tracking-tight" x-text="formatVnd(overview.net_worth)"></div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-800 flex justify-between items-center text-xs text-gray-400">
                        <div>
                            <span class="block text-[10px] text-gray-500 font-bold">VỐN KHẢ DỤNG</span>
                            <span class="text-gray-200 font-semibold" x-text="formatVnd(overview.total_cash)"></span>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] text-gray-500 font-bold">LỢI NHUẬN ĐẦU TƯ</span>
                            <span class="font-bold flex items-center gap-1 justify-end" :class="overview.investment_pnl >= 0 ? 'text-emeraldAccent' : 'text-roseAccent'">
                                <i class="fa-solid" :class="overview.investment_pnl >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'"></i>
                                <span x-text="(overview.investment_pnl >= 0 ? '+' : '') + formatVnd(overview.investment_pnl)"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <button @click="openAddTransactionModal('expense')" class="glass-card py-3 px-4 rounded-2xl flex items-center gap-3 active:scale-95 transition">
                        <div class="w-10 h-10 rounded-xl bg-roseAccent/10 text-roseAccent flex items-center justify-center shadow">
                            <i class="fa-solid fa-arrow-up-from-bracket"></i>
                        </div>
                        <div class="text-left">
                            <span class="block text-[10px] text-gray-400 font-bold uppercase">CHI PHÍ</span>
                            <span class="text-xs font-bold text-roseAccent">Ghi chi tiêu</span>
                        </div>
                    </button>
                    <button @click="openAddTransactionModal('income')" class="glass-card py-3 px-4 rounded-2xl flex items-center gap-3 active:scale-95 transition">
                        <div class="w-10 h-10 rounded-xl bg-emeraldAccent/10 text-emeraldAccent flex items-center justify-center shadow">
                            <i class="fa-solid fa-arrow-down-to-bracket"></i>
                        </div>
                        <div class="text-left">
                            <span class="block text-[10px] text-gray-400 font-bold uppercase">THU NHẬP</span>
                            <span class="text-xs font-bold text-emeraldAccent">Ghi thu nhập</span>
                        </div>
                    </button>
                </div>

                <!-- Asset Allocation Chart Card -->
                <div class="glass-card rounded-3xl p-5 space-y-4">
                    <h3 class="text-sm font-bold text-amber-500 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie"></i> Cơ Cấu Tài Sản
                    </h3>
                    <div class="relative flex justify-center py-2">
                        <canvas id="allocationChart" style="max-height: 160px; max-width: 160px;"></canvas>
                        <div x-show="accounts.length === 0 && investments.length === 0" class="absolute inset-0 flex items-center justify-center text-xs text-gray-500">
                            Chưa có dữ liệu phân tích
                        </div>
                    </div>
                    
                    <!-- Chart breakdown legends -->
                    <div class="grid grid-cols-3 gap-2 text-center text-[10px] font-bold mt-2">
                        <div class="p-2 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-300">
                            <span>TIỀN MẶT / BANK</span>
                            <span class="block text-xs font-extrabold mt-0.5 text-gray-100" x-text="formatVnd(overview.total_cash)"></span>
                        </div>
                        <div class="p-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300">
                            <span>TÍCH SẢN ĐẦU TƯ</span>
                            <span class="block text-xs font-extrabold mt-0.5 text-gray-100" x-text="formatVnd(overview.total_investment)"></span>
                        </div>
                        <div class="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300">
                            <span>CHO VAY (NỢ THU)</span>
                            <span class="block text-xs font-extrabold mt-0.5 text-gray-100" x-text="formatVnd(overview.total_lend)"></span>
                        </div>
                    </div>
                </div>

                <!-- Accounts Breakdown -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xs text-gray-400 font-bold uppercase tracking-wider">TÀI KHOẢN & VÍ</h3>
                        <button @click="showAddAccountModal = true" class="text-xs text-amber-500 hover:text-amber-400 font-bold flex items-center gap-1">
                            <i class="fa-solid fa-plus-circle"></i> Thêm ví
                        </button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="acc in accounts" :key="acc.id">
                            <div class="glass-card p-4 rounded-2xl flex justify-between items-center border border-gray-800/40 relative group">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg"
                                         :class="{
                                             'bg-blue-500/10 text-blue-400': acc.type === 'bank',
                                             'bg-amber-500/10 text-amber-400': acc.type === 'cash',
                                             'bg-pink-500/10 text-pink-400': acc.type === 'e-wallet',
                                             'bg-purple-500/10 text-purple-400': acc.type === 'other'
                                         }">
                                        <i class="fa-solid" :class="{
                                            'fa-building-columns': acc.type === 'bank',
                                            'fa-wallet': acc.type === 'cash',
                                            'fa-mobile-screen-button': acc.type === 'e-wallet',
                                            'fa-ellipsis': acc.type === 'other'
                                        }"></i>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-bold text-gray-100" x-text="acc.name"></span>
                                        <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider" x-text="acc.type === 'bank' ? 'Ngân hàng' : (acc.type === 'e-wallet' ? 'Ví điện tử' : 'Tiền mặt')"></span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-extrabold text-amber-400" x-text="formatVnd(acc.balance)"></span>
                                    <!-- Delete account -->
                                    <button @click="deleteAccount(acc.id)" class="text-gray-500 hover:text-roseAccent text-xs transition p-1">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template x-if="accounts.length === 0">
                            <div class="text-center py-6 text-xs text-gray-500 glass-card rounded-2xl">
                                Chưa có ví tài khoản nào. Hãy tạo ví ngay!
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            <!-- Tab 2: Giao dịch -->
            <div x-show="activeTab === 'transactions'" x-transition:enter="transition ease-out duration-200" class="space-y-4">
                
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-amber-500 uppercase tracking-wider">
                        <i class="fa-solid fa-list-check"></i> Nhật ký Giao dịch
                    </h3>
                    <button @click="openAddTransactionModal('expense')" class="bg-gradient-to-r from-amber-500 to-yellow-500 text-darkBg text-xs font-extrabold px-3 py-1.5 rounded-xl flex items-center gap-1 shadow-lg shadow-amber-500/10 active:scale-95 transition">
                        <i class="fa-solid fa-circle-plus"></i> Ghi chép mới
                    </button>
                </div>

                <!-- Transaction List -->
                <div class="space-y-2">
                    <template x-for="tx in transactions" :key="tx.id">
                        <div class="glass-card p-3.5 rounded-2xl flex justify-between items-center border border-gray-800/40 hover:border-gray-700/40 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm"
                                     :class="{
                                         'bg-emeraldAccent/10 text-emeraldAccent': tx.type === 'income',
                                         'bg-roseAccent/10 text-roseAccent': tx.type === 'expense',
                                         'bg-blue-500/10 text-blue-400': tx.type === 'transfer'
                                     }">
                                    <i class="fa-solid" :class="{
                                        'fa-arrow-down-long': tx.type === 'income',
                                        'fa-arrow-up-long': tx.type === 'expense',
                                        'fa-right-left': tx.type === 'transfer'
                                    }"></i>
                                </div>
                                <div class="max-w-[180px]">
                                    <span class="block text-xs font-bold text-gray-100 truncate" x-text="tx.category"></span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[9px] text-gray-400 font-bold uppercase" x-text="tx.account ? tx.account.name : 'N/A'"></span>
                                        <template x-if="tx.type === 'transfer' && tx.to_account">
                                            <span class="text-[9px] text-gray-500 font-bold flex items-center gap-1">
                                                <i class="fa-solid fa-angles-right text-[8px]"></i>
                                                <span x-text="tx.to_account.name"></span>
                                            </span>
                                        </template>
                                    </div>
                                    <span class="block text-[10px] text-gray-400 italic truncate" x-text="tx.note || 'Không có ghi chú'"></span>
                                </div>
                            </div>
                            
                            <div class="text-right flex items-center gap-3">
                                <div>
                                    <span class="block text-xs font-extrabold"
                                          :class="{
                                              'text-emeraldAccent': tx.type === 'income',
                                              'text-roseAccent': tx.type === 'expense',
                                              'text-blue-400': tx.type === 'transfer'
                                          }"
                                          x-text="(tx.type === 'income' ? '+' : (tx.type === 'expense' ? '-' : '⇄ ')) + formatVnd(tx.amount)">
                                    </span>
                                    <span class="block text-[9px] text-gray-500 font-medium" x-text="formatDate(tx.transaction_date)"></span>
                                </div>
                                
                                <button @click="deleteTransaction(tx.id)" class="text-gray-500 hover:text-roseAccent text-xs transition p-1">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                    <template x-if="transactions.length === 0">
                        <div class="text-center py-10 text-xs text-gray-500 glass-card rounded-2xl">
                            Không có giao dịch nào gần đây. Hãy tạo một giao dịch mới!
                        </div>
                    </template>
                </div>

            </div>

            <!-- Tab 3: Ghi nợ -->
            <div x-show="activeTab === 'debts'" x-transition:enter="transition ease-out duration-200" class="space-y-4">
                
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-amber-500 uppercase tracking-wider">
                        <i class="fa-solid fa-handshake"></i> Quản Lý Nợ
                    </h3>
                    <button @click="showAddDebtModal = true" class="bg-gradient-to-r from-amber-500 to-yellow-500 text-darkBg text-xs font-extrabold px-3 py-1.5 rounded-xl flex items-center gap-1 shadow-lg shadow-amber-500/10 active:scale-95 transition">
                        <i class="fa-solid fa-plus-circle"></i> Thêm nợ mới
                    </button>
                </div>

                <!-- Debts totals summary -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="glass-card p-4 rounded-2xl border-l-4 border-emeraldAccent">
                        <span class="block text-[10px] text-gray-400 font-bold uppercase">BẠN CHO VAY (ĐANG NỢ)</span>
                        <span class="text-sm font-extrabold text-emeraldAccent mt-1 block" x-text="formatVnd(overview.total_lend)"></span>
                    </div>
                    <div class="glass-card p-4 rounded-2xl border-l-4 border-roseAccent">
                        <span class="block text-[10px] text-gray-400 font-bold uppercase">BẠN ĐI VAY (PHẢI TRẢ)</span>
                        <span class="text-sm font-extrabold text-roseAccent mt-1 block" x-text="formatVnd(overview.total_borrow)"></span>
                    </div>
                </div>

                <!-- Debts list -->
                <div class="space-y-2">
                    <template x-for="d in debts" :key="d.id">
                        <div class="glass-card p-3.5 rounded-2xl flex justify-between items-center border border-gray-800/40 relative hover:border-gray-700/40 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm"
                                     :class="d.type === 'lend' ? 'bg-emeraldAccent/10 text-emeraldAccent' : 'bg-roseAccent/10 text-roseAccent'">
                                    <i class="fa-solid" :class="d.type === 'lend' ? 'fa-hand-holding-dollar' : 'fa-handshake-angle'"></i>
                                </div>
                                <div class="max-w-[180px]">
                                    <span class="block text-xs font-bold text-gray-100" x-text="d.partner_name"></span>
                                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full inline-block mt-0.5 uppercase"
                                          :class="d.type === 'lend' ? 'bg-emeraldAccent/10 text-emeraldAccent' : 'bg-roseAccent/10 text-roseAccent'"
                                          x-text="d.type === 'lend' ? 'Cho vay' : 'Đi vay'"></span>
                                    <span class="block text-[10px] text-gray-400 italic mt-0.5 truncate" x-text="d.note || 'Không có ghi chú'"></span>
                                    <template x-if="d.due_date">
                                        <span class="block text-[9px] text-gray-500 font-bold mt-0.5">
                                            Hạn trả: <span x-text="formatDate(d.due_date)"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                            
                            <div class="text-right flex items-center gap-3">
                                <div>
                                    <span class="block text-xs font-extrabold" :class="d.type === 'lend' ? 'text-emeraldAccent' : 'text-roseAccent'" x-text="formatVnd(d.amount)"></span>
                                    <button @click="toggleDebt(d.id)" 
                                            class="mt-1 text-[9px] font-bold px-2 py-0.5 rounded-full inline-block transition active:scale-95"
                                            :class="d.status === 'paid' ? 'bg-gray-800 text-gray-400 border border-gray-700/50' : 'bg-amber-500 text-darkBg'"
                                            x-text="d.status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán'">
                                    </button>
                                </div>
                                
                                <button @click="deleteDebt(d.id)" class="text-gray-500 hover:text-roseAccent text-xs transition p-1">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                    <template x-if="debts.length === 0">
                        <div class="text-center py-10 text-xs text-gray-500 glass-card rounded-2xl">
                            Không có hồ sơ nợ vay nào.
                        </div>
                    </template>
                </div>

            </div>

            <!-- Tab 4: Tích sản / Đầu tư -->
            <div x-show="activeTab === 'investments'" x-transition:enter="transition ease-out duration-200" class="space-y-4">
                
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-amber-500 uppercase tracking-wider">
                        <i class="fa-solid fa-chart-line"></i> Đầu Tư Tích Sản
                    </h3>
                    <div class="flex items-center gap-2">
                        <button @click="updateRates()" class="bg-gray-800/80 border border-gray-700/40 hover:bg-gray-700/80 text-amber-500 w-8 h-8 rounded-xl flex items-center justify-center active:scale-90 transition">
                            <i class="fa-solid fa-arrows-rotate"></i>
                        </button>
                        <button @click="showAddInvestmentModal = true" class="bg-gradient-to-r from-amber-500 to-yellow-500 text-darkBg text-xs font-extrabold px-3 py-1.5 rounded-xl flex items-center gap-1 shadow-lg shadow-amber-500/10 active:scale-95 transition">
                            <i class="fa-solid fa-plus-circle"></i> Thêm tài sản
                        </button>
                    </div>
                </div>

                <!-- Live Ticker Indicator -->
                <div class="bg-amber-500/5 border border-amber-500/10 p-3 rounded-2xl flex items-center justify-between text-xs">
                    <span class="text-gray-400 font-medium">Lợi nhuận đầu tư ước tính</span>
                    <span class="font-extrabold flex items-center gap-1" :class="overview.investment_pnl >= 0 ? 'text-emeraldAccent' : 'text-roseAccent'">
                        <i class="fa-solid" :class="overview.investment_pnl >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'"></i>
                        <span x-text="(overview.investment_pnl >= 0 ? '+' : '') + formatVnd(overview.investment_pnl) + ' (' + overview.investment_pnl_percent.toFixed(2) + '%)'"></span>
                    </span>
                </div>

                <!-- Investments List -->
                <div class="space-y-2">
                    <template x-for="inv in investments" :key="inv.id">
                        <div class="glass-card p-3.5 rounded-2xl flex justify-between items-center border border-gray-800/40 relative hover:border-gray-700/40 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold shadow-inner"
                                     :class="inv.type === 'crypto' ? 'bg-yellow-500/10 text-yellow-500' : 'bg-indigo-500/10 text-indigo-400'">
                                    <span x-text="inv.symbol"></span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold text-gray-100" x-text="inv.symbol"></span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-gray-800 text-gray-400 font-bold uppercase tracking-wider" x-text="inv.type === 'crypto' ? 'Crypto' : 'Cổ phiếu'"></span>
                                    </div>
                                    <span class="block text-[10px] text-gray-400 mt-0.5">
                                        Số lượng: <span class="font-bold text-gray-200" x-text="inv.quantity"></span>
                                    </span>
                                    <span class="block text-[9px] text-gray-500">
                                        Mua: <span x-text="formatVnd(inv.buy_price)"></span> | Hiện tại: <span x-text="formatVnd(inv.current_price)"></span>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="text-right flex items-center gap-3">
                                <div>
                                    <span class="block text-xs font-extrabold text-amber-400" x-text="formatVnd(inv.quantity * inv.current_price)"></span>
                                    <!-- Investment Profit margin -->
                                    <span class="text-[9px] font-extrabold block mt-0.5" 
                                          :class="(inv.current_price - inv.buy_price) >= 0 ? 'text-emeraldAccent' : 'text-roseAccent'"
                                          x-text="((inv.current_price - inv.buy_price) >= 0 ? '+' : '') + (((inv.current_price - inv.buy_price) / inv.buy_price) * 100).toFixed(1) + '%'">
                                    </span>
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <!-- Delete investment -->
                                    <button @click="deleteInvestment(inv.id)" class="text-gray-500 hover:text-roseAccent text-xs transition p-1">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="investments.length === 0">
                        <div class="text-center py-10 text-xs text-gray-500 glass-card rounded-2xl">
                            Không có danh mục đầu tư nào.
                        </div>
                    </template>
                </div>

            </div>

        </main>

        <!-- Toast Notifications -->
        <div x-show="notification.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:leave="transition ease-in duration-200"
             class="fixed top-20 left-1/2 -translate-x-1/2 w-[85%] max-w-[340px] z-50 rounded-2xl p-4 shadow-2xl glass-card flex items-center gap-3 border"
             :class="notification.type === 'success' ? 'border-emeraldAccent/30 bg-emeraldAccent/5' : 'border-roseAccent/30 bg-roseAccent/5'"
             style="display: none;">
            <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs shadow"
                 :class="notification.type === 'success' ? 'bg-emeraldAccent/20 text-emeraldAccent' : 'bg-roseAccent/20 text-roseAccent'">
                <i class="fa-solid" :class="notification.type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'"></i>
            </div>
            <span class="text-xs font-bold text-gray-200" x-text="notification.message"></span>
        </div>

        <!-- -------------------- MODALS & SLIDE-UP BOTTOM SHEETS -------------------- -->

        <!-- Bottom Sheet Modal: Ghi chép Giao dịch (Income / Expense) -->
        <div x-show="showAddTransactionModal" class="fixed inset-0 z-40 bg-darkBg/60 backdrop-blur-sm flex items-end justify-center" style="display: none;">
            <div @click.away="showAddTransactionModal = false" class="w-full max-w-md bg-slate-900 border-t border-gray-800 rounded-t-[30px] p-6 space-y-4 animate-slide-up shadow-2xl">
                <div class="flex justify-between items-center border-b border-gray-800 pb-3">
                    <h3 class="text-sm font-bold text-amber-500 uppercase tracking-wider">Ghi chép tài chính mới</h3>
                    <button @click="showAddTransactionModal = false" class="text-gray-400 hover:text-gray-200 text-sm">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>

                <form @submit.prevent="submitTransaction" class="space-y-4 text-xs">
                    <!-- Type Selector Tabs -->
                    <div class="grid grid-cols-3 gap-2 bg-gray-950 p-1 rounded-xl">
                        <button type="button" @click="transactionForm.type = 'expense'" class="py-2 rounded-lg font-bold transition text-center uppercase" :class="transactionForm.type === 'expense' ? 'bg-roseAccent text-darkBg' : 'text-gray-400'">
                            Chi phí
                        </button>
                        <button type="button" @click="transactionForm.type = 'income'" class="py-2 rounded-lg font-bold transition text-center uppercase" :class="transactionForm.type === 'income' ? 'bg-emeraldAccent text-darkBg' : 'text-gray-400'">
                            Thu nhập
                        </button>
                        <button type="button" @click="transactionForm.type = 'transfer'" class="py-2 rounded-lg font-bold transition text-center uppercase" :class="transactionForm.type === 'transfer' ? 'bg-blue-500 text-darkBg' : 'text-gray-400'">
                            Chuyển ví
                        </button>
                    </div>

                    <!-- Source Account -->
                    <div>
                        <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Từ tài khoản / Ví</label>
                        <select x-model="transactionForm.account_id" class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none">
                            <option value="">-- Chọn tài khoản --</option>
                            <template x-for="acc in accounts" :key="acc.id">
                                <option :value="acc.id" x-text="acc.name + ' (' + formatVnd(acc.balance) + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Target Account (Only for transfers) -->
                    <div x-show="transactionForm.type === 'transfer'">
                        <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Đến tài khoản / Ví</label>
                        <select x-model="transactionForm.to_account_id" class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none">
                            <option value="">-- Chọn tài khoản đích --</option>
                            <template x-for="acc in accounts" :key="acc.id">
                                <option :value="acc.id" x-text="acc.name + ' (' + formatVnd(acc.balance) + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Số tiền (VND)</label>
                        <input type="number" step="any" x-model="transactionForm.amount" required placeholder="Nhập số tiền..." class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none text-sm font-extrabold text-amber-400">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Category -->
                        <div>
                            <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Danh mục</label>
                            <input type="text" x-model="transactionForm.category" required placeholder="Lương, Ăn uống, vv." class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none">
                        </div>
                        <!-- Transaction Date -->
                        <div>
                            <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Ngày giao dịch</label>
                            <input type="date" x-model="transactionForm.transaction_date" required class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none">
                        </div>
                    </div>

                    <!-- Note -->
                    <div>
                        <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Ghi chú chi tiết</label>
                        <input type="text" x-model="transactionForm.note" placeholder="Mua sắm ăn trưa..." class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-yellow-500 text-darkBg font-extrabold py-3.5 rounded-xl shadow-lg shadow-amber-500/10 active:scale-95 transition tracking-wider uppercase text-xs">
                        Xác nhận ghi chép
                    </button>
                </form>
            </div>
        </div>

        <!-- Bottom Sheet Modal: Thêm Tài khoản/Ví -->
        <div x-show="showAddAccountModal" class="fixed inset-0 z-40 bg-darkBg/60 backdrop-blur-sm flex items-end justify-center" style="display: none;">
            <div @click.away="showAddAccountModal = false" class="w-full max-w-md bg-slate-900 border-t border-gray-800 rounded-t-[30px] p-6 space-y-4 animate-slide-up shadow-2xl">
                <div class="flex justify-between items-center border-b border-gray-800 pb-3">
                    <h3 class="text-sm font-bold text-amber-500 uppercase tracking-wider">Tạo tài khoản / ví mới</h3>
                    <button @click="showAddAccountModal = false" class="text-gray-400 hover:text-gray-200 text-sm">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>

                <form @submit.prevent="submitAccount" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Tên ví / Ngân hàng</label>
                        <input type="text" x-model="accountForm.name" required placeholder="Ví dụ: Techcombank, Tiền mặt..." class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Loại tài khoản</label>
                        <select x-model="accountForm.type" class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none">
                            <option value="cash">Tiền mặt</option>
                            <option value="bank">Ngân hàng</option>
                            <option value="e-wallet">Ví điện tử</option>
                            <option value="other">Loại khác</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Số dư ban đầu (VND)</label>
                        <input type="number" step="any" x-model="accountForm.balance" required placeholder="0" class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none font-bold text-amber-400">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-yellow-500 text-darkBg font-extrabold py-3.5 rounded-xl shadow-lg shadow-amber-500/10 active:scale-95 transition tracking-wider uppercase text-xs">
                        Tạo ví tài khoản
                    </button>
                </form>
            </div>
        </div>

        <!-- Bottom Sheet Modal: Thêm Khoản Nợ -->
        <div x-show="showAddDebtModal" class="fixed inset-0 z-40 bg-darkBg/60 backdrop-blur-sm flex items-end justify-center" style="display: none;">
            <div @click.away="showAddDebtModal = false" class="w-full max-w-md bg-slate-900 border-t border-gray-800 rounded-t-[30px] p-6 space-y-4 animate-slide-up shadow-2xl">
                <div class="flex justify-between items-center border-b border-gray-800 pb-3">
                    <h3 class="text-sm font-bold text-amber-500 uppercase tracking-wider">Thêm hồ sơ nợ vay mới</h3>
                    <button @click="showAddDebtModal = false" class="text-gray-400 hover:text-gray-200 text-sm">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>

                <form @submit.prevent="submitDebt" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Đối tác liên quan</label>
                        <input type="text" x-model="debtForm.partner_name" required placeholder="Tên đối tác hoặc chủ nợ..." class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Loại hình</label>
                        <select x-model="debtForm.type" class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none">
                            <option value="lend">Tôi cho vay (Tài sản của tôi)</option>
                            <option value="borrow">Tôi đi vay (Khoản phải trả)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Số tiền (VND)</label>
                            <input type="number" step="any" x-model="debtForm.amount" required placeholder="0" class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none font-bold text-amber-400">
                        </div>
                        <div>
                            <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Hạn thanh toán</label>
                            <input type="date" x-model="debtForm.due_date" class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Ghi chú chi tiết</label>
                        <input type="text" x-model="debtForm.note" placeholder="Chi tiết lý do mượn..." class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-yellow-500 text-darkBg font-extrabold py-3.5 rounded-xl shadow-lg shadow-amber-500/10 active:scale-95 transition tracking-wider uppercase text-xs">
                        Ghi nhận hồ sơ nợ
                    </button>
                </form>
            </div>
        </div>

        <!-- Bottom Sheet Modal: Thêm Tài sản đầu tư -->
        <div x-show="showAddInvestmentModal" class="fixed inset-0 z-40 bg-darkBg/60 backdrop-blur-sm flex items-end justify-center" style="display: none;">
            <div @click.away="showAddInvestmentModal = false" class="w-full max-w-md bg-slate-900 border-t border-gray-800 rounded-t-[30px] p-6 space-y-4 animate-slide-up shadow-2xl">
                <div class="flex justify-between items-center border-b border-gray-800 pb-3">
                    <h3 class="text-sm font-bold text-amber-500 uppercase tracking-wider">Thêm tài sản đầu tư mới</h3>
                    <button @click="showAddInvestmentModal = false" class="text-gray-400 hover:text-gray-200 text-sm">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>

                <form @submit.prevent="submitInvestment" class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Mã tài sản (Symbol)</label>
                            <input type="text" x-model="investmentForm.symbol" required placeholder="BTC, ETH, FPT, HPG..." class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none font-bold uppercase">
                        </div>
                        <div>
                            <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Phân loại</label>
                            <select x-model="investmentForm.type" class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none">
                                <option value="stock">Cổ phiếu</option>
                                <option value="crypto">Tiền điện tử (Crypto)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Số lượng nắm giữ</label>
                        <input type="number" step="any" x-model="investmentForm.quantity" required placeholder="Ví dụ: 0.05 hoặc 500" class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none font-bold text-amber-400">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Giá mua trung bình (VND)</label>
                            <input type="number" step="any" x-model="investmentForm.buy_price" required placeholder="0" class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none font-bold text-gray-100">
                        </div>
                        <div>
                            <label class="block text-gray-400 font-bold mb-1 uppercase tracking-wider">Giá thị trường hiện tại</label>
                            <input type="number" step="any" x-model="investmentForm.current_price" placeholder="Để trống nếu = giá mua" class="w-full bg-gray-950 border border-gray-800/80 rounded-xl p-3 text-gray-200 focus:border-amber-500/80 outline-none font-bold text-emeraldAccent">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-yellow-500 text-darkBg font-extrabold py-3.5 rounded-xl shadow-lg shadow-amber-500/10 active:scale-95 transition tracking-wider uppercase text-xs">
                        Ghi nhận đầu tư
                    </button>
                </form>
            </div>
        </div>

        <!-- Fixed Bottom Navigation Bar -->
        <nav class="glass-card fixed bottom-0 z-30 w-full max-w-md h-16 border-t border-gray-800/60 shadow-2xl flex items-center justify-around px-2 text-gray-400">
            <button @click="activeTab = 'overview'" class="flex flex-col items-center justify-center flex-1 h-full active:scale-90 transition" :class="activeTab === 'overview' ? 'bottom-nav-active' : ''">
                <i class="fa-solid fa-house-chimney text-base"></i>
                <span class="text-[9px] font-bold mt-1 uppercase tracking-wider">Tổng quan</span>
            </button>
            <button @click="activeTab = 'transactions'" class="flex flex-col items-center justify-center flex-1 h-full active:scale-90 transition" :class="activeTab === 'transactions' ? 'bottom-nav-active' : ''">
                <i class="fa-solid fa-list-check text-base"></i>
                <span class="text-[9px] font-bold mt-1 uppercase tracking-wider">Giao dịch</span>
            </button>
            <button @click="activeTab = 'debts'" class="flex flex-col items-center justify-center flex-1 h-full active:scale-90 transition" :class="activeTab === 'debts' ? 'bottom-nav-active' : ''">
                <i class="fa-solid fa-handshake text-base"></i>
                <span class="text-[9px] font-bold mt-1 uppercase tracking-wider">Ghi nợ</span>
            </button>
            <button @click="activeTab = 'investments'" class="flex flex-col items-center justify-center flex-1 h-full active:scale-90 transition" :class="activeTab === 'investments' ? 'bottom-nav-active' : ''">
                <i class="fa-solid fa-chart-line text-base"></i>
                <span class="text-[9px] font-bold mt-1 uppercase tracking-wider">Tích sản</span>
            </button>
        </nav>

    </div>

    <script>
        function financeApp() {
            return {
                // Application states preloaded from controller
                activeTab: 'overview',
                accounts: @json($accounts),
                transactions: @json($transactions),
                debts: @json($debts),
                investments: @json($investments),
                overview: @json($overview),
                
                loading: false,
                isOnline: navigator.onLine,
                chart: null,
                
                notification: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                // Modals trigger states
                showAddTransactionModal: false,
                showAddAccountModal: false,
                showAddDebtModal: false,
                showAddInvestmentModal: false,

                // Forms models states
                transactionForm: {
                    account_id: '',
                    type: 'expense',
                    amount: '',
                    category: 'Ăn uống',
                    transaction_date: new Date().toISOString().substring(0, 10),
                    note: '',
                    to_account_id: ''
                },
                accountForm: {
                    name: '',
                    type: 'cash',
                    balance: ''
                },
                debtForm: {
                    partner_name: '',
                    type: 'lend',
                    amount: '',
                    due_date: new Date().toISOString().substring(0, 10),
                    note: ''
                },
                investmentForm: {
                    symbol: '',
                    type: 'stock',
                    quantity: '',
                    buy_price: '',
                    current_price: ''
                },

                init() {
                    // Sync network status
                    window.addEventListener('online', () => this.isOnline = true);
                    window.addEventListener('offline', () => this.isOnline = false);

                    // Initialize allocation graph
                    this.$nextTick(() => {
                        this.initCharts();
                    });

                    // Watch activeTab to update charts when entering home
                    this.$watch('activeTab', (val) => {
                        if (val === 'overview') {
                            this.$nextTick(() => this.initCharts());
                        }
                    });
                },

                showToast(msg, type = 'success') {
                    this.notification.message = msg;
                    this.notification.type = type;
                    this.notification.show = true;
                    setTimeout(() => {
                        this.notification.show = false;
                    }, 3500);
                },

                formatVnd(value) {
                    return new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    }).format(value || 0);
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const d = new Date(dateStr);
                    return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`;
                },

                // Core Chart.js Setup
                initCharts() {
                    const ctx = document.getElementById('allocationChart');
                    if (!ctx) return;
                    
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    const cash = parseFloat(this.overview.total_cash) || 0;
                    const investment = parseFloat(this.overview.total_investment) || 0;
                    const lend = parseFloat(this.overview.total_lend) || 0;

                    if (cash === 0 && investment === 0 && lend === 0) return;

                    this.chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Tiền mặt', 'Tích sản', 'Cho vay'],
                            datasets: [{
                                data: [cash, investment, lend],
                                backgroundColor: ['#6366f1', '#f59e0b', '#10b981'],
                                borderColor: '#0b0f19',
                                borderWidth: 2,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (context) => {
                                            return ` ${context.label}: ${this.formatVnd(context.raw)}`;
                                        }
                                    }
                                }
                            },
                            cutout: '70%',
                            responsive: true
                        }
                    });
                },

                // Refresh rates from CoinGecko API or local simulations
                async updateRates() {
                    this.loading = true;
                    try {
                        const response = await fetch('/api/finance/rates/update', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' }
                        });
                        const res = await response.json();
                        
                        if (res.status === 'success') {
                            this.investments = res.data;
                            this.showToast(res.message);
                            this.refreshData(); // Refresh summary values
                        } else {
                            this.showToast(res.message, 'error');
                        }
                    } catch (e) {
                        this.showToast('Không kết nối được server tỷ giá', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // Helper to sync Overview variables reactively after local data changes
                async refreshData() {
                    try {
                        const response = await fetch('/api/finance/overview');
                        const res = await response.json();
                        if (res.status === 'success') {
                            this.overview = res.data.overview;
                            this.accounts = res.data.accounts;
                            this.transactions = res.data.transactions;
                            this.debts = res.data.debts;
                            this.investments = res.data.investments;
                            this.initCharts();
                        }
                    } catch (e) {
                        console.error('Failed refreshing data from API:', e);
                    }
                },

                // Transaction Form Action
                openAddTransactionModal(type) {
                    this.transactionForm.type = type;
                    this.transactionForm.amount = '';
                    this.transactionForm.note = '';
                    if (this.accounts.length > 0) {
                        this.transactionForm.account_id = this.accounts[0].id;
                    }
                    this.showAddTransactionModal = true;
                },

                async submitTransaction() {
                    this.loading = true;
                    try {
                        const response = await fetch('/api/finance/transactions', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(this.transactionForm)
                        });
                        const res = await response.json();

                        if (res.status === 'success') {
                            this.showToast(res.message);
                            this.showAddTransactionModal = false;
                            await this.refreshData();
                        } else {
                            this.showToast(res.message, 'error');
                        }
                    } catch (e) {
                        this.showToast('Ghi nhận giao dịch thất bại!', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteTransaction(id) {
                    if (!confirm('Bạn có chắc chắn muốn xóa giao dịch này?')) return;
                    this.loading = true;
                    try {
                        const response = await fetch(`/api/finance/transactions/${id}`, {
                            method: 'DELETE'
                        });
                        const res = await response.json();

                        if (res.status === 'success') {
                            this.showToast(res.message);
                            await this.refreshData();
                        } else {
                            this.showToast(res.message, 'error');
                        }
                    } catch (e) {
                        this.showToast('Xóa giao dịch thất bại!', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // Account Form Actions
                async submitAccount() {
                    this.loading = true;
                    try {
                        const response = await fetch('/api/finance/accounts', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(this.accountForm)
                        });
                        const res = await response.json();

                        if (res.status === 'success') {
                            this.showToast(res.message);
                            this.showAddAccountModal = false;
                            this.accountForm = { name: '', type: 'cash', balance: '' };
                            await this.refreshData();
                        } else {
                            this.showToast(res.message, 'error');
                        }
                    } catch (e) {
                        this.showToast('Không tạo được tài khoản mới', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteAccount(id) {
                    if (!confirm('Xóa tài khoản sẽ xóa toàn bộ lịch sử giao dịch liên quan. Tiếp tục?')) return;
                    this.loading = true;
                    try {
                        const response = await fetch(`/api/finance/accounts/${id}`, {
                            method: 'DELETE'
                        });
                        const res = await response.json();

                        if (res.status === 'success') {
                            this.showToast(res.message);
                            await this.refreshData();
                        } else {
                            this.showToast(res.message, 'error');
                        }
                    } catch (e) {
                        this.showToast('Xóa ví thất bại!', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // Debts Form Actions
                async submitDebt() {
                    this.loading = true;
                    try {
                        const response = await fetch('/api/finance/debts', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(this.debtForm)
                        });
                        const res = await response.json();

                        if (res.status === 'success') {
                            this.showToast(res.message);
                            this.showAddDebtModal = false;
                            this.debtForm = { partner_name: '', type: 'lend', amount: '', due_date: new Date().toISOString().substring(0, 10), note: '' };
                            await this.refreshData();
                        } else {
                            this.showToast(res.message, 'error');
                        }
                    } catch (e) {
                        this.showToast('Tạo khoản nợ thất bại!', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async toggleDebt(id) {
                    this.loading = true;
                    try {
                        const response = await fetch(`/api/finance/debts/${id}/toggle`, {
                            method: 'POST'
                        });
                        const res = await response.json();

                        if (res.status === 'success') {
                            this.showToast(res.message);
                            await this.refreshData();
                        } else {
                            this.showToast(res.message, 'error');
                        }
                    } catch (e) {
                        this.showToast('Cập nhật trạng thái thất bại!', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteDebt(id) {
                    if (!confirm('Bạn có muốn xóa hồ sơ nợ này không?')) return;
                    this.loading = true;
                    try {
                        const response = await fetch(`/api/finance/debts/${id}`, {
                            method: 'DELETE'
                        });
                        const res = await response.json();

                        if (res.status === 'success') {
                            this.showToast(res.message);
                            await this.refreshData();
                        } else {
                            this.showToast(res.message, 'error');
                        }
                    } catch (e) {
                        this.showToast('Xóa hồ sơ nợ thất bại!', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // Investments Form Actions
                async submitInvestment() {
                    this.loading = true;
                    try {
                        const response = await fetch('/api/finance/investments', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(this.investmentForm)
                        });
                        const res = await response.json();

                        if (res.status === 'success') {
                            this.showToast(res.message);
                            this.showAddInvestmentModal = false;
                            this.investmentForm = { symbol: '', type: 'stock', quantity: '', buy_price: '', current_price: '' };
                            await this.refreshData();
                        } else {
                            this.showToast(res.message, 'error');
                        }
                    } catch (e) {
                        this.showToast('Thêm danh mục tích sản thất bại!', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async deleteInvestment(id) {
                    if (!confirm('Bạn muốn xóa tài sản đầu tư này?')) return;
                    this.loading = true;
                    try {
                        const response = await fetch(`/api/finance/investments/${id}`, {
                            method: 'DELETE'
                        });
                        const res = await response.json();

                        if (res.status === 'success') {
                            this.showToast(res.message);
                            await this.refreshData();
                        } else {
                            this.showToast(res.message, 'error');
                        }
                    } catch (e) {
                        this.showToast('Xóa tài sản thất bại!', 'error');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('FinanceTracker Service Worker registered successfully!', reg.scope))
                    .catch((err) => console.warn('Service Worker registration failed:', err));
            });
        }
    </script>
</body>
</html>
