<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\ProductPrice;
use Auth;

class ProductDetailUpdate implements ToCollection
{
    private $barcode;

    public function __construct($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $importData) {
            if ($key != 0 && $importData[0] != null) {
                if ($this->barcode == "on") {
                    $products = ProductPrice::with('product')->where('seller_id', Auth::user()->id)
                    ->whereHas('product', function($query) use($importData){
                        $query->where('barcode', $importData[1]);
                    })->first();

                } else {
                $products = ProductPrice::with('product')->where('seller_id', Auth::user()->id)
                    ->whereHas('product', function($query) use($importData){
                        $query->where('name', $importData[1]);
                    })->first();
                }

                if ($products) {

                    $products->sku = $importData[2];
                    $products->unit_price = $importData[3];
                    $products->purchase_price = $importData[4];
                    $products->current_stock = $importData[5];
                    $products->discount = $importData[6];
                    $products->tax = $importData[7];
                    $products->save();
                }
            }
        }
    }
}
