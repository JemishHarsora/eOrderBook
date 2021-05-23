<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ordersExport implements FromCollection, WithMapping, WithHeadings
{
    protected $orders;
    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function headings(): array {
        return [
           "orderid",
           "order date",
           "seller name",
           "party name",
           "product name",
           "qty",
           "total price",
        ];
      }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->orders;
    }

    public function map($orders): array
    {
        $product='';
        $qty='';
        $seller='';
        foreach ($orders->orderDetails as $i => $orderDetails) {
            if(!empty($orderDetails->product)){
                $product .= ','. $orderDetails->product->sku;
            }
            if($seller == ''){
                $seller .= $orders->orderDetails[0]->getseller->name;
            }
            $qty .=','. $orderDetails->quantity;

        }

        $product_name = ltrim($product,",");
        $qty = ltrim($qty,",");
        return [
            $orders->code,
            $orders->created_at,
            $seller,
            $orders->user->name,
            $product_name,
            $qty,
            $orders->grand_total,

        ];

    }
}
