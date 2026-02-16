<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AmcTemplate;

class AmcTemplateSeeder extends Seeder
{
    public function run(): void
    {
        AmcTemplate::create([
            'name' => 'Standard IT Maintenance',
            'content' => '<h1>Annual Maintenance Contract</h1><p>Customer: {customer_name}</p><p>Contract No: {contract_number}</p><p>Period: {start_date} to {end_date}</p><h2>Included Equipment</h2>{items_table}<h2>Included Services</h2>{included_services}<p>Total Amount: {amount}</p>',
            'is_default' => true
        ]);

        AmcTemplate::create([
            'name' => 'Premium 24/7 Support',
            'content' => '<h1>Premium Maintenance Contract</h1><p>Customer: {customer_name}</p><p>This contract provides 24/7 priority support for {customer_name}.</p><h2>Included Equipment</h2>{items_table}<h2>Service Schedule</h2>{services_table}',
            'is_default' => false
        ]);
    }
}
