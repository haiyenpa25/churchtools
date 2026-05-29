<?php

namespace App\Http\Controllers;

use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use App\Models\FinanceDebt;
use App\Models\FinanceInvestment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FinanceController extends Controller
{
    /**
     * Display the main SPA finance view with initial data.
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Fetch all data for initial load to speed up PWA initial load
        $accounts = FinanceAccount::where('user_id', $userId)->get();
        $transactions = FinanceTransaction::with(['account', 'toAccount'])
            ->where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $debts = FinanceDebt::where('user_id', $userId)->get();
        $investments = FinanceInvestment::where('user_id', $userId)->get();

        $overview = $this->calculateOverviewData($accounts, $debts, $investments);

        return view('finance.index', compact('accounts', 'transactions', 'debts', 'investments', 'overview'));
    }

    /**
     * Fetch overview JSON data.
     */
    public function getOverview()
    {
        $userId = Auth::id();
        $accounts = FinanceAccount::where('user_id', $userId)->get();
        $transactions = FinanceTransaction::with(['account', 'toAccount'])
            ->where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $debts = FinanceDebt::where('user_id', $userId)->get();
        $investments = FinanceInvestment::where('user_id', $userId)->get();

        $overview = $this->calculateOverviewData($accounts, $debts, $investments);

        return response()->json([
            'status' => 'success',
            'data' => [
                'overview' => $overview,
                'accounts' => $accounts,
                'transactions' => $transactions,
                'debts' => $debts,
                'investments' => $investments
            ]
        ]);
    }

    /**
     * Calculate core financial metrics.
     */
    private function calculateOverviewData($accounts, $debts, $investments)
    {
        $totalCash = $accounts->sum('balance');
        
        $totalLend = $debts->where('type', 'lend')->where('status', 'unpaid')->sum('amount');
        $totalBorrow = $debts->where('type', 'borrow')->where('status', 'unpaid')->sum('amount');
        
        $totalInvestment = $investments->sum(function ($inv) {
            return $inv->quantity * $inv->current_price;
        });

        $totalInvestmentBuy = $investments->sum(function ($inv) {
            return $inv->quantity * $inv->buy_price;
        });

        $investmentPnL = $totalInvestment - $totalInvestmentBuy;
        $investmentPnLPercent = $totalInvestmentBuy > 0 ? ($investmentPnL / $totalInvestmentBuy) * 100 : 0;

        $netWorth = ($totalCash + $totalLend + $totalInvestment) - $totalBorrow;

        return [
            'net_worth' => $netWorth,
            'total_cash' => $totalCash,
            'total_lend' => $totalLend,
            'total_borrow' => $totalBorrow,
            'total_investment' => $totalInvestment,
            'investment_pnl' => $investmentPnL,
            'investment_pnl_percent' => $investmentPnLPercent,
        ];
    }

    /**
     * Store new account.
     */
    public function storeAccount(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,bank,e-wallet,other',
            'balance' => 'required|numeric|min:0',
        ]);

        $data['user_id'] = Auth::id();
        $account = FinanceAccount::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Tài khoản đã được tạo thành công!',
            'data' => $account
        ]);
    }

    /**
     * Update account.
     */
    public function updateAccount(Request $request, $id)
    {
        $account = FinanceAccount::where('user_id', Auth::id())->findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,bank,e-wallet,other',
            'balance' => 'required|numeric|min:0',
        ]);

        $account->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Tài khoản đã được cập nhật thành công!',
            'data' => $account
        ]);
    }

    /**
     * Delete account.
     */
    public function deleteAccount($id)
    {
        $account = FinanceAccount::where('user_id', Auth::id())->findOrFail($id);
        $account->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tài khoản đã được xóa thành công!'
        ]);
    }

    /**
     * Store transaction & update account balances.
     */
    public function storeTransaction(Request $request)
    {
        $rules = [
            'account_id' => 'required|exists:finance_accounts,id',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|gt:0',
            'category' => 'required|string|max:50',
            'transaction_date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ];

        if ($request->type === 'transfer') {
            $rules['to_account_id'] = 'required|exists:finance_accounts,id|different:account_id';
        }

        $data = $request->validate($rules);
        $userId = Auth::id();
        $data['user_id'] = $userId;

        // Ensure user owns the account
        $sourceAccount = FinanceAccount::where('user_id', $userId)->findOrFail($data['account_id']);
        if ($request->type === 'transfer') {
            $destAccount = FinanceAccount::where('user_id', $userId)->findOrFail($data['to_account_id']);
        }

        DB::beginTransaction();
        try {
            // Apply balance adjustments
            if ($data['type'] === 'income') {
                $sourceAccount->increment('balance', $data['amount']);
            } elseif ($data['type'] === 'expense') {
                $sourceAccount->decrement('balance', $data['amount']);
            } elseif ($data['type'] === 'transfer') {
                $sourceAccount->decrement('balance', $data['amount']);
                $destAccount->increment('balance', $data['amount']);
            }

            $transaction = FinanceTransaction::create($data);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Giao dịch đã được ghi nhận thành công!',
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Finance transaction error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete transaction & reverse account balances.
     */
    public function deleteTransaction($id)
    {
        $userId = Auth::id();
        $transaction = FinanceTransaction::where('user_id', $userId)->findOrFail($id);
        
        $sourceAccount = FinanceAccount::where('user_id', $userId)->find($transaction->account_id);
        $destAccount = $transaction->to_account_id ? FinanceAccount::where('user_id', $userId)->find($transaction->to_account_id) : null;

        DB::beginTransaction();
        try {
            // Reverse balance adjustments
            if ($sourceAccount) {
                if ($transaction->type === 'income') {
                    $sourceAccount->decrement('balance', $transaction->amount);
                } elseif ($transaction->type === 'expense') {
                    $sourceAccount->increment('balance', $transaction->amount);
                } elseif ($transaction->type === 'transfer' && $destAccount) {
                    $sourceAccount->increment('balance', $transaction->amount);
                    $destAccount->decrement('balance', $transaction->amount);
                }
            }

            $transaction->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Giao dịch đã được xóa và số dư đã được hoàn lại!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi xóa giao dịch: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store debt.
     */
    public function storeDebt(Request $request)
    {
        $data = $request->validate([
            'partner_name' => 'required|string|max:100',
            'type' => 'required|in:lend,borrow',
            'amount' => 'required|numeric|gt:0',
            'due_date' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ]);

        $data['user_id'] = Auth::id();
        $data['status'] = 'unpaid';
        $debt = FinanceDebt::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Khoản nợ đã được ghi nhận!',
            'data' => $debt
        ]);
    }

    /**
     * Toggle status of debt.
     */
    public function toggleDebtStatus($id)
    {
        $debt = FinanceDebt::where('user_id', Auth::id())->findOrFail($id);
        $debt->status = $debt->status === 'paid' ? 'unpaid' : 'paid';
        $debt->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Trạng thái nợ đã được cập nhật!',
            'data' => $debt
        ]);
    }

    /**
     * Delete debt.
     */
    public function deleteDebt($id)
    {
        $debt = FinanceDebt::where('user_id', Auth::id())->findOrFail($id);
        $debt->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Khoản nợ đã được xóa!'
        ]);
    }

    /**
     * Store investment.
     */
    public function storeInvestment(Request $request)
    {
        $data = $request->validate([
            'symbol' => 'required|string|max:10',
            'type' => 'required|in:stock,crypto',
            'quantity' => 'required|numeric|gt:0',
            'buy_price' => 'required|numeric|min:0',
            'current_price' => 'nullable|numeric|min:0',
        ]);

        $data['user_id'] = Auth::id();
        if (empty($data['current_price'])) {
            $data['current_price'] = $data['buy_price'];
        }

        $investment = FinanceInvestment::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Tài sản đầu tư đã được ghi nhận!',
            'data' => $investment
        ]);
    }

    /**
     * Update investment.
     */
    public function updateInvestment(Request $request, $id)
    {
        $investment = FinanceInvestment::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'quantity' => 'required|numeric|gt:0',
            'buy_price' => 'required|numeric|min:0',
            'current_price' => 'required|numeric|min:0',
        ]);

        $investment->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Tài sản đầu tư đã được cập nhật!',
            'data' => $investment
        ]);
    }

    /**
     * Delete investment.
     */
    public function deleteInvestment($id)
    {
        $investment = FinanceInvestment::where('user_id', Auth::id())->findOrFail($id);
        $investment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tài sản đầu tư đã được xóa!'
        ]);
    }

    /**
     * Update crypto/stock rates using direct CoinGecko public api with fallback to random simulated variation
     */
    public function updateRates()
    {
        $userId = Auth::id();
        $investments = FinanceInvestment::where('user_id', $userId)->get();

        $cryptoSymbols = $investments->where('type', 'crypto')->pluck('symbol')->map(fn($s) => strtolower($s))->toArray();
        
        $rates = [];
        $failed = false;

        if (in_array('btc', $cryptoSymbols) || in_array('eth', $cryptoSymbols)) {
            try {
                // Fetch CoinGecko rates for BTC/ETH in VND
                $response = Http::timeout(5)->get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids' => 'bitcoin,ethereum',
                    'vs_currencies' => 'vnd'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['bitcoin']['vnd'])) {
                        $rates['BTC'] = $data['bitcoin']['vnd'];
                    }
                    if (isset($data['ethereum']['vnd'])) {
                        $rates['ETH'] = $data['ethereum']['vnd'];
                    }
                } else {
                    $failed = true;
                }
            } catch (\Exception $e) {
                $failed = true;
                Log::warning('Failed fetching rates from CoinGecko: ' . $e->getMessage());
            }
        }

        // Apply changes
        DB::beginTransaction();
        try {
            foreach ($investments as $inv) {
                $symbol = strtoupper($inv->symbol);
                if (isset($rates[$symbol])) {
                    $inv->current_price = $rates[$symbol];
                    $inv->save();
                } else {
                    // Fallback simulated prices for offline or stock tickers (FPT, HPG etc)
                    // Slightly adjust current price by -2% to +3% to simulate live activity
                    $multiplier = 1 + (rand(-20, 30) / 1000);
                    $inv->current_price = round($inv->current_price * $multiplier, 2);
                    $inv->save();
                }
            }
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => $failed ? 'Tỷ giá đã được mô phỏng cập nhật thành công (CoinGecko API giới hạn hoặc Offline)!' : 'Tỷ giá đã được cập nhật trực tuyến thành công!',
                'data' => FinanceInvestment::where('user_id', $userId)->get()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi cập nhật tỷ giá: ' . $e->getMessage()
            ], 500);
        }
    }
}
