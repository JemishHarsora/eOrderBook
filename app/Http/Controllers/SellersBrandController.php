<?php

namespace App\Http\Controllers;

use App\Area;
use App\Brand;
use App\City;
use App\SellersBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class SellersBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = null;
        if ($request->has('search')) {
            $search = $request->search;
            $brands = SellersBrand::with('brands','areas');
            $brands = $brands->where(function($q) use($search){
                $q->orWhereHas('brands', function($qa) use($search){
                        $qa->where(DB::raw("CONCAT(brands.name)"), 'LIKE', '%' . trim($search) . '%');

                  })->orWhereHas('areas', function($qa) use($search){
                        $qa->where(DB::raw("CONCAT(areas.name)"), 'LIKE', '%' . trim($search) . '%');

                  });
            })->where('seller_id',Auth::user()->id)->orderBy('created_at', 'desc')->paginate(15);
        }else{
            $brands = SellersBrand::with('brands','areas')->where('seller_id',Auth::user()->id)->orderBy('created_at', 'desc')->paginate(15);
        }
        // $brands = DB::table('seller_brand_area')
        // ->select('seller_brand_area.id','seller_brand_area.seller_id','areas.name as area_name', 'brands.name as brand_name', 'brands.logo as brand_logo')
        // ->leftJoin('areas','seller_brand_area.area_id','=','areas.id')
        // ->leftJoin('brands','seller_brand_area.brand_id','=','brands.id')
        // ->get();
        return view('frontend.user.seller.sellersBrand.index',compact('brands','search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::orderBy('id', 'desc')->get();
        $areas = Area::orderBy('id', 'desc')->get();
        if(Auth::user()->city){
            $areas = Area::where('city_id',Auth::user()->city)->orderBy('id', 'desc')->get();
        }
        $cities = City::orderBy('id', 'desc')->get();
        return view('frontend.user.seller.sellersBrand.create',compact('brands','cities','areas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $sellersbrand = SellersBrand::where('seller_id', Auth::user()->id)->where('brand_id', $request->brand_id)->first();
        if($sellersbrand){
            flash(translate('This Brand already exist'))->error();
            return redirect()->back();
        }
        foreach ($request->area_id as $key => $no) {
            $add_brand = new SellersBrand;
            $add_brand->seller_id = Auth::user()->id;
            $add_brand->brand_id = $request->brand_id;
            $add_brand->city_id = $request->city_id;
            $add_brand->area_id = $no;
            $add_brand->save();
        }

        flash(translate('Brand inserted successfully'))->success();
        return redirect()->route('myBrands.index');
    }

    public function changeStatus(Request $request)
    {
        $sellerCustomer = SellersBrand::findOrFail($request->id);
        $sellerCustomer->status = $request->status;
        $sellerCustomer->save();
        return 1;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $seller = SellersBrand::findOrFail($id);
        $brands = Brand::orderBy('id', 'desc')->get();
        $areas = Area::orderBy('id', 'desc')->get();
        $cities = City::orderBy('id', 'desc')->get();
        return view('frontend.user.seller.sellersBrand.edit', compact('seller','brands','areas','cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sellersbrand = SellersBrand::findOrFail($id);

        $sellersbrand = $sellersbrand::where('area_id', $request->area_id)->where('city_id', $request->city_id)->first();
        if($sellersbrand){
            flash(translate('Selected location already exist for this brand.'))->error();
            return redirect()->back();
        }
        $sellersbrand->seller_id = Auth::user()->id;
        $sellersbrand->brand_id = $request->brand_id;
        $sellersbrand->city_id = $request->city_id;
        $sellersbrand->area_id = $request->area_id;

        $sellersbrand->save();
        flash(translate('Brand updated successfully'))->success();
        return redirect()->route('myBrands.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SellersBrand::destroy($id);
        flash(translate('Brand has been deleted successfully'))->success();
        return redirect()->route('myBrands.index');
    }
}
