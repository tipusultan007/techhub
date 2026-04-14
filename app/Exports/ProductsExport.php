<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::with(['category', 'brand'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'SKU',
            'Type',
            'Category',
            'Brand',
            'Cost Price',
            'Selling Price',
            'Stock',
            'Status',
            'Created At'
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->sku,
            $product->type,
            $product->category ? $product->category->name : '',
            $product->brand ? $product->brand->name : '',
            $product->cost_price,
            $product->selling_price,
            $product->stock_quantity,
            $product->status,
            $product->created_at ? $product->created_at->format('Y-m-d H:i:s') : '',
        ];
    }
}
