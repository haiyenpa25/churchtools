<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use App\Models\FinanceDebt;
use App\Models\FinanceInvestment;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'haiyenpa25')->first();

        if (!$user) {
            return;
        }

        // Clean existing finance data
        FinanceTransaction::where('user_id', $user->id)->delete();
        FinanceAccount::where('user_id', $user->id)->delete();
        FinanceDebt::where('user_id', $user->id)->delete();
        FinanceInvestment::where('user_id', $user->id)->delete();

        // 1. Create accounts
        $cash = FinanceAccount::create([
            'user_id' => $user->id,
            'name' => 'Tiền mặt',
            'type' => 'cash',
            'balance' => 5400000.00,
        ]);

        $vcb = FinanceAccount::create([
            'user_id' => $user->id,
            'name' => 'Vietcombank',
            'type' => 'bank',
            'balance' => 48250000.00,
        ]);

        $momo = FinanceAccount::create([
            'user_id' => $user->id,
            'name' => 'Ví Momo',
            'type' => 'e-wallet',
            'balance' => 1850000.00,
        ]);

        // 2. Create transactions
        FinanceTransaction::create([
            'user_id' => $user->id,
            'account_id' => $vcb->id,
            'type' => 'income',
            'amount' => 15000000.00,
            'category' => 'Lương',
            'transaction_date' => '2026-05-05',
            'note' => 'Nhận lương tháng 5',
        ]);

        FinanceTransaction::create([
            'user_id' => $user->id,
            'account_id' => $cash->id,
            'type' => 'expense',
            'amount' => 250000.00,
            'category' => 'Ăn uống',
            'transaction_date' => '2026-05-28',
            'note' => 'Ăn trưa với đồng nghiệp',
        ]);

        FinanceTransaction::create([
            'user_id' => $user->id,
            'account_id' => $momo->id,
            'type' => 'expense',
            'amount' => 1200000.00,
            'category' => 'Mua sắm',
            'transaction_date' => '2026-05-27',
            'note' => 'Mua quần áo mới',
        ]);

        // 3. Create debts
        FinanceDebt::create([
            'user_id' => $user->id,
            'partner_name' => 'Anh Tuấn',
            'type' => 'lend',
            'amount' => 5000000.00,
            'due_date' => '2026-06-15',
            'status' => 'unpaid',
            'note' => 'Cho vay đóng học phí',
        ]);

        FinanceDebt::create([
            'user_id' => $user->id,
            'partner_name' => 'Chị Mai',
            'type' => 'borrow',
            'amount' => 2000000.00,
            'due_date' => '2026-06-30',
            'status' => 'unpaid',
            'note' => 'Mượn tiền chi tiêu cá nhân',
        ]);

        // 4. Create investments
        FinanceInvestment::create([
            'user_id' => $user->id,
            'symbol' => 'FPT',
            'type' => 'stock',
            'quantity' => 200,
            'buy_price' => 115000.00,
            'current_price' => 128000.00,
        ]);

        FinanceInvestment::create([
            'user_id' => $user->id,
            'symbol' => 'HPG',
            'type' => 'stock',
            'quantity' => 500,
            'buy_price' => 28000.00,
            'current_price' => 29500.00,
        ]);

        FinanceInvestment::create([
            'user_id' => $user->id,
            'symbol' => 'BTC',
            'type' => 'crypto',
            'quantity' => 0.05,
            'buy_price' => 1500000000.00,
            'current_price' => 1750000000.00,
        ]);

        FinanceInvestment::create([
            'user_id' => $user->id,
            'symbol' => 'ETH',
            'type' => 'crypto',
            'quantity' => 0.8,
            'buy_price' => 75000000.00,
            'current_price' => 90000000.00,
        ]);
    }
}
