<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Auth;
use Hash;

use App\Area;
use App\BlockUser;
use App\Category;
use App\FlashDeal;
use App\Brand;
use App\Product;
use App\PickupPoint;
use App\CustomerPackage;
use App\CustomerProduct;
use App\User;
use App\Seller;
use App\Shop;
use App\Color;
use App\Order;
use App\BusinessSetting;
use App\City;
use App\Http\Controllers\SearchController;
use ImageOptimizer;
use Illuminate\Support\Str;
use App\Mail\SecondEmailVerifyMailManager;
use App\ProductPrice;
use App\Route;
use App\SellersBrand;
use Mail;
use App\Utility\TranslationUtility;
use App\Utility\CategoryUtility;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;

class HomeController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('frontend.user_login');
    }

    public function registration(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        $referral_code = '';
        if ($request->has('referral_code')) {
            $referral_code = $request->referral_code;
            Cookie::queue('referral_code', $request->referral_code, 43200);
        }
        $cities = City::get();
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        // $cities = City::where("status", '=', 1)->pluck("id", "name");
        // dd($cities);
        return view('frontend.user_registration', compact('cities', 'categories', 'referral_code'));
    }

    public function registration2(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        if ($request->has('referral_code')) {
            Cookie::queue('referral_code', $request->referral_code, 43200);
        }
        $cities = City::where("status", 1)->pluck("name", "id");

        return view('frontend.user_registration2', compact('cities'));
    }

    public function getAreaByCity(Request $request)
    {
        $areas = Area::where("city_id", $request->city_id)->pluck("name", "id");
        return response()->json($areas);
    }

    public function cart_login(Request $request)
    {
        $user = User::whereIn('user_type', ['customer', 'seller'])->where('email', $request->email)->orWhere('phone', $request->email)->first();
        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                if ($request->has('remember')) {
                    auth()->login($user, true);
                } else {
                    auth()->login($user, false);
                }
            } else {
                flash(translate('Invalid email or password!'))->warning();
            }
        }
        return back();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_dashboard()
    {
        return view('backend.dashboard');
    }

    /**
     * Show the customer/seller dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $today = Carbon::now()->format('l');
        // $tomorrow = Carbon::now()->add(1, 'day')->format('l');

        if (Auth::user()->user_type == 'seller') {
            return view('frontend.user.seller.dashboard');
        } elseif (Auth::user()->user_type == 'customer') {
            return view('frontend.user.customer.dashboard');
        } elseif (Auth::user()->user_type == 'salesman') {
            $routes = \DB::table("routes")
                ->select("routes.*")
                ->whereRaw("find_in_set('" . Auth::user()->id . "',routes.user_id)")
                ->get();

            return view('frontend.user.sellerStaff.salesMan.dashboard', compact('routes'));
        } elseif (Auth::user()->user_type == 'delivery') {
            $routes = \DB::table("routes")
                ->select("routes.*")
                ->whereRaw("find_in_set('" . Auth::user()->id . "',routes.user_id)")
                ->get();
            return view('frontend.user.sellerStaff.deliveryBoy.dashboard', compact('routes'));
        } else {
            abort(404);
        }
    }
    public function sellerStaffDeshboard()
    {
        $today = Carbon::now()->format('l');
        // $tomorrow = Carbon::now()->add(1, 'day')->format('l');
        $routes = \DB::table("routes")
            ->select("routes.*")
            ->whereRaw("find_in_set('" . Auth::user()->id . "',routes.user_id)")
            ->get();
        // $routes = Route::where('user_id',Auth::user()->id)->where('day',$today)->get();
        if (Auth::user()->user_type == 'salesman') {
            return view('frontend.user.sellerStaff.salesMan.dashboard', compact('routes'));
        } elseif (Auth::user()->user_type == 'delivery') {
            return view('frontend.user.sellerStaff.deliveryBoy.dashboard', compact('routes'));
        } else {
            abort(404);
        }
    }

    public function profile(Request $request)
    {
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        if (Auth::user()->user_type == 'customer') {
            return view('frontend.user.customer.profile', compact('categories'));
        } elseif (Auth::user()->user_type == 'seller') {
            return view('frontend.user.seller.profile', compact('categories'));
        } elseif (Auth::user()->user_type == 'salesman') {
            return view('frontend.user.sellerStaff.profile', compact('categories'));
        } elseif (Auth::user()->user_type == 'delivery') {
            return view('frontend.user.sellerStaff.profile', compact('categories'));
        }
    }

    public function customer_update_profile(Request $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $user = Auth::user();
        $user->name = $request->name;
        $user->address = $request->address;
        $user->country = $request->country;
        $user->city = $request->city;
        $user->area = $request->area;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;
        $user->business_category = implode(",", $request->business_category);

        if ($request->new_password != null && ($request->new_password == $request->confirm_password)) {
            $user->password = Hash::make($request->new_password);
        }
        $user->avatar_original = $request->photo;

        if ($user->save()) {
            flash(translate('Your Profile has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }


    public function seller_update_profile(Request $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $user = Auth::user();
        $user->name = $request->name;
        $user->address = $request->address;
        $user->country = $request->country;
        $user->city = $request->city;
        $user->area = $request->area;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;
        $user->business_category = implode(",", $request->business_category);

        if ($request->new_password != null && ($request->new_password == $request->confirm_password)) {
            $user->password = Hash::make($request->new_password);
        }
        $user->avatar_original = $request->photo;

        $seller = $user->seller;
        $seller->cash_on_delivery_status = $request->cash_on_delivery_status;
        $seller->bank_payment_status = $request->bank_payment_status;
        $seller->bank_name = $request->bank_name;
        $seller->bank_acc_name = $request->bank_acc_name;
        $seller->bank_acc_no = $request->bank_acc_no;
        $seller->bank_routing_no = $request->bank_routing_no;

        if ($user->save() && $seller->save()) {
            flash(translate('Your Profile has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }
    public function staff_update_profile(Request $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $user = Auth::user();
        $user->name = $request->name;
        // $user->address = $request->address;
        // $user->country = $request->country;
        // $user->city = $request->city;
        // $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;

        if ($request->new_password != null && ($request->new_password == $request->confirm_password)) {
            $user->password = Hash::make($request->new_password);
        }
        $user->avatar_original = $request->photo;

        if ($user->save()) {
            flash(translate('Your Profile has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Show the application frontend home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $cookie = Cookie::make('name', 'samip', 120);
        // dd($cookie);
        return view('frontend.index');
    }

    public function flash_deal_details($slug)
    {
        $flash_deal = FlashDeal::where('slug', $slug)->first();
        if ($flash_deal != null)
            return view('frontend.flash_deal_details', compact('flash_deal'));
        else {
            abort(404);
        }
    }

    public function load_featured_section()
    {
        return view('frontend.partials.featured_products_section');
    }

    public function load_best_selling_section()
    {
        return view('frontend.partials.best_selling_section');
    }

    public function load_home_categories_section()
    {
        return view('frontend.partials.home_categories_section');
    }

    public function load_best_sellers_section()
    {
        return view('frontend.partials.best_sellers_section');
    }

    public function trackOrder(Request $request)
    {
        if ($request->has('order_code')) {
            $order = Order::where('code', $request->order_code)->first();
            if ($order != null) {
                return view('frontend.track_order', compact('order'));
            }
        }
        return view('frontend.track_order');
    }

    public function product(Request $request, $slug)
    {
        // dd($request->all(),$slug);
        $isblock = '';
        $sellersData = [];
        $detailedProduct  = Product::where('slug', $slug)->first();
        $ProductSeller = Product::where('barcode', $detailedProduct->barcode)->where('id', '!=', $detailedProduct->id)->get();

        if (isset(Auth::user()->id)) {
            $isblock = BlockUser::where([['user_id', '=', Auth::user()->id], ['blocker_id', '=', $detailedProduct->user_id]])->orWhere([['blocker_id', '=', Auth::user()->id], ['user_id', '=', $detailedProduct->user_id]])->first();
            foreach ($ProductSeller as $sellers) {
                $sellers->isblock = BlockUser::where([['user_id', '=', Auth::user()->id], ['blocker_id', '=', $sellers->user_id]])->orWhere([['blocker_id', '=', Auth::user()->id], ['user_id', '=', $sellers->user_id]])->first();
                array_push($sellersData, $sellers);
            }
        } else {
            foreach ($ProductSeller as $sellers) {
                $sellers->isblock = false;
                array_push($sellersData, $sellers);
            }
        }

        if ($detailedProduct != null && $detailedProduct->published) {
            //updateCartSetup();
            if ($request->has('product_referral_code')) {
                Cookie::queue('product_referral_code', $request->product_referral_code, 43200);
                Cookie::queue('referred_product_id', $detailedProduct->id, 43200);
            }
            if ($detailedProduct->digital == 1) {
                return view('frontend.digital_product_details', compact('detailedProduct', 'isblock', 'sellersData'));
            } else {
                return view('frontend.product_details', compact('detailedProduct', 'isblock', 'sellersData'));
            }
            // return view('frontend.product_details', compact('detailedProduct'));
        }
        abort(404);
    }

    public function shop($slug)
    {
        $shop  = Shop::where('slug', $slug)->first();
        if ($shop != null) {
            $seller = Seller::where('user_id', $shop->user_id)->first();
            $isCustomerBlockShop = [];
            if (Auth::check()) {
                $isCustomerBlockShop = DB::table('block_users')->where('user_id', Auth::user()->id)
                    ->whereIn('blocker_id', array($shop->user_id))->first();
            }

            if ($seller->verification_status != 0) {
                return view('frontend.seller_shop', compact('shop', 'isCustomerBlockShop'));
            } else {
                return view('frontend.seller_shop_without_verification', compact('shop', 'seller'));
            }
        }
        abort(404);
    }


    public function filter_shop($slug, $type)
    {
        $shop  = Shop::where('slug', $slug)->first();
        if ($shop != null && $type != null) {
            return view('frontend.seller_shop', compact('shop', 'type'));
        }
        abort(404);
    }

    public function all_categories(Request $request)
    {
        $categories = Category::where('level', 0)->orderBy('name', 'asc')->get();
        return view('frontend.all_category', compact('categories'));
    }
    public function all_brands(Request $request)
    {
        $categories = Category::all();
        return view('frontend.all_brand', compact('categories'));
    }

    public function show_product_upload_form(Request $request)
    {
        $business_category = explode(',', Auth::user()->business_category);
        $getbrands = SellersBrand::where('seller_id', Auth::user()->id)
            ->groupBy('brand_id')
            ->get(["brand_id"]);
        // dd($getbrands[0]->id);
        $brand_id = '';
        foreach ($getbrands as $key => $value) {
            $brand_id .= $value['brand_id'] . ",";
        }
        $brand_id = explode(',', rtrim($brand_id, ','));
        $brands = Brand::whereIn('id', $brand_id)->get();

        if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            if (Auth::user()->seller->remaining_uploads > 0) {
                $categories = Category::whereIn('id', $business_category)->get();
                return view('frontend.user.seller.product_upload', compact('categories', 'brands'));
            } else {
                flash(translate('Upload limit has been reached. Please upgrade your package.'))->warning();
                return back();
            }
        }
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->whereIn('id', $business_category)
            ->with('childrenCategories')
            ->get();

        return view('frontend.user.seller.product_upload', compact('categories', 'brands'));
    }

    public function show_product_edit_form(Request $request, $id)
    {
        $product = ProductPrice::where('product_id', $id)->where('seller_id', Auth::user()->id)->with(['product'])->first();
        // $product = Product::findOrFail($id);
        $lang = $request->lang;
        // $business_category = explode(',', Auth::user()->business_category);
        // $getbrands = SellersBrand::where('seller_id', Auth::user()->id)
        //     ->groupBy('brand_id')
        //     ->get(["brand_id"]);
        // dd($getbrands[0]->id);
        // $brand_id = '';
        // foreach ($getbrands as $key => $value) {
        //     $brand_id .= $value['brand_id'] . ",";
        // }
        // $brand_id = explode(',', rtrim($brand_id, ','));
        $brand = Brand::where('id', $product->product->brand_id)->first();
        $category = Category::where('id', $product->product->category_id)->first();
        $tags = json_decode($product->product->tags);
        // $category = Category::where('parent_id', 0)
        //     ->where('digital', 0)
        //     ->whereIn('id', $business_category)
        //     ->with('childrenCategories')
        //     ->get();

        return view('frontend.user.seller.product_edit', compact('product', 'category', 'tags', 'brand', 'lang'));
    }

    public function seller_product_list(Request $request)
    {
        $search = null;


        $products = ProductPrice::where('seller_id', Auth::user()->id)->with(['product.category', 'product' => function ($q) {
            $q->where('products.digital', 0);
        }]);

        $products = $products->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $products = $products->where('name', 'like', '%' . $search . '%');
        }
        $products = $products->paginate(10);
        return view('frontend.user.seller.products', compact('products', 'search'));
    }

    public function ajax_search(Request $request)
    {
        $area_seller = getAreaWiseBrand();
        $keywords = array();
        if ($area_seller['seller_ids'] != null) {
            if ($area_seller['seller_ids']['0'] != null) {
                $products = ProductPrice::with(['product' => function($query) use($area_seller,$request){
                    $query->whereIn('brand_id', $area_seller->brand_ids)
                    ->where('tags', 'like', '%' . $request->search . '%');
                }])->where('published', 1)->groupBy('product_id')->whereIn('seller_id', $area_seller->seller_ids)->get();

            } else {

                $products = ProductPrice::with(['product' => function($query) use($request,$area_seller){
                    $query->where('tags', 'like', '%' . $request->search . '%');
                    $query->where('id', $area_seller['seller_ids']['0']);
                }])->where('published', 1)->groupBy('product_id')->get();
                

                // $products = Product::where('published', 1)->where('tags', 'like', '%' . $request->search . '%')->where('id', $area_seller['seller_ids']['0'])->get();
            }
        } else {

            $products = ProductPrice::with(['product' => function($query) use($request){
                $query->where('tags', 'like', '%' . $request->search . '%');
            }])->where('published', 1)->groupBy('product_id')->get();
            

            // $products = Product::where('published', 1)->where('tags', 'like', '%' . $request->search . '%')->get();
        }

        foreach ($products as $key => $product) {
            foreach (explode(',', $product->tags) as $key => $tag) {
                if (stripos($tag, $request->search) !== false) {
                    if (sizeof($keywords) > 5) {
                        break;
                    } else {
                        if (!in_array(strtolower($tag), $keywords)) {
                            array_push($keywords, strtolower($tag));
                        }
                    }
                }
            }
        }

        $shops = Shop::whereIn('user_id', verified_sellers_id())->where('name', 'like', '%' . $request->search . '%')->get()->take(3);
        if ($area_seller['seller_ids'] != null) {
            if ($area_seller['seller_ids']['0'] != null) {
                $products = filter_products(
                        ProductPrice::with(['product' => function($query) use($request){
                            $query->whereIn('brand_id', $area_seller->brand_ids)
                            ->where('tags', 'like', '%' . $request->search . '%');
                    }])->where('published', 1)->where('seller_id', $area_seller['seller_ids']['0'])->groupBy('product_id'))->get()->take(3);

                    // Product::where('published', 1)->where('name', 'like', '%' . $request->search . '%')->whereIn('user_id', $area_seller->seller_ids)->whereIn('brand_id', $area_seller->brand_ids))->get()->take(3);
                $shops = Shop::whereIn('user_id', $area_seller->seller_ids)->where('name', 'like', '%' . $request->search . '%')->get()->take(3);
            } else {

                $products = ProductPrice::with(['product' => function($query) use($request,$area_seller){
                    $query->where('tags', 'like', '%' . $request->search . '%');
                    $query->where('id', $area_seller['seller_ids']['0']);
                }])->where('published', 1)->groupBy('product_id')->get();                

                // $products = Product::where('published', 1)->where('tags', 'like', '%' . $request->search . '%')->where('id', $area_seller['seller_ids']['0'])->get()->take(3);
                $shops = Shop::where('user_id', $area_seller['seller_ids']['0'])->where('name', 'like', '%' . $request->search . '%')->get()->take(3);
            }
        } else {
            $products = filter_products(
                
                ProductPrice::with(['product' => function($query) use($request){
                    $query->where('name', 'like', '%' . $request->search . '%');
                }])->where('published', 1)->groupBy('product_id'))->get()->take(3);

                // Product::where('published', 1)->where('name', 'like', '%' . $request->search . '%'))->get()->take(3);
        }
        $categories = Category::where('name', 'like', '%' . $request->search . '%')->get()->take(3);

        if (sizeof($keywords) > 0 || sizeof($categories) > 0 || sizeof($products) > 0 || sizeof($shops) > 0) {
            return view('frontend.partials.search_content', compact('products', 'categories', 'keywords', 'shops'));
        }
        return '0';
    }

    public function listing(Request $request)
    {
        return $this->search($request);
    }

    public function listingByCategory(Request $request, $category_slug)
    {
        $category = Category::where('slug', $category_slug)->first();
        if ($category != null) {
            return $this->search($request, $category->id);
        }
        abort(404);
    }

    public function listingByBrand(Request $request, $brand_slug)
    {
        $brand = Brand::where('slug', $brand_slug)->first();
        if ($brand != null) {
            return $this->search($request, null, $brand->id);
        }
        abort(404);
    }

    public function search(Request $request, $category_id = null, $brand_id = null)
    {

        $query = $request->q;
        $sort_by = $request->sort_by;
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $seller_id = $request->seller_id;
        $area_seller = getAreaWiseBrand();
        $conditions = [];
        $seller_query =[];
      
        $products = ProductPrice::with(['product'])->where('published', 1)->groupBy('product_id');
      
        if ($brand_id != null) {
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        }
         elseif ($request->brand != null) {
            $brand_id = (Brand::where('slug', $request->brand)->first() != null) ? Brand::where('slug', $request->brand)->first()->id : null;
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        }

        if ($seller_id != null) {
            $sellers_id = Seller::findOrFail($seller_id)->user->id;
            $products = $products->where('seller_id', $sellers_id);
        }
        if ($seller_id == null && $request->brand == null && $area_seller['seller_ids'] != null) {
            if ($area_seller['seller_ids']['0'] != null) {
                $products = $products->whereHas('product', function($query) use($conditions,$area_seller){
                    $query->where($conditions)->whereIn('brand_id', $area_seller->brand_ids);
                })->whereIn('seller_id', $area_seller->seller_ids);

                // $products = Product::where($conditions)->whereIn('user_id', $area_seller->seller_ids)->whereIn('brand_id', $area_seller->brand_ids);
            } else {
                $products = $products->whereHas('product', function($query) use($conditions,$area_seller){
                    $query->where($conditions)->whereIn('brand_id', $area_seller->brand_ids);
                })->where('product_id', $area_seller['seller_ids']['0']);
                // $products = Product::where($conditions)->where('id', $area_seller['seller_ids']['0']);
            }            
        } else {
            $products = $products->whereHas('product', function($query) use($conditions){
                $query->where($conditions);
            });
            // $products = Product::where($conditions);
        }

        if ($category_id != null) {
            $category_ids = CategoryUtility::children_ids($category_id);
            $category_ids[] = $category_id;
            
            // $products = $products->whereIn('category_id', $category_ids);

            $products = $products->whereHas('product', function($query) use($category_ids){
                $query->whereIn('category_id', $category_ids);
            });

            if ($area_seller['seller_ids'] != null) {
                if ($area_seller['seller_ids']['0'] != null) {

                    $products = $products->whereHas('product', function($query) use($category_ids,$area_seller){
                        $query->whereIn('category_id', $area_seller->category_ids)->whereIn('brand_id', $area_seller->brand_ids)->whereIn('seller_id', $area_seller->seller_ids);
                    });

                    // $products = $products->whereIn('category_id', $category_ids)->whereIn('user_id', $area_seller->seller_ids)->whereIn('brand_id', $area_seller->brand_ids);
                } else {
                    $products = $products->whereHas('product', function($query) use($category_ids,$area_seller){
                        $query->whereIn('category_id', $category_ids);
                    })->where('product_id', $area_seller['seller_ids']['0']);

                    // $products = $products->whereIn('category_id', $category_ids)->where('id', $area_seller['seller_ids']['0']);
                }
            }
        }

        if ($min_price != null && $max_price != null) {
            $products = $products->where('purchase_price', '>=', $min_price)->where('purchase_price', '<=', $max_price);
        }

        if ($query != null) {
            $searchController = new SearchController;
            $searchController->store($request);
            //aa query baki fdggf

            $products = $products->whereHas('product', function($q) use($query){
                $q->where('name', 'like', '%' . $query . '%')->orWhere('tags', 'like', '%' . $query . '%');
            });

            // $products = $products->where('name', 'like', '%' . $query . '%')->orWhere('tags', 'like', '%' . $query . '%');
        }

        if ($sort_by != null) {
            switch ($sort_by) {
                case 'newest':
                    $products->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $products->orderBy('created_at', 'asc');
                    break;
                case 'price-asc':
                    $products->orderBy('purchase_price', 'asc');
                    break;
                case 'price-desc':
                    $products->orderBy('purchase_price', 'desc');
                    break;
                default:
                    // code...
                    break;
            }
        }


        $non_paginate_products = filter_products($products)->get();
        // dd($non_paginate_products);
        //Attribute Filter

        $attributes = array();
        foreach ($non_paginate_products as $key => $product) {
            $product = $product->product;
            if ($product->attributes != null && is_array(json_decode($product->attributes))) {
                foreach (json_decode($product->attributes) as $key => $value) {
                    $flag = false;
                    $pos = 0;
                    foreach ($attributes as $key => $attribute) {
                        if ($attribute['id'] == $value) {
                            $flag = true;
                            $pos = $key;
                            break;
                        }
                    }
                    if (!$flag) {
                        $item['id'] = $value;
                        $item['values'] = array();
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                $item['values'] = $choice_option->values;
                                break;
                            }
                        }
                        array_push($attributes, $item);
                    } else {
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                foreach ($choice_option->values as $key => $value) {
                                    if (!in_array($value, $attributes[$pos]['values'])) {
                                        array_push($attributes[$pos]['values'], $value);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $selected_attributes = array();

        foreach ($attributes as $key => $attribute) {
            if ($request->has('attribute_' . $attribute['id'])) {
                foreach ($request['attribute_' . $attribute['id']] as $key => $value) {
                    $str = '"' . $value . '"';
                    // $products = $products->where('choice_options', 'like', '%' . $str . '%');
                    $products = $products->whereHas('product', function($q) use($str){
                        $q->where('choice_options', 'like', '%' . $str . '%');
                    });
                }

                $item['id'] = $attribute['id'];
                $item['values'] = $request['attribute_' . $attribute['id']];
                array_push($selected_attributes, $item);
            }
        }


        //Color Filter
        $all_colors = array();

        foreach ($non_paginate_products as $key => $product) {
            if ($product->colors != null) {
                foreach (json_decode($product->colors) as $key => $color) {
                    if (!in_array($color, $all_colors)) {
                        array_push($all_colors, $color);
                    }
                }
            }
        }

        $selected_color = null;

        if ($request->has('color')) {
            $str = '"' . $request->color . '"';
            // $products = $products->where('colors', 'like', '%' . $str . '%');
            
            $products = $products->whereHas('product', function($q) use($str){
                $q->where('colors', 'like', '%' . $str . '%');
            });

            $selected_color = $request->color;
        }

        
        $products = filter_products($products)->paginate(12)->appends(request()->query());
        // dd(request()->query());
        return view('frontend.product_listing', compact('products', 'query', 'category_id', 'brand_id', 'sort_by', 'seller_id', 'min_price', 'max_price', 'attributes', 'selected_attributes', 'all_colors', 'selected_color'));
    }

    public function home_settings(Request $request)
    {
        return view('home_settings.index');
    }

    public function top_10_settings(Request $request)
    {
        foreach (Category::all() as $key => $category) {
            if (is_array($request->top_categories) && in_array($category->id, $request->top_categories)) {
                $category->top = 1;
                $category->save();
            } else {
                $category->top = 0;
                $category->save();
            }
        }

        foreach (Brand::all() as $key => $brand) {
            if (is_array($request->top_brands) && in_array($brand->id, $request->top_brands)) {
                $brand->top = 1;
                $brand->save();
            } else {
                $brand->top = 0;
                $brand->save();
            }
        }

        flash(translate('Top 10 categories and brands have been updated successfully'))->success();
        return redirect()->route('home_settings.index');
    }

    public function variant_price(Request $request)
    {
        $product = Product::find($request->id);
        $str = '';
        $quantity = 0;

        if ($request->has('color')) {
            $data['color'] = $request['color'];
            $str = Color::where('code', $request['color'])->first()->name;
        }

        if (json_decode(Product::find($request->id)->choice_options) != null) {
            foreach (json_decode(Product::find($request->id)->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                } else {
                    $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                }
            }
        }


        if ($str != null && $product->variant_product) {
            $product_stock = $product->stocks->where('variant', $str)->first();
            $price = $product_stock->price;
            $quantity = $product_stock->qty;
        } else {
            $price = $product->purchase_price;
            $quantity = $product->current_stock;
        }

        //discount calculation
        $flash_deals = \App\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $key => $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
                $flash_deal_product = \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $price -= ($price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }
        if (!$inFlashDeal) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }
        return array('price' => single_price($price * $request->quantity), 'quantity' => $quantity, 'digital' => $product->digital);
    }

    public function sellerpolicy()
    {
        return view("frontend.policies.sellerpolicy");
    }

    public function returnpolicy()
    {
        return view("frontend.policies.returnpolicy");
    }

    public function supportpolicy()
    {
        return view("frontend.policies.supportpolicy");
    }

    public function terms()
    {
        return view("frontend.policies.terms");
    }

    public function privacypolicy()
    {
        return view("frontend.policies.privacypolicy");
    }

    public function get_pick_ip_points(Request $request)
    {
        $pick_up_points = PickupPoint::all();
        return view('frontend.partials.pick_up_points', compact('pick_up_points'));
    }

    public function get_category_items(Request $request)
    {
        $category = Category::findOrFail($request->id);
        return view('frontend.partials.category_elements', compact('category'));
    }

    public function premium_package_index()
    {
        $customer_packages = CustomerPackage::all();
        return view('frontend.user.customer_packages_lists', compact('customer_packages'));
    }

    public function seller_staff_list(Request $request)
    {
        $search = null;
        $staffs = User::where('created_by', Auth::user()->id)->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $search = $request->search;
            $staffs = $staffs->where('name', 'like', '%' . $search . '%')->where('created_by', Auth::user()->id);
        }
        $staffs = $staffs->paginate(10);
        // dd($staffs);
        return view('frontend.user.seller.staff.index', compact('staffs', 'search'));
    }

    public function show_seller_staff_upload_form()
    {
        if (Auth::user()->seller->verification_status == 1) {
            return view('frontend.user.seller.staff.create');
        } else {
            flash(translate('Please wait untill admin verified your store.'))->error();
            return back();
        }
    }

    public function seller_staff_store(Request $request)
    {
        if (User::where('email', $request->email)->first() == null) {
            $user = new User;
            $user->name = ucfirst($request->name);
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->user_type = $request->user_type;
            $user->created_by = $request->created_by;
            $user->password = Hash::make($request->password);
            if ($user->save()) {
                flash(translate('Staff has been inserted successfully'))->success();
                return redirect()->route('seller.staffs');
            }
        }

        flash(translate('Email already used'))->error();
        return back();
    }

    public function show_seller_staff_edit_form(Request $request, $id)
    {
        $staff = User::findOrFail($id);
        return view('frontend.user.seller.staff.edit', compact('staff'));
    }

    public function seller_staff_update(Request $request, $id)
    {

        $user = User::findOrFail($id);
        $user->name = ucfirst($request->name);
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->user_type = $request->user_type;
        if (strlen($request->password) > 0) {
            $user->password = Hash::make($request->password);
        }
        if ($user->save()) {
            flash(translate('Staff has been updated successfully'))->success();
            return redirect()->route('seller.staffs');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function seller_digital_product_list(Request $request)
    {
        $products = Product::where('user_id', Auth::user()->id)->where('digital', 1)->orderBy('created_at', 'desc')->paginate(10);
        return view('frontend.user.seller.digitalproducts.products', compact('products'));
    }
    public function show_digital_product_upload_form(Request $request)
    {
        if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            if (Auth::user()->seller->remaining_digital_uploads > 0) {
                $business_settings = BusinessSetting::where('type', 'digital_product_upload')->first();
                $categories = Category::where('digital', 1)->get();
                return view('frontend.user.seller.digitalproducts.product_upload', compact('categories'));
            } else {
                flash(translate('Upload limit has been reached. Please upgrade your package.'))->warning();
                return back();
            }
        }

        $business_settings = BusinessSetting::where('type', 'digital_product_upload')->first();
        $categories = Category::where('digital', 1)->get();
        return view('frontend.user.seller.digitalproducts.product_upload', compact('categories'));
    }

    public function show_digital_product_edit_form(Request $request, $id)
    {
        $categories = Category::where('digital', 1)->get();
        $lang = $request->lang;
        $product = Product::find($id);
        return view('frontend.user.seller.digitalproducts.product_edit', compact('categories', 'product', 'lang'));
    }

    // Ajax call
    public function new_verify(Request $request)
    {
        $email = $request->email;
        if (isUnique($email) == '0') {
            $response['status'] = 2;
            $response['message'] = 'Email already exists!';
            return json_encode($response);
        }

        $response = $this->send_email_change_verification_mail($request, $email);
        return json_encode($response);
    }


    // Form request
    public function update_email(Request $request)
    {
        $email = $request->email;
        if (isUnique($email)) {
            $this->send_email_change_verification_mail($request, $email);
            flash(translate('A verification mail has been sent to the mail you provided us with.'))->success();
            return back();
        }

        flash(translate('Email already exists!'))->warning();
        return back();
    }

    public function send_email_change_verification_mail($request, $email)
    {
        $response['status'] = 0;
        $response['message'] = 'Unknown';

        $verification_code = Str::random(32);

        $array['subject'] = 'Email Verification';
        $array['from'] = env('MAIL_USERNAME');
        $array['content'] = 'Verify your account';
        $array['link'] = route('email_change.callback') . '?new_email_verificiation_code=' . $verification_code . '&email=' . $email;
        $array['sender'] = Auth::user()->name;
        $array['details'] = "Email Second";

        $user = Auth::user();
        $user->new_email_verificiation_code = $verification_code;
        $user->save();

        try {
            Mail::to($email)->queue(new SecondEmailVerifyMailManager($array));

            $response['status'] = 1;
            $response['message'] = translate("Your verification mail has been Sent to your email.");
        } catch (\Exception $e) {
            // return $e->getMessage();
            $response['status'] = 0;
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function email_change_callback(Request $request)
    {
        if ($request->has('new_email_verificiation_code') && $request->has('email')) {
            $verification_code_of_url_param =  $request->input('new_email_verificiation_code');
            $user = User::where('new_email_verificiation_code', $verification_code_of_url_param)->first();

            if ($user != null) {

                $user->email = $request->input('email');
                $user->new_email_verificiation_code = null;
                $user->save();

                auth()->login($user, true);

                flash(translate('Email Changed successfully'))->success();
                return redirect()->route('dashboard');
            }
        }

        flash(translate('Email was not verified. Please resend your mail!'))->error();
        return redirect()->route('dashboard');
    }

    public function reset_password_with_code(Request $request)
    {
        if (($user = User::where('email', $request->email)->where('verification_code', $request->code)->first()) != null) {
            if ($request->password == $request->password_confirmation) {
                $user->password = Hash::make($request->password);
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->save();
                event(new PasswordReset($user));
                auth()->login($user, true);

                flash(translate('Password updated successfully'))->success();

                if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('home');
            } else {
                flash("Password and confirm password didn't match")->warning();
                return back();
            }
        } else {
            flash("Verification code mismatch")->error();
            return back();
        }
    }
}
