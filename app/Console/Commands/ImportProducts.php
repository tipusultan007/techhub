<?php

namespace App\Console\Commands;

use App\Imports\ProductsImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportProducts extends Command
{
    protected $signature = 'import:products {file}';
    protected $description = 'Import products from an Excel file (Columns: Title, Price, Old Price, Image URL)';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("Importing products from {$file}...");

        try {
            Excel::import(new ProductsImport, $file);
            $this->info('Products imported successfully!');
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
