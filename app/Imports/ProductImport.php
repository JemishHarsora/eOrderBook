<?php

namespace App\Imports;

use App\ProductStock;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Product;
use App\ProductPrice;
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
                $products = Product::where('barcode', $importData[1])->first();
                if (Auth::user()->user_type = 'seller') {
                    $seller_id  = Auth::user()->id;
                } else {
                    $seller_id = \App\User::where('user_type', 'admin')->first()->id;
                }
                if (!$products) {

                    $product = new Product;
                    $product->barcode = $importData[1];
                    $product->name = strtoupper($importData[2]);
                    $product->added_by = Auth::user()->user_type == 'seller' ? 'seller' : 'admin';
                    // $product->user_id = Auth::user()->user_type == 'seller' ? Auth::user()->id : User::where('user_type', 'admin')->first()->id;

                    $product->unit = $importData[5];

                    $product->description = "<p>" . ucfirst($importData[8]) . "</p>";
                    $product->meta_title = ucfirst($importData[2]);
                    $product->meta_description = ucfirst($importData[8]);
                    $product->colors = json_encode(array());
                    $product->choice_options = json_encode(array());
                    $product->variations = json_encode(array());
                    // $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $importData[2])) . '-' . Str::random(5);
                    $product->mfd_by = $importData[9];
                    $product->marketed_by = $importData[10];
                    $product->photos = $importData[11];
                    $product->thumbnail_img = $importData[15];
                    $product->category_id = $this->category;
                    $product->brand_id = $this->brand;

                    $product->tags = $importData[13];
                    $product->hsn_code = $importData[14];
                    $product->save();

                    $productPrice = new ProductPrice();
                    $productPrice->added_by = Auth::user()->user_type == 'seller' ? 'seller' : 'admin';
                    $productPrice->seller_id = $seller_id;
                    $productPrice->sku = $importData[7];
                    $productPrice->min_qty = $importData[12];
                    $productPrice->unit_price = ($importData[3]) ? $importData[3] : 0;
                    $productPrice->purchase_price = $importData[4] == null ? ($importData[3]) ? $importData[3] : 0 : $importData[4];
                    $productPrice->discount = '0.00';
                    $productPrice->discount_type = 'amount';
                    $productPrice->tax = '0.00';
                    $productPrice->tax_type = 'amount';
                    $productPrice->current_stock = $importData[6];
                    $productPrice->product_id = $product->id;
                    $productPrice->shipping_type = 'free';
                    $productPrice->shipping_cost = '0.00';
                    $productPrice->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $importData[2])) . '-' . Str::random(7);
                    $productPrice->save();

                    // $product_stock              = new ProductStock();
                    // $product_stock->product_id  = $product->id;
                    // $product_stock->price       = $importData[3];
                    // $product_stock->qty         = $importData[6];
                    // $product_stock->save();
                    // dd('if',$product,$productPrice);
                } else {

                    $product = ProductPrice::where('seller_id', $seller_id)->where('product_id', $products->id)->first();
                    if (!$product) {
                        $productPrice = new ProductPrice();
                        $productPrice->added_by = Auth::user()->user_type == 'seller' ? 'seller' : 'admin';
                        $productPrice->seller_id = $seller_id;
                        $productPrice->sku = $importData[7];
                        $productPrice->min_qty = $importData[12];
                        $productPrice->unit_price = ($importData[3]) ? $importData[3] : 0;
                        $productPrice->purchase_price = $importData[4] == null ? ($importData[3]) ? $importData[3] : 0 : $importData[4];
                        $productPrice->discount = '0.00';
                        $productPrice->discount_type = 'amount';
                        $productPrice->tax = '0.00';
                        $productPrice->tax_type = 'amount';
                        $productPrice->current_stock = $importData[6];
                        $productPrice->product_id = $products->id;
                        $productPrice->shipping_type = 'free';
                        $productPrice->shipping_cost = '0.00';
                        $productPrice->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $importData[2])) . '-' . Str::random(7);
                        $productPrice->save();
                    } else {
                        $product->seller_id = $seller_id;
                        $product->sku = $importData[7];
                        $product->min_qty = $importData[12];
                        $product->unit_price = ($importData[3]) ? $importData[3] : 0;
                        $product->purchase_price = $importData[4] == null ? ($importData[3]) ? $importData[3] : 0 : $importData[4];
                        $product->discount = '0.00';
                        $product->discount_type = 'amount';
                        $product->tax = '0.00';
                        $product->tax_type = 'amount';
                        $product->current_stock = $importData[6];
                        $product->product_id = $products->id;
                        $product->shipping_type = 'free';
                        $product->shipping_cost = '0.00';
                        $product->save();
                    }
                    // dd('else',$product,$productPrice);
                }
            }
        }
    }
}
