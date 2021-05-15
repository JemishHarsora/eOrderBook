<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Imports\ProductStockUpdate;
use Excel;
class ProductStockUpdateController extends Controller
{
    public function index()
    {
        if (Auth::user()->user_type == 'seller') {
            return view('frontend.user.seller.product_stock_upload.index');
        }
        elseif (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('backend.product.stock_upload.index');
        }
    }

    public function stock_update(Request $request)
    {
        if($request->hasFile('bulk_file')){
            Excel::import(new ProductStockUpdate($request->is_barcode), request()->file('bulk_file'));
        }
        flash(translate('Stock updated successfully'))->success();
        return back();
    }
}
