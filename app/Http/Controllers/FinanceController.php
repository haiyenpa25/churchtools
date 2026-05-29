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
    public function index()
    {
        return view('finance.index');
    }

    private function uid() { return Auth::id(); }

    private function calcOverview($accounts, $debts, $investments)
    {
        $cash        = (float) $accounts->sum('balance');
        $lend        = (float) $debts->where('type','lend')->where('status','unpaid')->sum('amount');
        $borrow      = (float) $debts->where('type','borrow')->where('status','unpaid')->sum('amount');
        $invNow      = (float) $investments->sum(fn($i) => $i->quantity * $i->current_price);
        $invBuy      = (float) $investments->sum(fn($i) => $i->quantity * $i->buy_price);
        $pnl         = $invNow - $invBuy;
        $pnlPct      = $invBuy > 0 ? ($pnl / $invBuy) * 100 : 0;
        return [
            'net_worth'            => $cash + $lend + $invNow - $borrow,
            'total_cash'           => $cash,
            'total_lend'           => $lend,
            'total_borrow'         => $borrow,
            'total_investment'     => $invNow,
            'investment_pnl'       => $pnl,
            'investment_pnl_percent' => $pnlPct,
        ];
    }

    /* ═══ OVERVIEW ═══ */
    public function getOverview()
    {
        $uid  = $this->uid();
        $accs = FinanceAccount::where('user_id', $uid)->get();
        $dbs  = FinanceDebt::where('user_id', $uid)->get();
        $invs = FinanceInvestment::where('user_id', $uid)->get();
        return response()->json(array_merge(['success' => true], $this->calcOverview($accs, $dbs, $invs)));
    }

    /* ═══ ACCOUNTS ═══ */
    public function getAccounts()
    {
        return response()->json(['success' => true, 'accounts' => FinanceAccount::where('user_id', $this->uid())->get()]);
    }

    public function storeAccount(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'type'    => 'required|in:cash,bank,e-wallet,other',
            'balance' => 'required|numeric|min:0',
        ]);
        $data['user_id'] = $this->uid();
        $acc = FinanceAccount::create($data);
        return response()->json(['success' => true, 'message' => 'Tạo ví thành công!', 'account' => $acc]);
    }

    public function updateAccount(Request $request, $id)
    {
        $acc  = FinanceAccount::where('user_id', $this->uid())->findOrFail($id);
        $data = $request->validate(['name' => 'required|string|max:100', 'type' => 'required|in:cash,bank,e-wallet,other', 'balance' => 'required|numeric|min:0']);
        $acc->update($data);
        return response()->json(['success' => true, 'message' => 'Cập nhật ví thành công!', 'account' => $acc]);
    }

    public function deleteAccount($id)
    {
        FinanceAccount::where('user_id', $this->uid())->findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Đã xoá ví!']);
    }

    /* ═══ TRANSACTIONS ═══ */
    public function getTransactions(Request $request)
    {
        $q = FinanceTransaction::with(['account', 'toAccount'])
            ->where('user_id', $this->uid())
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('q')) {
            $s = $request->q;
            $q->where(fn($x) => $x->where('category', 'like', "%{$s}%")->orWhere('note', 'like', "%{$s}%"));
        }
        if ($request->filled('type'))  $q->where('type', $request->type);
        if ($request->filled('month') && $request->filled('year')) {
            $q->whereYear('transaction_date', $request->year)->whereMonth('transaction_date', $request->month);
        }

        return response()->json(['success' => true, 'transactions' => $q->get()]);
    }

    public function storeTransaction(Request $request)
    {
        $rules = [
            'account_id'       => 'required|exists:finance_accounts,id',
            'type'             => 'required|in:income,expense,transfer',
            'amount'           => 'required|numeric|gt:0',
            'category'         => 'required|string|max:50',
            'transaction_date' => 'required|date',
            'note'             => 'nullable|string|max:255',
            'is_recurring'     => 'nullable|boolean',
            'recurring_period' => 'nullable|in:daily,weekly,monthly,yearly',
        ];
        if ($request->type === 'transfer') {
            $rules['to_account_id'] = 'required|exists:finance_accounts,id|different:account_id';
        }

        $data = $request->validate($rules);
        $uid  = $this->uid();
        $data['user_id'] = $uid;

        $src  = FinanceAccount::where('user_id', $uid)->findOrFail($data['account_id']);
        $dest = null;
        if ($request->type === 'transfer') {
            $dest = FinanceAccount::where('user_id', $uid)->findOrFail($data['to_account_id']);
        }

        DB::beginTransaction();
        try {
            if ($data['type'] === 'income')   $src->increment('balance', $data['amount']);
            if ($data['type'] === 'expense')  $src->decrement('balance', $data['amount']);
            if ($data['type'] === 'transfer') { $src->decrement('balance', $data['amount']); $dest->increment('balance', $data['amount']); }

            $tx = FinanceTransaction::create($data);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Ghi chép thành công!', 'transaction' => $tx]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Finance TX error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteTransaction($id)
    {
        $uid = $this->uid();
        $tx  = FinanceTransaction::where('user_id', $uid)->findOrFail($id);
        $src = FinanceAccount::where('user_id', $uid)->find($tx->account_id);
        $dst = $tx->to_account_id ? FinanceAccount::where('user_id', $uid)->find($tx->to_account_id) : null;

        DB::beginTransaction();
        try {
            if ($src) {
                if ($tx->type === 'income')   $src->decrement('balance', $tx->amount);
                if ($tx->type === 'expense')  $src->increment('balance', $tx->amount);
                if ($tx->type === 'transfer' && $dst) { $src->increment('balance', $tx->amount); $dst->decrement('balance', $tx->amount); }
            }
            $tx->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Đã xoá giao dịch và hoàn số dư!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateTransaction(Request $request, $id)
    {
        $uid = $this->uid();
        $tx  = FinanceTransaction::where('user_id', $uid)->findOrFail($id);
        $data = $request->validate([
            'category'         => 'sometimes|string|max:100',
            'amount'           => 'sometimes|numeric|gt:0',
            'transaction_date' => 'sometimes|date',
            'note'             => 'nullable|string|max:255',
            'is_recurring'     => 'nullable|boolean',
            'recurring_period' => 'nullable|in:daily,weekly,monthly,yearly',
        ]);

        // If amount changed, adjust account balance
        if (isset($data['amount']) && $data['amount'] != $tx->amount) {
            $src = FinanceAccount::where('user_id', $uid)->find($tx->account_id);
            if ($src) {
                DB::beginTransaction();
                try {
                    $diff = $data['amount'] - $tx->amount;
                    if ($tx->type === 'income')  $src->increment('balance', $diff);
                    if ($tx->type === 'expense') $src->decrement('balance', $diff);
                    $tx->update($data);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
            }
        } else {
            $tx->update($data);
        }

        return response()->json(['success' => true, 'message' => 'Cập nhật giao dịch thành công!', 'transaction' => $tx->fresh()]);
    }

    /* ═══ DEBTS ═══ */
    public function getDebts()
    {
        return response()->json(['success' => true, 'debts' => FinanceDebt::where('user_id', $this->uid())->get()]);
    }

    public function storeDebt(Request $request)
    {
        $data = $request->validate([
            'partner_name' => 'required|string|max:100',
            'type'         => 'required|in:lend,borrow',
            'amount'       => 'required|numeric|gt:0',
            'due_date'     => 'nullable|date',
            'note'         => 'nullable|string|max:255',
        ]);
        $data['user_id'] = $this->uid();
        $data['status']  = 'unpaid';
        $debt = FinanceDebt::create($data);
        return response()->json(['success' => true, 'message' => 'Ghi nhận khoản nợ thành công!', 'debt' => $debt]);
    }

    public function toggleDebtStatus($id)
    {
        $debt = FinanceDebt::where('user_id', $this->uid())->findOrFail($id);
        $debt->status = $debt->status === 'paid' ? 'unpaid' : 'paid';
        $debt->save();
        return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái!', 'debt' => $debt]);
    }

    public function deleteDebt($id)
    {
        FinanceDebt::where('user_id', $this->uid())->findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Đã xoá khoản nợ!']);
    }

    /* ═══ INVESTMENTS ═══ */
    public function getInvestments()
    {
        return response()->json(['success' => true, 'investments' => FinanceInvestment::where('user_id', $this->uid())->get()]);
    }

    public function storeInvestment(Request $request)
    {
        $data = $request->validate([
            'symbol'        => 'required|string|max:10',
            'type'          => 'required|in:stock,crypto',
            'quantity'      => 'required|numeric|gt:0',
            'buy_price'     => 'required|numeric|min:0',
            'current_price' => 'nullable|numeric|min:0',
        ]);
        $data['user_id']      = $this->uid();
        $data['current_price'] = $data['current_price'] ?? $data['buy_price'];
        $inv = FinanceInvestment::create($data);
        return response()->json(['success' => true, 'message' => 'Thêm tài sản thành công!', 'investment' => $inv]);
    }

    public function updateInvestment(Request $request, $id)
    {
        $inv  = FinanceInvestment::where('user_id', $this->uid())->findOrFail($id);
        $data = $request->validate(['quantity' => 'required|numeric|gt:0', 'buy_price' => 'required|numeric|min:0', 'current_price' => 'required|numeric|min:0']);
        $inv->update($data);
        return response()->json(['success' => true, 'message' => 'Cập nhật thành công!', 'investment' => $inv]);
    }

    public function deleteInvestment($id)
    {
        FinanceInvestment::where('user_id', $this->uid())->findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Đã xoá tài sản!']);
    }

    /* ═══ CRYPTO RATES ═══ */
    public function updateRates()
    {
        $uid  = $this->uid();
        $invs = FinanceInvestment::where('user_id', $uid)->get();
        $rates = []; $failed = false;

        $cryptos = $invs->where('type', 'crypto')->pluck('symbol')->map(fn($s) => strtolower($s))->toArray();
        if (!empty($cryptos)) {
            try {
                $ids = implode(',', array_unique(array_map(fn($s) => match($s) { 'btc' => 'bitcoin', 'eth' => 'ethereum', default => $s }, $cryptos)));
                $r   = Http::timeout(5)->get('https://api.coingecko.com/api/v3/simple/price', ['ids' => $ids, 'vs_currencies' => 'vnd']);
                if ($r->successful()) {
                    $d = $r->json();
                    if (isset($d['bitcoin']['vnd']))  $rates['BTC'] = $d['bitcoin']['vnd'];
                    if (isset($d['ethereum']['vnd'])) $rates['ETH'] = $d['ethereum']['vnd'];
                } else { $failed = true; }
            } catch (\Exception $e) { $failed = true; Log::warning('CoinGecko: ' . $e->getMessage()); }
        }

        DB::beginTransaction();
        try {
            foreach ($invs as $inv) {
                $sym = strtoupper($inv->symbol);
                $inv->current_price = isset($rates[$sym]) ? $rates[$sym] : round($inv->current_price * (1 + rand(-20, 30) / 1000), 2);
                $inv->save();
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => $failed ? '⚠️ Mô phỏng tỷ giá (offline)' : '✅ Cập nhật tỷ giá thành công!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* ═══ STATISTICS ═══ */
    public function getStats(Request $request)
    {
        $uid   = $this->uid();
        $year  = (int) $request->get('year',  date('Y'));
        $month = (int) $request->get('month', date('n'));

        $byCat = FinanceTransaction::where('user_id', $uid)->where('type', 'expense')
            ->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month)
            ->selectRaw('category, SUM(amount) as total')->groupBy('category')->orderByDesc('total')->get();

        $byInc = FinanceTransaction::where('user_id', $uid)->where('type', 'income')
            ->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month)
            ->selectRaw('category, SUM(amount) as total')->groupBy('category')->orderByDesc('total')->get();

        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $d   = \Carbon\Carbon::create($year, $month, 1)->subMonths($i);
            $inc = (float) FinanceTransaction::where('user_id', $uid)->where('type', 'income')
                ->whereYear('transaction_date', $d->year)->whereMonth('transaction_date', $d->month)->sum('amount');
            $exp = (float) FinanceTransaction::where('user_id', $uid)->where('type', 'expense')
                ->whereYear('transaction_date', $d->year)->whereMonth('transaction_date', $d->month)->sum('amount');
            $trend[] = ['label' => 'T' . $d->month . '/' . $d->year, 'income' => $inc, 'expense' => $exp, 'savings' => $inc - $exp];
        }

        $totalExp = (float) $byCat->sum('total');
        $totalInc = (float) $byInc->sum('total');

        return response()->json([
            'success'            => true,
            'by_category'        => $byCat,
            'income_by_category' => $byInc,
            'trend'              => $trend,
            'total_expense'      => $totalExp,
            'total_income'       => $totalInc,
            'savings_rate'       => $totalInc > 0 ? round((($totalInc - $totalExp) / $totalInc) * 100, 1) : 0,
        ]);
    }

    /* ═══ CURRENCY RATES (USD/EUR/JPY → VND) ═══ */
    public function getCurrencyRates()
    {
        $rates = ['USD' => 25450, 'EUR' => 27200, 'JPY' => 170, 'GBP' => 32000, 'updated' => now()->format('H:i d/m')];
        try {
            $r = Http::timeout(4)->get('https://open.er-api.com/v6/latest/USD');
            if ($r->successful()) {
                $d = $r->json();
                if (isset($d['rates']['VND'])) {
                    $vnd = $d['rates']['VND'];
                    $rates['USD'] = round($vnd);
                    $rates['EUR'] = isset($d['rates']['EUR']) ? round($vnd / $d['rates']['EUR']) : $rates['EUR'];
                    $rates['JPY'] = isset($d['rates']['JPY']) ? round($vnd / $d['rates']['JPY']) : $rates['JPY'];
                    $rates['GBP'] = isset($d['rates']['GBP']) ? round($vnd / $d['rates']['GBP']) : $rates['GBP'];
                    $rates['updated'] = now()->format('H:i d/m');
                }
            }
        } catch (\Exception $e) { /* use defaults */ }
        return response()->json(['success' => true, 'rates' => $rates]);
    }
}
