<?php

namespace App\Imports;

use App\ProductStock;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Product;
use Auth;

class ProductStockUpdate implements ToCollection
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
        foreach($collection as $key =>$importData)
        {
            if($key !=0 && $importData[0] != null)
            {
                if($this->barcode == "on"){
                    $products = Product::where('user_id', Auth::user()->id)->where('barcode', $importData[1])->first();
                }
                else
                {
                    $products = Product::where('user_id', Auth::user()->id)->where('sku', $importData[1])->first();
                }

                if($products){

                    $products->current_stock = $importData[2];
                    $products->save();

                    $product_stock = ProductStock::where('product_id', $products->id)->first();
                    $product_stock->qty = $importData[2];
                    $product_stock->save();
                }
            }
        }
    }
}
