<?php

namespace App\Imports;

use App\ProductStock;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Product;
use App\User;
use Illuminate\Support\Str;
use Auth;


class ProductImport implements ToCollection
{

    private $category;
    private $brand;

    public function __construct($category, $brand)
    {
        $this->category = $category;
        $this->brand = $brand;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        // dd($collection,$this->category,$this->brand);
        //
        foreach ($collection as $key => $importData) {
            if ($key != 0 && $importData[0] != null) {
                $products = Product::where('user_id', Auth::user()->id)->where('barcode', $importData[1])->first();
                if (!$products) {

                    $product = new Product;
                    $product->barcode = $importData[1];
                    $product->name = ucfirst($importData[2]);
                    $product->added_by = Auth::user()->user_type == 'seller' ? 'seller' : 'admin';
                    $product->user_id = Auth::user()->user_type == 'seller' ? Auth::user()->id : User::where('user_type', 'admin')->first()->id;
                    $product->unit_price = $importData[3];
                    $product->purchase_price = $importData[4] == null ? $importData[3] : $importData[4];
                    $product->unit = $importData[5];
                    $product->current_stock = $importData[6];
                    $product->sku = $importData[7];
                    $product->description = "<p>" . ucfirst($importData[8]) . "</p>";
                    $product->meta_title = ucfirst($importData[2]);
                    $product->meta_description = ucfirst($importData[8]);
                    $product->colors = json_encode(array());
                    $product->choice_options = json_encode(array());
                    $product->variations = json_encode(array());
                    $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $importData[2])) . '-' . Str::random(5);
                    $product->mfd_by = $importData[9];
                    $product->marketed_by = $importData[10];
                    $product->photos = $importData[11];
                    $product->thumbnail_img = $importData[11];
                    $product->category_id = $this->category;
                    $product->brand_id = $this->brand;
                    $product->min_qty = $importData[12];
                    $product->tags = $importData[13];
                    $product->hsn_code = $importData[14];

                    $product->discount = '0.00';
                    $product->discount_type = 'amount';
                    $product->tax = '0.00';
                    $product->tax_type = 'amount';

                    $product->save();

                    $product_stock              = new ProductStock();
                    $product_stock->product_id  = $product->id;
                    $product_stock->price       = $importData[3];
                    $product_stock->qty         = $importData[6];
                    $product_stock->save();
                }
            }
        }
    }
}
