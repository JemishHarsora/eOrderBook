<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\SubCategory;
use App\SubSubCategory;
use App\Brand;
use App\User;
use Auth;
use App\Imports\ProductImport;
use App\ProductsExport;
use App\SellersBrand;
use PDF;
use Excel;
use Illuminate\Support\Str;

class ProductBulkUploadController extends Controller
{
    public function index()
    {


        $categories = Category::where('parent_id', 0)
        ->where('digital', 0)
        ->with('childrenCategories')
        ->get();

        if (Auth::user()->user_type == 'seller') {

            $business_category = explode(',', Auth::user()->business_category);
            $getbrands = SellersBrand::where('seller_id', Auth::user()->id)
                ->groupBy('brand_id')
                ->get(["brand_id"]);
                // dd($getbrands[0]->id);
                $brand_id='';
                foreach ($getbrands as $key => $value) {
                    $brand_id .= $value['brand_id'].",";
                }
                $brand_id = explode(',',rtrim($brand_id,','));
                $brands = Brand::whereIn('id', $brand_id)->get();

                $categories = Category::where('parent_id', 0)
                ->where('digital', 0)
                ->whereIn('id', $business_category)
                ->with('childrenCategories')
                ->get();

            return view('frontend.user.seller.product_bulk_upload.index', compact('categories','brands'));
        }
        elseif (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('backend.product.bulk_upload.index', compact('categories'));
        }
    }

    public function export(){
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    public function pdf_download_category()
    {
        $categories = Category::all();
        $pdf = PDF::setOptions([
                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                        'logOutputFile' => storage_path('logs/log.htm'),
                        'tempDir' => storage_path('logs/')
                    ])->loadView('backend.downloads.category', compact('categories'));

        return $pdf->download('category.pdf');
    }

    public function pdf_download_sub_category()
    {
        $sub_categories = Subcategory::all();
        $pdf = PDF::setOptions([
                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                        'logOutputFile' => storage_path('logs/log.htm'),
                        'tempDir' => storage_path('logs/')
                    ])->loadView('backend.downloads.sub_category', compact('sub_categories'));

        return $pdf->download('sub_category.pdf');
    }

    public function pdf_download_sub_sub_category()
    {
        $sub_sub_categories = SubSubCategory::all();
        $pdf = PDF::setOptions([
                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                        'logOutputFile' => storage_path('logs/log.htm'),
                        'tempDir' => storage_path('logs/')
                    ])->loadView('backend.downloads.sub_sub_category', compact('sub_sub_categories'));

        return $pdf->download('sub_sub_category.pdf');
    }

    public function pdf_download_brand()
    {
        $brands = Brand::all();
        $pdf = PDF::setOptions([
                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                        'logOutputFile' => storage_path('logs/log.htm'),
                        'tempDir' => storage_path('logs/')
                    ])->loadView('backend.downloads.brand', compact('brands'));
        return $pdf->download('brands.pdf');
    }

    public function pdf_download_seller()
    {
        $users = User::where('user_type','seller')->get();
        $pdf = PDF::setOptions([
                        'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
                        'logOutputFile' => storage_path('logs/log.htm'),
                        'tempDir' => storage_path('logs/')
                    ])->loadView('backend.downloads.user', compact('users'));

        return $pdf->download('user.pdf');

    }

    public function bulk_upload(Request $request)
    {
        if($request->hasFile('bulk_file')){
            Excel::import(new ProductImport($request->category_id, $request->brand_id), request()->file('bulk_file'));
        }
//         flash(translate('Products imported successfully'))->success();
//         return back();
    }

}
