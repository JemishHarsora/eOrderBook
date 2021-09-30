<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Imports\ProductStockUpdate;
use App\Imports\ProductDetailUpdate;
use App\Exports\UsersExport;
use Excel;
use App\SellersBrand;
use App\Brand;
use App\Product;
use App\ProductPrice;


class ProductStockUpdateController extends Controller
{
    public function index()
    {
        if (Auth::user()->user_type == 'seller') {
            return view('frontend.user.seller.product_stock_upload.index');
        } elseif (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('backend.product.stock_upload.index');
        }
    }

    public function stock_update(Request $request)
    {
        if ($request->hasFile('bulk_file')) {
            Excel::import(new ProductStockUpdate($request->is_barcode), request()->file('bulk_file'));
        }
        flash(translate('Stock updated successfully'))->success();
        return back();
    }


    public function detailView()
    {
        if (Auth::user()->user_type == 'seller') {
            $getbrands = SellersBrand::where('seller_id', Auth::user()->id)
            ->groupBy('brand_id')
            ->get(["brand_id"]);
            $brand_id = '';
            foreach ($getbrands as $key => $value) {
                $brand_id .= $value['brand_id'] . ",";
            }
            $brand_id = explode(',', rtrim($brand_id, ','));
            $brands = Brand::whereIn('id', $brand_id)->get();
            return view('frontend.user.seller.product_detail_upload.index',compact('brands'));
        } elseif (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('backend.product.product_detail_upload.index');
        }
    }

    public function available_product_detail(Request $request)
    {
        $prouct_id = Product::where('brand_id',$request->brand_id)->get()->pluck('id');
        $product = ProductPrice::with(['product'])->whereIn('product_id',$prouct_id)->groupBy('product_id')->get();
        if (!empty($product['0'])) {
            return Excel::download(new UsersExport($product), 'users.xlsx');
            flash(translate('Product Get successfully'))->success();
        }
        else
        {
            flash(translate('No any Product found'))->warning();
        }
        return back();
    }

    public function detail_update(Request $request)
    {
        if ($request->hasFile('bulk_file')) {
            Excel::import(new ProductDetailUpdate($request->is_barcode), request()->file('bulk_file'));
        }
        flash(translate('Stock updated successfully'))->success();
        return back();
    }
}
