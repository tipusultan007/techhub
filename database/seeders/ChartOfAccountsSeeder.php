<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['code' => '1000', 'name' => 'Cash in Hand', 'type' => 'asset'],
            ['code' => '1010', 'name' => 'Bank Account', 'type' => 'asset'],
            ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'asset'],
            ['code' => '1300', 'name' => 'Inventory', 'type' => 'asset'],
            
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability'],
            ['code' => '2100', 'name' => 'VAT Payable', 'type' => 'liability'],
            ['code' => '2200', 'name' => 'Customer Deposits', 'type' => 'liability'],
            
            ['code' => '3000', 'name' => 'Owner\'s Equity', 'type' => 'equity'],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'equity'],
            
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue'],
            ['code' => '4100', 'name' => 'Service Revenue', 'type' => 'revenue'],
            
            ['code' => '5000', 'name' => 'Cost of Goods Sold (COGS)', 'type' => 'expense'],
            ['code' => '6000', 'name' => 'General Expenses', 'type' => 'expense'],
            ['code' => '6100', 'name' => 'Rent Expense', 'type' => 'expense'],
            ['code' => '6200', 'name' => 'Salary Expense', 'type' => 'expense'],
            ['code' => '6300', 'name' => 'Shipping Expense', 'type' => 'expense'],
            ['code' => '6400', 'name' => 'Discount Expense', 'type' => 'expense'],
        ];

        foreach ($accounts as $acc) {
            Account::firstOrCreate(['code' => $acc['code']], $acc);
        }
    }
}
