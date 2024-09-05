<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromCollection, WithHeadings
{
    protected $order;
    protected $items;

    public function __construct($order, $items)
    {
        $this->order = $order;
        $this->items = $items;
    }

    public function collection()
    {
        return collect($this->items)->map(function ($item) {
            return [
                'Item' => $item['name'],
                'Quantity' => $item['quantity'],
                'Price' => $item['price'],
                'Total' => $item['total_amount'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Item',
            'Quantity',
            'Price',
            'Total',
        ];
    }
}
