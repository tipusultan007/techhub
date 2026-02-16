<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amc;
use App\Models\Customer;
use App\Models\Product;
use App\Models\AmcItem;
use App\Models\AmcIncludedService;
use Carbon\Carbon;

class AmcSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::first();
        $product = Product::first();

        if (!$customer) {
            $customer = Customer::create([
                'name' => 'Demo Customer',
                'email' => 'demo@example.com',
                'phone' => '1234567890',
                'address' => 'Demo Street 123'
            ]);
        }

        $amc = Amc::create([
            'contract_number' => 'AMC-' . date('Y') . '-0001',
            'customer_id' => $customer->id,
            'site_name' => 'Main Office',
            'amount' => 5000,
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'frequency' => 'quarterly',
            'agreement_type' => 'template',
            'template_id' => 1,
            'status' => 'active'
        ]);

        if ($product) {
            AmcItem::create([
                'amc_id' => $amc->id,
                'product_id' => $product->id,
                'description' => 'Enterprise Server Node'
            ]);
        }

        AmcIncludedService::create([
            'amc_id' => $amc->id,
            'service_name' => 'Quarterly Health Check',
            'description' => 'Proactive system monitoring and physical cleaning'
        ]);

        AmcIncludedService::create([
            'amc_id' => $amc->id,
            'service_name' => 'Firmware Updates',
            'description' => 'Critical security patches and stability updates'
        ]);
    }
}
