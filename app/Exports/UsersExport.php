<?php 
namespace App\Exports;
  
use App\User;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithMapping, WithHeadings
{
    protected $products;
    public function __construct($products)
    {
        $this->products = $products;
    }
   
    public function headings(): array {
        return [
           "sr_no.",
           "name",
           "sku",
           "mrp",
           "selling_price",
           "current_stock",
           "discount",
           "tax",
        ];
    }

    public function collection()
    {
        return $this->products;
    }

    public function map($products): array
    {
        return [
            $products->id,
            $products->product->name,
            "",
            $products->unit_price,
            "",
            "",
           "0",
           "0",

        ];

    }
}